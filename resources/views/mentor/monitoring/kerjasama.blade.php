@extends('mentor.layout.mentor')

@section('title', 'Kerjasama')
@section('page-title', 'Kerjasama')

@push('styles')
<style>
    .cooperation-page .cooperation-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .cooperation-page .stat-card,
    .cooperation-page .filter-card,
    .cooperation-page .table-card,
    .cooperation-page .panel-card { border:0; border-radius:8px; }
    .cooperation-page .stat-card { cursor:pointer; transition:.2s ease; }
    .cooperation-page .stat-card:hover,
    .cooperation-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .cooperation-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .cooperation-page .action-group { display:flex; flex-wrap:wrap; gap:6px; }
    .cooperation-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .cooperation-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .cooperation-page .pagination .page-link { color:#2a8fbd; }
    .cooperation-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid cooperation-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.monitoring') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kerjasama</li>
        </ol>
    </nav>

    <section class="cooperation-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Monitoring Kerjasama Mitra</h3>
                <p class="mb-0">Pantau kerja sama institusi dan mitra pelaksanaan magang untuk mendukung koordinasi penempatan peserta.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-light me-2" type="button" id="newCooperationButton"><i class="bi bi-plus-circle"></i> Tambah Kerjasama</button>
                <button class="btn btn-dark" type="button" id="exportTopButton"><i class="bi bi-download"></i> Export</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-building-check"></i>
        <div>2 kerja sama akan berakhir, 1 kerja sama selesai, dan 18 peserta aktif berada di mitra berjalan.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mitra</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-building"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kerjasama Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="akan berakhir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Akan Berakhir</p><h3 class="mb-0" id="statEnding">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-archive"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-participant-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peserta Aktif</p><h3 class="mb-0" id="statParticipant">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-people"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari perguruan tinggi atau dokumen">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Kerjasama</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="akan berakhir">Akan Berakhir</option>
                        <option value="selesai">Selesai</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="typeFilter">Jenis Mitra</label>
                    <select class="form-select" id="typeFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="Negeri">Negeri</option>
                        <option value="Swasta">Swasta</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="2026">2026</option>
                        <option value="2025-2026">2025-2026</option>
                        <option value="2026-2027">2026-2027</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="participantFilter">Peserta</label>
                    <select class="form-select" id="participantFilter">
                        <option value="semua">Semua Jumlah</option>
                        <option value="banyak">5 ke atas</option>
                        <option value="sedang">2 - 4</option>
                        <option value="sedikit">0 - 1</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-warning" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Data Kerja Sama Mitra</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan data kerja sama</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-warning btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mentorMonitoringPanel" aria-controls="mentorMonitoringPanel"><i class="bi bi-layout-sidebar-inset-reverse me-1"></i> Buka Panel</button>
                        <label class="text-muted small" for="perPageSelect">Data</label>
                        <select class="form-select form-select-sm" id="perPageSelect" style="width:80px">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Mitra</th>
                                    <th>Jenis Mitra</th>
                                    <th>Fakultas</th>
                                    <th>Prodi</th>
                                    <th>Status</th>
                                    <th>Peserta Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="cooperationTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data kerja sama sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination kerjasama"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mentorMonitoringPanel" aria-labelledby="mentorMonitoringPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mentorMonitoringPanelLabel">Panel Monitoring Kerjasama</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
    </div>
    <div class="offcanvas-body">
            <div class="card panel-card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Panel Informasi</h5>
                        <span class="badge bg-warning text-dark" id="panelCount">0</span>
                    </div>
                    <div id="infoPanel"></div>
                </div>
            </div>
            <div class="card panel-card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Tindakan Cepat</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning text-start" type="button" data-panel-action="tambah kerjasama baru"><i class="bi bi-plus-circle me-2"></i> Tambah Kerjasama Baru</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="export data"><i class="bi bi-download me-2"></i> Export Data</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="monitoring peserta"><i class="bi bi-people me-2"></i> Monitoring Peserta</button>
            </div>
    </div>
</div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Kerjasama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Kerjasama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Ringkasan Perubahan</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan perubahan"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Data kerja sama diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cooperations = [
        { id:1, mitra:'Universitas Negeri Yogyakarta', jenisPt:'Negeri', fakultas:'Teknik', prodi:'Teknologi Informasi', lokasi:'Yogyakarta', periode:'01 Januari-31 Desember 2026', pic:'Andi Pratama', status:'aktif', peserta:6, catatan:'Kerja sama berjalan baik untuk program magang.' },
        { id:2, mitra:'Universitas Ahmad Dahlan', jenisPt:'Swasta', fakultas:'Teknologi Industri', prodi:'Informatika', lokasi:'Yogyakarta', periode:'01 Juli 2025-30 Juni 2026', pic:'Hendra Wijaya', status:'akan berakhir', peserta:4, catatan:'Perlu pembaruan dokumen sebelum akhir periode.' },
        { id:3, mitra:'Universitas Gadjah Mada', jenisPt:'Negeri', fakultas:'Teknik', prodi:'Teknologi Informasi', lokasi:'Sleman', periode:'01 Januari-31 Desember 2025', pic:'Rina Maharani', status:'selesai', peserta:5, catatan:'Dokumen kerja sama telah selesai dan diarsipkan.' },
        { id:4, mitra:'Universitas Sanata Dharma', jenisPt:'Swasta', fakultas:'Sains dan Teknologi', prodi:'Informatika', lokasi:'Yogyakarta', periode:'01 Maret 2026-28 Februari 2027', pic:'Sari Wulandari', status:'aktif', peserta:3, catatan:'Koordinasi program berjalan stabil.' },
        { id:5, mitra:'Universitas Negeri Yogyakarta', jenisPt:'Negeri', fakultas:'Ilmu Sosial', prodi:'Administrasi Publik', lokasi:'Yogyakarta', periode:'01 April 2025-31 Maret 2026', pic:'Dr. Maya Kusuma', status:'selesai', peserta:0, catatan:'Kerja sama selesai dan dokumen sudah diarsipkan.' },
        { id:6, mitra:'Universitas Ahmad Dahlan', jenisPt:'Swasta', fakultas:'Ekonomi dan Bisnis', prodi:'Manajemen', lokasi:'Yogyakarta', periode:'01 Mei 2026-30 April 2027', pic:'Fajar Nugroho', status:'draft', peserta:0, catatan:'Draft kerja sama menunggu finalisasi dokumen.' },
        { id:7, mitra:'Universitas Gadjah Mada', jenisPt:'Negeri', fakultas:'Ilmu Sosial dan Politik', prodi:'Manajemen dan Kebijakan Publik', lokasi:'Sleman', periode:'01 Juni 2026-31 Mei 2027', pic:'Ratna Sari', status:'akan berakhir', peserta:2, catatan:'Perlu evaluasi penempatan peserta periode berjalan.' }
    ];

    const tableBody = document.getElementById('cooperationTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const periodFilter = document.getElementById('periodFilter');
    const participantFilter = document.getElementById('participantFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2600 });
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingAction = null;

    const statusClass = (status) => ({ aktif:'success', 'akan berakhir':'warning text-dark', selesai:'info text-dark', draft:'secondary' }[status] || 'secondary');
    const participantGroup = (value) => {
        if (value >= 5) return 'banyak';
        if (value >= 2) return 'sedang';
        return 'sedikit';
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        return cooperations.filter((item) => {
            const keywordMatch = !keyword || `${item.mitra} ${item.jenisPt} ${item.fakultas} ${item.prodi} ${item.pic} ${item.lokasi} ${item.status}`.toLowerCase().includes(keyword);
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const typeMatch = typeFilter.value === 'semua' || item.jenisPt === typeFilter.value;
            const periodMatch = periodFilter.value === 'semua' || item.periode.includes(periodFilter.value);
            const participantMatch = participantFilter.value === 'semua' || participantGroup(item.peserta) === participantFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && statusMatch && typeMatch && periodMatch && participantMatch && cardMatch;
        });
    }

    function renderStats() {
        document.getElementById('statTotal').textContent = cooperations.length;
        document.getElementById('statActive').textContent = cooperations.filter((item) => item.status === 'aktif').length;
        document.getElementById('statEnding').textContent = cooperations.filter((item) => item.status === 'akan berakhir').length;
        document.getElementById('statDone').textContent = cooperations.filter((item) => item.status === 'selesai').length;
        document.getElementById('statParticipant').textContent = cooperations.reduce((sum, item) => sum + item.peserta, 0);
    }

    function renderPanel() {
        const important = cooperations.filter((item) => item.status !== 'aktif').slice(0, 4);
        document.getElementById('panelCount').textContent = important.length;
        document.getElementById('infoPanel').innerHTML = important.map((item) => `
            <div class="info-row">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.mitra}</strong>
                    <span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span>
                </div>
                <small class="text-muted d-block">${item.periode} - PIC ${item.pic}</small>
                <small>${item.catatan}</small>
            </div>
        `).join('');
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) {
            html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}" type="button">${page}</button></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage + 1}" type="button">Next</button></li>`;
        pagination.innerHTML = html;
    }

    function renderTable() {
        const data = filteredData();
        const perPage = Number(perPageSelect.value);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);
        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.mitra}</td>
                <td><span class="badge bg-${item.jenisPt === 'Negeri' ? 'success' : 'info text-dark'}">${item.jenisPt}</span></td>
                <td>${item.fakultas}</td>
                <td>${item.prodi}</td>
                <td><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></td>
                <td>${item.peserta}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail" data-id="${item.id}">Detail</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} kerja sama ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderPanel();
    }

    function openDetail(item) {
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Nama Mitra</small><h5 class="mb-0">${item.mitra}</h5></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Jenis Mitra</small><div class="mt-2"><span class="badge bg-${item.jenisPt === 'Negeri' ? 'success' : 'info text-dark'}">${item.jenisPt}</span></div></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Status</small><div class="mt-2"><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></div></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Fakultas</small><h6 class="mb-0">${item.fakultas}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Prodi</small><h6 class="mb-0">${item.prodi}</h6></div></div>
                <div class="col-md-3"><div class="border rounded p-3 h-100"><small class="text-muted">Peserta Aktif</small><h6 class="mb-0">${item.peserta}</h6></div></div>
                <div class="col-md-4"><div class="border rounded p-3 h-100"><small class="text-muted">Periode Kerja Sama</small><h6 class="mb-0">${item.periode}</h6></div></div>
                <div class="col-md-5"><div class="border rounded p-3 h-100"><small class="text-muted">PIC</small><h6 class="mb-0">${item.pic}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Lokasi</small><h6 class="mb-0">${item.lokasi}</h6></div></div>
                <div class="col-md-6"><div class="border rounded p-3 h-100"><small class="text-muted">Catatan Kerja Sama</small><p class="mb-0">${item.catatan}</p></div></div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <strong>${item.mitra}</strong>
                <small class="text-muted d-block">${item.jenisPt} - ${item.periode}</small>
                <hr>
                <small class="text-muted">Tindakan kerjasama</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = item.catatan;
        confirmModal.show();
    }

    document.querySelectorAll('.stat-card[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            activeStatus = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter kerjasama berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = 'semua';
        typeFilter.value = 'semua';
        periodFilter.value = 'semua';
        participantFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter kerjasama berhasil direset.', 'info');
    });
    [searchInput, statusFilter, typeFilter, periodFilter, participantFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
    searchInput.addEventListener('keyup', (event) => { if (event.key === 'Enter') { currentPage = 1; renderTable(); } });
    perPageSelect.addEventListener('change', () => { currentPage = 1; renderTable(); });
    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });
    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = cooperations.find((cooperation) => cooperation.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item);
            return;
        }
        openConfirm(item, action);
    });
    document.querySelectorAll('[data-panel-action]').forEach((button) => {
        button.addEventListener('click', () => openConfirm(cooperations[0], button.dataset.panelAction));
    });
    document.getElementById('newCooperationButton').addEventListener('click', () => openConfirm(cooperations[0], 'tambah kerjasama baru'));
    document.getElementById('exportTopButton').addEventListener('click', () => showToast('Data kerja sama berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        pendingAction.item.catatan = document.getElementById('confirmNote').value || pendingAction.item.catatan;
        if (pendingAction.action === 'ubah data') pendingAction.item.status = 'aktif';
        confirmModal.hide();
        renderTable();
        showToast(`Tindakan ${pendingAction.action} berhasil disimpan.`);
        pendingAction = null;
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi kerjasama: 2 kerja sama akan berakhir.', 'warning'), 700);
});
</script>
@endpush
