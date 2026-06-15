@extends('pembimbing.layout.pembimbing')

@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@push('styles')
<style>
    .password-page .security-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .password-page .stat-card,
    .password-page .filter-card,
    .password-page .table-card,
    .password-page .security-panel { border:0; border-radius:8px; }
    .password-page .stat-card { cursor:pointer; transition:.2s ease; }
    .password-page .stat-card:hover,
    .password-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .password-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .password-page .security-row,
    .password-page .device-row,
    .password-page .login-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .password-page .table { font-size:14px; }
    .password-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .password-page .table tbody tr:hover { background:#f7fcfe; }
    .password-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:410px; }
    .password-page .empty-state { min-height:180px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .password-page .pagination .page-link { color:#2a8fbd; }
    .password-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $lastUpdated = optional($user->updated_at)->format('d M Y H:i') ?? 'Belum ada';
@endphp

<div class="container-fluid password-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.pengaturan') }}">Pengaturan Akun</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah Password</li>
        </ol>
    </nav>

    <section class="security-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Keamanan Akun</h3>
                <p class="mb-0">Kelola password secara mandiri, pantau perangkat aktif, dan tinjau histori keamanan agar akses sistem tetap terlindungi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="securityBadge">Aman</span>
                <button class="btn btn-light" type="button" id="refreshButton"><i class="bi bi-arrow-clockwise"></i> Perbarui</button>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger mb-4">
            <strong>Password belum bisa disimpan.</strong>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Status Keamanan</p><h5 class="mb-0">Aman</h5></div>
                    <span class="stat-icon bg-success"><i class="bi bi-shield-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kekuatan</p><h5 class="mb-0" id="strengthStat">Belum Diisi</h5></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-key-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terakhir Ubah</p><h6 class="mb-0">{{ $lastUpdated }}</h6></div>
                    <span class="stat-icon bg-info"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Login Aktif</p><h3 class="mb-0">2</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-laptop"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Login Bulan Ini</p><h3 class="mb-0">18</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-activity"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Perlindungan</p><h5 class="mb-0">Aktif</h5></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-lock-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <div class="card security-panel shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Formulir Ubah Password</h5>
                    <small class="text-muted">Password akan divalidasi dan disimpan terenkripsi.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pembimbing.pengaturan.update') }}" id="passwordForm">
                        @csrf
                        <input type="hidden" name="action_type" value="password">
                        <div class="mb-3">
                            <label class="form-label" for="current_password">Password Lama</label>
                            <div class="input-group">
                                <input class="form-control password-field" id="current_password" name="current_password" type="password" autocomplete="current-password">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password Baru</label>
                            <div class="input-group">
                                <input class="form-control password-field" id="password" name="password" type="password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input class="form-control password-field" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="security-row">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>Tingkat Keamanan Password</strong>
                                <span id="strengthLabel">Belum diisi</span>
                            </div>
                            <div class="progress" style="height:10px">
                                <div class="progress-bar" id="strengthBar" style="width:0%"></div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan Password</button>
                            <button class="btn btn-outline-secondary" type="reset" id="resetFormButton"><i class="bi bi-arrow-counterclockwise"></i> Reset Formulir</button>
                            <button class="btn btn-outline-danger" type="button" id="logoutDevicesButton"><i class="bi bi-box-arrow-right"></i> Logout Semua Perangkat</button>
                            <button class="btn btn-outline-success" type="button" id="extraVerifyButton"><i class="bi bi-shield-plus"></i> Verifikasi Tambahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card security-panel shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Aktivitas Login</h5>
                    <div id="loginActivity"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card security-panel shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Perangkat Aktif</h5>
                    <div id="activeDevices"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori Keamanan</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Password">Password</option>
                        <option value="Login">Login</option>
                        <option value="Perangkat">Perangkat</option>
                        <option value="Verifikasi">Verifikasi</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="deviceFilter">Perangkat</label>
                    <select class="form-select" id="deviceFilter">
                        <option value="semua">Semua Perangkat</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Mobile">Mobile</option>
                        <option value="Browser">Browser</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="statusFilter">Status Login</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aman">Aman</option>
                        <option value="aktif">Aktif</option>
                        <option value="perlu dicek">Perlu Dicek</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Aktivitas</label>
                    <select class="form-select" id="dateFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="Hari Ini">Hari Ini</option>
                        <option value="Minggu Ini">Minggu Ini</option>
                        <option value="Bulan Ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Histori Keamanan Akun</h5>
                <small class="text-muted" id="tableInfo">Menampilkan aktivitas keamanan</small>
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
                            <th>Jenis Aktivitas</th>
                            <th>Perangkat</th>
                            <th>Lokasi Login</th>
                            <th>Waktu Aktivitas</th>
                            <th>Status Keamanan</th>
                            <th>Status Verifikasi</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="securityTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada histori keamanan sesuai filter.</p></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination keamanan"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Keamanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Informasi Logout Perangkat</label>
                <textarea class="form-control" id="confirmNote" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Keamanan akun diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const activities = [
        { id:1, jenis:'Password', perangkat:'Browser', lokasi:'Yogyakarta, Indonesia', waktu:'{{ $lastUpdated }}', keamanan:'aman', verifikasi:'terverifikasi', terakhir:'Perubahan password', periode:'Hari Ini' },
        { id:2, jenis:'Login', perangkat:'Laptop', lokasi:'Yogyakarta, Indonesia', waktu:'25 Mei 2026 08:10', keamanan:'aktif', verifikasi:'terverifikasi', terakhir:'Login dashboard', periode:'Hari Ini' },
        { id:3, jenis:'Perangkat', perangkat:'Mobile', lokasi:'Sleman, Indonesia', waktu:'24 Mei 2026 19:20', keamanan:'aktif', verifikasi:'terverifikasi', terakhir:'Sesi mobile aktif', periode:'Minggu Ini' },
        { id:4, jenis:'Verifikasi', perangkat:'Browser', lokasi:'Yogyakarta, Indonesia', waktu:'24 Mei 2026 10:05', keamanan:'aman', verifikasi:'terverifikasi', terakhir:'Verifikasi akun', periode:'Minggu Ini' },
        { id:5, jenis:'Login', perangkat:'Browser', lokasi:'Unknown', waktu:'22 Mei 2026 21:30', keamanan:'perlu dicek', verifikasi:'perlu validasi', terakhir:'Percobaan login', periode:'Bulan Ini' },
        { id:6, jenis:'Password', perangkat:'Laptop', lokasi:'Yogyakarta, Indonesia', waktu:'20 Mei 2026 09:00', keamanan:'aman', verifikasi:'terverifikasi', terakhir:'Reset formulir keamanan', periode:'Bulan Ini' }
    ];

    const tableBody = document.getElementById('securityTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const categoryFilter = document.getElementById('categoryFilter');
    const deviceFilter = document.getElementById('deviceFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const passwordInput = document.getElementById('password');
    const strengthLabel = document.getElementById('strengthLabel');
    const strengthBar = document.getElementById('strengthBar');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let pendingAction = null;

    const statusBadge = (value) => {
        const map = { aman:'success', aktif:'primary', 'perlu dicek':'warning', terverifikasi:'success', 'perlu validasi':'warning' };
        return `<span class="badge bg-${map[value] || 'secondary'} text-capitalize">${value}</span>`;
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function passwordStrength(value) {
        let score = 0;
        if (value.length >= 8) score += 25;
        if (/[A-Z]/.test(value)) score += 25;
        if (/[0-9]/.test(value)) score += 25;
        if (/[^A-Za-z0-9]/.test(value)) score += 25;
        return score;
    }

    function updateStrength() {
        const score = passwordStrength(passwordInput.value);
        const label = score >= 75 ? 'Kuat' : (score >= 50 ? 'Sedang' : (score > 0 ? 'Lemah' : 'Belum diisi'));
        const color = score >= 75 ? 'bg-success' : (score >= 50 ? 'bg-warning' : 'bg-danger');
        strengthLabel.textContent = label;
        document.getElementById('strengthStat').textContent = label;
        strengthBar.className = `progress-bar ${score === 0 ? '' : color}`;
        strengthBar.style.width = `${score}%`;
    }

    function filteredData() {
        return activities.filter((item) => {
            const categoryMatch = categoryFilter.value === 'semua' || item.jenis === categoryFilter.value;
            const deviceMatch = deviceFilter.value === 'semua' || item.perangkat === deviceFilter.value;
            const statusMatch = statusFilter.value === 'semua' || item.keamanan === statusFilter.value;
            const dateMatch = dateFilter.value === 'semua' || item.periode === dateFilter.value;
            return categoryMatch && deviceMatch && statusMatch && dateMatch;
        });
    }

    function renderPanels() {
        document.getElementById('loginActivity').innerHTML = activities.filter((item) => item.jenis === 'Login').map((item) => `
            <div class="login-row">
                <strong>${item.perangkat}</strong>
                <small class="text-muted d-block">${item.lokasi}</small>
                <small>${item.waktu}</small>
            </div>
        `).join('');
        document.getElementById('activeDevices').innerHTML = activities.filter((item) => item.keamanan === 'aktif').map((item) => `
            <div class="device-row">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.perangkat}</strong>
                    ${statusBadge(item.keamanan)}
                </div>
                <small class="text-muted d-block">${item.lokasi}</small>
                <small>${item.terakhir}</small>
            </div>
        `).join('');
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}" type="button">${page}</button></li>`;
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
                <td><strong>${item.jenis}</strong></td>
                <td>${item.perangkat}</td>
                <td>${item.lokasi}</td>
                <td>${item.waktu}</td>
                <td>${statusBadge(item.keamanan)}</td>
                <td>${statusBadge(item.verifikasi)}</td>
                <td>${item.terakhir}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="detail" data-id="${item.id}">Lihat Aktivitas</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="logout semua perangkat" data-id="${item.id}">Logout Perangkat</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="verifikasi tambahan" data-id="${item.id}">Verifikasi</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="reset formulir" data-id="${item.id}">Reset</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} aktivitas keamanan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderPanels();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div><strong>${item.jenis}</strong><small class="text-muted d-block">${item.perangkat} - ${item.lokasi}</small></div>
                    ${statusBadge(item.keamanan)}
                </div>
                <hr>
                <small class="text-muted">Tindakan</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = 'Perangkat aktif dapat diminta login ulang setelah perubahan keamanan.';
        confirmModal.show();
    }

    document.querySelectorAll('.toggle-password').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
            button.querySelector('i').className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    });

    passwordInput.addEventListener('input', updateStrength);
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter keamanan berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        categoryFilter.value = 'semua';
        deviceFilter.value = 'semua';
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        renderTable();
        showToast('Filter keamanan berhasil direset.', 'info');
    });
    [categoryFilter, deviceFilter, statusFilter, dateFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = activities.find((activity) => activity.id === Number(button.dataset.id));
        openConfirm(item, button.dataset.action);
    });
    document.getElementById('logoutDevicesButton').addEventListener('click', () => openConfirm(activities[1], 'logout semua perangkat'));
    document.getElementById('extraVerifyButton').addEventListener('click', () => openConfirm(activities[3], 'aktifkan verifikasi tambahan'));
    document.getElementById('resetFormButton').addEventListener('click', () => setTimeout(updateStrength, 0));
    document.getElementById('confirmAction').addEventListener('click', () => {
        confirmModal.hide();
        showToast(`Aksi ${pendingAction?.action || 'keamanan'} berhasil dicatat.`);
        pendingAction = null;
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('securityBadge').textContent = 'Diperbarui';
        showToast('Data keamanan akun berhasil diperbarui.', 'info');
    });

    renderTable();
    updateStrength();
    setTimeout(() => showToast('Status keamanan akun aktif dan tersinkron.', 'success'), 800);
});
</script>
@endpush
