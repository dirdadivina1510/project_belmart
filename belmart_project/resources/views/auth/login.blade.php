<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Belfoods Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>

<div class="background">
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>
    <div class="blob blob3"></div>
    <span class="leaf leaf1">🍃</span>
    <span class="leaf leaf2">🍃</span>
    <span class="leaf leaf3">🍃</span>
</div>

<div class="login-container">
    <div class="left-side">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" class="logo" alt="Belfoods" onerror="this.onerror=null; this.src='{{ asset('assets/images/logo.png') }}';">
        </a>
        <h1>Lezatnya <span>Kebaikan</span> Setiap Hari</h1>
        <p>Frozen food berkualitas tinggi untuk keluarga Indonesia. Praktis, lezat, bergizi, siap menemani setiap momen.</p>
        <div class="feature-list">
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-snowflake"></i></div>
                <div><h4>Kualitas Terjaga</h4><span>100% Fresh Frozen</span></div>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-award"></i></div>
                <div><h4>100% Halal & Higienis</h4><span>Teruji aman & bergizi</span></div>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-wallet"></i></div>
                <div><h4>Pembayaran Aman</h4><span>QRIS & E-Wallet</span></div>
            </div>
        </div>
    </div>

    <div class="right-side">
        <div class="login-card">
            <div class="top-icon"><i class="fa-regular fa-heart"></i></div>
            <h2>Selamat Datang Kembali!</h2>
            <p>Masuk untuk melanjutkan belanja produk favoritmu.</p>

            @if(session('error'))
                <div style="padding:10px 15px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:15px; font-size:14px;">
                    <i class="fa-solid fa-circle-exclamation" style="margin-right:5px;"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div style="padding:10px 15px; background:#d4edda; color:#155724; border-radius:8px; margin-bottom:15px; font-size:14px;">
                    <i class="fa-solid fa-circle-check" style="margin-right:5px;"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Email <span style="color:#e63946;">*</span></label>
                    <div class="input-box">
                        <i class="fa-regular fa-user"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required autofocus>
                    </div>
                    @error('email')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="input-group">
                    <label>Password <span style="color:#e63946;">*</span></label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input id="password" type="password" name="password" placeholder="Masukkan password" required>
                        <button type="button" id="togglePassword" class="show-password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <small style="color:#e63946; font-size:12px; display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="option">
                    <label>
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                </div>

                <button class="login-button" type="submit">
                    Masuk <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="register">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a>
            </div>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
</script>
</body>
</html>
