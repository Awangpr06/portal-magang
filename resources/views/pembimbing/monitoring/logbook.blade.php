@extends('pembimbing.layout.pembimbing')

@section('title', 'Logbook Harian')
@section('page-title', 'Logbook Harian')

@push('styles')
<style>
    .logbook-page .logbook-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .logbook-page .stat-card,
    .logbook-page .filter-card,
    .logbook-page .table-card,
    .logbook-page .side-panel { border:0; border-radius:8px; }
    .logbook-page .stat-card { cursor:pointer; transition:.2s ease; }
    .logbook-page .stat-card:hover,
    .logbook-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .logbook-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .logbook-page .table { font-size:14px; }
    .logbook-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .logbook-page .table tbody tr:hover { background:#f7fcfe; }
    .logbook-page .progress { height:10px; border-radius:999px; }
    .logbook-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:0; max-width:100%; }
    .logbook-page .action-group .btn { white-space:nowrap; }
    .logbook-page .detail-row,
    .logbook-page .history-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .logbook-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .logbook-page .pagination .page-link { color:#2a8fbd; }
    .logbook-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid logbook-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.monitoring.index') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Logbook Harian</li>
        </ol>
    </nav>

    <section class="logbook-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Logbook Harian</h3>
                <p class="mb-0">Pantau catatan aktivitas harian mahasiswa, output kegiatan, konsistensi pengisian, dan status review logbook.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="logbookBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-journal-check"></i>
        <div>5 logbook menunggu review dan 3 aktivitas terlambat membutuhkan tindak lanjut.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-card-filter="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Logbook</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-journals"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-date-card="hari ini">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Logbook Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar-day"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="diverifikasi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Diverifikasi</p><h3 class="mb-0" id="statVerified">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="menunggu review">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu Review</p><h3 class="mb-0" id="statWaiting">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="terlambat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terlambat</p><h3 class="mb-0" id="statLate">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-alarm"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-consistency-card="tinggi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Konsistensi</p><h3 class="mb-0" id="statConsistency">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-graph-up"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Mahasiswa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, kegiatan, atau output">
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
                    <label class="form-label" for="statusFilter">Status Logbook</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="diverifikasi">Diverifikasi</option>
                        <option value="menunggu review">Menunggu Review</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="perlu revisi">Perlu Revisi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Aktivitas</label>
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
                            <h5 class="mb-1">Tabel Logbook Mahasiswa</h5>
                            <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#logbookExtraPanel" aria-controls="logbookExtraPanel">
                                <i class="bi bi-layout-sidebar-inset"></i> Panel
                            </button>
                            <button class="btn btn-outline-primary btn-sm" type="button" id="exportButton"><i class="bi bi-download"></i> Export Data</button>
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
                                    <th>Tanggal Aktivitas</th>
                                    <th>Judul Kegiatan</th>
                                    <th>Durasi</th>
                                    <th>Output Kegiatan</th>
                                    <th>Status Logbook</th>
                                    <th>Waktu Input</th>
                                    <th>Status Review</th>
                                    <th width="260">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="logbookTable"></tbody>
                        </table>
                    </div>

                    <div class="empty-state d-none" id="emptyState">
                        <div>
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <h6 class="mb-1">Data logbook tidak ditemukan</h6>
                            <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                        <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                        <nav aria-label="Pagination logbook">
                            <ul class="pagination mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="logbookExtraPanel" aria-labelledby="logbookExtraPanelLabel" style="width:min(92vw, 460px);">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-0" id="logbookExtraPanelLabel">Panel Logbook</h5>
            <small class="text-muted">Detail aktivitas terbaru dan riwayat logbook</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
        <div class="card side-panel shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Detail Aktivitas Terbaru</h5>
                <div id="detailPanel"></div>
            </div>
        </div>
        <div class="card side-panel shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Riwayat Logbook</h5>
                <div id="historyPanel"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Logbook</h5>
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
                <h5 class="modal-title" id="confirmTitle">Review Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <i class="bi bi-journal-check text-primary fs-3"></i>
                            <div class="w-100">
                                <p class="mb-2" id="confirmMessage">Periksa logbook terlebih dahulu sebelum diverifikasi.</p>
                                <div class="small text-muted mb-3" id="confirmSummary"></div>
                                <label class="form-label" for="reviewNote">Catatan Evaluasi</label>
                                <textarea class="form-control" id="reviewNote" rows="3" placeholder="Tambahkan catatan evaluasi logbook"></textarea>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="reviewConfirmCheck">
                                    <label class="form-check-label" for="reviewConfirmCheck">Saya yakin menverifikasi logbook</label>
                                </div>
                                <div class="small text-muted mt-2" id="reviewStatusHint">Centang kotak untuk mengaktifkan tombol verifikasi.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="verifyAction" disabled>Verifikasi Logbook</button>
                <button type="button" class="btn btn-success" id="saveAction" disabled>Simpan Catatan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Pesan ke Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="messageForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="messageRecipient">Penerima</label>
                            <input type="text" class="form-control" id="messageRecipient" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="messageSubject">Subjek</label>
                            <input type="text" class="form-control" id="messageSubject" name="subject" maxlength="150" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="messageBody">Pesan</label>
                            <textarea class="form-control" id="messageBody" name="message" rows="5" maxlength="5000" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="messageAttachment">Lampiran URL</label>
                            <input type="text" class="form-control" id="messageAttachment" name="attachment" maxlength="255" placeholder="Opsional">
                        </div>
                    </div>
                    <div class="small text-muted mt-3" id="messageSummary"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="logbookToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const messageStoreUrlTemplate = @json(route('pembimbing.mahasiswa.pesan.store', ['internship' => '__INTERNSHIP__']));
    const logbooks = [
        { id: 1, nama: 'Aulia Berliana', nim: '220001', prodi: 'Informatika', instansi: 'Dinas Kominfo DIY', tanggal: '25 Mei 2026', period: 'hari ini', judul: 'Pengembangan dashboard monitoring', durasi: '7 jam', output: 'Komponen statistik selesai', status: 'menunggu review', input: '25 Mei 2026 16:20', review: 'Belum Direview', catatan: 'Perlu pengecekan dokumentasi.' },
        { id: 2, nama: 'Budi Santoso', nim: '220002', prodi: 'Manajemen', instansi: 'Bappeda DIY', tanggal: '25 Mei 2026', period: 'hari ini', judul: 'Rekap program kerja bidang', durasi: '6 jam', output: 'Rekap data mingguan', status: 'diverifikasi', input: '25 Mei 2026 15:40', review: 'Disetujui', catatan: 'Aktivitas sesuai target.' },
        { id: 3, nama: 'Dewi Lestari', nim: '220003', prodi: 'Administrasi Publik', instansi: 'Bank BPD DIY', tanggal: '24 Mei 2026', period: 'minggu ini', judul: 'Observasi layanan administrasi', durasi: '5 jam', output: 'Catatan observasi', status: 'perlu revisi', input: '24 Mei 2026 17:05', review: 'Perlu Revisi', catatan: 'Deskripsi kegiatan perlu diperjelas.' },
        { id: 4, nama: 'Gita Permata', nim: '220004', prodi: 'Akuntansi', instansi: 'PT Inovasi Digital', tanggal: '23 Mei 2026', period: 'minggu ini', judul: 'Dokumen audit internal', durasi: '4 jam', output: 'Draft audit', status: 'terlambat', input: '24 Mei 2026 09:10', review: 'Belum Direview', catatan: 'Input melewati batas waktu.' },
        { id: 5, nama: 'Intan Safitri', nim: '220005', prodi: 'Informatika', instansi: 'PT Inovasi Digital', tanggal: '25 Mei 2026', period: 'hari ini', judul: 'Pengujian fitur absensi', durasi: '8 jam', output: 'Laporan hasil uji', status: 'diverifikasi', input: '25 Mei 2026 16:00', review: 'Disetujui', catatan: 'Output lengkap.' },
        { id: 6, nama: 'Lukman Hakim', nim: '220006', prodi: 'Manajemen', instansi: 'Bappeda DIY', tanggal: '25 Mei 2026', period: 'hari ini', judul: 'Notulen rapat koordinasi', durasi: '6 jam', output: 'Notulen rapat', status: 'menunggu review', input: '25 Mei 2026 15:55', review: 'Belum Direview', catatan: 'Menunggu validasi pembimbing.' },
        { id: 7, nama: 'Maya Putri', nim: '220007', prodi: 'Administrasi Publik', instansi: 'Dinas Kominfo DIY', tanggal: '22 Mei 2026', period: 'minggu ini', judul: 'Klasifikasi dokumen layanan', durasi: '7 jam', output: 'Daftar klasifikasi', status: 'diverifikasi', input: '22 Mei 2026 16:30', review: 'Disetujui', catatan: 'Kegiatan selesai.' },
        { id: 8, nama: 'Naufal Rizky', nim: '220008', prodi: 'Informatika', instansi: 'PT Inovasi Digital', tanggal: '21 Mei 2026', period: 'bulan ini', judul: 'Wireframe laporan digital', durasi: '5 jam', output: 'Wireframe halaman', status: 'terlambat', input: '22 Mei 2026 10:15', review: 'Belum Direview', catatan: 'Perlu disiplin input harian.' },
        { id: 9, nama: 'Oktavia Rahma', nim: '220009', prodi: 'Akuntansi', instansi: 'Bank BPD DIY', tanggal: '25 Mei 2026', period: 'hari ini', judul: 'Input transaksi simulasi', durasi: '6 jam', output: 'Data transaksi', status: 'diverifikasi', input: '25 Mei 2026 16:05', review: 'Disetujui', catatan: 'Data lengkap.' },
        { id: 10, nama: 'Rani Kartika', nim: '220010', prodi: 'Manajemen', instansi: 'Bappeda DIY', tanggal: '24 Mei 2026', period: 'minggu ini', judul: 'Analisis kebutuhan program', durasi: '6 jam', output: 'Ringkasan analisis', status: 'menunggu review', input: '24 Mei 2026 16:50', review: 'Belum Direview', catatan: 'Perlu review laporan akhir.' },
        { id: 11, nama: 'Satria Wibowo', nim: '220011', prodi: 'Informatika', instansi: 'Dinas Kominfo DIY', tanggal: '23 Mei 2026', period: 'minggu ini', judul: 'Dokumentasi API internal', durasi: '4 jam', output: 'Dokumentasi awal', status: 'perlu revisi', input: '23 Mei 2026 17:25', review: 'Perlu Revisi', catatan: 'Dokumentasi belum lengkap.' }
    ];

    let currentPage = 1;
    let pendingAction = null;
    let reviewVerified = false;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const agencyFilter = document.getElementById('agencyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('logbookTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
    const messageForm = document.getElementById('messageForm');
    const reviewConfirmCheck = document.getElementById('reviewConfirmCheck');
    const reviewStatusHint = document.getElementById('reviewStatusHint');
    const verifyAction = document.getElementById('verifyAction');
    const saveAction = document.getElementById('saveAction');
    const toast = new bootstrap.Toast(document.getElementById('logbookToast'), { delay: 3000 });

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            diverifikasi: 'bg-success',
            'sudah direview': 'bg-secondary',
            'menunggu review': 'bg-warning text-dark',
            terlambat: 'bg-danger',
            'perlu revisi': 'bg-info text-dark'
        }[value];
    }

    function filteredLogbooks() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const agency = agencyFilter.value;
        const status = statusFilter.value;
        const date = dateFilter.value;

        return logbooks.filter((item) => {
            const matchKeyword = !keyword || [item.nama, item.nim, item.prodi, item.instansi, item.judul, item.output, item.status, item.review, item.catatan].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || item.prodi === study;
            const matchAgency = agency === 'semua' || item.instansi === agency;
            const matchStatus = status === 'semua' || item.status === status;
            const matchDate = date === 'semua' || item.period === date;
            return matchKeyword && matchStudy && matchAgency && matchStatus && matchDate;
        });
    }

    function updateStats() {
        const consistency = Math.round((logbooks.filter((item) => item.status === 'diverifikasi').length / logbooks.length) * 100);
        document.getElementById('statTotal').textContent = logbooks.length;
        document.getElementById('statToday').textContent = logbooks.filter((item) => item.period === 'hari ini').length;
        document.getElementById('statVerified').textContent = logbooks.filter((item) => item.status === 'diverifikasi').length;
        document.getElementById('statWaiting').textContent = logbooks.filter((item) => item.status === 'menunggu review').length;
        document.getElementById('statLate').textContent = logbooks.filter((item) => item.status === 'terlambat').length;
        document.getElementById('statConsistency').textContent = `${consistency}%`;
    }

    function renderPanels() {
        document.getElementById('detailPanel').innerHTML = logbooks.slice(0, 4).map((item) => `
            <div class="detail-row">
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <div class="fw-semibold">${item.nama}</div>
                        <div class="small text-muted">${item.judul}</div>
                    </div>
                    <span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span>
                </div>
                <div class="small mt-2">Output: ${item.output}</div>
            </div>
        `).join('');

        document.getElementById('historyPanel').innerHTML = logbooks.slice(4, 9).map((item) => `
            <div class="history-row">
                <div class="fw-semibold">${item.tanggal}</div>
                <div class="small text-muted">${item.nama} - ${item.review}</div>
            </div>
        `).join('');
    }

    function syncReviewActions() {
        const noteFilled = document.getElementById('reviewNote').value.trim().length > 0;
        verifyAction.disabled = reviewVerified || !reviewConfirmCheck.checked;
        saveAction.disabled = !noteFilled;
        if (!noteFilled) {
            saveAction.title = 'Isi catatan review terlebih dahulu';
        } else if (reviewConfirmCheck.checked) {
            saveAction.title = 'Simpan sebagai diverifikasi';
        } else {
            saveAction.title = 'Simpan sebagai sudah direview';
        }
    }

    function resetReviewActions() {
        reviewVerified = false;
        reviewConfirmCheck.checked = false;
        reviewConfirmCheck.disabled = false;
        reviewStatusHint.textContent = 'Centang kotak untuk mengaktifkan tombol verifikasi.';
        verifyAction.textContent = 'Verifikasi Logbook';
        saveAction.textContent = 'Simpan Catatan';
        verifyAction.disabled = true;
        saveAction.disabled = true;
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
        const data = filteredLogbooks();
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
                <td>${item.tanggal}</td>
                <td>${item.judul}</td>
                <td>${item.durasi}</td>
                <td>${item.output}</td>
                <td><span class="badge ${statusClass(item.status)}">${titleCase(item.status)}</span></td>
                <td>${item.input}</td>
                <td>${item.review}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <button class="btn btn-primary btn-sm" type="button" data-action="review" data-id="${item.id}">Review Aktivitas</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="pesan" data-id="${item.id}">Kirim Pesan</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} logbook ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} logbook`
            : 'Menampilkan 0 logbook';
        renderPagination(totalPages);
        updateStats();
        renderPanels();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('logbookToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function openDetail(item, action = 'detail') {
        const titles = {
            detail: 'Detail Logbook',
            review: 'Verifikasi Logbook',
            pesan: 'Kirim Pesan'
        };
        document.getElementById('detailTitle').textContent = titles[action];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Mahasiswa</strong><div>${item.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${item.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${item.prodi}</div></div>
                <div class="col-md-6"><strong>Instansi</strong><div>${item.instansi}</div></div>
                <div class="col-md-6"><strong>Tanggal Aktivitas</strong><div>${item.tanggal}</div></div>
                <div class="col-md-6"><strong>Durasi</strong><div>${item.durasi}</div></div>
                <div class="col-12"><strong>Judul Kegiatan</strong><div>${item.judul}</div></div>
                <div class="col-12"><strong>Output Kegiatan</strong><div>${item.output}</div></div>
                <div class="col-md-6"><strong>Status Logbook</strong><div>${titleCase(item.status)}</div></div>
                <div class="col-md-6"><strong>Status Review</strong><div>${item.review}</div></div>
                <div class="col-12"><strong>Catatan Evaluasi</strong><div>${item.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        const actionLabel = action === 'review' ? 'Verifikasi Logbook' : titleCase(action);
        document.getElementById('confirmTitle').textContent = action === 'review' ? 'Review Aktivitas' : 'Konfirmasi Logbook';
        document.getElementById('confirmMessage').textContent = action === 'review'
            ? `Periksa logbook ${item.nama} lalu verifikasi bila sudah sesuai.`
            : `Simpan tindak lanjut ${actionLabel} untuk logbook ${item.nama}?`;
        document.getElementById('confirmSummary').textContent = `${item.tanggal} - ${item.judul} - ${titleCase(item.status)}`;
        document.getElementById('reviewNote').value = item.catatan;
        resetReviewActions();
        syncReviewActions();
        confirmModal.show();
    }

    function openMessageModal(item) {
        document.getElementById('messageRecipient').value = `${item.nama} (${item.nim})`;
        document.getElementById('messageSubject').value = `Monitoring Logbook - ${item.nama}`;
        document.getElementById('messageBody').value = '';
        document.getElementById('messageAttachment').value = '';
        document.getElementById('messageSummary').textContent = `Pesan akan dikirim ke ${item.nama} dan tersimpan pada percakapan peserta.`;
        messageForm.action = messageStoreUrlTemplate.replace('__INTERNSHIP__', item.id);
        messageModal.show();
    }

    document.querySelectorAll('[data-card-filter], [data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-card-filter], [data-status-card], [data-date-card], [data-consistency-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard || 'semua';
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-date-card]').addEventListener('click', () => {
        dateFilter.value = 'hari ini';
        currentPage = 1;
        renderTable();
    });

    document.querySelector('[data-consistency-card]').addEventListener('click', () => showToast('Tingkat konsistensi dihitung dari logbook yang sudah diverifikasi.', 'info'));

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
        showToast('Filter logbook berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        agencyFilter.value = 'semua';
        statusFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-card-filter], [data-status-card], [data-date-card], [data-consistency-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-card-filter="semua"]').classList.add('active');
        renderTable();
        showToast('Filter logbook berhasil direset.', 'info');
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
        const item = logbooks.find((logbook) => logbook.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item, action);
            return;
        }
        if (action === 'pesan') {
            openMessageModal(item);
            return;
        }
        openConfirm(item, action);
    });

    reviewConfirmCheck.addEventListener('change', syncReviewActions);
    document.getElementById('reviewNote').addEventListener('input', syncReviewActions);

    verifyAction.addEventListener('click', () => {
        if (!pendingAction || pendingAction.action !== 'review') return;
        if (!reviewConfirmCheck.checked) return;
        reviewVerified = true;
        reviewConfirmCheck.disabled = true;
        reviewStatusHint.textContent = 'Logbook sudah diverifikasi. Silakan isi catatan lalu simpan.';
        verifyAction.disabled = true;
        syncReviewActions();
        showToast('Logbook siap disimpan. Tambahkan catatan review jika diperlukan.', 'info');
    });

    saveAction.addEventListener('click', () => {
        if (!pendingAction || pendingAction.action !== 'review') return;
        const note = document.getElementById('reviewNote').value.trim();
        if (!note) {
            showToast('Catatan review masih kosong.', 'warning');
            return;
        }
        pendingAction.item.catatan = note;
        if (reviewConfirmCheck.checked && reviewVerified) {
            pendingAction.item.status = 'diverifikasi';
            pendingAction.item.review = 'Disetujui';
        } else {
            pendingAction.item.status = 'sudah direview';
            pendingAction.item.review = 'Sudah Direview';
        }
        confirmModal.hide();
        renderTable();
        openDetail(pendingAction.item, pendingAction.action);
        showToast(`Review logbook ${pendingAction.item.nama} berhasil disimpan.`);
        pendingAction = null;
        reviewVerified = false;
    });

    document.getElementById('exportButton').addEventListener('click', () => showToast('Data logbook berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('logbookBadge').textContent = 'Diperbarui';
        showToast('Data logbook harian berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi logbook: 5 logbook menunggu review.', 'warning'), 800);
});
</script>
@endpush
