@extends('admin.layout.admin')

@section('title', 'Statistik Pengguna')

@push('styles')
<style>
    .user-stat-page .page-title { font-weight: 700; color: #163342; }
    .user-stat-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .user-stat-page .stat-card,
    .user-stat-page .filter-card,
    .user-stat-page .table-card,
    .user-stat-page .summary-card { border: 0; border-radius: 8px; }
    .user-stat-page .stat-card { cursor: pointer; transition: .2s ease; }
    .user-stat-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .user-stat-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .institution-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fbfdfe; }
    .institution-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
    .user-stat-page .table { font-size: 14px; }
    .user-stat-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .user-stat-page .table tbody tr:hover { background: #f8fbfd; }
    .user-stat-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 310px; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid user-stat-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.laporan-monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Statistik Pengguna</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Statistik Pengguna</h2>
            <p class="text-muted mb-0">Monitoring distribusi peran, aktivitas, status akun, dan performa penggunaan sistem.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="downloadButton"><i class="bi bi-file-earmark-arrow-down"></i> Unduh Laporan</button>
            <button class="btn btn-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Data</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Pengguna</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pengguna Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="nonaktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pengguna Nonaktif</p><h3 class="mb-0" id="statInactive">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-person-x"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-role-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peran</p><h3 class="mb-0" id="statRoles">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-shield-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-institution-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Instansi</p><h3 class="mb-0" id="statInstitutions">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-building"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="periodFilter" class="form-label">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="Semester Genap">Semester Genap</option>
                        <option value="Semester Ganjil">Semester Ganjil</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="roleFilter" class="form-label">Peran</label>
                    <select class="form-select" id="roleFilter">
                        <option value="semua">Semua Peran</option>
                        <option value="Super Admin">Super Admin</option>
                        <option value="Admin">Admin</option>
                        <option value="Peserta Magang">Peserta Magang</option>
                        <option value="Mentor">Mentor</option>
                        <option value="Pembimbing Akademik">Pembimbing Akademik</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="studyFilter" class="form-label">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi/Unit</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Akademik">Akademik</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="institutionFilter" class="form-label">Instansi</label>
                    <select class="form-select" id="institutionFilter">
                        <option value="semua">Semua Instansi</option>
                        <option value="Universitas Teknologi Yogyakarta">Universitas Teknologi Yogyakarta</option>
                        <option value="Universitas Ahmad Dahlan">Universitas Ahmad Dahlan</option>
                        <option value="LLDIKTI Wilayah V">LLDIKTI Wilayah V</option>
                        <option value="Dinas Kominfo DIY">Dinas Kominfo DIY</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status Akun</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                        <option value="menunggu">Menunggu</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="searchInput" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari Pengguna">
                        <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-search"></i></button>
                        <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card summary-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                    <h5 class="mb-1">Statistik</h5>
                    <p class="text-muted mb-0">Sebaran pengguna berdasarkan instansi dan peran.</p>
                        </div>
                        <span class="badge bg-light text-dark" id="institutionCount">0 instansi</span>
                    </div>
                    <div class="institution-grid" id="institutionSummary"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Daftar Pengguna Terdaftar</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengguna</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Program Studi/Unit</th>
                            <th>Instansi</th>
                            <th>Status</th>
                            <th>Tanggal Terdaftar</th>
                            <th>Terakhir Aktif</th>
                            <th width="300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data pengguna tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination statistik pengguna"><ul class="pagination mb-0" id="pagination"></ul></nav>
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
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>
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

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editUserId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="editName" class="form-label">Nama Pengguna</label>
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
                            <select class="form-select" id="editStatus">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="menunggu">Menunggu</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <div class="row g-2">
                                    <div class="col-md-6"><strong>Peran:</strong> <span id="editRoleLabel">-</span></div>
                                    <div class="col-md-6"><strong>Instansi:</strong> <span id="editInstitutionLabel">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="actionToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Tindakan berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = @json(csrf_token());
    const participantSource = @json($adminParticipants ?? []);
    const mentorSource = @json($adminMentors ?? []);
    const advisorSource = @json($adminAdvisors ?? []);
    const adminUserSource = @json($adminUsers ?? []);
    const exportUrl = @json(route('admin.laporan-monitoring.statistik-pengguna.export'));
    const downloadUrl = @json(route('admin.laporan-monitoring.statistik-pengguna.download'));
    const updateUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.update', ['user' => '__USER__']));
    const statusUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.status', ['user' => '__USER__']));
    const deleteUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.destroy', ['user' => '__USER__']));
    let users = [
        ...participantSource.map((item) => ({
            id: Number(item.user_id || item.id),
            name: item.nama || '-',
            username: item.username || '-',
            email: item.email || '-',
            role: 'Peserta Magang',
            unit: item.program_studi || '-',
            institution: item.instansi || '-',
            status: item.status || 'menunggu',
            registered: item.tanggal || '-',
            lastActive: item.tanggal || '-',
            period: item.program_magang || '2026',
            canToggle: true,
            canDelete: true,
        })),
        ...mentorSource.map((item) => ({
            id: Number(item.user_id || item.id),
            name: item.nama || '-',
            username: item.username || '-',
            email: item.email || '-',
            role: 'Mentor',
            unit: item.divisi || 'Akademik',
            institution: item.instansi || 'LLDIKTI Wilayah V',
            status: item.status || 'menunggu',
            registered: item.tanggal || '-',
            lastActive: item.tanggal || '-',
            period: '2026',
            canToggle: true,
            canDelete: true,
        })),
        ...advisorSource.map((item) => ({
            id: Number(item.user_id || item.id),
            name: item.nama || '-',
            username: item.username || '-',
            email: item.email || '-',
            role: 'Pembimbing Akademik',
            unit: item.program_studi || 'Akademik',
            institution: item.kampus || '-',
            status: item.status || 'menunggu',
            registered: item.tanggal || '-',
            lastActive: item.tanggal || '-',
            period: '2026',
            canToggle: true,
            canDelete: true,
        })),
        ...adminUserSource
            .filter((item) => ['admin', 'super_admin'].includes(item.role))
            .map((item) => ({
                id: Number(item.user_id || item.id),
                name: item.nama || '-',
                username: item.username || '-',
                email: item.email || '-',
                role: item.role === 'super_admin' ? 'Super Admin' : 'Admin',
                roleKey: item.role,
                roleLabel: item.role_label || (item.role === 'super_admin' ? 'Super Admin' : 'Admin'),
                unit: 'Administrasi',
                institution: item.instansi || 'LLDIKTI Wilayah V',
                status: item.status || 'aktif',
                registered: item.tanggal || '-',
                lastActive: item.verified_at || item.tanggal || '-',
                period: '2026',
                canToggle: item.role !== 'super_admin',
                canDelete: item.role !== 'super_admin',
            })),
    ];

    let filteredUsers = [...users];
    let currentPage = 1;
    const perPage = 5;
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    let pendingAction = null;
    let pendingEditUser = null;

    const badgeMap = {
        aktif: 'success',
        nonaktif: 'danger',
        menunggu: 'warning',
        ditolak: 'secondary'
    };

    function renderStats() {
        const active = users.filter(item => item.status === 'aktif').length;
        const inactive = users.filter(item => item.status === 'nonaktif').length;
        const roleCount = new Set(users.filter(item => (item.roleKey || item.role) !== 'super_admin').map(item => item.role)).size;
        const institutionCount = new Set(users.map(item => item.institution)).size;
        document.getElementById('statTotal').textContent = users.length;
        document.getElementById('statActive').textContent = active;
        document.getElementById('statInactive').textContent = inactive;
        document.getElementById('statRoles').textContent = roleCount;
        document.getElementById('statInstitutions').textContent = institutionCount;
    }

    function renderInstitutionSummary() {
        const summary = users.reduce((result, item) => {
            result[item.institution] = (result[item.institution] || 0) + 1;
            return result;
        }, {});
        const entries = Object.entries(summary);
        document.getElementById('institutionCount').textContent = `${entries.length} instansi`;

        if (!entries.length) {
            document.getElementById('institutionSummary').innerHTML = '<div class="text-muted small">Belum ada data instansi untuk ditampilkan.</div>';
            return;
        }

        document.getElementById('institutionSummary').innerHTML = entries.map(([institution, total]) => `
            <div class="institution-item shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                    <strong>${institution}</strong>
                    <span class="badge bg-primary">${total} pengguna</span>
                </div>
                <div class="progress mb-2" style="height:8px">
                    <div class="progress-bar bg-info" style="width:${users.length ? Math.max(20, total / users.length * 100) : 0}%"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted">
                    <span>Proporsi pengguna</span>
                    <span>${users.length ? Math.round(total / users.length * 100) : 0}%</span>
                </div>
            </div>
        `).join('');
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const pageItems = filteredUsers.slice(start, start + perPage);
        const tableBody = document.getElementById('userTable');
        const emptyState = document.getElementById('emptyState');
        const tableWrapper = document.getElementById('tableWrapper');

        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.email}</td>
                <td>${item.roleLabel || item.role}</td>
                <td>${item.unit}</td>
                <td>${item.institution}</td>
                <td><span class="badge bg-${badgeMap[item.status]}">${item.status}</span></td>
                <td>${item.registered}</td>
                <td>${item.lastActive}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-primary" data-action="detail" data-index="${users.indexOf(item)}"><i class="bi bi-eye"></i> Lihat</button>
                        <button class="btn btn-sm btn-outline-secondary" data-action="edit" data-index="${users.indexOf(item)}"><i class="bi bi-pencil"></i> Edit</button>
                        ${item.canToggle ? `<button class="btn btn-sm ${item.status === 'nonaktif' ? 'btn-outline-success' : 'btn-outline-dark'}" data-action="nonaktif" data-index="${users.indexOf(item)}"><i class="bi ${item.status === 'nonaktif' ? 'bi-person-check' : 'bi-person-dash'}"></i> ${item.status === 'nonaktif' ? 'Aktifkan' : 'Nonaktif'}</button>` : ''}
                        ${item.canDelete ? `<button class="btn btn-sm btn-outline-danger" data-action="hapus" data-index="${users.indexOf(item)}"><i class="bi bi-trash"></i> Hapus</button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        const hasData = filteredUsers.length > 0;
        emptyState.classList.toggle('d-none', hasData);
        tableWrapper.classList.toggle('d-none', !hasData);
        document.getElementById('tableSummary').textContent = hasData ? `Menampilkan ${start + 1} sampai ${Math.min(start + perPage, filteredUsers.length)} dari ${filteredUsers.length} data pengguna` : 'Menampilkan 0 data';
        document.getElementById('pageInfo').textContent = hasData ? `Halaman ${currentPage} dari ${Math.ceil(filteredUsers.length / perPage)}` : 'Menampilkan 0 data';
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.max(1, Math.ceil(filteredUsers.length / perPage));
        const pagination = document.getElementById('pagination');
        let items = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            items += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        items += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = items;
    }

    function applyFilter(statusOverride = null) {
        const period = document.getElementById('periodFilter').value;
        const role = document.getElementById('roleFilter').value;
        const study = document.getElementById('studyFilter').value;
        const institution = document.getElementById('institutionFilter').value;
        const status = statusOverride || document.getElementById('statusFilter').value;
        const keyword = document.getElementById('searchInput').value.toLowerCase();

        filteredUsers = users.filter(item => {
            const matchPeriod = period === 'semua' || item.period === period;
            const matchRole = role === 'semua' || item.role === role;
            const matchStudy = study === 'semua' || item.unit === study;
            const matchInstitution = institution === 'semua' || item.institution === institution;
            const matchStatus = status === 'semua' || item.status === status;
            const matchKeyword = !keyword || `${item.name} ${item.email} ${item.role} ${item.institution}`.toLowerCase().includes(keyword);
            return matchPeriod && matchRole && matchStudy && matchInstitution && matchStatus && matchKeyword;
        });

        currentPage = 1;
        renderTable();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('actionToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openConfirm(title, message, callback) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = message;
        const actionButton = document.getElementById('confirmAction');
        actionButton.onclick = async () => {
            try {
                await callback();
            } finally {
                confirmModal.hide();
            }
        };
        confirmModal.show();
    }

    document.getElementById('applyFilter').addEventListener('click', () => applyFilter());
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.querySelectorAll('.user-stat-page select').forEach(select => select.value = 'semua');
        document.getElementById('searchInput').value = '';
        filteredUsers = [...users];
        currentPage = 1;
        renderTable();
        showToast('Filter statistik pengguna telah direset.', 'info');
    });
    document.getElementById('searchInput').addEventListener('keyup', event => {
        if (event.key === 'Enter') applyFilter();
    });

    document.querySelectorAll('[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            const status = card.dataset.statusCard;
            document.getElementById('statusFilter').value = status;
            applyFilter(status);
        });
    });

    document.getElementById('pagination').addEventListener('click', event => {
        const page = Number(event.target.dataset.page);
        const totalPages = Math.max(1, Math.ceil(filteredUsers.length / perPage));
        if (!page || page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    });

    document.getElementById('userTable').addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const user = users[button.dataset.index];
        const action = button.dataset.action;

        if (action === 'detail') {
            document.getElementById('detailContent').innerHTML = `
                <dl class="row mb-0">
                <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">${user.name}</dd>
                <dt class="col-sm-4">Username</dt><dd class="col-sm-8">${user.username || '-'}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">${user.email}</dd>
                <dt class="col-sm-4">Peran</dt><dd class="col-sm-8">${user.roleLabel || user.role}</dd>
                <dt class="col-sm-4">Unit</dt><dd class="col-sm-8">${user.unit}</dd>
                <dt class="col-sm-4">Instansi</dt><dd class="col-sm-8">${user.institution}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge bg-${badgeMap[user.status]}">${user.status}</span></dd>
                <dt class="col-sm-4">Terakhir Aktif</dt><dd class="col-sm-8">${user.lastActive}</dd>
            </dl>
        `;
            detailModal.show();
            return;
        }

        if (action === 'edit') {
            pendingEditUser = user;
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editName').value = user.name || '';
            document.getElementById('editUsername').value = user.username || '';
            document.getElementById('editEmail').value = user.email || '';
            document.getElementById('editStatus').value = user.status || 'aktif';
            document.getElementById('editStatus').disabled = user.roleKey === 'super_admin';
            document.getElementById('editRoleLabel').textContent = user.roleLabel || user.role;
            document.getElementById('editInstitutionLabel').textContent = user.institution || '-';
            editModal.show();
            return;
        }

        if (action === 'nonaktif') {
            const nextLabel = user.status === 'nonaktif' ? 'aktifkan kembali' : 'nonaktifkan';
            const nextStatus = user.status === 'nonaktif' ? 'aktif' : 'nonaktif';
            openConfirm('Konfirmasi Status', `Apakah Anda yakin ingin ${nextLabel} akun ${user.name}?`, () => {
                submitStatusToggle(user, nextStatus);
            });
            return;
        }

        if (action === 'hapus') {
            openConfirm('Konfirmasi Hapus', `Apakah Anda yakin ingin menghapus akun ${user.name}?`, () => {
                submitDeleteUser(user);
            });
        }
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        window.location.href = exportUrl;
    });
    document.getElementById('downloadButton').addEventListener('click', () => {
        window.location.href = downloadUrl;
    });

    document.getElementById('editForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!pendingEditUser) {
            return;
        }

        await submitEditUser(pendingEditUser, {
            name: document.getElementById('editName').value.trim(),
            username: document.getElementById('editUsername').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            account_status: document.getElementById('editStatus').value,
        });
    });

    async function submitEditUser(user, payload) {
        const response = await fetch(updateUrlTemplate.replace('__USER__', user.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showToast(data.message || 'Gagal memperbarui data pengguna.', 'danger');
            return;
        }

        const index = users.findIndex((item) => item.id === user.id);
        if (index !== -1) {
            users[index] = {
                ...users[index],
                name: payload.name,
                username: payload.username || '-',
                email: payload.email,
                status: payload.account_status,
            };
        }

        editModal.hide();
        pendingEditUser = null;
        renderStats();
        renderInstitutionSummary();
        applyFilter();
        showToast(data.message || `Data pengguna ${user.name} berhasil diperbarui.`);
    }

    async function submitStatusToggle(user, nextStatus) {
        const response = await fetch(statusUrlTemplate.replace('__USER__', user.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: nextStatus }),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showToast(data.message || 'Gagal memperbarui status pengguna.', 'danger');
            return;
        }

        const index = users.findIndex((item) => item.id === user.id);
        if (index !== -1) {
            users[index] = {
                ...users[index],
                status: data.status || nextStatus,
            };
        }

        renderStats();
        renderInstitutionSummary();
        applyFilter();
        showToast(data.message || 'Status pengguna berhasil diperbarui.');
    }

    async function submitDeleteUser(user) {
        const response = await fetch(deleteUrlTemplate.replace('__USER__', user.id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showToast(data.message || 'Gagal menghapus pengguna.', 'danger');
            return;
        }

        users = users.filter((item) => item.id !== user.id);
        selectedIds.delete(user.id);
        currentPage = 1;
        renderStats();
        renderInstitutionSummary();
        applyFilter();
        showToast(data.message || `Akun ${user.name} berhasil dihapus.`);
    }

    renderStats();
    renderInstitutionSummary();
    renderTable();
});
</script>
@endpush
