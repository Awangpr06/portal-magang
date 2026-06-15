<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Akun Peserta</title>

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
        .content-wrap { width:100%; padding:10px; flex:1; }
        .soft-card, .stat-card, .table-card, .photo-card, .validation-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .content-wrap .soft-card,
        .content-wrap .table-card,
        .content-wrap .photo-card,
        .content-wrap .validation-card { padding: 0.6rem !important; }
        .profile-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card { height:100%; transition:.2s ease; min-height:72px; }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-card .p-3 { padding: .55rem !important; }
        .stat-icon { width:36px; height:36px; flex:0 0 36px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:16px; }
        .large-avatar { width:72px; height:72px; object-fit:cover; border:3px solid #fff; box-shadow:0 10px 24px rgba(22,51,66,.16); }
        .validation-card { padding: 0.55rem !important; }
        .profile-hero { padding: 0.7rem !important; }
        .small-muted { font-size:12px; }
        .content-wrap h3 { font-size:1.25rem; }
        .content-wrap h5 { font-size:1rem; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .nav-pills .nav-link { border-radius:8px; color:#315363; }
        .nav-pills .nav-link.active { background:var(--brand); }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $user = $user ?? auth()->user()?->loadMissing(['peserta.perguruanTinggi', 'peserta.internship', 'mentor']);
        $peserta = $user?->peserta;
        $mentor = $user?->mentor;
        $perguruanTinggi = $peserta?->perguruanTinggi;
        $internship = $peserta?->internship;
        $userName = $user?->name ?? 'Aulia Berliana';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $accountStatus = $user?->account_status ?? $peserta?->status ?? 'menunggu';
        $lastLogin = optional($user?->last_login_at)->format('H:i') ?? '--';
        $joinedAt = optional($user?->created_at)->format('M Y') ?? '-';
        $programStudi = $peserta?->jurusan ?? $peserta?->program_studi ?? '-';
        $semester = $peserta?->semester ?? '-';
        $currentInstitution = $internship?->instansi ?: ($perguruanTinggi?->nama_pt ?? 'LLDIKTI Wilayah V Yogyakarta');
        $position = $internship?->penempatan ?? $internship?->posisi ?? ($peserta?->program_magang ?? 'Penempatan belum diisi');
        $mentorName = $mentor?->name ?? 'Mentor belum ditentukan';
        $completed = collect([
            $user?->name,
            $user?->email,
            $peserta?->nim,
            $peserta?->tempat_lahir,
            $peserta?->tanggal_lahir,
            $peserta?->no_hp,
            $peserta?->alamat,
            $perguruanTinggi?->nama_pt,
            $programStudi,
            $semester,
            $mentorName,
            $currentInstitution,
        ])->filter(fn ($value) => filled($value))->count();
        $completionPercent = round(($completed / 12) * 100);
        $profileRows = [
            ['label' => 'Identitas Pribadi', 'value' => $userName.' / '.($peserta?->nim ?? '-'), 'status' => filled($peserta?->nim) ? 'Valid' : 'Perlu Update', 'date' => optional($user?->updated_at)->format('d M Y') ?? '-'],
            ['label' => 'Kontak', 'value' => ($user?->email ?? '-').' / '.($peserta?->no_hp ?? '-'), 'status' => filled($user?->email) && filled($peserta?->no_hp) ? 'Terverifikasi' : 'Perlu Update', 'date' => optional($user?->updated_at)->format('d M Y') ?? '-'],
            ['label' => 'Akademik', 'value' => ($perguruanTinggi?->nama_pt ?? '-').' / '.$programStudi.' / Semester '.$semester, 'status' => filled($perguruanTinggi?->nama_pt) ? 'Review' : 'Perlu Update', 'date' => optional($user?->updated_at)->format('d M Y') ?? '-'],
            ['label' => 'Magang', 'value' => $currentInstitution.' / '.$position.' / '.$mentorName, 'status' => filled($currentInstitution) ? 'Valid' : 'Perlu Update', 'date' => optional($internship?->updated_at)->format('d M Y') ?? optional($user?->updated_at)->format('d M Y') ?? '-'],
        ];
    @endphp

    <aside class="sidebar">
        <div class="text-center mb-3">
            <img src="{{ asset('images/logo-lldikti.png') }}" class="brand-logo mb-2" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
            <h6 class="fw-bold mb-1">Portal Magang</h6>
            <small>LLDIKTI Wilayah V Yogyakarta</small>
        </div>
        <div class="text-center mb-3">
            <img src="{{ $avatar }}" class="rounded-circle profile-photo mb-2" alt="Foto profil peserta">
            <div class="fw-bold">{{ $userName }}</div>
            <div class="small d-inline-flex align-items-center gap-2 mt-1"><span class="status-dot"></span> Online</div>
        </div>
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
        <div class="collapse show" id="pengaturanMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.pengaturan.profil') }}" class="active">Profil Akun</a><a href="{{ route('peserta.pengaturan.password') }}">Ubah Password</a></div></div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button></form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
                    <div><h2 class="fw-bold mb-1">Profil Akun</h2><div class="small-muted">Kelola identitas, kontak, akademik, dan informasi magang peserta.</div></div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    @include('peserta.partials.topbar-actions')
                    <img src="{{ $avatar }}" class="rounded-circle" width="42" height="42" alt="Foto profil peserta">
                </div>
            </div>
        </header>

        <div class="content-wrap">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('peserta.pengaturan') }}">Pengaturan Akun</a></li><li class="breadcrumb-item active" aria-current="page">Profil Akun</li></ol>
            </nav>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <section class="profile-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-8">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">Peserta Magang | {{ $position }} | {{ $currentInstitution }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2"><span class="badge bg-success text-capitalize">Akun {{ str_replace('_', ' ', $accountStatus) }}</span><span class="badge bg-primary">Email {{ filled($user?->email) ? 'Valid' : 'Belum' }}</span><span class="badge bg-info text-dark">Telepon {{ filled($peserta?->no_hp) ? 'Valid' : 'Belum' }}</span></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4"><div class="validation-card p-3"><strong>Validasi Data</strong><div class="small-muted mt-1">Identitas pribadi, kontak, akademik, dan informasi magang sudah lengkap {{ $completionPercent }}%.</div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="small-muted">Peran Pengguna</div><h5 class="fw-bold mb-1">Peserta</h5><span class="badge bg-primary">Magang</span></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="small-muted">Status Akun</div><h5 class="fw-bold mb-1 text-capitalize">{{ str_replace('_', ' ', $accountStatus) }}</h5><span class="badge bg-success">Valid</span></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="small-muted">Bergabung Sejak</div><h5 class="fw-bold mb-1">{{ $joinedAt }}</h5><span class="badge bg-info text-dark">Portal</span></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="small-muted">Login Terakhir</div><h5 class="fw-bold mb-1">{{ $lastLogin }}</h5><span class="badge bg-warning text-dark">Hari ini</span></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="small-muted">Verifikasi Email</div><h5 class="fw-bold mb-1">{{ filled($user?->email) ? 'Valid' : 'Belum' }}</h5><span class="badge bg-success">Terverifikasi</span></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="small-muted">Verifikasi Telepon</div><h5 class="fw-bold mb-1">{{ filled($peserta?->no_hp) ? 'Valid' : 'Belum' }}</h5><span class="badge bg-success">Terverifikasi</span></div></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-xl-3">
                    <section class="photo-card p-3 p-lg-4 text-center h-100">
                        <img src="{{ $avatar }}" class="rounded-circle large-avatar mb-3" alt="Foto profil peserta">
                        <h5 class="fw-bold mb-1">{{ $userName }}</h5>
                        <div class="small-muted mb-3">Peserta Magang</div>
                        <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#photoModal"><i class="bi bi-camera me-1"></i> Ubah Foto</button>
                        <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#historyModal">Riwayat Perubahan</button>
                    </section>
                </div>
                <div class="col-xl-9">
                    <section class="soft-card p-3 p-lg-4">
                        <ul class="nav nav-pills gap-2 mb-3 align-items-center" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#pribadi" type="button">Informasi Pribadi</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#kontak" type="button">Kontak</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#akademik" type="button">Akademik</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#magang" type="button">Magang</button></li>
                            <li class="nav-item">
                                <button class="icon-button" type="button" data-bs-toggle="collapse" data-bs-target="#profileSearchPanel" aria-expanded="false" aria-controls="profileSearchPanel" title="Cari bagian profil">
                                    <i class="bi bi-search"></i>
                                </button>
                            </li>
                        </ul>
                        <div class="collapse mb-4" id="profileSearchPanel">
                            <div class="border rounded-3 bg-light p-3">
                                <label class="form-label" for="profileSearchInput">Cari Bagian Profil</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" id="profileSearchInput" placeholder="Cari identitas, kontak, akademik, atau magang">
                                    <button class="btn btn-outline-secondary" type="button" id="profileSearchClear" title="Hapus pencarian"><i class="bi bi-x-lg"></i></button>
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('peserta.pengaturan.update') }}" id="profileForm">
                            @csrf
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pribadi">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" name="name" value="{{ old('name', $userName) }}" required></div>
                                        <div class="col-md-6"><label class="form-label">NIM</label><input class="form-control" name="nim" value="{{ old('nim', $peserta?->nim ?? '') }}" required></div>
                                        <div class="col-md-6"><label class="form-label">Tempat Lahir</label><input class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir', $peserta?->tempat_lahir ?? '') }}"></div>
                                        <div class="col-md-6"><label class="form-label">Tanggal Lahir</label><input type="date" class="form-control" name="tanggal_lahir" value="{{ old('tanggal_lahir', $peserta?->tanggal_lahir ? \Illuminate\Support\Carbon::parse($peserta->tanggal_lahir)->format('Y-m-d') : '') }}"></div>
                                        <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat" rows="3" required>{{ old('alamat', $peserta?->alamat ?? '') }}</textarea></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="kontak">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', $user?->email ?? '-') }}" required></div>
                                        <div class="col-md-6"><label class="form-label">Nomor Telepon</label><input class="form-control" name="no_hp" value="{{ old('no_hp', $peserta?->no_hp ?? '') }}" required></div>
                                        <div class="col-12"><label class="form-label">Username</label><input class="form-control" name="username" value="{{ old('username', $user?->username ?? '') }}"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="akademik">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label">Perguruan Tinggi</label><input class="form-control" value="{{ $perguruanTinggi?->nama_pt ?? '-' }}" readonly></div>
                                        <div class="col-md-6"><label class="form-label">Program Studi</label><input class="form-control" name="jurusan" value="{{ old('jurusan', $programStudi) }}" required></div>
                                        <div class="col-md-6"><label class="form-label">Fakultas</label><input class="form-control" name="fakultas" value="{{ old('fakultas', $peserta?->fakultas ?? '') }}"></div>
                                        <div class="col-md-6"><label class="form-label">Semester</label><input class="form-control" name="semester" value="{{ old('semester', $semester) }}" required></div>
                                        <div class="col-md-6"><label class="form-label">IPK</label><input class="form-control" value="{{ $peserta?->ipk ?? '-' }}" readonly></div>
                                        <div class="col-md-6"><label class="form-label">Program Magang</label><input class="form-control" name="program_magang" value="{{ old('program_magang', $peserta?->program_magang ?? '') }}"></div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="magang">
                                    <div class="row g-3">
                                        <div class="col-md-6"><label class="form-label">Instansi Magang</label><input class="form-control" value="{{ $currentInstitution }}" readonly></div>
                                        <div class="col-md-6"><label class="form-label">Posisi</label><input class="form-control" value="{{ $position }}" readonly></div>
                                        <div class="col-md-6"><label class="form-label">Mentor</label><input class="form-control" value="{{ $mentorName }}" readonly></div>
                                        <div class="col-md-6"><label class="form-label">Pembimbing Akademik</label><input class="form-control" name="pembimbing_akademik" value="{{ old('pembimbing_akademik', $peserta?->pembimbing_akademik ?? '') }}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button type="reset" class="btn btn-outline-secondary">Reset Form</button>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <section class="soft-card p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                    <div><h5 class="fw-bold mb-1">Status Informasi Profil</h5><div class="small-muted">Daftar data profil tersimpan dan validasinya.</div></div>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#historyModal">Lihat Riwayat Perubahan</button>
                </div>
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>No</th><th>Jenis Informasi</th><th>Data Saat Ini</th><th>Status Validasi</th><th>Tanggal Perubahan</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($profileRows as $row)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row['label'] }}</td>
                                        <td>{{ $row['value'] }}</td>
                                        <td>
                                            @if ($row['status'] === 'Perlu Update')
                                                <span class="badge bg-warning text-dark">{{ $row['status'] }}</span>
                                            @elseif ($row['status'] === 'Review')
                                                <span class="badge bg-info text-dark">{{ $row['status'] }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $row['status'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['date'] }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-outline-primary"
                                                type="button"
                                                data-action="detail"
                                                data-label="{{ $row['label'] }}"
                                                data-value="{{ $row['value'] }}"
                                                data-status="{{ $row['status'] }}"
                                                data-date="{{ $row['date'] }}"
                                            >
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                    <div class="small-muted">Menampilkan {{ count($profileRows) }} dari {{ count($profileRows) }} histori profil</div>
                    <nav aria-label="Pagination profil"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Perubahan Profil</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pastikan data profil yang diubah sudah benar sebelum disimpan ke sistem.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#profileToast">Simpan</button></div></div></div>
    </div>
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('peserta.pengaturan.foto') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title" id="photoLabel">Ubah Foto Profil</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                    <div class="modal-body">
                        <input type="file" name="foto" class="form-control mb-2" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                        <div class="small-muted">Gunakan foto formal dengan ukuran maksimal 2 MB.</div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Unggah</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="detailLabel">Detail Validasi Profil</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body" id="detailContent">Pilih data pada tabel untuk melihat detailnya.</div>
                <div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="historyLabel">Riwayat Perubahan Profil</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Riwayat perubahan menyimpan aktivitas pembaruan identitas, kontak, akademik, informasi magang, dan foto profil.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="profileToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Profil Akun</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Perubahan profil berhasil diproses dan indikator validasi diperbarui.</div>
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

        const profileSearchPanel = document.getElementById('profileSearchPanel');
        const profileSearchInput = document.getElementById('profileSearchInput');
        const profileSearchClear = document.getElementById('profileSearchClear');
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        const profileSections = [
            { target: '#pribadi', keywords: ['pribadi', 'identitas', 'nama', 'nim', 'lahir', 'alamat'] },
            { target: '#kontak', keywords: ['kontak', 'email', 'telepon', 'darurat'] },
            { target: '#akademik', keywords: ['akademik', 'kampus', 'perguruan', 'program studi', 'semester', 'ipk'] },
            { target: '#magang', keywords: ['magang', 'instansi', 'posisi', 'mentor', 'periode', 'penempatan'] },
        ];

        profileSearchPanel?.addEventListener('shown.bs.collapse', () => profileSearchInput?.focus());

        profileSearchInput?.addEventListener('input', () => {
            const keyword = profileSearchInput.value.trim().toLowerCase();
            if (!keyword) return;

            const section = profileSections.find((item) =>
                item.keywords.some((itemKeyword) => itemKeyword.includes(keyword) || keyword.includes(itemKeyword))
            );
            const tabButton = section ? document.querySelector(`[data-bs-target="${section.target}"]`) : null;
            if (tabButton) bootstrap.Tab.getOrCreateInstance(tabButton).show();
        });

        profileSearchClear?.addEventListener('click', () => {
            profileSearchInput.value = '';
            profileSearchInput.focus();
        });

        document.querySelectorAll('button[data-action="detail"]').forEach((button) => {
            button.addEventListener('click', () => {
                const label = button.dataset.label || '-';
                const value = button.dataset.value || '-';
                const status = button.dataset.status || '-';
                const date = button.dataset.date || '-';

                document.getElementById('detailLabel').textContent = `Detail ${label}`;
                document.getElementById('detailContent').innerHTML = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Jenis Informasi</small>
                                <h6 class="mb-0">${label}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Data Saat Ini</small>
                                <h6 class="mb-0">${value}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Status Validasi</small>
                                <h6 class="mb-0">${status}</h6>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <small class="text-muted">Tanggal Perubahan</small>
                                <h6 class="mb-0">${date}</h6>
                            </div>
                        </div>
                    </div>
                `;
                detailModal.show();
            });
        });
    </script>
</body>
</html>
