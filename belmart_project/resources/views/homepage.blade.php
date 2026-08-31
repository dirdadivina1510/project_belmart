@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

    <!-- HERO BANNER -->
    <section class="hero-banner-full" style="position:relative; width:100%; border-radius:24px; border: 6px solid #4CAF50; overflow:hidden; margin-bottom:30px; box-shadow:0 10px 30px rgba(0,0,0,0.08); background-color:#e8f5e9;">
        <img src="{{ asset('images/banner.jpg') }}" alt="Belfoods Banner Spesial" style="width:100%; height:auto; display:block; object-fit:cover;"
             onerror="this.onerror=null; this.src='{{ asset('assets/images/banner.jpg') }}';">

        <!-- Hiasan Lengkungan Hijau (Green Decorations) -->
        <div style="position:absolute; top:-30px; left:-30px; width:150px; height:150px; background:rgba(76, 175, 80, 0.4); border-radius:50%; filter:blur(20px); z-index:1;"></div>
        <div style="position:absolute; bottom:-30px; right:-30px; width:200px; height:200px; background:rgba(56, 142, 60, 0.5); border-radius:50%; filter:blur(25px); z-index:1;"></div>
        <div style="position:absolute; top:20px; right:50px; width:100px; height:100px; background:rgba(129, 199, 132, 0.4); border-radius:50%; filter:blur(15px); z-index:1;"></div>

    </section>

    <!-- FEATURE BOX -->
    <div class="feature-box">
        <div class="feature">
            <i class="fa-solid fa-shield-heart"></i>
            <div>
                <h4>Kualitas Terjamin</h4>
                <p>Fresh Frozen, higienis</p>
            </div>
        </div>
        <div class="feature">
            <i class="fa-solid fa-store"></i>
            <div>
                <h4>Self Pickup</h4>
                <p>Ambil langsung di toko</p>
            </div>
        </div>
        <div class="feature">
            <i class="fa-solid fa-wallet"></i>
            <div>
                <h4>Bayar QRIS</h4>
                <p>Mudah & Praktis</p>
            </div>
        </div>
        <div class="feature">
            <i class="fa-solid fa-snowflake"></i>
            <div>
                <h4>Cold Chain</h4>
                <p>Suhu selalu terjaga</p>
            </div>
        </div>
    </div>

    <!-- TITLE ROW -->
    <div class="title-row">
        <h2>Produk Unggulan</h2>
        <a href="{{ route('products.nugget') }}">Lihat Semua →</a>
    </div>

    <!-- CHIP FILTER -->
    <div class="chip-group">
        <span class="{{ !request('filter') ? 'active' : '' }}" onclick="window.location='{{ route('home') }}'">Semua Produk</span>
        <span class="{{ request('filter') == 'best_seller' ? 'active' : '' }}" onclick="window.location='{{ route('home', ['filter' => 'best_seller']) }}'">⭐ Best Seller</span>
        <span class="{{ request('filter') == 'premium' ? 'active' : '' }}" onclick="window.location='{{ route('home', ['filter' => 'premium']) }}'">👑 Premium</span>
        <span class="{{ request('filter') == 'hemat' ? 'active' : '' }}" onclick="window.location='{{ route('home', ['filter' => 'hemat']) }}'">🏷️ Hemat</span>
    </div>

    <!-- PRODUCT GRID -->
    <div class="product-grid">
        @forelse($products ?? [] as $product)
            <div class="product-card" style="cursor:pointer;">
                <div class="product-image" onclick="window.location='{{ route('products.show', $product->id) }}'">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="{{ $product->name }}"
                             onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
                    @endif
                </div>

                @if($product->is_best_seller)
                    <span class="discount" style="background:#e63946; color:#fff; padding:6px 12px; border-radius:12px; font-weight:700; font-size:12px;">⭐ Best Seller</span>
                @elseif($product->is_premium)
                    <span class="premium" style="background:#f59e0b; color:#fff; padding:6px 12px; border-radius:12px; font-weight:700; font-size:12px;">👑 Premium</span>
                @elseif($product->is_hemat)
                    <span class="discount" style="background:#10b981; color:#fff; padding:6px 12px; border-radius:12px; font-weight:700; font-size:12px;">🏷️ Hemat</span>
                @endif

                <button class="wishlist" onclick="this.classList.toggle('active'); this.querySelector('i').classList.toggle('fa-solid'); this.querySelector('i').classList.toggle('fa-regular');">
                    <i class="fa-regular fa-heart"></i>
                </button>

                <div class="product-body">
                    <h3>
                        <a href="{{ route('products.show', $product->id) }}" style="color:inherit; text-decoration:none;">
                            {{ $product->name }}
                        </a>
                    </h3>
                    @if($product->shelf_life)
                        <span class="weight">{{ $product->shelf_life }}</span>
                    @endif
                    <div class="bottom">
                        <span class="price">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                        <form action="{{ route('products.addToCart', $product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="cart-btn" title="Tambah ke keranjang">
                                <i class="fa-solid fa-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1; text-align:center; padding:50px 20px;">
                <i class="fa-solid fa-box-open" style="font-size:48px; color:#ccc; margin-bottom:15px;"></i>
                <h3>Belum ada produk tersedia</h3>
                <p style="color:#777; margin-top:5px;">Silakan kunjungi kategori produk kami di menu sebelah kiri.</p>
            </div>
        @endforelse
    </div>

    <!-- TODAY PROMO -->
    <div class="today-promo">
        <div>
            <h3>🎁 Promo Hari Ini</h3>
            <p>Gunakan kode promo kami untuk diskon eksklusif produk pilihan!</p>
        </div>
        <button onclick="window.location='{{ route('home') }}'">Lihat Promo</button>
    </div>

    <!-- BOTTOM FEATURE -->
    <div class="bottom-feature">
        <div class="bottom-card">
            <div class="icon"><i class="fa-solid fa-award"></i></div>
            <div>
                <h4>Produk Berkualitas</h4>
                <p>Bahan baku pilihan, proses higienis, dan bersertifikat BPOM & Halal MUI.</p>
            </div>
        </div>
        <div class="bottom-card">
            <div class="icon"><i class="fa-solid fa-store"></i></div>
            <div>
                <h4>Self Pickup Toko</h4>
                <p>Ambil langsung pesananmu di toko Belfoods secara cepat & praktis.</p>
            </div>
        </div>
        <div class="bottom-card">
            <div class="icon"><i class="fa-solid fa-qrcode"></i></div>
            <div>
                <h4>Bayar QRIS</h4>
                <p>Cukup scan, bayar instan dengan berbagai dompet digital.</p>
            </div>
        </div>
        <div class="bottom-card">
            <div class="icon"><i class="fa-solid fa-headset"></i></div>
            <div>
                <h4>Customer Service</h4>
                <p>Tim kami siap membantu 08.00 – 20.00 WIB setiap harinya.</p>
            </div>
        </div>
    </div>

@endsection