<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Magang - Penempatan</title>

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
        .placement-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .action-card { display:block; color:inherit; text-decoration:none; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .nav-tabs .nav-link { color:var(--muted); font-weight:600; }
        .nav-tabs .nav-link.active { color:var(--brand); border-color:var(--line) var(--line) #fff; }
        .info-row { display:flex; justify-content:space-between; gap:16px; padding:13px 0; border-bottom:1px solid var(--line); }
        .info-row:last-child { border-bottom:0; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .timeline-item { position:relative; padding-left:28px; padding-bottom:18px; }
        .timeline-item:before { content:""; position:absolute; left:8px; top:6px; width:10px; height:10px; border-radius:50%; background:var(--brand); }
        .timeline-item:after { content:""; position:absolute; left:12px; top:18px; bottom:0; width:2px; background:var(--line); }
        .timeline-item:last-child:after { display:none; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } .info-row { flex-direction:column; gap:2px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $user = $user ?? auth()->user();
        $userName = $userName ?? ($user?->name ?? 'Peserta Magang');
        $avatar = $avatar ?? ($user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff');
        $peserta = $peserta ?? $user?->peserta ?? null;
        $internship = $internship ?? $peserta?->internship ?? null;
        $mentor = $mentor ?? $internship?->mentor ?? null;
        $mentorName = $mentorName ?? ($mentor?->user?->name ?? '-');
        $mentorEmail = $mentorEmail ?? ($mentor?->user?->email ?? '-');
        $mentorJabatan = $mentorJabatan ?? ($mentor?->jabatan ?? '-');
        $pembimbingName = $pembimbingName ?? ($pembimbingUser?->name ?? $internship?->pembimbing?->user?->name ?? '-');
        $placementInstitution = $placementInstitution ?? ($internship?->instansi ?? $peserta?->perguruanTinggi?->nama_pt ?? 'LLDIKTI Wilayah V Yogyakarta');
        $placementPosition = $placementPosition ?? ($internship?->posisi ?? $internship?->divisi ?? $internship?->unit_kerja ?? '-');
        $placementName = $placementPosition !== '-'
            ? $placementInstitution . ' - ' . $placementPosition
            : $placementInstitution;
        $placementStatus = $placementStatus ?? ($internship?->status ?? 'belum tersedia');
        $placementStatusBadge = match ($placementStatus) {
            'berjalan', 'aktif' => 'bg-success',
            'selesai' => 'bg-secondary',
            'menunggu' => 'bg-warning text-dark',
            default => 'bg-info text-dark',
        };
        $mentorNip = $mentor?->nip ?? $internship?->mentor?->nip ?? '-';
        $startDate = $internship?->tanggal_mulai;
        $endDate = $internship?->tanggal_selesai;
        $periodText = $startDate && $endDate
            ? $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y')
            : '-';
        $internshipProgress = $internshipProgress ?? ($startDate && $endDate
            ? min(100, (int) round((max($startDate->diffInDays(now()), 0) / max($startDate->diffInDays($endDate), 1)) * 100))
            : 0);
        $placementSummary = [
            ['label' => 'Instansi', 'value' => $placementInstitution, 'icon' => 'bi-building', 'badge' => 'bg-secondary'],
            ['label' => 'Posisi', 'value' => $placementPosition, 'icon' => 'bi-diagram-3', 'badge' => $placementStatus === 'selesai' ? 'bg-secondary' : 'bg-primary'],
            ['label' => 'Mentor', 'value' => $mentorName ?: 'Belum ditentukan', 'icon' => 'bi-person-badge', 'badge' => 'bg-warning'],
            ['label' => 'Periode', 'value' => $periodText, 'icon' => 'bi-calendar-range', 'badge' => 'bg-info text-dark'],
            ['label' => 'Status', 'value' => ucfirst($placementStatus), 'icon' => 'bi-patch-check', 'badge' => $placementStatusBadge],
        ];
        $placementHistory = [];

        if ($internship) {
            $placementHistory[] = [
                'title' => 'Penempatan ditetapkan',
                'meta' => optional($internship->created_at)->translatedFormat('d M Y, H:i') . ' WIB - Sistem',
            ];

            if ($internship->updated_at && (! $internship->created_at || $internship->updated_at->ne($internship->created_at))) {
                $placementHistory[] = [
                    'title' => 'Penempatan diperbarui',
                    'meta' => optional($internship->updated_at)->translatedFormat('d M Y, H:i') . ' WIB - Sistem',
                ];
            }
        }
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
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dataMagangMenu" aria-expanded="true" aria-controls="dataMagangMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="dataMagangMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a>
                <a href="{{ route('peserta.data-magang.penempatan') }}" class="active">Penempatan</a>
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
        <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button></form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
                    <div>
                        <h2 class="fw-bold mb-1">Penempatan</h2>
                        <div class="small-muted">Detail penempatan peserta di LLDIKTI Wilayah V, berfokus pada divisi atau sub bagian tempat peserta ditempatkan.</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    @include('peserta.partials.topbar-actions')
                    <div class="dropdown">
                        <button class="btn btn-light border d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <img src="{{ $avatar }}" class="rounded-circle" width="42" height="42" alt="Foto profil peserta">
                            <span class="text-start d-none d-sm-block"><strong class="d-block">{{ $userName }}</strong><small class="text-muted">Peserta Magang</small></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('peserta.data-magang.profil') }}"><i class="bi bi-person me-2"></i>Profil Peserta</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></form></li>
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
                    <li class="breadcrumb-item active" aria-current="page">Penempatan</li>
                </ol>
            </nav>

            <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <div>
                    @if($internship)
                        Penempatan peserta ini tersimpan di database dan mengikuti record `internships` milik akun yang login.
                    @else
                        Belum ada data penempatan pada akun peserta ini.
                    @endif
                </div>
            </div>

            <section class="placement-hero p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                    <div class="d-flex gap-3">
                        <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                        <div>
                            <h3 class="fw-bold mb-1">{{ $placementName ?: 'Penempatan belum tersedia' }}</h3>
                            <div class="text-secondary">{{ $internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta' }} | {{ $internship?->divisi ?? $internship?->posisi ?? $internship?->unit_kerja ?? '-' }} | {{ $internship?->mentor?->user?->name ?? 'Mentor belum ditentukan' }}</div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge {{ $placementStatusBadge }}">Penempatan {{ ucfirst($placementStatus) }}</span>
                                <span class="badge bg-info text-dark">{{ $periodText }}</span>
                                <span class="badge bg-light text-dark border">Mentor: {{ $internship?->mentor?->user?->name ?? 'Belum ditentukan' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="align-self-xl-center" style="min-width:260px;">
                        <div class="d-flex justify-content-between mb-2"><strong>Progress Magang</strong><span>{{ $internshipProgress }}%</span></div>
                        <div class="progress"><div class="progress-bar bg-success" style="width: {{ $internshipProgress }}%"></div></div>
                        <div class="small-muted mt-2">Data diperbarui otomatis berdasarkan aktivitas dan administrasi terbaru.</div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    @foreach($placementSummary as $summary)
                        <div class="col-md-6 col-xl-2">
                            <div class="stat-card p-3">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="small-muted">{{ $summary['label'] }}</div>
                                        <h5 class="fw-bold mb-1">{{ $summary['value'] }}</h5>
                                        <span class="badge {{ $summary['badge'] }}">Data DB</span>
                                    </div>
                                    <span class="stat-icon bg-primary"><i class="bi {{ $summary['icon'] }}"></i></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#placementDetailModal"><span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Detail Penempatan</strong><span class="small-muted">Lihat informasi lengkap</span></button></div>
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 border-0 text-start d-block" href="{{ route('peserta.data-magang.penempatan.download') }}"><span class="action-icon bg-secondary mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Unduh Surat</strong><span class="small-muted">Ringkasan penempatan</span></a></div>
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 border-0 text-start d-block" href="{{ route('peserta.komunikasi.pesan') }}"><span class="action-icon bg-warning mb-2"><i class="bi bi-chat-dots"></i></span><strong class="d-block">Hubungi Mentor</strong><span class="small-muted">Kirim pesan cepat</span></a></div>
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 border-0 text-start d-block" href="{{ route('peserta.dokumen.pendukung') }}"><span class="action-icon bg-success mb-2"><i class="bi bi-folder2-open"></i></span><strong class="d-block">Lihat Dokumen</strong><span class="small-muted">Surat dan lampiran</span></a></div>
                </div>
            </section>

            <section class="soft-card mb-4">
                <ul class="nav nav-tabs px-3 pt-3">
                    <li class="nav-item"><a class="nav-link" href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('peserta.data-magang.penempatan') }}">Penempatan</a></li>
                </ul>

                <div class="p-3 p-lg-4">
                    <div class="row g-4 mb-4">
                        <div class="col-xl-7">
                            <div class="border rounded-3 p-3 h-100">
                                <h5 class="fw-bold mb-3">Detail Penempatan Aktif</h5>
                                <div class="info-row"><span class="small-muted">Instansi</span><strong>{{ $internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Posisi</span><strong>{{ $internship?->divisi ?? $internship?->posisi ?? $internship?->unit_kerja ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Administrasi</span><strong><span class="badge bg-success">{{ $internship ? 'Data penempatan tersimpan di database' : 'Belum tersedia' }}</span></strong></div>
                            </div>
                        </div>
                        <div class="col-xl-5">
                            <div class="border rounded-3 p-3 h-100">
                                <h5 class="fw-bold mb-3">Mentor dan Periode</h5>
                                <div class="info-row"><span class="small-muted">Mentor Lapangan</span><strong>{{ $internship?->mentor?->user?->name ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">NIP Mentor</span><strong>{{ $mentorNip }}</strong></div>
                                <div class="info-row"><span class="small-muted">Jabatan Mentor</span><strong>{{ $mentor?->jabatan ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Kontak Mentor</span><strong>{{ $internship?->mentor?->user?->email ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Pembimbing Akademik</span><strong>{{ $internship?->pembimbing?->user?->name ?? '-' }}</strong></div>
                                <div class="info-row"><span class="small-muted">Periode Magang</span><strong>{{ $periodText }}</strong></div>
                                <div class="info-row"><span class="small-muted">Status</span><strong><span class="badge {{ $placementStatusBadge }}">{{ ucfirst($placementStatus) }}</span></strong></div>
                            </div>
                        </div>
                    </div>

                    <form class="filter-card p-3 mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-lg-3">
                                <label class="form-label">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="search" class="form-control" placeholder="Cari posisi, mentor..." value="{{ $placementPosition }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <label class="form-label">Status Penempatan</label>
                                <select class="form-select"><option>Semua</option><option {{ in_array($placementStatus, ['aktif','berjalan']) ? 'selected' : '' }}>Aktif</option><option {{ $placementStatus === 'selesai' ? 'selected' : '' }}>Selesai</option><option {{ $placementStatus === 'menunggu' ? 'selected' : '' }}>Menunggu</option></select>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <label class="form-label">Periode</label>
                                <select class="form-select"><option>Semua Periode</option><option>{{ $startDate ? $startDate->format('Y') : '2026' }}</option><option>{{ $startDate ? $startDate->copy()->subYear()->format('Y') : '2025' }}</option></select>
                            </div>
                            <div class="col-md-4 col-lg-2">
                                <label class="form-label">Posisi</label>
                                <select class="form-select"><option>{{ $placementPosition }}</option></select>
                            </div>
                            <div class="col-lg-3 d-flex flex-wrap gap-2">
                                <button class="btn btn-primary" type="button"><i class="bi bi-search me-1"></i> Cari</button>
                                <button class="btn btn-outline-secondary" type="reset"><i class="bi bi-x-circle me-1"></i> Reset</button>
                                <a class="btn btn-outline-primary ms-lg-auto" href="{{ route('peserta.data-magang.penempatan.download') }}"><i class="bi bi-download me-1"></i> Unduh</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-card mb-4">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Instansi</th>
                                        <th>Posisi</th>
                                        <th>Mentor</th>
                                        <th>Periode</th>
                                        <th>Status Penempatan</th>
                                        <th>Progress</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($placementRecords as $index => $record)
                                        @php
                                            $recordStatus = strtolower((string) ($record->status ?? 'belum tersedia'));
                                            $recordStatusBadge = match ($recordStatus) {
                                                'berjalan', 'aktif' => 'bg-success',
                                                'selesai' => 'bg-secondary',
                                                'menunggu' => 'bg-warning text-dark',
                                                default => 'bg-info text-dark',
                                            };
                                            $recordStartDate = $record->tanggal_mulai;
                                            $recordEndDate = $record->tanggal_selesai;
                                            $recordPeriodText = $recordStartDate && $recordEndDate
                                                ? $recordStartDate->translatedFormat('d M Y') . ' - ' . $recordEndDate->translatedFormat('d M Y')
                                                : '-';
                                            $recordProgress = $recordStartDate && $recordEndDate
                                                ? min(100, (int) round((max($recordStartDate->diffInDays(now()), 0) / max($recordStartDate->diffInDays($recordEndDate), 1)) * 100))
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $record->instansi ?? 'LLDIKTI Wilayah V Yogyakarta' }}</strong></td>
                                            <td>{{ $record->divisi ?? $record->posisi ?? $record->unit_kerja ?? '-' }}</td>
                                            <td>{{ $record->mentor?->user?->name ?? '-' }}</td>
                                            <td>{{ $recordPeriodText }}</td>
                                            <td><span class="badge {{ $recordStatusBadge }}">{{ ucfirst($recordStatus) }}</span></td>
                                            <td style="min-width:150px;"><div class="d-flex justify-content-between small"><span>{{ $recordProgress }}%</span><span>{{ $recordStatus === 'selesai' ? 'Selesai' : 'Berjalan' }}</span></div><div class="progress"><div class="progress-bar bg-success" style="width:{{ $recordProgress }}%"></div></div></td>
                                            <td><div class="btn-group btn-group-sm flex-nowrap"><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#placementDetailModal">Detail</button><a class="btn btn-outline-secondary" href="{{ route('peserta.data-magang.penempatan.download') }}">Unduh</a></div></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">Data penempatan belum tersedia untuk akun ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-xl-7">
                            <h5 class="fw-bold mb-3">Histori Perubahan Penempatan</h5>
                            @forelse($placementHistory as $history)
                                <div class="timeline-item"><strong>{{ $history['title'] }}</strong><div class="small-muted">{{ $history['meta'] }}</div></div>
                            @empty
                                <div class="text-muted">Belum ada histori penempatan.</div>
                            @endforelse
                        </div>
                        <div class="col-xl-5">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="small-muted">Menampilkan {{ $placementRecords->count() ? '1-' . $placementRecords->count() . ' dari ' . $placementRecords->count() . ' data' : '0 data' }}</span>
                                    <select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select>
                                </div>
                                <nav aria-label="Pagination penempatan">
                                    <ul class="pagination mb-0">
                                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="placementDetailModal" tabindex="-1" aria-labelledby="placementDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="placementDetailLabel">Detail Penempatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="info-row"><span class="small-muted">Peserta</span><strong>{{ $userName }}</strong></div>
                    <div class="info-row"><span class="small-muted">Penempatan</span><strong>{{ $placementName }}</strong></div>
                    <div class="info-row"><span class="small-muted">Instansi</span><strong>{{ $internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Posisi</span><strong>{{ $internship?->divisi ?? $internship?->posisi ?? $internship?->unit_kerja ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Mentor</span><strong>{{ $internship?->mentor?->user?->name ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Pembimbing Akademik</span><strong>{{ $internship?->pembimbing?->user?->name ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Periode</span><strong>{{ $periodText }}</strong></div>
                    <div class="info-row"><span class="small-muted">Status</span><strong><span class="badge {{ $placementStatusBadge }}">{{ ucfirst($placementStatus) }}</span></strong></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="{{ route('peserta.data-magang.penempatan.download') }}">Unduh Ringkasan</a>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
