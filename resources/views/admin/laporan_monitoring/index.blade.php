@extends('admin.layout.admin')

@section('title', 'Monitoring')

@push('styles')
<style>
    .report-monitor-page .page-title { font-weight: 700; color: #163342; }
    .report-monitor-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .report-monitor-page .stat-card,
    .report-monitor-page .filter-card,
    .report-monitor-page .table-card,
    .report-monitor-page .activity-card { border: 0; border-radius: 8px; }
    .report-monitor-page .stat-card { cursor: pointer; transition: .2s ease; }
    .report-monitor-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .report-monitor-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .activity-item { border-left: 3px solid #0b5f86; padding-left: 12px; margin-bottom: 14px; }
    .report-monitor-page .table { font-size: 14px; }
    .report-monitor-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .report-monitor-page .table tbody tr:hover { background: #f8fbfd; }
    .report-monitor-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 280px; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
@php
    $activeTab = $activeTab ?? 'dashboard';
@endphp

<div class="container-fluid report-monitor-page" data-active-tab="{{ $activeTab }}">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Monitoring</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Monitoring</h2>
            <p class="text-muted mb-0">Pusat evaluasi laporan, monitoring aktivitas, rekapitulasi, dan analisis sistem kerja sama perguruan tinggi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportAllButton"><i class="bi bi-download"></i> Ekspor Laporan</button>
            <button class="btn btn-primary" type="button" id="analysisButton"><i class="bi bi-graph-up"></i> Monitoring Lanjutan</button>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a class="btn {{ $activeTab === 'dashboard' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.laporan-monitoring.index') }}">Dashboard</a>
        <a class="btn {{ $activeTab === 'rekap-absensi' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.laporan-monitoring.rekap-absensi') }}">Rekap Absensi</a>
        <a class="btn {{ $activeTab === 'rekap-kegiatan' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.laporan-monitoring.rekap-kegiatan') }}">Rekap Kegiatan</a>
        <a class="btn {{ $activeTab === 'statistik-pengguna' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.laporan-monitoring.statistik-pengguna') }}">Statistik Pengguna</a>
        <a class="btn {{ $activeTab === 'statistik-perguruan-tinggi' ? 'btn-primary' : 'btn-outline-primary' }}" href="{{ route('admin.laporan-monitoring.statistik-perguruan-tinggi') }}">Statistik Perguruan Tinggi</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Magang Aktif</p><h3 class="mb-0" id="statActiveInternship">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-briefcase"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-type-card="Institusi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Instansi Mitra</p><h3 class="mb-0" id="statPartners">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-building"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-type-card="Magang">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Mahasiswa Terlibat</p><h3 class="mb-0" id="statStudents">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-mortarboard"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" data-status-card="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Laporan Disetujui</p><h3 class="mb-0" id="statApproved">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="typeFilter">Jenis Laporan</label>
                    <select class="form-select" id="typeFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="Berkala">Berkala</option>
                        <option value="Akhir">Akhir</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="studyFilter">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Semua Program Studi">Semua Program Studi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="review">Review</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="searchInput">Pencarian</label>
                    <input class="form-control" id="searchInput" type="search" placeholder="Cari laporan">
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-9">
            <div class="card table-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Rekapitulasi Data</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                    </div>
                    <div class="table-responsive" id="tableWrapper">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Laporan</th>
                                    <th>Periode</th>
                                    <th>Program Studi</th>
                                    <th>Instansi</th>
                                    <th>Mahasiswa</th>
                                    <th>Status</th>
                            <th>Diunggah Oleh</th>
                                    <th>Tanggal</th>
                                    <th width="300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="reportTable"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data laporan tidak ditemukan.</p></div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination laporan monitoring"><ul class="pagination mb-0" id="pagination"></ul></nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card activity-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Monitoring Aktivitas</h5>
                    <div id="activityList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Monitoring</h5>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <p class="mb-0" id="confirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="reportMonitorToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const activeTab = document.querySelector('.report-monitor-page').dataset.activeTab;
    const reportSource = @json($adminMagangReports ?? []);
    const activitySource = @json($adminRecentActivities ?? []);
    const adminStats = @json($adminStats ?? []);
    const rekapAbsensiExportUrl = @json(route('admin.laporan-monitoring.rekap-absensi.export'));
    const rekapKegiatanUrl = @json(route('admin.laporan-monitoring.rekap-kegiatan'));
    const reports = reportSource.map((report) => ({
        id: report.id,
        type: report.jenis || 'Berkala',
        period: report.periode || '-',
        study: report.prodi || '-',
        agency: report.instansi || '-',
        mahasiswa: `${report.nama || '-'}${report.nim ? ` - ${report.nim}` : ''}`,
        status: report.status || 'menunggu',
        author: report.nama || '-',
        date: report.tanggal || '-',
        title: report.judul || '-',
        downloadUrl: report.download_url || '#',
    }));

    const activities = activitySource.map((activity) => [activity.aktivitas, activity.tanggal]);

    const perPage = 5;
    let currentPage = 1;
    let pendingAction = null;
    let pendingId = null;

    const periodFilter = document.getElementById('periodFilter');
    const typeFilter = document.getElementById('typeFilter');
    const studyFilter = document.getElementById('studyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('reportTable');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('reportMonitorToast'), { delay: 3000 });

    if (activeTab === 'magang') typeFilter.value = 'Berkala';
    if (activeTab === 'institusi') typeFilter.value = 'Akhir';
    if (activeTab === 'aktivitas') typeFilter.value = 'Berkala';
    if (activeTab === 'analisis') typeFilter.value = 'Akhir';

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(status) {
        return { disetujui: 'bg-success', review: 'bg-info text-dark', arsip: 'bg-secondary', menunggu: 'bg-warning text-dark', 'perlu revisi': 'bg-danger', 'perlu diperbaiki': 'bg-danger' }[status] || 'bg-secondary';
    }

    function filteredReports() {
        const keyword = searchInput.value.trim().toLowerCase();
        return reports.filter((item) => {
            const matchKeyword = !keyword || [item.title, item.type, item.period, item.study, item.agency, item.status, item.author].join(' ').toLowerCase().includes(keyword);
            const matchPeriod = periodFilter.value === 'semua' || item.period === periodFilter.value;
            const matchType = typeFilter.value === 'semua' || item.type === typeFilter.value;
            const matchStudy = studyFilter.value === 'semua' || item.study === studyFilter.value;
            const matchStatus = statusFilter.value === 'semua' || item.status === statusFilter.value;
            return matchKeyword && matchPeriod && matchType && matchStudy && matchStatus;
        });
    }

    function updateStats() {
        const waitingCount = reports.filter((item) => item.status === 'menunggu').length;
        const revisionCount = reports.filter((item) => ['perlu revisi', 'perlu diperbaiki'].includes(item.status)).length;
        const approvedCount = reports.filter((item) => item.status === 'disetujui').length;
        const partnerCount = new Set(reports.map((item) => item.agency)).size;
        document.getElementById('statActiveInternship').textContent = String(adminStats.active_participants ?? reports.length);
        document.getElementById('statPartners').textContent = String(partnerCount);
        document.getElementById('statStudents').textContent = String(adminStats.total_participants ?? reports.length);
        document.getElementById('statApproved').textContent = String(approvedCount);

        const firstCardLabel = document.querySelector('[data-status-card="aktif"] .text-muted');
        if (firstCardLabel) firstCardLabel.textContent = 'Peserta Aktif';
        const secondCardLabel = document.querySelector('[data-type-card="Institusi"] .text-muted');
        if (secondCardLabel) secondCardLabel.textContent = 'Instansi Mitra';
        const thirdCardLabel = document.querySelector('[data-type-card="Magang"] .text-muted');
        if (thirdCardLabel) thirdCardLabel.textContent = 'Total Laporan';
        const fourthCardLabel = document.querySelector('[data-status-card="disetujui"] .text-muted');
        if (fourthCardLabel) fourthCardLabel.textContent = 'Laporan Disetujui';
    }

    function renderTable() {
        const data = filteredReports();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.type}</td>
                <td>${item.period}</td>
                <td>${item.study}</td>
                <td>${item.agency}</td>
                <td>
                    <div class="fw-semibold">${item.mahasiswa}</div>
                </td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>${item.author}</td>
                <td>${item.date}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                        <a class="btn btn-primary btn-sm" href="${item.downloadUrl}" target="_blank" rel="noopener">Unduh</a>
                        <button class="btn btn-success btn-sm" type="button" data-action="disetujui" data-id="${item.id}">Validasi</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-action="arsip" data-id="${item.id}">Arsip</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('tableSummary').textContent = `${data.length} laporan ditemukan`;
        document.getElementById('tableWrapper').classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyState').classList.toggle('d-none', data.length > 0);
        document.getElementById('pageInfo').textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} laporan` : 'Menampilkan 0 laporan';
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" type="button" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderActivities() {
        document.getElementById('activityList').innerHTML = activities.map((item) => `
            <div class="activity-item">
                <h6 class="mb-1">${item[0]}</h6>
                <p class="text-muted small mb-0">${item[1]}</p>
            </div>
        `).join('');
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('reportMonitorToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const item = reports.find((report) => report.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Judul</dt><dd class="col-sm-8">${item.title}</dd>
                <dt class="col-sm-4">Jenis Laporan</dt><dd class="col-sm-8">${item.type}</dd>
                <dt class="col-sm-4">Periode</dt><dd class="col-sm-8">${item.period}</dd>
                <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${item.study}</dd>
                <dt class="col-sm-4">Instansi</dt><dd class="col-sm-8">${item.agency}</dd>
                <dt class="col-sm-4">Mahasiswa</dt><dd class="col-sm-8">${item.mahasiswa}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">${titleCase(item.status)}</dd>
                <dt class="col-sm-4">Diunggah Oleh</dt><dd class="col-sm-8">${item.author}</dd>
                <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8">${item.date}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function confirmAction(action, id) {
        const item = reports.find((report) => report.id === id);
        pendingAction = action;
        pendingId = id;
        const labels = {
            disetujui: ['Konfirmasi Validasi Laporan', `Validasi laporan "${item.title}"?`],
            arsip: ['Konfirmasi Arsip Laporan', `Arsipkan laporan "${item.title}"?`],
            hapus: ['Konfirmasi Hapus Laporan', `Hapus laporan "${item.title}"?`]
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    function renderAll() {
        updateStats();
        renderTable();
        renderActivities();
    }

    [periodFilter, typeFilter, studyFilter, statusFilter].forEach((input) => input.addEventListener('change', () => {
        currentPage = 1;
        renderAll();
    }));

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderAll();
        }
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderAll();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        periodFilter.value = 'semua';
        typeFilter.value = 'semua';
        studyFilter.value = 'semua';
        statusFilter.value = 'semua';
        searchInput.value = '';
        currentPage = 1;
        renderAll();
    });

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderAll();
        });
    });

    document.querySelectorAll('[data-type-card]').forEach((card) => {
        card.addEventListener('click', () => {
            typeFilter.value = card.dataset.typeCard;
            currentPage = 1;
            renderAll();
        });
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const id = Number(button.dataset.id);
        const action = button.dataset.action;
        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'ekspor') {
            showToast('Laporan berhasil disiapkan untuk ekspor.');
        } else {
            confirmAction(action, id);
        }
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderAll();
    });

    document.getElementById('exportAllButton').addEventListener('click', () => {
        window.location.href = rekapAbsensiExportUrl;
    });
    document.getElementById('analysisButton').addEventListener('click', () => {
        window.location.href = rekapKegiatanUrl;
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        const index = reports.findIndex((report) => report.id === pendingId);
        if (index < 0 || !pendingAction) return;
        const item = reports[index];
        let message = '';
        if (pendingAction === 'hapus') {
            reports.splice(index, 1);
            message = 'Laporan berhasil dihapus.';
        } else {
            item.status = pendingAction;
            message = pendingAction === 'disetujui' ? 'Laporan berhasil divalidasi.' : 'Laporan berhasil diarsipkan.';
        }
        pendingAction = null;
        pendingId = null;
        confirmModal.hide();
        renderAll();
        showToast(message);
    });

    renderAll();
</script>
@endpush
