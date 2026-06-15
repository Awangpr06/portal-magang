@extends('mentor.layout.mentor')

@section('title', 'Status Magang')
@section('page-title', 'Status Magang')

@push('styles')
<style>
    .internship-status-page .status-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .internship-status-page .stat-card,
    .internship-status-page .filter-card,
    .internship-status-page .table-card,
    .internship-status-page .panel-card { border:0; border-radius:8px; }
    .internship-status-page .stat-card { cursor:pointer; transition:.2s ease; }
    .internship-status-page .stat-card:hover,
    .internship-status-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .internship-status-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .internship-status-page .progress { height:8px; background:#e8eef2; }
    .internship-status-page .progress-bar { background:#2a8fbd; }
    .internship-status-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:520px; }
    .internship-status-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .internship-status-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .internship-status-page .pagination .page-link { color:#2a8fbd; }
    .internship-status-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid internship-status-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.monitoring') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Status Magang</li>
        </ol>
    </nav>

    <section class="status-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Status Magang</h3>
                <p class="mb-0">Pantau status pelaksanaan magang peserta yang dihitung dari periode magang dan riwayat absensi. Status yang digunakan hanya sedang magang, akan selesai, dan selesai.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-light me-2" type="button" id="exportExcelButton"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                <button class="btn btn-dark" type="button" id="exportPdfButton"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-flag-fill"></i>
        <div>Status magang otomatis berubah menjadi <strong>akan selesai</strong> saat sisa periode magang tinggal 7 hari atau kurang.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="sedang magang">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sedang Magang</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-play-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="akan selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Akan Selesai</p><h3 class="mb-0" id="statEnding">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Peserta</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari peserta, kampus, penempatan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Magang</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="sedang magang">Sedang Magang</option>
                        <option value="akan selesai">Akan Selesai</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="placementFilter">Penempatan</label>
                    <select class="form-select" id="placementFilter">
                        <option value="semua">Semua Penempatan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="campusFilter">Perguruan Tinggi</label>
                    <select class="form-select" id="campusFilter">
                        <option value="semua">Semua Perguruan Tinggi</option>
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
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Tabel Status Magang Peserta</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data status magang</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-warning btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mentorMonitoringPanel" aria-controls="mentorMonitoringPanel"><i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel</button>
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
                                    <th>Nama Peserta</th>
                                    <th>Perguruan Tinggi</th>
                                    <th>Penempatan</th>
                                    <th>Periode Magang</th>
                                    <th>Pembimbing Akademik</th>
                                    <th>Status Magang</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="statusTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data status magang sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination status magang"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mentorMonitoringPanel" aria-labelledby="mentorMonitoringPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mentorMonitoringPanelLabel">Panel Monitoring Status Magang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
            <div class="card panel-card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Panel Informasi</h5>
                        <span class="badge bg-warning text-dark" id="panelCount">0</span>
                    </div>
                    <div id="infoPanel"></div>
                </div>
            </div>
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Tindakan Cepat</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning text-start" type="button" data-panel-action="monitoring progress"><i class="bi bi-graph-up me-2"></i> Monitoring Progress</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="export excel"><i class="bi bi-file-earmark-excel me-2"></i> Export Excel</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="export pdf"><i class="bi bi-file-earmark-pdf me-2"></i> Export PDF</button>
            </div>
    </div>
</div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Status Magang</h5>
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
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Status Magang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Ringkasan Perubahan Status</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan status"></textarea>
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
            <div class="toast-body" id="toastMessage">Data status magang diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const statuses = @json($statusMagangData ?? []);

    const tableBody = document.getElementById('statusTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const placementFilter = document.getElementById('placementFilter');
    const campusFilter = document.getElementById('campusFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2600 });
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingAction = null;

    const statusClass = (status) => ({ 'sedang magang':'success', 'akan selesai':'warning text-dark', selesai:'info text-dark' }[status] || 'secondary');

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        return statuses.filter((item) => {
            const keywordMatch = !keyword || `${item.nama} ${item.kampus} ${item.penempatan ?? ''} ${item.pembimbing_akademik ?? ''} ${item.status} ${item.prodi} ${item.catatan}`.toLowerCase().includes(keyword);
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const periodMatch = periodFilter.value === 'semua' || item.periode === periodFilter.value;
            const placementMatch = placementFilter.value === 'semua' || (item.penempatan ?? '-') === placementFilter.value;
            const campusMatch = campusFilter.value === 'semua' || item.kampus === campusFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && statusMatch && periodMatch && placementMatch && campusMatch && cardMatch;
        });
    }

    function populatePlacementFilter() {
        const placements = [...new Set(statuses.map((item) => item.penempatan).filter((value) => value && value !== '-'))];
        placementFilter.innerHTML = '<option value="semua">Semua Penempatan</option>' + placements.map((value) => `<option value="${value}">${value}</option>`).join('');
    }

    function populateCampusFilter() {
        const campuses = [...new Set(statuses.map((item) => item.kampus).filter((value) => value && value !== '-'))];
        campusFilter.innerHTML = '<option value="semua">Semua Perguruan Tinggi</option>' + campuses.map((value) => `<option value="${value}">${value}</option>`).join('');
    }

    function renderStats() {
        document.getElementById('statTotal').textContent = statuses.length;
        document.getElementById('statActive').textContent = statuses.filter((item) => item.status === 'sedang magang').length;
        document.getElementById('statEnding').textContent = statuses.filter((item) => item.status === 'akan selesai').length;
        document.getElementById('statDone').textContent = statuses.filter((item) => item.status === 'selesai').length;
    }

    function renderPanel() {
        const important = statuses.filter((item) => item.status !== 'sedang magang').slice(0, 4);
        document.getElementById('panelCount').textContent = important.length;
        document.getElementById('infoPanel').innerHTML = important.map((item) => `
            <div class="info-row">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.nama}</strong>
                    <span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span>
                </div>
                <small class="text-muted d-block">${item.penempatan ?? '-'} - ${item.periode}</small>
                <div class="progress my-2"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div>
                <small>${item.progress}% - ${item.catatan}</small>
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
                <td><strong>${item.nama}</strong><small class="text-muted d-block">${item.prodi}</small></td>
                <td>${item.kampus}</td>
                <td>${item.penempatan ?? '-'}</td>
                <td>${item.periode}</td>
                <td>${item.pembimbing_akademik ?? '-'}</td>
                <td><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></td>
                <td>
                    <div class="d-flex justify-content-between small"><span>${item.progress}%</span><span>${item.hari_tersisa === null ? '-' : item.hari_tersisa + ' hari tersisa'}</span></div>
                    <div class="progress"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div>
                </td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail peserta" data-id="${item.id}">Detail</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} status magang ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderPanel();
    }

    function openDetail(item, title = 'Detail Status Magang') {
        document.getElementById('detailModalLabel').textContent = title;
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Peserta</small><h5>${item.nama}</h5><div>${item.prodi}</div></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Status Magang</small><div class="mb-2"><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></div><div>${item.periode}</div></div></div>
                <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Perguruan Tinggi</small><h6>${item.kampus}</h6></div></div>
                <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Penempatan</small><h6>${item.penempatan ?? '-'}</h6></div></div>
                <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Pembimbing Akademik</small><h6>${item.pembimbing_akademik ?? '-'}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted">Periode Magang</small><h6 class="mb-0">${item.tanggal_mulai} - ${item.tanggal_selesai}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted">Absensi Terakhir</small><h6 class="mb-0">${item.absensi_terakhir}</h6></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Progress</small><div class="progress my-2"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div><strong>${item.progress}%</strong></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan Status</small><p class="mb-0">${item.catatan}</p></div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <strong>${item.nama}</strong>
                <small class="text-muted d-block">${item.penempatan ?? '-'} - ${item.periode}</small>
                <hr>
                <small class="text-muted">Tindakan status magang</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = item.catatan;
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
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter status magang berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        placementFilter.value = 'semua';
        campusFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter status magang berhasil direset.', 'info');
    });
    populatePlacementFilter();
    populateCampusFilter();
    [searchInput, statusFilter, periodFilter, placementFilter, campusFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = statuses.find((status) => status.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail peserta') {
            openDetail(item, 'Detail Status Magang');
            return;
        }
    });
    document.querySelectorAll('[data-panel-action]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!statuses.length) {
                showToast('Belum ada data status magang untuk diproses.', 'warning');
                return;
            }

            openConfirm(statuses[0], button.dataset.panelAction);
        });
    });
    document.getElementById('exportExcelButton').addEventListener('click', () => showToast('Data status magang berhasil disiapkan dalam format Excel.', 'info'));
    document.getElementById('exportPdfButton').addEventListener('click', () => showToast('Data status magang berhasil disiapkan dalam format PDF.', 'info'));
    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.catatan = document.getElementById('confirmNote').value || pendingAction.item.catatan;
        if (pendingAction.action === 'monitoring progress') pendingAction.item.status = 'sedang magang';
        confirmModal.hide();
        renderTable();
        showToast(`Tindakan ${pendingAction.action} berhasil disimpan.`);
        pendingAction = null;
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi status magang: 4 progres peserta diperbarui.', 'warning'), 700);
});
</script>
@endpush
