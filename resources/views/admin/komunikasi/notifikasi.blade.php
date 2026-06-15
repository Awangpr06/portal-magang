@extends('admin.layout.admin')

@section('title', 'Notifikasi')

@push('styles')
<style>
    .notification-page .page-title { font-weight: 700; color: #163342; }
    .notification-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .notification-page .stat-card,
    .notification-page .filter-card,
    .notification-page .table-card,
    .notification-page .side-card { border: 0; border-radius: 8px; }
    .notification-page .stat-card { cursor: pointer; transition: .2s ease; }
    .notification-page .stat-card:hover,
    .notification-page .stat-card.active { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .notification-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .notification-page .table { font-size: 14px; }
    .notification-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .notification-page .table tbody tr { cursor: pointer; }
    .notification-page .table tbody tr:hover,
    .notification-page .table tbody tr.active { background: #f8fbfd; }
    .notification-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 280px; }
    .preference-row { display: flex; justify-content: space-between; gap: 12px; align-items: center; border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fff; }
    .detail-box { border: 1px solid #e2ebef; border-radius: 8px; padding: 14px; background: #fbfdfe; min-height: 180px; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid notification-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.komunikasi.index') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Notifikasi</h2>
            <p class="text-muted mb-0">Kelola distribusi notifikasi sistem, preferensi, audit pengiriman, dan monitoring pemberitahuan pengguna.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Audit</button>
            <button class="btn btn-outline-secondary" type="button" id="resetPreferenceButton"><i class="bi bi-arrow-clockwise"></i> Reset Preferensi</button>
            <button class="btn btn-primary" type="button" id="addButton"><i class="bi bi-bell"></i> Buat Notifikasi</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Notifikasi</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-bell"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Dibaca</p><h3 class="mb-0" id="statSent">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="belum dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statScheduled">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-envelope-exclamation"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sistem</p><h3 class="mb-0" id="statFailed">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-preference-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pengaturan Aktif</p><h3 class="mb-0" id="statActivePreference">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-sliders"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="searchInput" class="form-label">Notifikasi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari Notifikasi">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="categoryFilter" class="form-label">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Verifikasi">Verifikasi</option>
                        <option value="Magang">Magang</option>
                        <option value="Sistem">Sistem</option>
                        <option value="Pengumuman">Pengumuman</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="targetFilter" class="form-label">Target</label>
                    <select class="form-select" id="targetFilter">
                        <option value="semua">Semua Target</option>
                        <option value="Peserta Magang">Peserta Magang</option>
                        <option value="Mentor">Mentor</option>
                        <option value="Pembimbing Akademik">Pembimbing Akademik</option>
                        <option value="Semua Pengguna">Semua Pengguna</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="dateFilter" class="form-label">Tanggal</label>
                    <select class="form-select" id="dateFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="hari ini">Hari Ini</option>
                        <option value="minggu ini">Minggu Ini</option>
                        <option value="bulan ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="searchButton"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card table-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Daftar Notifikasi</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                    </div>
                    <div class="table-responsive" id="tableWrapper">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul Notifikasi</th>
                                    <th>Kategori</th>
                                    <th>Target Pengguna</th>
                                    <th>Tanggal</th>
                                    <th>Dikirim Oleh</th>
                                    <th width="300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="notificationTable"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data notifikasi tidak ditemukan.</p></div>
                    </div>
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination notifikasi"><ul class="pagination mb-0" id="pagination"></ul></nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card side-card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Detail Notifikasi</h5>
                    <div class="detail-box" id="detailPanel">
                        <div class="empty-state">
                            <div><i class="bi bi-bell fs-1 d-block mb-2"></i><p class="mb-0">Pilih notifikasi untuk melihat detail.</p></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card side-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Preferensi Notifikasi</h5>
                    <div id="preferenceList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Buat Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="formTitleInput">Judul</label>
                        <input class="form-control" id="formTitleInput" placeholder="Judul notifikasi">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="formCategory">Kategori</label>
                        <select class="form-select" id="formCategory">
                            <option value="Verifikasi">Verifikasi</option>
                            <option value="Magang">Magang</option>
                            <option value="Sistem">Sistem</option>
                            <option value="Pengumuman">Pengumuman</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formTarget">Target Pengguna</label>
                        <select class="form-select" id="formTarget">
                            <option value="Peserta Magang">Peserta Magang</option>
                            <option value="Mentor">Mentor</option>
                            <option value="Pembimbing Akademik">Pembimbing Akademik</option>
                            <option value="Semua Pengguna">Semua Pengguna</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formMessage">Isi Notifikasi</label>
                        <textarea class="form-control" id="formMessage" rows="4" placeholder="Isi notifikasi sistem"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveForm">Simpan</button>
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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="notificationToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const notifications = @json($notificationData ?? []);
    const preferences = @json($notificationPreferences ?? []);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const perPage = 5;
    let currentPage = 1;
    let selectedId = notifications[0]?.id || null;
    let editingId = null;
    let pendingAction = null;
    let pendingId = null;

    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const targetFilter = document.getElementById('targetFilter');
    const dateFilter = document.getElementById('dateFilter');
    const tableBody = document.getElementById('notificationTable');
    const pagination = document.getElementById('pagination');
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('notificationToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function filteredNotifications() {
        const keyword = searchInput.value.trim().toLowerCase();
        const category = categoryFilter.value;
        const target = targetFilter.value;
        const date = dateFilter.value;
        return notifications.filter((item) => {
            const matchKeyword = !keyword || [item.title, item.category, item.target, item.sender, item.message].join(' ').toLowerCase().includes(keyword);
            const matchCategory = category === 'semua' || item.category === category;
            const matchTarget = target === 'semua' || item.target === target;
            const matchDate = date === 'semua' || item.time === date;
            return matchKeyword && matchCategory && matchTarget && matchDate;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = notifications.length;
        document.getElementById('statSent').textContent = notifications.filter((item) => item.read).length;
        document.getElementById('statScheduled').textContent = notifications.filter((item) => !item.read).length;
        document.getElementById('statFailed').textContent = notifications.filter((item) => item.category === 'Sistem').length;
        document.getElementById('statActivePreference').textContent = preferences.filter((item) => item.active).length;
    }

    function renderTable() {
        const data = filteredNotifications();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr class="${item.id === selectedId ? 'active' : ''}" data-row-id="${item.id}">
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${item.title}</td>
                <td>${item.category}</td>
                <td>${item.target}</td>
                <td>${item.date}</td>
                <td>${item.sender}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${item.id}">Edit</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('tableSummary').textContent = `${data.length} notifikasi ditemukan`;
        document.getElementById('tableWrapper').classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyState').classList.toggle('d-none', data.length > 0);
        document.getElementById('pageInfo').textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} notifikasi` : 'Menampilkan 0 notifikasi';
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" type="button" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" type="button" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderDetail() {
        const item = notifications.find((notification) => notification.id === selectedId);
        if (!item) return;
        document.getElementById('detailPanel').innerHTML = `
            <h6>${item.title}</h6>
            <p class="text-muted small mb-3">${item.category} • ${item.target}</p>
            <p>${item.message}</p>
            <dl class="row small mb-0">
                <dt class="col-5">Tanggal</dt><dd class="col-7">${item.date}</dd>
                <dt class="col-5">Dikirim Oleh</dt><dd class="col-7">${item.sender}</dd>
            </dl>
        `;
    }

    function renderPreferences() {
        document.getElementById('preferenceList').innerHTML = preferences.map((item, index) => `
            <div class="preference-row">
                <div>
                    <strong>${item.key}</strong>
                    <p class="text-muted small mb-0">${item.active ? 'Aktif' : 'Nonaktif'}</p>
                </div>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input preference-toggle" type="checkbox" data-index="${index}" ${item.active ? 'checked' : ''}>
                </div>
            </div>
        `).join('');
    }

    function renderAll() {
        updateStats();
        renderTable();
        renderDetail();
        renderPreferences();
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('notificationToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    async function apiRequest(url, method, body = null) {
        const response = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: body ? JSON.stringify(body) : null,
        });

        const payload = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(payload.message || 'Permintaan gagal diproses.');
        }

        return payload;
    }

    function openForm(id = null) {
        editingId = id;
        const item = id ? notifications.find((notification) => notification.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Notifikasi' : 'Buat Notifikasi';
        document.getElementById('formTitleInput').value = item?.title || '';
        document.getElementById('formCategory').value = item?.category || 'Verifikasi';
        document.getElementById('formTarget').value = item?.target || 'Peserta Magang';
        document.getElementById('formMessage').value = item?.message || '';
        formModal.show();
    }

    function confirmAction(action, id = selectedId) {
        if (!id && action !== 'reset') {
            showToast('Pilih notifikasi terlebih dahulu.', false);
            return;
        }
        const item = notifications.find((notification) => notification.id === id);
        pendingAction = action;
        pendingId = id;
        const labels = {
            hapus: ['Konfirmasi Hapus', `Hapus notifikasi "${item?.title}"?`],
            reset: ['Konfirmasi Reset Preferensi', 'Reset seluruh preferensi notifikasi ke pengaturan awal?']
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    document.querySelector('[data-preference-card]').addEventListener('click', () => {
        showToast(`${preferences.filter((item) => item.active).length} preferensi notifikasi sedang aktif.`);
    });

    [categoryFilter, targetFilter, dateFilter].forEach((input) => input.addEventListener('change', () => {
        currentPage = 1;
        renderAll();
    }));

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderAll();
        }
    });

    document.getElementById('searchButton').addEventListener('click', () => {
        currentPage = 1;
        renderAll();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = 'semua';
        targetFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        renderAll();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        const row = event.target.closest('tr[data-row-id]');
        if (button) {
            const id = Number(button.dataset.id);
            const action = button.dataset.action;
            if (action === 'detail') {
                selectedId = id;
                renderAll();
            } else if (action === 'edit') {
                openForm(id);
            } else {
                confirmAction(action, id);
            }
            return;
        }
        if (row) {
            selectedId = Number(row.dataset.rowId);
            renderAll();
        }
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderAll();
    });

    document.getElementById('addButton').addEventListener('click', () => openForm());
    document.getElementById('exportButton').addEventListener('click', () => showToast('Data audit notifikasi berhasil disiapkan untuk ekspor.'));
    document.getElementById('resetPreferenceButton').addEventListener('click', () => confirmAction('reset', null));

    document.getElementById('preferenceList').addEventListener('change', (event) => {
        const toggle = event.target.closest('.preference-toggle');
        if (!toggle) return;

        const updated = preferences.map((item) => ({ ...item }));
        updated[Number(toggle.dataset.index)].active = toggle.checked;

        apiRequest(`{{ url('/admin/komunikasi/notifikasi/preferensi') }}`, 'PATCH', {
            pesan: updated.find((item) => item.key === 'Pesan')?.active ?? true,
            laporan: updated.find((item) => item.key === 'Laporan')?.active ?? true,
            penugasan: updated.find((item) => item.key === 'Penugasan')?.active ?? true,
            absensi: updated.find((item) => item.key === 'Absensi')?.active ?? true,
            pengumuman: updated.find((item) => item.key === 'Pengumuman')?.active ?? true,
            email: updated.find((item) => item.key === 'Email')?.active ?? true,
        })
            .then((response) => {
                preferences.forEach((item, index) => {
                    item.active = updated[index].active;
                });
                renderAll();
                showToast(response.message || 'Preferensi notifikasi berhasil diperbarui.');
            })
            .catch((error) => showToast(error.message, false));
    });

    document.getElementById('saveForm').addEventListener('click', () => {
        const title = document.getElementById('formTitleInput').value.trim();
        const category = document.getElementById('formCategory').value;
        const target = document.getElementById('formTarget').value;
        const message = document.getElementById('formMessage').value.trim();

        if (!title || !message) {
            showToast('Judul dan isi notifikasi wajib diisi.', false);
            return;
        }

        const endpoint = editingId
            ? `{{ url('/admin/komunikasi/notifikasi') }}/${editingId}`
            : `{{ url('/admin/komunikasi/notifikasi') }}`;
        const method = editingId ? 'PATCH' : 'POST';

        apiRequest(endpoint, method, {
            judul: title,
            kategori: category,
            target,
            message,
            dibaca: editingId ? !!notifications.find((notification) => notification.id === editingId)?.read : false,
        })
            .then((response) => {
                const saved = response.notification;
                if (editingId) {
                    const index = notifications.findIndex((notification) => notification.id === editingId);
                    if (index >= 0) {
                        notifications[index] = { ...notifications[index], ...saved };
                    }
                    showToast(`Notifikasi "${saved.title}" berhasil diperbarui.`);
                } else {
                    notifications.unshift(saved);
                    selectedId = saved.id;
                    showToast(`Notifikasi "${saved.title}" berhasil dibuat.`);
                }

                editingId = null;
                formModal.hide();
                renderAll();
            })
            .catch((error) => showToast(error.message, false));
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (pendingAction === 'reset') {
            apiRequest(`{{ url('/admin/komunikasi/notifikasi/preferensi') }}`, 'PATCH', {
                pesan: true,
                laporan: true,
                penugasan: true,
                absensi: true,
                pengumuman: true,
                email: true,
            })
                .then((response) => {
                    preferences.forEach((item) => item.active = true);
                    confirmModal.hide();
                    pendingAction = null;
                    pendingId = null;
                    renderAll();
                    showToast(response.message || 'Preferensi notifikasi berhasil direset.');
                })
                .catch((error) => showToast(error.message, false));
            return;
        }

        const index = notifications.findIndex((item) => item.id === pendingId);
        if (index < 0 || !pendingAction) return;
        if (pendingAction === 'hapus') {
            apiRequest(`{{ url('/admin/komunikasi/notifikasi') }}/${pendingId}`, 'DELETE')
                .then((response) => {
                    notifications.splice(index, 1);
                    selectedId = notifications[0]?.id || null;
                    pendingAction = null;
                    pendingId = null;
                    confirmModal.hide();
                    renderAll();
                    showToast(response.message || 'Notifikasi berhasil dihapus.');
                })
                .catch((error) => showToast(error.message, false));
        }
    });

    renderAll();
</script>
@endpush
