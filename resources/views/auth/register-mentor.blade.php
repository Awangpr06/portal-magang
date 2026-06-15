<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Mentor</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; font-family: Arial, sans-serif; background: #fffaf0; color: #3a2b14; }
        .page { min-height: 100vh; padding: 32px 18px; background: linear-gradient(135deg, rgba(245,169,22,.96), rgba(217,143,10,.9)) top / 100% 310px no-repeat, #fffaf0; }
        .header { max-width: 1080px; margin: 0 auto 22px; color: #fff; }
        .brand { display: inline-flex; align-items: center; margin-bottom: 12px; }
        .brand img { width: 88px; height: 88px; object-fit: contain; }
        .header h1 { font-size: clamp(28px, 4vw, 42px); margin-bottom: 10px; }
        .header p { font-size: 17px; opacity: .95; }
        .form-card { max-width: 1080px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 22px 50px rgba(9,50,68,.16); overflow: hidden; }
        .form-body { padding: 28px; }
        .section { border: 1px solid #dfe9ee; border-radius: 8px; padding: 22px; margin-bottom: 20px; background: #fbfdfe; }
        .section-title { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; font-size: 19px; }
        .section-title span { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; background: #f5a916; color: #2f230c; font-weight: 700; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .field.full { grid-column: 1 / -1; }
        label { display: block; margin-bottom: 7px; font-weight: 700; color: #294554; }
        input, textarea { width: 100%; border: 1px solid #cbd8de; border-radius: 8px; padding: 12px 13px; font: inherit; color: #173241; background: #fff; outline: none; transition: .18s ease; }
        textarea { min-height: 92px; resize: vertical; }
        input:focus, textarea:focus { border-color: #f5a916; box-shadow: 0 0 0 3px rgba(245,169,22,.18); }
        .field.valid input, .field.valid textarea { border-color: #2fb36f; background: #f3fff8; }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 48px; }
        .toggle-password { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: #fff6df; color: #9a6400; border-radius: 8px; width: 34px; height: 34px; cursor: pointer; font-weight: 700; }
        .radio-row { display: flex; flex-wrap: wrap; gap: 14px; min-height: 45px; align-items: center; }
        .radio-option { display: inline-flex; align-items: center; gap: 8px; padding: 10px 12px; border: 1px solid #cbd8de; border-radius: 8px; background: #fff; cursor: pointer; }
        .radio-option input { width: auto; }
        .upload-area { display: grid; grid-template-columns: 150px 1fr; gap: 18px; align-items: center; }
        .photo-preview { width: 138px; height: 138px; border-radius: 8px; border: 2px dashed #a9c2cf; background: #edf6fa; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #65808d; font-weight: 700; text-align: center; padding: 10px; }
        .photo-preview img { width: 100%; height: 100%; object-fit: cover; }
        .file-button { display: inline-block; background: #f5a916; color: #2f230c; border-radius: 8px; padding: 12px 16px; font-weight: 800; cursor: pointer; }
        .file-button input { display: none; }
        .help { margin-top: 9px; color: #637d89; font-size: 14px; }
        .validation-panel { display: flex; align-items: flex-start; gap: 12px; padding: 16px; border-radius: 8px; background: #eaf8f0; border: 1px solid #bce7ce; color: #21603f; margin-bottom: 20px; }
        .validation-panel strong { display: block; margin-bottom: 4px; }
        .error { display: block; margin-top: 6px; color: #c83a3a; font-size: 13px; }
        .actions { display: flex; justify-content: flex-end; gap: 12px; padding-top: 4px; }
        .btn { border: 0; border-radius: 8px; padding: 13px 22px; font: inherit; font-weight: 800; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-secondary { background: #e7eef2; color: #244554; }
        .btn-primary { background: #f5a916; color: #2f230c; }
        .success-backdrop { position: fixed; inset: 0; background: rgba(15,38,48,.55); display: flex; align-items: center; justify-content: center; padding: 18px; z-index: 20; }
        .success-card { width: min(520px, 100%); background: #fff; border-radius: 8px; padding: 30px; text-align: center; box-shadow: 0 25px 70px rgba(0,0,0,.22); }
        .check-icon { width: 74px; height: 74px; border-radius: 50%; background: #2fb36f; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 42px; font-weight: 900; margin-bottom: 18px; }
        .account-info { text-align: left; background: #f3f8fb; border-radius: 8px; padding: 16px; margin: 18px 0; line-height: 1.8; }
        @media (max-width: 760px) { .form-body { padding: 18px; } .grid, .upload-area { grid-template-columns: 1fr; } .actions { flex-direction: column-reverse; } .btn { width: 100%; } }
    </style>
</head>
<body>
<main class="page">
    <header class="header">
        <div class="brand">
            <img src="{{ asset('images/logo-lldikti.png') }}" alt="Logo LLDIKTI">
        </div>
        <h1>Pendaftaran Akun Mentor</h1>
        <p>Lengkapi data berikut untuk membuat akun mentor.</p>
    </header>

    <form class="form-card" method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="role" value="mentor">

        <div class="form-body">
            <section class="section">
                <h2 class="section-title"><span>1</span> Data Akun</h2>
                <div class="grid">
                    <div class="field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                        @error('username') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-wrap">
                            <input type="password" id="password" name="password" required>
                            <button class="toggle-password" type="button" data-target="password">L</button>
                        </div>
                        @error('password') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="password_confirmation">Ulangi Password</label>
                        <div class="password-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" required>
                            <button class="toggle-password" type="button" data-target="password_confirmation">L</button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title"><span>2</span> Data Identitas</h2>
                <div class="grid">
                    <div class="field">
                        <label for="nip">NIP</label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required>
                        @error('nip') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label>Jenis Kelamin</label>
                        <div class="radio-row">
                            <label class="radio-option"><input type="radio" name="jenis_kelamin" value="Laki-laki" {{ old('jenis_kelamin') === 'Laki-laki' ? 'checked' : '' }} required> Laki-laki</label>
                            <label class="radio-option"><input type="radio" name="jenis_kelamin" value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'checked' : '' }} required> Perempuan</label>
                        </div>
                        @error('jenis_kelamin') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="no_hp">Nomor Telepon</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required>
                        @error('no_hp') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field full">
                        <label for="alamat">Alamat</label>
                        <textarea id="alamat" name="alamat" required>{{ old('alamat') }}</textarea>
                        @error('alamat') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title"><span>3</span> Data Instansi</h2>
                <div class="grid">
                    <div class="field full">
                        <label>Instansi</label>
                        <div class="radio-row">
                            <label class="radio-option">
                                <input type="radio" name="perguruan_tinggi" value="LLDIKTI Wilayah V Yogyakarta" {{ old('perguruan_tinggi', 'LLDIKTI Wilayah V Yogyakarta') === 'LLDIKTI Wilayah V Yogyakarta' ? 'checked' : '' }} required>
                                LLDIKTI Wilayah V Yogyakarta
                            </label>
                        </div>
                        @error('perguruan_tinggi') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required>
                        @error('jabatan') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field">
                        <label for="divisi">Unit/Bidang Kerja</label>
                        <input type="text" id="divisi" name="divisi" value="{{ old('divisi') }}" required>
                        @error('divisi') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="section">
                <h2 class="section-title"><span>4</span> Upload Dokumen</h2>
                <div class="upload-area">
                    <div class="photo-preview" id="photoPreview">Preview<br>Foto Profil</div>
                    <div>
                        <label class="file-button" for="foto">
                            Browse File
                            <input type="file" id="foto" name="foto" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                        </label>
                        <p class="help">Format file yang diperbolehkan: jpg, jpeg, png. Max. 2MB.</p>
                        @error('foto') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <div class="validation-panel">
                <strong>i</strong>
                <div>
                    <strong>Panel Validasi Data</strong>
                    Seluruh form atau data akan berwarna hijau jika data sudah benar dan terisi.
                </div>
            </div>

            <div class="actions">
                <a class="btn btn-secondary" href="{{ route('register') }}">Batal</a>
                <button class="btn btn-primary" type="submit">Daftar</button>
            </div>
        </div>
    </form>
</main>

@if(session('registration_success'))
    <div class="success-backdrop">
        <div class="success-card">
            <div class="check-icon">✓</div>
            <h2>Pendaftaran Berhasil</h2>
            <p>Akun anda telah berhasil didaftarkan dan sedang menunggu verifikasi dari super admin.</p>
            <div class="account-info">
                <div><strong>Nama Lengkap:</strong> {{ session('registration_success.name') }}</div>
                <div><strong>Username:</strong> {{ session('registration_success.username') }}</div>
                <div><strong>Email:</strong> {{ session('registration_success.email') }}</div>
            </div>
            <div class="actions">
                <a class="btn btn-secondary" href="{{ url('/') }}">Kembali ke Beranda</a>
                <a class="btn btn-primary" href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </div>
@endif

<script>
    const fields = document.querySelectorAll('.field');

    function updateFieldState(field) {
        const inputs = field.querySelectorAll('input, textarea');
        const valid = Array.from(inputs).some((input) => {
            if (input.type === 'radio') {
                return document.querySelector(`input[name="${input.name}"]:checked`);
            }
            if (input.type === 'email') {
                return input.value.trim() && input.checkValidity();
            }
            return input.value.trim().length > 0;
        });
        field.classList.toggle('valid', Boolean(valid));
    }

    fields.forEach((field) => {
        field.querySelectorAll('input, textarea').forEach((input) => {
            input.addEventListener('input', () => updateFieldState(field));
            input.addEventListener('change', () => updateFieldState(field));
        });
        updateFieldState(field);
    });

    document.querySelectorAll('.toggle-password').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
            button.textContent = input.type === 'password' ? 'L' : 'S';
        });
    });

    document.getElementById('foto').addEventListener('change', (event) => {
        const file = event.target.files[0];
        const preview = document.getElementById('photoPreview');
        if (!file) {
            preview.innerHTML = 'Preview<br>Foto Profil';
            return;
        }
        const image = document.createElement('img');
        image.src = URL.createObjectURL(file);
        image.onload = () => URL.revokeObjectURL(image.src);
        preview.innerHTML = '';
        preview.appendChild(image);
    });
</script>
</body>
</html>
