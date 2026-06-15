@extends('pembimbing.layout.pembimbing')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@push('styles')
<style>
    .notification-page .notification-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .notification-page .stat-card,
    .notification-page .filter-card,
    .notification-page .table-card,
    .notification-page .side-panel { border:0; border-radius:8px; }
    .notification-page .stat-card { cursor:pointer; transition:.2s ease; }
    .notification-page .stat-card:hover,
    .notification-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .notification-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .notification-page .notification-item,
    .notification-page .activity-row,
    .notification-page .priority-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .notification-page .notification-item { cursor:pointer; transition:.2s ease; }
    .notification-page .notification-item:hover,
    .notification-page .notification-item.active { border-color:#2a8fbd; background:#e8f5fb; }
    .notification-page .table { font-size:14px; }
    .notification-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .notification-page .table tbody tr:hover { background:#f7fcfe; }
    .notification-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:440px; }
    .notification-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .notification-page .pagination .page-link { color:#2a8fbd; }
    .notification-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php($notificationItems = collect($notificationData ?? []))
<div class="container-fluid notification-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.komunikasi') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
        </ol>
    </nav>

    <section class="notification-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Notifikasi Sistem</h3>
                <p class="mb-0">Pantau aktivitas mahasiswa, perubahan dokumen, laporan, komunikasi, dan agenda magang agar tindak lanjut dapat dilakukan tepat waktu.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="notificationBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-bell-fill"></i>
        <div>Notifikasi pembimbing ditampilkan langsung dari database, termasuk pesan, laporan, dokumen, dan agenda penting.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-bell"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="baru">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Baru</p><h3 class="mb-0" id="statNew">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-bell-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Dibaca</p><h3 class="mb-0" id="statRead">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-priority-card="tinggi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Prioritas Tinggi</p><h3 class="mb-0" id="statHigh">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-date-card="Hari Ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar2-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-follow-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Tindak Lanjut</p><h3 class="mb-0" id="statFollow">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-arrow-repeat"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Notifikasi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari aktivitas, pengirim, atau ringkasan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Aktivitas Mahasiswa">Aktivitas Mahasiswa</option>
                        <option value="Dokumen">Dokumen</option>
                        <option value="Laporan">Laporan</option>
                        <option value="Komunikasi">Komunikasi</option>
                        <option value="Agenda">Agenda</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="priorityFilter">Prioritas</label>
                    <select class="form-select" id="priorityFilter">
                        <option value="semua">Semua Prioritas</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="sedang">Sedang</option>
                        <option value="rendah">Rendah</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Baca</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="baru">Baru</option>
                        <option value="dibaca">Dibaca</option>
                        <option value="penting">Penting</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Aktivitas</label>
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

    @if($notificationItems->isNotEmpty())
    <section class="card table-card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Notifikasi Dari Database</h5>
                    <small class="text-muted">Menampilkan notifikasi terbaru untuk pembimbing.</small>
                </div>
                <span class="badge bg-primary">{{ $notificationItems->count() }} data</span>
            </div>
            <div class="row g-3">
                @foreach($notificationItems->take(4) as $notification)
                    <div class="col-md-6 col-xl-3">
                        <div class="notification-item h-100">
                            <span class="badge bg-info text-dark mb-2">{{ $notification['jenis'] ?? 'Notifikasi' }}</span>
                            <h6 class="fw-bold mb-2">{{ $notification['title'] ?? '-' }}</h6>
                            <p class="small text-muted mb-2">{{ $notification['ringkasan'] ?? '-' }}</p>
                            <div class="d-flex justify-content-between small">
                                <span>{{ $notification['tanggal'] ?? '-' }}</span>
                                <span class="text-capitalize">{{ $notification['status'] ?? 'baru' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Daftar Notifikasi</h5>
                        <small class="text-muted" id="listInfo">Informasi aktivitas terbaru</small>
                    </div>
                    <span class="badge bg-primary" id="notificationCount">0</span>
                </div>
                <div class="card-body">
                    <div id="notificationList" class="row g-3"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card side-panel shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Aktivitas Terbaru</h5>
                    <div id="activityList"></div>
                </div>
            </div>
            <div class="card side-panel shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Prioritas Notifikasi</h5>
                    <div id="priorityList"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Tabel Notifikasi</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data notifikasi</small>
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
                            <th>Jenis Notifikasi</th>
                            <th>Pengirim</th>
                            <th>Ringkasan Aktivitas</th>
                            <th>Tanggal Aktivitas</th>
                            <th>Status Baca</th>
                            <th>Tingkat Prioritas</th>
                            <th>Status Tindak Lanjut</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="notificationTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Tidak ada notifikasi sesuai filter.</p>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination notifikasi">
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Notifikasi</h5>
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
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Status Perubahan</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan tindak lanjut notifikasi"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Notifikasi diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifications = (@json($notificationData ?? []) || []).map((item) => ({
        id: Number(item.id),
        jenis: String(item.jenis || item.category || 'Aktivitas Mahasiswa'),
        pengirim: String(item.pengirim || item.source || 'Sistem'),
        ringkasan: String(item.ringkasan || item.detail || '-'),
        tanggal: String(item.tanggal || item.time || '-'),
        status: String(item.status || 'baru'),
        prioritas: String(item.prioritas || item.priority || 'rendah').toLowerCase(),
        tindak: String(item.tindak || item.follow || 'tindak lanjut'),
        periode: String(item.periode || 'Bulan Ini'),
        detail: String(item.detail || item.ringkasan || '-'),
    }));

    const tableBody = document.getElementById('notificationTableBody');
    const notificationList = document.getElementById('notificationList');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let activeStatus = 'semua';
    let selectedId = notifications[0].id;
    let pendingAction = null;

    const statusBadge = (status) => {
        const map = { baru:'warning', dibaca:'success', penting:'danger', arsip:'secondary' };
        return `<span class="badge bg-${map[status] || 'secondary'} text-capitalize">${status}</span>`;
    };
    const priorityBadge = (priority) => {
        const map = { tinggi:'danger', sedang:'warning', rendah:'secondary' };
        return `<span class="badge bg-${map[priority] || 'secondary'} text-capitalize">${priority}</span>`;
    };
    const followBadge = (follow) => {
        const color = follow === 'selesai' ? 'success' : (follow === 'diprioritaskan' ? 'danger' : 'info');
        return `<span class="badge bg-${color} text-capitalize">${follow}</span>`;
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function filteredData() {
        const keyword = searchInput.value.toLowerCase();
        return notifications.filter((item) => {
            const keywordMatch = `${item.jenis} ${item.pengirim} ${item.ringkasan} ${item.detail}`.toLowerCase().includes(keyword);
            const categoryMatch = categoryFilter.value === 'semua' || item.jenis === categoryFilter.value;
            const priorityMatch = priorityFilter.value === 'semua' || item.prioritas === priorityFilter.value;
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const dateMatch = dateFilter.value === 'semua' || item.periode === dateFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && categoryMatch && priorityMatch && statusMatch && dateMatch && cardMatch;
        });
    }

    function renderStats() {
        const done = notifications.filter((item) => item.tindak === 'selesai').length;
        document.getElementById('statTotal').textContent = notifications.length;
        document.getElementById('statNew').textContent = notifications.filter((item) => item.status === 'baru').length;
        document.getElementById('statRead').textContent = notifications.filter((item) => item.status === 'dibaca').length;
        document.getElementById('statHigh').textContent = notifications.filter((item) => item.prioritas === 'tinggi').length;
        document.getElementById('statToday').textContent = notifications.filter((item) => item.periode === 'Hari Ini').length;
        document.getElementById('statFollow').textContent = `${Math.round((done / notifications.length) * 100)}%`;
    }

    function renderNotificationList(data) {
        document.getElementById('notificationCount').textContent = data.length;
        document.getElementById('listInfo').textContent = `Menampilkan ${data.length} notifikasi`;
        notificationList.innerHTML = data.slice(0, 4).map((item) => `
            <div class="col-md-6">
                <div class="notification-item ${item.id === selectedId ? 'active' : ''}" data-notification-id="${item.id}">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <h6 class="mb-0">${item.jenis}</h6>
                        ${priorityBadge(item.prioritas)}
                    </div>
                    <small class="text-muted d-block">${item.pengirim} - ${item.tanggal}</small>
                    <p class="mb-2 mt-2">${item.ringkasan}</p>
                    <div class="d-flex flex-wrap gap-2">${statusBadge(item.status)} ${followBadge(item.tindak)}</div>
                </div>
            </div>
        `).join('');
    }

    function renderPanels() {
        document.getElementById('activityList').innerHTML = notifications.slice(0, 5).map((item) => `
            <div class="activity-row">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.jenis}</strong>
                    ${statusBadge(item.status)}
                </div>
                <small class="text-muted d-block">${item.tanggal}</small>
                <small>${item.ringkasan}</small>
            </div>
        `).join('');

        const priorities = ['tinggi', 'sedang', 'rendah'];
        document.getElementById('priorityList').innerHTML = priorities.map((priority) => {
            const count = notifications.filter((item) => item.prioritas === priority).length;
            return `
                <div class="priority-row">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="text-capitalize">${priority}</strong>
                        ${priorityBadge(priority)}
                    </div>
                    <small>${count} notifikasi</small>
                </div>
            `;
        }).join('');
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
                <td><strong>${item.jenis}</strong></td>
                <td>${item.pengirim}</td>
                <td>${item.ringkasan}</td>
                <td>${item.tanggal}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${priorityBadge(item.prioritas)}</td>
                <td>${followBadge(item.tindak)}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="detail" data-id="${item.id}">Buka Detail</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="dibaca" data-id="${item.id}">Tandai Dibaca</button>
                        <button class="btn btn-outline-warning btn-sm" type="button" data-action="penting" data-id="${item.id}">Penting</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="arsip" data-id="${item.id}">Arsipkan</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                        <button class="btn btn-outline-info btn-sm" type="button" data-action="aktivitas" data-id="${item.id}">Aktivitas</button>
                        <button class="btn btn-outline-dark btn-sm" type="button" data-action="riwayat" data-id="${item.id}">Riwayat</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} notifikasi`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderNotificationList(data);
        renderPanels();
    }

    function openDetail(item, action = 'detail') {
        document.getElementById('detailModalLabel').textContent = action === 'riwayat' ? 'Riwayat Notifikasi' : 'Detail Notifikasi';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Jenis Notifikasi</small>
                        <h5>${item.jenis}</h5>
                        <div>${item.pengirim}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Status</small>
                        <div class="d-flex flex-wrap gap-2 mb-2">${statusBadge(item.status)} ${priorityBadge(item.prioritas)}</div>
                        <div>${followBadge(item.tindak)}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <small class="text-muted">Ringkasan Aktivitas</small>
                        <h6>${item.ringkasan}</h6>
                        <p class="mb-0">${item.detail}</p>
                    </div>
                </div>
                <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted">Tanggal Aktivitas</small><h6>${item.tanggal}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted">Periode</small><h6>${item.periode}</h6></div></div>
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
                        <strong>${item.jenis}</strong>
                        <small class="text-muted d-block">${item.pengirim} - ${item.tanggal}</small>
                    </div>
                    ${priorityBadge(item.prioritas)}
                </div>
                <hr>
                <p class="mb-2">${item.ringkasan}</p>
                <small class="text-muted">Status perubahan</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = item.detail;
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

    notificationList.addEventListener('click', (event) => {
        const itemEl = event.target.closest('[data-notification-id]');
        if (!itemEl) return;
        const item = notifications.find((notification) => notification.id === Number(itemEl.dataset.notificationId));
        selectedId = item.id;
        openDetail(item);
        renderTable();
    });

    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter notifikasi berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = 'semua';
        priorityFilter.value = 'semua';
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter notifikasi berhasil direset.', 'info');
    });
    [searchInput, categoryFilter, priorityFilter, statusFilter, dateFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = notifications.find((notification) => notification.id === Number(button.dataset.id));
        const action = button.dataset.action;
        selectedId = item.id;
        if (['detail', 'aktivitas', 'riwayat'].includes(action)) {
            openDetail(item, action);
            return;
        }
        openConfirm(item, action);
    });
    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        const { item, action } = pendingAction;
        item.detail = document.getElementById('confirmNote').value || item.detail;
        if (action === 'dibaca') item.status = 'dibaca';
        if (action === 'penting') item.status = 'penting';
        if (action === 'arsip' || action === 'hapus') item.status = 'arsip';
        if (action === 'dibaca' || action === 'penting') item.tindak = 'sedang ditindaklanjuti';
        confirmModal.hide();
        renderTable();
        openDetail(item, action);
        showToast(`Aksi ${action} berhasil disimpan.`);
        pendingAction = null;
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('notificationBadge').textContent = 'Diperbarui';
        showToast('Data notifikasi berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi sistem: 5 aktivitas baru tersedia.', 'warning'), 800);
});
</script>
@endpush
