@extends('mentor.layout.mentor')

@section('title', 'Daftar Peserta Magang')
@section('page-title', 'Daftar Peserta Magang')

@push('styles')
<style>
    .participant-page .participant-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .participant-page .stat-card,
    .participant-page .filter-card,
    .participant-page .table-card,
    .participant-page .action-card { border:0; border-radius:8px; }
    .participant-page .stat-card { cursor:pointer; transition:.2s ease; }
    .participant-page .stat-card:hover,
    .participant-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .participant-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .participant-page .participant-photo { width:44px; height:44px; border-radius:50%; object-fit:cover; }
    .participant-page .progress { height:8px; background:#e8eef2; }
    .participant-page .progress-bar { background:#2a8fbd; }
    .participant-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:240px; }
    .participant-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .participant-page .pagination .page-link { color:#2a8fbd; }
    .participant-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid participant-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daftar Peserta Magang</li>
        </ol>
    </nav>

    <section class="participant-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Pusat Data Peserta Magang</h3>
                <p class="mb-0">Kelola identitas peserta, perkembangan kegiatan magang, status kehadiran, histori aktivitas, dan akses cepat menuju monitoring serta evaluasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="systemBadge">Real-time</span>
                <button class="btn btn-dark" type="button" id="refreshButton">
                    <i class="bi bi-arrow-clockwise"></i> Perbarui
                </button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            {{ $mentorSummary['total'] ?? 0 }} peserta diambil langsung dari database penempatan mentor.
            Kolom penempatan sekarang menampilkan data dari record database.
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Peserta</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peserta Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-person-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="review">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu Tindak Lanjut</p><h3 class="mb-0" id="statReview">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-status-card="selesai">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Selesai</p><h3 class="mb-0" id="statDone">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-progress-card="tinggi">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Progress Rata-rata</p><h3 class="mb-0" id="statProgress">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-graph-up"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card" data-attendance-card="rendah">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kehadiran</p><h3 class="mb-0" id="statAttendance">0%</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-calendar-check"></i></span>
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
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, kampus, penempatan">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Peserta</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="review">Menunggu Tindak Lanjut</option>
                        <option value="selesai">Selesai</option>
                        <option value="perlu perhatian">Perlu Perhatian</option>
                    </select>
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
                    <label class="form-label" for="periodFilter">Periode Magang</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
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

    <div class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-1">Aksi Cepat Peserta</h5>
                <p class="text-muted mb-0">Pilih data peserta pada tabel, lalu jalankan tindakan lanjutan.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-warning" type="button" data-bulk-action="monitoring"><i class="bi bi-activity"></i> Monitoring</button>
                <button class="btn btn-outline-warning" type="button" data-bulk-action="pesan"><i class="bi bi-send"></i> Kirim Pesan</button>
                <button class="btn btn-outline-warning" type="button" data-bulk-action="nilai"><i class="bi bi-award"></i> Input Nilai</button>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Tabel Data Peserta Magang</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data peserta</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small" for="perPageSelect">Data</label>
                <select class="form-select form-select-sm" id="perPageSelect" style="width:80px">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
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
                            <th>Foto</th>
                            <th>Nama Peserta</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Perguruan Tinggi</th>
                            <th>Periode Magang</th>
                            <th>Penempatan</th>
                            <th>PA</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="participantTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Tidak ada peserta sesuai filter.</p>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination peserta">
                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Peserta Magang</h5>
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

<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">Berikan Catatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="text-muted small" id="noteTargetMeta">Pilih peserta untuk mengisi catatan.</div>
                    <strong id="noteTargetName">-</strong>
                </div>
                <label class="form-label" for="mentorNoteInput">Catatan</label>
                <textarea class="form-control" id="mentorNoteInput" rows="5" placeholder="Tuliskan catatan untuk peserta"></textarea>
                <div class="form-text">Catatan ini akan muncul di riwayat laporan peserta sebagai catatan mentor dan status laporan akan menjadi perlu revisi.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="cancelNoteButton">Batal</button>
                <button type="button" class="btn btn-warning" id="saveNoteButton"><i class="bi bi-save"></i> Simpan Catatan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Data peserta diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const participants = @json($pesertaBimbingan ?? []);
    const messageUrlTemplate = @json(route('mentor.komunikasi.pesan.peserta', ['peserta' => '__PESERTA__']));

    const tableBody = document.getElementById('participantTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const studyFilter = document.getElementById('studyFilter');
    const periodFilter = document.getElementById('periodFilter');
    const progressFilter = document.getElementById('progressFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'), { delay: 2600 });
    let currentPage = 1;
    let activeStatus = 'semua';
    let pendingNoteTarget = null;
    const mentorNoteStorageKey = 'portal_magang_mentor_notes';

    const statusClass = (status) => ({
        aktif:'success',
        review:'warning text-dark',
        selesai:'info text-dark',
        'perlu perhatian':'danger'
    }[status] || 'secondary');

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

    function loadMentorNotes() {
        try {
            return JSON.parse(localStorage.getItem(mentorNoteStorageKey) || '[]');
        } catch (error) {
            return [];
        }
    }

    function saveMentorNotes(notes) {
        localStorage.setItem(mentorNoteStorageKey, JSON.stringify(notes));
    }

    function avatarUrl(name, photo = null) {
        return photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=2a8fbd&color=ffffff`;
    }

    function filteredData() {
        const keyword = searchInput.value.trim().toLowerCase();
        return participants.filter((item) => {
            const keywordMatch = !keyword || `${item.nama} ${item.nim} ${item.prodi} ${item.kampus} ${item.penempatan ?? item.divisi ?? ''} ${item.pa} ${item.aktivitas}`.toLowerCase().includes(keyword);
            const statusMatch = statusFilter.value === 'semua' || item.status === statusFilter.value;
            const studyMatch = studyFilter.value === 'semua' || item.prodi === studyFilter.value;
            const periodMatch = periodFilter.value === 'semua' || item.periode === periodFilter.value;
            const progressMatch = progressFilter.value === 'semua' || progressGroup(item.progress) === progressFilter.value;
            const cardMatch = activeStatus === 'semua' || item.status === activeStatus;
            return keywordMatch && statusMatch && studyMatch && periodMatch && progressMatch && cardMatch;
        });
    }

    function renderStats() {
        const avgProgress = participants.length ? Math.round(participants.reduce((total, item) => total + Number(item.progress || 0), 0) / participants.length) : 0;
        const avgAttendance = participants.length ? Math.round(participants.reduce((total, item) => total + Number(item.hadir || 0), 0) / participants.length) : 0;
        document.getElementById('statTotal').textContent = participants.length;
        document.getElementById('statActive').textContent = participants.filter((item) => item.status === 'aktif').length;
        document.getElementById('statReview').textContent = participants.filter((item) => item.status === 'review').length;
        document.getElementById('statDone').textContent = participants.filter((item) => item.status === 'selesai').length;
        document.getElementById('statProgress').textContent = `${avgProgress}%`;
        document.getElementById('statAttendance').textContent = `${avgAttendance}%`;
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
                <td><img class="participant-photo" src="${avatarUrl(item.nama, item.foto)}" alt="Foto ${item.nama}"></td>
                <td><strong>${item.nama}</strong></td>
                <td>${item.nim}</td>
                <td>${item.prodi}</td>
                <td>${item.kampus}</td>
                <td>${item.periode}</td>
                <td>${item.penempatan ?? item.divisi ?? '-'}</td>
                <td>${item.pa}</td>
                <td><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></td>
                <td>
                    <div class="d-flex justify-content-between small"><span>${item.progress}%</span><span>${item.hadir}% hadir</span></div>
                    <div class="progress"><div class="progress-bar ${item.progress < 50 ? 'bg-danger' : ''}" style="width:${item.progress}%"></div></div>
                </td>
                <td>${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-warning btn-sm" type="button" data-action="detail" data-id="${item.id}">Lihat Detail</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="note" data-id="${item.id}">Beri Catatan</button>
                        <button class="btn btn-outline-primary btn-sm" type="button" data-action="pesan" data-id="${item.id}">Kirim Pesan</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `${data.length} peserta ditemukan`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
        renderStats();
    }

    function openDetail(item, mode = 'detail') {
        document.getElementById('detailModalLabel').textContent = 'Detail Peserta Magang';
        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3">
                <div class="col-md-4 text-center">
                    <img class="rounded-circle mb-3" src="${avatarUrl(item.nama, item.foto)}" width="96" height="96" alt="Foto ${item.nama}">
                    <h5>${item.nama}</h5>
                    <div class="text-muted">${item.nim}</div>
                    <div class="mt-2"><span class="badge bg-${statusClass(item.status)} text-capitalize">${item.status}</span></div>
                </div>
                <div class="col-md-8">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${item.prodi}</dd>
                        <dt class="col-sm-4">Perguruan Tinggi</dt><dd class="col-sm-8">${item.kampus}</dd>
                        <dt class="col-sm-4">Instansi</dt><dd class="col-sm-8">${item.instansi ?? '-'}</dd>
                        <dt class="col-sm-4">Periode</dt><dd class="col-sm-8">${item.periode}</dd>
                        <dt class="col-sm-4">Penempatan</dt><dd class="col-sm-8">${item.penempatan ?? item.divisi ?? '-'}</dd>
                        <dt class="col-sm-4">PA</dt><dd class="col-sm-8">${item.pa}</dd>
                        <dt class="col-sm-4">Progress</dt><dd class="col-sm-8">${item.progress}%</dd>
                        <dt class="col-sm-4">Kehadiran</dt><dd class="col-sm-8">${item.hadir}%</dd>
                        <dt class="col-sm-4">Dokumen</dt><dd class="col-sm-8">${item.dokumen} dokumen</dd>
                        <dt class="col-sm-4">Aktivitas</dt><dd class="col-sm-8">${item.aktivitas}</dd>
                    </dl>
                </div>
                <div class="col-12">
                    <div class="border rounded p-3 bg-light">
                        <small class="text-muted">Histori Aktivitas</small>
                        <p class="mb-0">${item.histori}</p>
                    </div>
                </div>
            </div>
        `;
        detailModal.show();
    }

    function openNoteModal(item) {
        pendingNoteTarget = item;
        document.getElementById('noteTargetName').textContent = item.nama;
        document.getElementById('noteTargetMeta').textContent = `${item.nim} - ${item.prodi} - ${item.kampus}`;
        const existingNotes = loadMentorNotes();
        const existing = existingNotes.find((note) => note.participantId === item.id);
        document.getElementById('mentorNoteInput').value = existing?.note || '';
        noteModal.show();
    }

    function closeNoteCard() {
        pendingNoteTarget = null;
        document.getElementById('mentorNoteInput').value = '';
        document.getElementById('noteTargetName').textContent = '-';
        document.getElementById('noteTargetMeta').textContent = 'Pilih peserta untuk mengisi catatan.';
        noteModal.hide();
    }

    function storeMentorNote(item, note) {
        const notes = loadMentorNotes();
        const payload = {
            participantId: item.id,
            participantName: item.nama,
            note,
            updatedAt: new Date().toISOString(),
        };
        const index = notes.findIndex((entry) => entry.participantId === item.id);
        if (index >= 0) {
            notes[index] = payload;
        } else {
            notes.unshift(payload);
        }
        saveMentorNotes(notes);
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
    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter peserta berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        statusFilter.value = 'semua';
        studyFilter.value = 'semua';
        periodFilter.value = 'semua';
        progressFilter.value = 'semua';
        activeStatus = 'semua';
        document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
        document.querySelector('.stat-card[data-status-card="semua"]').classList.add('active');
        currentPage = 1;
        renderTable();
        showToast('Filter peserta berhasil direset.', 'info');
    });
    [searchInput, statusFilter, studyFilter, periodFilter, progressFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = participants.find((participant) => participant.id === Number(button.dataset.id));
        const action = button.dataset.action;
        if (action === 'detail') {
            openDetail(item, action);
            return;
        }
        if (action === 'note') {
            openNoteModal(item);
            return;
        }
        if (action === 'pesan') {
            const pesertaId = item.peserta_id || item.user_id;
            if (!pesertaId) {
                showToast('Data peserta tidak memiliki identitas pesan yang valid.', 'warning');
                return;
            }
            window.location.href = messageUrlTemplate.replace('__PESERTA__', pesertaId);
            return;
        }
        showToast(`${action} untuk ${item.nama} siap diproses.`, 'info');
    });
    document.querySelectorAll('[data-bulk-action]').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.dataset.bulkAction === 'pesan') {
                const firstItem = filteredData()[0] || participants[0];
                const pesertaId = firstItem?.peserta_id || firstItem?.user_id;
                if (!firstItem || !pesertaId) {
                    showToast('Tidak ada peserta yang bisa dibuka untuk pesan.', 'warning');
                    return;
                }
                window.location.href = messageUrlTemplate.replace('__PESERTA__', pesertaId);
                return;
            }

            showToast(`${button.textContent.trim()} siap diproses.`, 'info');
        });
    });
    document.getElementById('saveNoteButton').addEventListener('click', () => {
        if (!pendingNoteTarget) return;
        const note = document.getElementById('mentorNoteInput').value.trim();
        if (!note) {
            showToast('Catatan mentor tidak boleh kosong.', 'warning');
            return;
        }
        storeMentorNote(pendingNoteTarget, note);
        showToast(`Catatan untuk ${pendingNoteTarget.nama} berhasil disimpan dan siap tampil di riwayat laporan peserta.`);
        renderTable();
        closeNoteCard();
    });
    document.getElementById('cancelNoteButton').addEventListener('click', closeNoteCard);
    document.getElementById('noteModal').addEventListener('hidden.bs.modal', () => {
        pendingNoteTarget = null;
        document.getElementById('mentorNoteInput').value = '';
        document.getElementById('noteTargetName').textContent = '-';
        document.getElementById('noteTargetMeta').textContent = 'Pilih peserta untuk mengisi catatan.';
    });
    document.getElementById('refreshButton').addEventListener('click', () => {
        document.getElementById('systemBadge').textContent = 'Diperbarui';
        showToast('Data peserta berhasil diperbarui.', 'info');
    });

    renderTable();
    setTimeout(() => showToast('Notifikasi sistem: 3 aktivitas peserta baru tersedia.', 'warning'), 700);
});
</script>
@endpush
