@extends('mentor.layout.mentor')

@section('title', 'Rekap Nilai')
@section('page-title', 'Rekap Nilai')

@push('styles')
<style>
    .recap-page .recap-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .recap-page .stat-card,
    .recap-page .filter-card,
    .recap-page .table-card,
    .recap-page .panel-card { border:0; border-radius:8px; }
    .recap-page .stat-card { cursor:pointer; transition:.2s ease; }
    .recap-page .stat-card:hover,
    .recap-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .recap-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .recap-page .score-box { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; }
    .recap-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:360px; }
    .recap-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .recap-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .recap-page .pagination .page-link { color:#2a8fbd; }
    .recap-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid recap-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.penilaian') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Nilai</li>
        </ol>
    </nav>

    <section class="recap-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Rekap Nilai Peserta Magang</h3>
                <p class="mb-0">Lihat hasil akhir penilaian peserta secara terintegrasi sebagai dasar evaluasi capaian selama periode magang.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="recapBadge">Real-time</span>
                <button class="btn btn-dark" type="button" id="exportTopButton"><i class="bi bi-download"></i> Export Data</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-clipboard-data-fill"></i>
        <div>Rekap nilai menampilkan nilai akhir, grade, dan status penilaian di tabel. Rincian kehadiran, aktivitas, dan laporan tersedia di detail peserta.</div>
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
                    <label class="form-label" for="searchInput">Peserta</label>
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
                        <option value="draft">Draft</option>
                        <option value="sudah dinilai">Sudah Dinilai</option>
                    </select>
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
                        @foreach(($studyOptionsData ?? []) as $study)
                            <option value="{{ $study }}">{{ $study }}</option>
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
                        <h5 class="mb-0">Tabel Rekap Nilai</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data rekap nilai</small>
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
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status Penilaian</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="recapTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data rekap sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination rekap nilai"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
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
                        <a class="btn btn-warning text-start" href="{{ route('mentor.penilaian.input') }}"><i class="bi bi-pencil-square me-2"></i> Input Nilai</a>
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
                <button type="button" class="btn btn-warning" id="detailEditButton"><i class="bi bi-pencil-square"></i> Edit Penilaian</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Nilai Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editRowId">
                <input type="hidden" id="editAssessmentId">
                <input type="hidden" id="editPesertaId">
                <input type="hidden" id="editPeriod">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="score-box">
                            <small class="text-muted">Peserta</small>
                            <div class="fw-bold" id="editStudentName">-</div>
                            <small class="text-muted" id="editStudentMeta">-</small>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="score-box">
                            <small class="text-muted">Nilai Sementara</small>
                            <div class="fw-bold fs-4" id="editScorePreview">0</div>
                            <small class="text-muted" id="editGradePreview">Grade -</small>
                        </div>
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="editPresenceScore">Kehadiran</label>
                        <input class="form-control edit-score-input" id="editPresenceScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="editActivityScore">Aktivitas</label>
                        <input class="form-control edit-score-input" id="editActivityScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="editReportScore">Laporan</label>
                        <input class="form-control edit-score-input" id="editReportScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="editAttitudeScore">Sikap</label>
                        <input class="form-control edit-score-input" id="editAttitudeScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-md">
                        <label class="form-label" for="editCompetencyScore">Kompetensi</label>
                        <input class="form-control edit-score-input" id="editCompetencyScore" type="number" min="0" max="100">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="editMentorNote">Catatan Mentor</label>
                        <textarea class="form-control" id="editMentorNote" rows="3" placeholder="Tuliskan catatan objektif untuk peserta"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-outline-warning" id="editDraftButton"><i class="bi bi-journal-check"></i> Simpan Draft</button>
                <button type="button" class="btn btn-warning" id="editSaveButton"><i class="bi bi-send"></i> Simpan Nilai</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="recapToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Informasi rekap nilai diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const recaps = @json($rekapRowsData ?? []);
    const saveUrl = @json(route('mentor.penilaian.input.save'));
    const updateUrlTemplate = @json(route('mentor.penilaian.input.update', ['assessment' => '__ASSESSMENT__']));

    let filtered = [...recaps];
    let currentPage = 1;
    let perPage = 5;
    let activeDetailItem = null;
    let activeEditItem = null;

    const tableBody = document.getElementById('recapTableBody');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const toast = new bootstrap.Toast(document.getElementById('recapToast'));

    const gradeFromScore = score => score >= 90 ? 'A' : score >= 85 ? 'B+' : score >= 75 ? 'B' : score >= 70 ? 'C+' : score > 0 ? 'C' : '-';
    const titleCase = text => text.replace(/\b\w/g, char => char.toUpperCase());
    const statusBadge = status => {
        const map = { 'sudah dinilai':'success', 'draft':'info', 'belum dinilai':'warning text-dark' };
        return `<span class="badge bg-${map[status] || 'secondary'}">${titleCase(status)}</span>`;
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const showToast = message => {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    };
    const getScore = (item, key) => Number(item?.[key] ?? 0);
    const getFinalScore = item => {
        const scores = ['presence', 'activity', 'report', 'attitude', 'competency'].map(key => getScore(item, key));
        if (!scores.some(value => value > 0)) {
            return Number(item?.final ?? 0);
        }
        return Math.round(scores.reduce((sum, value) => sum + value, 0) / scores.length);
    };
    const getEditUrl = assessmentId => updateUrlTemplate.replace('__ASSESSMENT__', assessmentId);

    const updateStats = () => {
        const done = recaps.filter(item => item.status === 'sudah dinilai');
        const scores = done.map(item => item.final);
        document.getElementById('statTotal').textContent = recaps.length;
        document.getElementById('statPending').textContent = recaps.filter(item => item.status === 'belum dinilai').length;
        document.getElementById('statDone').textContent = done.length;
        document.getElementById('statAverage').textContent = scores.length ? Math.round(scores.reduce((sum, score) => sum + score, 0) / scores.length) : 0;
        document.getElementById('statHighest').textContent = scores.length ? Math.max(...scores) : 0;
        document.getElementById('statLowest').textContent = scores.length ? Math.min(...scores) : 0;
        document.getElementById('panelCount').textContent = recaps.filter(item => item.status !== 'sudah dinilai').length;
    };

    const renderPanel = () => {
        const items = recaps.filter(item => item.status !== 'sudah dinilai').slice(0, 4);
        document.getElementById('infoPanel').innerHTML = items.map(item => `
            <div class="info-row">
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold">${item.name || item.nama || '-'}</div>
                        <small class="text-muted">${item.study || item.prodi || '-'} - ${item.period || item.periode || '-'}</small>
                    </div>
                    ${statusBadge(item.status)}
                </div>
                <small class="text-muted">${item.note}</small>
            </div>
        `).join('') || '<p class="text-muted mb-0">Semua nilai peserta sudah final.</p>';
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);

        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><div class="fw-semibold">${item.name || item.nama || '-'}</div><small class="text-muted">${item.nim} - ${item.study || item.prodi || '-'}</small></td>
                <td class="fw-bold text-center">${item.final ?? '-'}</td>
                <td class="text-center"><span class="badge bg-dark">${item.grade}</span></td>
                <td class="text-center">${statusBadge(item.status)}</td>
                <td class="text-center">
                    <div class="action-group">
                        <button class="btn btn-sm btn-warning" type="button" data-action="input" data-id="${item.id}"><i class="bi bi-pencil-square"></i> Input Nilai</button>
                        <button class="btn btn-sm btn-outline-warning" type="button" data-action="edit" data-id="${item.id}"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-id="${item.id}"><i class="bi bi-eye"></i> Detail</button>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-action="pdf" data-id="${item.id}"><i class="bi bi-filetype-pdf"></i></button>
                        <button class="btn btn-sm btn-outline-success" type="button" data-action="excel" data-id="${item.id}"><i class="bi bi-file-earmark-spreadsheet"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('emptyState').classList.toggle('d-none', filtered.length > 0);
        document.getElementById('tableInfo').textContent = `Menampilkan ${filtered.length} dari ${recaps.length} data rekap`;
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
        const status = document.getElementById('statusFilter').value;
        const period = document.getElementById('periodFilter').value;
        const score = document.getElementById('scoreFilter').value;
        const study = document.getElementById('studyFilter').value;
        filtered = recaps.filter(item => {
            const text = [item.name, item.nim, item.study, item.note].join(' ').toLowerCase();
            const matchesScore = score === 'semua' || (() => {
                const [min, max] = score.split('-').map(Number);
                return item.final >= min && item.final <= max;
            })();
            return text.includes(keyword)
                && (status === 'semua' || item.status === status)
                && (period === 'semua' || item.period === period)
                && (study === 'semua' || item.study === study)
                && matchesScore;
        });
        currentPage = 1;
        renderTable();
    };

    const openDetail = item => {
        activeDetailItem = item;
        document.getElementById('detailModalBody').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="score-box"><small class="text-muted">Peserta</small><div class="fw-bold">${item.name || item.nama || '-'}</div><small>${item.nim} - ${item.study || item.prodi || '-'}</small></div></div>
                <div class="col-md-6"><div class="score-box"><small class="text-muted">Status</small><div>${statusBadge(item.status)}</div><small>${item.period || item.periode || '-'}</small></div></div>
                <div class="col-12"><div class="score-box"><small class="text-muted d-block mb-2">Ringkasan Komponen</small><div class="row g-3"><div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">Kehadiran</small><div class="fw-bold fs-4">${item.presence ?? '-'}</div></div></div><div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">Aktivitas</small><div class="fw-bold fs-4">${item.activity ?? '-'}</div></div></div><div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">Laporan</small><div class="fw-bold fs-4">${item.report ?? '-'}</div></div></div></div></div></div>
                <div class="col-md-4"><div class="score-box h-100"><small class="text-muted">Nilai Akhir</small><div class="fw-bold fs-4">${item.final ?? '-'}</div><small>Grade ${item.grade}</small></div></div>
                <div class="col-12"><div class="score-box"><small class="text-muted">Catatan Mentor</small><p class="mb-0">${item.note}</p></div></div>
            </div>
        `;
        detailModal.show();
    };

    const updateEditPreview = () => {
        const scores = ['editPresenceScore', 'editActivityScore', 'editReportScore', 'editAttitudeScore', 'editCompetencyScore']
            .map(id => Number(document.getElementById(id).value || 0));
        const final = Math.round(scores.reduce((sum, value) => sum + value, 0) / scores.length);
        document.getElementById('editScorePreview').textContent = final;
        document.getElementById('editGradePreview').textContent = `Grade ${gradeFromScore(final)}`;
        return { final, grade: gradeFromScore(final) };
    };

    const openEdit = item => {
        activeEditItem = item;
        document.getElementById('editRowId').value = item.id || '';
        document.getElementById('editAssessmentId').value = item.assessment_id || item.id || '';
        document.getElementById('editPesertaId').value = item.peserta_id || '';
        document.getElementById('editPeriod').value = item.periode || item.period || '';
        document.getElementById('editStudentName').textContent = item.name || item.nama || '-';
        document.getElementById('editStudentMeta').textContent = `${item.nim || '-'} - ${item.study || item.prodi || '-'} - ${item.instansi || '-'}`;
        document.getElementById('editPresenceScore').value = getScore(item, 'presence');
        document.getElementById('editActivityScore').value = getScore(item, 'activity');
        document.getElementById('editReportScore').value = getScore(item, 'report');
        document.getElementById('editAttitudeScore').value = getScore(item, 'attitude');
        document.getElementById('editCompetencyScore').value = getScore(item, 'competency');
        document.getElementById('editMentorNote').value = item.note || '';
        updateEditPreview();
        detailModal.hide();
        editModal.show();
    };

    const persistEdit = status => {
        if (!activeEditItem) return;
        const preview = updateEditPreview();
        const assessmentId = Number(document.getElementById('editAssessmentId').value || 0) || null;
        const payload = {
            assessment_id: assessmentId,
            peserta_id: Number(document.getElementById('editPesertaId').value || 0) || null,
            periode: document.getElementById('editPeriod').value || '',
            presence: Number(document.getElementById('editPresenceScore').value || 0),
            activity: Number(document.getElementById('editActivityScore').value || 0),
            report: Number(document.getElementById('editReportScore').value || 0),
            attitude: Number(document.getElementById('editAttitudeScore').value || 0),
            competency: Number(document.getElementById('editCompetencyScore').value || 0),
            final: preview.final,
            grade: preview.grade,
            note: document.getElementById('editMentorNote').value.trim(),
            status,
        };

        if (!payload.peserta_id) {
            showToast('Data peserta tidak ditemukan.');
            return;
        }

        fetch(assessmentId ? getEditUrl(assessmentId) : saveUrl, {
            method: assessmentId ? 'PATCH' : 'POST',
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
                    throw new Error(data.message || 'Gagal menyimpan nilai.');
                }

                const updatedItem = {
                    ...activeEditItem,
                    assessment_id: data.assessment?.assessment_id || data.assessment?.id || activeEditItem.assessment_id || activeEditItem.id,
                    peserta_id: data.assessment?.peserta_id || activeEditItem.peserta_id,
                    presence: payload.presence,
                    activity: payload.activity,
                    report: payload.report,
                    attitude: payload.attitude,
                    competency: payload.competency,
                    final: payload.final,
                    grade: payload.grade,
                    status: payload.status,
                    note: data.assessment?.note || payload.note || '-',
                    updated: new Date().toLocaleString('id-ID'),
                };

                const index = recaps.findIndex(row => row.id === activeEditItem.id);
                if (index !== -1) {
                    recaps[index] = updatedItem;
                }
                activeEditItem = updatedItem;
                editModal.hide();
                detailModal.hide();
                updateStats();
                renderPanel();
                applyFilters();
                showToast(data.message || `Nilai ${updatedItem.name || updatedItem.nama || '-'} berhasil disimpan.`);
            })
            .catch(error => {
                showToast(error.message || 'Gagal menyimpan nilai.');
            });
    };

    tableBody.addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = recaps.find(row => row.id === Number(button.dataset.id));
        if (!item) return;
        if (button.dataset.action === 'detail') openDetail(item);
        if (button.dataset.action === 'edit' || button.dataset.action === 'input') openEdit(item);
        if (button.dataset.action === 'pdf' || button.dataset.action === 'excel') {
            showToast(`${button.dataset.action.toUpperCase()} untuk ${item.name} belum terhubung.`);
        }
    });

    document.getElementById('detailEditButton').addEventListener('click', () => {
        if (!activeDetailItem) return;
        detailModal.hide();
        setTimeout(() => openEdit(activeDetailItem), 150);
    });

    document.querySelectorAll('.edit-score-input').forEach(input => input.addEventListener('input', updateEditPreview));
    document.getElementById('editDraftButton').addEventListener('click', () => persistEdit('draft'));
    document.getElementById('editSaveButton').addEventListener('click', () => persistEdit('sudah dinilai'));

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
            showToast(`${button.dataset.panelAction} belum terhubung.`);
        });
    });

    document.getElementById('exportTopButton').addEventListener('click', () => {
        showToast('Export data rekap belum terhubung.');
    });

    updateStats();
    renderPanel();
    applyFilters();
});
</script>
@endpush
