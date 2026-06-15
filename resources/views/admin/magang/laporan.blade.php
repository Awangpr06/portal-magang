@extends('admin.layout.admin')

@php
    $pageTitle = $pageTitle ?? 'Laporan Berkala';
    $isLaporanAkhir = $pageTitle === 'Laporan Akhir';
@endphp

@section('title', $pageTitle)

@push('styles')
<style>
    .report-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .report-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .report-page .stat-card,
    .report-page .filter-card,
    .report-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .report-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .report-page .stat-card:hover,
    .report-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .report-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .report-page .table {
        font-size: 14px;
    }

    .report-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 13px;
        white-space: nowrap;
    }

    .report-page .table tbody tr {
        transition: 0.15s ease;
    }

    .report-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .report-page .action-group {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        overflow-x: auto;
        min-width: 280px;
    }

    .report-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .report-page .pagination .page-link {
        color: #0b5f86;
    }

    .report-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid report-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">{{ $pageTitle }}</h2>
            <p class="text-muted mb-0">
                Monitoring, evaluasi, validasi, dan dokumentasi laporan peserta magang.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton">
                <i class="bi bi-download"></i>
                Ekspor Laporan
            </button>
            <button class="btn btn-primary" type="button" id="addReportButton">
                <i class="bi bi-plus-lg"></i>
                Tambah Laporan
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Laporan</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-files"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Disetujui</p>
                        <h3 class="mb-0" id="statDisetujui">0</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="review">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Dalam Review</p>
                        <h3 class="mb-0" id="statReview">0</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-search"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="ditolak">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Ditolak</p>
                        <h3 class="mb-0" id="statDitolak">0</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="perlu diperbaiki">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Perlu Diperbaiki</p>
                        <h3 class="mb-0" id="statRevisi">0</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-tools"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="reportFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Mahasiswa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari Mahasiswa">
                        </div>
                    </div>

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
                            <option value="Administrasi Publik">Administrasi Publik</option>
                            <option value="Akuntansi">Akuntansi</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="agencyFilter" class="form-label">Instansi</label>
                        <select class="form-select" id="agencyFilter">
                            <option value="semua">Semua Instansi</option>
                            <option value="Dinas Kominfo DIY">Dinas Kominfo DIY</option>
                            <option value="Bappeda DIY">Bappeda DIY</option>
                            <option value="Bank BPD DIY">Bank BPD DIY</option>
                            <option value="PT Inovasi Digital">PT Inovasi Digital</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="review">Dalam Review</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="perlu diperbaiki">Perlu Diperbaiki</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex gap-2">
                        <button class="btn btn-primary w-50" type="button" id="searchButton" title="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                        <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Data {{ $pageTitle }}</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Upload</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th>Judul Laporan</th>
                                @if($isLaporanAkhir)
                                    <th>Status</th>
                                @endif
                                <th width="{{ $isLaporanAkhir ? '420' : '220' }}">Aksi</th>
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
                <nav aria-label="Pagination {{ \Illuminate\Support\Str::slug($pageTitle, '-') }}">
                    <ul class="pagination mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail {{ $pageTitle }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Tambah Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="formName">Nama Mahasiswa</label>
                        <input class="form-control" id="formName" placeholder="Nama mahasiswa">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formNim">NIM</label>
                        <input class="form-control" id="formNim" placeholder="NIM">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formTitleInput">Judul Laporan</label>
                        <input class="form-control" id="formTitleInput" placeholder="Judul laporan">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveForm">Simpan</button>
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
                    <div class="w-100">
                        <p id="confirmMessage" class="mb-2">Apakah Anda yakin ingin melanjutkan?</p>
                        <textarea class="form-control d-none" id="revisionNote" rows="3" placeholder="Catatan revisi atau alasan penolakan"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="certificateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Sertifikat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Unggah sertifikat hanya untuk laporan akhir yang sudah disetujui.</p>
                <input type="file" class="form-control" id="certificateFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <div class="form-text">Format yang didukung: PDF, DOC, DOCX, JPG, JPEG, PNG.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="saveCertificate">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="reportToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const reports = @json($adminMagangReports ?? []);

    const perPage = 10;
    let currentPage = 1;
    let pendingAction = null;
    let editingId = null;
    let certificateReportId = null;

    const searchInput = document.getElementById('searchInput');
    const periodFilter = document.getElementById('periodFilter');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('reportTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');
    const revisionNote = document.getElementById('revisionNote');
    const certificateModal = new bootstrap.Modal(document.getElementById('certificateModal'));
    const certificateFile = document.getElementById('certificateFile');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const approvalBaseUrl = @json(url('/admin/manajemen-magang/laporan'));
    const certificateUploadBaseUrl = @json(url('/admin/manajemen-magang/laporan'));

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('reportToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            disetujui: 'bg-success',
            review: 'bg-info text-dark',
            ditolak: 'bg-danger',
            'perlu diperbaiki': 'bg-warning text-dark'
        }[value];
    }

    function statusLabel(value, report = null) {
        if (report?.status_label) {
            return report.status_label;
        }

        return value === 'review' ? 'Dalam Review' : titleCase(value);
    }

    function isLaporanAkhir() {
        return @json($isLaporanAkhir);
    }

    function canUploadCertificate(report) {
        return isLaporanAkhir()
            && ['approved', 'disetujui'].includes((report.status || '').toLowerCase())
            && (report.admin_approved_at || report.approval_done || report.approval_ready)
            && !report.sertifikat_url;
    }

    function filteredReports() {
        const keyword = searchInput.value.trim().toLowerCase();
        const period = periodFilter.value;
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;

        return reports.filter((report) => {
            const matchKeyword = !keyword || [report.nama, report.nim, report.prodi, report.instansi, report.periode, report.judul, report.status].join(' ').toLowerCase().includes(keyword);
            const matchPeriod = period === 'semua' || report.periode === period;
            const matchStudy = study === 'semua' || report.prodi === study;
            const matchAgency = agency === 'semua' || report.instansi === agency;
            const matchStatus = status === 'semua' || report.status === status;

            return matchKeyword && matchPeriod && matchStudy && matchAgency && matchStatus;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = reports.length;
        document.getElementById('statDisetujui').textContent = reports.filter((item) => ['approved', 'disetujui'].includes((item.status || '').toLowerCase())).length;
        document.getElementById('statReview').textContent = reports.filter((item) => item.status === 'review').length;
        document.getElementById('statDitolak').textContent = reports.filter((item) => item.status === 'ditolak').length;
        document.getElementById('statRevisi').textContent = reports.filter((item) => item.status === 'perlu diperbaiki').length;
    }

    function renderTable() {
        const data = filteredReports();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((report, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${report.tanggal}</td>
                <td class="fw-semibold">${report.nama}</td>
                <td>${report.nim}</td>
                <td>${report.judul}</td>
                ${isLaporanAkhir() ? `<td><span class="badge ${statusClass(report.status)}">${statusLabel(report.status, report)}</span></td>` : ''}
                <td>
                    <div class="action-group flex-nowrap">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${report.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${report.id}">Edit</button>
                        ${isLaporanAkhir() ? `<button class="btn btn-success btn-sm" type="button" data-action="setujui" data-id="${report.id}">Setujui</button>` : ''}
                        ${isLaporanAkhir()
                            ? `<button class="btn ${canUploadCertificate(report) ? 'btn-success' : 'btn-outline-secondary'} btn-sm" type="button" data-action="upload-sertifikat" data-id="${report.id}" ${canUploadCertificate(report) ? '' : 'disabled title="Aktif setelah laporan disetujui"'}>Upload Sertifikat</button>`
                            : ''}
                        ${isLaporanAkhir() && report.sertifikat_url ? `<button class="btn btn-outline-primary btn-sm" type="button" data-action="download-sertifikat" data-id="${report.id}">Unduh Sertifikat</button>` : ''}
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${report.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} laporan ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} laporan`
            : 'Menampilkan 0 laporan';

        renderPagination(totalPages);
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

    function showToast(message, success = true) {
        const toastElement = document.getElementById('reportToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const report = reports.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Mahasiswa</dt>
                <dd class="col-sm-8">${report.nama}</dd>
                <dt class="col-sm-4">NIM</dt>
                <dd class="col-sm-8">${report.nim}</dd>
                <dt class="col-sm-4">Program Studi</dt>
                <dd class="col-sm-8">${report.prodi}</dd>
                <dt class="col-sm-4">Instansi</dt>
                <dd class="col-sm-8">${report.instansi}</dd>
                <dt class="col-sm-4">Periode</dt>
                <dd class="col-sm-8">${report.periode}</dd>
                <dt class="col-sm-4">Tanggal Upload</dt>
                <dd class="col-sm-8">${report.tanggal}</dd>
                <dt class="col-sm-4">Judul Laporan</dt>
                <dd class="col-sm-8">${report.judul}</dd>
                ${isLaporanAkhir() ? `<dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge ${statusClass(report.status)}">${statusLabel(report.status, report)}</span></dd>` : `<dt class="col-sm-4">Dokumen</dt><dd class="col-sm-8">${report.dokumen}</dd>`}
                ${isLaporanAkhir() ? `<dt class="col-sm-4">Sertifikat</dt><dd class="col-sm-8">${report.sertifikat?.nomor || report.sertifikat || '-'}</dd><dt class="col-sm-4">Predikat</dt><dd class="col-sm-8">${report.sertifikat?.predikat || '-'}</dd>` : ''}
            </dl>
            <div class="card border mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-1">Aksi Dokumen</h6>
                    <p class="text-muted small mb-3">${isLaporanAkhir() ? 'Setujui laporan akhir dan unggah sertifikat setelah penilaian selesai.' : 'Unduh dokumen laporan atau kirim permintaan revisi kepada peserta.'}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-secondary" type="button" data-detail-action="unduh" data-id="${report.id}">
                            <i class="bi bi-download me-1"></i> Unduh Laporan
                        </button>
                        ${isLaporanAkhir()
                            ? `<button class="btn btn-success" type="button" data-detail-action="setujui" data-id="${report.id}"><i class="bi bi-check2-circle me-1"></i> Setujui</button><button class="btn ${canUploadCertificate(report) ? 'btn-success' : 'btn-outline-secondary'}" type="button" data-detail-action="upload-sertifikat" data-id="${report.id}" ${canUploadCertificate(report) ? '' : 'disabled title="Aktif setelah laporan disetujui"'}><i class="bi bi-upload me-1"></i> Upload Sertifikat</button>${report.sertifikat_url ? `<button class="btn btn-outline-primary" type="button" data-detail-action="download-sertifikat" data-id="${report.id}"><i class="bi bi-cloud-arrow-down me-1"></i> Unduh Sertifikat</button>` : ''}`
                            : `<button class="btn btn-warning" type="button" data-detail-action="revisi" data-id="${report.id}"><i class="bi bi-arrow-repeat me-1"></i> Revisi</button>`
                        }
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const report = id ? reports.find((item) => item.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Laporan' : 'Tambah Laporan';
        document.getElementById('formName').value = report?.nama || '';
        document.getElementById('formNim').value = report?.nim || '';
        document.getElementById('formTitleInput').value = report?.judul || '';
        formModal.show();
    }

    function showConfirm(id, action) {
        const report = reports.find((item) => item.id === id);
        pendingAction = { id, action };
        revisionNote.classList.toggle('d-none', !['revisi', 'ditolak'].includes(action));
        revisionNote.value = '';

        const labels = {
            setujui: ['Konfirmasi Persetujuan Laporan', `Setujui laporan "${report.judul}" milik ${report.nama}?`],
            revisi: ['Konfirmasi Permintaan Revisi', `Minta perbaikan untuk laporan "${report.judul}" milik ${report.nama}?`],
            hapus: ['Konfirmasi Hapus Laporan', `Hapus laporan "${report.judul}" milik ${report.nama}?`]
        };

        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    [periodFilter, studyFilter, agencyFilter, statusFilter].forEach((input) => {
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

    document.getElementById('searchButton').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        periodFilter.value = 'semua';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Data laporan berhasil disiapkan untuk ekspor.');
    });

    document.getElementById('addReportButton').addEventListener('click', () => openForm());

    document.getElementById('saveForm').addEventListener('click', () => {
        const name = document.getElementById('formName').value.trim();
        const nim = document.getElementById('formNim').value.trim();
        const title = document.getElementById('formTitleInput').value.trim();

        if (!name || !nim || !title) {
            showToast('Nama, NIM, dan judul laporan wajib diisi.', false);
            return;
        }

        if (editingId) {
            const report = reports.find((item) => item.id === editingId);
            report.nama = name;
            report.nim = nim;
            report.judul = title;
            showToast(`Laporan ${report.nama} berhasil diperbarui.`);
        } else {
            reports.unshift({
                id: Date.now(),
                tanggal: '24 Mei 2026',
                nama: name,
                nim,
                prodi: 'Informatika',
                instansi: 'Dinas Kominfo DIY',
                periode: 'Batch 1 2026',
                judul: title,
                status: 'review',
                dokumen: 'laporan-baru.pdf'
            });
            showToast(`Laporan ${name} berhasil ditambahkan.`);
        }

        formModal.hide();
        updateStats();
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        const action = button.dataset.action;

        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'edit') {
            openForm(id);
        } else if (action === 'upload-sertifikat') {
            const report = reports.find((item) => item.id === id);
            if (!canUploadCertificate(report)) {
                showToast('Upload sertifikat aktif setelah laporan akhir disetujui.', false);
                return;
            }

            certificateReportId = id;
            certificateFile.value = '';
            certificateModal.show();
        } else if (action === 'unduh') {
            const report = reports.find((item) => item.id === id);
            showToast(`Dokumen ${report.dokumen} berhasil disiapkan.`);
        } else if (action === 'download-sertifikat') {
            const report = reports.find((item) => item.id === id);
            if (!report?.sertifikat_url) {
                showToast('Sertifikat belum tersedia.', false);
                return;
            }

            window.open(report.sertifikat_url, '_blank', 'noopener');
        } else {
            showConfirm(id, action);
        }
    });

    document.getElementById('detailContent').addEventListener('click', (event) => {
        const button = event.target.closest('button[data-detail-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        const action = button.dataset.detailAction;
        const report = reports.find((item) => item.id === id);

        if (action === 'unduh') {
            showToast(`Dokumen ${report.dokumen} berhasil disiapkan.`);
            return;
        }

        if (action === 'download-sertifikat') {
            if (!report?.sertifikat_url) {
                showToast('Sertifikat belum tersedia.', false);
                return;
            }

            window.open(report.sertifikat_url, '_blank', 'noopener');
            return;
        }

        if (action === 'upload-sertifikat') {
            if (!canUploadCertificate(report)) {
                showToast('Upload sertifikat aktif setelah laporan akhir disetujui.', false);
                return;
            }

            certificateReportId = id;
            certificateFile.value = '';
            certificateModal.show();
            return;
        }

        detailModal.hide();
        showConfirm(id, action);
    });

    document.getElementById('saveCertificate').addEventListener('click', () => {
        if (!certificateReportId) {
            return;
        }

        const report = reports.find((item) => item.id === certificateReportId);
        const file = certificateFile.files[0];

        if (!file) {
            showToast('Pilih file sertifikat terlebih dahulu.', false);
            return;
        }

        const formData = new FormData();
        formData.append('certificate', file);
        formData.append('_token', csrfToken);

        const uploadUrl = `${certificateUploadBaseUrl}/${certificateReportId}/sertifikat`;

        fetch(uploadUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: formData,
        })
            .then(async (response) => {
                const payload = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(payload?.message || 'Gagal mengunggah sertifikat.');
                }

                return payload;
            })
            .then((payload) => {
                report.sertifikat = payload.certificate;
                report.sertifikat_url = payload.certificate.download_url;
                report.sertifikat_file = payload.certificate.file_name;
                certificateModal.hide();
                certificateReportId = null;
                showToast(payload.message || `Sertifikat ${payload.certificate.file_name} berhasil diunggah.`);
                renderTable();
            })
            .catch((error) => {
                showToast(error.message, false);
            });
    });

    document.getElementById('confirmAction').addEventListener('click', async () => {
        if (!pendingAction) {
            return;
        }

        const index = reports.findIndex((item) => item.id === pendingAction.id);
        const report = reports[index];
        let message = '';

        if (pendingAction.action === 'hapus') {
            reports.splice(index, 1);
            message = `Laporan ${report.nama} berhasil dihapus.`;
        } else if (pendingAction.action === 'setujui') {
            try {
                const response = await fetch(`${approvalBaseUrl}/${report.id}/approve`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({}),
                });

                const payload = await response.json().catch(() => null);

                if (!response.ok) {
                    throw new Error(payload?.message || 'Gagal menyetujui laporan.');
                }

                report.status = payload.status || 'approved';
                report.status_label = payload.status_label || 'Disetujui';
                report.admin_approved_at = payload.admin_approved_at || report.admin_approved_at || new Date().toISOString();
                report.approval_done = true;
                message = payload.message || `Laporan ${report.nama} berhasil disetujui.`;
            } catch (error) {
                showToast(error.message || 'Gagal menyetujui laporan.', false);
                return;
            }
        } else if (pendingAction.action === 'revisi') {
            report.status = 'perlu diperbaiki';
            message = `Permintaan revisi laporan ${report.nama} berhasil dikirim.`;
        }

        confirmModal.hide();
        updateStats();
        renderTable();
        showToast(message);
        pendingAction = null;
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    updateStats();
    renderTable();
</script>
@endpush
