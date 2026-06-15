@extends('mentor.layout.mentor')

@section('title', 'Input Nilai')
@section('page-title', 'Input Nilai')

@push('styles')
<style>
    .score-input-page .score-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .score-input-page .stat-card,
    .score-input-page .filter-card,
    .score-input-page .table-card,
    .score-input-page .panel-card { border:0; border-radius:8px; }
    .score-input-page .stat-card { cursor:pointer; transition:.2s ease; }
    .score-input-page .stat-card:hover,
    .score-input-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .score-input-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .score-input-page .score-box { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; }
    .score-input-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:520px; }
    .score-input-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .score-input-page .table thead th {
        background: #eef8fc;
        color: #39596a;
        font-size: 13px;
        white-space: nowrap;
        vertical-align: middle;
    }
    .score-input-page .table tbody td {
        vertical-align: middle;
    }
    .score-input-page .student-cell {
        min-width: 210px;
    }
    .score-input-page .component-column {
        min-width: 92px;
        text-align: center;
    }
    .score-input-page .score-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 54px;
        padding: 5px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
        line-height: 1;
        background: #f2f7fb;
        color: #1f2d3d;
        border: 1px solid #d7eaf2;
    }
    .score-input-page .score-pill.low {
        background: #fff2f2;
        color: #c82333;
        border-color: #f2c7cb;
    }
    .score-input-page .score-pill.mid {
        background: #fff8e6;
        color: #a76b00;
        border-color: #f4dda4;
    }
    .score-input-page .score-pill.high {
        background: #eefaf3;
        color: #218838;
        border-color: #cce8d4;
    }
    .score-input-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .score-input-page .pagination .page-link { color:#2a8fbd; }
    .score-input-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid score-input-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.penilaian') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Input Nilai</li>
        </ol>
    </nav>

    <section class="score-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Input Nilai Peserta Magang</h3>
                <p class="mb-0">Berikan penilaian berdasarkan kehadiran, aktivitas, laporan, sikap, dan kompetensi agar evaluasi peserta terdokumentasi objektif.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="scoreBadge">Form Aktif</span>
                <button class="btn btn-dark" type="button" id="resetAllButton"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-info-circle-fill"></i>
        <div>Data peserta dan nilai di halaman ini diambil dari database penilaian mentor. Gunakan Simpan Draft untuk menyimpan sementara atau Kirim Penilaian untuk finalisasi.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people"></i></span>
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
            <div class="card stat-card" data-status-card="belum dinilai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dinilai</p><h3 class="mb-0" id="statPending">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Nilai</p><h3 class="mb-0" id="statAverage">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-bar-chart"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Nilai Tertinggi</p><h3 class="mb-0" id="statHighest">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-trophy"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Penyelesaian</p><h3 class="mb-0" id="statCompletion">0%</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-pie-chart"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, lokasi">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode Magang</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        @foreach(($periodOptionsData ?? []) as $period)
                            <option value="{{ $period }}">{{ $period }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Penilaian</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="belum dinilai">Belum Dinilai</option>
                        <option value="review">Review</option>
                        <option value="draft">Draft</option>
                        <option value="sudah dinilai">Sudah Dinilai</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="studyFilter">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        @foreach(($studyOptionsData ?? []) as $study)
                            <option value="{{ $study }}">{{ $study }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="locationFilter">Lokasi Magang</label>
                    <select class="form-select" id="locationFilter">
                        <option value="semua">Semua Lokasi</option>
                        @foreach(($instansiOptionsData ?? []) as $instansi)
                            <option value="{{ $instansi }}">{{ $instansi }}</option>
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
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Daftar Komponen Penilaian</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data peserta</small>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#scoreInfoPanel" aria-controls="scoreInfoPanel">
                            <i class="bi bi-layout-sidebar-inset"></i> Panel Info
                        </button>
                        <div class="d-flex align-items-center gap-2">
                            <label class="text-muted small" for="perPageSelect">Data</label>
                            <select class="form-select form-select-sm" id="perPageSelect" style="width:80px">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                            </select>
                        </div>
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
                                    <th>Sikap</th>
                                    <th>Kompetensi</th>
                                    <th>Nilai Akhir</th>
                                    <th>Status Penilaian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="scoreTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada peserta sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination input nilai"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="scoreInfoPanel" aria-labelledby="scoreInfoPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="scoreInfoPanelLabel">Panel Informasi Penilaian</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card panel-card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Informasi Peserta Terpilih</h5>
                <div id="selectedPanel">
                    <p class="text-muted mb-0">Pilih peserta pada tabel untuk melihat ringkasan dan mengisi nilai.</p>
                </div>
            </div>
        </div>
        <div class="card panel-card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Panel Informasi</h5>
                <div id="infoPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
                            <div class="fw-bold" id="scoreStudentName">-</div>
                            <small class="text-muted" id="scoreStudentMeta">-</small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="score-box">
                            <small class="text-muted">Nilai Sementara</small>
                            <div class="fw-bold fs-4" id="scorePreview">0</div>
                            <small class="text-muted" id="gradePreview">Grade -</small>
                        </div>
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="presenceScore">Kehadiran</label>
                        <input class="form-control score-input" id="presenceScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="activityScore">Aktivitas</label>
                        <input class="form-control score-input" id="activityScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="reportScore">Laporan</label>
                        <input class="form-control score-input" id="reportScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="attitudeScore">Sikap</label>
                        <input class="form-control score-input" id="attitudeScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="competencyScore">Kompetensi</label>
                        <input class="form-control score-input" id="competencyScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="mentorNote">Catatan Mentor</label>
                        <textarea class="form-control" id="mentorNote" rows="3" placeholder="Tuliskan catatan objektif untuk peserta"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="resetScoreButton"><i class="bi bi-arrow-counterclockwise"></i> Reset Nilai</button>
                <button type="button" class="btn btn-outline-warning" id="draftScoreButton"><i class="bi bi-journal-check"></i> Simpan Draft</button>
                <button type="button" class="btn btn-dark" id="previewScoreButton"><i class="bi bi-eye"></i> Preview Nilai</button>
                <button type="button" class="btn btn-warning" id="saveScoreButton"><i class="bi bi-send"></i> Kirim Penilaian</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="scoreToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Informasi nilai diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const students = @json($scoreInputRowsData ?? []);
    const saveUrl = @json(route('mentor.penilaian.input.save'));

    let filtered = [...students];
    let currentPage = 1;
    let perPage = 5;
    let selectedId = null;
    let pendingSave = null;

    const tableBody = document.getElementById('scoreTableBody');
    const pagination = document.getElementById('pagination');
    const scoreModal = new bootstrap.Modal(document.getElementById('scoreModal'));
    const toast = new bootstrap.Toast(document.getElementById('scoreToast'));

    const gradeFromScore = score => score >= 90 ? 'A' : score >= 85 ? 'B+' : score >= 75 ? 'B' : score >= 70 ? 'C+' : score > 0 ? 'C' : '-';
    const titleCase = text => text.replace(/\b\w/g, char => char.toUpperCase());
    const scoreClass = (value) => {
        const numeric = Number(value || 0);
        if (numeric >= 85) return 'high';
        if (numeric >= 70) return 'mid';
        return 'low';
    };
    const statusBadge = status => {
        const map = { 'sudah dinilai':'success', 'draft':'info', 'belum dinilai':'warning text-dark', 'review':'warning text-dark', 'perlu revisi':'warning text-dark' };
        return `<span class="badge bg-${map[status] || 'secondary'}">${titleCase((status || '-').toString())}</span>`;
    };
    const showToast = message => {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    };

    const scorePayload = () => {
        const values = ['presence','activity','report','attitude','competency'].map(key => Number(document.getElementById(`${key}Score`).value || 0));
        const final = Math.round(values.reduce((sum, value) => sum + value, 0) / values.length);
        return {
            id:Number(document.getElementById('scoreRowId').value),
            assessmentId: Number(document.getElementById('scoreAssessmentId').value || 0) || null,
            pesertaId: Number(document.getElementById('scorePesertaId').value || 0) || null,
            period: document.getElementById('scorePeriod').value,
            presence:values[0],
            activity:values[1],
            report:values[2],
            attitude:values[3],
            competency:values[4],
            final,
            grade:gradeFromScore(final),
            note:document.getElementById('mentorNote').value.trim()
        };
    };

    const updatePreview = () => {
        const payload = scorePayload();
        document.getElementById('scorePreview').textContent = payload.final;
        document.getElementById('gradePreview').textContent = `Grade ${payload.grade}`;
    };

    const updateStats = () => {
        const done = students.filter(item => item.status === 'sudah dinilai');
        const scores = done.map(item => item.final || item.nilai || 0);
        document.getElementById('statTotal').textContent = students.length;
        document.getElementById('statDone').textContent = done.length;
        document.getElementById('statPending').textContent = students.filter(item => item.status === 'belum dinilai').length;
        document.getElementById('statAverage').textContent = scores.length ? Math.round(scores.reduce((sum, score) => sum + score, 0) / scores.length) : 0;
        document.getElementById('statHighest').textContent = scores.length ? Math.max(...scores) : 0;
        document.getElementById('statCompletion').textContent = students.length ? `${Math.round((done.length / students.length) * 100)}%` : '0%';
    };

    const renderPanel = () => {
        const selected = students.find(item => item.id === selectedId);
        document.getElementById('selectedPanel').innerHTML = selected ? `
            <div class="info-row">
                <div class="fw-bold">${selected.nama || selected.name}</div>
                <small class="text-muted">${selected.nim} - ${selected.prodi || selected.study}</small>
                <div class="mt-2">${statusBadge(selected.status)}</div>
                <div class="small text-muted mt-2">${selected.instansi || selected.location} - ${selected.periode || selected.period}</div>
                <div class="mt-3 d-grid">
                    <button class="btn btn-warning" type="button" data-panel-open="${selected.id}"><i class="bi bi-pencil-square"></i> Input Nilai</button>
                </div>
            </div>
        ` : '<p class="text-muted mb-0">Pilih peserta pada tabel untuk melihat ringkasan dan mengisi nilai.</p>';

        const priority = students.filter(item => item.status !== 'sudah dinilai').slice(0, 3);
        document.getElementById('infoPanel').innerHTML = priority.map(item => `
            <div class="info-row">
                <div class="d-flex justify-content-between gap-2">
                    <div><div class="fw-semibold">${item.nama || item.name}</div><small class="text-muted">${item.instansi || item.location}</small></div>
                    ${statusBadge(item.status)}
                </div>
                <small class="text-muted">${item.note}</small>
            </div>
        `).join('');
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td class="text-center">${start + index + 1}</td>
                <td class="student-cell">
                    <div class="fw-semibold">${item.nama || item.name}</div>
                    <small class="text-muted d-block">${item.nim} - ${item.prodi || item.study}</small>
                </td>
                <td class="component-column"><span class="score-pill ${scoreClass(item.disiplin ?? item.presence)}">${item.disiplin ?? item.presence ?? '-'}</span></td>
                <td class="component-column"><span class="score-pill ${scoreClass(item.kinerja ?? item.activity)}">${item.kinerja ?? item.activity ?? '-'}</span></td>
                <td class="component-column"><span class="score-pill ${scoreClass(item.laporan ?? item.report)}">${item.laporan ?? item.report ?? '-'}</span></td>
                <td class="component-column"><span class="score-pill ${scoreClass(item.attitude)}">${item.attitude ?? '-'}</span></td>
                <td class="component-column"><span class="score-pill ${scoreClass(item.competency)}">${item.competency ?? '-'}</span></td>
                <td class="component-column fw-bold">${item.final || item.nilai || '-'}</td>
                <td>${statusBadge(item.status)}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-warning" type="button" data-action="input" data-id="${item.id}"><i class="bi bi-pencil-square"></i> Input Nilai</button>
                        <button class="btn btn-sm btn-outline-warning" type="button" data-action="draft" data-id="${item.id}"><i class="bi bi-journal-check"></i> Draft</button>
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="preview" data-id="${item.id}"><i class="bi bi-eye"></i> Preview</button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('emptyState').classList.toggle('d-none', filtered.length > 0);
        document.getElementById('tableInfo').textContent = `Menampilkan ${filtered.length} dari ${students.length} peserta`;
        document.getElementById('paginationInfo').textContent = filtered.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, filtered.length)} dari ${filtered.length} data` : 'Menampilkan 0 data';
        renderPagination(totalPages);
    };

    const renderPagination = totalPages => {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    };

    const applyFilters = () => {
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const period = document.getElementById('periodFilter').value;
        const status = document.getElementById('statusFilter').value;
        const study = document.getElementById('studyFilter').value;
        const location = document.getElementById('locationFilter').value;
        filtered = students.filter(item => {
            const text = [item.nama || item.name, item.nim, item.prodi || item.study, item.instansi || item.location, item.note].join(' ').toLowerCase();
            return text.includes(keyword)
                && (period === 'semua' || (item.periode || item.period) === period)
                && (status === 'semua' || item.status === status)
                && (study === 'semua' || (item.prodi || item.study) === study)
                && (location === 'semua' || (item.instansi || item.location) === location);
        });
        currentPage = 1;
        renderTable();
    };

    const openForm = item => {
        selectedId = item.id;
        document.getElementById('scoreRowId').value = item.id;
        document.getElementById('scoreAssessmentId').value = item.assessment_id || '';
        document.getElementById('scorePesertaId').value = item.peserta_id || '';
        document.getElementById('scorePeriod').value = item.periode || item.period || '';
        document.getElementById('scoreStudentName').textContent = item.nama || item.name;
        document.getElementById('scoreStudentMeta').textContent = `${item.nim} - ${item.prodi || item.study} - ${item.instansi || item.location}`;
        const scoreMap = {
            presence: item.disiplin ?? item.presence ?? 0,
            activity: item.kinerja ?? item.activity ?? 0,
            report: item.laporan ?? item.report ?? 0,
            attitude: item.attitude ?? 0,
            competency: item.competency ?? 0,
        };
        Object.keys(scoreMap).forEach(key => {
            document.getElementById(`${key}Score`).value = scoreMap[key];
        });
        document.getElementById('mentorNote').value = item.note;
        updatePreview();
        renderPanel();
        scoreModal.show();
    };

    const resetFormValues = () => {
        ['presence','activity','report','attitude','competency'].forEach(key => document.getElementById(`${key}Score`).value = 0);
        document.getElementById('mentorNote').value = '';
        updatePreview();
    };

    document.querySelectorAll('.score-input').forEach(input => input.addEventListener('input', updatePreview));

    tableBody.addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = students.find(row => row.id === Number(button.dataset.id));
        if (!item) return;
        selectedId = item.id;
        renderPanel();
        if (button.dataset.action === 'input' || button.dataset.action === 'draft') openForm(item);
        if (button.dataset.action === 'preview') showToast(`Preview nilai ${item.nama || item.name}: ${item.final || item.nilai || 0}, grade ${item.grade}.`);
    });

    document.getElementById('selectedPanel').addEventListener('click', event => {
        const button = event.target.closest('button[data-panel-open]');
        if (!button) return;
        const item = students.find(row => row.id === Number(button.dataset.panelOpen));
        if (item) openForm(item);
    });

    const submitScore = status => {
        pendingSave = { ...scorePayload(), status };
        const item = students.find(row => row.id === pendingSave.id);
        if (!item) {
            showToast('Data penilaian tidak ditemukan.');
            return;
        }

        const note = document.getElementById('mentorNote').value.trim() || pendingSave.note;
        const payload = {
            assessment_id: pendingSave.assessmentId,
            peserta_id: pendingSave.pesertaId,
            periode: pendingSave.period,
            presence: pendingSave.presence,
            activity: pendingSave.activity,
            report: pendingSave.report,
            attitude: pendingSave.attitude,
            competency: pendingSave.competency,
            final: pendingSave.final,
            grade: pendingSave.grade,
            note,
            status: pendingSave.status,
        };

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menyimpan penilaian.');
                }
                Object.assign(item, data.assessment || {});
                item.note = data.assessment?.note || note;
                scoreModal.hide();
                updateStats();
                renderPanel();
                applyFilters();
                showToast(data.message || `Nilai ${item.nama || item.name} berhasil disimpan.`);
            })
            .catch(error => {
                showToast(error.message || 'Gagal menyimpan penilaian.');
            });
    };

    document.getElementById('draftScoreButton').addEventListener('click', () => submitScore('draft'));

    document.getElementById('saveScoreButton').addEventListener('click', () => submitScore('sudah dinilai'));

    document.getElementById('resetScoreButton').addEventListener('click', resetFormValues);
    document.getElementById('previewScoreButton').addEventListener('click', () => {
        const payload = scorePayload();
        showToast(`Preview nilai sementara: ${payload.final}, grade ${payload.grade}.`);
    });
    document.getElementById('resetAllButton').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('periodFilter').value = 'semua';
        document.getElementById('statusFilter').value = 'semua';
        document.getElementById('studyFilter').value = 'semua';
        document.getElementById('locationFilter').value = 'semua';
        document.querySelectorAll('.stat-card').forEach(item => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]')?.classList.add('active');
        currentPage = 1;
        applyFilters();
        showToast('Filter penilaian berhasil direset.');
    });

    pagination.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('perPageSelect').addEventListener('change', event => {
        perPage = Number(event.target.value);
        currentPage = 1;
        renderTable();
    });

    document.getElementById('applyFilter').addEventListener('click', applyFilters);
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.querySelectorAll('#periodFilter,#statusFilter,#studyFilter,#locationFilter').forEach(input => input.addEventListener('change', applyFilters));
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('periodFilter').value = 'semua';
        document.getElementById('statusFilter').value = 'semua';
        document.getElementById('studyFilter').value = 'semua';
        document.getElementById('locationFilter').value = 'semua';
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

    updateStats();
    renderPanel();
    applyFilters();
});
</script>
@endpush
