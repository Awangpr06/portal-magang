@extends('admin.layout.admin')

@section('title', 'Manajemen Magang')

@push('styles')
<style>
    .internship-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .internship-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .internship-page .stat-card,
    .internship-page .filter-card,
    .internship-page .shortcut-card,
    .internship-page .table-card,
    .internship-page .info-card {
        border: 0;
        border-radius: 8px;
    }

    .internship-page .stat-card,
    .internship-page .shortcut-card {
        transition: 0.2s ease;
    }

    .internship-page .stat-card:hover,
    .internship-page .shortcut-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .internship-page .stat-icon,
    .internship-page .shortcut-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .internship-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .internship-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .internship-page .empty-state {
        min-height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .internship-page .pagination .page-link {
        color: #0b5f86;
    }

    .internship-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid internship-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manajemen Magang</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Manajemen Magang</h2>
            <p class="text-muted mb-0">
                Pusat pengelolaan absensi, kegiatan, laporan, penempatan, dan periode magang.
            </p>
        </div>
        <button class="btn btn-outline-primary" type="button" id="exportButton">
            <i class="bi bi-download"></i>
            Ekspor Ringkasan
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Peserta Magang</p>
                        <h3 class="mb-0">{{ $magangStats['total_participants'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Hadir Hari Ini</p>
                        <h3 class="mb-0">{{ $magangStats['attendance_today'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-calendar-check-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Laporan Masuk</p>
                        <h3 class="mb-0">{{ $magangStats['reports_count'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-file-earmark-text-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Penempatan Aktif</p>
                        <h3 class="mb-0">{{ $magangStats['active_placements'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-geo-alt-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Periode Berjalan</p>
                        <h3 class="mb-0">{{ $magangStats['running_periods'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-calendar-range-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="internshipFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="searchInput" class="form-label">Pencarian Global</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari peserta, laporan, absensi, penempatan, atau periode">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="categoryFilter" class="form-label">Kategori</label>
                        <select class="form-select" id="categoryFilter">
                            <option value="semua">Semua Kategori</option>
                            <option value="Absensi">Absensi</option>
                            <option value="Kegiatan">Kegiatan</option>
                            <option value="Laporan">Laporan</option>
                            <option value="Penempatan">Penempatan</option>
                            <option value="Periode">Periode</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="valid">Valid</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="perlu tindak lanjut">Perlu Tindak Lanjut</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="periodFilter" class="form-label">Periode</label>
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

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="row g-3">
                <div class="col-md-6 col-lg">
                    <div class="card shortcut-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="shortcut-icon bg-success mb-3"><i class="bi bi-calendar-check"></i></span>
                            <h6 class="fw-bold">Absensi</h6>
                            <p class="text-muted small">Pantau kehadiran peserta magang.</p>
                            <button class="btn btn-success btn-sm mt-auto access-menu" data-target="{{ route('admin.magang.absensi') }}" data-title="Absensi" type="button">Akses Submenu</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg">
                    <div class="card shortcut-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="shortcut-icon bg-primary mb-3"><i class="bi bi-list-task"></i></span>
                            <h6 class="fw-bold">Kegiatan Magang</h6>
                            <p class="text-muted small">Kelola aktivitas harian peserta.</p>
                            <button class="btn btn-primary btn-sm mt-auto access-menu" data-target="{{ route('admin.magang.kegiatan') }}" data-title="Kegiatan Magang" type="button">Akses Submenu</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg">
                    <div class="card shortcut-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="shortcut-icon bg-info mb-3"><i class="bi bi-file-earmark-text"></i></span>
                            <h6 class="fw-bold">Laporan Berkala</h6>
                            <p class="text-muted small">Validasi laporan dan dokumen akhir.</p>
                            <button class="btn btn-info btn-sm mt-auto access-menu" data-target="{{ route('admin.magang.laporan') }}" data-title="Laporan Berkala" type="button">Akses Submenu</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg">
                    <div class="card shortcut-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="shortcut-icon bg-danger mb-3"><i class="bi bi-file-earmark-ruled"></i></span>
                            <h6 class="fw-bold">Laporan Akhir</h6>
                            <p class="text-muted small">Tinjau dan validasi laporan akhir peserta.</p>
                            <button class="btn btn-danger btn-sm mt-auto access-menu" data-target="{{ route('admin.magang.laporan-akhir') }}" data-title="Laporan Akhir" type="button">Akses Submenu</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg">
                    <div class="card shortcut-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="shortcut-icon bg-warning mb-3"><i class="bi bi-geo-alt"></i></span>
                            <h6 class="fw-bold">Penempatan</h6>
                            <p class="text-muted small">Atur instansi dan mentor peserta.</p>
                            <button class="btn btn-warning btn-sm mt-auto access-menu" data-target="{{ route('admin.magang.penempatan') }}" data-title="Penempatan" type="button">Akses Submenu</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg">
                    <div class="card shortcut-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="shortcut-icon bg-secondary mb-3"><i class="bi bi-calendar-range"></i></span>
                            <h6 class="fw-bold">Periode Magang</h6>
                            <p class="text-muted small">Kelola batch dan rentang periode.</p>
                            <button class="btn btn-secondary btn-sm mt-auto access-menu" data-target="{{ route('admin.magang.periode') }}" data-title="Periode Magang" type="button">Akses Submenu</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card info-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Informasi Sistem</h5>
                    <div class="alert alert-info mb-3">
                        12 laporan baru membutuhkan validasi admin.
                    </div>
                    <div class="alert alert-warning mb-0">
                        5 peserta belum memiliki penempatan aktif.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Preview Aktivitas Magang</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peserta</th>
                            <th>Kategori Data</th>
                            <th>Periode</th>
                            <th>Tanggal Aktivitas</th>
                            <th width="180">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody id="previewTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data magang tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination preview magang">
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
                <h5 class="modal-title">Detail Aktivitas Magang</h5>
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
                <h5 class="modal-title">Konfirmasi Tindakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="confirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="internshipToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const previewData = @json($adminMagangPreview ?? []);

    const perPage = 10;
    let currentPage = 1;
    let targetUrl = '';
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const tableBody = document.getElementById('previewTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('internshipToast'), { delay: 3000 });

    function titleCase(value) {
        return String(value || '')
            .split(' ')
            .filter(Boolean)
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function filteredPreview() {
        const keyword = searchInput.value.trim().toLowerCase();
        const category = categoryFilter.value;
        const status = statusFilter.value;
        const period = periodFilter.value;

        return previewData.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.kategori, item.periode, item.detail, item.status].join(' ').toLowerCase().includes(keyword);
            const matchCategory = category === 'semua' || item.kategori === category;
            const matchStatus = status === 'semua' || item.status === status;
            const matchPeriod = period === 'semua' || item.periode === period;

            return matchKeyword && matchCategory && matchStatus && matchPeriod;
        });
    }

    function renderTable() {
        const data = filteredPreview();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${item.nama}</td>
                <td><span class="badge bg-primary">${item.kategori}</span></td>
                <td>${item.periode}</td>
                <td>${item.tanggal}</td>
                <td>
                    <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} data ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data`
            : 'Menampilkan 0 data';

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
        const toastElement = document.getElementById('internshipToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-warning', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const item = previewData.find((data) => data.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Peserta</dt>
                <dd class="col-sm-8">${item.nama}</dd>
                <dt class="col-sm-4">Kategori</dt>
                <dd class="col-sm-8">${item.kategori}</dd>
                <dt class="col-sm-4">Periode</dt>
                <dd class="col-sm-8">${item.periode}</dd>
                <dt class="col-sm-4">Tanggal</dt>
                <dd class="col-sm-8">${item.tanggal}</dd>
                <dt class="col-sm-4">Detail</dt>
                <dd class="col-sm-8">${item.detail}</dd>
            </dl>
        `;
        detailModal.show();
    }

    [categoryFilter, statusFilter, periodFilter].forEach((input) => {
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
        categoryFilter.value = 'semua';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        renderTable();
        showToast('Filter berhasil direset.');
    });

    document.querySelectorAll('.access-menu').forEach((button) => {
        button.addEventListener('click', () => {
            targetUrl = button.dataset.target;
            document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin membuka submenu ${button.dataset.title}?`;
            confirmModal.show();
        });
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'detail') {
            showDetail(id);
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (targetUrl) {
            window.location.href = targetUrl;
        }
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Ringkasan manajemen magang berhasil disiapkan untuk ekspor.');
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    renderTable();
</script>
@endpush
