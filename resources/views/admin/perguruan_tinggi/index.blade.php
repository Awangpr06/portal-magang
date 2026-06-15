@extends('admin.layout.admin')

@section('title', 'Manajemen Perguruan Tinggi')

@push('styles')
<style>
    .campus-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .campus-page .stat-card,
    .campus-page .submenu-card,
    .campus-page .info-card,
    .campus-page .search-card {
        border: 0;
        border-radius: 8px;
    }

    .campus-page .stat-card {
        transition: 0.2s ease;
    }

    .campus-page .stat-card:hover,
    .campus-page .submenu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .campus-page .stat-icon,
    .campus-page .submenu-icon {
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .campus-page .growth {
        font-size: 13px;
    }

    .campus-page .submenu-card {
        min-height: 230px;
        transition: 0.2s ease;
    }

    .campus-page .quick-result {
        border: 1px solid #e9eef2;
        border-radius: 8px;
        padding: 12px 14px;
        background: #fff;
    }

    .campus-page .pagination .page-link {
        color: #0b5f86;
    }

    .campus-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid campus-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Manajemen Perguruan Tinggi</h2>
            <p class="text-muted mb-0">
                Pusat navigasi pengelolaan data perguruan tinggi dan kerja sama secara terintegrasi.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Perguruan Tinggi</p>
                        <h3 class="mb-1">{{ $campusStats['total'] ?? 0 }}</h3>
                        <span class="growth text-success"><i class="bi bi-arrow-up-short"></i> {{ $campusStats['new_this_month'] ?? 0 }} baru bulan ini</span>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-building"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Kerja Sama</p>
                        <h3 class="mb-1">{{ $cooperationStats['total'] ?? 0 }}</h3>
                        <span class="growth text-success"><i class="bi bi-arrow-up-short"></i> {{ $cooperationStats['new_this_month'] ?? 0 }} baru bulan ini</span>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-file-earmark-text-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Kerja Sama Aktif</p>
                        <h3 class="mb-1">{{ $cooperationStats['aktif'] ?? 0 }}</h3>
                        <span class="growth text-success"><i class="bi bi-check2-circle"></i> dari total kerja sama</span>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-patch-check-fill"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Kerja Sama Berakhir</p>
                        <h3 class="mb-1">{{ $cooperationStats['berakhir'] ?? 0 }}</h3>
                        <span class="growth text-warning"><i class="bi bi-exclamation-circle"></i> perlu tinjauan</span>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-calendar-x-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card search-card shadow-sm mb-4">
        <div class="card-body">
            <label for="globalSearch" class="form-label">Pencarian Global</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" class="form-control" id="globalSearch" placeholder="Cari menu, data, atau informasi...">
                <button class="btn btn-primary" type="button" id="searchButton">Cari</button>
            </div>
            <div class="mt-3 d-none" id="searchResult"></div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="row g-3" id="submenuList">
                <div class="col-md-6 submenu-item">
                    <div class="card submenu-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="submenu-icon bg-primary mb-3"><i class="bi bi-building-fill"></i></span>
                            <h5 class="fw-bold">Data Perguruan Tinggi</h5>
                            <p class="text-muted">
                                Kelola profil perguruan tinggi, jenis institusi, PIC, email, dan status verifikasi.
                            </p>
                            <button class="btn btn-primary mt-auto access-menu"
                                    type="button"
                                    data-target="{{ route('admin.perguruan-tinggi.data') }}"
                                    data-title="Data Perguruan Tinggi">
                                Akses Menu
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 submenu-item">
                    <div class="card submenu-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <span class="submenu-icon bg-success mb-3"><i class="bi bi-journal-check"></i></span>
                            <h5 class="fw-bold">Data Kerja Sama</h5>
                            <p class="text-muted">
                                Pantau kerja sama perguruan tinggi, periode pelaksanaan, status aktif, dan masa berakhir.
                            </p>
                            <button class="btn btn-success mt-auto access-menu"
                                    type="button"
                                    data-target="{{ route('admin.perguruan-tinggi.kerjasama') }}"
                                    data-title="Data Kerja Sama">
                                Akses Menu
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mt-3">
                <span class="text-muted" id="pageInfo">Menampilkan 1-2 dari 2 menu</span>
                <nav aria-label="Pagination submenu perguruan tinggi">
                    <ul class="pagination mb-0">
                        <li class="page-item disabled"><button class="page-link" type="button">Previous</button></li>
                        <li class="page-item active"><button class="page-link" type="button">1</button></li>
                        <li class="page-item disabled"><button class="page-link" type="button">Next</button></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card info-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Panel Informasi</h5>
                    <div class="quick-result mb-3">
                        <strong>Panduan singkat</strong>
                        <p class="text-muted mb-0">Gunakan Data Perguruan Tinggi untuk mengelola institusi, dan Data Kerja Sama untuk memantau kontrak kerja sama.</p>
                    </div>
                    <div class="quick-result mb-3">
                        <strong>Aktivitas terbaru</strong>
                        <p class="text-muted mb-0">3 kerja sama baru menunggu pemeriksaan dokumen periode ini.</p>
                    </div>
                    <div class="quick-result">
                        <strong>Perhatian</strong>
                        <p class="text-muted mb-0">28 kerja sama tercatat berakhir dan perlu ditinjau ulang.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Navigasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="confirmMessage">Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Ya, Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="campusToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let targetUrl = '';
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('campusToast'), { delay: 3000 });

    function showToast(message, success = true) {
        const toastElement = document.getElementById('campusToast');
        toastElement.classList.toggle('text-bg-success', success);
        toastElement.classList.toggle('text-bg-warning', !success);
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    document.querySelectorAll('.access-menu').forEach((button) => {
        button.addEventListener('click', () => {
            targetUrl = button.dataset.target;
            document.getElementById('confirmMessage').textContent = `Apakah Anda yakin ingin membuka menu ${button.dataset.title}?`;
            confirmModal.show();
        });
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        if (targetUrl) {
            window.location.href = targetUrl;
        }
    });

    document.getElementById('searchButton').addEventListener('click', () => {
        const keyword = document.getElementById('globalSearch').value.trim().toLowerCase();
        const result = document.getElementById('searchResult');

        if (!keyword) {
            result.className = 'mt-3 alert alert-warning';
            result.textContent = 'Masukkan kata kunci pencarian terlebih dahulu.';
            showToast('Pencarian belum memiliki kata kunci.', false);
            return;
        }

        const matches = ['data perguruan tinggi', 'data kerja sama', 'kerja sama aktif', 'kerja sama berakhir']
            .filter((item) => item.includes(keyword));

        result.className = matches.length ? 'mt-3 alert alert-success' : 'mt-3 alert alert-secondary';
        result.textContent = matches.length
            ? `Ditemukan hasil: ${matches.join(', ')}.`
            : 'Tidak ada hasil yang sesuai dengan kata kunci.';
        showToast(matches.length ? 'Hasil pencarian ditemukan.' : 'Hasil pencarian tidak ditemukan.', matches.length > 0);
    });

    document.getElementById('globalSearch').addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            document.getElementById('searchButton').click();
        }
    });
</script>
@endpush
