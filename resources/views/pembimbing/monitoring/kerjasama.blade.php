@extends('pembimbing.layout.pembimbing')

@section('title', 'Kerja Sama')
@section('page-title', 'Kerja Sama')

@push('styles')
<style>
    .cooperation-monitor-page .cooperation-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .cooperation-monitor-page .stat-card,
    .cooperation-monitor-page .filter-card,
    .cooperation-monitor-page .table-card,
    .cooperation-monitor-page .side-panel { border:0; border-radius:8px; }
    .cooperation-monitor-page .stat-card { cursor:pointer; transition:.2s ease; }
    .cooperation-monitor-page .stat-card:hover,
    .cooperation-monitor-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .cooperation-monitor-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .cooperation-monitor-page .table { font-size:14px; }
    .cooperation-monitor-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .cooperation-monitor-page .table tbody tr:hover { background:#f7fcfe; }
    .cooperation-monitor-page .action-group { display:flex; flex-wrap:nowrap; gap:6px; align-items:center; white-space:nowrap; overflow-x:auto; }
    .cooperation-monitor-page .action-group .btn { flex:0 0 auto; }
    .cooperation-monitor-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .cooperation-monitor-page .pagination .page-link { color:#2a8fbd; }
    .cooperation-monitor-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
    .cooperation-monitor-page .student-pill { display:inline-flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #e2ebef; border-radius:8px; background:#fbfdfe; margin-bottom:8px; width:100%; }
</style>
@endpush

@section('content')
@php
    $cooperationRows = collect($cooperationRows ?? []);
    $cooperationStats = $cooperationStats ?? ['total' => 0, 'menunggu' => 0, 'disetujui' => 0, 'ditolak' => 0, 'revisi' => 0];
    $cooperationDocumentTypes = $cooperationRows->pluck('jenis')->filter()->unique()->sort()->values();
    $cooperationCampuses = $cooperationRows->pluck('kampus')->filter()->unique()->sort()->values();
@endphp

<div class="container-fluid cooperation-monitor-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kerja Sama</li>
        </ol>
    </nav>

    <section class="cooperation-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                    <h3 class="fw-bold mb-2">Monitoring Kerja Sama</h3>
                    <p class="mb-0">Pantau dokumen kerja sama peserta bimbingan yang tersimpan di database dengan tampilan data yang sama seperti user admin.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="cooperationBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-handshake-fill"></i>
        <div id="bannerText">Memuat dokumen kerja sama peserta bimbingan dari database...</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Dokumen</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-journal-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-status-card="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Disetujui</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-patch-check-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu</p><h3 class="mb-0" id="statPending">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-status-card="ditolak">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Ditolak</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-archive-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Cari Dokumen</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari peserta, kampus, atau dokumen">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="revisi">Revisi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="typeFilter">Jenis Dokumen</label>
                    <select class="form-select" id="typeFilter">
                        <option value="semua">Semua Jenis</option>
                        @foreach($cooperationDocumentTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="participantFilter">Perguruan Tinggi</label>
                    <select class="form-select" id="participantFilter">
                        <option value="semua">Semua PT</option>
                        @foreach($cooperationCampuses as $campus)
                            <option value="{{ $campus }}">{{ $campus }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="perPageSelect">Data per halaman</label>
                    <select class="form-select" id="perPageSelect">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="15">15</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
            <div>
                <h5 class="mb-1">Data Dokumen Kerja Sama Peserta</h5>
                <small class="text-muted" id="tableInfo">Menampilkan dokumen kerja sama peserta dari database</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-warning btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#cooperationPanel" aria-controls="cooperationPanel">
                    <i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peserta</th>
                            <th>Kampus</th>
                            <th>Jenis</th>
                            <th>Nama Dokumen</th>
                            <th>Status</th>
                            <th>Upload</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cooperationTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data kerja sama sesuai filter.</p></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination kerjasama"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="cooperationPanel" aria-labelledby="cooperationPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="cooperationPanelLabel">Panel Kerja Sama</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Ringkasan</h5>
                    <span class="badge bg-warning text-dark" id="panelCount">0</span>
                </div>
                <div id="infoPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Tindakan Cepat</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-warning text-start" type="button" data-panel-action="tambah kerja sama"><i class="bi bi-plus-circle me-2"></i> Lihat Dokumen Baru</button>
                    <button class="btn btn-outline-warning text-start" type="button" data-panel-action="ekspor data"><i class="bi bi-download me-2"></i> Export Data</button>
                    <button class="btn btn-outline-warning text-start" type="button" data-panel-action="lihat peserta aktif"><i class="bi bi-people me-2"></i> Lihat Peserta Pengunggah</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Kerja Sama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Validasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Catatan Validasi</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan validasi"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Data kerja sama diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cooperationRows = @json($cooperationRows ?? []);
    let rows = cooperationRows.map((item, index) => ({ ...item, no: index + 1 }));
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingAction = null;

    const tableBody = document.getElementById('cooperationTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const tableInfo = document.getElementById('tableInfo');
    const paginationInfo = document.getElementById('paginationInfo');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const participantFilter = document.getElementById('participantFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2500 });

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function titleCase(value) {
        return String(value || '-').split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(status) {
        return {
            disetujui: 'success',
            approved: 'success',
            aktif: 'success',
            menunggu: 'warning text-dark',
            pending: 'warning text-dark',
            ditolak: 'danger',
            rejected: 'danger',
            revisi: 'warning text-dark',
        }[status] || 'secondary';
    }

    function filteredRows() {
        const keyword = searchInput.value.trim().toLowerCase();
        return rows.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.pemilik, item.nim, item.kampus, item.jenis, item.status_label, item.catatan, item.file_name].join(' ').toLowerCase().includes(keyword);
            const matchStatus = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const matchType = typeFilter.value === 'semua' || item.jenis === typeFilter.value;
            const matchParticipant = participantFilter.value === 'semua' || item.kampus === participantFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return matchKeyword && matchStatus && matchType && matchParticipant && cardMatch;
        });
    }

    function updateBanner() {
        const newItems = rows.filter((item) => ['disetujui', 'approved', 'aktif', 'menunggu', 'pending', 'revisi'].includes(item.status)).length;
        document.getElementById('bannerText').textContent = `${newItems} dokumen kerja sama peserta dan ${rows.length} unggahan tercatat pada database.`;
    }

    function renderStats(data = rows) {
        document.getElementById('statTotal').textContent = data.length;
        document.getElementById('statActive').textContent = data.filter((item) => ['disetujui', 'approved', 'aktif'].includes(item.status)).length;
        document.getElementById('statPending').textContent = data.filter((item) => ['menunggu', 'pending'].includes(item.status)).length;
        document.getElementById('statDone').textContent = data.filter((item) => ['ditolak', 'rejected', 'nonaktif'].includes(item.status)).length;
    }

    function renderPanel(data = rows) {
        const important = data.slice(0, 4);
        document.getElementById('panelCount').textContent = important.length;
        document.getElementById('infoPanel').innerHTML = important.map((item) => `
            <div class="student-pill">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between gap-2">
                        <strong>${item.pemilik}</strong>
                        <span class="badge bg-${statusClass(item.status)} text-capitalize">${titleCase(item.status)}</span>
                    </div>
                    <small class="text-muted d-block">${item.kampus} - ${item.jenis}</small>
                    <small>${item.nama}</small>
                </div>
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
        const data = filteredRows();
        const perPage = Number(perPageSelect.value);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>
                    <strong>${item.pemilik}</strong>
                    <div class="small text-muted">${item.nim}</div>
                </td>
                <td>${item.kampus}</td>
                <td><span class="badge bg-info text-dark">${item.jenis}</span></td>
                <td>
                    <strong>${item.nama}</strong>
                    <div class="small text-muted">${item.file_name || '-'}</div>
                </td>
                <td><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status_label}</span></td>
                <td>${item.uploaded_at}</td>
                <td>${item.catatan}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                        ${item.download_url ? `<a class="btn btn-outline-primary btn-sm" href="${item.download_url}" target="_blank" rel="noopener">Unduh</a>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} dokumen kerja sama ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats(data);
        renderPanel(data);
        updateBanner();
    }

    function openDetail(item) {
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Peserta</small><h5 class="mb-0">${item.pemilik}</h5><div class="small text-muted">${item.nim}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Jenis Dokumen</small><div class="mt-2"><span class="badge bg-info text-dark">${item.jenis}</span></div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Status</small><div class="mt-2"><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status_label}</span></div></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Kampus</small><h6 class="mb-0">${item.kampus}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Nama Dokumen</small><h6 class="mb-0">${item.nama}</h6></div></div>
                <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">Upload</small><h6 class="mb-0">${item.uploaded_at}</h6></div></div>
                <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">Ukuran File</small><h6 class="mb-0">${item.size}</h6></div></div>
                <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">File</small><h6 class="mb-0">${item.file_name || '-'}</h6></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan</small><p class="mb-0">${item.catatan}</p></div></div>
                <div class="col-12">
                    <div class="border rounded p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <small class="text-muted">Tindakan</small>
                            <div class="fw-semibold">Dokumen kerja sama peserta bimbingan</div>
                        </div>
                        ${item.download_url ? `<a href="${item.download_url}" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">Unduh</a>` : ''}
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item) {
        pendingAction = item;
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <strong>${item.pemilik}</strong>
                <small class="text-muted d-block">${item.kampus} - ${item.jenis}</small>
                <hr>
                <small class="text-muted">Tindakan dokumen</small>
                <h6 class="mb-0">Dokumen kerja sama peserta</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = item.catatan;
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            activeStatus = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
        showToast('Filter dokumen kerja sama berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = 'semua';
        typeFilter.value = 'semua';
        participantFilter.value = 'semua';
        activeStatus = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter dokumen kerja sama berhasil direset.', 'info');
    });

    perPageSelect.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderTable();
        }
    });

    [statusFilter, typeFilter, participantFilter].forEach((input) => input.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    }));

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = rows.find((row) => row.id === Number(button.dataset.id));
        if (!item) return;

        if (button.dataset.action === 'detail') {
            openDetail(item);
            return;
        }

        if (button.dataset.action === 'download' && item.download_url) {
            window.open(item.download_url, '_blank', 'noopener');
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.catatan = document.getElementById('confirmNote').value || pendingAction.catatan;
        pendingAction.status = 'disetujui';
        pendingAction.status_label = 'Disetujui';
        pendingAction.status_class = 'success';
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction);
        showToast(`Dokumen "${pendingAction.nama}" berhasil diperbarui.`);
        pendingAction = null;
    });

    document.querySelectorAll('[data-panel-action]').forEach((button) => {
        button.addEventListener('click', () => {
            const action = button.dataset.panelAction;
            const messages = {
                'tambah kerja sama': 'Daftar dokumen kerja sama terbaru dibuka.',
                'ekspor data': 'Data dokumen kerja sama disiapkan untuk ekspor.',
                'lihat peserta aktif': 'Daftar peserta pengunggah ditampilkan.',
            };
            showToast(messages[action] || 'Aksi panel dijalankan.', 'info');
        });
    });

    document.getElementById('refreshButton').addEventListener('click', () => {
        showToast('Data dokumen kerja sama berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi kerjasama: data dokumen terbaru berhasil dimuat.', 'warning'), 700);
});
</script>
@endpush
