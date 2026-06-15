@extends('admin.layout.admin')

@section('title', 'Profil Akun')

@push('styles')
<style>
    .profile-page .page-title { font-weight: 700; color: #163342; }
    .profile-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .profile-page .stat-card,
    .profile-page .profile-card,
    .profile-page .form-card,
    .profile-page .filter-card,
    .profile-page .table-card,
    .profile-page .security-card { border: 0; border-radius: 8px; }
    .profile-page .profile-card .card-body,
    .profile-page .form-card .card-body,
    .profile-page .security-card .card-body,
    .profile-page .table-card .card-body { padding: 0.65rem 0.8rem; }
    .profile-page .profile-card .card-body { padding: 0.55rem 0.7rem; }
    .profile-page .stat-card { cursor: pointer; transition: .2s ease; }
    .profile-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .profile-page .stat-card .card-body { padding: 0.55rem 0.7rem; }
    .profile-page .stat-icon { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .profile-photo { width: 76px; height: 76px; border-radius: 50%; object-fit: cover; border: 3px solid #e7f1f6; background: #f5f9fb; }
    .profile-initial { width: 76px; height: 76px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 26px; font-weight: 700; color: #0b5f86; border: 3px solid #e7f1f6; background: #f5f9fb; }
    .profile-page .stat-card h3 { font-size: 1.25rem; margin-bottom: 0; }
    .profile-page .stat-card h4 { font-size: 1rem; margin-bottom: 0; }
    .profile-page .page-title { font-size: 1.6rem; }
    .security-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 9px; margin-bottom: 8px; background: #fbfdfe; }
    .profile-page .table { font-size: 13px; }
    .profile-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 12px; white-space: nowrap; padding-top: 0.55rem; padding-bottom: 0.55rem; }
    .profile-page .table tbody tr:hover { background: #f8fbfd; }
    .profile-page .table tbody td { padding-top: 0.55rem; padding-bottom: 0.55rem; }
    .profile-page .action-group { display: flex; flex-wrap: wrap; gap: 4px; min-width: 140px; }
    .empty-state { min-height: 100px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $displayName = $user->name ?? 'Super Admin';
    $displayEmail = $user->email ?? 'admin@lldikti5.id';
    $initial = strtoupper(substr($displayName, 0, 1));
    $avatarUrl = $user->avatar_url;
    $securityDevices = collect($profileHistories ?? [])->pluck('device')->filter()->unique()->count();
    $statusLabel = strtoupper((string) ($user->account_status ?? 'aktif'));
    $initialProfile = [
        'name' => $displayName,
        'username' => $user->username ?? '',
        'email' => $displayEmail,
        'phone' => $user->phone ?? '',
        'address' => $user->address ?? '',
        'avatar_url' => $avatarUrl,
        'initial' => $initial,
    ];
@endphp

<div class="container-fluid profile-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.pengaturan.index') }}">Pengaturan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil Akun</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Profil Akun</h2>
            <p class="text-muted mb-0">Pengelolaan identitas pribadi, preferensi akun, keamanan dasar, dan histori perubahan profil.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="downloadProfileButton"><i class="bi bi-file-earmark-arrow-down"></i> Unduh Data Profil</button>
            <button class="btn btn-primary" type="submit" form="profileForm" id="saveTopButton"><i class="bi bi-save"></i> Simpan Perubahan</button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card profile-card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mx-auto mb-3" id="profilePreviewWrap">
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="Foto profil admin" class="profile-photo mx-auto" id="profilePreview">
                        @else
                            <div class="profile-initial mx-auto" id="profilePreview">{{ $initial }}</div>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-1" id="profileNamePreview">{{ $displayName }}</h4>
                    <p class="text-muted mb-3" id="profileEmailPreview">{{ $displayEmail }}</p>
                    <span class="badge bg-success mb-3"><i class="bi bi-check2-circle"></i> Akun Aktif</span>
                    <div class="d-flex justify-content-center gap-2">
                        <label class="btn btn-outline-primary mb-0">
                            <i class="bi bi-camera"></i> Ubah Foto
                            <input class="d-none" type="file" id="photoInput" accept=".jpg,.jpeg,.png">
                        </label>
                        <button class="btn btn-outline-secondary" type="button" id="verifyProfileButton"><i class="bi bi-patch-check"></i> Verifikasi Ulang</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="row g-3 h-100">
                <div class="col-md-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Status Akun</p><h3 class="mb-0">{{ $statusLabel }}</h3></div>
                            <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Profil Terverifikasi</p><h3 class="mb-0">{{ $user->verified_at ? 'Ya' : 'Belum' }}</h3></div>
                            <span class="stat-icon bg-primary"><i class="bi bi-patch-check"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Email Terverifikasi</p><h3 class="mb-0">{{ $user->verified_at ? 'Ya' : 'Belum' }}</h3></div>
                            <span class="stat-icon bg-info"><i class="bi bi-envelope-check"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Perangkat Aktif</p><h3 class="mb-0">{{ $securityDevices }}</h3></div>
                            <span class="stat-icon bg-warning"><i class="bi bi-laptop"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl">
                    <div class="card stat-card h-100">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Status Keamanan</p><h3 class="mb-0">{{ $user->two_factor_enabled ? 'Terlindungi' : 'Aman' }}</h3></div>
                            <span class="stat-icon bg-secondary"><i class="bi bi-shield-lock"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card form-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Form Data Pribadi</h5>
                    <form class="row g-3" id="profileForm" method="POST" action="{{ route('admin.pengaturan.update') }}">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label" for="nameInput">Nama Lengkap</label>
                            <input class="form-control" id="nameInput" name="name" value="{{ old('name', $displayName) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="usernameInput">Username</label>
                            <input class="form-control" id="usernameInput" name="username" value="{{ old('username', $user->username ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emailInput">Email Utama</label>
                            <input class="form-control" id="emailInput" name="email" type="email" value="{{ old('email', $displayEmail) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phoneInput">Nomor Telepon</label>
                            <input class="form-control" id="phoneInput" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="addressInput">Alamat</label>
                            <textarea class="form-control" id="addressInput" name="address" rows="3">{{ old('address', $user->address ?? '') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="roleInput">Role</label>
                            <input class="form-control" id="roleInput" value="Super Admin" readonly>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="submit" id="saveProfileButton"><i class="bi bi-save"></i> Simpan Perubahan</button>
                            <button class="btn btn-outline-danger" type="button" id="resetProfileButton"><i class="bi bi-arrow-counterclockwise"></i> Reset Profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card form-card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Form Preferensi Akun</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="languageInput">Bahasa Sistem</label>
                            <select class="form-select" id="languageInput">
                                <option>Indonesia</option>
                                <option>English</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="timezoneInput">Zona Waktu</label>
                            <select class="form-select" id="timezoneInput">
                                <option>Asia/Jakarta</option>
                                <option>Asia/Makassar</option>
                                <option>Asia/Jayapura</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="themeInput">Tampilan Sistem</label>
                            <select class="form-select" id="themeInput">
                                <option>Default</option>
                                <option>Kontras Tinggi</option>
                                <option>Ringkas</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card security-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Panel Keamanan Dasar</h5>
                    <div id="securityPanel"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Histori Aktivitas Profil</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <button class="btn btn-outline-primary" type="button" id="exportHistoryButton"><i class="bi bi-download"></i> Ekspor Histori Akun</button>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu Perubahan</th>
                            <th>Jenis Perubahan</th>
                            <th>Detail Perubahan</th>
                            <th>Perangkat</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Histori profil tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination histori profil"><ul class="pagination mb-0" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Histori Profil</h5>
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const uploadPhotoUrl = @json(route('admin.pengaturan.foto'));
    const downloadProfileUrl = @json(route('admin.pengaturan.profil.download'));
    const exportHistoryUrl = @json(route('admin.pengaturan.profil.history.export'));
    const resetProfileUrl = @json(route('admin.pengaturan.profil.reset'));
    const verifyProfileUrl = @json(route('admin.pengaturan.profil.verify'));
    const histories = @json($profileHistories ?? []);
    const initialProfile = @json($initialProfile);

    let filteredHistories = [...histories];
    let currentPage = 1;
    const perPage = 5;
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    const badgeMap = { berhasil: 'success', dibatalkan: 'secondary', gagal: 'danger' };

    function renderSecurityPanel() {
        const items = [
            ['Autentikasi', 'Sesi pengguna aktif dan tervalidasi', 'success', 'Aman'],
            ['Email Utama', 'Email telah diverifikasi', 'primary', 'Terverifikasi'],
            ['Perangkat', '3 perangkat aktif terpantau', 'warning', 'Dipantau'],
            ['Aktivitas', 'Tidak ada anomali terbaru', 'info', 'Normal']
        ];
        document.getElementById('securityPanel').innerHTML = items.map(item => `
            <div class="security-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div><strong>${item[0]}</strong><div class="small text-muted">${item[1]}</div></div>
                    <span class="badge bg-${item[2]}">${item[3]}</span>
                </div>
            </div>
        `).join('');
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const pageItems = filteredHistories.slice(start, start + perPage);
        const tableBody = document.getElementById('historyTable');
        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.time}</td>
                <td>${item.category}</td>
                <td>${item.detail}</td>
                <td>${item.device}</td>
                <td>${item.location}</td>
                <td><span class="badge bg-${badgeMap[item.status]}">${item.status}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-primary" data-action="detail" data-index="${histories.indexOf(item)}"><i class="bi bi-eye"></i> Lihat</button>
                        <button class="btn btn-sm btn-outline-warning" data-action="audit" data-index="${histories.indexOf(item)}"><i class="bi bi-shield-check"></i> Audit</button>
                    </div>
                </td>
            </tr>
        `).join('');

        const hasData = filteredHistories.length > 0;
        document.getElementById('emptyState').classList.toggle('d-none', hasData);
        document.getElementById('tableWrapper').classList.toggle('d-none', !hasData);
        document.getElementById('tableSummary').textContent = hasData ? `Menampilkan ${start + 1} sampai ${Math.min(start + perPage, filteredHistories.length)} dari ${filteredHistories.length} histori profil` : 'Menampilkan 0 data';
        document.getElementById('pageInfo').textContent = hasData ? `Halaman ${currentPage} dari ${Math.ceil(filteredHistories.length / perPage)}` : 'Menampilkan 0 data';
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.max(1, Math.ceil(filteredHistories.length / perPage));
        let items = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            items += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        items += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        document.getElementById('pagination').innerHTML = items;
    }

    function applyFilter() {
        const category = document.getElementById('categoryFilter').value;
        const date = document.getElementById('dateFilter').value;
        const device = document.getElementById('deviceFilter').value;
        const location = document.getElementById('locationFilter').value;
        filteredHistories = histories.filter(item => {
            const matchCategory = category === 'semua' || item.category === category;
            const matchDate = date === 'semua' || item.date === date;
            const matchDevice = device === 'semua' || item.device === device;
            const matchLocation = location === 'semua' || item.location === location;
            return matchCategory && matchDate && matchDevice && matchLocation;
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

    function setProfileAvatar(url) {
        const wrap = document.getElementById('profilePreviewWrap');
        if (wrap) {
            wrap.innerHTML = `<img src="${url}" alt="Foto profil admin" class="profile-photo mx-auto" id="profilePreview">`;
        }
        document.querySelectorAll('.js-admin-avatar').forEach((img) => {
            img.src = url;
        });
    }

    function setProfileFields(profile) {
        document.getElementById('nameInput').value = profile.name || '';
        document.getElementById('usernameInput').value = profile.username || '';
        document.getElementById('emailInput').value = profile.email || '';
        document.getElementById('phoneInput').value = profile.phone || '';
        document.getElementById('addressInput').value = profile.address || '';
        document.getElementById('profileNamePreview').textContent = profile.name || 'Super Admin';
        document.getElementById('profileEmailPreview').textContent = profile.email || 'admin@lldikti5.id';
        if (profile.avatar_url) {
            setProfileAvatar(profile.avatar_url);
        } else {
            const wrap = document.getElementById('profilePreviewWrap');
            if (wrap) {
                wrap.innerHTML = `<div class="profile-initial mx-auto" id="profilePreview">${(profile.initial || 'S').charAt(0)}</div>`;
            }
        }
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

    document.getElementById('resetProfileButton').addEventListener('click', () => {
        openConfirm('Reset Profil', 'Apakah Anda yakin ingin mengembalikan data profil ke nilai yang tersimpan di database?', async () => {
            try {
                const response = await fetch(resetProfileUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal memuat ulang profil.');
                }
                setProfileFields({
                    ...initialProfile,
                    ...payload.profile,
                });
                showToast(payload.message || 'Profil berhasil dimuat ulang dari database.', 'info');
            } catch (error) {
                showToast(error.message, 'danger');
            }
        });
    });
    document.getElementById('verifyProfileButton').addEventListener('click', () => {
        openConfirm('Verifikasi Ulang Profil', 'Apakah Anda yakin ingin mengajukan verifikasi ulang profil?', async () => {
            try {
                const response = await fetch(verifyProfileUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal mengajukan verifikasi ulang.');
                }
                showToast(payload.message || 'Verifikasi ulang profil berhasil diajukan.');
            } catch (error) {
                showToast(error.message, 'danger');
            }
        });
    });
    document.getElementById('downloadProfileButton').addEventListener('click', () => {
        window.location.href = downloadProfileUrl;
    });
    document.getElementById('exportHistoryButton').addEventListener('click', () => {
        window.location.href = exportHistoryUrl;
    });

    document.getElementById('photoInput').addEventListener('change', async (event) => {
        const file = event.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran foto maksimal 2MB.', 'danger');
            return;
        }
        const formData = new FormData();
        formData.append('foto', file);

        try {
            const response = await fetch(uploadPhotoUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Foto profil gagal disimpan.');
            }

            if (payload.avatar_url) {
                setProfileAvatar(payload.avatar_url);
            }

            showToast(payload.message || 'Foto profil berhasil diperbarui.');
        } catch (error) {
            showToast(error.message, 'danger');
        }
    });

    document.getElementById('pagination').addEventListener('click', event => {
        const page = Number(event.target.dataset.page);
        const totalPages = Math.max(1, Math.ceil(filteredHistories.length / perPage));
        if (!page || page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    });
    document.getElementById('historyTable').addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = histories[button.dataset.index];
        if (button.dataset.action === 'detail') {
            document.getElementById('detailContent').innerHTML = `
                <dl class="row mb-0">
                    <dt class="col-sm-4">Waktu</dt><dd class="col-sm-8">${item.time}</dd>
                    <dt class="col-sm-4">Kategori</dt><dd class="col-sm-8">${item.category}</dd>
                    <dt class="col-sm-4">Detail</dt><dd class="col-sm-8">${item.detail}</dd>
                    <dt class="col-sm-4">Perangkat</dt><dd class="col-sm-8">${item.device}</dd>
                    <dt class="col-sm-4">Lokasi</dt><dd class="col-sm-8">${item.location}</dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge bg-${badgeMap[item.status]}">${item.status}</span></dd>
                </dl>
            `;
            detailModal.show();
            return;
        }
        openConfirm('Audit Histori Profil', `Apakah Anda yakin ingin melakukan audit pada histori "${item.detail}"?`, () => showToast('Audit histori profil berhasil diproses.'));
    });

    renderSecurityPanel();
    renderTable();
});
</script>
@endpush
