@extends('admin.layout.admin')

@section('title', 'Data Pembimbing Akademik')

@push('styles')
<style>
    .advisor-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .advisor-page .stat-card,
    .advisor-page .filter-card,
    .advisor-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .advisor-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .advisor-page .stat-card:hover,
    .advisor-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .advisor-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .advisor-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .advisor-page .table tbody tr {
        transition: 0.15s ease;
    }

    .advisor-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .advisor-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .advisor-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .advisor-page .pagination .page-link {
        color: #0b5f86;
    }

    .advisor-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid advisor-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Pembimbing Akademik</h2>
            <p class="text-muted mb-0">
                Kelola data dosen pembimbing yang memantau dan mengevaluasi kegiatan magang mahasiswa.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h5 class="mb-1">Tambah Pembimbing Akademik</h5>
                <p class="text-muted mb-0">Gunakan formulir yang sama seperti pendaftaran akun untuk menambahkan pembimbing baru.</p>
            </div>
            <button class="btn btn-primary" type="button" id="openAddAdvisorModal">
                <i class="bi bi-person-plus"></i> Tambah Pembimbing
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Pembimbing</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary">
                        <i class="bi bi-person-workspace"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pembimbing Aktif</p>
                        <h3 class="mb-0" id="statAktif">0</h3>
                    </div>
                    <span class="stat-icon bg-success">
                        <i class="bi bi-person-check-fill"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Menunggu Verifikasi</p>
                        <h3 class="mb-0" id="statMenunggu">0</h3>
                    </div>
                    <span class="stat-icon bg-warning">
                        <i class="bi bi-hourglass-split"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="nonaktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pembimbing Nonaktif</p>
                        <h3 class="mb-0" id="statNonaktif">0</h3>
                    </div>
                    <span class="stat-icon bg-danger">
                        <i class="bi bi-person-x-fill"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="advisorFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                            <option value="menunggu">Menunggu Verifikasi</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="campusFilter" class="form-label">Perguruan Tinggi</label>
                        <select class="form-select" id="campusFilter">
                            <option value="semua">Semua Perguruan Tinggi</option>
                            <option value="Universitas Negeri Yogyakarta">Universitas Negeri Yogyakarta</option>
                            <option value="Universitas Gadjah Mada">Universitas Gadjah Mada</option>
                            <option value="Universitas Ahmad Dahlan">Universitas Ahmad Dahlan</option>
                            <option value="Universitas Atma Jaya Yogyakarta">Universitas Atma Jaya Yogyakarta</option>
                            <option value="Universitas Sanata Dharma">Universitas Sanata Dharma</option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="searchInput" class="form-label">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari nama, NIDN/NIP, email, atau kampus">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" type="button" id="resetFilter" title="Reset filter">
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
                    <h5 class="mb-1">Daftar Pembimbing Akademik</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <span class="badge bg-light text-dark" id="selectedCount">0 pembimbing dipilih</span>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </th>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIDN/NIP</th>
                            <th>Perguruan Tinggi</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="advisorTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data pembimbing akademik tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination pembimbing akademik">
                    <ul class="pagination mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="advisorFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('admin.pengguna.pembimbing.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pembimbing Akademik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="advisorUsername">Username</label>
                            <input class="form-control" id="advisorUsername" name="username" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorEmail">Email</label>
                            <input class="form-control" id="advisorEmail" name="email" type="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorPassword">Password</label>
                            <input class="form-control" id="advisorPassword" name="password" type="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorPasswordConfirmation">Konfirmasi Password</label>
                            <input class="form-control" id="advisorPasswordConfirmation" name="password_confirmation" type="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorNidn">NIDN/NIP</label>
                            <input class="form-control" id="advisorNidn" name="nidn_nip" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorName">Nama Lengkap</label>
                            <input class="form-control" id="advisorName" name="name" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorTempatLahir">Tempat Lahir</label>
                            <input class="form-control" id="advisorTempatLahir" name="tempat_lahir" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorTanggalLahir">Tanggal Lahir</label>
                            <input class="form-control" id="advisorTanggalLahir" name="tanggal_lahir" type="date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <label class="btn btn-outline-secondary">
                                    <input type="radio" name="jenis_kelamin" value="Laki-laki" class="me-1" required> Laki-laki
                                </label>
                                <label class="btn btn-outline-secondary">
                                    <input type="radio" name="jenis_kelamin" value="Perempuan" class="me-1" required> Perempuan
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorPhone">Nomor Telepon</label>
                            <input class="form-control" id="advisorPhone" name="no_hp" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="advisorAddress">Alamat Lengkap</label>
                            <textarea class="form-control" id="advisorAddress" name="alamat" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorCampus">Perguruan Tinggi</label>
                            <input class="form-control" id="advisorCampus" name="perguruan_tinggi" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="advisorStudy">Program Studi</label>
                            <input class="form-control" id="advisorStudy" name="program_studi" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="advisorJabatan">Jabatan Akademik</label>
                            <input class="form-control" id="advisorJabatan" name="jabatan" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="advisorFoto">Foto Profil</label>
                            <input class="form-control" id="advisorFoto" name="foto" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pembimbing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembimbing Akademik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editAdvisorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editAdvisorForm" novalidate>
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Edit Pembimbing Akademik</h5>
                        <small class="text-muted">Perubahan akan disimpan langsung ke database.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editAdvisorId">
                    <input type="hidden" id="editAdvisorUserId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorName">Nama Lengkap</label>
                            <input class="form-control" id="editAdvisorName" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorUsername">Username</label>
                            <input class="form-control" id="editAdvisorUsername" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorEmail">Email</label>
                            <input class="form-control" id="editAdvisorEmail" type="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorStatus">Status Akun</label>
                            <select class="form-select" id="editAdvisorStatus" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="menunggu">Menunggu Verifikasi</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorNidn">NIDN/NIP</label>
                            <input class="form-control" id="editAdvisorNidn" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorBirthPlace">Tempat Lahir</label>
                            <input class="form-control" id="editAdvisorBirthPlace" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorBirthDate">Tanggal Lahir</label>
                            <input class="form-control" id="editAdvisorBirthDate" type="date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorGender">Jenis Kelamin</label>
                            <select class="form-select" id="editAdvisorGender" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorPhone">Nomor Telepon</label>
                            <input class="form-control" id="editAdvisorPhone" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorCampus">Perguruan Tinggi</label>
                            <input class="form-control" id="editAdvisorCampus" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorStudy">Program Studi</label>
                            <input class="form-control" id="editAdvisorStudy" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAdvisorJabatan">Jabatan</label>
                            <input class="form-control" id="editAdvisorJabatan" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="editAdvisorAddress">Alamat Lengkap</label>
                            <textarea class="form-control" id="editAdvisorAddress" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="saveAdvisorEditButton">Simpan Perubahan</button>
                </div>
            </form>
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
                <p class="mb-3" id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="advisorToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const advisors = @json($adminAdvisors ?? []);
    const csrfToken = @json(csrf_token());
    const updateUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.update', ['user' => '__USER__']));
    const deleteUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.destroy', ['user' => '__USER__']));

    const perPage = 10;
    let currentPage = 1;
    let selectedIds = new Set();
    let pendingAction = null;

    const statusFilter = document.getElementById('statusFilter');
    const campusFilter = document.getElementById('campusFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('advisorTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');
    const selectedCount = document.getElementById('selectedCount');
    const checkAll = document.getElementById('checkAll');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const editModalElement = document.getElementById('editAdvisorModal');
    const editModal = new bootstrap.Modal(editModalElement);
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const advisorFormModal = new bootstrap.Modal(document.getElementById('advisorFormModal'));
    const toast = new bootstrap.Toast(document.getElementById('advisorToast'), { delay: 3000 });
    const confirmActionButton = document.getElementById('confirmAction');

    const editAdvisorForm = document.getElementById('editAdvisorForm');
    const editAdvisorId = document.getElementById('editAdvisorId');
    const editAdvisorUserId = document.getElementById('editAdvisorUserId');
    const editAdvisorName = document.getElementById('editAdvisorName');
    const editAdvisorUsername = document.getElementById('editAdvisorUsername');
    const editAdvisorEmail = document.getElementById('editAdvisorEmail');
    const editAdvisorStatus = document.getElementById('editAdvisorStatus');
    const editAdvisorNidn = document.getElementById('editAdvisorNidn');
    const editAdvisorBirthPlace = document.getElementById('editAdvisorBirthPlace');
    const editAdvisorBirthDate = document.getElementById('editAdvisorBirthDate');
    const editAdvisorGender = document.getElementById('editAdvisorGender');
    const editAdvisorPhone = document.getElementById('editAdvisorPhone');
    const editAdvisorCampus = document.getElementById('editAdvisorCampus');
    const editAdvisorStudy = document.getElementById('editAdvisorStudy');
    const editAdvisorJabatan = document.getElementById('editAdvisorJabatan');
    const editAdvisorAddress = document.getElementById('editAdvisorAddress');

    editModalElement.addEventListener('hidden.bs.modal', () => {
        editAdvisorForm.reset();
    });

    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
        confirmActionButton.textContent = 'Konfirmasi';
        confirmActionButton.className = 'btn btn-primary';
    });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            aktif: 'bg-success',
            nonaktif: 'bg-danger',
            menunggu: 'bg-warning text-dark',
            ditolak: 'bg-secondary'
        }[value];
    }

    function formatDisplayDate(value) {
        if (!value) {
            return '-';
        }

        const parsed = new Date(String(value).includes('T') ? value : `${value}T00:00:00`);
        if (Number.isNaN(parsed.getTime())) {
            return value;
        }

        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }).format(parsed);
    }

    function filteredAdvisors() {
        const status = statusFilter.value;
        const campus = campusFilter.value;
        const keyword = searchInput.value.trim().toLowerCase();

        return advisors.filter((advisor) => {
            const matchStatus = status === 'semua' || advisor.status === status;
            const matchCampus = campus === 'semua' || advisor.kampus === campus;
            const matchKeyword = !keyword || [advisor.nama, advisor.nidn, advisor.kampus, advisor.email, advisor.status]
                .join(' ')
                .toLowerCase()
                .includes(keyword);

            return matchStatus && matchCampus && matchKeyword;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = advisors.length;
        document.getElementById('statAktif').textContent = advisors.filter((item) => item.status === 'aktif').length;
        document.getElementById('statMenunggu').textContent = advisors.filter((item) => item.status === 'menunggu').length;
        document.getElementById('statNonaktif').textContent = advisors.filter((item) => item.status === 'nonaktif').length;
    }

    function renderTable() {
        const data = filteredAdvisors();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((advisor, index) => `
            <tr>
                <td>
                    <input class="form-check-input row-check" type="checkbox" value="${advisor.id}" ${selectedIds.has(advisor.id) ? 'checked' : ''}>
                </td>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${advisor.nama}</td>
                <td>${advisor.nidn}</td>
                <td>${advisor.kampus}</td>
                <td>${advisor.email}</td>
                <td><span class="badge ${statusClass(advisor.status)}">${titleCase(advisor.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${advisor.id}" data-user-id="${advisor.user_id}">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${advisor.id}" data-user-id="${advisor.user_id}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="hapus" data-id="${advisor.id}" data-user-id="${advisor.user_id}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} pembimbing ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} pembimbing`
            : 'Menampilkan 0 pembimbing';
        checkAll.checked = pageData.length > 0 && pageData.every((advisor) => selectedIds.has(advisor.id));

        renderPagination(totalPages);
        updateSelectedCount();
    }

    function renderPagination(totalPages) {
        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" type="button" data-page="${currentPage - 1}">Prev</button>
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
        selectedCount.textContent = `${selectedIds.size} pembimbing dipilih`;
    }

    function showDetail(id) {
        const advisor = advisors.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            ${advisor.foto ? `<div class="text-center mb-3"><img src="${advisor.foto}" alt="Foto ${advisor.nama}" class="rounded" style="width:110px;height:110px;object-fit:cover"></div>` : ''}
            <h6 class="fw-bold border-bottom pb-2">Data Akun</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Username</dt><dd class="col-sm-8">${advisor.username}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">${advisor.email}</dd>
            </dl>
            <h6 class="fw-bold border-bottom pb-2 mt-3">Data Identitas Pembimbing</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">NIDN/NIP</dt><dd class="col-sm-8">${advisor.nidn}</dd>
                <dt class="col-sm-4">Nama Lengkap</dt><dd class="col-sm-8">${advisor.nama}</dd>
                <dt class="col-sm-4">Tempat Lahir</dt><dd class="col-sm-8">${advisor.tempat_lahir}</dd>
                <dt class="col-sm-4">Tanggal Lahir</dt><dd class="col-sm-8">${formatDisplayDate(advisor.tanggal_lahir_raw || advisor.tanggal_lahir)}</dd>
                <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8">${advisor.jenis_kelamin}</dd>
                <dt class="col-sm-4">Nomor Telepon</dt><dd class="col-sm-8">${advisor.no_hp}</dd>
                <dt class="col-sm-4">Alamat Lengkap</dt><dd class="col-sm-8">${advisor.alamat}</dd>
                <dt class="col-sm-4">Perguruan Tinggi</dt><dd class="col-sm-8">${advisor.kampus}</dd>
                <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${advisor.program_studi}</dd>
                <dt class="col-sm-4">Jabatan</dt><dd class="col-sm-8">${advisor.jabatan}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function showConfirm(id, action) {
        const advisor = advisors.find((item) => item.id === id);
        pendingAction = { id, action };

        document.getElementById('confirmTitle').textContent = 'Konfirmasi Hapus';
        document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin menghapus data pembimbing ${advisor.nama}?`;
        confirmActionButton.textContent = 'Hapus';
        confirmActionButton.className = 'btn btn-danger';
        confirmModal.show();
    }

    function openEditModal(advisor) {
        editAdvisorId.value = advisor.id;
        editAdvisorUserId.value = advisor.user_id;
        editAdvisorName.value = advisor.nama ?? '';
        editAdvisorUsername.value = advisor.username ?? '';
        editAdvisorEmail.value = advisor.email ?? '';
        editAdvisorStatus.value = advisor.status ?? 'aktif';
        editAdvisorNidn.value = advisor.nidn ?? '';
        editAdvisorBirthPlace.value = advisor.tempat_lahir ?? '';
        editAdvisorBirthDate.value = advisor.tanggal_lahir_raw ?? '';
        editAdvisorGender.value = advisor.jenis_kelamin ?? '';
        editAdvisorPhone.value = advisor.no_hp ?? '';
        editAdvisorCampus.value = advisor.perguruan_tinggi_raw ?? advisor.kampus ?? '';
        editAdvisorStudy.value = advisor.program_studi ?? '';
        editAdvisorJabatan.value = advisor.jabatan ?? '';
        editAdvisorAddress.value = advisor.alamat ?? '';
        editModal.show();
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

    function showToast(message, success = true) {
        const toastElement = document.getElementById('advisorToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    document.getElementById('openAddAdvisorModal').addEventListener('click', () => {
        advisorFormModal.show();
    });

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

    [statusFilter, campusFilter].forEach((input) => {
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

    document.getElementById('resetFilter').addEventListener('click', () => {
        statusFilter.value = 'semua';
        campusFilter.value = 'semua';
        searchInput.value = '';
        selectedIds.clear();
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'detail') {
            showDetail(id);
            return;
        }

        if (button.dataset.action === 'edit') {
            const advisor = advisors.find((item) => item.id === id);
            if (advisor) {
                openEditModal(advisor);
            }
            return;
        }

        showConfirm(id, button.dataset.action);
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
        filteredAdvisors()
            .slice((currentPage - 1) * perPage, currentPage * perPage)
            .forEach((advisor) => {
                if (checkAll.checked) {
                    selectedIds.add(advisor.id);
                } else {
                    selectedIds.delete(advisor.id);
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

        const advisor = advisors.find((item) => item.id === pendingAction.id);
        if (!advisor) {
            confirmModal.hide();
            pendingAction = null;
            return;
        }

        try {
            const data = await sendRequest(deleteUrlTemplate.replace('__USER__', advisor.user_id), 'DELETE');
            const advisorIndex = advisors.findIndex((item) => item.id === pendingAction.id);
            if (advisorIndex !== -1) {
                advisors.splice(advisorIndex, 1);
                selectedIds.delete(pendingAction.id);
            }

            confirmModal.hide();
            confirmActionButton.textContent = 'Konfirmasi';
            confirmActionButton.className = 'btn btn-primary';
            updateStats();
            renderTable();
            showToast(data.message || `Data pembimbing ${advisor.nama} berhasil dihapus.`);
        } catch (error) {
            showToast(error.message, false);
        } finally {
            pendingAction = null;
        }
    });

    editAdvisorForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const advisor = advisors.find((item) => item.id === Number(editAdvisorId.value));
        if (!advisor) {
            showToast('Data pembimbing tidak ditemukan.', false);
            return;
        }

        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('name', editAdvisorName.value.trim());
        formData.append('username', editAdvisorUsername.value.trim());
        formData.append('email', editAdvisorEmail.value.trim());
        formData.append('account_status', editAdvisorStatus.value);
        formData.append('nidn', editAdvisorNidn.value.trim());
        formData.append('tempat_lahir', editAdvisorBirthPlace.value.trim());
        formData.append('tanggal_lahir', editAdvisorBirthDate.value);
        formData.append('jenis_kelamin', editAdvisorGender.value);
        formData.append('no_hp', editAdvisorPhone.value.trim());
        formData.append('alamat', editAdvisorAddress.value.trim());
        formData.append('perguruan_tinggi', editAdvisorCampus.value.trim());
        formData.append('program_studi', editAdvisorStudy.value.trim());
        formData.append('jabatan', editAdvisorJabatan.value.trim());

        try {
            const data = await sendRequest(updateUrlTemplate.replace('__USER__', editAdvisorUserId.value), 'POST', formData);
            advisor.nama = data.user?.name ?? advisor.nama;
            advisor.username = data.user?.username ?? advisor.username;
            advisor.email = data.user?.email ?? advisor.email;
            advisor.status = data.user?.status ?? advisor.status;
            advisor.nidn = data.user?.nidn ?? advisor.nidn;
            advisor.tempat_lahir = data.user?.tempat_lahir ?? advisor.tempat_lahir;
            advisor.tanggal_lahir = formatDisplayDate(data.user?.tanggal_lahir ?? advisor.tanggal_lahir_raw ?? advisor.tanggal_lahir);
            advisor.tanggal_lahir_raw = data.user?.tanggal_lahir ?? advisor.tanggal_lahir_raw;
            advisor.jenis_kelamin = data.user?.jenis_kelamin ?? advisor.jenis_kelamin;
            advisor.no_hp = data.user?.no_hp ?? advisor.no_hp;
            advisor.alamat = data.user?.alamat ?? advisor.alamat;
            advisor.kampus = data.user?.kampus ?? advisor.kampus;
            advisor.perguruan_tinggi_raw = data.user?.kampus ?? advisor.perguruan_tinggi_raw;
            advisor.program_studi = data.user?.program_studi ?? advisor.program_studi;
            advisor.jabatan = data.user?.jabatan ?? advisor.jabatan;
            editModal.hide();
            updateStats();
            renderTable();
            showToast(data.message || `Data pembimbing ${advisor.nama} berhasil diperbarui.`);
        } catch (error) {
            showToast(error.message, false);
        }
    });

    updateStats();
    renderTable();
</script>
@endpush
