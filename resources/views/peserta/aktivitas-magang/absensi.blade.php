<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Magang - Absensi</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .attendance-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .server-time { font-size:36px; font-weight:800; line-height:1; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } .server-time { font-size:30px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $context = $pesertaContext ?? [];
        $user = $context['user'] ?? auth()->user();
        $stats = $context['stats'] ?? [];
        $notificationCount = $stats['notification_unread'] ?? 0;
        $messageCount = $stats['message_unread'] ?? 0;
        $userName = $user?->name ?? 'Peserta Magang';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $todayStatus = $todayAttendance?->status;
        $statusLabels = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Tidak Hadir', 'tidak_hadir' => 'Tidak Hadir'];
        $statusClasses = ['hadir' => 'bg-success', 'terlambat' => 'bg-warning text-dark', 'izin' => 'bg-info text-dark', 'sakit' => 'bg-danger', 'alpa' => 'bg-secondary', 'tidak_hadir' => 'bg-secondary'];
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.aktivitas-magang') }}"><i class="bi bi-activity"></i> Aktivitas Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#aktivitasMenu" aria-expanded="true" aria-controls="aktivitasMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="aktivitasMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.aktivitas-magang.absensi') }}" class="active">Absensi</a>
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
        <div class="sidebar-parent">
            <a href="{{ route('peserta.pengaturan') }}"><i class="bi bi-gear-fill"></i> Pengaturan Akun</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#pengaturanMenu" aria-expanded="false" aria-controls="pengaturanMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse" id="pengaturanMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.pengaturan.profil') }}">Profil Akun</a>
                <a href="{{ route('peserta.pengaturan.password') }}">Ubah Password</a>
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
                        <h2 class="fw-bold mb-1">Absensi</h2>
                        <div class="small-muted">Pencatatan dan pemantauan kehadiran peserta selama periode magang.</div>
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
                    <li class="breadcrumb-item"><a href="{{ route('peserta.aktivitas-magang') }}">Aktivitas Magang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Absensi</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Absensi hanya dapat dilakukan sesuai jadwal magang aktif dan akan tercatat pada histori kehadiran.</div>
            </div>
            @if (session('success'))
                <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}</div>
            @endif

            <section class="attendance-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-5">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">{{ $peserta?->program_magang ?? 'Peserta Magang' }} | LLDIKTI Wilayah V Yogyakarta</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">Jadwal Aktif</span>
                                    <span class="badge bg-info text-dark">Shift 08.00-16.00 WIB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3">
                        <div class="soft-card p-3 text-center">
                            <div class="small-muted mb-1">Waktu Server</div>
                            <div class="server-time" id="serverTime">{{ $now->format('H:i:s') }}</div>
                            <div class="small-muted" id="serverDate">Waktu Indonesia Barat (WIB)</div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="small-muted">Status Kehadiran Hari Ini</div>
                                    <h4 class="fw-bold mb-0">
                                        @if (! $todayAttendance)
                                            Belum Ada Absensi
                                        @elseif ($todayAttendance->jam_pulang)
                                            Absensi Hari Ini Lengkap
                                        @elseif ($todayAttendance->jam_masuk)
                                            Sudah Absen Masuk
                                        @else
                                            {{ $statusLabels[$todayStatus] ?? ucfirst($todayStatus) }}
                                        @endif
                                    </h4>
                                </div>
                                <span class="badge {{ $statusClasses[$todayStatus] ?? 'bg-secondary' }}">{{ $statusLabels[$todayStatus] ?? 'Belum Absen' }}</span>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-success attendance-trigger" type="button" data-mode="masuk" data-bs-toggle="modal" data-bs-target="#attendanceModal" {{ $todayAttendance?->jam_masuk || in_array($todayStatus, ['izin', 'sakit']) ? 'disabled' : '' }}><i class="bi bi-box-arrow-in-right me-1"></i> Absensi Masuk</button>
                                <button class="btn btn-outline-primary attendance-trigger" type="button" data-mode="pulang" data-bs-toggle="modal" data-bs-target="#attendanceModal" {{ ! $todayAttendance?->jam_masuk || $todayAttendance?->jam_pulang ? 'disabled' : '' }}><i class="bi bi-box-arrow-right me-1"></i> Absensi Pulang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Absensi</div><h4 class="fw-bold mb-1">{{ $attendanceStats['total'] }}</h4><span class="badge bg-primary">Hari</span></div><span class="stat-icon bg-primary"><i class="bi bi-calendar2-week"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Hadir</div><h4 class="fw-bold mb-1">{{ $attendanceStats['present'] }}</h4><span class="badge bg-success">Normal</span></div><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Izin</div><h4 class="fw-bold mb-1">{{ $attendanceStats['permit'] }}</h4><span class="badge bg-info text-dark">Hari</span></div><span class="stat-icon bg-info"><i class="bi bi-envelope-check"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Sakit</div><h4 class="fw-bold mb-1">{{ $attendanceStats['sick'] }}</h4><span class="badge bg-danger">Hari</span></div><span class="stat-icon bg-danger"><i class="bi bi-bandaid"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Terlambat</div><h4 class="fw-bold mb-1">{{ $attendanceStats['late'] }}</h4><span class="badge bg-warning text-dark">Hari</span></div><span class="stat-icon bg-warning"><i class="bi bi-alarm"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-2"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Kehadiran</div><h4 class="fw-bold mb-1">{{ $attendanceStats['percentage'] }}%</h4><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $attendanceStats['percentage'] }}%"></div></div></div><span class="stat-icon bg-secondary"><i class="bi bi-graph-up-arrow"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><button class="action-card w-100 p-3 border-0 text-start attendance-trigger" data-mode="masuk" data-bs-toggle="modal" data-bs-target="#attendanceModal" {{ $todayAttendance?->jam_masuk || in_array($todayStatus, ['izin', 'sakit']) ? 'disabled' : '' }}><span class="action-icon bg-success mb-2"><i class="bi bi-box-arrow-in-right"></i></span><strong class="d-block">Absensi Masuk</strong><span class="small-muted">Catat jam masuk</span></button></div>
                    <div class="col-md-6 col-xl"><button class="action-card w-100 p-3 border-0 text-start attendance-trigger" data-mode="pulang" data-bs-toggle="modal" data-bs-target="#attendanceModal" {{ ! $todayAttendance?->jam_masuk || $todayAttendance?->jam_pulang ? 'disabled' : '' }}><span class="action-icon bg-primary mb-2"><i class="bi bi-box-arrow-right"></i></span><strong class="d-block">Absensi Pulang</strong><span class="small-muted">Catat jam pulang</span></button></div>
                    <div class="col-md-6 col-xl"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#permitModal"><span class="action-icon bg-warning mb-2"><i class="bi bi-envelope-plus"></i></span><strong class="d-block">Ajukan Izin</strong><span class="small-muted">Bisa beberapa hari</span></button></div>
                    <div class="col-md-6 col-xl"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#sickModal"><span class="action-icon bg-danger mb-2"><i class="bi bi-bandaid"></i></span><strong class="d-block">Ajukan Sakit</strong><span class="small-muted">Diajukan per hari</span></button></div>
                </div>
            </section>

            <section class="soft-card p-3 p-lg-4 mb-4">
                <form class="filter-card p-3 mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3">
                            <label class="form-label">Pencarian</label>
                            <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari tanggal/keterangan"></div>
                        </div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Status Kehadiran</label><select class="form-select"><option>Semua</option><option>Hadir</option><option>Izin</option><option>Terlambat</option><option>Tidak Hadir</option></select></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Bulan</label><select class="form-select"><option>Mei 2026</option><option>April 2026</option><option>Maret 2026</option></select></div>
                        <div class="col-md-4 col-lg-2"><label class="form-label">Periode</label><input type="date" class="form-control" value="2026-05-28"></div>
                        <div class="col-lg-3 d-flex flex-wrap gap-2"><button class="btn btn-primary" type="button" data-bs-toggle="toast" data-bs-target="#attendanceToast"><i class="bi bi-search me-1"></i> Cari</button><button class="btn btn-outline-secondary" type="reset"><i class="bi bi-x-circle me-1"></i> Reset</button></div>
                    </div>
                </form>

                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>No</th><th>Tanggal</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status Kehadiran</th><th>Durasi</th><th>Keterangan</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse ($attendances as $attendance)
                                    @php
                                        $hours = intdiv((int) $attendance->durasi_menit, 60);
                                        $minutes = (int) $attendance->durasi_menit % 60;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $attendance->tanggal->locale('id')->translatedFormat('d F Y') }}</strong><div class="small-muted">{{ $attendance->tanggal->locale('id')->translatedFormat('l') }}</div></td>
                                        <td>{{ $attendance->jam_masuk?->format('H:i').' WIB' ?? '-' }}</td>
                                        <td>{{ $attendance->jam_pulang?->format('H:i').' WIB' ?? '-' }}</td>
                                        <td><span class="badge {{ $statusClasses[$attendance->status] ?? 'bg-secondary' }}">{{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}</span></td>
                                        <td>{{ $attendance->durasi_menit !== null ? ($hours.' jam '.$minutes.' menit') : ($attendance->jam_masuk ? 'Berjalan' : '-') }}</td>
                                        <td>{{ $attendance->keterangan ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary detail-trigger" type="button" data-bs-toggle="modal" data-bs-target="#detailModal"
                                                data-date="{{ $attendance->tanggal->locale('id')->translatedFormat('d F Y') }}"
                                                data-status="{{ $statusLabels[$attendance->status] ?? ucfirst($attendance->status) }}"
                                                data-in="{{ $attendance->jam_masuk?->format('H:i').' WIB' ?? '-' }}"
                                                data-out="{{ $attendance->jam_pulang?->format('H:i').' WIB' ?? '-' }}"
                                                data-note="{{ $attendance->keterangan ?? '-' }}"
                                                data-attachment="{{ $attendance->lampiran ? asset('storage/'.$attendance->lampiran) : '' }}">Detail</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-secondary py-4">Belum ada data absensi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                    <div class="small-muted">Menampilkan {{ $attendances->count() }}</div>
                </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="attendanceLabel">Konfirmasi Absensi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><span id="attendanceMessage">Absensi akan dicatat berdasarkan waktu Indonesia Barat.</span><div class="fw-bold fs-4 mt-2" id="modalServerTime"></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><form id="attendanceForm" method="POST" action="{{ route('peserta.aktivitas-magang.absensi.masuk') }}">@csrf<button type="submit" class="btn btn-primary" id="attendanceSubmit">Catat Absensi Masuk</button></form></div></div></div>
    </div>
    <div class="modal fade" id="permitModal" tabindex="-1" aria-labelledby="permitLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form class="modal-content" method="POST" action="{{ route('peserta.aktivitas-magang.absensi.izin') }}" enctype="multipart/form-data">@csrf<div class="modal-header"><h5 class="modal-title" id="permitLabel">Ajukan Izin</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><div class="mb-3"><label class="form-label">Alasan Izin</label><textarea class="form-control" name="keterangan" rows="3" required>{{ old('keterangan') }}</textarea></div><div class="row g-3"><div class="col-sm-6"><label class="form-label">Tanggal Mulai</label><input class="form-control" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $now->toDateString()) }}" required></div><div class="col-sm-6"><label class="form-label">Tanggal Selesai</label><input class="form-control" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $now->toDateString()) }}" required></div></div><div class="mt-3"><label class="form-label">Surat Izin <span class="text-secondary">(opsional)</span></label><input class="form-control" type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png"><div class="form-text">PDF/JPG/PNG, maksimal 5 MB.</div></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Kirim Izin</button></div></form></div>
    </div>
    <div class="modal fade" id="sickModal" tabindex="-1" aria-labelledby="sickLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form class="modal-content" method="POST" action="{{ route('peserta.aktivitas-magang.absensi.sakit') }}" enctype="multipart/form-data">@csrf<div class="modal-header"><h5 class="modal-title" id="sickLabel">Ajukan Sakit</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><div class="alert alert-info py-2">Sakit diajukan per hari. Surat dokter bersifat opsional dan sebaiknya dilampirkan jika sakit berlanjut lebih dari tiga hari.</div><div class="mb-3"><label class="form-label">Keterangan</label><textarea class="form-control" name="keterangan" rows="3" required></textarea></div><div class="mb-3"><label class="form-label">Tanggal Sakit</label><input class="form-control" type="date" name="tanggal" value="{{ $now->toDateString() }}" required></div><div><label class="form-label">Surat Dokter <span class="text-secondary">(opsional)</span></label><input class="form-control" type="file" name="lampiran" accept=".pdf,.jpg,.jpeg,.png"><div class="form-text">PDF/JPG/PNG, maksimal 5 MB.</div></div></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger">Kirim Pengajuan Sakit</button></div></form></div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="detailLabel">Detail Absensi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><dl class="row mb-0"><dt class="col-5">Tanggal</dt><dd class="col-7" id="detailDate">-</dd><dt class="col-5">Status</dt><dd class="col-7" id="detailStatus">-</dd><dt class="col-5">Jam Masuk</dt><dd class="col-7" id="detailIn">-</dd><dt class="col-5">Jam Pulang</dt><dd class="col-7" id="detailOut">-</dd><dt class="col-5">Keterangan</dt><dd class="col-7" id="detailNote">-</dd><dt class="col-5">Lampiran</dt><dd class="col-7"><a id="detailAttachment" class="d-none" target="_blank" rel="noopener">Lihat lampiran</a><span id="detailNoAttachment">Tidak ada</span></dd></dl></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="attendanceToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Notifikasi Sistem</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan absensi berhasil diproses. Status dan histori kehadiran diperbarui.</div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        const jakartaClock = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
        });
        const jakartaDate = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
        });
        const updateClock = () => {
            const now = new Date();
            document.getElementById('serverTime').textContent = jakartaClock.format(now).replaceAll('.', ':');
            document.getElementById('serverDate').textContent = jakartaDate.format(now) + ' WIB';
            document.getElementById('modalServerTime').textContent = jakartaClock.format(now).replaceAll('.', ':') + ' WIB';
        };
        updateClock();
        setInterval(updateClock, 1000);

        document.querySelectorAll('.attendance-trigger').forEach((button) => {
            button.addEventListener('click', () => {
                const isCheckOut = button.dataset.mode === 'pulang';
                document.getElementById('attendanceLabel').textContent = isCheckOut ? 'Konfirmasi Absensi Pulang' : 'Konfirmasi Absensi Masuk';
                document.getElementById('attendanceMessage').textContent = isCheckOut ? 'Jam pulang akan dicatat berdasarkan waktu Indonesia Barat.' : 'Jam masuk akan dicatat berdasarkan waktu Indonesia Barat. Setelah pukul 08.15 WIB status otomatis menjadi terlambat.';
                document.getElementById('attendanceForm').action = isCheckOut ? @json(route('peserta.aktivitas-magang.absensi.pulang')) : @json(route('peserta.aktivitas-magang.absensi.masuk'));
                document.getElementById('attendanceSubmit').textContent = isCheckOut ? 'Catat Absensi Pulang' : 'Catat Absensi Masuk';
            });
        });

        document.querySelectorAll('.detail-trigger').forEach((button) => {
            button.addEventListener('click', () => {
                ['Date', 'Status', 'In', 'Out', 'Note'].forEach((key) => {
                    document.getElementById('detail' + key).textContent = button.dataset[key.toLowerCase()] || '-';
                });
                const link = document.getElementById('detailAttachment');
                const empty = document.getElementById('detailNoAttachment');
                link.href = button.dataset.attachment || '#';
                link.classList.toggle('d-none', !button.dataset.attachment);
                empty.classList.toggle('d-none', Boolean(button.dataset.attachment));
            });
        });
    </script>
</body>
</html>
