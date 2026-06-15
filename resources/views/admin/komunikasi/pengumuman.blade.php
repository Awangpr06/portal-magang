@extends('admin.layout.admin')

@section('title', 'Pengumuman')

@push('styles')
<style>
    .announcement-page .page-title { font-weight: 700; color: #163342; }
    .announcement-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .announcement-page .stat-card,
    .announcement-page .filter-card,
    .announcement-page .table-card,
    .announcement-page .side-card { border: 0; border-radius: 8px; }
    .announcement-page .stat-card { cursor: pointer; transition: .2s ease; }
    .announcement-page .stat-card:hover,
    .announcement-page .stat-card.active { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .announcement-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .announcement-page .table { font-size: 14px; }
    .announcement-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .announcement-page .table tbody tr:hover { background: #f8fbfd; }
    .announcement-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 280px; }
    .announcement-page .latest-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fbfdfe; }
    .announcement-page .category-pill { display: inline-flex; align-items: center; justify-content: space-between; gap: 8px; width: 100%; border: 1px solid #e2ebef; border-radius: 8px; padding: 10px 12px; margin-bottom: 8px; background: #fff; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid announcement-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.komunikasi.index') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Pengumuman</h2>
            <p class="text-muted mb-0">Kelola informasi resmi sistem untuk peserta, mentor, pembimbing, perguruan tinggi, dan mitra instansi.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Data</button>
            <button class="btn btn-primary" type="button" id="addButton"><i class="bi bi-megaphone"></i> Buat Pengumuman</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Pengumuman</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-megaphone"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="dipublikasikan">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Dipublikasikan</p><h3 class="mb-0" id="statPublished">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="terjadwal">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terjadwal</p><h3 class="mb-0" id="statScheduled">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar-event"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="diarsipkan">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Diarsipkan</p><h3 class="mb-0" id="statArchived">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-archive"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="searchInput" class="form-label">Pengumuman</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari Pengumuman">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="categoryFilter" class="form-label">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Peserta">Peserta</option>
                        <option value="Mentor">Mentor</option>
                        <option value="Pembimbing">Pembimbing</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="dipublikasikan">Dipublikasikan</option>
                        <option value="terjadwal">Terjadwal</option>
                        <option value="diarsipkan">Diarsipkan</option>
                        <option value="draft">Draft</option>
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
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="searchButton"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-9">
            <div class="card table-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Daftar Pengumuman</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                    </div>

                    <div class="table-responsive" id="tableWrapper">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Publikasi</th>
                                    <th>Status</th>
                                    <th>Dilihat</th>
                                    <th width="300">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="announcementTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data pengumuman tidak ditemukan.</p></div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination pengumuman"><ul class="pagination mb-0" id="pagination"></ul></nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <div class="card side-card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Pengumuman Terbaru</h5>
                    <div id="latestList"></div>
                </div>
            </div>
            <div class="card side-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Kategori Pengumuman</h5>
                    <div id="categoryList"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Buat Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="formTitleInput">Judul</label>
                        <input class="form-control" id="formTitleInput" placeholder="Judul pengumuman">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="formCategory">Kategori</label>
                        <select class="form-select" id="formCategory">
                            <option value="peserta">Peserta</option>
                            <option value="mentor">Mentor</option>
                            <option value="pembimbing">Pembimbing</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formDate">Tanggal Publikasi</label>
                        <input class="form-control" id="formDate" type="date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formStatus">Status</label>
                        <select class="form-select" id="formStatus">
                            <option value="draft">Draft</option>
                            <option value="dipublikasikan">Dipublikasikan</option>
                            <option value="terjadwal">Terjadwal</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formContent">Isi Pengumuman</label>
                        <textarea class="form-control" id="formContent" rows="4" placeholder="Isi informasi resmi"></textarea>
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
    <div id="announcementToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const announcements = @json($announcementData ?? []);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const perPage = 5;
    let currentPage = 1;
    let editingId = null;
    let pendingAction = null;
    let pendingId = null;

    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const tableBody = document.getElementById('announcementTable');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('announcementToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(status) {
        return { dipublikasikan: 'bg-success', terjadwal: 'bg-info text-dark', diarsipkan: 'bg-secondary', draft: 'bg-warning text-dark' }[status];
    }

    function filteredAnnouncements() {
        const keyword = searchInput.value.trim().toLowerCase();
        const category = categoryFilter.value;
        const status = statusFilter.value;
        const date = dateFilter.value;
        return announcements.filter((item) => {
            const matchKeyword = !keyword || [item.title, item.category, item.author, item.content, item.status].join(' ').toLowerCase().includes(keyword);
            const matchCategory = category === 'semua' || item.category === category;
            const matchStatus = status === 'semua' || item.status === status;
            const matchDate = date === 'semua' || item.time === date;
            return matchKeyword && matchCategory && matchStatus && matchDate;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = announcements.length;
        document.getElementById('statPublished').textContent = announcements.filter((item) => item.status === 'dipublikasikan').length;
        document.getElementById('statScheduled').textContent = announcements.filter((item) => item.status === 'terjadwal').length;
        document.getElementById('statArchived').textContent = announcements.filter((item) => item.status === 'diarsipkan').length;
    }

    function renderTable() {
        const data = filteredAnnouncements();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${item.title}</td>
                <td>${item.category}</td>
                <td>${item.author}</td>
                <td>${item.date}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>${item.views}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${item.id}">Edit</button>
                        ${item.status === 'draft'
                            ? `<button class="btn btn-success btn-sm" type="button" data-action="dipublikasikan" data-id="${item.id}">Publikasi</button>`
                            : `<span class="badge bg-light text-muted align-self-center">Sudah dipublikasikan</span>`}
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('tableSummary').textContent = `${data.length} pengumuman ditemukan`;
        document.getElementById('tableWrapper').classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyState').classList.toggle('d-none', data.length > 0);
        document.getElementById('pageInfo').textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} pengumuman` : 'Menampilkan 0 pengumuman';
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

    function renderSidePanels() {
        const latest = [...announcements].slice(0, 4);
        document.getElementById('latestList').innerHTML = latest.map((item) => `
            <div class="latest-item">
                <span class="badge ${statusClass(item.status)} mb-2">${titleCase(item.status)}</span>
                <h6 class="mb-1">${item.title}</h6>
                <p class="text-muted small mb-0">${item.date} • ${item.views} dilihat</p>
            </div>
        `).join('');

        const categorySummary = announcements.reduce((acc, item) => {
            const key = item.category || 'Tanpa Kategori';
            acc[key] = (acc[key] || 0) + 1;
            return acc;
        }, {});
        const categories = Object.entries(categorySummary).sort((a, b) => b[1] - a[1]);
        document.getElementById('categoryList').innerHTML = categories.length
            ? categories.map(([category, total]) => `
            <div class="category-pill">
                <span>${category}</span>
                <strong>${total}</strong>
            </div>
        `).join('')
            : '<div class="small-muted">Belum ada kategori pengumuman.</div>';
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('announcementToast');
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

    function showDetail(id) {
        const item = announcements.find((announcement) => announcement.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Judul</dt><dd class="col-sm-8">${item.title}</dd>
                <dt class="col-sm-4">Kategori</dt><dd class="col-sm-8">${item.category}</dd>
                <dt class="col-sm-4">Dibuat Oleh</dt><dd class="col-sm-8">${item.author}</dd>
                <dt class="col-sm-4">Tanggal Publikasi</dt><dd class="col-sm-8">${item.date}</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">${titleCase(item.status)}</dd>
                <dt class="col-sm-4">Jumlah Dilihat</dt><dd class="col-sm-8">${item.views}</dd>
                <dt class="col-sm-4">Isi</dt><dd class="col-sm-8">${item.content}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const item = id ? announcements.find((announcement) => announcement.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Pengumuman' : 'Buat Pengumuman';
        document.getElementById('formTitleInput').value = item?.title || '';
        document.getElementById('formCategory').value = (item?.category || 'peserta').toString().toLowerCase();
        document.getElementById('formStatus').value = item?.status === 'diarsipkan' ? 'draft' : (item?.status || 'draft');
        document.getElementById('formDate').value = item?.date ? '' : '';
        document.getElementById('formContent').value = item?.content || '';
        formModal.show();
    }

    function confirmAction(action, id) {
        const item = announcements.find((announcement) => announcement.id === id);
        pendingAction = action;
        pendingId = id;
        const labels = {
            dipublikasikan: ['Konfirmasi Publikasi', `Publikasikan pengumuman "${item.title}"?`],
            hapus: ['Konfirmasi Hapus', `Hapus pengumuman "${item.title}"?`]
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    function renderAll() {
        updateStats();
        renderTable();
        renderSidePanels();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderAll();
        });
    });

    [categoryFilter, statusFilter, dateFilter].forEach((input) => input.addEventListener('change', () => {
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
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderAll();
    });

    document.getElementById('addButton').addEventListener('click', () => openForm());
    document.getElementById('exportButton').addEventListener('click', () => showToast('Data pengumuman berhasil disiapkan untuk ekspor.'));

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const id = Number(button.dataset.id);
        const action = button.dataset.action;
        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'edit') {
            openForm(id);
        } else {
            confirmAction(action, id);
        }
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderAll();
    });

    document.getElementById('saveForm').addEventListener('click', () => {
        const title = document.getElementById('formTitleInput').value.trim();
        const category = document.getElementById('formCategory').value;
        const status = document.getElementById('formStatus').value;
        const date = document.getElementById('formDate').value;
        const content = document.getElementById('formContent').value.trim();

        if (!title || !content) {
            showToast('Judul dan isi pengumuman wajib diisi.', false);
            return;
        }

        const endpoint = editingId
            ? `{{ url('/admin/komunikasi/pengumuman') }}/${editingId}`
            : `{{ url('/admin/komunikasi/pengumuman') }}`;
        const method = editingId ? 'PATCH' : 'POST';

        apiRequest(endpoint, method, { judul: title, kategori: category, isi: content, status, tanggal: date || null })
            .then((response) => {
                const saved = response.announcement;

                if (editingId) {
                    const index = announcements.findIndex((announcement) => announcement.id === editingId);
                    if (index >= 0) {
                        announcements[index] = { ...announcements[index], ...saved };
                    }
                    showToast(`Pengumuman "${saved.title}" berhasil diperbarui.`);
                } else {
                    announcements.unshift(saved);
                    showToast(`Pengumuman "${saved.title}" berhasil dibuat.`);
                }

                editingId = null;
                formModal.hide();
                renderAll();
            })
            .catch((error) => {
                showToast(error.message, false);
            });
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        const index = announcements.findIndex((item) => item.id === pendingId);
        if (index < 0 || !pendingAction) return;
        const item = announcements[index];

        if (pendingAction === 'hapus') {
            apiRequest(`{{ url('/admin/komunikasi/pengumuman') }}/${pendingId}`, 'DELETE')
                .then((response) => {
                    announcements.splice(index, 1);
                    pendingAction = null;
                    pendingId = null;
                    confirmModal.hide();
                    renderAll();
                    showToast(response.message || 'Pengumuman berhasil dihapus.');
                })
                .catch((error) => showToast(error.message, false));
            return;
        }

        apiRequest(`{{ url('/admin/komunikasi/pengumuman') }}/${pendingId}`, 'PATCH', {
            judul: item.title,
            kategori: item.category,
            isi: item.content,
            status: pendingAction,
            tanggal: null,
        })
            .then((response) => {
                const saved = response.announcement || {};
                announcements[index] = {
                    ...item,
                    ...saved,
                    status: saved.status || pendingAction,
                };
                pendingAction = null;
                pendingId = null;
                confirmModal.hide();
                renderAll();
                showToast(response.message || 'Pengumuman berhasil dipublikasikan.');
            })
            .catch((error) => showToast(error.message, false));
    });

    renderAll();
</script>
@endpush
