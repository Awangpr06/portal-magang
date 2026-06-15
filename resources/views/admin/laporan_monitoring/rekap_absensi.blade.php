@extends('admin.layout.admin')

@section('title', 'Rekap Absensi')

@push('styles')
<style>
    .attendance-recap-page .page-title { font-weight: 700; color: #163342; }
    .attendance-recap-page .breadcrumb a { color: #0b5f86; text-decoration: none; }
    .attendance-recap-page .stat-card,
    .attendance-recap-page .filter-card,
    .attendance-recap-page .table-card,
    .attendance-recap-page .side-card { border: 0; border-radius: 8px; }
    .attendance-recap-page .stat-card { cursor: pointer; transition: .2s ease; }
    .attendance-recap-page .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(11,95,134,.16); }
    .attendance-recap-page .stat-icon { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; color: #fff; }
    .placement-recap-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
    .placement-item { border: 1px solid #e2ebef; border-radius: 12px; padding: 14px; background: #fbfdfe; height: 100%; }
    .placement-item .progress { height: 8px; background: #e9eef2; }
    .attendance-recap-page .table { font-size: 14px; }
    .attendance-recap-page .table thead th { background: #f1f6f9; color: #3b5664; font-size: 13px; white-space: nowrap; }
    .attendance-recap-page .table tbody tr:hover { background: #f8fbfd; }
    .attendance-recap-page .action-group { display: flex; flex-wrap: wrap; gap: 6px; min-width: 270px; }
    .empty-state { min-height: 210px; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; }
    .pagination .page-link { color: #0b5f86; }
    .pagination .active .page-link { background: #0b5f86; border-color: #0b5f86; color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid attendance-recap-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.laporan-monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rekap Absensi</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Rekap Absensi</h2>
            <p class="text-muted mb-0">Evaluasi, monitoring, dan dokumentasi seluruh data kehadiran peserta magang.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor Laporan</button>
            <button class="btn btn-primary" type="button" id="validateAllButton"><i class="bi bi-check2-circle"></i> Validasi Massal</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mahasiswa</p><h3 class="mb-0" id="statStudents">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-mortarboard"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="baik">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Kehadiran</p><h3 class="mb-0" id="statPresent">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-attendance-card="izin">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Izin</p><h3 class="mb-0" id="statPermit">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-file-earmark-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-attendance-card="tidak hadir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Tidak Hadir</p><h3 class="mb-0" id="statAbsent">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-person-x"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="sangat baik">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Kehadiran</p><h3 class="mb-0" id="statAverage">0%</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-percent"></i></span>
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
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="placementFilter" class="form-label">Penempatan</label>
                    <select class="form-select" id="placementFilter">
                        <option value="semua">Semua Penempatan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="sangat baik">Sangat Baik</option>
                        <option value="baik">Baik</option>
                        <option value="perlu perhatian">Perlu Perhatian</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="searchInput" class="form-label">Mahasiswa</label>
                    <div class="input-group">
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari Mahasiswa">
                        <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-search"></i></button>
                        <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-clockwise"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card side-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Rekap Per Penempatan</h5>
                            <p class="text-muted mb-0">Distribusi rata-rata kehadiran peserta berdasarkan penempatan/divisi.</p>
                        </div>
                        <span class="badge bg-light text-dark" id="placementCount">0 penempatan</span>
                    </div>
                    <div class="placement-recap-grid" id="placementRecap"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Rekap Absensi Mahasiswa</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>
            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Program Studi</th>
                            <th>Penempatan</th>
                            <th>Hari Kerja</th>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Tidak Hadir</th>
                            <th>Kehadiran</th>
                            <th>Status</th>
                            <th width="300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTable"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Data rekap absensi tidak ditemukan.</p></div>
            </div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination rekap absensi"><ul class="pagination mb-0" id="pagination"></ul></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Rekap Absensi</h5>
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

<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="reviewForm">
                @csrf
                <input type="hidden" id="reviewPesertaId">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewTitle">Review Rekap Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="reviewStatus">Status Review</label>
                        <select class="form-select" id="reviewStatus">
                            <option value="divalidasi">Divalidasi</option>
                            <option value="perlu_tinjauan">Perlu Tinjauan</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="reviewNote">Catatan</label>
                        <textarea class="form-control" id="reviewNote" rows="4" placeholder="Catatan review admin"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="reviewSubmitButton">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="attendanceToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const attendanceSource = @json($adminAttendanceRecaps ?? []);
    const attendanceBaseUrl = @json(url('/admin/laporan-monitoring/rekap-absensi'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const attendances = attendanceSource.map((item) => ({
        id: item.peserta_id,
        nim: item.nim || '-',
        name: item.nama || '-',
        study: item.prodi || '-',
        agency: item.instansi || 'LLDIKTI Wilayah V Yogyakarta',
        placement: item.penempatan || '-',
        workdays: Number(item.workdays || 0),
        present: Number(item.present || 0),
        permit: Number(item.permit || 0),
        absent: Number(item.absent || 0),
        period: item.periode || '-',
        batch: item.status === 'sangat baik' ? 'Kelompok A' : (item.status === 'baik' ? 'Kelompok B' : 'Kelompok C'),
        status: item.status || 'perlu perhatian',
        lastActive: item.last_active || '-',
        reviewStatus: item.review_status || 'draft',
        reviewNote: item.review_note || '-',
        reviewedAt: item.reviewed_at || '-',
    }));

    const perPage = 5;
    let currentPage = 1;
    let pendingAction = null;
    let pendingId = null;

    const periodFilter = document.getElementById('periodFilter');
    const studyFilter = document.getElementById('studyFilter');
    const placementFilter = document.getElementById('placementFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('attendanceTable');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
    const toast = new bootstrap.Toast(document.getElementById('attendanceToast'), { delay: 3000 });
    const reviewForm = document.getElementById('reviewForm');

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function attendancePercent(item) {
        return Math.round((item.present / item.workdays) * 100);
    }

    function statusClass(status) {
        return { 'sangat baik': 'bg-success', baik: 'bg-info text-dark', 'perlu perhatian': 'bg-warning text-dark' }[status] || 'bg-secondary';
    }

    function filteredAttendances() {
        const keyword = searchInput.value.trim().toLowerCase();
        return attendances.filter((item) => {
            const matchKeyword = !keyword || [item.nim, item.name, item.study, item.agency, item.placement, item.status].join(' ').toLowerCase().includes(keyword);
            const matchPeriod = periodFilter.value === 'semua' || item.period === periodFilter.value;
            const matchStudy = studyFilter.value === 'semua' || item.study === studyFilter.value;
            const matchPlacement = placementFilter.value === 'semua' || item.placement === placementFilter.value;
            const matchStatus = statusFilter.value === 'semua' || item.status === statusFilter.value;
            return matchKeyword && matchPeriod && matchStudy && matchPlacement && matchStatus;
        });
    }

    function updateStats() {
        const totalPresent = attendances.reduce((sum, item) => sum + item.present, 0);
        const totalPermit = attendances.reduce((sum, item) => sum + item.permit, 0);
        const totalAbsent = attendances.reduce((sum, item) => sum + item.absent, 0);
        const average = attendances.length
            ? Math.round(attendances.reduce((sum, item) => sum + attendancePercent(item), 0) / attendances.length)
            : 0;
        document.getElementById('statStudents').textContent = attendances.length;
        document.getElementById('statPresent').textContent = totalPresent;
        document.getElementById('statPermit').textContent = totalPermit;
        document.getElementById('statAbsent').textContent = totalAbsent;
        document.getElementById('statAverage').textContent = `${average}%`;
    }

    function renderTable() {
        const data = filteredAttendances();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.nim}</td>
                <td class="fw-semibold">${item.name}</td>
                <td>${item.study}</td>
                <td>${item.placement}</td>
                <td>${item.workdays}</td>
                <td>${item.present}</td>
                <td>${item.permit}</td>
                <td>${item.absent}</td>
                <td>${attendancePercent(item)}%</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                        <button class="btn btn-primary btn-sm" type="button" data-action="unduh" data-id="${item.id}">Unduh</button>
                        <button class="btn btn-success btn-sm" type="button" data-action="validasi" data-id="${item.id}">Validasi</button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${item.id}">Edit</button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');

        document.getElementById('tableSummary').textContent = `${data.length} rekap absensi ditemukan`;
        document.getElementById('tableWrapper').classList.toggle('d-none', data.length === 0);
        document.getElementById('emptyState').classList.toggle('d-none', data.length > 0);
        document.getElementById('pageInfo').textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data absensi` : 'Menampilkan 0 data absensi';
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

    function renderPlacementRecap() {
        const placements = [...new Set(attendances.map((item) => item.placement))].filter((placement) => placement && placement !== '-');
        document.getElementById('placementCount').textContent = `${placements.length} penempatan`;

        if (!placements.length) {
            document.getElementById('placementRecap').innerHTML = '<div class="text-muted small">Belum ada data penempatan untuk ditampilkan.</div>';
            return;
        }

        document.getElementById('placementRecap').innerHTML = placements.map((placement) => {
            const items = attendances.filter((item) => item.placement === placement);
            const average = Math.round(items.reduce((sum, item) => sum + attendancePercent(item), 0) / items.length);
            return `
                <div class="placement-item shadow-sm">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <strong class="d-block">${placement}</strong>
                            <small class="text-muted">${items.length} peserta</small>
                        </div>
                        <span class="fw-semibold">${average}%</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" style="width:${average}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Rata-rata kehadiran</span>
                        <span>${average}%</span>
                    </div>
                </div>
            `;
        }).join('');
    }

    function showToast(message, success = true) {
        const toastElement = document.getElementById('attendanceToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-danger', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const item = attendances.find((data) => data.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">NIM</dt><dd class="col-sm-8">${item.nim}</dd>
                <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">${item.name}</dd>
                <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${item.study}</dd>
                <dt class="col-sm-4">Instansi</dt><dd class="col-sm-8">${item.agency}</dd>
                <dt class="col-sm-4">Penempatan</dt><dd class="col-sm-8">${item.placement}</dd>
                <dt class="col-sm-4">Periode</dt><dd class="col-sm-8">${item.period}</dd>
        <dt class="col-sm-4">Kelompok</dt><dd class="col-sm-8">${item.batch}</dd>
                <dt class="col-sm-4">Hari Kerja</dt><dd class="col-sm-8">${item.workdays}</dd>
                <dt class="col-sm-4">Hadir</dt><dd class="col-sm-8">${item.present}</dd>
                <dt class="col-sm-4">Izin</dt><dd class="col-sm-8">${item.permit}</dd>
                <dt class="col-sm-4">Tidak Hadir</dt><dd class="col-sm-8">${item.absent}</dd>
                <dt class="col-sm-4">Persentase</dt><dd class="col-sm-8">${attendancePercent(item)}%</dd>
                <dt class="col-sm-4">Status</dt><dd class="col-sm-8">${titleCase(item.status)}</dd>
                <dt class="col-sm-4">Review Admin</dt><dd class="col-sm-8">${titleCase((item.reviewStatus || 'draft').replace(/_/g, ' '))}</dd>
                <dt class="col-sm-4">Catatan Review</dt><dd class="col-sm-8">${item.reviewNote}</dd>
                <dt class="col-sm-4">Direview</dt><dd class="col-sm-8">${item.reviewedAt}</dd>
                <dt class="col-sm-4">Terakhir Aktif</dt><dd class="col-sm-8">${item.lastActive}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function confirmAction(action, id = null) {
        pendingAction = action;
        pendingId = id;
        const item = id ? attendances.find((data) => data.id === id) : null;
        const labels = {
            validasi: ['Konfirmasi Validasi Absensi', `Validasi rekap absensi ${item?.name}?`],
            hapus: ['Konfirmasi Hapus Absensi', `Hapus rekap absensi ${item?.name}?`],
            massal: ['Konfirmasi Validasi Massal', 'Validasi seluruh data rekap absensi yang tampil?']
        };
        document.getElementById('confirmTitle').textContent = labels[action][0];
        document.getElementById('confirmMessage').textContent = labels[action][1];
        confirmModal.show();
    }

    function getAttendance(id) {
        return attendances.find((data) => data.id === id);
    }

    async function submitReview(id, method, urlSuffix, payload = {}) {
        const response = await fetch(`${attendanceBaseUrl}/${id}${urlSuffix}`, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const data = await response.json().catch(() => null);
            throw new Error(data?.message || 'Permintaan gagal diproses.');
        }

        return response.json();
    }

    function applyReviewResult(id, review, shouldRender = true) {
        const item = getAttendance(id);
        if (!item) return;

        item.reviewStatus = review.status || item.reviewStatus;
        item.reviewNote = review.catatan || '-';
        item.reviewedAt = review.reviewed_at || item.reviewedAt;
        if (shouldRender) {
            renderAll();
        }
    }

    function renderAll() {
        updateStats();
        renderTable();
        renderPlacementRecap();
    }

    function populatePlacementFilter() {
        const placements = [...new Set(attendances.map((item) => item.placement).filter((value) => value && value !== '-'))].sort((a, b) => a.localeCompare(b));
        placementFilter.innerHTML = '<option value="semua">Semua Penempatan</option>' + placements.map((placement) => `<option value="${placement}">${placement}</option>`).join('');
    }

    [periodFilter, studyFilter, placementFilter, statusFilter].forEach((input) => input.addEventListener('change', () => {
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

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderAll();
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        periodFilter.value = 'semua';
        studyFilter.value = 'semua';
        placementFilter.value = 'semua';
        statusFilter.value = 'semua';
        searchInput.value = '';
        currentPage = 1;
        renderAll();
    });

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderAll();
        });
    });

    document.querySelectorAll('[data-attendance-card]').forEach((card) => {
        card.addEventListener('click', () => {
            showToast(`Filter cepat ${card.dataset.attendanceCard} dapat digunakan melalui tabel rekap.`);
        });
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const id = Number(button.dataset.id);
        const action = button.dataset.action;
        if (action === 'detail') {
            showDetail(id);
        } else if (action === 'unduh') {
            window.location.href = `${attendanceBaseUrl}/${id}/unduh`;
        } else if (action === 'edit') {
            const item = getAttendance(id);
            document.getElementById('reviewTitle').textContent = `Edit Review - ${item?.name ?? '-'}`;
            document.getElementById('reviewPesertaId').value = id;
            document.getElementById('reviewStatus').value = item?.reviewStatus || 'draft';
            document.getElementById('reviewNote').value = item?.reviewNote === '-' ? '' : (item?.reviewNote || '');
            reviewModal.show();
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

    document.getElementById('exportButton').addEventListener('click', () => {
        window.location.href = `${attendanceBaseUrl}/ekspor`;
    });
    document.getElementById('validateAllButton').addEventListener('click', () => confirmAction('massal'));

    document.getElementById('confirmAction').addEventListener('click', async () => {
        try {
            if (pendingAction === 'hapus') {
                const result = await submitReview(pendingId, 'DELETE', '');
                const item = getAttendance(pendingId);
                if (item) {
                    item.reviewStatus = 'draft';
                    item.reviewNote = '-';
                    item.reviewedAt = '-';
                }
                showToast(result.message || 'Review rekap absensi berhasil dihapus.');
            } else if (pendingAction === 'validasi') {
                const result = await submitReview(pendingId, 'POST', '/validasi', { catatan: 'Rekap absensi telah divalidasi admin.' });
                applyReviewResult(pendingId, result.review);
                showToast(result.message || 'Rekap absensi berhasil divalidasi.');
            } else {
                const items = filteredAttendances();
                for (const item of items) {
                    const result = await submitReview(item.id, 'POST', '/validasi', { catatan: 'Validasi massal oleh admin.' });
                    applyReviewResult(item.id, result.review, false);
                }
                showToast('Validasi massal rekap absensi berhasil diproses.');
            }
        } catch (error) {
            showToast(error.message || 'Gagal memproses tindakan.', false);
        } finally {
            pendingAction = null;
            pendingId = null;
            confirmModal.hide();
            renderAll();
        }
    });

    reviewForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const id = Number(document.getElementById('reviewPesertaId').value);
        try {
            const result = await submitReview(id, 'PATCH', '', {
                status: document.getElementById('reviewStatus').value,
                catatan: document.getElementById('reviewNote').value,
            });
            applyReviewResult(id, result.review);
            reviewModal.hide();
            showToast(result.message || 'Review rekap absensi berhasil diperbarui.');
        } catch (error) {
            showToast(error.message || 'Gagal menyimpan review.', false);
        }
    });

    populatePlacementFilter();
    renderAll();
</script>
@endpush
