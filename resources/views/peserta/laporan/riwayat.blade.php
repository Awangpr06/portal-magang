<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan Peserta</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .info-card, .timeline-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .history-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .timeline-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .timeline-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $hideInfoPanel = (auth()->user()->role ?? null) === 'peserta';
        $reportItems = $reports ?? collect();
        $reportPayloads = $reportItems->map(function ($report) {
            $statusKey = strtolower($report->status ?? 'draft');
            $jenisRaw = strtolower(trim((string) ($report->jenis ?? 'berkala')));
            $isFinalReport = str_contains($jenisRaw, 'akhir') || str_contains($jenisRaw, 'final');
            $mentorStatusLabel = filled($report->catatan_mentor)
                ? (in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true) ? 'Perlu Revisi' : 'Disetujui')
                : 'Menunggu';
            $pembimbingReviewStatus = strtolower((string) ($report->pembimbing_review_status ?? 'menunggu review'));
            $pembimbingStatusLabel = match ($pembimbingReviewStatus) {
                'disetujui' => 'Disetujui',
                'perlu revisi', 'revisi', 'rejected', 'ditolak' => 'Perlu Revisi',
                default => 'Menunggu',
            };
            $adminStatusLabel = strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir'
                ? (filled($report->admin_approved_at) ? 'Disetujui Admin' : 'Menunggu Admin')
                : '-';

            return [
                'id' => $report->id,
                'judul' => $report->judul,
                'jenis_raw' => $isFinalReport ? 'akhir' : 'berkala',
                'jenis' => $isFinalReport ? 'Akhir' : 'Berkala',
                'periode' => $report->periode ?? '-',
                'durasi' => $report->durasi_jam ? $report->durasi_jam.' jam' : '-',
                'tanggal' => optional($report->created_at)->translatedFormat('d F Y') ?? '-',
                'status' => match ($statusKey) {
                    'approved', 'disetujui' => 'Disetujui',
                    'pending', 'menunggu' => 'Menunggu',
                    'revisi', 'rejected', 'ditolak' => 'Perlu Revisi',
                    default => 'Draft',
                },
                'status_key' => $statusKey,
                'mentor_status_label' => $mentorStatusLabel,
                'pembimbing_status_label' => $pembimbingStatusLabel,
                'pembimbing_review_status' => $pembimbingReviewStatus,
                'admin_status_label' => $adminStatusLabel,
                'catatan' => $report->catatan ?: 'Laporan peserta',
                'catatan_mentor' => $report->catatan_mentor ?: '-',
                'catatan_pembimbing' => $report->catatan_pembimbing ?: '-',
                'file' => $report->file,
                'download_url' => route('reports.download', $report),
            ];
        })->values();
        $approvedReports = $reportItems->whereIn('status', ['approved', 'disetujui'])->count();
        $waitingReports = $reportItems->whereIn('status', ['pending', 'menunggu'])->count();
        $revisionReports = $reportItems->whereIn('status', ['revisi', 'rejected', 'ditolak'])->count();
        $draftReports = $reportItems->where('status', 'draft')->count();
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.laporan') }}"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="true" aria-controls="laporanMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="laporanMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.laporan.input') }}">Input Laporan</a>
                <a href="{{ route('peserta.laporan.riwayat') }}" class="active">Riwayat Laporan</a>
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
                        <h2 class="fw-bold mb-1">Riwayat Laporan</h2>
                        <div class="small-muted">Pantau histori laporan, status persetujuan, revisi, dan tindak lanjut laporan magang.</div>
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
                    <li class="breadcrumb-item"><a href="{{ route('peserta.laporan') }}">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Laporan</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Riwayat laporan ditampilkan berdasarkan data terbaru dan hanya dapat diakses oleh peserta terkait.</div>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-bold mb-1">Terjadi masalah saat memproses laporan:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="history-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">Dokumentasi Laporan Magang</h3>
                                <div class="text-secondary">{{ $userName }} | UI/UX Intern | PT Solusi Teknologi</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success" id="reportApprovedCount">{{ $approvedReports }} Disetujui</span>
                                    <span class="badge bg-warning text-dark" id="reportWaitingCount">{{ $waitingReports }} Menunggu</span>
                                    <span class="badge bg-danger" id="reportRevisionCount">{{ $revisionReports }} Perlu Revisi</span>
                                    <span class="badge bg-info text-dark" id="reportDraftCount">{{ $draftReports }} Draft</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            @php
                                $reportProgress = $reportItems->count() > 0 ? (int) round(($approvedReports / max($reportItems->count(), 1)) * 100) : 0;
                            @endphp
                            <div class="d-flex justify-content-between mb-2"><strong>Progress Persetujuan</strong><span id="reportProgressValue">{{ $reportProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-success" id="reportProgressBar" style="width:{{ $reportProgress }}%"></div></div>
                            <div class="small-muted mt-2">Data diambil langsung dari tabel `reports` peserta.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Laporan</div><h4 class="fw-bold mb-1">{{ $reportItems->count() }}</h4><span class="badge bg-primary">Histori</span></div><span class="stat-icon bg-primary"><i class="bi bi-journals"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Disetujui</div><h4 class="fw-bold mb-1">{{ $approvedReports }}</h4><span class="badge bg-success">Valid</span></div><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Menunggu</div><h4 class="fw-bold mb-1">{{ $waitingReports }}</h4><span class="badge bg-warning text-dark">Verifikasi</span></div><span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Perlu Revisi</div><h4 class="fw-bold mb-1">{{ $revisionReports }}</h4><span class="badge bg-danger">Tindak Lanjut</span></div><span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Draft</div><h4 class="fw-bold mb-1">{{ $draftReports }}</h4><span class="badge bg-info text-dark">Belum Dikirim</span></div><span class="stat-icon bg-info"><i class="bi bi-save"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#detailModal"><span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Lihat Detail</strong><span class="small-muted">Buka laporan terpilih</span></button></div>
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 border-0 text-start d-block text-decoration-none text-dark" href="{{ route('peserta.laporan.export', request()->query()) }}"><span class="action-icon bg-secondary mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Unduh Laporan</strong><span class="small-muted">Ambil file laporan</span></a></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#revisionModal"><span class="action-icon bg-warning mb-2"><i class="bi bi-cloud-upload"></i></span><strong class="d-block">Upload Revisi</strong><span class="small-muted">Kirim perbaikan</span></button></div>
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 border-0 text-start d-block text-decoration-none text-dark" href="{{ route('peserta.laporan.export', request()->query()) }}"><span class="action-icon bg-success mb-2"><i class="bi bi-file-earmark-arrow-down"></i></span><strong class="d-block">Unduh Rekap</strong><span class="small-muted">Ekspor seluruh histori</span></a></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#pesertaLaporanPanel" aria-controls="pesertaLaporanPanel"><i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel</button>
                        </div>
                        <form class="filter-card p-3 mb-4" method="GET" action="{{ route('peserta.laporan.riwayat') }}">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul laporan"></div>
                                </div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Jenis</label><select class="form-select" name="jenis"><option value="semua" @selected(request('jenis', 'semua') === 'semua')>Semua</option><option value="berkala" @selected(request('jenis') === 'berkala')>Berkala</option><option value="akhir" @selected(request('jenis') === 'akhir')>Akhir</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Periode</label><select class="form-select" name="periode"><option value="semua" @selected(request('periode', 'semua') === 'semua')>Semua</option>@foreach(($reportPeriodOptions ?? collect()) as $periodOption)<option value="{{ $periodOption }}" @selected(request('periode') === $periodOption)>{{ $periodOption }}</option>@endforeach</select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Status</label><select class="form-select" name="status"><option value="semua" @selected(request('status', 'semua') === 'semua')>Semua</option><option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option><option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option><option value="revisi" @selected(request('status') === 'revisi')>Revisi</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select></div>
                                <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary w-100" type="submit">Filter</button><a class="btn btn-outline-secondary w-100" href="{{ route('peserta.laporan.riwayat') }}">Reset</a></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Judul/Jenis Laporan</th><th>Periode</th><th>Durasi</th><th>Tanggal Dibuat</th><th>Status Mentor</th><th>Status Pembimbing</th><th>Status Admin</th><th>Penilai/Pembimbing</th><th>Catatan Mentor</th><th>Catatan Pembimbing Akademik</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($reportItems as $report)
                                            @php
                                                $statusKey = strtolower((string) ($report->status ?? 'draft'));
                                                $statusClass = match ($statusKey) {
                                                    'approved', 'disetujui' => 'bg-success',
                                                    'pending', 'menunggu' => 'bg-warning text-dark',
                                                    'revisi', 'rejected', 'ditolak' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                                $jenisLabel = strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir' ? 'Laporan Akhir' : 'Laporan Berkala';
                                                $mentorStatus = filled($report->catatan_mentor)
                                                    ? (in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true) ? 'Perlu Revisi' : 'Disetujui')
                                                    : 'Menunggu';
                                                $pembimbingStatusKey = strtolower((string) ($report->pembimbing_review_status ?? 'menunggu review'));
                                                $pembimbingStatus = match ($pembimbingStatusKey) {
                                                    'disetujui' => 'Disetujui',
                                                    'perlu revisi', 'revisi', 'rejected', 'ditolak' => 'Perlu Revisi',
                                                    default => 'Menunggu',
                                                };
                                                $penilai = $report->reviewer?->name ?? $peserta?->internship?->mentor?->user?->name ?? $peserta?->internship?->pembimbing?->user?->name ?? '-';
                                                $actionDownload = $report->file ? route('reports.download', $report) : null;
                                            @endphp
                                            <tr data-report-index="{{ $loop->index }}" data-report-id="{{ $report->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $report->judul ?: 'Laporan Magang' }}</strong><div class="small-muted">{{ $jenisLabel }}</div></td>
                                                <td>{{ $report->periode ?: '-' }}</td>
                                                <td>{{ $report->durasi_jam ? $report->durasi_jam.' jam' : '-' }}</td>
                                                <td>{{ optional($report->created_at)->translatedFormat('d M Y') ?? '-' }}</td>
                                                <td data-report-status><span class="badge {{ $mentorStatus === 'Disetujui' ? 'bg-success' : ($mentorStatus === 'Perlu Revisi' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $mentorStatus }}</span></td>
                                                <td><span class="badge {{ $pembimbingStatus === 'Disetujui' ? 'bg-success' : ($pembimbingStatus === 'Perlu Revisi' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $pembimbingStatus }}</span></td>
                                                <td>
                                                    @if (strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir')
                                                        <span class="badge {{ filled($report->admin_approved_at) ? 'bg-success' : 'bg-warning text-dark' }}">
                                                            {{ filled($report->admin_approved_at) ? 'Disetujui Admin' : 'Menunggu Admin' }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $penilai }}</td>
                                                <td data-report-mentor>{{ $report->catatan_mentor ?: '-' }}</td>
                                                <td>{{ $report->catatan_pembimbing ?: '-' }}</td>
                                                <td data-report-actions>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" type="button" data-action="detail" data-report-id="{{ $report->id }}">Detail</button>
                                                        @if ($actionDownload)
                                                            <a class="btn btn-outline-secondary" href="{{ $actionDownload }}" target="_blank" rel="noopener">Unduh</a>
                                                        @endif
                                                        @if (in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true))
                                                            <a class="btn btn-outline-success" href="{{ route('peserta.laporan.input', ['report_id' => $report->id]) }}">Edit</a>
                                                        @elseif ($statusKey === 'pending' || $statusKey === 'menunggu')
                                                            <a class="btn btn-outline-warning" href="{{ route('peserta.laporan.input', ['report_id' => $report->id]) }}">Kirim Ulang</a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4">
                                                    <div class="small-muted">Belum ada laporan yang tersimpan di database.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan 1-{{ min($reportItems->count(), 4) }} dari {{ $reportItems->count() }} laporan</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select></div>
                            <nav aria-label="Pagination riwayat laporan"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">3</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="pesertaLaporanPanel" aria-labelledby="pesertaLaporanPanelLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="pesertaLaporanPanelLabel">Panel Riwayat Laporan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
                    </div>
                    <div class="offcanvas-body">
                    <section class="info-card p-3 p-lg-4 mb-4">
                        <h5 class="fw-bold mb-1">Panel Informasi</h5>
                        <div class="small-muted mb-3">Ringkasan status laporan yang perlu diperhatikan.</div>
                        <div class="mb-3"><div class="d-flex justify-content-between"><strong>Persetujuan</strong><span>{{ $approvedReports }}</span></div><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $reportItems->count() > 0 ? (int) round(($approvedReports / max($reportItems->count(), 1)) * 100) : 0 }}%"></div></div></div>
                        <div class="mb-3"><div class="d-flex justify-content-between"><strong>Revisi</strong><span>{{ $revisionReports }} laporan</span></div><div class="progress mt-2"><div class="progress-bar bg-danger" style="width:{{ $reportItems->count() > 0 ? (int) round(($revisionReports / max($reportItems->count(), 1)) * 100) : 0 }}%"></div></div></div>
                        <div class="alert alert-warning mb-0">Laporan yang berstatus revisi akan menampilkan aksi upload revisi di tabel.</div>
                    </section>
                    <section class="timeline-card p-3 p-lg-4">
                        <h5 class="fw-bold mb-1">Aktivitas Terbaru</h5>
                        <div class="small-muted mb-2">Perubahan terakhir pada histori laporan.</div>
                        <div class="timeline-item"><strong>Laporan Mingguan 8 disetujui</strong><div class="small-muted">28 Mei 2026 oleh Dr. Rina Prasetya</div><span class="badge bg-success mt-1">Disetujui</span></div>
                        <div class="timeline-item"><strong>Laporan Mingguan 9 dikirim</strong><div class="small-muted">28 Mei 2026 oleh {{ $userName }}</div><span class="badge bg-warning text-dark mt-1">Menunggu</span></div>
                        <div class="timeline-item"><strong>Catatan revisi diterima</strong><div class="small-muted">21 Mei 2026, lampiran perlu ditambahkan</div><span class="badge bg-danger mt-1">Revisi</span></div>
                    </section>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailLabel">Detail Riwayat Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent" class="row g-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a id="detailDownloadLink" class="btn btn-primary" href="#" target="_blank" rel="noopener">Unduh Laporan</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="revisionLabel">Upload Revisi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <form method="POST" action="{{ route('peserta.laporan.input.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="report_id" id="revisionReportId">
                    <div class="modal-body">
                        <label class="form-label">File Revisi</label>
                        <input class="form-control mb-2" type="file" name="file" required>
                        <div class="small-muted">Unggah file revisi sesuai catatan verifikator.</div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning">Upload Revisi</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Tindakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pastikan tindakan pada laporan sudah benar sebelum sistem memproses perubahan riwayat.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#historyToast">Lanjutkan</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="historyToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Riwayat Laporan</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan laporan berhasil diproses dan riwayat laporan diperbarui.</div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        const reportItems = @json($reportPayloads);
        const currentUserName = @json($userName);
        const mentorNoteStorageKey = 'portal_magang_mentor_notes';
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

        function loadMentorNotes() {
            try {
                return JSON.parse(localStorage.getItem(mentorNoteStorageKey) || '[]');
            } catch (error) {
                return [];
            }
        }

        function applyMentorNotesToHistory() {
            const notes = loadMentorNotes();
            const note = notes.find((entry) => (entry.participantName || '').toLowerCase() === currentUserName.toLowerCase());
            if (!note || !reportItems.length) return;

            const targetReport = reportItems[0];
            targetReport.catatan_mentor = note.note;
            targetReport.status = 'Perlu Revisi';
            targetReport.status_key = 'revisi';
            targetReport.status_class = 'bg-danger';

            const firstRow = document.querySelector('tr[data-report-index="0"]');
            if (!firstRow) return;

            const statusCell = firstRow.querySelector('[data-report-status]');
            if (statusCell) {
                statusCell.innerHTML = '<span class="badge bg-danger">Perlu Revisi</span>';
            }

            const mentorCell = firstRow.querySelector('[data-report-mentor]');
            if (mentorCell) {
                mentorCell.textContent = note.note;
            }

            const actionCell = firstRow.querySelector('[data-report-actions]');
            if (actionCell) {
                actionCell.innerHTML = `
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" type="button" data-action="detail" data-report-id="${targetReport.id}">Detail</button>
                        <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#revisionModal" data-revision-report-id="${targetReport.id}">Upload Revisi</button>
                    </div>
                `;
            }
        }

        function refreshReportSummary() {
            const approved = reportItems.filter((item) => ['approved', 'disetujui'].includes((item.status_key || '').toLowerCase())).length;
            const waiting = reportItems.filter((item) => ['pending', 'menunggu'].includes((item.status_key || '').toLowerCase())).length;
            const revision = reportItems.filter((item) => ['revisi', 'rejected', 'ditolak'].includes((item.status_key || '').toLowerCase())).length;
            const draft = reportItems.filter((item) => (item.status_key || '').toLowerCase() === 'draft').length;
            const progress = reportItems.length > 0 ? Math.round((approved / reportItems.length) * 100) : 0;

            document.getElementById('reportApprovedCount').textContent = `${approved} Disetujui`;
            document.getElementById('reportWaitingCount').textContent = `${waiting} Menunggu`;
            document.getElementById('reportRevisionCount').textContent = `${revision} Perlu Revisi`;
            document.getElementById('reportDraftCount').textContent = `${draft} Draft`;
            document.getElementById('reportProgressValue').textContent = `${progress}%`;
            document.getElementById('reportProgressBar').style.width = `${progress}%`;
        }

        applyMentorNotesToHistory();
        refreshReportSummary();

        function openReportDetail(reportId) {
            const report = reportItems.find((item) => Number(item.id) === Number(reportId));
            if (!report) return;

            document.getElementById('detailLabel').textContent = `Detail Laporan - ${report.judul}`;
            document.getElementById('detailContent').innerHTML = `
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Judul</small><h6 class="mb-0">${report.judul}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Jenis</small><h6 class="mb-0">${report.jenis}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Periode</small><h6 class="mb-0">${report.periode}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Jam</small><h6 class="mb-0">${report.durasi}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Tanggal Dibuat</small><h6 class="mb-0">${report.tanggal}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Status Mentor</small><h6 class="mb-0">${report.mentor_status_label}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Status Pembimbing</small><h6 class="mb-0">${report.pembimbing_status_label}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Status Admin</small><h6 class="mb-0">${report.admin_status_label || '-'}</h6></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan Laporan</small><p class="mb-0">${report.catatan}</p></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan Mentor</small><p class="mb-0">${report.catatan_mentor}</p></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan Pembimbing Akademik</small><p class="mb-0">${report.catatan_pembimbing}</p></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">File Lampiran</small><div class="mb-0 text-break">${report.file || '-'}</div></div></div>
            `;
            document.getElementById('detailDownloadLink').href = report.download_url;
            detailModal.show();
        }

        document.querySelectorAll('[data-bs-toggle="toast"]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.querySelector(button.dataset.bsTarget);
                if (target) bootstrap.Toast.getOrCreateInstance(target).show();
            });
        });

        document.addEventListener('click', (event) => {
            const detailButton = event.target.closest('[data-action="detail"]');
            if (detailButton) {
                openReportDetail(detailButton.dataset.reportId);
                return;
            }

            const revisionButton = event.target.closest('[data-revision-report-id]');
            if (revisionButton) {
                const target = document.getElementById('revisionReportId');
                if (target) target.value = revisionButton.dataset.reportId;
            }
        });
    </script>
</body>
</html>
