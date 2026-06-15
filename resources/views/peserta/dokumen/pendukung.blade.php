<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumen Pendukung</title>

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
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .recent-card, .upload-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .document-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; border-radius:8px; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .table thead th { background:#eef5f8; color:#3b5664; font-size:13px; white-space:nowrap; vertical-align:middle; }
        .table tbody tr:hover { background:#f7fcfe; }
        .progress { height:9px; border-radius:99px; background:#e8eef2; }
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
        $activeDocument = $activeDocument ?? 'Dokumen Pendukung';
        $requiredDocuments = [
            'proposal' => 'Proposal',
            'ktm' => 'KTM',
            'transkip' => 'Transkip',
            'cv' => 'CV',
            'surat_pengantar' => 'Surat Pengantar',
            'sertifikat_pendukung' => 'Sertifikat Pendukung',
        ];
        $documentRows = collect($documents ?? []);
        if ($documentRows->isEmpty() && $user?->documents) {
            $documentRows = $user->documents()
                ->where('kategori', 'Dokumen Pendukung')
                ->latest()
                ->get();
        }
        $documentLookup = [];
        foreach ($documentRows as $document) {
            $key = $document->jenis_dokumen;
            if (! $key) {
                $source = strtolower(trim((string) ($document->nama_dokumen ?? $document->file ?? '')));
                $key = str_contains($source, 'transkrip') ? 'transkip' : (str_contains($source, 'sertifikat') ? 'sertifikat_pendukung' : str_replace('-', '_', str_replace(' ', '_', $source)));
            }
            if (strtolower((string) ($document->kategori ?? '')) === 'dokumen pendukung' || array_key_exists($key, $requiredDocuments)) {
                $documentLookup[$key] = $document;
            }
        }
        $uploadedCount = count(array_filter($documentLookup));
        $completedRows = collect($requiredDocuments)->map(function ($label, $key) use ($documentLookup) {
            return $documentLookup[$key] ?? null;
        })->filter();
        $completionPercent = (int) round(($uploadedCount / max(count($requiredDocuments), 1)) * 100);
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
                <a href="{{ route('peserta.dokumen.pendukung') }}" class="active">Dokumen Pendukung</a>
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
                        <h2 class="fw-bold mb-1">Dokumen Pendukung</h2>
                        <div class="small-muted">Upload dokumen peserta dan simpan sebagai arsip data yang tampil di admin.</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    @include('peserta.partials.topbar-actions')
                    <img src="{{ $avatar }}" class="rounded-circle" width="42" height="42" alt="Foto profil peserta">
                </div>
            </div>
        </header>

        <div class="content-wrap">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('peserta.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('peserta.dokumen') }}">Dokumen</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dokumen Pendukung</li>
                </ol>
            </nav>

            <section class="document-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-7">
                        <div class="d-flex gap-3 align-items-center">
                            <img src="{{ $avatar }}" class="rounded-circle" width="78" height="78" alt="Foto profil peserta">
                            <div>
                                <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                                <div class="text-secondary">Dokumen pendukung yang Anda upload akan otomatis tersimpan sebagai arsip di menu admin.</div>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <span class="badge bg-success">{{ $uploadedCount }} Tersimpan</span>
                                    <span class="badge bg-info text-dark">{{ count($requiredDocuments) }} Jenis Dokumen</span>
                                    <span class="badge bg-warning text-dark">{{ max(count($requiredDocuments) - $uploadedCount, 0) }} Belum Lengkap</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between mb-2"><strong>Kelengkapan Dokumen</strong><span>{{ $completionPercent }}%</span></div>
                            <div class="progress"><div class="progress-bar bg-info" style="width:{{ $completionPercent }}%"></div></div>
                            <div class="small-muted mt-2">Simpan minimal satu file per jenis dokumen untuk melengkapi arsip admin.</div>
                        </div>
                    </div>
                </div>
            </section>

            <form method="POST" action="{{ route('peserta.dokumen.pendukung.store') }}" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="row g-3">
                    @foreach($requiredDocuments as $key => $label)
                        <div class="col-md-6 col-xl-4">
                            <div class="upload-card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <div>
                                        <div class="fw-bold">{{ $label }}</div>
                                        <div class="small-muted">Satu file untuk jenis dokumen ini.</div>
                                    </div>
                                    @php $currentDoc = $documentLookup[$key] ?? null; @endphp
                                    <span class="badge {{ $currentDoc ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $currentDoc ? 'Tersimpan' : 'Belum Upload' }}
                                    </span>
                                </div>
                                <input type="file" name="{{ $key }}" class="form-control mb-2" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="small-muted">
                                    {{ $currentDoc ? 'File saat ini: '.$currentDoc->nama_dokumen : 'Belum ada file untuk jenis ini.' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-cloud-upload me-1"></i> Simpan Dokumen</button>
                    <a class="btn btn-outline-secondary" href="{{ route('peserta.dokumen.status') }}">Lihat Status Dokumen</a>
                </div>
            </form>

            <section class="soft-card p-3 p-lg-4">
                        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Daftar Dokumen Pendukung</h5>
                            </div>
                            <span class="badge bg-primary">{{ count($requiredDocuments) }} Jenis</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jenis Dokumen</th>
                                        <th>Nama File</th>
                                        <th>Kategori</th>
                                        <th>Tanggal Unggah</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($documentRows as $document)
                                        @php
                                            $docKey = $document->jenis_dokumen;
                                            if (! $docKey) {
                                                $source = strtolower(trim((string) ($document->nama_dokumen ?? $document->file ?? '')));
                                                $docKey = str_contains($source, 'transkrip') ? 'transkip' : (str_contains($source, 'sertifikat') ? 'sertifikat_pendukung' : str_replace('-', '_', str_replace(' ', '_', $source)));
                                            }
                                            $label = $requiredDocuments[$docKey] ?? ucfirst(str_replace('_', ' ', $docKey ?: 'dokumen'));
                                            $uploadedAt = $document->created_at
                                                ? $document->created_at->copy()->timezone('Asia/Jakarta')->translatedFormat('d M Y')
                                                : '-';
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $label }}</strong>
                                            </td>
                                            <td>
                                                {{ $document->nama_dokumen ?? '-' }}
                                                @if($document->file)
                                                    <div class="small-muted">{{ basename($document->file) }}</div>
                                                @endif
                                            </td>
                                            <td>{{ $document->kategori ?? '-' }}</td>
                                            <td>{{ $uploadedAt }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($document->file && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file))
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-primary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#documentPreviewModal"
                                                            data-document-title="{{ $document->nama_dokumen ?? $label }}"
                                                            data-document-url="{{ asset('storage/'.$document->file) }}"
                                                        >
                                                            Lihat
                                                        </button>
                                                        <a class="btn btn-outline-success" href="{{ asset('storage/'.$document->file) }}" download>Download</a>
                                                    @else
                                                        <button class="btn btn-outline-secondary" type="button" disabled>Lihat</button>
                                                        <button class="btn btn-outline-secondary" type="button" disabled>Download</button>
                                                    @endif
                                                    <form method="POST" action="{{ route('peserta.dokumen.pendukung.destroy', $document->id) }}" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="small-muted">Belum ada dokumen tersimpan di database untuk akun ini.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-labelledby="documentPreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="documentPreviewLabel">Preview Dokumen</h5>
                        <div class="small-muted" id="documentPreviewSubtitle">Memuat file...</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9" style="min-height:70vh;">
                        <iframe id="documentPreviewFrame" src="" title="Preview dokumen" style="border:0;"></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="documentPreviewDownload" href="#" class="btn btn-outline-success" download>Download</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadInfoModal" tabindex="-1" aria-labelledby="uploadInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadInfoLabel">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Pilih file pada form di atas lalu klik <strong>Simpan Dokumen</strong>. Setiap file akan tersimpan ke database `documents` dengan `user_id` milik akun peserta yang sedang login, sehingga admin langsung melihat centang pada tabel dokumen peserta.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        const previewModal = document.getElementById('documentPreviewModal');
        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const url = button?.getAttribute('data-document-url') || '';
                const title = button?.getAttribute('data-document-title') || 'Preview Dokumen';

                const titleEl = document.getElementById('documentPreviewLabel');
                const subtitleEl = document.getElementById('documentPreviewSubtitle');
                const frameEl = document.getElementById('documentPreviewFrame');
                const downloadEl = document.getElementById('documentPreviewDownload');

                if (titleEl) titleEl.textContent = title;
                if (subtitleEl) subtitleEl.textContent = url ? 'Klik download jika ingin menyimpan file ke perangkat.' : 'File tidak tersedia.';
                if (frameEl) frameEl.src = url;
                if (downloadEl) downloadEl.href = url;
            });

            previewModal.addEventListener('hidden.bs.modal', function () {
                const titleEl = document.getElementById('documentPreviewLabel');
                const subtitleEl = document.getElementById('documentPreviewSubtitle');
                const frameEl = document.getElementById('documentPreviewFrame');
                const downloadEl = document.getElementById('documentPreviewDownload');

                if (titleEl) titleEl.textContent = 'Preview Dokumen';
                if (subtitleEl) subtitleEl.textContent = 'Memuat file...';
                if (frameEl) frameEl.src = '';
                if (downloadEl) downloadEl.href = '#';
            });
        }
    </script>
</body>
</html>
