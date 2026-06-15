<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Laporan Peserta</title>

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
        .soft-card, .stat-card, .table-card, .action-card, .guide-card, .upload-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .input-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
        .guide-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .guide-item:last-child { border-bottom:0; padding-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $reportToEdit = $reportToEdit ?? null;
        $selectedJenis = old('jenis', $selectedJenis ?? $reportToEdit?->jenis ?? 'berkala');
        $pembimbingName = $pembimbingUser?->name
            ?? $pembimbing?->user?->name
            ?? $peserta?->pembimbing_akademik
            ?? 'Pembimbing belum tersedia';
        $pembimbingEmail = $pembimbingUser?->email
            ?? $pembimbing?->user?->email
            ?? '-';
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
        <div class="sidebar-parent active">
            <a href="{{ route('peserta.laporan') }}"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a>
            <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="true" aria-controls="laporanMenu"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="laporanMenu">
            <div class="sidebar-submenu">
                <a href="{{ route('peserta.laporan.input') }}" class="active">Input Laporan</a>
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
                        <h2 class="fw-bold mb-1">Input Laporan</h2>
                        <div class="small-muted">Buat, simpan draft, dan kirim laporan kegiatan magang sesuai ketentuan.</div>
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
                    <li class="breadcrumb-item"><a href="{{ route('peserta.laporan') }}">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Input Laporan</li>
                </ol>
            </nav>

            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                <div>Draft laporan dapat disimpan sebelum dikirim untuk verifikasi mentor dan pembimbing akademik.</div>
            </div>

            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-bold mb-1">Ada data laporan yang belum valid:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="soft-card p-3 p-lg-4 mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-5">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="62" height="62" alt="Foto profil peserta">
                            <div>
                                <h4 class="fw-bold mb-1">Form Laporan Magang</h4>
                                <div class="text-secondary small">{{ $userName }} | UI/UX Intern</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-primary">Periode Aktif: Mei-Juli 2026</span>
                                    <span class="badge bg-info text-dark">Draft Otomatis</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="soft-card p-3 bg-light">
                            <div class="d-flex justify-content-between mb-2"><strong>Kelengkapan Input</strong><span>70%</span></div>
                            <div class="progress"><div class="progress-bar bg-info" style="width:70%"></div></div>
                            <div class="small-muted mt-2">Judul, periode, ringkasan, dan lampiran utama sudah siap dilengkapi.</div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="small-muted mb-2">Ringkasan cepat</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary">12 Laporan</span>
                            <span class="badge bg-success">8 Disetujui</span>
                            <span class="badge bg-warning text-dark">2 Menunggu</span>
                            <span class="badge bg-danger">1 Revisi</span>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row g-4 mb-4">
                <div class="col-12">
                    <section class="soft-card p-3 p-lg-4 h-100">
                        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 align-items-lg-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Form Input Laporan</h5>
                                <div class="small-muted">Lengkapi data laporan, lampiran, dan ringkasan kegiatan sebelum dikirim.</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-info text-dark">Autosave aktif</span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('peserta.laporan.input.store') }}" enctype="multipart/form-data" id="reportInputForm">
                            @csrf
                            <input type="hidden" name="report_id" value="{{ old('report_id', $reportToEdit?->id) }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Judul Laporan</label>
                                    <input type="text" class="form-control" name="judul" value="{{ old('judul', $reportToEdit?->judul ?? 'Laporan Mingguan 9') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Laporan</label>
                                    <select class="form-select" name="jenis">
                                        <option value="berkala" @selected($selectedJenis === 'berkala')>Laporan Berkala</option>
                                        <option value="akhir" @selected($selectedJenis === 'akhir')>Laporan Akhir</option>
                                    </select>
                                    <div class="form-text">Pilihan ini menentukan apakah laporan masuk sebagai berkala atau laporan akhir di database.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Periode Kegiatan</label>
                                    <input type="text" class="form-control" name="periode" value="{{ old('periode', $reportToEdit?->periode ?? '27 Mei-2 Juni 2026') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Durasi Jam Logbook</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="durasi_jam" min="1" max="24" step="1" value="{{ old('durasi_jam', $reportToEdit?->durasi_jam ?? '') }}" placeholder="Contoh: 3">
                                        <span class="input-group-text">jam</span>
                                    </div>
                                    <div class="form-text">Opsional, isi hanya jika diperlukan.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pembimbing/Penilai</label>
                                    <input type="text" class="form-control" value="{{ $pembimbingName }}" readonly>
                                    <div class="form-text">Terhubung otomatis dengan pembimbing akademik yang tersimpan di database.</div>
                                    <input type="hidden" name="pembimbing_nama" value="{{ $pembimbingName }}">
                                    <input type="hidden" name="pembimbing_email" value="{{ $pembimbingEmail }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Ringkasan Kegiatan</label>
                                    <textarea class="form-control" name="catatan" rows="4">{{ old('catatan', $reportToEdit?->catatan ?? 'Menyusun rancangan wireframe dashboard peserta, memperbarui komponen tabel laporan, dan mendokumentasikan hasil diskusi dengan mentor.') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Capaian</label>
                                    <textarea class="form-control" rows="2">Wireframe halaman laporan selesai 80% dan siap direview mentor.</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kendala dan Tindak Lanjut</label>
                                    <textarea class="form-control" rows="2">Menunggu validasi format laporan akhir. Tindak lanjut: koordinasi dengan pembimbing akademik.</textarea>
                                </div>
                                <div class="col-12">
                                    <div class="upload-card p-3">
                                        <label class="form-label fw-bold">Upload Lampiran</label>
                                        <input class="form-control mb-2" type="file" name="file" {{ $reportToEdit ? '' : 'required' }}>
                                        <div class="small-muted">Format disarankan PDF, DOCX, JPG, atau PNG. Ukuran maksimal 10 MB per file.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <button class="btn btn-outline-secondary" type="button">Batal</button>
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#previewModal"><i class="bi bi-eye me-1"></i> Preview Laporan</button>
                                <button class="btn btn-info text-dark" type="button" data-bs-toggle="modal" data-bs-target="#confirmModal"><i class="bi bi-save me-1"></i> Simpan Draft</button>
                                <button class="btn btn-primary" type="submit" name="submission_action" value="submit"><i class="bi bi-send me-1"></i> Kirim Laporan</button>
                            </div>
                        </form>
                    </section>
                </div>

            </div>

        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="previewLabel">Preview Laporan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Preview menampilkan judul, periode, ringkasan kegiatan, capaian, kendala, lampiran, serta status kelengkapan laporan.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="toast" data-bs-target="#inputToast">Siap Diproses</button></div></div></div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="confirmLabel">Konfirmasi Laporan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body">Pastikan data laporan, periode, ringkasan, dan lampiran sudah benar sebelum disimpan atau dikirim.</div><div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-info text-dark" form="reportInputForm" name="submission_action" value="draft">Simpan Draft</button><button type="submit" class="btn btn-primary" form="reportInputForm" name="submission_action" value="submit">Kirim Laporan</button></div></div></div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="inputToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Input Laporan</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Tindakan laporan berhasil diproses dan histori aktivitas diperbarui.</div>
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

        // Tombol submit memakai mekanisme HTML form native agar lebih stabil.
    </script>
</body>
</html>
