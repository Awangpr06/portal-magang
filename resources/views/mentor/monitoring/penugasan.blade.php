@extends('mentor.layout.mentor')

@section('title', 'Penugasan Peserta')
@section('page-title', 'Penugasan Peserta')

@push('styles')
<style>
    .assignment-page .assignment-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .assignment-page .stat-card,
    .assignment-page .filter-card,
    .assignment-page .table-card,
    .assignment-page .panel-card { border:0; border-radius:8px; }
    .assignment-page .stat-card { cursor:pointer; transition:.2s ease; }
    .assignment-page .stat-card:hover,
    .assignment-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .assignment-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .assignment-page .progress { height:8px; background:#e8eef2; }
    .assignment-page .progress-bar { background:#2a8fbd; }
    .assignment-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:430px; }
    .assignment-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .assignment-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .assignment-page .pagination .page-link { color:#2a8fbd; }
    .assignment-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid assignment-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.monitoring') }}">Monitoring</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penugasan</li>
        </ol>
    </nav>

    <section class="assignment-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Manajemen Penugasan Peserta</h3>
                <p class="mb-0">Buat, kelola, pantau, dan evaluasi pelaksanaan tugas peserta magang selama kegiatan berlangsung.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-light me-2" type="button" id="newAssignmentButton" data-bs-toggle="modal" data-bs-target="#assignmentModal"><i class="bi bi-plus-circle"></i> Buat Penugasan</button>
                <button class="btn btn-dark" type="button" id="exportTopButton"><i class="bi bi-download"></i> Export</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-list-task"></i>
        <div>3 tugas melewati deadline, 5 tugas belum selesai, dan 4 progres tugas baru diperbarui peserta.</div>
    </div>


    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Tugas Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-list-task"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Tugas Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-participant-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peserta</p><h3 class="mb-0" id="statParticipant">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-people"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="belum selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Selesai</p><h3 class="mb-0" id="statPending">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="terlambat">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Lewat Deadline</p><h3 class="mb-0" id="statLate">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="tinggi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Penyelesaian</p><h3 class="mb-0" id="statCompletion">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-graph-up"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari tugas, peserta, kategori">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Tugas</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="belum selesai">Belum Selesai</option>
                        <option value="selesai">Selesai</option>
                        <option value="terlambat">Terlambat</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Minggu Ini">Minggu Ini</option>
                        <option value="Bulan Ini">Bulan Ini</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Analisis">Analisis</option>
                        <option value="Dokumentasi">Dokumentasi</option>
                        <option value="Pengembangan">Pengembangan</option>
                        <option value="Administrasi">Administrasi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="progressFilter">Progress</label>
                    <select class="form-select" id="progressFilter">
                        <option value="semua">Semua Progress</option>
                        <option value="tinggi">75% ke atas</option>
                        <option value="sedang">50% - 74%</option>
                        <option value="rendah">Di bawah 50%</option>
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
                        <h5 class="mb-0">Daftar Penugasan</h5>
                        <small class="text-muted" id="tableInfo">Menampilkan daftar tugas peserta</small>
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
                                    <th>Judul Tugas</th>
                                    <th>Peserta</th>
                                    <th>Kategori</th>
                                    <th>Tanggal Diberikan</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="assignmentTableBody"></tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="emptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data penugasan sesuai filter.</p></div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
                    <nav aria-label="Pagination penugasan"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="mentorMonitoringPanel" aria-labelledby="mentorMonitoringPanelLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mentorMonitoringPanelLabel">Panel Monitoring Penugasan</h5>
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
                        <button class="btn btn-warning text-start" type="button" data-panel-action="buat penugasan baru"><i class="bi bi-plus-circle me-2"></i> Buat Penugasan Baru</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="export data"><i class="bi bi-download me-2"></i> Export Data</button>
                        <button class="btn btn-outline-warning text-start" type="button" data-panel-action="monitoring progress"><i class="bi bi-graph-up me-2"></i> Monitoring Progress</button>
            </div>
    </div>
</div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Tugas</h5>
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
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Penugasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Catatan Perubahan</label>
                <textarea class="form-control" id="confirmNote" rows="3" placeholder="Tambahkan catatan penugasan"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmAction">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="assignmentModalLabel">Tambah Penugasan</h5>
                    <div class="text-muted small">Isi data tugas baru untuk peserta magang.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="assignmentForm" class="row g-3" enctype="multipart/form-data">
                    <input type="hidden" id="assignmentId" value="">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label" for="assignmentTitle">Judul Tugas</label>
                        <input type="text" class="form-control" id="assignmentTitle" placeholder="Masukkan judul tugas" required>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label" for="assignmentRecipient">Penerima Tugas (Peserta Magang)</label>
                        <select class="form-select" id="assignmentRecipient" required>
                            <option value="">Pilih peserta magang</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label" for="assignmentCategory">Kategori</label>
                        <select class="form-select" id="assignmentCategory" required>
                            <option value="Administrasi">Administrasi</option>
                            <option value="Analisis">Analisis</option>
                            <option value="Dokumentasi">Dokumentasi</option>
                            <option value="Pengembangan">Pengembangan</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label" for="assignmentDeadline">Deadline</label>
                        <input type="date" class="form-control" id="assignmentDeadline" required>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label" for="assignmentFile">Upload Berkas</label>
                        <input type="file" class="form-control" id="assignmentFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar">
                        <div class="form-text">Format: PDF, DOC, DOCX, JPG, PNG, ZIP, atau RAR.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="assignmentNote">Catatan</label>
                        <textarea class="form-control" id="assignmentNote" rows="3" placeholder="Catatan tugas"></textarea>
                    </div>
                    <div class="col-12 d-flex flex-wrap justify-content-end gap-2">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-warning" type="submit" id="assignmentSubmitButton"><i class="bi bi-save me-1"></i>Simpan Penugasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Data penugasan diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const assignments = @json($assignmentRows ?? []);
    const participants = @json($monitoringRows ?? []);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const createUrl = @json(route('mentor.monitoring.penugasan.store'));
    const updateBaseUrl = @json(url('/mentor/monitoring/penugasan'));

    const tableBody = document.getElementById('assignmentTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const periodFilter = document.getElementById('periodFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const progressFilter = document.getElementById('progressFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const assignmentModal = new bootstrap.Modal(document.getElementById('assignmentModal'));
    const assignmentForm = document.getElementById('assignmentForm');
    const assignmentId = document.getElementById('assignmentId');
    const assignmentTitle = document.getElementById('assignmentTitle');
    const assignmentRecipient = document.getElementById('assignmentRecipient');
    const assignmentCategory = document.getElementById('assignmentCategory');
    const assignmentDeadline = document.getElementById('assignmentDeadline');
    const assignmentFile = document.getElementById('assignmentFile');
    const assignmentNote = document.getElementById('assignmentNote');
    const assignmentSubmitButton = document.getElementById('assignmentSubmitButton');
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2600 });
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingAction = null;

    const statusClass = (status) => ({ aktif:'primary', selesai:'success', 'belum selesai':'warning text-dark', terlambat:'danger' }[status] || 'secondary');
    const progressGroup = (value) => {
        if (value >= 75) return 'tinggi';
        if (value >= 50) return 'sedang';
        return 'rendah';
    };

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function formatDateLabel(dateValue) {
        if (!dateValue) return '-';
        const date = new Date(dateValue);
        if (Number.isNaN(date.getTime())) return '-';
        return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#39;');
    }

    function buildFileLink(url, label, emptyLabel = 'Belum ada file') {
        if (!url) {
            return `<span class="text-muted">${emptyLabel}</span>`;
        }

        return `
            <a href="${url}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                <i class="bi bi-download me-1"></i>${label}
            </a>
        `;
    }

    function populateAssignmentRecipients() {
        const uniqueParticipants = participants
            .filter((item) => item.peserta_id && item.nama && item.nama !== '-')
            .filter((item, index, array) => array.findIndex((row) => row.peserta_id === item.peserta_id) === index);
        assignmentRecipient.innerHTML = '<option value="">Pilih peserta magang</option>' + uniqueParticipants.map((item) => `<option value="${item.peserta_id}">${item.nama}</option>`).join('');
    }

    function resetAssignmentForm() {
        assignmentId.value = '';
        assignmentForm.reset();
        assignmentCategory.value = 'Administrasi';
        assignmentFile.required = true;
        const modalLabel = document.getElementById('assignmentModalLabel');
        const modalDescription = document.querySelector('#assignmentModal .modal-body .text-muted.small');
        if (modalLabel) modalLabel.textContent = 'Tambah Penugasan';
        if (modalDescription) modalDescription.textContent = 'Isi data tugas baru untuk peserta magang.';
        assignmentSubmitButton.innerHTML = '<i class="bi bi-save me-1"></i>Simpan Penugasan';
    }

    function openAssignmentEditor(item = null) {
        resetAssignmentForm();

        if (item) {
            assignmentId.value = item.id;
            assignmentTitle.value = item.judul || '';
            assignmentRecipient.value = item.peserta_id || '';
            assignmentCategory.value = item.kategori_key || item.kategori || 'Administrasi';
            assignmentDeadline.value = item.deadline_raw || '';
            assignmentNote.value = item.catatan || '';
            assignmentFile.required = false;
            const modalLabel = document.getElementById('assignmentModalLabel');
            const modalDescription = document.querySelector('#assignmentModal .modal-body .text-muted.small');
            if (modalLabel) modalLabel.textContent = 'Edit Penugasan';
            if (modalDescription) modalDescription.textContent = 'Perbarui data tugas yang sudah tersimpan di database.';
            assignmentSubmitButton.innerHTML = '<i class="bi bi-save me-1"></i>Simpan Perubahan';
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('assignmentModal')).show();
    }

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        return assignments.filter((item) => {
            const keywordMatch = !keyword || `${item.judul} ${item.peserta} ${item.kategori} ${item.status} ${item.catatan}`.toLowerCase().includes(keyword);
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const periodMatch = periodFilter.value === 'semua' || item.periode === periodFilter.value;
            const categoryMatch = categoryFilter.value === 'semua' || item.kategori === categoryFilter.value;
            const progressMatch = progressFilter.value === 'semua' || progressGroup(item.progress) === progressFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && statusMatch && periodMatch && categoryMatch && progressMatch && cardMatch;
        });
    }

    function renderStats() {
        const participants = new Set(assignments.map((item) => item.peserta)).size;
        const completion = assignments.length ? Math.round(assignments.reduce((sum, item) => sum + item.progress, 0) / assignments.length) : 0;
        document.getElementById('statActive').textContent = assignments.filter((item) => item.status === 'aktif').length;
        document.getElementById('statDone').textContent = assignments.filter((item) => item.status === 'selesai').length;
        document.getElementById('statParticipant').textContent = participants;
        document.getElementById('statPending').textContent = assignments.filter((item) => item.status === 'belum selesai').length;
        document.getElementById('statLate').textContent = assignments.filter((item) => item.status === 'terlambat').length;
        document.getElementById('statCompletion').textContent = `${completion}%`;
    }

    function renderPanel() {
        const important = assignments.filter((item) => item.status !== 'selesai').slice(0, 4);
        document.getElementById('panelCount').textContent = important.length;
        document.getElementById('infoPanel').innerHTML = important.map((item) => `
            <div class="info-row">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.peserta}</strong>
                    <span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span>
                </div>
                <small class="text-muted d-block">${item.judul}</small>
                <div class="progress my-2"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div>
                <small>${item.progress}% - ${item.deadline}</small>
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
                <td><strong>${item.judul}</strong><small class="text-muted d-block">${item.catatan}</small></td>
                <td>${item.peserta}</td>
                <td>${item.kategori}</td>
                <td>${item.diberikan}</td>
                <td>${item.deadline}</td>
                <td><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail tugas" data-id="${item.id}">Detail</button>
                        <button class="btn btn-outline-warning btn-sm" type="button" data-action="edit tugas" data-id="${item.id}">Edit</button>
                        <button class="btn btn-outline-danger btn-sm" type="button" data-action="hapus tugas" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} penugasan ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
        renderPanel();
    }

    function openDetail(item) {
        if (!item) {
            showToast('Data penugasan tidak ditemukan.', 'danger');
            return;
        }

        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-7">
                    <div class="border rounded p-3 h-100">
                        <h6 class="fw-semibold mb-3">Informasi Penugasan</h6>
                        <div class="row g-2 small">
                            <div class="col-5 text-muted">Judul Tugas</div>
                            <div class="col-7 fw-semibold">${escapeHtml(item.judul || '-')}</div>
                            <div class="col-5 text-muted">Peserta</div>
                            <div class="col-7">${escapeHtml(item.peserta || '-')}</div>
                            <div class="col-5 text-muted">Kategori</div>
                            <div class="col-7">${escapeHtml(item.kategori || '-')}</div>
                            <div class="col-5 text-muted">Tanggal Diberikan</div>
                            <div class="col-7">${escapeHtml(item.diberikan || '-')}</div>
                            <div class="col-5 text-muted">Tanggal Deadline</div>
                            <div class="col-7">${escapeHtml(item.deadline || '-')}</div>
                            <div class="col-5 text-muted">Status</div>
                            <div class="col-7"><span class="badge bg-${statusClass(item.status)} text-capitalize">${escapeHtml(item.status || '-')}</span></div>
                            <div class="col-5 text-muted">Progress</div>
                            <div class="col-7">${escapeHtml(item.progress ?? 0)}%</div>
                            <div class="col-5 text-muted">Catatan</div>
                            <div class="col-7">${escapeHtml(item.catatan || 'Belum ada catatan.')}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="border rounded p-3 mb-3">
                        <h6 class="fw-semibold mb-3">File Penugasan Mentor</h6>
                        ${buildFileLink(item.file_url, 'Unduh File Mentor')}
                    </div>
                    <div class="border rounded p-3">
                        <h6 class="fw-semibold mb-3">File Tugas Peserta</h6>
                        <div class="small text-muted mb-2">Diupload</div>
                        <div class="fw-semibold mb-3">${escapeHtml(item.submission_at || '-')}</div>
                        ${buildFileLink(item.submission_url, 'Unduh File Peserta')}
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `
            <div class="border rounded p-3">
                <strong>${item.judul}</strong>
                <small class="text-muted d-block">${item.peserta} - ${item.deadline}</small>
                <hr>
                <small class="text-muted">Tindakan penugasan</small>
                <h6 class="text-capitalize mb-0">${action}</h6>
            </div>
        `;
        document.getElementById('confirmNote').value = `Catatan ${action} untuk ${item.judul}.`;
        confirmModal.show();
    }

    async function submitAssignmentForm() {
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('peserta_id', assignmentRecipient.value);
        formData.append('judul', assignmentTitle.value.trim());
        formData.append('kategori', assignmentCategory.value);
        formData.append('deadline', assignmentDeadline.value);
        formData.append('catatan', assignmentNote.value.trim());
        if (assignmentFile.files[0]) {
            formData.append('file', assignmentFile.files[0]);
        }

        const isEdit = Boolean(assignmentId.value);
        const url = isEdit ? `${updateBaseUrl}/${assignmentId.value}` : createUrl;

        if (isEdit) {
            formData.append('_method', 'PATCH');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            body: formData,
        });

        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(payload.message || 'Gagal menyimpan penugasan.');
        }

        showToast(payload.message || 'Penugasan berhasil disimpan.');
        window.setTimeout(() => window.location.reload(), 650);
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
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter penugasan berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = 'semua';
        periodFilter.value = 'semua';
        categoryFilter.value = 'semua';
        progressFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter penugasan berhasil direset.', 'info');
    });
    [searchInput, statusFilter, periodFilter, categoryFilter, progressFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = assignments.find((assignment) => assignment.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail tugas') {
            openDetail(item);
            return;
        }
        if (action === 'edit tugas') {
            openAssignmentEditor(item);
            return;
        }
        openConfirm(item, action);
    });
    document.querySelectorAll('[data-panel-action]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!assignments.length) {
                showToast('Belum ada penugasan untuk diproses.', 'warning');
                return;
            }

            openConfirm(assignments[0], button.dataset.panelAction);
        });
    });

    document.getElementById('newAssignmentButton').addEventListener('click', () => {
        openAssignmentEditor();
        setTimeout(() => assignmentTitle?.focus(), 250);
    });

    if (assignmentForm) {
        populateAssignmentRecipients();
        assignmentForm.addEventListener('submit', (event) => {
            event.preventDefault();
            const title = assignmentTitle.value.trim();
            const recipient = assignmentRecipient.value;
            const deadline = assignmentDeadline.value;
            const isEdit = Boolean(assignmentId.value);

            if (!title || !recipient || !deadline) {
                showToast('Lengkapi judul tugas, penerima tugas, dan deadline.', 'danger');
                return;
            }

            if (!isEdit && !assignmentFile.files[0]) {
                showToast('Berkas tugas wajib diunggah saat membuat penugasan baru.', 'danger');
                return;
            }

            submitAssignmentForm().catch((error) => {
                showToast(error.message || 'Gagal menyimpan penugasan.', 'danger');
            });
        });
    }

    document.getElementById('exportTopButton').addEventListener('click', () => showToast('Data penugasan berhasil disiapkan untuk ekspor.', 'info'));
    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) return;
        const action = pendingAction.action;

        if (action !== 'hapus tugas') {
            confirmModal.hide();
            pendingAction = null;
            showToast(`Tindakan ${action} berhasil diproses.`, 'info');
            return;
        }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'DELETE');

        fetch(`${updateBaseUrl}/${pendingAction.item.id}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Accept: 'application/json',
            },
            body: formData,
        })
            .then((response) => response.json().then((payload) => ({ response, payload })))
            .then(({ response, payload }) => {
                if (!response.ok) throw new Error(payload.message || 'Gagal menghapus penugasan.');
                confirmModal.hide();
                pendingAction = null;
                showToast(payload.message || 'Penugasan berhasil dihapus.');
                window.setTimeout(() => window.location.reload(), 650);
            })
            .catch((error) => {
                showToast(error.message || 'Gagal menghapus penugasan.', 'danger');
            });
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi penugasan: 4 progress tugas baru diperbarui.', 'warning'), 700);
});
</script>
@endpush
