@extends('admin.layout.admin')

@section('title', 'Manajemen Pengguna')

@push('styles')
<style>
    .user-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .user-page .stat-card,
    .user-page .filter-card,
    .user-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .user-page .stat-card {
        transition: 0.2s ease;
        cursor: pointer;
    }

    .user-page .stat-card:hover,
    .user-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .user-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .user-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .user-page .table tbody tr {
        transition: 0.15s ease;
    }

    .user-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .user-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .user-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .user-page .pagination .page-link {
        color: #0b5f86;
    }

    .user-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid user-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Manajemen Pengguna</h2>
            <p class="text-muted mb-0">
                Kelola data peserta magang, mentor, pembimbing akademik, dan admin secara terintegrasi.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Pengguna</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary">
                        <i class="bi bi-people-fill"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Aktif</p>
                        <h3 class="mb-0" id="statAktif">0</h3>
                    </div>
                    <span class="stat-icon bg-success">
                        <i class="bi bi-person-check-fill"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Menunggu Verifikasi</p>
                        <h3 class="mb-0" id="statMenunggu">0</h3>
                    </div>
                    <span class="stat-icon bg-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="nonaktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Nonaktif</p>
                        <h3 class="mb-0" id="statNonaktif">0</h3>
                    </div>
                    <span class="stat-icon bg-danger">
                        <i class="bi bi-person-x-fill"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="userFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="menunggu">Menunggu Verifikasi</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="roleFilter" class="form-label">Peran</label>
                        <select class="form-select" id="roleFilter">
                            <option value="semua">Semua Peran</option>
                            <option value="peserta">Peserta Magang</option>
                            <option value="mentor">Mentor</option>
                            <option value="pembimbing">Pembimbing Akademik</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="searchInput" class="form-label">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari nama, email, peran, atau instansi">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" type="button" id="resetFilter">
                            <i class="bi bi-arrow-clockwise"></i>
                            Reset Filter
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
                    <h5 class="mb-1">Data Pengguna</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <span class="badge bg-light text-dark" id="selectedCount">0 pengguna dipilih</span>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Peran</th>
                            <th>Email</th>
                            <th>Instansi</th>
                            <th>Status</th>
                            <th width="240">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data pengguna tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination pengguna">
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
                <h5 class="modal-title">Detail Pengguna</h5>
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
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <input type="hidden" id="editRole">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editName" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="editName" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername">
                        </div>
                        <div class="col-md-6">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" required>
                        </div>
                        <div class="col-md-6">
                            <label for="editStatus" class="form-label">Status Akun</label>
                            <select class="form-select" id="editStatus" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-4" data-role-section="peserta">
                        <h6 class="fw-bold mb-3">Detail Peserta</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editNim" class="form-label">NIM</label>
                                <input type="text" class="form-control" id="editNim">
                            </div>
                            <div class="col-md-4">
                                <label for="editTempatLahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="editTempatLahir">
                            </div>
                            <div class="col-md-4">
                                <label for="editTanggalLahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="editTanggalLahir">
                            </div>
                            <div class="col-md-4">
                                <label for="editJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="editJenisKelamin">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="editNoHp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="editNoHp">
                            </div>
                            <div class="col-md-4">
                                <label for="editPerguruanTinggi" class="form-label">Perguruan Tinggi</label>
                                <input type="text" class="form-control" id="editPerguruanTinggi">
                            </div>
                            <div class="col-md-6">
                                <label for="editProgramStudi" class="form-label">Program Studi</label>
                                <input type="text" class="form-control" id="editProgramStudi">
                            </div>
                            <div class="col-md-6">
                                <label for="editFakultas" class="form-label">Fakultas</label>
                                <input type="text" class="form-control" id="editFakultas">
                            </div>
                            <div class="col-md-6">
                                <label for="editProgramMagang" class="form-label">Program Magang</label>
                                <input type="text" class="form-control" id="editProgramMagang">
                            </div>
                            <div class="col-md-6">
                                <label for="editPembimbingAkademik" class="form-label">Pembimbing Akademik</label>
                                <input type="text" class="form-control" id="editPembimbingAkademik">
                            </div>
                            <div class="col-md-12">
                                <label for="editAlamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="editAlamat" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="editTanggalMulaiMagang" class="form-label">Tanggal Mulai Magang</label>
                                <input type="date" class="form-control" id="editTanggalMulaiMagang">
                            </div>
                            <div class="col-md-6">
                                <label for="editTanggalSelesaiMagang" class="form-label">Tanggal Selesai Magang</label>
                                <input type="date" class="form-control" id="editTanggalSelesaiMagang">
                            </div>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-4" data-role-section="mentor">
                        <h6 class="fw-bold mb-3">Detail Mentor</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editNip" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="editNip">
                            </div>
                            <div class="col-md-4">
                                <label for="editMentorJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="editMentorJenisKelamin">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="editJabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="editJabatan">
                            </div>
                            <div class="col-md-6">
                                <label for="editDivisi" class="form-label">Divisi</label>
                                <input type="text" class="form-control" id="editDivisi">
                            </div>
                            <div class="col-md-6">
                                <label for="editMentorPerguruanTinggi" class="form-label">Perguruan Tinggi</label>
                                <input type="text" class="form-control" id="editMentorPerguruanTinggi">
                            </div>
                            <div class="col-md-6">
                                <label for="editMentorNoHp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="editMentorNoHp">
                            </div>
                            <div class="col-md-6">
                                <label for="editMentorAlamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="editMentorAlamat" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-4" data-role-section="pembimbing">
                        <h6 class="fw-bold mb-3">Detail Pembimbing Akademik</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="editNidn" class="form-label">NIDN / NIP</label>
                                <input type="text" class="form-control" id="editNidn">
                            </div>
                            <div class="col-md-4">
                                <label for="editPembimbingTempatLahir" class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" id="editPembimbingTempatLahir">
                            </div>
                            <div class="col-md-4">
                                <label for="editPembimbingTanggalLahir" class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" id="editPembimbingTanggalLahir">
                            </div>
                            <div class="col-md-4">
                                <label for="editPembimbingJenisKelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="editPembimbingJenisKelamin">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="editPembimbingNoHp" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="editPembimbingNoHp">
                            </div>
                            <div class="col-md-4">
                                <label for="editPembimbingPerguruanTinggi" class="form-label">Perguruan Tinggi</label>
                                <input type="text" class="form-control" id="editPembimbingPerguruanTinggi">
                            </div>
                            <div class="col-md-6">
                                <label for="editPembimbingProgramStudi" class="form-label">Program Studi</label>
                                <input type="text" class="form-control" id="editPembimbingProgramStudi">
                            </div>
                            <div class="col-md-6">
                                <label for="editPembimbingJabatan" class="form-label">Jabatan</label>
                                <input type="text" class="form-control" id="editPembimbingJabatan">
                            </div>
                            <div class="col-md-12">
                                <label for="editPembimbingAlamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="editPembimbingAlamat" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-4" data-role-section="admin">
                        <h6 class="fw-bold mb-3">Detail Admin</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="editPhone" class="form-label">No HP</label>
                                <input type="text" class="form-control" id="editPhone">
                            </div>
                            <div class="col-md-12">
                                <label for="editAddress" class="form-label">Alamat</label>
                                <textarea class="form-control" id="editAddress" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveEditButton">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="userToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const users = @json($adminUsers ?? []);
    const csrfToken = @json(csrf_token());
    const updateUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.update', ['user' => '__USER__']));
    const statusUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.status', ['user' => '__USER__']));
    const deleteUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.destroy', ['user' => '__USER__']));

    const perPage = 10;
    let currentPage = 1;
    let selectedIds = new Set();
    let pendingAction = null;

    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('userTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');
    const selectedCount = document.getElementById('selectedCount');
    const checkAll = document.getElementById('checkAll');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('userToast'), { delay: 3000 });

    function titleCase(value) {
        return String(value || '')
            .split(' ')
            .filter(Boolean)
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function roleLabel(value) {
        return {
            peserta: 'Peserta Magang',
            mentor: 'Mentor',
            pembimbing: 'Pembimbing Akademik',
            admin: 'Admin',
            super_admin: 'Super Admin'
        }[value];
    }

    function statusClass(value) {
        return {
            aktif: 'bg-success',
            nonaktif: 'bg-danger',
            menunggu: 'bg-warning text-dark',
            ditolak: 'bg-secondary',
            disetujui: 'bg-success'
        }[value];
    }

    function roleClass(value) {
        return {
            peserta: 'bg-primary',
            mentor: 'bg-info text-dark',
            pembimbing: 'bg-success',
            admin: 'bg-dark',
            super_admin: 'bg-secondary'
        }[value];
    }

    function filteredUsers() {
        const status = statusFilter.value;
        const role = roleFilter.value;
        const keyword = searchInput.value.trim().toLowerCase();

        return users.filter((user) => {
            const matchStatus = status === 'semua' || user.status === status;
            const matchRole = role === 'semua' || user.role === role;
            const matchKeyword = !keyword || [user.nama, user.email, user.instansi, roleLabel(user.role), user.status]
                .join(' ')
                .toLowerCase()
                .includes(keyword);

            return matchStatus && matchRole && matchKeyword;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = users.length;
        document.getElementById('statAktif').textContent = users.filter((user) => user.status === 'aktif').length;
        document.getElementById('statMenunggu').textContent = users.filter((user) => user.status === 'menunggu').length;
        document.getElementById('statNonaktif').textContent = users.filter((user) => user.status === 'nonaktif').length;
    }

    function renderTable() {
        const data = filteredUsers();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((user, index) => `
            <tr>
                <td>
                    <input class="form-check-input row-check" type="checkbox" value="${user.id}" ${selectedIds.has(user.id) ? 'checked' : ''}>
                </td>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${user.nama}</td>
                <td><span class="badge ${roleClass(user.role)}">${roleLabel(user.role)}</span></td>
                <td>${user.email}</td>
                <td>${user.instansi}</td>
                <td><span class="badge ${statusClass(user.status)}">${titleCase(user.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${user.id}">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${user.id}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        ${user.role !== 'super_admin' ? `
                        <button class="btn ${user.status === 'nonaktif' ? 'btn-success' : 'btn-outline-dark'} btn-sm" type="button" data-action="toggle-status" data-id="${user.id}">
                            <i class="bi ${user.status === 'nonaktif' ? 'bi-person-check' : 'bi-person-dash'}"></i> ${user.status === 'nonaktif' ? 'Aktifkan' : 'Nonaktif'}
                        </button>
                        ` : ''}
                        ${user.role !== 'super_admin' ? `
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="hapus" data-id="${user.id}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} pengguna ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} pengguna`
            : 'Menampilkan 0 pengguna';
        checkAll.checked = pageData.length > 0 && pageData.every((user) => selectedIds.has(user.id));

        renderPagination(totalPages);
        updateSelectedCount();
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

    function updateSelectedCount() {
        selectedCount.textContent = `${selectedIds.size} pengguna dipilih`;
    }

    function detailFields(user) {
        const commonFields = [
            ['Nama', user.nama],
            ['Username', user.username],
            ['Email', user.email],
            ['Peran', roleLabel(user.role)],
            ['Status', titleCase(user.status)],
            ['Instansi', user.instansi],
        ];

        const roleFields = {
            peserta: [
                ['NIM', user.nim],
                ['Tempat Lahir', user.tempat_lahir],
                ['Tanggal Lahir', user.tanggal_lahir],
                ['Jenis Kelamin', user.jenis_kelamin],
                ['No HP', user.no_hp],
                ['Alamat', user.alamat],
                ['Perguruan Tinggi', user.perguruan_tinggi],
                ['Fakultas', user.fakultas],
                ['Program Studi', user.program_studi],
                ['Program Magang', user.program_magang],
                ['Pembimbing Akademik', user.pembimbing_akademik],
                ['Tanggal Mulai Magang', user.tanggal_mulai_magang],
                ['Tanggal Selesai Magang', user.tanggal_selesai_magang],
            ],
            mentor: [
                ['NIP', user.nip],
                ['Jenis Kelamin', user.jenis_kelamin],
                ['Jabatan', user.jabatan],
                ['Divisi', user.divisi],
                ['No HP', user.no_hp],
                ['Alamat', user.alamat],
                ['Perguruan Tinggi', user.perguruan_tinggi],
            ],
            pembimbing: [
                ['NIDN / NIP', user.nidn],
                ['Tempat Lahir', user.tempat_lahir],
                ['Tanggal Lahir', user.tanggal_lahir],
                ['Jenis Kelamin', user.jenis_kelamin],
                ['No HP', user.no_hp],
                ['Alamat', user.alamat],
                ['Perguruan Tinggi', user.kampus],
                ['Program Studi', user.program_studi],
                ['Jabatan', user.jabatan],
            ],
            admin: [
                ['No HP', user.phone],
                ['Alamat', user.address],
            ],
            super_admin: [
                ['No HP', user.phone],
                ['Alamat', user.address],
            ],
        };

        return [...commonFields, ...(roleFields[user.role] || [])];
    }

    function showDetail(id) {
        const user = users.find((item) => item.id === id);
        const photo = user.foto
            ? `<img src="${user.foto}" alt="Foto ${user.nama}" class="rounded-circle border" style="width:88px;height:88px;object-fit:cover;">`
            : `<div class="rounded-circle border bg-light d-inline-flex align-items-center justify-content-center" style="width:88px;height:88px;"><i class="bi bi-person fs-2 text-muted"></i></div>`;

        document.getElementById('detailContent').innerHTML = `
            <div class="d-flex flex-column flex-sm-row gap-3 align-items-sm-center mb-3">
                <div>${photo}</div>
                <div>
                    <h5 class="mb-1">${user.nama}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge ${roleClass(user.role)}">${roleLabel(user.role)}</span>
                        <span class="badge ${statusClass(user.status)}">${titleCase(user.status)}</span>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <tbody>
                        ${detailFields(user).map(([label, value]) => `
                            <tr>
                                <th style="width: 42%;">${label}</th>
                                <td>${value || '-'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
        detailModal.show();
    }

    function openEditModal(id) {
        const user = users.find((item) => item.id === id);
        document.getElementById('editUserId').value = user.id;
        document.getElementById('editRole').value = user.role || '';
        document.getElementById('editName').value = user.nama || '';
        document.getElementById('editUsername').value = user.username || '';
        document.getElementById('editEmail').value = user.email || '';
        const statusSelect = document.getElementById('editStatus');
        statusSelect.value = user.status || 'aktif';
        statusSelect.disabled = user.role === 'super_admin';

        setFieldValue('editNim', user.nim);
        setFieldValue('editTempatLahir', user.tempat_lahir);
        setFieldValue('editTanggalLahir', user.tanggal_lahir_raw || '');
        setFieldValue('editJenisKelamin', user.jenis_kelamin);
        setFieldValue('editNoHp', user.no_hp);
        setFieldValue('editAlamat', user.alamat);
        setFieldValue('editPerguruanTinggi', user.perguruan_tinggi);
        setFieldValue('editProgramStudi', user.program_studi);
        setFieldValue('editFakultas', user.fakultas);
        setFieldValue('editProgramMagang', user.program_magang);
        setFieldValue('editPembimbingAkademik', user.pembimbing_akademik);
        setFieldValue('editTanggalMulaiMagang', user.tanggal_mulai_magang_raw || '');
        setFieldValue('editTanggalSelesaiMagang', user.tanggal_selesai_magang_raw || '');

        setFieldValue('editNip', user.nip);
        setFieldValue('editMentorJenisKelamin', user.jenis_kelamin);
        setFieldValue('editJabatan', user.jabatan);
        setFieldValue('editDivisi', user.divisi);
        setFieldValue('editMentorNoHp', user.no_hp);
        setFieldValue('editMentorPerguruanTinggi', user.perguruan_tinggi);
        setFieldValue('editMentorAlamat', user.alamat);

        setFieldValue('editNidn', user.nidn);
        setFieldValue('editPembimbingTempatLahir', user.tempat_lahir);
        setFieldValue('editPembimbingTanggalLahir', user.tanggal_lahir_raw || '');
        setFieldValue('editPembimbingJenisKelamin', user.jenis_kelamin);
        setFieldValue('editPembimbingNoHp', user.no_hp);
        setFieldValue('editPembimbingPerguruanTinggi', user.kampus);
        setFieldValue('editPembimbingProgramStudi', user.program_studi);
        setFieldValue('editPembimbingJabatan', user.jabatan);
        setFieldValue('editPembimbingAlamat', user.alamat);

        setFieldValue('editPhone', user.phone);
        setFieldValue('editAddress', user.address);

        toggleEditSections(user.role);
        editModal.show();
    }

    function showConfirm(id, action) {
        const user = users.find((item) => item.id === id);
        pendingAction = { id, action };

        const labels = {
            hapus: 'menghapus',
            toggleStatus: user.status === 'nonaktif' ? 'mengaktifkan kembali' : 'menonaktifkan'
        };

        document.getElementById('confirmTitle').textContent = `Konfirmasi ${titleCase(action === 'toggleStatus' ? 'status' : action)}`;
        document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin ${labels[action]} akun ${user.nama}?`;
        confirmModal.show();
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('userToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function setFieldValue(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.value = value ?? '';
        }
    }

    function formatDisplayDate(value) {
        if (!value) {
            return '-';
        }

        const date = new Date(`${value}T00:00:00`);
        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(date);
    }

    function toggleEditSections(role) {
        const visibleSections = {
            peserta: ['peserta'],
            mentor: ['mentor'],
            pembimbing: ['pembimbing'],
            admin: ['admin'],
            super_admin: ['admin'],
        }[role] || [];

        document.querySelectorAll('[data-role-section]').forEach((section) => {
            section.classList.toggle('d-none', !visibleSections.includes(section.dataset.roleSection));
        });
    }

    async function sendJson(url, method, payload = null) {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: payload ? JSON.stringify(payload) : null,
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || 'Permintaan gagal diproses.');
        }

        return data;
    }

    function updateLocalUser(id, updater) {
        const index = users.findIndex((item) => item.id === id);
        if (index === -1) {
            return null;
        }

        users[index] = {
            ...users[index],
            ...updater(users[index]),
        };

        return users[index];
    }

    function collectEditPayload(role) {
        const payload = {
            name: document.getElementById('editName').value.trim(),
            username: document.getElementById('editUsername').value.trim() || null,
            email: document.getElementById('editEmail').value.trim(),
            account_status: document.getElementById('editStatus').value,
        };

        if (role === 'peserta') {
            Object.assign(payload, {
                nim: document.getElementById('editNim').value.trim(),
                tempat_lahir: document.getElementById('editTempatLahir').value.trim(),
                tanggal_lahir: document.getElementById('editTanggalLahir').value || null,
                jenis_kelamin: document.getElementById('editJenisKelamin').value,
                no_hp: document.getElementById('editNoHp').value.trim(),
                alamat: document.getElementById('editAlamat').value.trim(),
                perguruan_tinggi: document.getElementById('editPerguruanTinggi').value.trim(),
                program_studi: document.getElementById('editProgramStudi').value.trim(),
                fakultas: document.getElementById('editFakultas').value.trim(),
                program_magang: document.getElementById('editProgramMagang').value.trim(),
                pembimbing_akademik: document.getElementById('editPembimbingAkademik').value.trim(),
                tanggal_mulai_magang: document.getElementById('editTanggalMulaiMagang').value || null,
                tanggal_selesai_magang: document.getElementById('editTanggalSelesaiMagang').value || null,
            });
        } else if (role === 'mentor') {
            Object.assign(payload, {
                nip: document.getElementById('editNip').value.trim(),
                jenis_kelamin: document.getElementById('editMentorJenisKelamin').value,
                jabatan: document.getElementById('editJabatan').value.trim(),
                divisi: document.getElementById('editDivisi').value.trim(),
                no_hp: document.getElementById('editMentorNoHp').value.trim(),
                alamat: document.getElementById('editMentorAlamat').value.trim(),
                perguruan_tinggi: document.getElementById('editMentorPerguruanTinggi').value.trim(),
            });
        } else if (role === 'pembimbing') {
            Object.assign(payload, {
                nidn: document.getElementById('editNidn').value.trim(),
                tempat_lahir: document.getElementById('editPembimbingTempatLahir').value.trim(),
                tanggal_lahir: document.getElementById('editPembimbingTanggalLahir').value || null,
                jenis_kelamin: document.getElementById('editPembimbingJenisKelamin').value,
                no_hp: document.getElementById('editPembimbingNoHp').value.trim(),
                alamat: document.getElementById('editPembimbingAlamat').value.trim(),
                perguruan_tinggi: document.getElementById('editPembimbingPerguruanTinggi').value.trim(),
                program_studi: document.getElementById('editPembimbingProgramStudi').value.trim(),
                jabatan: document.getElementById('editPembimbingJabatan').value.trim(),
            });
        } else if (role === 'admin' || role === 'super_admin') {
            Object.assign(payload, {
                phone: document.getElementById('editPhone').value.trim(),
                address: document.getElementById('editAddress').value.trim(),
            });
        }

        return payload;
    }

    async function submitEditForm(event) {
        event.preventDefault();
        const id = Number(document.getElementById('editUserId').value);
        const user = users.find((item) => item.id === id);
        const role = document.getElementById('editRole').value;

        try {
            const data = await sendJson(updateUrlTemplate.replace('__USER__', id), 'PATCH', collectEditPayload(role));

            updateLocalUser(id, (current) => ({
                nama: data.user?.name || document.getElementById('editName').value.trim(),
                username: data.user?.username ?? document.getElementById('editUsername').value.trim(),
                email: data.user?.email || document.getElementById('editEmail').value.trim(),
                status: data.user?.status || document.getElementById('editStatus').value,
                ...(current.role === 'peserta' ? {
                    nim: data.user?.nim ?? current.nim,
                    tempat_lahir: data.user?.tempat_lahir ?? current.tempat_lahir,
                    tanggal_lahir_raw: data.user?.tanggal_lahir ?? current.tanggal_lahir_raw,
                    tanggal_lahir: data.user?.tanggal_lahir ? formatDisplayDate(data.user.tanggal_lahir) : current.tanggal_lahir,
                    jenis_kelamin: data.user?.jenis_kelamin ?? current.jenis_kelamin,
                    no_hp: data.user?.no_hp ?? current.no_hp,
                    alamat: data.user?.alamat ?? current.alamat,
                    perguruan_tinggi: data.user?.perguruan_tinggi ?? current.perguruan_tinggi,
                    program_studi: data.user?.program_studi ?? current.program_studi,
                    fakultas: data.user?.fakultas ?? current.fakultas,
                    program_magang: data.user?.program_magang ?? current.program_magang,
                    pembimbing_akademik: data.user?.pembimbing_akademik ?? current.pembimbing_akademik,
                    tanggal_mulai_magang_raw: data.user?.tanggal_mulai_magang ?? current.tanggal_mulai_magang_raw,
                    tanggal_mulai_magang: data.user?.tanggal_mulai_magang ? formatDisplayDate(data.user.tanggal_mulai_magang) : current.tanggal_mulai_magang,
                    tanggal_selesai_magang_raw: data.user?.tanggal_selesai_magang ?? current.tanggal_selesai_magang_raw,
                    tanggal_selesai_magang: data.user?.tanggal_selesai_magang ? formatDisplayDate(data.user.tanggal_selesai_magang) : current.tanggal_selesai_magang,
                } : {}),
                ...(current.role === 'mentor' ? {
                    nip: data.user?.nip ?? current.nip,
                    jenis_kelamin: data.user?.jenis_kelamin ?? current.jenis_kelamin,
                    jabatan: data.user?.jabatan ?? current.jabatan,
                    divisi: data.user?.divisi ?? current.divisi,
                    no_hp: data.user?.no_hp ?? current.no_hp,
                    alamat: data.user?.alamat ?? current.alamat,
                    perguruan_tinggi: data.user?.perguruan_tinggi ?? current.perguruan_tinggi,
                } : {}),
                ...(current.role === 'pembimbing' ? {
                    nidn: data.user?.nidn ?? current.nidn,
                    tempat_lahir: data.user?.tempat_lahir ?? current.tempat_lahir,
                    tanggal_lahir_raw: data.user?.tanggal_lahir ?? current.tanggal_lahir_raw,
                    tanggal_lahir: data.user?.tanggal_lahir ? formatDisplayDate(data.user.tanggal_lahir) : current.tanggal_lahir,
                    jenis_kelamin: data.user?.jenis_kelamin ?? current.jenis_kelamin,
                    no_hp: data.user?.no_hp ?? current.no_hp,
                    alamat: data.user?.alamat ?? current.alamat,
                    kampus: data.user?.kampus ?? current.kampus,
                    program_studi: data.user?.program_studi ?? current.program_studi,
                    jabatan: data.user?.jabatan ?? current.jabatan,
                } : {}),
                ...(current.role === 'admin' || current.role === 'super_admin' ? {
                    phone: data.user?.phone ?? current.phone,
                    address: data.user?.address ?? current.address,
                } : {}),
            }));

            editModal.hide();
            updateStats();
            renderTable();
            showToast(data.message || `Data pengguna ${user.nama} berhasil diperbarui.`);
        } catch (error) {
            showToast(error.message, false);
        }
    }

    async function submitToggleStatus(id) {
        const user = users.find((item) => item.id === id);
        try {
            const data = await sendJson(statusUrlTemplate.replace('__USER__', id), 'PATCH');
            updateLocalUser(id, () => ({ status: data.status || (user.status === 'nonaktif' ? 'aktif' : 'nonaktif') }));
            updateStats();
            renderTable();
            showToast(data.message || `Status akun ${user.nama} berhasil diperbarui.`);
        } catch (error) {
            showToast(error.message, false);
        }
    }

    async function submitDeleteUser(id) {
        const user = users.find((item) => item.id === id);
        try {
            const data = await sendJson(deleteUrlTemplate.replace('__USER__', id), 'DELETE');
            const index = users.findIndex((item) => item.id === id);
            if (index !== -1) {
                users.splice(index, 1);
            }
            selectedIds.delete(id);
            updateStats();
            renderTable();
            showToast(data.message || `Data pengguna ${user.nama} berhasil dihapus.`);
        } catch (error) {
            showToast(error.message, false);
        }
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

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderTable();
        }
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        statusFilter.value = 'semua';
        roleFilter.value = 'semua';
        searchInput.value = '';
        selectedIds.clear();
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'detail') {
            showDetail(id);
            return;
        }

        if (button.dataset.action === 'edit') {
            openEditModal(id);
            return;
        }

        showConfirm(id, button.dataset.action === 'toggle-status' ? 'toggleStatus' : button.dataset.action);
    });

    tableBody.addEventListener('change', (event) => {
        if (!event.target.classList.contains('row-check')) {
            return;
        }

        const id = Number(event.target.value);
        if (event.target.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        renderTable();
    });

    checkAll.addEventListener('change', () => {
        filteredUsers()
            .slice((currentPage - 1) * perPage, currentPage * perPage)
            .forEach((user) => {
                if (checkAll.checked) {
                    selectedIds.add(user.id);
                } else {
                    selectedIds.delete(user.id);
                }
            });

        renderTable();
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('editForm').addEventListener('submit', submitEditForm);

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) {
            return;
        }

        const action = pendingAction.action;
        const id = pendingAction.id;

        confirmModal.hide();
        pendingAction = null;

        if (action === 'toggleStatus') {
            submitToggleStatus(id);
            return;
        }

        if (action === 'hapus') {
            submitDeleteUser(id);
        }
    });

    updateStats();
    renderTable();
</script>
@endpush
