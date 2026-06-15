@extends('mentor.layout.mentor')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@push('styles')
<style>
    .account-page .account-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .account-page .stat-card,
    .account-page .filter-card,
    .account-page .table-card,
    .account-page .panel-card { border:0; border-radius:8px; }
    .account-page .stat-card { cursor:pointer; transition:.2s ease; }
    .account-page .stat-card:hover { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .account-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .account-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .account-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:420px; }
    .account-page .spec-table th { width:210px; background:#fff8e8; color:#6b4a05; }
    .account-page .section-title { border-left:4px solid #2a8fbd; padding-left:12px; }
    .account-page .pagination .page-link { color:#2a8fbd; }
    .account-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $user = $user ?? auth()->user()?->loadMissing('mentor');
    $mentor = $user?->mentor;
    $profileFields = [
        $user?->name,
        $user?->username,
        $user?->email,
        optional($mentor)->nip,
        optional($mentor)->jabatan,
        optional($mentor)->divisi,
        optional($mentor)->no_hp,
        optional($mentor)->alamat,
        optional($mentor)->instansi,
    ];
    $filledProfile = collect($profileFields)->filter(fn ($value) => filled($value))->count();
    $profileCompletion = count($profileFields) ? round(($filledProfile / count($profileFields)) * 100) : 0;
    $accountStatus = $user?->account_status ?? 'menunggu';
    $verifiedAt = optional($user?->verified_at)->format('d M Y H:i') ?? 'Belum diverifikasi';
    $lastUpdated = optional($user?->updated_at)->format('d M Y H:i') ?? 'Belum ada';
    $lastLogin = optional($user?->last_login_at)->format('d M Y H:i') ?? $lastUpdated;
    $joinedAt = optional($user?->created_at)->format('d M Y') ?? '-';
    $institution = optional($mentor)->instansi ?: (optional($mentor)->divisi ?: 'LLDIKTI Wilayah V Yogyakarta');
    $settingsRows = [
        ['category' => 'Profil', 'detail' => 'Nama lengkap akun', 'value' => $user?->name ?: 'Belum diisi', 'status' => filled($user?->name) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Sistem', 'history' => 'users.name'],
        ['category' => 'Profil', 'detail' => 'Username login', 'value' => $user?->username ?: 'Belum diisi', 'status' => filled($user?->username) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Sistem', 'history' => 'users.username'],
        ['category' => 'Kontak', 'detail' => 'Email akun', 'value' => $user?->email ?: 'Belum diisi', 'status' => filled($user?->email) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Sistem', 'history' => 'users.email'],
        ['category' => 'Profesional', 'detail' => 'NIP mentor', 'value' => optional($mentor)->nip ?: 'Belum diisi', 'status' => filled(optional($mentor)->nip) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Mentor', 'history' => 'mentors.nip'],
        ['category' => 'Profesional', 'detail' => 'Jabatan', 'value' => optional($mentor)->jabatan ?: 'Belum diisi', 'status' => filled(optional($mentor)->jabatan) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Mentor', 'history' => 'mentors.jabatan'],
        ['category' => 'Penempatan', 'detail' => 'Divisi / Instansi', 'value' => $institution, 'status' => filled($institution) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Admin', 'history' => 'mentors.instansi / mentors.divisi'],
        ['category' => 'Kontak', 'detail' => 'Nomor HP', 'value' => optional($mentor)->no_hp ?: 'Belum diisi', 'status' => filled(optional($mentor)->no_hp) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Mentor', 'history' => 'mentors.no_hp'],
        ['category' => 'Kontak', 'detail' => 'Alamat', 'value' => optional($mentor)->alamat ?: 'Belum diisi', 'status' => filled(optional($mentor)->alamat) ? 'Tersimpan' : 'Perlu Update', 'date' => $lastUpdated, 'by' => 'Mentor', 'history' => 'mentors.alamat'],
        ['category' => 'Akun', 'detail' => 'Status akun', 'value' => str_replace('_', ' ', $accountStatus), 'status' => $accountStatus === 'disetujui' ? 'Aman' : 'Perlu Update', 'date' => $verifiedAt, 'by' => 'Sistem', 'history' => 'users.account_status'],
        ['category' => 'Audit', 'detail' => 'Login terakhir', 'value' => $lastLogin, 'status' => 'Tersimpan', 'date' => $lastLogin, 'by' => 'Sistem', 'history' => 'users.last_login_at'],
    ];
@endphp
<div class="container-fluid account-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengaturan Akun</li>
        </ol>
    </nav>

    <section class="account-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Pengaturan Akun Mentor</h3>
                <p class="mb-0">Kelola profil, keamanan, preferensi sistem, dan histori perubahan akun mentor secara terpusat.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a class="btn btn-light me-2" href="{{ route('mentor.pengaturan.profil') }}"><i class="bi bi-person"></i> Profil</a>
                <a class="btn btn-dark" href="{{ route('mentor.pengaturan.password') }}"><i class="bi bi-shield-lock"></i> Password</a>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-gear-fill"></i>
        <div>Profil mentor {{ $profileCompletion }}% lengkap, status akun {{ str_replace('_', ' ', $accountStatus) }}, dan data terakhir diperbarui pada {{ $lastUpdated }}.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Kelengkapan Profil</p><h3 class="mb-0">{{ $profileCompletion }}%</h3></div><span class="stat-icon bg-primary"><i class="bi bi-person-check"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Status Akun</p><h3 class="mb-0 fs-5 text-capitalize">{{ str_replace('_', ' ', $accountStatus) }}</h3></div><span class="stat-icon bg-success"><i class="bi bi-check-circle"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Aktivitas Terakhir</p><h3 class="mb-0 fs-6">{{ $lastUpdated }}</h3></div><span class="stat-icon bg-info"><i class="bi bi-clock-history"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Bergabung</p><h3 class="mb-0">{{ $joinedAt }}</h3></div><span class="stat-icon bg-dark"><i class="bi bi-laptop"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Terverifikasi</p><h3 class="mb-0 fs-5">{{ $verifiedAt !== 'Belum diverifikasi' ? 'Ya' : 'Proses' }}</h3></div><span class="stat-icon bg-warning"><i class="bi bi-shield-check"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Data Terisi</p><h3 class="mb-0 fs-5">{{ $filledProfile }} / {{ count($profileFields) }}</h3></div><span class="stat-icon bg-danger"><i class="bi bi-arrow-repeat"></i></span>
            </div></div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori Akun</label>
                    <select class="form-select" id="categoryFilter"><option value="semua">Semua</option><option>Profil</option><option>Keamanan</option><option>Preferensi</option><option>Notifikasi</option></select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label" for="securityFilter">Keamanan</label>
                    <select class="form-select" id="securityFilter"><option value="semua">Semua</option><option>Aman</option><option>Perlu Update</option><option>Aktif</option></select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label" for="preferenceFilter">Preferensi</label>
                    <select class="form-select" id="preferenceFilter"><option value="semua">Semua</option><option>Email</option><option>Dashboard</option><option>Privasi</option></select>
                </div>
                <div class="col-lg-12 d-flex gap-2">
                    <button class="btn btn-warning flex-fill" type="button" id="saveButton"><i class="bi bi-save"></i> Simpan</button>
                    <button class="btn btn-outline-secondary flex-fill" type="button" id="resetButton"><i class="bi bi-arrow-counterclockwise"></i> Default</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card table-card shadow-sm">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div><h5 class="mb-0">Konfigurasi dan Histori Pengaturan</h5><small class="text-muted" id="tableInfo">Menampilkan data pengaturan</small></div>
                    <select class="form-select form-select-sm" id="perPageSelect" style="width:80px"><option value="5">5</option><option value="10">10</option></select>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead><tr><th>No</th><th>Kategori Pengaturan</th><th>Detail Pengaturan</th><th>Nilai Saat Ini</th><th>Status Perubahan</th><th>Tanggal Perubahan</th><th>Diubah Oleh</th><th>Riwayat</th><th>Aksi</th></tr></thead>
                            <tbody id="settingsTableBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card panel-card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Ringkasan Profil</h5>
                    <div class="info-row"><small class="text-muted">Nama Mentor</small><div class="fw-bold">{{ $user->name ?? 'Mentor Lapangan' }}</div></div>
                    <div class="info-row"><small class="text-muted">Email</small><div class="fw-semibold">{{ $user->email ?? 'mentor@lldikti5.id' }}</div></div>
                    <div class="info-row"><small class="text-muted">Peran</small><div class="fw-semibold">Mentor</div></div>
                </div>
            </div>
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Shortcut Akun</h5>
                    <div class="d-grid gap-2">
                        <a class="btn btn-warning text-start" href="{{ route('mentor.pengaturan.profil') }}"><i class="bi bi-person me-2"></i> Edit Profil</a>
                        <a class="btn btn-outline-warning text-start" href="{{ route('mentor.pengaturan.password') }}"><i class="bi bi-shield-lock me-2"></i> Ubah Password</a>
                        <a class="btn btn-outline-warning text-start" href="{{ route('mentor.pengaturan.profil') }}#auditTab"><i class="bi bi-clock-history me-2"></i> Lihat Aktivitas</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-danger text-start w-100" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Keluar Akun</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pengaturan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body" id="confirmText">Simpan perubahan pengaturan akun?</div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-warning" id="confirmActionButton">Simpan</button></div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="accountToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex"><div class="toast-body" id="toastMessage">Pengaturan akun diperbarui.</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = @json($settingsRows);
    const profileUrl = @json(route('mentor.pengaturan.profil'));
    const passwordUrl = @json(route('mentor.pengaturan.password'));
    let filtered = [...rows];
    let currentPage = 1;
    let perPage = 5;
    const tableBody = document.getElementById('settingsTableBody');
    const pagination = document.getElementById('pagination');
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('accountToast'));
    const showToast = message => { document.getElementById('toastMessage').textContent = message; toast.show(); };
    const badge = status => ({ 'Tersimpan':'success', 'Perlu Update':'warning text-dark', 'Aman':'info' }[status] || 'secondary');
    const tabForCategory = category => {
        if (category === 'Profesional') return 'professionalTab';
        if (category === 'Kontak') return 'contactTab';
        if (category === 'Audit') return 'auditTab';
        return 'identityTab';
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);
        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.category}</td>
                <td>${item.detail}</td>
                <td>${item.value}</td>
                <td><span class="badge bg-${badge(item.status)}">${item.status}</span></td>
                <td>${item.date}</td>
                <td>${item.by}</td>
                <td>${item.history}</td>
                <td><div class="action-group"><button class="btn btn-sm btn-warning" data-row-action="detail">Detail</button><button class="btn btn-sm btn-outline-warning" data-row-action="ubah">Ubah</button><button class="btn btn-sm btn-outline-secondary" data-row-action="histori">Histori</button></div></td>
            </tr>
        `).join('');
        document.getElementById('tableInfo').textContent = `Menampilkan ${filtered.length} dari ${rows.length} pengaturan`;
        document.getElementById('paginationInfo').textContent = filtered.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, filtered.length)} dari ${filtered.length} data` : 'Menampilkan 0 data';
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    };

    const applyFilters = () => {
        const category = document.getElementById('categoryFilter').value;
        const security = document.getElementById('securityFilter').value;
        const preference = document.getElementById('preferenceFilter').value;
        filtered = rows.filter(item => {
            return (category === 'semua' || item.category === category)
                && (security === 'semua' || item.status === security || item.value.includes(security))
                && (preference === 'semua' || item.detail.includes(preference) || item.value.includes(preference));
        });
        currentPage = 1;
        renderTable();
    };

    document.querySelectorAll('#categoryFilter,#securityFilter,#preferenceFilter').forEach(input => input.addEventListener('change', applyFilters));
    document.getElementById('perPageSelect').addEventListener('change', event => { perPage = Number(event.target.value); currentPage = 1; renderTable(); });
    pagination.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });
    document.getElementById('settingsTableBody').addEventListener('click', event => {
        const button = event.target.closest('button[data-row-action]');
        if (!button) return;
        const row = filtered.find(item => item.detail === button.closest('tr')?.querySelector('td:nth-child(3)')?.textContent) || null;
        const rowIndex = Array.from(tableBody.querySelectorAll('tr')).indexOf(button.closest('tr'));
        const item = rowIndex >= 0 ? filtered[(currentPage - 1) * perPage + rowIndex] : null;

        if (button.dataset.rowAction === 'histori') {
            window.location.href = `${profileUrl}#auditTab`;
            return;
        }

        if (button.dataset.rowAction === 'ubah') {
            if (item?.category === 'Akun') {
                window.location.href = passwordUrl;
                return;
            }
            window.location.href = `${profileUrl}#${tabForCategory(item?.category || 'Identitas')}`;
            return;
        }

        window.location.href = `${profileUrl}#${tabForCategory(item?.category || 'Identitas')}`;
    });
    document.getElementById('saveButton').addEventListener('click', () => {
        window.location.href = `${profileUrl}#identityTab`;
    });
    document.getElementById('resetButton').addEventListener('click', () => {
        document.getElementById('categoryFilter').value = 'semua';
        document.getElementById('securityFilter').value = 'semua';
        document.getElementById('preferenceFilter').value = 'semua';
        currentPage = 1;
        applyFilters();
        showToast('Filter pengaturan berhasil direset.');
    });
    document.getElementById('confirmActionButton').addEventListener('click', () => { modal.hide(); showToast('Aksi pengaturan akun berhasil diproses.'); });
    applyFilters();
});
</script>
@endpush
