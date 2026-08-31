<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Belfoods Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}">
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Belfoods"
                 onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
        </a>

        <div class="search-box">
            <form action="{{ route('home') }}" method="GET" style="display:flex; width:100%;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk...">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <nav>
            <a href="{{ route('home') }}">
                <i class="fa-solid fa-store"></i> Toko
            </a>
            <a href="{{ route('cart') }}" class="active">
                <i class="fa-solid fa-cart-shopping"></i> Keranjang
            </a>
            @auth
                <a href="{{ route('profile') }}">
                    <i class="fa-regular fa-user"></i> {{ auth()->user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>
            @endauth
        </nav>
    </div>
</header>

<!-- FLASH ALERTS -->
@if(session('success'))
    <div style="max-width:1450px; margin:15px auto; width:92%; padding:12px 20px; background:#d4edda; color:#155724; border-radius:12px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="max-width:1450px; margin:15px auto; width:92%; padding:12px 20px; background:#f8d7da; color:#721c24; border-radius:12px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

<!-- BREADCRUMB -->
<section class="breadcrumb">
    <div class="container">
        <a href="{{ route('home') }}">Beranda</a>
        <i class="fas fa-chevron-right" style="font-size:12px;"></i>
        <span>Keranjang Belanja</span>
    </div>
</section>

<!-- MAIN CART -->
<section class="cart">
    @php
        $cartRaw = session('cart', []);
        $productIds = array_keys($cartRaw);
        $dbProducts = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');

        $cartItems = [];
        foreach ($cartRaw as $id => $item) {
            if (isset($dbProducts[$id])) {
                $dbProd = $dbProducts[$id];
                $item['price'] = $dbProd->price;
                $item['name'] = $dbProd->name;
                $item['image'] = $dbProd->image;
            }
            $cartItems[$id] = $item;
        }

        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
        $totalItems = collect($cartItems)->sum('quantity');
        $discount = 0;
        $total = max(0, $subtotal - $discount);
    @endphp

    <div class="container">
        <div class="cart-left">
            <div class="cart-header">
                <label>
                    <input type="checkbox"> Pilih Semua ({{ $totalItems }} Barang)
                </label>
                <button onclick="window.location='{{ route('home') }}'">Kembali Belanja</button>
            </div>

            @forelse($cartItems as $id => $item)
                <div class="cart-item">
                    <div class="check">
                        <input type="checkbox" checked>
                    </div>
                    <div class="product-image">
                        @if(!empty($item['image']))
                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                 onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="{{ $item['name'] }}"
                                 onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
                        @endif
                    </div>
                    <div class="product-info">
                        <h3>{{ $item['name'] }}</h3>
                        <p>Harga Satuan: Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                        <div class="product-action">
                            <button class="delete"><i class="fa-regular fa-trash-can"></i> Hapus</button>
                        </div>
                    </div>
                    <div class="quantity">
                        <button>−</button>
                        <input type="number" value="{{ $item['quantity'] }}" min="1" readonly>
                        <button>+</button>
                    </div>
                    <div class="price">
                        <h4>Rp{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            @empty
                <div style="background:#fff; padding:60px 30px; border-radius:25px; text-align:center; box-shadow:0 15px 35px rgba(0,0,0,.05);">
                    <i class="fa-solid fa-cart-shopping" style="font-size:56px; color:#ccc; margin-bottom:20px;"></i>
                    <h3 style="font-size:22px; margin-bottom:10px; color:#444;">Keranjang Belanja Masih Kosong</h3>
                    <p style="color:#888; margin-bottom:25px;">Yuk isi keranjangmu dengan produk frozen food lezat dari Belfoods!</p>
                    <a href="{{ route('home') }}" style="display:inline-block; padding:14px 30px; background:#5b991d; color:#fff; border-radius:18px; font-weight:600; text-decoration:none;">
                        <i class="fa-solid fa-store"></i> Mulai Belanja
                    </a>
                </div>
            @endforelse
        </div>

        <div class="cart-right">
            {{-- VOUCHER CARD --}}
            <div class="voucher-card">
                <div class="voucher-title">
                    <i class="fas fa-tag"></i>
                    <h3>Gunakan Voucher</h3>
                </div>
                <div class="voucher-input">
                    <input type="text" id="promoCode" placeholder="Masukkan kode promo...">
                    <button type="button">Pakai</button>
                </div>
            </div>

            {{-- SUMMARY CARD --}}
            <div class="summary-card">
                <h2>Ringkasan Belanja</h2>

                <div class="summary-row">
                    <span>Subtotal ({{ $totalItems }} produk)</span>
                    <b>Rp{{ number_format($subtotal, 0, ',', '.') }}</b>
                </div>

                <div class="summary-row">
                    <span>Metode Ambil</span>
                    <b style="color:#5b991d;"><i class="fa-solid fa-store"></i> Self Pickup (Ambil Sendiri)</b>
                </div>

                @if($discount > 0)
                    <div class="summary-row discount">
                        <span>Diskon Promo</span>
                        <b>- Rp{{ number_format($discount, 0, ',', '.') }}</b>
                    </div>
                @endif

                <div class="line"></div>

                <div class="summary-total">
                    <span>Total Pembayaran</span>
                    <h3>Rp{{ number_format($total, 0, ',', '.') }}</h3>
                </div>

                @if($subtotal > 0)
                    <a href="{{ route('payment') }}" class="checkout-btn">
                        <i class="fas fa-qrcode"></i> Bayar via QRIS
                    </a>
                @else
                    <a href="{{ route('home') }}" class="checkout-btn" style="opacity:0.6; pointer-events:none;">
                        Keranjang Kosong
                    </a>
                @endif

                <div class="guarantee">
                    <div class="item">
                        <i class="fas fa-shield-heart"></i>
                        <div>
                            <strong>Pembayaran QRIS Aman</strong>
                            <p>Upload bukti bayar & verifikasi admin</p>
                        </div>
                    </div>
                    <div class="item">
                        <i class="fas fa-store"></i>
                        <div>
                            <strong>Self Pickup Toko</strong>
                            <p>Ambil pesanan langsung di toko Belfoods</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>