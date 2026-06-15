<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Magang</title>

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
        .soft-card, .stat-card, .table-card, .action-card, .insight-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .activity-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .timeline-item { border-left:4px solid var(--brand); padding:12px 12px 12px 14px; background:#f8fcfe; border-radius:0 8px 8px 0; margin-bottom:12px; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $context = $pesertaContext ?? [];
        $user = $context['user'] ?? auth()->user();
        $userName = $user?->name ?? 'Peserta Magang';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $stats = $context['stats'] ?? [];
        $timelineRows = collect($context['activityTimeline'] ?? []);
        $latestTimeline = $timelineRows->first();
        $activityTotal = $timelineRows->count();
        $activityDone = $timelineRows->where('status_label', 'Selesai')->count();
        $activityWorking = $timelineRows->where('status_label', 'Berjalan')->count();
        $activityLate = $timelineRows->where('status_label', 'Terlambat')->count();
        $activityProgress = $stats['internship_progress'] ?? ($activityTotal > 0 ? (int) round(($activityDone / $activityTotal) * 100) : 0);
        $attendanceCount = $stats['attendance_total'] ?? 0;
        $assignmentCount = $stats['assignment_total'] ?? 0;
        $notificationCount = $stats['notification_unread'] ?? 0;
        $messageCount = $stats['message_unread'] ?? 0;
        $summaryCards = [
            ['label' => 'Absensi', 'value' => $attendanceCount, 'icon' => 'bi-calendar-check', 'color' => 'bg-success', 'route' => 'peserta.aktivitas-magang.absensi'],
            ['label' => 'Penugasan', 'value' => $assignmentCount, 'icon' => 'bi-list-task', 'color' => 'bg-warning', 'route' => 'peserta.aktivitas-magang.penugasan'],
            ['label' => 'Riwayat', 'value' => $activityTotal, 'icon' => 'bi-clock-history', 'color' => 'bg-primary', 'route' => 'peserta.aktivitas-magang.riwayat'],
        ];
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
        <div class="collapse" id="dataMagangMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a><a href="{{ route('peserta.data-magang.penempatan') }}">Penempatan</a></div></div>
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.aktivitas-magang') }}"><i class="bi bi-activity"></i> Aktivitas Magang</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#aktivitasMenu" aria-expanded="true" aria-controls="aktivitasMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="aktivitasMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.aktivitas-magang.absensi') }}">Absensi</a><a href="{{ route('peserta.aktivitas-magang.penugasan') }}">Penugasan</a><a href="{{ route('peserta.aktivitas-magang.riwayat') }}">Riwayat Kegiatan</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.dokumen') }}"><i class="bi bi-folder2-open"></i> Dokumen</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dokumenMenu" aria-expanded="false" aria-controls="dokumenMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="dokumenMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.dokumen.kerjasama') }}">Dokumen Kerjasama</a><a href="{{ route('peserta.dokumen.pendukung') }}">Dokumen Pendukung</a><a href="{{ route('peserta.dokumen.status') }}">Status Dokumen</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.laporan') }}"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="false" aria-controls="laporanMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="laporanMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.laporan.input') }}">Input Laporan</a><a href="{{ route('peserta.laporan.riwayat') }}">Riwayat Laporan</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.penilaian') }}"><i class="bi bi-award-fill"></i> Penilaian</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#penilaianMenu" aria-expanded="false" aria-controls="penilaianMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="penilaianMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.penilaian.rekap') }}">Rekap Nilai</a><a href="{{ route('peserta.penilaian.sertifikat') }}">Sertifikat</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.komunikasi') }}"><i class="bi bi-chat-dots-fill"></i> Komunikasi</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#komunikasiMenu" aria-expanded="false" aria-controls="komunikasiMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="komunikasiMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.komunikasi.pesan') }}">Pesan</a><a href="{{ route('peserta.komunikasi.pengumuman') }}">Pengumuman</a><a href="{{ route('peserta.komunikasi.notifikasi') }}">Notifikasi</a></div></div>
        <div class="sidebar-parent"><a href="{{ route('peserta.pengaturan') }}"><i class="bi bi-gear-fill"></i> Pengaturan Akun</a><button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#pengaturanMenu" aria-expanded="false" aria-controls="pengaturanMenu"><i class="bi bi-chevron-down"></i></button></div>
        <div class="collapse" id="pengaturanMenu"><div class="sidebar-submenu"><a href="{{ route('peserta.pengaturan.profil') }}">Profil Akun</a><a href="{{ route('peserta.pengaturan.password') }}">Ubah Password</a></div></div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button></form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
                    <div>
                        <h2 class="fw-bold mb-1">Aktivitas Magang</h2>
                        <div class="small-muted">Seluruh ringkasan berikut dibentuk dari data absensi, logbook, penugasan, dan riwayat aktivitas di database.</div>
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
                <ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item active" aria-current="page">Aktivitas Magang</li></ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Data aktivitas berikut sudah disinkronkan dengan database akun peserta yang login.</div>
            </div>

            <section class="activity-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">{{ $context['peserta']?->program_magang ?? 'Program magang belum tersedia' }} | {{ $context['placementInstitution'] ?? 'LLDIKTI Wilayah V Yogyakarta' }} | {{ $context['mentorUser']?->name ?? 'Mentor belum ditentukan' }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">{{ $activityDone }} Kegiatan Selesai</span>
                                    <span class="badge bg-info text-dark">{{ $activityWorking }} Sedang Berjalan</span>
                                    <span class="badge bg-danger">{{ $activityLate }} Perlu Tindak Lanjut</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Progress Aktivitas</strong><span>{{ $activityProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-success" style="width:{{ $activityProgress }}%"></div></div>
                            <div class="small-muted mt-2">Progress dihitung dari data aktivitas, laporan, logbook, dan penugasan di database.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    @foreach($summaryCards as $card)
                        <div class="col-md-6 col-xl">
                            <a class="stat-card p-3 d-block text-decoration-none text-dark" href="{{ route($card['route']) }}">
                                <div class="d-flex justify-content-between gap-3">
                                    <div>
                                        <div class="small-muted">{{ $card['label'] }}</div>
                                        <h4 class="fw-bold mb-1">{{ $card['value'] }}</h4>
                                        <span class="badge {{ $card['color'] }}">Database</span>
                                    </div>
                                    <span class="stat-icon {{ $card['color'] }}"><i class="bi {{ $card['icon'] }}"></i></span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Timeline Aktivitas Terbaru</h5>
                                <div class="small-muted">Gabungan data dari laporan, logbook, penugasan, dan aktivitas sistem.</div>
                            </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-outline-primary" href="{{ route('peserta.aktivitas-magang.absensi') }}"><i class="bi bi-calendar-check me-1"></i>Absensi</a>
                                <a class="btn btn-outline-primary" href="{{ route('peserta.aktivitas-magang.penugasan') }}"><i class="bi bi-list-task me-1"></i>Penugasan</a>
                            </div>
                        </div>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Jenis</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Status</th>
                                            <th>Durasi/Progress</th>
                                            <th>Sumber</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($timelineRows->take(8) as $row)
                                            <tr>
                                                <td><strong>{{ $row['tanggal'] }}</strong></td>
                                                <td>{{ $row['jenis'] }}</td>
                                                <td>{{ $row['judul'] }}</td>
                                                <td>{{ $row['kategori'] }}</td>
                                                <td><span class="badge bg-{{ $row['status_class'] }}">{{ $row['status_label'] }}</span></td>
                                                <td>{{ $row['durasi'] }}</td>
                                                <td>{{ $row['sumber'] }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data aktivitas dari database.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="small-muted">Menampilkan {{ $timelineRows->count() ? '1-' . min(8, $timelineRows->count()) : '0' }} dari {{ $timelineRows->count() }} aktivitas</div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('peserta.aktivitas-magang.riwayat') }}">Buka Riwayat Lengkap</a>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-xl-4">
                    <section class="insight-card p-3 p-lg-4 mb-4">
                        <h5 class="fw-bold mb-1">Ringkasan Data</h5>
                        <div class="small-muted mb-3">Status data utama yang tersimpan di database.</div>
                        <div class="timeline-item">
                            <strong>Absensi</strong>
                            <div class="small-muted">{{ $attendanceCount }} data absensi tersimpan.</div>
                        </div>
                        <div class="timeline-item">
                            <strong>Penugasan</strong>
                            <div class="small-muted">{{ $assignmentCount }} penugasan tersimpan.</div>
                        </div>
                        <div class="timeline-item mb-0">
                            <strong>Riwayat</strong>
                            <div class="small-muted">{{ $activityTotal }} catatan aktivitas dari berbagai sumber.</div>
                        </div>
                    </section>

                    <section class="soft-card p-3 p-lg-4">
                        <h5 class="fw-bold mb-3">Aktivitas Terkini</h5>
                        @forelse($timelineRows->take(3) as $row)
                            <div class="timeline-item">
                                <strong>{{ $row['judul'] }}</strong>
                                <div class="small-muted">{{ $row['tanggal'] }} - {{ $row['sumber'] }}</div>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada aktivitas yang tersinkron.</div>
                        @endforelse
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
