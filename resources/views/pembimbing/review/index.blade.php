@extends('pembimbing.layout.pembimbing')

@section('title', 'Review')
@section('page-title', 'Review')

@push('styles')
<style>
    .review-page .review-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .review-page .stat-card,
    .review-page .filter-card,
    .review-page .table-card,
    .review-page .side-panel { border:0; border-radius:8px; }
    .review-page .stat-card { cursor:pointer; transition:.2s ease; }
    .review-page .stat-card:hover,
    .review-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .review-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .review-page .table { font-size:14px; }
    .review-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .review-page .table tbody tr:hover { background:#f7fcfe; }
    .review-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:430px; }
    .review-page .activity-row,
    .review-page .distribution-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .review-page .progress { height:10px; border-radius:999px; }
    .review-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .review-page .pagination .page-link { color:#2a8fbd; }
    .review-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $mode = $mode ?? 'utama';
    $pageTitle = $mode === 'riwayat' ? 'Riwayat Review' : 'Review Laporan';
    $reportItems = collect($reportItems ?? []);
@endphp

<div class="container-fluid review-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Review</li>
        </ol>
    </nav>

    <section class="review-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">{{ $pageTitle }}</h3>
                <p class="mb-0">Periksa, evaluasi, beri umpan balik, dan dokumentasikan hasil review laporan mahasiswa magang.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="reviewBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-bell-fill"></i>
        <div>6 laporan menunggu review dan 3 laporan membutuhkan revisi mahasiswa.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Laporan</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-files"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="sudah direview">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sudah Direview</p><h3 class="mb-0" id="statReviewed">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="menunggu review">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu</p><h3 class="mb-0" id="statWaiting">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="direvisi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Direvisi</p><h3 class="mb-0" id="statRevision">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-pencil-square"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Disetujui</p><h3 class="mb-0" id="statApproved">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-award"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-completion-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Penyelesaian</p><h3 class="mb-0" id="statCompletion">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Mahasiswa / Dokumen</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari mahasiswa, NIM, judul, atau catatan">
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
                        <option value="Dinas Kominfo DIY">Dinas Kominfo DIY</option>
                        <option value="Bappeda DIY">Bappeda DIY</option>
                        <option value="Bank BPD DIY">Bank BPD DIY</option>
                        <option value="PT Inovasi Digital">PT Inovasi Digital</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Review</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu review">Menunggu Review</option>
                        <option value="sudah direview">Sudah Direview</option>
                        <option value="direvisi">Direvisi</option>
                        <option value="disetujui">Disetujui</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Unggah</label>
                    <select class="form-select" id="dateFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="hari ini">Hari Ini</option>
                        <option value="minggu ini">Minggu Ini</option>
                        <option value="bulan ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter" title="Terapkan Filter"><i class="bi bi-funnel"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">Daftar Laporan Mahasiswa</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePanelButton" data-bs-toggle="offcanvas" data-bs-target="#reviewExtraPanel" aria-controls="reviewExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Tampilkan Panel
                            </button>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="exportButton"><i class="bi bi-download"></i> Export Data</button>
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
                                    <th>Jenis Dokumen</th>
                                    <th>Judul Laporan</th>
                                    <th>Tanggal Unggah</th>
                                    <th>Status Review</th>
                                    <th>Catatan Terakhir</th>
                                    <th>Tanggal Review</th>
                                    <th width="430">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="reviewTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data review tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination review">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="reviewExtraPanel" aria-labelledby="reviewExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="reviewExtraPanelLabel">Panel Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="mb-3">Aktivitas Review</h5>
                <div id="activityPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Status Review</h5>
                <div id="distributionPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Review</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <i class="bi bi-journal-check text-primary fs-3"></i>
                            <div class="w-100">
                                <p class="mb-2" id="confirmMessage">Simpan hasil review?</p>
                                <div class="small text-muted mb-2" id="confirmSummary"></div>
                                <label class="form-label" for="reviewNote">Catatan Evaluasi</label>
                                <textarea class="form-control" id="reviewNote" rows="3" placeholder="Tambahkan catatan review"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveReviewAction">Simpan Review</button>
                <button type="button" class="btn btn-success d-none" id="approveReviewAction">Setujui Review</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="reviewToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const initialMode = @json($mode);
    const reviews = @json($reportItems);

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('reviewTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const saveReviewAction = document.getElementById('saveReviewAction');
    const approveReviewAction = document.getElementById('approveReviewAction');
    const toast = new bootstrap.Toast(document.getElementById('reviewToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            'menunggu review': 'bg-warning text-dark',
            'sudah direview': 'bg-info text-dark',
            direvisi: 'bg-secondary',
            disetujui: 'bg-success',
            draft: 'bg-secondary'
        }[value];
    }

    function filteredReviews() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const date = dateFilter.value;
        return reviews.filter((item) => {
            const historyMode = initialMode === 'riwayat' ? item.reviewDate !== '-' : true;
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.penempatan, item.jenis, item.judul, item.status, item.catatan].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchDate = date === 'semua' || item.period === date || item.unggah === date;
            return historyMode && matchKeyword && matchStudy && matchAgency && matchStatus && matchDate;
        });
    }

    function updateStats() {
        const reviewed = reviews.filter((item) => ['sudah direview', 'disetujui', 'direvisi'].includes(item.status)).length;
        document.getElementById('statTotal').textContent = reviews.length;
        document.getElementById('statReviewed').textContent = reviewed;
        document.getElementById('statWaiting').textContent = reviews.filter((item) => item.status === 'menunggu review').length;
        document.getElementById('statRevision').textContent = reviews.filter((item) => item.status === 'direvisi').length;
        document.getElementById('statApproved').textContent = reviews.filter((item) => item.status === 'disetujui').length;
        document.getElementById('statCompletion').textContent = reviews.length ? `${Math.round((reviewed / reviews.length) * 100)}%` : '0%';
    }

    function renderPanels() {
        document.getElementById('activityPanel').innerHTML = reviews
            .filter((item) => item.reviewDate !== '-')
            .slice(0, 5)
            .map((item) => `
                <div class="activity-row">
                    <div class="d-flex justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">${item.nama}</div>
                            <div class="small text-muted">${item.judul}</div>
                        </div>
                        <span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span>
                    </div>
                    <div class="small mt-2">${item.reviewDate} - ${item.catatan}</div>
                </div>
            `).join('');

        const statuses = ['menunggu review', 'sudah direview', 'direvisi', 'disetujui'];
        document.getElementById('distributionPanel').innerHTML = statuses.map((status) => {
            const count = reviews.filter((item) => item.status === status).length;
            const percent = Math.round((count / reviews.length) * 100);
            return `
                <div class="distribution-row">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">${titleCase(status)}</span>
                        <span>${count} laporan</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar ${statusClass(status).replace(' text-dark', '')}" style="width:${percent}%"></div>
                    </div>
                </div>
            `;
        }).join('');
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
        const data = filteredReviews();
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
                    <div class="small text-muted">${item.nim} - ${item.prodi}</div>
                </td>
                <td>${item.jenis}</td>
                <td>${item.judul}</td>
                <td>${item.unggah}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>${item.catatan}</td>
                <td>${item.reviewDate}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="dokumen" data-id="${item.id}">Lihat Dokumen</button>
                        <button class="btn btn-primary btn-sm" type="button" data-action="review" data-id="${item.id}">Review</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} laporan ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} laporan`
            : 'Menampilkan 0 laporan';
        renderPagination(totalPages);
        updateStats();
        renderPanels();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('reviewToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'dokumen') {
        const titles = {
            dokumen: 'Detail Dokumen',
            review: 'Review'
        };
        document.getElementById('detailTitle').textContent = titles[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Penempatan</strong><div>${item.penempatan}</div></div>
                <div class="col-md-6"><strong>Jenis Dokumen</strong><div>${item.jenis}</div></div>
                <div class="col-md-6"><strong>Dokumen</strong><div>${item.dokumen || '-'}</div></div>
                <div class="col-12"><strong>Judul Laporan</strong><div>${item.judul}</div></div>
                <div class="col-md-6"><strong>Status Review</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Tanggal Review</strong><div>${item.reviewDate}</div></div>
                <div class="col-12"><strong>Catatan Terakhir</strong><div>${item.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = `Simpan tindakan ${titleCase(action)} untuk ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.jenis} - ${item.judul} - ${titleCase(item.status)}`;
        document.getElementById('reviewNote').value = item.catatan;
        saveReviewAction.textContent = 'Simpan Review';
        approveReviewAction.classList.toggle('d-none', action !== 'review');
        approveReviewAction.textContent = 'Setujui Review';
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card], [data-completion-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-completion-card]').addEventListener('click', () => showToast('Rata-rata penyelesaian review ditampilkan.', 'info'));

    [studyFilter, agencyFilter, statusFilter, dateFilter].forEach((input) => {
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
        showToast('Filter review berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card], [data-completion-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter review berhasil direset.', 'info');
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
        const item = reviews.find((review) => review.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'dokumen') {
            openDetail(item, action);
            return;
        }
        openConfirm(item, action);
    });

    saveReviewAction.addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.catatan = document.getElementById('reviewNote').value || pendingAction.item.catatan;
        pendingAction.item.reviewDate = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        pendingAction.item.status = pendingAction.action === 'review' ? 'sudah direview' : pendingAction.item.status;
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Hasil review ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
    });

    approveReviewAction.addEventListener('click', () => {
        if (!pendingAction || pendingAction.action !== 'review') return;
        pendingAction.item.catatan = document.getElementById('reviewNote').value || pendingAction.item.catatan;
        pendingAction.item.reviewDate = new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        pendingAction.item.status = 'disetujui';
        pendingAction.item.revisi = 100;
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Review ${pendingAction.item.nama} berhasil disetujui.`);
        pendingAction = null;
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data review berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('reviewBadge').textContent = 'Diperbarui';
        showToast('Data review berhasil diperbarui.', 'info');
    });

    const reviewExtraPanel = document.getElementById('reviewExtraPanel');
    const togglePanelButton = document.getElementById('togglePanelButton');
    reviewExtraPanel.addEventListener('show.bs.offcanvas', () => {
        togglePanelButton.innerHTML = '<i class="bi bi-layout-sidebar-inset-reverse"></i> Sembunyikan Panel';
    });
    reviewExtraPanel.addEventListener('hide.bs.offcanvas', () => {
        togglePanelButton.innerHTML = '<i class="bi bi-layout-sidebar-inset"></i> Tampilkan Panel';
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi review: 6 laporan menunggu evaluasi.', 'warning'), 800);
});
</script>
@endpush
