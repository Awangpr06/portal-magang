@extends('admin.layout.admin')

@section('title', 'Rekap Kegiatan')

@push('styles')
<style>
    .activity-recap-page .page-title { font-weight: 700; color: #163342; }
    .activity-recap-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .activity-recap-page .stat-card,
    .activity-recap-page .filter-card,
    .activity-recap-page .table-card,
    .activity-recap-page .side-card { border: 0; border-radius: 8px; }
    .activity-recap-page .stat-card { cursor: pointer; transition: .2s ease; }
    .activity-recap-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .activity-recap-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .trend-row { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .trend-row .progress { flex: 1; height: 12px; }
    .agency-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; background: #fbfdfe; }
    .agency-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
    .activity-recap-page .table { font-size: 14px; }
    .activity-recap-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .activity-recap-page .table tbody tr:hover { background: #f8fbfd; }
    .activity-recap-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 270px; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid activity-recap-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.laporan-monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Kegiatan</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Rekap Kegiatan</h2>
            <p class="text-muted mb-0">Monitoring, evaluasi, dokumentasi, dan analisis aktivitas kegiatan peserta magang.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Laporan</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Kegiatan</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-list-task"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kegiatan Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="proses">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kegiatan Proses</p><h3 class="mb-0" id="statProcess">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-play-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="tertunda">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kegiatan Tertunda</p><h3 class="mb-0" id="statPending">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-progress-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Progress</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-percent"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="periodFilter" class="form-label">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="studyFilter" class="form-label">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="placementFilter" class="form-label">Penempatan</label>
                    <select class="form-select" id="placementFilter">
                        <option value="semua">Semua Penempatan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="selesai">Selesai</option>
                        <option value="proses">Proses</option>
                        <option value="tertunda">Tertunda</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="searchInput" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari kegiatan">
                        <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-search"></i></button>
                        <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card side-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Rekap Per Penempatan</h5>
                            <p class="text-muted mb-0">Ringkasan progress kegiatan berdasarkan penempatan/divisi.</p>
                        </div>
                        <span class="badge bg-light text-dark" id="agencyCount">0 penempatan</span>
                    </div>
                    <div class="agency-grid" id="placementRecap"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Rekap Kegiatan Mahasiswa</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Penempatan</th>
                            <th>Judul Kegiatan</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th width="300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="activityTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data rekap kegiatan tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination rekap kegiatan"><ul class="pagination mb-0" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Rekap Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
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
    <div id="activityToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const activities = (@json($adminMagangActivities ?? [])).map((item) => ({
        id: item.id,
        nim: item.nim || '-',
        name: item.nama || '-',
        study: item.prodi || '-',
        agency: item.penempatan || '-',
        title: item.kegiatan || '-',
        start: item.tanggal_mulai || item.tanggal || '-',
        end: item.tanggal_selesai || item.tanggal || '-',
        progress: Number(item.progress || 0),
        status: item.status === 'selesai' ? 'selesai' : (item.status === 'berlangsung' ? 'proses' : 'tertunda'),
        period: item.periode || '-',
        batch: item.status === 'selesai' ? 'Kelompok A' : (item.status === 'proses' ? 'Kelompok B' : 'Kelompok C'),
        downloadUrl: item.download_url || '#',
    }));

    const perPage = 5;
    let currentPage = 1;
    let pendingAction = null;
    let pendingId = null;

    const periodFilter = document.getElementById('periodFilter');
    const studyFilter = document.getElementById('studyFilter');
    const placementFilter = document.getElementById('placementFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('activityTable');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('activityToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(status) {
        return { selesai: 'bg-success', proses: 'bg-info text-dark', tertunda: 'bg-warning text-dark' }[status] || 'bg-secondary';
    }

    function filteredActivities() {
        const keyword = searchInput.value.trim().toLowerCase();
        return activities.filter((item) => {
            const matchKeyword = !keyword || [item.nim, item.name, item.study, item.agency, item.title, item.status].join(' ').toLowerCase().includes(keyword);
            const matchPeriod = periodFilter.value === 'semua' || item.period === periodFilter.value;
            const matchStudy = studyFilter.value === 'semua' || item.study === studyFilter.value;
            const matchAgency = placementFilter.value === 'semua' || item.agency === placementFilter.value;
        const matchStatus = statusFilter.value === 'semua' || item.status === statusFilter.value;
        return matchKeyword && matchPeriod && matchStudy && matchAgency && matchStatus;
        });
    }

    function updateStats() {
        const average = activities.length
            ? Math.round(activities.reduce((sum, item) => sum + item.progress, 0) / activities.length)
            : 0;
        document.getElementById('statTotal').textContent = activities.length;
        document.getElementById('statDone').textContent = activities.filter((item) => item.status === 'selesai').length;
        document.getElementById('statProcess').textContent = activities.filter((item) => item.status === 'proses').length;
        document.getElementById('statPending').textContent = activities.filter((item) => item.status === 'tertunda').length;
        document.getElementById('statAverage').textContent = `${average}%`;
    }

    function renderTable() {
        const data = filteredActivities();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.nim}</td>
                <td class="fw-semibold">${item.name}</td>
                <td>${item.study}</td>
                <td>${item.agency}</td>
                <td>${item.title}</td>
                <td>${item.start}</td>
                <td>${item.end}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:8px; min-width:80px;">
                            <div class="progress-bar bg-success" style="width:${item.progress}%"></div>
                        </div>
                        <span>${item.progress}%</span>
                    </div>
                </td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${item.id}">Edit</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                        <a class="btn btn-primary btn-sm" href="${item.downloadUrl}" target="_blank" rel="noopener">Unduh</a>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('tableSummary').textContent = `${data.length} rekap kegiatan ditemukan`;
        document.getElementById('tableWrapper').classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyState').classList.toggle('d-none', data.length > 0);
        document.getElementById('pageInfo').textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data kegiatan` : 'Menampilkan 0 data kegiatan';
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

    function renderAgencyRecap() {
        const placements = [...new Set(activities.map((item) => item.agency))].filter((placement) => placement && placement !== '-');
        document.getElementById('agencyCount').textContent = `${placements.length} penempatan`;

        if (!placements.length) {
            document.getElementById('placementRecap').innerHTML = '<div class="text-muted small">Belum ada data penempatan untuk ditampilkan.</div>';
            return;
        }

        document.getElementById('placementRecap').innerHTML = placements.map((placement) => {
            const items = activities.filter((item) => item.agency === placement);
            const average = Math.round(items.reduce((sum, item) => sum + item.progress, 0) / items.length);
            return `
                <div class="agency-item shadow-sm">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <strong class="d-block">${placement}</strong>
                            <small class="text-muted">${items.length} kegiatan</small>
                        </div>
                        <span class="fw-semibold">${average}%</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" style="width:${average}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Rata-rata progress</span>
                        <span>${average}%</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('activityToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const item = activities.find((data) => data.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">NIM</dt><dd class="col-sm-8">${item.nim}</dd>
                <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">${item.name}</dd>
                <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${item.study}</dd>
                <dt class="col-sm-4">Penempatan</dt><dd class="col-sm-8">${item.agency}</dd>
                <dt class="col-sm-4">Kegiatan</dt><dd class="col-sm-8">${item.title}</dd>
                <dt class="col-sm-4">Periode</dt><dd class="col-sm-8">${item.period}</dd>
                <dt class="col-sm-4">Tanggal Mulai</dt><dd class="col-sm-8">${item.start}</dd>
                <dt class="col-sm-4">Tanggal Selesai</dt><dd class="col-sm-8">${item.end}</dd>
                <dt class="col-sm-4">Progress</dt><dd class="col-sm-8">${item.progress}%</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">${titleCase(item.status)}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function confirmAction(action, id = null) {
        pendingAction = action;
        pendingId = id;
        const item = id ? activities.find((data) => data.id === id) : null;
        const labels = {
            hapus: ['Konfirmasi Hapus Kegiatan', `Hapus rekap kegiatan ${item?.name}?`],
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    function populatePlacementFilter() {
        const placements = [...new Set(activities.map((item) => item.agency).filter(Boolean))].sort((a, b) => a.localeCompare(b));
        placementFilter.innerHTML = '<option value="semua">Semua Penempatan</option>' + placements.map((placement) => `<option value="${placement}">${placement}</option>`).join('');
    }

    function renderAll() {
        updateStats();
        renderTable();
        renderAgencyRecap();
    }

    [periodFilter, studyFilter, placementFilter, statusFilter].forEach((input) => input.addEventListener('change', () => {
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

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderAll();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        periodFilter.value = 'semua';
        studyFilter.value = 'semua';
        placementFilter.value = 'semua';
        statusFilter.value = 'semua';
        searchInput.value = '';
        currentPage = 1;
        renderAll();
    });

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderAll();
        });
    });

    document.querySelector('[data-progress-card]').addEventListener('click', () => {
        showToast(`Rata-rata progress kegiatan saat ini ${document.getElementById('statAverage').textContent}.`);
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const id = Number(button.dataset.id);
        const action = button.dataset.action;
        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'unduh') {
            showToast('Laporan kegiatan berhasil disiapkan untuk diunduh.');
        } else if (action === 'edit') {
            showToast('Form perbaikan data kegiatan berhasil dibuka.');
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

    document.getElementById('exportButton').addEventListener('click', () => showToast('Ekspor laporan rekap kegiatan berhasil disiapkan.'));
    document.getElementById('confirmAction').addEventListener('click', () => {
        let message = '';
        if (pendingAction === 'hapus') {
            const index = activities.findIndex((item) => item.id === pendingId);
            if (index >= 0) activities.splice(index, 1);
            message = 'Data rekap kegiatan berhasil dihapus.';
        }
        pendingAction = null;
        pendingId = null;
        confirmModal.hide();
        renderAll();
        showToast(message);
    });

    populatePlacementFilter();
    renderAll();
</script>
@endpush
