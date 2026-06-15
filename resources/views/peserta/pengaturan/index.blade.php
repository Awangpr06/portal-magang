<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun Peserta</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .setting-card, .activity-card, .system-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .setting-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .setting-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .setting-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .setting-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .activity-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .activity-item:last-child { border-bottom:0; padding-bottom:0; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
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
        $activeSetting = $activeSetting ?? 'Ringkasan Akun';
        $accountStatus = $user?->account_status ?? $peserta?->status ?? 'menunggu';
        $lastUpdated = optional($user?->updated_at)->format('d M Y H:i') ?? 'Belum ada';
        $lastLogin = optional($user?->last_login_at)->format('H:i') ?? '--';
        $joinedAt = optional($user?->created_at)->format('M Y') ?? '-';
        $semester = $peserta?->semester ?? '-';
        $programStudi = $peserta?->jurusan ?? $peserta?->program_studi ?? '-';
        $currentInstitution = $internship?->instansi ?: ($perguruanTinggi?->nama_pt ?? 'LLDIKTI Wilayah V Yogyakarta');
        $position = $internship?->penempatan ?? $internship?->posisi ?? ($peserta?->program_magang ?? 'Penempatan belum diisi');
        $mentorName = $mentor?->name ?? 'Mentor belum ditentukan';
        $profileCompletion = collect([
            $user?->name,
            $user?->email,
            $peserta?->nim,
            $peserta?->no_hp,
            $peserta?->alamat,
            $perguruanTinggi?->nama_pt,
            $programStudi,
            $semester,
            $mentorName,
            $currentInstitution,
        ])->filter(fn ($value) => filled($value))->count();
        $profilePercent = round(($profileCompletion / 10) * 100);
        $profileRows = [
            ['label' => 'Identitas Pribadi', 'value' => $userName.' / '.($peserta?->nim ?? '-'), 'status' => filled($userName) && filled($peserta?->nim) ? 'Valid' : 'Perlu Update', 'date' => $lastUpdated],
            ['label' => 'Kontak', 'value' => ($user?->email ?? '-').' / '.($peserta?->no_hp ?? '-'), 'status' => filled($user?->email) && filled($peserta?->no_hp) ? 'Terverifikasi' : 'Perlu Update', 'date' => $lastUpdated],
            ['label' => 'Akademik', 'value' => ($perguruanTinggi?->nama_pt ?? '-').' / '.($programStudi ?: '-').' / Semester '.($semester ?: '-'), 'status' => filled($perguruanTinggi?->nama_pt) ? 'Review' : 'Perlu Update', 'date' => $lastUpdated],
            ['label' => 'Magang', 'value' => $currentInstitution.' / '.$position.' / '.$mentorName, 'status' => filled($currentInstitution) ? 'Valid' : 'Perlu Update', 'date' => $lastUpdated],
        ];
        $accountActivities = [
            ['title' => 'Login akun peserta', 'category' => 'Keamanan', 'date' => $lastLogin !== '--' ? $lastLogin : $lastUpdated, 'status' => 'Berhasil', 'device' => 'Sistem', 'action' => 'Detail'],
            ['title' => 'Pembaruan profil', 'category' => 'Profil', 'date' => $lastUpdated, 'status' => filled($user?->updated_at) ? 'Tersimpan' : 'Menunggu', 'device' => 'Sistem', 'action' => 'Detail'],
            ['title' => 'Sinkronisasi akademik', 'category' => 'Akademik', 'date' => $lastUpdated, 'status' => filled($perguruanTinggi?->nama_pt) ? 'Diperbarui' : 'Menunggu', 'device' => 'Database', 'action' => 'Detail'],
            ['title' => 'Penempatan magang', 'category' => 'Magang', 'date' => $lastUpdated, 'status' => filled($currentInstitution) ? 'Tersimpan' : 'Perlu Update', 'device' => 'Database', 'action' => 'Tinjau'],
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
        <div class="sidebar-parent">
            <a href="{{ route('peserta.data-magang') }}"><i class="bi bi-briefcase-fill"></i> Data Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dataMagangMenu" aria-expanded="false" aria-controls="dataMagangMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="dataMagangMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a>
                <a href="{{ route('peserta.data-magang.penempatan') }}">Penempatan</a>
            </div>
        </div>
        <div class="sidebar-parent">
            <a href="{{ route('peserta.aktivitas-magang') }}"><i class="bi bi-activity"></i> Aktivitas Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#aktivitasMenu" aria-expanded="false" aria-controls="aktivitasMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="aktivitasMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.aktivitas-magang.absensi') }}">Absensi</a>
                <a href="{{ route('peserta.aktivitas-magang.penugasan') }}">Penugasan</a>
                <a href="{{ route('peserta.aktivitas-magang.riwayat') }}">Riwayat Kegiatan</a>
            </div>
        </div>
        <div class="sidebar-parent">
            <a href="{{ route('peserta.dokumen') }}"><i class="bi bi-folder2-open"></i> Dokumen</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dokumenMenu" aria-expanded="false" aria-controls="dokumenMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="dokumenMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.dokumen.kerjasama') }}">Dokumen Kerjasama</a>
                <a href="{{ route('peserta.dokumen.pendukung') }}">Dokumen Pendukung</a>
                <a href="{{ route('peserta.dokumen.status') }}">Status Dokumen</a>
            </div>
        </div>
        <div class="sidebar-parent">
            <a href="{{ route('peserta.laporan') }}"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="false" aria-controls="laporanMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="laporanMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.laporan.input') }}">Input Laporan</a>
                <a href="{{ route('peserta.laporan.riwayat') }}">Riwayat Laporan</a>
            </div>
        </div>
        <div class="sidebar-parent">
            <a href="{{ route('peserta.penilaian') }}"><i class="bi bi-award-fill"></i> Penilaian</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#penilaianMenu" aria-expanded="false" aria-controls="penilaianMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="penilaianMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.penilaian.rekap') }}">Rekap Nilai</a>
                <a href="{{ route('peserta.penilaian.sertifikat') }}">Sertifikat</a>
            </div>
        </div>
        <div class="sidebar-parent">
            <a href="{{ route('peserta.komunikasi') }}"><i class="bi bi-chat-dots-fill"></i> Komunikasi</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#komunikasiMenu" aria-expanded="false" aria-controls="komunikasiMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="komunikasiMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.komunikasi.pesan') }}">Pesan</a>
                <a href="{{ route('peserta.komunikasi.pengumuman') }}">Pengumuman</a>
                <a href="{{ route('peserta.komunikasi.notifikasi') }}">Notifikasi</a>
            </div>
        </div>
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.pengaturan') }}"><i class="bi bi-gear-fill"></i> Pengaturan Akun</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#pengaturanMenu" aria-expanded="true" aria-controls="pengaturanMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="pengaturanMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.pengaturan.profil') }}" class="{{ $activeSetting === 'Profil Akun' ? 'active' : '' }}">Profil Akun</a>
                <a href="{{ route('peserta.pengaturan.password') }}" class="{{ $activeSetting === 'Ubah Password' ? 'active' : '' }}">Ubah Password</a>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button></form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
                    <div>
                        <h2 class="fw-bold mb-1">Pengaturan Akun</h2>
                        <div class="small-muted">Kelola profil, keamanan, notifikasi, dan aktivitas akun peserta.</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    @include('peserta.partials.topbar-actions')
                    <img src="{{ $avatar }}" class="rounded-circle" width="42" height="42" alt="Foto profil peserta">
                </div>
            </div>
        </header>

        <div class="content-wrap">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengaturan Akun</li>
                </ol>
            </nav>

            <section class="setting-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-8">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $activeSetting }}</h3>
                                <div class="text-secondary">{{ $userName }} | Peserta Magang | {{ $currentInstitution }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success text-capitalize">Akun {{ str_replace('_', ' ', $accountStatus) }}</span>
                                    <span class="badge bg-primary">Keamanan {{ $profilePercent }}%</span>
                                    <span class="badge bg-info text-dark">Login terakhir: {{ $lastLogin }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="soft-card p-3">
                            <strong>Indikator Keamanan</strong>
                            <div class="d-flex justify-content-between mt-2"><span class="small-muted">Kelengkapan keamanan</span><span>{{ $profilePercent }}%</span></div>
                            <div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $profilePercent }}%"></div></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Status Akun</div><h4 class="fw-bold mb-1 text-capitalize">{{ str_replace('_', ' ', $accountStatus) }}</h4><span class="badge bg-success">Terverifikasi</span></div><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Peran Pengguna</div><h4 class="fw-bold mb-1">Peserta</h4><span class="badge bg-primary">Magang</span></div><span class="stat-icon bg-primary"><i class="bi bi-person-badge"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Penempatan</div><h4 class="fw-bold mb-1">{{ $currentInstitution }}</h4><span class="badge bg-info text-dark">{{ $position }}</span></div><span class="stat-icon bg-info"><i class="bi bi-calendar-range"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Login Terakhir</div><h4 class="fw-bold mb-1">{{ $lastLogin }}</h4><span class="badge bg-warning text-dark">{{ $joinedAt }}</span></div><span class="stat-icon bg-warning"><i class="bi bi-clock-history"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Keamanan Akun</div><h4 class="fw-bold mb-1">{{ $profilePercent }}%</h4><span class="badge bg-secondary">Baik</span></div><span class="stat-icon bg-secondary"><i class="bi bi-shield-lock"></i></span></div></div></div>
                </div>
            </section>

            <section class="filter-card p-3 mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari pengaturan atau aktivitas"></div>
                    </div>
                    <div class="col-md-3"><label class="form-label">Kategori</label><select class="form-select"><option>Semua</option><option>Profil</option><option>Keamanan</option><option>Notifikasi</option><option>Bantuan</option></select></div>
                    <div class="col-md-3"><label class="form-label">Aktivitas</label><select class="form-select"><option>Semua</option><option>Login</option><option>Update Profil</option><option>Ubah Password</option></select></div>
                    <div class="col-md-2"><label class="form-label">Status Keamanan</label><select class="form-select"><option>Semua</option><option>Aman</option><option>Perlu Tinjauan</option></select></div>
                    <div class="col-lg-1"><button class="btn btn-primary w-100" type="button" data-bs-toggle="toast" data-bs-target="#settingToast">Filter</button></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><a class="setting-card p-3 d-block text-decoration-none" href="{{ route('peserta.pengaturan.profil') }}"><span class="setting-icon bg-primary mb-2"><i class="bi bi-person-lines-fill"></i></span><strong class="d-block text-dark">Kelola Profil</strong><span class="small-muted">Perbarui identitas dan data kontak</span></a></div>
                    <div class="col-md-6 col-xl"><a class="setting-card p-3 d-block text-decoration-none" href="{{ route('peserta.pengaturan.password') }}"><span class="setting-icon bg-warning mb-2"><i class="bi bi-key"></i></span><strong class="d-block text-dark">Ubah Password</strong><span class="small-muted">Perbarui kredensial akun</span></a></div>
                    <div class="col-md-6 col-xl"><button class="setting-card p-3 border-0 text-start w-100" data-bs-toggle="modal" data-bs-target="#notificationModal"><span class="setting-icon bg-info mb-2"><i class="bi bi-bell"></i></span><strong class="d-block">Kelola Notifikasi</strong><span class="small-muted">Atur kanal pemberitahuan</span></button></div>
                    <div class="col-md-6 col-xl"><button class="setting-card p-3 border-0 text-start w-100" data-bs-toggle="modal" data-bs-target="#securityModal"><span class="setting-icon bg-success mb-2"><i class="bi bi-shield-check"></i></span><strong class="d-block">Kelola Keamanan</strong><span class="small-muted">Pantau perangkat dan sesi</span></button></div>
                    <div class="col-md-6 col-xl"><button class="setting-card p-3 border-0 text-start w-100" data-bs-toggle="modal" data-bs-target="#helpModal"><span class="setting-icon bg-secondary mb-2"><i class="bi bi-question-circle"></i></span><strong class="d-block">Bantuan Sistem</strong><span class="small-muted">Panduan penggunaan akun</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                            <div><h5 class="fw-bold mb-1">Aktivitas Akun</h5><div class="small-muted">Histori penggunaan dan perubahan pengaturan akun.</div></div>
                            <button class="btn btn-outline-primary" data-bs-toggle="toast" data-bs-target="#settingToast"><i class="bi bi-download me-1"></i> Unduh Aktivitas</button>
                        </div>
                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Aktivitas</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Perangkat</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @foreach ($profileRows as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $row['label'] }}</strong></td>
                                                <td>{{ $row['label'] === 'Magang' ? 'Magang' : $row['label'] }}</td>
                                                <td>{{ $row['date'] }}</td>
                                                <td>
                                                    @if (str_contains($row['status'], 'Perlu'))
                                                        <span class="badge bg-warning text-dark">{{ $row['status'] }}</span>
                                                    @elseif (str_contains($row['status'], 'Review'))
                                                        <span class="badge bg-info text-dark">{{ $row['status'] }}</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $row['status'] }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $currentInstitution }}</td>
                                                <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ count($profileRows) }} dari {{ count($profileRows) }} aktivitas</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option></select></div>
                            <nav aria-label="Pagination aktivitas akun"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>

                <div class="col-xl-4">
                    <section class="activity-card p-3 p-lg-4 mb-4">
                        <h5 class="fw-bold mb-1">Panel Aktivitas</h5>
                        <div class="small-muted mb-2">Aktivitas akun terbaru.</div>
                        @foreach (array_slice($accountActivities, 0, 3) as $activity)
                            <div class="activity-item">
                                <strong>{{ $activity['title'] }}</strong>
                                <div class="small-muted">{{ $activity['category'] }}, {{ $activity['date'] }}</div>
                                <span class="badge mt-1 {{ str_contains($activity['status'], 'Diperbarui') ? 'bg-info text-dark' : (str_contains($activity['status'], 'Perlu') || str_contains($activity['status'], 'Menunggu') ? 'bg-warning text-dark' : 'bg-success') }}">{{ $activity['status'] }}</span>
                            </div>
                        @endforeach
                    </section>
                    <section class="system-card p-3 p-lg-4">
                        <h5 class="fw-bold mb-1">Informasi Sistem</h5>
                        <div class="small-muted mb-3">Status layanan dan keamanan akun.</div>
                        <div class="mb-3"><div class="d-flex justify-content-between"><strong>Keamanan</strong><span>{{ $profilePercent }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $profilePercent }}%"></div></div></div>
                        <div class="alert alert-info mb-0">Data keamanan akun mengikuti informasi yang tersimpan di database.</div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="detailLabel">Detail Aktivitas Akun</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail aktivitas memuat waktu, perangkat, lokasi, kategori, status, dan catatan keamanan yang berkaitan dengan akun peserta.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#settingToast">Lanjutkan</button></div></div></div>
    </div>
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="notificationLabel">Kelola Notifikasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" checked id="emailNotif"><label class="form-check-label" for="emailNotif">Notifikasi email</label></div><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked id="systemNotif"><label class="form-check-label" for="systemNotif">Notifikasi sistem</label></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#settingToast">Simpan</button></div></div></div>
    </div>
    <div class="modal fade" id="securityModal" tabindex="-1" aria-labelledby="securityLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="securityLabel">Kelola Keamanan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Tinjau perangkat aktif, sesi login, kekuatan password, dan aktivitas yang memerlukan perhatian.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#settingToast">Simpan Perubahan</button></div></div></div>
    </div>
    <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="helpLabel">Bantuan Sistem</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Bantuan sistem memuat panduan pengelolaan profil, perubahan password, preferensi notifikasi, dan keamanan akun.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="settingToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Pengaturan Akun</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan pengaturan akun berhasil diproses dan histori aktivitas diperbarui.</div>
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
    </script>
</body>
</html>
