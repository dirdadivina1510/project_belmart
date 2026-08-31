@extends('layouts.app')

@section('title', 'Pembayaran QRIS')

@section('content')
<div style="max-width:850px; margin:40px auto; padding:0 20px;">
    <div style="background:#fff; border-radius:24px; padding:40px; border:1px solid #eee; box-shadow:0 10px 30px rgba(0,0,0,0.05); text-align:center;">
        
        <span style="background:#eef9e7; color:#5b991d; padding:8px 20px; border-radius:30px; font-weight:700; font-size:13px; display:inline-flex; align-items:center; gap:8px;">
            <i class="fa-solid fa-qrcode"></i> Pembayaran QRIS Self-Pickup
        </span>

        <h1 style="font-size:26px; font-weight:700; margin:20px 0 8px; color:#222;">Scan QRIS & Upload Bukti Pembayaran</h1>
        <p style="color:#666; font-size:14px; max-width:550px; margin:0 auto 25px;">
            Silakan scan QRIS di bawah ini melalui m-Banking atau e-Wallet pilihanmu, lalu upload foto/screenshot bukti bayar agar diverifikasi oleh Admin.
        </p>

        <!-- TIMER COUNTER JS -->
        <div style="background:linear-gradient(135deg, #fff5f5, #ffebee); border:1px solid #ffcdd2; border-radius:16px; padding:15px; max-width:420px; margin:0 auto 25px; color:#c62828;">
            <div style="font-size:13px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">Sisa Waktu Pembayaran</div>
            <div id="payment-timer" style="font-size:32px; font-weight:800; margin-top:4px; font-family:monospace; color:#d32f2f;">15:00</div>
            <small style="font-size:11px; color:#b71c1c;">Selesaikan pembayaran sebelum timer habis.</small>
        </div>

        <!-- QRIS IMAGE DEMO -->
        <div style="background:#fafafa; border:2px dashed #79C33B; border-radius:20px; padding:25px; display:inline-block; margin-bottom:30px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=BELFOODS_STORE_SELF_PICKUP_QRIS" alt="QRIS Code" style="width:220px; height:220px; object-fit:contain; border-radius:12px;">
            <div style="margin-top:15px; font-weight:700; color:#333; font-size:15px;">Belfoods Official Store</div>
            <div style="font-size:12px; color:#777; margin-top:2px;">NMID: ID1029384756102 (Self Pickup)</div>
        </div>

        <!-- UPLOAD BUKTI BAYAR FORM -->
        <form action="{{ route('payment') }}" method="POST" enctype="multipart/form-data" style="max-width:500px; margin:0 auto; text-align:left; background:#f9fbf8; padding:25px; border-radius:18px; border:1px solid #e2ebd9;">
            @csrf
            <label style="display:block; font-weight:600; color:#333; font-size:14px; margin-bottom:8px;">
                <i class="fa-solid fa-cloud-arrow-up" style="color:#5b991d; margin-right:6px;"></i> Upload Bukti Pembayaran (JPG, PNG, WEBP)
            </label>
            <input type="file" name="payment_proof" accept="image/*" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:10px; background:#fff; margin-bottom:15px;">

            <button type="submit" style="width:100%; padding:14px; background:linear-gradient(135deg, #7FC53D, #5b991d); color:#fff; border:none; border-radius:12px; font-weight:700; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px;">
                <i class="fa-solid fa-paper-plane"></i> Kirim Bukti Bayar
            </button>
        </form>

        <div style="margin-top:25px; font-size:13px; color:#777; display:flex; justify-content:center; gap:20px;">
            <a href="{{ route('home') }}" style="color:#666; text-decoration:none;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Toko</a>
        </div>
    </div>
</div>

<script>
// REAL-TIME 15 MINUTES TIMER COUNTER
(function() {
    let duration = 15 * 60; // 15 minutes in seconds
    const timerDisplay = document.getElementById('payment-timer');

    const countdown = setInterval(function() {
        let minutes = Math.floor(duration / 60);
        let seconds = duration % 60;

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        if (timerDisplay) {
            timerDisplay.textContent = minutes + ":" + seconds;
        }

        if (--duration < 0) {
            clearInterval(countdown);
            if (timerDisplay) {
                timerDisplay.textContent = "00:00 - WAKTU HABIS";
            }
            alert("Waktu pembayaran telah habis. Silakan ulang kembali.");
        }
    }, 1000);
})();
</script>
@endsection