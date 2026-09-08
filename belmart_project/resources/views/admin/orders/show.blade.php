@extends('layouts.admin')

@section('title', 'Detail Order #' . $order->order_number)

@section('content')
<div style="padding:20px;">
    <!-- BREADCRUMB -->
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:#777;">
        <a href="{{ route('admin.dashboard') }}" style="color:#5b991d; text-decoration:none;">Dashboard</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
        <a href="{{ route('admin.orders.index') }}" style="color:#5b991d; text-decoration:none;">Order Masuk</a>
        <i class="fa-solid fa-chevron-right" style="font-size:9px;"></i>
        <span>#{{ $order->order_number }}</span>
    </div>

    <div style="background:#fff; border-radius:18px; padding:30px; box-shadow:0 10px 30px rgba(0,0,0,0.05); border:1px solid #eee;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; border-bottom:1px solid #eee; padding-bottom:20px; margin-bottom:25px;">
            <div>
                <h2>Detail Order #{{ $order->order_number }}</h2>
                <p style="color:#777; margin:4px 0 0; font-size:13px;">Dipesan pada {{ $order->created_at->format('d M Y, H:i') }} WIB</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('admin.orders.index') }}" class="btn" style="background:#eef2f7; color:#333; padding:8px 16px; border-radius:8px; text-decoration:none; font-size:13px;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:25px;">
            <!-- Customer Info -->
            <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:18px;">
                <h4 style="margin:0 0 10px; font-size:15px; color:#333;"><i class="fa-solid fa-user" style="color:#5b991d; margin-right:6px;"></i> Informasi Customer</h4>
                <div style="font-size:13px; color:#555; line-height:1.7;">
                    <div><strong>Nama:</strong> {{ $order->user->name ?? $order->shipping_name }}</div>
                    <div><strong>Email:</strong> {{ $order->user->email ?? '-' }}</div>
                    <div><strong>No. Telp:</strong> {{ $order->shipping_phone ?? $order->user->phone ?? '-' }}</div>
                    <div><strong>Metode Ambil:</strong> Self Pickup (Toko Belfoods)</div>
                </div>
            </div>

            <!-- Status Controls -->
            <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:18px;">
                <h4 style="margin:0 0 10px; font-size:15px; color:#333;"><i class="fa-solid fa-sliders" style="color:#5b991d; margin-right:6px;"></i> Ubah Status</h4>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    <div>
                        <label style="font-size:12px; color:#666; font-weight:600; display:block; margin-bottom:4px;">Status Pembayaran:</label>
                        <form action="{{ route('admin.orders.payment', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="payment_status" onchange="this.form.submit()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; font-weight:600;">
                                <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>❌ Belum Bayar</option>
                                <option value="waiting" {{ $order->payment_status === 'waiting' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>✅ ACC (Lunas)</option>
                                <option value="rejected" {{ $order->payment_status === 'rejected' ? 'selected' : '' }}>🚫 Tolak Pembayaran</option>
                            </select>
                        </form>
                    </div>

                    <div>
                        <label style="font-size:12px; color:#666; font-weight:600; display:block; margin-bottom:4px;">Status Pesanan:</label>
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; font-weight:600;">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses (Siap Ambil)</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <h4 style="margin:0 0 15px; font-size:16px; color:#333;">Produk yang Dipesan</h4>
        <div style="overflow-x:auto; margin-bottom:25px;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#f1f8ed; text-align:left;">
                        <th style="padding:12px 15px; border-bottom:1px solid #e2ebd9;">Produk</th>
                        <th style="padding:12px 15px; border-bottom:1px solid #e2ebd9;">Harga</th>
                        <th style="padding:12px 15px; border-bottom:1px solid #e2ebd9;">Jumlah</th>
                        <th style="padding:12px 15px; border-bottom:1px solid #e2ebd9; text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px 15px; font-weight:600;">{{ $item->product_name }}</td>
                            <td style="padding:12px 15px;">Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td style="padding:12px 15px;">{{ $item->quantity }}</td>
                            <td style="padding:12px 15px; text-align:right; font-weight:700; color:#5b991d;">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="background:#fafafa;">
                        <td colspan="3" style="padding:12px 15px; text-align:right; font-weight:700;">Total Pembayaran:</td>
                        <td style="padding:12px 15px; text-align:right; font-weight:800; color:#5b991d; font-size:16px;">Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bukti Pembayaran -->
        @php
            $proofPath = $order->payment->proof_image ?? $order->payment->payment_proof ?? null;
        @endphp
        @if($proofPath)
            <div style="background:#fafafa; border:1px solid #eee; border-radius:12px; padding:20px; text-align:center;">
                <h4 style="margin:0 0 15px; font-size:15px; color:#333;"><i class="fa-solid fa-receipt" style="color:#5b991d;"></i> Bukti Pembayaran dari Customer</h4>
                <a href="{{ asset('storage/' . $proofPath) }}" target="_blank">
                    <img src="{{ asset('storage/' . $proofPath) }}" alt="Bukti Pembayaran" style="max-height:350px; max-width:100%; border-radius:8px; border:1px solid #ddd; object-fit:contain;">
                </a>
                <p style="font-size:12px; color:#777; margin-top:8px;">Klik gambar untuk melihat ukuran penuh</p>
            </div>
        @endif
    </div>
</div>
@endsection
