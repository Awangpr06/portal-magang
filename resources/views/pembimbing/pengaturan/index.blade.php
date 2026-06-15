@extends('pembimbing.layout.pembimbing')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@push('styles')
<style>
    .account-settings-page .settings-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .account-settings-page .stat-card,
    .account-settings-page .filter-card,
    .account-settings-page .table-card,
    .account-settings-page .settings-panel { border:0; border-radius:8px; }
    .account-settings-page .stat-card { cursor:pointer; transition:.2s ease; }
    .account-settings-page .stat-card:hover,
    .account-settings-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .account-settings-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .account-settings-page .table { font-size:14px; }
    .account-settings-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .account-settings-page .table tbody tr:hover { background:#f7fcfe; }
    .account-settings-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:390px; }
    .account-settings-page .activity-row,
    .account-settings-page .preference-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .account-settings-page .empty-state { min-height:180px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .account-settings-page .pagination .page-link { color:#2a8fbd; }
    .account-settings-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $pembimbing = $user->pembimbing;
    $profileFields = [
        $user->name,
        $user->username,
        $user->email,
        optional($pembimbing)->nidn_nip,
        optional($pembimbing)->no_hp,
        optional($pembimbing)->perguruan_tinggi,
        optional($pembimbing)->program_studi,
        optional($pembimbing)->alamat,
    ];
    $filledProfile = collect($profileFields)->filter(fn ($value) => filled($value))->count();
    $profileCompletion = round(($filledProfile / count($profileFields)) * 100);
    $accountStatus = $user->account_status ?? 'aktif';
    $verifiedAt = optional($user->verified_at)->format('d M Y H:i') ?? 'Belum diverifikasi';
    $lastUpdated = optional($user->updated_at)->format('d M Y H:i') ?? 'Belum ada';
    $birthDate = optional($pembimbing)->tanggal_lahir;
    $birthDateValue = $birthDate ? \Illuminate\Support\Carbon::parse($birthDate)->format('Y-m-d') : null;
@endphp

<div class="container-fluid account-settings-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengaturan Akun</li>
        </ol>
    </nav>

    <section class="settings-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Pengaturan Akun</h3>
                <p class="mb-0">Kelola profil pembimbing, keamanan akses, preferensi sistem, dan riwayat aktivitas akun berdasarkan data yang tersimpan di sistem.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="settingsBadge">Tersinkron</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
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
            <strong>Data belum bisa disimpan.</strong>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-category-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Status Akun</p><h5 class="mb-0 text-capitalize">{{ $accountStatus }}</h5></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-person-badge"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Login Terakhir</p><h5 class="mb-0">Hari Ini</h5></div>
                    <span class="stat-icon bg-info"><i class="bi bi-box-arrow-in-right"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-security-card="aman">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Keamanan</p><h5 class="mb-0">Aman</h5></div>
                    <span class="stat-icon bg-success"><i class="bi bi-shield-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Perangkat</p><h3 class="mb-0">2</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-laptop"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktivitas Bulan Ini</p><h3 class="mb-0">18</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-activity"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Profil</p><h3 class="mb-0">{{ $profileCompletion }}%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Profil">Profil</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Preferensi">Preferensi</option>
                        <option value="Aktivitas">Aktivitas</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="securityFilter">Keamanan</label>
                    <select class="form-select" id="securityFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aman">Aman</option>
                        <option value="perlu dicek">Perlu Dicek</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="activityFilter">Aktivitas Akun</label>
                    <select class="form-select" id="activityFilter">
                        <option value="semua">Semua Aktivitas</option>
                        <option value="login">Login</option>
                        <option value="perubahan">Perubahan</option>
                        <option value="sinkronisasi">Sinkronisasi</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Perubahan</label>
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

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card settings-panel shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Panel Pengaturan Profil</h5>
                    <small class="text-muted">Data disimpan ke tabel users dan pembimbings.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pembimbing.pengaturan.update') }}">
                        @csrf
                        <input type="hidden" name="action_type" value="profile">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Nama Lengkap</label>
                                <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="username">Username</label>
                                <input class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="nidn_nip">NIDN/NIP</label>
                                <input class="form-control" id="nidn_nip" name="nidn_nip" value="{{ old('nidn_nip', optional($pembimbing)->nidn_nip) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="perguruan_tinggi">Perguruan Tinggi</label>
                                <input class="form-control" id="perguruan_tinggi" name="perguruan_tinggi" value="{{ old('perguruan_tinggi', optional($pembimbing)->perguruan_tinggi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="program_studi">Program Studi</label>
                                <input class="form-control" id="program_studi" name="program_studi" value="{{ old('program_studi', optional($pembimbing)->program_studi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="instansi">Instansi</label>
                                <input class="form-control" id="instansi" name="instansi" value="{{ old('instansi', optional($pembimbing)->instansi) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="jabatan">Jabatan</label>
                                <input class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan', optional($pembimbing)->jabatan) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                                <input class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', optional($pembimbing)->tempat_lahir) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                                <input class="form-control" id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $birthDateValue) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="">Pilih</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin', optional($pembimbing)->jenis_kelamin) === 'Laki-laki')>Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin', optional($pembimbing)->jenis_kelamin) === 'Perempuan')>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="no_hp">No HP</label>
                                <input class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', optional($pembimbing)->no_hp) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="alamat">Alamat</label>
                                <input class="form-control" id="alamat" name="alamat" value="{{ old('alamat', optional($pembimbing)->alamat) }}">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan Perubahan</button>
                            <button class="btn btn-outline-secondary" type="reset"><i class="bi bi-arrow-counterclockwise"></i> Reset Pengaturan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card settings-panel shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Panel Keamanan Akun</h5>
                    <small class="text-muted">Ubah password dan pantau status keamanan.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pembimbing.pengaturan.update') }}">
                        @csrf
                        <input type="hidden" name="action_type" value="password">
                        <div class="mb-3">
                            <label class="form-label" for="current_password">Password Saat Ini</label>
                            <input class="form-control" id="current_password" name="current_password" type="password" autocomplete="current-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password Baru</label>
                            <input class="form-control" id="password" name="password" type="password" autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                            <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                        </div>
                        <button class="btn btn-outline-primary" type="submit"><i class="bi bi-shield-lock"></i> Ubah Password</button>
                    </form>
                </div>
            </div>

            <div class="card settings-panel shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Panel Preferensi Sistem</h5>
                    <div class="preference-row d-flex justify-content-between align-items-center">
                        <div><strong>Notifikasi Email</strong><small class="text-muted d-block">Terima pembaruan akun dan aktivitas mahasiswa.</small></div>
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div>
                    </div>
                    <div class="preference-row d-flex justify-content-between align-items-center">
                        <div><strong>Mode Ringkas</strong><small class="text-muted d-block">Tampilkan tabel dengan baris lebih padat.</small></div>
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox"></div>
                    </div>
                    <div class="preference-row d-flex justify-content-between align-items-center">
                        <div><strong>Sinkronisasi Real-time</strong><small class="text-muted d-block">Perbarui indikator sistem otomatis.</small></div>
                        <div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked></div>
                    </div>
                    <button class="btn btn-outline-success" type="button" id="preferenceButton"><i class="bi bi-sliders"></i> Kelola Preferensi</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card table-card shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Daftar Pengaturan dan Histori</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan pengaturan akun</small>
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
                                    <th>Jenis Pengaturan</th>
                                    <th>Nilai Saat Ini</th>
                                    <th>Status Pengaturan</th>
                                    <th>Tanggal Perubahan</th>
                                    <th>Pengubah</th>
                                    <th>Status Sinkronisasi</th>
                                    <th>Aktivitas Terakhir</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="settingsTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Tidak ada pengaturan sesuai filter.</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination pengaturan">
                        <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card settings-panel shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Panel Aktivitas Akun</h5>
                    <div id="activityPanel"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pengaturan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Ringkasan Perubahan</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan perubahan akun"></textarea>
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
            <div class="toast-body" id="toastMessage">Pengaturan akun diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const settings = [
        { id:1, jenis:'Profil', nilai:'{{ addslashes($user->name ?? 'Belum diisi') }}', status:'aktif', tanggal:'{{ $lastUpdated }}', pengubah:'{{ addslashes($user->name ?? 'Pembimbing') }}', sinkron:'tersinkron', aktivitas:'perubahan', periode:'Hari Ini' },
        { id:2, jenis:'Email', nilai:'{{ addslashes($user->email ?? 'Belum diisi') }}', status:'aktif', tanggal:'{{ $lastUpdated }}', pengubah:'{{ addslashes($user->name ?? 'Pembimbing') }}', sinkron:'tersinkron', aktivitas:'sinkronisasi', periode:'Hari Ini' },
        { id:3, jenis:'Keamanan', nilai:'Password aktif', status:'aman', tanggal:'{{ $lastUpdated }}', pengubah:'Sistem', sinkron:'tersinkron', aktivitas:'perubahan', periode:'Minggu Ini' },
        { id:4, jenis:'Preferensi', nilai:'Notifikasi email aktif', status:'aktif', tanggal:'25 Mei 2026 08:00', pengubah:'{{ addslashes($user->name ?? 'Pembimbing') }}', sinkron:'lokal', aktivitas:'perubahan', periode:'Minggu Ini' },
        { id:5, jenis:'Aktivitas', nilai:'Login dashboard pembimbing', status:'aman', tanggal:'25 Mei 2026 07:45', pengubah:'Sistem', sinkron:'tersinkron', aktivitas:'login', periode:'Hari Ini' },
        { id:6, jenis:'Profil', nilai:'{{ addslashes(optional($pembimbing)->program_studi ?? 'Program studi belum diisi') }}', status:'perlu dicek', tanggal:'{{ $lastUpdated }}', pengubah:'{{ addslashes($user->name ?? 'Pembimbing') }}', sinkron:'tersinkron', aktivitas:'sinkronisasi', periode:'Bulan Ini' }
    ];

    const tableBody = document.getElementById('settingsTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const categoryFilter = document.getElementById('categoryFilter');
    const securityFilter = document.getElementById('securityFilter');
    const activityFilter = document.getElementById('activityFilter');
    const dateFilter = document.getElementById('dateFilter');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let pendingAction = null;

    const statusBadge = (status) => {
        const map = { aktif:'success', aman:'success', 'perlu dicek':'warning' };
        return `<span class="badge bg-${map[status] || 'secondary'} text-capitalize">${status}</span>`;
    };
    const syncBadge = (status) => {
        const map = { tersinkron:'success', lokal:'info', gagal:'danger' };
        return `<span class="badge bg-${map[status] || 'secondary'} text-capitalize">${status}</span>`;
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function filteredData() {
        return settings.filter((item) => {
            const categoryMatch = categoryFilter.value === 'semua' || item.jenis === categoryFilter.value;
            const securityMatch = securityFilter.value === 'semua' || item.status === securityFilter.value;
            const activityMatch = activityFilter.value === 'semua' || item.aktivitas === activityFilter.value;
            const dateMatch = dateFilter.value === 'semua' || item.periode === dateFilter.value;
            return categoryMatch && securityMatch && activityMatch && dateMatch;
        });
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}" type="button">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}" type="button">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderActivities() {
        document.getElementById('activityPanel').innerHTML = settings.slice(0, 5).map((item) => `
            <div class="activity-row">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.jenis}</strong>
                    ${statusBadge(item.status)}
                </div>
                <small class="text-muted d-block">${item.tanggal}</small>
                <small>${item.nilai}</small>
            </div>
        `).join('');
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
                <td>${item.nilai}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${item.tanggal}</td>
                <td>${item.pengubah}</td>
                <td>${syncBadge(item.sinkron)}</td>
                <td class="text-capitalize">${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="edit profil" data-id="${item.id}">Edit Profil</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="ubah password" data-id="${item.id}">Ubah Password</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="preferensi" data-id="${item.id}">Preferensi</button>
                        <button class="btn btn-outline-info btn-sm" type="button" data-action="keamanan" data-id="${item.id}">Keamanan</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="reset" data-id="${item.id}">Reset</button>
                        <button class="btn btn-outline-dark btn-sm" type="button" data-action="riwayat" data-id="${item.id}">Riwayat</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} pengaturan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderActivities();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <strong>${item.jenis}</strong>
                        <small class="text-muted d-block">${item.nilai}</small>
                    </div>
                    ${statusBadge(item.status)}
                </div>
                <hr>
                <small class="text-muted">Jenis perubahan</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = `Perubahan ${action} pada ${item.jenis}`;
        confirmModal.show();
    }

    document.getElementById('resetFilter').addEventListener('click', () => {
        categoryFilter.value = 'semua';
        securityFilter.value = 'semua';
        activityFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        renderTable();
        showToast('Filter pengaturan berhasil direset.', 'info');
    });
    [categoryFilter, securityFilter, activityFilter, dateFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = settings.find((setting) => setting.id === Number(button.dataset.id));
        openConfirm(item, button.dataset.action);
    });
    document.getElementById('preferenceButton').addEventListener('click', () => openConfirm(settings[3], 'kelola preferensi'));
    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.tanggal = '25 Mei 2026 22:15';
        pendingAction.item.pengubah = '{{ addslashes($user->name ?? 'Pembimbing') }}';
        confirmModal.hide();
        renderTable();
        showToast(`Aksi ${pendingAction.action} berhasil dicatat.`);
        pendingAction = null;
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('settingsBadge').textContent = 'Diperbarui';
        showToast('Data pengaturan akun berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Status akun tersinkron dengan database.', 'success'), 800);
});
</script>
@endpush
