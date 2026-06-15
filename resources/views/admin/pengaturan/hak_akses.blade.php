@extends('admin.layout.admin')

@section('title', 'Hak Akses')

@push('styles')
<style>
    .access-page .page-title { font-weight: 700; color: #163342; }
    .access-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .access-page .stat-card,
    .access-page .table-card,
    .access-page .panel-card { border: 0; border-radius: 8px; }
    .access-page .stat-card { cursor: pointer; transition: .2s ease; }
    .access-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .access-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .permission-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fbfdfe; }
    .permission-group { border: 1px solid #dce8ee; border-radius: 10px; padding: 14px; margin-bottom: 12px; background: #fff; }
    .permission-group:last-child { margin-bottom: 0; }
    .permission-group-header { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; margin-bottom: 10px; }
    .permission-checklist { display: grid; gap: 8px; }
    .permission-check { display: flex; align-items: flex-start; gap: 10px; padding: 10px 12px; border: 1px solid #eef3f6; border-radius: 8px; background: #fbfdfe; }
    .permission-check input { margin-top: 3px; }
    .permission-check .permission-label { line-height: 1.35; }
    .permission-check .permission-label small { display: block; color: #6c757d; }
    .access-page .table { font-size: 14px; }
    .access-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .access-page .table tbody tr:hover { background: #f8fbfd; }
    .access-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 300px; }
    .access-page .info-row { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; background: #fbfdfe; margin-bottom: 10px; }
    .empty-state { min-height: 190px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid access-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.pengaturan.index') }}">Pengaturan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Hak Akses</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Hak Akses</h2>
            <p class="text-muted mb-0">Pusat pengelolaan peran, izin menu, kontrol modul, dan otorisasi administratif sistem.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Data Peran</button>
            <button class="btn btn-primary" type="button" id="addRoleButton"><i class="bi bi-plus-circle"></i> Tambah Peran</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peran</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-person-badge"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peran Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="nonaktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peran Nonaktif</p><h3 class="mb-0" id="statInactive">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Menu</p><h3 class="mb-0" id="statMenus">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-grid-3x3-gap"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Izin Akses</p><h3 class="mb-0" id="statPermissions">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-key"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card table-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Daftar Peran</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <button class="btn btn-outline-warning btn-sm" type="button" id="openPermissionPanelButton">
                            <i class="bi bi-layout-sidebar-inset-reverse me-1"></i>
                            Buka Panel
                        </button>
                    </div>
                    <div class="table-responsive" id="tableWrapper">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Peran</th>
                                    <th>Tipe Pengguna</th>
                                    <th>Status</th>
                                    <th>Jumlah Pengguna</th>
                                    <th>Menu Diakses</th>
                                    <th>Total Izin</th>
                                    <th>Terakhir Diperbarui</th>
                                    <th width="320">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="roleTable"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data hak akses tidak ditemukan.</p></div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination hak akses"><ul class="pagination mb-0" id="pagination"></ul></nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Tambah Peran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="roleNameInput">Nama Peran</label>
                        <input class="form-control" id="roleNameInput" placeholder="Nama peran">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="roleTypeInput">Tipe Pengguna</label>
                        <select class="form-select" id="roleTypeInput">
                            <option>Internal</option>
                            <option>Eksternal</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Izin Modul</label>
                        <div id="permissionEditor" class="permission-editor"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveRoleButton">Simpan Peran</button>
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

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="actionToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Tindakan berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="permissionPanel" aria-labelledby="permissionPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="permissionPanelLabel">Panel Detail Izin Akses</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card panel-card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-1">Informasi Peran</h5>
                <p class="text-muted mb-0" id="selectedRoleText">Klik tombol Detail pada tabel untuk menampilkan izin akses.</p>
            </div>
        </div>
        <div class="card panel-card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Daftar Izin</h5>
                <div id="permissionDetail"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const permissionCatalog = {
        'Super Admin': [
            { menu: 'Dashboard', items: [] },
            { menu: 'Verifikasi', items: ['Verifikasi Akun', 'Riwayat Verifikasi'] },
            { menu: 'Manajemen Pengguna', items: ['Peserta Magang', 'Mentor', 'Pembimbing Akademik'] },
            { menu: 'Manajemen Perguruan Tinggi', items: ['Data Perguruan Tinggi', 'Data Kerja Sama'] },
            { menu: 'Manajemen Magang', items: ['Dokumen Peserta', 'Absensi', 'Kegiatan Magang', 'Laporan Berkala', 'Laporan Akhir', 'Periode Magang', 'Penempatan', 'Penilaian'] },
            { menu: 'Monitoring', items: ['Rekap Absensi', 'Rekap Kegiatan', 'Statistik Pengguna', 'Statistik Perguruan Tinggi'] },
            { menu: 'Komunikasi', items: ['Pesan', 'Pengumuman', 'Notifikasi'] },
            { menu: 'Pengaturan Akun', items: ['Profil Akun', 'Ubah Password', 'Hak Akses'] }
        ],
        'Mentor': [
            { menu: 'Dashboard', items: [] },
            { menu: 'Daftar Peserta Magang', items: [] },
            { menu: 'Monitoring', items: ['Absensi', 'Penugasan', 'Logbook Harian', 'Kerjasama', 'Status Magang'] },
            { menu: 'Review', items: ['Laporan', 'Daftar Laporan', 'Riwayat Review'] },
            { menu: 'Penilaian', items: ['Input Nilai', 'Rekap Nilai'] },
            { menu: 'Komunikasi', items: ['Pesan', 'Pengumuman', 'Notifikasi'] },
            { menu: 'Pengaturan Akun', items: ['Profil', 'Ubah Password'] }
        ],
        'Peserta Magang': [
            { menu: 'Dashboard', items: [] },
            { menu: 'Data Magang', items: ['Profil Peserta', 'Penempatan', 'Status Verifikasi'] },
            { menu: 'Aktivitas Magang', items: ['Absensi', 'Logbook', 'Penugasan', 'Riwayat Kegiatan'] },
            { menu: 'Dokumen', items: ['Dokumen Kerjasama', 'Dokumen Pendukung', 'Status Dokumen'] },
            { menu: 'Laporan', items: ['Input Laporan', 'Riwayat Laporan'] },
            { menu: 'Penilaian', items: ['Rekap Nilai', 'Sertifikat'] },
            { menu: 'Komunikasi', items: ['Pesan', 'Pengumuman', 'Notifikasi'] },
            { menu: 'Pengaturan Akun', items: ['Profil Akun', 'Ubah Password'] }
        ],
        'Pembimbing Akademik': [
            { menu: 'Dashboard', items: [] },
            { menu: 'Mahasiswa Bimbingan', items: [] },
            { menu: 'Monitoring', items: ['Kegiatan Mahasiswa', 'Absensi', 'Progres Magang', 'Logbook Harian'] },
            { menu: 'Review', items: ['Review Laporan', 'Daftar Laporan', 'Riwayat Review'] },
            { menu: 'Penilaian', items: ['Input Nilai', 'Rekap Nilai'] },
            { menu: 'Komunikasi', items: ['Pesan', 'Pengumuman', 'Notifikasi'] },
            { menu: 'Pengaturan Akun', items: ['Profil', 'Ubah Password'] }
        ]
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const roleBaseUrl = @json(url('/admin/pengaturan/hak-akses'));
    const formatToday = () => new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date());
    const flattenPermissions = tree => tree.flatMap(group => group.items.length ? group.items.map(item => `${group.menu} > ${item}`) : [group.menu]);
    const countMenus = tree => tree.length;
    const uniqueParentMenus = permissions => new Set(permissions.map(permission => permission.includes(' > ') ? permission.split(' > ')[0] : permission)).size;
    const roles = @json($accessRoles ?? []);
    const permissionCatalogFromServer = @json($accessPermissionCatalog ?? []);
    const mentorUsers = @json($adminMentors ?? []);
    const advisorUsers = @json($adminAdvisors ?? []);

    let currentPage = 1;
    let activePermissionRole = roles[0];
    let editingRoleIndex = null;
    const perPage = 5;
    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    const permissionPanel = new bootstrap.Offcanvas(document.getElementById('permissionPanel'));
    const badgeMap = { aktif: 'success', nonaktif: 'secondary', review: 'warning' };

    function renderStats() {
        document.getElementById('statTotal').textContent = roles.length;
        document.getElementById('statActive').textContent = roles.filter(item => item.status === 'aktif').length;
        document.getElementById('statInactive').textContent = roles.filter(item => item.status === 'nonaktif').length;
        document.getElementById('statMenus').textContent = roles.length ? Math.max(...roles.map(item => item.menus)) : 0;
        document.getElementById('statPermissions').textContent = roles.reduce((total, item) => total + item.permissions, 0);
    }

    function permissionCheckbox(permission, checked = false) {
        const safeId = permission.toLowerCase().replace(/[^a-z0-9]+/g, '-');
        return `
            <label class="permission-check" for="${safeId}">
                <input class="form-check-input" type="checkbox" id="${safeId}" value="${permission}" ${checked ? 'checked' : ''}>
                <span class="permission-label">
                    <strong>${permission}</strong>
                    <small>Izin akses mengikuti menu dan submenu yang tersedia.</small>
                </span>
            </label>
        `;
    }

    function renderPermissionEditor(role = activePermissionRole) {
        const selectedPermissions = new Set(role.permissionsList || []);
        const editor = document.getElementById('permissionEditor');
        editor.innerHTML = (role.permissionTree || []).map(group => {
            const items = group.items.length
                ? group.items.map(item => {
                    const permission = `${group.menu} > ${item}`;
                    return permissionCheckbox(permission, selectedPermissions.has(permission));
                }).join('')
                : permissionCheckbox(group.menu, selectedPermissions.has(group.menu));

            return `
                <div class="permission-group">
                    <div class="permission-group-header">
                        <div>
                            <div class="fw-bold">${group.menu}</div>
                            <div class="small text-muted">${group.items.length ? `${group.items.length} submenu tersedia` : 'Menu utama tanpa submenu'}</div>
                        </div>
                        <span class="badge bg-light text-dark">${group.items.length ? group.items.length : 1} izin</span>
                    </div>
                    <div class="permission-checklist">${items}</div>
                </div>
            `;
        }).join('');
    }

    function renderPermissionDetail(role = activePermissionRole) {
        activePermissionRole = role;
        const identityList = role.name === 'Mentor'
            ? mentorUsers.map(item => item.nip).filter(Boolean)
            : role.name === 'Pembimbing Akademik'
                ? advisorUsers.map(item => item.nidn).filter(Boolean)
                : [];
        document.getElementById('selectedRoleText').textContent = `Detail izin untuk peran ${role.name}.`;
        document.getElementById('permissionDetail').innerHTML = `
            <div class="info-row">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>${role.name}</strong>
                    <span class="badge bg-${badgeMap[role.status]}">${role.status}</span>
                </div>
                <div class="small text-muted">${role.type} | ${role.users} pengguna | ${role.permissions} izin | ${role.menus} menu</div>
            </div>
            ${identityList.length ? `
                <div class="info-row">
                    <div class="fw-semibold mb-2">${role.name === 'Mentor' ? 'NIP Mentor' : 'NIDN/NIP Pembimbing'} Terdaftar</div>
                    <div class="d-flex flex-wrap gap-2">
                        ${identityList.map(identity => `<span class="badge bg-light text-dark border">${identity}</span>`).join('')}
                    </div>
                </div>
            ` : ''}
            ${(role.permissionsList || []).map(permission => `
                <div class="permission-item d-flex justify-content-between align-items-center">
                    <span>${permission}</span>
                    <span class="badge bg-primary">Diizinkan</span>
                </div>
            `).join('')}
        `;
    }

    function openPermissionPanel(role = activePermissionRole) {
        renderPermissionDetail(role);
        permissionPanel.show();
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const pageItems = roles.slice(start, start + perPage);
        const tableBody = document.getElementById('roleTable');
        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.type}</td>
                <td><span class="badge bg-${badgeMap[item.status]}">${item.status}</span></td>
                <td>${item.users}</td>
                <td>${item.menus}</td>
                <td>${item.permissions}</td>
                <td>${item.updated}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-primary" data-action="detail" data-index="${roles.indexOf(item)}"><i class="bi bi-eye"></i> Detail</button>
                        <button class="btn btn-sm btn-outline-secondary" data-action="edit" data-index="${roles.indexOf(item)}"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-outline-warning" data-action="toggle" data-index="${roles.indexOf(item)}"><i class="bi bi-power"></i> Aktif/Nonaktif</button>
                        <button class="btn btn-sm btn-outline-info" data-action="history" data-index="${roles.indexOf(item)}"><i class="bi bi-clock-history"></i> Riwayat</button>
                        <button class="btn btn-sm btn-outline-danger" data-action="delete" data-index="${roles.indexOf(item)}"><i class="bi bi-trash"></i> Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        const hasData = roles.length > 0;
        document.getElementById('emptyState').classList.toggle('d-none', hasData);
        document.getElementById('tableWrapper').classList.toggle('d-none', !hasData);
        document.getElementById('tableSummary').textContent = hasData ? `Menampilkan ${start + 1} sampai ${Math.min(start + perPage, roles.length)} dari ${roles.length} data peran` : 'Menampilkan 0 data';
        document.getElementById('pageInfo').textContent = hasData ? `Halaman ${currentPage} dari ${Math.ceil(roles.length / perPage)}` : 'Menampilkan 0 data';
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.max(1, Math.ceil(roles.length / perPage));
        let items = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            items += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        items += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        document.getElementById('pagination').innerHTML = items;
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
        actionButton.onclick = () => {
            callback();
            confirmModal.hide();
        };
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            showToast(`Ringkasan ${card.dataset.statusCard === 'semua' ? 'semua peran' : `status ${card.dataset.statusCard}`} ditampilkan.`, 'info');
        });
    });
    document.getElementById('pagination').addEventListener('click', event => {
        const page = Number(event.target.dataset.page);
        const totalPages = Math.max(1, Math.ceil(roles.length / perPage));
        if (!page || page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    });
    async function requestRoleMutation(url, method, payload = null) {
        const response = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: payload ? JSON.stringify(payload) : null,
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'Gagal memproses permintaan.');
        }

        return data;
    }

    function refreshPage(message = 'Perubahan berhasil disimpan.') {
        showToast(message);
        setTimeout(() => window.location.reload(), 700);
    }

    document.getElementById('roleTable').addEventListener('click', async event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const role = roles[button.dataset.index];
        const action = button.dataset.action;
        if (action === 'detail') {
            openPermissionPanel(role);
            showToast(`Detail izin ${role.name} ditampilkan.`, 'info');
            return;
        }
        if (action === 'edit') {
            document.getElementById('roleModalTitle').textContent = `Edit Hak Akses ${role.name}`;
            document.getElementById('roleNameInput').value = role.name;
            document.getElementById('roleTypeInput').value = role.type;
            document.getElementById('roleNameInput').readOnly = true;
            document.getElementById('roleTypeInput').disabled = true;
            editingRoleIndex = Number(button.dataset.index);
            renderPermissionEditor(role);
            roleModal.show();
            return;
        }
        const label = { toggle: 'mengubah status akses', history: 'membuka riwayat perubahan', delete: 'menghapus peran' }[action];
        openConfirm('Konfirmasi Hak Akses', `Apakah Anda yakin ingin ${label} ${role.name}?`, async () => {
            try {
                if (action === 'toggle') {
                    const result = await requestRoleMutation(`${roleBaseUrl}/${role.id}/status`, 'PATCH');
                    refreshPage(result.message || `Status ${role.name} berhasil diperbarui.`);
                    return;
                }
                if (action === 'delete') {
                    const result = await requestRoleMutation(`${roleBaseUrl}/${role.id}`, 'DELETE');
                    refreshPage(result.message || `${role.name} berhasil dihapus.`);
                    return;
                }
                showToast(`Riwayat perubahan ${role.name} belum disediakan dari database.`, 'info');
            } catch (error) {
                showToast(error.message, 'danger');
            }
        });
    });

    document.getElementById('addRoleButton').addEventListener('click', () => {
        document.getElementById('roleModalTitle').textContent = 'Tambah Peran';
        document.getElementById('roleNameInput').value = '';
        document.getElementById('roleTypeInput').value = 'Internal';
        document.getElementById('roleNameInput').readOnly = false;
        document.getElementById('roleTypeInput').disabled = false;
        editingRoleIndex = null;
        document.getElementById('permissionEditor').innerHTML = '<div class="alert alert-info mb-0">Role inti dibatasi 4 akun utama. Silakan edit izin pada role yang tersedia.</div>';
        roleModal.show();
    });
    document.getElementById('saveRoleButton').addEventListener('click', () => {
        if (editingRoleIndex === null) {
            roleModal.hide();
            showToast('Penambahan role baru masih dibatasi. Silakan edit role yang tersedia.', 'info');
            return;
        }
        const checkedPermissions = [...document.querySelectorAll('#permissionEditor input[type="checkbox"]:checked')].map(input => input.value);
        requestRoleMutation(`${roleBaseUrl}/${roles[editingRoleIndex].id}`, 'PATCH', {
            type: roles[editingRoleIndex].type,
            status: roles[editingRoleIndex].status,
            permissions: checkedPermissions,
        }).then(result => {
            roleModal.hide();
            refreshPage(result.message || `Izin akses ${roles[editingRoleIndex].name} berhasil diperbarui.`);
        }).catch(error => {
            showToast(error.message, 'danger');
        });
    });
    document.getElementById('openPermissionPanelButton').addEventListener('click', () => {
        openPermissionPanel();
    });
    document.getElementById('exportButton').addEventListener('click', () => {
        openConfirm('Ekspor Data Peran', 'Apakah Anda yakin ingin mengekspor data hak akses?', () => showToast('Data hak akses berhasil diekspor.'));
    });

    renderStats();
    if (roles.length) {
        renderPermissionEditor(roles[0]);
        renderPermissionDetail(roles[0]);
    }
    renderTable();
});
</script>
@endpush
