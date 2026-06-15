@extends('admin.layout.admin')

@section('title', 'Pengaturan')

@push('styles')
<style>
    .settings-page .page-title { font-weight: 700; color: #163342; }
    .settings-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .settings-page .stat-card,
    .settings-page .table-card,
    .settings-page .quick-card,
    .settings-page .status-card { border: 0; border-radius: 8px; }
    .settings-page .stat-card { cursor: pointer; transition: .2s ease; }
    .settings-page .stat-card:hover,
    .quick-menu-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .settings-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .quick-menu-card { border: 1px solid #e2ebef; border-radius: 8px; padding: 14px; height: 100%; transition: .2s ease; text-decoration: none; color: inherit; display: block; background: #fbfdfe; }
    .status-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fbfdfe; }
    .settings-page .table { font-size: 14px; }
    .settings-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .settings-page .table tbody tr:hover { background: #f8fbfd; }
    .settings-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 260px; }
    .empty-state { min-height: 190px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid settings-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Pengaturan</h2>
            <p class="text-muted mb-0">Pusat pengelolaan akun, keamanan, hak akses, aktivitas, dan konfigurasi sistem.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="auditButton"><i class="bi bi-shield-check"></i> Audit Sistem</button>
            <button class="btn btn-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Konfigurasi</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Status Akun</p><h3 class="mb-0">Aktif</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-category-card="Keamanan">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Status Keamanan</p><h3 class="mb-0">Aman</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-shield-lock"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-category-card="Hak Akses">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Hak Akses Aktif</p><h3 class="mb-0">12</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-key"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-category-card="Notifikasi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Notifikasi Sistem</p><h3 class="mb-0">8</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-bell"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-activity-card="terbaru">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktivitas Terbaru</p><h3 class="mb-0">24</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card quick-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Panel Menu Cepat</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a class="quick-menu-card" href="{{ route('admin.pengaturan.profil') }}">
                                <i class="bi bi-person-circle text-primary fs-3"></i>
                                <h6 class="mt-3 mb-1">Profil Akun</h6>
                                <p class="text-muted mb-0 small">Kelola identitas dan informasi akun pengguna.</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="quick-menu-card" href="{{ route('admin.pengaturan.password') }}">
                                <i class="bi bi-lock-fill text-success fs-3"></i>
                                <h6 class="mt-3 mb-1">Ubah Password</h6>
                                <p class="text-muted mb-0 small">Perbarui keamanan kata sandi akun.</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a class="quick-menu-card" href="{{ route('admin.pengaturan.hak-akses') }}">
                                <i class="bi bi-person-gear text-warning fs-3"></i>
                                <h6 class="mt-3 mb-1">Hak Akses</h6>
                                <p class="text-muted mb-0 small">Atur role, izin, dan akses administratif.</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card status-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Ringkasan Status Sistem</h5>
                    <div id="statusSummary"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Konfigurasi Utama</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <button class="btn btn-outline-danger" type="button" id="resetSystemButton"><i class="bi bi-arrow-counterclockwise"></i> Reset Pengaturan</button>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Submenu</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Terakhir Diperbarui</th>
                            <th>Pengguna Terakhir</th>
                            <th>Keamanan</th>
                            <th width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="settingsTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data konfigurasi tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination konfigurasi"><ul class="pagination mb-0" id="pagination"></ul></nav>
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const settings = [
        { name: 'Profil Akun', category: 'Akun', status: 'aktif', updated: '24 Mei 2026', user: 'Super Admin', security: 'Terverifikasi', activity: 'diperbarui', route: '{{ route('admin.pengaturan.profil') }}' },
        { name: 'Ubah Password', category: 'Keamanan', status: 'aktif', updated: '23 Mei 2026', user: 'Super Admin', security: 'Aman', activity: 'baru', route: '{{ route('admin.pengaturan.password') }}' },
        { name: 'Hak Akses', category: 'Hak Akses', status: 'aktif', updated: '22 Mei 2026', user: 'Admin Sistem', security: 'Perlu Review', activity: 'diperbarui', route: '{{ route('admin.pengaturan.hak-akses') }}' },
        { name: 'Notifikasi Sistem', category: 'Sistem', status: 'aktif', updated: '21 Mei 2026', user: 'Admin Sistem', security: 'Aman', activity: 'stabil', route: '#' },
        { name: 'Log Aktivitas', category: 'Keamanan', status: 'perlu audit', updated: '20 Mei 2026', user: 'Auditor', security: 'Audit Berkala', activity: 'baru', route: '#' },
        { name: 'Integrasi Sistem', category: 'Sistem', status: 'nonaktif', updated: '18 Mei 2026', user: 'Super Admin', security: 'Dibatasi', activity: 'stabil', route: '#' }
    ];

    let currentPage = 1;
    const perPage = 5;
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    const badgeMap = { aktif: 'success', 'perlu audit': 'warning', nonaktif: 'secondary' };

    function renderStatusSummary() {
        const summaries = [
            ['Akun Aktif', 'Profil dan sesi berjalan normal', 'success', 100],
            ['Keamanan', 'Password dan audit aman', 'primary', 86],
            ['Hak Akses', '12 izin aktif', 'warning', 72],
            ['Konfigurasi', '6 menu terpantau', 'info', 94]
        ];
        document.getElementById('statusSummary').innerHTML = summaries.map(item => `
            <div class="status-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div><strong>${item[0]}</strong><div class="small text-muted">${item[1]}</div></div>
                    <span class="badge bg-${item[2]}">${item[3]}%</span>
                </div>
                <div class="progress" style="height:8px">
                    <div class="progress-bar bg-${item[2]}" style="width:${item[3]}%"></div>
                </div>
            </div>
        `).join('');
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const pageItems = settings.slice(start, start + perPage);
        const tableBody = document.getElementById('settingsTable');
        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.category}</td>
                <td><span class="badge bg-${badgeMap[item.status]}">${item.status}</span></td>
                <td>${item.updated}</td>
                <td>${item.user}</td>
                <td>${item.security}</td>
                <td>
                    <div class="action-group">
                        <a class="btn btn-sm btn-outline-primary" href="${item.route}"><i class="bi bi-box-arrow-up-right"></i> Buka</a>
                        <button class="btn btn-sm btn-outline-secondary" data-action="edit" data-name="${item.name}"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-sm btn-outline-warning" data-action="audit" data-name="${item.name}"><i class="bi bi-shield-check"></i> Audit</button>
                        <button class="btn btn-sm btn-outline-danger" data-action="reset" data-name="${item.name}"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                    </div>
                </td>
            </tr>
        `).join('');

        const hasData = settings.length > 0;
        document.getElementById('emptyState').classList.toggle('d-none', hasData);
        document.getElementById('tableWrapper').classList.toggle('d-none', !hasData);
        document.getElementById('tableSummary').textContent = hasData ? `Menampilkan ${start + 1} sampai ${Math.min(start + perPage, settings.length)} dari ${settings.length} data konfigurasi` : 'Menampilkan 0 data';
        document.getElementById('pageInfo').textContent = hasData ? `Halaman ${currentPage} dari ${Math.ceil(settings.length / perPage)}` : 'Menampilkan 0 data';
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.max(1, Math.ceil(settings.length / perPage));
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

    document.getElementById('pagination').addEventListener('click', event => {
        const page = Number(event.target.dataset.page);
        const totalPages = Math.max(1, Math.ceil(settings.length / perPage));
        if (!page || page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    });
    document.getElementById('settingsTable').addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const action = button.dataset.action;
        const name = button.dataset.name;
        openConfirm('Konfirmasi Pengaturan', `Apakah Anda yakin ingin menjalankan tindakan ${action} pada ${name}?`, () => {
            showToast(`Tindakan ${action} pada ${name} berhasil diproses.`);
        });
    });
    document.getElementById('exportButton').addEventListener('click', () => {
        openConfirm('Ekspor Konfigurasi', 'Apakah Anda yakin ingin mengekspor konfigurasi sistem?', () => showToast('Konfigurasi sistem berhasil diekspor.'));
    });
    document.getElementById('auditButton').addEventListener('click', () => showToast('Audit sistem berhasil dijalankan.', 'info'));
    document.getElementById('resetSystemButton').addEventListener('click', () => {
        openConfirm('Reset Pengaturan', 'Tindakan ini akan mereset konfigurasi terpilih. Lanjutkan?', () => showToast('Reset pengaturan berhasil diproses.', 'warning'));
    });

    renderStatusSummary();
    renderTable();
});
</script>
@endpush
