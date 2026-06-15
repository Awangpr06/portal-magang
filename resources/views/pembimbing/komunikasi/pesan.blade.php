@extends('pembimbing.layout.pembimbing')

@section('title', 'Pesan')
@section('page-title', 'Pesan')

@push('styles')
<style>
    .message-page .message-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .message-page .stat-card,
    .message-page .filter-card,
    .message-page .table-card,
    .message-page .chat-panel,
    .message-page .side-panel { border:0; border-radius:8px; }
    .message-page .stat-card { cursor:pointer; transition:.2s ease; }
    .message-page .stat-card:hover,
    .message-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .message-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .message-page .conversation-item { border:1px solid #e2ebef; border-radius:8px; padding:12px; background:#fbfdfe; cursor:pointer; transition:.2s ease; }
    .message-page .conversation-item:hover,
    .message-page .conversation-item.active { border-color:#2a8fbd; background:#e8f5fb; }
    .message-page .avatar { width:42px; height:42px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:700; color:#fff; background:#2a8fbd; flex:0 0 auto; }
    .message-page .chat-box { min-height:360px; max-height:460px; overflow:auto; background:#f7fcfe; border-radius:8px; padding:16px; }
    .message-page .bubble { max-width:78%; padding:10px 12px; border-radius:8px; margin-bottom:10px; }
    .message-page .bubble.in { background:#fff; border:1px solid #e2ebef; }
    .message-page .bubble.out { background:#2a8fbd; color:#fff; margin-left:auto; }
    .message-page .table { font-size:14px; }
    .message-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .message-page .table tbody tr:hover { background:#f7fcfe; }
    .message-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:420px; }
    .message-page .info-row,
    .message-page .activity-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .message-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .message-page .pagination .page-link { color:#2a8fbd; }
    .message-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid message-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.komunikasi') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pesan</li>
        </ol>
    </nav>

    <section class="message-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Pesan Mahasiswa</h3>
                <p class="mb-0">Kelola komunikasi langsung, konsultasi, koordinasi, arahan, dan tindak lanjut kegiatan magang secara cepat dan terdokumentasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="messageBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-envelope-paper-fill"></i>
        <div>6 pesan masuk hari ini, 4 pesan belum dibaca, dan rata-rata respons pembimbing 18 menit.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pesan Masuk</p><h3 class="mb-0" id="statIncoming">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-inbox-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-direction-card="terkirim">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terkirim</p><h3 class="mb-0" id="statSent">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-send-check-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="belum dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statUnread">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-envelope-exclamation"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Mahasiswa Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-person-check-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-date-card="Hari Ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-calendar2-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Respons</p><h3 class="mb-0" id="statResponse">18m</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-stopwatch"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Pesan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari subjek, isi pesan, atau nama">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="studentFilter">Mahasiswa</label>
                    <select class="form-select" id="studentFilter">
                        <option value="semua">Semua Mahasiswa</option>
                        @foreach (($studentOptions ?? []) as $studentName)
                            <option value="{{ $studentName }}">{{ $studentName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Baca</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="belum dibaca">Belum Dibaca</option>
                        <option value="dibaca">Dibaca</option>
                        <option value="penting">Penting</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori Pesan</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Konsultasi">Konsultasi</option>
                        <option value="Koordinasi">Koordinasi</option>
                        <option value="Arahan">Arahan</option>
                        <option value="Tindak Lanjut">Tindak Lanjut</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal</label>
                    <select class="form-select" id="dateFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="Hari Ini">Hari Ini</option>
                        <option value="Minggu Ini">Minggu Ini</option>
                        <option value="Bulan Ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card side-panel shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Percakapan</h5>
                    <span class="badge bg-primary" id="conversationCount">0</span>
                </div>
                <div class="card-body">
                    <div id="conversationList" class="d-grid gap-2"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card chat-panel shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0" id="chatTitle">Percakapan Aktif</h5>
                        <small class="text-muted" id="chatSubtitle">Pilih percakapan untuk membuka pesan</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" id="newMessageButton"><i class="bi bi-pencil-square"></i> Pesan Baru</button>
                </div>
                <div class="card-body">
                    <div class="chat-box" id="chatBox"></div>
                    <div class="input-group mt-3">
                        <input class="form-control" id="messageInput" type="text" placeholder="Tulis pesan balasan">
                        <button class="btn btn-primary" type="button" id="sendMessageButton"><i class="bi bi-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card side-panel shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Informasi Komunikasi</h5>
                    <div id="communicationInfo"></div>
                    <hr>
                    <h6>Aktivitas Pesan</h6>
                    <div id="activityList"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Daftar Pesan</h5>
                <small class="text-muted" id="tableInfo">Menampilkan histori pesan</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small" for="perPageSelect">Data</label>
                <select class="form-select form-select-sm" id="perPageSelect" style="width:80px">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengirim</th>
                            <th>Penerima</th>
                            <th>Subjek</th>
                            <th>Ringkasan Pesan</th>
                            <th>Waktu Kirim</th>
                            <th>Status Baca</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="messageTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Tidak ada pesan sesuai filter.</p>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination pesan">
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmMessage">Ringkasan Pesan</label>
                <textarea class="form-control" id="confirmMessage" rows="3" placeholder="Tulis pesan atau catatan tindakan"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Kirim/Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageModalLabel">Pesan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="newMessageRecipient">Peserta Bimbingan</label>
                    <select class="form-select" id="newMessageRecipient">
                        <option value="">Pilih peserta</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="newMessageSubject">Subjek</label>
                    <input class="form-control" id="newMessageSubject" type="text" placeholder="Tulis subjek pesan">
                </div>
                <div class="mb-0">
                    <label class="form-label" for="newMessageBody">Isi Pesan</label>
                    <textarea class="form-control" id="newMessageBody" rows="4" placeholder="Tulis pesan untuk peserta"></textarea>
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
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Pesan diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const messages = @json($messagesData ?? []);
    const threadMap = @json($threadMap ?? []);
    const studentContacts = @json($studentContacts ?? []);
    const messageStats = @json($messageStats ?? []);
    const sendUrl = @json(route('komunikasi.send'));
    const replyRouteTemplate = @json(route('pembimbing.komunikasi.pesan.reply', ['conversation' => '__CONVERSATION__']));
    const currentUserName = @json(auth()->user()->name);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const tableBody = document.getElementById('messageTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const studentFilter = document.getElementById('studentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const dateFilter = document.getElementById('dateFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const newMessageModal = new bootstrap.Modal(document.getElementById('newMessageModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let activeStatus = 'semua';
    let selectedId = @json($activeConversationId ?? null) || (messages[0] ? messages[0].id : null);
    let pendingAction = null;

    const statusBadge = (status) => {
        const map = { 'belum dibaca':'warning', dibaca:'success', penting:'danger', arsip:'secondary', aktif:'primary' };
        return `<span class="badge bg-${map[status] || 'secondary'} text-capitalize">${status}</span>`;
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function populateNewMessageRecipients() {
        const select = document.getElementById('newMessageRecipient');
        if (!select) return;

        const options = studentContacts.map((student) => `
            <option value="${student.recipient_id}">
                ${student.label}
            </option>
        `).join('');

        select.innerHTML = '<option value="">Pilih peserta</option>' + options;
    }

    async function postNewMessage(formData) {
        const response = await fetch(sendUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            const payload = await response.json().catch(() => ({}));
            throw new Error(payload.message || 'Pesan gagal dikirim.');
        }

        return response.json();
    }

    function filteredData() {
        const keyword = searchInput.value.toLowerCase();
        return messages.filter((item) => {
            const keywordMatch = `${item.pengirim} ${item.penerima} ${item.subjek} ${item.ringkasan} ${item.kategori}`.toLowerCase().includes(keyword);
            const studentMatch = studentFilter.value === 'semua' || item.nama === studentFilter.value || item.pengirim === studentFilter.value || item.penerima === studentFilter.value;
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const categoryMatch = categoryFilter.value === 'semua' || item.kategori === categoryFilter.value;
            const dateMatch = dateFilter.value === 'semua' || item.tanggal === dateFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && studentMatch && statusMatch && categoryMatch && dateMatch && cardMatch;
        });
    }

    function getConversationThread(item) {
        return threadMap[item.id] || item.thread || [];
    }

    function updateConversationCache(itemId, patch = {}) {
        const item = messages.find((message) => message.id === itemId);
        if (!item) return null;
        Object.assign(item, patch);
        return item;
    }

    async function sendConversationMessage(item, content) {
        const text = content.trim();
        if (!item || !text) {
            showToast('Pilih percakapan dan isi pesan terlebih dahulu.', 'danger');
            return false;
        }

        const response = await fetch(replyRouteTemplate.replace('__CONVERSATION__', item.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ message: text }),
        });

        if (!response.ok) {
            const payload = await response.json().catch(() => ({}));
            const errorMessage = payload.message || 'Pesan gagal dikirim.';
            throw new Error(errorMessage);
        }

        const payload = await response.json();
        const thread = threadMap[item.id] || [];
        thread.push(payload.latest_message);
        threadMap[item.id] = thread;
        updateConversationCache(item.id, {
            pengirim: currentUserName,
            penerima: item.nama,
            status: 'dibaca',
            aktivitas: 'Baru saja',
            ringkasan: payload.latest_message.text,
            waktu: payload.conversation.last_message_at || item.waktu,
            subjek: payload.conversation.topik || item.subjek,
            arah: 'terkirim',
            sent_count: (item.sent_count || 0) + 1,
            unread_count: 0,
        });
        return true;
    }

    function renderStats() {
        const incoming = messages.reduce((sum, item) => sum + Number(item.incoming_count || 0), 0);
        const sent = messages.reduce((sum, item) => sum + Number(item.sent_count || 0), 0);
        const unread = messages.reduce((sum, item) => sum + Number(item.unread_count || 0), 0);
        const today = messages.filter((item) => item.tanggal === 'Hari Ini').length;
        document.getElementById('statIncoming').textContent = incoming;
        document.getElementById('statSent').textContent = sent;
        document.getElementById('statUnread').textContent = unread;
        document.getElementById('statActive').textContent = messages.length;
        document.getElementById('statToday').textContent = today;
    }

    function groupedConversations() {
        const grouped = new Map();

        messages.forEach((item) => {
            const key = (item.recipient_name || item.nama || item.penerima || 'Tanpa Penerima').toString().trim().toLowerCase();
            if (!grouped.has(key)) {
                grouped.set(key, item);
            }
        });

        return Array.from(grouped.values());
    }

    function renderConversations() {
        const conversations = groupedConversations();
        document.getElementById('conversationCount').textContent = conversations.length;
        document.getElementById('conversationList').innerHTML = conversations.map((item) => {
            const recipientName = item.recipient_name || item.nama || item.penerima || '-';
            const activeRecipient = item.penerima || recipientName;
            return `
            <div class="conversation-item ${item.id === selectedId ? 'active' : ''}" data-conversation-id="${item.id}">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar">${recipientName.charAt(0)}</span>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between gap-2">
                            <strong>${recipientName}</strong>
                            ${statusBadge(item.status)}
                        </div>
                        <small class="text-muted d-block">Penerima: ${activeRecipient}</small>
                        <small class="text-muted d-block">${item.subjek}</small>
                        <small>${item.aktivitas}</small>
                    </div>
                </div>
            </div>
        `;
        }).join('');
    }

    function renderChat(item) {
        if (!item) {
            document.getElementById('chatTitle').textContent = 'Percakapan Aktif';
            document.getElementById('chatSubtitle').textContent = 'Pilih percakapan untuk membuka pesan';
            document.getElementById('chatBox').innerHTML = '';
            document.getElementById('communicationInfo').innerHTML = '<div class="text-muted">Belum ada percakapan.</div>';
            return;
        }

        selectedId = item.id;
        const partner = item.nama || item.penerima;
        document.getElementById('chatTitle').textContent = partner;
        document.getElementById('chatSubtitle').textContent = `${item.subjek} - ${item.kategori}`;
        const thread = getConversationThread(item);
        document.getElementById('chatBox').innerHTML = thread.length
            ? thread.map((message) => `
            <div class="bubble ${message.type}">
                <div>${message.text}</div>
                <small class="${message.type === 'out' ? 'text-white-50' : 'text-muted'}">${message.time}</small>
            </div>
        `).join('')
            : '<div class="text-muted">Belum ada pesan di percakapan ini.</div>';
        document.getElementById('communicationInfo').innerHTML = `
            <div class="info-row">
                <small class="text-muted">Penerima/Pengirim</small>
                <h6 class="mb-1">${partner}</h6>
                <div>${item.kategori}</div>
                <div class="mt-2">${statusBadge(item.status)}</div>
            </div>
            <div class="info-row">
                <small class="text-muted">Subjek</small>
                <p class="mb-0">${item.subjek}</p>
            </div>
            <div class="info-row">
                <small class="text-muted">Waktu Kirim</small>
                <p class="mb-0">${item.waktu}</p>
            </div>
        `;
        renderConversations();
    }

    function renderActivities() {
        document.getElementById('activityList').innerHTML = messages.slice(0, 4).map((item) => `
            <div class="activity-row">
                <strong>${item.subjek}</strong>
                <small class="text-muted d-block">${item.aktivitas}</small>
            </div>
        `).join('');
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}" type="button">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}" type="button">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderTable() {
        const data = filteredData();
        const perPage = Number(perPageSelect.value);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);
        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.pengirim}</td>
                <td>${item.penerima}</td>
                <td><strong>${item.subjek}</strong><small class="text-muted d-block">${item.kategori}</small></td>
                <td>${item.ringkasan}</td>
                <td>${item.waktu}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="kirim pesan" data-id="${item.id}">Kirim Pesan</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="balas" data-id="${item.id}">Balas</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="tandai dibaca" data-id="${item.id}">Dibaca</button>
                        <button class="btn btn-outline-warning btn-sm" type="button" data-action="penting" data-id="${item.id}">Penting</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="arsip" data-id="${item.id}">Arsipkan</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                        <button class="btn btn-outline-dark btn-sm" type="button" data-action="riwayat" data-id="${item.id}">Riwayat</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} pesan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderActivities();
        renderChat(messages.find((item) => item.id === selectedId) || messages[0] || null);
    }

    function openDetail(item, action = 'detail') {
        document.getElementById('detailModalLabel').textContent = action === 'riwayat' ? 'Riwayat Pesan' : 'Detail Pesan';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Pengirim</small><h5>${item.pengirim}</h5><div>${item.waktu}</div></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Penerima</small><h5>${item.penerima}</h5><div>${statusBadge(item.status)}</div></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Subjek</small><h5>${item.subjek}</h5><p class="mb-0">${item.ringkasan}</p></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Percakapan</small><div class="mt-2">${(getConversationThread(item) || []).map((msg) => `<div class="small mb-2"><strong>${msg.type === 'out' ? 'Saya' : item.nama}:</strong> ${msg.text}</div>`).join('')}</div></div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div><strong>${item.subjek}</strong><small class="text-muted d-block">${item.pengirim} ke ${item.penerima}</small></div>
                    ${statusBadge(item.status)}
                </div>
                <hr>
                <small class="text-muted">Jenis Tindakan</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmMessage').value = item.ringkasan;
        confirmModal.show();
    }

    document.querySelectorAll('.stat-card[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            activeStatus = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.getElementById('conversationList').addEventListener('click', (event) => {
        const row = event.target.closest('[data-conversation-id]');
        if (!row) return;
        window.location.href = `${window.location.pathname}?conversation=${Number(row.dataset.conversationId)}`;
    });

    document.getElementById('sendMessageButton').addEventListener('click', async () => {
        const item = messages.find((message) => message.id === selectedId);
        const input = document.getElementById('messageInput');
        if (!item || !input.value.trim()) {
            showToast('Pilih percakapan dan isi pesan terlebih dahulu.', 'danger');
            return;
        }
        try {
            const success = await sendConversationMessage(item, input.value);
            if (!success) return;
            input.value = '';
            renderTable();
            showToast('Pesan berhasil dikirim.');
        } catch (error) {
            showToast(error.message || 'Pesan gagal dikirim.', 'danger');
        }
    });

    document.getElementById('newMessageButton').addEventListener('click', () => {
        if (!studentContacts.length) {
            showToast('Belum ada peserta bimbingan yang dapat dikirimi pesan.', 'info');
            return;
        }
        populateNewMessageRecipients();
        document.getElementById('newMessageSubject').value = '';
        document.getElementById('newMessageBody').value = '';
        newMessageModal.show();
    });
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter pesan berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studentFilter.value = 'semua';
        statusFilter.value = 'semua';
        categoryFilter.value = 'semua';
        dateFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter pesan berhasil direset.', 'info');
    });
    [searchInput, studentFilter, statusFilter, categoryFilter, dateFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
    searchInput.addEventListener('keyup', (event) => { if (event.key === 'Enter') { currentPage = 1; renderTable(); } });
    perPageSelect.addEventListener('change', () => { currentPage = 1; renderTable(); });
    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });
    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = messages.find((message) => message.id === Number(button.dataset.id));
        const action = button.dataset.action;
        renderChat(item);
        if (action === 'riwayat') {
            openDetail(item, action);
            return;
        }
        if (action === 'kirim pesan' || action === 'balas') {
            openConfirm(item, action);
            return;
        }
        if (action === 'tandai dibaca') item.status = 'dibaca';
        if (action === 'penting') item.status = 'penting';
        if (action === 'arsip' || action === 'hapus') item.status = 'arsip';
        renderTable();
        showToast(`Aksi ${action} berhasil disimpan.`, 'info');
    });
    document.getElementById('confirmAction').addEventListener('click', async () => {
        if (!pendingAction) return;
        const { item, action } = pendingAction;
        const message = document.getElementById('confirmMessage').value.trim();
        try {
            if (['balas', 'kirim pesan'].includes(action)) {
                await sendConversationMessage(item, message);
                showToast('Pesan berhasil dikirim.');
            } else {
                if (action === 'tandai dibaca') item.status = 'dibaca';
                if (action === 'penting') item.status = 'penting';
                if (action === 'arsip' || action === 'hapus') item.status = 'arsip';
                showToast(`Aksi ${action} berhasil disimpan.`);
            }
            confirmModal.hide();
            renderTable();
            openDetail(item, action);
            pendingAction = null;
        } catch (error) {
            showToast(error.message || 'Aksi gagal diproses.', 'danger');
        }
    });

    document.getElementById('sendNewMessageButton').addEventListener('click', async () => {
        const recipientId = document.getElementById('newMessageRecipient').value;
        const subject = document.getElementById('newMessageSubject').value.trim();
        const body = document.getElementById('newMessageBody').value.trim();
        const recipient = studentContacts.find((student) => String(student.recipient_id) === String(recipientId));

        if (!recipient || !body) {
            showToast('Pilih peserta dan isi pesan terlebih dahulu.', 'danger');
            return;
        }

        const formData = new FormData();
        formData.append('recipient_type', 'peserta');
        formData.append('recipient_id', recipient.recipient_id);
        formData.append('subject', subject || 'Pesan Pembimbing');
        formData.append('message', body);

        try {
            await postNewMessage(formData);
            newMessageModal.hide();
            showToast('Pesan baru berhasil dikirim.');
            window.location.reload();
        } catch (error) {
            showToast(error.message || 'Pesan gagal dikirim.', 'danger');
        }
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('messageBadge').textContent = 'Diperbarui';
        showToast('Data pesan berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi pesan: 4 pesan belum dibaca.', 'warning'), 800);
});
</script>
@endpush
