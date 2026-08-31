<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Belfoods Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/adminpage.css') }}">
    @stack('styles')
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Belfoods" style="max-height:65px; width:auto; object-fit:contain;" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
            </a>
        </div>
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-chart-pie"></i>
                    Dashboard
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-bag-shopping"></i>
                    Order Masuk
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.orders.completed') ? 'active' : '' }}">
                <a href="{{ route('admin.orders.completed') }}">
                    <i class="fas fa-clock-rotate-left"></i>
                    Riwayat Order
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a href="{{ route('admin.products.index') }}">
                    <i class="fas fa-box"></i>
                    Kelola Produk
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.promos.*') ? 'active' : '' }}">
                <a href="{{ route('admin.promos.index') }}">
                    <i class="fas fa-tags"></i>
                    Kelola Promo
                </a>
            </li>
            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                    <i class="fas fa-right-from-bracket"></i>
                    Logout
                </a>
                <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <div class="topbar">
            <div>
                <h1>@yield('title', 'Admin Dashboard')</h1>
                <p>Panel Kontrol Belfoods Store</p>
            </div>
            <div class="top-action">
                <a href="{{ route('home') }}" class="btn" style="background:#eef2f7; color:#333; padding:8px 16px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                    <i class="fas fa-store"></i> Lihat Toko
                </a>
                <div class="profile">
                    <div style="width:40px; height:40px; border-radius:50%; background:#e0e0e0; display:flex; align-items:center; justify-content:center; font-weight:600; color:#444;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <h4>{{ auth()->user()->name ?? 'Administrator' }}</h4>
                        <span>{{ ucfirst(auth()->user()->role ?? 'Admin') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="padding:12px 20px; background:#d4edda; color:#155724; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
                <div><i class="fas fa-check-circle" style="margin-right:8px;"></i> {{ session('success') }}</div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#155724;"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div style="padding:12px 20px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
                <div><i class="fas fa-exclamation-circle" style="margin-right:8px;"></i> {{ session('error') }}</div>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#721c24;"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
