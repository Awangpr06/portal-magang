@extends('admin.layout.admin')

@section('title', 'Dokumen Peserta')

@push('styles')
<style>
    .document-page {
        --doc-brand: #0b5f86;
        --doc-brand-dark: #094967;
        --doc-line: #d8e8ef;
        --doc-soft: #f5fafc;
        --doc-ink: #163342;
        --doc-muted: #6c7f88;
    }

    .document-page .page-title {
        font-weight: 700;
        color: var(--doc-ink);
    }

    .document-page .breadcrumb a {
        color: var(--doc-brand);
        text-decoration: none;
    }

    .document-page .stat-card,
    .document-page .filter-card,
    .document-page .table-card {
        border: 0;
        border-radius: 10px;
    }

    .document-page .stat-card {
        transition: 0.2s ease;
    }

    .document-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.14);
    }

    .document-page .stat-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: #fff;
    }

    .document-page .document-check {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1rem;
    }

    .document-page .document-check.is-present {
        background: rgba(98, 189, 66, 0.13);
        color: #2f8a21;
    }

    .document-page .document-check.is-empty {
        color: #a1b1ba;
        background: rgba(161, 177, 186, 0.12);
    }

    .document-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
        vertical-align: middle;
    }

    .document-page .table tbody tr {
        transition: 0.15s ease;
    }

    .document-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .document-page .doc-name {
        font-weight: 600;
        color: var(--doc-ink);
    }

    .document-page .doc-meta {
        font-size: 12px;
        color: var(--doc-muted);
    }

    .document-page .empty-state {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: var(--doc-muted);
    }

    .document-page .pagination .page-link {
        color: var(--doc-brand);
    }

    .document-page .pagination .active .page-link {
        background: var(--doc-brand);
        border-color: var(--doc-brand);
        color: #fff;
    }

    .document-page .doc-summary {
        border: 1px solid var(--doc-line);
        border-radius: 10px;
        background: var(--doc-soft);
    }

    .document-page .doc-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.6rem;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid document-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dokumen Peserta</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Dokumen Peserta</h2>
            <p class="text-muted mb-0">
                Pantau seluruh dokumen peserta per jenis file. Centang otomatis muncul jika dokumen sudah diunggah ke database.
            </p>
        </div>
        <button class="btn btn-outline-primary" type="button" id="exportButton">
            <i class="bi bi-download me-1"></i>
            Ekspor Ringkasan
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Peserta</p>
                        <h3 class="mb-0" id="statTotalParticipants">0</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Dokumen Lengkap</p>
                        <h3 class="mb-0" id="statComplete">0</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Perlu Lengkapi</p>
                        <h3 class="mb-0" id="statPartial">0</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-folder2-open"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">File Terunggah</p>
                        <h3 class="mb-0" id="statUploaded">0</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-cloud-check-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="searchInput" class="form-label">Pencarian Peserta</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" id="searchInput" placeholder="Cari nama, NIM, prodi, instansi, atau nama file">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="completionFilter" class="form-label">Status Kelengkapan</label>
                    <select class="form-select" id="completionFilter">
                        <option value="semua">Semua Status</option>
                        <option value="Lengkap">Lengkap</option>
                        <option value="Sebagian">Sebagian</option>
                        <option value="Belum Upload">Belum Upload</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Catatan</label>
                    <div class="doc-summary px-3 py-2">
                        <div class="small text-muted mb-1">Indikator centang mengikuti data `documents` pada database.</div>
                        <div class="small fw-semibold text-success">Download tersedia sesuai jenis dokumen yang sudah terunggah.</div>
                    </div>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100" type="button" id="resetFilter" title="Reset">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Tabel Dokumen Peserta</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>

            <div class="table-responsive" id="tableWrapper">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Proposal</th>
                            <th>KTM</th>
                            <th>Transkip</th>
                            <th>CV</th>
                            <th>Surat Pengantar</th>
                            <th>Sertifikat pendukung</th>
                            <th width="210">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="documentTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data dokumen peserta tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination dokumen peserta">
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
                <h5 class="modal-title">Detail Dokumen Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="documentToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const documentTypes = @json($adminDocumentTypes ?? []);
    const participants = @json($adminDocumentParticipants ?? []);
    const perPage = 10;
    let currentPage = 1;

    const searchInput = document.getElementById('searchInput');
    const completionFilter = document.getElementById('completionFilter');
    const tableBody = document.getElementById('documentTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const toast = new bootstrap.Toast(document.getElementById('documentToast'), { delay: 3000 });

    function titleCase(value) {
        return String(value || '')
            .split(' ')
            .filter(Boolean)
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    function statusBadgeClass(value) {
        return {
            disetujui: 'bg-success',
            revisi: 'bg-danger',
            ditolak: 'bg-danger',
            menunggu: 'bg-warning text-dark',
            'belum diunggah': 'bg-secondary',
        }[value] || 'bg-secondary';
    }

    function completionBadgeClass(value) {
        return {
            'Lengkap': 'bg-success',
            'Sebagian': 'bg-warning text-dark',
            'Belum Upload': 'bg-secondary',
        }[value] || 'bg-secondary';
    }

    function normalizeText(value) {
        return String(value || '').toLowerCase();
    }

    function filteredParticipants() {
        const keyword = normalizeText(searchInput.value.trim());
        const completion = completionFilter.value;

        return participants.filter((participant) => {
            const documentBlob = documentTypes.map((type) => {
                const doc = participant.documents?.[type.key];
                return doc
                    ? [doc.label, doc.nama_dokumen, doc.file, doc.status, doc.tanggal].join(' ')
                    : '';
            }).join(' ');

            const matchKeyword = !keyword || [
                participant.nama,
                participant.nim,
                participant.prodi,
                participant.instansi,
                participant.periode,
                participant.completion,
                documentBlob,
            ].join(' ').toLowerCase().includes(keyword);

            const matchCompletion = completion === 'semua' || participant.completion === completion;

            return matchKeyword && matchCompletion;
        });
    }

    function updateStats() {
        const complete = participants.filter((item) => item.completion === 'Lengkap').length;
        const partial = participants.filter((item) => item.completion === 'Sebagian').length;
        const empty = participants.filter((item) => item.completion === 'Belum Upload').length;
        const uploaded = participants.reduce((carry, item) => carry + Number(item.uploaded_count || 0), 0);

        document.getElementById('statTotalParticipants').textContent = participants.length;
        document.getElementById('statComplete').textContent = complete;
        document.getElementById('statPartial').textContent = partial + empty;
        document.getElementById('statUploaded').textContent = uploaded;
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
        const data = filteredParticipants();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((participant, index) => {
            const documentCells = documentTypes.map((type) => {
                const doc = participant.documents?.[type.key];

                if (!doc || !doc.uploaded) {
                    return `
                        <td class="text-center">
                            <span class="document-check is-empty" title="Belum diunggah">-</span>
                        </td>
                    `;
                }

                return `
                    <td class="text-center">
                        <span class="document-check is-present" title="${titleCase(doc.status)}">
                            <i class="bi bi-check2"></i>
                        </span>
                    </td>
                `;
            }).join('');

            const downloadButtons = documentTypes.map((type) => {
                const doc = participant.documents?.[type.key];

                if (!doc || !doc.download_url) {
                    return `
                        <li>
                            <span class="dropdown-item text-muted disabled">${type.label}</span>
                        </li>
                    `;
                }

                return `
                    <li>
                        <a class="dropdown-item" href="${doc.download_url}">
                            ${type.label}
                        </a>
                    </li>
                `;
            }).join('');

            return `
                <tr>
                    <td>${start + index + 1}</td>
                    <td>
                        <div class="doc-name">${participant.nama}</div>
                        <div class="doc-meta">${participant.nim} - ${participant.prodi}</div>
                        <div class="doc-meta">${participant.instansi}</div>
                    </td>
                    ${documentCells}
                    <td>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-info btn-sm text-white" type="button" data-action="view" data-id="${participant.id}">
                                Lihat
                            </button>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Download
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    ${downloadButtons}
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} peserta ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} peserta`
            : 'Menampilkan 0 peserta';

        renderPagination(totalPages);
    }

    function openDetail(id) {
        const participant = participants.find((item) => item.id === id);
        if (!participant) {
            return;
        }

        const documentRows = documentTypes.map((type) => {
            const doc = participant.documents?.[type.key];
            const status = doc?.uploaded ? doc.status : 'belum diunggah';

            return `
                <tr>
                    <td class="fw-semibold">${type.label}</td>
                    <td>${doc?.nama_dokumen ?? '-'}</td>
                    <td><span class="badge ${statusBadgeClass(status)}">${titleCase(status)}</span></td>
                    <td>${doc?.tanggal ?? '-'}</td>
                    <td>
                        ${doc?.download_url
                            ? `<a class="btn btn-outline-primary btn-sm" href="${doc.download_url}">Download</a>`
                            : '<button class="btn btn-outline-secondary btn-sm" type="button" disabled>Belum Ada</button>'}
                    </td>
                </tr>
            `;
        }).join('');

        const completionClass = completionBadgeClass(participant.completion);

        document.getElementById('detailContent').innerHTML = `
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Nama Peserta</div>
                        <div class="fw-semibold">${participant.nama}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">NIM / Prodi</div>
                        <div class="fw-semibold">${participant.nim}</div>
                        <div class="small text-muted">${participant.prodi}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">Status Kelengkapan</div>
                        <span class="badge ${completionClass} mt-1">${participant.completion}</span>
                        <div class="small text-muted mt-2">${participant.uploaded_count} dari ${participant.required_count} dokumen terunggah</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Jenis Dokumen</th>
                            <th>Nama File</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${documentRows}
                    </tbody>
                </table>
            </div>
        `;

        detailModal.show();
    }

    function showToast(message) {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            currentPage = 1;
            renderTable();
        }
    });

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        completionFilter.value = 'semua';
        currentPage = 1;
        renderTable();
        showToast('Filter dokumen berhasil direset.');
    });

    document.getElementById('exportButton').addEventListener('click', () => {
        showToast('Ringkasan dokumen peserta berhasil disiapkan untuk ekspor.');
    });

    document.getElementById('searchInput').addEventListener('input', () => {
        currentPage = 1;
        renderTable();
    });

    completionFilter.addEventListener('change', () => {
        currentPage = 1;
        renderTable();
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action="view"]');
        if (!button) {
            return;
        }

        openDetail(Number(button.dataset.id));
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
