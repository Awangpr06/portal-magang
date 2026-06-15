@extends('admin.layout.admin')

@section('title', 'Penilaian Magang')

@push('styles')
<style>
    .assessment-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .assessment-page .breadcrumb a {
        color: #0b5f86;
        text-decoration: none;
    }

    .assessment-page .stat-card,
    .assessment-page .filter-card,
    .assessment-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .assessment-page .stat-card {
        transition: 0.2s ease;
    }

    .assessment-page .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(11, 95, 134, 0.16);
    }

    .assessment-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .assessment-page .table {
        font-size: 14px;
    }

    .assessment-page .component-value {
        min-width: 92px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.35rem 0.55rem;
        border-radius: 999px;
        background: #eef6fb;
        color: #163342;
        font-weight: 600;
        line-height: 1;
    }

    .assessment-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 13px;
        white-space: nowrap;
    }

    .assessment-page .table tbody tr:hover {
        background: #f8fbfd;
    }

    .assessment-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        min-width: 250px;
    }

    .assessment-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }

    .assessment-page .pagination .page-link {
        color: #0b5f86;
    }

    .assessment-page .pagination .active .page-link {
        background: #0b5f86;
        border-color: #0b5f86;
        color: #fff;
    }
</style>
@endpush

@section('content')
@php
    $assessmentCollection = collect($assessmentRows ?? []);
    $participantCollection = collect($participants ?? []);
    $mentorCollection = collect($mentors ?? []);
    $pembimbingCollection = collect($pembimbings ?? []);
    $statusBadgeClass = function (string $status): string {
        return match ($status) {
            'final', 'selesai', 'disetujui' => 'bg-success',
            'revisi' => 'bg-danger',
            'review' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    };
@endphp

<div class="container-fluid assessment-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.magang.index') }}">Manajemen Magang</a></li>
            <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Penilaian Magang</h2>
            <p class="text-muted mb-0">
                Menampilkan nilai yang diberikan mentor dan pembimbing akademik berdasarkan data yang tersimpan di database.
            </p>
        </div>
        <button class="btn btn-primary" type="button" id="addAssessmentButton">
            <i class="bi bi-plus-lg"></i>
            Tambah Penilaian
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Penilaian</p>
                        <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-award"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Nilai Mentor</p>
                        <h3 class="mb-0">{{ $stats['mentor'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-person-check-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Nilai Pembimbing</p>
                        <h3 class="mb-0">{{ $stats['pembimbing'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-info"><i class="bi bi-person-badge-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Final</p>
                        <h3 class="mb-0">{{ $stats['final'] ?? 0 }}</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" for="searchInput">Pencarian</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="search" class="form-control" id="searchInput" placeholder="Cari peserta, penilai, komponen, atau status">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="jenisFilter">Jenis</label>
                    <select class="form-select" id="jenisFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="mentor">Mentor</option>
                        <option value="pembimbing">Pembimbing Akademik</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="statusFilter">Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="review">Review</option>
                        <option value="final">Final</option>
                        <option value="revisi">Revisi</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="perPageSelect">Data</label>
                    <select class="form-select" id="perPageSelect">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Daftar Penilaian</h5>
                    <p class="text-muted mb-0" id="tableSummary">Menampilkan {{ $assessmentCollection->count() }} data</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peserta</th>
                            <th>Jenis</th>
                            <th>Penilai</th>
                            <th class="text-center">Kehadiran</th>
                            <th class="text-center">Aktivitas</th>
                            <th class="text-center">Laporan</th>
                            <th class="text-center">Sikap</th>
                            <th class="text-center">Kompetensi</th>
                            <th class="text-center">Nilai</th>
                            <th class="text-center">Nilai Akhir</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="assessmentTableBody">
                        @forelse ($assessmentCollection as $assessment)
                            <tr data-assessment-row
                                data-search="{{ strtolower(($assessment['peserta'] ?? '') . ' ' . ($assessment['nim'] ?? '') . ' ' . ($assessment['prodi'] ?? '') . ' ' . ($assessment['kampus'] ?? '') . ' ' . ($assessment['jenis_label'] ?? '') . ' ' . ($assessment['penilai'] ?? '') . ' ' . ($assessment['komponen_presence'] ?? '') . ' ' . ($assessment['komponen_activity'] ?? '') . ' ' . ($assessment['komponen_report'] ?? '') . ' ' . ($assessment['komponen_attitude'] ?? '') . ' ' . ($assessment['komponen_competency'] ?? '') . ' ' . ($assessment['status'] ?? '')) }}"
                                data-jenis="{{ $assessment['jenis'] }}"
                                data-status="{{ $assessment['status'] }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $assessment['peserta'] }}</div>
                                    <small class="text-muted">{{ $assessment['nim'] }} | {{ $assessment['kampus'] }}</small>
                                </td>
                                <td><span class="badge bg-primary">{{ $assessment['jenis_label'] }}</span></td>
                                <td>{{ $assessment['penilai'] }}</td>
                                <td class="text-center"><span class="component-value">{{ $assessment['komponen_presence'] }}</span></td>
                                <td class="text-center"><span class="component-value">{{ $assessment['komponen_activity'] }}</span></td>
                                <td class="text-center"><span class="component-value">{{ $assessment['komponen_report'] }}</span></td>
                                <td class="text-center"><span class="component-value">{{ $assessment['komponen_attitude'] }}</span></td>
                                <td class="text-center"><span class="component-value">{{ $assessment['komponen_competency'] }}</span></td>
                                <td class="text-center">{{ number_format((float) $assessment['nilai'], 2, ',', '.') }}</td>
                                <td class="text-center">{{ number_format((float) $assessment['nilai_akhir'], 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $statusBadgeClass($assessment['status']) }}">
                                        {{ ucfirst($assessment['status_label']) }}
                                    </span>
                                </td>
                                <td>{{ $assessment['tanggal'] }}</td>
                                <td>
                                    <div class="action-group">
                                        <button class="btn btn-info btn-sm" type="button" data-action="detail" data-assessment='@json($assessment)'>Detail</button>
                                        <button class="btn btn-warning btn-sm" type="button" data-action="edit" data-assessment='@json($assessment)'>Edit</button>
                                        <form action="{{ route('admin.magang.penilaian.destroy', $assessment['id']) }}" method="POST" onsubmit="return confirm('Hapus penilaian ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14">
                                    <div class="empty-state">
                                        <div>
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <h6 class="mb-1">Belum ada data penilaian</h6>
                                            <p class="mb-0">Klik tombol Tambah Penilaian untuk membuat data baru.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="assessmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="assessmentForm" method="POST" action="{{ route('admin.magang.penilaian.store') }}">
                @csrf
                <input type="hidden" name="_method" id="methodField" disabled>
                <div class="modal-header">
                    <h5 class="modal-title" id="assessmentModalLabel">Tambah Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="peserta_id">Peserta</label>
                            <select class="form-select" id="peserta_id" name="peserta_id" required>
                                <option value="">Pilih peserta</option>
                                @foreach ($participantCollection as $participant)
                                    <option value="{{ $participant['id'] }}">{{ $participant['nama'] }} - {{ $participant['nim'] }} - {{ $participant['kampus'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="jenis">Jenis Penilaian</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="mentor">Mentor</option>
                                <option value="pembimbing">Pembimbing Akademik</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="periode">Periode</label>
                            <input type="text" class="form-control" id="periode" name="periode" placeholder="Contoh: Batch 1 2026">
                        </div>
                        <div class="col-md-6" id="mentorField">
                            <label class="form-label" for="mentor_id">Mentor</label>
                            <select class="form-select" id="mentor_id" name="mentor_id">
                                <option value="">Pilih mentor</option>
                                @foreach ($mentorCollection as $mentor)
                                    <option value="{{ $mentor['id'] }}">{{ $mentor['nama'] }} - {{ $mentor['nip'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-none" id="pembimbingField">
                            <label class="form-label" for="pembimbing_id">Pembimbing Akademik</label>
                            <select class="form-select" id="pembimbing_id" name="pembimbing_id">
                                <option value="">Pilih pembimbing</option>
                                @foreach ($pembimbingCollection as $pembimbing)
                                    <option value="{{ $pembimbing['id'] }}">{{ $pembimbing['nama'] }} - {{ $pembimbing['nidn_nip'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="komponen">Komponen Penilaian</label>
                            <input type="text" class="form-control" id="komponen" name="komponen" placeholder="Contoh: Sikap Kerja">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="bobot">Bobot</label>
                            <input type="number" class="form-control" id="bobot" name="bobot" min="0" max="100" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="nilai">Nilai</label>
                            <input type="number" class="form-control" id="nilai" name="nilai" min="0" max="100" step="0.01" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="nilai_akhir">Nilai Akhir</label>
                            <input type="number" class="form-control" id="nilai_akhir" name="nilai_akhir" min="0" max="100" step="0.01" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="review">Review</option>
                                <option value="final">Final</option>
                                <option value="selesai">Selesai</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="revisi">Revisi</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="catatan">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Catatan penilaian"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveAssessmentButton">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penilaian</h5>
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
    <div id="assessmentToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Aksi penilaian berhasil diproses.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = Array.from(document.querySelectorAll('[data-assessment-row]'));
    const searchInput = document.getElementById('searchInput');
    const jenisFilter = document.getElementById('jenisFilter');
    const statusFilter = document.getElementById('statusFilter');
    const perPageSelect = document.getElementById('perPageSelect');
    const tableSummary = document.getElementById('tableSummary');
    const tableBody = document.getElementById('assessmentTableBody');
    const toast = new bootstrap.Toast(document.getElementById('assessmentToast'));
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const assessmentModal = new bootstrap.Modal(document.getElementById('assessmentModal'));
    const form = document.getElementById('assessmentForm');
    const methodField = document.getElementById('methodField');
    const modalTitle = document.getElementById('assessmentModalLabel');
    const mentorField = document.getElementById('mentorField');
    const pembimbingField = document.getElementById('pembimbingField');
    const jenisSelect = document.getElementById('jenis');
    const addButton = document.getElementById('addAssessmentButton');
    const resetButton = document.getElementById('resetFilter');
    let currentPage = 1;

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('assessmentToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function statusText(status) {
        return {
            draft: 'Draft',
            review: 'Review',
            final: 'Final',
            selesai: 'Selesai',
            disetujui: 'Disetujui',
            revisi: 'Revisi',
        }[status] || status;
    }

    function statusClass(status) {
        return {
            draft: 'bg-secondary',
            review: 'bg-warning text-dark',
            final: 'bg-success',
            selesai: 'bg-success',
            disetujui: 'bg-success',
            revisi: 'bg-danger',
        }[status] || 'bg-secondary';
    }

    function applyRoleVisibility() {
        const isMentor = jenisSelect.value === 'mentor';
        mentorField.classList.toggle('d-none', !isMentor);
        pembimbingField.classList.toggle('d-none', isMentor);
        document.getElementById('mentor_id').required = isMentor;
        document.getElementById('pembimbing_id').required = !isMentor;
    }

    function filterRows() {
        const keyword = searchInput.value.trim().toLowerCase();
        const jenis = jenisFilter.value;
        const status = statusFilter.value;
        let visible = 0;

        rows.forEach((row) => {
            const matchKeyword = !keyword || row.dataset.search.includes(keyword);
            const matchJenis = jenis === 'semua' || row.dataset.jenis === jenis;
            const matchStatus = status === 'semua' || row.dataset.status === status;
            const show = matchKeyword && matchJenis && matchStatus;
            row.classList.toggle('d-none', !show);
            if (show) visible += 1;
        });

        tableSummary.textContent = `Menampilkan ${visible} data`;
        currentPage = 1;
    }

    function resetForm() {
        form.action = @json(route('admin.magang.penilaian.store'));
        methodField.disabled = true;
        methodField.value = '';
        modalTitle.textContent = 'Tambah Penilaian';
        form.reset();
        jenisSelect.value = 'mentor';
        document.getElementById('bobot').value = 0;
        document.getElementById('nilai').value = 0;
        document.getElementById('nilai_akhir').value = 0;
        applyRoleVisibility();
    }

    function fillForm(data) {
        form.action = `${@json(url('/admin/manajemen-magang/penilaian'))}/${data.id}`;
        methodField.disabled = false;
        methodField.value = 'PATCH';
        modalTitle.textContent = 'Edit Penilaian';

        document.getElementById('peserta_id').value = data.peserta_id || '';
        jenisSelect.value = data.jenis || 'mentor';
        document.getElementById('periode').value = data.periode || '';
        document.getElementById('mentor_id').value = data.mentor_id || '';
        document.getElementById('pembimbing_id').value = data.pembimbing_id || '';
        document.getElementById('komponen').value = data.komponen || '';
        document.getElementById('bobot').value = data.bobot ?? 0;
        document.getElementById('nilai').value = data.nilai ?? 0;
        document.getElementById('nilai_akhir').value = data.nilai_akhir ?? 0;
        document.getElementById('status').value = data.status || 'draft';
        document.getElementById('catatan').value = data.catatan || '';
        applyRoleVisibility();
    }

    addButton.addEventListener('click', () => {
        resetForm();
        assessmentModal.show();
    });

    jenisSelect.addEventListener('change', applyRoleVisibility);

    document.querySelectorAll('[data-action="detail"]').forEach((button) => {
        button.addEventListener('click', () => {
            const data = JSON.parse(button.dataset.assessment);
            document.getElementById('detailContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted">Peserta</small><div class="fw-semibold">${data.peserta}</div><div class="text-muted">${data.nim} | ${data.kampus}</div></div></div>
                    <div class="col-md-6"><div class="border rounded p-3"><small class="text-muted">Penilai</small><div class="fw-semibold">${data.penilai}</div><div class="text-muted">${data.jenis_label}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Kehadiran</small><div class="fw-semibold">${data.komponen_presence}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Aktivitas</small><div class="fw-semibold">${data.komponen_activity}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Laporan</small><div class="fw-semibold">${data.komponen_report}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Sikap</small><div class="fw-semibold">${data.komponen_attitude}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Kompetensi</small><div class="fw-semibold">${data.komponen_competency}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Periode</small><div class="fw-semibold">${data.periode}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Status</small><div><span class="badge ${statusClass(data.status)}">${statusText(data.status)}</span></div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Bobot</small><div class="fw-semibold">${Number(data.bobot).toFixed(0)}%</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Nilai</small><div class="fw-semibold">${Number(data.nilai).toFixed(2)}</div></div></div>
                    <div class="col-md-4"><div class="border rounded p-3"><small class="text-muted">Nilai Akhir</small><div class="fw-semibold">${Number(data.nilai_akhir).toFixed(2)}</div></div></div>
                    <div class="col-12"><div class="border rounded p-3"><small class="text-muted">Catatan</small><div>${data.catatan || '-'}</div></div></div>
                </div>
            `;
            detailModal.show();
        });
    });

    document.querySelectorAll('[data-action="edit"]').forEach((button) => {
        button.addEventListener('click', () => {
            const data = JSON.parse(button.dataset.assessment);
            fillForm(data);
            assessmentModal.show();
        });
    });

    searchInput.addEventListener('input', filterRows);
    jenisFilter.addEventListener('change', filterRows);
    statusFilter.addEventListener('change', filterRows);
    perPageSelect.addEventListener('change', filterRows);
    resetButton.addEventListener('click', () => {
        searchInput.value = '';
        jenisFilter.value = 'semua';
        statusFilter.value = 'semua';
        perPageSelect.value = '10';
        filterRows();
        showToast('Filter penilaian berhasil direset.', 'info');
    });

    form.addEventListener('submit', () => {
        showToast('Penilaian sedang disimpan...', 'info');
    });

    filterRows();
    applyRoleVisibility();
});
</script>
@endpush
