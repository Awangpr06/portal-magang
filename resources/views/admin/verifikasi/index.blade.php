@extends('admin.layout.admin')

@section('title', 'Verifikasi')

@push('styles')
<style>
    .verification-page .page-title { font-weight: 700; color: #163342; }
    .verification-page .stat-card,
    .verification-page .filter-card,
    .verification-page .table-card { border: 0; border-radius: 8px; }
    .verification-page .stat-card { cursor: pointer; transition: .2s ease; }
    .verification-page .stat-card:hover,
    .verification-page .stat-card.active { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .verification-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .verification-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 14px; white-space: nowrap; }
    .verification-page .table tbody tr { transition: .15s ease; }
    .verification-page .table tbody tr:hover { background: #f8fbfd; }
    .verification-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; }
    .verification-page .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .verification-page .pagination .page-link { color: #0b5f86; }
    .verification-page .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
@php
    $verificationAccounts = collect($adminVerificationAccounts ?? []);
@endphp
<div class="container-fluid verification-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Verifikasi</h2>
            <p class="text-muted mb-0">
                Kelola verifikasi akun peserta, mentor, dan pembimbing berdasarkan data yang tersimpan di database.
            </p>
        </div>
        <div>
            <a class="btn btn-outline-primary" href="{{ route('admin.pengguna.index') }}">
                <i class="bi bi-arrow-right-circle"></i> Buka Manajemen Pengguna
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card w-100 text-start active" role="button" tabindex="0" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Akun Menunggu</p>
                        <h3 class="mb-0" id="statWaiting">0</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card w-100 text-start" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Akun Disetujui</p>
                        <h3 class="mb-0" id="statApproved">0</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card w-100 text-start" role="button" tabindex="0" data-status-card="ditolak">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Akun Ditolak</p>
                        <h3 class="mb-0" id="statRejected">0</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card w-100 text-start" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Akun</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-person-badge-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="aktif">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="roleFilter" class="form-label">Role</label>
                    <select class="form-select" id="roleFilter">
                        <option value="semua">Semua Role</option>
                        <option value="peserta">Peserta</option>
                        <option value="mentor">Mentor</option>
                        <option value="pembimbing">Pembimbing Akademik</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchInput" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" id="searchInput" placeholder="Cari nama, email, atau instansi">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" type="button" id="resetFilter">
                        <i class="bi bi-arrow-clockwise"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Data Verifikasi Akun</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Instansi</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="verificationTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination verifikasi">
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
                <h5 class="modal-title">Detail Verifikasi Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <a class="btn btn-primary" href="{{ route('admin.pengguna.index') }}">Kelola Pengguna</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const verificationData = @json($verificationAccounts);
    const manageUrl = @json(route('admin.pengguna.index'));
    const perPage = 10;
    let currentPage = 1;

    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('verificationTable');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const pageInfo = document.getElementById('pageInfo');
    const tableSummary = document.getElementById('tableSummary');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));

    function badgeClass(type, value) {
        if (type === 'status') {
            return {
                menunggu: 'bg-warning text-dark',
                aktif: 'bg-success',
                ditolak: 'bg-danger',
            }[value] || 'bg-secondary';
        }

        return {
            peserta: 'bg-primary',
            mentor: 'bg-info text-dark',
            pembimbing: 'bg-secondary',
        }[value] || 'bg-secondary';
    }

    function titleCase(value) {
        return String(value).split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function filteredData() {
        const status = statusFilter.value;
        const role = roleFilter.value;
        const keyword = searchInput.value.trim().toLowerCase();

        return verificationData.filter((item) => {
            const matchStatus = status === 'semua' || item.status === status;
            const matchRole = role === 'semua' || item.role === role;
            const matchKeyword = !keyword || [item.nama, item.email, item.instansi, item.role_label, item.tanggal]
                .join(' ')
                .toLowerCase()
                .includes(keyword);

            return matchStatus && matchRole && matchKeyword;
        });
    }

    function updateStats() {
        document.getElementById('statWaiting').textContent = verificationData.filter((item) => item.status === 'menunggu').length;
        document.getElementById('statApproved').textContent = verificationData.filter((item) => item.status === 'aktif').length;
        document.getElementById('statRejected').textContent = verificationData.filter((item) => item.status === 'ditolak').length;
        document.getElementById('statTotal').textContent = verificationData.length;
    }

    function renderPagination(totalPages) {
        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" type="button" data-page="${currentPage - 1}">Prev</button>
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
        const item = verificationData.find((data) => data.id === id);

        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama</dt>
                <dd class="col-sm-8">${item.nama}</dd>
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">${item.email}</dd>
                <dt class="col-sm-4">Role</dt>
                <dd class="col-sm-8">${item.role_label}</dd>
                <dt class="col-sm-4">Instansi</dt>
                <dd class="col-sm-8">${item.instansi}</dd>
                <dt class="col-sm-4">Tanggal Daftar</dt>
                <dd class="col-sm-8">${item.tanggal}</dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8"><span class="badge ${badgeClass('status', item.status)}">${titleCase(item.status)}</span></dd>
                <dt class="col-sm-4">Terakhir Verifikasi</dt>
                <dd class="col-sm-8">${item.verified_at}</dd>
                <dt class="col-sm-4">Catatan</dt>
                <dd class="col-sm-8">${item.rejection_reason}</dd>
            </dl>
        `;

        detailModal.show();
    }

    function renderTable() {
        const data = filteredData();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${item.nama}</td>
                <td><span class="badge ${badgeClass('role', item.role)}">${item.role_label}</span></td>
                <td>${item.instansi}</td>
                <td>${item.tanggal}</td>
                <td><span class="badge ${badgeClass('status', item.status)}">${titleCase(item.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="lihat" data-id="${item.id}">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <a class="btn btn-primary btn-sm" href="${manageUrl}">
                            <i class="bi bi-arrow-right-circle"></i> Kelola
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');

        emptyState.classList.toggle('d-none', data.length > 0);
        document.querySelector('.table-responsive').classList.toggle('d-none', data.length === 0);
        tableSummary.textContent = `${data.length} data ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data`
            : 'Menampilkan 0 data';

        renderPagination(totalPages);
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

    [statusFilter, roleFilter].forEach((input) => {
        input.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });
    });

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        renderTable();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        statusFilter.value = 'semua';
        roleFilter.value = 'semua';
        searchInput.value = '';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action="lihat"]');
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
