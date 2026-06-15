@extends('mentor.layout.mentor')

@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@push('styles')
<style>
    .password-page .password-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .password-page .stat-card,
    .password-page .filter-card,
    .password-page .table-card,
    .password-page .panel-card { border:0; border-radius:8px; }
    .password-page .stat-card { cursor:pointer; transition:.2s ease; }
    .password-page .stat-card:hover { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .password-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .password-page .security-box,
    .password-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .password-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:360px; }
    .password-page .strength-track { height:8px; background:#e9ecef; border-radius:999px; overflow:hidden; }
    .password-page .strength-bar { height:100%; width:0%; background:#dc3545; transition:.2s ease; }
    .password-page .empty-state { min-height:190px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .password-page .pagination .page-link { color:#2a8fbd; }
    .password-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid password-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.pengaturan') }}">Pengaturan Akun</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah Password</li>
        </ol>
    </nav>

    <section class="password-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Ubah Password</h3>
                <p class="mb-0">Perbarui kata sandi akun mentor secara mandiri agar keamanan data dan akses portal magang tetap terjaga.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="securityBadge">Keamanan Aktif</span>
                <button class="btn btn-dark" type="button" id="logoutDevicesButton"><i class="bi bi-box-arrow-right"></i> Keluar Perangkat</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-shield-lock-fill"></i>
        <div>Password terakhir diubah 30 hari lalu. Gunakan kombinasi huruf besar, huruf kecil, angka, dan simbol untuk meningkatkan keamanan akun.</div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Status Keamanan</p><h3 class="mb-0 fs-5">Aman</h3></div><span class="stat-icon bg-success"><i class="bi bi-shield-check"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Terakhir Ubah</p><h3 class="mb-0 fs-6">30 Hari</h3></div><span class="stat-icon bg-warning"><i class="bi bi-clock-history"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Kekuatan Password</p><h3 class="mb-0 fs-5" id="statStrength">Sedang</h3></div><span class="stat-icon bg-info"><i class="bi bi-key"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Login Terakhir</p><h3 class="mb-0 fs-6">Hari Ini</h3></div><span class="stat-icon bg-primary"><i class="bi bi-calendar-check"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Perangkat Aktif</p><h3 class="mb-0">2</h3></div><span class="stat-icon bg-dark"><i class="bi bi-laptop"></i></span>
            </div></div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card"><div class="card-body d-flex justify-content-between align-items-center">
                <div><p class="text-muted mb-1">Verifikasi Akun</p><h3 class="mb-0 fs-5">Valid</h3></div><span class="stat-icon bg-danger"><i class="bi bi-patch-check"></i></span>
            </div></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Formulir Perubahan Password</h5>
                    <small class="text-muted">Pastikan password baru berbeda dari password lama.</small>
                </div>
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{ route('mentor.pengaturan.password.update') }}" id="passwordForm">
                        @csrf
                        <div class="col-12">
                            <label class="form-label" for="currentPassword">Password Lama</label>
                            <div class="input-group">
                                <input class="form-control password-field" id="currentPassword" name="current_password" type="password" placeholder="Masukkan password lama" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="currentPassword"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="newPassword">Password Baru</label>
                            <div class="input-group">
                                <input class="form-control password-field" id="newPassword" name="password" type="password" placeholder="Masukkan password baru" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="confirmPassword">Konfirmasi Password</label>
                            <div class="input-group">
                                <input class="form-control password-field" id="confirmPassword" name="password_confirmation" type="password" placeholder="Ulangi password baru" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="security-box mb-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-semibold">Indikator Keamanan Password</span>
                                    <span id="strengthLabel">Belum diisi</span>
                                </div>
                                <div class="strength-track"><div class="strength-bar" id="strengthBar"></div></div>
                                <small class="text-muted d-block mt-2" id="matchLabel">Konfirmasi password akan divalidasi otomatis.</small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-end gap-2">
                    <button class="btn btn-outline-secondary" type="button" id="cancelButton"><i class="bi bi-x-circle"></i> Batalkan</button>
                    <button class="btn btn-outline-warning" type="button" id="resetPasswordButton"><i class="bi bi-arrow-counterclockwise"></i> Reset Password</button>
                    <button class="btn btn-warning" type="button" id="savePasswordButton"><i class="bi bi-save"></i> Simpan Password</button>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card panel-card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Panel Informasi Keamanan</h5>
                    <div class="info-row"><small class="text-muted">Akun</small><div class="fw-bold">{{ auth()->user()->email ?? 'mentor@lldikti5.id' }}</div></div>
                    <div class="info-row"><small class="text-muted">Metode Verifikasi</small><div class="fw-semibold">Email dan sesi login</div></div>
                    <div class="info-row"><small class="text-muted">Rekomendasi</small><div>Perbarui password berkala dan keluar dari perangkat yang tidak digunakan.</div></div>
                </div>
            </div>
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Aksi Keamanan</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning text-start" type="button" id="showAllButton"><i class="bi bi-eye me-2"></i> Tampilkan Password</button>
                        <button class="btn btn-outline-warning text-start" type="button" id="logoutAllButton"><i class="bi bi-box-arrow-right me-2"></i> Keluar dari Semua Perangkat</button>
                        <button class="btn btn-outline-warning text-start" type="button" id="activityButton"><i class="bi bi-clock-history me-2"></i> Lihat Aktivitas</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label" for="activityFilter">Aktivitas Akun</label>
                    <select class="form-select" id="activityFilter"><option value="semua">Semua</option><option>Ubah Password</option><option>Login</option><option>Logout</option><option>Verifikasi</option></select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label" for="deviceFilter">Perangkat</label>
                    <select class="form-select" id="deviceFilter"><option value="semua">Semua</option><option>Desktop</option><option>Mobile</option><option>Tablet</option></select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label" for="securityFilter">Status Keamanan</label>
                    <select class="form-select" id="securityFilter"><option value="semua">Semua</option><option>Aman</option><option>Berhasil</option><option>Perlu Tinjauan</option></select>
                </div>
                <div class="col-lg-12 d-flex gap-2">
                    <button class="btn btn-warning flex-fill" type="button" id="applyFilter"><i class="bi bi-funnel"></i> Terapkan</button>
                    <button class="btn btn-outline-secondary flex-fill" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h5 class="mb-0">Histori Keamanan Akun</h5><small class="text-muted" id="tableInfo">Menampilkan aktivitas keamanan</small></div>
            <select class="form-select form-select-sm" id="perPageSelect" style="width:80px"><option value="5">5</option><option value="10">10</option></select>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>No</th><th>Jenis Aktivitas</th><th>Waktu Aktivitas</th><th>Perangkat</th><th>Lokasi Login</th><th>Status Verifikasi</th><th>Metode Akses</th><th>Status Keamanan</th><th>Aksi</th></tr></thead>
                    <tbody id="securityTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState"><div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada histori keamanan sesuai filter.</p></div></div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="confirmModalLabel">Konfirmasi Perubahan Password</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body" id="confirmText">Simpan perubahan password akun?</div>
            <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-warning" id="confirmActionButton">Simpan</button></div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="passwordToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex"><div class="toast-body" id="toastMessage">Keamanan akun diperbarui.</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const normalizeStatus = (status) => {
        const lower = (status ?? '').toString().toLowerCase();
        if (lower === 'berhasil') return 'Berhasil';
        if (lower === 'aman') return 'Aman';
        if (lower === 'perlu tinjauan' || lower === 'menunggu') return 'Perlu Tinjauan';
        if (lower === 'ditolak') return 'Ditolak';
        return status ?? 'Berhasil';
    };

    const activities = @json($securityHistories ?? []).map(item => ({
        type: (item.jenis === 'Password' ? 'Ubah Password' : (item.jenis ?? item.type ?? 'Aktivitas')),
        time: item.waktu ?? item.time ?? '-',
        device: item.perangkat ?? item.device ?? '-',
        location: item.lokasi ?? item.location ?? '-',
        verify: normalizeStatus(item.status ?? item.verify ?? '-'),
        method: item.browser ?? item.method ?? '-',
        security: normalizeStatus(item.status ?? item.security ?? 'Berhasil'),
        note: item.metode ?? item.aktivitas ?? '-',
    }));
    let filtered = [...activities];
    let currentPage = 1;
    let perPage = 5;
    let pendingAction = null;
    const tableBody = document.getElementById('securityTableBody');
    const pagination = document.getElementById('pagination');
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('passwordToast'));
    const showToast = message => { document.getElementById('toastMessage').textContent = message; toast.show(); };
    const badge = status => ({
        aman: 'success',
        berhasil: 'info',
        'perlu tinjauan': 'warning text-dark',
        menunggu: 'warning text-dark',
        ditolak: 'danger',
    }[(status ?? '').toString().toLowerCase()] || 'secondary');

    const scorePassword = value => {
        let score = 0;
        if (value.length >= 8) score += 25;
        if (/[A-Z]/.test(value)) score += 20;
        if (/[a-z]/.test(value)) score += 20;
        if (/\d/.test(value)) score += 20;
        if (/[^A-Za-z0-9]/.test(value)) score += 15;
        return Math.min(100, score);
    };
    const updateStrength = () => {
        const value = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        const score = scorePassword(value);
        const bar = document.getElementById('strengthBar');
        bar.style.width = `${score}%`;
        bar.style.background = score >= 80 ? '#198754' : score >= 55 ? '#2a8fbd' : '#dc3545';
        const label = score >= 80 ? 'Kuat' : score >= 55 ? 'Sedang' : value ? 'Lemah' : 'Belum diisi';
        document.getElementById('strengthLabel').textContent = label;
        document.getElementById('statStrength').textContent = label;
        document.getElementById('matchLabel').textContent = confirm && value !== confirm ? 'Konfirmasi password belum sesuai.' : 'Konfirmasi password sesuai atau belum diisi.';
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);
      tableBody.innerHTML = pageItems.map((item, index) => `
              <tr>
                  <td>${start + index + 1}</td>
                  <td>${item.type}</td>
                  <td>${item.time}</td>
                <td>${item.device}</td>
                <td>${item.location}</td>
                <td>${item.verify}</td>
                <td>${item.method}</td>
                  <td><span class="badge bg-${badge(item.security)}">${item.security}</span></td>
                <td><div class="action-group"><button class="btn btn-sm btn-outline-dark" data-row-action="detail">Detail</button><button class="btn btn-sm btn-outline-secondary" data-row-action="arsip">Arsipkan</button></div></td>
            </tr>
        `).join('');
        document.getElementById('emptyState').classList.toggle('d-none', filtered.length > 0);
        document.getElementById('tableInfo').textContent = `Menampilkan ${filtered.length} dari ${activities.length} aktivitas keamanan`;
        document.getElementById('paginationInfo').textContent = filtered.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, filtered.length)} dari ${filtered.length} data` : 'Menampilkan 0 data';
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    };
    const applyFilters = () => {
        const activity = document.getElementById('activityFilter').value;
        const device = document.getElementById('deviceFilter').value;
        const security = document.getElementById('securityFilter').value;
        filtered = activities.filter(item => {
            return (activity === 'semua' || item.type === activity)
                && (device === 'semua' || item.device === device)
                && (security === 'semua' || item.security === security);
        });
        currentPage = 1;
        renderTable();
    };

    document.querySelectorAll('.password-field').forEach(input => input.addEventListener('input', updateStrength));
    document.querySelectorAll('.toggle-password').forEach(button => button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
    }));
    document.getElementById('showAllButton').addEventListener('click', () => {
        document.querySelectorAll('.password-field').forEach(input => input.type = input.type === 'password' ? 'text' : 'password');
        showToast('Tampilan password diperbarui.');
    });
    document.getElementById('savePasswordButton').addEventListener('click', () => {
        const current = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;
        if (!current || !newPassword || !confirm) {
            showToast('Lengkapi seluruh field password terlebih dahulu.');
            return;
        }
        if (newPassword !== confirm) {
            showToast('Password baru dan konfirmasi harus sesuai.');
            return;
        }
        pendingAction = () => document.getElementById('passwordForm').submit();
        document.getElementById('confirmText').textContent = 'Simpan perubahan password secara permanen? Setelah disimpan, kredensial akun akan diperbarui.';
        modal.show();
    });
    ['logoutDevicesButton','logoutAllButton','resetPasswordButton','activityButton','cancelButton'].forEach(id => {
        document.getElementById(id).addEventListener('click', () => {
            pendingAction = () => showToast(`Aksi ${document.getElementById(id).textContent.trim()} berhasil diproses.`);
            document.getElementById('confirmText').textContent = `Lanjutkan aksi ${document.getElementById(id).textContent.trim()}?`;
            modal.show();
        });
    });
    document.getElementById('confirmActionButton').addEventListener('click', () => { if (pendingAction) pendingAction(); pendingAction = null; modal.hide(); });
    document.querySelectorAll('#activityFilter,#deviceFilter,#securityFilter').forEach(input => input.addEventListener('change', applyFilters));
    document.getElementById('applyFilter').addEventListener('click', applyFilters);
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('activityFilter').value = 'semua';
        document.getElementById('deviceFilter').value = 'semua';
        document.getElementById('securityFilter').value = 'semua';
        applyFilters();
    });
    document.getElementById('perPageSelect').addEventListener('change', event => { perPage = Number(event.target.value); currentPage = 1; renderTable(); });
    pagination.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });
    tableBody.addEventListener('click', event => {
          const button = event.target.closest('button[data-row-action]');
          if (!button) return;
          showToast(`Aksi ${button.dataset.rowAction} aktivitas keamanan diproses.`);
      });
    updateStrength();
    applyFilters();
});
</script>
@endpush
