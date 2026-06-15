@extends('admin.layout.admin')

@section('title', 'Statistik Perguruan Tinggi')

@push('styles')
<style>
    .campus-stat-page .page-title { font-weight: 700; color: #163342; }
    .campus-stat-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .campus-stat-page .stat-card,
    .campus-stat-page .filter-card,
    .campus-stat-page .chart-card,
    .campus-stat-page .table-card,
    .campus-stat-page .summary-card { border: 0; border-radius: 8px; }
    .campus-stat-page .stat-card { cursor: pointer; transition: .2s ease; }
    .campus-stat-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .campus-stat-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .campus-chart { min-height: 245px; display: flex; align-items: end; gap: 14px; padding: 18px; border: 1px solid #e2ebef; border-radius: 8px; background: #fbfdfe; }
    .chart-bar { flex: 1; min-width: 42px; border-radius: 8px 8px 0 0; background: #0b5f86; position: relative; }
    .chart-bar span { position: absolute; left: 50%; bottom: -30px; transform: translateX(-50%); font-size: 12px; color: #627986; white-space: nowrap; }
    .trend-row { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .trend-row .progress { flex: 1; height: 12px; }
    .summary-item { border: 1px solid #e2ebef; border-radius: 8px; padding: 12px; margin-bottom: 10px; background: #fbfdfe; }
    .campus-stat-page .table { font-size: 14px; }
    .campus-stat-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .campus-stat-page .table tbody tr:hover { background: #f8fbfd; }
    .campus-stat-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 330px; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid campus-stat-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.laporan-monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Statistik Perguruan Tinggi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Statistik Perguruan Tinggi</h2>
            <p class="text-muted mb-0">Monitoring institusi, status kerja sama, distribusi wilayah, dan keterlibatan mahasiswa.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="downloadButton"><i class="bi bi-file-earmark-arrow-down"></i> Unduh Laporan</button>
            <button class="btn btn-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Data</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Perguruan Tinggi</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-bank"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">PT Aktif Kerja Sama</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="proses">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">PT Dalam Proses</p><h3 class="mb-0" id="statProcess">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="tidak aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">PT Tidak Aktif</p><h3 class="mb-0" id="statInactive">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-student-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mahasiswa</p><h3 class="mb-0" id="statStudents">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-mortarboard"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="periodFilter" class="form-label">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="Semester Genap">Semester Genap</option>
                        <option value="Semester Ganjil">Semester Ganjil</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="provinceFilter" class="form-label">Provinsi</label>
                    <select class="form-select" id="provinceFilter">
                        <option value="semua">Semua Provinsi</option>
                        <option value="DI Yogyakarta">DI Yogyakarta</option>
                        <option value="Jawa Tengah">Jawa Tengah</option>
                        <option value="Jawa Timur">Jawa Timur</option>
                        <option value="DKI Jakarta">DKI Jakarta</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="typeFilter" class="form-label">Jenis PT</label>
                    <select class="form-select" id="typeFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="Swasta">Swasta</option>
                        <option value="Negeri">Negeri</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status Kerja Sama</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="proses">Dalam Proses</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchInput" class="form-label">Pencarian</label>
                    <div class="input-group">
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari Perguruan Tinggi">
                        <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-search"></i> Terapkan</button>
                        <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i> Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Daftar Perguruan Tinggi</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perguruan Tinggi</th>
                            <th>Jenis PT</th>
                            <th>Provinsi</th>
                            <th>Status Kerja Sama</th>
                            <th>Total Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Instansi Mitra</th>
                            <th>Terakhir Aktif</th>
                            <th width="350">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="campusTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data perguruan tinggi tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination statistik perguruan tinggi"><ul class="pagination mb-0" id="pagination"></ul></nav>
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

<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Perguruan Tinggi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editCampusId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="editNamaPt">Nama Perguruan Tinggi</label>
                            <input class="form-control" id="editNamaPt" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editJenis">Jenis PT</label>
                            <select class="form-select" id="editJenis" required>
                                <option value="Negeri">Negeri</option>
                                <option value="Swasta">Swasta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editAlamat">Alamat / Provinsi</label>
                            <input class="form-control" id="editAlamat" type="text" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editEmail">Email</label>
                            <input class="form-control" id="editEmail" type="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editPic">PIC</label>
                            <input class="form-control" id="editPic" type="text">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editPicNip">NIP PIC</label>
                            <input class="form-control" id="editPicNip" type="text">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editFakultas">Fakultas</label>
                            <input class="form-control" id="editFakultas" type="text">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editProgramStudi">Program Studi</label>
                            <input class="form-control" id="editProgramStudi" type="text">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="editStatus">Status Kerja Sama</label>
                            <select class="form-select" id="editStatus" required>
                                <option value="aktif">Aktif</option>
                                <option value="proses">Dalam Proses</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <div class="row g-2">
                                    <div class="col-md-4"><strong>Mahasiswa:</strong> <span id="editStudentsLabel">0</span></div>
                                    <div class="col-md-4"><strong>Program Studi:</strong> <span id="editStudiesLabel">0</span></div>
                                    <div class="col-md-4"><strong>Instansi Mitra:</strong> <span id="editPartnersLabel">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
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
    const csrfToken = @json(csrf_token());
    const campusSource = @json($adminCampuses ?? []);
    const exportUrl = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.export'));
    const downloadUrl = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.download'));
    const updateUrlTemplate = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.update', ['campus' => '__CAMPUS__']));
    const statusUrlTemplate = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.status', ['campus' => '__CAMPUS__']));
    const deleteUrlTemplate = @json(route('admin.laporan-monitoring.statistik-perguruan-tinggi.destroy', ['campus' => '__CAMPUS__']));
    const campuses = campusSource.map((item) => ({
        id: Number(item.id),
        name: item.nama || '-',
        type: item.jenis || 'Swasta',
        province: item.provinsi || 'DI Yogyakarta',
        address: item.alamat || item.provinsi || '-',
        status: item.status || 'aktif',
        students: Number(item.mahasiswa_count || 0),
        studies: Number(item.program_studi_count || 0),
        partners: Number(item.instansi_mitra_count || 0),
        lastActive: item.terakhir_aktif || item.tanggal || '-',
        period: item.tanggal ? item.tanggal.split(' ').pop() : '2026',
        email: item.email || '-',
        pic: item.pic || '-',
        picNip: item.pic_nip || '-',
        faculty: item.fakultas || '-',
        programStudy: item.program_studi || '-',
    }));

    let filteredCampuses = [...campuses];
    let currentPage = 1;
    const perPage = 5;
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'));
    let pendingCampusAction = null;
    let pendingEditCampus = null;

    const badgeMap = {
        aktif: 'success',
        proses: 'warning',
        'tidak aktif': 'danger',
        nonaktif: 'danger'
    };

    const statusLabelMap = {
        aktif: 'Aktif',
        proses: 'Dalam Proses',
        'tidak aktif': 'Tidak Aktif',
        nonaktif: 'Tidak Aktif'
    };

    function renderStats() {
        document.getElementById('statTotal').textContent = campuses.length;
        document.getElementById('statActive').textContent = campuses.filter(item => item.status === 'aktif').length;
        document.getElementById('statProcess').textContent = campuses.filter(item => item.status === 'proses').length;
        document.getElementById('statInactive').textContent = campuses.filter(item => item.status === 'tidak aktif').length;
        document.getElementById('statStudents').textContent = campuses.reduce((total, item) => total + item.students, 0);
    }

    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const pageItems = filteredCampuses.slice(start, start + perPage);
        const tableBody = document.getElementById('campusTable');
        const emptyState = document.getElementById('emptyState');
        const tableWrapper = document.getElementById('tableWrapper');

        tableBody.innerHTML = pageItems.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><strong>${item.name}</strong></td>
                <td>${item.type}</td>
                <td>${item.province}</td>
                <td><span class="badge bg-${badgeMap[item.status]}">${statusLabelMap[item.status] || item.status}</span></td>
                <td>${item.students}</td>
                <td>${item.studies}</td>
                <td>${item.partners}</td>
                <td>${item.lastActive}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-primary" data-action="detail" data-index="${campuses.indexOf(item)}"><i class="bi bi-eye"></i> Lihat</button>
                        <button class="btn btn-sm btn-outline-secondary" data-action="edit" data-index="${campuses.indexOf(item)}"><i class="bi bi-pencil"></i> Edit</button>
                        <button class="btn btn-sm ${item.status === 'tidak aktif' ? 'btn-outline-success' : 'btn-outline-dark'}" data-action="nonaktif" data-index="${campuses.indexOf(item)}"><i class="bi ${item.status === 'tidak aktif' ? 'bi-check-circle' : 'bi-slash-circle'}"></i> ${item.status === 'tidak aktif' ? 'Aktifkan' : 'Nonaktif'}</button>
                        <button class="btn btn-sm btn-outline-danger" data-action="hapus" data-index="${campuses.indexOf(item)}"><i class="bi bi-trash"></i> Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        const hasData = filteredCampuses.length > 0;
        emptyState.classList.toggle('d-none', hasData);
        tableWrapper.classList.toggle('d-none', !hasData);
        document.getElementById('tableSummary').textContent = hasData ? `Menampilkan ${start + 1} sampai ${Math.min(start + perPage, filteredCampuses.length)} dari ${filteredCampuses.length} data perguruan tinggi` : 'Menampilkan 0 data';
        document.getElementById('pageInfo').textContent = hasData ? `Halaman ${currentPage} dari ${Math.ceil(filteredCampuses.length / perPage)}` : 'Menampilkan 0 data';
        renderPagination();
    }

    function renderPagination() {
        const totalPages = Math.max(1, Math.ceil(filteredCampuses.length / perPage));
        const pagination = document.getElementById('pagination');
        let items = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            items += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}">${page}</button></li>`;
        }
        items += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}">Next</button></li>`;
        pagination.innerHTML = items;
    }

    function applyFilter(statusOverride = null) {
        const period = document.getElementById('periodFilter').value;
        const province = document.getElementById('provinceFilter').value;
        const type = document.getElementById('typeFilter').value;
        const status = statusOverride || document.getElementById('statusFilter').value;
        const keyword = document.getElementById('searchInput').value.toLowerCase();

        filteredCampuses = campuses.filter(item => {
            const matchPeriod = period === 'semua' || item.period === period;
            const matchProvince = province === 'semua' || item.province === province;
            const matchType = type === 'semua' || item.type === type;
            const matchStatus = status === 'semua' || item.status === status;
            const matchKeyword = !keyword || `${item.name} ${item.type} ${item.province}`.toLowerCase().includes(keyword);
            return matchPeriod && matchProvince && matchType && matchStatus && matchKeyword;
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
        actionButton.onclick = async () => {
            try {
                await callback();
            } finally {
                confirmModal.hide();
            }
        };
        confirmModal.show();
    }

    function openEditModal(campus) {
        pendingEditCampus = campus;
        document.getElementById('editCampusId').value = campus.id;
        document.getElementById('editNamaPt').value = campus.name || '';
        document.getElementById('editJenis').value = campus.type || 'Swasta';
        document.getElementById('editAlamat').value = campus.address || campus.province || '';
        document.getElementById('editEmail').value = campus.email || '';
        document.getElementById('editPic').value = campus.pic || '';
        document.getElementById('editPicNip').value = campus.picNip || '';
        document.getElementById('editFakultas').value = campus.faculty || '';
        document.getElementById('editProgramStudi').value = campus.programStudy || '';
        document.getElementById('editStatus').value = campus.status || 'aktif';
        document.getElementById('editStudentsLabel').textContent = campus.students;
        document.getElementById('editStudiesLabel').textContent = campus.studies;
        document.getElementById('editPartnersLabel').textContent = campus.partners;
        editModal.show();
    }

    async function submitEditCampus(campus, payload) {
        const response = await fetch(updateUrlTemplate.replace('__CAMPUS__', campus.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showToast(data.message || 'Gagal memperbarui perguruan tinggi.', 'danger');
            return;
        }

        const index = campuses.findIndex((item) => item.id === campus.id);
        if (index !== -1) {
            campuses[index] = {
                ...campuses[index],
                name: payload.nama_pt,
                type: payload.jenis,
                address: payload.alamat,
                province: payload.alamat,
                email: payload.email,
                pic: payload.pic || '-',
                picNip: payload.pic_nip || '-',
                faculty: payload.fakultas || '-',
                programStudy: payload.program_studi || '-',
                status: payload.status_kerja_sama,
            };
        }

        editModal.hide();
        pendingEditCampus = null;
        applyFilter();
        renderStats();
        showToast(data.message || `Data perguruan tinggi ${campus.name} berhasil diperbarui.`);
    }

    async function submitStatusToggle(campus, nextStatus) {
        const response = await fetch(statusUrlTemplate.replace('__CAMPUS__', campus.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status_kerja_sama: nextStatus }),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showToast(data.message || 'Gagal memperbarui status perguruan tinggi.', 'danger');
            return;
        }

        const index = campuses.findIndex((item) => item.id === campus.id);
        if (index !== -1) {
            campuses[index] = {
                ...campuses[index],
                status: data.status || nextStatus,
            };
        }

        applyFilter();
        renderStats();
        showToast(data.message || 'Status perguruan tinggi berhasil diperbarui.');
    }

    async function submitDeleteCampus(campus) {
        const response = await fetch(deleteUrlTemplate.replace('__CAMPUS__', campus.id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            showToast(data.message || 'Gagal menghapus perguruan tinggi.', 'danger');
            return;
        }

        const index = campuses.findIndex((item) => item.id === campus.id);
        if (index !== -1) {
            campuses.splice(index, 1);
        }

        filteredCampuses = filteredCampuses.filter((item) => item.id !== campus.id);
        currentPage = 1;
        applyFilter();
        renderStats();
        showToast(data.message || `Perguruan tinggi ${campus.name} berhasil dihapus.`);
    }

    document.getElementById('applyFilter').addEventListener('click', () => applyFilter());
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.querySelectorAll('.campus-stat-page select').forEach(select => select.value = 'semua');
        document.getElementById('searchInput').value = '';
        filteredCampuses = [...campuses];
        currentPage = 1;
        renderTable();
        showToast('Filter statistik perguruan tinggi telah direset.', 'info');
    });
    document.getElementById('searchInput').addEventListener('keyup', event => {
        if (event.key === 'Enter') applyFilter();
    });

    document.querySelectorAll('[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            const status = card.dataset.statusCard;
            document.getElementById('statusFilter').value = status;
            applyFilter(status);
        });
    });

    document.getElementById('pagination').addEventListener('click', event => {
        const page = Number(event.target.dataset.page);
        const totalPages = Math.max(1, Math.ceil(filteredCampuses.length / perPage));
        if (!page || page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    });

    document.getElementById('campusTable').addEventListener('click', event => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const campus = campuses[button.dataset.index];
        const action = button.dataset.action;

        if (action === 'detail') {
            document.getElementById('detailContent').innerHTML = `
                <dl class="row mb-0">
                    <dt class="col-sm-5">Perguruan Tinggi</dt><dd class="col-sm-7">${campus.name}</dd>
                    <dt class="col-sm-5">Jenis PT</dt><dd class="col-sm-7">${campus.type}</dd>
                    <dt class="col-sm-5">Provinsi</dt><dd class="col-sm-7">${campus.province}</dd>
                    <dt class="col-sm-5">Status Kerja Sama</dt><dd class="col-sm-7"><span class="badge bg-${badgeMap[campus.status]}">${statusLabelMap[campus.status] || campus.status}</span></dd>
                    <dt class="col-sm-5">Total Mahasiswa</dt><dd class="col-sm-7">${campus.students}</dd>
                    <dt class="col-sm-5">Program Studi</dt><dd class="col-sm-7">${campus.studies}</dd>
                    <dt class="col-sm-5">Instansi Mitra</dt><dd class="col-sm-7">${campus.partners}</dd>
                    <dt class="col-sm-5">Terakhir Aktif</dt><dd class="col-sm-7">${campus.lastActive}</dd>
                </dl>
            `;
            detailModal.show();
            return;
        }

        if (action === 'edit') {
            openEditModal(campus);
            return;
        }

        if (action === 'nonaktif') {
            const nextStatus = campus.status === 'tidak aktif' ? 'aktif' : 'tidak aktif';
            const label = nextStatus === 'tidak aktif' ? 'nonaktifkan' : 'aktifkan kembali';
            openConfirm('Konfirmasi Status', `Apakah Anda yakin ingin ${label} ${campus.name}?`, () => submitStatusToggle(campus, nextStatus));
            return;
        }

        if (action === 'hapus') {
            openConfirm('Konfirmasi Hapus', `Apakah Anda yakin ingin menghapus perguruan tinggi ${campus.name}?`, () => submitDeleteCampus(campus));
        }
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        window.location.href = exportUrl;
    });
    document.getElementById('downloadButton').addEventListener('click', () => {
        window.location.href = downloadUrl;
    });

    document.getElementById('editForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!pendingEditCampus) {
            return;
        }

        await submitEditCampus(pendingEditCampus, {
            nama_pt: document.getElementById('editNamaPt').value.trim(),
            jenis: document.getElementById('editJenis').value,
            alamat: document.getElementById('editAlamat').value.trim(),
            email: document.getElementById('editEmail').value.trim(),
            pic: document.getElementById('editPic').value.trim(),
            pic_nip: document.getElementById('editPicNip').value.trim(),
            fakultas: document.getElementById('editFakultas').value.trim(),
            program_studi: document.getElementById('editProgramStudi').value.trim(),
            status_kerja_sama: document.getElementById('editStatus').value,
        });
    });

    renderStats();
    renderTable();
});
</script>
@endpush
