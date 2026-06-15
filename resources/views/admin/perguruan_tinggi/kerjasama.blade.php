@extends('admin.layout.admin')

@section('title', 'Data Kerja Sama')

@push('styles')
<style>
    .cooperation-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .cooperation-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .cooperation-page .stat-card,
    .cooperation-page .filter-card,
    .cooperation-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .cooperation-page .stat-card {
        cursor: pointer;
        transition: 0.2s ease;
    }

    .cooperation-page .stat-card:hover,
    .cooperation-page .stat-card.active {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .cooperation-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .cooperation-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 13px;
        white-space: nowrap;
    }

    .cooperation-page .table th,
    .cooperation-page .table td {
        padding: 9px 10px;
        font-size: 13px;
        vertical-align: middle;
    }

    .cooperation-page .table tbody tr {
        transition: 0.15s ease;
    }

    .cooperation-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .cooperation-page .action-group {
        display: flex;
        flex-wrap: nowrap;
        gap: 4px;
        white-space: nowrap;
        overflow-x: auto;
        padding-bottom: 2px;
    }

    .cooperation-page .action-group .btn {
        flex: 0 0 auto;
        padding: 4px 7px;
        font-size: 12px;
        line-height: 1.2;
    }

    .cooperation-page .table .badge {
        font-size: 11px;
        padding: 5px 7px;
    }

    .cooperation-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .cooperation-page .pagination .page-link {
        color: #0b5f86;
    }

    .cooperation-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid cooperation-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.perguruan-tinggi.index') }}">Manajemen Perguruan Tinggi</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Data Kerja Sama</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Data Kerja Sama</h2>
            <p class="text-muted mb-0">
                Kelola kontrak, dokumen, status, dan masa berlaku kerja sama perguruan tinggi.
            </p>
        </div>
        <button class="btn btn-primary" type="button" id="addCooperationButton">
            <i class="bi bi-plus-lg"></i>
            Tambah Kerja Sama
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4" role="alert">
            <div class="fw-semibold mb-1">Terdapat kesalahan saat memproses validasi.</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Dokumen Kerja Sama Peserta</h5>
                    <p class="text-muted mb-0">Dokumen yang diunggah peserta menunggu validasi admin sebelum dianggap aktif.</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-warning text-dark">Menunggu: {{ $cooperationStats['menunggu'] ?? 0 }}</span>
                    <span class="badge bg-success">Disetujui: {{ $cooperationStats['disetujui'] ?? 0 }}</span>
                    <span class="badge bg-danger">Ditolak: {{ $cooperationStats['ditolak'] ?? 0 }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peserta</th>
                            <th>Kampus</th>
                            <th>Jenis</th>
                            <th>Nama Dokumen</th>
                            <th>Status</th>
                            <th>Upload</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cooperationUploads ?? [] as $document)
                            @php
                                $statusClass = match($document['status']) {
                                    'disetujui' => 'success',
                                    'ditolak' => 'danger',
                                    'revisi' => 'warning text-dark',
                                    default => 'warning text-dark',
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $document['pemilik'] }}</strong>
                                    <div class="small text-muted">{{ $document['nim'] }}</div>
                                </td>
                                <td>{{ $document['kampus'] }}</td>
                                <td>{{ $document['jenis'] }}</td>
                                <td>
                                    <strong>{{ $document['nama'] }}</strong>
                                    <div class="small text-muted">{{ $document['size'] }}</div>
                                </td>
                                <td><span class="badge bg-{{ $statusClass }}">{{ ucfirst($document['status']) }}</span></td>
                                <td>{{ $document['uploaded_at'] }}</td>
                                <td>{{ $document['catatan'] }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.magang.dokumen.download', [$document['user_id'], $document['jenis_key']]) }}">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.perguruan-tinggi.kerjasama.review', $document['id']) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="disetujui">
                                            <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.perguruan-tinggi.kerjasama.review', $document['id']) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="ditolak">
                                            <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    Belum ada dokumen kerja sama dari peserta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card active" role="button" tabindex="0" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kerja Sama</p>
                        <h3 class="mb-0" id="statTotal">{{ $cooperationTableStats['total'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-journal-text"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Kerja Sama Aktif</p>
                        <h3 class="mb-0" id="statAktif">{{ $cooperationTableStats['aktif'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-patch-check-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="berakhir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Kerja Sama Berakhir</p>
                        <h3 class="mb-0" id="statBerakhir">{{ $cooperationTableStats['berakhir'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-calendar-x-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card" role="button" tabindex="0" data-status-card="menunggu">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Menunggu Verifikasi</p>
                        <h3 class="mb-0" id="statMenunggu">{{ $cooperationTableStats['menunggu'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form id="cooperationFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="searchInput" class="form-label">Judul Kerja Sama</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="searchInput" placeholder="Cari Judul Kerja Sama">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label for="typeFilter" class="form-label">Jenis Kerja Sama</label>
                        <select class="form-select" id="typeFilter">
                            <option value="semua">Semua Jenis</option>
                            @foreach(($cooperationTypes ?? []) as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="semua">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="berakhir">Berakhir</option>
                            <option value="menunggu">Menunggu Verifikasi</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="collegeFilter" class="form-label">Perguruan Tinggi</label>
                        <select class="form-select" id="collegeFilter">
                            <option value="semua">Semua PT</option>
                            @foreach(($cooperationCampuses ?? []) as $campus)
                                <option value="{{ $campus }}">{{ $campus }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="periodFilter" class="form-label">Masa Berlaku</label>
                        <select class="form-select" id="periodFilter">
                            <option value="semua">Semua Masa</option>
                            @foreach(($cooperationPeriods ?? []) as $period)
                                <option value="{{ $period }}">{{ \Illuminate\Support\Str::title($period) }}</option>
                            @endforeach
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
                    <h5 class="mb-1">Tabel Data Kerja Sama</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted" for="perPageSelect">Tampil</label>
                    <select class="form-select form-select-sm" id="perPageSelect" style="width:80px">
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
                            <th>Perguruan Tinggi</th>
                            <th>Jenis Kerja Sama</th>
                            <th>Nomor Dokumen</th>
                            <th role="button" data-sort="judul">Judul Kerja Sama <i class="bi bi-arrow-down-up"></i></th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Berakhir</th>
                            <th width="260">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cooperationTable"></tbody>
                </table>
            </div>

            <div class="empty-state d-none" id="emptyState">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <h6 class="mb-1">Data kerja sama tidak ditemukan</h6>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter.</p>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 0 data</span>
                <nav aria-label="Pagination kerja sama">
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
                <h5 class="modal-title">Detail Kerja Sama</h5>
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
                <h5 class="modal-title" id="formTitle">Tambah Kerja Sama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="formTitleInput">Judul Kerja Sama</label>
                        <input class="form-control" id="formTitleInput" placeholder="Masukkan judul kerja sama">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formCollege">Mitra / Perguruan Tinggi</label>
                        <input class="form-control" id="formCollege" placeholder="Masukkan mitra atau perguruan tinggi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formType">Jenis Dokumen</label>
                        <select class="form-select" id="formType">
                            <option value="mou">MoU</option>
                            <option value="pks">PKS</option>
                            <option value="addendum">Addendum</option>
                            <option value="surat_kerja_sama">Surat Kerja Sama</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formDocument">Nomor Dokumen</label>
                        <input class="form-control" id="formDocument" placeholder="Nomor dokumen">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="formFile">Upload Dokumen</label>
                        <input class="form-control" id="formFile" type="file">
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
                <h5 class="modal-title" id="confirmTitle">Konfirmasi Aksi</h5>
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
    <div id="cooperationToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const cooperations = @json($cooperationRows ?? [], JSON_UNESCAPED_UNICODE);
    const csrfToken = @json(csrf_token());
    const storeUrl = @json(route('admin.perguruan-tinggi.kerjasama.store'));
    const updateUrlTemplate = @json(route('admin.perguruan-tinggi.kerjasama.update', ['document' => '__DOCUMENT__']));
    const deleteUrlTemplate = @json(route('admin.perguruan-tinggi.kerjasama.destroy', ['document' => '__DOCUMENT__']));
    const reviewUrlTemplate = @json(route('admin.perguruan-tinggi.kerjasama.review', ['document' => '__DOCUMENT__']));
    const downloadUrlTemplate = @json(route('admin.perguruan-tinggi.kerjasama.download', ['document' => '__DOCUMENT__']));

    let perPage = 10;
    let currentPage = 1;
    let pendingAction = null;
    let editingId = null;
    let sortAsc = true;

    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const collegeFilter = document.getElementById('collegeFilter');
    const periodFilter = document.getElementById('periodFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableBody = document.getElementById('cooperationTable');
    const tableWrapper = document.getElementById('tableWrapper');
    const emptyState = document.getElementById('emptyState');
    const tableSummary = document.getElementById('tableSummary');
    const pageInfo = document.getElementById('pageInfo');
    const pagination = document.getElementById('pagination');

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('cooperationToast'), { delay: 3000 });
    const confirmActionButton = document.getElementById('confirmAction');
    const formFileInput = document.getElementById('formFile');

    document.getElementById('confirmModal').addEventListener('hidden.bs.modal', () => {
        confirmActionButton.textContent = 'Ya, Lanjutkan';
        confirmActionButton.className = 'btn btn-primary';
    });

    function titleCase(value) {
        return String(value || '').split(' ').filter(Boolean).map((word) => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function statusClass(value) {
        return {
            aktif: 'bg-success',
            berakhir: 'bg-danger',
            menunggu: 'bg-warning text-dark',
            nonaktif: 'bg-secondary'
        }[value] || 'bg-secondary';
    }

    function labelJenisDokumen(value) {
        return {
            mou: 'MoU',
            pks: 'PKS',
            addendum: 'Addendum',
            surat_kerja_sama: 'Surat Kerja Sama',
        }[value] || titleCase(value);
    }

    function normalizeJenisValue(value) {
        const lowered = String(value || '').toLowerCase();
        if (['mou', 'pks', 'addendum', 'surat_kerja_sama'].includes(lowered)) {
            return lowered;
        }

        if (lowered.includes('mou')) return 'mou';
        if (lowered.includes('pks')) return 'pks';
        if (lowered.includes('addendum')) return 'addendum';
        if (lowered.includes('surat')) return 'surat_kerja_sama';

        return 'mou';
    }

    async function sendRequest(url, method, body = null) {
        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        };

        if (body instanceof FormData) {
            options.body = body;
        } else if (body) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        const response = await fetch(url, options);
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const validationMessage = data.errors
                ? Object.values(data.errors).flat().find(Boolean)
                : null;
            throw new Error(validationMessage || data.message || 'Permintaan gagal diproses.');
        }

        return data;
    }

    function filteredCooperations() {
        const keyword = searchInput.value.trim().toLowerCase();
        const type = typeFilter.value;
        const status = statusFilter.value;
        const college = collegeFilter.value;
        const period = periodFilter.value;

        return cooperations
            .filter((item) => {
                const matchKeyword = !keyword || [item.judul, item.kampus, item.jenis, item.dokumen, item.status, item.masa]
                    .join(' ')
                    .toLowerCase()
                    .includes(keyword);
                const matchType = type === 'semua' || item.jenis === type;
                const matchStatus = status === 'semua' || item.status === status;
                const matchCollege = college === 'semua' || item.kampus === college;
                const matchPeriod = period === 'semua' || item.masa === period;

                return matchKeyword && matchType && matchStatus && matchCollege && matchPeriod;
            })
            .sort((a, b) => sortAsc ? a.judul.localeCompare(b.judul) : b.judul.localeCompare(a.judul));
    }

    function updateStats() {
        document.getElementById('statTotal').textContent = cooperations.length;
        document.getElementById('statAktif').textContent = cooperations.filter((item) => item.status === 'aktif').length;
        document.getElementById('statBerakhir').textContent = cooperations.filter((item) => item.status === 'berakhir').length;
        document.getElementById('statMenunggu').textContent = cooperations.filter((item) => item.status === 'menunggu').length;
    }

    function renderTable() {
        const data = filteredCooperations();
        const totalPages = Math.max(Math.ceil(data.length / perPage), 1);
        currentPage = Math.min(currentPage, totalPages);
        const start = (currentPage - 1) * perPage;
        const pageData = data.slice(start, start + perPage);

        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.kampus}</td>
                <td><span class="badge bg-primary">${item.jenis}</span></td>
                <td>${item.dokumen}</td>
                <td class="fw-semibold">${item.judul}</td>
                <td>${item.mulai}</td>
                <td>${item.berakhir}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-info btn-sm" type="button" data-action="lihat" data-id="${item.id}" title="Lihat detail">
                            <i class="bi bi-eye"></i> Lihat
                        </button>
                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-id="${item.id}" title="Edit data">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <button class="btn btn-success btn-sm" type="button" data-action="download" data-id="${item.id}" title="Download dokumen">
                            <i class="bi bi-download"></i> Dokumen
                        </button>
                        <button class="btn btn-success btn-sm" type="button" data-action="verifikasi" data-id="${item.id}" title="Verifikasi data">
                            <i class="bi bi-check2-circle"></i> Verifikasi
                        </button>
                        <button class="btn btn-danger btn-sm" type="button" data-action="hapus" data-id="${item.id}" title="Hapus data">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tableWrapper.classList.toggle('d-none', data.length === 0);
        emptyState.classList.toggle('d-none', data.length > 0);
        tableSummary.textContent = `${data.length} kerja sama ditemukan`;
        pageInfo.textContent = data.length
            ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data kerja sama`
            : 'Menampilkan 0 data kerja sama';

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
        const toastElement = document.getElementById('cooperationToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-warning', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function showDetail(id) {
        const item = cooperations.find((data) => data.id === id);
        document.getElementById('detailContent').innerHTML = `
            <dl class="row mb-0">
                <dt class="col-sm-4">Perguruan Tinggi</dt>
                <dd class="col-sm-8">${item.kampus}</dd>
                <dt class="col-sm-4">Jenis</dt>
                <dd class="col-sm-8">${item.jenis}</dd>
                <dt class="col-sm-4">Nomor Dokumen</dt>
                <dd class="col-sm-8">${item.dokumen}</dd>
                <dt class="col-sm-4">Judul</dt>
                <dd class="col-sm-8">${item.judul}</dd>
                <dt class="col-sm-4">Tanggal Mulai</dt>
                <dd class="col-sm-8">${item.mulai}</dd>
                <dt class="col-sm-4">Tanggal Berakhir</dt>
                <dd class="col-sm-8">${item.berakhir}</dd>
                <dt class="col-sm-4">Status</dt>
                <dd class="col-sm-8">${titleCase(item.status)}</dd>
                <dt class="col-sm-4">Masa Berlaku</dt>
                <dd class="col-sm-8">${titleCase(item.masa)}</dd>
                <dt class="col-sm-4">Catatan</dt>
                <dd class="col-sm-8">${item.catatan ?? '-'}</dd>
            </dl>
        `;
        detailModal.show();
    }

    function openForm(id = null) {
        editingId = id;
        const item = id ? cooperations.find((data) => data.id === id) : null;
        document.getElementById('formTitle').textContent = id ? 'Edit Kerja Sama' : 'Tambah Kerja Sama';
        document.getElementById('formTitleInput').value = item?.judul || '';
        document.getElementById('formCollege').value = item?.kampus || 'Universitas Negeri Yogyakarta';
        document.getElementById('formType').value = item?.jenis_key || normalizeJenisValue(item?.jenis);
        document.getElementById('formDocument').value = item?.dokumen || '';
        formFileInput.required = !id;
        formFileInput.value = '';
        formModal.show();
    }

    function showConfirm(id, action) {
        const item = cooperations.find((data) => data.id === id);
        const messages = {
            verifikasi: `Apakah Anda yakin ingin memverifikasi kerja sama "${item.judul}"?`,
            hapus: `Tindakan ini akan menghapus kerja sama "${item.judul}". Apakah Anda yakin ingin melanjutkan?`
        };
        pendingAction = { id, action };
        document.getElementById('confirmTitle').textContent = `Konfirmasi ${titleCase(action)}`;
        document.getElementById('confirmMessage').textContent = messages[action];
        if (action === 'hapus') {
            confirmActionButton.textContent = 'Hapus';
            confirmActionButton.className = 'btn btn-danger';
        } else if (action === 'verifikasi') {
            confirmActionButton.textContent = 'Verifikasi';
            confirmActionButton.className = 'btn btn-success';
        }
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

    [typeFilter, statusFilter, collegeFilter, periodFilter].forEach((input) => {
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
        typeFilter.value = 'semua';
        statusFilter.value = 'semua';
        collegeFilter.value = 'semua';
        periodFilter.value = 'semua';
        currentPage = 1;
        document.querySelectorAll('[data-status-card]').forEach((item) => item.classList.remove('active'));
        document.querySelector('[data-status-card="semua"]').classList.add('active');
        renderTable();
    });

    perPageSelect.addEventListener('change', () => {
        perPage = Number(perPageSelect.value);
        currentPage = 1;
        renderTable();
    });

    document.querySelector('[data-sort="judul"]').addEventListener('click', () => {
        sortAsc = !sortAsc;
        renderTable();
    });

    document.getElementById('addCooperationButton').addEventListener('click', () => openForm());

    document.getElementById('saveForm').addEventListener('click', () => {
        const title = document.getElementById('formTitleInput').value.trim();
        const campus = document.getElementById('formCollege').value.trim();
        const type = document.getElementById('formType').value;
        const documentNumber = document.getElementById('formDocument').value.trim();
        const file = formFileInput.files?.[0] || null;

        if (!title || !campus || !type || !documentNumber || (!editingId && !file)) {
            showToast('Judul, mitra, jenis dokumen, nomor dokumen, dan file wajib diisi.', false);
            return;
        }

        const payload = new FormData();
        payload.append('judul', title);
        payload.append('kampus', campus);
        payload.append('jenis_dokumen', type);
        payload.append('nomor_dokumen', documentNumber);
        if (file) {
            payload.append('file', file);
        }

        const targetUrl = editingId
            ? updateUrlTemplate.replace('__DOCUMENT__', editingId)
            : storeUrl;
        const method = editingId ? 'POST' : 'POST';
        if (editingId) {
            payload.append('_method', 'PATCH');
        }

        sendRequest(targetUrl, method, payload)
            .then((data) => {
                const documentRow = data.document;
                if (editingId) {
                    const index = cooperations.findIndex((item) => item.id === editingId);
                    if (index !== -1 && documentRow) {
                        cooperations[index] = documentRow;
                    }
                } else if (documentRow) {
                    cooperations.unshift(documentRow);
                }

                formModal.hide();
                updateStats();
                renderTable();
                showToast(data.message || `Data kerja sama "${title}" berhasil disimpan.`);
            })
            .catch((error) => {
                showToast(error.message, false);
            });
    });

    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        if (button.dataset.action === 'lihat') {
            showDetail(id);
        } else if (button.dataset.action === 'edit') {
            openForm(id);
        } else if (button.dataset.action === 'download') {
            window.location.href = downloadUrlTemplate.replace('__DOCUMENT__', id);
        } else {
            showConfirm(id, button.dataset.action);
        }
    });

    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) {
            return;
        }

        currentPage = Number(button.dataset.page);
        renderTable();
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (!pendingAction) {
            return;
        }

        const itemIndex = cooperations.findIndex((item) => item.id === pendingAction.id);
        const item = cooperations[itemIndex];
        let message = '';

        if (pendingAction.action === 'hapus') {
            sendRequest(deleteUrlTemplate.replace('__DOCUMENT__', pendingAction.id), 'DELETE')
                .then((data) => {
                    cooperations.splice(itemIndex, 1);
                    message = data.message || `Data kerja sama "${item.judul}" berhasil dihapus.`;
                    confirmModal.hide();
                    updateStats();
                    renderTable();
                    showToast(message);
                })
                .catch((error) => {
                    showToast(error.message, false);
                })
                .finally(() => {
                    pendingAction = null;
                });
            return;
        } else if (pendingAction.action === 'verifikasi') {
            sendRequest(reviewUrlTemplate.replace('__DOCUMENT__', pendingAction.id), 'PATCH', { status: 'disetujui' })
                .then((data) => {
                    if (data.document) {
                        cooperations[itemIndex] = data.document;
                    } else {
                        item.status = 'aktif';
                    }
                    message = data.message || `Kerja sama "${item.judul}" berhasil diverifikasi.`;
                    confirmModal.hide();
                    updateStats();
                    renderTable();
                    showToast(message);
                })
                .catch((error) => {
                    showToast(error.message, false);
                })
                .finally(() => {
                    pendingAction = null;
                });
            return;
        }

        confirmModal.hide();
        updateStats();
        renderTable();
        showToast(message);
        pendingAction = null;
    });

    updateStats();
    renderTable();
</script>
@endpush
