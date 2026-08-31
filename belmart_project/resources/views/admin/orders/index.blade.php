@extends('layouts.admin')

@section('title', 'Order Masuk')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/listorder.css') }}">
</head>

<div class="orders-page">
    <div class="page-header">
        <div>
            <h2>Order Masuk</h2>
            <p>Kelola seluruh pesanan pelanggan Belfoods.</p>
        </div>
        <div class="header-button">
            <button class="btn btn-outline" onclick="window.print();">
                <i class="fa-solid fa-file-export"></i> Cetak / Export
            </button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary" style="text-decoration:none;">
                <i class="fa-solid fa-rotate-right"></i> Refresh
            </a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form action="{{ route('admin.orders.index') }}" method="GET" class="filter-card" style="display:flex; gap:10px; align-items:center; margin-bottom:20px; flex-wrap:wrap;">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Order atau Customer">
        </div>

        <select name="status" style="padding:10px; border-radius:8px; border:1px solid #ccc;">
            <option value="all">Semua Status Order</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Dikirim</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
        </select>

        <select name="payment_status" style="padding:10px; border-radius:8px; border:1px solid #ccc;">
            <option value="all">Semua Status Bayar</option>
            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
            <option value="waiting" {{ request('payment_status') == 'waiting' ? 'selected' : '' }}>Menunggu Verifikasi</option>
            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
            <option value="rejected" {{ request('payment_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
        </select>

        <button type="submit" class="btn btn-filter" style="padding:10px 18px; background:#e63946; color:#fff; border:none; border-radius:8px; cursor:pointer;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if(request()->hasAny(['search', 'status', 'payment_status']))
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline" style="text-decoration:none;">Reset</a>
        @endif
    </form>

    {{-- DYNAMIC ORDERS TABLE --}}
    <div class="table-card" style="overflow-x:auto;">
        <table class="table-order">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Order</th>
                    <th>Customer</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td>{{ $orders->firstItem() + $index }}</td>
                        <td><strong>{{ $order->order_number }}</strong></td>
                        <td>
                            <div class="customer">
                                <div>
                                    <h5>{{ $order->user->name ?? $order->shipping_name }}</h5>
                                    <span>{{ $order->user->phone ?? $order->shipping_phone }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $order->created_at->format('d M Y') }}
                            <br>
                            <small>{{ $order->created_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            @foreach($order->items as $item)
                                {{ $item->product_name }} ({{ $item->quantity }})@if(!$loop->last), <br>@endif
                            @endforeach
                        </td>
                        <td><strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="payment" style="text-transform:uppercase;">{{ $order->payment_method }}</span>
                            <br>
                            <small style="color:{{ $order->payment_status === 'paid' ? '#155724' : ($order->payment_status === 'waiting' ? '#856404' : '#721c24') }}">
                                ({{ ucfirst($order->payment_status) }})
                            </small>
                            <br>
                            @php
                                $proofPath = $order->payment->proof_image ?? $order->payment->payment_proof ?? null;
                            @endphp
                            @if($order->payment && $proofPath)
                                <button type="button" onclick="showProofModal('{{ asset('storage/' . $proofPath) }}')" style="margin-top:6px; padding:3px 8px; background:#10b981; color:#fff; border:none; border-radius:4px; font-size:11px; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="fa-solid fa-receipt"></i> Bukti Bayar
                                </button>
                            @else
                                <span style="font-size:10px; color:#aaa; display:block; margin-top:4px;">Belum upload</span>
                            @endif
                        </td>
                        <td>
                            @if($order->status === 'completed')
                                <span class="status success">Selesai</span>
                            @elseif($order->status === 'processing')
                                <span class="status process">Diproses</span>
                            @elseif($order->status === 'shipped')
                                <span class="status shipping">Dikirim</span>
                            @elseif($order->status === 'cancelled')
                                <span class="status cancel" style="background:#f8d7da; color:#721c24; padding:4px 8px; border-radius:4px;">Dibatalkan</span>
                            @else
                                <span class="status waiting">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:6px;">
                                <form action="{{ route('admin.orders.payment', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <select name="payment_status" onchange="this.form.submit()" style="padding:4px 8px; border-radius:4px; font-size:12px; border:1px solid #10b981; background:#f0fdf4; font-weight:600; cursor:pointer;">
                                        <option value="unpaid" {{ $order->payment_status === 'unpaid' ? 'selected' : '' }}>❌ Belum Bayar</option>
                                        <option value="waiting" {{ $order->payment_status === 'waiting' ? 'selected' : '' }}>⏳ Menunggu Verifikasi</option>
                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>✅ ACC (Lunas)</option>
                                        <option value="rejected" {{ $order->payment_status === 'rejected' ? 'selected' : '' }}>🚫 Tolak Pembayaran</option>
                                    </select>
                                </form>

                                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" style="padding:4px 8px; border-radius:4px; font-size:12px; cursor:pointer;">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses (Siap Ambil)</option>
                                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Selesai (Sudah Diambil)</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                        </select>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center; padding:30px; color:#777;">
                            <i class="fa-solid fa-inbox" style="font-size:32px; margin-bottom:10px;"></i>
                            <p>Belum ada order masuk.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->count() > 0)
        <div style="margin-top:20px;">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<!-- MODAL BUKTI BAYAR -->
<div id="proofModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:99999; justify-content:center; align-items:center; padding:20px;">
    <div style="background:#fff; border-radius:16px; max-width:550px; width:100%; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; position:relative; box-shadow:0 20px 50px rgba(0,0,0,0.3);">
        <div style="padding:15px 20px; background:#f8fafc; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:16px; color:#334155;"><i class="fa-solid fa-receipt" style="color:#10b981; margin-right:6px;"></i> Bukti Pembayaran QRIS</h3>
            <button type="button" onclick="closeProofModal()" style="background:none; border:none; font-size:20px; color:#64748b; cursor:pointer;">&times;</button>
        </div>
        <div style="padding:20px; overflow-y:auto; text-align:center; background:#0f172a;">
            <img id="modalProofImg" src="" alt="Bukti Pembayaran" style="max-width:100%; max-height:70vh; object-fit:contain; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.5);">
        </div>
        <div style="padding:12px 20px; background:#f8fafc; border-top:1px solid #e2e8f0; text-align:right;">
            <button type="button" onclick="closeProofModal()" style="padding:8px 18px; background:#64748b; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Tutup</button>
        </div>
    </div>
</div>

<script>
function showProofModal(imgUrl) {
    document.getElementById('modalProofImg').src = imgUrl;
    document.getElementById('proofModal').style.display = 'flex';
}
function closeProofModal() {
    document.getElementById('proofModal').style.display = 'none';
    document.getElementById('modalProofImg').src = '';
}
</script>
@endsection
