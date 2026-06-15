@extends('pembimbing.layout.pembimbing')

@section('title', 'Komunikasi')
@section('page-title', 'Komunikasi')

@push('styles')
<style>
    .communication-page .communication-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .communication-page .stat-card,
    .communication-page .filter-card,
    .communication-page .table-card,
    .communication-page .side-panel,
    .communication-page .chat-panel { border:0; border-radius:8px; }
    .communication-page .stat-card { cursor:pointer; transition:.2s ease; }
    .communication-page .stat-card:hover,
    .communication-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .communication-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .communication-page .contact-item { border:1px solid #e2ebef; border-radius:8px; padding:12px; cursor:pointer; transition:.2s ease; background:#fbfdfe; }
    .communication-page .contact-item:hover,
    .communication-page .contact-item.active { border-color:#2a8fbd; background:#e8f5fb; }
    .communication-page .avatar { width:42px; height:42px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:700; color:#fff; background:#2a8fbd; flex:0 0 auto; }
    .communication-page .message-box { min-height:300px; max-height:420px; overflow:auto; background:#f7fcfe; border-radius:8px; padding:16px; }
    .communication-page .message-bubble { max-width:78%; padding:10px 12px; border-radius:8px; margin-bottom:10px; }
    .communication-page .message-bubble.in { background:#fff; border:1px solid #e2ebef; }
    .communication-page .message-bubble.out { background:#2a8fbd; color:#fff; margin-left:auto; }
    .communication-page .table { font-size:14px; }
    .communication-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .communication-page .table tbody tr:hover { background:#f7fcfe; }
    .communication-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:430px; }
    .communication-page .activity-row,
    .communication-page .info-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .communication-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .communication-page .pagination .page-link { color:#2a8fbd; }
    .communication-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $mode = $mode ?? 'utama';
    $pageTitle = match ($mode) {
        'pesan' => 'Pesan',
        'pengumuman' => 'Pengumuman',
        'notifikasi' => 'Notifikasi',
        default => 'Komunikasi'
    };
@endphp

<div class="container-fluid communication-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
        </ol>
    </nav>

    <section class="communication-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">{{ $pageTitle }} Pembimbing</h3>
                <p class="mb-0">Kelola percakapan, koordinasi, pengumuman, dan notifikasi mahasiswa magang secara terintegrasi dan terdokumentasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="communicationBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-chat-dots-fill"></i>
        <div>{{ ($summaryStats['unread'] ?? 0) }} pesan belum dibaca, {{ ($summaryStats['announcement'] ?? 0) }} pengumuman aktif, dan {{ ($summaryStats['today'] ?? 0) }} percakapan aktif hari ini.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Percakapan</p><h3 class="mb-0" id="statConversation">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-chat-square-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-category-card="Hari Ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pesan Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-send-check"></i></span>
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
                    <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-type-card="pengumuman">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pengumuman</p><h3 class="mb-0" id="statAnnouncement">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-megaphone"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-category-card="Terakhir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktivitas</p><h3 class="mb-0" id="statLatest">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-clock-history"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari pesan, topik, atau nama">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="studentFilter">Mahasiswa</label>
                    <select class="form-select" id="studentFilter">
                        <option value="semua">Semua Mahasiswa</option>
                        @foreach (($contactOptions ?? []) as $contactName)
                            <option value="{{ $contactName }}">{{ $contactName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori Pesan</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Konsultasi">Konsultasi</option>
                        <option value="Koordinasi">Koordinasi</option>
                        <option value="Pengumuman">Pengumuman</option>
                        <option value="Tindak Lanjut">Tindak Lanjut</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Baca</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="belum dibaca">Belum Dibaca</option>
                        <option value="dibaca">Dibaca</option>
                        <option value="aktif">Aktif</option>
                        <option value="arsip">Arsip</option>
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
        <div class="col-xl-9">
            <div class="card chat-panel shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0" id="chatTitle">Percakapan Aktif</h5>
                        <small class="text-muted" id="chatSubtitle">Pilih kontak untuk membuka pesan</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" type="button" id="newMessageButton"><i class="bi bi-pencil-square"></i> Pesan Baru</button>
                </div>
                <div class="card-body">
                    <div class="message-box" id="messageBox"></div>
                    <div class="input-group mt-3">
                        <input class="form-control" id="replyInput" type="text" placeholder="Tulis pesan balasan">
                        <button class="btn btn-primary" type="button" id="sendReplyButton"><i class="bi bi-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card side-panel shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Informasi Pengguna</h5>
                    <div id="userInfo"></div>
                    <hr>
                    <h6>Aktivitas Terbaru</h6>
                    <div id="latestActivity"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Daftar Komunikasi</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data komunikasi</small>
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
                            <th>Topik</th>
                            <th>Isi Singkat</th>
                            <th>Tanggal Kirim</th>
                            <th>Status Pesan</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="communicationTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Tidak ada komunikasi sesuai filter.</p>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination komunikasi">
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Komunikasi</h5>
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
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Komunikasi</h5>
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

<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="composeModalLabel">Pesan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="composeRecipient">Peserta Bimbingan</label>
                    <select class="form-select" id="composeRecipient">
                        <option value="">Pilih peserta</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="composeSubject">Subjek</label>
                    <input class="form-control" id="composeSubject" type="text" placeholder="Tulis subjek pesan">
                </div>
                <div class="mb-0">
                    <label class="form-label" for="composeBody">Isi Pesan</label>
                    <textarea class="form-control" id="composeBody" rows="4" placeholder="Tulis pesan untuk peserta"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="sendComposeButton">Kirim</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Komunikasi diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const communications = @json($communicationsData ?? []);
    const messageMap = @json($messageMap ?? []);
    const studentContacts = @json($studentContacts ?? []);
    const replyRouteTemplate = @json(route('pembimbing.komunikasi.pesan.reply', ['conversation' => '__CONVERSATION__']));
    const sendUrl = @json(route('komunikasi.send'));
    const currentUserName = @json(auth()->user()->name);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const tableBody = document.getElementById('communicationTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const studentFilter = document.getElementById('studentFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const composeModal = new bootstrap.Modal(document.getElementById('composeModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let activeStatus = 'semua';
    let selectedId = @json($activeCommunicationId ?? null) || (communications[0] ? communications[0].id : null);
    let pendingAction = null;

    const statusBadge = (status) => {
        const map = {
            'belum dibaca':'warning',
            dibaca:'success',
            aktif:'primary',
            arsip:'secondary'
        };
        return `<span class="badge bg-${map[status] || 'secondary'} text-capitalize">${status}</span>`;
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function populateComposeRecipients() {
        const select = document.getElementById('composeRecipient');
        if (!select) return;

        const options = studentContacts.map((student) => `
            <option value="${student.recipient_id}">${student.label}</option>
        `).join('');

        select.innerHTML = '<option value="">Pilih peserta</option>' + options;
    }

    async function postComposeMessage(formData) {
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
        return communications.filter((item) => {
            const keywordMatch = `${item.pengirim} ${item.penerima} ${item.topik} ${item.isi} ${item.kategori}`.toLowerCase().includes(keyword);
            const studentMatch = studentFilter.value === 'semua' || item.pengirim === studentFilter.value || item.penerima === studentFilter.value;
            const categoryMatch = categoryFilter.value === 'semua' || item.kategori === categoryFilter.value;
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const dateMatch = dateFilter.value === 'semua' || item.tanggalKategori === dateFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && studentMatch && categoryMatch && statusMatch && dateMatch && cardMatch;
        });
    }

    function getThread(item) {
        return messageMap[item.id] || item.thread || [];
    }

    function syncItem(itemId, patch = {}) {
        const item = communications.find((communication) => communication.id === itemId);
        if (!item) return null;
        Object.assign(item, patch);
        return item;
    }

    async function sendReply(item, content) {
        const text = content.trim();
        if (!item || !text) {
            showToast('Pilih percakapan dan isi pesan terlebih dahulu.', 'danger');
            return false;
        }

        const response = await fetch(replyRouteTemplate.replace('__CONVERSATION__', item.conversation_id || item.id), {
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
            throw new Error(payload.message || 'Pesan gagal dikirim.');
        }

        const payload = await response.json();
        const thread = messageMap[item.id] || [];
        thread.push(payload.latest_message);
        messageMap[item.id] = thread;
        syncItem(item.id, {
            pengirim: @json(auth()->user()->name),
            status: 'dibaca',
            pengirim: item.partner_name || item.pengirim,
            penerima: currentUserName,
            partner_name: item.partner_name || item.pengirim,
            aktivitas: 'Baru saja',
            isi: payload.latest_message.text,
            tanggal: payload.conversation.last_message_at || item.tanggal,
            topik: payload.conversation.topik || item.topik,
            tanggalKategori: 'Hari Ini',
            unread_count: 0,
        });
        return true;
    }

    function renderStats() {
        document.getElementById('statConversation').textContent = communications.length;
        document.getElementById('statToday').textContent = communications.filter((item) => item.tanggalKategori === 'Hari Ini').length;
        document.getElementById('statUnread').textContent = communications.filter((item) => item.status === 'belum dibaca' || Number(item.unread_count || 0) > 0).length;
        document.getElementById('statActive').textContent = communications.filter((item) => item.aktif).length;
        document.getElementById('statAnnouncement').textContent = @json(($summaryStats['announcement'] ?? 0));
        document.getElementById('statLatest').textContent = communications.filter((item) => item.tanggalKategori === 'Hari Ini').length;
    }

    function renderChat(item) {
        if (!item) {
            document.getElementById('chatTitle').textContent = 'Percakapan Aktif';
            document.getElementById('chatSubtitle').textContent = 'Pilih kontak untuk membuka pesan';
            document.getElementById('messageBox').innerHTML = '';
            document.getElementById('userInfo').innerHTML = '<div class="text-muted">Belum ada percakapan.</div>';
            return;
        }
        selectedId = item.id;
        document.getElementById('chatTitle').textContent = item.penerima === @json(auth()->user()->name) ? item.pengirim : item.penerima;
        document.getElementById('chatSubtitle').textContent = `${item.topik} - ${item.status}`;
        document.getElementById('chatTitle').textContent = item.partner_name || item.pengirim || item.penerima;
        const messages = getThread(item);
        document.getElementById('messageBox').innerHTML = messages.map((message) => `
            <div class="message-bubble ${message.direction}">
                <div>${message.text}</div>
                <small class="${message.direction === 'out' ? 'text-white-50' : 'text-muted'}">${message.time}</small>
            </div>
        `).join('');
        document.getElementById('userInfo').innerHTML = `
            <div class="info-row">
                <small class="text-muted">Penerima/Pengirim</small>
                <h6 class="mb-1">${item.penerima === @json(auth()->user()->name) ? item.pengirim : item.penerima}</h6>
                <div>${item.kategori}</div>
                <div class="mt-2">${statusBadge(item.status)}</div>
            </div>
            <div class="info-row">
                <small class="text-muted">Topik</small>
                <p class="mb-0">${item.topik}</p>
            </div>
        `;
        document.getElementById('userInfo').innerHTML = `
            <div class="info-row">
                <small class="text-muted">Penerima/Pengirim</small>
                <h6 class="mb-1">${item.partner_name || item.pengirim || item.penerima}</h6>
                <div>${item.kategori}</div>
                <div class="mt-2">${statusBadge(item.status)}</div>
            </div>
            <div class="info-row">
                <small class="text-muted">Topik</small>
                <p class="mb-0">${item.topik}</p>
            </div>
        `;
    }

    function renderLatestActivity() {
        document.getElementById('latestActivity').innerHTML = communications.slice(0, 4).map((item) => `
            <div class="activity-row">
                <strong>${item.topik}</strong>
                <small class="text-muted d-block">${item.aktivitas}</small>
            </div>
        `).join('');
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button>
            </li>
        `;
        for (let page = 1; page <= totalPages; page++) {
            html += `
                <li class="page-item ${page === currentPage ? 'active' : ''}">
                    <button class="page-link" data-page="${page}" type="button">${page}</button>
                </li>
            `;
        }
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" data-page="${currentPage + 1}" type="button">Next</button>
            </li>
        `;
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
                <td><strong>${item.topik}</strong><small class="text-muted d-block">${item.kategori}</small></td>
                <td>${item.isi}</td>
                <td>${item.tanggal}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="kirim pesan" data-id="${item.id}">Kirim Pesan</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="balas" data-id="${item.id}">Balas</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="tandai dibaca" data-id="${item.id}">Dibaca</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="arsip" data-id="${item.id}">Arsipkan</button>
                        <button class="btn btn-outline-dark btn-sm" type="button" data-action="riwayat" data-id="${item.id}">Riwayat</button>
                    </div>
                </td>
            </tr>
        `).join('');

        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} komunikasi`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderLatestActivity();
        renderChat(communications.find((item) => item.id === selectedId) || communications[0] || null);
    }

    function openDetail(item, action = 'detail') {
        document.getElementById('detailModalLabel').textContent = action === 'riwayat' ? 'Riwayat Komunikasi' : 'Detail Komunikasi';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Pengirim</small>
                        <h5 class="mb-1">${item.pengirim}</h5>
                        <div>${item.tanggal}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Penerima</small>
                        <h5 class="mb-1">${item.penerima}</h5>
                        <div>${statusBadge(item.status)}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <small class="text-muted">Topik</small>
                        <h5>${item.topik}</h5>
                        <p class="mb-0">${item.isi}</p>
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <strong>${item.topik}</strong>
                        <small class="text-muted d-block">${item.pengirim} ke ${item.penerima}</small>
                    </div>
                    ${statusBadge(item.status)}
                </div>
                <hr>
                <small class="text-muted">Jenis Tindakan</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmMessage').value = item.isi;
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

    document.getElementById('sendReplyButton').addEventListener('click', async () => {
        const item = communications.find((communication) => communication.id === selectedId);
        const input = document.getElementById('replyInput');
        if (!item || !input.value.trim()) {
            showToast('Pilih percakapan dan isi pesan terlebih dahulu.', 'danger');
            return;
        }
        try {
            const success = await sendReply(item, input.value);
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
        populateComposeRecipients();
        document.getElementById('composeSubject').value = '';
        document.getElementById('composeBody').value = '';
        composeModal.show();
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
        showToast('Filter komunikasi berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studentFilter.value = 'semua';
        categoryFilter.value = 'semua';
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter komunikasi berhasil direset.', 'info');
    });

    [searchInput, studentFilter, categoryFilter, statusFilter, dateFilter].forEach((element) => {
        element.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });
    });

    searchInput.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') {
            currentPage = 1;
            renderTable();
        }
    });

    perPageSelect.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = communications.find((communication) => communication.id === Number(button.dataset.id));
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
        openConfirm(item, action);
    });

    document.getElementById('confirmAction').addEventListener('click', async () => {
        if (!pendingAction) return;
        const { item, action } = pendingAction;
        const message = document.getElementById('confirmMessage').value.trim();
        try {
            if (['balas', 'kirim pesan'].includes(action)) {
                await sendReply(item, message);
                showToast('Pesan berhasil dikirim.');
            } else {
                if (action === 'tandai dibaca') item.status = 'dibaca';
                if (action === 'arsip') item.status = 'arsip';
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

    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('communicationBadge').textContent = 'Diperbarui';
        showToast('Data komunikasi berhasil diperbarui.', 'info');
    });

    document.getElementById('sendComposeButton').addEventListener('click', async () => {
        const recipientId = document.getElementById('composeRecipient').value;
        const subject = document.getElementById('composeSubject').value.trim();
        const body = document.getElementById('composeBody').value.trim();
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
            await postComposeMessage(formData);
            composeModal.hide();
            showToast('Pesan baru berhasil dikirim.');
            window.location.reload();
        } catch (error) {
            showToast(error.message || 'Pesan gagal dikirim.', 'danger');
        }
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi komunikasi: 5 pesan belum dibaca.', 'warning'), 800);
});
</script>
@endpush
