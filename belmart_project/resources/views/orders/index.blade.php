@extends('layouts.app')

@section('title', 'Riwayat Pesanan Saya')

@section('content')
<div style="max-width:1100px; margin:30px auto; padding:0 20px;">
    <!-- BREADCRUMB -->
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:#777;">
        <a href="{{ route('home') }}" style="color:#5b991d; text-decoration:none;">Beranda</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
        <span>Riwayat Pesanan</span>
    </div>

    <div style="background:#fff; border-radius:20px; padding:30px; border:1px solid #eee; box-shadow:0 10px 30px rgba(0,0,0,0.03);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom:1px solid #eee; padding-bottom:18px;">
            <div>
                <h1 style="font-size:24px; font-weight:700; color:#222; margin-bottom:4px;">
                    <i class="fa-solid fa-receipt" style="color:#5b991d; margin-right:8px;"></i> Riwayat Pesanan Saya
                </h1>
                <p style="color:#777; font-size:14px; margin:0;">Pantau status pesanan dan rincian transaksi Self-Pickup Anda.</p>
            </div>
            <a href="{{ route('home') }}" style="padding:10px 20px; background:#eef9e7; color:#5b991d; border-radius:12px; font-weight:600; text-decoration:none; font-size:13px;">
                + Pesan Lagi
            </a>
        </div>

        @forelse($orders as $order)
            <div style="border:1px solid #e2ebd9; border-radius:16px; margin-bottom:20px; overflow:hidden; background:#fafdf8;">
                <!-- ORDER HEADER -->
                <div style="background:#f1f8ed; padding:15px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; border-bottom:1px solid #e2ebd9;">
                    <div>
                        <span style="font-weight:700; font-size:15px; color:#333;">#{{ $order->order_number }}</span>
                        <span style="font-size:12px; color:#777; margin-left:10px;">
                            <i class="fa-regular fa-clock"></i> {{ $order->created_at->format('d M Y H:i') }} WIB
                        </span>
                    </div>

                    <div style="display:flex; align-items:center; gap:10px;">
                        <!-- PAYMENT STATUS BADGE -->
                        @if($order->payment_status === 'paid')
                            <span style="background:#d1fae5; color:#065f46; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                ✅ Lunas (Verifikasi ACC)
                            </span>
                        @elseif($order->payment_status === 'waiting')
                            <span style="background:#fef3c7; color:#92400e; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                ⏳ Menunggu Verifikasi Admin
                            </span>
                        @elseif($order->payment_status === 'rejected')
                            <span style="background:#fee2e2; color:#991b1b; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                🚫 Pembayaran Ditolak
                            </span>
                        @else
                            <span style="background:#f3f4f6; color:#4b5563; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                ❌ Belum Bayar
                            </span>
                        @endif

                        <!-- ORDER STATUS BADGE -->
                        @if($order->status === 'completed')
                            <span style="background:#10b981; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                Selesai (Sudah Diambil)
                            </span>
                        @elseif($order->status === 'processing')
                            <span style="background:#3b82f6; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                Diproses (Siap Pickup)
                            </span>
                        @elseif($order->status === 'cancelled')
                            <span style="background:#ef4444; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                Dibatalkan
                            </span>
                        @else
                            <span style="background:#f59e0b; color:#fff; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700;">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>

                <!-- ORDER ITEMS -->
                <div style="padding:20px;">
                    @foreach($order->items as $item)
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; padding-bottom:12px; border-bottom:1px dashed #eee;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:45px; height:45px; background:#eef9e7; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#5b991d; font-size:18px;">
                                    <i class="fa-solid fa-cookie-bite"></i>
                                </div>
                                <div>
                                    <h4 style="font-size:15px; font-weight:600; margin:0; color:#333;">{{ $item->product_name }}</h4>
                                    <span style="font-size:13px; color:#777;">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <strong style="color:#5b991d; font-size:15px;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                        </div>
                    @endforeach

                    <!-- ORDER FOOTER SUMMARY -->
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:15px; flex-wrap:wrap; gap:10px;">
                        <div>
                            <span style="font-size:13px; color:#777;">Metode Ambil: </span>
                            <strong style="color:#333; font-size:13px;"><i class="fa-solid fa-store" style="color:#5b991d;"></i> Self Pickup Toko Belfoods</strong>
                        </div>

                        <div style="text-align:right;">
                            <span style="font-size:13px; color:#777;">Total Pesanan: </span>
                            <strong style="font-size:18px; color:#5b991d; font-weight:800;">Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:60px 20px;">
                <i class="fa-solid fa-box-open" style="font-size:60px; color:#ccc; margin-bottom:20px;"></i>
                <h3 style="color:#444; font-size:20px; margin-bottom:8px;">Belum Ada Riwayat Pesanan</h3>
                <p style="color:#777; font-size:14px; margin-bottom:20px;">Anda belum melakukan transaksi pemesanan produk di Belfoods Store.</p>
                <a href="{{ route('home') }}" style="display:inline-block; padding:12px 28px; background:#5b991d; color:#fff; border-radius:14px; font-weight:700; text-decoration:none;">
                    Mulai Belanja Sekarang
                </a>
            </div>
        @endforelse

        @if(method_exists($orders, 'links'))
            <div style="margin-top:25px;">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
