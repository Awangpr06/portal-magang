<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Peserta</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .calc-card, .info-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .score-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .score-circle { width:118px; height:118px; border-radius:50%; display:grid; place-items:center; background:conic-gradient(var(--accent) 0 90%, #e4eef2 90% 100%); }
        .score-circle-inner { width:90px; height:90px; border-radius:50%; background:#fff; display:grid; place-items:center; text-align:center; }
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
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $assessmentCollection = isset($assessments) ? collect($assessments) : collect();
        $assessmentStats = $stats ?? [];

        $normalize = static function (?string $value): string {
            return strtolower(trim((string) $value));
        };

        $gradeFromScore = static function (float $score): string {
            if ($score >= 90) return 'A';
            if ($score >= 85) return 'B+';
            if ($score >= 80) return 'B';
            if ($score >= 75) return 'C+';
            if ($score >= 70) return 'C';
            return $score > 0 ? 'D' : '-';
        };

        $componentAliases = [
            'kehadiran' => ['kehadiran', 'presence', 'absensi'],
            'aktivitas' => ['aktivitas', 'activity', 'penugasan'],
            'laporan' => ['laporan', 'report', 'logbook'],
            'sikap' => ['sikap', 'attitude', 'perilaku'],
            'kompetensi' => ['kompetensi', 'competency', 'skill'],
        ];

        $assessmentRows = $assessmentCollection
            ->groupBy(function ($assessment) {
                $reviewer = $assessment->mentor?->user?->name
                    ?? $assessment->pembimbing?->user?->name
                    ?? 'Penilai';

                return implode('|', [
                    $assessment->periode ?: 'Tanpa Periode',
                    $reviewer,
                ]);
            })
            ->values()
            ->map(function ($group, $index) use ($normalize, $gradeFromScore, $componentAliases) {
                $first = $group->first();
                $penilai = $first->mentor?->user?->name
                    ?? $first->pembimbing?->user?->name
                    ?? '-';
                $periode = $first->periode ?: '-';
                $statusKey = strtolower((string) ($group->pluck('status')->filter()->first() ?? $first->status ?? 'draft'));

                $statusMeta = match ($statusKey) {
                    'final', 'selesai', 'disetujui' => ['label' => 'Dinilai', 'class' => 'success'],
                    'finalisasi' => ['label' => 'Finalisasi', 'class' => 'warning text-dark'],
                    'draft' => ['label' => 'Draft', 'class' => 'secondary'],
                    default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Draft', 'class' => 'secondary'],
                };

                $components = [
                    'kehadiran' => '-',
                    'aktivitas' => '-',
                    'laporan' => '-',
                    'sikap' => '-',
                    'kompetensi' => '-',
                ];

                foreach ($group as $assessment) {
                    $assessmentName = $normalize($assessment->komponen);
                    foreach ($componentAliases as $target => $aliases) {
                        foreach ($aliases as $alias) {
                            if (str_contains($assessmentName, $alias)) {
                                $components[$target] = number_format((float) ($assessment->nilai_akhir ?? $assessment->nilai ?? 0), 2);
                                break 2;
                            }
                        }
                    }
                }

                $numericScores = collect($components)->filter(fn ($value) => $value !== '-')->map(fn ($value) => (float) $value);
                $finalScore = $numericScores->count()
                    ? (int) round((float) $numericScores->avg())
                    : (int) round((float) ($first->nilai_akhir ?? $first->nilai ?? 0));

                return [
                    'no' => $index + 1,
                    'periode' => $periode,
                    'penilai' => $penilai,
                    'kehadiran' => $components['kehadiran'],
                    'aktivitas' => $components['aktivitas'],
                    'laporan' => $components['laporan'],
                    'sikap' => $components['sikap'],
                    'kompetensi' => $components['kompetensi'],
                    'nilai_akhir' => $finalScore,
                    'grade' => $gradeFromScore($finalScore),
                    'status_label' => $statusMeta['label'],
                    'status_class' => $statusMeta['class'],
                    'catatan' => $first->catatan ?: '-',
                ];
            });

        $mentorRows = $assessmentCollection->filter(fn ($item) => strtolower((string) ($item->jenis ?? '')) === 'mentor');
        $pembimbingRows = $assessmentCollection->filter(fn ($item) => strtolower((string) ($item->jenis ?? '')) === 'pembimbing');
        $firstAssessment = $assessmentRows->first();
        $mentorScore = $assessmentStats['mentor_score'] ?? ($mentorRows->count() ? (int) round((float) $mentorRows->avg('nilai_akhir')) : 0);
        $pembimbingScore = $assessmentStats['pembimbing_score'] ?? ($pembimbingRows->count() ? (int) round((float) $pembimbingRows->avg('nilai_akhir')) : 0);
        $finalScore = $assessmentStats['assessment_final'] ?? ($assessmentCollection->count() ? (int) round((float) $assessmentCollection->whereIn('status', ['final', 'selesai', 'disetujui'])->avg('nilai_akhir') ?: (float) $assessmentCollection->avg('nilai_akhir')) : 0);
        $finalGrade = $finalScore >= 90 ? 'A' : ($finalScore >= 80 ? 'B' : ($finalScore >= 70 ? 'C' : 'D'));
        $assessmentTotal = $assessmentStats['assessment_total'] ?? $assessmentRows->count();
        $finalizedCount = $assessmentCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['final', 'selesai', 'disetujui'], true))->count();
        $assessmentProgress = $assessmentTotal > 0 ? (int) round(($finalizedCount / $assessmentTotal) * 100) : 0;
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
                <a href="{{ route('peserta.penilaian.rekap') }}" class="active">Rekap Nilai</a>
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
                        <h2 class="fw-bold mb-1">Rekap Nilai</h2>
                        <div class="small-muted">Lihat hasil evaluasi mentor dan pembimbing akademik selama periode magang.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Rekap Nilai</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Rekap nilai dihitung dari evaluasi mentor, pembimbing akademik, dan dokumentasi aktivitas magang terbaru.</div>
            </div>

            <section class="score-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-12">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">Rekapitulasi Nilai Magang</h3>
                                <div class="text-secondary">{{ $userName }} | UI/UX Intern | PT Solusi Teknologi</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">Nilai Akhir: {{ $finalGrade }} ({{ $finalScore }})</span>
                                    <span class="badge bg-primary">Mentor: {{ $mentorScore }}</span>
                                    <span class="badge bg-info text-dark">Pembimbing: {{ $pembimbingScore }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Nilai Akhir</div><h4 class="fw-bold mb-1">{{ $finalGrade }} ({{ $finalScore }})</h4><span class="badge bg-success">Memuaskan</span></div><span class="stat-icon bg-success"><i class="bi bi-star-fill"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Nilai Mentor</div><h4 class="fw-bold mb-1">{{ $mentorScore }}</h4><span class="badge bg-primary">Bobot 60%</span></div><span class="stat-icon bg-primary"><i class="bi bi-person-workspace"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Nilai Pembimbing</div><h4 class="fw-bold mb-1">{{ $pembimbingScore }}</h4><span class="badge bg-info text-dark">Bobot 40%</span></div><span class="stat-icon bg-info"><i class="bi bi-mortarboard"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Periode Magang</div><h4 class="fw-bold mb-1">{{ data_get($firstAssessment, 'periode', 'Belum ada') }}</h4><span class="badge bg-secondary">{{ data_get($firstAssessment, 'periode') ? 'Database' : 'Belum lengkap' }}</span></div><span class="stat-icon bg-secondary"><i class="bi bi-calendar-range"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Status</div><h4 class="fw-bold mb-1">{{ $assessmentProgress }}%</h4><span class="badge bg-warning text-dark">Finalisasi</span></div><span class="stat-icon bg-warning"><i class="bi bi-clipboard-check"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3">
                        <button
                            type="button"
                            class="action-card w-100 p-3 border-0 text-start"
                            data-action="assessment-detail"
                            data-periode="{{ e(data_get($firstAssessment, 'periode', '-')) }}"
                            data-penilai="{{ e(data_get($firstAssessment, 'penilai', '-')) }}"
                            data-kehadiran="{{ e(data_get($firstAssessment, 'kehadiran', '-')) }}"
                            data-aktivitas="{{ e(data_get($firstAssessment, 'aktivitas', '-')) }}"
                            data-laporan="{{ e(data_get($firstAssessment, 'laporan', '-')) }}"
                            data-sikap="{{ e(data_get($firstAssessment, 'sikap', '-')) }}"
                            data-kompetensi="{{ e(data_get($firstAssessment, 'kompetensi', '-')) }}"
                            data-nilai-akhir="{{ e(data_get($firstAssessment, 'nilai_akhir', $finalScore)) }}"
                            data-grade="{{ e(data_get($firstAssessment, 'grade', $finalGrade)) }}"
                            data-status="{{ e(data_get($firstAssessment, 'status_label', 'Dinilai')) }}"
                        >
                            <span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span>
                            <strong class="d-block">Detail Nilai</strong>
                            <span class="small-muted">Rincian komponen</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#confirmModal"><span class="action-icon bg-danger mb-2"><i class="bi bi-filetype-pdf"></i></span><strong class="d-block">Unduh PDF</strong><span class="small-muted">Simpan rekap</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#scoreToast"><span class="action-icon bg-secondary mb-2"><i class="bi bi-printer"></i></span><strong class="d-block">Cetak Rekap</strong><span class="small-muted">Cetak halaman</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#contactModal"><span class="action-icon bg-success mb-2"><i class="bi bi-chat-dots"></i></span><strong class="d-block">Hubungi Penilai</strong><span class="small-muted">Mentor/pembimbing</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Rekap Nilai</h5>
                                <div class="small-muted">Lihat hasil evaluasi mentor dan pembimbing akademik selama periode magang.</div>
                            </div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#scoreInfoPanel" aria-controls="scoreInfoPanel">
                                <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                            </button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari komponen"></div>
                                </div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Jenis</label><select class="form-select"><option>Semua</option><option>Mentor</option><option>Pembimbing</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Periode</label><select class="form-select"><option>Mei-Juli 2026</option><option>Mei 2026</option><option>Juni 2026</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Penilai</label><select class="form-select"><option>Semua</option><option>Budi Santoso</option><option>Dr. Rina</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Dinilai</option><option>Finalisasi</option></select></div>
                                <div class="col-lg-1"><button class="btn btn-primary w-100" type="button" data-bs-toggle="toast" data-bs-target="#scoreToast">Filter</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Periode</th><th>Penilai</th><th>Kehadiran</th><th>Aktivitas</th><th>Laporan</th><th>Sikap</th><th>Kompetensi</th><th>Nilai Akhir</th><th>Grade</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($assessmentRows as $assessment)
                                            <tr>
                                                <td>{{ $assessment['no'] }}</td>
                                                <td>{{ $assessment['periode'] }}</td>
                                                <td>{{ $assessment['penilai'] }}</td>
                                                <td>{{ $assessment['kehadiran'] }}</td>
                                                <td>{{ $assessment['aktivitas'] }}</td>
                                                <td>{{ $assessment['laporan'] }}</td>
                                                <td>{{ $assessment['sikap'] }}</td>
                                                <td>{{ $assessment['kompetensi'] }}</td>
                                                <td>{{ $assessment['nilai_akhir'] }}</td>
                                                <td>{{ $assessment['grade'] }}</td>
                                                <td><span class="badge bg-{{ $assessment['status_class'] }}">{{ $assessment['status_label'] }}</span></td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        data-action="assessment-detail"
                                                        data-periode="{{ e($assessment['periode']) }}"
                                                        data-penilai="{{ e($assessment['penilai']) }}"
                                                        data-kehadiran="{{ e($assessment['kehadiran']) }}"
                                                        data-aktivitas="{{ e($assessment['aktivitas']) }}"
                                                        data-laporan="{{ e($assessment['laporan']) }}"
                                                        data-sikap="{{ e($assessment['sikap']) }}"
                                                        data-kompetensi="{{ e($assessment['kompetensi']) }}"
                                                        data-nilai-akhir="{{ e($assessment['nilai_akhir']) }}"
                                                        data-grade="{{ e($assessment['grade']) }}"
                                                        data-status="{{ e($assessment['status_label']) }}"
                                                    >
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center py-4">
                                                    <div class="small-muted">Belum ada data penilaian dari database.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $assessmentRows->count() ? '1-' . min(4, $assessmentRows->count()) : '0' }} dari {{ $assessmentRows->count() }} rekap</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option></select></div>
                            <nav aria-label="Pagination rekap nilai"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailLabel">Detail Nilai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div id="assessmentDetailContent" class="vstack gap-2 small">
                        <div class="text-secondary">Pilih salah satu rekap untuk melihat detail komponennya.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#scoreToast">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="contactLabel">Hubungi Penilai</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pilih komunikasi dengan mentor lapangan atau pembimbing akademik untuk menanyakan detail hasil evaluasi.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-success" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#scoreToast">Kirim Pesan</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Tindakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Sistem akan menyiapkan rekap nilai dalam format PDF berdasarkan data penilaian terbaru.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#scoreToast">Lanjutkan</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="scoreToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Rekap Nilai</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan rekap nilai berhasil diproses dan histori akses diperbarui.</div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="scoreInfoPanel" aria-labelledby="scoreInfoPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="scoreInfoPanelLabel">Panel Informasi Rekap Nilai</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <section class="calc-card p-3 p-lg-4 mb-4">
                <h5 class="fw-bold mb-1">Perhitungan Nilai Akhir</h5>
                <div class="small-muted mb-3">Akumulasi nilai berdasarkan bobot evaluasi.</div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Mentor</strong><span>{{ $mentorScore }} x 60%</span></div><div class="progress mt-2"><div class="progress-bar bg-primary" style="width:60%"></div></div></div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Pembimbing</strong><span>{{ $pembimbingScore }} x 40%</span></div><div class="progress mt-2"><div class="progress-bar bg-info" style="width:40%"></div></div></div>
                <div class="alert alert-success mb-0"><strong>Nilai akhir: {{ $finalScore }}</strong><br>Kategori {{ $finalGrade }} dengan status finalisasi.</div>
            </section>
            <section class="info-card p-3 p-lg-4">
                <h5 class="fw-bold mb-1">Informasi Penilaian</h5>
                <div class="small-muted mb-3">Catatan evaluasi terbaru dari sistem.</div>
                <div class="d-flex gap-3 mb-3"><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span><div><strong>Data lengkap</strong><div class="small-muted">Seluruh komponen utama sudah tersedia di database.</div></div></div>
                <div class="d-flex gap-3 mb-3"><span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span><div><strong>Finalisasi</strong><div class="small-muted">Status komponen mengikuti data penilai terbaru.</div></div></div>
                <div class="d-flex gap-3"><span class="stat-icon bg-primary"><i class="bi bi-shield-check"></i></span><div><strong>Dokumentasi</strong><div class="small-muted">Rekap dapat diunduh untuk arsip peserta.</div></div></div>
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

        const assessmentDetailModalEl = document.getElementById('detailModal');
        const assessmentDetailContentEl = document.getElementById('assessmentDetailContent');
        const assessmentDetailModal = assessmentDetailModalEl ? bootstrap.Modal.getOrCreateInstance(assessmentDetailModalEl) : null;

        function renderAssessmentDetail(button) {
            if (!assessmentDetailContentEl || !assessmentDetailModal) {
                return;
            }

            const rows = [
                ['Periode', button.dataset.periode || '-'],
                ['Penilai', button.dataset.penilai || '-'],
                ['Kehadiran', button.dataset.kehadiran || '-'],
                ['Aktivitas', button.dataset.aktivitas || '-'],
                ['Laporan', button.dataset.laporan || '-'],
                ['Sikap', button.dataset.sikap || '-'],
                ['Kompetensi', button.dataset.kompetensi || '-'],
                ['Nilai Akhir', button.dataset.nilaiAkhir || '-'],
                ['Grade', button.dataset.grade || '-'],
                ['Status', button.dataset.status || '-'],
            ];

            assessmentDetailContentEl.innerHTML = rows.map(([label, value]) => `
                <div class="info-row">
                    <span class="text-secondary">${label}</span>
                    <strong class="text-end">${value}</strong>
                </div>
            `).join('');

            assessmentDetailModal.show();
        }

        document.querySelectorAll('[data-action="assessment-detail"]').forEach((button) => {
            button.addEventListener('click', () => renderAssessmentDetail(button));
        });
    </script>
</body>
</html>
