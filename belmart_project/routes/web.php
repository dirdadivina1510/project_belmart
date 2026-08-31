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

Route::get('/orders', function (Illuminate\Http\Request $request) {
    $userId = auth()->id() ?? 1;
    $orders = \App\Models\Order::where('user_id', $userId)->with('items')->latest()->paginate(10);
    return view('orders.index', compact('orders'));
})->name('orders.index');

// Alias for cart.add
Route::post('/cart/add/{product}', [ProductController::class, 'addToCart'])->name('cart.add');

Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::post('/profile', function (Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:50',
        'old_password' => 'nullable|string',
        'new_password' => 'nullable|string|min:6|confirmed',
    ]);

    if (auth()->check()) {
        $user = auth()->user();
        if ($request->filled('name')) $user->name = $request->name;
        if ($request->filled('email')) $user->email = $request->email;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('address')) $user->address = $request->address;

        if ($request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
                return back()->with('error', 'Password lama salah.');
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }
        $user->save();
    }

    if ($request->has('name') && $request->name !== null) session(['profile_name' => $request->name]);
    if ($request->has('email') && $request->email !== null) session(['profile_email' => $request->email]);
    if ($request->has('phone') && $request->phone !== null) session(['profile_phone' => $request->phone]);
    if ($request->has('birthdate') && $request->birthdate !== null) session(['profile_birthdate' => $request->birthdate]);
    if ($request->has('gender') && $request->gender !== null) session(['profile_gender' => $request->gender]);
    if ($request->has('job') && $request->job !== null) session(['profile_job' => $request->job]);
    if ($request->has('bio') && $request->bio !== null) session(['profile_bio' => $request->bio]);
    if ($request->has('address') && $request->address !== null) session(['profile_address' => $request->address]);
    if ($request->has('province') && $request->province !== null) session(['profile_province' => $request->province]);
    if ($request->has('city') && $request->city !== null) session(['profile_city' => $request->city]);
    if ($request->has('district') && $request->district !== null) session(['profile_district' => $request->district]);
    if ($request->has('postal_code') && $request->postal_code !== null) session(['profile_postal_code' => $request->postal_code]);

    return back()->with('success', 'Profil berhasil diperbarui!');
})->name('profile.update');

Route::get('/payment', function () {
    return view('payment');
})->name('payment');

Route::post('/payment', function (Illuminate\Http\Request $request) {
    $request->validate([
        'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
    ]);

    $path = $request->file('payment_proof')->store('payments', 'public');

    $cartRaw = session('cart', []);
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

    // Create Order in DB
    $orderNumber = 'BF' . date('YmdHis') . rand(10, 99);
    $userId = auth()->id() ?? 1;

    $order = \App\Models\Order::create([
        'order_number' => $orderNumber,
        'user_id' => $userId,
        'subtotal' => $subtotal,
        'total' => $subtotal,
        'status' => 'pending',
        'payment_status' => 'waiting',
        'payment_method' => 'qris',
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
            $product->decrement('stock', $item['quantity']);
            $product->increment('sold_count', $item['quantity']);
        }
    }

    // Save Payment record
    \App\Models\Payment::create([
        'order_id' => $order->id,
        'payment_method' => 'qris',
        'status' => 'waiting_verification',
        'amount' => $subtotal,
        'proof_image' => $path,
    ]);

    // Clear session cart
    session()->forget('cart');

    return view('donepay', compact('order'));
})->name('payment.process');

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
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Payments
    Route::get('/orders/{order}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/orders/{order}/payment', [PaymentController::class, 'store'])->name('payments.store');

    // Checkout
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