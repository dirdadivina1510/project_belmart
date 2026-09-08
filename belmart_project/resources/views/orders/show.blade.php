@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div style="max-width:900px; margin:30px auto; padding:0 20px;">
    <!-- BREADCRUMB -->
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:#777;">
        <a href="{{ route('home') }}" style="color:#5b991d; text-decoration:none;">Beranda</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
        <a href="{{ route('orders.index') }}" style="color:#5b991d; text-decoration:none;">Pesanan Saya</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
        <span>#{{ $order->order_number }}</span>
    </div>

    <div style="background:#fff; border-radius:24px; padding:35px; border:1px solid #eee; box-shadow:0 10px 30px rgba(0,0,0,0.04);">
        <!-- HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; border-bottom:1px solid #eee; padding-bottom:20px; margin-bottom:25px;">
            <div>
                <span style="font-size:13px; color:#777;">ID Pesanan</span>
                <h1 style="font-size:22px; font-weight:800; color:#222; margin:4px 0 0;">#{{ $order->order_number }}</h1>
                <span style="font-size:12px; color:#888;">
                    <i class="fa-regular fa-calendar-days"></i> {{ $order->created_at->format('d M Y, H:i') }} WIB
                </span>
            </div>

            <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                @if($order->payment_status === 'paid')
                    <span style="background:#d1fae5; color:#065f46; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        <i class="fa-solid fa-circle-check"></i> Pembayaran Lunas (ACC)
                    </span>
                @elseif($order->payment_status === 'waiting')
                    <span style="background:#fef3c7; color:#92400e; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        <i class="fa-solid fa-clock"></i> Menunggu Verifikasi Admin
                    </span>
                @elseif($order->payment_status === 'rejected')
                    <span style="background:#fee2e2; color:#991b1b; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        <i class="fa-solid fa-circle-xmark"></i> Pembayaran Ditolak
                    </span>
                @else
                    <span style="background:#f3f4f6; color:#4b5563; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        <i class="fa-solid fa-clock"></i> Belum Bayar
                    </span>
                @endif

                @if($order->status === 'completed')
                    <span style="background:#10b981; color:#fff; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        Selesai Diambil
                    </span>
                @elseif($order->status === 'processing')
                    <span style="background:#3b82f6; color:#fff; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        Siap Diambil di Toko
                    </span>
                @elseif($order->status === 'cancelled')
                    <span style="background:#ef4444; color:#fff; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        Dibatalkan
                    </span>
                @else
                    <span style="background:#f59e0b; color:#fff; padding:6px 16px; border-radius:30px; font-size:13px; font-weight:700;">
                        Menunggu Proses
                    </span>
                @endif
            </div>
        </div>

        <!-- ITEMS -->
        <h3 style="font-size:16px; font-weight:700; color:#333; margin-bottom:15px;">Daftar Produk</h3>
        <div style="background:#f9fbf8; border:1px solid #e2ebd9; border-radius:16px; padding:20px; margin-bottom:25px;">
            @foreach($order->items as $item)
                <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 0; {{ !$loop->last ? 'border-bottom:1px dashed #e2ebd9;' : '' }}">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div style="width:42px; height:42px; background:#eef9e7; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#5b991d; font-size:16px;">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <div style="font-weight:600; color:#222; font-size:14px;">{{ $item->product_name }}</div>
                            <span style="font-size:13px; color:#777;">{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <strong style="color:#5b991d; font-size:15px;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                </div>
            @endforeach
        </div>

        <!-- INFO GRID -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:25px;">
            <!-- PICKUP INFO -->
            <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:20px;">
                <h4 style="font-size:14px; font-weight:700; color:#444; margin:0 0 12px; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-store" style="color:#5b991d;"></i> Informasi Pengambilan
                </h4>
                <div style="font-size:13px; color:#666; line-height:1.7;">
                    <div><strong>Nama:</strong> {{ $order->shipping_name ?? $order->user->name }}</div>
                    <div><strong>No. Telp:</strong> {{ $order->shipping_phone ?? $order->user->phone ?? '-' }}</div>
                    <div><strong>Metode:</strong> Self Pickup (Toko Belfoods)</div>
                    <div><strong>Lokasi:</strong> {{ $order->shipping_address ?? 'Store Belfoods Official' }}</div>
                </div>
            </div>

            <!-- PAYMENT SUMMARY -->
            <div style="background:#fff; border:1px solid #eee; border-radius:16px; padding:20px;">
                <h4 style="font-size:14px; font-weight:700; color:#444; margin:0 0 12px; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-receipt" style="color:#5b991d;"></i> Rincian Pembayaran
                </h4>
                <div style="font-size:13px; color:#666; line-height:1.8;">
                    <div style="display:flex; justify-content:space-between;">
                        <span>Subtotal:</span>
                        <strong>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</strong>
                    </div>
                    @if($order->discount > 0)
                        <div style="display:flex; justify-content:space-between; color:#10b981;">
                            <span>Diskon Promo:</span>
                            <strong>-Rp{{ number_format($order->discount, 0, ',', '.') }}</strong>
                        </div>
                    @endif
                    <div style="display:flex; justify-content:space-between; border-top:1px solid #eee; padding-top:8px; margin-top:8px; font-size:15px; color:#222;">
                        <strong>Total:</strong>
                        <strong style="color:#5b991d; font-size:17px;">Rp{{ number_format($order->total, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAYMENT PROOF PREVIEW -->
        @php
            $proofPath = $order->payment->proof_image ?? $order->payment->payment_proof ?? null;
        @endphp
        @if($proofPath)
            <div style="background:#fafafa; border:1px solid #eee; border-radius:16px; padding:20px; margin-bottom:25px; text-align:center;">
                <h4 style="font-size:14px; font-weight:700; color:#444; margin:0 0 12px;">Bukti Pembayaran yang Dikirim</h4>
                <img src="{{ asset('storage/' . $proofPath) }}" alt="Bukti Pembayaran" style="max-height:280px; max-width:100%; border-radius:12px; border:1px solid #ddd; object-fit:contain;">
            </div>
        @endif

        <!-- ACTION BUTTONS -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-top:10px;">
            <a href="{{ route('orders.index') }}" style="display:inline-flex; align-items:center; gap:8px; color:#666; text-decoration:none; font-size:14px; font-weight:600;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat Pesanan
            </a>
            <a href="{{ route('home') }}" style="padding:12px 24px; background:#5b991d; color:#fff; border-radius:12px; font-weight:700; text-decoration:none; font-size:14px;">
                Belanja Lagi
            </a>
        </div>
    </div>
</div>
@endsection
