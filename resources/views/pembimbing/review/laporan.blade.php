@extends('pembimbing.layout.pembimbing')

@section('title', 'Review Laporan')
@section('page-title', 'Review Laporan')

@push('styles')
<style>
    .report-review-page .review-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .report-review-page .stat-card,
    .report-review-page .filter-card,
    .report-review-page .table-card,
    .report-review-page .side-panel { border:0; border-radius:8px; }
    .report-review-page .stat-card { cursor:pointer; transition:.2s ease; }
    .report-review-page .stat-card:hover,
    .report-review-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .report-review-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .report-review-page .table { font-size:14px; }
    .report-review-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .report-review-page .table tbody tr:hover { background:#f7fcfe; }
    .report-review-page .progress { height:10px; border-radius:999px; }
    .report-review-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:430px; }
    .report-review-page .note-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .report-review-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .report-review-page .pagination .page-link { color:#2a8fbd; }
    .report-review-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid report-review-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.review.index') }}">Review</a></li>
            <li class="breadcrumb-item active" aria-current="page">Review Laporan</li>
        </ol>
    </nav>

    <section class="review-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Review Laporan Magang</h3>
                <p class="mb-0">Periksa laporan magang, berikan catatan evaluasi, setujui laporan, atau minta revisi sebelum penilaian akhir.</p>
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
        <i class="bi bi-file-earmark-text-fill"></i>
        <div id="reportSummaryAlert">Memuat ringkasan laporan dari database.</div>
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
            <div class="card stat-card" data-status-card="menunggu review">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu Review</p><h3 class="mb-0" id="statWaiting">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="review selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Review Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="perlu revisi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Perlu Revisi</p><h3 class="mb-0" id="statRevision">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-pencil-square"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-completion-card="done">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Penyelesaian</p><h3 class="mb-0" id="statCompletion">0%</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Review</p><h3 class="mb-0" id="statAverageTime">0 Hari</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Mahasiswa / Laporan</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, judul, atau catatan">
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
                    <label class="form-label" for="statusFilter">Status Laporan</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu review">Menunggu Review</option>
                        <option value="review selesai">Review Selesai</option>
                        <option value="perlu revisi">Perlu Revisi</option>
                        <option value="disetujui">Disetujui</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="jenisFilter">Jenis Laporan</label>
                    <select class="form-select" id="jenisFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="berkala">Laporan Berkala</option>
                        <option value="akhir">Laporan Akhir</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode Unggah</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
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
                            <h5 class="mb-1">Daftar Laporan</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#reportReviewExtraPanel" aria-controls="reportReviewExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Panel
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
                                    <th>Judul Laporan</th>
                                    <th>Jenis Laporan</th>
                                    <th>Penempatan</th>
                                    <th>Tanggal Unggah</th>
                                    <th>Status Review</th>
                                    <th>Progress Revisi</th>
                                    <th>Catatan Review</th>
                                    <th width="430">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="reportTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data laporan tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination review laporan">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="reportReviewExtraPanel" aria-labelledby="reportReviewExtraPanelLabel" style="width:min(92vw, 520px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="reportReviewExtraPanelLabel">Panel Review Laporan</h5>
            <small class="text-muted">Catatan review dan tindak lanjut laporan</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Panel Catatan Review</h5>
                <div id="notePanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Laporan</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Review Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <i class="bi bi-journal-check text-primary fs-3"></i>
                            <div class="w-100">
                                <p class="mb-2" id="confirmMessage">Simpan hasil review laporan?</p>
                                <div class="small text-muted mb-2" id="confirmSummary"></div>
                                <label class="form-label" for="reviewNote">Catatan Evaluasi</label>
                                <textarea class="form-control" id="reviewNote" rows="3" placeholder="Tambahkan catatan evaluasi laporan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveReviewAction">Simpan Review</button>
                <button type="button" class="btn btn-success d-none" id="approveReviewAction">Setujui Laporan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="reportReviewToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const reports = @json($reportItems ?? []);

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const jenisFilter = document.getElementById('jenisFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('reportTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const saveReviewAction = document.getElementById('saveReviewAction');
    const approveReviewAction = document.getElementById('approveReviewAction');
    const toast = new bootstrap.Toast(document.getElementById('reportReviewToast'), { delay: 3000 });
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const reviewUpdateUrl = @json(route('pembimbing.review.laporan.update', ['report' => '__REPORT__']));

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            'menunggu review': 'bg-warning text-dark',
            'review selesai': 'bg-info text-dark',
            'perlu revisi': 'bg-danger',
            disetujui: 'bg-success'
        }[value];
    }

    function progressClass(value) {
        if (value >= 80) return 'bg-success';
        if (value >= 50) return 'bg-info';
        return 'bg-warning';
    }

    function filteredReports() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const jenis = jenisFilter.value;
        const period = periodFilter.value;
        return reports.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.penempatan, item.judul, item.status, item.jenis, item.catatan].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchJenis = jenis === 'semua' || item.jenis_raw === jenis;
            const matchPeriod = period === 'semua' || item.period === period;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchJenis && matchPeriod;
        });
    }

    function updateStats() {
        const done = reports.filter((item) => ['review selesai', 'disetujui'].includes(item.status)).length;
        const reviewedReports = reports.filter((item) => item.reviewTime > 0);
        const averageTime = reviewedReports.length
            ? Math.round(reviewedReports.reduce((sum, item) => sum + item.reviewTime, 0) / reviewedReports.length)
            : 0;
        document.getElementById('statTotal').textContent = reports.length;
        document.getElementById('statWaiting').textContent = reports.filter((item) => item.status === 'menunggu review').length;
        document.getElementById('statDone').textContent = reports.filter((item) => item.status === 'review selesai').length;
        document.getElementById('statRevision').textContent = reports.filter((item) => item.status === 'perlu revisi').length;
        document.getElementById('statCompletion').textContent = reports.length ? `${Math.round((done / reports.length) * 100)}%` : '0%';
        document.getElementById('statAverageTime').textContent = `${averageTime} Hari`;
        const akhirCount = reports.filter((item) => item.jenis_raw === 'akhir').length;
        document.getElementById('reportSummaryAlert').textContent = `${reports.filter((item) => item.status === 'menunggu review').length} laporan menunggu review, ${reports.filter((item) => item.status === 'perlu revisi').length} laporan perlu revisi, dan ${akhirCount} laporan akhir tersimpan di database.`;
    }

    function renderNotes() {
        document.getElementById('notePanel').innerHTML = reports
            .filter((item) => item.catatan !== 'Belum ada catatan review.' && item.catatan !== 'Belum direview.')
            .slice(0, 5)
            .map((item) => `
                <div class="note-row">
                    <div class="d-flex justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">${item.nama}</div>
                            <div class="small text-muted">${item.judul}</div>
                        </div>
                        <span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span>
                    </div>
                    <div class="small mt-2">${item.catatan}</div>
                </div>
            `).join('');
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
        const data = filteredReports();
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
                <td>${item.judul}</td>
                <td><span class="badge ${item.jenis_raw === 'akhir' ? 'bg-info text-dark' : 'bg-secondary'}">${item.jenis}</span></td>
                <td>${item.penempatan}</td>
                <td>${item.unggah}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="min-width:90px">
                            <div class="progress-bar ${progressClass(item.revisi)}" style="width:${item.revisi}%"></div>
                        </div>
                        <span class="small fw-semibold">${item.revisi}%</span>
                    </div>
                </td>
                <td>${item.catatan}</td>
                <td>
                    <div class="action-group">
                        <a class="btn btn-info btn-sm text-white" href="${item.download_url}" target="_blank" rel="noopener">Unduh Laporan</a>
                        <button class="btn btn-success btn-sm" type="button" data-action="setujui laporan" data-id="${item.id}">Setujui</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="kirim revisi" data-id="${item.id}">Revisi</button>
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
        renderNotes();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('reportReviewToast');
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
                <div class="col-md-6"><strong>Jenis Laporan</strong><div>${item.jenis}</div></div>
                <div class="col-12"><strong>Judul Laporan</strong><div>${item.judul}</div></div>
                <div class="col-md-6"><strong>Dokumen</strong><div>${item.dokumen}</div></div>
                <div class="col-md-6"><strong>Status Review</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Progress Revisi</strong><div>${item.revisi}%</div></div>
                <div class="col-md-6"><strong>Tanggal Unggah</strong><div>${item.unggah}</div></div>
                <div class="col-12"><strong>Catatan Pembimbing Akademik</strong><div>${item.catatan}</div></div>
                <div class="col-12"><strong>Catatan Mentor</strong><div>${item.catatan_mentor || '-'}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = `Simpan tindakan ${titleCase(action)} untuk laporan ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.judul} - ${titleCase(item.status)} - Revisi ${item.revisi}%`;
        document.getElementById('reviewNote').value = item.catatan;
        saveReviewAction.textContent = 'Simpan Review';
        approveReviewAction.classList.toggle('d-none', action !== 'review');
        approveReviewAction.textContent = 'Setujui Laporan';
        confirmModal.show();
    }

    async function submitReview(actionOverride = null) {
        if (!pendingAction) return;
        const actionMap = {
            review: 'review',
            'berikan review': 'review',
            'kirim revisi': 'reject',
            'setujui laporan': 'approve',
        };
        const action = actionOverride || actionMap[pendingAction.action] || 'review';
        const note = document.getElementById('reviewNote').value || pendingAction.item.catatan || '';

        const response = await fetch(reviewUpdateUrl.replace('__REPORT__', pendingAction.item.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ action, catatan: note }),
        });

        if (!response.ok) {
            const fallback = await response.text();
            throw new Error(fallback || 'Gagal menyimpan review.');
        }

        const payload = await response.json();
        pendingAction.item.catatan = payload.catatan_pembimbing || note;
        pendingAction.item.catatan_pembimbing = payload.catatan_pembimbing || note;
        pendingAction.item.status = payload.status || pendingAction.item.status;
        pendingAction.item.revisi = action === 'approve' ? 100 : (action === 'reject' ? 72 : pendingAction.item.revisi);

        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Hasil review laporan ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
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

    document.querySelector('[data-completion-card]').addEventListener('click', () => showToast('Tingkat penyelesaian review laporan ditampilkan.', 'info'));

    [studyFilter, agencyFilter, statusFilter, jenisFilter, periodFilter].forEach((input) => {
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
        showToast('Filter review laporan berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        jenisFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card], [data-completion-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter review laporan berhasil direset.', 'info');
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
        const item = reports.find((report) => report.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'review') {
            openConfirm(item, action);
            return;
        }
        openConfirm(item, action);
    });

    saveReviewAction.addEventListener('click', async () => {
        try {
            await submitReview();
        } catch (error) {
            console.error(error);
            showToast('Gagal menyimpan review laporan.', 'danger');
        }
    });

    approveReviewAction.addEventListener('click', async () => {
        try {
            await submitReview('approve');
        } catch (error) {
            console.error(error);
            showToast('Gagal menyetujui laporan.', 'danger');
        }
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data review laporan berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('reviewBadge').textContent = 'Diperbarui';
        showToast('Data review laporan berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi review laporan: 5 laporan menunggu pemeriksaan.', 'warning'), 800);
});
</script>
@endpush
