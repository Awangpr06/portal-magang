@extends('admin.layout.admin')

@section('title', 'Periode Magang')

@push('styles')
<style>
    .period-page .page-title { font-weight: 700; color: #163342; }
    .period-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .period-page .stat-card,
    .period-page .filter-card,
    .period-page .table-card { border: 0; border-radius: 8px; }
    .period-page .stat-card { cursor: pointer; transition: .2s ease; }
    .period-page .stat-card:hover,
    .period-page .stat-card.active { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .period-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .period-page .table { font-size: 14px; }
    .period-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .period-page .table tbody tr { transition: .15s ease; }
    .period-page .table tbody tr:hover { background: #f8fbfd; }
    .period-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 255px; }
    .period-page .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .period-page .pagination .page-link { color: #0b5f86; }
    .period-page .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid period-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Periode Magang</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Periode Magang</h2>
            <p class="text-muted mb-0">Kelola jadwal, durasi, tahun akademik, dan status periode program magang.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton">
                <i class="bi bi-download"></i> Ekspor Periode
            </button>
            <button class="btn btn-primary" type="button" id="addPeriodButton">
                <i class="bi bi-plus-lg"></i> Tambah Periode
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Periode</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-calendar3"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktif</p><h3 class="mb-0" id="statAktif">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="akan dimulai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Akan Dimulai</p><h3 class="mb-0" id="statAkan">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="berlangsung">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Berlangsung</p><h3 class="mb-0" id="statBerlangsung">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-play-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Selesai</p><h3 class="mb-0" id="statSelesai">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-flag"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="periodFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Periode</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari Periode">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="yearFilter" class="form-label">Tahun Akademik</label>
                        <select class="form-select" id="yearFilter">
                            <option value="semua">Semua Tahun</option>
                            <option value="2025/2026">2025/2026</option>
                            <option value="2026/2027">2026/2027</option>
                            <option value="2027/2028">2027/2028</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="studyFilter" class="form-label">Program Studi</label>
                        <select class="form-select" id="studyFilter">
                            <option value="semua">Semua Prodi</option>
                            <option value="Informatika">Informatika</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Administrasi Publik">Administrasi Publik</option>
                            <option value="Akuntansi">Akuntansi</option>
                            <option value="Semua Program Studi">Semua Program Studi</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="akan dimulai">Akan Dimulai</option>
                            <option value="berlangsung">Berlangsung</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button class="btn btn-primary w-50" type="button" id="searchButton" title="Cari"><i class="bi bi-search"></i></button>
                        <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Data Periode Magang</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Periode</th>
                            <th>Tahun Akademik</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Durasi</th>
                            <th>Program Studi</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="periodTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data periode tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination periode magang"><ul class="pagination mb-0" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Detail Periode Magang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body" id="detailContent"></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
    </div></div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="formTitle">Tambah Periode</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label" for="formName">Nama Periode</label><input class="form-control" id="formName" placeholder="Nama periode"></div>
                <div class="col-md-6"><label class="form-label" for="formYear">Tahun Akademik</label><input class="form-control" id="formYear" placeholder="2026/2027"></div>
                <div class="col-md-6"><label class="form-label" for="formStart">Tanggal Mulai</label><input class="form-control" id="formStart" type="date"></div>
                <div class="col-md-6"><label class="form-label" for="formEnd">Tanggal Selesai</label><input class="form-control" id="formEnd" type="date"></div>
                <div class="col-12"><label class="form-label" for="formNote">Keterangan</label><textarea class="form-control" id="formNote" rows="3" placeholder="Keterangan periode"></textarea></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="saveForm">Simpan</button>
        </div>
    </div></div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
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
    </div></div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="periodToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const periods = [
        { id: 1, nama: 'Batch 1 2026', tahun: '2025/2026', mulai: '01 Januari 2026', selesai: '30 Juni 2026', durasi: '6 Bulan', prodi: 'Semua Program Studi', status: 'berlangsung', keterangan: 'Periode utama semester genap' },
        { id: 2, nama: 'Batch 2 2026', tahun: '2026/2027', mulai: '01 Juli 2026', selesai: '31 Desember 2026', durasi: '6 Bulan', prodi: 'Semua Program Studi', status: 'akan dimulai', keterangan: 'Periode semester gasal' },
        { id: 3, nama: 'MBKM Informatika 2026', tahun: '2025/2026', mulai: '15 Februari 2026', selesai: '15 Agustus 2026', durasi: '6 Bulan', prodi: 'Informatika', status: 'aktif', keterangan: 'Program MBKM khusus Informatika' },
        { id: 4, nama: 'Magang Manajemen 2026', tahun: '2025/2026', mulai: '01 Maret 2026', selesai: '30 Juni 2026', durasi: '4 Bulan', prodi: 'Manajemen', status: 'berlangsung', keterangan: 'Program magang prodi Manajemen' },
        { id: 5, nama: 'Administrasi Publik 2025', tahun: '2025/2026', mulai: '01 September 2025', selesai: '31 Desember 2025', durasi: '4 Bulan', prodi: 'Administrasi Publik', status: 'selesai', keterangan: 'Periode telah selesai' },
        { id: 6, nama: 'Akuntansi Batch Khusus', tahun: '2026/2027', mulai: '10 Agustus 2026', selesai: '10 Desember 2026', durasi: '4 Bulan', prodi: 'Akuntansi', status: 'akan dimulai', keterangan: 'Batch khusus audit dan keuangan' },
        { id: 7, nama: 'MBKM 2027', tahun: '2027/2028', mulai: '01 Februari 2027', selesai: '31 Juli 2027', durasi: '6 Bulan', prodi: 'Semua Program Studi', status: 'aktif', keterangan: 'Periode perencanaan MBKM' },
        { id: 8, nama: 'Informatika Industri 2025', tahun: '2025/2026', mulai: '01 Agustus 2025', selesai: '31 Januari 2026', durasi: '6 Bulan', prodi: 'Informatika', status: 'selesai', keterangan: 'Kerja sama industri teknologi' },
        { id: 9, nama: 'Manajemen Batch Mitra', tahun: '2026/2027', mulai: '01 September 2026', selesai: '31 Januari 2027', durasi: '5 Bulan', prodi: 'Manajemen', status: 'akan dimulai', keterangan: 'Batch bersama instansi mitra' },
        { id: 10, nama: 'Akuntansi Semester Genap', tahun: '2025/2026', mulai: '03 Februari 2026', selesai: '03 Juni 2026', durasi: '4 Bulan', prodi: 'Akuntansi', status: 'berlangsung', keterangan: 'Magang semester genap' },
        { id: 11, nama: 'Administrasi Publik MBKM', tahun: '2026/2027', mulai: '15 Juli 2026', selesai: '15 November 2026', durasi: '4 Bulan', prodi: 'Administrasi Publik', status: 'aktif', keterangan: 'Program MBKM administrasi' }
    ];

    const perPage = 10;
    let currentPage = 1;
    let pendingAction = null;
    let editingId = null;

    const searchInput = document.getElementById('searchInput');
    const yearFilter = document.getElementById('yearFilter');
    const studyFilter = document.getElementById('studyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('periodTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('periodToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return { aktif: 'bg-success', 'akan dimulai': 'bg-info text-dark', berlangsung: 'bg-warning text-dark', selesai: 'bg-secondary' }[value];
    }

    function filteredPeriods() {
        const keyword = searchInput.value.trim().toLowerCase();
        const year = yearFilter.value;
        const study = studyFilter.value;
        const status = statusFilter.value;

        return periods.filter((period) => {
            const matchKeyword = !keyword || [period.nama, period.tahun, period.prodi, period.status, period.keterangan].join(' ').toLowerCase().includes(keyword);
            const matchYear = year === 'semua' || period.tahun === year;
            const matchStudy = study === 'semua' || period.prodi === study;
            const matchStatus = status === 'semua' || period.status === status;
            return matchKeyword && matchYear && matchStudy && matchStatus;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = periods.length;
        document.getElementById('statAktif').textContent = periods.filter((item) => item.status === 'aktif').length;
        document.getElementById('statAkan').textContent = periods.filter((item) => item.status === 'akan dimulai').length;
        document.getElementById('statBerlangsung').textContent = periods.filter((item) => item.status === 'berlangsung').length;
        document.getElementById('statSelesai').textContent = periods.filter((item) => item.status === 'selesai').length;
    }

    function renderTable() {
        const data = filteredPeriods();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((period, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${period.nama}</td>
                <td>${period.tahun}</td>
                <td>${period.mulai}</td>
                <td>${period.selesai}</td>
                <td>${period.durasi}</td>
                <td>${period.prodi}</td>
                <td><span class="badge ${statusClass(period.status)}">${titleCase(period.status)}</span></td>
                <td>${period.keterangan}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${period.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${period.id}">Edit</button>
                        <button class="btn btn-success btn-sm" type="button" data-action="aktif" data-id="${period.id}">Aktifkan</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-action="selesai" data-id="${period.id}">Selesai</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${period.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} periode ditemukan`;
        pageInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} periode` : 'Menampilkan 0 periode';
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

    function showToast(message, success = true) {
        const toastElement = document.getElementById('periodToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const period = periods.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Periode</dt><dd class="col-sm-8">${period.nama}</dd>
                <dt class="col-sm-4">Tahun Akademik</dt><dd class="col-sm-8">${period.tahun}</dd>
                <dt class="col-sm-4">Tanggal Mulai</dt><dd class="col-sm-8">${period.mulai}</dd>
                <dt class="col-sm-4">Tanggal Selesai</dt><dd class="col-sm-8">${period.selesai}</dd>
                <dt class="col-sm-4">Durasi</dt><dd class="col-sm-8">${period.durasi}</dd>
                <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${period.prodi}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">${titleCase(period.status)}</dd>
                <dt class="col-sm-4">Keterangan</dt><dd class="col-sm-8">${period.keterangan}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const period = id ? periods.find((item) => item.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Periode' : 'Tambah Periode';
        document.getElementById('formName').value = period?.nama || '';
        document.getElementById('formYear').value = period?.tahun || '';
        document.getElementById('formStart').value = '';
        document.getElementById('formEnd').value = '';
        document.getElementById('formNote').value = period?.keterangan || '';
        formModal.show();
    }

    function showConfirm(id, action) {
        const period = periods.find((item) => item.id === id);
        pendingAction = { id, action };
        const labels = {
            aktif: ['Konfirmasi Aktifkan Periode', `Aktifkan periode "${period.nama}"?`],
            selesai: ['Konfirmasi Selesai Periode', `Tandai periode "${period.nama}" sebagai selesai?`],
            hapus: ['Konfirmasi Hapus Periode', `Hapus periode "${period.nama}"?`]
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
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

    [yearFilter, studyFilter, statusFilter].forEach((input) => {
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
        yearFilter.value = 'semua';
        studyFilter.value = 'semua';
        statusFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data periode berhasil disiapkan untuk ekspor.'));
    document.getElementById('addPeriodButton').addEventListener('click', () => openForm());

    document.getElementById('saveForm').addEventListener('click', () => {
        const name = document.getElementById('formName').value.trim();
        const year = document.getElementById('formYear').value.trim();
        const note = document.getElementById('formNote').value.trim();

        if (!name || !year) {
            showToast('Nama periode dan tahun akademik wajib diisi.', false);
            return;
        }

        if (editingId) {
            const period = periods.find((item) => item.id === editingId);
            period.nama = name;
            period.tahun = year;
            period.keterangan = note || '-';
            showToast(`Periode ${period.nama} berhasil diperbarui.`);
        } else {
            periods.unshift({
                id: Date.now(),
                nama: name,
                tahun: year,
                mulai: '24 Mei 2026',
                selesai: '24 November 2026',
                durasi: '6 Bulan',
                prodi: 'Semua Program Studi',
                status: 'akan dimulai',
                keterangan: note || 'Periode baru'
            });
            showToast(`Periode ${name} berhasil ditambahkan.`);
        }

        formModal.hide();
        updateStats();
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;

        const id = Number(button.dataset.id);
        const action = button.dataset.action;

        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'edit') {
            openForm(id);
        } else {
            showConfirm(id, action);
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;

        const index = periods.findIndex((item) => item.id === pendingAction.id);
        const period = periods[index];
        let message = '';

        if (pendingAction.action === 'hapus') {
            periods.splice(index, 1);
            message = `Periode ${period.nama} berhasil dihapus.`;
        } else {
            period.status = pendingAction.action;
            message = `Status periode ${period.nama} berhasil diperbarui menjadi ${titleCase(pendingAction.action)}.`;
        }

        confirmModal.hide();
        updateStats();
        renderTable();
        showToast(message);
        pendingAction = null;
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    updateStats();
    renderTable();
</script>
@endpush
