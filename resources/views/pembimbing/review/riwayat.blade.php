@extends('pembimbing.layout.pembimbing')

@section('title', 'Riwayat Review')
@section('page-title', 'Riwayat Review')

@push('styles')
<style>
    .review-history-page .history-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .review-history-page .stat-card,
    .review-history-page .filter-card,
    .review-history-page .table-card,
    .review-history-page .side-panel { border:0; border-radius:8px; }
    .review-history-page .stat-card { cursor:pointer; transition:.2s ease; }
    .review-history-page .stat-card:hover,
    .review-history-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .review-history-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .review-history-page .table { font-size:14px; }
    .review-history-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .review-history-page .table tbody tr:hover { background:#f7fcfe; }
    .review-history-page .progress { height:10px; border-radius:999px; }
    .review-history-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:0; max-width:100%; }
    .review-history-page .action-group .btn { white-space:nowrap; }
    .review-history-page .activity-row,
    .review-history-page .distribution-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .review-history-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .review-history-page .pagination .page-link { color:#2a8fbd; }
    .review-history-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid review-history-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.review.index') }}">Review</a></li>
            <li class="breadcrumb-item active" aria-current="page">Riwayat Review</li>
        </ol>
    </nav>

    <section class="history-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Riwayat Review</h3>
                <p class="mb-0">Telusuri arsip evaluasi laporan, catatan pembimbing, hasil keputusan, dan aktivitas review yang pernah dilakukan.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="historyBadge">Tersinkron</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-clock-history"></i>
        <div>12 aktivitas review tersimpan bulan ini dan siap ditelusuri kembali.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Review</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="disetujui">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Disetujui</p><h3 class="mb-0" id="statApproved">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="revisi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Revisi</p><h3 class="mb-0" id="statRevision">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-pencil-square"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="ditolak">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Ditolak</p><h3 class="mb-0" id="statRejected">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Rata-rata Review</p><h3 class="mb-0" id="statAverageTime">0 Hari</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-stopwatch"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-month-card="bulan ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Bulan Ini</p><h3 class="mb-0" id="statThisMonth">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-calendar-month"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Mahasiswa / Histori</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, laporan, atau catatan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="studyFilter">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                        <option value="Akuntansi">Akuntansi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="agencyFilter">Instansi</label>
                    <select class="form-select" id="agencyFilter">
                        <option value="semua">Semua Instansi</option>
                        <option value="Dinas Kominfo DIY">Dinas Kominfo DIY</option>
                        <option value="Bappeda DIY">Bappeda DIY</option>
                        <option value="Bank BPD DIY">Bank BPD DIY</option>
                        <option value="PT Inovasi Digital">PT Inovasi Digital</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Review</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="revisi">Revisi</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Review</label>
                    <select class="form-select" id="dateFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="hari ini">Hari Ini</option>
                        <option value="minggu ini">Minggu Ini</option>
                        <option value="bulan ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter" title="Terapkan Filter"><i class="bi bi-funnel"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                        <div>
                            <h5 class="mb-1">Tabel Histori Review</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#reviewHistoryExtraPanel" aria-controls="reviewHistoryExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Panel
                            </button>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="exportButton"><i class="bi bi-download"></i> Export Riwayat</button>
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="printButton"><i class="bi bi-printer"></i> Cetak Riwayat</button>
                            <label for="perPageSelect" class="text-muted small">Data per halaman</label>
                            <select class="form-select form-select-sm" id="perPageSelect" style="width:90px">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="15">15</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive" id="tableWrapper">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mahasiswa</th>
                                    <th>Judul Laporan</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Tanggal Review</th>
                                    <th>Status Review</th>
                                    <th>Catatan Terakhir</th>
                                    <th>Reviewer</th>
                                    <th width="260">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="historyTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Riwayat review tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination riwayat review">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="reviewHistoryExtraPanel" aria-labelledby="reviewHistoryExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="reviewHistoryExtraPanelLabel">Panel Riwayat Review</h5>
            <small class="text-muted">Aktivitas terbaru dan distribusi hasil review</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Aktivitas Terbaru</h5>
                <div id="activityPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Distribusi Hasil Review</h5>
                <div id="distributionPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Riwayat Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Riwayat Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                    <div>
                        <p class="mb-2" id="confirmMessage">Jalankan tindakan riwayat?</p>
                        <div class="small text-muted" id="confirmSummary"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="historyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const histories = [
        { id: 1, nama: 'Budi Santoso', nim: '220002', prodi: 'Manajemen', instansi: 'Bappeda DIY', judul: 'Evaluasi Program Kerja Bidang', jenis: 'Laporan Akhir', tanggal: '25 Mei 2026', period: 'hari ini', status: 'disetujui', catatan: 'Laporan lengkap dan siap penilaian.', reviewer: 'Dr. Citra Maharani', durasi: 2, dokumen: 'laporan-budi.pdf' },
        { id: 2, nama: 'Dewi Lestari', nim: '220003', prodi: 'Administrasi Publik', instansi: 'Bank BPD DIY', judul: 'Observasi Layanan Administrasi', jenis: 'Laporan Mingguan', tanggal: '25 Mei 2026', period: 'hari ini', status: 'revisi', catatan: 'Tambahkan data pendukung observasi.', reviewer: 'Dr. Citra Maharani', durasi: 1, dokumen: 'laporan-dewi.pdf' },
        { id: 3, nama: 'Intan Safitri', nim: '220005', prodi: 'Informatika', instansi: 'PT Inovasi Digital', judul: 'Pengujian Modul Absensi', jenis: 'Laporan Akhir', tanggal: '24 Mei 2026', period: 'minggu ini', status: 'disetujui', catatan: 'Dokumen final diterima.', reviewer: 'Dr. Citra Maharani', durasi: 3, dokumen: 'laporan-intan.pdf' },
        { id: 4, nama: 'Lukman Hakim', nim: '220006', prodi: 'Manajemen', instansi: 'Bappeda DIY', judul: 'Koordinasi Program Kerja', jenis: 'Laporan Mingguan', tanggal: '22 Mei 2026', period: 'minggu ini', status: 'revisi', catatan: 'Perbaiki format tabel kegiatan.', reviewer: 'Dr. Citra Maharani', durasi: 2, dokumen: 'laporan-lukman.pdf' },
        { id: 5, nama: 'Maya Putri', nim: '220007', prodi: 'Administrasi Publik', instansi: 'Dinas Kominfo DIY', judul: 'Klasifikasi Dokumen Layanan Publik', jenis: 'Laporan Akhir', tanggal: '21 Mei 2026', period: 'minggu ini', status: 'disetujui', catatan: 'Sangat baik.', reviewer: 'Dr. Citra Maharani', durasi: 1, dokumen: 'laporan-maya.pdf' },
        { id: 6, nama: 'Naufal Rizky', nim: '220008', prodi: 'Informatika', instansi: 'PT Inovasi Digital', judul: 'Wireframe Laporan Digital', jenis: 'Dokumen Kegiatan', tanggal: '20 Mei 2026', period: 'bulan ini', status: 'revisi', catatan: 'Lengkapi lampiran desain.', reviewer: 'Dr. Citra Maharani', durasi: 4, dokumen: 'wireframe-naufal.pdf' },
        { id: 7, nama: 'Rani Kartika', nim: '220010', prodi: 'Manajemen', instansi: 'Bappeda DIY', judul: 'Analisis Kebutuhan Program', jenis: 'Laporan Akhir', tanggal: '19 Mei 2026', period: 'bulan ini', status: 'revisi', catatan: 'Revisi minor pada kesimpulan.', reviewer: 'Dr. Citra Maharani', durasi: 2, dokumen: 'laporan-rani.pdf' },
        { id: 8, nama: 'Satria Wibowo', nim: '220011', prodi: 'Informatika', instansi: 'Dinas Kominfo DIY', judul: 'Dokumentasi API Internal', jenis: 'Dokumen Kegiatan', tanggal: '18 Mei 2026', period: 'bulan ini', status: 'ditolak', catatan: 'Dokumen tidak lengkap.', reviewer: 'Dr. Citra Maharani', durasi: 3, dokumen: 'api-satria.pdf' },
        { id: 9, nama: 'Oktavia Rahma', nim: '220009', prodi: 'Akuntansi', instansi: 'Bank BPD DIY', judul: 'Input Transaksi Simulasi', jenis: 'Laporan Mingguan', tanggal: '17 Mei 2026', period: 'bulan ini', status: 'disetujui', catatan: 'Data lengkap.', reviewer: 'Dr. Citra Maharani', durasi: 2, dokumen: 'laporan-oktavia.pdf' },
        { id: 10, nama: 'Aulia Berliana', nim: '220001', prodi: 'Informatika', instansi: 'Dinas Kominfo DIY', judul: 'Dashboard Monitoring Magang', jenis: 'Laporan Mingguan', tanggal: '16 Mei 2026', period: 'bulan ini', status: 'arsip', catatan: 'Riwayat review diarsipkan.', reviewer: 'Dr. Citra Maharani', durasi: 2, dokumen: 'laporan-aulia.pdf' }
    ];

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('historyTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('historyToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return { disetujui: 'bg-success', revisi: 'bg-warning text-dark', ditolak: 'bg-danger', arsip: 'bg-secondary' }[value];
    }

    function filteredHistories() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const date = dateFilter.value;
        return histories.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.instansi, item.judul, item.jenis, item.status, item.catatan, item.reviewer].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.instansi === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchDate = date === 'semua' || item.period === date;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchDate;
        });
    }

    function updateStats() {
        const average = Math.round(histories.reduce((sum, item) => sum + item.durasi, 0) / histories.length);
        document.getElementById('statTotal').textContent = histories.length;
        document.getElementById('statApproved').textContent = histories.filter((item) => item.status === 'disetujui').length;
        document.getElementById('statRevision').textContent = histories.filter((item) => item.status === 'revisi').length;
        document.getElementById('statRejected').textContent = histories.filter((item) => item.status === 'ditolak').length;
        document.getElementById('statAverageTime').textContent = `${average} Hari`;
        document.getElementById('statThisMonth').textContent = histories.filter((item) => item.period === 'bulan ini' || item.period === 'minggu ini' || item.period === 'hari ini').length;
    }

    function renderPanels() {
        document.getElementById('activityPanel').innerHTML = histories.slice(0, 5).map((item) => `
            <div class="activity-row">
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold">${item.nama}</div>
                        <div class="small text-muted">${item.tanggal} - ${item.judul}</div>
                    </div>
                    <span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span>
                </div>
                <div class="small mt-2">${item.catatan}</div>
            </div>
        `).join('');

        const statuses = ['disetujui', 'revisi', 'ditolak', 'arsip'];
        document.getElementById('distributionPanel').innerHTML = statuses.map((status) => {
            const count = histories.filter((item) => item.status === status).length;
            const percent = Math.round((count / histories.length) * 100);
            return `
                <div class="distribution-row">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">${titleCase(status)}</span>
                        <span>${count} review</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar ${statusClass(status).replace(' text-dark', '')}" style="width:${percent}%"></div>
                    </div>
                </div>
            `;
        }).join('');
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

    function renderTable() {
        const data = filteredHistories();
        const perPage = Number(perPageSelect.value);
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>
                    <div class="fw-semibold">${item.nama}</div>
                    <div class="small text-muted">${item.nim} - ${item.prodi}</div>
                </td>
                <td>${item.judul}</td>
                <td>${item.jenis}</td>
                <td>${item.tanggal}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>${item.catatan}</td>
                <td>${item.reviewer}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} riwayat review ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} riwayat`
            : 'Menampilkan 0 riwayat';
        renderPagination(totalPages);
        updateStats();
        renderPanels();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('historyToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'detail') {
        window.__currentHistoryItem = item;
        const titles = {
            detail: 'Detail Riwayat Review'
        };
        document.getElementById('detailTitle').textContent = titles[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Instansi</strong><div>${item.instansi}</div></div>
                <div class="col-12"><strong>Judul Laporan</strong><div>${item.judul}</div></div>
                <div class="col-md-6"><strong>Jenis Dokumen</strong><div>${item.jenis}</div></div>
                <div class="col-md-6"><strong>Dokumen</strong><div>${item.dokumen}</div></div>
                <div class="col-md-6"><strong>Tanggal Review</strong><div>${item.tanggal}</div></div>
                <div class="col-md-6"><strong>Status Review</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Reviewer</strong><div>${item.reviewer}</div></div>
                <div class="col-md-6"><strong>Durasi Review</strong><div>${item.durasi} hari</div></div>
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button class="btn btn-outline-primary btn-sm" type="button" data-detail-action="dokumen">Lihat Dokumen</button>
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-detail-action="catatan">Lihat Catatan</button>
                            </div>
                            <div id="detailActionPanel">
                                <div class="text-muted small">Pilih salah satu aksi di atas untuk melihat rincian tambahan.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmMessage').textContent = `Jalankan tindakan ${titleCase(action)} untuk riwayat ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.judul} - ${titleCase(item.status)} - ${item.tanggal}`;
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card], [data-month-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-month-card]').addEventListener('click', () => {
        dateFilter.value = 'bulan ini';
        currentPage = 1;
        renderTable();
    });

    [studyFilter, agencyFilter, statusFilter, dateFilter].forEach((input) => {
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

    document.getElementById('applyFilter').addEventListener('click', () => {
        currentPage = 1;
        renderTable();
        showToast('Filter riwayat review berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card], [data-month-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter riwayat review berhasil direset.', 'info');
    });

    perPageSelect.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = histories.find((history) => history.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item, action);
            return;
        }
    });

    document.getElementById('detailContent').addEventListener('click', (event) => {
        const button = event.target.closest('button[data-detail-action]');
        if (!button || !window.__currentHistoryItem) return;
        const action = button.dataset.detailAction;
        const item = window.__currentHistoryItem;
        const panel = document.getElementById('detailActionPanel');
        if (action === 'dokumen') {
            panel.innerHTML = `
                <div class="border rounded p-3 bg-white">
                    <div class="fw-semibold mb-2">Dokumen Review</div>
                    <div class="small text-muted mb-1">${item.dokumen}</div>
                    <div class="small text-muted">Dokumen ini menjadi arsip hasil pemeriksaan laporan pada tanggal ${item.tanggal}.</div>
                </div>
            `;
            return;
        }
        panel.innerHTML = `
            <div class="border rounded p-3 bg-white">
                <div class="fw-semibold mb-2">Catatan Review</div>
                <div class="small text-muted">${item.catatan}</div>
            </div>
        `;
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Riwayat review berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('printButton').addEventListener('click', () => showToast('Riwayat review siap dicetak.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('historyBadge').textContent = 'Diperbarui';
        showToast('Riwayat review berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi riwayat: arsip review bulan ini sudah diperbarui.', 'warning'), 800);
});
</script>
@endpush
