@extends('admin.layout.admin')

@section('title', 'Data Mentor')

@push('styles')
<style>
    .mentor-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .mentor-page .stat-card,
    .mentor-page .filter-card,
    .mentor-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .mentor-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .mentor-page .stat-card:hover,
    .mentor-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .mentor-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .mentor-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .mentor-page .table tbody tr {
        transition: 0.15s ease;
    }

    .mentor-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .mentor-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .mentor-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .mentor-page .pagination .page-link {
        color: #0b5f86;
    }

    .mentor-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid mentor-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Mentor</h2>
            <p class="text-muted mb-0">
                Kelola data mentor yang bertugas membimbing peserta magang selama kegiatan berlangsung.
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
                <h5 class="mb-1">Tambah Mentor Baru</h5>
                <p class="text-muted mb-0">Gunakan formulir yang sama seperti pendaftaran mentor untuk menambahkan akun baru ke database.</p>
            </div>
            <button class="btn btn-primary" type="button" id="openAddMentorModal">
                <i class="bi bi-person-plus"></i> Tambah Mentor
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Mentor</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary">
                        <i class="bi bi-person-badge-fill"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Mentor Aktif</p>
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
                        <p class="text-muted mb-1">Mentor Nonaktif</p>
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
            <form id="mentorFilterForm">
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
                        <label for="unitFilter" class="form-label">Unit/Bidang Kerja</label>
                        <select class="form-select" id="unitFilter">
                            <option value="semua">Semua Unit/Bidang</option>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="searchInput" class="form-label">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari nama, email, jabatan, atau unit kerja">
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
                    <h5 class="mb-1">Daftar Mentor</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <span class="badge bg-light text-dark" id="selectedCount">0 mentor dipilih</span>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th width="40">
                                <input class="form-check-input" type="checkbox" id="checkAll">
                            </th>
                            <th>No</th>
                            <th>Nama Mentor</th>
                            <th>Unit/Bidang Kerja</th>
                            <th>Email</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th width="280">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="mentorTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data mentor tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination mentor">
                    <ul class="pagination mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="mentorFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('admin.pengguna.mentor.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Mentor Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="mentorUsername">Username</label>
                            <input class="form-control" id="mentorUsername" name="username" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mentorEmail">Email</label>
                            <input class="form-control" id="mentorEmail" name="email" type="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mentorPassword">Password</label>
                            <input class="form-control" id="mentorPassword" name="password" type="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mentorPasswordConfirmation">Konfirmasi Password</label>
                            <input class="form-control" id="mentorPasswordConfirmation" name="password_confirmation" type="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mentorNip">NIP</label>
                            <input class="form-control" id="mentorNip" name="nip" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mentorName">Nama Lengkap</label>
                            <input class="form-control" id="mentorName" name="name" type="text" required>
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
                            <label class="form-label" for="mentorPhone">Nomor Telepon</label>
                            <input class="form-control" id="mentorPhone" name="no_hp" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="mentorAddress">Alamat</label>
                            <textarea class="form-control" id="mentorAddress" name="alamat" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instansi</label>
                            <div class="form-control bg-light">LLDIKTI Wilayah V Yogyakarta</div>
                            <input type="hidden" name="perguruan_tinggi" value="LLDIKTI Wilayah V Yogyakarta">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mentorJabatan">Jabatan</label>
                            <input class="form-control" id="mentorJabatan" name="jabatan" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="mentorDivisi">Unit/Bidang Kerja</label>
                            <input class="form-control" id="mentorDivisi" name="divisi" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="mentorFoto">Foto Profil</label>
                            <input class="form-control" id="mentorFoto" name="foto" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Mentor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Mentor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editMentorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editMentorForm" novalidate>
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Edit Mentor</h5>
                        <small class="text-muted">Perubahan akan disimpan langsung ke database.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editMentorId">
                    <input type="hidden" id="editMentorUserId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorName">Nama Lengkap</label>
                            <input class="form-control" id="editMentorName" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorUsername">Username</label>
                            <input class="form-control" id="editMentorUsername" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorEmail">Email</label>
                            <input class="form-control" id="editMentorEmail" type="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorStatus">Status Akun</label>
                            <select class="form-select" id="editMentorStatus" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="menunggu">Menunggu Verifikasi</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorNip">NIP</label>
                            <input class="form-control" id="editMentorNip" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorGender">Jenis Kelamin</label>
                            <select class="form-select" id="editMentorGender" required>
                                <option value="">Pilih jenis kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorPhone">Nomor Telepon</label>
                            <input class="form-control" id="editMentorPhone" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editMentorJabatan">Jabatan</label>
                            <input class="form-control" id="editMentorJabatan" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="editMentorDivisi">Unit/Bidang Kerja</label>
                            <input class="form-control" id="editMentorDivisi" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="editMentorCampus">Instansi</label>
                            <input class="form-control" id="editMentorCampus" type="text" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="editMentorAddress">Alamat Lengkap</label>
                            <textarea class="form-control" id="editMentorAddress" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="saveMentorEditButton">Simpan Perubahan</button>
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
    <div id="mentorToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const mentors = @json($adminMentors ?? []);
    const csrfToken = @json(csrf_token());
    const updateUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.update', ['user' => '__USER__']));
    const deleteUrlTemplate = @json(route('admin.laporan-monitoring.statistik-pengguna.destroy', ['user' => '__USER__']));

    const perPage = 10;
    let currentPage = 1;
    let selectedIds = new Set();
    let pendingAction = null;

    const statusFilter = document.getElementById('statusFilter');
    const unitFilter = document.getElementById('unitFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('mentorTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');
    const selectedCount = document.getElementById('selectedCount');
    const checkAll = document.getElementById('checkAll');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const editModalElement = document.getElementById('editMentorModal');
    const editModal = new bootstrap.Modal(editModalElement);
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const mentorFormModal = new bootstrap.Modal(document.getElementById('mentorFormModal'));
    const toast = new bootstrap.Toast(document.getElementById('mentorToast'), { delay: 3000 });
    const confirmActionButton = document.getElementById('confirmAction');

    const editMentorForm = document.getElementById('editMentorForm');
    const editMentorId = document.getElementById('editMentorId');
    const editMentorUserId = document.getElementById('editMentorUserId');
    const editMentorName = document.getElementById('editMentorName');
    const editMentorUsername = document.getElementById('editMentorUsername');
    const editMentorEmail = document.getElementById('editMentorEmail');
    const editMentorStatus = document.getElementById('editMentorStatus');
    const editMentorNip = document.getElementById('editMentorNip');
    const editMentorGender = document.getElementById('editMentorGender');
    const editMentorPhone = document.getElementById('editMentorPhone');
    const editMentorJabatan = document.getElementById('editMentorJabatan');
    const editMentorDivisi = document.getElementById('editMentorDivisi');
    const editMentorCampus = document.getElementById('editMentorCampus');
    const editMentorAddress = document.getElementById('editMentorAddress');

    editModalElement.addEventListener('hidden.bs.modal', () => {
        editMentorForm.reset();
    });

    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
        confirmActionButton.textContent = 'Konfirmasi';
        confirmActionButton.className = 'btn btn-primary';
    });

    function titleCase(value) {
        return String(value || '')
            .split(' ')
            .filter(Boolean)
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
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

    function filteredMentors() {
        const status = statusFilter.value;
        const unit = unitFilter.value;
        const keyword = searchInput.value.trim().toLowerCase();

        return mentors.filter((mentor) => {
            const matchStatus = status === 'semua' || mentor.status === status;
            const matchUnit = unit === 'semua' || mentor.unit_kerja === unit;
            const matchKeyword = !keyword || [mentor.nama, mentor.unit_kerja, mentor.email, mentor.jabatan, mentor.status]
                .join(' ')
                .toLowerCase()
                .includes(keyword);

            return matchStatus && matchUnit && matchKeyword;
        });
    }

    function populateUnitFilter() {
        const units = [...new Set(mentors.map((mentor) => mentor.unit_kerja).filter(Boolean))].sort();
        unitFilter.innerHTML = `
            <option value="semua">Semua Unit/Bidang</option>
            ${units.map((unit) => `<option value="${unit}">${unit}</option>`).join('')}
        `;
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = mentors.length;
        document.getElementById('statAktif').textContent = mentors.filter((item) => item.status === 'aktif').length;
        document.getElementById('statMenunggu').textContent = mentors.filter((item) => item.status === 'menunggu').length;
        document.getElementById('statNonaktif').textContent = mentors.filter((item) => item.status === 'nonaktif').length;
    }

    function renderTable() {
        const data = filteredMentors();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((mentor, index) => `
            <tr>
                <td>
                    <input class="form-check-input row-check" type="checkbox" value="${mentor.id}" ${selectedIds.has(mentor.id) ? 'checked' : ''}>
                </td>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${mentor.nama}</td>
                <td>${mentor.unit_kerja}</td>
                <td>${mentor.email}</td>
                <td>${mentor.jabatan}</td>
                <td><span class="badge ${statusClass(mentor.status)}">${titleCase(mentor.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${mentor.id}" data-user-id="${mentor.user_id}">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${mentor.id}" data-user-id="${mentor.user_id}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="hapus" data-id="${mentor.id}" data-user-id="${mentor.user_id}">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} mentor ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} mentor`
            : 'Menampilkan 0 mentor';
        checkAll.checked = pageData.length > 0 && pageData.every((mentor) => selectedIds.has(mentor.id));

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
        selectedCount.textContent = `${selectedIds.size} mentor dipilih`;
    }

    function showDetail(id) {
        const mentor = mentors.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            ${mentor.foto ? `<div class="text-center mb-3"><img src="${mentor.foto}" alt="Foto ${mentor.nama}" class="rounded" style="width:110px;height:110px;object-fit:cover"></div>` : ''}
            <h6 class="fw-bold border-bottom pb-2">Data Akun</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">Username</dt><dd class="col-sm-8">${mentor.username}</dd>
                <dt class="col-sm-4">Email</dt><dd class="col-sm-8">${mentor.email}</dd>
            </dl>
            <h6 class="fw-bold border-bottom pb-2 mt-3">Data Identitas Mentor</h6>
            <dl class="row mb-0">
                <dt class="col-sm-4">NIP</dt><dd class="col-sm-8">${mentor.nip}</dd>
                <dt class="col-sm-4">Nama Mentor</dt>
                <dd class="col-sm-8">${mentor.nama}</dd>
                <dt class="col-sm-4">Jenis Kelamin</dt><dd class="col-sm-8">${mentor.jenis_kelamin}</dd>
                <dt class="col-sm-4">Nomor Telepon</dt><dd class="col-sm-8">${mentor.no_hp}</dd>
                <dt class="col-sm-4">Alamat Lengkap</dt><dd class="col-sm-8">${mentor.alamat}</dd>
                <dt class="col-sm-4">Unit/Bidang Kerja</dt><dd class="col-sm-8">${mentor.unit_kerja}</dd>
                <dt class="col-sm-4">Jabatan</dt><dd class="col-sm-8">${mentor.jabatan}</dd>
                <dt class="col-sm-4">Divisi</dt><dd class="col-sm-8">${mentor.divisi}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function showConfirm(id, action) {
        const mentor = mentors.find((item) => item.id === id);
        pendingAction = { id, action };

        document.getElementById('confirmTitle').textContent = 'Konfirmasi Hapus';
        document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin menghapus data mentor ${mentor.nama}?`;
        confirmActionButton.textContent = 'Hapus';
        confirmActionButton.className = 'btn btn-danger';
        confirmModal.show();
    }

    function openEditModal(mentor) {
        editMentorId.value = mentor.id;
        editMentorUserId.value = mentor.user_id;
        editMentorName.value = mentor.nama ?? '';
        editMentorUsername.value = mentor.username ?? '';
        editMentorEmail.value = mentor.email ?? '';
        editMentorStatus.value = mentor.status ?? 'aktif';
        editMentorNip.value = mentor.nip ?? '';
        editMentorGender.value = mentor.jenis_kelamin ?? '';
        editMentorPhone.value = mentor.no_hp ?? '';
        editMentorJabatan.value = mentor.jabatan ?? '';
        editMentorDivisi.value = mentor.divisi ?? '';
        editMentorCampus.value = mentor.perguruan_tinggi_raw ?? mentor.unit_kerja ?? mentor.instansi ?? '';
        editMentorAddress.value = mentor.alamat ?? '';
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
        const toastElement = document.getElementById('mentorToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
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

    document.getElementById('openAddMentorModal').addEventListener('click', () => {
        mentorFormModal.show();
    });

    [statusFilter, unitFilter].forEach((input) => {
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
        unitFilter.value = 'semua';
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
            const mentor = mentors.find((item) => item.id === id);
            if (mentor) {
                openEditModal(mentor);
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
        filteredMentors()
            .slice((currentPage - 1) * perPage, currentPage * perPage)
            .forEach((mentor) => {
                if (checkAll.checked) {
                    selectedIds.add(mentor.id);
                } else {
                    selectedIds.delete(mentor.id);
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

        const mentor = mentors.find((item) => item.id === pendingAction.id);
        if (!mentor) {
            confirmModal.hide();
            pendingAction = null;
            return;
        }

        try {
            const data = await sendRequest(deleteUrlTemplate.replace('__USER__', mentor.user_id), 'DELETE');
            const mentorIndex = mentors.findIndex((item) => item.id === pendingAction.id);
            if (mentorIndex !== -1) {
                mentors.splice(mentorIndex, 1);
                selectedIds.delete(pendingAction.id);
            }

            confirmModal.hide();
            confirmActionButton.textContent = 'Konfirmasi';
            confirmActionButton.className = 'btn btn-primary';
            updateStats();
            renderTable();
            showToast(data.message || `Data mentor ${mentor.nama} berhasil dihapus.`);
        } catch (error) {
            showToast(error.message, false);
        } finally {
            pendingAction = null;
        }
    });

    editMentorForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const mentor = mentors.find((item) => item.id === Number(editMentorId.value));
        if (!mentor) {
            showToast('Data mentor tidak ditemukan.', false);
            return;
        }

        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('name', editMentorName.value.trim());
        formData.append('username', editMentorUsername.value.trim());
        formData.append('email', editMentorEmail.value.trim());
        formData.append('account_status', editMentorStatus.value);
        formData.append('nip', editMentorNip.value.trim());
        formData.append('jenis_kelamin', editMentorGender.value);
        formData.append('no_hp', editMentorPhone.value.trim());
        formData.append('jabatan', editMentorJabatan.value.trim());
        formData.append('divisi', editMentorDivisi.value.trim());
        formData.append('perguruan_tinggi', editMentorCampus.value.trim());
        formData.append('alamat', editMentorAddress.value.trim());

        try {
            const data = await sendRequest(updateUrlTemplate.replace('__USER__', editMentorUserId.value), 'POST', formData);
            mentor.nama = data.user?.name ?? mentor.nama;
            mentor.username = data.user?.username ?? mentor.username;
            mentor.email = data.user?.email ?? mentor.email;
            mentor.status = data.user?.status ?? mentor.status;
            mentor.nip = data.user?.nip ?? mentor.nip;
            mentor.jenis_kelamin = data.user?.jenis_kelamin ?? mentor.jenis_kelamin;
            mentor.no_hp = data.user?.no_hp ?? mentor.no_hp;
            mentor.jabatan = data.user?.jabatan ?? mentor.jabatan;
            mentor.divisi = data.user?.divisi ?? mentor.divisi;
            mentor.unit_kerja = data.user?.divisi ?? mentor.unit_kerja;
            mentor.perguruan_tinggi_raw = data.user?.perguruan_tinggi ?? mentor.perguruan_tinggi_raw;
            mentor.alamat = data.user?.alamat ?? mentor.alamat;
            editModal.hide();
            updateStats();
            renderTable();
            showToast(data.message || `Data mentor ${mentor.nama} berhasil diperbarui.`);
        } catch (error) {
            showToast(error.message, false);
        }
    });

    populateUnitFilter();
    updateStats();
    renderTable();
</script>
@endpush
