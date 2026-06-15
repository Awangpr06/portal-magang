<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peserta Magang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand: #2a8fbd;
            --brand-dark: #185f80;
            --brand-soft: #edf8fc;
            --accent: #62bd42;
            --ink: #163342;
            --muted: #697b86;
            --line: #d7eaf2;
            --page: #f6fbfd;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            background: var(--page);
            color: var(--ink);
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 286px;
            min-height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            background: var(--brand);
            color: #fff;
            overflow-y: auto;
            padding: 18px;
            z-index: 1030;
        }

        .brand-logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .profile-photo {
            width: 62px;
            height: 62px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, .7);
        }

        .status-dot {
            width: 9px;
            height: 9px;
            display: inline-block;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 3px rgba(98, 189, 66, .18);
        }

        .sidebar a,
        .sidebar .logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #edf8fc;
            text-decoration: none;
            padding: 11px 13px;
            border-radius: 8px;
            border: 0;
            background: transparent;
            font-size: 14px;
            text-align: left;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active,
        .sidebar .logout-button:hover,
        .sidebar-parent.active {
            background: var(--brand-dark);
            color: #fff;
        }

        .sidebar-parent {
            display: flex;
            align-items: stretch;
            border-radius: 8px;
            margin-bottom: 5px;
            overflow: hidden;
        }

        .sidebar-parent a {
            flex: 1;
            margin-bottom: 0;
            border-radius: 8px 0 0 8px;
        }

        .sidebar-parent a:hover {
            background: transparent;
        }

        .sidebar-toggle {
            width: 38px;
            border: 0;
            color: #fff;
            background: transparent;
        }

        .sidebar-toggle:hover {
            background: #174f6a;
        }

        .sidebar-toggle[aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        .sidebar-toggle .bi-chevron-down {
            transition: .2s ease;
        }

        .sidebar-submenu {
            padding: 4px 0 7px 20px;
        }

        .sidebar-submenu a {
            padding: 9px 12px;
            font-size: 13px;
            color: #d9eef6;
        }

        .main-content {
            margin-left: 286px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(244, 248, 251, .94);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--line);
            padding: 16px 24px;
        }

        .header-logo {
            width: 44px;
            height: 44px;
            object-fit: contain;
        }

        .icon-button {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--brand);
        }

        .content-wrap {
            width: 100%;
            padding: 24px;
            flex: 1;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #e9f7fb 0%, #f2f9ed 100%);
            border: 1px solid #d7ebee;
            border-radius: 8px;
            overflow: hidden;
        }

        .hero-illustration {
            max-width: 310px;
            width: 100%;
            height: auto;
        }

        .soft-card,
        .stat-card,
        .quick-action-card,
        .document-card,
        .activity-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(22, 51, 66, .05);
        }

        .quick-action-card,
        .stat-card {
            height: 100%;
            transition: .2s ease;
        }

        .stat-card {
            padding: 12px;
        }

        .quick-action-card {
            padding: 12px 13px;
        }

        .document-card {
            padding: 12px;
        }

        .quick-action-card:hover,
        .stat-card:hover,
        .document-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(11, 95, 134, .12);
        }

        .quick-action-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 13px;
            color: var(--ink);
            text-decoration: none;
        }

        .menu-icon,
        .stat-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #fff;
            font-size: 18px;
        }

        .section-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-title h4,
        .section-title h5 {
            margin: 0;
            font-weight: 700;
        }

        .progress {
            height: 9px;
            border-radius: 99px;
            background: #e8eef2;
        }

        .progress-row,
        .activity-row,
        .document-card {
            padding: 14px;
        }

        .activity-row + .activity-row {
            border-top: 1px solid var(--line);
        }

        .small-muted {
            color: var(--muted);
            font-size: 13px;
        }

        .footer-system {
            padding: 18px 24px;
            text-align: center;
            font-size: 13px;
            color: #6d7d86;
            border-top: 1px solid var(--line);
            background: #fff;
        }

        @media (max-width: 991px) {
            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
                border-radius: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .top-header {
                position: sticky;
                padding: 14px 16px;
            }

            .content-wrap {
                padding: 16px;
            }
        }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $user = $user ?? auth()->user();
        $peserta = $peserta ?? $user?->peserta ?? null;
        $internship = $internship ?? $peserta?->internship ?? null;
        $mentorUser = $mentorUser ?? $internship?->mentor?->user ?? null;
        $pembimbingUser = $pembimbingUser ?? $internship?->pembimbing?->user ?? null;
        $stats = $stats ?? [];
        $documents = $documents ?? collect();
        $reports = $reports ?? collect();
        $activities = $activities ?? collect();
        $notifications = $notifications ?? collect();
        $userName = $user?->name ?? 'Peserta Magang';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $placementInstitution = $placementInstitution ?? ($internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta');
        $placementDivision = $placementDivision ?? ($internship?->divisi ?? $internship?->unit_kerja ?? $internship?->posisi ?? '-');
        $placementUnit = $placementUnit ?? $placementDivision;
        $placementName = $placementName ?? ($placementDivision !== '-' ? $placementInstitution . ' - ' . $placementDivision : $placementInstitution);
        $periodText = $periodText ?? (
            $internship?->tanggal_mulai && $internship?->tanggal_selesai
                ? $internship->tanggal_mulai->translatedFormat('d M Y') . ' - ' . $internship->tanggal_selesai->translatedFormat('d M Y')
                : (($peserta?->tanggal_mulai_magang && $peserta?->tanggal_selesai_magang)
                    ? $peserta->tanggal_mulai_magang->translatedFormat('d M Y') . ' - ' . $peserta->tanggal_selesai_magang->translatedFormat('d M Y')
                    : 'Periode belum tersedia')
        );
        $placementStatus = $placementStatus ?? ($internship?->status ?? $peserta?->status ?? 'belum tersedia');
        $placementStatusBadge = match ($placementStatus) {
            'berjalan', 'aktif' => 'bg-success',
            'selesai' => 'bg-secondary',
            'menunggu' => 'bg-warning text-dark',
            default => 'bg-info text-dark',
        };
        $daysLeft = $daysLeft ?? ($stats['days_left'] ?? null);
        $documentProgress = $documentProgress ?? (($stats['document_total'] ?? 0) > 0 ? (int) round((($stats['document_approved'] ?? 0) / max($stats['document_total'], 1)) * 100) : 0);
        $reportProgress = $reportProgress ?? (($stats['report_total'] ?? 0) > 0 ? (int) round((($stats['report_approved'] ?? 0) / max($stats['report_total'], 1)) * 100) : 0);
        $internshipProgress = $internshipProgress ?? ($stats['internship_progress'] ?? 0);
        $documentTotal = (int) ($stats['document_total'] ?? 0);
        $documentApproved = (int) ($stats['document_approved'] ?? 0);
        $documentWaiting = (int) ($stats['document_waiting'] ?? 0);
        $documentRevision = (int) ($stats['document_revision'] ?? 0);
        $reportTotal = (int) ($stats['report_total'] ?? 0);
        $reportApproved = (int) ($stats['report_approved'] ?? 0);
        $reportWaiting = (int) ($stats['report_waiting'] ?? 0);
        $reportRevision = (int) ($stats['report_revision'] ?? 0);
        $activityCount = (int) ($activities->count() ?? 0);
        $latestActivity = $activities->first();
        $progressDokumenBadge = match (true) {
            $documentTotal === 0 => ['label' => 'Belum Ada Data', 'class' => 'bg-secondary'],
            $documentProgress >= 80 => ['label' => 'Sesuai Target', 'class' => 'bg-success'],
            $documentProgress >= 40 => ['label' => 'Perlu Dipantau', 'class' => 'bg-warning text-dark'],
            default => ['label' => 'Butuh Tindak Lanjut', 'class' => 'bg-danger'],
        };
        $progressLaporanBadge = match (true) {
            $reportTotal === 0 => ['label' => 'Belum Ada Data', 'class' => 'bg-secondary'],
            $reportProgress >= 80 => ['label' => 'Sangat Baik', 'class' => 'bg-success'],
            $reportProgress >= 40 => ['label' => 'Perlu Dipercepat', 'class' => 'bg-warning text-dark'],
            default => ['label' => 'Butuh Tindak Lanjut', 'class' => 'bg-danger'],
        };
        $progressMagangBadge = match (true) {
            $internshipProgress >= 100 => ['label' => 'Selesai', 'class' => 'bg-success'],
            $internshipProgress >= 60 => ['label' => 'Berjalan', 'class' => 'bg-info text-dark'],
            $internshipProgress > 0 => ['label' => 'Dalam Review', 'class' => 'bg-warning text-dark'],
            default => ['label' => 'Belum Dimulai', 'class' => 'bg-secondary'],
        };
        $documentApprovedBadge = match (true) {
            $documentApproved === 0 => ['label' => 'Belum Ada', 'class' => 'bg-secondary'],
            $documentApproved === $documentTotal => ['label' => 'Semua Disetujui', 'class' => 'bg-success'],
            default => ['label' => $documentApproved . ' Disetujui', 'class' => 'bg-success'],
        };
        $activityBadge = match (true) {
            $activityCount === 0 => ['label' => 'Kosong', 'class' => 'bg-secondary'],
            optional($latestActivity?->created_at)->isToday() => ['label' => 'Hari Ini', 'class' => 'bg-success'],
            default => ['label' => 'Tercatat', 'class' => 'bg-info text-dark'],
        };
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
            <div class="small d-inline-flex align-items-center gap-2 mt-1">
                <span class="status-dot"></span>
                Online
            </div>
        </div>

        <hr class="border-light opacity-25">

        <a href="{{ route('peserta.dashboard') }}" class="active"><i class="bi bi-grid-fill"></i> Dashboard</a>

        <div class="sidebar-parent">
            <a href="{{ route('peserta.data-magang') }}"><i class="bi bi-briefcase-fill"></i> Data Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dataMagangMenu" aria-expanded="false" aria-controls="dataMagangMenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse" id="dataMagangMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a>
                <a href="{{ route('peserta.data-magang.penempatan') }}">Penempatan</a>
            </div>
        </div>

        <div class="sidebar-parent">
            <a href="{{ route('peserta.aktivitas-magang') }}"><i class="bi bi-activity"></i> Aktivitas Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#aktivitasMenu" aria-expanded="false" aria-controls="aktivitasMenu">
                <i class="bi bi-chevron-down"></i>
            </button>
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
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dokumenMenu" aria-expanded="false" aria-controls="dokumenMenu">
                <i class="bi bi-chevron-down"></i>
            </button>
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

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button>
        </form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
                    <div>
                        <h2 class="fw-bold mb-1">Dashboard</h2>
                        <div class="small-muted">Portal Magang LLDIKTI Wilayah V Yogyakarta</div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3">
                    @include('peserta.partials.topbar-actions')
                    <div class="dropdown">
                        <button class="btn btn-light border d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ $avatar }}" class="rounded-circle" width="42" height="42" alt="Foto profil peserta">
                            <span class="text-start d-none d-sm-block">
                                <strong class="d-block">{{ $userName }}</strong>
                                <small class="text-muted">Peserta Magang</small>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('peserta.pengaturan.profil') }}"><i class="bi bi-person me-2"></i>Profil Akun</a></li>
                            <li><a class="dropdown-item" href="{{ route('peserta.pengaturan') }}"><i class="bi bi-sliders me-2"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrap">
            <section class="welcome-banner p-4 mb-4">
                <div class="row align-items-center g-4">
                    <div class="col-xl-8">
                        <h3 class="fw-bold mb-2">Selamat Datang, {{ $userName }}</h3>
                        <p class="mb-3 text-secondary">Kelola aktivitas, laporan, dan dokumen magang Anda dengan mudah.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge text-bg-light border px-3 py-2"><i class="bi bi-calendar-range me-1"></i> Periode: {{ $periodText }}</span>
                            <span class="badge bg-success px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> {{ $daysLeft === null ? 'Hari tersisa belum tersedia' : $daysLeft . ' hari tersisa' }}</span>
                            <span class="badge bg-info text-dark px-3 py-2"><i class="bi bi-building me-1"></i> {{ $placementName }}</span>
                        </div>
                    </div>
                    <div class="col-xl-4 text-xl-end">
                        <img src="{{ asset('images/logo-peserta.png') }}" class="hero-illustration" alt="Ilustrasi aktivitas magang">
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="section-title">
                    <h4>Aksi Cepat</h4>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-xl-2"><a class="quick-action-card" href="{{ route('peserta.dokumen') }}"><span class="menu-icon bg-primary"><i class="bi bi-cloud-upload"></i></span><strong>Upload Dokumen</strong></a></div>
                    <div class="col-md-6 col-xl-2"><a class="quick-action-card" href="{{ route('peserta.laporan.input') }}"><span class="menu-icon bg-success"><i class="bi bi-pencil-square"></i></span><strong>Input Laporan</strong></a></div>
                    <div class="col-md-6 col-xl-2"><a class="quick-action-card" href="{{ route('peserta.aktivitas-magang.absensi') }}"><span class="menu-icon bg-info"><i class="bi bi-calendar-check"></i></span><strong>Absensi Hari Ini</strong></a></div>
                    <div class="col-md-6 col-xl-2"><a class="quick-action-card" href="{{ route('peserta.aktivitas-magang.penugasan') }}"><span class="menu-icon bg-secondary"><i class="bi bi-list-task"></i></span><strong>Lihat Penugasan</strong></a></div>
                    <div class="col-md-6 col-xl-2"><a class="quick-action-card" href="{{ route('peserta.komunikasi.pesan') }}"><span class="menu-icon bg-danger"><i class="bi bi-send"></i></span><strong>Kirim Pesan</strong></a></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="section-title">
                    <div>
                        <h4>Ringkasan Magang</h4>
                        <div class="small-muted">Statistik utama berdasarkan data terbaru peserta.</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-xxl">
                        <div class="stat-card">
                            <div class="d-flex justify-content-between gap-3">
                                <div>
                                    <div class="small-muted">Penempatan</div>
                                    <h5 class="fw-bold mb-1">{{ $placementDivision }}</h5>
                                    <span class="badge {{ $placementStatusBadge }}">{{ ucfirst($placementStatus) }}</span>
                                </div>
                                <span class="stat-icon bg-primary"><i class="bi bi-building"></i></span>
                            </div>
                            <div class="small-muted mt-2">LLDIKTI Wilayah V Yogyakarta</div>
                            <div class="small-muted">Mentor: {{ $mentorUser?->name ?? 'Belum ditentukan' }}</div>
                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ route('peserta.data-magang.penempatan') }}">Detail</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Periode Magang</div><h5 class="fw-bold mb-1">{{ $periodText }}</h5><span class="badge bg-info text-dark">{{ ucfirst($internship?->status ?? 'Belum tersedia') }}</span></div>
                                <span class="stat-icon bg-info"><i class="bi bi-calendar-range"></i></span>
                            </div>
                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ route('peserta.data-magang.penempatan') }}">Detail</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Progress Magang</div><h5 class="fw-bold mb-1">{{ $internshipProgress }}%</h5><span class="badge {{ $progressMagangBadge['class'] }}">{{ $progressMagangBadge['label'] }}</span></div>
                                <span class="stat-icon bg-success"><i class="bi bi-clipboard2-check"></i></span>
                            </div>
                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ route('peserta.aktivitas-magang') }}">Detail</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Laporan</div><h5 class="fw-bold mb-1">{{ $reportTotal }} Laporan</h5><span class="badge {{ $reportApproved > 0 ? 'bg-success' : 'bg-secondary' }}">{{ $reportApproved > 0 ? $reportApproved . ' Disetujui' : 'Belum Disetujui' }}</span></div>
                                <span class="stat-icon bg-danger"><i class="bi bi-award"></i></span>
                            </div>
                            <a class="btn btn-sm btn-outline-primary mt-3" href="{{ route('peserta.laporan.riwayat') }}">Detail</a>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <section class="col-xl-7">
                    <div class="soft-card h-100 p-3 p-lg-4">
                        <div class="section-title">
                            <div>
                                <h5>Progress Magang</h5>
                                <div class="small-muted">Target yang sudah tercapai dan yang perlu ditindaklanjuti.</div>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('peserta.aktivitas-magang') }}">Detail</a>
                        </div>
                        <div class="d-grid gap-3">
                            <div class="progress-row border rounded-3">
                                <div class="d-flex justify-content-between"><strong>Progress Dokumen</strong><span>{{ $documentProgress }}%</span></div>
                                <div class="progress my-2"><div class="progress-bar bg-success" style="width: {{ $documentProgress }}%"></div></div>
                                <span class="badge {{ $progressDokumenBadge['class'] }}">{{ $progressDokumenBadge['label'] }}</span>
                            </div>
                            <div class="progress-row border rounded-3">
                                <div class="d-flex justify-content-between"><strong>Progress Laporan</strong><span>{{ $reportProgress }}%</span></div>
                                <div class="progress my-2"><div class="progress-bar bg-warning" style="width: {{ $reportProgress }}%"></div></div>
                                <span class="badge {{ $progressLaporanBadge['class'] }}">{{ $progressLaporanBadge['label'] }}</span>
                            </div>
                            <div class="progress-row border rounded-3">
                                <div class="d-flex justify-content-between"><strong>Progress Magang</strong><span>{{ $internshipProgress }}%</span></div>
                                <div class="progress my-2"><div class="progress-bar bg-warning" style="width: {{ $internshipProgress }}%"></div></div>
                                <span class="badge {{ $progressMagangBadge['class'] }}">{{ $progressMagangBadge['label'] }}</span>
                            </div>
                            <div class="progress-row border rounded-3">
                                <div class="d-flex justify-content-between"><strong>Dokumen Disetujui</strong><span>{{ $stats['document_approved'] ?? 0 }}/{{ $stats['document_total'] ?? 0 }}</span></div>
                                <div class="progress my-2"><div class="progress-bar bg-success" style="width: {{ $documentProgress }}%"></div></div>
                                <span class="badge {{ $documentApprovedBadge['class'] }}">{{ $documentApprovedBadge['label'] }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="col-xl-5">
                    <div class="activity-card h-100">
                        <div class="p-3 p-lg-4 pb-2">
                            <div class="section-title mb-2">
                                <div>
                                    <h5>Aktivitas Sistem</h5>
                                    <div class="small-muted">Perkembangan terbaru aktivitas magang.</div>
                                </div>
                            </div>
                        </div>
                        @forelse($activities->take(4) as $activity)
                            @php
                                $activityRowBadge = optional($activity->created_at)->isToday()
                                    ? ['label' => 'Hari Ini', 'class' => 'bg-success']
                                    : ['label' => 'Tercatat', 'class' => 'bg-info text-dark'];
                            @endphp
                            <div class="activity-row d-flex justify-content-between gap-3">
                                <div><strong>{{ $activity->aktivitas }}</strong><div class="small-muted">{{ $activity->created_at?->translatedFormat('d M Y, H.i') }} WIB</div></div>
                                <span class="badge {{ $activityRowBadge['class'] }} align-self-start">{{ $activityRowBadge['label'] }}</span>
                            </div>
                        @empty
                            <div class="activity-row">
                                <strong>Belum ada aktivitas akun</strong>
                                <div class="small-muted">Aktivitas akan muncul setelah peserta menggunakan fitur sistem.</div>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="mb-4">
                <div class="section-title">
                    <div>
                        <h4>Dokumen Perlu Perhatian</h4>
                        <div class="small-muted">Ringkasan dokumen penting dan status tindak lanjut.</div>
                    </div>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('peserta.dokumen') }}">Lihat Semua</a>
                </div>
                <div class="row g-3">
                    @forelse($documents->take(6) as $document)
                        @php
                            $badge = match($document->status) {
                                'disetujui', 'approved' => 'bg-success',
                                'revisi', 'ditolak', 'rejected' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <div class="col-md-6 col-xl-4">
                            <div class="document-card h-100">
                                <div class="fw-semibold">{{ $document->nama_dokumen }}</div>
                                <div class="small-muted">{{ $document->kategori }}</div>
                                <span class="badge {{ $badge }} mt-2">{{ ucfirst($document->status) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="document-card">
                                <div class="fw-semibold">Belum ada dokumen peserta</div>
                                <div class="small-muted">Dokumen yang diunggah oleh peserta akan tampil otomatis di bagian ini.</div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <footer class="footer-system">
            &copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0
        </footer>
    </main>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
