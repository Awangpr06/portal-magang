@extends('mentor.layout.mentor')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@push('styles')
<style>
    .mentor-report-page .report-banner {
        background: linear-gradient(135deg, #2a8fbd, #1f739a);
        color: #fff;
        border-radius: 8px;
    }
    .mentor-report-page .stat-card,
    .mentor-report-page .filter-card,
    .mentor-report-page .table-card,
    .mentor-report-page .side-panel {
        border: 0;
        border-radius: 8px;
    }
    .mentor-report-page .stat-card {
        cursor: pointer;
        transition: .2s ease;
    }
    .mentor-report-page .stat-card:hover,
    .mentor-report-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, .16);
    }
    .mentor-report-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }
    .mentor-report-page .table {
        font-size: 14px;
    }
    .mentor-report-page .table thead th {
        background: #eef8fc;
        color: #3b5664;
        font-size: 13px;
        white-space: nowrap;
    }
    .mentor-report-page .table tbody tr:hover {
        background: #f7fcfe;
    }
    .mentor-report-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-width: 430px;
    }
    .mentor-report-page .note-row {
        border: 1px solid #e2ebef;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        background: #fbfdfe;
    }
    .mentor-report-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }
    .mentor-report-page .progress {
        height: 10px;
        border-radius: 999px;
    }
    .mentor-report-page .pagination .page-link {
        color: #2a8fbd;
    }
    .mentor-report-page .pagination .active .page-link {
        background: #2a8fbd;
        border-color: #2a8fbd;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mentor-report-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Laporan</li>
        </ol>
    </nav>

    <section class="report-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Laporan Peserta Magang</h3>
                <p class="mb-0">Periksa laporan peserta, beri catatan mentor, setujui, atau minta revisi sebelum data dipakai untuk tindak lanjut.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="reportBadge">Real-time</span>
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
                    <div><p class="text-muted mb-1">Menunggu</p><h3 class="mb-0" id="statWaiting">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="review selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Catatan Tersimpan</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-check2-circle"></i></span>
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
            <div class="card stat-card" data-status-card="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Disetujui</p><h3 class="mb-0" id="statApproved">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-award"></i></span>
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
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="agencyFilter">Penempatan</label>
                    <select class="form-select" id="agencyFilter">
                        <option value="semua">Semua Penempatan</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu review">Menunggu Review</option>
                        <option value="review selesai">Catatan Tersimpan</option>
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
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mentorLaporanPanel" aria-controls="mentorLaporanPanel">
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
                                    <th>Status</th>
                                    <th>Catatan Mentor</th>
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
                        <nav aria-label="Pagination laporan mentor">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mentorLaporanPanel" aria-labelledby="mentorLaporanPanelLabel" style="width:min(92vw, 520px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="mentorLaporanPanelLabel">Panel Laporan Mentor</h5>
            <small class="text-muted">Ringkasan catatan dan tindak lanjut laporan</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-3">
            <div class="card-body">
                <h5 class="mb-3">Catatan Terbaru</h5>
                <div id="notePanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Status</h5>
                <div id="distributionPanel"></div>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Tindakan Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <i class="bi bi-journal-check text-primary fs-3"></i>
                            <div class="w-100">
                                <p class="mb-2" id="confirmMessage">Simpan catatan mentor?</p>
                                <div class="small text-muted mb-2" id="confirmSummary"></div>
                                <label class="form-label" for="reviewNote">Catatan Mentor</label>
                                <textarea class="form-control" id="reviewNote" rows="3" placeholder="Tambahkan catatan mentor"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveReviewAction">Simpan Catatan</button>
                <button type="button" class="btn btn-success" id="approveReviewAction">Setujui</button>
                <button type="button" class="btn btn-danger" id="rejectReviewAction">Minta Revisi</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="mentorReportToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const exportUrl = @json(route('mentor.laporan.export'));

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
    const rejectReviewAction = document.getElementById('rejectReviewAction');
    const toast = new bootstrap.Toast(document.getElementById('mentorReportToast'), { delay: 3000 });
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const reviewUpdateUrl = @json(route('mentor.laporan.update', ['report' => '__REPORT__']));

    function titleCase(value) {
        return (value || '')
            .split(' ')
            .filter(Boolean)
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function statusClass(value) {
        return {
            'menunggu review': 'bg-warning text-dark',
            'review selesai': 'bg-info text-dark',
            'perlu revisi': 'bg-danger',
            disetujui: 'bg-success',
        }[value] || 'bg-secondary';
    }

    function progressClass(value) {
        if (value >= 80) return 'bg-success';
        if (value >= 50) return 'bg-info';
        return 'bg-warning';
    }

    function periodGroup(dateString) {
        if (!dateString) return 'semua';
        const value = new Date(dateString);
        if (Number.isNaN(value.getTime())) return 'semua';
        const today = new Date();
        const diffDays = Math.floor((today.setHours(0, 0, 0, 0) - value.setHours(0, 0, 0, 0)) / 86400000);
        if (diffDays === 0) return 'hari ini';
        if (Math.abs(diffDays) <= 7) return 'minggu ini';
        if (Math.abs(diffDays) <= 30) return 'bulan ini';
        return 'semua';
    }

    function filteredReports() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const jenis = jenisFilter.value;
        const period = periodFilter.value;

        return reports.filter((item) => {
            const matchKeyword = !keyword || [
                item.nama,
                item.nim,
                item.prodi,
                item.penempatan,
                item.judul,
                item.status,
                item.jenis,
                item.catatan,
            ].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchJenis = jenis === 'semua' || item.jenis_raw === jenis;
            const matchPeriod = period === 'semua' || periodGroup(item.unggah_raw) === period;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchJenis && matchPeriod;
        });
    }

    function updateStats() {
        const total = reports.length;
        const done = reports.filter((item) => ['review selesai', 'disetujui'].includes(item.status)).length;
        const waiting = reports.filter((item) => item.status === 'menunggu review').length;
        const revision = reports.filter((item) => item.status === 'perlu revisi').length;
        const approved = reports.filter((item) => item.status === 'disetujui').length;
        const reviewedReports = reports.filter((item) => item.reviewTime > 0);
        const averageTime = reviewedReports.length
            ? Math.round(reviewedReports.reduce((sum, item) => sum + item.reviewTime, 0) / reviewedReports.length)
            : 0;

        document.getElementById('statTotal').textContent = total;
        document.getElementById('statWaiting').textContent = waiting;
        document.getElementById('statDone').textContent = done;
        document.getElementById('statRevision').textContent = revision;
        document.getElementById('statApproved').textContent = approved;
        document.getElementById('statAverageTime').textContent = `${averageTime} Hari`;
        document.getElementById('reportSummaryAlert').textContent = `${waiting} laporan menunggu review, ${revision} laporan perlu revisi, dan ${approved} laporan sudah disetujui mentor.`;
    }

    function populateFilters() {
        const studyOptions = [...new Set(reports.map((item) => item.prodi).filter((value) => value && value !== '-'))];
        const agencyOptions = [...new Set(reports.map((item) => item.penempatan).filter((value) => value && value !== '-'))];
        studyFilter.innerHTML = '<option value="semua">Semua Prodi</option>' + studyOptions.map((value) => `<option value="${value}">${value}</option>`).join('');
        agencyFilter.innerHTML = '<option value="semua">Semua Penempatan</option>' + agencyOptions.map((value) => `<option value="${value}">${value}</option>`).join('');
    }

    function renderNotes() {
        document.getElementById('notePanel').innerHTML = reports
            .filter((item) => item.catatan !== 'Belum ada catatan mentor.' && item.catatan !== 'Belum direview.')
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

    function renderDistribution() {
        const groups = [
            ['Menunggu Review', reports.filter((item) => item.status === 'menunggu review').length],
            ['Catatan Tersimpan', reports.filter((item) => item.status === 'review selesai').length],
            ['Perlu Revisi', reports.filter((item) => item.status === 'perlu revisi').length],
            ['Disetujui', reports.filter((item) => item.status === 'disetujui').length],
        ];

        document.getElementById('distributionPanel').innerHTML = groups.map(([label, count]) => `
            <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">
                <span>${label}</span>
                <strong>${count}</strong>
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
                    <div class="small text-muted mt-1">${item.catatan}</div>
                </td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <a class="btn btn-outline-info btn-sm" href="${item.download_url}" target="_blank" rel="noopener">Unduh</a>
                        <button class="btn btn-primary btn-sm" type="button" data-action="review" data-id="${item.id}">Catatan</button>
                        <button class="btn btn-success btn-sm" type="button" data-action="approve" data-id="${item.id}">Setujui</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="reject" data-id="${item.id}">Minta Revisi</button>
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
        renderDistribution();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('mentorReportToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item) {
        document.getElementById('detailTitle').textContent = 'Detail Laporan';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Penempatan</strong><div>${item.penempatan}</div></div>
                <div class="col-md-6"><strong>Jenis Laporan</strong><div>${item.jenis}</div></div>
                <div class="col-12"><strong>Judul Laporan</strong><div>${item.judul}</div></div>
                <div class="col-md-6"><strong>Status</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Tanggal Unggah</strong><div>${item.unggah}</div></div>
                <div class="col-md-6"><strong>Rata-rata Review</strong><div>${item.reviewTime} Hari</div></div>
                <div class="col-md-6"><strong>Reviewer</strong><div>${item.reviewer}</div></div>
                <div class="col-12"><strong>Catatan Mentor</strong><div>${item.catatan_mentor}</div></div>
                <div class="col-12"><strong>Catatan Pembimbing</strong><div>${item.catatan_pembimbing}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = action === 'review'
            ? 'Simpan catatan mentor untuk laporan ini?'
            : action === 'approve'
                ? 'Setujui laporan ini?'
                : 'Minta revisi untuk laporan ini?';
        document.getElementById('confirmSummary').textContent = `${item.nama} - ${item.judul} - ${titleCase(item.status)}`;
        document.getElementById('reviewNote').value = item.catatan_mentor === '-' ? item.catatan : item.catatan_mentor;
        saveReviewAction.classList.toggle('d-none', action !== 'review');
        approveReviewAction.classList.toggle('d-none', action !== 'approve');
        rejectReviewAction.classList.toggle('d-none', action !== 'reject');
        confirmModal.show();
    }

    async function submitReview(actionOverride = null) {
        if (!pendingAction) return;

        const action = actionOverride || pendingAction.action || 'review';
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
            throw new Error(fallback || 'Gagal menyimpan laporan.');
        }

        const payload = await response.json();
        pendingAction.item.catatan_mentor = payload.catatan_mentor || note;
        pendingAction.item.catatan = payload.catatan_mentor || note;
        pendingAction.item.status = payload.status || pendingAction.item.status;
        pendingAction.item.revisi = action === 'approve'
            ? 100
            : (action === 'reject' ? 72 : (pendingAction.item.status === 'review selesai' ? 88 : pendingAction.item.revisi));

        confirmModal.hide();
        renderTable();
        showToast(`Laporan ${pendingAction.item.nama} berhasil diperbarui.`);
        pendingAction = null;
    }

    populateFilters();
    renderTable();

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

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
        showToast('Filter laporan berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        jenisFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter laporan berhasil direset.', 'info');
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
        if (!item) return;
        const action = button.dataset.action;

        if (action === 'detail') {
            openDetail(item);
            return;
        }

        openConfirm(item, action);
    });

    saveReviewAction.addEventListener('click', async () => {
        try {
            await submitReview('review');
        } catch (error) {
            console.error(error);
            showToast('Gagal menyimpan catatan mentor.', 'danger');
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

    rejectReviewAction.addEventListener('click', async () => {
        try {
            await submitReview('reject');
        } catch (error) {
            console.error(error);
            showToast('Gagal mengajukan revisi laporan.', 'danger');
        }
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        window.location.href = exportUrl;
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        window.location.reload();
    });

    setTimeout(() => showToast('Notifikasi laporan: ada laporan baru dari peserta.', 'warning'), 800);
});
</script>
@endpush
