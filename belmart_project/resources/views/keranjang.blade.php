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
    <style>
        .flash-alert {
            max-width: 1450px;
            margin: 15px auto;
            width: 92%;
            padding: 14px 22px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 500;
            animation: fadeIn 0.3s ease;
        }
        .flash-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        .flash-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .flash-close {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: inherit;
            opacity: 0.7;
            padding: 0 4px;
        }
        .flash-close:hover {
            opacity: 1;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

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
            $item['stock'] = $dbProd->stock;
            $item['is_active'] = $dbProd->is_active;
        } else {
            $item['stock'] = 0;
            $item['is_active'] = false;
        }
        $cartItems[$id] = $item;
    }

    $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
    $totalItems = collect($cartItems)->sum('quantity');

    $promoSession = session('cart_promo') ?? session('checkout_promo');
    $discount = 0;
    if ($promoSession && isset($promoSession['discount'])) {
        $discount = min((float)$promoSession['discount'], $subtotal);
    }
    $total = max(0, $subtotal - $discount);
@endphp

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
                @if($totalItems > 0)
                    <span class="badge">{{ $totalItems }}</span>
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
                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk
                </a>
            @endauth
        </nav>
    </div>
</header>

<!-- FLASH ALERTS -->
@if(session('success'))
    <div class="flash-alert flash-success">
        <div>
            <i class="fas fa-check-circle" style="margin-right:8px;"></i> {{ session('success') }}
        </div>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div class="flash-alert flash-error">
        <div>
            <i class="fas fa-exclamation-circle" style="margin-right:8px;"></i> {{ session('error') }}
        </div>
        <button type="button" class="flash-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
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
    <div class="container">
        <div class="cart-left">
            <div class="cart-header">
                <label style="cursor:pointer;">
                    <input type="checkbox" id="selectAllCheckbox" checked> Pilih Semua ({{ $totalItems }} Barang)
                </label>
                <div style="display:flex; gap:10px; align-items:center;">
                    @if(count($cartItems) > 0)
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan semua produk di keranjang?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="border:none; background:#FFF0F0; color:#E74C3C; padding:10px 16px; border-radius:12px; font-weight:600; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:6px;">
                                <i class="fa-regular fa-trash-can"></i> Kosongkan Keranjang
                            </button>
                        </form>
                    @endif
                    <button onclick="window.location='{{ route('home') }}'" style="border:none; background:#F4F7EF; color:#444; padding:10px 16px; border-radius:12px; font-weight:600; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:6px;">
                        <i class="fa-solid fa-store"></i> Belanja Lagi
                    </button>
                </div>
            </div>

            @forelse($cartItems as $id => $item)
                <div class="cart-item">
                    <div class="check">
                        <input type="checkbox" class="item-check" checked>
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
                        <p style="margin-bottom:6px;">Harga Satuan: Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                        @if(isset($item['stock']))
                            <p style="font-size:12px; color: {{ $item['stock'] <= 5 ? '#e65100' : '#777' }}; margin-bottom:12px;">
                                <i class="fa-solid fa-boxes-stacked"></i> Stok tersedia: {{ $item['stock'] }}
                            </p>
                        @endif
                        <div class="product-action">
                            <form action="{{ route('cart.remove', $id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ addslashes($item['name']) }} dari keranjang?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete" title="Hapus produk dari keranjang">
                                    <i class="fa-regular fa-trash-can"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="quantity">
                        {{-- DECREASE BUTTON FORM --}}
                        <form action="{{ route('cart.update', $id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit" title="Kurangi 1">−</button>
                        </form>

                        <input type="number" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] ?? 999 }}" readonly>

                        {{-- INCREASE BUTTON FORM --}}
                        <form action="{{ route('cart.update', $id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="increase">
                            <button type="submit" title="Tambah 1" {{ isset($item['stock']) && $item['quantity'] >= $item['stock'] ? 'disabled' : '' }}>+</button>
                        </form>
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
                <form action="{{ route('cart.promo.apply') }}" method="POST" class="voucher-input" style="margin-bottom:0;">
                    @csrf
                    <input type="text" name="promo_code" id="promoCode" placeholder="Masukkan kode promo..." value="{{ $promoSession['code'] ?? '' }}" required style="text-transform:uppercase;">
                    <button type="submit">Pakai</button>
                </form>
                @if($promoSession)
                    <div style="margin-top:14px; padding:12px 16px; background:#f0f9eb; border:1px solid #cce8b5; color:#2e7d32; border-radius:14px; display:flex; justify-content:space-between; align-items:center; font-size:13px;">
                        <div>
                            <i class="fas fa-check-circle" style="color:#5b991d; margin-right:6px;"></i>
                            Voucher <strong>{{ $promoSession['code'] }}</strong> aktif (-Rp{{ number_format($discount, 0, ',', '.') }})
                        </div>
                        <form action="{{ route('cart.promo.remove') }}" method="POST" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none; border:none; color:#c62828; cursor:pointer; font-weight:600; font-size:13px; text-decoration:underline;">Hapus</button>
                        </form>
                    </div>
                @endif
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
                        <b style="color:#2e7d32;">- Rp{{ number_format($discount, 0, ',', '.') }}</b>
                    </div>
                @endif

                <div class="line"></div>

                <div class="summary-total">
                    <span>Total Pembayaran</span>
                    <h3>Rp{{ number_format($total, 0, ',', '.') }}</h3>
                </div>

                @if($subtotal > 0)
                    @auth
                        <a href="{{ route('payment') }}" class="checkout-btn">
                            <i class="fas fa-qrcode"></i> Bayar via QRIS
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="checkout-btn" style="background: linear-gradient(135deg, #5b991d, #427514); text-align: center; text-decoration: none;">
                            <i class="fas fa-right-to-bracket"></i> Login / Daftar untuk Checkout
                        </a>
                        <p style="font-size: 13px; color: #666; text-align: center; margin-top: 10px;">
                            Wajib masuk akun sebelum checkout. Belum punya akun? <a href="{{ route('register') }}" style="color: #5b991d; font-weight: 700; text-decoration: underline;">Daftar di sini</a>
                        </p>
                    @endauth
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const itemCheckboxes = document.querySelectorAll('.item-check');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(function(cb) {
                cb.checked = selectAllCheckbox.checked;
            });
        });
    }

    itemCheckboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            if (!this.checked && selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            } else if (selectAllCheckbox) {
                const allChecked = Array.from(itemCheckboxes).every(function(c) {
                    return c.checked;
                });
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
});
</script>

</body>
</html>