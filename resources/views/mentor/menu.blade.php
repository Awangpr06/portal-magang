@extends('mentor.layout.mentor')

@section('title', $title)
@section('page-title', $title)

@section('content')
@php
    $icons = [
        'participants' => ['bi-people-fill', 'bg-primary', 'Data Peserta'],
        'monitoring' => ['bi-activity', 'bg-success', 'Data Monitoring'],
        'attendance' => ['bi-calendar-check-fill', 'bg-success', 'Data Absensi'],
        'activities' => ['bi-journal-check', 'bg-warning', 'Data Kegiatan'],
        'review' => ['bi-file-earmark-check-fill', 'bg-warning', 'Data Tindak Lanjut'],
        'assessment' => ['bi-award-fill', 'bg-info', 'Data Penilaian'],
        'settings' => ['bi-gear-fill', 'bg-dark', 'Data Pengaturan'],
    ];
    [$icon, $color, $tableTitle] = $icons[$type] ?? $icons['participants'];
@endphp

<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
        </ol>
    </nav>

    <section class="hero-panel p-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h3 class="fw-bold mb-2">{{ $title }}</h3>
                <p class="mb-0">{{ $description }}</p>
            </div>
            <button class="btn btn-light" type="button" id="exportButton"><i class="bi bi-download"></i> Ekspor</button>
        </div>
    </section>

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
                    <div><p class="text-muted mb-1">Menunggu Tindak Lanjut</p><h3 class="mb-0" id="statReview">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Perlu Tindak Lanjut</p><h3 class="mb-0" id="statFollowUp">0</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label" for="searchInput">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari nama, NIM, divisi, atau status">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="divisionFilter">Divisi</label>
                    <select class="form-select" id="divisionFilter">
                        <option value="semua">Semua Divisi</option>
                        <option value="Data & Aplikasi">Data & Aplikasi</option>
                        <option value="Keuangan">Keuangan</option>
                        <option value="Digital Service">Digital Service</option>
                        <option value="Perencanaan">Perencanaan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua</option>
                        <option value="aktif">Aktif</option>
                        <option value="review">Tindak Lanjut</option>
                        <option value="tindak lanjut">Tindak Lanjut</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-warning w-50" id="searchButton" type="button" title="Cari"><i class="bi bi-search"></i></button>
                    <button class="btn btn-outline-secondary w-50" id="resetButton" type="button" title="Reset"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
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
                            <th>Nama Peserta</th>
                            <th>NIM</th>
                            <th>Divisi</th>
                            <th>Kehadiran</th>
                            <th>Progres</th>
                            <th>Status</th>
                            <th width="230">Aksi</th>
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
                <h5 class="modal-title">Detail Peserta</h5>
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
document.addEventListener('DOMContentLoaded', () => {
    const rows = [
        { nama: 'Aulia Berliana', nim: '220001', divisi: 'Data & Aplikasi', hadir: '96%', progres: '82%', status: 'aktif', catatan: 'Output harian stabil dan siap diberi tugas lanjutan.' },
        { nama: 'Dewi Lestari', nim: '220003', divisi: 'Keuangan', hadir: '88%', progres: '64%', status: 'review', catatan: 'Minggu ini perlu dilengkapi bukti pekerjaan.' },
        { nama: 'Naufal Rizky', nim: '220008', divisi: 'Digital Service', hadir: '92%', progres: '58%', status: 'tindak lanjut', catatan: 'Perlu evaluasi target mingguan bersama mentor.' },
        { nama: 'Rani Kartika', nim: '220010', divisi: 'Perencanaan', hadir: '98%', progres: '76%', status: 'aktif', catatan: 'Kehadiran dan capaian kerja berjalan baik.' },
        { nama: 'Satria Wibowo', nim: '220011', divisi: 'Data & Aplikasi', hadir: '90%', progres: '69%', status: 'review', catatan: 'Menunggu tindak lanjut aktivitas harian.' }
    ];

    const tableBody = document.getElementById('dataTable');
    const searchInput = document.getElementById('searchInput');
    const divisionFilter = document.getElementById('divisionFilter');
    const statusFilter = document.getElementById('statusFilter');
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const toast = new bootstrap.Toast(document.getElementById('actionToast'), { delay: 2500 });

    const label = (value) => value.charAt(0).toUpperCase() + value.slice(1);
    const badge = (value) => ({ aktif:'bg-success', review:'bg-warning text-dark', 'tindak lanjut':'bg-danger' }[value] || 'bg-secondary');

    function filteredRows() {
        const keyword = searchInput.value.trim().toLowerCase();
        return rows.filter((item) => {
            const matchKeyword = !keyword || Object.values(item).join(' ').toLowerCase().includes(keyword);
            const matchDivision = divisionFilter.value === 'semua' || item.divisi === divisionFilter.value;
            const matchStatus = statusFilter.value === 'semua' || item.status === statusFilter.value;
            return matchKeyword && matchDivision && matchStatus;
        });
    }

    function showToast(message) {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function updateStats(data) {
        document.getElementById('statTotal').textContent = rows.length;
        document.getElementById('statActive').textContent = rows.filter((item) => item.status === 'aktif').length;
        document.getElementById('statReview').textContent = rows.filter((item) => item.status === 'review').length;
        document.getElementById('statFollowUp').textContent = rows.filter((item) => item.status === 'tindak lanjut').length;
        document.getElementById('tableSummary').textContent = `${data.length} data ditemukan`;
    }

    function renderTable() {
        const data = filteredRows();
        tableBody.innerHTML = data.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td class="fw-semibold">${item.nama}</td>
                <td>${item.nim}</td>
                <td>${item.divisi}</td>
                <td>${item.hadir}</td>
                <td>${item.progres}</td>
                <td><span class="badge ${badge(item.status)}">${label(item.status)}</span></td>
                <td>
                    <button class="btn btn-info btn-sm text-white" type="button" data-action="detail" data-index="${rows.indexOf(item)}">Detail</button>
                    <button class="btn btn-success btn-sm" type="button" data-action="validasi" data-index="${rows.indexOf(item)}">Validasi</button>
                    <button class="btn btn-warning btn-sm" type="button" data-action="review" data-index="${rows.indexOf(item)}">Tindak Lanjut</button>
                </td>
            </tr>
        `).join('');
        updateStats(data);
    }

    [divisionFilter, statusFilter].forEach((input) => input.addEventListener('change', renderTable));
    document.getElementById('searchButton').addEventListener('click', renderTable);
    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            renderTable();
        }
    });
    document.getElementById('resetButton').addEventListener('click', () => {
        searchInput.value = '';
        divisionFilter.value = 'semua';
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
                    <dt class="col-sm-4">Divisi</dt><dd class="col-sm-8">${item.divisi}</dd>
                    <dt class="col-sm-4">Kehadiran</dt><dd class="col-sm-8">${item.hadir}</dd>
                    <dt class="col-sm-4">Progres</dt><dd class="col-sm-8">${item.progres}</dd>
                    <dt class="col-sm-4">Catatan</dt><dd class="col-sm-8">${item.catatan}</dd>
                </dl>
            `;
            detailModal.show();
            return;
        }
        item.status = button.dataset.action === 'validasi' ? 'aktif' : 'review';
        renderTable();
        showToast(`Data ${item.nama} berhasil diperbarui.`);
    });

    renderTable();
});
</script>
@endpush
