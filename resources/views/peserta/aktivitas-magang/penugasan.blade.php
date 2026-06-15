<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas Magang - Penugasan</title>

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
        .assignment-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .deadline-item { border-left:4px solid var(--brand); padding:12px 12px 12px 14px; background:#f8fcfe; border-radius:0 8px 8px 0; }
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
        $mentorUser = $context['mentorUser'] ?? ($mentorUser ?? null);
        $internship = $context['internship'] ?? ($internship ?? null);
        $notificationCount = $stats['notification_unread'] ?? 0;
        $messageCount = $stats['message_unread'] ?? 0;
        $userName = $user?->name ?? 'Peserta Magang';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $assignmentRows = collect($assignmentRows ?? [])->values();
        $assignmentTotal = $assignmentRows->count();
        $assignmentDone = $assignmentRows->filter(fn ($item) => in_array(strtolower((string) ($item['status_label'] ?? '')), ['selesai', 'done', 'completed', 'disetujui'], true))->count();
        $assignmentWorking = $assignmentRows->filter(fn ($item) => in_array(strtolower((string) ($item['status_label'] ?? '')), ['dikerjakan', 'review', 'finalisasi'], true))->count();
        $assignmentLate = $assignmentRows->filter(fn ($item) => in_array(strtolower((string) ($item['status_label'] ?? '')), ['terlambat', 'late'], true))->count();
        $assignmentProgress = $assignmentTotal > 0 ? (int) round(($assignmentDone / $assignmentTotal) * 100) : 0;
        $priorityHigh = $assignmentRows->filter(fn ($item) => in_array(strtolower((string) ($item['prioritas_label'] ?? '')), ['tinggi', 'high'], true))->count();
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
                <a href="{{ route('peserta.aktivitas-magang.penugasan') }}" class="active">Penugasan</a>
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
                        <h2 class="fw-bold mb-1">Penugasan</h2>
                        <div class="small-muted">Kelola tugas, progress pengerjaan, hasil pekerjaan, feedback mentor, dan deadline.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Penugasan</li>
                </ol>
            </nav>

            <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    @if ($priorityHigh > 0)
                        Ada {{ $priorityHigh }} penugasan prioritas tinggi yang mendekati deadline. Perbarui progress atau unggah hasil pekerjaan.
                    @else
                        Tidak ada penugasan prioritas tinggi saat ini. Semua data penugasan telah sinkron dengan database.
                    @endif
                </div>
            </div>

            <section class="assignment-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">{{ $peserta?->program_magang ?? 'Program magang belum tersedia' }} | Mentor: {{ $mentorUser?->name ?? 'Mentor belum ditentukan' }} | {{ $internship?->instansi ?? 'Instansi belum tersedia' }}</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">{{ $assignmentDone }} Tugas Selesai</span>
                                    <span class="badge bg-warning text-dark">{{ $assignmentWorking }} Sedang Dikerjakan</span>
                                    <span class="badge bg-danger">{{ $assignmentLate }} Terlambat</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Penyelesaian Tugas</strong><span>{{ $assignmentProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-warning" style="width:{{ $assignmentProgress }}%"></div></div>
                            <div class="small-muted mt-2">Data tugas tersinkron dari database: {{ $assignmentTotal }} penugasan.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Penugasan</div><h4 class="fw-bold mb-1">{{ $assignmentTotal }}</h4><span class="badge bg-primary">Tugas</span></div><span class="stat-icon bg-primary"><i class="bi bi-list-task"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Selesai</div><h4 class="fw-bold mb-1">{{ $assignmentDone }}</h4><span class="badge bg-success">Tuntas</span></div><span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Dikerjakan</div><h4 class="fw-bold mb-1">{{ $assignmentWorking }}</h4><span class="badge bg-info text-dark">Berjalan</span></div><span class="stat-icon bg-info"><i class="bi bi-hourglass-split"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Terlambat</div><h4 class="fw-bold mb-1">{{ $assignmentLate }}</h4><span class="badge bg-danger">Prioritas</span></div><span class="stat-icon bg-danger"><i class="bi bi-alarm"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Penyelesaian</div><h4 class="fw-bold mb-1">{{ $assignmentProgress }}%</h4><div class="progress mt-2"><div class="progress-bar bg-warning" style="width:{{ $assignmentProgress }}%"></div></div></div><span class="stat-icon bg-warning"><i class="bi bi-graph-up-arrow"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" type="button" id="openFirstAssignmentButton"><span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Detail Tugas</strong><span class="small-muted">Baca instruksi</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" type="button" id="openUploadAssignmentButton"><span class="action-icon bg-success mb-2"><i class="bi bi-cloud-upload"></i></span><strong class="d-block">Unggah Hasil</strong><span class="small-muted">Kirim pekerjaan</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#progressModal"><span class="action-icon bg-warning mb-2"><i class="bi bi-sliders"></i></span><strong class="d-block">Update Progress</strong><span class="small-muted">Perbarui persen</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#historyModal"><span class="action-icon bg-info mb-2"><i class="bi bi-clock-history"></i></span><strong class="d-block">Lihat Riwayat</strong><span class="small-muted">Feedback mentor</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#recapModal"><span class="action-icon bg-secondary mb-2"><i class="bi bi-file-earmark-text"></i></span><strong class="d-block">Rekap</strong><span class="small-muted">Ringkasan tugas</span></button></div>
                    <div class="col-md-6 col-xl-2"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#assignmentToast"><span class="action-icon bg-danger mb-2"><i class="bi bi-filetype-pdf"></i></span><strong class="d-block">Unduh PDF</strong><span class="small-muted">Ekspor data</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Daftar Penugasan</h5>
                                <div class="small-muted">Pantau tugas, progress, dan hasil pekerjaan peserta.</div>
                            </div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#assignmentInfoPanel" aria-controls="assignmentInfoPanel">
                                <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                            </button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari judul atau deskripsi tugas"></div>
                                </div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Selesai</option><option>Dikerjakan</option><option>Terlambat</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Prioritas</label><select class="form-select"><option>Semua</option><option>Tinggi</option><option>Sedang</option><option>Rendah</option></select></div>
                                <div class="col-md-4 col-lg-2"><label class="form-label">Periode</label><input type="date" class="form-control" value="2026-05-28"></div>
                                <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-fill" type="button" data-bs-toggle="toast" data-bs-target="#assignmentToast">Filter</button><button class="btn btn-outline-secondary" type="reset">Reset</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Judul Penugasan</th><th>Deskripsi Singkat</th><th>Prioritas</th><th>Pemberi Tugas</th><th>Deadline</th><th>Status</th><th>Progress</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($assignmentRows as $assignment)
                                            <tr>
                                                <td>{{ $assignment['no'] }}</td>
                                                <td><strong>{{ $assignment['judul'] }}</strong></td>
                                                <td>{{ $assignment['deskripsi'] }}</td>
                                                <td><span class="badge bg-{{ $assignment['prioritas_class'] }}">{{ $assignment['prioritas_label'] }}</span></td>
                                                <td>{{ $assignment['pemberi'] }}</td>
                                                <td>{{ $assignment['deadline'] }}</td>
                                                <td><span class="badge bg-{{ $assignment['status_class'] }}">{{ $assignment['status_label'] }}</span></td>
                                                <td style="min-width:130px;">
                                                    <div class="d-flex justify-content-between small"><span>{{ $assignment['progress'] }}%</span><span>Proses</span></div>
                                                    <div class="progress"><div class="progress-bar bg-warning" style="width:{{ $assignment['progress'] }}%"></div></div>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary assignment-detail-button" type="button" data-bs-toggle="modal" data-bs-target="#assignmentModal" data-assignment-index="{{ $loop->index }}">Detail</button>
                                                        @if (!empty($assignment['download_url']))
                                                            <a class="btn btn-outline-success" href="{{ $assignment['download_url'] }}">Download</a>
                                                        @endif
                                                        <button class="btn btn-outline-success assignment-upload-button" type="button" data-bs-toggle="modal" data-bs-target="#assignmentUploadModal" data-assignment-index="{{ $loop->index }}">{{ !empty($assignment['submission_url']) ? 'Perbarui Unggah' : 'Unggah' }}</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4">
                                                    <div class="small-muted">Belum ada data penugasan dari database.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $assignmentRows->count() ? '1-' . min(4, $assignmentRows->count()) : '0' }} dari {{ $assignmentRows->count() }} penugasan</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select></div>
                            <nav aria-label="Pagination penugasan"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentLabel">Detail Penugasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="assignmentDetailBody">
                    <div class="alert alert-info mb-0">Pilih salah satu penugasan untuk melihat detail.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="assignmentUploadModal" tabindex="-1" aria-labelledby="assignmentUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="assignmentUploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignmentUploadLabel">Unggah Hasil Penugasan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small-muted mb-3" id="assignmentUploadSummary">Pilih file hasil tugas yang akan dikirim ke mentor.</p>
                        <input type="hidden" name="assignment_id" id="assignmentUploadId">
                        <div class="mb-3">
                            <label class="form-label" for="assignmentUploadFile">File Hasil</label>
                            <input class="form-control" type="file" name="file" id="assignmentUploadFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar" required>
                            <div class="form-text">PDF, DOC, DOCX, JPG, PNG, ZIP, atau RAR. Maksimal 10 MB.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Kirim ke Mentor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="progressLabel">Perbarui Progress</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pastikan progress yang diperbarui sesuai kondisi pengerjaan dan didukung hasil pekerjaan yang valid.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-warning" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#assignmentToast">Perbarui</button></div></div></div>
    </div>
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="historyLabel">Riwayat Penugasan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Riwayat berisi perubahan progress, unggahan hasil, dan feedback mentor pada tugas terkait.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div>
    </div>
    <div class="modal fade" id="recapModal" tabindex="-1" aria-labelledby="recapLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="recapLabel">Rekap Penugasan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Rekap menampilkan jumlah tugas, status penyelesaian, prioritas, progress, dan catatan mentor.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="assignmentToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Notifikasi Sistem</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan penugasan berhasil diproses. Progress, hasil pekerjaan, dan dashboard diperbarui.</div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="assignmentInfoPanel" aria-labelledby="assignmentInfoPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="assignmentInfoPanelLabel">Panel Informasi Penugasan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <section class="info-card p-3 p-lg-4 mb-4">
                <h5 class="fw-bold mb-1">Panel Deadline</h5>
                <div class="small-muted mb-3">Tugas prioritas berdasarkan batas waktu.</div>
                <div class="d-grid gap-3">
                    @forelse ($assignmentRows->sortBy('deadline')->take(3) as $assignment)
                        <div class="deadline-item">
                            <strong>{{ $assignment['judul'] }}</strong>
                            <div class="small-muted">Deadline: {{ $assignment['deadline'] }}</div>
                            <span class="badge bg-{{ $assignment['prioritas_class'] }} mt-2">{{ $assignment['prioritas_label'] }}</span>
                        </div>
                    @empty
                        <div class="deadline-item">
                            <strong>Belum ada penugasan</strong>
                            <div class="small-muted">Data deadline akan muncul setelah tugas tersimpan di database.</div>
                            <span class="badge bg-secondary mt-2">Kosong</span>
                        </div>
                    @endforelse
                </div>
            </section>
            <section class="soft-card p-3 p-lg-4">
                <h5 class="fw-bold mb-3">Informasi Penugasan</h5>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Unggah Hasil</strong><span>{{ $assignmentTotal > 0 ? (int) round(($assignmentDone / $assignmentTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-warning" style="width:{{ $assignmentTotal > 0 ? (int) round(($assignmentDone / $assignmentTotal) * 100) : 0 }}%"></div></div></div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Feedback Mentor</strong><span>{{ $assignmentTotal > 0 ? (int) round(($assignmentWorking / $assignmentTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-info" style="width:{{ $assignmentTotal > 0 ? (int) round(($assignmentWorking / $assignmentTotal) * 100) : 0 }}%"></div></div></div>
                <div><div class="d-flex justify-content-between"><strong>Target Selesai</strong><span>{{ $assignmentTotal > 0 ? (int) round((($assignmentDone + $assignmentWorking) / $assignmentTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $assignmentTotal > 0 ? (int) round((($assignmentDone + $assignmentWorking) / $assignmentTotal) * 100) : 0 }}%"></div></div></div>
            </section>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        const assignmentRows = @json($assignmentRows->values());
        const assignmentUploadBaseUrl = @json(url('/peserta/aktivitas-magang/penugasan'));
        const assignmentDetailBody = document.getElementById('assignmentDetailBody');
        const assignmentUploadForm = document.getElementById('assignmentUploadForm');
        const assignmentUploadId = document.getElementById('assignmentUploadId');
        const assignmentUploadFile = document.getElementById('assignmentUploadFile');
        const assignmentUploadSummary = document.getElementById('assignmentUploadSummary');
        const assignmentModalEl = document.getElementById('assignmentModal');
        const assignmentModal = bootstrap.Modal.getOrCreateInstance(assignmentModalEl);
        const assignmentUploadModalEl = document.getElementById('assignmentUploadModal');
        const assignmentUploadModal = bootstrap.Modal.getOrCreateInstance(assignmentUploadModalEl);

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function openAssignmentDetail(index) {
            const item = assignmentRows[index];
            if (!item) {
                assignmentDetailBody.innerHTML = '<div class="alert alert-info mb-0">Belum ada tugas untuk ditampilkan.</div>';
                assignmentModal.show();
                return;
            }

            assignmentDetailBody.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Judul Tugas</small>
                            <h5 class="mb-2">${escapeHtml(item.judul)}</h5>
                            <span class="badge bg-${item.prioritas_class}">${escapeHtml(item.prioritas_label)}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">Status</small>
                            <div class="mb-2"><span class="badge bg-${item.status_class}">${escapeHtml(item.status_label)}</span></div>
                            <div><strong>Pemberi:</strong> ${escapeHtml(item.pemberi)}</div>
                            <div><strong>Deadline:</strong> ${escapeHtml(item.deadline)}</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <small class="text-muted">Deskripsi Tugas</small>
                            <p class="mb-0">${escapeHtml(item.deskripsi)}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Progress</small>
                            <h5 class="mb-0">${escapeHtml(item.progress)}%</h5>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Dibuat</small>
                            <h6 class="mb-0">${escapeHtml(item.created_at_label ?? '-')}</h6>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Diperbarui</small>
                            <h6 class="mb-0">${escapeHtml(item.updated_at_label ?? '-')}</h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted">File Hasil Peserta</small>
                            <h6 class="mb-1">${escapeHtml(item.submission_name ?? '-')}</h6>
                            ${item.submission_url ? `<a class="btn btn-sm btn-outline-success" href="${escapeHtml(item.submission_url)}">Download Hasil</a><div class="small text-muted mt-2">Dikirim ${escapeHtml(item.submission_uploaded_at ?? '-')}</div>` : '<span class="text-muted">Belum ada hasil yang diunggah peserta.</span>'}
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="border rounded p-3">
                            <small class="text-muted">Catatan Mentor</small>
                            <p class="mb-0">${escapeHtml(item.catatan ?? '-')}</p>
                        </div>
                    </div>
                </div>
            `;

            assignmentModal.show();
        }

        function openAssignmentUpload(index) {
            const item = assignmentRows[index];
            if (!item) {
                return;
            }

            assignmentUploadForm.action = `${assignmentUploadBaseUrl}/${item.id}/unggah`;
            assignmentUploadId.value = item.id;
            assignmentUploadFile.value = '';
            assignmentUploadSummary.textContent = `Unggah hasil untuk "${item.judul}" kepada ${item.pemberi}.`;
            assignmentUploadModal.show();
        }

        document.querySelectorAll('[data-bs-target="#assignmentToast"]').forEach((button) => {
            button.addEventListener('click', () => {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('assignmentToast')).show();
            });
        });

        document.querySelectorAll('.assignment-detail-button').forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.assignmentIndex || 0);
                openAssignmentDetail(index);
            });
        });

        document.querySelectorAll('.assignment-upload-button').forEach((button) => {
            button.addEventListener('click', () => {
                const index = Number(button.dataset.assignmentIndex || 0);
                openAssignmentUpload(index);
            });
        });

        const openFirstAssignmentButton = document.getElementById('openFirstAssignmentButton');
        if (openFirstAssignmentButton) {
            openFirstAssignmentButton.addEventListener('click', () => openAssignmentDetail(0));
        }

        const openUploadAssignmentButton = document.getElementById('openUploadAssignmentButton');
        if (openUploadAssignmentButton) {
            openUploadAssignmentButton.addEventListener('click', () => {
                if (!assignmentRows.length) {
                    bootstrap.Toast.getOrCreateInstance(document.getElementById('assignmentToast')).show();
                    return;
                }
                openAssignmentUpload(0);
            });
        }
    </script>
</body>
</html>
