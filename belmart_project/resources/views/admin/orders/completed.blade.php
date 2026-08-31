@extends('layouts.admin')

@section('title', 'Riwayat Order')

@section('content')

<head>
    <link rel="stylesheet" href="{{ asset('css/listdone.css') }}">
</head>

<div class="history-page">
    <div class="page-header">
        <div>
            <h2>Riwayat Order Selesai</h2>
            <p>Lihat seluruh pesanan yang telah selesai.</p>
        </div>
        <div class="header-action">
            <button class="btn-export" onclick="window.print();">
                <i class="fa-solid fa-file-export"></i> Export / Cetak
            </button>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form action="{{ route('admin.orders.completed') }}" method="GET" class="filter-card" style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID Order / Nama Customer">
        </div>

        <button type="submit" class="btn-filter" style="padding:10px 18px; background:#e63946; color:#fff; border:none; border-radius:8px; cursor:pointer;">
            <i class="fa-solid fa-filter"></i> Filter
        </button>

        @if(request()->has('search'))
            <a href="{{ route('admin.orders.completed') }}" style="text-decoration:none; padding:10px; color:#333;">Reset</a>
        @endif
    </form>

    {{-- TABLE --}}
    <div class="table-card" style="overflow-x:auto;">
        <table class="history-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Order</th>
                    <th>Customer</th>
                    <th>Tanggal Selesai</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
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
                            {{ $order->updated_at->format('d M Y') }}
                            <br>
                            <small>{{ $order->updated_at->format('H:i') }} WIB</small>
                        </td>
                        <td>
                            @foreach($order->items as $item)
                                {{ $item->product_name }} ({{ $item->quantity }})@if(!$loop->last), <br>@endif
                            @endforeach
                        </td>
                        <td><strong>Rp{{ number_format($order->total, 0, ',', '.') }}</strong></td>
                        <td>
                            <span class="payment" style="text-transform:uppercase;">{{ $order->payment_method }}</span>
                        </td>
                        <td>
                            <span class="status success">Selesai</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:30px; color:#777;">
                            <i class="fa-solid fa-clock-rotate-left" style="font-size:32px; margin-bottom:10px;"></i>
                            <p>Belum ada riwayat order selesai.</p>
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
@endsection
