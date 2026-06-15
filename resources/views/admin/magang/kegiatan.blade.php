@extends('admin.layout.admin')

@section('title', 'Kegiatan Magang')

@push('styles')
<style>
    .activity-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .activity-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .activity-page .stat-card,
    .activity-page .filter-card,
    .activity-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .activity-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .activity-page .stat-card:hover,
    .activity-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .activity-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .activity-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .activity-page .table tbody tr {
        transition: 0.15s ease;
    }

    .activity-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .activity-page .action-group {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        white-space: nowrap;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .activity-page .action-group .btn {
        flex: 0 0 auto;
    }

    .activity-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .activity-page .pagination .page-link {
        color: #0b5f86;
    }

    .activity-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid activity-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kegiatan Magang</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Kegiatan Magang</h2>
            <p class="text-muted mb-0">
                Monitoring, evaluasi, dan dokumentasi aktivitas peserta magang.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton">
                <i class="bi bi-download"></i>
                Ekspor Kegiatan
            </button>
            <button class="btn btn-primary" type="button" id="addActivityButton">
                <i class="bi bi-plus-lg"></i>
                Tambah Kegiatan
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kegiatan</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-list-task"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Selesai</p>
                        <h3 class="mb-0" id="statSelesai">0</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="berlangsung">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Sedang Berlangsung</p>
                        <h3 class="mb-0" id="statBerlangsung">0</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-play-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="belum dimulai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Belum Dimulai</p>
                        <h3 class="mb-0" id="statBelum">0</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-report-card="dibuat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Laporan Dibuat</p>
                        <h3 class="mb-0" id="statLaporan">0</h3>
                    </div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-file-earmark-text"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="activityFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Mahasiswa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari Mahasiswa">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="periodFilter" class="form-label">Periode</label>
                        <select class="form-select" id="periodFilter">
                            <option value="semua">Semua Periode</option>
                            <option value="Batch 1 2026">Batch 1 2026</option>
                            <option value="Batch 2 2026">Batch 2 2026</option>
                            <option value="MBKM 2026">MBKM 2026</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="studyFilter" class="form-label">Program Studi</label>
                        <select class="form-select" id="studyFilter">
                            <option value="semua">Semua Prodi</option>
                            <option value="Informatika">Informatika</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Administrasi Publik">Administrasi Publik</option>
                            <option value="Akuntansi">Akuntansi</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="agencyFilter" class="form-label">Penempatan</label>
                        <select class="form-select" id="agencyFilter">
                            <option value="semua">Semua Penempatan</option>
                            <option value="Sub Bagian Umum">Sub Bagian Umum</option>
                            <option value="Sub Bagian Kepegawaian">Sub Bagian Kepegawaian</option>
                            <option value="Sub Bagian Keuangan">Sub Bagian Keuangan</option>
                            <option value="Sub Bagian Program">Sub Bagian Program</option>
                            <option value="Sub Bagian Humas dan Kerja Sama">Sub Bagian Humas dan Kerja Sama</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="selesai">Selesai</option>
                            <option value="berlangsung">Sedang Berlangsung</option>
                            <option value="belum dimulai">Belum Dimulai</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex gap-2">
                        <button class="btn btn-primary w-50" type="button" id="searchButton" title="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                        <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Data Kegiatan Magang</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Penempatan</th>
                            <th>Kegiatan</th>
                            <th>Status</th>
                            <th>Laporan</th>
                            <th width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="activityTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data kegiatan tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination kegiatan magang">
                    <ul class="pagination mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kegiatan Magang</h5>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formTitle">Tambah Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label" for="formTitleInput">Judul Kegiatan</label>
                        <input class="form-control" id="formTitleInput" placeholder="Judul kegiatan" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="formRecipient">Penerima Kegiatan (Peserta Magang)</label>
                        <select class="form-select" id="formRecipient" required>
                            <option value="">Pilih peserta magang</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="formCategory">Kategori</label>
                        <select class="form-select" id="formCategory" required>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Analisis">Analisis</option>
                            <option value="Dokumentasi">Dokumentasi</option>
                            <option value="Pengembangan">Pengembangan</option>
                        </select>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="formDeadline">Deadline</label>
                        <input type="date" class="form-control" id="formDeadline" required>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="formFile">Upload Berkas</label>
                        <input type="file" class="form-control" id="formFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar">
                        <div class="form-text">Berkas tugas, pedoman, atau lampiran kegiatan.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formNote">Catatan</label>
                        <textarea class="form-control" id="formNote" rows="3" placeholder="Catatan kegiatan"></textarea>
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
    <div id="activityToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const activities = @json($adminMagangActivities ?? []);
    const participants = @json($adminMagangParticipants ?? []);
    const csrfToken = @json(csrf_token());
    const storeUrl = @json(route('admin.magang.kegiatan.store'));
    const updateUrlTemplate = @json(route('admin.magang.kegiatan.update', ['assignment' => '__ASSIGNMENT__']));
    const destroyUrlTemplate = @json(route('admin.magang.kegiatan.destroy', ['assignment' => '__ASSIGNMENT__']));

    const perPage = 10;
    let currentPage = 1;
    let pendingAction = null;
    let editingId = null;

    const searchInput = document.getElementById('searchInput');
    const periodFilter = document.getElementById('periodFilter');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('activityTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');
    const formRecipient = document.getElementById('formRecipient');
    const formTitleInput = document.getElementById('formTitleInput');
    const formCategory = document.getElementById('formCategory');
    const formDeadline = document.getElementById('formDeadline');
    const formFile = document.getElementById('formFile');
    const formNote = document.getElementById('formNote');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('activityToast'), { delay: 3000 });

    function statusClass(value) {
        return {
            selesai: 'bg-success',
            berlangsung: 'bg-info text-dark',
            'belum dimulai': 'bg-warning text-dark'
        }[value];
    }

    function reportClass(value) {
        return value === 'dibuat' ? 'bg-success' : 'bg-secondary';
    }

    function titleCase(value) {
        return String(value || '').split(' ').filter(Boolean).map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function normalizeStatus(value) {
        const lowered = String(value || '').toLowerCase();
        if (['selesai', 'done', 'completed', 'disetujui'].includes(lowered)) return 'selesai';
        if (['terlambat', 'late'].includes(lowered)) return 'terlambat';
        if (['aktif', 'belum_dikerjakan', 'belum dikerjakan', 'draft', 'pending', 'menunggu', 'belum dimulai'].includes(lowered)) return lowered === 'aktif' ? 'berlangsung' : 'belum dimulai';
        return lowered || 'belum dimulai';
    }

    async function sendRequest(url, method, body = null) {
        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        };

        if (body instanceof FormData) {
            options.body = body;
        } else if (body) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        const response = await fetch(url, options);
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationMessage = data.errors
                ? Object.values(data.errors).flat().find(Boolean)
                : null;
            throw new Error(validationMessage || data.message || 'Permintaan gagal diproses.');
        }

        return data;
    }

    function populateRecipients() {
        const uniqueParticipants = participants
            .filter((item) => item.id && item.nama && item.nama !== '-')
            .filter((item, index, array) => array.findIndex((row) => row.id === item.id) === index);

        formRecipient.innerHTML = '<option value="">Pilih peserta magang</option>' + uniqueParticipants
            .map((item) => `<option value="${item.id}">${item.nama} - ${item.nim ?? '-'}</option>`)
            .join('');
    }

    function filteredActivities() {
        const keyword = searchInput.value.trim().toLowerCase();
        const period = periodFilter.value;
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;

        return activities.filter((activity) => {
            const matchKeyword = !keyword || [activity.nama, activity.nim, activity.kegiatan, activity.prodi, activity.penempatan ?? activity.instansi, activity.laporan].join(' ').toLowerCase().includes(keyword);
            const matchPeriod = period === 'semua' || activity.periode === period;
            const matchStudy = study === 'semua' || activity.prodi === study;
            const matchAgency = agency === 'semua' || (activity.penempatan ?? activity.instansi) === agency;
            const matchStatus = status === 'semua' || activity.status === status;

            return matchKeyword && matchPeriod && matchStudy && matchAgency && matchStatus;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = activities.length;
        document.getElementById('statSelesai').textContent = activities.filter((item) => item.status === 'selesai').length;
        document.getElementById('statBerlangsung').textContent = activities.filter((item) => item.status === 'berlangsung').length;
        document.getElementById('statBelum').textContent = activities.filter((item) => item.status === 'belum dimulai').length;
        document.getElementById('statLaporan').textContent = activities.filter((item) => item.laporan === 'dibuat').length;
    }

    function renderTable() {
        const data = filteredActivities();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((activity, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${activity.tanggal}</td>
                <td class="fw-semibold">${activity.nama}</td>
                <td>${activity.nim}</td>
                <td>${activity.prodi}</td>
                <td>${activity.penempatan ?? activity.instansi}</td>
                <td>${activity.kegiatan}</td>
                <td><span class="badge ${statusClass(activity.status)}">${titleCase(activity.status)}</span></td>
                <td><span class="badge ${reportClass(activity.laporan)}">${titleCase(activity.laporan)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${activity.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${activity.id}">Edit</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${activity.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} kegiatan ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} kegiatan`
            : 'Menampilkan 0 kegiatan';

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" type="button" data-page="${currentPage - 1}">Previous</button>
            </li>
        `;

        for (let page = 1; page <= totalPages; page++) {
            html += `
                <li class="page-item ${page === currentPage ? 'active' : ''}">
                    <button class="page-link" type="button" data-page="${page}">${page}</button>
                </li>
            `;
        }

        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <button class="page-link" type="button" data-page="${currentPage + 1}">Next</button>
            </li>
        `;

        pagination.innerHTML = html;
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('activityToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const activity = activities.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Mahasiswa</dt>
                <dd class="col-sm-8">${activity.nama}</dd>
                <dt class="col-sm-4">NIM</dt>
                <dd class="col-sm-8">${activity.nim}</dd>
                <dt class="col-sm-4">Program Studi</dt>
                <dd class="col-sm-8">${activity.prodi}</dd>
                <dt class="col-sm-4">Penempatan</dt>
                <dd class="col-sm-8">${activity.penempatan ?? activity.instansi}</dd>
                <dt class="col-sm-4">Tanggal</dt>
                <dd class="col-sm-8">${activity.tanggal}</dd>
                <dt class="col-sm-4">Kegiatan</dt>
                <dd class="col-sm-8">${activity.kegiatan}</dd>
                <dt class="col-sm-4">Pemberi</dt>
                <dd class="col-sm-8">${activity.pemberi ?? '-'}</dd>
                <dt class="col-sm-4">Deadline</dt>
                <dd class="col-sm-8">${activity.deadline ?? '-'}</dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">${titleCase(activity.status)}</dd>
                <dt class="col-sm-4">Laporan</dt>
                <dd class="col-sm-8">${titleCase(activity.laporan)}</dd>
                <dt class="col-sm-4">Catatan</dt>
                <dd class="col-sm-8">${activity.catatan ?? '-'}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const activity = id ? activities.find((item) => item.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Kegiatan' : 'Tambah Kegiatan';
        formTitleInput.value = activity?.judul || activity?.kegiatan || '';
        formRecipient.value = activity?.peserta_id || '';
        formCategory.value = activity?.kategori_key || activity?.kategori || 'Administrasi';
        formDeadline.value = activity?.deadline_raw || '';
        formNote.value = activity?.catatan || '';
        formFile.value = '';
        formFile.required = !id;
        formModal.show();
    }

    function showConfirm(id, action) {
        const activity = activities.find((item) => item.id === id);
        pendingAction = { id, action };
        document.getElementById('confirmTitle').textContent = 'Konfirmasi Hapus Kegiatan';
        document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin menghapus kegiatan ${activity.nama}?`;
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    document.querySelector('[data-report-card]').addEventListener('click', () => {
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-report-card]').classList.add('active');
        statusFilter.value = 'semua';
        searchInput.value = 'dibuat';
        currentPage = 1;
        renderTable();
    });

    [periodFilter, studyFilter, agencyFilter, statusFilter].forEach((input) => {
        input.addEventListener('change', () => {
            currentPage = 1;
            renderTable();
        });
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderTable();
        }
    });

    document.getElementById('searchButton').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        periodFilter.value = 'semua';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card], [data-report-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Data kegiatan berhasil disiapkan untuk ekspor.');
    });

    document.getElementById('addActivityButton').addEventListener('click', () => openForm());

    populateRecipients();

    document.getElementById('saveForm').addEventListener('click', () => {
        const title = formTitleInput.value.trim();
        const participantId = formRecipient.value;
        const category = formCategory.value.trim();
        const deadline = formDeadline.value;
        const note = formNote.value.trim();
        const file = formFile.files?.[0] || null;

        if (!title || !participantId || !category || !deadline || (!editingId && !file)) {
            showToast('Judul, peserta tujuan, kategori, deadline, dan file wajib diisi.', false);
            return;
        }

        const payload = new FormData();
        payload.append('peserta_id', participantId);
        payload.append('judul', title);
        payload.append('kategori', category);
        payload.append('deadline', deadline);
        payload.append('catatan', note);
        if (file) {
            payload.append('file', file);
        }

        const targetUrl = editingId
            ? updateUrlTemplate.replace('__ASSIGNMENT__', editingId)
            : storeUrl;
        if (editingId) {
            payload.append('_method', 'PATCH');
        }

        sendRequest(targetUrl, 'POST', payload)
            .then((data) => {
                const row = data.activity;
                if (editingId) {
                    const index = activities.findIndex((item) => item.id === editingId);
                    if (index !== -1 && row) {
                        activities[index] = row;
                    }
                } else if (row) {
                    activities.unshift(row);
                }

                formModal.hide();
                updateStats();
                renderTable();
                showToast(data.message || `Kegiatan "${title}" berhasil disimpan.`);
            })
            .catch((error) => {
                showToast(error.message, false);
            });
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'detail') {
            showDetail(id);
        } else if (button.dataset.action === 'edit') {
            openForm(id);
        } else {
            showConfirm(id, button.dataset.action);
        }
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) {
            return;
        }

        const index = activities.findIndex((item) => item.id === pendingAction.id);
        const activity = activities[index];

        if (pendingAction.action === 'hapus') {
            sendRequest(destroyUrlTemplate.replace('__ASSIGNMENT__', pendingAction.id), 'DELETE')
                .then((data) => {
                    activities.splice(index, 1);
                    confirmModal.hide();
                    updateStats();
                    renderTable();
                    showToast(data.message || `Kegiatan ${activity.nama} berhasil dihapus.`);
                })
                .catch((error) => {
                    showToast(error.message, false);
                })
                .finally(() => {
                    pendingAction = null;
                });
            return;
        }

        confirmModal.hide();
        updateStats();
        renderTable();
        pendingAction = null;
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    updateStats();
    renderTable();
</script>
@endpush
