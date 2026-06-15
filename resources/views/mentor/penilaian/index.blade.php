@extends('mentor.layout.mentor')

@section('title', 'Penilaian')
@section('page-title', 'Penilaian')

@push('styles')
<style>
    .assessment-page .assessment-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .assessment-page .stat-card,
    .assessment-page .filter-card,
    .assessment-page .table-card,
    .assessment-page .panel-card { border:0; border-radius:8px; }
    .assessment-page .stat-card { cursor:pointer; transition:.2s ease; }
    .assessment-page .stat-card:hover,
    .assessment-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .assessment-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .assessment-page .score-box { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; }
    .assessment-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:520px; }
    .assessment-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .assessment-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .assessment-page .pagination .page-link { color:#2a8fbd; }
    .assessment-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid assessment-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
        </ol>
    </nav>

    <section class="assessment-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Penilaian Peserta Magang</h3>
                <p class="mb-0">Kelola evaluasi kinerja, aktivitas, laporan, kedisiplinan, dan capaian peserta sebagai dasar penilaian akhir.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="assessmentBadge">Real-time</span>
                <button class="btn btn-dark" type="button" id="exportTopButton"><i class="bi bi-download"></i> Export Data</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-award-fill"></i>
        <div>Data penilaian ditampilkan langsung dari database dan diperbarui otomatis sesuai evaluasi terbaru.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta Dinilai</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="belum dinilai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dinilai</p><h3 class="mb-0" id="statPending">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="sudah dinilai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sudah Dinilai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-score-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Nilai</p><h3 class="mb-0" id="statAverage">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-bar-chart"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-score-card="highest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Nilai Tertinggi</p><h3 class="mb-0" id="statHighest">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-trophy"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-score-card="lowest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Nilai Terendah</p><h3 class="mb-0" id="statLowest">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-graph-down"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Peserta / Program Studi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, prodi">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Penilaian</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="belum dinilai">Belum Dinilai</option>
                        <option value="sudah dinilai">Sudah Dinilai</option>
                        <option value="perlu revisi">Perlu Revisi</option>
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
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="scoreFilter">Rentang Nilai</label>
                    <select class="form-select" id="scoreFilter">
                        <option value="semua">Semua Nilai</option>
                        <option value="90-100">90 - 100</option>
                        <option value="80-89">80 - 89</option>
                        <option value="70-79">70 - 79</option>
                        <option value="0-69">0 - 69</option>
                    </select>
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
        <div class="col-xl-8">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Daftar Penilaian Peserta</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data penilaian</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
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
                                    <th>Kehadiran</th>
                                    <th>Aktivitas</th>
                                    <th>Laporan</th>
                                    <th>Nilai Akhir</th>
                                    <th>Grade</th>
                                    <th>Status Penilaian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="assessmentTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data penilaian sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination penilaian"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
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
                        <button class="btn btn-warning text-start" type="button" data-panel-action="input nilai"><i class="bi bi-pencil-square me-2"></i> Input Nilai</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="export pdf"><i class="bi bi-filetype-pdf me-2"></i> Export PDF</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="export excel"><i class="bi bi-file-earmark-spreadsheet me-2"></i> Export Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" id="openEditFromDetail"><i class="bi bi-pencil-square"></i> Edit Penilaian</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scoreModalLabel">Input Penilaian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="scoreItemId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="score-box">
                            <small class="text-muted">Peserta</small>
                            <div class="fw-bold" id="scoreStudentName">-</div>
                            <small class="text-muted" id="scoreStudentMeta">-</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="score-box">
                            <small class="text-muted">Nilai Akhir</small>
                            <div class="fw-bold fs-4" id="scorePreview">0</div>
                            <small class="text-muted" id="gradePreview">Grade -</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="presenceScore">Kehadiran</label>
                        <input class="form-control score-input" id="presenceScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="activityScore">Aktivitas</label>
                        <input class="form-control score-input" id="activityScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="reportScore">Laporan</label>
                        <input class="form-control score-input" id="reportScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="attitudeScore">Sikap</label>
                        <input class="form-control score-input" id="attitudeScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="competencyScore">Kompetensi</label>
                        <input class="form-control score-input" id="competencyScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="mentorNote">Catatan Mentor</label>
                        <textarea class="form-control" id="mentorNote" rows="3" placeholder="Tambahkan catatan evaluasi peserta"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="saveScoreButton"><i class="bi bi-save"></i> Simpan Penilaian</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="assessmentToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Informasi penilaian diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const assessments = @json($assessmentRowsData ?? []);
    const saveUrl = @json(route('mentor.penilaian.input.save'));
    const exportUrl = @json(route('mentor.penilaian.export'));

    let filtered = [...assessments];
    let currentPage = 1;
    let perPage = 5;

    const tableBody = document.getElementById('assessmentTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const scoreModal = new bootstrap.Modal(document.getElementById('scoreModal'));
    const toast = new bootstrap.Toast(document.getElementById('assessmentToast'));

    const gradeFromScore = (score) => {
        if (score >= 90) return 'A';
        if (score >= 85) return 'B+';
        if (score >= 75) return 'B';
        if (score >= 70) return 'C+';
        if (score > 0) return 'C';
        return '-';
    };

    const statusBadge = (status) => {
        const map = {
            'sudah dinilai':'success',
            'belum dinilai':'warning text-dark',
            'perlu revisi':'danger'
        };
        return `<span class="badge bg-${map[status] || 'secondary'}">${status.replace(/\b\w/g, char => char.toUpperCase())}</span>`;
    };

    const showToast = (message) => {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    };

    const getScorePayload = () => {
        const presence = Number(document.getElementById('presenceScore').value || 0);
        const activity = Number(document.getElementById('activityScore').value || 0);
        const report = Number(document.getElementById('reportScore').value || 0);
        const attitude = Number(document.getElementById('attitudeScore').value || 0);
        const competency = Number(document.getElementById('competencyScore').value || 0);
        const final = Math.round((presence + activity + report + attitude + competency) / 5);
        return {
            id: Number(document.getElementById('scoreItemId').value),
            presence,
            activity,
            report,
            attitude,
            competency,
            final,
            grade: gradeFromScore(final),
            note: document.getElementById('mentorNote').value.trim(),
        };
    };

    const updateScorePreview = () => {
        const payload = getScorePayload();
        document.getElementById('scorePreview').textContent = payload.final;
        document.getElementById('gradePreview').textContent = `Grade ${payload.grade}`;
    };

    const updateStats = () => {
        const scored = assessments.filter(item => item.status === 'sudah dinilai');
        const scores = scored.map(item => item.final);
        const average = scores.length ? Math.round(scores.reduce((sum, score) => sum + score, 0) / scores.length) : 0;
        document.getElementById('statTotal').textContent = assessments.length;
        document.getElementById('statPending').textContent = assessments.filter(item => item.status === 'belum dinilai').length;
        document.getElementById('statDone').textContent = scored.length;
        document.getElementById('statAverage').textContent = average;
        document.getElementById('statHighest').textContent = scores.length ? Math.max(...scores) : 0;
        document.getElementById('statLowest').textContent = scores.length ? Math.min(...scores) : 0;
        document.getElementById('panelCount').textContent = assessments.filter(item => item.status !== 'sudah dinilai').length;
    };

    const renderPanel = () => {
        const priority = assessments.filter(item => item.status !== 'sudah dinilai').slice(0, 4);
        document.getElementById('infoPanel').innerHTML = priority.map(item => `
            <div class="info-row">
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold">${item.name}</div>
                        <small class="text-muted">${item.study} - ${item.period}</small>
                    </div>
                    ${statusBadge(item.status)}
                </div>
                <div class="small text-muted mt-2">${item.note}</div>
            </div>
        `).join('') || '<p class="text-muted mb-0">Semua peserta sudah dinilai.</p>';
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>
                    <div class="fw-semibold">${item.name}</div>
                    <small class="text-muted">${item.nim} - ${item.study}</small>
                </td>
                <td>${item.presence || '-'}</td>
                <td>${item.activity || '-'}</td>
                <td>${item.report || '-'}</td>
                <td class="fw-bold">${item.final || '-'}</td>
                <td><span class="badge bg-dark">${item.grade}</span></td>
                <td>${statusBadge(item.status)}</td>
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

        emptyState.classList.toggle('d-none', filtered.length > 0);
        tableInfo.textContent = `Menampilkan ${filtered.length} dari ${assessments.length} data penilaian`;
        paginationInfo.textContent = filtered.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, filtered.length)} dari ${filtered.length} data` : 'Menampilkan 0 data';
        renderPagination(totalPages);
    };

    const renderPagination = (totalPages) => {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    };

    const applyFilters = () => {
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        const period = document.getElementById('periodFilter').value;
        const score = document.getElementById('scoreFilter').value;
        const study = document.getElementById('studyFilter').value;

        filtered = assessments.filter(item => {
            const matchesKeyword = [item.name, item.nim, item.study, item.note].join(' ').toLowerCase().includes(keyword);
            const matchesStatus = status === 'semua' || item.status === status;
            const matchesPeriod = period === 'semua' || item.period === period;
            const matchesStudy = study === 'semua' || item.study === study;
            const matchesScore = score === 'semua' || (() => {
                const [min, max] = score.split('-').map(Number);
                return item.final >= min && item.final <= max;
            })();
            return matchesKeyword && matchesStatus && matchesPeriod && matchesStudy && matchesScore;
        });

        currentPage = 1;
        renderTable();
    };

    const openDetail = (item) => {
        document.getElementById('detailModalBody').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="score-box"><small class="text-muted">Nama Peserta</small><div class="fw-bold">${item.name}</div><small>${item.nim} - ${item.study}</small></div></div>
                <div class="col-md-6"><div class="score-box"><small class="text-muted">Status</small><div>${statusBadge(item.status)}</div><small>${item.period}</small></div></div>
                <div class="col-md-3"><div class="score-box"><small class="text-muted">Kehadiran</small><div class="fw-bold fs-4">${item.presence || '-'}</div></div></div>
                <div class="col-md-3"><div class="score-box"><small class="text-muted">Aktivitas</small><div class="fw-bold fs-4">${item.activity || '-'}</div></div></div>
                <div class="col-md-3"><div class="score-box"><small class="text-muted">Laporan</small><div class="fw-bold fs-4">${item.report || '-'}</div></div></div>
                <div class="col-md-3"><div class="score-box"><small class="text-muted">Nilai Akhir</small><div class="fw-bold fs-4">${item.final || '-'}</div><small>Grade ${item.grade}</small></div></div>
                <div class="col-12"><div class="score-box"><small class="text-muted">Catatan Mentor</small><p class="mb-0">${item.note}</p></div></div>
            </div>
        `;
        document.getElementById('openEditFromDetail').dataset.id = item.id;
        detailModal.show();
    };

    const openScoreForm = (item) => {
        document.getElementById('scoreItemId').value = item.id;
        document.getElementById('scoreItemId').dataset.assessmentId = item.assessment_id || item.id;
        document.getElementById('scoreStudentName').textContent = item.name;
        document.getElementById('scoreStudentMeta').textContent = `${item.nim} - ${item.study} - ${item.period}`;
        document.getElementById('presenceScore').value = item.presence || 0;
        document.getElementById('activityScore').value = item.activity || 0;
        document.getElementById('reportScore').value = item.report || 0;
        document.getElementById('attitudeScore').value = item.attitude || 0;
        document.getElementById('competencyScore').value = item.competency || 0;
        document.getElementById('mentorNote').value = item.note;
        updateScorePreview();
        scoreModal.show();
    };

    document.querySelectorAll('.score-input').forEach(input => input.addEventListener('input', updateScorePreview));

    const submitScore = async (status) => {
        const pendingSave = getScorePayload();
        const item = assessments.find(row => row.id === pendingSave.id);
        if (!item) {
            showToast('Data penilaian tidak ditemukan.');
            return;
        }

        const payload = {
            assessment_id: Number(document.getElementById('scoreItemId').dataset.assessmentId || 0) || null,
            peserta_id: item.peserta_id || item.pesertaId || item.id,
            periode: item.period || item.periode || '',
            presence: pendingSave.presence,
            activity: pendingSave.activity,
            report: pendingSave.report,
            attitude: pendingSave.attitude,
            competency: pendingSave.competency,
            final: pendingSave.final,
            grade: pendingSave.grade,
            note: pendingSave.note,
            status,
        };

        const confirmed = window.confirm(`Simpan nilai akhir ${pendingSave.final} dengan grade ${pendingSave.grade}?`);
        if (!confirmed) return;

        try {
            const response = await fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data.message || 'Gagal menyimpan penilaian.');
            }

            Object.assign(item, data.assessment || {}, {
                name: item.name,
                nim: item.nim,
                study: item.study,
                period: item.period,
                instansi: item.instansi,
                status: data.assessment?.status || status,
                note: data.assessment?.note || pendingSave.note || '-',
            });
            scoreModal.hide();
            updateStats();
            renderPanel();
            applyFilters();
            showToast(data.message || `Penilaian ${item.name} berhasil disimpan.`);
        } catch (error) {
            showToast(error.message || 'Gagal menyimpan penilaian.');
        }
    };

    document.getElementById('saveScoreButton').addEventListener('click', () => submitScore('sudah dinilai'));

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = assessments.find(row => row.id === Number(button.dataset.id));
        if (!item) return;
        const action = button.dataset.action;
        if (action === 'detail') openDetail(item);
        if (action === 'score' || action === 'edit') openScoreForm(item);
        if (action === 'preview') openDetail(item);
        if (action === 'pdf' || action === 'excel') {
            const format = action === 'pdf' ? 'pdf' : 'csv';
            const assessmentId = item.assessment_id || item.id;
            window.open(`${exportUrl}/${assessmentId}?format=${format}`, '_blank', 'noopener');
        }
    });

    document.getElementById('openEditFromDetail').addEventListener('click', (event) => {
        const item = assessments.find(row => row.id === Number(event.currentTarget.dataset.id));
        if (!item) return;
        detailModal.hide();
        openScoreForm(item);
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('perPageSelect').addEventListener('change', (event) => {
        perPage = Number(event.target.value);
        currentPage = 1;
        renderTable();
    });

    document.getElementById('applyFilter').addEventListener('click', applyFilters);
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.querySelectorAll('#statusFilter,#periodFilter,#scoreFilter,#studyFilter').forEach(input => input.addEventListener('change', applyFilters));

    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = 'semua';
        document.getElementById('periodFilter').value = 'semua';
        document.getElementById('scoreFilter').value = 'semua';
        document.getElementById('studyFilter').value = 'semua';
        applyFilters();
    });

    document.querySelectorAll('.stat-card[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach(item => item.classList.remove('active'));
            card.classList.add('active');
            document.getElementById('statusFilter').value = card.dataset.statusCard;
            applyFilters();
        });
    });

    document.querySelectorAll('[data-panel-action]').forEach(button => {
        button.addEventListener('click', () => {
            if (button.dataset.panelAction === 'input nilai') {
                const firstItem = filtered[0] || assessments[0];
                if (firstItem) {
                    openScoreForm(firstItem);
                } else {
                    showToast('Tidak ada data penilaian untuk dibuka.');
                }
                return;
            }

            if (button.dataset.panelAction === 'export pdf') {
                window.open(`${exportUrl}?format=pdf`, '_blank', 'noopener');
                return;
            }

            if (button.dataset.panelAction === 'export excel') {
                window.location.href = `${exportUrl}?format=csv`;
                return;
            }
        });
    });

    document.getElementById('exportTopButton').addEventListener('click', () => {
        window.location.href = `${exportUrl}?format=csv`;
    });
    document.getElementById('resetAllButton').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = 'semua';
        document.getElementById('periodFilter').value = 'semua';
        document.getElementById('scoreFilter').value = 'semua';
        document.getElementById('studyFilter').value = 'semua';
        document.querySelectorAll('.stat-card').forEach(item => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]')?.classList.add('active');
        currentPage = 1;
        applyFilters();
        showToast('Filter penilaian berhasil direset.');
    });

    updateStats();
    renderPanel();
    applyFilters();
});
</script>
@endpush
