<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

/*
|--------------------------------------------------------------------------
| PUBLIC & LANDING PAGE ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function (Illuminate\Http\Request $request) {
    $query = \App\Models\Product::where('is_active', true);

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('filter')) {
        switch ($request->filter) {
            case 'best_seller':
                $query->where('is_best_seller', true);
                break;
            case 'premium':
                $query->where('is_premium', true);
                break;
            case 'hemat':
                $query->where('is_hemat', true);
                break;
        }
    }

    $products = $query->latest()->get();

    return view('homepage', compact('products'));
})->name('home');

Route::get('/keranjang', function () {
    return view('keranjang');
})->name('cart');

// Cart Operations
Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');
Route::match(['patch', 'post'], '/cart/update/{product}', [ProductController::class, 'updateCart'])->name('cart.update');
Route::match(['delete', 'post'], '/cart/remove/{product}', [ProductController::class, 'removeFromCart'])->name('cart.remove');
Route::match(['delete', 'post'], '/cart/clear', [ProductController::class, 'clearCart'])->name('cart.clear');
Route::post('/cart/promo', [ProductController::class, 'applyPromo'])->name('cart.promo.apply');
Route::match(['delete', 'post'], '/cart/promo/remove', [ProductController::class, 'removePromo'])->name('cart.promo.remove');

/*
|--------------------------------------------------------------------------
| CATEGORY ROUTES (Dynamic Product Lists)
|--------------------------------------------------------------------------
*/

Route::get('/sosis', [ProductController::class, 'sosis'])->name('products.sosis');
Route::get('/bakso', [ProductController::class, 'bakso'])->name('products.bakso');
Route::get('/for-kids', [ProductController::class, 'forkids'])->name('products.for-kids');
Route::get('/others', [ProductController::class, 'others'])->name('products.others');
Route::get('/nugget', [ProductController::class, 'nugget'])->name('products.nugget');

Route::get('/produk/{product}', [ProductController::class, 'show'])->name('products.show');
Route::post('/produk/{product}/cart', [ProductController::class, 'addToCart'])->name('products.addToCart');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES (Guest)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.process');

    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES (Requires Login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // User Profile
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::post('/profile', function (Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|min:10|max:50',
            'birthdate' => 'nullable|date',
            'gender' => 'nullable|string|max:50',
            'job' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'address' => 'required|string',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Kolom nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'phone.required' => 'Kolom nomor telepon wajib diisi.',
            'phone.min' => 'Nomor telepon minimal 10 digit.',
            'address.required' => 'Kolom alamat lengkap wajib diisi.',
            'avatar.image' => 'File foto profil harus berupa gambar.',
            'avatar.mimes' => 'Format foto profil harus berupa JPG, JPEG, PNG, atau WEBP.',
            'avatar.max' => 'Ukuran foto profil maksimal 2 MB.',
            'new_password.min' => 'Password baru minimal terdiri dari 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = auth()->user();

        // Handle Avatar / Profile Photo
        if ($request->hasFile('avatar')) {
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('avatar')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        // Handle Personal Info
        if ($request->has('name') && $request->name !== null) $user->name = $request->name;
        if ($request->has('email') && $request->email !== null) $user->email = $request->email;
        if ($request->has('phone')) $user->phone = $request->phone;
        if ($request->has('birthdate')) $user->birthdate = $request->birthdate;
        if ($request->has('gender')) $user->gender = $request->gender;
        if ($request->has('job')) $user->job = $request->job;
        if ($request->has('bio')) $user->bio = $request->bio;

        // Handle Address
        if ($request->has('address')) $user->address = $request->address;
        if ($request->has('province')) $user->province = $request->province;
        if ($request->has('city')) $user->city = $request->city;
        if ($request->has('district')) $user->district = $request->district;
        if ($request->has('postal_code')) $user->postal_code = $request->postal_code;

        // Handle Password
        if ($request->filled('new_password')) {
            if (!$request->filled('old_password')) {
                return back()->with('error', 'Masukkan password lama untuk mengonfirmasi perubahan password.')->withInput();
            }
            if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Password lama tidak cocok / salah.')->withInput();
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Data profil, foto, alamat, dan password berhasil diperbarui!');
    })->name('profile.update');

    // Checkout & Payment (QRIS)
    Route::get('/payment', function () {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda masih kosong.');
        }
        return view('payment');
    })->name('payment');

    Route::post('/payment', function (Illuminate\Http\Request $request) {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
            'payment_proof.image' => 'File bukti pembayaran harus berupa gambar.',
            'payment_proof.mimes' => 'Format file harus berupa JPG, JPEG, PNG, atau WEBP.',
            'payment_proof.max' => 'Ukuran file bukti pembayaran maksimal 4 MB.',
        ]);

        $cartRaw = session('cart', []);
        if (empty($cartRaw)) {
            return redirect()->route('cart')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $path = $request->file('payment_proof')->store('payments', 'public');

        $productIds = array_keys($cartRaw);
        $dbProducts = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        foreach ($cartRaw as $id => $item) {
            if (isset($dbProducts[$id])) {
                $item['price'] = $dbProducts[$id]->price;
                $item['name'] = $dbProducts[$id]->name;
            }
            $cartItems[$id] = $item;
        }

        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);

        // Check promo discount if applied
        $promoSession = session('cart_promo') ?? session('checkout_promo');
        $discount = 0;
        $promoId = null;
        $promoCode = null;
        if ($promoSession && isset($promoSession['discount'])) {
            $discount = min((float)$promoSession['discount'], $subtotal);
            $promoId = $promoSession['id'] ?? null;
            $promoCode = $promoSession['code'] ?? null;
        }
        $total = max(0, $subtotal - $discount);

        $order = null;

        \Illuminate\Support\Facades\DB::transaction(function () use ($path, $cartItems, $subtotal, $discount, $total, $promoId, $promoCode, &$order) {
            $user = auth()->user();
            $orderNumber = 'BF' . date('YmdHis') . rand(10, 99);

            // Reduce promo quota if any
            if ($promoId) {
                $promoDb = \App\Models\Promo::find($promoId);
                if ($promoDb && !is_null($promoDb->quota) && $promoDb->quota > 0) {
                    $promoDb->decrement('quota');
                }
            }

            // Create Order in DB
            $order = \App\Models\Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'promo_id' => $promoId,
                'promo_code' => $promoCode,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => 0,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'waiting',
                'payment_method' => 'qris',
                'shipping_name' => $user->name,
                'shipping_phone' => $user->phone ?? '',
                'shipping_address' => $user->address ?? 'Self Pickup Toko Belfoods',
                'ordered_at' => now(),
            ]);

            foreach ($cartItems as $productId => $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);

                // Reduce stock
                $product = \App\Models\Product::find($productId);
                if ($product) {
                    if ($product->stock >= $item['quantity']) {
                        $product->decrement('stock', $item['quantity']);
                    } else {
                        $product->update(['stock' => 0]);
                    }
                    $product->increment('sold_count', $item['quantity']);
                }
            }

            // Save Payment record
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'qris',
                'status' => 'waiting_verification',
                'amount' => $total,
                'proof_image' => $path,
                'payment_proof' => $path,
            ]);
        });

        // Clear session cart & promo
        session()->forget('cart');
        session()->forget('cart_promo');
        session()->forget('checkout_promo');

        $order->load(['items.product', 'payment', 'user']);

        return view('donepay', compact('order'));
    })->name('payment.process');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Payments
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');

    // Checkout Controller
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/promo', [CheckoutController::class, 'applyPromo'])->name('checkout.promo');
    Route::delete('/checkout/promo', [CheckoutController::class, 'removePromo'])->name('checkout.promo.remove');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', function () {
        $totalOrders = \App\Models\Order::count();
        $totalProducts = \App\Models\Product::count();
        $activePromos = \App\Models\Promo::where('is_active', true)->count();
        $totalRevenue = \App\Models\Order::where('payment_status', 'paid')->orWhere('status', 'completed')->sum('total');

        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
        $completedOrders = \App\Models\Order::with('user')->where('status', 'completed')->latest()->take(5)->get();
        $products = \App\Models\Product::latest()->take(5)->get();
        $promos = \App\Models\Promo::latest()->take(5)->get();

        return view('adminpage', compact(
            'totalOrders', 'totalProducts', 'activePromos', 'totalRevenue',
            'recentOrders', 'completedOrders', 'products', 'promos'
        ));
    })->name('dashboard');

    // Admin Products CRUD
    Route::get('/products', [ProductController::class, 'adminIndex'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Admin Promos CRUD
    Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');
    Route::get('/promos/create', [PromoController::class, 'create'])->name('promos.create');
    Route::post('/promos', [PromoController::class, 'store'])->name('promos.store');
    Route::get('/promos/{promo}/edit', [PromoController::class, 'edit'])->name('promos.edit');
    Route::put('/promos/{promo}', [PromoController::class, 'update'])->name('promos.update');
    Route::delete('/promos/{promo}', [PromoController::class, 'destroy'])->name('promos.destroy');
    Route::patch('/promos/{promo}/toggle-status', [PromoController::class, 'toggleStatus'])->name('promos.toggle-status');

    // Admin Orders
    Route::get('/orders', [OrderController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/completed', [OrderController::class, 'completed'])->name('orders.completed');
    Route::get('/orders/{order}', [OrderController::class, 'adminShow'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment');

    // Admin Payments
    Route::get('/payments', [PaymentController::class, 'adminIndex'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'adminShow'])->name('payments.show');
    Route::patch('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
});