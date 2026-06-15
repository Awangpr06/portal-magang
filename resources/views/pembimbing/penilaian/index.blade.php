@extends('pembimbing.layout.pembimbing')

@section('title', 'Penilaian')
@section('page-title', 'Penilaian')

@push('styles')
<style>
    .assessment-page .assessment-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .assessment-page .stat-card,
    .assessment-page .filter-card,
    .assessment-page .table-card,
    .assessment-page .side-panel { border:0; border-radius:8px; }
    .assessment-page .stat-card { cursor:pointer; transition:.2s ease; }
    .assessment-page .stat-card:hover,
    .assessment-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .assessment-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .assessment-page .table { font-size:14px; }
    .assessment-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .assessment-page .table tbody tr:hover { background:#f7fcfe; }
    .assessment-page .progress { height:10px; border-radius:999px; }
    .assessment-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:0; }
    .assessment-page .distribution-row,
    .assessment-page .activity-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .assessment-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .assessment-page .pagination .page-link { color:#2a8fbd; }
    .assessment-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $mode = $mode ?? 'utama';
    $pageTitle = $mode === 'rekap' ? 'Rekap Nilai' : ($mode === 'input' ? 'Input Nilai' : 'Penilaian Mahasiswa');
@endphp

<div class="container-fluid assessment-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
        </ol>
    </nav>

    <section class="assessment-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">{{ $pageTitle }}</h3>
                <p class="mb-0">Evaluasi capaian magang mahasiswa berdasarkan indikator penilaian agar hasil akademik objektif, terukur, dan terdokumentasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="assessmentBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-award-fill"></i>
        <div>Beberapa mahasiswa sudah dinilai, sebagian masih menunggu input nilai, dan sebagian lain perlu revisi.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Dinilai</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="sudah dinilai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sudah Dinilai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="belum dinilai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dinilai</p><h3 class="mb-0" id="statWaiting">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-score-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Nilai Rata-rata</p><h3 class="mb-0" id="statAverage">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-bar-chart"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-score-card="highest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Nilai Tertinggi</p><h3 class="mb-0" id="statHighest">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-trophy"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-completion-card="done">
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
                    <label class="form-label" for="searchInput">Mahasiswa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, atau keterangan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="studyFilter">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        @foreach (($studyOptionsData ?? []) as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="agencyFilter">Instansi</label>
                    <select class="form-select" id="agencyFilter">
                        <option value="semua">Semua Instansi</option>
                        @foreach (($instansiOptionsData ?? []) as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Penilaian</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="sudah dinilai">Sudah Dinilai</option>
                        <option value="belum dinilai">Belum Dinilai</option>
                        <option value="perlu revisi">Perlu Revisi</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <label class="form-label" for="scoreFilter">Nilai</label>
                    <select class="form-select" id="scoreFilter">
                        <option value="semua">Semua</option>
                        <option value="tinggi">&gt;= 85</option>
                        <option value="sedang">70-84</option>
                        <option value="rendah">&lt; 70</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua</option>
                        @foreach (($periodOptionsData ?? []) as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
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
                            <h5 class="mb-1">Daftar Penilaian Mahasiswa</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePanelButton" data-bs-toggle="offcanvas" data-bs-target="#assessmentExtraPanel" aria-controls="assessmentExtraPanel">
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
                                    <th>Instansi Magang</th>
                                    <th>Kehadiran</th>
                                    <th>Aktivitas</th>
                                    <th>Laporan</th>
                                    <th>Nilai Akhir</th>
                                    <th>Grade</th>
                                    <th>Status Penilaian</th>
                                    <th width="240">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="assessmentTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data penilaian tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination penilaian">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="assessmentExtraPanel" aria-labelledby="assessmentExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="assessmentExtraPanelLabel">Panel Penilaian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Nilai</h5>
                <div id="distributionPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">Aktivitas Penilaian</h5>
                <div id="activityPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Penilaian</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div class="w-100">
                        <p class="mb-2" id="confirmMessage">Simpan hasil penilaian?</p>
                        <div class="small text-muted mb-2" id="confirmSummary"></div>
                        <label class="form-label" for="evaluationNote">Catatan Evaluasi</label>
                        <textarea class="form-control" id="evaluationNote" rows="3" placeholder="Tambahkan catatan evaluasi"></textarea>
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

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="assessmentToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const assessments = @json($assessmentRowsData ?? []);

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const scoreFilter = document.getElementById('scoreFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('assessmentTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('assessmentToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function gradeFromScore(score) {
        if (score >= 90) return 'A';
        if (score >= 85) return 'B+';
        if (score >= 75) return 'B';
        if (score >= 70) return 'C+';
        if (score > 0) return 'C';
        return '-';
    }

    function normalizeStatus(item) {
        if (item.status === 'sudah dinilai' || item.final >= 85) return 'sudah dinilai';
        if (item.status === 'perlu revisi' || (item.final > 0 && item.final < 75)) return 'perlu revisi';
        return 'belum dinilai';
    }

    function statusClass(value) {
        return {
            'sudah dinilai': 'bg-success',
            'belum dinilai': 'bg-secondary',
            'perlu revisi': 'bg-warning text-dark'
        }[value];
    }

    function scoreClass(value) {
        if (value >= 85) return 'bg-success';
        if (value >= 70) return 'bg-info';
        if (value > 0) return 'bg-warning';
        return 'bg-secondary';
    }

    function scoreMatch(value, filter) {
        if (filter === 'tinggi') return value >= 85;
        if (filter === 'sedang') return value >= 70 && value < 85;
        if (filter === 'rendah') return value > 0 && value < 70;
        return true;
    }

    function filteredAssessments() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const score = scoreFilter.value;
        const period = periodFilter.value;
        return assessments.filter((item) => {
            const displayStatus = normalizeStatus(item);
            const rekapMode = initialMode === 'rekap' ? displayStatus !== 'belum dinilai' : true;
            const inputMode = initialMode === 'input' ? displayStatus !== 'sudah dinilai' : true;
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.instansi, item.presence, item.activity, item.report, item.final, item.note, displayStatus].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.instansi === agency;
            const matchStatus = status === 'semua' || displayStatus === status;
            const matchScore = scoreMatch(item.final, score);
            const matchPeriod = period === 'semua' || item.periode === period;
            return rekapMode && inputMode && matchKeyword && matchStudy && matchAgency && matchStatus && matchScore && matchPeriod;
        });
    }

    function updateStats() {
        const valued = assessments.filter((item) => item.final > 0);
        const done = assessments.filter((item) => normalizeStatus(item) === 'sudah dinilai').length;
        const average = valued.length ? Math.round(valued.reduce((sum, item) => sum + item.final, 0) / valued.length) : 0;
        const highest = valued.length ? Math.max(...valued.map((item) => item.final)) : 0;
        document.getElementById('statTotal').textContent = assessments.length;
        document.getElementById('statDone').textContent = done;
        document.getElementById('statWaiting').textContent = assessments.filter((item) => normalizeStatus(item) === 'belum dinilai').length;
        document.getElementById('statAverage').textContent = average;
        document.getElementById('statHighest').textContent = highest;
        document.getElementById('statCompletion').textContent = assessments.length ? `${Math.round((done / assessments.length) * 100)}%` : '0%';
    }

    function renderPanels() {
        const groups = [
            ['A', assessments.filter((item) => item.final >= 85).length, 'bg-success'],
            ['B', assessments.filter((item) => item.final >= 70 && item.final < 85).length, 'bg-info'],
            ['C', assessments.filter((item) => item.final > 0 && item.final < 70).length, 'bg-warning'],
            ['Belum Dinilai', assessments.filter((item) => item.final === 0).length, 'bg-secondary']
        ];
        const maxValue = Math.max(...groups.map((item) => item[1]), 1);
        document.getElementById('distributionPanel').innerHTML = groups.map(([label, count, color]) => `
            <div class="distribution-row">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">${label}</span>
                    <span>${count} mahasiswa</span>
                </div>
                <div class="progress">
                    <div class="progress-bar ${color}" style="width:${Math.round((count / maxValue) * 100)}%"></div>
                </div>
            </div>
        `).join('');

        document.getElementById('activityPanel').innerHTML = assessments
            .filter((item) => item.final !== 0)
            .slice(0, 5)
            .map((item) => `
                <div class="activity-row">
                    <div class="d-flex justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">${item.nama}</div>
                            <div class="small text-muted">${item.periode} - Nilai ${item.final}</div>
                        </div>
                        <span class="badge ${statusClass(normalizeStatus(item))}">${titleCase(normalizeStatus(item))}</span>
                    </div>
                    <div class="small mt-2">${item.note}</div>
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
        const data = filteredAssessments();
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
                <td>${item.instansi}</td>
                <td><span class="badge ${scoreClass(item.presence)}">${item.presence || '-'}</span></td>
                <td><span class="badge ${scoreClass(item.activity)}">${item.activity || '-'}</span></td>
                <td><span class="badge ${scoreClass(item.report)}">${item.report || '-'}</span></td>
                <td><span class="badge ${scoreClass(item.final)}">${item.final || '-'}</span></td>
                <td><span class="badge bg-dark">${item.grade || gradeFromScore(item.final)}</span></td>
                <td><span class="badge ${statusClass(normalizeStatus(item))}">${titleCase(normalizeStatus(item))}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-warning" type="button" data-action="score" data-id="${item.id}"><i class="bi bi-pencil-square"></i> Input Nilai</button>
                        <button class="btn btn-sm btn-outline-warning" type="button" data-action="edit" data-id="${item.id}"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-id="${item.id}"><i class="bi bi-eye"></i> Detail</button>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-action="pdf" data-id="${item.id}"><i class="bi bi-filetype-pdf"></i></button>
                        <button class="btn btn-sm btn-outline-success" type="button" data-action="excel" data-id="${item.id}"><i class="bi bi-file-earmark-spreadsheet"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} data penilaian ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} penilaian`
            : 'Menampilkan 0 penilaian';
        renderPagination(totalPages);
        updateStats();
        renderPanels();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('assessmentToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'detail') {
        const titles = {
            input: 'Input Nilai',
            edit: 'Edit Nilai',
            detail: 'Detail Penilaian',
        };
        document.getElementById('detailTitle').textContent = titles[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Instansi</strong><div>${item.instansi}</div></div>
                <div class="col-md-3"><strong>Kehadiran</strong><div>${item.presence || '-'}</div></div>
                <div class="col-md-3"><strong>Aktivitas</strong><div>${item.activity || '-'}</div></div>
                <div class="col-md-3"><strong>Laporan</strong><div>${item.report || '-'}</div></div>
                <div class="col-md-3"><strong>Nilai Akhir</strong><div>${item.final || '-'}</div></div>
                <div class="col-md-6"><strong>Grade</strong><div>${item.grade || gradeFromScore(item.final)}</div></div>
                <div class="col-md-6"><strong>Status Penilaian</strong><div>${titleCase(normalizeStatus(item))}</div></div>
                <div class="col-12"><strong>Keterangan</strong><div>${item.note}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = action === 'edit'
            ? `Perbarui nilai akhir ${item.nama}?`
            : `Simpan nilai akhir ${item.final || '-'} dengan grade ${item.grade || gradeFromScore(item.final)}?`;
        document.getElementById('confirmSummary').textContent = `${item.nama} - ${item.periode} - ${titleCase(normalizeStatus(item))}`;
        document.getElementById('evaluationNote').value = item.note;
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card], [data-score-card], [data-completion-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-score-card="highest"]').addEventListener('click', () => {
        scoreFilter.value = 'tinggi';
        currentPage = 1;
        renderTable();
    });
    document.querySelector('[data-score-card="average"]').addEventListener('click', () => showToast('Nilai rata-rata seluruh mahasiswa ditampilkan.', 'info'));
    document.querySelector('[data-completion-card]').addEventListener('click', () => showToast('Persentase penyelesaian penilaian ditampilkan.', 'info'));

    [studyFilter, agencyFilter, statusFilter, scoreFilter, periodFilter].forEach((input) => {
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
        showToast('Filter penilaian berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        scoreFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card], [data-score-card], [data-completion-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter penilaian berhasil direset.', 'info');
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
        const item = assessments.find((assessment) => assessment.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item, action);
            return;
        }
        if (action === 'pdf' || action === 'excel') {
            showToast(`${action === 'pdf' ? 'Export PDF' : 'Export Excel'} untuk ${item.nama} disiapkan.`, 'info');
            return;
        }
        if (action === 'score' || action === 'edit') {
            openConfirm(item, action);
            return;
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.note = document.getElementById('evaluationNote').value || pendingAction.item.note;
        if ((pendingAction.action === 'score' || pendingAction.action === 'edit') && pendingAction.item.final === 0) {
            pendingAction.item.presence = 80;
            pendingAction.item.activity = 80;
            pendingAction.item.report = 80;
            pendingAction.item.final = 80;
            pendingAction.item.grade = gradeFromScore(80);
            pendingAction.item.status = 'sudah dinilai';
        }
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Penilaian ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data penilaian berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('assessmentBadge').textContent = 'Diperbarui';
        showToast('Data penilaian berhasil diperbarui.', 'info');
    });

    const assessmentExtraPanel = document.getElementById('assessmentExtraPanel');
    const togglePanelButton = document.getElementById('togglePanelButton');
    assessmentExtraPanel.addEventListener('show.bs.offcanvas', () => {
        togglePanelButton.innerHTML = '<i class="bi bi-layout-sidebar-inset-reverse"></i> Sembunyikan Panel';
    });
    assessmentExtraPanel.addEventListener('hide.bs.offcanvas', () => {
        togglePanelButton.innerHTML = '<i class="bi bi-layout-sidebar-inset"></i> Tampilkan Panel';
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi penilaian: 4 mahasiswa menunggu input nilai.', 'warning'), 800);
});
</script>
@endpush
