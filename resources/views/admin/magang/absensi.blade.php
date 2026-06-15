@extends('admin.layout.admin')

@section('title', 'Data Absensi')

@push('styles')
<style>
    .attendance-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .attendance-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .attendance-page .stat-card,
    .attendance-page .filter-card,
    .attendance-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .attendance-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .attendance-page .stat-card:hover,
    .attendance-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .attendance-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .attendance-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .attendance-page .table tbody tr {
        transition: 0.15s ease;
    }

    .attendance-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .attendance-page .action-group {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        white-space: nowrap;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .attendance-page .action-group .btn {
        flex: 0 0 auto;
    }

    .attendance-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .attendance-page .pagination .page-link {
        color: #0b5f86;
    }

    .attendance-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid attendance-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Absensi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Absensi</h2>
            <p class="text-muted mb-0">
                Monitoring dan pengelolaan kehadiran peserta magang.
            </p>
        </div>
        <button class="btn btn-outline-primary" type="button" id="exportButton">
            <i class="bi bi-download"></i>
            Ekspor Absensi
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Absensi</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-calendar-week-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Hadir Hari Ini</p>
                        <h3 class="mb-0" id="statHadir">0</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="tidak hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tidak Hadir</p>
                        <h3 class="mb-0" id="statTidakHadir">0</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="izin">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Izin</p>
                        <h3 class="mb-0" id="statIzin">0</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-envelope-check"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="sakit">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Sakit</p>
                        <h3 class="mb-0" id="statSakit">0</h3>
                    </div>
                    <span class="stat-icon bg-dark"><i class="bi bi-emoji-frown"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="terlambat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Terlambat</p>
                        <h3 class="mb-0" id="statTerlambat">0</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-alarm-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="attendanceFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Peserta Magang</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari Peserta Magang">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="dateFilter" class="form-label">Tanggal</label>
                        <select class="form-select" id="dateFilter">
                            <option value="semua">Semua Tanggal</option>
                            <option value="hari ini">Hari Ini</option>
                            <option value="minggu ini">Minggu Ini</option>
                            <option value="bulan ini">Bulan Ini</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="tidak hadir">Tidak Hadir</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="valid">Valid</option>
                    </select>
                    </div>

                    <div class="col-md-3">
                        <label for="periodFilter" class="form-label">Periode Magang</label>
                        <select class="form-select" id="periodFilter">
                            <option value="semua">Semua Periode</option>
                            <option value="Batch 1 2026">Batch 1 2026</option>
                            <option value="Batch 2 2026">Batch 2 2026</option>
                            <option value="MBKM 2026">MBKM 2026</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-primary w-100" type="button" id="searchButton" title="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" type="button" id="resetFilter" title="Reset">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Data Absensi</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Instansi/Perusahaan</th>
                            <th>Tanggal Absensi</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status Kehadiran</th>
                            <th>Keterangan</th>
                            <th width="280">Aksi</th>
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

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="editCheckIn">Jam Masuk</label>
                        <input class="form-control" id="editCheckIn" type="time">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="editCheckOut">Jam Keluar</label>
                        <input class="form-control" id="editCheckOut" type="time">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="editNote">Keterangan</label>
                        <textarea class="form-control" id="editNote" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveEdit">Simpan</button>
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
    const attendances = @json($adminAttendances ?? []);

    const perPage = 10;
    let currentPage = 1;
    let pendingAction = null;
    let editingId = null;

    const searchInput = document.getElementById('searchInput');
    const dateFilter = document.getElementById('dateFilter');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const tableBody = document.getElementById('attendanceTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('attendanceToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            hadir: 'bg-success',
            valid: 'bg-success',
            'tidak hadir': 'bg-danger',
            izin: 'bg-info text-dark',
            sakit: 'bg-dark',
            terlambat: 'bg-warning text-dark'
        }[value];
    }

    function filteredAttendances() {
        const keyword = searchInput.value.trim().toLowerCase();
        const date = dateFilter.value;
        const status = statusFilter.value;
        const period = periodFilter.value;

        return attendances.filter((attendance) => {
            const matchKeyword = !keyword || [attendance.nama, attendance.instansi, attendance.status, attendance.keterangan].join(' ').toLowerCase().includes(keyword);
            const matchDate = date === 'semua' || attendance.periodeTanggal === date;
            const matchStatus = status === 'semua' || attendance.status === status;
            const matchPeriod = period === 'semua' || attendance.periode === period;

            return matchKeyword && matchDate && matchStatus && matchPeriod;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = attendances.length;
        document.getElementById('statHadir').textContent = attendances.filter((item) => item.status === 'hadir' && item.periodeTanggal === 'hari ini').length;
        document.getElementById('statTidakHadir').textContent = attendances.filter((item) => item.status === 'tidak hadir').length;
        document.getElementById('statIzin').textContent = attendances.filter((item) => item.status === 'izin').length;
        document.getElementById('statSakit').textContent = attendances.filter((item) => item.status === 'sakit').length;
        document.getElementById('statTerlambat').textContent = attendances.filter((item) => item.status === 'terlambat').length;
    }

    function renderTable() {
        const data = filteredAttendances();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((attendance, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${attendance.nama}</td>
                <td>${attendance.instansi}</td>
                <td>${attendance.tanggal}</td>
                <td>${attendance.masuk}</td>
                <td>${attendance.keluar}</td>
                <td><span class="badge ${statusClass(attendance.status)}">${titleCase(attendance.status)}</span></td>
                <td>${attendance.keterangan}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${attendance.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${attendance.id}">Edit</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${attendance.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} absensi ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data absensi`
            : 'Menampilkan 0 data absensi';

        renderPagination(totalPages);
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

    function showToast(message, success = true) {
        const toastElement = document.getElementById('attendanceToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const attendance = attendances.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Peserta</dt>
                <dd class="col-sm-8">${attendance.nama}</dd>
                <dt class="col-sm-4">Instansi</dt>
                <dd class="col-sm-8">${attendance.instansi}</dd>
                <dt class="col-sm-4">Tanggal</dt>
                <dd class="col-sm-8">${attendance.tanggal}</dd>
                <dt class="col-sm-4">Jam Masuk</dt>
                <dd class="col-sm-8">${attendance.masuk}</dd>
                <dt class="col-sm-4">Jam Keluar</dt>
                <dd class="col-sm-8">${attendance.keluar}</dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">${titleCase(attendance.status)}</dd>
                <dt class="col-sm-4">Keterangan</dt>
                <dd class="col-sm-8">${attendance.keterangan}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openEdit(id) {
        editingId = id;
        const attendance = attendances.find((item) => item.id === id);
        document.getElementById('editCheckIn').value = attendance.masuk === '-' ? '' : attendance.masuk;
        document.getElementById('editCheckOut').value = attendance.keluar === '-' ? '' : attendance.keluar;
        document.getElementById('editNote').value = attendance.keterangan;
        editModal.show();
    }

    function showConfirm(id, action) {
        const attendance = attendances.find((item) => item.id === id);
        pendingAction = { id, action };
        document.getElementById('confirmTitle').textContent = 'Konfirmasi Hapus Absensi';
        document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin menghapus data absensi ${attendance.nama}?`;
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    [dateFilter, statusFilter, periodFilter].forEach((input) => {
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

    document.getElementById('searchButton').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        dateFilter.value = 'semua';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Data absensi berhasil disiapkan untuk ekspor.');
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'detail') {
            showDetail(id);
        } else if (button.dataset.action === 'edit') {
            openEdit(id);
        } else {
            showConfirm(id, button.dataset.action);
        }
    });

    document.getElementById('saveEdit').addEventListener('click', () => {
        const attendance = attendances.find((item) => item.id === editingId);
        attendance.masuk = document.getElementById('editCheckIn').value || '-';
        attendance.keluar = document.getElementById('editCheckOut').value || '-';
        attendance.keterangan = document.getElementById('editNote').value || '-';
        editModal.hide();
        renderTable();
        showToast(`Data absensi ${attendance.nama} berhasil diperbarui.`);
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) {
            return;
        }

        const index = attendances.findIndex((item) => item.id === pendingAction.id);
        const attendance = attendances[index];

        if (pendingAction.action === 'hapus') {
            attendances.splice(index, 1);
            showToast(`Data absensi ${attendance.nama} berhasil dihapus.`);
        }

        confirmModal.hide();
        updateStats();
        renderTable();
        pendingAction = null;
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    updateStats();
    renderTable();
</script>
@endpush
