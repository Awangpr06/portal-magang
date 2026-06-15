@extends('admin.layout.admin')

@section('title', 'Komunikasi')

@push('styles')
<style>
    .communication-page .page-title { font-weight: 700; color: #163342; }
    .communication-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .communication-page .stat-card,
    .communication-page .filter-card,
    .communication-page .communication-panel,
    .communication-page .info-panel { border: 0; border-radius: 8px; }
    .communication-page .stat-card { cursor: pointer; transition: .2s ease; }
    .communication-page .stat-card:hover,
    .communication-page .stat-card.active { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .communication-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .communication-tabs { display: flex; flex-wrap: wrap; gap: 8px; }
    .communication-tabs .btn { border-radius: 8px; }
    .communication-list { max-height: 560px; overflow: auto; }
    .communication-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 14px; margin-bottom: 10px; cursor: pointer; transition: .15s ease; background: #fff; }
    .communication-item:hover,
    .communication-item.active { border-color: #0b5f86; background: #f5fbfe; }
    .message-box { min-height: 340px; border: 1px solid #e2ebef; border-radius: 8px; padding: 18px; background: #fbfdfe; }
    .message-bubble { max-width: 78%; border-radius: 8px; padding: 10px 12px; margin-bottom: 10px; background: #eaf3f7; }
    .message-bubble.admin { margin-left: auto; background: #0b5f86; color: #fff; }
    .empty-state { min-height: 220px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
@php
    $initialTab = $activeTab ?? 'semua';
@endphp

<div class="container-fluid communication-page" data-initial-tab="{{ $initialTab }}">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Komunikasi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Komunikasi</h2>
            <p class="text-muted mb-0">Kelola pesan, pengumuman, grup komunikasi, dan notifikasi sistem secara terpusat.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="archiveButton"><i class="bi bi-archive"></i> Arsipkan</button>
            <button class="btn btn-primary" type="button" id="composeButton"><i class="bi bi-pencil-square"></i> Buat Pesan</button>
        </div>
    </div>

    <div class="communication-tabs mb-4">
        <a href="{{ route('admin.komunikasi.index') }}" class="btn {{ $initialTab === 'semua' ? 'btn-primary' : 'btn-outline-primary' }}">Semua</a>
        <a href="{{ route('admin.komunikasi.pesan') }}" class="btn {{ $initialTab === 'pesan' ? 'btn-primary' : 'btn-outline-primary' }}">Pesan</a>
        <a href="{{ route('admin.komunikasi.pengumuman') }}" class="btn {{ $initialTab === 'pengumuman' ? 'btn-primary' : 'btn-outline-primary' }}">Pengumuman</a>
        <a href="{{ route('admin.komunikasi.notifikasi') }}" class="btn {{ $initialTab === 'notifikasi' ? 'btn-primary' : 'btn-outline-primary' }}">Notifikasi</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-filter-card="all">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Percakapan</p><h3 class="mb-0" id="statPesan">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-chat-left-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-filter-card="unread">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statPengumuman">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-megaphone"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-filter-card="active">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Percakapan Aktif</p><h3 class="mb-0" id="statGrup">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-people"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-filter-card="today">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pesan Hari Ini</p><h3 class="mb-0" id="statNotifikasi">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-bell"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="searchInput" class="form-label">Pencarian Global</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari percakapan, pengumuman, grup, atau notifikasi">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="categoryFilter" class="form-label">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua</option>
                        <option value="pesan">Pesan</option>
                        <option value="pengumuman">Pengumuman</option>
                        <option value="grup">Grup</option>
                        <option value="notifikasi">Notifikasi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="timeFilter" class="form-label">Waktu</label>
                    <select class="form-select" id="timeFilter">
                        <option value="semua">Semua Waktu</option>
                        <option value="hari ini">Hari Ini</option>
                        <option value="minggu ini">Minggu Ini</option>
                        <option value="bulan ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="baru">Baru</option>
                        <option value="aktif">Aktif</option>
                        <option value="terkirim">Terkirim</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="searchButton"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card communication-panel shadow-sm">
                <div class="card-body">
                    <h5 class="mb-1">Daftar Komunikasi</h5>
                    <p class="text-muted mb-3" id="listSummary">Menampilkan 0 data</p>
                    <div class="communication-list" id="communicationList"></div>
                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data komunikasi tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted small" id="pageInfo">0 data</span>
                        <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card communication-panel shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h5 class="mb-1" id="messageTitle">Pilih komunikasi</h5>
                            <p class="text-muted mb-0" id="messageMeta">Detail pesan akan tampil di sini.</p>
                        </div>
                        <span class="badge bg-secondary" id="messageBadge">-</span>
                    </div>

                    <div class="message-box" id="messageBox">
                        <div class="empty-state">
                            <div>
                                <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                                <h6 class="mb-1">Belum ada komunikasi dipilih</h6>
                                <p class="mb-0">Pilih salah satu data di daftar komunikasi.</p>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mt-3">
                        <input class="form-control" id="replyInput" placeholder="Tulis balasan administratif">
                        <button class="btn btn-primary" type="button" id="sendReply">Kirim</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card info-panel shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Informasi</h5>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Kontak Terkait</p>
                        <h6 class="mb-0" id="infoContact">-</h6>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Role Kontak</p>
                        <h6 class="mb-0" id="infoCategory">-</h6>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Terakhir Diperbarui</p>
                        <h6 class="mb-0" id="infoTime">-</h6>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" type="button" id="validateButton">Validasi Komunikasi</button>
                        <button class="btn btn-outline-secondary" type="button" id="archiveSelected">Arsipkan</button>
                        <button class="btn btn-outline-danger" type="button" id="deleteSelected">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Buat Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
    <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="formCategory">Kategori</label>
                        <select class="form-select" id="formCategory">
                            <option value="pesan">Pesan</option>
                            <option value="pengumuman">Pengumuman</option>
                            <option value="grup">Grup</option>
                            <option value="notifikasi">Notifikasi</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formContact">Penerima/Grup</label>
                        <input class="form-control" id="formContact" placeholder="Nama penerima atau grup">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formSubject">Subjek</label>
                        <input class="form-control" id="formSubject" placeholder="Subjek komunikasi">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formMessage">Isi Pesan</label>
                        <textarea class="form-control" id="formMessage" rows="4" placeholder="Tulis isi komunikasi"></textarea>
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
    <div id="communicationToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const contactOptions = Array.isArray(communicationData.contacts) ? communicationData.contacts : [];
    const sourceConversations = Array.isArray(communicationData.conversations) ? communicationData.conversations : [];

    const stats = communicationData.stats || {};
    const communications = sourceConversations.map((conversation) => {
        const thread = Array.isArray(conversation.messages) ? conversation.messages : [];
        const latestMessage = thread[thread.length - 1] || {};
        const unreadCount = Number(conversation.unread_count || 0);
        const label = conversation.last_message_label || 'Baru';

        return {
            id: conversation.id,
            category: 'pesan',
            contact: conversation.contact_name || 'Kontak',
            contact_role: conversation.contact_role || 'Kontak',
            subject: conversation.subject || conversation.last_message || 'Percakapan',
            preview: conversation.last_message || latestMessage.text || 'Belum ada pesan',
            time: label.toLowerCase().includes('hari') ? 'hari ini' : (label.toLowerCase().includes('minggu') ? 'minggu ini' : 'bulan ini'),
            status: unreadCount > 0 ? 'baru' : (conversation.status || 'aktif'),
            thread,
            recipient_type: conversation.recipient_type,
            recipient_id: conversation.recipient_id,
        };
    });

    const perPage = 6;
    let currentPage = 1;
    let selectedId = communications[0]?.id ?? null;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const timeFilter = document.getElementById('timeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const communicationList = document.getElementById('communicationList');
    const emptyState = document.getElementById('emptyState');
    const listSummary = document.getElementById('listSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('communicationToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function categoryClass(category) {
        return { pesan: 'bg-primary', pengumuman: 'bg-success', grup: 'bg-info text-dark', notifikasi: 'bg-warning text-dark' }[category];
    }

    function filteredCommunications() {
        const keyword = searchInput.value.trim().toLowerCase();
        const category = categoryFilter.value;
        const time = timeFilter.value;
        const status = statusFilter.value;

        return communications.filter((item) => {
            const matchKeyword = !keyword || [item.contact, item.subject, item.preview, item.category, item.status].join(' ').toLowerCase().includes(keyword);
            const matchCategory = category === 'semua' || item.category === category;
            const matchTime = time === 'semua' || item.time === time;
            const matchStatus = status === 'semua' || item.status === status;
            return matchKeyword && matchCategory && matchTime && matchStatus;
        });
    }

    function updateStats() {
        document.getElementById('statPesan').textContent = Number(stats.total ?? communications.length);
        document.getElementById('statPengumuman').textContent = Number(stats.unread ?? communications.filter((item) => item.status === 'baru').length);
        document.getElementById('statGrup').textContent = Number(stats.active ?? communications.filter((item) => item.status === 'aktif').length);
        document.getElementById('statNotifikasi').textContent = Number(stats.today ?? communications.filter((item) => item.time === 'hari ini').length);
    }

    function renderList() {
        const data = filteredCommunications();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        communicationList.innerHTML = pageData.map((item) => `
            <div class="communication-item ${item.id === selectedId ? 'active' : ''}" role="button" tabindex="0" data-id="${item.id}">
                <div class="d-flex justify-content-between gap-2 mb-2">
                    <span class="badge ${categoryClass(item.category)}">${titleCase(item.category)}</span>
                    <small class="text-muted">${titleCase(item.time)}</small>
                </div>
                <h6 class="mb-1">${item.subject}</h6>
                <p class="text-muted small mb-2">${item.preview}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="small">${item.contact}</strong>
                    <span class="badge bg-light text-dark">${titleCase(item.status)}</span>
                </div>
            </div>
        `).join('');

        communicationList.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        listSummary.textContent = `${data.length} komunikasi ditemukan`;
        pageInfo.textContent = data.length ? `${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length}` : '0 data';
        renderPagination(totalPages);

        if (!selectedId && pageData.length) {
            selectCommunication(pageData[0].id);
        }
    }

    function renderPagination(totalPages) {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" type="button" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderConversationPanel(item) {
        if (!item) {
            document.getElementById('messageTitle').textContent = 'Pilih komunikasi';
            document.getElementById('messageMeta').textContent = 'Detail pesan akan tampil di sini.';
            document.getElementById('messageBadge').textContent = '-';
            document.getElementById('messageBadge').className = 'badge bg-secondary';
            document.getElementById('infoContact').textContent = '-';
            document.getElementById('infoCategory').textContent = '-';
            document.getElementById('infoTime').textContent = '-';
            document.getElementById('messageBox').innerHTML = `
                <div class="empty-state">
                    <div>
                        <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                        <h6 class="mb-1">Belum ada komunikasi dipilih</h6>
                        <p class="mb-0">Pilih salah satu data di daftar komunikasi.</p>
                    </div>
                </div>
            `;
            return;
        }

        document.getElementById('messageTitle').textContent = item.subject;
        document.getElementById('messageMeta').textContent = `${item.contact} • ${titleCase(item.time)}`;
        document.getElementById('messageBadge').textContent = titleCase(item.category);
        document.getElementById('messageBadge').className = `badge ${categoryClass(item.category)}`;
        document.getElementById('infoContact').textContent = item.contact;
        document.getElementById('infoCategory').textContent = item.contact_role;
        document.getElementById('infoTime').textContent = titleCase(item.time);
        document.getElementById('messageBox').innerHTML = (item.thread || []).map((message) => `
            <div class="message-bubble ${message.direction === 'out' ? 'admin' : ''}">
                <div class="small fw-semibold mb-1">${message.direction === 'out' ? 'Super Admin' : item.contact}</div>
                <div>${escapeHtml(message.text || '')}</div>
            </div>
        `).join('') || `
            <div class="empty-state">
                <div>
                    <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Belum ada pesan</h6>
                    <p class="mb-0">Percakapan ini belum memiliki isi pesan.</p>
                </div>
            </div>
        `;
    }

    function selectCommunication(id) {
        selectedId = id;
        const item = communications.find((communication) => communication.id === id);
        document.getElementById('messageTitle').textContent = item.subject;
        document.getElementById('messageMeta').textContent = `${item.contact} • ${titleCase(item.time)}`;
        document.getElementById('messageBadge').textContent = titleCase(item.category);
        document.getElementById('messageBadge').className = `badge ${categoryClass(item.category)}`;
        document.getElementById('infoContact').textContent = item.contact;
        document.getElementById('infoCategory').textContent = item.contact_role;
        document.getElementById('infoTime').textContent = titleCase(item.time);
        document.getElementById('messageBox').innerHTML = (item.thread || []).map((message) => `
            <div class="message-bubble ${message.direction === 'out' ? 'admin' : ''}">
                <div class="small fw-semibold mb-1">${message.direction === 'out' ? 'Super Admin' : item.contact}</div>
                <div>${escapeHtml(message.text || '')}</div>
            </div>
        `).join('');
        renderConversationPanel(item);
        renderList();
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('communicationToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showConfirm(action) {
        if (!selectedId) {
            showToast('Pilih data komunikasi terlebih dahulu.', false);
            return;
        }

        const item = communications.find((communication) => communication.id === selectedId);
        pendingAction = action;
        const labels = {
            arsip: ['Konfirmasi Arsip Komunikasi', `Arsipkan komunikasi "${item.subject}"?`],
            hapus: ['Konfirmasi Hapus Komunikasi', `Hapus komunikasi "${item.subject}"?`],
            validasi: ['Konfirmasi Validasi Komunikasi', `Validasi komunikasi "${item.subject}"?`]
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
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
                // ignore
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

    document.querySelectorAll('[data-filter-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-filter-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            const mode = card.dataset.filterCard;
            categoryFilter.value = 'semua';
            timeFilter.value = 'semua';
            statusFilter.value = 'semua';
            if (mode === 'unread') statusFilter.value = 'baru';
            if (mode === 'active') statusFilter.value = 'aktif';
            if (mode === 'today') timeFilter.value = 'hari ini';
            selectedId = null;
            currentPage = 1;
            renderList();
        });
    });

    [categoryFilter, timeFilter, statusFilter].forEach((input) => {
        input.addEventListener('change', () => {
            selectedId = null;
            currentPage = 1;
            renderList();
        });
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            selectedId = null;
            currentPage = 1;
            renderList();
        }
    });

    document.getElementById('searchButton').addEventListener('click', () => {
        selectedId = null;
        currentPage = 1;
        renderList();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = 'semua';
        timeFilter.value = 'semua';
        statusFilter.value = 'semua';
        selectedId = null;
        currentPage = 1;
        renderList();
    });

    communicationList.addEventListener('click', (event) => {
        const item = event.target.closest('[data-id]');
        if (item) {
            selectCommunication(Number(item.dataset.id));
        }
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderList();
    });

    document.getElementById('composeButton').addEventListener('click', () => formModal.show());
    document.getElementById('archiveButton').addEventListener('click', () => showConfirm('arsip'));
    document.getElementById('archiveSelected').addEventListener('click', () => showConfirm('arsip'));
    document.getElementById('deleteSelected').addEventListener('click', () => showConfirm('hapus'));
    document.getElementById('validateButton').addEventListener('click', () => showConfirm('validasi'));

    document.getElementById('sendReply').addEventListener('click', async () => {
        const reply = document.getElementById('replyInput').value.trim();
        if (!selectedId || !reply) {
            showToast('Pilih komunikasi dan isi balasan terlebih dahulu.', false);
            return;
        }

        const item = communications.find((communication) => communication.id === selectedId);
        const formData = new FormData();
        formData.append('message', reply);
        formData.append('subject', item?.subject || item?.contact || 'Pesan');

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
        const category = document.getElementById('formCategory').value;
        const contact = document.getElementById('formContact').value.trim();
        const subject = document.getElementById('formSubject').value.trim();
        const message = document.getElementById('formMessage').value.trim();

        if (!contact || !subject || !message) {
            showToast('Penerima, subjek, dan isi pesan wajib diisi.', false);
            return;
        }

        const matchedContact = contactOptions.find((item) => item.name.toLowerCase() === contact.toLowerCase());

        if (!matchedContact) {
            showToast('Kontak tujuan tidak ditemukan di database.', false);
            return;
        }

        const formData = new FormData();
        formData.append('recipient_type', matchedContact.recipient_type);
        formData.append('recipient_id', matchedContact.recipient_id);
        formData.append('subject', subject);
        formData.append('message', message);

        try {
            await postForm(sendUrl, formData);
            formModal.hide();
            showToast('Komunikasi baru berhasil dikirim.');
            window.location.reload();
        } catch (error) {
            showToast(error.message, false);
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        const index = communications.findIndex((communication) => communication.id === selectedId);
        if (index < 0 || !pendingAction) return;

        const item = communications[index];
        (async () => {
            try {
                if (pendingAction === 'hapus') {
                    await deleteConversation(item.id);
                    selectedId = null;
                    showToast(`Komunikasi "${item.subject}" berhasil dihapus.`);
                } else if (pendingAction === 'arsip') {
                    await patchConversationStatus(item.id, 'arsip');
                    showToast(`Komunikasi "${item.subject}" berhasil diarsipkan.`);
                } else {
                    await patchConversationStatus(item.id, 'aktif');
                    showToast(`Komunikasi "${item.subject}" berhasil divalidasi.`);
                }

                confirmModal.hide();
                pendingAction = null;
                window.location.reload();
            } catch (error) {
                showToast(error.message, false);
            }
        })();
    });

    updateStats();
    renderList();
    if (selectedId) {
        renderConversationPanel(communications.find((communication) => communication.id === selectedId));
    }
    if (selectedId) {
        const initialConversation = communications.find((communication) => communication.id === selectedId);
        if (initialConversation) {
            document.getElementById('messageTitle').textContent = initialConversation.subject;
            document.getElementById('messageMeta').textContent = `${initialConversation.contact} â€¢ ${titleCase(initialConversation.time)}`;
            document.getElementById('messageBadge').textContent = titleCase(initialConversation.category);
            document.getElementById('messageBadge').className = `badge ${categoryClass(initialConversation.category)}`;
            document.getElementById('infoContact').textContent = initialConversation.contact;
            document.getElementById('infoCategory').textContent = initialConversation.contact_role;
            document.getElementById('infoTime').textContent = titleCase(initialConversation.time);
            document.getElementById('messageBox').innerHTML = (initialConversation.thread || []).map((message) => `
                <div class="message-bubble ${message.direction === 'out' ? 'admin' : ''}">
                    <div class="small fw-semibold mb-1">${message.direction === 'out' ? 'Super Admin' : initialConversation.contact}</div>
                    <div>${escapeHtml(message.text || '')}</div>
                </div>
            `).join('');
        }
    }
</script>
@endpush
