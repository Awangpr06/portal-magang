@extends('pembimbing.layout.pembimbing')

@section('title', 'Input Nilai')
@section('page-title', 'Input Nilai')

@push('styles')
<style>
    .score-input-page .score-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .score-input-page .stat-card,
    .score-input-page .filter-card,
    .score-input-page .table-card,
    .score-input-page .side-panel,
    .score-input-page .form-card { border:0; border-radius:8px; }
    .score-input-page .stat-card { cursor:pointer; transition:.2s ease; }
    .score-input-page .stat-card:hover,
    .score-input-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .score-input-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .score-input-page .table { font-size:14px; }
    .score-input-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .score-input-page .table tbody tr:hover { background:#f7fcfe; }
    .score-input-page .progress { height:10px; border-radius:999px; }
    .score-input-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:390px; }
    .score-input-page .weight-row,
    .score-input-page .history-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .score-input-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .score-input-page .pagination .page-link { color:#2a8fbd; }
    .score-input-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid score-input-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Input Nilai</li>
        </ol>
    </nav>

    <section class="score-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Input Nilai Mahasiswa</h3>
                <p class="mb-0">Isi, kelola, hitung otomatis, dan validasi nilai magang berdasarkan komponen penilaian yang telah ditentukan.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="scoreBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-award-fill"></i>
        <div>Data input nilai dimuat langsung dari database sesuai peserta yang terhubung dengan pembimbing akademik.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mahasiswa</p><h3 class="mb-0" id="statTotal">0</h3></div>
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
            <div class="card stat-card" data-score-card="highest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Nilai Tertinggi</p><h3 class="mb-0" id="statHighest">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-trophy"></i></span>
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
                        <option value="draft">Draft</option>
                        <option value="review">Review</option>
                        <option value="final">Final</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode Magang</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
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
                            <h5 class="mb-1">Tabel Mahasiswa dan Komponen Nilai</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#scoreInputExtraPanel" aria-controls="scoreInputExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Panel
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
                                    <th>Nama Peserta</th>
                                    <th>Kehadiran</th>
                                    <th>Aktivitas</th>
                                    <th>Laporan</th>
                                    <th>Sikap</th>
                                    <th>Kompetensi</th>
                                    <th>Nilai Akhir</th>
                                    <th>Status Penilaian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="scoreTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data mahasiswa tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination input nilai">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="scoreInputExtraPanel" aria-labelledby="scoreInputExtraPanelLabel" style="width:min(92vw, 420px);">
        <div class="offcanvas-header border-bottom">
            <div>
                <h5 class="offcanvas-title mb-0" id="scoreInputExtraPanelLabel">Panel Informasi Penilaian</h5>
                <small class="text-muted">Bobot penilaian dan histori nilai</small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="card side-panel shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Bobot Penilaian</h5>
                    <div id="weightPanel"></div>
                </div>
            </div>

        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Histori Perubahan Nilai</h5>
                <div id="historyPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scoreModalLabel">Formulir Input Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="scoreRowId">
                <input type="hidden" id="scoreAssessmentId">
                <input type="hidden" id="scorePesertaId">
                <input type="hidden" id="scorePeriod">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="score-box">
                            <small class="text-muted">Peserta</small>
                            <div class="fw-bold" id="selectedStudent">-</div>
                            <small class="text-muted" id="scoreStudentMeta">-</small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="score-box">
                            <small class="text-muted">Nilai Sementara</small>
                            <div class="fw-bold fs-4" id="finalScore">0</div>
                            <small class="text-muted" id="gradePreview">Grade -</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="scoreDiscipline">Kehadiran</label>
                        <input class="form-control score-input" id="scoreDiscipline" type="number" min="0" max="100" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="scorePerformance">Aktivitas</label>
                        <input class="form-control score-input" id="scorePerformance" type="number" min="0" max="100" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="scoreReport">Laporan</label>
                        <input class="form-control score-input" id="scoreReport" type="number" min="0" max="100" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="scoreAttitude">Sikap</label>
                        <input class="form-control score-input" id="scoreAttitude" type="number" min="0" max="100" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="scoreCompetency">Kompetensi</label>
                        <input class="form-control score-input" id="scoreCompetency" type="number" min="0" max="100" value="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="scoreNote">Catatan Mentor</label>
                        <textarea class="form-control" id="scoreNote" rows="3" placeholder="Tuliskan catatan objektif untuk peserta"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" type="button" id="draftScoreButton"><i class="bi bi-file-earmark"></i> Simpan Draft</button>
                <button class="btn btn-outline-secondary" type="button" id="resetScoreButton"><i class="bi bi-arrow-clockwise"></i> Reset Nilai</button>
                <button class="btn btn-outline-dark" type="button" id="previewScoreButton"><i class="bi bi-eye"></i> Preview Nilai</button>
                <button class="btn btn-warning" type="button" id="saveScoreButton"><i class="bi bi-send"></i> Kirim Penilaian</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Nilai</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Input Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div class="w-100">
                        <p class="mb-2" id="confirmMessage">Simpan hasil penilaian?</p>
                        <div class="small text-muted mb-2" id="confirmSummary"></div>
                        <label class="form-label" for="confirmNote">Catatan Penilaian</label>
                        <textarea class="form-control" id="confirmNote" rows="3"></textarea>
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
    <div id="scoreToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const students = @json($scoreInputRowsData ?? []);
    const saveUrl = @json(route('pembimbing.penilaian.input.save'));

    let currentPage = 1;
    let selectedId = null;
    let pendingAction = null;

    const weights = { disiplin: 30, kinerja: 40, laporan: 30 };
    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('scoreTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const scoreModal = new bootstrap.Modal(document.getElementById('scoreModal'));
    const toast = new bootstrap.Toast(document.getElementById('scoreToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return { 'sudah dinilai': 'bg-success', 'belum dinilai': 'bg-warning text-dark', draft: 'bg-secondary', review: 'bg-info text-dark', final: 'bg-success' }[value];
    }

    function scoreClass(value) {
        if (value >= 85) return 'bg-success';
        if (value >= 70) return 'bg-info';
        if (value > 0) return 'bg-warning';
        return 'bg-secondary';
    }

    function gradeFromScore(value) {
        if (value >= 90) return 'A';
        if (value >= 85) return 'B+';
        if (value >= 75) return 'B';
        if (value >= 70) return 'C+';
        if (value > 0) return 'C';
        return '-';
    }

    function calculateFinal() {
        const disiplin = Number(document.getElementById('scoreDiscipline').value) || 0;
        const kinerja = Number(document.getElementById('scorePerformance').value) || 0;
        const laporan = Number(document.getElementById('scoreReport').value) || 0;
        const sikap = Number(document.getElementById('scoreAttitude').value) || 0;
        const kompetensi = Number(document.getElementById('scoreCompetency').value) || 0;
        const finalScore = Math.round((disiplin + kinerja + laporan + sikap + kompetensi) / 5);
        document.getElementById('finalScore').textContent = finalScore;
        document.getElementById('gradePreview').textContent = `Grade ${gradeFromScore(finalScore)}`;
        return finalScore;
    }

    function filteredStudents() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const period = periodFilter.value;
        return students.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.instansi, item.status, item.note].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.instansi === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchPeriod = period === 'semua' || item.periode === period;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchPeriod;
        });
    }

    function updateStats() {
        const valued = students.filter((item) => item.nilai > 0);
        const done = students.filter((item) => item.status === 'sudah dinilai').length;
        const total = students.length;
        document.getElementById('statTotal').textContent = students.length;
        document.getElementById('statDone').textContent = done;
        document.getElementById('statWaiting').textContent = students.filter((item) => item.status === 'belum dinilai').length;
        document.getElementById('statHighest').textContent = valued.length ? Math.max(...valued.map((item) => item.nilai)) : 0;
        document.getElementById('statAverage').textContent = valued.length ? Math.round(valued.reduce((sum, item) => sum + item.nilai, 0) / valued.length) : 0;
        document.getElementById('statCompletion').textContent = total ? `${Math.round((done / total) * 100)}%` : '0%';
    }

    function renderPanels() {
        document.getElementById('weightPanel').innerHTML = `
            <div class="weight-row"><div class="d-flex justify-content-between"><strong>Kedisiplinan</strong><span>${weights.disiplin}%</span></div></div>
            <div class="weight-row"><div class="d-flex justify-content-between"><strong>Kinerja Magang</strong><span>${weights.kinerja}%</span></div></div>
            <div class="weight-row"><div class="d-flex justify-content-between"><strong>Laporan Magang</strong><span>${weights.laporan}%</span></div></div>
        `;

        document.getElementById('historyPanel').innerHTML = students
            .filter((item) => item.nilai > 0)
            .slice(0, 5)
            .map((item) => `
                <div class="history-row">
                    <div class="d-flex justify-content-between gap-2">
                        <div>
                            <div class="fw-semibold">${item.nama}</div>
                            <div class="small text-muted">Nilai akhir ${item.nilai}</div>
                        </div>
                        <span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span>
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
        const data = filteredStudents();
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
                <td>${item.disiplin ?? item.presence ?? '-'}</td>
                <td>${item.kinerja ?? item.activity ?? '-'}</td>
                <td>${item.laporan ?? item.report ?? '-'}</td>
                <td>${item.attitude ?? '-'}</td>
                <td>${item.competency ?? '-'}</td>
                <td class="fw-bold">${item.nilai || '-'}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="input" data-id="${item.id}">Input Nilai</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="draft" data-id="${item.id}">Simpan Draft</button>
                        <button class="btn btn-outline-dark btn-sm" type="button" data-action="preview" data-id="${item.id}">Preview</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} mahasiswa ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} mahasiswa`
            : 'Menampilkan 0 mahasiswa';
        renderPagination(totalPages);
        updateStats();
        renderPanels();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('scoreToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function fillForm(item) {
        selectedId = item.id;
        document.getElementById('selectedStudent').textContent = `${item.nama || item.name} (${item.nim})`;
        document.getElementById('scoreStudentMeta').textContent = `${item.prodi || item.study || '-'} - ${item.instansi || item.location || '-'} - ${item.periode || item.period || '-'}`;
        document.getElementById('scoreDiscipline').value = item.disiplin ?? item.presence ?? 0;
        document.getElementById('scorePerformance').value = item.kinerja ?? item.activity ?? 0;
        document.getElementById('scoreReport').value = item.laporan ?? item.report ?? 0;
        document.getElementById('scoreAttitude').value = item.attitude ?? 0;
        document.getElementById('scoreCompetency').value = item.competency ?? 0;
        document.getElementById('scoreNote').value = item.note;
        calculateFinal();
        scoreModal.show();
    }

    function openDetail(item, action = 'input') {
        document.getElementById('detailTitle').textContent = titleCase(action) + ' Nilai';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Instansi</strong><div>${item.instansi}</div></div>
                <div class="col-md-6"><strong>Status</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-4"><strong>Kedisiplinan</strong><div>${item.disiplin || '-'}</div></div>
                <div class="col-md-4"><strong>Kinerja</strong><div>${item.kinerja || '-'}</div></div>
                <div class="col-md-4"><strong>Laporan</strong><div>${item.laporan || '-'}</div></div>
                <div class="col-md-6"><strong>Nilai Akhir</strong><div>${item.nilai || '-'}</div></div>
                <div class="col-md-6"><strong>Periode</strong><div>${item.periode}</div></div>
                <div class="col-12"><strong>Catatan</strong><div>${item.note}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        const finalScore = calculateFinal();
        document.getElementById('confirmMessage').textContent = `Simpan tindakan ${titleCase(action)} untuk ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `Nilai akhir ${finalScore} - Status ${titleCase(item.status)} - ${item.periode}`;
        document.getElementById('confirmNote').value = document.getElementById('scoreNote').value || item.note;
        confirmModal.show();
    }

    document.querySelectorAll('.score-input').forEach((input) => input.addEventListener('input', calculateFinal));

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card], [data-score-card], [data-completion-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
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
        showToast('Filter input nilai berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        renderTable();
        showToast('Filter input nilai berhasil direset.', 'info');
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
        const item = students.find((student) => student.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (!item) return;
        if (action === 'preview') {
            openDetail(item, action);
            return;
        }
        fillForm(item);
        if (action === 'input' || action === 'draft' || action === 'edit' || action === 'validasi') {
            return;
        }
    });

    document.getElementById('saveScoreButton').addEventListener('click', () => {
        const item = students.find((student) => student.id === selectedId);
        if (!item) {
            showToast('Pilih mahasiswa terlebih dahulu.', 'danger');
            return;
        }
        openConfirm(item, 'simpan');
    });

    document.getElementById('draftScoreButton').addEventListener('click', () => {
        const item = students.find((student) => student.id === selectedId);
        if (!item) {
            showToast('Pilih mahasiswa terlebih dahulu.', 'danger');
            return;
        }
        openConfirm(item, 'draft');
    });

    document.getElementById('resetScoreButton').addEventListener('click', () => {
        document.getElementById('scoreDiscipline').value = 0;
        document.getElementById('scorePerformance').value = 0;
        document.getElementById('scoreReport').value = 0;
        document.getElementById('scoreAttitude').value = 0;
        document.getElementById('scoreCompetency').value = 0;
        document.getElementById('scoreNote').value = '';
        calculateFinal();
        showToast('Form nilai berhasil direset.', 'info');
    });

    document.getElementById('previewScoreButton').addEventListener('click', () => {
        const finalScore = calculateFinal();
        showToast(`Preview nilai sementara: ${finalScore}, grade ${gradeFromScore(finalScore)}.`, 'info');
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        const item = pendingAction.item;
        if (pendingAction.action === 'reset') {
            item.disiplin = 0;
            item.kinerja = 0;
            item.laporan = 0;
            item.nilai = 0;
            item.status = 'belum dinilai';
            item.note = document.getElementById('confirmNote').value || item.note;
            confirmModal.hide();
            scoreModal.hide();
            renderTable();
            showToast(`Nilai ${item.nama} berhasil direset.`);
            pendingAction = null;
            return;
        }
        const finalScore = calculateFinal();
        const statusValue = pendingAction.action === 'draft' ? 'draft' : 'sudah dinilai';
        const payload = {
            assessment_id: item.assessment_id || null,
            peserta_id: item.peserta_id,
            periode: item.periode || '',
            presence: Number(document.getElementById('scoreDiscipline').value) || 0,
            activity: Number(document.getElementById('scorePerformance').value) || 0,
            report: Number(document.getElementById('scoreReport').value) || 0,
            attitude: Number(document.getElementById('scoreAttitude').value) || 0,
            competency: Number(document.getElementById('scoreCompetency').value) || 0,
            final: finalScore,
            grade: gradeFromScore(finalScore),
            note: document.getElementById('confirmNote').value || item.note,
            status: statusValue,
        };

        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify(payload),
        })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(data.message || 'Gagal menyimpan penilaian.');
                Object.assign(item, {
                    assessment_id: data.assessment?.assessment_id ?? item.assessment_id,
                    peserta_id: data.assessment?.peserta_id ?? item.peserta_id,
                    disiplin: data.assessment?.presence ?? item.disiplin,
                    kinerja: data.assessment?.activity ?? item.kinerja,
                    laporan: data.assessment?.report ?? item.laporan,
                    attitude: data.assessment?.attitude ?? item.attitude,
                    competency: data.assessment?.competency ?? item.competency,
                    nilai: data.assessment?.final ?? item.nilai,
                    status: data.assessment?.status ?? item.status,
                    note: data.assessment?.note ?? item.note,
                });
                confirmModal.hide();
                scoreModal.hide();
                renderTable();
                showToast(data.message || `Nilai ${item.nama} berhasil disimpan.`);
                pendingAction = null;
            })
            .catch(error => {
                showToast(error.message || 'Gagal menyimpan penilaian.', 'danger');
            });
    });

    renderTable();
    setTimeout(() => showToast(`Notifikasi input nilai: ${students.filter((item) => item.status === 'belum dinilai').length} mahasiswa belum dinilai.`, 'warning'), 800);
});
</script>
@endpush
