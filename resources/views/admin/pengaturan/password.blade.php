@extends('admin.layout.admin')

@section('title', 'Ubah Password')

@push('styles')
<style>
    .password-page .page-title { font-weight: 700; color: #163342; }
    .password-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .password-page .stat-card,
    .password-page .form-card,
    .password-page .tips-card,
    .password-page .filter-card,
    .password-page .table-card { border: 0; border-radius: 8px; }
    .password-page .stat-card { cursor: pointer; transition: .2s ease; }
    .password-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .password-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .strength-meter { height: 10px; border-radius: 999px; background: #e9eef2; overflow: hidden; }
    .strength-meter span { display: block; height: 100%; width: 0%; transition: .2s ease; }
    .tip-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fbfdfe; }
    .password-page .table { font-size: 14px; }
    .password-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .password-page .table tbody tr:hover { background: #f8fbfd; }
    .password-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 160px; }
    .empty-state { min-height: 190px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
@php
    $passwordChangedLabel = $passwordChangedAt ? $passwordChangedAt->translatedFormat('d M Y H:i') : 'Belum ada';
@endphp
<div class="container-fluid password-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.pengaturan.index') }}">Pengaturan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah Password</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Ubah Password</h2>
            <p class="text-muted mb-0">Pusat pembaruan kata sandi, evaluasi kekuatan password, dan audit keamanan autentikasi akun.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="downloadHistoryButton"><i class="bi bi-file-earmark-arrow-down"></i> Unduh Histori</button>
            <button class="btn btn-primary" type="button" id="enable2faButton"><i class="bi bi-shield-lock"></i> Aktifkan 2FA</button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Status Keamanan</p><h3 class="mb-0">{{ $twoFactorEnabled ? 'Aman' : 'Perlu Perlindungan' }}</h3></div>
                            <span class="stat-icon bg-success"><i class="bi bi-shield-check"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Kekuatan Password</p><h3 class="mb-0" id="strengthStat">Baik</h3></div>
                            <span class="stat-icon bg-primary"><i class="bi bi-key"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Waktu Perubahan</p><h3 class="mb-0">{{ $passwordChangedLabel }}</h3></div>
                            <span class="stat-icon bg-warning"><i class="bi bi-clock-history"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div><p class="text-muted mb-1">Autentikasi 2FA</p><h3 class="mb-0" id="twoFaStat">{{ $twoFactorEnabled ? 'Aktif' : 'Nonaktif' }}</h3></div>
                            <span class="stat-icon bg-info"><i class="bi bi-phone"></i></span>
                        </div>
                    </div>
                </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card form-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Form Ubah Password</h5>
                    <form class="row g-3" id="passwordForm" method="POST" action="{{ route('admin.pengaturan.password.update') }}">
                        @csrf
                        <div class="col-md-6">
                            <label for="currentPassword" class="form-label">Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control password-input" id="currentPassword" name="current_password" autocomplete="current-password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="currentPassword"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control password-input" id="newPassword" name="password" autocomplete="new-password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control password-input" id="confirmPassword" name="password_confirmation" autocomplete="new-password" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Indikator Kekuatan Password</label>
                            <div class="strength-meter mb-2"><span id="strengthBar"></span></div>
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted" id="strengthText">Masukkan password baru</span>
                                <span class="small" id="matchText">Belum divalidasi</span>
                            </div>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button class="btn btn-primary" type="button" id="savePasswordButton"><i class="bi bi-save"></i> Simpan Perubahan Password</button>
                            <button class="btn btn-outline-secondary" type="button" id="resetFormButton"><i class="bi bi-arrow-clockwise"></i> Reset Form</button>
                            <button class="btn btn-outline-danger" type="button" id="logoutDevicesButton"><i class="bi bi-box-arrow-right"></i> Logout Semua Perangkat</button>
                            <button class="btn btn-outline-success" type="button" id="verifySecurityButton"><i class="bi bi-shield-check"></i> Verifikasi Keamanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card tips-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Tips Keamanan Password</h5>
                    <div class="tip-item"><i class="bi bi-check-circle text-success me-2"></i>Gunakan minimal 8 karakter.</div>
                    <div class="tip-item"><i class="bi bi-check-circle text-success me-2"></i>Kombinasikan huruf besar, huruf kecil, angka, dan simbol.</div>
                    <div class="tip-item"><i class="bi bi-check-circle text-success me-2"></i>Hindari penggunaan nama, tanggal lahir, atau email.</div>
                    <div class="tip-item"><i class="bi bi-check-circle text-success me-2"></i>Perbarui password secara berkala dan jangan bagikan ke pihak lain.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Histori Perubahan Password</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal Perubahan</th>
                            <th>Perangkat</th>
                            <th>Browser</th>
                            <th>Lokasi</th>
                            <th>IP Address</th>
                            <th>Status Perubahan</th>
                            <th width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="historyTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Histori keamanan password tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination histori password"><ul class="pagination mb-0" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Histori Password</h5>
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
    const exportHistoryUrl = @json(route('admin.pengaturan.password.history.export'));
    const toggle2FaUrl = @json(route('admin.pengaturan.password.2fa'));
    const logoutDevicesUrl = @json(route('admin.pengaturan.password.logout-devices'));
    const verifySecurityUrl = @json(route('admin.pengaturan.password.verify-security'));
    const histories = @json($securityHistories ?? []).map(item => ({
        waktu: item.waktu ?? item.date ?? '-',
        perangkat: item.perangkat ?? item.device ?? '-',
        browser: item.browser ?? '-',
        lokasi: item.lokasi ?? item.location ?? '-',
        ip: item.ip ?? item.ip_address ?? '-',
        status: item.status ?? 'berhasil',
        aktivitas: item.aktivitas ?? item.metode ?? '-',
        jenis: item.jenis ?? item.category ?? 'Aktivitas',
    }));

    let filteredHistories = [...histories];
    let currentPage = 1;
    const perPage = 5;
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    const badgeMap = { berhasil: 'success', ditolak: 'danger', peringatan: 'warning' };

    function evaluateStrength() {
        const password = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        let score = 0;
        if (password.length >= 8) score += 25;
        if (/[A-Z]/.test(password)) score += 25;
        if (/[0-9]/.test(password)) score += 25;
        if (/[^A-Za-z0-9]/.test(password)) score += 25;

        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        const strengthStat = document.getElementById('strengthStat');
        let label = 'Lemah';
        let color = '#dc3545';
        if (score >= 75) {
            label = 'Kuat';
            color = '#198754';
        } else if (score >= 50) {
            label = 'Cukup';
            color = '#ffc107';
        }

        strengthBar.style.width = `${score}%`;
        strengthBar.style.background = color;
        strengthText.textContent = password ? `Kekuatan: ${label}` : 'Masukkan password baru';
        strengthStat.textContent = password ? label : 'Baik';

        const matchText = document.getElementById('matchText');
        if (!confirm) {
            matchText.textContent = 'Belum divalidasi';
            matchText.className = 'small text-muted';
        } else if (password === confirm) {
            matchText.textContent = 'Password sesuai';
            matchText.className = 'small text-success';
        } else {
            matchText.textContent = 'Password tidak sesuai';
            matchText.className = 'small text-danger';
        }
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const pageItems = filteredHistories.slice(start, start + perPage);
        const tableBody = document.getElementById('historyTable');
        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.waktu}</td>
                <td>${item.perangkat}</td>
                <td>${item.browser}</td>
                <td>${item.lokasi}</td>
                <td>${item.ip}</td>
                <td><span class="badge bg-${badgeMap[item.status] ?? 'success'}">${item.status}</span></td>
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
        document.getElementById('tableSummary').textContent = hasData ? `Menampilkan ${start + 1} sampai ${Math.min(start + perPage, filteredHistories.length)} dari ${filteredHistories.length} histori password` : 'Menampilkan 0 data';
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
        const date = document.getElementById('dateFilter').value;
        const device = document.getElementById('deviceFilter').value;
        const location = document.getElementById('locationFilter').value;
        const status = document.getElementById('statusFilter').value;
        filteredHistories = histories.filter(item => {
            const matchDate = date === 'semua' || item.waktu.includes(date);
            const matchDevice = device === 'semua' || item.perangkat === device;
            const matchLocation = location === 'semua' || item.lokasi === location;
            const matchStatus = status === 'semua' || item.status === status;
            return matchDate && matchDevice && matchLocation && matchStatus;
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
        actionButton.onclick = () => {
            callback();
            confirmModal.hide();
        };
        confirmModal.show();
    }

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', () => {
            const target = document.getElementById(button.dataset.target);
            target.type = target.type === 'password' ? 'text' : 'password';
            button.querySelector('i').className = target.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    });
    document.getElementById('newPassword').addEventListener('input', evaluateStrength);
    document.getElementById('confirmPassword').addEventListener('input', evaluateStrength);

    document.getElementById('savePasswordButton').addEventListener('click', () => {
        const current = document.getElementById('currentPassword').value;
        const password = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        if (!current || !password || !confirm) {
            showToast('Lengkapi seluruh field password terlebih dahulu.', 'danger');
            return;
        }
        if (password !== confirm) {
            showToast('Password baru dan konfirmasi belum sesuai.', 'danger');
            return;
        }
        openConfirm('Ubah Password Utama', 'Apakah Anda yakin ingin menyimpan perubahan password?', () => document.getElementById('passwordForm').submit());
    });
    document.getElementById('resetFormButton').addEventListener('click', () => {
        document.querySelectorAll('.password-input').forEach(input => input.value = '');
        evaluateStrength();
        showToast('Form password berhasil direset.', 'info');
    });
    document.getElementById('enable2faButton').addEventListener('click', () => {
        openConfirm('Autentikasi 2FA', 'Apakah Anda yakin ingin mengubah status autentikasi 2FA akun ini?', async () => {
            try {
                const response = await fetch(toggle2FaUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal mengubah status 2FA.');
                }
                document.getElementById('twoFaStat').textContent = payload.enabled ? 'Aktif' : 'Nonaktif';
                showToast(payload.message || 'Status 2FA berhasil diperbarui.');
            } catch (error) {
                showToast(error.message, 'danger');
            }
        });
    });
    document.getElementById('logoutDevicesButton').addEventListener('click', () => {
        openConfirm('Logout Semua Perangkat', 'Tindakan ini akan mengeluarkan akun dari seluruh perangkat aktif. Lanjutkan?', async () => {
            try {
                const response = await fetch(logoutDevicesUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal logout perangkat.');
                }
                showToast(payload.message || 'Semua perangkat berhasil dilogout.', 'warning');
            } catch (error) {
                showToast(error.message, 'danger');
            }
        });
    });
    document.getElementById('verifySecurityButton').addEventListener('click', () => {
        openConfirm('Verifikasi Keamanan', 'Apakah Anda yakin ingin mencatat verifikasi keamanan pada database?', async () => {
            try {
                const response = await fetch(verifySecurityUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal menjalankan verifikasi.');
                }
                showToast(payload.message || 'Verifikasi keamanan berhasil dijalankan.', 'info');
            } catch (error) {
                showToast(error.message, 'danger');
            }
        });
    });
    document.getElementById('downloadHistoryButton').addEventListener('click', () => {
        window.location.href = exportHistoryUrl;
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
                    <dt class="col-sm-4">Tanggal</dt><dd class="col-sm-8">${item.waktu}</dd>
                    <dt class="col-sm-4">Perangkat</dt><dd class="col-sm-8">${item.perangkat}</dd>
                    <dt class="col-sm-4">Browser</dt><dd class="col-sm-8">${item.browser}</dd>
                    <dt class="col-sm-4">Lokasi</dt><dd class="col-sm-8">${item.lokasi}</dd>
                    <dt class="col-sm-4">IP Address</dt><dd class="col-sm-8">${item.ip}</dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge bg-${badgeMap[item.status] ?? 'success'}">${item.status}</span></dd>
                </dl>
            `;
            detailModal.show();
            return;
        }
        openConfirm('Audit Keamanan Password', `Apakah Anda yakin ingin mengaudit histori dari ${item.perangkat} - ${item.browser}?`, () => showToast('Audit keamanan password berhasil diproses.'));
    });

    evaluateStrength();
    renderTable();
});
</script>
@endpush
