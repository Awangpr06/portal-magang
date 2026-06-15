@extends('admin.layout.admin')

@section('title', 'Penempatan Magang')

@push('styles')
<style>
    .placement-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .placement-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .placement-page .stat-card,
    .placement-page .filter-card,
    .placement-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .placement-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .placement-page .stat-card:hover,
    .placement-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .placement-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .placement-page .table {
        font-size: 14px;
    }

    .placement-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 13px;
        white-space: nowrap;
    }

    .placement-page .table tbody tr {
        transition: 0.15s ease;
    }

    .placement-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .placement-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-width: 255px;
    }

    .placement-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .placement-page .pagination .page-link {
        color: #0b5f86;
    }

    .placement-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
@php
    $placementAgencies = collect($adminPlacements ?? [])
        ->pluck('perguruan_tinggi')
        ->filter(fn ($value) => filled($value) && $value !== '-')
        ->unique()
        ->sort()
        ->values();
@endphp
<div class="container-fluid placement-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penempatan</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Penempatan Magang</h2>
            <p class="text-muted mb-0">
                Kelola penempatan peserta magang ke perguruan tinggi, perusahaan, dan lembaga tujuan.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton">
                <i class="bi bi-download"></i>
                Ekspor Penempatan
            </button>
            <button class="btn btn-primary" type="button" id="addPlacementButton">
                <i class="bi bi-plus-lg"></i>
                Tambah Penempatan
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Penempatan</p>
                        <h3 class="mb-0" id="statTotal">0</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-pin-map"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Aktif</p>
                        <h3 class="mb-0" id="statAktif">0</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="menunggu konfirmasi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Menunggu Konfirmasi</p>
                        <h3 class="mb-0" id="statMenunggu">0</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="ditolak">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Ditolak</p>
                        <h3 class="mb-0" id="statDitolak">0</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
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
                    <span class="stat-icon bg-secondary"><i class="bi bi-flag"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="placementFilterForm">
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
                        <label for="agencyFilter" class="form-label">Perguruan Tinggi</label>
                        <select class="form-select" id="agencyFilter">
                            <option value="semua">Semua Perguruan Tinggi</option>
                            @foreach($placementAgencies as $agency)
                                <option value="{{ $agency }}">{{ $agency }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="menunggu konfirmasi">Menunggu Konfirmasi</option>
                            <option value="ditolak">Ditolak</option>
                            <option value="selesai">Selesai</option>
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
                    <h5 class="mb-1">Tabel Data Penempatan</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Perguruan Tinggi</th>
                            <th>Posisi/Divisi</th>
                            <th>Periode</th>
                            <th>Tanggal Penempatan</th>
                            <th width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="placementTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data penempatan tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination penempatan magang">
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
                <h5 class="modal-title">Detail Penempatan Magang</h5>
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
                <h5 class="modal-title" id="formTitle">Tambah Penempatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="formPlacementId">
                <div class="alert alert-info small mb-3" id="studentLookupStatus">
                    Masukkan NIM untuk memuat data mahasiswa dari database.
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="formNim">NIM</label>
                        <input class="form-control" id="formNim" placeholder="Masukkan NIM mahasiswa">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formName">Nama Mahasiswa</label>
                        <input class="form-control" id="formName" placeholder="Nama mahasiswa" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formStudy">Program Studi</label>
                        <input class="form-control" id="formStudy" placeholder="Program studi" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formCampus">Perguruan Tinggi</label>
                        <input class="form-control" id="formCampus" placeholder="Perguruan tinggi" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formPosition">Posisi/Divisi</label>
                        <input class="form-control" id="formPosition" placeholder="Posisi atau divisi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formPeriod">Periode</label>
                        <input class="form-control" id="formPeriod" placeholder="Contoh: Batch 1 2026">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formPlacementDate">Tanggal Penempatan</label>
                        <input type="date" class="form-control" id="formPlacementDate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formMentorNip">NIP Mentor</label>
                        <input class="form-control" id="formMentorNip" placeholder="NIP mentor">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formMentorEmail">Email Mentor</label>
                        <input type="email" class="form-control" id="formMentorEmail" placeholder="email@domain.id">
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
    <div id="placementToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const placements = @json($adminPlacements ?? []);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const lookupUrl = "{{ route('admin.magang.penempatan.lookup') }}";
    const storeUrl = "{{ route('admin.magang.penempatan.store') }}";
    const destroyUrlTemplate = "{{ route('admin.magang.penempatan.destroy', ['internship' => '__ID__']) }}";

    const perPage = 10;
    let currentPage = 1;
    let pendingAction = null;
    let editingId = null;
    let lookupTimer = null;
    let activeLookupNim = '';

    const searchInput = document.getElementById('searchInput');
    const periodFilter = document.getElementById('periodFilter');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const tableBody = document.getElementById('placementTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('placementToast'), { delay: 3000 });
    const formPlacementId = document.getElementById('formPlacementId');
    const studentLookupStatus = document.getElementById('studentLookupStatus');
    const formNim = document.getElementById('formNim');
    const formName = document.getElementById('formName');
    const formStudy = document.getElementById('formStudy');
    const formCampus = document.getElementById('formCampus');
    const formPosition = document.getElementById('formPosition');
    const formPeriod = document.getElementById('formPeriod');
    const formPlacementDate = document.getElementById('formPlacementDate');
    const formMentorNip = document.getElementById('formMentorNip');
    const formMentorEmail = document.getElementById('formMentorEmail');

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            aktif: 'bg-success',
            'menunggu konfirmasi': 'bg-info text-dark',
            ditolak: 'bg-danger',
            selesai: 'bg-secondary'
        }[value];
    }

    function emptyStudentFields(message = 'Masukkan NIM untuk memuat data mahasiswa dari database.') {
        formName.value = '';
        formStudy.value = '';
        formCampus.value = '';
        studentLookupStatus.textContent = message;
    }

    function fillFormFromPlacement(placement) {
        formPlacementId.value = placement?.id || '';
        formNim.value = placement?.nim || '';
        formName.value = placement?.nama || '';
        formStudy.value = placement?.prodi || '';
        formCampus.value = placement?.perguruan_tinggi || '';
        formPosition.value = placement?.penempatan || '';
        formPeriod.value = placement?.periode || '';
        formPlacementDate.value = placement?.tanggal_penempatan_raw || '';
        formMentorNip.value = placement?.mentor_nip || '';
        formMentorEmail.value = placement?.mentor_email || '';
        studentLookupStatus.textContent = placement
            ? 'Data mahasiswa dan penempatan dimuat dari database.'
            : 'Masukkan NIM untuk memuat data mahasiswa dari database.';
    }

    function buildDestroyUrl(id) {
        return destroyUrlTemplate.replace('__ID__', id);
    }

    async function lookupStudentByNim(nim) {
        const value = nim.trim();

        if (!value) {
            activeLookupNim = '';
            emptyStudentFields();
            return;
        }

        activeLookupNim = value;
        studentLookupStatus.textContent = 'Mencari data mahasiswa...';

        try {
            const response = await fetch(`${lookupUrl}?nim=${encodeURIComponent(value)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const payload = await response.json();

            if (!response.ok) {
                emptyStudentFields(payload.message || 'Data peserta tidak ditemukan.');
                return;
            }

            if (activeLookupNim !== value) {
                return;
            }

            const peserta = payload.peserta || {};
            const penempatan = payload.penempatan || {};

            formName.value = peserta.nama || '';
            formStudy.value = peserta.prodi || '';
            formCampus.value = peserta.perguruan_tinggi || '';
            formPeriod.value = penempatan.periode || peserta.program_magang || '';
            formPosition.value = penempatan.penempatan || penempatan.posisi || '';
            formPlacementDate.value = penempatan.tanggal_penempatan || '';
            formMentorNip.value = penempatan.nip_mentor || '';
            formMentorEmail.value = penempatan.email_mentor || '';

            studentLookupStatus.textContent = `Data ${peserta.nama || 'mahasiswa'} berhasil dimuat dari database.`;

            if (penempatan.id) {
                formPlacementId.value = penempatan.id;
            }
        } catch (error) {
            console.error(error);
            emptyStudentFields('Gagal memuat data mahasiswa. Coba lagi.');
        }
    }

    function filteredPlacements() {
        const keyword = searchInput.value.trim().toLowerCase();
        const period = periodFilter.value;
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;

        return placements.filter((placement) => {
            const matchKeyword = !keyword || [placement.nama, placement.nim, placement.prodi, placement.perguruan_tinggi, placement.penempatan, placement.posisi, placement.periode, placement.status].join(' ').toLowerCase().includes(keyword);
            const matchPeriod = period === 'semua' || placement.periode === period;
            const matchStudy = study === 'semua' || placement.prodi === study;
            const matchAgency = agency === 'semua' || placement.perguruan_tinggi === agency;
            const matchStatus = status === 'semua' || placement.status === status;

            return matchKeyword && matchPeriod && matchStudy && matchAgency && matchStatus;
        });
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = placements.length;
        document.getElementById('statAktif').textContent = placements.filter((item) => item.status === 'aktif').length;
        document.getElementById('statMenunggu').textContent = placements.filter((item) => item.status === 'menunggu konfirmasi').length;
        document.getElementById('statDitolak').textContent = placements.filter((item) => item.status === 'ditolak').length;
        document.getElementById('statSelesai').textContent = placements.filter((item) => item.status === 'selesai').length;
    }

    function renderTable() {
        const data = filteredPlacements();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((placement, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${placement.nama}</td>
                <td>${placement.nim}</td>
                <td>${placement.prodi}</td>
                <td>${placement.perguruan_tinggi}</td>
                <td>${placement.penempatan}</td>
                <td>${placement.periode}</td>
                <td>${placement.tanggal_penempatan || placement.tanggal}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${placement.id}">Detail</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${placement.id}">Edit</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${placement.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} penempatan ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} penempatan`
            : 'Menampilkan 0 penempatan';

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
        const toastElement = document.getElementById('placementToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const placement = placements.find((item) => item.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Nama Mahasiswa</dt>
                <dd class="col-sm-8">${placement.nama}</dd>
                <dt class="col-sm-4">NIM</dt>
                <dd class="col-sm-8">${placement.nim}</dd>
                <dt class="col-sm-4">Program Studi</dt>
                <dd class="col-sm-8">${placement.prodi}</dd>
                <dt class="col-sm-4">Perguruan Tinggi</dt>
                <dd class="col-sm-8">${placement.perguruan_tinggi}</dd>
                <dt class="col-sm-4">Posisi/Divisi</dt>
                <dd class="col-sm-8">${placement.penempatan}</dd>
                <dt class="col-sm-4">Mentor</dt>
                <dd class="col-sm-8">${placement.mentor}</dd>
                <dt class="col-sm-4">Periode</dt>
                <dd class="col-sm-8">${placement.periode}</dd>
                <dt class="col-sm-4">Tanggal Penempatan</dt>
                <dd class="col-sm-8">${placement.tanggal_penempatan || placement.tanggal}</dd>
                <dt class="col-sm-4">NIP Mentor</dt>
                <dd class="col-sm-8">${placement.mentor_nip || '-'}</dd>
                <dt class="col-sm-4">Email Mentor</dt>
                <dd class="col-sm-8">${placement.mentor_email || '-'}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const placement = id ? placements.find((item) => item.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Penempatan' : 'Tambah Penempatan';
        fillFormFromPlacement(placement);
        studentLookupStatus.textContent = placement
            ? 'Data mahasiswa dimuat dari database. Ubah NIM untuk mencari mahasiswa lain.'
            : 'Masukkan NIM untuk memuat data mahasiswa dari database.';
        formModal.show();
    }

    function showConfirm(id, action) {
        const placement = placements.find((item) => item.id === id);
        pendingAction = { id, action };

        const labels = {
            aktif: ['Konfirmasi Validasi Penempatan', `Aktifkan penempatan ${placement.nama} di ${placement.perguruan_tinggi}?`],
            selesai: ['Konfirmasi Selesai Penempatan', `Tandai penempatan ${placement.nama} sebagai selesai?`],
            hapus: ['Konfirmasi Hapus Penempatan', `Hapus data penempatan ${placement.nama}?`]
        };

        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
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
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Data penempatan berhasil disiapkan untuk ekspor.');
    });

    document.getElementById('addPlacementButton').addEventListener('click', () => openForm());

    formNim.addEventListener('input', () => {
        clearTimeout(lookupTimer);
        lookupTimer = setTimeout(() => lookupStudentByNim(formNim.value), 450);
    });

    formNim.addEventListener('blur', () => lookupStudentByNim(formNim.value));

    document.getElementById('saveForm').addEventListener('click', async () => {
        const nim = formNim.value.trim();
        const posisi = formPosition.value.trim();
        const periode = formPeriod.value.trim();
        const tanggalPenempatan = formPlacementDate.value;
        const nipMentor = formMentorNip.value.trim();
        const emailMentor = formMentorEmail.value.trim();

        if (!nim || !posisi || !periode || !tanggalPenempatan || !nipMentor) {
            showToast('NIM, posisi/divisi, periode, tanggal penempatan, dan NIP mentor wajib diisi.', false);
            return;
        }

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    nim,
                    posisi,
                    periode,
                    tanggal_penempatan: tanggalPenempatan,
                    nip_mentor: nipMentor,
                    email_mentor: emailMentor || null,
                    placement_id: formPlacementId.value || null,
                }),
            });

            const payload = await response.json();

            if (!response.ok) {
                showToast(payload.message || 'Gagal menyimpan penempatan.', false);
                return;
            }

            formModal.hide();
            showToast(payload.message || 'Penempatan berhasil disimpan ke database.');
            window.location.reload();
        } catch (error) {
            console.error(error);
            showToast('Gagal menyimpan penempatan. Coba lagi.', false);
        }
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        const action = button.dataset.action;

        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'edit') {
            openForm(id);
        } else {
            showConfirm(id, action);
        }
    });

    document.getElementById('confirmAction').addEventListener('click', async () => {
        if (!pendingAction) {
            return;
        }

        const index = placements.findIndex((item) => item.id === pendingAction.id);
        const placement = placements[index];
        let message = '';

        if (pendingAction.action === 'hapus') {
            try {
                const response = await fetch(buildDestroyUrl(pendingAction.id), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });

                const payload = await response.json();

                if (!response.ok) {
                    showToast(payload.message || 'Gagal menghapus penempatan.', false);
                    return;
                }

                message = payload.message || `Penempatan ${placement.nama} berhasil dihapus.`;
                confirmModal.hide();
                showToast(message);
                window.location.reload();
                pendingAction = null;
                return;
            } catch (error) {
                console.error(error);
                showToast('Gagal menghapus penempatan. Coba lagi.', false);
                return;
            }
        }

        confirmModal.hide();
        showToast(message);
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
