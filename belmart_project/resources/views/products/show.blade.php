<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} | Belfoods Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Belfoods" style="max-height:48px; width:auto; object-fit:contain;"
                 onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
        </a>

        <div class="nav-menu">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('products.nugget') }}">Nugget</a>
        </div>

        <div class="search-box">
            <form action="{{ route('home') }}" method="GET" style="display:flex; width:100%;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk...">
            </form>
            <span>🔍</span>
        </div>

        @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
        <a href="{{ route('cart') }}" class="cart-button">
            <i class="fa-solid fa-cart-shopping"></i>
            @if($cartCount > 0)
                <span class="cart-count">{{ $cartCount }}</span>
            @endif
        </a>

        @auth
            <a href="{{ route('profile') }}" class="profile-button">
                <i class="fa-regular fa-user"></i>
            </a>
        @else
            <a href="{{ route('login') }}" style="padding:8px 18px; background:#5b991d; color:#fff; border-radius:20px; text-decoration:none; font-size:14px;">
                Masuk
            </a>
        @endauth
    </div>
</nav>

<!-- FLASH ALERTS -->
@if(session('success'))
    <div class="main-container" style="padding-bottom:0; padding-top:20px;">
        <div class="alert success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    </div>
@endif

<!-- MAIN CONTENT -->
<main class="main-container">
    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="{{ route('home') }}">Beranda</a>
        <span>›</span>
        <a href="{{ route('home') }}" style="text-transform:lowercase;">{{ $product->category }}</a>
        <span>›</span>
        <strong>{{ $product->name }}</strong>
    </div>

    <!-- PRODUCT DETAIL SECTION -->
    <section class="product-detail">
        <!-- IMAGE -->
        <div class="product-image-section">
            <div class="image-wrapper">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="main-product-image">
                @else
                    <div class="no-image">
                        <span>🥩</span>
                        <p>Belum ada gambar</p>
                    </div>
                @endif
                <div class="fresh-badge">
                    <span style="font-size:9px; font-weight:700; text-align:center; line-height:1.2;">FRESH<br>FROZEN</span>
                </div>
            </div>
        </div>

        <!-- INFO -->
        <div class="product-information">
            <span class="product-category" style="display:inline-block; background:#eef9e7; color:#5b991d; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; margin-bottom:16px;">
                {{ $product->category }}
            </span>

            <h1 style="font-size:28px; font-weight:700; color:#23372a; line-height:1.2; margin-bottom:15px;">
                {{ $product->name }}
            </h1>

            @if($product->description)
                <p style="color:#64796a; line-height:1.7; font-size:14px; margin-bottom:22px;">
                    {{ $product->description }}
                </p>
            @endif

            <div style="font-size:32px; font-weight:800; color:#5b991d; margin-bottom:22px;">
                Rp {{ number_format($product->price, 0, ',', '.') }}
                @if($product->shelf_life)
                    <span style="font-size:14px; color:#8a9e8d; font-weight:400;">/ {{ $product->shelf_life }}</span>
                @endif
            </div>

            <div style="border-top:1px solid #e8f0e5; margin-bottom:22px;"></div>

            @if($product->stock > 0)
                <div style="color:#4d8a28; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-circle-check"></i> Stok Tersedia ({{ $product->stock }} sisa)
                </div>

                <form action="{{ route('products.addToCart', $product) }}" method="POST" id="cartForm">
                    @csrf
                    <label style="display:block; font-weight:600; margin-bottom:10px; font-size:14px;">Jumlah Pembelian</label>
                    <div style="display:flex; gap:15px; align-items:center; margin-bottom:20px;">
                        <div style="display:flex; align-items:center; border:1px solid #dce7d8; border-radius:10px; overflow:hidden;">
                            <button type="button" onclick="decreaseQty()" style="width:40px; height:40px; background:#f5f8f3; border:none; cursor:pointer; font-size:20px; color:#5b991d;">−</button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" readonly
                                   style="width:55px; height:40px; text-align:center; border:none; font-weight:600; font-size:16px;">
                            <button type="button" onclick="increaseQty()" style="width:40px; height:40px; background:#f5f8f3; border:none; cursor:pointer; font-size:20px; color:#5b991d;">+</button>
                        </div>
                        <button type="submit" style="flex:1; height:44px; background:#5b991d; color:#fff; border:none; border-radius:10px; font-weight:700; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                            <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </form>
            @else
                <div style="color:#e53935; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-circle-xmark"></i> Stok Sedang Habis
                </div>
            @endif

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-top:25px; border-top:1px solid #e8f0e5; padding-top:22px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:22px;">❄</span>
                    <div>
                        <strong style="font-size:13px; display:block;">Cold Chain Terjaga</strong>
                        <small style="color:#8a9e8d;">Selalu dalam suhu beku</small>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:22px;">✓</span>
                    <div>
                        <strong style="font-size:13px; display:block;">Halal & BPOM</strong>
                        <small style="color:#8a9e8d;">Sudah tersertifikasi</small>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function increaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) < parseInt(input.max)) input.value = parseInt(input.value) + 1;
}
function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
}
</script>
</body>
</html>
