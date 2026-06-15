<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Peserta</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .announcement-card, .category-card, .recent-card, .action-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .announcement-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card, .announcement-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover, .announcement-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .category-item, .recent-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .category-item:last-child, .recent-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.komunikasi') }}"><i class="bi bi-chat-dots-fill"></i> Komunikasi</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#komunikasiMenu" aria-expanded="true" aria-controls="komunikasiMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="komunikasiMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.komunikasi.pesan') }}">Pesan</a>
                <a href="{{ route('peserta.komunikasi.pengumuman') }}" class="active">Pengumuman</a>
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
                        <h2 class="fw-bold mb-1">Pengumuman</h2>
                        <div class="small-muted">Informasi resmi terkait kegiatan magang, jadwal, tugas, dan administrasi.</div>
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
                    <li class="breadcrumb-item"><a href="{{ route('peserta.komunikasi') }}">Komunikasi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
                </ol>
            </nav>

            <section class="announcement-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-8">
                        <h3 class="fw-bold mb-1">Pusat Pengumuman Magang</h3>
                        <div class="text-secondary">Pantau informasi resmi dari admin, mentor, dan pembimbing akademik.</div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-success">7 pengumuman aktif</span>
                            <span class="badge bg-warning text-dark">2 pengumuman mendatang</span>
                            <span class="badge bg-info text-dark">3 lampiran tersedia</span>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="soft-card p-3">
                            <strong>Pengumuman Terbaru</strong>
                            <div class="small-muted mt-1">Jadwal bimbingan akhir diperbarui pada 29 Mei 2026.</div>
                        </div>
                    </div>
                </div>
            </section>

            @php($announcementItems = collect($announcementData ?? []))
            @php($categorySummary = $announcementItems->groupBy(fn ($item) => $item['kategori'] ?? 'Peserta')->map->count()->sortDesc())

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Pengumuman</div><h4 class="fw-bold mb-1">16</h4><span class="badge bg-primary">Semua</span></div><span class="stat-icon bg-primary"><i class="bi bi-megaphone"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Aktif</div><h4 class="fw-bold mb-1">7</h4><span class="badge bg-success">Dibaca</span></div><span class="stat-icon bg-success"><i class="bi bi-broadcast"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Mendatang</div><h4 class="fw-bold mb-1">2</h4><span class="badge bg-warning text-dark">Jadwal</span></div><span class="stat-icon bg-warning"><i class="bi bi-calendar-event"></i></span></div></div></div>
                    <div class="col-md-6 col-xl-3"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Arsip</div><h4 class="fw-bold mb-1">7</h4><span class="badge bg-secondary">Selesai</span></div><span class="stat-icon bg-secondary"><i class="bi bi-archive"></i></span></div></div></div>
                </div>
            </section>

            <section class="filter-card p-3 mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label class="form-label">Pencarian</label>
                        <div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input type="search" class="form-control" placeholder="Cari judul pengumuman"></div>
                    </div>
                    <div class="col-md-3 col-lg-2"><label class="form-label">Kategori</label><select class="form-select"><option>Semua</option><option>Peserta</option></select></div>
                    <div class="col-md-3 col-lg-2"><label class="form-label">Status</label><select class="form-select"><option>Semua</option><option>Aktif</option><option>Mendatang</option><option>Arsip</option></select></div>
                    <div class="col-md-3 col-lg-2"><label class="form-label">Tanggal</label><input type="date" class="form-control" value="2026-05-29"></div>
                    <div class="col-md-3 col-lg-2 d-flex gap-2"><button class="btn btn-primary w-100" type="button" data-bs-toggle="toast" data-bs-target="#announcementToast">Filter</button><button class="btn btn-outline-secondary" type="reset">Reset</button></div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-xl-8">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Daftar Pengumuman</h5>
                                <div class="small-muted">Informasi terbaru yang perlu diperhatikan peserta.</div>
                            </div>
                            <span class="badge bg-danger align-self-start align-self-lg-center">{{ $announcementItems->where('status', 'aktif')->count() }} baru</span>
                        </div>
                        <div class="row g-3 mb-4">
                            @forelse($announcementItems->take(2) as $announcement)
                                <div class="col-md-6">
                                    <div class="announcement-card p-3 h-100">
                                        <span class="badge bg-warning text-dark mb-2">{{ $announcement['kategori'] ?? 'Peserta' }}</span>
                                        <h6 class="fw-bold">{{ $announcement['judul'] ?? '-' }}</h6>
                                        <p class="small-muted mb-3">{{ $announcement['isi'] ?? '-' }}</p>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal">Lihat Detail</button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="soft-card p-3">
                                        <div class="small-muted">Belum ada pengumuman untuk peserta.</div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                        <div class="table-card">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th>No</th><th>Judul Pengumuman</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th>Ringkasan Isi</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        @forelse($announcementItems as $index => $announcement)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>{{ $announcement['judul'] ?? '-' }}</strong></td>
                                                <td>{{ $announcement['kategori'] ?? '-' }}</td>
                                                <td>{{ $announcement['publikasi'] ?? '-' }}</td>
                                                <td><span class="badge bg-success">{{ ucfirst($announcement['status'] ?? 'draft') }}</span></td>
                                                <td>{{ \Illuminate\Support\Str::limit($announcement['isi'] ?? '-', 60) }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal">Detail</button>
                                                        <button class="btn btn-outline-success" data-bs-toggle="toast" data-bs-target="#announcementToast">Baca</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengumuman.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                            <div class="d-flex flex-wrap align-items-center gap-2"><span class="small-muted">Menampilkan 1-{{ $announcementItems->count() }} dari {{ $announcementItems->count() }} pengumuman</span><select class="form-select form-select-sm" style="width:90px;"><option>10</option><option>25</option></select></div>
                            <nav aria-label="Pagination pengumuman"><ul class="pagination mb-0"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li><li class="page-item active"><a class="page-link" href="#">1</a></li><li class="page-item"><a class="page-link" href="#">2</a></li><li class="page-item"><a class="page-link" href="#">Next</a></li></ul></nav>
                        </div>
                    </section>
                </div>
                <div class="col-xl-4">
                    <section class="category-card p-3 p-lg-4 mb-4">
                        <h5 class="fw-bold mb-1">Kategori Pengumuman</h5>
                        <div class="small-muted mb-2">Pengelompokan informasi berdasarkan tujuan pengumuman.</div>
                        @forelse($categorySummary as $category => $count)
                            <div class="category-item d-flex justify-content-between"><span>{{ $category }}</span><span class="badge bg-primary">{{ $count }}</span></div>
                        @empty
                            <div class="small-muted">Belum ada kategori pengumuman.</div>
                        @endforelse
                    </section>
                    <section class="recent-card p-3 p-lg-4 mb-4">
                        <h5 class="fw-bold mb-1">Pengumuman Terbaru</h5>
                        <div class="recent-item"><strong>Perubahan Jadwal Bimbingan Akhir</strong><div class="small-muted">Admin LLDIKTI, 29 Mei 2026</div><span class="badge bg-success mt-1">Aktif</span></div>
                        <div class="recent-item"><strong>Batas Unggah Laporan Mingguan</strong><div class="small-muted">Mentor, 28 Mei 2026</div><span class="badge bg-info text-dark mt-1">Administrasi</span></div>
                        <div class="recent-item"><strong>Penugasan Presentasi Akhir</strong><div class="small-muted">Pembimbing, 27 Mei 2026</div><span class="badge bg-warning text-dark mt-1">Mendatang</span></div>
                    </section>
                    <section class="soft-card p-3 p-lg-4">
                        <h5 class="fw-bold mb-1">Informasi Tambahan</h5>
                        <div class="small-muted">Pengumuman yang sudah ditandai dibaca tetap tersimpan di arsip dan dapat dibuka kembali melalui filter status.</div>
                    </section>
                </div>
            </div>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="detailLabel">Detail Pengumuman</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Detail pengumuman memuat judul, kategori, tanggal, isi lengkap, lampiran, sumber informasi, dan status baca peserta.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#announcementToast">Tandai Dibaca</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Tindakan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Sistem akan mengunduh lampiran atau menyimpan pengumuman ke daftar informasi tersimpan.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#announcementToast">Lanjutkan</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="announcementToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Pengumuman</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan pengumuman berhasil diproses dan status informasi diperbarui.</div>
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
