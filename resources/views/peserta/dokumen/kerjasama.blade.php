<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Kerja Sama</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .recent-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .cooperation-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .recent-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .recent-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $user = $user ?? auth()->user();
        $userName = $user?->name ?? 'Peserta';
        $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $kerjasamaCollection = collect($kerjasamaDocuments ?? []);
        if ($kerjasamaCollection->isEmpty() && $user?->documents) {
            $kerjasamaCollection = $user->documents()
                ->where('kategori', 'Dokumen Kerja Sama')
                ->latest()
                ->get();
        }
        $kerjasamaRows = $kerjasamaCollection->values()->map(function ($document, $index) {
            $statusKey = strtolower((string) ($document->status ?? 'menunggu'));
            $statusMeta = match ($statusKey) {
                'disetujui', 'approved', 'aktif' => ['label' => 'Disetujui', 'class' => 'success'],
                'ditolak', 'rejected', 'nonaktif' => ['label' => 'Ditolak', 'class' => 'danger'],
                'revisi' => ['label' => 'Revisi', 'class' => 'warning text-dark'],
                default => ['label' => 'Menunggu Validasi', 'class' => 'warning text-dark'],
            };

            return [
                'no' => $index + 1,
                'nama' => $document->nama_dokumen ?: '-',
                'jenis' => strtoupper((string) ($document->jenis_dokumen ?? '-')),
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
                'uploaded_at' => optional($document->created_at)->translatedFormat('d M Y') ?? '-',
                'uploaded_time' => optional($document->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'size' => $document->ukuran_file ? number_format($document->ukuran_file / 1024, 1) . ' KB' : '-',
                'catatan' => $document->catatan ?: 'Menunggu validasi admin.',
                'status_key' => $statusKey,
                'file' => $document->file,
                'url' => $document->file ? asset('storage/'.$document->file) : null,
            ];
        });

        $kerjasamaTotal = $kerjasamaRows->count();
        $kerjasamaApproved = $kerjasamaCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['disetujui', 'approved', 'aktif'], true))->count();
        $kerjasamaPending = $kerjasamaCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['menunggu', 'pending'], true))->count();
        $kerjasamaRejected = $kerjasamaCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['ditolak', 'rejected', 'nonaktif'], true))->count();
        $kerjasamaRevision = $kerjasamaCollection->filter(fn ($item) => in_array(strtolower((string) ($item->status ?? '')), ['revisi'], true))->count();
        $kerjasamaProgress = $kerjasamaTotal > 0 ? (int) round(($kerjasamaApproved / $kerjasamaTotal) * 100) : 0;
        $latestKerjasama = $kerjasamaRows->first();
        $pendingKerjasama = $kerjasamaRows->first(fn ($item) => $item['status_key'] === 'menunggu' || $item['status_key'] === 'pending');
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
                <a href="{{ route('peserta.dokumen.kerjasama') }}" class="active">Dokumen Kerjasama</a>
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
                        <h2 class="fw-bold mb-1">Dokumen Kerja Sama</h2>
                        <div class="small-muted">MoU, PKS, addendum, surat kerja sama, dan dokumen mitra magang.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Dokumen Kerja Sama</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Dokumen kerja sama ditampilkan sesuai hak akses peserta dan relasi penempatan magang aktif.</div>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-semibold mb-1">Upload gagal diproses.</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="cooperation-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">Kerja Sama Magang Aktif</h3>
                                <div class="text-secondary">{{ $userName }} | PT Solusi Teknologi | UI/UX Intern</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">MoU Aktif</span>
                                    <span class="badge bg-info text-dark">PKS Terverifikasi</span>
                                    <span class="badge bg-warning text-dark">1 Addendum Proses</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Ringkasan Penyimpanan</strong><span>{{ $kerjasamaProgress }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-info" style="width:{{ $kerjasamaProgress }}%"></div></div>
                            <div class="small-muted mt-2">{{ $kerjasamaTotal }} dokumen kerja sama tersimpan di database.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Dokumen</div><h4 class="fw-bold mb-1">{{ $kerjasamaTotal }}</h4><span class="badge bg-primary">Kerja Sama</span></div><span class="stat-icon bg-primary"><i class="bi bi-files"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Dokumen Disetujui</div><h4 class="fw-bold mb-1">{{ $kerjasamaApproved }}</h4><span class="badge bg-success">Berlaku</span></div><span class="stat-icon bg-success"><i class="bi bi-patch-check"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Dalam Proses</div><h4 class="fw-bold mb-1">{{ $kerjasamaPending + $kerjasamaRevision }}</h4><span class="badge bg-warning text-dark">Review</span></div><span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Selesai/Nonaktif</div><h4 class="fw-bold mb-1">{{ $kerjasamaRejected }}</h4><span class="badge bg-secondary">Arsip</span></div><span class="stat-icon bg-secondary"><i class="bi bi-archive"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Penyimpanan</div><h4 class="fw-bold mb-1">{{ $kerjasamaProgress }}%</h4><div class="progress mt-2"><div class="progress-bar bg-info" style="width:{{ $kerjasamaProgress }}%"></div></div></div><span class="stat-icon bg-info"><i class="bi bi-hdd"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="soft-card p-3 p-lg-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">Upload Dokumen Kerja Sama</h5>
                            <div class="small-muted">Klik kartu upload untuk membuka form pengiriman MoU, PKS, addendum, atau surat kerja sama.</div>
                        </div>
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#uploadKerjasamaModal">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Buka Form Upload
                        </button>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#cooperationModal"><span class="action-icon bg-primary mb-2"><i class="bi bi-eye"></i></span><strong class="d-block">Lihat Dokumen</strong><span class="small-muted">Buka detail file</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="toast" data-bs-target="#cooperationToast"><span class="action-icon bg-success mb-2"><i class="bi bi-download"></i></span><strong class="d-block">Unduh Dokumen</strong><span class="small-muted">Simpan file</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#uploadKerjasamaModal"><span class="action-icon bg-warning mb-2"><i class="bi bi-cloud-upload"></i></span><strong class="d-block">Upload Dokumen</strong><span class="small-muted">Tambah lampiran</span></button></div>
                    <div class="col-md-6 col-xl-3"><button class="action-card w-100 p-3 border-0 text-start" data-bs-toggle="modal" data-bs-target="#folderModal"><span class="action-icon bg-secondary mb-2"><i class="bi bi-folder-plus"></i></span><strong class="d-block">Buat Folder</strong><span class="small-muted">Kelompokkan arsip</span></button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#documentSidePanel" aria-controls="documentSidePanel"><i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel</button>
                        </div>
                        <form class="filter-card p-3 mb-4">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-3">
                                    <label class="form-label">Pencarian</label>
                                    <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari nama dokumen"></div>
                                </div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Jenis Dokumen</label><select class="form-select"><option>Semua</option><option>MoU</option><option>PKS</option><option>Addendum</option><option>Surat Kerja Sama</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Aktif</option><option>Proses</option><option>Nonaktif</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Mitra</label><select class="form-select"><option>Semua</option><option>PT Solusi Teknologi</option><option>LLDIKTI Wilayah V</option></select></div>
                                <div class="col-md-3 col-lg-2"><label class="form-label">Periode</label><input type="date" class="form-control" value="2026-05-28"></div>
                                <div class="col-lg-1"><button class="btn btn-primary" type="button" data-bs-toggle="toast" data-bs-target="#cooperationToast">Filter</button></div>
                            </div>
                        </form>

                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Nama Dokumen</th><th>Mitra Kerja Sama</th><th>Jenis Dokumen</th><th>Status</th><th>Tanggal Unggah</th><th>Ukuran File</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse ($kerjasamaRows as $document)
                                            <tr>
                                                <td>{{ $document['no'] }}</td>
                                                <td><strong>{{ $document['nama'] }}</strong><div class="small-muted">{{ $document['catatan'] }}</div></td>
                                                <td>{{ $internship?->instansi ?? $peserta?->perguruanTinggi?->nama_pt ?? '-' }}</td>
                                                <td>{{ $document['jenis'] }}</td>
                                                <td><span class="badge bg-{{ $document['status_class'] }}">{{ $document['status_label'] }}</span></td>
                                                <td>{{ $document['uploaded_at'] }}</td>
                                                <td>{{ $document['size'] }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($document['url'])
                                                            <button
                                                                type="button"
                                                                class="btn btn-outline-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#kerjasamaPreviewModal"
                                                                data-document-title="{{ $document['nama'] }}"
                                                                data-document-url="{{ $document['url'] }}"
                                                            >
                                                                Lihat
                                                            </button>
                                                            <a class="btn btn-outline-success" href="{{ $document['url'] }}" download>Download</a>
                                                        @else
                                                            <button class="btn btn-outline-secondary" type="button" disabled>Lihat</button>
                                                            <button class="btn btn-outline-secondary" type="button" disabled>Download</button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="small-muted">Belum ada dokumen kerja sama yang diunggah.</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan {{ $kerjasamaRows->count() ? '1-' . min(4, $kerjasamaRows->count()) : '0' }} dari {{ $kerjasamaRows->count() }} dokumen kerja sama</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option><option>50</option></select></div>
                            <nav aria-label="Pagination dokumen kerja sama"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>

                <div class="offcanvas offcanvas-end" tabindex="-1" id="documentSidePanel" aria-labelledby="documentSidePanelLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="documentSidePanelLabel">Panel Dokumen Kerja Sama</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
                    </div>
                    <div class="offcanvas-body">
                        <section class="recent-card p-3 p-lg-4">
                            <h5 class="fw-bold mb-1">Aktivitas Terbaru</h5>
                            <div class="small-muted mb-2">Riwayat akses dan perubahan dokumen kerja sama.</div>
                            @forelse ($kerjasamaRows->take(3) as $document)
                                <div class="recent-item">
                                    <strong>{{ $document['nama'] }}</strong>
                                    <div class="small-muted">{{ $document['uploaded_time'] }}</div>
                                    <span class="badge bg-{{ $document['status_class'] }} mt-1">{{ $document['status_label'] }}</span>
                                </div>
                            @empty
                                <div class="recent-item">
                                    <strong>Belum ada aktivitas</strong>
                                    <div class="small-muted">Dokumen kerja sama yang diunggah akan muncul di sini.</div>
                                </div>
                            @endforelse
                        </section>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="kerjasamaPreviewModal" tabindex="-1" aria-labelledby="kerjasamaPreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="kerjasamaPreviewLabel">Preview Dokumen Kerja Sama</h5>
                        <div class="small-muted" id="kerjasamaPreviewSubtitle">Memuat file...</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9" style="min-height:70vh;">
                        <iframe id="kerjasamaPreviewFrame" src="" title="Preview dokumen kerja sama" style="border:0;"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="kerjasamaPreviewDownload" href="#" class="btn btn-outline-success" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cooperationModal" tabindex="-1" aria-labelledby="cooperationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="cooperationLabel">Detail Dokumen Kerja Sama</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail memuat metadata dokumen, mitra kerja sama, masa berlaku, status, ukuran file, dan riwayat akses dokumen.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#cooperationToast">Lanjutkan</button></div></div></div>
    </div>
    <div class="modal fade" id="folderModal" tabindex="-1" aria-labelledby="folderLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="folderLabel">Buat Folder Kerja Sama</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Folder baru akan membantu mengelompokkan MoU, PKS, addendum, dan surat kerja sama berdasarkan mitra.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-warning" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#cooperationToast">Buat Folder</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Tindakan File</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pastikan tindakan pada dokumen kerja sama sudah benar. Sistem akan memproses file dan mencatat aktivitas dokumen.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#cooperationToast">Lanjutkan</button></div></div></div>
    </div>
    <div class="modal fade" id="uploadKerjasamaModal" tabindex="-1" aria-labelledby="uploadKerjasamaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('peserta.dokumen.kerjasama.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadKerjasamaLabel">Upload Dokumen Kerja Sama</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis_dokumen_modal" class="form-label">Jenis Dokumen</label>
                            <select class="form-select" id="jenis_dokumen_modal" name="jenis_dokumen" required>
                                <option value="mou">MoU</option>
                                <option value="pks">PKS</option>
                                <option value="addendum">Addendum</option>
                                <option value="surat_kerja_sama">Surat Kerja Sama</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="file_modal" class="form-label">File Dokumen</label>
                            <input class="form-control" type="file" id="file_modal" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="alert alert-light border mb-0 small-muted">
                            File akan tersimpan di database dan menunggu validasi admin.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="cooperationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Notifikasi Sistem</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan dokumen kerja sama berhasil diproses. Dashboard dan histori akses diperbarui.</div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelectorAll('[data-bs-target="#cooperationToast"]').forEach((button) => {
            button.addEventListener('click', () => {
                bootstrap.Toast.getOrCreateInstance(document.getElementById('cooperationToast')).show();
            });
        });

        const kerjasamaPreviewModal = document.getElementById('kerjasamaPreviewModal');
        if (kerjasamaPreviewModal) {
            kerjasamaPreviewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const url = button?.getAttribute('data-document-url') || '';
                const title = button?.getAttribute('data-document-title') || 'Preview Dokumen Kerja Sama';

                const titleEl = document.getElementById('kerjasamaPreviewLabel');
                const subtitleEl = document.getElementById('kerjasamaPreviewSubtitle');
                const frameEl = document.getElementById('kerjasamaPreviewFrame');
                const downloadEl = document.getElementById('kerjasamaPreviewDownload');

                if (titleEl) titleEl.textContent = title;
                if (subtitleEl) subtitleEl.textContent = url ? 'Klik download jika ingin menyimpan file ke perangkat.' : 'File tidak tersedia.';
                if (frameEl) frameEl.src = url;
                if (downloadEl) downloadEl.href = url;
            });

            kerjasamaPreviewModal.addEventListener('hidden.bs.modal', function () {
                const titleEl = document.getElementById('kerjasamaPreviewLabel');
                const subtitleEl = document.getElementById('kerjasamaPreviewSubtitle');
                const frameEl = document.getElementById('kerjasamaPreviewFrame');
                const downloadEl = document.getElementById('kerjasamaPreviewDownload');

                if (titleEl) titleEl.textContent = 'Preview Dokumen Kerja Sama';
                if (subtitleEl) subtitleEl.textContent = 'Memuat file...';
                if (frameEl) frameEl.src = '';
                if (downloadEl) downloadEl.href = '#';
            });
        }
    </script>
</body>
</html>
