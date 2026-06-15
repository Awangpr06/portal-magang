<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Peserta</title>

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
        .assessment-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .score-circle { width:112px; height:112px; border-radius:50%; display:grid; place-items:center; background:conic-gradient(var(--accent) 0 90%, #e4eef2 90% 100%); }
        .score-circle-inner { width:86px; height:86px; border-radius:50%; background:#fff; display:grid; place-items:center; text-align:center; }
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
        $assessmentCollection = collect($assessments ?? []);
        $assessmentRows = collect($assessmentRows ?? []);
        $activeAssessment = $activeAssessment ?? 'Ringkasan Penilaian';
        $hideInfoPanel = (auth()->user()->role ?? null) === 'peserta';
        $latestAssessment = $assessmentCollection->sortByDesc('created_at')->first();
        $finalScore = (float) ($stats['assessment_final'] ?? ($assessmentCollection->isNotEmpty() ? $assessmentCollection->avg('nilai_akhir') : 0));
        $finalScoreDisplay = $assessmentCollection->isNotEmpty() && $finalScore > 0 ? number_format($finalScore, 0, ',', '.') : '-';
        $finalGrade = match (true) {
            $finalScore >= 90 => 'A',
            $finalScore >= 85 => 'B+',
            $finalScore >= 80 => 'B',
            $finalScore >= 75 => 'C+',
            $finalScore >= 70 => 'C',
            $finalScore > 0 => 'D',
            default => '-',
        };
        $assessmentStatusLabel = $assessmentCollection->isEmpty()
            ? 'Menunggu'
            : ($assessmentCollection->contains(fn ($assessment) => in_array(strtolower((string) ($assessment->status ?? '')), ['final', 'selesai', 'disetujui'], true)) ? 'Finalisasi' : 'Dinilai');
        $assessmentStatusClass = match ($assessmentStatusLabel) {
            'Finalisasi' => 'bg-info text-dark',
            'Dinilai' => 'bg-success',
            default => 'bg-warning text-dark',
        };
        $mentorName = $latestAssessment?->mentor?->user?->name
            ?? $peserta?->internship?->mentor?->user?->name
            ?? '-';
        $pembimbingName = $latestAssessment?->pembimbing?->user?->name
            ?? $peserta?->internship?->pembimbing?->user?->name
            ?? '-';
        $periodLabel = $latestAssessment?->periode
            ?? $peserta?->program_magang
            ?? 'Belum ada periode';
        $assessmentTotal = (int) ($stats['assessment_total'] ?? $assessmentCollection->count());
        $finalizedCount = $assessmentCollection->filter(fn ($assessment) => in_array(strtolower((string) ($assessment->status ?? '')), ['final', 'selesai', 'disetujui'], true))->count();
        $assessmentProgress = $assessmentTotal > 0 ? (int) round(($finalizedCount / $assessmentTotal) * 100) : 0;
        $attendancePercent = (int) ($stats['attendance_percent'] ?? 0);
        $reportTotal = (int) ($stats['report_total'] ?? 0);
        $reportApproved = (int) ($stats['report_approved'] ?? 0);
        $reportProgress = $reportTotal > 0
            ? (int) round(($reportApproved / $reportTotal) * 100)
            : 0;
        $assignmentProgress = (int) ($stats['assignment_progress'] ?? 0);
        $scoreCircleStyle = 'background:conic-gradient(var(--accent) 0 '.$assessmentProgress.'%, #e4eef2 '.$assessmentProgress.'% 100%);';
        $recentTimeline = $assessmentRows->take(3);
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
                <a href="{{ route('peserta.penilaian.rekap') }}" class="{{ $activeAssessment === 'Rekap Nilai' ? 'active' : '' }}">Rekap Nilai</a>
                <a href="{{ route('peserta.penilaian.sertifikat') }}" class="{{ $activeAssessment === 'Sertifikat' ? 'active' : '' }}">Sertifikat</a>
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
                        <h2 class="fw-bold mb-1">Penilaian</h2>
                        <div class="small-muted">Pantau ringkasan evaluasi, rekap nilai, dan akses sertifikat magang.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Ringkasan penilaian diperbarui otomatis berdasarkan evaluasi mentor dan pembimbing akademik.</div>
            </div>

            <section class="assessment-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $activeAssessment }}</h3>
                                <div class="text-secondary">{{ $userName }} | {{ $peserta?->program_magang ?? 'Program Magang' }} | {{ $internship?->instansi ?? 'Instansi Magang' }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">Nilai Akhir {{ $finalGrade }}{{ $finalScoreDisplay !== '-' ? ' ('.$finalScoreDisplay.')' : '' }}</span>
                                    <span class="badge {{ $assessmentStatusClass }}">Status: {{ $assessmentStatusLabel }}</span>
                                    <span class="badge bg-primary">Periode {{ $periodLabel }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Nilai Akhir</div><h4 class="fw-bold mb-1">{{ $finalGrade }}{{ $finalScoreDisplay !== '-' ? ' ('.$finalScoreDisplay.')' : '' }}</h4><span class="badge bg-success">Database</span></div><span class="stat-icon bg-success"><i class="bi bi-star-fill"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Status Penilaian</div><h4 class="fw-bold mb-1">{{ $assessmentStatusLabel }}</h4><span class="badge {{ $assessmentStatusClass }}">Review akhir</span></div><span class="stat-icon bg-info"><i class="bi bi-clipboard-check"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Mentor</div><h4 class="fw-bold mb-1">{{ $mentorName }}</h4><span class="badge bg-primary">Lapangan</span></div><span class="stat-icon bg-primary"><i class="bi bi-person-workspace"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Pembimbing</div><h4 class="fw-bold mb-1">{{ $pembimbingName }}</h4><span class="badge bg-warning text-dark">Akademik</span></div><span class="stat-icon bg-warning"><i class="bi bi-mortarboard"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Periode Magang</div><h4 class="fw-bold mb-1">{{ $periodLabel }}</h4><span class="badge bg-secondary">{{ $assessmentTotal }} data</span></div><span class="stat-icon bg-secondary"><i class="bi bi-calendar-range"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 text-start text-decoration-none d-block" href="{{ route('peserta.penilaian.rekap') }}"><span class="action-icon bg-primary mb-2"><i class="bi bi-table"></i></span><strong class="d-block text-dark">Rekap Nilai</strong><span class="small-muted">Lihat detail komponen</span></a></div>
                    <div class="col-md-6 col-xl-3"><a class="action-card w-100 p-3 text-start text-decoration-none d-block" href="{{ route('peserta.penilaian.sertifikat') }}"><span class="action-icon bg-success mb-2"><i class="bi bi-patch-check"></i></span><strong class="d-block text-dark">Sertifikat</strong><span class="small-muted">Akses sertifikat magang</span></a></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#detailModal"><span class="action-icon bg-info mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Lihat Detail</strong><span class="small-muted">Buka rincian evaluasi</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#infoModal"><span class="action-icon bg-warning mb-2"><i class="bi bi-info-circle"></i></span><strong class="d-block">Informasi Penilaian</strong><span class="small-muted">Ketentuan evaluasi</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Penilaian Peserta</h5>
                                <div class="small-muted">Daftar penilaian dan rekap hasil evaluasi peserta.</div>
                            </div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#assessmentInfoPanel" aria-controls="assessmentInfoPanel">
                                <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                            </button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari jenis penilaian"></div>
                                </div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Jenis</label><select class="form-select"><option>Semua</option><option>Mentor</option><option>Pembimbing</option><option>Administrasi</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Periode</label><select class="form-select"><option>Mei-Juli 2026</option><option>Mei 2026</option><option>Juni 2026</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Finalisasi</option><option>Dinilai</option><option>Menunggu</option></select></div>
                                <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary w-100" type="button" data-bs-toggle="toast" data-bs-target="#assessmentToast">Filter</button><button class="btn btn-outline-secondary" type="reset">Reset</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Jenis Penilaian</th><th>Periode</th><th>Mentor</th><th>Pembimbing Akademik</th><th>Nilai Akhir</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($assessmentRows as $assessment)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><strong>{{ $assessment['jenis_label'] }}</strong><div class="small-muted">{{ $assessment['komponen'] }}</div></td>
                                                <td>{{ $assessment['periode'] }}</td>
                                                <td>{{ $assessment['penilai'] }}</td>
                                                <td>{{ $pembimbingName }}</td>
                                                <td>{{ $assessment['nilai_akhir'] }}</td>
                                                <td><span class="badge bg-{{ $assessment['status_class'] }}">{{ $assessment['status_label'] }}</span></td>
                                                <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-secondary">Belum ada data penilaian dari database.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="small-muted">
                                    @if ($assessmentRows->isNotEmpty())
                                        Menampilkan 1-{{ min(4, $assessmentRows->count()) }} dari {{ $assessmentRows->count() }} penilaian
                                    @else
                                        Belum ada data penilaian
                                    @endif
                                </span>
                                <select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option></select>
                            </div>
                            <nav aria-label="Pagination penilaian"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="detailLabel">Detail Penilaian</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail penilaian memuat komponen kehadiran, logbook, penugasan, laporan, sikap kerja, catatan mentor, dan catatan pembimbing akademik.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#assessmentToast">Buka Rekap</button></div></div></div>
    </div>
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="infoLabel">Informasi Penilaian</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Nilai akhir dihitung dari evaluasi mentor, pembimbing akademik, kehadiran, dokumen, laporan, dan penugasan selama periode magang.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Akses</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Sistem akan membuka informasi lanjutan sesuai submenu atau aksi yang dipilih.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#assessmentToast">Lanjutkan</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="assessmentToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Penilaian</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan penilaian berhasil diproses dan informasi terbaru ditampilkan.</div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="assessmentInfoPanel" aria-labelledby="assessmentInfoPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="assessmentInfoPanelLabel">Panel Informasi Penilaian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <section class="info-card p-3 p-lg-4 mb-4">
                <h5 class="fw-bold mb-1">Panel Informasi</h5>
                <div class="small-muted mb-3">Komponen evaluasi utama peserta.</div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Kehadiran</strong><span>{{ $attendancePercent }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $attendancePercent }}%"></div></div></div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Laporan</strong><span>{{ $reportProgress }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-info" style="width:{{ $reportProgress }}%"></div></div></div>
                <div><div class="d-flex justify-content-between"><strong>Penugasan</strong><span>{{ $assignmentProgress }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-primary" style="width:{{ $assignmentProgress }}%"></div></div></div>
            </section>
            <section class="timeline-card p-3 p-lg-4">
                <h5 class="fw-bold mb-1">Timeline Penilaian</h5>
                <div class="small-muted mb-2">Riwayat proses evaluasi.</div>
                @forelse ($recentTimeline as $timeline)
                    <div class="timeline-item">
                        <strong>{{ $timeline['jenis_label'] }} {{ $timeline['status_label'] === 'Dinilai' ? 'selesai' : $timeline['status_label'] }}</strong>
                        <div class="small-muted">{{ $timeline['tanggal'] }} oleh {{ $timeline['penilai'] }}</div>
                        <span class="badge bg-{{ $timeline['status_class'] }} mt-1">{{ $timeline['status_label'] }}</span>
                    </div>
                @empty
                    <div class="timeline-item">
                        <strong>Belum ada riwayat penilaian</strong>
                        <div class="small-muted">Timeline akan muncul setelah penilai menyimpan data ke database.</div>
                        <span class="badge bg-warning text-dark mt-1">Menunggu</span>
                    </div>
                @endforelse
            </section>
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
