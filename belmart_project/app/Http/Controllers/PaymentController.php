<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | USER - PAYMENT PAGE
    |--------------------------------------------------------------------------
    */

    public function create(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method === 'cod') {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Pesanan COD tidak membutuhkan bukti pembayaran.'
                );
        }

        if ($order->payment_status === 'paid') {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'success',
                    'Pembayaran sudah diverifikasi.'
                );
        }

        return view(
            'payments.create',
            compact('order')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | USER - UPLOAD PAYMENT PROOF
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Order $order
    ) {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->payment_method === 'cod') {
            return back()
                ->with(
                    'error',
                    'COD tidak membutuhkan bukti pembayaran.'
                );
        }

        if ($order->payment_status === 'paid') {
            return back()
                ->with(
                    'error',
                    'Pembayaran sudah diterima.'
                );
        }

        $validated = $request->validate([
            'payment_proof' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ], [
            'payment_proof.required' => 'File bukti pembayaran wajib diunggah.',
            'payment_proof.image' => 'File bukti pembayaran harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus berupa JPG, JPEG, PNG, atau WEBP.',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 4 MB.',
        ]);


        DB::transaction(function () use (
            $request,
            $order
        ) {

            $payment = Payment::firstOrNew([
                'order_id' => $order->id,
            ]);


            /*
            |--------------------------------------------------------------------------
            | DELETE OLD PROOF
            |--------------------------------------------------------------------------
            */

            if (
                $payment->exists &&
                $payment->payment_proof
            ) {

                Storage::disk('public')
                    ->delete(
                        $payment->payment_proof
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | STORE NEW PROOF
            |--------------------------------------------------------------------------
            */

            $path = $request
                ->file('payment_proof')
                ->store(
                    'payments',
                    'public'
                );


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $payment->fill([

                'payment_method' =>
                    $order->payment_method,

                'amount' =>
                    $order->total,

                'payment_proof' =>
                    $path,

                'status' =>
                    'pending',

                'paid_at' =>
                    null,

                'admin_note' =>
                    null,

            ]);

            $payment->save();


            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            $order->update([

                'payment_status' =>
                    'waiting',

            ]);
        });


        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim. Tunggu verifikasi admin.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - PAYMENT LIST
    |--------------------------------------------------------------------------
    */

    public function adminIndex(
        Request $request
    ) {
        $query = Payment::with([
            'order.user',
        ]);


        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas(
                'order',
                function ($q) use ($search) {

                    $q->where(
                        'order_number',
                        'like',
                        "%{$search}%"
                    );

                    $q->orWhereHas(
                        'user',
                        function ($user) use ($search) {

                            $user->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );

                            $user->orWhere(
                                'email',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            );
        }


        $payments = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view(
            'admin.payments.index',
            compact('payments')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - PAYMENT DETAIL
    |--------------------------------------------------------------------------
    */

    public function adminShow(
        Payment $payment
    ) {
        $payment->load([
            'order.user',
            'order.items.product',
        ]);

        return view(
            'admin.payments.show',
            compact('payment')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN - VERIFY PAYMENT
    |--------------------------------------------------------------------------
    */

    public function verify(
        Request $request,
        Payment $payment
    ) {

        $validated = $request->validate([

            'status' => [
                'required',
                'in:paid,rejected',
            ],

            'admin_note' => [
                'nullable',
                'string',
                'max:1000',
            ],

        ], [
            'status.required' => 'Status verifikasi pembayaran wajib dipilih.',
            'status.in' => 'Pilihan status verifikasi pembayaran tidak valid.',
        ]);


        DB::transaction(function () use (
            $payment,
            $validated
        ) {

            $status =
                $validated['status'];


            /*
            |--------------------------------------------------------------------------
            | PAYMENT
            |--------------------------------------------------------------------------
            */

            $payment->update([

                'status' =>
                    $status,

                'admin_note' =>
                    $validated['admin_note']
                    ?? null,

                'paid_at' =>
                    $status === 'paid'
                    ? now()
                    : null,

            ]);


            /*
            |--------------------------------------------------------------------------
            | ORDER
            |--------------------------------------------------------------------------
            */

            if ($status === 'paid') {

                $payment->order->update([

                    'payment_status' =>
                        'paid',

                ]);

            } else {

                $payment->order->update([

                    'payment_status' =>
                        'rejected',

                ]);
            }
        });


        if ($validated['status'] === 'paid') {

            return back()
                ->with(
                    'success',
                    'Pembayaran berhasil diterima.'
                );
        }


        return back()
            ->with(
                'success',
                'Pembayaran ditolak. User dapat mengupload bukti pembayaran kembali.'
            );
    }
}