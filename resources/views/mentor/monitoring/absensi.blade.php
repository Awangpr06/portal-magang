@extends('mentor.layout.mentor')

@section('title', 'Absensi Peserta')
@section('page-title', 'Absensi Peserta')

@push('styles')
<style>
    .attendance-page .attendance-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .attendance-page .stat-card,
    .attendance-page .filter-card,
    .attendance-page .table-card,
    .attendance-page .panel-card { border:0; border-radius:8px; }
    .attendance-page .stat-card { cursor:pointer; transition:.2s ease; }
    .attendance-page .stat-card:hover,
    .attendance-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .attendance-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .attendance-page .progress { height:8px; background:#e8eef2; }
    .attendance-page .progress-bar { background:#2a8fbd; }
    .attendance-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:220px; }
    .attendance-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .attendance-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .attendance-page .pagination .page-link { color:#2a8fbd; }
    .attendance-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid attendance-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.monitoring') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Absensi</li>
        </ol>
    </nav>

    <section class="attendance-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Absensi Peserta</h3>
                <p class="mb-0">Pantau tingkat kehadiran, keterlambatan, ketidakhadiran, dan konsistensi peserta selama pelaksanaan magang.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="attendanceBadge">Real-time</span>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-calendar-check-fill"></i>
        <div>3 peserta terlambat hari ini, 1 peserta tidak hadir, dan 5 data absensi baru perlu dipantau mentor.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-attendance-card="tinggi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Kehadiran</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-graph-up"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-today-card="hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Hadir Hari Ini</p><h3 class="mb-0" id="statPresent">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar2-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="tidak hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Tidak Hadir</p><h3 class="mb-0" id="statAbsent">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-calendar-x"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Izin</p><h3 class="mb-0" id="statPermission">0</h3></div>
                    <span class="stat-icon bg-warning text-dark"><i class="bi bi-journal-x"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sakit</p><h3 class="mb-0" id="statSick">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-clipboard2-pulse"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="terlambat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kedisiplinan</p><h3 class="mb-0" id="statDiscipline">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-clock-history"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, penempatan, status">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Minggu Ini">Minggu Ini</option>
                        <option value="Bulan Ini">Bulan Ini</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Kehadiran</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="tidak hadir">Tidak Hadir</option>
                        <option value="dipantau">Dipantau</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="locationFilter">Penempatan</label>
                    <select class="form-select" id="locationFilter">
                        <option value="semua">Semua Penempatan</option>
                        @foreach(($attendancePlacementOptions ?? []) as $placementOption)
                            <option value="{{ $placementOption }}">{{ $placementOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="attendanceFilter">Persentase</label>
                    <select class="form-select" id="attendanceFilter">
                        <option value="semua">Semua</option>
                        <option value="tinggi">90% ke atas</option>
                        <option value="sedang">80% - 89%</option>
                        <option value="rendah">Di bawah 80%</option>
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
                        <h5 class="mb-0">Rekapitulasi Absensi Peserta</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data absensi</small>
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
                                    <th>Penempatan</th>
                                    <th>Jumlah Hadir</th>
                                    <th>Terlambat</th>
                                    <th>Tidak Hadir</th>
                                    <th>Izin</th>
                                    <th>Sakit</th>
                                    <th>Persentase Kehadiran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data absensi sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination absensi"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Absensi</h5>
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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Data absensi diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const attendances = @json($attendanceRows ?? []);

    const tableBody = document.getElementById('attendanceTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const periodFilter = document.getElementById('periodFilter');
    const statusFilter = document.getElementById('statusFilter');
    const locationFilter = document.getElementById('locationFilter');
    const attendanceFilter = document.getElementById('attendanceFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2600 });
    let currentPage = 1;
    let activeStatus = 'semua';

    const statusClass = (status) => ({ hadir:'success', terlambat:'warning text-dark', 'tidak hadir':'danger', dipantau:'info text-dark' }[status] || 'secondary');
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

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        return attendances.filter((item) => {
            const keywordMatch = !keyword || `${item.nama} ${item.penempatan ?? item.lokasi ?? ''} ${item.status} ${item.catatan}`.toLowerCase().includes(keyword);
            const periodMatch = periodFilter.value === 'semua' || item.periode === periodFilter.value;
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const locationMatch = locationFilter.value === 'semua' || (item.penempatan ?? item.lokasi) === locationFilter.value;
            const attendanceMatch = attendanceFilter.value === 'semua' || attendanceGroup(item.persen) === attendanceFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && periodMatch && statusMatch && locationMatch && attendanceMatch && cardMatch;
        });
    }

    function renderStats() {
        const avg = attendances.length ? Math.round(attendances.reduce((sum, item) => sum + Number(item.persen || 0), 0) / attendances.length) : 0;
        const totalPresence = attendances.reduce((sum, item) => sum + Number(item.hadir || 0) + Number(item.terlambat || 0) + Number(item.absen || 0), 0);
        const discipline = totalPresence ? Math.round(100 - (attendances.reduce((sum, item) => sum + Number(item.terlambat || 0), 0) / totalPresence * 100)) : 0;
        document.getElementById('statTotal').textContent = attendances.length;
        document.getElementById('statAverage').textContent = `${avg}%`;
        document.getElementById('statPresent').textContent = attendances.filter((item) => item.status === 'hadir').length;
        document.getElementById('statAbsent').textContent = attendances.filter((item) => item.status === 'tidak hadir').length;
        document.getElementById('statPermission').textContent = attendances.reduce((sum, item) => sum + Number(item.izin || 0), 0);
        document.getElementById('statSick').textContent = attendances.reduce((sum, item) => sum + Number(item.sakit || 0), 0);
        document.getElementById('statDiscipline').textContent = `${discipline}%`;
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
                <td>${item.penempatan ?? item.lokasi ?? '-'}</td>
                <td>${item.hadir}</td>
                <td>${item.terlambat}</td>
                <td>${item.absen}</td>
                <td>${item.izin ?? 0}</td>
                <td>${item.sakit ?? 0}</td>
                <td>
                    <div class="d-flex justify-content-between small"><span>${item.persen}%</span><span>${attendanceGroup(item.persen)}</span></div>
                    <div class="progress"><div class="progress-bar ${item.persen < 80 ? 'bg-danger' : ''}" style="width:${item.persen}%"></div></div>
                </td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail absensi" data-id="${item.id}">Detail Absensi</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} data absensi ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
    }

    function openDetail(item, title = 'Detail Absensi') {
        document.getElementById('detailModalLabel').textContent = title;
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Peserta</small><h5>${item.nama}</h5><div>${item.penempatan ?? item.lokasi ?? '-'}</div></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Status</small><div class="mb-2"><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></div><div>${item.periode}</div></div></div>
                <div class="col-md-3"><div class="border rounded p-3"><small class="text-muted">Hadir</small><h5>${item.hadir}</h5></div></div>
                <div class="col-md-3"><div class="border rounded p-3"><small class="text-muted">Terlambat</small><h5>${item.terlambat}</h5></div></div>
                <div class="col-md-3"><div class="border rounded p-3"><small class="text-muted">Tidak Hadir</small><h5>${item.absen}</h5></div></div>
                <div class="col-md-3"><div class="border rounded p-3"><small class="text-muted">Izin</small><h5>${item.izin ?? 0}</h5></div></div>
                <div class="col-md-3"><div class="border rounded p-3"><small class="text-muted">Sakit</small><h5>${item.sakit ?? 0}</h5></div></div>
                <div class="col-md-3"><div class="border rounded p-3"><small class="text-muted">Persentase</small><h5>${item.persen}%</h5></div></div>
                <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan</small><p class="mb-0">${item.catatan}</p></div></div>
            </div>
        `;
        detailModal.show();
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
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter absensi berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        periodFilter.value = 'semua';
        statusFilter.value = 'semua';
        locationFilter.value = 'semua';
        attendanceFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter absensi berhasil direset.', 'info');
    });
    [searchInput, periodFilter, statusFilter, locationFilter, attendanceFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = attendances.find((attendance) => attendance.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail absensi') {
            openDetail(item, 'Detail Absensi');
            return;
        }
        showToast(`Aksi ${action} tidak tersedia pada halaman ini.`, 'info');
    });
    renderTable();
    setTimeout(() => showToast('Notifikasi absensi: 5 data kehadiran baru masuk.', 'warning'), 700);
});
</script>
@endpush
