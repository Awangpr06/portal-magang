@extends('pembimbing.layout.pembimbing')

@section('title', 'Kegiatan Mahasiswa')
@section('page-title', 'Kegiatan Mahasiswa')

@push('styles')
<style>
    .activity-monitor-page .activity-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .activity-monitor-page .stat-card,
    .activity-monitor-page .filter-card,
    .activity-monitor-page .table-card,
    .activity-monitor-page .side-panel { border:0; border-radius:8px; }
    .activity-monitor-page .stat-card { cursor:pointer; transition:.2s ease; }
    .activity-monitor-page .stat-card:hover,
    .activity-monitor-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .activity-monitor-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .activity-monitor-page .table { font-size:14px; }
    .activity-monitor-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .activity-monitor-page .table tbody tr:hover { background:#f7fcfe; }
    .activity-monitor-page .progress { height:10px; border-radius:999px; }
    .activity-monitor-page .action-group { display:flex; flex-wrap:nowrap; gap:6px; align-items:center; min-width:0; max-width:100%; }
    .activity-monitor-page .action-group .btn { white-space:nowrap; flex:0 0 auto; }
    .activity-monitor-page .nav-pills .nav-link { color:#2a8fbd; border-radius:8px; }
    .activity-monitor-page .nav-pills .nav-link.active { background:#2a8fbd; color:#fff; }
    .activity-monitor-page .distribution-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .activity-monitor-page .timeline-item { border-left:3px solid #2a8fbd; padding-left:12px; margin-bottom:14px; }
    .activity-monitor-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .activity-monitor-page .pagination .page-link { color:#2a8fbd; }
    .activity-monitor-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid activity-monitor-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kegiatan Mahasiswa</li>
        </ol>
    </nav>

    <section class="activity-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Kegiatan Mahasiswa</h3>
                <p class="mb-0">Pantau penugasan mahasiswa yang dikirim peserta sebagai dasar pembimbingan dan evaluasi akademik berkala.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="activityBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-bell-fill"></i>
        <div id="activityAlertText">Memuat data penugasan mahasiswa dari database...</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-card-filter="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mahasiswa</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="terverifikasi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terverifikasi</p><h3 class="mb-0" id="statVerified">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu</p><h3 class="mb-0" id="statWaiting">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="terlambat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terlambat</p><h3 class="mb-0" id="statLate">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-alarm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Selesai</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-today-card="hari ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktivitas Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-lightning-charge"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Nama/NIM Mahasiswa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, atau kegiatan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="studyFilter">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                        <option value="Akuntansi">Akuntansi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="agencyFilter">Penempatan</label>
                    <select class="form-select" id="agencyFilter">
                        <option value="semua">Semua Penempatan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Kegiatan</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="terverifikasi">Terverifikasi</option>
                        <option value="menunggu">Menunggu Verifikasi</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="perlu catatan">Perlu Catatan</option>
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
                <div class="col-lg-1 col-md-6 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter" title="Terapkan Filter"><i class="bi bi-funnel"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills gap-2 mb-4" id="activityTabs">
        <li class="nav-item"><button class="nav-link active" type="button" data-category="semua">Semua</button></li>
        <li class="nav-item"><button class="nav-link" type="button" data-category="penugasan">Penugasan</button></li>
    </ul>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Aktivitas Mahasiswa</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data penugasan</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#activityExtraPanel" aria-controls="activityExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Panel
                            </button>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="exportButton">
                                <i class="bi bi-download"></i> Export Data
                            </button>
                            <label for="perPageSelect" class="text-muted small">Data per halaman</label>
                            <select class="form-select form-select-sm" id="perPageSelect" style="width:90px">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="15">15</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive" id="tableWrapper">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mahasiswa</th>
                                    <th>Program Studi</th>
                                    <th>Penempatan</th>
                                    <th>Kegiatan Terakhir</th>
                                    <th>Terakhir Diperbarui</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="activityTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data kegiatan tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter penugasan.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data penugasan</span>
                        <nav aria-label="Pagination kegiatan mahasiswa">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="activityExtraPanel" aria-labelledby="activityExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="activityExtraPanelLabel">Panel Aktivitas</h5>
            <small class="text-muted">Distribusi status dan aktivitas terbaru</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Status</h5>
                <div id="distributionPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Aktivitas Terbaru</h5>
                <div id="latestPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Kegiatan Mahasiswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Tindak Lanjut</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div class="w-100">
                        <p class="mb-2" id="confirmMessage">Apakah tindakan akan disimpan?</p>
                        <div class="small text-muted mb-2" id="confirmSummary"></div>
                        <label class="form-label" for="lecturerNote">Catatan Pembimbing</label>
                        <textarea class="form-control" id="lecturerNote" rows="3" placeholder="Tambahkan catatan kegiatan"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Pesan ke Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="messageForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="messageRecipient">Penerima</label>
                            <input type="text" class="form-control" id="messageRecipient" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="messageSubject">Subjek</label>
                            <input type="text" class="form-control" id="messageSubject" name="subject" maxlength="150" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="messageBody">Pesan</label>
                            <textarea class="form-control" id="messageBody" name="message" rows="5" maxlength="5000" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="messageAttachment">Lampiran URL</label>
                            <input type="text" class="form-control" id="messageAttachment" name="attachment" maxlength="255" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="small text-muted mt-3" id="messageSummary"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
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
document.addEventListener('DOMContentLoaded', () => {
    const messageStoreUrlTemplate = @json(route('pembimbing.mahasiswa.pesan.store', ['internship' => '__INTERNSHIP__']));
    const activities = @json($activitiesData ?? []);
    const penempatanOptions = @json($penempatanOptions ?? []);

    let currentPage = 1;
    let activeCategory = 'semua';
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('activityTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageForm = document.getElementById('messageForm');
    const toast = new bootstrap.Toast(document.getElementById('activityToast'), { delay: 3000 });

    penempatanOptions.forEach((penempatan) => {
        const option = document.createElement('option');
        option.value = penempatan;
        option.textContent = penempatan;
        agencyFilter.appendChild(option);
    });

    function titleCase(value) {
        return String(value || '-').split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            terverifikasi: 'bg-success',
            menunggu: 'bg-warning text-dark',
            terlambat: 'bg-danger',
            'perlu catatan': 'bg-info text-dark'
        }[value] || 'bg-secondary';
    }

    function filteredActivities() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const period = periodFilter.value;

        return activities.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.penempatan, item.kegiatan, item.status, item.catatan].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchPeriod = period === 'semua' || item.periode === period;
            const matchCategory = activeCategory === 'semua' || item.kategori === activeCategory;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchPeriod && matchCategory;
        });
    }

    function updateStats() {
        const totalActivities = activities.length;
        const average = totalActivities
            ? Math.round(activities.reduce((sum, item) => sum + Number(item.progress || 0), 0) / totalActivities)
            : 0;
        document.getElementById('statTotal').textContent = new Set(activities.map((item) => item.nim)).size;
        document.getElementById('statVerified').textContent = activities.filter((item) => item.status === 'terverifikasi').length;
        document.getElementById('statWaiting').textContent = activities.filter((item) => item.status === 'menunggu').length;
        document.getElementById('statLate').textContent = activities.filter((item) => item.status === 'terlambat').length;
        document.getElementById('statAverage').textContent = `${average}%`;
        document.getElementById('statToday').textContent = activities.filter((item) => String(item.updated || '').includes('25 Mei 2026')).length;
    }

    function renderDistribution() {
        const statuses = ['terverifikasi', 'menunggu', 'terlambat', 'perlu catatan'];
        const total = activities.length || 1;
        document.getElementById('distributionPanel').innerHTML = statuses.map((status) => {
            const count = activities.filter((item) => item.status === status).length;
            const percent = Math.round((count / total) * 100);
            return `
                <div class="distribution-row">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">${titleCase(status)}</span>
                        <span>${count} data</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar ${statusClass(status).replace(' text-dark', '')}" style="width:${percent}%"></div>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('latestPanel').innerHTML = activities.slice(0, 5).map((item) => `
            <div class="timeline-item">
                <div class="fw-semibold">${item.nama}</div>
                <div class="small text-muted">${item.kegiatan}</div>
                <span class="badge ${statusClass(item.status)} mt-1">${titleCase(item.status)}</span>
            </div>
        `).join('');
    }

    function updateBanner() {
        const newActivitiesCount = activities.filter((item) => ['menunggu', 'terlambat', 'perlu catatan'].includes(item.status)).length;
        const waitingCount = activities.filter((item) => item.status === 'menunggu').length;
        document.getElementById('activityAlertText').textContent = `${newActivitiesCount} penugasan perlu perhatian dan ${waitingCount} penugasan menunggu verifikasi pembimbing.`;
    }

    function renderPagination(totalPages) {
        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" type="button" data-page="${currentPage - 1}">Previous</button>
            </li>
        `;
        for (let page = 1; page <= totalPages; page++) {
            html += `
                <li class="page-item ${page === currentPage ? 'active' : ''}">
                    <button class="page-link" type="button" data-page="${page}">${page}</button>
                </li>
            `;
        }
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" type="button" data-page="${currentPage + 1}">Next</button>
            </li>
        `;
        pagination.innerHTML = html;
    }

    function renderTable() {
        const data = filteredActivities();
        const perPage = Number(perPageSelect.value);
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>
                    <div class="fw-semibold">${item.nama}</div>
                    <div class="small text-muted">${item.nim}</div>
                </td>
                <td>${item.prodi}</td>
                <td>${item.penempatan}</td>
                <td>${item.kegiatan}</td>
                <td>${item.updated}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="catatan" data-id="${item.id}">Beri Catatan</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} penugasan ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} penugasan`
            : 'Menampilkan 0 penugasan';
        renderPagination(totalPages);
        updateStats();
        renderDistribution();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('activityToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'detail') {
        const titles = {
            detail: 'Detail Kegiatan Mahasiswa',
            monitoring: 'Monitoring Kegiatan',
            catatan: 'Catatan Kegiatan',
            pesan: 'Kirim Pesan',
            logbook: 'Logbook Mahasiswa',
            riwayat: 'Riwayat Aktivitas'
        };
        document.getElementById('detailTitle').textContent = titles[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Penempatan</strong><div>${item.penempatan}</div></div>
                <div class="col-md-6"><strong>Jenis Aktivitas</strong><div>${item.kategori}</div></div>
                <div class="col-12"><strong>Kegiatan Terakhir</strong><div>${item.kegiatan}</div></div>
                <div class="col-md-6"><strong>Status Kegiatan</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Progress</strong><div>${item.progress}%</div></div>
                <div class="col-md-6"><strong>Periode</strong><div>${item.periode}</div></div>
                <div class="col-md-6"><strong>Terakhir Diperbarui</strong><div>${item.updated}</div></div>
                <div class="col-12"><strong>Catatan Pembimbing</strong><div>${item.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = `Simpan tindak lanjut ${titleCase(action)} untuk ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.nim} - ${item.kegiatan} - ${titleCase(item.status)}`;
        document.getElementById('lecturerNote').value = item.catatan;
        confirmModal.show();
    }

    function openMessageModal(item) {
        document.getElementById('messageRecipient').value = `${item.nama} (${item.nim})`;
        document.getElementById('messageSubject').value = `Monitoring Kegiatan - ${item.nama}`;
        document.getElementById('messageBody').value = '';
        document.getElementById('messageAttachment').value = '';
        document.getElementById('messageSummary').textContent = `Pesan akan dikirim ke ${item.nama} dan tersimpan pada percakapan peserta.`;
        messageForm.action = messageStoreUrlTemplate.replace('__INTERNSHIP__', item.id);
        messageModal.show();
    }

    document.querySelectorAll('[data-card-filter], [data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-card-filter], [data-status-card], [data-progress-card], [data-today-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard || 'semua';
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-progress-card]').addEventListener('click', () => {
        searchInput.value = '';
        currentPage = 1;
        renderTable();
        showToast('Statistik rata-rata penyelesaian ditampilkan.', 'info');
    });

    document.querySelector('[data-today-card]').addEventListener('click', () => {
        searchInput.value = '25 Mei 2026';
        currentPage = 1;
        renderTable();
    });

    document.querySelectorAll('#activityTabs button').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('#activityTabs button').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            activeCategory = button.dataset.category;
            currentPage = 1;
            renderTable();
        });
    });

    [studyFilter, agencyFilter, statusFilter, periodFilter].forEach((input) => {
        input.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderTable();
        }
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
        showToast('Filter kegiatan mahasiswa berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        activeCategory = 'semua';
        currentPage = 1;
        document.querySelectorAll('#activityTabs button').forEach((item) => item.classList.remove('active'));
        document.querySelector('#activityTabs button[data-category="semua"]').classList.add('active');
        document.querySelectorAll('[data-card-filter], [data-status-card], [data-progress-card], [data-today-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-card-filter="semua"]').classList.add('active');
        renderTable();
        showToast('Filter kegiatan mahasiswa berhasil direset.', 'info');
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
        const item = activities.find((activity) => activity.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item, action);
            return;
        }
        if (action === 'pesan') {
            openMessageModal(item);
            return;
        }
        openConfirm(item, action);
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.catatan = document.getElementById('lecturerNote').value || pendingAction.item.catatan;
        if (pendingAction.action === 'catatan' || pendingAction.action === 'monitoring') {
            pendingAction.item.status = 'terverifikasi';
        }
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Tindak lanjut kegiatan ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
    });

        document.getElementById('exportButton').addEventListener('click', () => showToast('Data penugasan mahasiswa berhasil disiapkan untuk ekspor.', 'info'));
        document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('activityBadge').textContent = 'Diperbarui';
        showToast('Data penugasan mahasiswa berhasil diperbarui.', 'info');
    });

    renderTable();
    updateBanner();
    setTimeout(() => showToast(`Notifikasi penugasan: ${activities.filter((item) => item.status === 'menunggu').length} penugasan menunggu verifikasi.`, 'warning'), 800);
});
</script>
@endpush
