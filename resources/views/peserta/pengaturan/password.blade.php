<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password Peserta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root { --brand:#2a8fbd; --brand-dark:#185f80; --accent:#62bd42; --ink:#163342; --muted:#697b86; --line:#d7eaf2; --page:#f6fbfd; }
        * { box-sizing:border-box; }
        body { min-height:100vh; background:var(--page); color:var(--ink); font-family:Arial, sans-serif; overflow-x:auto; min-width:1200px; }
        .sidebar { width:286px; min-height:100vh; position:fixed; inset:0 auto 0 0; background:var(--brand); color:#fff; overflow-y:auto; padding:18px; z-index:1030; }
        .brand-logo { width:68px; height:68px; object-fit:contain; }
        .profile-photo { width:62px; height:62px; object-fit:cover; border:3px solid rgba(255,255,255,.7); }
        .status-dot { width:9px; height:9px; display:inline-block; border-radius:50%; background:var(--accent); box-shadow:0 0 0 3px rgba(98,189,66,.18); }
        .sidebar a, .sidebar .logout-button { width:100%; display:flex; align-items:center; gap:10px; color:#edf8fc; text-decoration:none; padding:11px 13px; border-radius:8px; border:0; background:transparent; font-size:14px; text-align:left; margin-bottom:5px; }
        .sidebar a:hover, .sidebar a.active, .sidebar .logout-button:hover, .sidebar-parent.active { background:var(--brand-dark); color:#fff; }
        .sidebar-parent { display:flex; align-items:stretch; border-radius:8px; margin-bottom:5px; overflow:hidden; }
        .sidebar-parent a { flex:1; margin-bottom:0; border-radius:8px 0 0 8px; }
        .sidebar-parent a:hover { background:transparent; }
        .sidebar-toggle { width:38px; border:0; color:#fff; background:transparent; }
        .sidebar-toggle:hover { background:#174f6a; }
        .sidebar-toggle[aria-expanded="true"] .bi-chevron-down { transform:rotate(180deg); }
        .sidebar-toggle .bi-chevron-down { transition:.2s ease; }
        .sidebar-submenu { padding:4px 0 7px 20px; }
        .sidebar-submenu a { padding:9px 12px; font-size:13px; color:#d9eef6; }
        .main-content { margin-left:286px; min-height:100vh; display:flex; flex-direction:column; }
        .top-header { position:sticky; top:0; z-index:1020; background:rgba(246,251,253,.94); backdrop-filter:blur(10px); border-bottom:1px solid var(--line); padding:16px 24px; }
        .header-logo { width:44px; height:44px; object-fit:contain; }
        .icon-button { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; border:1px solid var(--line); background:#fff; color:var(--brand); }
        .content-wrap { width:100%; padding:24px; flex:1; }
        .soft-card, .stat-card, .table-card, .requirement-card, .security-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .security-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card { height:100%; transition:.2s ease; }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .requirement-item { padding:10px 0; border-bottom:1px solid var(--line); }
        .requirement-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
    @endphp

    <aside class="sidebar">
        <div class="text-center mb-3"><img src="{{ asset('images/logo-lldikti.png') }}" class="brand-logo mb-2" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta"><h6 class="fw-bold mb-1">Portal Magang</h6><small>LLDIKTI Wilayah V Yogyakarta</small></div>
        <div class="text-center mb-3"><img src="{{ $avatar }}" class="rounded-circle profile-photo mb-2" alt="Foto profil peserta"><div class="fw-bold">{{ $userName }}</div><div class="small d-inline-flex align-items-center gap-2 mt-1"><span class="status-dot"></span> Online</div></div>
        <hr class="border-light opacity-25">
        <a href="{{ route('peserta.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a>
        <div class="sidebar-parent"><a href="{{ route('peserta.data-magang') }}"><i class="bi bi-briefcase-fill"></i> Data Magang</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dataMagangMenu" aria-expanded="false" aria-controls="dataMagangMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="dataMagangMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a><a href="{{ route('peserta.data-magang.penempatan') }}">Penempatan</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.aktivitas-magang') }}"><i class="bi bi-activity"></i> Aktivitas Magang</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#aktivitasMenu" aria-expanded="false" aria-controls="aktivitasMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="aktivitasMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.aktivitas-magang.absensi') }}">Absensi</a><a href="{{ route('peserta.aktivitas-magang.penugasan') }}">Penugasan</a><a href="{{ route('peserta.aktivitas-magang.riwayat') }}">Riwayat Kegiatan</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.dokumen') }}"><i class="bi bi-folder2-open"></i> Dokumen</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dokumenMenu" aria-expanded="false" aria-controls="dokumenMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="dokumenMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.dokumen.kerjasama') }}">Dokumen Kerjasama</a><a href="{{ route('peserta.dokumen.pendukung') }}">Dokumen Pendukung</a><a href="{{ route('peserta.dokumen.status') }}">Status Dokumen</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.laporan') }}"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="false" aria-controls="laporanMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="laporanMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.laporan.input') }}">Input Laporan</a><a href="{{ route('peserta.laporan.riwayat') }}">Riwayat Laporan</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.penilaian') }}"><i class="bi bi-award-fill"></i> Penilaian</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#penilaianMenu" aria-expanded="false" aria-controls="penilaianMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="penilaianMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.penilaian.rekap') }}">Rekap Nilai</a><a href="{{ route('peserta.penilaian.sertifikat') }}">Sertifikat</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.komunikasi') }}"><i class="bi bi-chat-dots-fill"></i> Komunikasi</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#komunikasiMenu" aria-expanded="false" aria-controls="komunikasiMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="komunikasiMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.komunikasi.pesan') }}">Pesan</a><a href="{{ route('peserta.komunikasi.pengumuman') }}">Pengumuman</a><a href="{{ route('peserta.komunikasi.notifikasi') }}">Notifikasi</a></div></div>
        <div class="sidebar-parent active"><a href="{{ route('peserta.pengaturan') }}"><i class="bi bi-gear-fill"></i> Pengaturan Akun</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#pengaturanMenu" aria-expanded="true" aria-controls="pengaturanMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse show" id="pengaturanMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.pengaturan.profil') }}">Profil Akun</a><a href="{{ route('peserta.pengaturan.password') }}" class="active">Ubah Password</a></div></div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button></form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3"><img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta"><div><h2 class="fw-bold mb-1">Ubah Password</h2><div class="small-muted">Perbarui kata sandi untuk menjaga keamanan akun Portal Magang.</div></div></div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">@include('peserta.partials.topbar-actions')<img src="{{ $avatar }}" class="rounded-circle" width="42" height="42" alt="Foto profil peserta"></div>
            </div>
        </header>

        <div class="content-wrap">
            <nav aria-label="breadcrumb" class="mb-3"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('peserta.pengaturan') }}">Pengaturan Akun</a></li><li class="breadcrumb-item active" aria-current="page">Ubah Password</li></ol></nav>

            <section class="security-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-8">
                        <h3 class="fw-bold mb-1">Keamanan Akun Peserta</h3>
                        <div class="text-secondary">Pastikan password kuat, unik, dan tidak digunakan pada layanan lain.</div>
                        <div class="d-flex flex-wrap gap-2 mt-3"><span class="badge bg-success">Akun aman</span><span class="badge bg-primary">Verifikasi aktif</span><span class="badge bg-warning text-dark">Password terakhir diubah 45 hari lalu</span></div>
                    </div>
                    <div class="col-xl-4">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Kekuatan Password</strong><span>78%</span></div>
                            <div class="progress"><div class="progress-bar bg-success" style="width:78%"></div></div>
                            <div class="small-muted mt-2">Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="small-muted">Status Keamanan</div><h5 class="fw-bold mb-1">Aman</h5><span class="badge bg-success">Normal</span></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="small-muted">Login Terakhir</div><h5 class="fw-bold mb-1">21.45</h5><span class="badge bg-primary">Hari ini</span></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="small-muted">Aktivitas Terakhir</div><h5 class="fw-bold mb-1">Login</h5><span class="badge bg-info text-dark">Chrome</span></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="small-muted">Kekuatan Password</div><h5 class="fw-bold mb-1">78%</h5><span class="badge bg-warning text-dark">Baik</span></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="small-muted">Verifikasi Akun</div><h5 class="fw-bold mb-1">Aktif</h5><span class="badge bg-success">Valid</span></div></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-xl-7">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <h5 class="fw-bold mb-1">Form Ubah Password</h5>
                        <div class="small-muted mb-4">Isi password lama, password baru, dan konfirmasi password baru.</div>
                        <form id="passwordForm" method="POST" action="{{ route('peserta.pengaturan.password.update') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="currentPassword">Password Lama</label>
                                <div class="input-group">
                                    <input id="currentPassword" name="current_password" type="password" class="form-control" placeholder="Masukkan password lama" required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-target="currentPassword"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="newPassword">Password Baru</label>
                                <div class="input-group">
                                    <input id="newPassword" name="password" type="password" class="form-control" placeholder="Masukkan password baru" required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-target="newPassword"><i class="bi bi-eye"></i></button>
                                </div>
                                <div class="d-flex justify-content-between mt-2"><span class="small-muted">Indikator kekuatan</span><span class="small-muted" id="strengthLabel">Belum diisi</span></div>
                                <div class="progress mt-1"><div class="progress-bar bg-secondary" id="strengthBar" style="width:0%"></div></div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="confirmPassword">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input id="confirmPassword" name="password_confirmation" type="password" class="form-control" placeholder="Ulangi password baru" required>
                                    <button class="btn btn-outline-secondary" type="button" data-toggle-target="confirmPassword"><i class="bi bi-eye"></i></button>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2"><button type="reset" class="btn btn-outline-secondary" id="resetPasswordButton">Batalkan Perubahan</button><button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#activityModal">Lihat Aktivitas Akun</button><button type="button" class="btn btn-primary" id="savePasswordButton"><i class="bi bi-shield-lock me-1"></i> Simpan Password</button></div>
                        </form>
                    </section>
                </div>
                <div class="col-xl-5">
                    <section class="requirement-card p-3 p-lg-4 mb-4">
                        <h5 class="fw-bold mb-1">Persyaratan Password</h5>
                        <div class="small-muted mb-2">Sistem memeriksa kecocokan dan kekuatan password.</div>
                        <div class="requirement-item d-flex gap-2"><i class="bi bi-check-circle-fill text-success"></i><span>Minimal 8 karakter</span></div>
                        <div class="requirement-item d-flex gap-2"><i class="bi bi-check-circle-fill text-success"></i><span>Mengandung huruf besar dan kecil</span></div>
                        <div class="requirement-item d-flex gap-2"><i class="bi bi-check-circle-fill text-success"></i><span>Mengandung angka</span></div>
                        <div class="requirement-item d-flex gap-2"><i class="bi bi-check-circle-fill text-success"></i><span>Mengandung simbol</span></div>
                    </section>
                    <section class="security-card p-3 p-lg-4">
                        <h5 class="fw-bold mb-1">Informasi Keamanan</h5>
                        <div class="small-muted mb-3">Perubahan password akan memperbarui sesi akun dan dicatat pada histori keamanan.</div>
                        <div class="alert alert-warning mb-0">Jangan bagikan password kepada mentor, pembimbing, admin, atau pihak lain.</div>
                    </section>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <section class="soft-card p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                    <div><h5 class="fw-bold mb-1">Riwayat Aktivitas Keamanan</h5><div class="small-muted">Histori login, perubahan password, dan perangkat yang digunakan.</div></div>
                    <button class="btn btn-outline-primary" data-bs-toggle="toast" data-bs-target="#passwordToast"><i class="bi bi-download me-1"></i> Unduh Histori</button>
                </div>
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>No</th><th>Aktivitas</th><th>Perangkat</th><th>Browser</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse(collect($securityHistories ?? [])->take(10) as $history)
                                    @php
                                        $status = strtolower((string) ($history['status'] ?? 'berhasil'));
                                        $badge = in_array($status, ['berhasil', 'aman', 'tersimpan'], true)
                                            ? 'success'
                                            : (in_array($status, ['menunggu', 'perlu tinjauan'], true) ? 'warning text-dark' : 'secondary');
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $history['jenis'] ?? $history['aktivitas'] ?? '-' }}</td>
                                        <td>{{ $history['perangkat'] ?? '-' }}</td>
                                        <td>{{ $history['browser'] ?? '-' }}</td>
                                        <td>{{ $history['waktu'] ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $badge }}">{{ $history['status'] ?? 'berhasil' }}</span></td>
                                        <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#activityModal">Detail</button></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada histori keamanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                    <div class="small-muted">Menampilkan {{ min(10, collect($securityHistories ?? [])->count()) }} dari {{ collect($securityHistories ?? [])->count() }} aktivitas keamanan</div>
                </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Ubah Password</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Sistem akan memperbarui password akun dan mencatat perubahan pada histori keamanan.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" id="confirmActionButton">Simpan Password</button></div></div></div>
      </div>
    <div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="activityLabel">Detail Aktivitas Keamanan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail aktivitas memuat perangkat, browser, waktu, alamat akses, status, dan catatan keamanan akun.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="passwordToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Ubah Password</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Perubahan keamanan berhasil diproses dan histori akun diperbarui.</div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelectorAll('[data-bs-toggle="toast"]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.querySelector(button.dataset.bsTarget);
                if (target) bootstrap.Toast.getOrCreateInstance(target).show();
            });
        });
        document.querySelectorAll('[data-toggle-target]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.toggleTarget);
                if (!input) return;
                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });

        const form = document.getElementById('passwordForm');
        const currentPassword = document.getElementById('currentPassword');
        const newPassword = document.getElementById('newPassword');
        const confirmPassword = document.getElementById('confirmPassword');
        const strengthBar = document.getElementById('strengthBar');
        const strengthLabel = document.getElementById('strengthLabel');
        const confirmActionButton = document.getElementById('confirmActionButton');
        const savePasswordButton = document.getElementById('savePasswordButton');

        const scorePassword = (value) => {
            let score = 0;
            if ((value || '').length >= 8) score += 25;
            if (/[A-Z]/.test(value)) score += 20;
            if (/[a-z]/.test(value)) score += 20;
            if (/\d/.test(value)) score += 20;
            if (/[^A-Za-z0-9]/.test(value)) score += 15;
            return Math.min(100, score);
        };

        const updateStrength = () => {
            const score = scorePassword(newPassword.value);
            strengthBar.style.width = `${score}%`;
            strengthBar.className = `progress-bar ${score >= 80 ? 'bg-success' : score >= 55 ? 'bg-info' : score ? 'bg-danger' : 'bg-secondary'}`;
            strengthLabel.textContent = score >= 80 ? 'Kuat' : score >= 55 ? 'Sedang' : newPassword.value ? 'Lemah' : 'Belum diisi';
        };

        newPassword.addEventListener('input', updateStrength);
        confirmPassword.addEventListener('input', updateStrength);
        updateStrength();

        savePasswordButton.addEventListener('click', () => {
            if (!currentPassword.value || !newPassword.value || !confirmPassword.value) {
                alert('Lengkapi seluruh field password terlebih dahulu.');
                return;
            }
            if (newPassword.value !== confirmPassword.value) {
                alert('Password baru dan konfirmasi harus sesuai.');
                return;
            }
            new bootstrap.Modal(document.getElementById('confirmModal')).show();
        });

        confirmActionButton.addEventListener('click', () => form.submit());
        document.getElementById('resetPasswordButton').addEventListener('click', () => {
            strengthBar.style.width = '0%';
            strengthBar.className = 'progress-bar bg-secondary';
            strengthLabel.textContent = 'Belum diisi';
        });
    </script>
</body>
</html>
