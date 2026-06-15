@extends('pembimbing.layout.pembimbing')

@section('title', $title)
@section('page-title', $title)

@section('content')
@php
    $icons = [
        'students' => ['bi-people-fill', 'bg-primary', 'Data Mahasiswa'],
        'attendance' => ['bi-calendar-check-fill', 'bg-success', 'Data Absensi'],
        'activities' => ['bi-journal-check', 'bg-warning', 'Data Logbook'],
        'reports' => ['bi-file-earmark-text-fill', 'bg-info', 'Data Laporan'],
        'cooperation' => ['bi-handshake-fill', 'bg-primary', 'Data Kerja Sama'],
        'status' => ['bi-clipboard-data-fill', 'bg-dark', 'Status Magang'],
        'messages' => ['bi-chat-dots-fill', 'bg-secondary', 'Data Komunikasi'],
        'settings' => ['bi-gear-fill', 'bg-dark', 'Data Pengaturan'],
    ];
    [$icon, $color, $tableTitle] = $icons[$type] ?? $icons['students'];
@endphp

<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">{{ $title }}</h2>
            <p class="text-muted mb-0">{{ $description }}</p>
        </div>
        <button class="btn btn-outline-primary" type="button" id="exportButton">
            <i class="bi bi-download"></i> Ekspor Data
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Data</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon {{ $color }}"><i class="bi {{ $icon }}"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Menunggu Review</p><h3 class="mb-0" id="statReview">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Perlu Revisi</p><h3 class="mb-0" id="statRevision">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="searchInput">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, instansi, atau status">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="periodFilter">Periode</label>
                    <select class="form-select" id="periodFilter">
                        <option value="semua">Semua Periode</option>
                        <option value="Batch 1 2026">Batch 1 2026</option>
                        <option value="Batch 2 2026">Batch 2 2026</option>
                        <option value="MBKM 2026">MBKM 2026</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="review">Review</option>
                        <option value="revisi">Revisi</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-50" id="searchButton" type="button" title="Cari"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" id="resetButton" type="button" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">{{ $tableTitle }}</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan 0 data</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Instansi</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th width="220">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dataTable"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="actionToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rows = [
        { nama: 'Aulia Berliana', nim: '220001', prodi: 'Informatika', instansi: 'Dinas Kominfo DIY', periode: 'Batch 1 2026', status: 'aktif', catatan: 'Progres magang stabil dan logbook lengkap.' },
        { nama: 'Dewi Lestari', nim: '220003', prodi: 'Administrasi Publik', instansi: 'Bank BPD DIY', periode: 'MBKM 2026', status: 'revisi', catatan: 'Butuh perbaikan laporan mingguan.' },
        { nama: 'Naufal Rizky', nim: '220008', prodi: 'Informatika', instansi: 'PT Inovasi Digital', periode: 'Batch 2 2026', status: 'review', catatan: 'Laporan akhir sedang direview.' },
        { nama: 'Rani Kartika', nim: '220010', prodi: 'Manajemen', instansi: 'Bappeda DIY', periode: 'Batch 1 2026', status: 'aktif', catatan: 'Kehadiran dan kegiatan berjalan baik.' },
        { nama: 'Satria Wibowo', nim: '220011', prodi: 'Informatika', instansi: 'Dinas Kominfo DIY', periode: 'Batch 2 2026', status: 'review', catatan: 'Menunggu validasi logbook pekan ini.' },
        { nama: 'Oktavia Rahma', nim: '220009', prodi: 'Akuntansi', instansi: 'Bank BPD DIY', periode: 'MBKM 2026', status: 'aktif', catatan: 'Laporan dan absensi lengkap.' }
    ];

    const tableBody = document.getElementById('dataTable');
    const searchInput = document.getElementById('searchInput');
    const periodFilter = document.getElementById('periodFilter');
    const statusFilter = document.getElementById('statusFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'), { delay: 2500 });

    function label(value) {
        return value.charAt(0).toUpperCase() + value.slice(1);
    }

    function badge(value) {
        return { aktif: 'bg-success', review: 'bg-info text-dark', revisi: 'bg-warning text-dark' }[value];
    }

    function filteredRows() {
        const keyword = searchInput.value.trim().toLowerCase();
        const period = periodFilter.value;
        const status = statusFilter.value;
        return rows.filter((item) => {
            const matchKeyword = !keyword || Object.values(item).join(' ').toLowerCase().includes(keyword);
            const matchPeriod = period === 'semua' || item.periode === period;
            const matchStatus = status === 'semua' || item.status === status;
            return matchKeyword && matchPeriod && matchStatus;
        });
    }

    function updateStats(data) {
        document.getElementById('statTotal').textContent = rows.length;
        document.getElementById('statActive').textContent = rows.filter((item) => item.status === 'aktif').length;
        document.getElementById('statReview').textContent = rows.filter((item) => item.status === 'review').length;
        document.getElementById('statRevision').textContent = rows.filter((item) => item.status === 'revisi').length;
        document.getElementById('tableSummary').textContent = `${data.length} data ditemukan`;
    }

    function renderTable() {
        const data = filteredRows();
        tableBody.innerHTML = data.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td class="fw-semibold">${item.nama}</td>
                <td>${item.nim}</td>
                <td>${item.prodi}</td>
                <td>${item.instansi}</td>
                <td>${item.periode}</td>
                <td><span class="badge ${badge(item.status)}">${label(item.status)}</span></td>
                <td>
                    <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-index="${rows.indexOf(item)}">Detail</button>
                    <button class="btn btn-success btn-sm" type="button" data-action="validasi" data-index="${rows.indexOf(item)}">Validasi</button>
                </td>
            </tr>
        `).join('');
        updateStats(data);
    }

    function showToast(message) {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    [periodFilter, statusFilter].forEach((input) => input.addEventListener('change', renderTable));
    document.getElementById('searchButton').addEventListener('click', renderTable);
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            renderTable();
        }
    });
    document.getElementById('resetButton').addEventListener('click', () => {
        searchInput.value = '';
        periodFilter.value = 'semua';
        statusFilter.value = 'semua';
        renderTable();
        showToast('Filter berhasil direset.');
    });
    document.getElementById('exportButton').addEventListener('click', () => showToast('Data berhasil disiapkan untuk ekspor.'));
    tableBody.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = rows[Number(button.dataset.index)];
        if (button.dataset.action === 'detail') {
            document.getElementById('detailContent').innerHTML = `
                <dl class="row mb-0">
                    <dt class="col-sm-4">Nama</dt><dd class="col-sm-8">${item.nama}</dd>
                    <dt class="col-sm-4">NIM</dt><dd class="col-sm-8">${item.nim}</dd>
                    <dt class="col-sm-4">Program Studi</dt><dd class="col-sm-8">${item.prodi}</dd>
                    <dt class="col-sm-4">Instansi</dt><dd class="col-sm-8">${item.instansi}</dd>
                    <dt class="col-sm-4">Periode</dt><dd class="col-sm-8">${item.periode}</dd>
                    <dt class="col-sm-4">Catatan</dt><dd class="col-sm-8">${item.catatan}</dd>
                </dl>
            `;
            detailModal.show();
            return;
        }
        item.status = 'aktif';
        renderTable();
        showToast(`Data ${item.nama} berhasil divalidasi.`);
    });

    renderTable();
</script>
@endpush
