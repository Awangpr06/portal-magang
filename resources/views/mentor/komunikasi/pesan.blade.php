@extends('mentor.layout.mentor')

@section('title', 'Pesan')
@section('page-title', 'Pesan')

@push('styles')
<style>
    .message-page .message-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .message-page .stat-card,
    .message-page .filter-card,
    .message-page .table-card,
    .message-page .panel-card { border:0; border-radius:8px; }
    .message-page .stat-card { cursor:pointer; transition:.2s ease; }
    .message-page .stat-card:hover,
    .message-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .message-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .message-page .conversation-list { max-height:390px; overflow:auto; }
    .message-page .message-box { min-height:310px; max-height:390px; overflow:auto; border:1px solid #d7eaf2; border-radius:8px; background:#f8f9fa; }
    .message-page .bubble { max-width:78%; display:inline-block; border-radius:8px; padding:10px 12px; }
    .message-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .message-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:340px; }
    .message-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .message-page .pagination .page-link { color:#2a8fbd; }
    .message-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid message-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.komunikasi') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pesan</li>
        </ol>
    </nav>

    <section class="message-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Pesan Mentor</h3>
                <p class="mb-0">Kelola komunikasi langsung dengan peserta magang dan pengguna lain agar koordinasi terdokumentasi rapi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="messageBadge">Real-time</span>
                <button class="btn btn-dark" type="button" id="newMessageButton"><i class="bi bi-plus-circle"></i> Pesan Baru</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-envelope-paper-fill"></i>
        <div>3 pesan belum dibaca dan 5 pesan terkirim hari ini.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Pesan</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-envelope"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="belum dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statUnread">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-envelope-exclamation"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="terkirim">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pesan Terkirim</p><h3 class="mb-0" id="statSent">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-send-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kontak Aktif</p><h3 class="mb-0" id="statContacts">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-person-lines-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Respons Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-reply-all"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Respons</p><h3 class="mb-0" id="statAverage">0m</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-stopwatch"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Nama / Isi Pesan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari pengguna, subjek, pesan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal</label>
                    <input class="form-control" id="dateFilter" type="date">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="typeFilter">Jenis Pesan</label>
                    <select class="form-select" id="typeFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="Peserta">Peserta</option>
                        <option value="Pembimbing">Pembimbing</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-warning" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Percakapan</h5>
                    <span class="badge bg-warning text-dark" id="conversationCount">0</span>
                </div>
                <div class="card-body conversation-list" id="conversationList"></div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-1" id="chatTitle">Isi Pesan</h5>
                        <small class="text-muted" id="chatSubtitle">Pilih kontak untuk membuka percakapan.</small>
                    </div>
                    <button class="btn btn-sm btn-outline-warning" type="button" id="attachButton"><i class="bi bi-paperclip"></i></button>
                </div>
                <div class="card-body">
                    <div class="message-box p-3 mb-3" id="messageBox"></div>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="button" id="attachInputButton"><i class="bi bi-paperclip"></i></button>
                        <input class="form-control" id="replyInput" type="text" placeholder="Tulis balasan">
                        <button class="btn btn-warning" type="button" id="replyButton"><i class="bi bi-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Informasi Pengguna</h5>
                    <div id="userInfoPanel"></div>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning text-start" type="button" id="replyPanelButton"><i class="bi bi-reply me-2"></i> Balas Pesan</button>
                        <button class="btn btn-outline-warning text-start" type="button" id="markReadButton"><i class="bi bi-check2-all me-2"></i> Tandai Dibaca</button>
                        <button class="btn btn-outline-danger text-start" type="button" id="deleteButton"><i class="bi bi-trash me-2"></i> Hapus Percakapan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Histori Pesan</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data pesan</small>
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
                            <th>Nama Pengirim</th>
                            <th>Subjek Pesan</th>
                            <th>Jenis Pesan</th>
                            <th>Waktu Kirim</th>
                            <th>Respons Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="messageTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada pesan sesuai filter.</p></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 pesan</small>
            <nav aria-label="Pagination pesan"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
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
                    <label class="form-label" for="recipientInput">Penerima</label>
                    <select class="form-select" id="recipientInput">
                        <option>Ayu Lestari</option>
                        <option>Bima Pratama</option>
                        <option>Dr. Budi Santoso</option>
                        <option>Admin Portal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="subjectInput">Subjek</label>
                    <input class="form-control" id="subjectInput" type="text" placeholder="Subjek pesan">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="attachmentInput">Lampiran</label>
                    <input class="form-control" id="attachmentInput" type="text" placeholder="Nama dokumen atau tautan lampiran">
                </div>
                <div>
                    <label class="form-label" for="messageInput">Isi Pesan</label>
                    <textarea class="form-control" id="messageInput" rows="4" placeholder="Tulis pesan"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="sendNewMessageButton">Kirim</button>
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
            <div class="modal-body" id="confirmText">Lanjutkan aksi pesan?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmActionButton">Lanjutkan</button>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const communicationData = @json($communicationData ?? []);
    const csrfToken = @json(csrf_token());
    const sendUrl = @json(route('komunikasi.send'));
    const replyUrlBase = @json(url('/komunikasi'));

    const toDate = value => {
        if (!value) return null;
        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    };

    const escapeHtml = value => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const mapConversations = () => (communicationData.conversations || []).map(conversation => {
        const thread = (conversation.messages || []).map(message => ({
            ...message,
            created_at: message.created_at_iso || message.created_at || null,
            date: message.date || (toDate(message.created_at_iso || message.created_at) ? new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).format(toDate(message.created_at_iso || message.created_at)) : '-'),
            time: message.time || (toDate(message.created_at_iso || message.created_at) ? new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit' }).format(toDate(message.created_at_iso || message.created_at)) : '-'),
        }));
        const latestMessage = thread[thread.length - 1] || null;
        const latestDate = toDate(conversation.last_message_at_iso || latestMessage?.created_at);

        return {
            id: conversation.id,
            sender: conversation.contact_name || 'Kontak',
            type: conversation.contact_role || conversation.type || 'Pesan',
            subject: conversation.subject || 'Percakapan',
            time: latestDate ? latestDate.toISOString().slice(0, 10) : '-',
            status: Number(conversation.unread_count || 0) > 0 ? 'belum dibaca' : 'dibaca',
            response: conversation.last_message_label || (latestDate ? latestDate.toLocaleString('id-ID') : '-'),
            priority: Number(conversation.unread_count || 0) > 0 ? 'Tinggi' : 'Rendah',
            attachment: latestMessage?.attachment || '-',
            contents: thread.map(message => message.text).filter(Boolean),
            recipient_type: conversation.recipient_type,
            recipient_id: conversation.recipient_id,
            thread,
        };
    });

    let messages = mapConversations();
    let filtered = [...messages];
    let selectedId = communicationData.selected_conversation_id || messages[0]?.id || null;
    let currentPage = 1;
    let perPage = 5;
    let pendingAction = null;

    const newMessageModal = new bootstrap.Modal(document.getElementById('newMessageModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('messageToast'));
    const tableBody = document.getElementById('messageTableBody');
    const pagination = document.getElementById('pagination');

    const titleCase = text => text.replace(/\b\w/g, char => char.toUpperCase());
    const showToast = message => {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    };
    const populateRecipients = () => {
        const recipientInput = document.getElementById('recipientInput');
        if (!recipientInput) return;

        const options = (communicationData.contacts || [])
            .filter(contact => contact.recipient_type === 'peserta')
            .map(contact => `
            <option value="${contact.recipient_type}:${contact.recipient_id}">
                ${escapeHtml(contact.name)} - ${escapeHtml(contact.role)}
            </option>
        `).join('');

        recipientInput.innerHTML = options || '<option value="">Tidak ada kontak tersedia</option>';
    };
    const selectedMessage = () => messages.find(item => item.id === selectedId) || messages[0] || null;

    const updateStats = () => {
        document.getElementById('statTotal').textContent = messages.length;
        document.getElementById('statUnread').textContent = messages.filter(item => Number(item.unread_count || 0) > 0).length;
        document.getElementById('statSent').textContent = messages.length;
        document.getElementById('statContacts').textContent = new Set(messages.map(item => item.sender)).size;
        document.getElementById('statToday').textContent = messages.filter(item => {
            const value = toDate(item.time);
            if (!value) return false;
            return value.toDateString() === new Date().toDateString();
        }).length;
        document.getElementById('statAverage').textContent = messages.length ? `${Math.max(1, Math.round(messages.reduce((sum, item) => sum + (item.unread_count || 0), 0) / messages.length))}m` : '0m';
        document.getElementById('conversationCount').textContent = filtered.length;
    };

    const renderConversations = () => {
        document.getElementById('conversationList').innerHTML = filtered.map(item => `
            <button class="list-group-item list-group-item-action ${item.id === selectedId ? 'active' : ''}" type="button" data-message="${item.id}">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.sender}</strong>
                </div>
                <small class="d-block">${item.subject}</small>
                <small class="d-block">${item.response}</small>
            </button>
        `).join('') || '<p class="text-muted mb-0">Tidak ada percakapan.</p>';
    };

    const renderChat = () => {
        const item = selectedMessage();
        if (!item) {
            document.getElementById('chatTitle').textContent = 'Tidak ada percakapan';
            document.getElementById('chatSubtitle').textContent = 'Belum ada pesan yang tersedia.';
            document.getElementById('messageBox').innerHTML = '<div class="empty-state"><div><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">Belum ada pesan.</p></div></div>';
            document.getElementById('userInfoPanel').innerHTML = '<div class="info-row"><div class="fw-bold">-</div><small class="text-muted">-</small></div>';
            return;
        }
        document.getElementById('chatTitle').textContent = item.sender;
        document.getElementById('chatSubtitle').textContent = `${item.type} - ${item.subject}`;
        document.getElementById('messageBox').innerHTML = (item.thread || []).map((message) => `
            <div class="mb-2 ${message.direction === 'out' ? 'text-end' : ''}">
                <span class="bubble ${message.direction === 'out' ? 'bg-warning text-dark' : 'bg-white border'}">${escapeHtml(message.text || '')}</span>
            </div>
        `).join('');
        document.getElementById('userInfoPanel').innerHTML = `
            <div class="info-row">
                <div class="fw-bold">${item.sender}</div>
                <small class="text-muted">${item.type}</small>
            </div>
            <div class="info-row">
                <small class="text-muted">Lampiran</small>
                <div class="fw-semibold">${item.attachment}</div>
            </div>
        `;
        renderConversations();
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><div class="fw-semibold">${item.sender}</div><small class="text-muted">${item.type}</small></td>
                <td>${item.subject}</td>
                <td>${item.type}</td>
                <td>${item.time}</td>
                <td>${item.response}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-warning" type="button" data-action="reply" data-id="${item.id}"><i class="bi bi-reply"></i> Balas</button>
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="open" data-id="${item.id}"><i class="bi bi-eye"></i> Buka</button>
                    </div>
                </td>
            </tr>
        `).join('');
        document.getElementById('emptyState').classList.toggle('d-none', filtered.length > 0);
        document.getElementById('tableInfo').textContent = `Menampilkan ${filtered.length} dari ${messages.length} pesan`;
        document.getElementById('paginationInfo').textContent = filtered.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, filtered.length)} dari ${filtered.length} pesan` : 'Menampilkan 0 pesan';
        renderPagination(totalPages);
    };

    const renderPagination = totalPages => {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    };

    const applyFilters = () => {
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const date = document.getElementById('dateFilter').value;
        const type = document.getElementById('typeFilter').value;
        filtered = messages.filter(item => {
            const text = [item.sender, item.subject, item.type, item.contents.join(' ')].join(' ').toLowerCase();
            return text.includes(keyword)
                && (!date || item.time === date)
                && (type === 'semua' || item.type === type);
        });
        currentPage = 1;
        updateStats();
        renderConversations();
        renderTable();
    };

    const refreshAll = () => {
        updateStats();
        applyFilters();
        renderChat();
    };

    document.getElementById('conversationList').addEventListener('click', event => {
        const button = event.target.closest('button[data-message]');
        if (!button) return;
        window.location.href = `${window.location.pathname}?conversation=${Number(button.dataset.message)}`;
    });

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
                // ignore parse error
            }
            throw new Error(message);
        }

        return response.json();
    };

    const sendReply = async () => {
        const input = document.getElementById('replyInput');
        const item = selectedMessage();
        if (!item || !input.value.trim()) return;

        const formData = new FormData();
        formData.append('message', input.value.trim());
        formData.append('subject', item.subject || '');
        const attachment = document.getElementById('attachmentInput').value.trim();
        if (attachment) {
            formData.append('attachment', attachment);
        }

        try {
            await postForm(`${replyUrlBase}/${item.id}/balas`, formData);
            input.value = '';
            showToast('Balasan pesan berhasil dikirim.');
            window.location.href = `${window.location.pathname}?conversation=${item.id}`;
        } catch (error) {
            showToast(error.message, false);
        }
    };

    document.getElementById('replyButton').addEventListener('click', sendReply);
    document.getElementById('replyPanelButton').addEventListener('click', () => document.getElementById('replyInput').focus());

    tableBody.addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = messages.find(row => row.id === Number(button.dataset.id));
        if (!item) return;
        selectedId = item.id;
        if (button.dataset.action === 'reply' || button.dataset.action === 'open') renderChat();
        refreshAll();
    });

    document.getElementById('sendNewMessageButton').addEventListener('click', () => {
        const recipientValue = document.getElementById('recipientInput').value;
        const recipientLabel = document.getElementById('recipientInput').selectedOptions[0]?.textContent?.trim() || '-';
        const [recipientType, recipientId] = recipientValue.split(':');
        const subject = document.getElementById('subjectInput').value.trim() || 'Pesan Baru';
        const attachment = document.getElementById('attachmentInput').value.trim();
        const body = document.getElementById('messageInput').value.trim();

        if (!recipientType || !recipientId || !body) {
            showToast('Penerima dan isi pesan wajib diisi.', false);
            return;
        }

        pendingAction = async () => {
            const formData = new FormData();
            formData.append('recipient_type', recipientType);
            formData.append('recipient_id', recipientId);
            formData.append('subject', subject);
            formData.append('message', body);
            if (attachment) {
                formData.append('attachment', attachment);
            }

            await postForm(sendUrl, formData);
            showToast(`Pesan untuk ${recipientLabel} berhasil dikirim.`);
            window.location.reload();
        };
        document.getElementById('confirmText').textContent = `Kirim pesan "${subject}" kepada ${recipientLabel}?${attachment ? ` Lampiran: ${attachment}.` : ''}`;
        confirmModal.show();
    });

    document.getElementById('confirmActionButton').addEventListener('click', async () => {
        if (pendingAction) {
            await pendingAction();
        }
        pendingAction = null;
        confirmModal.hide();
        newMessageModal.hide();
        refreshAll();
    });

    populateRecipients();
    document.getElementById('newMessageButton').addEventListener('click', () => newMessageModal.show());
    document.getElementById('markReadButton').addEventListener('click', async () => {
        const item = selectedMessage();
        if (!item) return;

        try {
            await postForm(`${replyUrlBase}/${item.id}/baca`, new FormData());
            showToast('Percakapan ditandai dibaca.');
            window.location.href = `${window.location.pathname}?conversation=${item.id}`;
        } catch (error) {
            showToast(error.message, false);
        }
    });
    document.getElementById('deleteButton').addEventListener('click', () => {
        pendingAction = () => {
            messages = messages.filter(item => item.id !== selectedId);
            selectedId = messages[0]?.id;
            showToast('Percakapan berhasil dihapus.');
        };
        document.getElementById('confirmText').textContent = `Hapus percakapan dengan ${selectedMessage().sender}?`;
        confirmModal.show();
    });
    document.getElementById('attachButton').addEventListener('click', () => showToast('Lampiran dokumen siap ditambahkan.'));
    document.getElementById('attachInputButton').addEventListener('click', () => showToast('Pilih dokumen lampiran dari perangkat.'));

    pagination.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('perPageSelect').addEventListener('change', event => {
        perPage = Number(event.target.value);
        currentPage = 1;
        renderTable();
    });

    document.getElementById('applyFilter').addEventListener('click', applyFilters);
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.querySelectorAll('#dateFilter,#typeFilter').forEach(input => input.addEventListener('change', applyFilters));
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('dateFilter').value = '';
        document.getElementById('typeFilter').value = 'semua';
        applyFilters();
    });

    document.querySelectorAll('.stat-card[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach(item => item.classList.remove('active'));
            card.classList.add('active');
            applyFilters();
        });
    });

    refreshAll();
});
</script>
@endpush
