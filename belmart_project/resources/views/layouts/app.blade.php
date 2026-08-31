<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Belfoods Store') | Toko Frozen Food Terpercaya</title>
    <meta name="description" content="Belfoods Store - Solusi frozen food lezat dan higienis untuk seluruh keluarga Indonesia.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    @stack('styles')
    @yield('head')
</head>
<body>

<div class="wrapper">
    <!-- ===================== SIDEBAR ===================== -->
    <aside class="sidebar">
        <div class="logo-area">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Belfoods Store" style="max-height:55px; width:auto; object-fit:contain;"
                     onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
            </a>
        </div>

        <div class="sidebar-box">
            <div class="sidebar-title">
                <h3>Kategori & Filter</h3>
                <i class="fa-solid fa-sliders"></i>
            </div>
            <ul class="category-list">
                <li class="{{ !request('filter') && request()->routeIs('products.nugget') ? 'active' : '' }}" style="position:relative;">
                    <i class="fa-solid fa-cookie-bite"></i> Nugget
                    <a href="{{ route('products.nugget') }}" style="position:absolute;inset:0;"></a>
                </li>
                <li class="{{ request('filter') == 'best_seller' ? 'active' : '' }}" style="position:relative;">
                    <i class="fa-solid fa-star" style="color:#e63946;"></i> Best Seller
                    <a href="{{ route('home', ['filter' => 'best_seller']) }}" style="position:absolute;inset:0;"></a>
                </li>
                <li class="{{ request('filter') == 'premium' ? 'active' : '' }}" style="position:relative;">
                    <i class="fa-solid fa-crown" style="color:#f59e0b;"></i> Premium
                    <a href="{{ route('home', ['filter' => 'premium']) }}" style="position:absolute;inset:0;"></a>
                </li>
                <li class="{{ request('filter') == 'hemat' ? 'active' : '' }}" style="position:relative;">
                    <i class="fa-solid fa-tag" style="color:#10b981;"></i> Hemat
                    <a href="{{ route('home', ['filter' => 'hemat']) }}" style="position:absolute;inset:0;"></a>
                </li>
            </ul>
        </div>

        <div class="sidebar-box">
            <div class="sidebar-title">
                <h3>Akun</h3>
            </div>
            @auth
                <ul class="category-list">
                    <li style="position:relative;">
                        <i class="fa-regular fa-user"></i> {{ auth()->user()->name }}
                        <a href="{{ route('profile') }}" style="position:absolute;inset:0;"></a>
                    </li>
                    @if(auth()->user()->role === 'admin')
                    <li style="position:relative;">
                        <i class="fa-solid fa-user-shield"></i> Admin Panel
                        <a href="{{ route('admin.dashboard') }}" style="position:absolute;inset:0;"></a>
                    </li>
                    @endif
                    <li style="position:relative;">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                        <a href="#" onclick="event.preventDefault(); document.getElementById('sidebar-logout').submit();" style="position:absolute;inset:0;"></a>
                        <form id="sidebar-logout" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                    </li>
                </ul>
            @else
                <ul class="category-list">
                    <li style="position:relative;">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk
                        <a href="{{ route('login') }}" style="position:absolute;inset:0;"></a>
                    </li>
                    <li style="position:relative;">
                        <i class="fa-solid fa-user-plus"></i> Daftar
                        <a href="{{ route('register') }}" style="position:absolute;inset:0;"></a>
                    </li>
                </ul>
            @endauth
        </div>
    </aside>

    <!-- ===================== CONTENT ===================== -->
    <div class="content">
        <!-- NAVBAR -->
        <header class="navbar">
            <div class="search-box">
                <form action="{{ route('home') }}" method="GET" style="display:flex; width:100%;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk favoritmu...">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <nav class="nav-menu">
                <a href="{{ route('home') }}">
                    <i class="fa-solid fa-store"></i> Toko
                </a>

                @php
                    $cart = session('cart', []);
                    $cartCount = collect($cart)->sum('quantity');
                @endphp

                <a href="{{ route('cart') }}" class="cart">
                    <i class="fa-solid fa-cart-shopping"></i> Keranjang
                    @if($cartCount > 0)
                        <span>{{ $cartCount }}</span>
                    @endif
                </a>

                <a href="{{ route('orders.index') }}">
                    <i class="fa-solid fa-receipt"></i> Pesanan Saya
                </a>

                @auth
                    <a href="{{ route('profile') }}">
                        <i class="fa-regular fa-user"></i> {{ auth()->user()->name }}
                    </a>
                @else
                    <a href="{{ route('login') }}" style="background:#5b991d; color:#fff; padding:8px 18px; border-radius:20px;">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk
                    </a>
                @endauth
            </nav>
        </header>

        <!-- Global Flash Alerts -->
        @if(session('success'))
            <div style="padding:12px 20px; background:#d4edda; color:#155724; border-radius:12px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
                <div><i class="fas fa-check-circle" style="margin-right:8px;"></i> {{ session('success') }}</div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#155724;"><i class="fas fa-times"></i></button>
            </div>
        @endif
        @if(session('error'))
            <div style="padding:12px 20px; background:#f8d7da; color:#721c24; border-radius:12px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
                <div><i class="fas fa-exclamation-circle" style="margin-right:8px;"></i> {{ session('error') }}</div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#721c24;"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')

        <!-- Footer -->
        <footer style="background:#1e293b; color:#cbd5e1; padding:30px 25px 20px; margin-top:40px; border-radius:20px;">
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:25px;">
                <div>
                    <h4 style="color:#fff; margin-bottom:12px;">Belfoods Store</h4>
                    <p style="font-size:13px; line-height:1.6;">Solusi praktis frozen food lezat dan higienis untuk seluruh keluarga Indonesia.</p>
                </div>
                <div>
                    <h5 style="color:#fff; margin-bottom:12px;">Kategori</h5>
                    <ul style="list-style:none; padding:0; font-size:13px; line-height:2;">
                        <li><a href="{{ route('products.nugget') }}" style="color:#cbd5e1;">Nugget</a></li>
                    </ul>
                </div>
                <div>
                    <h5 style="color:#fff; margin-bottom:12px;">Layanan</h5>
                    <p style="font-size:13px; line-height:1.8;">
                        <i class="fa-solid fa-phone" style="margin-right:6px;"></i> 0812-3456-7890<br>
                        <i class="fa-solid fa-envelope" style="margin-right:6px;"></i> support@belfoods.id
                    </p>
                </div>
            </div>
            <div style="border-top:1px solid #334155; margin-top:25px; padding-top:15px; text-align:center; font-size:12px;">
                &copy; {{ date('Y') }} Belfoods Store. All rights reserved.
            </div>
        </footer>
    </div>
</div>

@stack('scripts')
</body>
</html>
