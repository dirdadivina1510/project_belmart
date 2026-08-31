<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pembayaran Berhasil | Belfoods</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/donepay.css') }}">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body>

<div class="success-page">

<div class="success-card">

<div class="success-icon">

<i class="fas fa-circle-check"></i>

</div>

<h1>

Pembayaran Berhasil 🎉

</h1>

<p>

Terima kasih telah berbelanja di
<strong>Belfoods Store.</strong>

Pembayaran QRIS berhasil diverifikasi
dan pesanan sedang diproses.

</p>

<div class="order-number">

<span>No. Pesanan</span>

<h2>

#{{ $order->order_number ?? 'BF' . date('YmdHis') }}

</h2>

</div>

<div class="detail">

<div>

<span>Total Pembayaran</span>

<strong>

Rp{{ number_format($order->total ?? 0, 0, ',', '.') }}

</strong>

</div>

<div>

<span>Metode</span>

<strong>

QRIS (Self Pickup)

</strong>

</div>

<div>

<span>Status</span>

<strong class="green" style="color:#e67e22;">

Menunggu Verifikasi Admin

</strong>

</div>

</div>

<div class="timeline">

<div class="step active">

<i class="fas fa-circle-check"></i>

Pembayaran Berhasil

</div>

<div class="line"></div>

<div class="step">

<i class="fas fa-box"></i>

Pesanan Diproses

</div>

<div class="line"></div>

<div class="step">

<i class="fas fa-truck"></i>

Dikirim

</div>

<div class="line"></div>

<div class="step">

<i class="fas fa-house"></i>

Selesai

</div>

</div>

<div class="button-group">

<a href="{{ route('home') }}" class="home">

<i class="fas fa-house"></i>

Kembali ke Home

</a>

<a href="{{ route('orders.index') }}" class="order">

<i class="fas fa-box-open"></i>

Lihat Pesanan

</a>

</div>

</div>

</div>

</body>

</html>