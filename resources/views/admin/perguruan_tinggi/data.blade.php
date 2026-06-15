@extends('admin.layout.admin')

@section('title', 'Data Perguruan Tinggi')

@push('styles')
<style>
    .college-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .college-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .college-page .stat-card,
    .college-page .filter-card,
    .college-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .college-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .college-page .stat-card:hover,
    .college-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .college-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .college-page .growth {
        font-size: 13px;
    }

    .college-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .college-page .table tbody tr {
        transition: 0.15s ease;
    }

    .college-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .college-page .action-group {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        white-space: nowrap;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .college-page .action-group .btn {
        flex: 0 0 auto;
    }

    .college-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .college-page .pagination .page-link {
        color: #0b5f86;
    }

    .college-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid college-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.perguruan-tinggi.index') }}">Manajemen Perguruan Tinggi</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Data Perguruan Tinggi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Perguruan Tinggi</h2>
            <p class="text-muted mb-0">
                Kelola data institusi, status verifikasi, dan tindakan administratif perguruan tinggi.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton">
                <i class="bi bi-download"></i>
                Ekspor
            </button>
            <button class="btn btn-primary" type="button" id="addCollegeButton">
                <i class="bi bi-plus-lg"></i>
                Tambah Perguruan Tinggi
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Perguruan Tinggi</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                        <span class="growth text-success"><i class="bi bi-arrow-up-short"></i> +6 periode ini</span>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-building"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Perguruan Tinggi Aktif</p>
                        <h3 class="mb-0" id="statAktif">0</h3>
                        <span class="growth text-success"><i class="bi bi-check2-circle"></i> stabil</span>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-patch-check-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Menunggu Verifikasi</p>
                        <h3 class="mb-0" id="statMenunggu">0</h3>
                        <span class="growth text-warning"><i class="bi bi-hourglass-split"></i> perlu validasi</span>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="nonaktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Tidak Aktif</p>
                        <h3 class="mb-0" id="statNonaktif">0</h3>
                        <span class="growth text-danger"><i class="bi bi-exclamation-circle"></i> perlu tinjauan</span>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-building-x"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="collegeFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Nama Perguruan Tinggi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari nama perguruan tinggi">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="menunggu">Menunggu Verifikasi</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="typeFilter" class="form-label">Jenis</label>
                        <select class="form-select" id="typeFilter">
                            <option value="semua">Semua Jenis</option>
                            <option value="Negeri">Negeri</option>
                            <option value="Swasta">Swasta</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="regionFilter" class="form-label">Provinsi/Wilayah</label>
                        <select class="form-select" id="regionFilter">
                            <option value="semua">Semua Wilayah</option>
                            <option value="DI Yogyakarta">DI Yogyakarta</option>
                            <option value="Jawa Tengah">Jawa Tengah</option>
                            <option value="Jawa Timur">Jawa Timur</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-primary w-100" type="button" id="searchButton" title="Cari">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" type="button" id="resetFilter" title="Reset">
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
                    <h5 class="mb-1">Tabel Data Perguruan Tinggi</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <span class="badge bg-light text-dark" id="selectedCount">0 data dipilih</span>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40"><input class="form-check-input" type="checkbox" id="checkAll"></th>
                            <th>No</th>
                            <th role="button" data-sort="nama">Nama Perguruan Tinggi <i class="bi bi-arrow-down-up"></i></th>
                            <th>Nama PIC</th>
                            <th>NIP PIC</th>
                            <th>Fakultas</th>
                            <th>Program Studi</th>
                            <th>Jenis</th>
                            <th>Provinsi</th>
                            <th>Status</th>
                            <th>Tanggal Terdaftar</th>
                            <th width="300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="collegeTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data perguruan tinggi tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination perguruan tinggi">
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
                <h5 class="modal-title">Detail Perguruan Tinggi</h5>
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
                <h5 class="modal-title" id="formTitle">Tambah Perguruan Tinggi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="formName">Nama Perguruan Tinggi</label>
                        <input class="form-control" id="formName" placeholder="Masukkan nama perguruan tinggi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formFaculty">Fakultas</label>
                        <input class="form-control" id="formFaculty" placeholder="Nama fakultas">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formStudyProgram">Program Studi</label>
                        <input class="form-control" id="formStudyProgram" placeholder="Nama program studi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formType">Jenis</label>
                        <select class="form-select" id="formType">
                            <option>Negeri</option>
                            <option>Swasta</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formRegion">Provinsi</label>
                        <select class="form-select" id="formRegion">
                            <option>DI Yogyakarta</option>
                            <option>Jawa Tengah</option>
                            <option>Jawa Timur</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formEmail">Email</label>
                        <input class="form-control" id="formEmail" placeholder="contoh@kampus.ac.id">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formPic">Nama PIC</label>
                        <input class="form-control" id="formPic" placeholder="Nama penanggung jawab">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formPicNip">NIP PIC</label>
                        <input class="form-control" id="formPicNip" placeholder="NIP penanggung jawab">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formAddress">Alamat</label>
                        <textarea class="form-control" id="formAddress" rows="3" placeholder="Alamat perguruan tinggi"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="formStatus">Status Kerja Sama</label>
                        <select class="form-select" id="formStatus">
                            <option value="aktif">Aktif</option>
                            <option value="proses">Proses</option>
                            <option value="nonaktif">Tidak Aktif</option>
                        </select>
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Aksi</h5>
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
    <div id="collegeToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const colleges = @json($adminCampuses ?? []);
    const csrfToken = @json(csrf_token());
    const statusUrlTemplate = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.status', ['campus' => '__CAMPUS__']));

    const perPage = 10;
    let currentPage = 1;
    let selectedIds = new Set();
    let pendingAction = null;
    let editingId = null;
    let sortAsc = true;

    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const regionFilter = document.getElementById('regionFilter');
    const tableBody = document.getElementById('collegeTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');
    const selectedCount = document.getElementById('selectedCount');
    const checkAll = document.getElementById('checkAll');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('collegeToast'), { delay: 3000 });
    const confirmActionButton = document.getElementById('confirmAction');
    const deleteUrlTemplate = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.destroy', ['campus' => '__CAMPUS__']));

    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
        confirmActionButton.textContent = 'Ya, Lanjutkan';
        confirmActionButton.className = 'btn btn-primary';
    });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            aktif: 'bg-success',
            menunggu: 'bg-warning text-dark',
            proses: 'bg-warning text-dark',
            nonaktif: 'bg-danger'
        }[value];
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

    function filteredColleges() {
        const keyword = searchInput.value.trim().toLowerCase();
        const status = statusFilter.value;
        const type = typeFilter.value;
        const region = regionFilter.value;

        return colleges
            .filter((college) => {
                const matchKeyword = !keyword || [college.nama, college.pic, college.pic_nip, college.fakultas, college.program_studi, college.jenis, college.provinsi, college.email, college.status]
                    .join(' ')
                    .toLowerCase()
                    .includes(keyword);
                const normalizedStatus = college.status === 'proses' ? 'menunggu' : college.status;
                const matchStatus = status === 'semua' || normalizedStatus === status;
                const matchType = type === 'semua' || college.jenis === type;
                const matchRegion = region === 'semua' || college.provinsi === region;

                return matchKeyword && matchStatus && matchType && matchRegion;
            })
            .sort((a, b) => sortAsc ? a.nama.localeCompare(b.nama) : b.nama.localeCompare(a.nama));
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = colleges.length;
        document.getElementById('statAktif').textContent = colleges.filter((item) => item.status === 'aktif').length;
        document.getElementById('statMenunggu').textContent = colleges.filter((item) => item.status === 'menunggu' || item.status === 'proses').length;
        document.getElementById('statNonaktif').textContent = colleges.filter((item) => item.status === 'nonaktif').length;
    }

    function renderTable() {
        const data = filteredColleges();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((college, index) => `
            <tr>
                <td><input class="form-check-input row-check" type="checkbox" value="${college.id}" ${selectedIds.has(college.id) ? 'checked' : ''}></td>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${college.nama}</td>
                <td>${college.pic}</td>
                <td>${college.pic_nip}</td>
                <td>${college.fakultas}</td>
                <td>${college.program_studi}</td>
                <td><span class="badge bg-primary">${college.jenis}</span></td>
                <td>${college.provinsi}</td>
                <td><span class="badge ${statusClass(college.status)}">${titleCase(college.status === 'proses' ? 'menunggu' : college.status)}</span></td>
                <td>${college.tanggal}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="lihat" data-id="${college.id}" title="Lihat detail">
                            <i class="bi bi-eye"></i> Lihat
                        </button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${college.id}" title="Edit data">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-secondary btn-sm" type="button" data-action="status" data-id="${college.id}" title="Aktifkan/nonaktifkan">
                            <i class="bi bi-toggle2-on"></i> Status
                        </button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${college.id}" title="Hapus data">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} perguruan tinggi ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data`
            : 'Menampilkan 0 data';
        checkAll.checked = pageData.length > 0 && pageData.every((college) => selectedIds.has(college.id));

        renderPagination(totalPages);
        updateSelectedCount();
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

    function updateSelectedCount() {
        selectedCount.textContent = `${selectedIds.size} data dipilih`;
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('collegeToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-warning', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const college = colleges.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama</dt>
                <dd class="col-sm-8">${college.nama}</dd>
                <dt class="col-sm-4">Jenis</dt>
                <dd class="col-sm-8">${college.jenis}</dd>
                <dt class="col-sm-4">Nama PIC</dt>
                <dd class="col-sm-8">${college.pic}</dd>
                <dt class="col-sm-4">NIP PIC</dt>
                <dd class="col-sm-8">${college.pic_nip}</dd>
                <dt class="col-sm-4">Fakultas</dt>
                <dd class="col-sm-8">${college.fakultas}</dd>
                <dt class="col-sm-4">Program Studi</dt>
                <dd class="col-sm-8">${college.program_studi}</dd>
                <dt class="col-sm-4">Provinsi</dt>
                <dd class="col-sm-8">${college.provinsi}</dd>
                <dt class="col-sm-4">Email</dt>
                <dd class="col-sm-8">${college.email}</dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">${titleCase(college.status === 'proses' ? 'menunggu' : college.status)}</dd>
                <dt class="col-sm-4">Tanggal Terdaftar</dt>
                <dd class="col-sm-8">${college.tanggal}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const college = id ? colleges.find((item) => item.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Perguruan Tinggi' : 'Tambah Perguruan Tinggi';
        document.getElementById('formName').value = college?.nama || '';
        document.getElementById('formPic').value = college?.pic || '';
        document.getElementById('formPicNip').value = college?.pic_nip || '';
        document.getElementById('formFaculty').value = college?.fakultas || '';
        document.getElementById('formStudyProgram').value = college?.program_studi || '';
        document.getElementById('formType').value = college?.jenis || 'Negeri';
        document.getElementById('formRegion').value = college?.provinsi || 'DI Yogyakarta';
        document.getElementById('formEmail').value = college?.email || '';
        document.getElementById('formAddress').value = college?.alamat || '';
        document.getElementById('formStatus').value = college?.status || 'aktif';
        formModal.show();
    }

    function showConfirm(id, action) {
        const college = colleges.find((item) => item.id === id);
        const messages = {
            status: `Apakah Anda yakin ingin mengubah status ${college.nama}?`,
            hapus: `Tindakan ini akan menghapus ${college.nama}. Apakah Anda yakin ingin melanjutkan?`
        };
        pendingAction = { id, action };
        document.getElementById('confirmTitle').textContent = `Konfirmasi ${titleCase(action)}`;
        document.getElementById('confirmMessage').textContent = messages[action];
        if (action === 'status') {
            confirmActionButton.textContent = 'Ubah Status';
            confirmActionButton.className = 'btn btn-warning';
        } else if (action === 'hapus') {
            confirmActionButton.textContent = 'Hapus';
            confirmActionButton.className = 'btn btn-danger';
        }
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

    [statusFilter, typeFilter, regionFilter].forEach((input) => {
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
        statusFilter.value = 'semua';
        typeFilter.value = 'semua';
        regionFilter.value = 'semua';
        selectedIds.clear();
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    document.querySelector('[data-sort="nama"]').addEventListener('click', () => {
        sortAsc = !sortAsc;
        renderTable();
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Data perguruan tinggi berhasil disiapkan untuk ekspor.');
    });

    document.getElementById('addCollegeButton').addEventListener('click', () => openForm());

    document.getElementById('saveForm').addEventListener('click', async () => {
        const name = document.getElementById('formName').value.trim();
        const pic = document.getElementById('formPic').value.trim();
        const picNip = document.getElementById('formPicNip').value.trim();
        const faculty = document.getElementById('formFaculty').value.trim();
        const studyProgram = document.getElementById('formStudyProgram').value.trim();
        const email = document.getElementById('formEmail').value.trim();
        const address = document.getElementById('formAddress').value.trim();
        const status = document.getElementById('formStatus').value;

        if (!name || !pic || !picNip || !faculty || !studyProgram || !email || !address || !status) {
            showToast('Nama perguruan tinggi, data PIC, fakultas, program studi, email, alamat, dan status wajib diisi.', false);
            return;
        }

        if (editingId) {
            const college = colleges.find((item) => item.id === editingId);
            const payload = new FormData();
            payload.append('_method', 'PATCH');
            payload.append('nama_pt', name);
            payload.append('jenis', document.getElementById('formType').value);
            payload.append('alamat', address);
            payload.append('email', email);
            payload.append('pic', pic);
            payload.append('pic_nip', picNip);
            payload.append('fakultas', faculty);
            payload.append('program_studi', studyProgram);
            payload.append('status_kerja_sama', status);

            try {
                const data = await sendRequest(@json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.update', ['campus' => '__CAMPUS__'])).replace('__CAMPUS__', editingId), 'POST', payload);
                college.nama = data.campus?.nama ?? name;
                college.pic = pic;
                college.pic_nip = picNip;
                college.fakultas = faculty;
                college.program_studi = studyProgram;
                college.jenis = document.getElementById('formType').value;
                college.provinsi = document.getElementById('formRegion').value;
                college.email = email;
                college.alamat = address;
                college.status = data.campus?.status ?? status;
                formModal.hide();
                updateStats();
                renderTable();
                showToast(data.message || `Data ${college.nama} berhasil diperbarui.`);
            } catch (error) {
                showToast(error.message, false);
            }
            return;
        }

        colleges.unshift({
            id: Date.now(),
            nama: name,
            pic,
            pic_nip: picNip,
            fakultas: faculty,
            program_studi: studyProgram,
            jenis: document.getElementById('formType').value,
            provinsi: document.getElementById('formRegion').value,
            email,
            alamat: address,
            status,
            tanggal: '20 Mei 2026'
        });
        showToast(`Data ${name} berhasil ditambahkan.`);

        formModal.hide();
        updateStats();
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'lihat') {
            showDetail(id);
        } else if (button.dataset.action === 'edit') {
            openForm(id);
        } else {
            showConfirm(id, button.dataset.action);
        }
    });

    tableBody.addEventListener('change', (event) => {
        if (!event.target.classList.contains('row-check')) {
            return;
        }

        const id = Number(event.target.value);
        if (event.target.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        renderTable();
    });

    checkAll.addEventListener('change', () => {
        filteredColleges()
            .slice((currentPage - 1) * perPage, currentPage * perPage)
            .forEach((college) => {
                if (checkAll.checked) {
                    selectedIds.add(college.id);
                } else {
                    selectedIds.delete(college.id);
                }
            });

        renderTable();
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('confirmAction').addEventListener('click', async () => {
        if (!pendingAction) {
            return;
        }

        const college = colleges.find((item) => item.id === pendingAction.id);
        if (!college) {
            confirmModal.hide();
            pendingAction = null;
            return;
        }

        try {
            let message = '';

            if (pendingAction.action === 'status') {
                const data = await sendRequest(statusUrlTemplate.replace('__CAMPUS__', college.id), 'PATCH');
                college.status = data.status || college.status;
                message = data.message || `Status ${college.nama} berhasil diubah.`;
            } else if (pendingAction.action === 'hapus') {
                const data = await sendRequest(deleteUrlTemplate.replace('__CAMPUS__', college.id), 'DELETE');
                const index = colleges.findIndex((item) => item.id === pendingAction.id);
                if (index !== -1) {
                    colleges.splice(index, 1);
                }
                selectedIds.delete(pendingAction.id);
                message = data.message || `Data ${college.nama} berhasil dihapus.`;
            }

            confirmModal.hide();
            updateStats();
            renderTable();
            showToast(message);
        } catch (error) {
            showToast(error.message, false);
        } finally {
            pendingAction = null;
        }
    });

    updateStats();
    renderTable();
</script>
@endpush
