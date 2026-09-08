<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya | Belfoods</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<style>
.avatar {
    position: relative;
    cursor: pointer;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.avatar:hover {
    transform: scale(1.03);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.avatar:hover .edit-photo {
    background: #238b1f;
    transform: scale(1.1);
}
.edit-photo {
    transition: all 0.2s ease;
}
.alert-box {
    padding: 14px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-weight: 500;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
</head>

<body>

<div class="container">

<nav>

<div class="logo">
    <a href="{{ route('home') }}" style="text-decoration:none; color:inherit;">belf<span>oods</span></a>
</div>

<div class="search">
    <form action="{{ route('home') }}" method="GET" style="display:flex; width:100%;">
        <input type="text" name="search" placeholder="Cari produk favoritmu...">
        <button type="submit">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="menu">
    <a href="{{ route('home') }}"><i class="fa-solid fa-store"></i> Toko</a>
    <a href="{{ route('cart') }}"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a>
    <a href="{{ route('orders.index') }}"><i class="fa-solid fa-receipt"></i> Pesanan</a>
    <a href="{{ route('profile') }}" class="active" style="color:#299b1d; font-weight:600;"><i class="fa-regular fa-user"></i> Akun</a>
</div>

</nav>

<div class="wrapper">

<div class="sidebar">

<h3>Menu Akun</h3>

<ul>

<li class="active">
    <a href="#section-profile" style="display:flex; align-items:center; width:100%; height:100%; color:inherit; text-decoration:none;">
        <i class="fa-regular fa-user"></i> Profil Saya
    </a>
</li>

<li>
    <a href="#section-address" style="display:flex; align-items:center; width:100%; height:100%; color:inherit; text-decoration:none;">
        <i class="fa-solid fa-location-dot"></i> Alamat
    </a>
</li>

<li>
    <a href="#section-password" style="display:flex; align-items:center; width:100%; height:100%; color:inherit; text-decoration:none;">
        <i class="fa-solid fa-lock"></i> Ubah Password
    </a>
</li>

<li>
    <a href="{{ route('orders.index') }}" style="display:flex; align-items:center; width:100%; height:100%; color:inherit; text-decoration:none;">
        <i class="fa-solid fa-receipt"></i> Pesanan Saya
    </a>
</li>

<li>
    <a href="{{ route('home') }}" style="display:flex; align-items:center; width:100%; height:100%; color:inherit; text-decoration:none;">
        <i class="fa-solid fa-store"></i> Beranda Toko
    </a>
</li>

<li>
    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="display:flex; align-items:center; width:100%; height:100%; color:#d32f2f; text-decoration:none;">
        <i class="fa-solid fa-right-from-bracket"></i> Keluar
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
</li>

</ul>

</div>

<div class="content">

<!-- PROFILE CARD HEADER -->
<div class="profile-card">

<div class="profile-left">

    {{-- AVATAR PHOTO WITH CLICK-TO-UPLOAD --}}
    <div class="avatar" id="avatarBox" title="Klik untuk ubah foto profil">
        @if(auth()->user()->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists(auth()->user()->profile_photo))
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}" id="avatarImg" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        @else
            <div id="avatarPlaceholder" style="width:100%; height:100%; display:flex; justify-content:center; align-items:center; color:#299b1d; font-size:55px;">
                <i class="fa-solid fa-user"></i>
            </div>
        @endif

        <div class="edit-photo" id="editPhotoBtn" title="Ganti Foto">
            <i class="fa-solid fa-camera"></i>
        </div>
    </div>

    <div class="profile-info">
        <h1>{{ auth()->user()->name }}</h1>
        <p><i class="fa-regular fa-envelope" style="margin-right:6px;"></i> {{ auth()->user()->email }}</p>
        <p><i class="fa-solid fa-phone" style="margin-right:6px;"></i> {{ auth()->user()->phone ?? 'Belum ada nomor telepon' }}</p>
        <div class="badge">
            <i class="fa-solid fa-shield-check" style="margin-right:4px;"></i> Member Belfoods
        </div>
    </div>

</div>

<button type="button" class="edit-btn" onclick="document.getElementById('avatarInput').click();">
    <i class="fa-solid fa-camera"></i> Ganti Foto Profil
</button>

</div>

<!-- FLASH ALERTS -->
@if(session('success'))
<div class="alert-box alert-success">
    <div><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i> {{ session('success') }}</div>
    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#155724; font-size:16px;"><i class="fas fa-times"></i></button>
</div>
@endif

@if(session('error'))
<div class="alert-box alert-danger">
    <div><i class="fa-solid fa-circle-exclamation" style="margin-right:8px;"></i> {{ session('error') }}</div>
    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; cursor:pointer; color:#721c24; font-size:16px;"><i class="fas fa-times"></i></button>
</div>
@endif

@if($errors->any())
<div class="alert-box alert-danger" style="display:block;">
    <div style="margin-bottom:6px; font-weight:700;"><i class="fa-solid fa-triangle-exclamation" style="margin-right:8px;"></i> Terjadi beberapa kesalahan:</div>
    <ul style="margin:0; padding-left:25px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- FORM EDIT PROFIL, ALAMAT, & PASSWORD -->
<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">

    @csrf

    <!-- ================= INFORMASI PRIBADI ================= -->

    <div class="form-card" id="section-profile">

        <h2>Informasi Pribadi</h2>

        <div class="grid">

            <div class="input-group">
                <label>Nama Lengkap <span style="color:#d32f2f;">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
            </div>

            <div class="input-group">
                <label>Email <span style="color:#d32f2f;">*</span></label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
            </div>

            <div class="input-group">
                <label>Nomor Telepon <span style="color:#d32f2f;">*</span></label>
                <input type="text" name="phone" placeholder="Contoh: 081234567890" value="{{ old('phone', auth()->user()->phone) }}" required>
            </div>

            <div class="input-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="birthdate" value="{{ old('birthdate', auth()->user()->birthdate ? \Carbon\Carbon::parse(auth()->user()->birthdate)->format('Y-m-d') : '') }}">
            </div>

            <div class="input-group">
                <label>Jenis Kelamin</label>
                <select name="gender">
                    <option value="">-- Pilih Jenis Kelamin --</option>
                    <option value="Laki-laki" {{ old('gender', auth()->user()->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('gender', auth()->user()->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="input-group">
                <label>Pekerjaan</label>
                <input type="text" name="job" placeholder="Contoh: Mahasiswa, Karyawan" value="{{ old('job', auth()->user()->job) }}">
            </div>

        </div>

        <br>

        <div class="input-group">
            <label>Bio Singkat</label>
            <textarea name="bio" placeholder="Tulis bio singkat...">{{ old('bio', auth()->user()->bio) }}</textarea>
        </div>

        <br>

        <div class="input-group">
            <label>Pilih File Foto Profil Baru (JPG, PNG, WEBP, Maks 2MB)</label>
            <input type="file" name="avatar" id="avatarInput" accept="image/*">
            <small style="color:#666; display:block; margin-top:4px;">Klik foto profil di atas atau pilih file di sini untuk mengganti foto.</small>
        </div>

        <button type="submit" class="save" style="margin-top:20px;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Data Pribadi
        </button>

    </div>

    <!-- ================= ALAMAT ================= -->

    <div class="form-card" id="section-address">

        <h2>Alamat Lengkap</h2>

        <div class="input-group">
            <label>Alamat Lengkap (Jalan, RT/RW, No. Rumah) <span style="color:#d32f2f;">*</span></label>
            <textarea name="address" placeholder="Masukkan alamat lengkap rumah / tempat tinggal..." required>{{ old('address', auth()->user()->address) }}</textarea>
        </div>

        <br>

        <div class="grid">

            <div class="input-group">
                <label>Provinsi</label>
                <input type="text" name="province" placeholder="Contoh: Jawa Barat" value="{{ old('province', auth()->user()->province) }}">
            </div>

            <div class="input-group">
                <label>Kota / Kabupaten</label>
                <input type="text" name="city" placeholder="Contoh: Bandung" value="{{ old('city', auth()->user()->city) }}">
            </div>

            <div class="input-group">
                <label>Kecamatan</label>
                <input type="text" name="district" placeholder="Contoh: Coblong" value="{{ old('district', auth()->user()->district) }}">
            </div>

            <div class="input-group">
                <label>Kode Pos</label>
                <input type="text" name="postal_code" placeholder="Contoh: 40132" value="{{ old('postal_code', auth()->user()->postal_code) }}">
            </div>

        </div>

        <button type="submit" class="save" style="margin-top: 20px;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Alamat
        </button>

    </div>

    <!-- ================= PASSWORD ================= -->

    <div class="form-card" id="section-password">

        <h2>Ubah Password</h2>
        <p style="color:#666; font-size:13px; margin-bottom:20px;">Kosongkan bagian ini jika Anda tidak ingin mengubah password.</p>

        <div class="grid">

            <div class="input-group">
                <label>Password Lama</label>
                <div class="password-box">
                    <input type="password" name="old_password" id="oldPassword" placeholder="Masukkan password lama">
                    <i class="fa-solid fa-eye toggle-password" style="cursor: pointer;"></i>
                </div>
            </div>

            <div class="input-group">
                <label>Password Baru</label>
                <div class="password-box">
                    <input type="password" name="new_password" id="newPassword" placeholder="Minimal 6 karakter">
                    <i class="fa-solid fa-eye toggle-password" style="cursor: pointer;"></i>
                </div>
            </div>

            <div class="input-group">
                <label>Konfirmasi Password Baru</label>
                <div class="password-box">
                    <input type="password" name="new_password_confirmation" id="confirmPassword" placeholder="Ulangi password baru">
                    <i class="fa-solid fa-eye toggle-password" style="cursor: pointer;"></i>
                </div>
            </div>

        </div>

        <button type="submit" class="save" style="margin-top: 20px;">
            <i class="fa-solid fa-lock"></i> Update Password
        </button>

    </div>

</form>

</div>

</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Trigger File Upload when clicking on Avatar or Edit Photo Button
    const avatarBox = document.getElementById("avatarBox");
    const avatarInput = document.getElementById("avatarInput");

    if (avatarBox && avatarInput) {
        avatarBox.addEventListener("click", function () {
            avatarInput.click();
        });
    }

    // 2. Real-time Preview when a new profile photo is selected
    if (avatarInput) {
        avatarInput.addEventListener("change", function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                if (avatarBox) {
                    avatarBox.innerHTML = `
                        <img src="${e.target.result}" id="avatarImg" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <div class="edit-photo" id="editPhotoBtn">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    `;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // 3. Toggle Password Visibility (Eye Icon)
    const togglePasswords = document.querySelectorAll(".toggle-password");
    togglePasswords.forEach(toggle => {
        toggle.addEventListener("click", function () {
            const input = this.parentElement.querySelector("input");
            if (input) {
                if (input.type === "password") {
                    input.type = "text";
                    this.classList.remove("fa-eye");
                    this.classList.add("fa-eye-slash");
                } else {
                    input.type = "password";
                    this.classList.remove("fa-eye-slash");
                    this.classList.add("fa-eye");
                }
            }
        });
    });
});
</script>

</body>

</html>