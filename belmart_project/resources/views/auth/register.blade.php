<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Belfoods Store</title>
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
        <h1>Bergabung <span>Bersama</span> Belfoods</h1>
        <p>Daftar sekarang untuk menikmati berbagai promo diskon menarik, kemudahan pemesanan, dan produk frozen food lezat berkualitas setiap hari.</p>
        <div class="feature-list">
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-gift"></i></div>
                <div><h4>Promo Spesial Member</h4><span>Diskon & penawaran eksklusif</span></div>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-award"></i></div>
                <div><h4>100% Halal & Higienis</h4><span>Teruji aman & bergizi</span></div>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <div><h4>Keamanan Terjamin</h4><span>Data pribadi terlindungi</span></div>
            </div>
        </div>
    </div>

    <div class="right-side">
        <div class="login-card">
            <div class="top-icon"><i class="fa-solid fa-user-plus"></i></div>
            <h2>Buat Akun Baru</h2>
            <p>Lengkapi data diri Anda untuk mendaftar.</p>

            @if($errors->any())
                <div style="padding:10px 15px; background:#f8d7da; color:#721c24; border-radius:8px; margin-bottom:15px; font-size:13px;">
                    <ul style="margin:0; padding-left:15px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.process') }}" method="POST">
                @csrf
                <div class="input-group">
                    <label>Nama Lengkap</label>
                    <div class="input-box">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Nomor HP / WhatsApp</label>
                    <div class="input-box">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                    </div>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input id="password" type="password" name="password" placeholder="Minimal 6 karakter" required>
                        <button type="button" id="togglePassword" class="show-password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="input-group">
                    <label>Konfirmasi Password</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password" required>
                        <button type="button" id="toggleConfirmPassword" class="show-password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button class="login-button" type="submit">
                    Daftar Sekarang <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="register">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>
</div>

<script>
    function setupToggle(buttonId, inputId) {
        const toggleBtn = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        if (toggleBtn && input) {
            toggleBtn.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }
    }
    setupToggle('togglePassword', 'password');
    setupToggle('toggleConfirmPassword', 'password_confirmation');
</script>
</body>
</html>
