@extends('pembimbing.layout.pembimbing')

@section('title', 'Rekap Nilai')
@section('page-title', 'Rekap Nilai')

@push('styles')
<style>
    .score-recap-page .recap-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .score-recap-page .stat-card,
    .score-recap-page .filter-card,
    .score-recap-page .table-card,
    .score-recap-page .side-panel { border:0; border-radius:8px; }
    .score-recap-page .stat-card { cursor:pointer; transition:.2s ease; }
    .score-recap-page .stat-card:hover,
    .score-recap-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .score-recap-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .score-recap-page .table { font-size:14px; }
    .score-recap-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .score-recap-page .table tbody tr:hover { background:#f7fcfe; }
    .score-recap-page .progress { height:10px; border-radius:999px; }
    .score-recap-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:430px; }
    .score-recap-page .distribution-row,
    .score-recap-page .history-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .score-recap-page .score-chip { min-width:62px; display:inline-flex; justify-content:center; }
    .score-recap-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .score-recap-page .pagination .page-link { color:#2a8fbd; }
    .score-recap-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid score-recap-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.penilaian.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Nilai</li>
        </ol>
    </nav>

    <section class="recap-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Rekap Nilai Mahasiswa</h3>
                <p class="mb-0">Pantau hasil akhir penilaian magang, distribusi predikat, validasi nilai, dan riwayat pembaruan secara terintegrasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="recapBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-clipboard2-check-fill"></i>
        <div>3 nilai akhir menunggu validasi dan 2 rekap sudah siap dicetak untuk pelaporan akademik.</div>
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
            <div class="card stat-card" data-score-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata</p><h3 class="mb-0" id="statAverage">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-bar-chart"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-score-card="highest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Tertinggi</p><h3 class="mb-0" id="statHighest">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-trophy"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-score-card="lowest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terendah</p><h3 class="mb-0" id="statLowest">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-graph-down"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="final">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Final</p><h3 class="mb-0" id="statFinal">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-patch-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-pass-card="passed">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kelulusan</p><h3 class="mb-0" id="statPassed">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-mortarboard"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, atau predikat">
                    </div>
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
                    <label class="form-label" for="agencyFilter">Instansi</label>
                    <select class="form-select" id="agencyFilter">
                        <option value="semua">Semua Instansi</option>
                        @foreach(($instansiOptionsData ?? []) as $instansi)
                            <option value="{{ $instansi }}">{{ $instansi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori Nilai</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
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
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card table-card shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Tabel Rekap Nilai Mahasiswa</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data rekap nilai</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#scoreRecapExtraPanel" aria-controls="scoreRecapExtraPanel">
                            <i class="bi bi-layout-sidebar-inset"></i> Panel
                        </button>
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
                                    <th>Mahasiswa</th>
                                    <th>Program Studi</th>
                                    <th>Instansi Magang</th>
                                    <th>Nilai Mentor</th>
                                    <th>Nilai Dosen Pembimbing</th>
                                    <th>Nilai Akhir</th>
                                    <th>Predikat</th>
                                    <th>Status Penilaian</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="recapTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Tidak ada rekap nilai sesuai filter.</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination rekap nilai">
                        <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="scoreRecapExtraPanel" aria-labelledby="scoreRecapExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="scoreRecapExtraPanelLabel">Panel Rekap Nilai</h5>
            <small class="text-muted">Distribusi nilai dan riwayat pembaruan</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Nilai</h5>
                <div id="distributionList"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Riwayat Pembaruan Nilai</h5>
                <div id="historyList"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Rekap Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Rekap Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Catatan Evaluasi</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan validasi atau pembaruan rekap nilai"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Rekap nilai diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const recaps = @json($rekapRowsData ?? []);

    const tableBody = document.getElementById('recapTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const periodFilter = document.getElementById('periodFilter');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingAction = null;

    const finalScore = (item) => Math.round((item.mentor * 0.4) + (item.dosen * 0.6));
    const predicate = (score) => {
        if (score >= 85) return 'A';
        if (score >= 75) return 'B';
        if (score >= 65) return 'C';
        return 'D';
    };
    const statusBadge = (status) => {
        const map = {
            final:'success',
            'menunggu validasi':'warning',
            draft:'secondary',
            arsip:'info'
        };
        return `<span class="badge bg-${map[status] || 'secondary'} text-capitalize">${status}</span>`;
    };
    const predicateBadge = (value) => {
        const map = { A:'success', B:'primary', C:'warning', D:'danger' };
        return `<span class="badge bg-${map[value]} score-chip">${value}</span>`;
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function filteredData() {
        const keyword = searchInput.value.toLowerCase();
        return recaps.filter((item) => {
            const score = finalScore(item);
            const pred = predicate(score);
            const keywordMatch = `${item.nama} ${item.nim} ${item.prodi} ${item.instansi} ${pred} ${item.status}`.toLowerCase().includes(keyword);
            const studyMatch = studyFilter.value === 'semua' || item.prodi === studyFilter.value;
            const agencyMatch = agencyFilter.value === 'semua' || item.instansi === agencyFilter.value;
            const categoryMatch = categoryFilter.value === 'semua' || pred === categoryFilter.value;
            const periodMatch = periodFilter.value === 'semua' || item.periode === periodFilter.value;
            const statusMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && studyMatch && agencyMatch && categoryMatch && periodMatch && statusMatch;
        });
    }

    function renderStats() {
        const scores = recaps.map(finalScore);
        const total = recaps.length;
        const finalCount = recaps.filter((item) => item.status === 'final' || item.status === 'arsip').length;
        const passCount = recaps.filter((item) => finalScore(item) >= 70).length;
        document.getElementById('statTotal').textContent = total;
        document.getElementById('statAverage').textContent = Math.round(scores.reduce((sum, score) => sum + score, 0) / total);
        document.getElementById('statHighest').textContent = Math.max(...scores);
        document.getElementById('statLowest').textContent = Math.min(...scores);
        document.getElementById('statFinal').textContent = finalCount;
        document.getElementById('statPassed').textContent = `${Math.round((passCount / total) * 100)}%`;
    }

    function renderDistribution() {
        const labels = ['A', 'B', 'C', 'D'];
        const total = recaps.length;
        document.getElementById('distributionList').innerHTML = labels.map((label) => {
            const count = recaps.filter((item) => predicate(finalScore(item)) === label).length;
            const percent = Math.round((count / total) * 100);
            return `
                <div class="distribution-row">
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Predikat ${label}</strong>
                        <span>${count} mahasiswa</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" style="width:${percent}%"></div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderHistory() {
        document.getElementById('historyList').innerHTML = recaps
            .slice()
            .sort((a, b) => b.updated.localeCompare(a.updated))
            .slice(0, 5)
            .map((item) => `
                <div class="history-row">
                    <div class="d-flex justify-content-between gap-2">
                        <strong>${item.nama}</strong>
                        <span class="badge bg-light text-dark">${predicate(finalScore(item))}</span>
                    </div>
                    <small class="text-muted d-block">${item.updated}</small>
                    <small>${item.note}</small>
                </div>
            `).join('');
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button>
            </li>
        `;
        for (let page = 1; page <= totalPages; page++) {
            html += `
                <li class="page-item ${page === currentPage ? 'active' : ''}">
                    <button class="page-link" data-page="${page}" type="button">${page}</button>
                </li>
            `;
        }
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" data-page="${currentPage + 1}" type="button">Next</button>
            </li>
        `;
        pagination.innerHTML = html;
    }

    function renderTable() {
        const data = filteredData();
        const perPage = Number(perPageSelect.value);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);
        tableBody.innerHTML = pageData.map((item, index) => {
            const score = finalScore(item);
            const pred = predicate(score);
            return `
                <tr>
                    <td>${start + index + 1}</td>
                    <td>
                        <strong>${item.nama}</strong>
                        <small class="text-muted d-block">${item.nim}</small>
                    </td>
                    <td>${item.prodi}</td>
                    <td>${item.instansi}</td>
                    <td>${item.mentor}</td>
                    <td>${item.dosen}</td>
                    <td><strong>${score}</strong></td>
                    <td>${predicateBadge(pred)}</td>
                    <td>${statusBadge(item.status)}</td>
                    <td>
                        <div class="action-group">
                            <button class="btn btn-primary btn-sm" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                            <button class="btn btn-outline-info btn-sm" type="button" data-action="komponen" data-id="${item.id}">Komponen</button>
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-action="cetak rekap" data-id="${item.id}">Cetak</button>
                            <button class="btn btn-outline-success btn-sm" type="button" data-action="export data" data-id="${item.id}">Export</button>
                            <button class="btn btn-outline-primary btn-sm" type="button" data-action="validasi" data-id="${item.id}">Validasi</button>
                            <button class="btn btn-outline-dark btn-sm" type="button" data-action="arsip" data-id="${item.id}">Arsipkan</button>
                            <button class="btn btn-outline-warning btn-sm" type="button" data-action="riwayat" data-id="${item.id}">Riwayat</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} rekap nilai`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderDistribution();
        renderHistory();
    }

    function openDetail(item, action = 'detail') {
        const score = finalScore(item);
        const pred = predicate(score);
        document.getElementById('detailModalLabel').textContent = action === 'komponen' ? 'Komponen Nilai' : 'Detail Rekap Nilai';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Mahasiswa</small>
                        <h5 class="mb-1">${item.nama}</h5>
                        <div>${item.nim} - ${item.prodi}</div>
                        <div class="text-muted">${item.instansi}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 h-100">
                        <small class="text-muted">Ringkasan Nilai Akhir</small>
                        <h3 class="mb-1">${score} ${predicateBadge(pred)}</h3>
                        <div>${statusBadge(item.status)} <span class="ms-2">${item.periode}</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <small class="text-muted">Nilai Mentor</small>
                        <h4 class="mb-0">${item.mentor}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <small class="text-muted">Nilai Dosen Pembimbing</small>
                        <h4 class="mb-0">${item.dosen}</h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <small class="text-muted">Tanggal Pembaruan</small>
                        <h6 class="mb-0">${item.updated}</h6>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3">
                        <small class="text-muted">Catatan Evaluasi</small>
                        <p class="mb-0">${item.note}</p>
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        const score = finalScore(item);
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <strong>${item.nama}</strong>
                        <small class="text-muted d-block">${item.nim} - ${item.instansi}</small>
                    </div>
                    ${statusBadge(item.status)}
                </div>
                <hr>
                <div class="row g-2">
                    <div class="col-4"><small class="text-muted">Nilai Akhir</small><h5>${score}</h5></div>
                    <div class="col-4"><small class="text-muted">Predikat</small><h5>${predicate(score)}</h5></div>
                    <div class="col-4"><small class="text-muted">Tindakan</small><h6 class="text-capitalize">${action}</h6></div>
                </div>
            </div>
        `;
        document.getElementById('confirmNote').value = item.note;
        confirmModal.show();
    }

    document.querySelectorAll('.stat-card[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            activeStatus = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
        showToast('Filter rekap nilai berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        categoryFilter.value = 'semua';
        periodFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter rekap nilai berhasil direset.', 'info');
    });

    [searchInput, studyFilter, agencyFilter, categoryFilter, periodFilter].forEach((element) => {
        element.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });
    });

    searchInput.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') {
            currentPage = 1;
            renderTable();
        }
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
        const item = recaps.find((recap) => recap.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (['detail', 'komponen', 'riwayat', 'cetak rekap', 'export data'].includes(action)) {
            openDetail(item, action);
            if (action !== 'detail' && action !== 'komponen') showToast(`Aksi ${action} untuk ${item.nama} disiapkan.`, 'info');
            return;
        }
        openConfirm(item, action);
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        const { item, action } = pendingAction;
        item.note = document.getElementById('confirmNote').value || item.note;
        item.updated = '25 Mei 2026 21:45';
        if (action === 'validasi') item.status = 'final';
        if (action === 'arsip') item.status = 'arsip';
        confirmModal.hide();
        renderTable();
        openDetail(item, action);
        showToast(`Rekap nilai ${item.nama} berhasil diperbarui.`);
        pendingAction = null;
    });

    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('recapBadge').textContent = 'Diperbarui';
        showToast('Data rekap nilai berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi rekap nilai: 3 nilai akhir menunggu validasi.', 'warning'), 800);
});
</script>
@endpush
