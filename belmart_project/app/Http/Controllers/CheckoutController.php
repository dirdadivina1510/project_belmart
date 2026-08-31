<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CHECKOUT PAGE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }

        $subtotal = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $totalItems = $cart->items->sum(
            'quantity'
        );

        $shippingCost = $this->calculateShipping(
            $subtotal
        );

        $discount = 0;
        $promo = null;

        return view(
            'checkout.index',
            compact(
                'cart',
                'subtotal',
                'shippingCost',
                'discount',
                'promo',
                'totalItems'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPLY PROMO
    |--------------------------------------------------------------------------
    */

    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => [
                'required',
                'string',
            ],
        ]);

        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()
                ->with(
                    'error',
                    'Keranjang masih kosong.'
                );
        }

        $promo = Promo::where(
            'code',
            strtoupper($request->promo_code)
        )
        ->where('is_active', true)
        ->first();

        if (!$promo) {
            return back()
                ->with(
                    'error',
                    'Kode promo tidak ditemukan.'
                );
        }

        $now = now();

        if (
            $now->lt($promo->start_date) ||
            $now->gt($promo->end_date)
        ) {
            return back()
                ->with(
                    'error',
                    'Promo sudah tidak berlaku.'
                );
        }

        if (
            !is_null($promo->quota) &&
            $promo->quota <= 0
        ) {
            return back()
                ->with(
                    'error',
                    'Kuota promo sudah habis.'
                );
        }

        $subtotal = $cart->items->sum(
            function ($item) {
                return $item->product->price
                    * $item->quantity;
            }
        );

        if (
            $subtotal <
            $promo->minimum_purchase
        ) {
            return back()
                ->with(
                    'error',
                    'Minimum pembelian untuk promo ini adalah Rp' .
                    number_format(
                        $promo->minimum_purchase,
                        0,
                        ',',
                        '.'
                    )
                );
        }

        $discount = $this->calculateDiscount(
            $promo,
            $subtotal
        );

        session([
            'checkout_promo' => [
                'id' => $promo->id,
                'code' => $promo->code,
                'discount' => $discount,
            ],
        ]);

        return back()
            ->with(
                'success',
                'Promo berhasil digunakan.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE PROMO
    |--------------------------------------------------------------------------
    */

    public function removePromo()
    {
        session()->forget(
            'checkout_promo'
        );

        return back()
            ->with(
                'success',
                'Promo berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ORDER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([

            'shipping_name' => [
                'required',
                'string',
                'max:255',
            ],

            'shipping_phone' => [
                'required',
                'string',
                'max:30',
            ],

            'shipping_address' => [
                'required',
                'string',
            ],

            'payment_method' => [
                'required',
                'in:cod,transfer,qris',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $order = DB::transaction(function () use (
            $validated
        ) {

            $cart = Cart::with(
                'items.product'
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->lockForUpdate()
            ->first();

            if (
                !$cart ||
                $cart->items->isEmpty()
            ) {

                abort(
                    422,
                    'Keranjang masih kosong.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK STOCK
            |--------------------------------------------------------------------------
            */

            foreach (
                $cart->items
                as $item
            ) {

                $product = $item->product;

                if (
                    !$product ||
                    !$product->is_active
                ) {

                    abort(
                        422,
                        'Salah satu produk sudah tidak tersedia.'
                    );
                }

                if (
                    $product->stock
                    <
                    $item->quantity
                ) {

                    abort(
                        422,
                        'Stok ' .
                        $product->name .
                        ' tidak mencukupi.'
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL
            |--------------------------------------------------------------------------
            */

            $subtotal = $cart->items->sum(
                function ($item) {
                    return $item->product->price
                        * $item->quantity;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | PROMO
            |--------------------------------------------------------------------------
            */

            $discount = 0;
            $promoCode = null;
            $promo = null;

            $promoSession = session(
                'checkout_promo'
            );

            if ($promoSession) {

                $promo = Promo::find(
                    $promoSession['id']
                );

                if (
                    $promo &&
                    $promo->is_active &&
                    now()->between(
                        $promo->start_date,
                        $promo->end_date
                    )
                ) {

                    if (
                        $subtotal >=
                        $promo->minimum_purchase
                    ) {

                        $discount =
                            $this->calculateDiscount(
                                $promo,
                                $subtotal
                            );

                        $promoCode =
                            $promo->code;
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | SHIPPING
            |--------------------------------------------------------------------------
            */

            $shippingCost =
                $this->calculateShipping(
                    $subtotal
                );


            /*
            |--------------------------------------------------------------------------
            | TOTAL
            |--------------------------------------------------------------------------
            */

            $total =
                max(
                    0,
                    $subtotal
                    -
                    $discount
                    +
                    $shippingCost
                );


            /*
            |--------------------------------------------------------------------------
            | ORDER NUMBER
            |--------------------------------------------------------------------------
            */

            $orderNumber =
                'ORD-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    Str::random(6)
                );


            /*
            |--------------------------------------------------------------------------
            | CREATE ORDER
            |--------------------------------------------------------------------------
            */

            $order = Order::create([

                'user_id' =>
                    auth()->id(),

                'order_number' =>
                    $orderNumber,

                'subtotal' =>
                    $subtotal,

                'discount' =>
                    $discount,

                'shipping_cost' =>
                    $shippingCost,

                'total' =>
                    $total,

                'promo_code' =>
                    $promoCode,

                'status' =>
                    'pending',

                'payment_status' =>
                    $validated['payment_method']
                    === 'cod'
                    ? 'waiting'
                    : 'unpaid',

                'payment_method' =>
                    $validated['payment_method'],

                'shipping_name' =>
                    $validated['shipping_name'],

                'shipping_phone' =>
                    $validated['shipping_phone'],

                'shipping_address' =>
                    $validated['shipping_address'],

                'notes' =>
                    $validated['notes'] ?? null,
            ]);


            /*
            |--------------------------------------------------------------------------
            | ORDER ITEMS
            |--------------------------------------------------------------------------
            */

            foreach (
                $cart->items
                as $item
            ) {

                $product =
                    $item->product;

                $itemSubtotal =
                    $product->price
                    *
                    $item->quantity;

                OrderItem::create([

                    'order_id' =>
                        $order->id,

                    'product_id' =>
                        $product->id,

                    'product_name' =>
                        $product->name,

                    'price' =>
                        $product->price,

                    'quantity' =>
                        $item->quantity,

                    'subtotal' =>
                        $itemSubtotal,
                ]);


                /*
                |--------------------------------------------------------------------------
                | REDUCE STOCK
                |--------------------------------------------------------------------------
                */

                $product->decrement(
                    'stock',
                    $item->quantity
                );


                /*
                |--------------------------------------------------------------------------
                | SOLD COUNT
                |--------------------------------------------------------------------------
                */

                $product->increment(
                    'sold_count',
                    $item->quantity
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REDUCE PROMO QUOTA
            |--------------------------------------------------------------------------
            */

            if (
                $promo &&
                !is_null($promo->quota)
            ) {

                $promo->decrement(
                    'quota'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CLEAR CART
            |--------------------------------------------------------------------------
            */

            $cart->items()->delete();


            return $order;
        });


        /*
        |--------------------------------------------------------------------------
        | CLEAR PROMO SESSION
        |--------------------------------------------------------------------------
        */

        session()->forget(
            'checkout_promo'
        );


        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Pesanan berhasil dibuat.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DISCOUNT CALCULATOR
    |--------------------------------------------------------------------------
    */

    private function calculateDiscount(
        Promo $promo,
        float $subtotal
    ): float {

        if (
            $promo->discount_type
            ===
            'percentage'
        ) {

            $discount =
                $subtotal
                *
                (
                    $promo->discount_value
                    /
                    100
                );

        } else {

            $discount =
                $promo->discount_value;
        }


        /*
        |--------------------------------------------------------------------------
        | MAXIMUM DISCOUNT
        |--------------------------------------------------------------------------
        */

        if (
            !is_null(
                $promo->maximum_discount
            )
        ) {

            $discount =
                min(
                    $discount,
                    $promo->maximum_discount
                );
        }


        return min(
            $discount,
            $subtotal
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SHIPPING CALCULATOR
    |--------------------------------------------------------------------------
    */

    private function calculateShipping(
        float $subtotal
    ): float {

        /*
        |--------------------------------------------------------------------------
        | FREE SHIPPING
        |--------------------------------------------------------------------------
        */

        if ($subtotal >= 150000) {
            return 0;
        }


        /*
        |--------------------------------------------------------------------------
        | DEFAULT SHIPPING
        |--------------------------------------------------------------------------
        */

        return 15000;
    }
}