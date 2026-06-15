<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Magang - Riwayat Kegiatan</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .chart-card, .recent-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .history-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .bar-row { display:grid; grid-template-columns:110px 1fr 42px; align-items:center; gap:10px; margin-bottom:14px; }
        .recent-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .recent-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } .bar-row { grid-template-columns:1fr; gap:4px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $context = $pesertaContext ?? [];
        $user = $context['user'] ?? auth()->user();
        $stats = $context['stats'] ?? ($stats ?? []);
        $peserta = $context['peserta'] ?? ($peserta ?? null);
        $internship = $context['internship'] ?? ($internship ?? null);
        $notificationCount = $stats['notification_unread'] ?? 0;
        $messageCount = $stats['message_unread'] ?? 0;
        $userName = $user?->name ?? 'Peserta Magang';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $timelineRows = collect($activityTimeline ?? []);
        if ($timelineRows->isEmpty()) {
            $reportCollection = isset($reports) ? collect($reports) : collect();
            $logbookCollection = isset($logbooks) ? collect($logbooks) : collect();
            $assignmentCollection = isset($assignments) ? collect($assignments) : collect();
            $activityCollection = isset($activities) ? collect($activities) : collect();

            $timelineRows = collect()
                ->concat($reportCollection->map(function ($report) {
                    $statusKey = strtolower((string) ($report->status ?? ''));
                    $statusMeta = match ($statusKey) {
                        'approved', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                        'pending', 'menunggu' => ['label' => 'Berjalan', 'class' => 'info text-dark'],
                        'revisi', 'rejected', 'ditolak' => ['label' => 'Terlambat', 'class' => 'danger'],
                        default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Selesai', 'class' => 'success'],
                    };

                    return [
                        'tanggal' => optional($report->created_at)->translatedFormat('d M Y') ?? '-',
                        'jenis' => 'Laporan',
                        'judul' => $report->judul ?: 'Laporan Magang',
                        'kategori' => $report->jenis ?: 'Administrasi',
                        'status_label' => $statusMeta['label'],
                        'status_class' => $statusMeta['class'],
                        'durasi' => $report->periode ?: '-',
                        'sumber' => 'Laporan',
                        'created_at' => $report->created_at,
                    ];
                }))
                ->concat($logbookCollection->map(function ($logbook) {
                    $statusKey = strtolower((string) ($logbook->status ?? ''));
                    $statusMeta = match ($statusKey) {
                        'approved', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                        'pending', 'menunggu' => ['label' => 'Berjalan', 'class' => 'info text-dark'],
                        'revisi', 'rejected', 'ditolak' => ['label' => 'Terlambat', 'class' => 'danger'],
                        default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Berjalan', 'class' => 'warning text-dark'],
                    };

                    return [
                        'tanggal' => optional($logbook->tanggal)->translatedFormat('d M Y') ?? '-',
                        'jenis' => 'Logbook',
                        'judul' => $logbook->kegiatan ?: 'Logbook Harian',
                        'kategori' => 'Harian',
                        'status_label' => $statusMeta['label'],
                        'status_class' => $statusMeta['class'],
                        'durasi' => '-',
                        'sumber' => 'Logbook',
                        'created_at' => $logbook->created_at,
                    ];
                }))
                ->concat($assignmentCollection->map(function ($assignment) {
                    $statusKey = strtolower((string) ($assignment->status ?? ''));
                    $statusMeta = match ($statusKey) {
                        'selesai', 'done', 'completed', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                        'terlambat', 'late' => ['label' => 'Terlambat', 'class' => 'danger'],
                        default => ['label' => 'Berjalan', 'class' => 'info text-dark'],
                    };

                    return [
                        'tanggal' => optional($assignment->deadline)->translatedFormat('d M Y') ?? '-',
                        'jenis' => 'Penugasan',
                        'judul' => $assignment->judul ?: 'Penugasan',
                        'kategori' => $assignment->prioritas ?: 'Tugas',
                        'status_label' => $statusMeta['label'],
                        'status_class' => $statusMeta['class'],
                        'durasi' => ($assignment->progress ?? 0) . '%',
                        'sumber' => 'Penugasan',
                        'created_at' => $assignment->created_at,
                    ];
                }))
                ->concat($activityCollection->map(function ($activity) {
                    return [
                        'tanggal' => optional($activity->created_at)->translatedFormat('d M Y') ?? '-',
                        'jenis' => 'Aktivitas',
                        'judul' => $activity->aktivitas ?: 'Aktivitas Sistem',
                        'kategori' => 'Administrasi',
                        'status_label' => 'Selesai',
                        'status_class' => 'success',
                        'durasi' => '-',
                        'sumber' => 'Aktivitas Magang',
                        'created_at' => $activity->created_at,
                    ];
                }))
                ->sortByDesc('created_at')
                ->values();
        }
        $timelineTotal = $timelineRows->count();
        $timelineDone = $timelineRows->whereIn('status_label', ['Selesai'])->count();
        $timelineWorking = $timelineRows->where('status_label', 'Berjalan')->count();
        $timelineLate = $timelineRows->where('status_label', 'Terlambat')->count();
        $timelineProgress = $timelineTotal > 0 ? (int) round(($timelineDone / $timelineTotal) * 100) : 0;
        $timelineSources = $timelineRows->pluck('sumber')->unique()->count();
        $latestTimeline = $timelineRows->first();
        $timelineRowsForJs = $timelineRows->map(function ($timeline) {
            return [
                'tanggal' => $timeline['tanggal'] ?? '-',
                'jenis' => $timeline['jenis'] ?? '-',
                'judul' => $timeline['judul'] ?? '-',
                'kategori' => $timeline['kategori'] ?? '-',
                'status_label' => $timeline['status_label'] ?? '-',
                'status_class' => $timeline['status_class'] ?? 'secondary',
                'durasi' => $timeline['durasi'] ?? '-',
                'sumber' => $timeline['sumber'] ?? '-',
            ];
        })->values();
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
                <a href="{{ route('peserta.aktivitas-magang.absensi') }}">Absensi</a>
                <a href="{{ route('peserta.aktivitas-magang.penugasan') }}">Penugasan</a>
                <a href="{{ route('peserta.aktivitas-magang.riwayat') }}" class="active">Riwayat Kegiatan</a>
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
                        <h2 class="fw-bold mb-1">Riwayat Kegiatan</h2>
                        <div class="small-muted">Dokumentasi aktivitas dari absensi, logbook, penugasan, dan aktivitas magang.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Kegiatan</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Seluruh riwayat kegiatan tersimpan terintegrasi dan dapat digunakan untuk monitoring serta evaluasi magang.</div>
            </div>

            <section class="history-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">{{ $peserta?->program_magang ?? 'Program magang belum tersedia' }} | {{ $internship?->instansi ?? 'Instansi belum tersedia' }} | {{ $latestTimeline['tanggal'] ?? 'Belum ada riwayat' }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">{{ $timelineDone }} Aktivitas Tercatat</span>
                                    <span class="badge bg-info text-dark">{{ $timelineSources }} Sumber Data</span>
                                    <span class="badge bg-warning text-dark">{{ $timelineLate }} Perlu Tindak Lanjut</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Rekap Aktivitas</strong><span>{{ $timelineProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-success" style="width:{{ $timelineProgress }}%"></div></div>
                            <div class="small-muted mt-2">Rekap dihitung dari data riwayat yang tersimpan di database.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Kegiatan</div><h4 class="fw-bold mb-1">{{ $timelineTotal }}</h4><span class="badge bg-primary">Aktivitas</span></div><span class="stat-icon bg-primary"><i class="bi bi-collection"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Selesai</div><h4 class="fw-bold mb-1">{{ $timelineDone }}</h4><span class="badge bg-success">Tuntas</span></div><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Dikerjakan</div><h4 class="fw-bold mb-1">{{ $timelineWorking }}</h4><span class="badge bg-info text-dark">Berjalan</span></div><span class="stat-icon bg-info"><i class="bi bi-hourglass-split"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Terlambat</div><h4 class="fw-bold mb-1">{{ $timelineLate }}</h4><span class="badge bg-danger">Prioritas</span></div><span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Rekap</div><h4 class="fw-bold mb-1">{{ $timelineProgress }}%</h4><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $timelineProgress }}%"></div></div></div><span class="stat-icon bg-warning"><i class="bi bi-graph-up-arrow"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" type="button" id="openLatestHistoryButton"><span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Lihat Detail</strong><span class="small-muted">Buka informasi kegiatan</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#historyToast"><span class="action-icon bg-success mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Ekspor Riwayat</strong><span class="small-muted">Unduh data mentah</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#exportModal"><span class="action-icon bg-danger mb-2"><i class="bi bi-filetype-pdf"></i></span><strong class="d-block">Unduh Rekap PDF</strong><span class="small-muted">Rekap formal</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#reportModal"><span class="action-icon bg-info mb-2"><i class="bi bi-file-earmark-text"></i></span><strong class="d-block">Lihat Laporan</strong><span class="small-muted">Laporan kegiatan</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Riwayat Kegiatan</h5>
                                <div class="small-muted">Rekap aktivitas dari absensi, logbook, penugasan, dan aktivitas magang.</div>
                            </div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#historyInfoPanel" aria-controls="historyInfoPanel">
                                <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                            </button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari judul kegiatan"></div>
                                </div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Jenis</label><select class="form-select"><option>Semua</option><option>Absensi</option><option>Logbook</option><option>Penugasan</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Kategori</label><select class="form-select"><option>Semua</option><option>Harian</option><option>Administrasi</option><option>Tugas</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Selesai</option><option>Berjalan</option><option>Terlambat</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Periode</label><input type="date" class="form-control" value="2026-05-28"></div>
                                <div class="col-lg-1 d-flex gap-2"><button class="btn btn-primary" type="button" data-bs-toggle="toast" data-bs-target="#historyToast">Filter</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Tanggal</th><th>Jenis Kegiatan</th><th>Judul Kegiatan</th><th>Kategori</th><th>Status</th><th>Durasi</th><th>Sumber</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($timelineRows as $timeline)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $timeline['tanggal'] }}</td>
                                                <td>{{ $timeline['jenis'] }}</td>
                                                <td><strong>{{ $timeline['judul'] }}</strong></td>
                                                <td>{{ $timeline['kategori'] }}</td>
                                                <td><span class="badge bg-{{ $timeline['status_class'] }}">{{ $timeline['status_label'] }}</span></td>
                                                <td>{{ $timeline['durasi'] }}</td>
                                                <td>{{ $timeline['sumber'] }}</td>
                                                <td><button class="btn btn-sm btn-outline-primary timeline-detail-button" type="button" data-timeline-index="{{ $loop->index }}">Detail</button></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <div class="small-muted">Belum ada riwayat kegiatan dari database.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $timelineRows->count() ? '1-' . min(4, $timelineRows->count()) : '0' }} dari {{ $timelineRows->count() }} riwayat</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select></div>
                            <nav aria-label="Pagination riwayat"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">3</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyLabel">Detail Riwayat Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="historyDetailContent">
                    <div class="text-center text-secondary py-4">Pilih riwayat kegiatan untuk melihat detailnya.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="exportLabel">Konfirmasi Ekspor Riwayat</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Riwayat kegiatan akan diekspor sesuai filter aktif dan tindakan ini akan dicatat sistem.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#historyToast">Lanjutkan</button></div></div></div>
    </div>
    <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="reportLabel">Laporan Kegiatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Laporan kegiatan berisi rekap aktivitas dari absensi, logbook, penugasan, dan catatan administrasi magang.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="historyToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Notifikasi Sistem</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan riwayat kegiatan berhasil diproses. Histori akses dan data rekap diperbarui.</div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="historyInfoPanel" aria-labelledby="historyInfoPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="historyInfoPanelLabel">Panel Informasi Riwayat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <section class="chart-card p-3 p-lg-4 mb-4">
                <h5 class="fw-bold mb-1">Grafik Rekap Kegiatan</h5>
                <div class="small-muted mb-3">Distribusi aktivitas berdasarkan sumber.</div>
                <div class="bar-row"><strong>Absensi</strong><div class="progress"><div class="progress-bar bg-success" style="width:{{ $timelineSources > 0 ? (int) round((collect($timelineRows)->where('jenis', 'Absensi')->count() / max($timelineTotal, 1)) * 100) : 0 }}%"></div></div><span>{{ collect($timelineRows)->where('jenis', 'Absensi')->count() }}</span></div>
                <div class="bar-row"><strong>Logbook</strong><div class="progress"><div class="progress-bar bg-info" style="width:{{ $timelineSources > 0 ? (int) round((collect($timelineRows)->where('jenis', 'Logbook')->count() / max($timelineTotal, 1)) * 100) : 0 }}%"></div></div><span>{{ collect($timelineRows)->where('jenis', 'Logbook')->count() }}</span></div>
                <div class="bar-row"><strong>Penugasan</strong><div class="progress"><div class="progress-bar bg-warning" style="width:{{ $timelineSources > 0 ? (int) round((collect($timelineRows)->where('jenis', 'Penugasan')->count() / max($timelineTotal, 1)) * 100) : 0 }}%"></div></div><span>{{ collect($timelineRows)->where('jenis', 'Penugasan')->count() }}</span></div>
                <div class="bar-row"><strong>Lainnya</strong><div class="progress"><div class="progress-bar bg-primary" style="width:{{ $timelineSources > 0 ? (int) round((collect($timelineRows)->whereNotIn('jenis', ['Absensi', 'Logbook', 'Penugasan'])->count() / max($timelineTotal, 1)) * 100) : 0 }}%"></div></div><span>{{ collect($timelineRows)->whereNotIn('jenis', ['Absensi', 'Logbook', 'Penugasan'])->count() }}</span></div>
            </section>
            <section class="recent-card p-3 p-lg-4">
                <h5 class="fw-bold mb-1">Aktivitas Terbaru</h5>
                <div class="small-muted mb-2">Update terakhir dari sistem.</div>
                @forelse ($timelineRows->take(3) as $timeline)
                    <div class="recent-item">
                        <strong>{{ $timeline['judul'] }}</strong>
                        <div class="small-muted">{{ $timeline['tanggal'] }} - {{ $timeline['sumber'] }}</div>
                    </div>
                @empty
                    <div class="recent-item">
                        <strong>Belum ada aktivitas</strong>
                        <div class="small-muted">Data terbaru akan muncul setelah sistem menyimpan riwayat ke database.</div>
                    </div>
                @endforelse
            </section>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        const timelineRows = @json($timelineRowsForJs ?? []);
        const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
        const historyDetailContent = document.getElementById('historyDetailContent');

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function renderHistoryDetail(item) {
            if (!item) {
                historyDetailContent.innerHTML = '<div class="text-center text-secondary py-4">Belum ada data riwayat untuk ditampilkan.</div>';
                return;
            }

            historyDetailContent.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-semibold mb-3">Informasi Riwayat</h6>
                            <div class="row g-2 small">
                                <div class="col-5 text-muted">Tanggal</div>
                                <div class="col-7 fw-semibold">${escapeHtml(item.tanggal)}</div>
                                <div class="col-5 text-muted">Jenis Kegiatan</div>
                                <div class="col-7">${escapeHtml(item.jenis)}</div>
                                <div class="col-5 text-muted">Judul Kegiatan</div>
                                <div class="col-7">${escapeHtml(item.judul)}</div>
                                <div class="col-5 text-muted">Kategori</div>
                                <div class="col-7">${escapeHtml(item.kategori)}</div>
                                <div class="col-5 text-muted">Status</div>
                                <div class="col-7"><span class="badge bg-${escapeHtml(item.status_class || 'secondary')}">${escapeHtml(item.status_label)}</span></div>
                                <div class="col-5 text-muted">Durasi / Progress</div>
                                <div class="col-7">${escapeHtml(item.durasi)}</div>
                                <div class="col-5 text-muted">Sumber Data</div>
                                <div class="col-7">${escapeHtml(item.sumber)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="border rounded p-3 h-100">
                            <h6 class="fw-semibold mb-3">Catatan</h6>
                            <div class="text-secondary small">
                                Data riwayat ini ditarik langsung dari database berdasarkan sumber aktivitas yang sudah tersimpan.
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        document.querySelectorAll('[data-bs-target="#historyToast"]').forEach((button) => {
            button.addEventListener('click', () => {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('historyToast')).show();
            });
        });

        document.getElementById('openLatestHistoryButton')?.addEventListener('click', () => {
            renderHistoryDetail(timelineRows[0] || null);
            historyModal.show();
        });

        document.querySelectorAll('.timeline-detail-button').forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.timelineIndex);
                renderHistoryDetail(timelineRows[index] || null);
                historyModal.show();
            });
        });
    </script>
</body>
</html>
