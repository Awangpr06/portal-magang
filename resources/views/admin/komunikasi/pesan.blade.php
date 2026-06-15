@extends('admin.layout.admin')

@section('title', 'Pesan')

@push('styles')
<style>
    .message-page .page-title { font-weight: 700; color: #163342; }
    .message-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .message-page .stat-card,
    .message-page .filter-card,
    .message-page .panel-card,
    .message-page .table-card { border: 0; border-radius: 8px; }
    .message-page .stat-card { cursor: pointer; transition: .2s ease; }
    .message-page .stat-card:hover,
    .message-page .stat-card.active { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .message-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .conversation-list { max-height: 430px; overflow: auto; }
    .conversation-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 13px; margin-bottom: 10px; cursor: pointer; transition: .15s ease; background: #fff; }
    .conversation-item:hover,
    .conversation-item.active { border-color: #0b5f86; background: #f5fbfe; }
    .chat-box { min-height: 310px; border: 1px solid #e2ebef; border-radius: 8px; padding: 18px; background: #fbfdfe; }
    .bubble { max-width: 78%; border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; background: #eaf3f7; }
    .bubble.admin { margin-left: auto; background: #0b5f86; color: #fff; }
    .message-page .table { font-size: 14px; }
    .message-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .message-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 250px; }
    .empty-state { min-height: 170px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid message-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.komunikasi.index') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pesan</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Pesan</h2>
            <p class="text-muted mb-0">Kelola komunikasi langsung antar super admin, peserta, mentor, pembimbing, perguruan tinggi, dan mitra instansi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Histori</button>
            <button class="btn btn-outline-secondary" type="button" id="broadcastButton"><i class="bi bi-megaphone"></i> Broadcast</button>
            <button class="btn btn-primary" type="button" id="composeButton"><i class="bi bi-pencil-square"></i> Kirim Pesan</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Pesan</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-chat-left-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="belum dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statUnread">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-envelope-exclamation"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-kind-card="grup">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Grup Aktif</p><h3 class="mb-0" id="statGroup">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-people"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-kind-card="pengumuman">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pengumuman Baru</p><h3 class="mb-0" id="statAnnouncement">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-megaphone"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="searchInput" class="form-label">Percakapan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari Percakapan">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="kindFilter" class="form-label">Kategori</label>
                    <select class="form-select" id="kindFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="langsung">Pesan Langsung</option>
                        <option value="grup">Grup</option>
                        <option value="pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="timeFilter" class="form-label">Tanggal</label>
                    <select class="form-select" id="timeFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="hari ini">Hari Ini</option>
                        <option value="minggu ini">Minggu Ini</option>
                        <option value="bulan ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="searchButton"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-1">Daftar Percakapan</h5>
                    <p class="text-muted mb-3" id="conversationSummary">0 percakapan ditemukan</p>
                    <div class="conversation-list" id="conversationList"></div>
                    <div class="empty-state d-none" id="emptyConversation">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Percakapan tidak ditemukan.</p></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="mb-1" id="chatTitle">Pilih Percakapan</h5>
                            <p class="text-muted mb-0" id="chatMeta">Histori chat akan tampil di sini.</p>
                        </div>
                        <span class="badge bg-secondary" id="chatBadge">-</span>
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
        </div>

        <div class="col-xl-3">
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Profil Kontak Aktif</h5>
                    <p class="text-muted mb-1">Nama/Kontak</p>
                    <h6 id="profileName" class="mb-3">-</h6>
                    <p class="text-muted mb-1">Role</p>
                    <h6 id="profileRole" class="mb-3">-</h6>
                    <p class="text-muted mb-1">Terakhir Aktif</p>
                    <h6 id="profileTime" class="mb-3">-</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" type="button" id="importantButton">Tandai Penting</button>
                        <button class="btn btn-outline-secondary" type="button" id="archiveButton">Arsipkan</button>
                        <button class="btn btn-outline-danger" type="button" id="deleteButton">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Histori Pesan</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengirim</th>
                            <th>Penerima</th>
                            <th>Jenis Pesan</th>
                            <th>Isi Pesan Ringkas</th>
                            <th>Tanggal</th>
                            <th width="270">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyTable">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Histori pesan tidak ditemukan.</p></div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination histori pesan"><ul class="pagination mb-0" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Kirim Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="formRole">Role Penerima</label>
                        <select class="form-select" id="formRole">
                            <option value="peserta">Peserta</option>
                            <option value="mentor">Mentor</option>
                            <option value="pembimbing">PA / Pembimbing Akademik</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formReceiver">Penerima</label>
                        <select class="form-select" id="formReceiver"></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formKind">Jenis Pesan</label>
                        <select class="form-select" id="formKind">
                            <option value="langsung">Pesan Langsung</option>
                            <option value="grup">Grup</option>
                            <option value="pengumuman">Pengumuman</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formMessage">Isi Pesan</label>
                        <textarea class="form-control" id="formMessage" rows="4" placeholder="Tulis isi pesan"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveForm">Kirim</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <p class="mb-0" id="confirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="messageToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const communicationData = @json($communicationData ?? []);
    const csrfToken = @json(csrf_token());
    const sendUrl = @json(route('komunikasi.send'));
    const replyUrlBase = @json(url('/komunikasi'));
    const conversationStatusUrlBase = @json(url('/admin/komunikasi'));
    const contacts = Array.isArray(communicationData.contacts) ? communicationData.contacts : [];
    const histories = (Array.isArray(communicationData.conversations) ? communicationData.conversations : []).map(conversation => {
        const thread = Array.isArray(conversation.messages) ? conversation.messages : [];
        const latestMessage = thread[thread.length - 1] || null;
        const latestDate = conversation.last_message_at_iso || latestMessage?.created_at_iso || null;
        const unreadCount = Number(conversation.unread_count || 0);

        return {
            id: conversation.id,
            conversation_id: conversation.id,
            pengirim: 'Super Admin',
            penerima: conversation.contact_name || 'Kontak',
            role: conversation.contact_role || 'Pesan',
            jenis: 'langsung',
            isi: conversation.last_message || latestMessage?.text || 'Belum ada pesan',
            tanggal: conversation.last_message_at || '-',
            waktu: latestDate && !Number.isNaN(new Date(latestDate).getTime())
                ? (new Date(latestDate).toLocaleDateString('id-ID') === new Date().toLocaleDateString('id-ID') ? 'hari ini' : 'baru')
                : 'baru',
            status: unreadCount > 0 ? 'belum dibaca' : 'dibaca',
            penting: unreadCount > 0,
            thread,
            recipient_type: conversation.recipient_type,
            recipient_id: conversation.recipient_id,
            contact_name: conversation.contact_name || 'Kontak',
            contact_role: conversation.contact_role || 'Pesan',
        };
    });

    const perPage = 6;
    let currentPage = 1;
    let selectedId = communicationData.selected_conversation_id || histories[0]?.id || null;
    let pendingAction = null;
    let pendingId = null;

    const searchInput = document.getElementById('searchInput');
    const kindFilter = document.getElementById('kindFilter');
    const timeFilter = document.getElementById('timeFilter');
    const conversationList = document.getElementById('conversationList');
    const historyTable = document.getElementById('historyTable');
    const pagination = document.getElementById('pagination');
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('messageToast'), { delay: 3000 });
    const formRole = document.getElementById('formRole');

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(status) {
        return { 'belum dibaca': 'bg-warning text-dark', dibaca: 'bg-info text-dark', terkirim: 'bg-success', arsip: 'bg-secondary' }[status];
    }

    function kindLabel(kind) {
        return { langsung: 'Pesan Langsung', grup: 'Grup', pengumuman: 'Pengumuman' }[kind];
    }

    function populateRecipients() {
        const select = document.getElementById('formReceiver');
        if (!select) return;
        const selectedRole = formRole?.value || 'peserta';

        const labels = {
            peserta: 'Peserta',
            mentor: 'Mentor',
            pembimbing: 'PA / Pembimbing Akademik',
            admin: 'Admin',
        };

        const options = contacts
            .filter((contact) => contact.recipient_type === selectedRole)
            .map((contact) => `
                <option value="${contact.recipient_type}:${contact.recipient_id}">
                    ${contact.name} - ${contact.role}
                </option>
            `).join('');

        select.innerHTML = options || '<option value="">Tidak ada kontak tersedia</option>';
    }

    function filteredHistories() {
        const keyword = searchInput.value.trim().toLowerCase();
        const kind = kindFilter.value;
        const time = timeFilter.value;
        return histories.filter((item) => {
            const matchKeyword = !keyword || [item.pengirim, item.penerima, item.role, item.jenis, item.isi].join(' ').toLowerCase().includes(keyword);
            const matchKind = kind === 'semua' || item.jenis === kind;
            const matchTime = time === 'semua' || item.waktu === time;
            return matchKeyword && matchKind && matchTime;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = histories.length;
        document.getElementById('statUnread').textContent = histories.filter((item) => item.status === 'belum dibaca').length;
        document.getElementById('statGroup').textContent = histories.filter((item) => item.jenis === 'grup' && item.status !== 'arsip').length;
        document.getElementById('statAnnouncement').textContent = histories.filter((item) => item.jenis === 'pengumuman' && item.waktu === 'bulan ini').length;
    }

    function renderConversations() {
        const data = filteredHistories();
        conversationList.innerHTML = data.map((item) => `
            <div class="conversation-item ${item.id === selectedId ? 'active' : ''}" role="button" tabindex="0" data-id="${item.id}">
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <small class="text-muted">${item.tanggal}</small>
                </div>
                <h6 class="mb-1">${item.pengirim === 'Super Admin' ? item.penerima : item.pengirim}</h6>
                <p class="text-muted small mb-1">${item.isi}</p>
                <small>${kindLabel(item.jenis)}${item.penting ? ' • Penting' : ''}</small>
            </div>
        `).join('');
        document.getElementById('conversationSummary').textContent = `${data.length} percakapan ditemukan`;
        conversationList.classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyConversation').classList.toggle('d-none', data.length > 0);
    }

    function renderTable() {
        const data = filteredHistories();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        historyTable.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.pengirim}</td>
                <td>${item.penerima}</td>
                <td>${kindLabel(item.jenis)}</td>
                <td>${item.isi}</td>
                <td>${item.tanggal}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="lihat" data-id="${item.id}">Lihat</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('tableSummary').textContent = `${data.length} histori pesan ditemukan`;
        document.getElementById('tableWrapper').classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyTable').classList.toggle('d-none', data.length > 0);
        document.getElementById('pageInfo').textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} pesan` : 'Menampilkan 0 pesan';
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" type="button" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderChat() {
        const item = histories.find((history) => history.id === selectedId);
        if (!item) {
            document.getElementById('chatTitle').textContent = 'Pilih Percakapan';
            document.getElementById('chatMeta').textContent = 'Histori chat akan tampil di sini.';
            document.getElementById('chatBadge').textContent = '-';
            document.getElementById('chatBox').innerHTML = '<div class="empty-state"><div><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">Belum ada percakapan dipilih.</p></div></div>';
            return;
        }
        const contactName = item.pengirim === 'Super Admin' ? item.penerima : item.pengirim;
        document.getElementById('chatTitle').textContent = contactName;
        document.getElementById('chatMeta').textContent = `${item.role} • ${item.tanggal}`;
        document.getElementById('chatBadge').textContent = kindLabel(item.jenis);
        document.getElementById('chatBadge').className = 'badge bg-primary';
        document.getElementById('profileName').textContent = contactName;
        document.getElementById('profileRole').textContent = item.role;
        document.getElementById('profileTime').textContent = titleCase(item.waktu);
        document.getElementById('chatBox').innerHTML = (item.thread || []).map((message) => `
            <div class="bubble ${message.direction === 'out' ? 'admin' : ''}">
                ${message.direction === 'out' ? 'Super Admin' : contactName}<br>${message.text}
            </div>
        `).join('') || `
            <div class="bubble">${item.isi}</div>
            <div class="bubble admin">Pesan diterima. Admin akan menindaklanjuti.</div>
        `;
    }

    function renderAll() {
        updateStats();
        renderConversations();
        renderTable();
        renderChat();
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('messageToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    async function postForm(url, formData) {
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
    }

    async function patchConversationStatus(id, status) {
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('status', status);
        return postForm(`${conversationStatusUrlBase}/${id}/status`, formData);
    }

    async function deleteConversation(id) {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        return postForm(`${conversationStatusUrlBase}/${id}`, formData);
    }

    function confirmAction(action, id = selectedId) {
        if (!id) {
            showToast('Pilih pesan terlebih dahulu.', false);
            return;
        }
        const item = histories.find((history) => history.id === id);
        pendingAction = action;
        pendingId = id;
        const labels = {
            arsip: ['Konfirmasi Arsip Pesan', `Arsipkan pesan dari ${item.pengirim}?`],
            hapus: ['Konfirmasi Hapus Pesan', `Hapus pesan dari ${item.pengirim}?`],
            penting: ['Konfirmasi Tandai Penting', `Tandai pesan dari ${item.pengirim} sebagai penting?`],
            broadcast: ['Konfirmasi Broadcast', 'Kirim broadcast komunikasi ke penerima yang dipilih?']
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            currentPage = 1;
            renderAll();
        });
    });

    document.querySelectorAll('[data-kind-card]').forEach((card) => {
        card.addEventListener('click', () => {
            kindFilter.value = card.dataset.kindCard;
            currentPage = 1;
            renderAll();
        });
    });

    [kindFilter, timeFilter].forEach((input) => input.addEventListener('change', () => {
        currentPage = 1;
        renderAll();
    }));

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderAll();
        }
    });

    document.getElementById('searchButton').addEventListener('click', () => {
        currentPage = 1;
        renderAll();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        kindFilter.value = 'semua';
        timeFilter.value = 'semua';
        currentPage = 1;
        renderAll();
    });

    conversationList.addEventListener('click', (event) => {
        const item = event.target.closest('[data-id]');
        if (!item) return;
        window.location.href = `${window.location.pathname}?conversation=${Number(item.dataset.id)}`;
    });

    historyTable.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const id = Number(button.dataset.id);
        const action = button.dataset.action;
        if (action === 'lihat') {
            window.location.href = `${window.location.pathname}?conversation=${id}`;
        } else {
            confirmAction(action, id);
        }
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderAll();
    });

    document.getElementById('composeButton').addEventListener('click', () => formModal.show());
    document.getElementById('broadcastButton').addEventListener('click', () => formModal.show());
    document.getElementById('exportButton').addEventListener('click', () => showToast('Histori pesan berhasil disiapkan untuk ekspor.'));
    document.getElementById('archiveButton').addEventListener('click', () => confirmAction('arsip'));
    document.getElementById('deleteButton').addEventListener('click', () => confirmAction('hapus'));
    document.getElementById('importantButton').addEventListener('click', () => confirmAction('penting'));

    document.getElementById('replyButton').addEventListener('click', async () => {
        const reply = document.getElementById('replyInput').value.trim();
        if (!reply || !selectedId) {
            showToast('Pilih percakapan dan isi balasan terlebih dahulu.', false);
            return;
        }

        const item = histories.find((history) => history.id === selectedId);
        const formData = new FormData();
        formData.append('message', reply);
        formData.append('subject', item?.subject || item?.isi || 'Pesan');

        try {
            await postForm(`${replyUrlBase}/${selectedId}/balas`, formData);
            document.getElementById('replyInput').value = '';
            showToast('Balasan berhasil dikirim.');
            window.location.href = `${window.location.pathname}?conversation=${selectedId}`;
        } catch (error) {
            showToast(error.message, false);
        }
    });

    document.getElementById('saveForm').addEventListener('click', async () => {
        const receiver = document.getElementById('formReceiver').value;
        const [recipientType, recipientId] = receiver.split(':');
        const kind = document.getElementById('formKind').value;
        const message = document.getElementById('formMessage').value.trim();
        if (!recipientType || !recipientId || !message) {
            showToast('Penerima dan isi pesan wajib diisi.', false);
            return;
        }

        const formData = new FormData();
        formData.append('recipient_type', recipientType);
        formData.append('recipient_id', recipientId);
        formData.append('subject', kind === 'pengumuman' ? 'Broadcast' : 'Pesan Baru');
        formData.append('message', message);

        try {
            await postForm(sendUrl, formData);
            formModal.hide();
            showToast(kind === 'pengumuman' ? 'Broadcast berhasil dikirim.' : 'Pesan berhasil dikirim.');
            window.location.reload();
        } catch (error) {
            showToast(error.message, false);
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        const index = histories.findIndex((item) => item.id === pendingId);
        if (index < 0 || !pendingAction) return;
        const item = histories[index];
        (async () => {
            try {
                if (pendingAction === 'hapus') {
                    await deleteConversation(item.id);
                    selectedId = histories[0]?.id || null;
                    showToast('Pesan berhasil dihapus.');
                } else if (pendingAction === 'arsip') {
                    await patchConversationStatus(item.id, 'arsip');
                    showToast('Pesan berhasil diarsipkan.');
                } else {
                    await patchConversationStatus(item.id, 'aktif');
                    showToast('Pesan berhasil ditandai penting.');
                }
                pendingAction = null;
                pendingId = null;
                confirmModal.hide();
                window.location.reload();
            } catch (error) {
                showToast(error.message, false);
            }
        })();
    });

    populateRecipients();
    renderAll();

    if (formRole) {
        formRole.addEventListener('change', () => {
            populateRecipients();
        });
    }
</script>
@endpush
