@extends('mentor.layout.mentor')

@section('title', 'Monitoring')
@section('page-title', 'Monitoring')

@push('styles')
<style>
    .monitoring-page .monitoring-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .monitoring-page .stat-card,
    .monitoring-page .filter-card,
    .monitoring-page .table-card,
    .monitoring-page .panel-card { border:0; border-radius:8px; }
    .monitoring-page .stat-card { cursor:pointer; transition:.2s ease; }
    .monitoring-page .stat-card:hover,
    .monitoring-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .monitoring-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .monitoring-page .subnav .btn.active { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
    .monitoring-page .activity-item,
    .monitoring-page .report-item { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .monitoring-page .progress { height:8px; background:#e8eef2; }
    .monitoring-page .progress-bar { background:#2a8fbd; }
    .monitoring-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:470px; }
    .monitoring-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .monitoring-page .pagination .page-link { color:#2a8fbd; }
    .monitoring-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $activeMonitor = $activeMonitor ?? 'Semua Monitoring';
@endphp

<div class="container-fluid monitoring-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
        </ol>
    </nav>

    <section class="monitoring-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Dashboard Monitoring Peserta</h3>
                <p class="mb-0">Pantau kehadiran, aktivitas harian, penyelesaian tugas, perkembangan laporan, dan capaian peserta magang secara real-time.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="monitorBadge">{{ $activeMonitor }}</span>
                <button class="btn btn-dark" type="button" id="refreshButton"><i class="bi bi-arrow-clockwise"></i> Perbarui</button>
            </div>
        </div>
    </section>

    <div class="subnav d-flex flex-wrap gap-2 mb-4">
        <a class="btn btn-outline-warning {{ $activeMonitor === 'Semua Monitoring' ? 'active' : '' }}" href="{{ route('mentor.monitoring') }}">Semua</a>
        <a class="btn btn-outline-warning {{ $activeMonitor === 'Absensi' ? 'active' : '' }}" href="{{ route('mentor.monitoring.absensi') }}">Absensi</a>
        <a class="btn btn-outline-warning {{ $activeMonitor === 'Penugasan' ? 'active' : '' }}" href="{{ route('mentor.monitoring.penugasan') }}">Penugasan</a>
        <a class="btn btn-outline-warning {{ $activeMonitor === 'Laporan' ? 'active' : '' }}" href="{{ route('mentor.laporan') }}">Laporan</a>
        <a class="btn btn-outline-warning {{ $activeMonitor === 'Status Magang' ? 'active' : '' }}" href="{{ route('mentor.monitoring.status') }}">Status Magang</a>
    </div>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-activity"></i>
        <div>5 aktivitas baru, 4 laporan masuk, dan 3 peserta membutuhkan tindak lanjut monitoring hari ini.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peserta Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-attendance-card="rendah">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kehadiran Peserta</p><h3 class="mb-0" id="statAttendance">0%</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-calendar-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-report-card="masuk">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Laporan Masuk</p><h3 class="mb-0" id="statReport">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-file-earmark-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-progress-card="sedang">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Progress Kegiatan</p><h3 class="mb-0" id="statProgress">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-graph-up"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari peserta, kampus, aktivitas">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="participantFilter">Peserta</label>
                    <select class="form-select" id="participantFilter">
                        <option value="semua">Semua Peserta</option>
                        <option value="Aulia Berliana">Aulia Berliana</option>
                        <option value="Dewi Lestari">Dewi Lestari</option>
                        <option value="Naufal Rizky">Naufal Rizky</option>
                        <option value="Rani Kartika">Rani Kartika</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Aktivitas</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="sesuai target">Sesuai Target</option>
                        <option value="proses">Proses</option>
                        <option value="perlu review">Perlu Tindak Lanjut</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="attendanceFilter">Kehadiran</label>
                    <select class="form-select" id="attendanceFilter">
                        <option value="semua">Semua Kehadiran</option>
                        <option value="tinggi">90% ke atas</option>
                        <option value="sedang">80% - 89%</option>
                        <option value="rendah">Di bawah 80%</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
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
                        <h5 class="mb-0">Tabel Monitoring Peserta</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data monitoring</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-warning btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mentorMonitoringPanel" aria-controls="mentorMonitoringPanel"><i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel</button>
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
                                    <th>Perguruan Tinggi</th>
                                    <th>Kehadiran</th>
                                    <th>Aktivitas</th>
                                    <th>Laporan</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="monitorTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data monitoring sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination monitoring"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mentorMonitoringPanel" aria-labelledby="mentorMonitoringPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mentorMonitoringPanelLabel">Panel Monitoring</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
            <div class="card panel-card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Aktivitas Terbaru</h5>
                    <div id="activityPanel"></div>
                </div>
            </div>
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Informasi Laporan</h5>
                    <div id="reportPanel"></div>
            </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Monitoring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Monitoring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Catatan Tindakan</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan monitoring"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmAction">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Monitoring diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const monitors = @json($monitoringRows ?? []);
    const messageUrlTemplate = @json(route('mentor.komunikasi.pesan.peserta', ['peserta' => '__PESERTA__']));
    const reportUrl = @json(route('mentor.laporan'));
    const actionUrlTemplate = @json(route('mentor.monitoring.action', ['internship' => '__INTERNSHIP__']));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const tableBody = document.getElementById('monitorTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const participantFilter = document.getElementById('participantFilter');
    const statusFilter = document.getElementById('statusFilter');
    const attendanceFilter = document.getElementById('attendanceFilter');
    const periodFilter = document.getElementById('periodFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2600 });
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingAction = null;

    const statusClass = (status) => ({
        'sesuai target':'success',
        proses:'warning text-dark',
        'perlu review':'info text-dark',
        terlambat:'danger'
    }[status] || 'secondary');

    const attendanceGroup = (value) => {
        if (value >= 90) return 'tinggi';
        if (value >= 80) return 'sedang';
        return 'rendah';
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function populateParticipantFilter() {
        const names = [...new Set(monitors.map((item) => item.nama).filter((value) => value && value !== '-'))];
        participantFilter.innerHTML = '<option value="semua">Semua Peserta</option>' + names.map((name) => `<option value="${name}">${name}</option>`).join('');
    }

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        return monitors.filter((item) => {
            const keywordMatch = !keyword || `${item.nama} ${item.kampus} ${item.status} ${item.terakhir} ${item.detail}`.toLowerCase().includes(keyword);
            const participantMatch = participantFilter.value === 'semua' || item.nama === participantFilter.value;
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const attendanceMatch = attendanceFilter.value === 'semua' || attendanceGroup(item.hadir) === attendanceFilter.value;
            const periodMatch = periodFilter.value === 'semua' || item.periode === periodFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && participantMatch && statusMatch && attendanceMatch && periodMatch && cardMatch;
        });
    }

    function renderStats() {
        const avgAttendance = Math.round(monitors.reduce((sum, item) => sum + item.hadir, 0) / monitors.length);
        const avgProgress = Math.round(monitors.reduce((sum, item) => sum + item.progress, 0) / monitors.length);
        document.getElementById('statTotal').textContent = monitors.length;
        document.getElementById('statActive').textContent = monitors.filter((item) => item.status !== 'terlambat').length;
        document.getElementById('statAttendance').textContent = `${avgAttendance}%`;
        document.getElementById('statReport').textContent = monitors.reduce((sum, item) => sum + item.laporan, 0);
        document.getElementById('statProgress').textContent = `${avgProgress}%`;
    }

    function renderPanels() {
        document.getElementById('activityPanel').innerHTML = monitors.slice(0, 4).map((item) => `
            <div class="activity-item">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.nama}</strong>
                    <span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span>
                </div>
                <small class="text-muted d-block">${item.terakhir}</small>
                <small>${item.detail}</small>
            </div>
        `).join('');
        document.getElementById('reportPanel').innerHTML = monitors.slice(0, 4).map((item) => `
            <div class="report-item">
                <div class="d-flex justify-content-between">
                    <strong>${item.nama}</strong>
                    <span>${item.laporan} laporan</span>
                </div>
                <div class="progress mt-2"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div>
                    <small class="text-muted">${item.progress}% progres kegiatan</small>
            </div>
        `).join('');
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}" type="button">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}" type="button">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderTable() {
        const data = filteredData();
        const perPage = Number(perPageSelect.value);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);
        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><strong>${item.nama}</strong><small class="text-muted d-block">${item.periode}</small></td>
                <td>${item.kampus}</td>
                <td>${item.hadir}%</td>
                <td>${item.aktivitas} aktivitas<small class="text-muted d-block">${item.terakhir}</small></td>
                <td>${item.laporan} laporan</td>
                <td>
                    <div class="d-flex justify-content-between small"><span>${item.progress}%</span><span>${item.status}</span></div>
                    <div class="progress"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div>
                </td>
                <td><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="review aktivitas" data-id="${item.id}">Tindak Lanjut Aktivitas</button>
                        <button class="btn btn-outline-warning btn-sm" type="button" data-action="berikan catatan" data-id="${item.id}">Berikan Catatan</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="hubungi peserta" data-id="${item.id}">Hubungi Peserta</button>
                        <button class="btn btn-outline-info btn-sm" type="button" data-action="lihat laporan" data-id="${item.id}">Lihat Laporan</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} data monitoring ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderPanels();
    }

    function openDetail(item) {
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Peserta</small><h5>${item.nama}</h5><div>${item.kampus}</div></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Status</small><div class="mb-2"><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></div><div>${item.periode}</div></div></div>
                <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Kehadiran</small><h5>${item.hadir}%</h5></div></div>
                <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Aktivitas</small><h5>${item.aktivitas}</h5></div></div>
                <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Laporan</small><h5>${item.laporan}</h5></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Ringkasan Monitoring</small><p class="mb-0">${item.detail}</p></div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <strong>${item.nama}</strong>
                <small class="text-muted d-block">${item.kampus} - ${item.periode}</small>
                <hr>
                <small class="text-muted">Tindakan monitoring</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = `Catatan ${action} untuk ${item.nama}.`;
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
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter monitoring berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        participantFilter.value = 'semua';
        statusFilter.value = 'semua';
        attendanceFilter.value = 'semua';
        periodFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter monitoring berhasil direset.', 'info');
    });
    [searchInput, participantFilter, statusFilter, attendanceFilter, periodFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
    searchInput.addEventListener('keyup', (event) => { if (event.key === 'Enter') { currentPage = 1; renderTable(); } });
    perPageSelect.addEventListener('change', () => { currentPage = 1; renderTable(); });
    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });
    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = monitors.find((monitor) => monitor.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail' || action === 'lihat laporan') {
            if (action === 'lihat laporan') {
                window.location.href = reportUrl;
                return;
            }
            openDetail(item);
            return;
        }
        if (action === 'hubungi peserta') {
            const pesertaId = item?.peserta_id;
            if (!pesertaId) {
                showToast('Data peserta belum memiliki identitas pesan yang valid.', 'warning');
                return;
            }
            window.location.href = messageUrlTemplate.replace('__PESERTA__', pesertaId);
            return;
        }
        openConfirm(item, action);
    });
    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        const note = document.getElementById('confirmNote').value.trim();
        const actionUrl = actionUrlTemplate.replace('__INTERNSHIP__', pendingAction.item.id);

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                action: pendingAction.action,
                note,
            }),
        })
            .then(async response => {
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal menyimpan tindakan monitoring.');
                }

                pendingAction.item.detail = payload.detail || note || pendingAction.item.detail;
                pendingAction.item.status = payload.status || pendingAction.item.status;
                confirmModal.hide();
                renderTable();
                showToast(payload.message || `Tindakan ${pendingAction.action} berhasil disimpan.`);
                pendingAction = null;
            })
            .catch(error => {
                showToast(error.message || 'Gagal menyimpan tindakan monitoring.', 'danger');
            });
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        window.location.reload();
    });

    populateParticipantFilter();
    renderTable();
    setTimeout(() => showToast('Notifikasi monitoring: 5 aktivitas peserta baru masuk.', 'warning'), 700);
});
</script>
@endpush
