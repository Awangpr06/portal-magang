<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pesan Peserta</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand: #2a8fbd;
            --brand-dark: #185f80;
            --accent: #62bd42;
            --ink: #163342;
            --muted: #6c7d86;
            --line: #d7eaf2;
            --page: #f6fbfd;
        }

        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: var(--page);
            color: var(--ink);
            font-family: Arial, sans-serif;
            overflow-x: auto;
            min-width: 1200px;
        }

        .sidebar {
            width: 286px;
            min-height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            background: var(--brand);
            color: #fff;
            overflow-y: auto;
            padding: 18px;
            z-index: 1030;
        }

        .brand-logo { width: 68px; height: 68px; object-fit: contain; }
        .profile-photo { width: 62px; height: 62px; object-fit: cover; border: 3px solid rgba(255,255,255,.7); }
        .status-dot { width: 9px; height: 9px; display: inline-block; border-radius: 50%; background: var(--accent); box-shadow: 0 0 0 3px rgba(98,189,66,.18); }
        .sidebar a, .sidebar .logout-button {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #edf8fc;
            text-decoration: none;
            padding: 11px 13px;
            border-radius: 8px;
            border: 0;
            background: transparent;
            font-size: 14px;
            text-align: left;
            margin-bottom: 5px;
        }
        .sidebar a:hover, .sidebar a.active, .sidebar .logout-button:hover, .sidebar-parent.active {
            background: var(--brand-dark);
            color: #fff;
        }
        .sidebar-parent { display: flex; align-items: stretch; border-radius: 8px; margin-bottom: 5px; overflow: hidden; }
        .sidebar-parent a { flex: 1; margin-bottom: 0; border-radius: 8px 0 0 8px; }
        .sidebar-parent a:hover { background: transparent; }
        .sidebar-toggle { width: 38px; border: 0; color: #fff; background: transparent; }
        .sidebar-toggle:hover { background: #174f6a; }
        .sidebar-submenu { padding: 4px 0 7px 20px; }
        .sidebar-submenu a { padding: 9px 12px; font-size: 13px; color: #d9eef6; }
        .main-content { margin-left: 286px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-header {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(246,251,253,.94);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--line);
            padding: 16px 24px;
        }
        .header-logo { width: 44px; height: 44px; object-fit: contain; }
        .icon-button {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--brand);
        }
        .content-wrap { width: 100%; padding: 24px; flex: 1; }
        .panel-card, .soft-card, .message-card, .composer-card, .contact-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(22,51,66,.05);
        }
        .message-hero {
            background: linear-gradient(135deg, #e8f7fb 0%, #f4fbf0 100%);
            border: 1px solid #d7ebee;
            border-radius: 8px;
        }
        .small-muted { color: var(--muted); font-size: 13px; }
        .conversation-list {
            max-height: 540px;
            overflow: auto;
        }
        .conversation-item {
            border: 1px solid #e2ebef;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: .15s ease;
            background: #fff;
            text-align: left;
        }
        .conversation-item:hover,
        .conversation-item.active {
            border-color: var(--brand);
            background: #f5fbfe;
        }
        .chat-box {
            min-height: 420px;
            border: 1px solid #e2ebef;
            border-radius: 8px;
            padding: 18px;
            background: #fbfdfe;
        }
        .bubble {
            max-width: 78%;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #eaf3f7;
        }
        .bubble.out {
            margin-left: auto;
            background: var(--brand);
            color: #fff;
        }
        .empty-state {
            min-height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #6c757d;
        }
        .contact-item {
            display: flex;
            align-items: center;
            justify-content: between;
            gap: 12px;
            border: 1px solid #e2ebef;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 10px;
        }
        .contact-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            background: #eaf3f7;
        }
        .footer-system {
            padding: 18px 24px;
            text-align: center;
            font-size: 13px;
            color: #6d7d86;
            border-top: 1px solid var(--line);
            background: #fff;
        }
        @media (max-width: 991px) {
            .sidebar { position: fixed; width: 286px; min-height: 100vh; }
            .main-content { margin-left: 286px; }
            .top-header { padding: 14px 16px; }
            .content-wrap { padding: 16px; }
        }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
    @php
        $communicationData = $communicationData ?? [];
        $currentUser = $communicationData['current_user'] ?? [];
        $contacts = $communicationData['contacts'] ?? [];
        $conversations = $communicationData['conversations'] ?? [];
        $selectedConversation = $communicationData['selected_conversation'] ?? null;
        $stats = $communicationData['stats'] ?? ['total' => 0, 'unread' => 0, 'active' => 0, 'today' => 0];
        $userName = auth()->user()->name ?? 'Aulia Berliana';
        $avatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=2a8fbd&color=ffffff';
        $selectedConversationId = $selectedConversation['id'] ?? ($conversations[0]['id'] ?? null);
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
                <a href="{{ route('peserta.komunikasi.pesan') }}" class="active">Pesan</a>
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
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button>
        </form>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset('images/logo-lldikti.png') }}" class="header-logo" alt="Logo Portal Magang LLDIKTI Wilayah V Yogyakarta">
                    <div>
                        <h2 class="fw-bold mb-1">Pesan</h2>
                        <div class="small-muted">Kirim, terima, dan balas pesan langsung dengan mentor, pembimbing, atau admin.</div>
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
                    <li class="breadcrumb-item active" aria-current="page">Pesan</li>
                </ol>
            </nav>

            <section class="message-hero p-3 p-lg-4 mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-xl-8">
                        <h3 class="fw-bold mb-1">Ruang Pesan Peserta</h3>
                        <div class="text-secondary">Halaman ini khusus untuk percakapan langsung. Ringkasan komunikasi tetap ada di menu komunikasi utama, sedangkan pesan dibuka di halaman terpisah ini.</div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <span class="badge bg-success">Aktif</span>
                            <span class="badge bg-primary">Total percakapan: {{ $stats['total'] ?? 0 }}</span>
                            <span class="badge bg-warning text-dark">Belum dibaca: {{ $stats['unread'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="soft-card p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Status Pesan</strong>
                                    <div class="small-muted mt-1">Percakapan terbaru ditampilkan dari database.</div>
                                </div>
                                <span class="badge bg-info text-dark">Realtime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="panel-card p-3">
                            <div class="small-muted">Total Percakapan</div>
                            <h4 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel-card p-3">
                            <div class="small-muted">Belum Dibaca</div>
                            <h4 class="fw-bold mb-0">{{ $stats['unread'] ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel-card p-3">
                            <div class="small-muted">Percakapan Aktif</div>
                            <h4 class="fw-bold mb-0">{{ $stats['active'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </section>

            <section class="row g-4">
                <div class="col-xl-4">
                    <div class="panel-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="mb-1">Daftar Percakapan</h5>
                                <div class="small-muted">{{ count($conversations) }} percakapan ditemukan</div>
                            </div>
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#composeModal">
                                <i class="bi bi-pencil-square me-1"></i> Pesan Baru
                            </button>
                        </div>
                        <div class="conversation-list" id="conversationList"></div>
                        <div class="empty-state d-none" id="emptyConversation">
                            <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Percakapan tidak ditemukan.</p></div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-1">Kontak Tersedia</h6>
                                <div class="small-muted">Pilih kontak untuk mulai percakapan.</div>
                            </div>
                        </div>
                        <div id="contactList"></div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="panel-card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h5 class="mb-1" id="chatTitle">Pilih Percakapan</h5>
                                <p class="text-muted mb-0" id="chatMeta">Histori pesan akan tampil di sini.</p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-secondary" id="chatBadge">-</span>
                                <div class="small-muted mt-1" id="chatTime">-</div>
                            </div>
                        </div>

                        <div class="chat-box" id="chatBox">
                            <div class="empty-state">
                                <div><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">Belum ada percakapan dipilih.</p></div>
                            </div>
                        </div>

                        <div class="input-group mt-3">
                            <input class="form-control" id="replyInput" placeholder="Tulis balasan">
                            <button class="btn btn-primary" type="button" id="replyButton">Balas</button>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="panel-card p-3 mb-4">
                        <h5 class="mb-3">Profil Kontak Aktif</h5>
                        <p class="text-muted mb-1">Nama</p>
                        <h6 id="profileName" class="mb-3">-</h6>
                        <p class="text-muted mb-1">Peran</p>
                        <h6 id="profileRole" class="mb-3">-</h6>
                        <p class="text-muted mb-1">Info</p>
                        <h6 id="profileMeta" class="mb-3">-</h6>
                        <p class="text-muted mb-1">Status</p>
                        <span class="badge bg-success" id="profileStatus">Aktif</span>
                    </div>
                    <div class="panel-card p-3">
                        <h5 class="mb-3">Aksi Cepat</h5>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#composeModal">Pesan Baru</button>
                            <button class="btn btn-outline-secondary" type="button" id="markReadButton">Tandai Dibaca</button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="footer-system">Portal Magang LLDIKTI Wilayah V Yogyakarta</div>
    </main>

    <div class="modal fade" id="composeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Kirim Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="recipientInput">Penerima</label>
                        <select class="form-select" id="recipientInput"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="subjectInput">Subjek</label>
                        <input class="form-control" id="subjectInput" type="text" placeholder="Subjek pesan">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="attachmentInput">Lampiran</label>
                        <input class="form-control" id="attachmentInput" type="text" placeholder="Nama file atau tautan">
                    </div>
                    <div>
                        <label class="form-label" for="messageInput">Isi Pesan</label>
                        <textarea class="form-control" id="messageInput" rows="4" placeholder="Tulis pesan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="sendNewMessageButton">Kirim</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="messageToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage">Pesan diperbarui.</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pageData = @json($communicationData);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const sendUrl = @json(route('komunikasi.send'));
            const replyUrlBase = @json(url('/komunikasi'));
            const conversations = pageData.conversations || [];
            const contacts = pageData.contacts || [];
            let selectedId = @json($selectedConversationId);
            let currentList = [...conversations];
            const toast = bootstrap.Toast.getOrCreateInstance(document.getElementById('messageToast'));

            const conversationList = document.getElementById('conversationList');
            const contactList = document.getElementById('contactList');
            const chatBox = document.getElementById('chatBox');
            const replyInput = document.getElementById('replyInput');
            const recipientInput = document.getElementById('recipientInput');
            const subjectInput = document.getElementById('subjectInput');
            const attachmentInput = document.getElementById('attachmentInput');
            const messageInput = document.getElementById('messageInput');

            const showToast = (message, success = true) => {
                const toastElement = document.getElementById('messageToast');
                toastElement.classList.toggle('text-bg-dark', success);
                toastElement.classList.toggle('text-bg-danger', !success);
                document.getElementById('toastMessage').textContent = message;
                toast.show();
            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const titleCase = (value) => String(value || '')
                .split(' ')
                .filter(Boolean)
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');

            const selectedConversation = () => currentList.find((item) => item.id === selectedId) || currentList[0] || null;

            const postForm = async (url, formData) => {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (!response.ok) {
                    let message = 'Permintaan gagal diproses.';
                    try {
                        const payload = await response.json();
                        message = payload.message || message;
                    } catch (error) {
                        // ignore
                    }
                    throw new Error(message);
                }

                return response.json();
            };

            const populateRecipients = () => {
                recipientInput.innerHTML = (contacts || []).map((contact) => `
                    <option value="${escapeHtml(contact.recipient_type)}:${escapeHtml(contact.recipient_id)}">
                        ${escapeHtml(contact.name)} - ${escapeHtml(contact.role)}
                    </option>
                `).join('') || '<option value="">Tidak ada kontak tersedia</option>';
            };

            const renderConversations = () => {
                conversationList.innerHTML = currentList.map((item) => `
                    <button type="button" class="w-100 conversation-item ${item.id === selectedId ? 'active' : ''}" data-id="${item.id}">
                        <div class="d-flex justify-content-between gap-2 mb-2">
                            <span class="badge ${item.unread_count > 0 ? 'bg-warning text-dark' : 'bg-success'}">${titleCase(item.status)}</span>
                            <small class="text-muted">${escapeHtml(item.last_message_label)}</small>
                        </div>
                        <div class="d-flex justify-content-between gap-2 align-items-start">
                            <div class="text-start">
                                <h6 class="mb-1">${escapeHtml(item.contact_name)}</h6>
                                <p class="text-muted small mb-1">${escapeHtml(item.subject)}</p>
                                <small>${escapeHtml(item.contact_role)}</small>
                            </div>
                            ${item.unread_count > 0 ? `<span class="badge bg-danger">${item.unread_count}</span>` : ''}
                        </div>
                    </button>
                `).join('') || '<div class="empty-state"><div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Percakapan tidak ditemukan.</p></div></div>';
            };

            const renderContacts = () => {
                contactList.innerHTML = (contacts || []).map((contact) => `
                    <div class="contact-item">
                        <img class="contact-avatar" src="${escapeHtml(contact.avatar || 'https://ui-avatars.com/api/?name=U&background=2a8fbd&color=ffffff')}" alt="${escapeHtml(contact.name)}">
                        <div class="flex-grow-1">
                            <div class="fw-semibold">${escapeHtml(contact.name)}</div>
                            <div class="small-muted">${escapeHtml(contact.role)}</div>
                            <div class="small-muted">${escapeHtml(contact.meta)}</div>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-contact="${escapeHtml(contact.recipient_type)}:${escapeHtml(contact.recipient_id)}">Chat</button>
                    </div>
                `).join('') || '<div class="empty-state"><div><i class="bi bi-people fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada kontak tersedia.</p></div></div>';
            };

            const renderChat = () => {
                const item = selectedConversation();

                if (!item) {
                    document.getElementById('chatTitle').textContent = 'Pilih Percakapan';
                    document.getElementById('chatMeta').textContent = 'Histori pesan akan tampil di sini.';
                    document.getElementById('chatBadge').textContent = '-';
                    document.getElementById('chatTime').textContent = '-';
                    document.getElementById('profileName').textContent = '-';
                    document.getElementById('profileRole').textContent = '-';
                    document.getElementById('profileMeta').textContent = '-';
                    chatBox.innerHTML = '<div class="empty-state"><div><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">Belum ada percakapan dipilih.</p></div></div>';
                    return;
                }

                document.getElementById('chatTitle').textContent = item.contact_name;
                document.getElementById('chatMeta').textContent = `${item.contact_role} · ${item.subject}`;
                document.getElementById('chatBadge').textContent = titleCase(item.status);
                document.getElementById('chatTime').textContent = item.last_message_at || '-';
                document.getElementById('profileName').textContent = item.contact_name;
                document.getElementById('profileRole').textContent = item.contact_role;
                document.getElementById('profileMeta').textContent = item.contact_meta;

                chatBox.innerHTML = (item.messages || []).map((message) => `
                    <div class="mb-3 ${message.direction === 'out' ? 'text-end' : ''}">
                        <div class="small text-muted mb-1">${escapeHtml(message.sender_name)} · ${escapeHtml(message.date)} ${escapeHtml(message.time)}</div>
                        <div class="bubble ${message.direction === 'out' ? 'out' : ''}">${escapeHtml(message.text)}</div>
                        ${message.attachment ? `<div class="small-muted mt-1">${escapeHtml(message.attachment)}</div>` : ''}
                    </div>
                `).join('') || '<div class="empty-state"><div><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">Belum ada pesan.</p></div></div>';
            };

            const sendReply = async () => {
                const item = selectedConversation();
                if (!item || !replyInput.value.trim()) return;

                const formData = new FormData();
                formData.append('message', replyInput.value.trim());
                formData.append('subject', item.subject || '');

                try {
                    await postForm(`${replyUrlBase}/${item.id}/balas`, formData);
                    replyInput.value = '';
                    showToast('Balasan berhasil dikirim.');
                    window.location.href = `${window.location.pathname}?conversation=${item.id}`;
                } catch (error) {
                    showToast(error.message, false);
                }
            };

            const markRead = async () => {
                const item = selectedConversation();
                if (!item) return;

                try {
                    await postForm(`${replyUrlBase}/${item.id}/baca`, new FormData());
                    showToast('Percakapan ditandai sudah dibaca.');
                    window.location.href = `${window.location.pathname}?conversation=${item.id}`;
                } catch (error) {
                    showToast(error.message, false);
                }
            };

            populateRecipients();
            renderConversations();
            renderContacts();
            renderChat();

            conversationList.addEventListener('click', (event) => {
                const button = event.target.closest('[data-id]');
                if (!button) return;
                const nextId = Number(button.dataset.id);
                window.location.href = `${window.location.pathname}?conversation=${nextId}`;
            });

            contactList.addEventListener('click', (event) => {
                const button = event.target.closest('[data-contact]');
                if (!button) return;
                recipientInput.value = button.dataset.contact;
                new bootstrap.Modal(document.getElementById('composeModal')).show();
            });

            document.getElementById('replyButton').addEventListener('click', sendReply);
            document.getElementById('markReadButton').addEventListener('click', markRead);

            document.getElementById('sendNewMessageButton').addEventListener('click', async () => {
                const receiver = recipientInput.value;
                const [recipientType, recipientId] = receiver.split(':');
                const subject = subjectInput.value.trim() || 'Pesan Baru';
                const message = messageInput.value.trim();
                const attachment = attachmentInput.value.trim();

                if (!recipientType || !recipientId || !message) {
                    showToast('Penerima dan isi pesan wajib diisi.', false);
                    return;
                }

                const formData = new FormData();
                formData.append('recipient_type', recipientType);
                formData.append('recipient_id', recipientId);
                formData.append('subject', subject);
                formData.append('message', message);
                if (attachment) {
                    formData.append('attachment', attachment);
                }

                try {
                    await postForm(sendUrl, formData);
                    showToast('Pesan berhasil dikirim.');
                    window.location.reload();
                } catch (error) {
                    showToast(error.message, false);
                }
            });
        });
    </script>
</body>
</html>
