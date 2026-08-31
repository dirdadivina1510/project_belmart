<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil Saya | Belfoods</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

</head>

<body>

<div class="container">

<nav>

<div class="logo">
belf<span>oods</span>
</div>

<div class="search">

<input type="text" placeholder="Cari produk favoritmu...">

<button>
<i class="fas fa-search"></i>
</button>

</div>

<div class="menu">

<a href="{{ route('home') }}"><i class="fa-solid fa-store"></i> Toko</a>
<a href="{{ route('cart') }}"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a>
<a href="{{ route('profile') }}"><i class="fa-regular fa-user"></i> Akun</a>

</div>

</nav>

<div class="wrapper">

<div class="sidebar">

<h3>Menu</h3>

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
    <a href="#section-profile" style="display:flex; align-items:center; width:100%; height:100%; color:inherit; text-decoration:none;">
        <i class="fa-solid fa-phone"></i> Nomor Telepon
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

<div class="profile-card">

<div class="profile-left">

<div class="avatar">

<i class="fa-solid fa-user"></i>

<div class="edit-photo">

<i class="fa-solid fa-camera"></i>

</div>

</div>

<div class="profile-info">

<h1>{{ session('profile_name', auth()->check() ? auth()->user()->name : 'Raka Putra') }}</h1>

<p>{{ session('profile_email', auth()->check() ? auth()->user()->email : 'rakaputra@email.com') }}</p>

<p>{{ session('profile_phone', auth()->check() ? auth()->user()->phone : '+62 81234567890') }}</p>

<div class="badge">

Member Belfoods

</div>

</div>

</div>

<button class="edit-btn">

<i class="fa-solid fa-pen"></i>

Edit Profil

</button>

</div>

@if(session('success'))
<div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
</div>
@endif

<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">

    @csrf

    <!-- ================= INFORMASI PRIBADI ================= -->

    <div class="form-card" id="section-profile">

        <h2>Informasi Pribadi</h2>

        <div class="grid">

            <div class="input-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', session('profile_name', auth()->check() ? auth()->user()->name : 'Raka Putra')) }}">
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', session('profile_email', auth()->check() ? auth()->user()->email : 'rakaputra@email.com')) }}">
            </div>

            <div class="input-group">
                <label>Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', session('profile_phone', auth()->check() ? auth()->user()->phone : '+62 81234567890')) }}">
            </div>

            <div class="input-group">
                <label>Tanggal Lahir</label>
                <input type="date" name="birthdate" value="{{ old('birthdate', session('profile_birthdate', '')) }}">
            </div>

            <div class="input-group">
                <label>Jenis Kelamin</label>

                <select name="gender">

                    <option value="Laki-laki" {{ session('profile_gender', 'Laki-laki') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ session('profile_gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>

                </select>

            </div>

            <div class="input-group">
                <label>Pekerjaan</label>
                <input type="text" name="job" placeholder="Contoh : Mahasiswa" value="{{ old('job', session('profile_job', '')) }}">
            </div>

        </div>

        <br>

        <div class="input-group">

            <label>Bio</label>

            <textarea name="bio" placeholder="Tulis bio singkat...">{{ old('bio', session('profile_bio', '')) }}</textarea>

        </div>

        <br>

        <div class="input-group">

            <label>Foto Profil</label>

            <input type="file" name="avatar">

        </div>

        <button type="submit" class="save">

            <i class="fa-solid fa-floppy-disk"></i>

            Simpan Perubahan

        </button>

    </div>

    <!-- ================= ALAMAT ================= -->

    <div class="form-card" id="section-address">

        <h2>Alamat</h2>

        <div class="input-group">

            <label>Alamat Lengkap</label>

            <textarea name="address" placeholder="Masukkan alamat lengkap">{{ old('address', session('profile_address', auth()->check() ? auth()->user()->address : '')) }}</textarea>

        </div>

        <br>

        <div class="grid">

            <div class="input-group">

                <label>Provinsi</label>

                <input type="text" name="province" placeholder="Jawa Barat" value="{{ old('province', session('profile_province', '')) }}">

            </div>

            <div class="input-group">

                <label>Kota / Kabupaten</label>

                <input type="text" name="city" placeholder="Bandung" value="{{ old('city', session('profile_city', '')) }}">

            </div>

            <div class="input-group">

                <label>Kecamatan</label>

                <input type="text" name="district" value="{{ old('district', session('profile_district', '')) }}">

            </div>

            <div class="input-group">

                <label>Kode Pos</label>

                <input type="text" name="postal_code" value="{{ old('postal_code', session('profile_postal_code', '')) }}">

            </div>

        </div>

        <button type="submit" class="save" style="margin-top: 20px;">

            <i class="fa-solid fa-floppy-disk"></i>

            Simpan Alamat

        </button>

    </div>

    <!-- ================= PASSWORD ================= -->

    <div class="form-card" id="section-password">

        <h2>Ubah Password</h2>

        <div class="grid">

            <div class="input-group">

                <label>Password Lama</label>

                <div class="password-box">

                    <input type="password" name="old_password" id="oldPassword">

                    <i class="fa-solid fa-eye toggle-password" style="cursor: pointer;"></i>

                </div>

            </div>

            <div class="input-group">

                <label>Password Baru</label>

                <div class="password-box">

                    <input type="password" name="new_password" id="newPassword">

                    <i class="fa-solid fa-eye toggle-password" style="cursor: pointer;"></i>

                </div>

            </div>

            <div class="input-group">

                <label>Konfirmasi Password</label>

                <div class="password-box">

                    <input type="password" name="new_password_confirmation" id="confirmPassword">

                    <i class="fa-solid fa-eye toggle-password" style="cursor: pointer;"></i>

                </div>

            </div>

        </div>

        <button type="submit" class="save" style="margin-top: 20px;">

            <i class="fa-solid fa-lock"></i>

            Update Password

        </button>

    </div>

</form>

</div>

</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.querySelector(".edit-btn");
    const inputs = document.querySelectorAll(
        '.form-card input:not([type="hidden"]), .form-card textarea, .form-card select'
    );

    // Mula-mula nonaktifkan input sampai tombol "Edit Profil" diklik
    inputs.forEach(input => {
        input.disabled = true;
    });

    if (editBtn) {
        editBtn.addEventListener("click", function (e) {
            e.preventDefault();
            const isEditing = editBtn.dataset.edit === "true";

            if (isEditing) {
                inputs.forEach(input => {
                    input.disabled = true;
                });
                editBtn.innerHTML = '<i class="fa-solid fa-pen"></i> Edit Profil';
                editBtn.dataset.edit = "false";
            } else {
                inputs.forEach(input => {
                    input.disabled = false;
                });
                editBtn.innerHTML = '<i class="fa-solid fa-check"></i> Selesai';
                editBtn.dataset.edit = "true";
                const firstInput = document.querySelector('.form-card input');
                if (firstInput) firstInput.focus();
            }
        });
    }

    // Toggle Password Visibility
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

    // Preview Foto Profil
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const avatar = document.querySelector(".avatar");

    fileInputs.forEach(fileInput => {
        fileInput.addEventListener("change", function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                if (avatar) {
                    avatar.innerHTML = `
                        <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <div class="edit-photo">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                    `;
                }
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>

</body>

</html>