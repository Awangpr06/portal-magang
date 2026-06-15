<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Dokumen Kerja Sama</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .summary-card, .priority-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .status-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .priority-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .priority-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $documentCollection = isset($kerjasamaDocuments) ? collect($kerjasamaDocuments) : collect();

        $documentRows = $documentCollection->values()->map(function ($document, $index) {
            $statusKey = strtolower((string) ($document->status ?? ''));
            $statusMeta = match ($statusKey) {
                'disetujui', 'approved', 'terverifikasi', 'valid', 'verified' => ['label' => 'Terverifikasi', 'class' => 'success'],
                'menunggu', 'pending' => ['label' => 'Menunggu', 'class' => 'warning text-dark'],
                'revisi', 'rejected', 'ditolak' => ['label' => 'Perlu Revisi', 'class' => 'danger'],
                default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Draft', 'class' => 'secondary'],
            };

            return [
                'no' => $index + 1,
                'nama' => $document->nama_dokumen ?: '-',
                'jenis' => strtoupper((string) ($document->jenis_file ?: $document->jenis_dokumen ?: '-')),
                'tanggal' => optional($document->created_at)->translatedFormat('d M Y') ?? '-',
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
                'catatan' => $document->catatan ?: 'Tidak ada catatan.',
                'status_key' => $statusKey,
            ];
        });

        $documentTotal = $documentRows->count();
        $documentApproved = $documentCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['disetujui', 'approved', 'terverifikasi', 'valid', 'verified'], true))->count();
        $documentWaiting = $documentCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['menunggu', 'pending'], true))->count();
        $documentRevision = $documentCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['revisi', 'rejected', 'ditolak'], true))->count();
        $documentRejected = $documentCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['rejected', 'ditolak'], true))->count();
        $documentProgress = $documentTotal > 0 ? (int) round(($documentApproved / $documentTotal) * 100) : 0;
        $priorityDocuments = $documentRows->filter(fn ($item) => in_array($item['status_key'], ['menunggu', 'pending', 'revisi', 'rejected', 'ditolak'], true))->values();
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.dokumen') }}"><i class="bi bi-folder2-open"></i> Dokumen</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dokumenMenu" aria-expanded="true" aria-controls="dokumenMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="dokumenMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.dokumen.kerjasama') }}">Dokumen Kerjasama</a>
                <a href="{{ route('peserta.dokumen.pendukung') }}">Dokumen Pendukung</a>
                <a href="{{ route('peserta.dokumen.status') }}" class="active">Status Dokumen</a>
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
                        <h2 class="fw-bold mb-1">Status Dokumen Kerja Sama</h2>
        <div class="small-muted">Monitoring status dokumen kerja sama dan MoU yang diverifikasi admin.</div>
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
                    <li class="breadcrumb-item"><a href="{{ route('peserta.dokumen') }}">Dokumen</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Status Dokumen Kerja Sama</li>
                </ol>
            </nav>

            <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>Halaman ini hanya menampilkan dokumen kerja sama atau MoU yang diverifikasi admin. Dokumen pendukung tidak masuk ke status verifikasi.</div>
            </div>

            <section class="status-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">Progres Verifikasi Kerja Sama</h3>
                                <div class="text-secondary">{{ $userName }} | Dokumen kerja sama magang</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">{{ $documentApproved }} Terverifikasi</span>
                                    <span class="badge bg-warning text-dark">{{ $documentWaiting }} Menunggu</span>
                                    <span class="badge bg-danger">{{ $documentRevision }} Revisi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Ringkasan Status Kerja Sama</strong><span>{{ $documentProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-success" style="width:{{ $documentProgress }}%"></div></div>
                            <div class="small-muted mt-2">Dokumen kerja sama tersinkron dari database: {{ $documentTotal }} file.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Dokumen</div><h4 class="fw-bold mb-1">{{ $documentTotal }}</h4><span class="badge bg-primary">Kerja Sama</span></div><span class="stat-icon bg-primary"><i class="bi bi-files"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Terverifikasi</div><h4 class="fw-bold mb-1">{{ $documentApproved }}</h4><span class="badge bg-success">Valid</span></div><span class="stat-icon bg-success"><i class="bi bi-patch-check"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Menunggu</div><h4 class="fw-bold mb-1">{{ $documentWaiting }}</h4><span class="badge bg-warning text-dark">Review</span></div><span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Perlu Revisi / Ditolak</div><h4 class="fw-bold mb-1">{{ $documentRevision + $documentRejected }}</h4><span class="badge bg-danger">Tindak lanjut</span></div><span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#statusModal"><span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Lihat Dokumen</strong><span class="small-muted">Buka detail status</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#confirmModal"><span class="action-icon bg-danger mb-2"><i class="bi bi-arrow-repeat"></i></span><strong class="d-block">Upload Ulang</strong><span class="small-muted">Perbaiki dokumen</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#noteModal"><span class="action-icon bg-warning mb-2"><i class="bi bi-card-text"></i></span><strong class="d-block">Lihat Catatan</strong><span class="small-muted">Catatan verifikator</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#statusToast"><span class="action-icon bg-secondary mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Unduh Rekap</strong><span class="small-muted">Rekap status</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                            <div>
                                <h5 class="fw-bold mb-1">Status Dokumen Kerja Sama</h5>
                                <div class="small-muted">Monitoring verifikasi, catatan perbaikan, dan tindak lanjut dokumen kerja sama.</div>
                            </div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#documentStatusPanel" aria-controls="documentStatusPanel">
                                <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                            </button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari nama dokumen"></div>
                                </div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Jenis</label><select class="form-select"><option>Semua</option><option>MoU</option><option>PKS</option><option>Addendum</option><option>Surat Kerja Sama</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Terverifikasi</option><option>Menunggu</option><option>Revisi</option><option>Ditolak</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Periode</label><input type="date" class="form-control" value="2026-05-29"></div>
                                <div class="col-lg-1"><button class="btn btn-primary" type="button" data-bs-toggle="toast" data-bs-target="#statusToast">Filter</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Nama Dokumen</th><th>Jenis Dokumen</th><th>Tanggal Unggah</th><th>Status Verifikasi</th><th>Catatan Verifikator</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($documentRows as $document)
                                            <tr>
                                                <td>{{ $document['no'] }}</td>
                                                <td><strong>{{ $document['nama'] }}</strong></td>
                                                <td>{{ $document['jenis'] }}</td>
                                                <td>{{ $document['tanggal'] }}</td>
                                                <td><span class="badge bg-{{ $document['status_class'] }}">{{ $document['status_label'] }}</span></td>
                                                <td>{{ $document['catatan'] }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#statusModal">Lihat</button>
                                                        <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#historyModal">Riwayat</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="small-muted">Belum ada data dokumen kerja sama dari database.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $documentRows->count() ? '1-' . min(4, $documentRows->count()) : '0' }} dari {{ $documentRows->count() }} status dokumen kerja sama</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select></div>
                            <nav aria-label="Pagination status dokumen"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">3</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="statusLabel">Detail Status Dokumen Kerja Sama</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail status memuat dokumen kerja sama, histori verifikasi, status terbaru, catatan verifikator, dan tindak lanjut yang disarankan.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#statusToast">Lanjutkan</button></div></div></div>
    </div>
    <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="noteLabel">Catatan Verifikator</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Catatan terbaru: pastikan dokumen terbaca jelas, sesuai format, dan memuat tanda tangan atau stempel jika diwajibkan.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div>
    </div>
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="historyLabel">Riwayat Verifikasi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Riwayat berisi perubahan status, nama verifikator, catatan perbaikan, dan waktu pembaruan dokumen.</div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Upload Ulang</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Dokumen kerja sama revisi akan menggantikan file sebelumnya dan status berubah menjadi menunggu verifikasi.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-danger" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#statusToast">Upload Ulang</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="statusToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Notifikasi Sistem</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan status dokumen kerja sama berhasil diproses. Riwayat verifikasi dan dashboard diperbarui.</div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="documentStatusPanel" aria-labelledby="documentStatusPanelLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="documentStatusPanelLabel">Panel Ringkasan Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <section class="summary-card p-3 p-lg-4 mb-4">
                <h5 class="fw-bold mb-1">Panel Ringkasan Status</h5>
                <div class="small-muted mb-3">Distribusi status dokumen kerja sama peserta.</div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Terverifikasi</strong><span>{{ $documentTotal > 0 ? (int) round(($documentApproved / $documentTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-success" style="width:{{ $documentTotal > 0 ? (int) round(($documentApproved / $documentTotal) * 100) : 0 }}%"></div></div></div>
                <div class="mb-3"><div class="d-flex justify-content-between"><strong>Menunggu</strong><span>{{ $documentTotal > 0 ? (int) round(($documentWaiting / $documentTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-warning" style="width:{{ $documentTotal > 0 ? (int) round(($documentWaiting / $documentTotal) * 100) : 0 }}%"></div></div></div>
                <div><div class="d-flex justify-content-between"><strong>Revisi/Ditolak</strong><span>{{ $documentTotal > 0 ? (int) round((($documentRevision + $documentRejected) / $documentTotal) * 100) : 0 }}%</span></div><div class="progress mt-2"><div class="progress-bar bg-danger" style="width:{{ $documentTotal > 0 ? (int) round((($documentRevision + $documentRejected) / $documentTotal) * 100) : 0 }}%"></div></div></div>
            </section>
            <section class="priority-card p-3 p-lg-4">
                <h5 class="fw-bold mb-1">Dokumen Prioritas</h5>
        <div class="small-muted mb-2">Dokumen kerja sama yang perlu tindak lanjut.</div>
                @forelse ($priorityDocuments->take(3) as $document)
                    <div class="priority-item">
                        <strong>{{ $document['nama'] }}</strong>
                        <div class="small-muted">{{ $document['catatan'] }}</div>
                        <span class="badge {{ $document['status_class'] === 'warning text-dark' ? 'bg-warning text-dark' : 'bg-' . $document['status_class'] }} mt-1">{{ $document['status_label'] }}</span>
                    </div>
                @empty
                    <div class="priority-item">
                        <strong>Semua dokumen aman</strong>
                        <div class="small-muted">Tidak ada dokumen yang membutuhkan tindak lanjut saat ini.</div>
                        <span class="badge bg-success mt-1">Lancar</span>
                    </div>
                @endforelse
            </section>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelectorAll('[data-bs-target="#statusToast"]').forEach((button) => {
            button.addEventListener('click', () => {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('statusToast')).show();
            });
        });
    </script>
</body>
</html>
