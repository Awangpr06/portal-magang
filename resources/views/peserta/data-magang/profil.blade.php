<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Magang - Profil Peserta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand: #2a8fbd;
            --brand-dark: #185f80;
            --accent: #62bd42;
            --ink: #163342;
            --muted: #697b86;
            --line: #dbe7ed;
            --page: #f4f8fb;
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
            background: #083f59;
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

        .soft-card,
        .stat-card,
        .filter-card,
        .table-card,
        .action-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(22, 51, 66, .05);
        }

        .stat-card,
        .action-card {
            height: 100%;
            transition: .2s ease;
        }

        .stat-card:hover,
        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(11, 95, 134, .12);
        }

        .stat-icon,
        .action-icon {
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

        .profile-hero {
            background: linear-gradient(135deg, #e9f7fb 0%, #f2f9ed 100%);
            border: 1px solid #d7ebee;
            border-radius: 8px;
        }

        .profile-avatar {
            width: 94px;
            height: 94px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 8px 18px rgba(11, 95, 134, .14);
        }

        .small-muted {
            color: var(--muted);
            font-size: 13px;
        }

        .breadcrumb a {
            color: var(--brand);
            text-decoration: none;
        }

        .nav-tabs .nav-link {
            color: var(--muted);
            font-weight: 600;
        }

        .nav-tabs .nav-link.active {
            color: var(--brand);
            border-color: var(--line) var(--line) #fff;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 0;
            border-bottom: 1px solid var(--line);
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .progress {
            height: 9px;
            border-radius: 99px;
            background: #e8eef2;
        }

        .table thead th {
            background: #eef5f8;
            color: #3b5664;
            font-size: 13px;
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background: #f8fbfd;
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
            }

            .main-content {
                margin-left: 0;
            }

            .top-header {
                padding: 14px 16px;
            }

            .content-wrap {
                padding: 16px;
            }

            .info-row {
                flex-direction: column;
                gap: 2px;
            }
        }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $context = $pesertaContext ?? [];
        $user = $context['user'] ?? auth()->user();
        $peserta = $context['peserta'] ?? $user?->peserta;
        $perguruanTinggi = $context['perguruanTinggi'] ?? $peserta?->perguruanTinggi;
        $internship = $context['internship'] ?? $peserta?->internship;
        $mentorUser = $context['mentorUser'] ?? $internship?->mentor?->user;
        $pembimbingUser = $context['pembimbingUser'] ?? $internship?->pembimbing?->user;
        $stats = $context['stats'] ?? [];
        $documents = collect($context['documents'] ?? []);
        $activities = collect($context['activities'] ?? []);
        $documentTotal = $documents->count();
        $documentApproved = $stats['document_approved'] ?? $documents->whereIn('status', ['disetujui', 'approved'])->count();
        $documentWaiting = $stats['document_waiting'] ?? $documents->whereIn('status', ['menunggu', 'pending'])->count();
        $documentRevision = $stats['document_revision'] ?? $documents->whereIn('status', ['revisi', 'rejected', 'ditolak'])->count();
        $completedFields = collect([
            $peserta?->nim,
            $peserta?->tempat_lahir,
            $peserta?->tanggal_lahir,
            $peserta?->jenis_kelamin,
            $peserta?->jurusan,
            $peserta?->fakultas,
            $peserta?->program_magang,
            $peserta?->pembimbing_akademik,
            $peserta?->no_hp,
            $peserta?->alamat,
        ])->filter(fn ($value) => filled($value))->count();
        $profileCompletion = (int) round((($completedFields + min($documentTotal, 6)) / 16) * 100);
        $userName = $user?->name ?? 'Peserta Magang';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=0b5f86&color=ffffff';
        $nim = $peserta?->nim ?? '-';
        $jurusan = $peserta?->jurusan ?? '-';
        $fakultas = $peserta?->fakultas ?? '-';
        $programMagang = $peserta?->program_magang ?? '-';
        $semester = $peserta?->semester ?? '-';
        $perguruanTinggiName = $perguruanTinggi?->nama_pt ?? '-';
        $alamat = $peserta?->alamat ?? '-';
        $noHp = $peserta?->no_hp ?? '-';
        $tempatLahir = $peserta?->tempat_lahir ?? '-';
        $tanggalLahir = $peserta?->tanggal_lahir ? $peserta->tanggal_lahir->translatedFormat('d F Y') : '-';
        $statusAkun = $user?->account_status ?? $peserta?->status ?? 'aktif';
        $statusAkunLabel = match ($statusAkun) {
            'disetujui', 'aktif' => 'Aktif',
            'menunggu', 'pending' => 'Menunggu',
            'ditolak' => 'Ditolak',
            default => ucfirst((string) $statusAkun),
        };
        $statusAkunClass = match ($statusAkun) {
            'disetujui', 'aktif' => 'success',
            'menunggu', 'pending' => 'warning text-dark',
            'ditolak' => 'danger',
            default => 'secondary',
        };
        $joinedAt = $user?->created_at ? $user->created_at->translatedFormat('d F Y') : '-';
        $lastLogin = $user?->updated_at ? $user->updated_at->translatedFormat('d M Y, H:i') : '-';
        $avatarCount = $documents->count();
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.data-magang') }}"><i class="bi bi-briefcase-fill"></i> Data Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dataMagangMenu" aria-expanded="true" aria-controls="dataMagangMenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="dataMagangMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.data-magang.profil') }}" class="active">Profil Peserta</a>
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
                        <h2 class="fw-bold mb-1">Profil Peserta</h2>
                        <div class="small-muted">Kelola identitas, data akademik, akun, dokumen, dan aktivitas profil.</div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 gap-sm-3">
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
                            <li><a class="dropdown-item" href="{{ route('peserta.data-magang.profil') }}"><i class="bi bi-person me-2"></i>Profil Peserta</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-sliders me-2"></i>Pengaturan</a></li>
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
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Data Magang</li>
                    <li class="breadcrumb-item active" aria-current="page">Profil Peserta</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-shield-check"></i>
                <div>Profil ditampilkan sesuai akun peserta yang sedang login dan dapat diperbarui melalui aksi yang tersedia.</div>
            </div>

            <section class="profile-hero p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center">
                        <img src="{{ $avatar }}" class="rounded-circle profile-avatar" alt="Foto profil peserta">
                        <div>
                            <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                            <div class="text-secondary">{{ $nim }} | {{ $jurusan }} | {{ $perguruanTinggiName }}</div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge bg-{{ $statusAkunClass }}">{{ $statusAkunLabel }}</span>
                                <span class="badge bg-info text-dark">{{ $programMagang }}</span>
                                <span class="badge bg-light text-dark border">Peserta Magang</span>
                            </div>
                        </div>
                    </div>
                    <div class="align-self-xl-center" style="min-width:260px;">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Kelengkapan Profil</strong>
                            <span>{{ $profileCompletion }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ $profileCompletion }}%"></div>
                        </div>
                        <div class="small-muted mt-2">Data profil dan dokumen peserta ditarik langsung dari database akun yang sedang login.</div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-2">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Status Akun</div><h5 class="fw-bold mb-1">{{ $statusAkunLabel }}</h5><span class="badge bg-{{ $statusAkunClass }}">{{ $statusAkunLabel }}</span></div>
                                <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Kelengkapan</div><h5 class="fw-bold mb-1">{{ $profileCompletion }}%</h5><span class="badge bg-info text-dark">Aktual</span></div>
                                <span class="stat-icon bg-info"><i class="bi bi-pie-chart"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Bergabung Sejak</div><h5 class="fw-bold mb-1">{{ $user?->created_at?->format('Y') ?? '-' }}</h5><span class="badge bg-light text-dark border">{{ $user?->created_at?->translatedFormat('M') ?? '-' }}</span></div>
                                <span class="stat-icon bg-primary"><i class="bi bi-calendar-plus"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Login Terakhir</div><h5 class="fw-bold mb-1">{{ $lastLogin }}</h5><span class="badge bg-light text-dark border">Aktif</span></div>
                                <span class="stat-icon bg-secondary"><i class="bi bi-clock-history"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Data Akademik</div><h5 class="fw-bold mb-1">{{ $completedFields >= 6 ? 'Lengkap' : 'Perlu Cek' }}</h5><span class="badge bg-success">Database</span></div>
                                <span class="stat-icon bg-warning"><i class="bi bi-mortarboard"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-2">
                        <div class="stat-card p-3">
                            <div class="d-flex justify-content-between gap-3">
                                <div><div class="small-muted">Dokumen</div><h5 class="fw-bold mb-1">{{ $documentApproved }}/{{ max($documentTotal, 1) }}</h5><span class="badge bg-warning text-dark">{{ $documentWaiting + $documentRevision }} tindak lanjut</span></div>
                                <span class="stat-icon bg-danger"><i class="bi bi-folder-check"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#profileActionModal"><span class="action-icon bg-primary mb-2"><i class="bi bi-pencil-square"></i></span><strong class="d-block">Edit Profil</strong><span class="small-muted">Ubah identitas</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#profileActionModal"><span class="action-icon bg-success mb-2"><i class="bi bi-check2-circle"></i></span><strong class="d-block">Simpan Perubahan</strong><span class="small-muted">Validasi data</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#profileToast"><span class="action-icon bg-info mb-2"><i class="bi bi-camera"></i></span><strong class="d-block">Unggah Foto</strong><span class="small-muted">Perbarui avatar</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#profileToast"><span class="action-icon bg-warning mb-2"><i class="bi bi-list-check"></i></span><strong class="d-block">Lengkapi Profil</strong><span class="small-muted">Cek kekurangan</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#profileActionModal"><span class="action-icon bg-secondary mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Unduh Data Profil</strong><span class="small-muted">Arsip pribadi</span></button></div>
                    <div class="col-md-6 col-xl-2"><a class="action-card d-block p-3 text-decoration-none text-dark" href="#"><span class="action-icon bg-danger mb-2"><i class="bi bi-folder2-open"></i></span><strong class="d-block">Lihat Dokumen</strong><span class="small-muted">Dokumen peserta</span></a></div>
                </div>
            </section>

            <section class="soft-card mb-4">
                <ul class="nav nav-tabs px-3 pt-3" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation"><button class="nav-link active" id="identity-tab" data-bs-toggle="tab" data-bs-target="#identity-pane" type="button" role="tab">Identitas</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic-pane" type="button" role="tab">Akademik</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-pane" type="button" role="tab">Akun</button></li>
                    <li class="nav-item" role="presentation"><button class="nav-link" id="document-tab" data-bs-toggle="tab" data-bs-target="#document-pane" type="button" role="tab">Dokumen</button></li>
                </ul>
                <div class="tab-content p-3 p-lg-4">
                    <div class="tab-pane fade show active" id="identity-pane" role="tabpanel" tabindex="0">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Informasi Pribadi</h5>
                                <div class="info-row"><span class="small-muted">Nama Lengkap</span><strong>{{ $userName }}</strong></div>
                                <div class="info-row"><span class="small-muted">NIM</span><strong>{{ $nim }}</strong></div>
                                <div class="info-row"><span class="small-muted">Tempat, Tanggal Lahir</span><strong>{{ $tempatLahir }}, {{ $tanggalLahir }}</strong></div>
                                <div class="info-row"><span class="small-muted">Jenis Kelamin</span><strong>{{ $peserta?->jenis_kelamin ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Alamat</span><strong>{{ $alamat }}</strong></div>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Kontak</h5>
                                <div class="info-row"><span class="small-muted">Email</span><strong>{{ $user?->email ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">No. HP</span><strong>{{ $noHp }}</strong></div>
                                <div class="info-row"><span class="small-muted">Kontak Darurat</span><strong>{{ $user?->kontak_darurat ?? 'Belum dilengkapi' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Domisili</span><strong>{{ $peserta?->alamat ? explode(',', $alamat)[0] : '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Status Verifikasi</span><strong><span class="badge bg-{{ $statusAkunClass }}">{{ $statusAkunLabel }}</span></strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="academic-pane" role="tabpanel" tabindex="0">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Data Akademik</h5>
                                <div class="info-row"><span class="small-muted">NIM</span><strong>{{ $nim }}</strong></div>
                                <div class="info-row"><span class="small-muted">Perguruan Tinggi</span><strong>{{ $perguruanTinggiName }}</strong></div>
                                <div class="info-row"><span class="small-muted">Program Studi</span><strong>{{ $jurusan }}</strong></div>
                                <div class="info-row"><span class="small-muted">Semester</span><strong>{{ $semester }}</strong></div>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Magang Akademik</h5>
                                <div class="info-row"><span class="small-muted">Pembimbing Akademik</span><strong>{{ $peserta?->pembimbing_akademik ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Program Magang</span><strong>{{ $programMagang }}</strong></div>
                                <div class="info-row"><span class="small-muted">Status Akademik</span><strong><span class="badge bg-success">{{ $statusAkunLabel }}</span></strong></div>
                                <div class="info-row"><span class="small-muted">Validasi Kampus</span><strong><span class="badge bg-{{ $documentApproved > 0 ? 'success' : 'warning text-dark' }}">{{ $documentApproved > 0 ? 'Disetujui' : 'Menunggu' }}</span></strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="account-pane" role="tabpanel" tabindex="0">
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Informasi Akun</h5>
                                <div class="info-row"><span class="small-muted">Role</span><strong>{{ ucfirst($user?->role ?? 'peserta') }}</strong></div>
                                <div class="info-row"><span class="small-muted">Username</span><strong>{{ $user?->username ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Bergabung</span><strong>{{ $joinedAt }}</strong></div>
                                <div class="info-row"><span class="small-muted">Login Terakhir</span><strong>{{ $lastLogin }}</strong></div>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Keamanan</h5>
                                <div class="info-row"><span class="small-muted">Status Akun</span><strong><span class="badge bg-{{ $statusAkunClass }}">{{ $statusAkunLabel }}</span></strong></div>
                                <div class="info-row"><span class="small-muted">Email Terverifikasi</span><strong><span class="badge bg-{{ filled($user?->email) ? 'success' : 'secondary' }}">{{ filled($user?->email) ? 'Ya' : 'Belum' }}</span></strong></div>
                                <div class="info-row"><span class="small-muted">Perangkat Terakhir</span><strong>-</strong></div>
                                <div class="info-row"><span class="small-muted">Lokasi Login</span><strong>-</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="document-pane" role="tabpanel" tabindex="0">
                        <div class="row g-3">
                            @forelse($documents->take(6) as $document)
                                @php
                                    $documentStatus = strtolower((string) ($document->status ?? ''));
                                    $documentClass = match ($documentStatus) {
                                        'disetujui', 'approved' => 'success',
                                        'menunggu', 'pending' => 'warning text-dark',
                                        'revisi', 'rejected', 'ditolak' => 'danger',
                                        default => 'secondary',
                                    };
                                    $documentLabel = match ($documentStatus) {
                                        'disetujui', 'approved' => 'Disetujui',
                                        'menunggu', 'pending' => 'Menunggu',
                                        'revisi', 'rejected', 'ditolak' => 'Perlu Revisi',
                                        default => $documentStatus !== '' ? ucfirst($documentStatus) : 'Tersimpan',
                                    };
                                @endphp
                                <div class="col-md-6 col-xl-4">
                                    <div class="border rounded-3 p-3 h-100">
                                        <strong>{{ $document->nama_dokumen ?? $document->jenis_dokumen ?? 'Dokumen' }}</strong>
                                        <div class="small-muted">{{ $document->kategori ?? 'Dokumen' }}</div>
                                        <div class="small-muted">{{ optional($document->created_at)->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                        <span class="badge bg-{{ $documentClass }} mt-2">{{ $documentLabel }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-secondary mb-0">Belum ada dokumen yang diunggah untuk akun peserta ini.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="soft-card mb-4">
                <div class="p-3 p-lg-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Riwayat Aktivitas Akun</h5>
                            <div class="small-muted">Histori aktivitas dan perubahan data profil peserta.</div>
                        </div>
                    </div>

                    <form class="filter-card p-3 mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-4">
                                <label class="form-label">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" placeholder="Cari aktivitas, perangkat, lokasi...">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <label class="form-label">Aktivitas</label>
                                <select class="form-select"><option>Semua</option><option>Login</option><option>Profil</option><option>Dokumen</option></select>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <label class="form-label">Kategori Dokumen</label>
                                <select class="form-select"><option>Semua</option><option>Identitas</option><option>Akademik</option><option>Magang</option></select>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" value="2026-05-28">
                            </div>
                            <div class="col-lg-2 d-flex gap-2">
                                <button class="btn btn-primary flex-fill" type="button" data-bs-toggle="toast" data-bs-target="#profileToast">Cari</button>
                                <button class="btn btn-outline-secondary" type="reset">Reset</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-card">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Aktivitas</th>
                                        <th>Jenis Aktivitas</th>
                                        <th>Waktu</th>
                                        <th>Perangkat</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities->take(4) as $index => $activity)
                                        @php
                                            $activityText = (string) ($activity->aktivitas ?? '-');
                                            $activityType = match (true) {
                                                str_contains(strtolower($activityText), 'dokumen') => 'Dokumen',
                                                str_contains(strtolower($activityText), 'profil') => 'Profil',
                                                str_contains(strtolower($activityText), 'login') => 'Akun',
                                                str_contains(strtolower($activityText), 'laporan') => 'Laporan',
                                                default => 'Sistem',
                                            };
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $activityText }}</strong><div class="small-muted">Tercatat pada sistem</div></td>
                                            <td>{{ $activityType }}</td>
                                            <td>{{ optional($activity->created_at)->translatedFormat('d M Y, H:i') ?? '-' }}</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td><span class="badge bg-success">Tercatat</span></td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-action="activity-detail"
                                                    data-activity="{{ e($activityText) }}"
                                                    data-type="{{ e($activityType) }}"
                                                    data-time="{{ e(optional($activity->created_at)->translatedFormat('d M Y, H:i') ?? '-') }}"
                                                    data-device="-"
                                                    data-location="-"
                                                    data-status="Tercatat"
                                                >
                                                    Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-secondary py-4">Belum ada riwayat aktivitas dari database.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="small-muted">Menampilkan {{ $activities->count() ? '1-' . min(4, $activities->count()) : '0' }} dari {{ $activities->count() }} aktivitas</span>
                            <select class="form-select form-select-sm" style="width:90px;">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                            </select>
                        </div>
                        <nav aria-label="Pagination aktivitas akun">
                            <ul class="pagination mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </section>
        </div>

        <footer class="footer-system">
            &copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0
        </footer>
    </main>

    <div class="modal fade" id="profileActionModal" tabindex="-1" aria-labelledby="profileActionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileActionLabel">Konfirmasi Perubahan Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Pastikan data profil yang akan diproses sudah benar. Sistem akan menyimpan aktivitas ini ke riwayat akun.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#profileToast">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="activityDetailModal" tabindex="-1" aria-labelledby="activityDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityDetailLabel">Detail Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="activityDetailContent" class="vstack gap-2 small">
                        <div class="text-secondary">Pilih salah satu aktivitas untuk melihat detailnya.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="profileToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                <strong class="me-auto">Notifikasi Sistem</strong>
                <small>Baru saja</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
            <div class="toast-body">
                Permintaan berhasil diproses. Informasi profil dan riwayat aktivitas diperbarui.
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelectorAll('[data-bs-target="#profileToast"]').forEach((button) => {
            button.addEventListener('click', () => {
                const toast = bootstrap.Toast.getOrCreateInstance(document.getElementById('profileToast'));
                toast.show();
            });
        });

        const activityDetailModalEl = document.getElementById('activityDetailModal');
        const activityDetailContentEl = document.getElementById('activityDetailContent');
        const activityDetailModal = activityDetailModalEl ? bootstrap.Modal.getOrCreateInstance(activityDetailModalEl) : null;

        document.querySelectorAll('[data-action="activity-detail"]').forEach((button) => {
            button.addEventListener('click', () => {
                if (!activityDetailContentEl || !activityDetailModal) {
                    return;
                }

                const activity = button.dataset.activity || '-';
                const type = button.dataset.type || '-';
                const time = button.dataset.time || '-';
                const device = button.dataset.device || '-';
                const location = button.dataset.location || '-';
                const status = button.dataset.status || '-';

                activityDetailContentEl.innerHTML = `
                    <div class="info-row">
                        <span class="text-secondary">Aktivitas</span>
                        <strong class="text-end">${activity}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-secondary">Jenis Aktivitas</span>
                        <strong class="text-end">${type}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-secondary">Waktu</span>
                        <strong class="text-end">${time}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-secondary">Perangkat</span>
                        <strong class="text-end">${device}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-secondary">Lokasi</span>
                        <strong class="text-end">${location}</strong>
                    </div>
                    <div class="info-row">
                        <span class="text-secondary">Status</span>
                        <strong class="text-end">${status}</strong>
                    </div>
                `;

                activityDetailModal.show();
            });
        });
    </script>
</body>
</html>
