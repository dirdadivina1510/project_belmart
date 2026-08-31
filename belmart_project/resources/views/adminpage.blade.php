@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<!-- Statistik Dashboard -->
<section class="stats">
    <div class="card green">
        <i class="fas fa-cart-shopping"></i>
        <div>
            <span>Total Order</span>
            <h2>{{ number_format($totalOrders ?? 0, 0, ',', '.') }}</h2>
        </div>
    </div>

    <div class="card yellow">
        <i class="fas fa-box"></i>
        <div>
            <span>Total Produk</span>
            <h2>{{ number_format($totalProducts ?? 0, 0, ',', '.') }}</h2>
        </div>
    </div>

    <div class="card orange">
        <i class="fas fa-tags"></i>
        <div>
            <span>Promo Aktif</span>
            <h2>{{ number_format($activePromos ?? 0, 0, ',', '.') }}</h2>
        </div>
    </div>

    <div class="card blue">
        <i class="fas fa-money-bill-wave"></i>
        <div>
            <span>Pendapatan</span>
            <h2>
                @php
                    $rev = $totalRevenue ?? 0;
                    if ($rev >= 1000000000) {
                        $val = $rev / 1000000000;
                        $formattedRev = 'Rp ' . rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ',') . ' M';
                    } elseif ($rev >= 1000000) {
                        $val = $rev / 1000000;
                        $formattedRev = 'Rp ' . rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ',') . ' Jt';
                    } elseif ($rev >= 1000) {
                        $val = $rev / 1000;
                        $formattedRev = 'Rp ' . rtrim(rtrim(number_format($val, 2, ',', '.'), '0'), ',') . ' Rb';
                    } else {
                        $formattedRev = 'Rp ' . number_format($rev, 0, ',', '.');
                    }
                @endphp
                {{ $formattedRev }}
            </h2>
        </div>
    </div>
</section>

<!-- Order Masuk Terbaru -->
<section class="panel" id="order">
    <div class="panel-title">
        <h2>Order Masuk Terbaru</h2>
        <a href="{{ route('admin.orders.index') }}" class="refresh" style="text-decoration:none;">Lihat Semua</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Order</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentOrders ?? [] as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>{{ $order->user->name ?? $order->shipping_name }}</td>
                    <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>
                        <span class="status {{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'process' : 'waiting') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.index') }}" class="accept" style="text-decoration:none; padding:4px 10px; border-radius:4px; font-size:12px;">Proses</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px; color:#777;">Belum ada order terbaru.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<!-- Kelola Produk Terbaru -->
<section class="panel" id="product">
    <div class="panel-title">
        <h2>Produk Terbaru</h2>
        <a href="{{ route('admin.products.create') }}" class="add-btn" style="text-decoration:none;">
            <i class="fas fa-plus"></i> Tambah Produk
        </a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products ?? [] as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category }}</td>
                    <td>Rp{{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}" class="edit" style="text-decoration:none; padding:4px 10px; border-radius:4px; font-size:12px;">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding:20px; color:#777;">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection