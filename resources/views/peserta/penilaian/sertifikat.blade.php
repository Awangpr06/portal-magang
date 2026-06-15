<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Peserta</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .info-card, .certificate-preview { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .certificate-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .certificate-paper { border:8px solid #d7b56d; border-radius:8px; background:linear-gradient(135deg,#fffdf7,#f6fbfd); min-height:330px; display:flex; align-items:center; justify-content:center; text-align:center; padding:28px; position:relative; overflow:hidden; }
        .certificate-paper::before { content:""; position:absolute; inset:18px; border:1px solid rgba(42,143,189,.25); border-radius:4px; pointer-events:none; }
        .certificate-seal { width:70px; height:70px; border-radius:50%; background:#2a8fbd; color:#fff; display:grid; place-items:center; margin:0 auto 14px; font-size:32px; }
        .action-split { display:flex; width:100%; }
        .action-split .btn { flex:1; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media print {
            body { background:#fff; }
            .sidebar, .top-header, .footer-system, .filter-card, .table-card, .offcanvas, .toast-container, .alert, .no-print, .action-card, .stat-card, .certificate-hero, .certificate-info-panel, .modal, .modal-backdrop { display:none !important; }
            .main-content { margin:0 !important; }
            .content-wrap { padding:0 !important; }
            .certificate-preview { border:0 !important; box-shadow:none !important; }
            .certificate-paper { min-height: 600px; page-break-inside: avoid; }
        }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $hideInfoPanel = (auth()->user()->role ?? null) === 'peserta';
        $certificateCollection = $certificates ?? collect();
        $latestCertificate = $certificateCollection->sortByDesc('tanggal_terbit')->first();
        $latestAssessment = ($assessments ?? collect())->sortByDesc('created_at')->first();
        $internshipFinished = ($internship?->status === 'selesai')
            || ($internship?->tanggal_selesai && now()->startOfDay()->greaterThanOrEqualTo($internship->tanggal_selesai->copy()->startOfDay()));
        $certificateEligible = $internshipFinished && $latestAssessment && in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true);
        $certificateIssued = (bool) $latestCertificate;
        $certificatePredikat = $latestCertificate?->predikat ?? match (true) {
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 90 => 'A+',
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 85 => 'A',
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 80 => 'AB',
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 75 => 'B+',
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 70 => 'B',
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 65 => 'BC',
            (float) ($latestAssessment?->nilai_akhir ?? 0) >= 60 => 'C',
            default => '-',
        };
        $certificateFile = $latestCertificate?->file ? asset('storage/'.$latestCertificate->file) : null;
        $certificateFileName = $latestCertificate?->file ? basename($latestCertificate->file) : null;
        $certificateFileExt = $latestCertificate?->file ? strtolower(pathinfo($latestCertificate->file, PATHINFO_EXTENSION)) : null;
        $certificateIsImage = in_array($certificateFileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
        $certificateIsPdf = $certificateFileExt === 'pdf';
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.penilaian') }}"><i class="bi bi-award-fill"></i> Penilaian</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#penilaianMenu" aria-expanded="true" aria-controls="penilaianMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="penilaianMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.penilaian.rekap') }}">Rekap Nilai</a>
                <a href="{{ route('peserta.penilaian.sertifikat') }}" class="active">Sertifikat</a>
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
                        <h2 class="fw-bold mb-1">Sertifikat</h2>
                        <div class="small-muted">Lihat, verifikasi, unduh, cetak, dan bagikan sertifikat magang.</div>
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
                    <li class="breadcrumb-item"><a href="{{ route('peserta.penilaian') }}">Penilaian</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sertifikat</li>
                </ol>
            </nav>

            <div class="alert {{ $certificateIssued ? 'alert-success' : 'alert-warning' }} d-flex align-items-center gap-2" role="alert">
                <i class="bi {{ $certificateIssued ? 'bi-patch-check-fill' : 'bi-hourglass-split' }}"></i>
                <div>
                    @if($certificateIssued)
                        Sertifikat tersedia setelah admin mengunggah file PDF berdasarkan penilaian akhir dan status magang selesai.
                    @elseif($certificateEligible)
                        Sertifikat sudah layak diterbitkan dan menunggu unggahan file PDF dari admin.
                    @else
                        Sertifikat belum tersedia. Menunggu penilaian final dan status magang selesai sebelum admin mengunggah sertifikat.
                    @endif
                </div>
            </div>

            <section class="certificate-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">Sertifikat Magang Peserta</h3>
                                <div class="text-secondary">{{ $userName }} | {{ $peserta?->program_magang ?? 'Program Magang' }} | {{ $internship?->instansi ?? 'Instansi Magang' }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge {{ $latestCertificate ? 'bg-success' : 'bg-warning text-dark' }}">{{ $latestCertificate ? 'Diterbitkan' : 'Menunggu Upload' }}</span>
                                    <span class="badge bg-primary">No: {{ $latestCertificate?->nomor ?? '-' }}</span>
                                    <span class="badge bg-info text-dark">Format PDF</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <strong>Status Penerbitan</strong>
                            <div class="small-muted mt-1">
                                {{ $latestCertificate
                                    ? 'Sertifikat diterbitkan pada '.optional($latestCertificate->tanggal_terbit)->translatedFormat('d F Y').' dan dapat diverifikasi melalui nomor sertifikat.'
                                    : 'Sertifikat akan diterbitkan oleh admin setelah penilaian akhir tersedia dan status magang selesai.'
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Status Sertifikat</div><h4 class="fw-bold mb-1">{{ $latestCertificate ? 'Terbit' : 'Menunggu' }}</h4><span class="badge {{ $latestCertificate ? 'bg-success' : 'bg-warning text-dark' }}">{{ $latestCertificate ? 'Valid' : 'Belum tersedia' }}</span></div><span class="stat-icon {{ $latestCertificate ? 'bg-success' : 'bg-warning text-dark' }}"><i class="bi bi-patch-check"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Tanggal Terbit</div><h4 class="fw-bold mb-1">{{ $latestCertificate ? optional($latestCertificate->tanggal_terbit)->translatedFormat('d M') : '-' }}</h4><span class="badge bg-primary">{{ $latestCertificate ? optional($latestCertificate->tanggal_terbit)->translatedFormat('Y') : '-' }}</span></div><span class="stat-icon bg-primary"><i class="bi bi-calendar-check"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Nomor Sertifikat</div><h4 class="fw-bold mb-1">{{ $latestCertificate?->nomor ?? '-' }}</h4><span class="badge bg-info text-dark">{{ $latestCertificate ? 'LLDIKTI-V' : 'Menunggu' }}</span></div><span class="stat-icon bg-info"><i class="bi bi-upc-scan"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Format</div><h4 class="fw-bold mb-1">PDF</h4><span class="badge bg-secondary">A4</span></div><span class="stat-icon bg-secondary"><i class="bi bi-filetype-pdf"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4 no-print">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        @if($latestCertificate)
                            <button class="action-card w-100 p-3 border-0 text-start" type="button" data-bs-toggle="modal" data-bs-target="#downloadChoiceModal">
                                <span class="action-icon bg-secondary mb-2"><i class="bi bi-printer"></i></span>
                                <strong class="d-block">Cetak Sertifikat</strong>
                                <span class="small-muted">Unduh PDF atau cetak langsung</span>
                            </button>
                        @else
                            <button class="action-card w-100 p-3 border-0 text-start" type="button" disabled>
                                <span class="action-icon bg-secondary mb-2"><i class="bi bi-printer"></i></span>
                                <strong class="d-block">Cetak Sertifikat</strong>
                                <span class="small-muted">Menunggu sertifikat dari admin</span>
                            </button>
                        @endif
                    </div>
                    <div class="col-md-6 col-xl-3">
                        @if($latestCertificate)
                            <button class="action-card w-100 p-3 border-0 text-start" type="button" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <span class="action-icon bg-primary mb-2"><i class="bi bi-share"></i></span>
                                <strong class="d-block">Bagikan Sertifikat</strong>
                                <span class="small-muted">Salin tautan</span>
                            </button>
                        @else
                            <button class="action-card w-100 p-3 border-0 text-start" type="button" disabled>
                                <span class="action-icon bg-primary mb-2"><i class="bi bi-share"></i></span>
                                <strong class="d-block">Bagikan Sertifikat</strong>
                                <span class="small-muted">Belum tersedia</span>
                            </button>
                        @endif
                    </div>
                    <div class="col-md-6 col-xl-3">
                        @if($latestCertificate)
                            <button class="action-card w-100 p-3 border-0 text-start" type="button" data-bs-toggle="modal" data-bs-target="#verifyModal">
                                <span class="action-icon bg-success mb-2"><i class="bi bi-shield-check"></i></span>
                                <strong class="d-block">Verifikasi Sertifikat</strong>
                                <span class="small-muted">Cek keaslian</span>
                            </button>
                        @else
                            <button class="action-card w-100 p-3 border-0 text-start" type="button" disabled>
                                <span class="action-icon bg-success mb-2"><i class="bi bi-shield-check"></i></span>
                                <strong class="d-block">Verifikasi Sertifikat</strong>
                                <span class="small-muted">Belum tersedia</span>
                            </button>
                        @endif
                    </div>
                </div>
            </section>

            <section class="filter-card p-3 mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari nomor sertifikat"></div>
                    </div>
                    <div class="col-md-3"><label class="form-label">Periode</label><select class="form-select"><option>Mei-Juli 2026</option><option>Januari-Maret 2026</option></select></div>
                    <div class="col-md-3"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Diterbitkan</option><option>Menunggu</option><option>Revisi</option></select></div>
                    <div class="col-md-2"><label class="form-label">Jenis</label><select class="form-select"><option>Magang</option><option>Kompetensi</option></select></div>
                    <div class="col-lg-1"><button class="btn btn-primary w-100" type="button" data-bs-toggle="toast" data-bs-target="#certificateToast">Filter</button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="certificate-preview p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Sertifikat Peserta</h5>
                                <div class="small-muted">Pratinjau sertifikat dan daftar sertifikat peserta.</div>
                            </div>
                            @unless($hideInfoPanel)
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#certificateInfoPanel" aria-controls="certificateInfoPanel">
                                    <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                                </button>
                            @endunless
                        </div>
                        {{--
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Preview Sertifikat</h5>
                                <div class="small-muted">Tampilan pratinjau sertifikat resmi peserta dari data yang diunggah admin.</div>
                            </div>
                            <span class="badge {{ $latestCertificate ? 'bg-success' : 'bg-warning text-dark' }} align-self-start align-self-lg-center">{{ $latestCertificate ? 'Terverifikasi' : 'Menunggu' }}</span>
                        </div>
                        <div class="certificate-paper">
                            <div>
                                <div class="certificate-seal"><i class="bi bi-award"></i></div>
                                <div class="text-uppercase small-muted mb-2">Portal Magang LLDIKTI Wilayah V Yogyakarta</div>
                                <h2 class="fw-bold mb-2">Sertifikat Magang</h2>
                                <p class="mb-1">Diberikan kepada</p>
                                <h3 class="fw-bold mb-2">{{ $userName }}</h3>
                                <p class="mb-2">
                                    atas penyelesaian program magang sebagai
                                    <strong>{{ $peserta?->program_magang ?? 'Peserta Magang' }}</strong>
                                    di <strong>{{ $internship?->instansi ?? 'Instansi Magang' }}</strong>
                                </p>
                                <div class="small-muted">
                                    Periode {{ $latestCertificate?->periode ?? ($peserta?->program_magang ?? '-') }}
                                    | Predikat {{ $latestCertificate?->predikat ?? $certificatePredikat }}
                                    | No. {{ $latestCertificate?->nomor ?? '-' }}
                                </div>
                                <div class="small-muted mt-1">
                                    {{ $latestCertificate ? 'Diunggah admin pada '.optional($latestCertificate->tanggal_terbit)->translatedFormat('d F Y').' • File: '.basename($latestCertificate->file ?: 'sertifikat.pdf') : 'Sertifikat menunggu unggahan admin.' }}
                                </div>
                            </div>
                        </div>
                        --}}
                    </section>
                </div>
            </div>

            <section class="soft-card p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Daftar Sertifikat</h5>
                        <div class="small-muted">Dokumentasi sertifikat yang telah diterbitkan.</div>
                    </div>
                    <button class="btn btn-outline-primary" data-bs-toggle="toast" data-bs-target="#certificateToast"><i class="bi bi-download me-1"></i> Unduh Rekap</button>
                </div>
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>No</th><th>Nomor Sertifikat</th><th>Nama Peserta</th><th>Periode Magang</th><th>Predikat</th><th>Status</th><th>Tanggal Terbit</th><th>Aksi</th></tr></thead>
                            <tbody>
                                @forelse($certificateCollection as $certificate)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $certificate->nomor }}</td>
                                        <td>{{ $userName }}</td>
                                        <td>{{ $certificate->periode ?? ($peserta?->program_magang ?? '-') }}</td>
                                        <td>{{ $certificate->predikat ?? $certificatePredikat }}</td>
                                        <td><span class="badge {{ in_array($certificate->status, ['terbit', 'tersedia', 'published'], true) ? 'bg-success' : 'bg-info text-dark' }}">{{ ucfirst($certificate->status ?? 'terbit') }}</span></td>
                                        <td>{{ optional($certificate->tanggal_terbit)->translatedFormat('d F Y') ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#verifyModal">Verifikasi</button>
                                                <a class="btn btn-outline-secondary" href="{{ route('peserta.penilaian.sertifikat.pdf') }}">Unduh</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Sertifikat belum diunggah admin.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                    <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $certificateCollection->count() }} sertifikat</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option></select></div>
                    <nav aria-label="Pagination sertifikat"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item disabled"><a class="page-link" href="#">Next</a></li></ul></nav>
                </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="verifyLabel">Verifikasi Sertifikat</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">{{ $latestCertificate ? 'Sertifikat nomor '.$latestCertificate->nomor.' valid, diterbitkan untuk '.$userName.', dan tercatat pada Portal Magang LLDIKTI Wilayah V Yogyakarta.' : 'Sertifikat belum diterbitkan.' }}</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#certificateToast">Salin Hasil</button></div></div></div>
    </div>
    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="shareLabel">Bagikan Sertifikat</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body"><label class="form-label">Tautan Sertifikat</label><input class="form-control" value="{{ route('peserta.penilaian.sertifikat.pdf') }}" readonly></div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#certificateToast">Salin Tautan</button></div></div></div>
    </div>
    <div class="modal fade" id="downloadChoiceModal" tabindex="-1" aria-labelledby="downloadChoiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadChoiceLabel">Cetak Sertifikat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Pilih metode keluaran sertifikat yang diinginkan.</p>
                    <div class="d-grid gap-2">
                        @if($latestCertificate)
                            <a class="btn btn-danger" href="{{ route('peserta.penilaian.sertifikat.pdf') }}">
                                <i class="bi bi-filetype-pdf me-1"></i> Unduh PDF
                            </a>
                            <button type="button" class="btn btn-secondary" id="printCertificateButton">
                                <i class="bi bi-printer me-1"></i> Cetak Langsung
                            </button>
                        @else
                            <button type="button" class="btn btn-danger" disabled>
                                <i class="bi bi-filetype-pdf me-1"></i> Unduh PDF
                            </button>
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="bi bi-printer me-1"></i> Cetak Langsung
                            </button>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="certificateToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Sertifikat</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan sertifikat berhasil diproses dan histori akses diperbarui.</div>
        </div>
    </div>

    @unless($hideInfoPanel)
        <div class="offcanvas offcanvas-end" tabindex="-1" id="certificateInfoPanel" aria-labelledby="certificateInfoPanelLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="certificateInfoPanelLabel">Panel Informasi Sertifikat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body">
                <section class="info-card p-3 p-lg-4 mb-4">
                    <h5 class="fw-bold mb-1">Panel Informasi Sertifikat</h5>
                    <div class="small-muted mb-3">Detail identitas dan status penerbitan.</div>
                    <div class="mb-3"><strong>Nama Peserta</strong><div class="small-muted">{{ $userName }}</div></div>
                    <div class="mb-3"><strong>Predikat</strong><div><span class="badge bg-success">{{ $certificatePredikat }}</span></div></div>
                    <div class="mb-3"><strong>Status Verifikasi</strong><div class="small-muted">{{ $latestCertificate ? 'Nomor sertifikat valid dan terdaftar pada sistem.' : 'Sertifikat belum diterbitkan.' }}</div></div>
                    <div><strong>Informasi Tambahan</strong><div class="small-muted">Sertifikat dapat digunakan sebagai bukti resmi penyelesaian magang.</div></div>
                </section>
                <section class="info-card p-3 p-lg-4">
                    <h5 class="fw-bold mb-1">Riwayat Akses</h5>
                    <div class="small-muted mb-3">Aktivitas terakhir terkait sertifikat.</div>
                    <div class="d-flex gap-3 mb-3"><span class="stat-icon bg-success"><i class="bi bi-download"></i></span><div><strong>Unduh PDF</strong><div class="small-muted">{{ $latestCertificate ? optional($latestCertificate->tanggal_terbit)->translatedFormat('d F Y') : '-' }}</div></div></div>
                    <div class="d-flex gap-3"><span class="stat-icon bg-primary"><i class="bi bi-shield-check"></i></span><div><strong>Verifikasi</strong><div class="small-muted">{{ $latestCertificate ? 'Sertifikat valid pada sistem.' : 'Belum ada sertifikat yang bisa diverifikasi.' }}</div></div></div>
                </section>
            </div>
        </div>
    @endunless

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        const certificatePreviewUrl = @json($certificateFile);
        const certificatePreviewName = @json($certificateFileName);
        const certificatePreviewIsPdf = @json($certificateIsPdf);
        const certificatePreviewIsImage = @json($certificateIsImage);

        document.addEventListener('DOMContentLoaded', () => {
            const previewCard = document.querySelector('.certificate-paper');
            if (!previewCard || !certificatePreviewUrl) {
                return;
            }

            if (certificatePreviewIsPdf) {
                previewCard.innerHTML = `
                    <iframe
                        src="${certificatePreviewUrl}"
                        title="Preview Sertifikat"
                        class="w-100"
                        style="min-height: 640px; border: 0;"
                    ></iframe>
                `;
            } else if (certificatePreviewIsImage) {
                previewCard.innerHTML = `
                    <img
                        src="${certificatePreviewUrl}"
                        alt="Preview sertifikat"
                        class="img-fluid w-100"
                        style="max-height: 760px; object-fit: contain;"
                    >
                `;
            } else {
                previewCard.innerHTML = `
                    <div class="w-100 p-5 text-center">
                        <div class="certificate-seal mb-3"><i class="bi bi-file-earmark"></i></div>
                        <h4 class="fw-bold mb-2">Preview tidak tersedia</h4>
                        <p class="small-muted mb-0">File sertifikat tersedia, tetapi tidak dapat dipratinjau langsung di browser. Silakan unduh file untuk melihat isinya.</p>
                    </div>
                `;
            }

            const previewLabel = document.querySelector('.small-muted');
            if (previewLabel && certificatePreviewName) {
                // Keep the existing caption untouched if another small-muted is matched first.
            }
        });

        document.querySelectorAll('[data-bs-toggle="toast"]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = document.querySelector(button.dataset.bsTarget);
                if (target) bootstrap.Toast.getOrCreateInstance(target).show();
            });
        });

        document.getElementById('printCertificateButton').addEventListener('click', () => {
            const modalEl = document.getElementById('downloadChoiceModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
            setTimeout(() => window.print(), 250);
        });
    </script>
</body>
</html>
