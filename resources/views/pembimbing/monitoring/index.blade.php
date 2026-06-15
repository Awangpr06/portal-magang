@extends('pembimbing.layout.pembimbing')

@section('title', 'Monitoring')
@section('page-title', 'Monitoring')

@push('styles')
<style>
    .monitoring-page .monitoring-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .monitoring-page .stat-card,
    .monitoring-page .filter-card,
    .monitoring-page .table-card,
    .monitoring-page .progress-panel { border:0; border-radius:8px; }
    .monitoring-page .stat-card { cursor:pointer; transition:.2s ease; }
    .monitoring-page .stat-card:hover,
    .monitoring-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .monitoring-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .monitoring-page .table { font-size:14px; }
    .monitoring-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .monitoring-page .table tbody tr:hover { background:#f7fcfe; }
    .monitoring-page .progress { height:10px; border-radius:999px; }
    .monitoring-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:390px; }
    .monitoring-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .monitoring-page .timeline-item { border-left:3px solid #2a8fbd; padding-left:12px; margin-bottom:14px; }
    .monitoring-page .pagination .page-link { color:#2a8fbd; }
    .monitoring-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $focus = $focus ?? 'semua';
    $focusLabel = [
        'semua' => 'Semua Monitoring',
        'aktivitas' => 'Monitoring Aktivitas',
        'absensi' => 'Absensi',
        'progress' => 'Monitoring Progress',
        'logbook' => 'Logbook Harian',
    ][$focus] ?? 'Semua Monitoring';
@endphp

<div class="container-fluid monitoring-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
        </ol>
    </nav>

    <section class="monitoring-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">{{ $focusLabel }}</h3>
                <p class="mb-0">Pantau aktivitas, kehadiran, progress capaian, laporan, dan dokumentasi kegiatan mahasiswa magang secara terintegrasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="monitoringBadge">Live</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-bell-fill"></i>
        <div>5 pembaruan monitoring terdeteksi dari aktivitas mahasiswa hari ini.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-card-filter="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Dimonitor</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-attendance-card="hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kehadiran Hari Ini</p><h3 class="mb-0" id="statAttendance">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-calendar-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="sedang">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Progress Rata-rata</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-logbook-card="lengkap">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Logbook Lengkap</p><h3 class="mb-0" id="statLogbook">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-journal-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="perlu tindak lanjut">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Perlu Tindak Lanjut</p><h3 class="mb-0" id="statProblem">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Monitoring Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-check2-square"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, atau aktivitas">
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
                    <label class="form-label" for="statusFilter">Status Magang</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="perlu tindak lanjut">Perlu Tindak Lanjut</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="progressFilter">Progress</label>
                    <select class="form-select" id="progressFilter">
                        <option value="semua">Semua Progress</option>
                        <option value="rendah">&lt; 50%</option>
                        <option value="sedang">50% - 79%</option>
                        <option value="tinggi">&gt;= 80%</option>
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
                            <h5 class="mb-1">Tabel Monitoring Mahasiswa</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="togglePanelButton" data-bs-toggle="offcanvas" data-bs-target="#monitoringExtraPanel" aria-controls="monitoringExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Tampilkan Panel
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
                                    <th>Nama Mahasiswa</th>
                                    <th>Penempatan</th>
                                    <th>Kehadiran</th>
                                    <th>Progress</th>
                                    <th>Status Logbook</th>
                                    <th>Status Laporan</th>
                                    <th>Aktivitas Terakhir</th>
                                    <th width="390">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="monitoringTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data monitoring tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination monitoring">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="monitoringExtraPanel" aria-labelledby="monitoringExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="monitoringExtraPanelLabel">Panel Progres Monitoring</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card progress-panel shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="mb-3">Panel Progres Monitoring</h5>
                <div id="progressPanel"></div>
            </div>
        </div>
        <div class="card progress-panel shadow-sm border-0">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Aktivitas Terbaru</h6>
                <div id="activityTimeline"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Monitoring</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Monitoring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div class="w-100">
                        <p class="mb-2" id="confirmMessage">Apakah tindakan akan dilanjutkan?</p>
                        <div class="small text-muted mb-2" id="confirmSummary"></div>
                        <label class="form-label" for="lecturerNote">Catatan Pembimbing</label>
                        <textarea class="form-control" id="lecturerNote" rows="3" placeholder="Tambahkan catatan tindak lanjut"></textarea>
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

<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Pesan ke Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="messageForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="messageRecipient">Penerima</label>
                            <input type="text" class="form-control" id="messageRecipient" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="messageSubject">Subjek</label>
                            <input type="text" class="form-control" id="messageSubject" name="subject" maxlength="150" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="messageBody">Pesan</label>
                            <textarea class="form-control" id="messageBody" name="message" rows="5" maxlength="5000" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="messageAttachment">Lampiran URL</label>
                            <input type="text" class="form-control" id="messageAttachment" name="attachment" maxlength="255" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="small text-muted mt-3" id="messageSummary"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="monitoringToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@php
    $monitoringRowsData = $monitoringRows ?? [];
    $monitoringPlacementOptionsData = $monitoringPlacementOptions ?? [];
    $monitoringSummaryData = $monitoringSummary ?? [
        'total' => 0,
        'aktif' => 0,
        'perlu_tindak_lanjut' => 0,
        'selesai' => 0,
        'rata_rata' => 0,
    ];
    $focusData = $focus ?? 'semua';
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const messageStoreUrlTemplate = @json(route('pembimbing.mahasiswa.pesan.store', ['internship' => '__INTERNSHIP__']));
    const focus = @json($focusData);
    const monitoringData = @json($monitoringRowsData);
    const placementOptions = @json($monitoringPlacementOptionsData);
    const monitoringSummary = @json($monitoringSummaryData);

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const progressFilter = document.getElementById('progressFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('monitoringTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageForm = document.getElementById('messageForm');
    const toast = new bootstrap.Toast(document.getElementById('monitoringToast'), { delay: 3000 });
    let selectedStudentId = null;

    placementOptions.forEach((placement) => {
        const option = document.createElement('option');
        option.value = placement;
        option.textContent = placement;
        agencyFilter.appendChild(option);
    });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return { aktif: 'bg-success', 'perlu tindak lanjut': 'bg-warning text-dark', selesai: 'bg-primary' }[value] || 'bg-secondary';
    }

    function attendanceClass(value) {
        return {
            Hadir: 'bg-success',
            Izin: 'bg-info text-dark',
            Terlambat: 'bg-warning text-dark',
            'Tidak Hadir': 'bg-danger'
        }[value];
    }

    function progressClass(value) {
        if (value >= 80) return 'bg-success';
        if (value >= 50) return 'bg-info';
        return 'bg-warning';
    }

    function progressMatch(value, filter) {
        if (filter === 'rendah') return value < 50;
        if (filter === 'sedang') return value >= 50 && value < 80;
        if (filter === 'tinggi') return value >= 80;
        return true;
    }

    function applyFocusFilters() {
        if (focus === 'absensi') searchInput.value = 'Hadir';
        if (focus === 'progress') progressFilter.value = 'sedang';
        if (focus === 'logbook') searchInput.value = 'Lengkap';
        if (focus === 'aktivitas') searchInput.value = '';
    }

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const progress = progressFilter.value;

        return monitoringData.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.penempatan, item.kampus, item.kehadiran, item.logbook, item.laporan, item.aktivitas, item.catatan].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchProgress = progressMatch(item.progress, progress);
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchProgress;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = monitoringSummary.total || 0;
        document.getElementById('statAttendance').textContent = monitoringData.filter((item) => item.kehadiran === 'Hadir').length;
        document.getElementById('statAverage').textContent = `${monitoringSummary.rata_rata || 0}%`;
        document.getElementById('statLogbook').textContent = monitoringData.filter((item) => item.logbook === 'Lengkap').length;
        document.getElementById('statProblem').textContent = monitoringSummary.perlu_tindak_lanjut || monitoringData.filter((item) => item.status === 'perlu tindak lanjut').length;
        document.getElementById('statDone').textContent = monitoringSummary.selesai || monitoringData.filter((item) => item.status === 'selesai').length;
    }

    function renderProgressPanel() {
        const topItems = monitoringData.slice(0, 5);
        document.getElementById('progressPanel').innerHTML = topItems.map((item) => `
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-semibold">${item.nama}</span>
                    <span>${item.progress}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar ${progressClass(item.progress)}" style="width:${item.progress}%"></div>
                </div>
            </div>
        `).join('');

        document.getElementById('activityTimeline').innerHTML = monitoringData.slice(0, 4).map((item) => `
            <div class="timeline-item">
                <div class="fw-semibold">${item.nama}</div>
                <div class="small text-muted">${item.aktivitas}</div>
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
        const data = filteredData();
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
                <td>${item.penempatan}</td>
                <td><span class="badge ${attendanceClass(item.kehadiran)}">${item.kehadiran}</span></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="min-width:90px">
                            <div class="progress-bar ${progressClass(item.progress)}" style="width:${item.progress}%"></div>
                        </div>
                        <span class="small fw-semibold">${item.progress}%</span>
                    </div>
                </td>
                <td>${item.logbook}</td>
                <td>${item.laporan}</td>
                <td>${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <button class="btn btn-primary btn-sm" type="button" data-action="aktivitas" data-id="${item.id}">Aktivitas</button>
                        <button class="btn btn-success btn-sm" type="button" data-action="kehadiran" data-id="${item.id}">Kehadiran</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-action="progress" data-id="${item.id}">Progress</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="catatan" data-id="${item.id}">Beri Catatan</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="pesan" data-id="${item.id}">Kirim Pesan</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} data monitoring ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data monitoring`
            : 'Menampilkan 0 data monitoring';
        renderPagination(totalPages);
        updateStats();
        renderProgressPanel();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('monitoringToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'detail') {
        const labels = {
            detail: 'Detail Monitoring',
            aktivitas: 'Monitoring Aktivitas',
            kehadiran: 'Monitoring Kehadiran',
            progress: 'Monitoring Progress',
            catatan: 'Catatan Pembimbing',
            pesan: 'Kirim Pesan'
        };
        document.getElementById('detailTitle').textContent = labels[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Nama Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Penempatan</strong><div>${item.penempatan}</div></div>
                <div class="col-md-6"><strong>Kehadiran</strong><div>${item.kehadiran}</div></div>
                <div class="col-md-6"><strong>Progress Magang</strong><div>${item.progress}%</div></div>
                <div class="col-md-6"><strong>Status Logbook</strong><div>${item.logbook}</div></div>
                <div class="col-md-6"><strong>Status Laporan</strong><div>${item.laporan}</div></div>
                <div class="col-12"><strong>Aktivitas Terakhir</strong><div>${item.aktivitas}</div></div>
                <div class="col-12"><strong>Catatan Pembimbing</strong><div>${item.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = `Simpan tindak lanjut ${titleCase(action)} untuk ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.nim} - ${item.penempatan} - Progress ${item.progress}%`;
        document.getElementById('lecturerNote').value = item.catatan;
        confirmModal.show();
    }

    function openMessageModal(item) {
        selectedStudentId = item.id;
        document.getElementById('messageRecipient').value = `${item.nama} (${item.nim})`;
        document.getElementById('messageSubject').value = `Monitoring - ${item.nama}`;
        document.getElementById('messageBody').value = '';
        document.getElementById('messageAttachment').value = '';
        document.getElementById('messageSummary').textContent = `Pesan akan dikirim ke ${item.nama} dan tersimpan pada percakapan peserta.`;
        messageForm.action = messageStoreUrlTemplate.replace('__INTERNSHIP__', item.id);
        messageModal.show();
    }

    document.querySelectorAll('[data-card-filter], [data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-card-filter], [data-status-card], [data-attendance-card], [data-progress-card], [data-logbook-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard || 'semua';
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-attendance-card]').addEventListener('click', () => {
        searchInput.value = 'Hadir';
        currentPage = 1;
        renderTable();
    });

    document.querySelector('[data-progress-card]').addEventListener('click', () => {
        progressFilter.value = 'sedang';
        currentPage = 1;
        renderTable();
    });

    document.querySelector('[data-logbook-card]').addEventListener('click', () => {
        searchInput.value = 'Lengkap';
        currentPage = 1;
        renderTable();
    });

    [studyFilter, agencyFilter, statusFilter, progressFilter].forEach((input) => {
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
        showToast('Filter monitoring berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        progressFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-card-filter], [data-status-card], [data-attendance-card], [data-progress-card], [data-logbook-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-card-filter="semua"]').classList.add('active');
        renderTable();
        showToast('Filter monitoring berhasil direset.', 'info');
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
        const item = monitoringData.find((data) => data.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item, action);
            return;
        }
        if (action === 'pesan') {
            openMessageModal(item);
            return;
        }
        openConfirm(item, action);
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.catatan = document.getElementById('lecturerNote').value || pendingAction.item.catatan;
        confirmModal.hide();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Tindak lanjut monitoring ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
    });

    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('monitoringBadge').textContent = 'Diperbarui';
        showToast('Data monitoring dan indikator pembaruan berhasil diperbarui.', 'info');
    });

    const monitoringExtraPanel = document.getElementById('monitoringExtraPanel');
    const togglePanelButton = document.getElementById('togglePanelButton');
    monitoringExtraPanel.addEventListener('show.bs.offcanvas', () => {
        togglePanelButton.innerHTML = '<i class="bi bi-layout-sidebar-inset-reverse"></i> Sembunyikan Panel';
    });
    monitoringExtraPanel.addEventListener('hide.bs.offcanvas', () => {
        togglePanelButton.innerHTML = '<i class="bi bi-layout-sidebar-inset"></i> Tampilkan Panel';
    });

    applyFocusFilters();
    renderTable();
    setTimeout(() => showToast('Notifikasi monitoring: 5 aktivitas terbaru perlu ditinjau.', 'warning'), 800);
});
</script>
@endpush
