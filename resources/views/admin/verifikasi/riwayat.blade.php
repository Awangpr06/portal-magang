@extends('admin.layout.admin')

@section('title', 'Riwayat Verifikasi')

@push('styles')
<style>
    .history-verification-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .history-verification-page .stat-card,
    .history-verification-page .filter-card,
    .history-verification-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .history-verification-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .history-verification-page .stat-card:hover,
    .history-verification-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .history-verification-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .history-verification-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .history-verification-page .table tbody tr {
        transition: 0.15s ease;
    }

    .history-verification-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .history-verification-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .history-verification-page .pagination .page-link {
        color: #0b5f86;
    }

    .history-verification-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
@php
    $verificationHistories = collect($adminVerificationHistories ?? []);
    $historyStats = $adminVerificationHistoryStats ?? [
        'total' => $verificationHistories->count(),
        'disetujui' => $verificationHistories->whereIn('status', ['disetujui', 'approved'])->count(),
        'ditolak' => $verificationHistories->whereIn('status', ['ditolak', 'rejected'])->count(),
        'hari_ini' => $verificationHistories->filter(fn ($item) => optional($item['verified_at'] ?? null)->isToday())->count(),
    ];
@endphp
<div class="container-fluid history-verification-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Riwayat Verifikasi</h2>
            <p class="text-muted mb-0">
                Lihat seluruh riwayat verifikasi akun, dokumen, dan perguruan tinggi yang telah diproses.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-card-filter="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Verifikasi</p>
                        <h3 class="mb-0" id="statTotal">{{ $historyStats['total'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-list-check"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-card-filter="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Disetujui</p>
                        <h3 class="mb-0" id="statDisetujui">{{ $historyStats['disetujui'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-card-filter="ditolak">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Ditolak</p>
                        <h3 class="mb-0" id="statDitolak">{{ $historyStats['ditolak'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-card-filter="hari ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Aktivitas Hari Ini</p>
                        <h3 class="mb-0" id="statToday">{{ $historyStats['hari_ini'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar2-check-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="historyFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="typeFilter" class="form-label">Jenis Data</label>
                        <select class="form-select" id="typeFilter">
                            <option value="semua">Semua Jenis</option>
                            <option value="Akun">Akun</option>
                            <option value="Dokumen">Dokumen</option>
                            <option value="Perguruan Tinggi">Perguruan Tinggi</option>
                        </select>
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

                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari nama, jenis, atau admin">
                        </div>
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
                    <h5 class="mb-1">Data Riwayat</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama/Entitas</th>
                            <th>Role</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Admin</th>
                            <th width="90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Riwayat verifikasi tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination riwayat verifikasi">
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
                <h5 class="modal-title">Detail Riwayat Verifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="historyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Data berhasil ditampilkan.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const histories = @json($verificationHistories);

    const perPage = 10;
    let currentPage = 1;

    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('historyTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const toast = new bootstrap.Toast(document.getElementById('historyToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            disetujui: 'bg-success',
            ditolak: 'bg-danger',
            menunggu: 'bg-warning text-dark'
        }[value] || 'bg-secondary';
    }

    function typeClass(value) {
        return {
            Akun: 'bg-primary',
            Dokumen: 'bg-info text-dark',
            'Perguruan Tinggi': 'bg-secondary'
        }[value] || 'bg-secondary';
    }

    function filteredHistories() {
        const status = statusFilter.value;
        const type = typeFilter.value;
        const date = dateFilter.value;
        const keyword = searchInput.value.trim().toLowerCase();

        return histories.filter((history) => {
            const matchStatus = status === 'semua' || history.status === status;
            const matchType = type === 'semua' || history.jenis_label === type;
            const matchDate = date === 'semua' || history.periode === date;
            const matchKeyword = !keyword || [history.nama, history.jenis_label, history.status, history.admin, history.verified_at]
                .join(' ')
                .toLowerCase()
                .includes(keyword);

            return matchStatus && matchType && matchDate && matchKeyword;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = histories.length;
        document.getElementById('statDisetujui').textContent = histories.filter((item) => item.status === 'disetujui').length;
        document.getElementById('statDitolak').textContent = histories.filter((item) => item.status === 'ditolak').length;
        document.getElementById('statToday').textContent = histories.filter((item) => item.periode === 'hari ini').length;
    }

    function renderTable() {
        const data = filteredHistories();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((history, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${history.nama}</td>
                <td>${history.role_label}</td>
                <td><span class="badge ${typeClass(history.jenis)}">${history.jenis}</span></td>
                <td><span class="badge ${statusClass(history.status)}">${titleCase(history.status)}</span></td>
                <td>${history.tanggal}</td>
                <td>${history.admin}</td>
                <td>
                    <button class="btn btn-info btn-sm" type="button" data-id="${history.id}">
                        Lihat
                    </button>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} riwayat ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} riwayat`
            : 'Menampilkan 0 riwayat';

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

    function showDetail(id) {
        const history = histories.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama/Entitas</dt>
                <dd class="col-sm-8">${history.nama}</dd>
                <dt class="col-sm-4">Role</dt>
                <dd class="col-sm-8">${history.role_label}</dd>
                <dt class="col-sm-4">Jenis</dt>
                <dd class="col-sm-8">${history.jenis_label}</dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">${titleCase(history.status)}</dd>
                <dt class="col-sm-4">Tanggal</dt>
                <dd class="col-sm-8">${history.tanggal}</dd>
                <dt class="col-sm-4">Admin</dt>
                <dd class="col-sm-8">${history.admin}</dd>
                <dt class="col-sm-4">Verifikasi</dt>
                <dd class="col-sm-8">${history.verified_at}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function showToast(message) {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    document.querySelectorAll('[data-card-filter]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-card-filter]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            const filter = card.dataset.cardFilter;
            statusFilter.value = ['disetujui', 'ditolak'].includes(filter) ? filter : 'semua';
            dateFilter.value = filter === 'hari ini' ? 'hari ini' : 'semua';
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

    [statusFilter, typeFilter, dateFilter].forEach((input) => {
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

    document.getElementById('resetFilter').addEventListener('click', () => {
        statusFilter.value = 'semua';
        typeFilter.value = 'semua';
        dateFilter.value = 'semua';
        searchInput.value = '';
        currentPage = 1;
        document.querySelectorAll('[data-card-filter]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-card-filter="semua"]').classList.add('active');
        renderTable();
        showToast('Filter riwayat berhasil direset.');
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-id]');
        if (!button) {
            return;
        }

        showDetail(Number(button.dataset.id));
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
