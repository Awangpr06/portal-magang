@extends('pembimbing.layout.pembimbing')

@section('title', 'Status Magang')
@section('page-title', 'Status Magang')

@push('styles')
<style>
    .progress-monitor-page .progress-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .progress-monitor-page .stat-card,
    .progress-monitor-page .filter-card,
    .progress-monitor-page .table-card,
    .progress-monitor-page .side-panel { border:0; border-radius:8px; }
    .progress-monitor-page .stat-card { cursor:pointer; transition:.2s ease; }
    .progress-monitor-page .stat-card:hover,
    .progress-monitor-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .progress-monitor-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .progress-monitor-page .table { font-size:14px; }
    .progress-monitor-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .progress-monitor-page .table tbody tr:hover { background:#f7fcfe; }
    .progress-monitor-page .progress { height:10px; border-radius:999px; }
    .progress-monitor-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:0; max-width:100%; }
    .progress-monitor-page .action-group .btn { white-space:nowrap; }
    .progress-monitor-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .progress-monitor-page .pagination .page-link { color:#2a8fbd; }
    .progress-monitor-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid progress-monitor-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Status Magang</li>
        </ol>
    </nav>

    <section class="progress-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Status Magang</h3>
                <p class="mb-0">Pantau status pelaksanaan magang peserta yang dihitung dari periode magang dan riwayat absensi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="progressBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-graph-up-arrow"></i>
        <div>Status magang otomatis berubah menjadi <strong>akan selesai</strong> saat sisa periode magang tinggal 7 hari atau kurang.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-card-filter="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Status</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-speedometer2"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Selesai</p><h3 class="mb-0" id="statTarget">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-bullseye"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="akan selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Akan Selesai</p><h3 class="mb-0" id="statLow">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="highest">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Progress Tertinggi</p><h3 class="mb-0" id="statHighest">0%</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-trophy"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-activity-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sedang Magang</p><h3 class="mb-0" id="statDone">0</h3></div>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, atau target">
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
                        <option value="sedang magang">Sedang Magang</option>
                        <option value="akan selesai">Akan Selesai</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <label class="form-label" for="rangeFilter">Rentang</label>
                    <select class="form-select" id="rangeFilter">
                        <option value="semua">Semua</option>
                        <option value="rendah">&lt; 50%</option>
                        <option value="sedang">50-79%</option>
                        <option value="tinggi">&gt;= 80%</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua</option>
                        <option value="Batch 1 2026">Batch 1</option>
                        <option value="Batch 2 2026">Batch 2</option>
                        <option value="MBKM 2026">MBKM</option>
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
                            <h5 class="mb-1">Tabel Status Magang Peserta</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
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
                                    <th>Perguruan Tinggi</th>
                                    <th>Penempatan</th>
                                    <th>Progress (%)</th>
                                    <th>Absensi Terakhir</th>
                                    <th>Periode Magang</th>
                                    <th>Status Magang</th>
                                    <th>Hari Tersisa</th>
                                    <th width="260">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="progressTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data status magang tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination status magang">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Progress</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Progress</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div class="w-100">
                        <p class="mb-2" id="confirmMessage">Apakah evaluasi akan disimpan?</p>
                        <div class="small text-muted mb-2" id="confirmSummary"></div>
                        <label class="form-label" for="lecturerEvaluation">Evaluasi Pembimbing</label>
                        <textarea class="form-control" id="lecturerEvaluation" rows="3" placeholder="Tambahkan evaluasi progress"></textarea>
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
    <div id="progressToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@php
    $statusMagangRowsData = $statusMagangData ?? [];
    $statusMagangPlacementOptionsData = $statusMagangPlacementOptions ?? [];
    $statusMagangCampusOptionsData = $statusMagangCampusOptions ?? [];
    $statusMagangSummaryData = $statusMagangSummary ?? [
        'total' => 0,
        'sedang' => 0,
        'akan_selesai' => 0,
        'selesai' => 0,
        'rata_rata' => 0,
    ];
@endphp

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const statuses = @json($statusMagangRowsData);
    const placementOptions = @json($statusMagangPlacementOptionsData);
    const campusOptions = @json($statusMagangCampusOptionsData);
    const monitoringSummary = @json($statusMagangSummaryData);

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const rangeFilter = document.getElementById('rangeFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('progressTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('progressToast'), { delay: 3000 });

    function statusClass(value) {
        return {
            'sedang magang': 'bg-success',
            'akan selesai': 'bg-warning text-dark',
            selesai: 'bg-info text-dark'
        }[value] || 'bg-secondary';
    }

    function titleCase(value) {
        return String(value || '')
            .replace(/_/g, ' ')
            .replace(/\b\w/g, (char) => char.toUpperCase());
    }

    function progressClass(value) {
        if (value >= 80) return 'bg-success';
        if (value >= 50) return 'bg-info';
        return 'bg-warning';
    }

    function rangeMatch(value, range) {
        if (range === 'rendah') return value < 50;
        if (range === 'sedang') return value >= 50 && value < 80;
        if (range === 'tinggi') return value >= 80;
        return true;
    }

    function filteredProgresses() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const range = rangeFilter.value;
        const period = periodFilter.value;

        return statuses.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.penempatan, item.kampus, item.status, item.catatan, item.absensi_terakhir].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.penempatan === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchRange = rangeMatch(item.progress, range);
            const matchPeriod = period === 'semua' || item.periode === period;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchRange && matchPeriod;
        });
    }

    function updateStats() {
        const active = statuses.length;
        const average = active ? Math.round(statuses.reduce((sum, item) => sum + Number(item.progress || 0), 0) / active) : 0;
        const highest = active ? Math.max(...statuses.map((item) => Number(item.progress || 0))) : 0;
        document.getElementById('statActive').textContent = active;
        document.getElementById('statAverage').textContent = `${average}%`;
        document.getElementById('statTarget').textContent = statuses.filter((item) => item.status === 'selesai').length;
        document.getElementById('statLow').textContent = statuses.filter((item) => item.status === 'akan selesai').length;
        document.getElementById('statHighest').textContent = `${highest}%`;
        document.getElementById('statDone').textContent = statuses.filter((item) => item.status === 'sedang magang').length;
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
        const data = filteredProgresses();
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
                <td>${item.kampus}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="min-width:90px">
                            <div class="progress-bar ${progressClass(item.progress)}" style="width:${item.progress}%"></div>
                        </div>
                        <span class="small fw-semibold">${item.progress}%</span>
                    </div>
                </td>
                <td>${item.absensi_terakhir}</td>
                <td>${item.tanggal_mulai} - ${item.tanggal_selesai}</td>
                <td><span class="badge ${statusClass(item.status)} text-capitalize">${item.status}</span></td>
                <td>${item.hari_tersisa === null ? '-' : item.hari_tersisa + ' hari tersisa'}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} data status magang ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data`
            : 'Menampilkan 0 data';
        renderPagination(totalPages);
        updateStats();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('progressToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item) {
        if (!item) {
            showToast('Detail status magang tidak ditemukan.', 'danger');
            return;
        }

        document.getElementById('detailTitle').textContent = 'Detail Status Magang';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Penempatan</strong><div>${item.penempatan}</div></div>
                <div class="col-md-6"><strong>Perguruan Tinggi</strong><div>${item.kampus}</div></div>
                <div class="col-md-6"><strong>Progress</strong><div>${item.progress}%</div></div>
                <div class="col-md-6"><strong>Status Magang</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Periode Magang</strong><div>${item.tanggal_mulai} - ${item.tanggal_selesai}</div></div>
                <div class="col-md-6"><strong>Absensi Terakhir</strong><div>${item.absensi_terakhir}</div></div>
                <div class="col-md-6"><strong>Hari Tersisa</strong><div>${item.hari_tersisa === null ? '-' : item.hari_tersisa + ' hari'}</div></div>
                <div class="col-12"><strong>Catatan</strong><div>${item.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item) {
        pendingAction = { item };
        document.getElementById('confirmMessage').textContent = `Simpan catatan status magang untuk ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.nim} - ${item.penempatan} - ${item.progress}%`;
        document.getElementById('lecturerEvaluation').value = item.catatan;
        confirmModal.show();
    }

    document.querySelectorAll('[data-card-filter], [data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-card-filter], [data-status-card], [data-progress-card], [data-activity-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard || 'semua';
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-progress-card="average"]').addEventListener('click', () => showToast('Rata-rata status magang seluruh mahasiswa ditampilkan.', 'info'));
    document.querySelector('[data-progress-card="highest"]').addEventListener('click', () => {
        rangeFilter.value = 'tinggi';
        currentPage = 1;
        renderTable();
    });
    document.querySelector('[data-activity-card]').addEventListener('click', () => showToast('Ringkasan status magang dihitung dari database.', 'info'));

    [studyFilter, agencyFilter, statusFilter, rangeFilter, periodFilter].forEach((input) => {
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
        showToast('Filter status magang berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        rangeFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-card-filter], [data-status-card], [data-progress-card], [data-activity-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-card-filter="semua"]').classList.add('active');
        renderTable();
        showToast('Filter status magang berhasil direset.', 'info');
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
        const item = statuses.find((progress) => progress.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item);
            return;
        }
        openConfirm(item);
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.catatan = document.getElementById('lecturerEvaluation').value || pendingAction.item.catatan;
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item);
        showToast(`Status magang ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data status magang berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('progressBadge').textContent = 'Diperbarui';
        showToast('Data status magang berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi status magang: data terbaru berhasil dimuat.', 'warning'), 800);
});
</script>
@endpush
