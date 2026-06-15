<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Magang - Logbook Harian</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .info-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .logbook-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
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
        $hideInfoPanel = (auth()->user()->role ?? null) === 'peserta';
        $logbookCollection = isset($logbooks) ? collect($logbooks) : collect();
        $logbookStats = $stats;

        $logbookRows = $logbookCollection->values()->map(function ($logbook, $index) {
            $statusKey = strtolower((string) ($logbook->status ?? ''));
            $statusMeta = match ($statusKey) {
                'approved', 'disetujui' => ['label' => 'Disetujui', 'class' => 'success'],
                'pending', 'menunggu' => ['label' => 'Menunggu Review', 'class' => 'warning text-dark'],
                'revisi', 'rejected', 'ditolak' => ['label' => 'Perlu Revisi', 'class' => 'danger'],
                default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Draft', 'class' => 'secondary'],
            };

            return [
                'no' => $index + 1,
                'tanggal' => optional($logbook->tanggal)->translatedFormat('d M Y') ?? '-',
                'hari' => optional($logbook->tanggal)->translatedFormat('l') ?? '-',
                'kegiatan' => $logbook->kegiatan ?: '-',
                'deskripsi' => $logbook->deskripsi ?: '-',
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
                'created_by' => $userName,
            ];
        });

        $logbookTotal = $logbookStats['logbook_total'] ?? $logbookRows->count();
        $logbookApproved = $logbookStats['logbook_approved'] ?? $logbookCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['approved', 'disetujui'], true))->count();
        $logbookWaiting = $logbookStats['logbook_waiting'] ?? $logbookCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['pending', 'menunggu'], true))->count();
        $logbookRevision = $logbookStats['logbook_revision'] ?? $logbookCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['revisi', 'rejected', 'ditolak'], true))->count();
        $logbookProgress = $logbookTotal > 0 ? (int) round(($logbookApproved / $logbookTotal) * 100) : 0;
        $latestLogbook = $logbookRows->first();
        $revisionLogbook = $logbookRows->first(fn ($item) => $item['status_class'] === 'danger');
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
                        <h2 class="fw-bold mb-1">Logbook Harian</h2>
                        <div class="small-muted">Dokumentasi pekerjaan, progres kegiatan, bukti pelaksanaan, dan status review.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Logbook Harian</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Logbook harian digunakan mentor dan pembimbing akademik untuk monitoring serta evaluasi aktivitas magang Anda.</div>
            </div>

            <section class="logbook-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">{{ $peserta?->program_magang ?? 'Program magang belum tersedia' }} | {{ $internship?->instansi ?? 'Instansi belum tersedia' }} | {{ $latestLogbook['tanggal'] ?? 'Belum ada logbook' }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">{{ $logbookApproved }} Logbook Disetujui</span>
                                    <span class="badge bg-warning text-dark">{{ $logbookWaiting }} Menunggu Review</span>
                                    <span class="badge bg-danger">{{ $logbookRevision }} Perlu Revisi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Progress Logbook</strong><span>{{ $logbookProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-success" style="width:{{ $logbookProgress }}%"></div></div>
                            <div class="small-muted mt-2">Total logbook tersinkron dari database: {{ $logbookTotal }} entri.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Logbook</div><h4 class="fw-bold mb-1">{{ $logbookTotal }}</h4><span class="badge bg-primary">Entri</span></div><span class="stat-icon bg-primary"><i class="bi bi-journal-text"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Disetujui</div><h4 class="fw-bold mb-1">{{ $logbookApproved }}</h4><span class="badge bg-success">Valid</span></div><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Menunggu Review</div><h4 class="fw-bold mb-1">{{ $logbookWaiting }}</h4><span class="badge bg-warning text-dark">Review</span></div><span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Perlu Revisi</div><h4 class="fw-bold mb-1">{{ $logbookRevision }}</h4><span class="badge bg-danger">Tindak lanjut</span></div><span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Progress</div><h4 class="fw-bold mb-1">{{ $logbookProgress }}%</h4><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $logbookProgress }}%"></div></div></div><span class="stat-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#logbookModal"><span class="action-icon bg-primary mb-2"><i class="bi bi-plus-circle"></i></span><strong class="d-block">Buat Logbook Baru</strong><span class="small-muted">Catat aktivitas hari ini</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#logbookToast"><span class="action-icon bg-success mb-2"><i class="bi bi-cloud-upload"></i></span><strong class="d-block">Unggah Lampiran</strong><span class="small-muted">Tambahkan bukti</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#recapModal"><span class="action-icon bg-info mb-2"><i class="bi bi-file-earmark-text"></i></span><strong class="d-block">Lihat Rekap</strong><span class="small-muted">Ringkasan mingguan</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#logbookToast"><span class="action-icon bg-secondary mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Unduh Rekap</strong><span class="small-muted">Ekspor logbook</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Logbook Harian</h5>
                                <div class="small-muted">Daftar logbook dan lampiran terbaru peserta.</div>
                            </div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#logbookInfoPanel" aria-controls="logbookInfoPanel">
                                <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                            </button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari judul atau ringkasan kegiatan"></div>
                                </div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Disetujui</option><option>Menunggu Review</option><option>Perlu Revisi</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Minggu</label><select class="form-select"><option>Minggu ke-4</option><option>Minggu ke-3</option><option>Minggu ke-2</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Tanggal</label><input type="date" class="form-control" value="2026-05-28"></div>
                                <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-fill" type="button" data-bs-toggle="toast" data-bs-target="#logbookToast">Filter</button><button class="btn btn-outline-secondary" type="reset">Reset</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Tanggal</th><th>Judul Kegiatan</th><th>Ringkasan Kegiatan</th><th>Status</th><th>Dibuat Oleh</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($logbookRows as $logbook)
                                            <tr>
                                                <td>{{ $logbook['no'] }}</td>
                                                <td><strong>{{ $logbook['tanggal'] }}</strong><div class="small-muted">{{ $logbook['hari'] }}</div></td>
                                                <td>{{ $logbook['kegiatan'] }}</td>
                                                <td>{{ $logbook['deskripsi'] }}</td>
                                                <td><span class="badge bg-{{ $logbook['status_class'] }}">{{ $logbook['status_label'] }}</span></td>
                                                <td>{{ $logbook['created_by'] }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button>
                                                        <button class="btn btn-outline-warning">Edit</button>
                                                        <button class="btn btn-outline-success" data-bs-toggle="toast" data-bs-target="#logbookToast">Lampiran</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="small-muted">Belum ada data logbook dari database.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $logbookRows->count() ? '1-' . min(4, $logbookRows->count()) : '0' }} dari {{ $logbookRows->count() }} logbook</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select></div>
                            <nav aria-label="Pagination logbook"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">3</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="logbookModal" tabindex="-1" aria-labelledby="logbookLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="logbookLabel">Konfirmasi Logbook</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pastikan data logbook harian sudah lengkap sebelum disimpan. Perubahan akan tercatat pada histori sistem.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#logbookToast">Simpan</button></div></div></div>
    </div>
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="detailLabel">Detail Logbook</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail logbook memuat ringkasan kegiatan, durasi, lampiran, status review mentor, dan catatan revisi bila tersedia.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div>
    </div>
    <div class="modal fade" id="recapModal" tabindex="-1" aria-labelledby="recapLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="recapLabel">Rekap Logbook</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Rekap menampilkan jumlah logbook, status review, progres mingguan, dan daftar lampiran pendukung.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div></div></div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="deleteLabel">Hapus Logbook</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Logbook yang dihapus akan dicatat pada histori perubahan. Lanjutkan tindakan ini?</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#logbookToast">Hapus</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="logbookToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Notifikasi Sistem</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan logbook berhasil diproses. Data, lampiran, dan status review diperbarui.</div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="logbookInfoPanel" aria-labelledby="logbookInfoPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="logbookInfoPanelLabel">Panel Informasi Logbook</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <section class="info-card p-3 p-lg-4 mb-4">
                <h5 class="fw-bold mb-1">Panel Informasi</h5>
                <div class="small-muted mb-3">Status review dan target pengisian logbook.</div>
                @if ($revisionLogbook)
                    <div class="alert alert-warning mb-3"><strong>{{ $revisionLogbook['status_label'] }}:</strong> {{ $revisionLogbook['deskripsi'] }}</div>
                @else
                    <div class="alert alert-success mb-3"><strong>Status Aman:</strong> belum ada logbook yang memerlukan revisi.</div>
                @endif
                <div class="alert alert-info mb-0"><strong>Target Mingguan:</strong> isi minimal 6 logbook pada minggu aktif.</div>
            </section>
            <section class="soft-card p-3 p-lg-4">
                <h5 class="fw-bold mb-3">Progress Logbook</h5>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Disetujui</strong><span>{{ $logbookTotal > 0 ? (int) round(($logbookApproved / $logbookTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $logbookTotal > 0 ? (int) round(($logbookApproved / $logbookTotal) * 100) : 0 }}%"></div></div></div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Menunggu Review</strong><span>{{ $logbookTotal > 0 ? (int) round(($logbookWaiting / $logbookTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-info" style="width:{{ $logbookTotal > 0 ? (int) round(($logbookWaiting / $logbookTotal) * 100) : 0 }}%"></div></div></div>
                <div><div class="d-flex justify-content-between"><strong>Perlu Revisi</strong><span>{{ $logbookTotal > 0 ? (int) round(($logbookRevision / $logbookTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-warning" style="width:{{ $logbookTotal > 0 ? (int) round(($logbookRevision / $logbookTotal) * 100) : 0 }}%"></div></div></div>
            </section>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelectorAll('[data-bs-target="#logbookToast"]').forEach((button) => {
            button.addEventListener('click', () => {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('logbookToast')).show();
            });
        });
    </script>
</body>
</html>
