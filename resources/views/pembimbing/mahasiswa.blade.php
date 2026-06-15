@extends('pembimbing.layout.pembimbing')

@section('title', 'Mahasiswa Bimbingan')
@section('page-title', 'Mahasiswa Bimbingan')

@push('styles')
<style>
    .student-page .student-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .student-page .stat-card,
    .student-page .filter-card,
    .student-page .table-card { border:0; border-radius:8px; }
    .student-page .stat-card { cursor:pointer; transition:.2s ease; }
    .student-page .stat-card:hover,
    .student-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .student-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .student-page .progress { height:10px; border-radius:999px; }
    .student-page .table { font-size:14px; }
    .student-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .student-page .table tbody tr:hover { background:#f7fcfe; }
    .student-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:220px; }
    .student-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .student-page .pagination .page-link { color:#2a8fbd; }
    .student-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid student-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Mahasiswa Bimbingan</li>
        </ol>
    </nav>

    <section class="student-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Daftar Mahasiswa Bimbingan</h3>
                <p class="mb-0">Pusat pemantauan mahasiswa magang yang menjadi tanggung jawab dosen pembimbing, ditampilkan langsung dari data database yang terhubung ke akun pembimbing aktif.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-success" type="button" id="refreshNotification">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui Data
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-info-circle-fill"></i>
        <div>3 aktivitas mahasiswa baru membutuhkan tindak lanjut pembimbing akademik.</div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>
                <div class="fw-semibold">Pesan gagal dikirim.</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Mahasiswa</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Mahasiswa Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="belum aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Aktif</p><h3 class="mb-0" id="statInactive">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-person-dash"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="average">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Progress Rata-rata</p><h3 class="mb-0" id="statProgress">0%</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-graph-up-arrow"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-report-card="review">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Laporan Review</p><h3 class="mb-0" id="statReports">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-file-earmark-text"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-score-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Penilaian Selesai</p><h3 class="mb-0" id="statScores">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-award"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="searchInput" class="form-label">Nama/NIM Mahasiswa</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" id="searchInput" placeholder="Cari nama atau NIM">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="studyFilter" class="form-label">Program Studi</label>
                    <select class="form-select" id="studyFilter">
                        <option value="semua">Semua Prodi</option>
                        <option value="Informatika">Informatika</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                        <option value="Akuntansi">Akuntansi</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="statusFilter" class="form-label">Status Magang</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="belum aktif">Belum Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="perlu tindak lanjut">Perlu Tindak Lanjut</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="progressFilter" class="form-label">Progress</label>
                    <select class="form-select" id="progressFilter">
                        <option value="semua">Semua Progress</option>
                        <option value="rendah">&lt; 50%</option>
                        <option value="sedang">50% - 79%</option>
                        <option value="tinggi">&gt;= 80%</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="periodFilter" class="form-label">Periode Magang</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
                    </select>
                </div>
                <div class="col-lg-1 col-md-6 d-flex gap-2">
                    <button class="btn btn-primary w-50" type="button" id="applyFilter" title="Terapkan Filter"><i class="bi bi-funnel"></i></button>
                    <button class="btn btn-outline-secondary w-50" type="button" id="resetFilter" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Mahasiswa Bimbingan</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <div class="d-flex align-items-center gap-2">
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
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Lokasi Magang</th>
                            <th>Periode Magang</th>
                            <th>Status Magang</th>
                            <th>Progress</th>
                            <th width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="studentTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data mahasiswa bimbingan tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination mahasiswa bimbingan">
                    <ul class="pagination mb-0" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTitle">Detail Mahasiswa</h5>
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
                <h5 class="modal-title" id="confirmTitle">Kirim Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="messageForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Penerima</label>
                        <input type="text" class="form-control" id="messageRecipient" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="messageSubject">Subjek</label>
                        <input type="text" class="form-control" id="messageSubject" name="subject" maxlength="150" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="messageBody">Pesan</label>
                        <textarea class="form-control" id="messageBody" name="message" rows="5" maxlength="5000" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="messageAttachment">Lampiran URL</label>
                        <input type="text" class="form-control" id="messageAttachment" name="attachment" maxlength="255" placeholder="Opsional">
                        <small class="text-muted">Isi dengan nama file atau URL lampiran jika diperlukan.</small>
                    </div>
                    <div class="small text-muted" id="confirmSummary"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="confirmAction">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
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
    const students = @json($studentsData ?? []);
    const messageStoreUrlTemplate = @json(route('pembimbing.mahasiswa.pesan.store', ['internship' => '__INTERNSHIP__']));

    let currentPage = 1;
    let pendingAction = null;

    const searchInput = document.getElementById('searchInput');
    const studyFilter = document.getElementById('studyFilter');
    const statusFilter = document.getElementById('statusFilter');
    const progressFilter = document.getElementById('progressFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const studentTable = document.getElementById('studentTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 3000 });
    const messageForm = document.getElementById('messageForm');

    function titleCase(value) {
        return value.split(' ').map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            aktif: 'bg-success',
            'belum aktif': 'bg-secondary',
            selesai: 'bg-primary',
            'perlu tindak lanjut': 'bg-warning text-dark'
        }[value] || 'bg-secondary';
    }

    function progressClass(value) {
        if (value >= 80) return 'bg-success';
        if (value >= 50) return 'bg-info';
        return 'bg-warning';
    }

    function progressMatch(value, filter) {
        if (filter === 'rendah') return value < 50;
        if (filter === 'sedang') return value >= 50 && value < 80;
        if (filter === 'tinggi') return value >= 80;
        return true;
    }

    function filteredStudents() {
        const keyword = searchInput.value.trim().toLowerCase();
        const study = studyFilter.value;
        const status = statusFilter.value;
        const progress = progressFilter.value;
        const period = periodFilter.value;

        return students.filter((student) => {
            const matchKeyword = !keyword || [student.nama, student.nim, student.prodi, student.lokasi, student.status].join(' ').toLowerCase().includes(keyword);
            const matchStudy = study === 'semua' || student.prodi === study;
            const matchStatus = status === 'semua' || student.status === status;
            const matchProgress = progressMatch(student.progress, progress);
            const matchPeriod = period === 'semua' || student.periode === period;
            return matchKeyword && matchStudy && matchStatus && matchProgress && matchPeriod;
        });
    }

    function updateStats() {
        const totalProgress = students.reduce((sum, student) => sum + student.progress, 0);
        document.getElementById('statTotal').textContent = students.length;
        document.getElementById('statActive').textContent = students.filter((student) => student.status === 'aktif').length;
        document.getElementById('statInactive').textContent = students.filter((student) => student.status === 'belum aktif').length;
        document.getElementById('statProgress').textContent = `${students.length ? Math.round(totalProgress / students.length) : 0}%`;
        document.getElementById('statReports').textContent = students.filter((student) => student.laporan === 'review').length;
        document.getElementById('statScores').textContent = students.filter((student) => student.nilai === 'selesai').length;
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
        const data = filteredStudents();
        const perPage = Number(perPageSelect.value);
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        studentTable.innerHTML = pageData.map((student, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td class="fw-semibold">${student.nama}</td>
                <td>${student.nim}</td>
                <td>${student.prodi}</td>
                <td>${student.lokasi}</td>
                <td>${student.periode}</td>
                <td><span class="badge ${statusClass(student.status)}">${titleCase(student.status)}</span></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="min-width:90px">
                            <div class="progress-bar ${progressClass(student.progress)}" style="width:${student.progress}%"></div>
                        </div>
                        <span class="small fw-semibold">${student.progress}%</span>
                    </div>
                </td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm text-white" data-action="detail" data-id="${student.id}" type="button">Lihat Detail</button>
                        <button class="btn btn-outline-secondary btn-sm" data-action="pesan" data-id="${student.id}" type="button">Kirim Pesan</button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        document.getElementById('tableSummary').textContent = `${data.length} mahasiswa ditemukan`;
        document.getElementById('pageInfo').textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} mahasiswa`
            : 'Menampilkan 0 mahasiswa';
        renderPagination(totalPages);
        updateStats();
    }

    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('systemToast');
        toastElement.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(student, mode = 'detail') {
        const titles = {
            detail: 'Detail Mahasiswa',
            pesan: 'Kirim Pesan'
        };
        document.getElementById('detailTitle').textContent = titles[mode];
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-6"><strong>Nama Mahasiswa</strong><div>${student.nama}</div></div>
                <div class="col-md-6"><strong>NIM</strong><div>${student.nim}</div></div>
                <div class="col-md-6"><strong>Program Studi</strong><div>${student.prodi}</div></div>
                <div class="col-md-6"><strong>Lokasi Magang</strong><div>${student.lokasi}</div></div>
                <div class="col-md-6"><strong>Periode Magang</strong><div>${student.periode}</div></div>
                <div class="col-md-6"><strong>Status Magang</strong><div>${titleCase(student.status)}</div></div>
                <div class="col-md-6"><strong>Progress</strong><div>${student.progress}%</div></div>
                <div class="col-md-6"><strong>Absensi</strong><div>${student.absensi}</div></div>
                <div class="col-md-6"><strong>Logbook</strong><div>${student.logbook}</div></div>
                <div class="col-md-6"><strong>Status Laporan</strong><div>${titleCase(student.laporan)}</div></div>
                <div class="col-12"><strong>Catatan Pembimbing</strong><div>${student.catatan}</div></div>
            </div>
        `;
        detailModal.show();
    }

    function openMessageModal(student) {
        pendingAction = { student, action: 'pesan' };
        document.getElementById('confirmTitle').textContent = 'Kirim Pesan';
        document.getElementById('messageRecipient').value = `${student.nama} (${student.nim})`;
        document.getElementById('messageSubject').value = `Bimbingan - ${student.nama}`;
        document.getElementById('messageBody').value = '';
        document.getElementById('messageAttachment').value = '';
        document.getElementById('confirmSummary').textContent = `Pesan ini akan tersimpan di percakapan ${student.nama}.`;
        messageForm.action = messageStoreUrlTemplate.replace('__INTERNSHIP__', student.id);
        confirmModal.show();
    }

    document.querySelectorAll('[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('[data-status-card], [data-report-card], [data-score-card], [data-progress-card]').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            statusFilter.value = card.dataset.statusCard;
            currentPage = 1;
            renderTable();
        });
    });

    document.querySelector('[data-report-card]').addEventListener('click', () => {
        searchInput.value = 'review';
        currentPage = 1;
        renderTable();
    });

    document.querySelector('[data-score-card]').addEventListener('click', () => {
        searchInput.value = 'selesai';
        currentPage = 1;
        renderTable();
    });

    document.querySelector('[data-progress-card]').addEventListener('click', () => {
        progressFilter.value = 'sedang';
        currentPage = 1;
        renderTable();
    });

    [studyFilter, statusFilter, progressFilter, periodFilter].forEach((input) => {
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
        showToast('Filter mahasiswa bimbingan berhasil diterapkan.', 'info');
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        studyFilter.value = 'semua';
        statusFilter.value = 'semua';
        progressFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card], [data-report-card], [data-score-card], [data-progress-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
        showToast('Filter mahasiswa bimbingan berhasil direset.', 'info');
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

    studentTable.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const student = students.find((item) => item.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            showDetail(student, action);
            return;
        }
        if (action === 'pesan') {
            openMessageModal(student);
        }
    });

    document.getElementById('refreshNotification').addEventListener('click', () => {
        showToast('Data dan notifikasi mahasiswa bimbingan berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi baru: 3 aktivitas mahasiswa perlu ditinjau.', 'warning'), 800);
});
</script>
@endpush
