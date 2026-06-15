@extends('mentor.layout.mentor')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@push('styles')
<style>
    .notification-page .notification-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .notification-page .stat-card,
    .notification-page .filter-card,
    .notification-page .table-card,
    .notification-page .panel-card { border:0; border-radius:8px; }
    .notification-page .stat-card { cursor:pointer; transition:.2s ease; }
    .notification-page .stat-card:hover,
    .notification-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .notification-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .notification-page .notification-list { max-height:420px; overflow:auto; }
    .notification-page .detail-box,
    .notification-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .notification-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:460px; }
    .notification-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .notification-page .pagination .page-link { color:#2a8fbd; }
    .notification-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid notification-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.komunikasi') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
        </ol>
    </nav>

    <section class="notification-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Notifikasi Sistem</h3>
                <p class="mb-0">Pantau seluruh pemberitahuan terkait aktivitas peserta, komunikasi, penilaian, laporan, dan administrasi secara terpusat.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="notificationBadge">Real-time</span>
                <button class="btn btn-dark" type="button" id="markAllButton"><i class="bi bi-check2-all"></i> Tandai Dibaca</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-bell-fill"></i>
        <div>Notifikasi mentor ditampilkan langsung dari database, termasuk pesan baru, pengumuman, dan aktivitas yang perlu ditindaklanjuti.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Notifikasi</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-bell"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="belum dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statUnread">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-bell-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Sudah Dibaca</p><h3 class="mb-0" id="statRead">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-priority-card="Tinggi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Prioritas Tinggi</p><h3 class="mb-0" id="statHigh">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Respons</p><h3 class="mb-0" id="statResponse">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-speedometer2"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Cari Notifikasi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari judul, sumber, atau isi">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Aktivitas Peserta">Aktivitas Peserta</option>
                        <option value="Komunikasi">Komunikasi</option>
                        <option value="Penilaian">Penilaian</option>
                        <option value="Laporan">Laporan</option>
                        <option value="Administrasi">Administrasi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Baca</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="belum dibaca">Belum Dibaca</option>
                        <option value="dibaca">Dibaca</option>
                        <option value="selesai">Selesai</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="priorityFilter">Prioritas</label>
                    <select class="form-select" id="priorityFilter">
                        <option value="semua">Semua Prioritas</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal</label>
                    <input class="form-control" id="dateFilter" type="date">
                </div>
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-warning" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm mb-4">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Riwayat Notifikasi</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data notifikasi</small>
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
                            <th>Judul Notifikasi</th>
                            <th>Kategori</th>
                            <th>Sumber</th>
                            <th>Waktu Terima</th>
                            <th>Status Baca</th>
                            <th>Prioritas</th>
                            <th>Tindak Lanjut</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="notificationTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada notifikasi sesuai filter.</p></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 notifikasi</small>
            <nav aria-label="Pagination notifikasi"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Notifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="confirmText">Terapkan perubahan status notifikasi?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmActionButton">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="notificationToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Notifikasi diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const notifications = (@json($notificationData ?? []) || []).map(item => ({
        id: Number(item.id),
        title: String(item.title || 'Notifikasi Sistem'),
        category: String(item.category || 'Sistem'),
        source: String(item.source || 'Sistem'),
        time: String(item.time || 'bulan ini'),
        date: String(item.date || ''),
        status: String(item.status || 'belum dibaca').toLowerCase(),
        priority: String(item.priority || 'Rendah'),
        follow: String(item.follow || 'Lihat Detail'),
        content: String(item.content || '-'),
    }));
    const csrfToken = @json(csrf_token());
    const notificationUrlBase = @json(url('/mentor/notifikasi'));
    const today = @json(now()->toDateString());

    let filtered = [...notifications];
    let selectedId = notifications[0]?.id || null;
    let currentPage = 1;
    let perPage = 5;
    let pendingAction = null;

    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('notificationToast'));
    const tableBody = document.getElementById('notificationTableBody');
    const pagination = document.getElementById('pagination');
    const notificationListEl = document.getElementById('notificationList');
    const detailTitleEl = document.getElementById('detailTitle');
    const detailSubtitleEl = document.getElementById('detailSubtitle');
    const detailPanelEl = document.getElementById('detailPanel');
    const infoPanelEl = document.getElementById('infoPanel');

    const titleCase = text => text.replace(/\b\w/g, char => char.toUpperCase());
    const selectedNotification = () => notifications.find(item => item.id === selectedId) || notifications[0] || null;
    const statusBadge = status => {
        const map = { 'belum dibaca':'warning text-dark', dibaca:'info', selesai:'success', arsip:'secondary' };
        return `<span class="badge bg-${map[status] || 'secondary'}">${titleCase(status)}</span>`;
    };
    const priorityBadge = priority => {
        const map = { Tinggi:'danger', Sedang:'warning text-dark', Rendah:'secondary' };
        return `<span class="badge bg-${map[priority] || 'secondary'}">${priority}</span>`;
    };
    const showToast = message => {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    };

    const postForm = async (url, formData) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            let message = 'Permintaan gagal diproses.';
            try {
                const payload = await response.json();
                message = payload.message || message;
            } catch (error) {
                // ignore parse error
            }
            throw new Error(message);
        }

        return response.json();
    };

    const updateNotification = async (item, status) => {
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('status', status);
        return postForm(`${notificationUrlBase}/${item.id}`, formData);
    };

    const deleteNotification = async (item) => {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        return postForm(`${notificationUrlBase}/${item.id}`, formData);
    };

    const updateStats = () => {
        const read = notifications.filter(item => item.status !== 'belum dibaca').length;
        document.getElementById('statTotal').textContent = notifications.length;
        document.getElementById('statUnread').textContent = notifications.filter(item => item.status === 'belum dibaca').length;
        document.getElementById('statRead').textContent = read;
        document.getElementById('statHigh').textContent = notifications.filter(item => item.priority === 'Tinggi').length;
        document.getElementById('statToday').textContent = notifications.filter(item => item.date === today).length;
        document.getElementById('statResponse').textContent = `${Math.round((read / Math.max(1, notifications.length)) * 100)}%`;
        const listCountEl = document.getElementById('listCount');
        if (listCountEl) {
            listCountEl.textContent = filtered.length;
        }
    };

    const renderList = () => {
        if (!notificationListEl) return;
        notificationListEl.innerHTML = filtered.map(item => `
            <button class="list-group-item list-group-item-action ${item.id === selectedId ? 'active' : ''}" type="button" data-notification="${item.id}">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.title}</strong>
                    ${priorityBadge(item.priority)}
                </div>
                <small class="d-block">${item.category} - ${item.source}</small>
                <small class="d-block">${statusBadge(item.status)} <span class="ms-1">${item.time}</span></small>
            </button>
        `).join('') || '<p class="text-muted mb-0">Tidak ada notifikasi.</p>';
    };

    const renderDetail = () => {
        if (!detailTitleEl || !detailSubtitleEl || !detailPanelEl || !infoPanelEl) return;
        const item = selectedNotification();
        if (!item) {
            detailTitleEl.textContent = 'Detail Notifikasi';
            detailSubtitleEl.textContent = 'Pilih notifikasi untuk melihat detail.';
            detailPanelEl.innerHTML = `
                <div class="empty-state">
                    <div><i class="bi bi-bell fs-1 d-block mb-2"></i><p class="mb-0">Belum ada notifikasi untuk ditampilkan.</p></div>
                </div>
            `;
            infoPanelEl.innerHTML = '<p class="text-muted mb-0">Belum ada data.</p>';
            return;
        }
        detailTitleEl.textContent = item.title;
        detailSubtitleEl.textContent = `${item.category} - ${item.source}`;
        detailPanelEl.innerHTML = `
            <div class="detail-box">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                    ${statusBadge(item.status)}
                    ${priorityBadge(item.priority)}
                </div>
                <p class="mb-0">${item.content}</p>
            </div>
            <div class="row g-2">
                <div class="col-md-6"><div class="detail-box"><small class="text-muted">Waktu Terima</small><div class="fw-semibold">${item.time}</div></div></div>
                <div class="col-md-6"><div class="detail-box"><small class="text-muted">Tindak Lanjut</small><div class="fw-semibold">${item.follow}</div></div></div>
            </div>
        `;
        infoPanelEl.innerHTML = `
            <div class="info-row"><small class="text-muted">Sumber</small><div class="fw-bold">${item.source}</div></div>
            <div class="info-row"><small class="text-muted">Kategori</small><div class="fw-semibold">${item.category}</div></div>
            <div class="info-row"><small class="text-muted">Status Aktivitas</small><div>${statusBadge(item.status)}</div></div>
        `;
        renderList();
    };

    const renderTable = () => {
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        const start = (currentPage - 1) * perPage;
        const pageItems = filtered.slice(start, start + perPage);
        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><div class="fw-semibold">${item.title}</div><small class="text-muted">${item.content ? `${item.content.slice(0, 52)}${item.content.length > 52 ? '...' : ''}` : '-'}</small></td>
                <td>${item.category}</td>
                <td>${item.source}</td>
                <td>${item.time}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${priorityBadge(item.priority)}</td>
                <td>${item.follow}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-id="${item.id}"><i class="bi bi-eye"></i> Detail</button>
                        <button class="btn btn-sm btn-warning" type="button" data-action="dibaca" data-id="${item.id}"><i class="bi bi-check2-all"></i> Dibaca</button>
                        <button class="btn btn-sm btn-outline-success" type="button" data-action="selesai" data-id="${item.id}"><i class="bi bi-check-circle"></i> Selesai</button>
                        <button class="btn btn-sm btn-outline-secondary" type="button" data-action="arsip" data-id="${item.id}"><i class="bi bi-archive"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
        document.getElementById('emptyState').classList.toggle('d-none', filtered.length > 0);
        document.getElementById('tableInfo').textContent = `Menampilkan ${filtered.length} dari ${notifications.length} notifikasi`;
        document.getElementById('paginationInfo').textContent = filtered.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, filtered.length)} dari ${filtered.length} notifikasi` : 'Menampilkan 0 notifikasi';
        renderPagination(totalPages);
    };

    const renderPagination = totalPages => {
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = html;
    };

    const applyFilters = () => {
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const category = document.getElementById('categoryFilter').value;
        const status = document.getElementById('statusFilter').value;
        const priority = document.getElementById('priorityFilter').value;
        const date = document.getElementById('dateFilter').value;
        filtered = notifications.filter(item => {
            const text = [item.title, item.category, item.source, item.content, item.follow].join(' ').toLowerCase();
            return text.includes(keyword)
                && (category === 'semua' || item.category === category)
                && (status === 'semua' || item.status === status)
                && (priority === 'semua' || item.priority === priority)
                && (!date || item.date === date);
        });
        currentPage = 1;
        updateStats();
        renderList();
        renderTable();
    };

    const confirmStatus = (item, status) => {
        pendingAction = async () => {
            await updateNotification(item, status);
            showToast(`Notifikasi "${item.title}" diperbarui menjadi ${status}.`);
            window.location.reload();
        };
        document.getElementById('confirmText').textContent = `Ubah status notifikasi "${item.title}" menjadi ${status}?`;
        confirmModal.show();
    };

    if (notificationListEl) {
        notificationListEl.addEventListener('click', event => {
            const button = event.target.closest('button[data-notification]');
            if (!button) return;
            selectedId = Number(button.dataset.notification);
            renderDetail();
        });
    }

    tableBody.addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = notifications.find(row => row.id === Number(button.dataset.id));
        if (!item) return;
        selectedId = item.id;
        if (button.dataset.action === 'detail') renderDetail();
        if (['dibaca','selesai','arsip'].includes(button.dataset.action)) confirmStatus(item, button.dataset.action);
    });

    document.getElementById('confirmActionButton').addEventListener('click', async () => {
        try {
            if (pendingAction) await pendingAction();
            confirmModal.hide();
        } finally {
            pendingAction = null;
        }
    });

    document.getElementById('markAllButton').addEventListener('click', () => {
        pendingAction = async () => {
            const formData = new FormData();
            formData.append('_method', 'PATCH');
            await postForm(`${notificationUrlBase}/mark-all`, formData);
            showToast('Semua notifikasi belum dibaca berhasil ditandai dibaca.');
            window.location.reload();
        };
        document.getElementById('confirmText').textContent = 'Tandai semua notifikasi belum dibaca menjadi dibaca?';
        confirmModal.show();
    });

    const readPanelButton = document.getElementById('readPanelButton');
    const donePanelButton = document.getElementById('donePanelButton');
    const archivePanelButton = document.getElementById('archivePanelButton');
    const deletePanelButton = document.getElementById('deletePanelButton');
    const openActivityButton = document.getElementById('openActivityButton');
    if (readPanelButton) readPanelButton.addEventListener('click', () => confirmStatus(selectedNotification(), 'dibaca'));
    if (donePanelButton) donePanelButton.addEventListener('click', () => confirmStatus(selectedNotification(), 'selesai'));
    if (archivePanelButton) archivePanelButton.addEventListener('click', () => confirmStatus(selectedNotification(), 'arsip'));
    if (deletePanelButton) deletePanelButton.addEventListener('click', () => {
        const item = selectedNotification();
        pendingAction = async () => {
            await deleteNotification(item);
            selectedId = notifications[0]?.id;
            showToast('Notifikasi berhasil dihapus.');
            window.location.reload();
        };
        document.getElementById('confirmText').textContent = `Hapus notifikasi "${item.title}"?`;
        confirmModal.show();
    });
    if (openActivityButton) openActivityButton.addEventListener('click', () => showToast(`Aktivitas "${selectedNotification().follow}" dibuka.`));

    pagination.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('perPageSelect').addEventListener('change', event => {
        perPage = Number(event.target.value);
        currentPage = 1;
        renderTable();
    });

    document.getElementById('applyFilter').addEventListener('click', applyFilters);
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.querySelectorAll('#categoryFilter,#statusFilter,#priorityFilter,#dateFilter').forEach(input => input.addEventListener('change', applyFilters));
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('categoryFilter').value = 'semua';
        document.getElementById('statusFilter').value = 'semua';
        document.getElementById('priorityFilter').value = 'semua';
        document.getElementById('dateFilter').value = '';
        applyFilters();
    });

    document.querySelectorAll('.stat-card[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach(item => item.classList.remove('active'));
            card.classList.add('active');
            document.getElementById('statusFilter').value = card.dataset.statusCard;
            applyFilters();
        });
    });
    document.querySelectorAll('.stat-card[data-priority-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('priorityFilter').value = card.dataset.priorityCard;
            applyFilters();
        });
    });

    updateStats();
    applyFilters();
    renderDetail();
});
</script>
@endpush
