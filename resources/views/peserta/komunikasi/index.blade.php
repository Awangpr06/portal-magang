<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Komunikasi Peserta</title>

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
        .content-wrap { width:100%; padding:24px; flex:1; }
        .soft-card, .stat-card, .filter-card, .table-card, .action-card, .conversation-card, .chat-card, .info-card, .message-hero, .announcement-card { border:1px solid var(--line); border-radius:8px; background:#fff; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .message-hero { background:linear-gradient(135deg,#e9f7fb 0%,#f2f9ed 100%); border:1px solid #d7ebee; }
        .stat-card, .action-card { height:100%; transition:.2s ease; }
        .stat-card:hover, .action-card:hover { transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .stat-icon, .action-icon { width:42px; height:42px; flex:0 0 42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:18px; }
        .small-muted { color:var(--muted); font-size:13px; }
        .breadcrumb a { color:var(--brand); text-decoration:none; }
        .conversation-item { display:block; border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fff; color:inherit; text-decoration:none; cursor:pointer; }
        .conversation-item.active, .conversation-item:hover { border-color:var(--brand); background:#f5fbfe; color:inherit; text-decoration:none; }
        .chat-window { min-height:420px; border:1px solid #e2ebef; border-radius:8px; padding:18px; background:#fbfdfe; }
        .bubble { max-width:78%; border-radius:8px; padding:10px 12px; margin-bottom:10px; display:inline-block; }
        .bubble.in { background:#fff; border:1px solid var(--line); }
        .bubble.out { margin-left:auto; background:var(--brand); color:#fff; }
        .recent-item { padding:12px 0; border-bottom:1px solid var(--line); }
        .recent-item:last-child { border-bottom:0; padding-bottom:0; }
        .attachment-item { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid var(--line); }
        .attachment-item:last-child { border-bottom:0; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .top-header { padding:14px 16px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $communicationData = $communicationData ?? [];
        $pesertaContext = $pesertaContext ?? [];
        $contacts = collect($communicationData['contacts'] ?? []);
        $conversations = collect($communicationData['conversations'] ?? []);
        $selectedConversation = $communicationData['selected_conversation'] ?? [];
        $stats = $communicationData['stats'] ?? ['total' => 0, 'unread' => 0, 'active' => 0, 'today' => 0];
        $pesertaStats = $pesertaContext['stats'] ?? [];
        $announcementItems = collect($pesertaContext['announcements'] ?? [])->take(3)->values();
        $notificationItems = collect($pesertaContext['notifications'] ?? [])->take(4)->values();
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $activeCommunication = $activeCommunication ?? 'Semua Komunikasi';
        $selectedMessages = collect($selectedConversation['messages'] ?? []);
        $selectedConversationId = $selectedConversation['id'] ?? null;
        $recentConversations = $conversations->take(4);
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
                        <h2 class="fw-bold mb-1">Komunikasi</h2>
                        <div class="small-muted">Ringkasan percakapan, pesan, dan aktivitas komunikasi peserta.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Komunikasi</li>
                </ol>
            </nav>

            <section class="message-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-8">
                        <h3 class="fw-bold mb-1">{{ $activeCommunication }}</h3>
                        <div class="text-secondary">Halaman utama komunikasi tetap menjadi ringkasan, sementara kartu pesan di bawah mengikuti tampilan sub menu pesan.</div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-success">Aktif</span>
                            <span class="badge bg-warning text-dark">{{ $stats['unread'] ?? 0 }} belum dibaca</span>
                            <span class="badge bg-info text-dark">{{ $stats['today'] ?? 0 }} hari ini</span>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="soft-card p-3">
                            <strong>Ringkasan Percakapan</strong>
                            <div class="small-muted mt-1">Total percakapan {{ $stats['total'] ?? 0 }} dan percakapan aktif {{ $stats['active'] ?? 0 }}.</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Total Percakapan</div><h4 class="fw-bold mb-1">{{ $stats['total'] ?? 0 }}</h4><span class="badge bg-primary">Aktif</span></div><span class="stat-icon bg-primary"><i class="bi bi-chat-left-text"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Belum Dibaca</div><h4 class="fw-bold mb-1">{{ $stats['unread'] ?? 0 }}</h4><span class="badge bg-warning text-dark">Baru</span></div><span class="stat-icon bg-warning"><i class="bi bi-envelope-exclamation"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Percakapan Aktif</div><h4 class="fw-bold mb-1">{{ $stats['active'] ?? 0 }}</h4><span class="badge bg-success">Realtime</span></div><span class="stat-icon bg-success"><i class="bi bi-people"></i></span></div></div></div>
                    <div class="col-md-6 col-xl"><div class="stat-card p-3"><div class="d-flex justify-content-between gap-3"><div><div class="small-muted">Pesan Hari Ini</div><h4 class="fw-bold mb-1">{{ $stats['today'] ?? 0 }}</h4><span class="badge bg-info text-dark">Hari ini</span></div><span class="stat-icon bg-info"><i class="bi bi-clock-history"></i></span></div></div></div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-4">
                    <div class="col-xl-6">
                        <div class="soft-card p-3 p-lg-4 h-100">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">Ringkasan Pengumuman</h5>
                                    <div class="small-muted">Pengumuman terbaru yang tersinkron dari database.</div>
                                </div>
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('peserta.komunikasi.pengumuman') }}">Buka Pengumuman</a>
                            </div>
                            <div class="row g-3">
                                @forelse ($announcementItems as $announcement)
                                    <div class="col-md-6">
                                        <div class="announcement-card p-3 h-100">
                                            <span class="badge bg-info text-dark mb-2">{{ $announcement['kategori'] ?? 'Peserta' }}</span>
                                            <h6 class="fw-bold">{{ $announcement['judul'] ?? '-' }}</h6>
                                            <p class="small-muted mb-3">{{ \Illuminate\Support\Str::limit($announcement['isi'] ?? '-', 90) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">{{ $announcement['publikasi'] ?? '-' }}</small>
                                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#announcementModal">Detail</button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-secondary">Belum ada pengumuman aktif.</div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="soft-card p-3 p-lg-4 h-100">
                            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">Ringkasan Notifikasi</h5>
                                    <div class="small-muted">Notifikasi terbaru dari sistem dan aktivitas magang.</div>
                                </div>
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('peserta.komunikasi.notifikasi') }}">Buka Notifikasi</a>
                            </div>
                            @forelse ($notificationItems as $notification)
                                <div class="recent-item">
                                    <div class="d-flex justify-content-between gap-3">
                                        <div>
                                            <strong>{{ $notification->judul ?? $notification->title ?? 'Notifikasi' }}</strong>
                                            <div class="small-muted">{{ $notification->pesan ?? $notification->isi ?? 'Pembaruan sistem' }}</div>
                                        </div>
                                        <span class="badge {{ $notification->dibaca ? 'bg-success' : 'bg-danger' }}">{{ $notification->dibaca ? 'Dibaca' : 'Baru' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-secondary">Belum ada notifikasi terbaru.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="soft-card p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Pesan</h5>
                        <div class="small-muted">Kartu pesan ini disamakan dengan sub menu pesan, tetapi tetap berada di halaman utama komunikasi.</div>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('peserta.komunikasi.pesan', ['conversation' => $selectedConversationId]) }}">
                        <i class="bi bi-chat-dots me-1"></i> Buka Pesan Lengkap
                    </a>
                </div>

                <div class="row g-4">
                    <div class="col-xl-4">
                        <section class="conversation-card h-100">
                            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                <strong>Daftar Percakapan</strong>
                                <span class="badge bg-danger">{{ $stats['unread'] ?? 0 }}</span>
                            </div>
                            <div class="p-2">
                                @forelse ($recentConversations as $conversation)
                                    <a href="{{ route('peserta.komunikasi', ['conversation' => $conversation['id']]) }}" class="conversation-item {{ $selectedConversationId === $conversation['id'] ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between gap-2 mb-2">
                                            <span class="badge {{ ($conversation['status'] ?? 'dibaca') === 'belum dibaca' ? 'bg-warning text-dark' : 'bg-success' }}">{{ ucfirst(str_replace('_', ' ', $conversation['status'] ?? 'dibaca')) }}</span>
                                            <small class="text-muted">{{ $conversation['last_message_label'] ?? '-' }}</small>
                                        </div>
                                        <h6 class="mb-1">{{ $conversation['contact_name'] ?? 'Kontak' }}</h6>
                                        <p class="text-muted small mb-1">{{ $conversation['last_message'] ?? 'Belum ada pesan' }}</p>
                                        <small>{{ $conversation['contact_role'] ?? 'Pesan' }}</small>
                                    </a>
                                @empty
                                    <div class="text-center text-secondary py-4">Belum ada percakapan.</div>
                                @endforelse
                            </div>
                        </section>
                    </div>
                    <div class="col-xl-5">
                        <section class="chat-card p-3 p-lg-4 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $selectedConversation['contact_name'] ?? 'Pilih Percakapan' }}</h5>
                                    <div class="small-muted">{{ $selectedConversation['contact_role'] ?? 'Histori pesan akan tampil di sini.' }}</div>
                                </div>
                                <span class="badge bg-secondary">{{ $selectedConversation['last_message_label'] ?? '-' }}</span>
                            </div>
                            <div class="chat-window mb-3">
                                @forelse ($selectedMessages as $message)
                                    <div class="mb-3 {{ ($message['direction'] ?? 'in') === 'out' ? 'text-end' : '' }}">
                                        <div class="small text-muted mb-1">{{ $message['sender_name'] ?? 'Sistem' }} - {{ $message['date'] ?? '-' }} {{ $message['time'] ?? '-' }}</div>
                                        <span class="bubble {{ ($message['direction'] ?? 'in') === 'out' ? 'out' : 'in' }}">{{ $message['text'] ?? '' }}</span>
                                        @if (! empty($message['attachment']))
                                            <div class="small text-muted mt-1">{{ $message['attachment'] }}</div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="d-flex align-items-center justify-content-center h-100 text-secondary">
                                        <div class="text-center">
                                            <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                                            <p class="mb-0">Belum ada percakapan dipilih.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#attachmentModal"><i class="bi bi-paperclip"></i></button>
                                <input class="form-control" placeholder="Tulis pesan" readonly>
                                <button class="btn btn-primary" type="button" data-bs-toggle="toast" data-bs-target="#communicationToast"><i class="bi bi-send"></i></button>
                            </div>
                        </section>
                    </div>
                    <div class="col-xl-3">
                        <section class="info-card p-3 p-lg-4 mb-4">
                            <h5 class="fw-bold mb-1">Informasi Percakapan</h5>
                            <div class="small-muted mb-3">Detail kontak dan status diskusi.</div>
                            <div class="mb-3"><strong>Peran</strong><div class="small-muted">{{ $selectedConversation['contact_role'] ?? '-' }}</div></div>
                            <div class="mb-3"><strong>Topik</strong><div class="small-muted">{{ $selectedConversation['subject'] ?? '-' }}</div></div>
                            <div><strong>Status</strong><div><span class="badge bg-success">{{ ucfirst((string) ($selectedConversation['status'] ?? 'aktif')) }}</span></div></div>
                        </section>
                        <section class="info-card p-3 p-lg-4">
                            <h5 class="fw-bold mb-1">Lampiran</h5>
                            @forelse ($selectedMessages->pluck('attachment')->filter()->take(2) as $attachment)
                                <div class="attachment-item">
                                    <div>
                                        <strong>{{ basename((string) $attachment) }}</strong>
                                        <div class="small-muted">Lampiran pesan</div>
                                    </div>
                                    <i class="bi bi-download text-primary"></i>
                                </div>
                            @empty
                                <div class="text-secondary py-3">Belum ada lampiran.</div>
                            @endforelse
                        </section>
                    </div>
                </div>
            </section>

            <section class="soft-card p-3 p-lg-4 mb-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 align-items-lg-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Ringkasan Histori</h5>
                        <div class="small-muted">Percakapan terbaru yang tersinkron dari database.</div>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('peserta.komunikasi.pesan') }}">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Halaman Pesan
                    </a>
                </div>
                <div class="table-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kontak</th>
                                    <th>Peran</th>
                                    <th>Topik</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentConversations as $conversation)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $conversation['contact_name'] ?? '-' }}</td>
                                        <td>{{ $conversation['contact_role'] ?? '-' }}</td>
                                        <td>{{ $conversation['subject'] ?? '-' }}</td>
                                        <td>{{ $conversation['last_message_label'] ?? '-' }}</td>
                                        <td><span class="badge {{ ($conversation['status'] ?? 'dibaca') === 'belum dibaca' ? 'bg-warning text-dark' : 'bg-success' }}">{{ ucfirst(str_replace('_', ' ', $conversation['status'] ?? 'dibaca')) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-secondary">Belum ada histori komunikasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <footer class="footer-system">&copy; 2026 Portal Magang dan Kearsipan LLDIKTI Wilayah V Yogyakarta - Version 1.0</footer>
    </main>

    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentLabel">Lampiran Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Lampiran dan balasan penuh tersedia di halaman Pesan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a class="btn btn-primary" href="{{ route('peserta.komunikasi.pesan', ['conversation' => $selectedConversationId]) }}">Buka Pesan</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmLabel">Konfirmasi Tindakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">Buka halaman Pesan untuk mengirim, membalas, atau mengarsipkan percakapan.</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="{{ route('peserta.komunikasi.pesan') }}">Buka Pesan</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementLabel">Detail Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Detail pengumuman tersedia lengkap di menu Pengumuman.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a class="btn btn-primary" href="{{ route('peserta.komunikasi.pengumuman') }}">Buka Pengumuman</a>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="communicationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header"><i class="bi bi-check-circle-fill text-success me-2"></i><strong class="me-auto">Komunikasi</strong><small>Baru saja</small><button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
            <div class="toast-body">Kartu pesan di halaman komunikasi utama sudah disamakan dengan sub menu pesan.</div>
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
