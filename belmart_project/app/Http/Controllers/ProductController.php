<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC CATEGORIES & SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function nugget(Request $request)
    {
        return $this->getCategoryView('Nugget', 'nugget', $request);
    }

    public function sosis(Request $request)
    {
        return $this->getCategoryView('Sosis', 'sosis', $request);
    }

    public function bakso(Request $request)
    {
        return $this->getCategoryView('Bakso', 'bakso', $request);
    }

    public function forkids(Request $request)
    {
        return $this->getCategoryView('For Kids', 'forkids', $request);
    }

    public function others(Request $request)
    {
        return $this->getCategoryView('Others', 'others', $request);
    }

    private function getCategoryView($categoryName, $viewName, Request $request)
    {
        $query = Product::where('is_active', true);

        if ($categoryName !== 'Semua') {
            $query->where(function ($q) use ($categoryName) {
                $q->where('category', 'like', '%' . $categoryName . '%');
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('sold_count', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        return view($viewName, compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | CART OPERATIONS
    |--------------------------------------------------------------------------
    */

    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.required' => 'Jumlah produk wajib diisi.',
            'quantity.integer' => 'Jumlah produk harus berupa angka.',
            'quantity.min' => 'Jumlah produk minimal 1.',
        ]);

        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock) {
            return back()->with('error', 'Jumlah produk melebihi stok yang tersedia.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $newQuantity = $cart[$product->id]['quantity'] + $quantity;

            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Jumlah produk di keranjang melebihi stok.');
            }

            $cart[$product->id]['quantity'] = $newQuantity;
            $cart[$product->id]['price'] = $product->price;
            $cart[$product->id]['name'] = $product->name;
            $cart[$product->id]['image'] = $product->image;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function updateCart(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$product->id])) {
            return back()->with('error', 'Produk tidak ditemukan di keranjang.');
        }

        $currentQty = (int) $cart[$product->id]['quantity'];
        $newQty = $currentQty;

        if ($request->filled('action')) {
            if ($request->action === 'increase') {
                $newQty = $currentQty + 1;
            } elseif ($request->action === 'decrease') {
                $newQty = $currentQty - 1;
            }
        } elseif ($request->filled('quantity')) {
            $newQty = (int) $request->quantity;
        }

        if ($newQty <= 0) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
            return back()->with('success', "{$product->name} berhasil dihapus dari keranjang.");
        }

        if ($newQty > $product->stock) {
            return back()->with('error', "Jumlah {$product->name} melebihi batas stok yang tersedia (Maks: {$product->stock}).");
        }

        $cart[$product->id]['quantity'] = $newQty;
        $cart[$product->id]['price'] = $product->price;
        $cart[$product->id]['name'] = $product->name;
        $cart[$product->id]['image'] = $product->image;

        session()->put('cart', $cart);

        return back()->with('success', "Jumlah {$product->name} berhasil diperbarui.");
    }

    public function removeFromCart(Product $product)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
            return back()->with('success', "{$product->name} berhasil dihapus dari keranjang.");
        }

        return back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    public function clearCart()
    {
        session()->forget('cart');
        session()->forget('cart_promo');
        session()->forget('checkout_promo');

        return back()->with('success', 'Keranjang belanja berhasil dikosongkan.');
    }

    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
        ], [
            'promo_code.required' => 'Silakan masukkan kode voucher promo terlebih dahulu.',
        ]);

        $cartRaw = session('cart', []);
        if (empty($cartRaw)) {
            return back()->with('error', 'Keranjang belanja masih kosong.');
        }

        $productIds = array_keys($cartRaw);
        $dbProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $subtotal = 0;
        foreach ($cartRaw as $id => $item) {
            $price = isset($dbProducts[$id]) ? $dbProducts[$id]->price : $item['price'];
            $subtotal += $price * $item['quantity'];
        }

        $promo = Promo::where('code', strtoupper(trim($request->promo_code)))
            ->where('is_active', true)
            ->first();

        if (!$promo) {
            return back()->with('error', 'Kode voucher promo tidak ditemukan atau tidak aktif.');
        }

        $now = now();
        if ($now->lt($promo->start_date) || $now->gt($promo->end_date)) {
            return back()->with('error', 'Masa berlaku voucher promo sudah habis atau belum dimulai.');
        }

        if (!is_null($promo->quota) && $promo->quota <= 0) {
            return back()->with('error', 'Kuota penggunaan voucher promo ini sudah habis.');
        }

        if ($subtotal < $promo->minimum_purchase) {
            return back()->with('error', 'Minimum pembelian untuk voucher ini adalah Rp' . number_format($promo->minimum_purchase, 0, ',', '.'));
        }

        $discount = 0;
        if ($promo->discount_type === 'percentage') {
            $discount = $subtotal * ($promo->discount_value / 100);
        } else {
            $discount = $promo->discount_value;
        }

        if (!is_null($promo->maximum_discount) && $promo->maximum_discount > 0) {
            $discount = min($discount, $promo->maximum_discount);
        }

        $discount = min($discount, $subtotal);

        $promoData = [
            'id' => $promo->id,
            'code' => $promo->code,
            'name' => $promo->name,
            'discount' => $discount,
        ];

        session(['cart_promo' => $promoData]);
        session(['checkout_promo' => $promoData]);

        return back()->with('success', "Voucher promo {$promo->code} berhasil digunakan! Diskon Rp" . number_format($discount, 0, ',', '.'));
    }

    public function removePromo()
    {
        session()->forget('cart_promo');
        session()->forget('checkout_promo');

        return back()->with('success', 'Voucher promo berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN PRODUCT CRUD
    |--------------------------------------------------------------------------
    */

    public function adminIndex(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'stock_low':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'stock_high':
                    $query->orderBy('stock', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10)->withQueryString();

        $totalProductsCount = Product::count();
        $totalStockSum = Product::sum('stock');
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        return view('admin.products.index', compact('products', 'totalProductsCount', 'totalStockSum', 'outOfStockCount'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'shelf_life' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_best_seller' => 'nullable|boolean',
            'is_hemat' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Kolom nama produk wajib diisi.',
            'category.required' => 'Kolom kategori produk wajib dipilih.',
            'price.required' => 'Kolom harga produk wajib diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk tidak boleh negatif.',
            'stock.required' => 'Kolom stok produk wajib diisi.',
            'stock.integer' => 'Stok produk harus berupa angka bulat.',
            'stock.min' => 'Stok produk tidak boleh negatif.',
            'image.image' => 'File foto produk harus berupa gambar.',
            'image.mimes' => 'Format foto produk harus berupa JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran foto produk maksimal 2 MB.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_hemat'] = $request->boolean('is_hemat');
        $validated['is_premium'] = $request->boolean('is_premium');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'shelf_life' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_best_seller' => 'nullable|boolean',
            'is_hemat' => 'nullable|boolean',
            'is_premium' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Kolom nama produk wajib diisi.',
            'category.required' => 'Kolom kategori produk wajib dipilih.',
            'price.required' => 'Kolom harga produk wajib diisi.',
            'price.numeric' => 'Harga produk harus berupa angka.',
            'price.min' => 'Harga produk tidak boleh negatif.',
            'stock.required' => 'Kolom stok produk wajib diisi.',
            'stock.integer' => 'Stok produk harus berupa angka bulat.',
            'stock.min' => 'Stok produk tidak boleh negatif.',
            'image.image' => 'File foto produk harus berupa gambar.',
            'image.mimes' => 'Format foto produk harus berupa JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran foto produk maksimal 2 MB.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_best_seller'] = $request->boolean('is_best_seller');
        $validated['is_hemat'] = $request->boolean('is_hemat');
        $validated['is_premium'] = $request->boolean('is_premium');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}