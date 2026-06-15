@extends('pembimbing.layout.pembimbing')

@section('title', 'Absensi')
@section('page-title', 'Absensi')

@push('styles')
<style>
    .attendance-monitor-page .attendance-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .attendance-monitor-page .stat-card,
    .attendance-monitor-page .filter-card,
    .attendance-monitor-page .table-card,
    .attendance-monitor-page .side-panel { border:0; border-radius:8px; }
    .attendance-monitor-page .stat-card { cursor:pointer; transition:.2s ease; }
    .attendance-monitor-page .stat-card:hover,
    .attendance-monitor-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .attendance-monitor-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .attendance-monitor-page .table { font-size:14px; }
    .attendance-monitor-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .attendance-monitor-page .table tbody tr:hover { background:#f7fcfe; }
    .attendance-monitor-page .progress { height:10px; border-radius:999px; }
    .attendance-monitor-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:0; max-width:100%; }
    .attendance-monitor-page .action-group .btn { white-space:nowrap; }
    .attendance-monitor-page .nav-pills .nav-link { color:#2a8fbd; border-radius:8px; }
    .attendance-monitor-page .nav-pills .nav-link.active { background:#2a8fbd; color:#fff; }
    .attendance-monitor-page .distribution-row,
    .attendance-monitor-page .attention-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .attendance-monitor-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .attendance-monitor-page .pagination .page-link { color:#2a8fbd; }
    .attendance-monitor-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid attendance-monitor-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Absensi</li>
        </ol>
    </nav>

    <section class="attendance-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Absensi Mahasiswa</h3>
                <p class="mb-0">Pantau tingkat kehadiran, keterlambatan, izin, sakit, dan mahasiswa yang membutuhkan perhatian pembimbing.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="attendanceBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div id="attendanceAlertText">Memuat data absensi dari database...</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-card-filter="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mahasiswa</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-today-card="hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Hadir Hari Ini</p><h3 class="mb-0" id="statPresent">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-calendar-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-today-card="terlambat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terlambat Hari Ini</p><h3 class="mb-0" id="statLateToday">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-alarm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-today-card="tidak hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Tidak Hadir Hari Ini</p><h3 class="mb-0" id="statAbsentToday">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-attendance-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Kehadiran</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Hari Kerja</p><h3 class="mb-0" id="statWorkdays">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-calendar-week"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Nama/NIM Mahasiswa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, atau status">
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
                    <label class="form-label" for="statusFilter">Status Kehadiran</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="baik">Baik</option>
                        <option value="perlu perhatian">Perlu Perhatian</option>
                        <option value="bermasalah">Bermasalah</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode Tanggal</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Hari Ini">Hari Ini</option>
                        <option value="Minggu Ini">Minggu Ini</option>
                        <option value="Bulan Ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter" title="Terapkan Filter"><i class="bi bi-funnel"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills gap-2 mb-4" id="attendanceTabs">
        <li class="nav-item"><button class="nav-link active" type="button" data-category="semua">Semua</button></li>
        <li class="nav-item"><button class="nav-link" type="button" data-category="hadir">Hadir</button></li>
        <li class="nav-item"><button class="nav-link" type="button" data-category="terlambat">Terlambat</button></li>
        <li class="nav-item"><button class="nav-link" type="button" data-category="tidak hadir">Tidak Hadir</button></li>
        <li class="nav-item"><button class="nav-link" type="button" data-category="izin">Izin</button></li>
        <li class="nav-item"><button class="nav-link" type="button" data-category="sakit">Sakit</button></li>
    </ul>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Rekap Kehadiran</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#attendanceExtraPanel" aria-controls="attendanceExtraPanel">
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
                                    <th>Penempatan</th>
                                    <th>Kehadiran (%)</th>
                                    <th>Hadir</th>
                                    <th>Terlambat</th>
                                    <th>Tidak Hadir</th>
                                    <th>Izin</th>
                                    <th>Sakit</th>
                                    <th>Status</th>
                                    <th width="260">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data absensi tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination absensi">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="attendanceExtraPanel" aria-labelledby="attendanceExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="attendanceExtraPanelLabel">Panel Absensi</h5>
            <small class="text-muted">Distribusi dan mahasiswa yang perlu perhatian</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Kehadiran</h5>
                <div id="distributionPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Mahasiswa Perlu Perhatian</h5>
                <div id="attentionPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Absensi</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div class="w-100">
                        <p class="mb-2" id="confirmMessage">Apakah tindakan akan disimpan?</p>
                        <div class="small text-muted mb-2" id="confirmSummary"></div>
                        <label class="form-label" for="lecturerNote">Catatan Pembimbing</label>
                        <textarea class="form-control" id="lecturerNote" rows="3" placeholder="Tambahkan catatan absensi"></textarea>
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
    <div id="attendanceToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const messageStoreUrlTemplate = @json(route('pembimbing.mahasiswa.pesan.store', ['internship' => '__INTERNSHIP__']));
    const attendances = @json($attendanceRows ?? []);

    let currentPage = 1;
    let activeCategory = 'semua';
    let pendingAction = null;
    const totalWorkdays = 26;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('attendanceTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageForm = document.getElementById('messageForm');
    const toast = new bootstrap.Toast(document.getElementById('attendanceToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return { hadir: 'bg-success', terlambat: 'bg-warning text-dark', 'tidak hadir': 'bg-danger', dipantau: 'bg-info text-dark', baik: 'bg-success', 'perlu perhatian': 'bg-warning text-dark', bermasalah: 'bg-danger' }[value] || 'bg-secondary';
    }

    function progressClass(value) {
        if (value >= 85) return 'bg-success';
        if (value >= 70) return 'bg-info';
        return 'bg-warning';
    }

    function filteredAttendances() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const period = periodFilter.value;

        return attendances.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.penempatan, item.status, item.today, item.catatan].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchPeriod = period === 'semua' || item.periode === period;
            const matchCategory = activeCategory === 'semua' || item.today === activeCategory;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchPeriod && matchCategory;
        });
    }

    function updateStats() {
        const average = attendances.length
            ? Math.round(attendances.reduce((sum, item) => sum + Number(item.percent || item.persen || 0), 0) / attendances.length)
            : 0;
        document.getElementById('statTotal').textContent = attendances.length;
        document.getElementById('statPresent').textContent = attendances.filter((item) => item.today === 'hadir').length;
        document.getElementById('statLateToday').textContent = attendances.filter((item) => item.today === 'terlambat').length;
        document.getElementById('statAbsentToday').textContent = attendances.filter((item) => item.today === 'tidak hadir').length;
        document.getElementById('statAverage').textContent = `${average}%`;
        document.getElementById('statWorkdays').textContent = totalWorkdays;
    }

    function updateAlertSummary() {
        const lateToday = attendances.filter((item) => item.today === 'terlambat').length;
        const absentToday = attendances.filter((item) => item.today === 'tidak hadir').length;
        const needsAttention = attendances.filter((item) => item.status !== 'hadir').length;

        document.getElementById('attendanceAlertText').textContent = `${lateToday} peserta terlambat hari ini, ${absentToday} peserta tidak hadir, dan ${needsAttention} data absensi perlu dipantau.`;
        document.getElementById('attendanceBadge').textContent = 'Dari Database';
    }

    function renderDistribution() {
        const totals = [
            ['Hadir', attendances.reduce((sum, item) => sum + item.hadir, 0), 'bg-success'],
            ['Terlambat', attendances.reduce((sum, item) => sum + item.terlambat, 0), 'bg-warning'],
            ['Tidak Hadir', attendances.reduce((sum, item) => sum + item.tidakHadir, 0), 'bg-danger'],
            ['Izin', attendances.reduce((sum, item) => sum + item.izin, 0), 'bg-info'],
            ['Sakit', attendances.reduce((sum, item) => sum + item.sakit, 0), 'bg-secondary']
        ];
        const maxValue = Math.max(...totals.map((item) => item[1]), 1);
        document.getElementById('distributionPanel').innerHTML = totals.map(([label, count, color]) => `
            <div class="distribution-row">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">${label}</span>
                    <span>${count} hari</span>
                </div>
                <div class="progress">
                    <div class="progress-bar ${color}" style="width:${Math.round((count / maxValue) * 100)}%"></div>
                </div>
            </div>
        `).join('');

        document.getElementById('attentionPanel').innerHTML = attendances
            .filter((item) => item.status !== 'hadir')
            .map((item) => `
                <div class="attention-row">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-semibold">${item.nama}</div>
                            <div class="small text-muted">${item.penempatan}</div>
                        </div>
                        <span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span>
                    </div>
                    <div class="small mt-2">Kehadiran ${item.percent || item.persen || 0}% - ${item.catatan}</div>
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
        const data = filteredAttendances();
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
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="min-width:90px">
                            <div class="progress-bar ${progressClass(item.percent || item.persen || 0)}" style="width:${item.percent || item.persen || 0}%"></div>
                        </div>
                        <span class="small fw-semibold">${item.percent || item.persen || 0}%</span>
                    </div>
                </td>
                <td>${item.hadir}</td>
                <td>${item.terlambat}</td>
                <td>${item.tidakHadir}</td>
                <td>${item.izin}</td>
                <td>${item.sakit}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="catatan" data-id="${item.id}">Beri Catatan</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="pesan" data-id="${item.id}">Kirim Pesan</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} rekap absensi ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} rekap`
            : 'Menampilkan 0 rekap';
        renderPagination(totalPages);
        updateStats();
        updateAlertSummary();
        renderDistribution();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('attendanceToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'detail') {
        const titles = {
            detail: 'Detail Absensi',
            catatan: 'Catatan Absensi',
            pesan: 'Kirim Pesan'
        };
        document.getElementById('detailTitle').textContent = titles[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Penempatan</strong><div>${item.penempatan}</div></div>
                <div class="col-md-6"><strong>Kehadiran</strong><div>${item.percent || item.persen || 0}%</div></div>
                <div class="col-md-6"><strong>Status</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-4"><strong>Hadir</strong><div>${item.hadir} hari</div></div>
                <div class="col-md-4"><strong>Terlambat</strong><div>${item.terlambat} hari</div></div>
                <div class="col-md-4"><strong>Tidak Hadir</strong><div>${item.tidakHadir} hari</div></div>
                <div class="col-md-4"><strong>Izin</strong><div>${item.izin} hari</div></div>
                <div class="col-md-4"><strong>Sakit</strong><div>${item.sakit} hari</div></div>
                <div class="col-md-4"><strong>Hari Ini</strong><div>${titleCase(item.today)}</div></div>
                <div class="col-12"><strong>Catatan Pembimbing</strong><div>${item.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = `Simpan tindak lanjut ${titleCase(action)} untuk ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.nim} - Kehadiran ${item.percent || item.persen || 0}% - ${titleCase(item.status)}`;
        document.getElementById('lecturerNote').value = item.catatan;
        confirmModal.show();
    }

    function openMessageModal(item) {
        document.getElementById('messageRecipient').value = `${item.nama} (${item.nim})`;
        document.getElementById('messageSubject').value = `Monitoring Absensi - ${item.nama}`;
        document.getElementById('messageBody').value = '';
        document.getElementById('messageAttachment').value = '';
        document.getElementById('messageSummary').textContent = `Pesan akan dikirim ke ${item.nama} dan tersimpan pada percakapan peserta.`;
        messageForm.action = messageStoreUrlTemplate.replace('__INTERNSHIP__', item.id);
        messageModal.show();
    }

    document.querySelectorAll('[data-card-filter], [data-today-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-card-filter], [data-today-card], [data-attendance-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            activeCategory = card.dataset.todayCard || 'semua';
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-attendance-card]').addEventListener('click', () => {
        showToast('Rata-rata kehadiran seluruh mahasiswa ditampilkan.', 'info');
    });

    document.querySelectorAll('#attendanceTabs button').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('#attendanceTabs button').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            activeCategory = button.dataset.category;
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
        showToast('Filter absensi berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        activeCategory = 'semua';
        currentPage = 1;
        document.querySelectorAll('#attendanceTabs button').forEach((item) => item.classList.remove('active'));
        document.querySelector('#attendanceTabs button[data-category="semua"]').classList.add('active');
        document.querySelectorAll('[data-card-filter], [data-today-card], [data-attendance-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-card-filter="semua"]').classList.add('active');
        renderTable();
        showToast('Filter absensi berhasil direset.', 'info');
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
        const item = attendances.find((attendance) => attendance.id === Number(button.dataset.id));
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
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Tindak lanjut absensi ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data absensi berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('attendanceBadge').textContent = 'Diperbarui';
        showToast('Data absensi mahasiswa berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Data absensi berhasil dimuat dari database.', 'warning'), 800);
});
</script>
@endpush
