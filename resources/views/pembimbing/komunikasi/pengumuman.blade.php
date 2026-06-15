@extends('pembimbing.layout.pembimbing')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')

@push('styles')
<style>
    .announcement-page .announcement-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .announcement-page .stat-card,
    .announcement-page .filter-card,
    .announcement-page .table-card,
    .announcement-page .side-panel { border:0; border-radius:8px; }
    .announcement-page .stat-card { cursor:pointer; transition:.2s ease; }
    .announcement-page .stat-card:hover,
    .announcement-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .announcement-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .announcement-page .announcement-item,
    .announcement-page .info-row,
    .announcement-page .schedule-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; margin-bottom:10px; background:#fbfdfe; }
    .announcement-page .announcement-item { cursor:pointer; transition:.2s ease; }
    .announcement-page .announcement-item:hover,
    .announcement-page .announcement-item.active { border-color:#2a8fbd; background:#e8f5fb; }
    .announcement-page .table { font-size:14px; }
    .announcement-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .announcement-page .table tbody tr:hover { background:#f7fcfe; }
    .announcement-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:380px; }
    .announcement-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .announcement-page .pagination .page-link { color:#2a8fbd; }
    .announcement-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $receivedItems = collect($receivedAnnouncementData ?? []);
    $sentItems = collect($sentAnnouncementData ?? []);
@endphp

<div class="container-fluid announcement-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.komunikasi') }}">Komunikasi</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
        </ol>
    </nav>

    <section class="announcement-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Pengumuman Magang</h3>
                <p class="mb-0">Kelola pengumuman yang diterima dari admin dan yang Anda kirim ke peserta, langsung dari database.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-light text-dark fs-6 me-2" id="announcementBadge">Real-time</span>
                <button class="btn btn-light" type="button" id="refreshButton"><i class="bi bi-arrow-clockwise"></i> Perbarui</button>
            </div>
        </div>
    </section>

    <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-megaphone-fill"></i>
        <div>Pengumuman pembimbing dimuat langsung dari database, termasuk yang dikirim admin dan yang Anda publikasikan ke peserta.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-megaphone"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="aktif">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Aktif</p><h3 class="mb-0" id="statActive">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-broadcast"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="terjadwal">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terjadwal</p><h3 class="mb-0" id="statScheduled">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-calendar2-event"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="berakhir">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Berakhir</p><h3 class="mb-0" id="statEnded">0</h3></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-archive"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Penerima</p><h3 class="mb-0" id="statReceiver">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Dibaca</p><h3 class="mb-0" id="statReadRate">0%</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-eye-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Pengumuman</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari judul atau isi pengumuman">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Pembimbing">Pembimbing</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Publikasi</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="terjadwal">Terjadwal</option>
                        <option value="berakhir">Berakhir</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="targetFilter">Tujuan</label>
                    <select class="form-select" id="targetFilter">
                        <option value="semua">Semua Tujuan</option>
                        <option value="Peserta">Peserta</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Publikasi</label>
                    <input class="form-control" id="dateFilter" type="date">
                </div>
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-primary" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-between flex-wrap gap-2">
                    <button class="btn btn-success" type="button" id="createButton"><i class="bi bi-plus-circle"></i> Buat Pengumuman</button>
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Pengumuman Diterima dari Admin</h5>
                        <small class="text-muted">Aksi: detail dan hapus.</small>
                    </div>
                    <span class="badge bg-warning text-dark" id="receivedCount">{{ $receivedItems->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Isi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="receivedTableBody">
                                @forelse ($receivedItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item['title'] }}</strong>
                                            <small class="text-muted d-block">{{ $item['author'] ?? '-' }}</small>
                                        </td>
                                        <td>{{ $item['date_label'] ?? $item['date'] ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item['status'] === 'aktif' ? 'success' : ($item['status'] === 'terjadwal' ? 'warning text-dark' : ($item['status'] === 'berakhir' ? 'dark' : 'secondary')) }}">
                                                {{ \Illuminate\Support\Str::title($item['status'] ?? 'draft') }}
                                            </span>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit((string) ($item['content'] ?? '-'), 60) }}</td>
                                        <td>
                                            <div class="action-group">
                                                <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-type="received" data-id="{{ $item['id'] }}">Detail</button>
                                                <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete" data-type="received" data-id="{{ $item['id'] }}">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="d-none" id="receivedServerEmpty"></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="receivedEmptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada pengumuman dari admin.</p></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card table-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-0">Pengumuman Dikirim ke Peserta</h5>
                        <small class="text-muted">Aksi: detail, edit, dan hapus.</small>
                    </div>
                    <button class="btn btn-warning btn-sm" type="button" id="addAnnouncementButton"><i class="bi bi-plus-circle"></i> Buat</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Isi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="sentTableBody">
                                @forelse ($sentItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item['title'] }}</strong>
                                            <small class="text-muted d-block">{{ $item['target'] }}</small>
                                        </td>
                                        <td>{{ $item['date_label'] ?? $item['date'] ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item['status'] === 'aktif' ? 'success' : ($item['status'] === 'terjadwal' ? 'warning text-dark' : ($item['status'] === 'berakhir' ? 'dark' : 'secondary')) }}">
                                                {{ \Illuminate\Support\Str::title($item['status'] ?? 'draft') }}
                                            </span>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit((string) ($item['content'] ?? '-'), 60) }}</td>
                                        <td>
                                            <div class="action-group">
                                                <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-type="sent" data-id="{{ $item['id'] }}">Detail</button>
                                                <button class="btn btn-sm btn-warning" type="button" data-action="edit" data-type="sent" data-id="{{ $item['id'] }}">Edit</button>
                                                <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete" data-type="sent" data-id="{{ $item['id'] }}">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="d-none" id="sentServerEmpty"></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="empty-state d-none" id="sentEmptyState">
                        <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Belum ada pengumuman yang dikirim ke peserta.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card panel-card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-1" id="detailTitle">Detail Pengumuman</h5>
                <small class="text-muted" id="detailSubtitle">Pilih pengumuman untuk melihat detail.</small>
            </div>
        </div>
        <div class="card-body" id="detailPanel">
            <div class="empty-state">
                <div>
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Pilih pengumuman di salah satu tabel untuk melihat detail.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Form Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="announcementId">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="titleInput">Judul Pengumuman</label>
                        <input class="form-control" id="titleInput" type="text">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="dateInput">Tanggal Publikasi</label>
                        <input class="form-control" id="dateInput" type="date">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="priorityInput">Prioritas</label>
                        <select class="form-select" id="priorityInput">
                            <option>Tinggi</option>
                            <option>Sedang</option>
                            <option>Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="statusInput">Status Publikasi</label>
                        <select class="form-select" id="statusInput">
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="terjadwal">Terjadwal</option>
                            <option value="berakhir">Berakhir</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="scheduleInput">Jadwal Tayang</label>
                        <input class="form-control" id="scheduleInput" type="datetime-local">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="contentInput">Isi Singkat</label>
                        <textarea class="form-control" id="contentInput" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-outline-warning" id="draftButton">Simpan Draft</button>
                <button type="button" class="btn btn-warning" id="saveAnnouncementButton">Publikasikan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Pengumuman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmContent">Ringkasan Isi</label>
                <textarea class="form-control" id="confirmContent" rows="3" placeholder="Tulis atau ubah ringkasan pengumuman"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan/Publikasikan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="announcementToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Pengumuman diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const receivedAnnouncements = (@json($receivedAnnouncementData ?? []) || []).map((item) => ({
        id: Number(item.id),
        title: String(item.title || 'Pengumuman'),
        category: String(item.category || 'Pembimbing'),
        target: String(item.target || 'Peserta'),
        date: String(item.date || ''),
        date_label: String(item.date_label || '-'),
        status: String(item.status || 'draft'),
        read: Number(item.read || 0),
        priority: String(item.priority || 'Rendah'),
        schedule: String(item.schedule || ''),
        schedule_label: String(item.schedule_label || '-'),
        content: String(item.content || '-'),
        author: String(item.author || '-'),
        type: 'received',
    }));
    const sentAnnouncements = (@json($sentAnnouncementData ?? []) || []).map((item) => ({
        id: Number(item.id),
        title: String(item.title || 'Pengumuman'),
        category: String(item.category || 'Pembimbing'),
        target: String(item.target || 'Peserta'),
        date: String(item.date || ''),
        date_label: String(item.date_label || '-'),
        status: String(item.status || 'draft'),
        read: Number(item.read || 0),
        priority: String(item.priority || 'Rendah'),
        schedule: String(item.schedule || ''),
        schedule_label: String(item.schedule_label || '-'),
        content: String(item.content || '-'),
        author: String(item.author || '-'),
        type: 'sent',
    }));

    let received = [...receivedAnnouncements];
    let sent = [...sentAnnouncements];
    let selectedType = received[0] ? 'received' : (sent[0] ? 'sent' : null);
    let selectedId = received[0]?.id || sent[0]?.id || null;
    let pendingAction = null;
    let editingId = null;

    const modal = new bootstrap.Modal(document.getElementById('announcementModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('announcementToast'));
    const csrfToken = @json(csrf_token());
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const targetFilter = document.getElementById('targetFilter');
    const dateFilter = document.getElementById('dateFilter');
    const applyFilterButton = document.getElementById('applyFilter');
    const resetFilterButton = document.getElementById('resetFilter');
    const refreshButton = document.getElementById('refreshButton');
    const createButton = document.getElementById('createButton');
    const addAnnouncementButton = document.getElementById('addAnnouncementButton');
    const bannerCreateButton = document.getElementById('addAnnouncementBannerButton');

    const titleCase = (text) => String(text || '').replace(/\b\w/g, (char) => char.toUpperCase());
    const statusBadge = (status) => {
        const map = { aktif: 'success', draft: 'secondary', terjadwal: 'warning text-dark', berakhir: 'dark' };
        return `<span class="badge bg-${map[status] || 'secondary'}">${titleCase(status)}</span>`;
    };
    const priorityBadge = (priority) => {
        const map = { Tinggi: 'danger', Sedang: 'warning text-dark', Rendah: 'secondary' };
        return `<span class="badge bg-${map[priority] || 'secondary'}">${priority}</span>`;
    };

    const normalized = (value) => String(value || '').trim().toLowerCase();

    const visibleAnnouncements = (items) => {
        const keyword = normalized(searchInput.value);
        const category = normalized(categoryFilter.value);
        const status = normalized(statusFilter.value);
        const target = normalized(targetFilter.value);
        const date = normalized(dateFilter.value);

        return items.filter((item) => {
            const itemCategory = normalized(item.category);
            const itemStatus = normalized(item.status);
            const itemTarget = normalized(item.target);
            const itemDate = normalized(item.date);
            const haystack = normalized([item.title, item.content, item.author, item.target, item.category].join(' '));

            return (
                (!keyword || haystack.includes(keyword)) &&
                (category === 'semua' || itemCategory === category) &&
                (status === 'semua' || itemStatus === status) &&
                (target === 'semua' || itemTarget === target) &&
                (!date || itemDate === date)
            );
        });
    };

    const selectedItem = () => {
        if (selectedType === 'received') return received.find((item) => item.id === selectedId) || received[0] || null;
        if (selectedType === 'sent') return sent.find((item) => item.id === selectedId) || sent[0] || null;
        return null;
    };

    const updateStats = () => {
        const all = [...received, ...sent];
        const totalRead = all.reduce((sum, item) => sum + Number(item.read || 0), 0);
        const totalRecipient = all.reduce((sum, item) => sum + Math.max(Number(item.read || 0), 1), 0);
        document.getElementById('statTotal').textContent = all.length;
        document.getElementById('statActive').textContent = all.filter((item) => item.status === 'aktif').length;
        document.getElementById('statScheduled').textContent = all.filter((item) => item.status === 'terjadwal').length;
        document.getElementById('statEnded').textContent = all.filter((item) => item.status === 'berakhir').length;
        document.getElementById('statReceiver').textContent = all.reduce((sum, item) => sum + Number(item.target ? 1 : 0), 0);
        document.getElementById('statReadRate').textContent = `${Math.min(100, Math.round((totalRead / Math.max(1, totalRecipient)) * 100))}%`;
        document.getElementById('receivedCount').textContent = received.length;
        document.getElementById('sentCount')?.textContent = sent.length;
    };

    const apiRequest = async (url, method, body = null) => {
        const response = await fetch(url, {
            method,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                Accept: 'application/json',
                'Content-Type': 'application/json',
            },
            body: body ? JSON.stringify(body) : null,
        });
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(payload.message || 'Permintaan gagal diproses.');
        return payload;
    };

    const renderDetail = () => {
        const item = selectedItem();
        if (!item) {
            document.getElementById('detailTitle').textContent = 'Detail Pengumuman';
            document.getElementById('detailSubtitle').textContent = 'Belum ada pengumuman.';
            document.getElementById('detailPanel').innerHTML = '<div class="empty-state"><div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Belum ada pengumuman untuk ditampilkan.</p></div></div>';
            return;
        }
        document.getElementById('detailTitle').textContent = item.title;
        document.getElementById('detailSubtitle').textContent = `${item.category} - ${item.target}`;
        document.getElementById('detailPanel').innerHTML = `
            <div class="detail-box">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                    ${statusBadge(item.status)}
                    ${priorityBadge(item.priority)}
                </div>
                <p class="mb-0">${item.content}</p>
            </div>
            <div class="row g-2">
                <div class="col-md-6"><div class="detail-box"><small class="text-muted">Tanggal Publikasi</small><div class="fw-semibold">${item.date_label || item.date}</div></div></div>
                <div class="col-md-6"><div class="detail-box"><small class="text-muted">Jadwal Tayang</small><div class="fw-semibold">${item.schedule_label || item.schedule || '-'}</div></div></div>
                <div class="col-md-6"><div class="detail-box"><small class="text-muted">Jumlah Dibaca</small><div class="fw-semibold">${item.read}</div></div></div>
                <div class="col-md-6"><div class="detail-box"><small class="text-muted">Target</small><div class="fw-semibold">${item.target}</div></div></div>
            </div>
        `;
    };

    const renderReceived = () => {
        const body = document.getElementById('receivedTableBody');
        const empty = document.getElementById('receivedEmptyState');
        const rows = visibleAnnouncements(received);
        body.innerHTML = rows.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.title}</strong><small class="text-muted d-block">${item.author || '-'}</small></td>
                <td>${item.date_label || item.date}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${String(item.content || '-').slice(0, 60)}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-type="received" data-id="${item.id}">Detail</button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete" data-type="received" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');
        empty.classList.toggle('d-none', rows.length > 0);
    };

    const renderSent = () => {
        const body = document.getElementById('sentTableBody');
        const empty = document.getElementById('sentEmptyState');
        const rows = visibleAnnouncements(sent);
        body.innerHTML = rows.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.title}</strong><small class="text-muted d-block">${item.target}</small></td>
                <td>${item.date_label || item.date}</td>
                <td>${statusBadge(item.status)}</td>
                <td>${String(item.content || '-').slice(0, 60)}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-sm btn-outline-dark" type="button" data-action="detail" data-type="sent" data-id="${item.id}">Detail</button>
                        <button class="btn btn-sm btn-warning" type="button" data-action="edit" data-type="sent" data-id="${item.id}">Edit</button>
                        <button class="btn btn-sm btn-outline-danger" type="button" data-action="delete" data-type="sent" data-id="${item.id}">Hapus</button>
                    </div>
                </td>
            </tr>
        `).join('');
        empty.classList.toggle('d-none', rows.length > 0);
    };

    const renderAll = () => {
        updateStats();
        renderReceived();
        renderSent();
        renderDetail();
    };

    const openForm = (item = null) => {
        editingId = item?.id || null;
        document.getElementById('announcementId').value = item?.id || '';
        document.getElementById('titleInput').value = item?.title || '';
        document.getElementById('dateInput').value = item?.date || new Date().toISOString().slice(0, 10);
        document.getElementById('priorityInput').value = item?.priority || 'Sedang';
        document.getElementById('statusInput').value = item?.status || 'draft';
        document.getElementById('scheduleInput').value = item?.schedule || '';
        document.getElementById('contentInput').value = item?.content || '';
        modal.show();
    };

    const readForm = (statusOverride) => ({
        id: Number(document.getElementById('announcementId').value) || Date.now(),
        title: document.getElementById('titleInput').value.trim() || 'Pengumuman Baru',
        date: document.getElementById('dateInput').value || new Date().toISOString().slice(0, 10),
        status: statusOverride || document.getElementById('statusInput').value,
        priority: document.getElementById('priorityInput').value,
        schedule: document.getElementById('scheduleInput').value || '',
        content: document.getElementById('contentInput').value.trim() || 'Isi pengumuman belum diisi.',
    });

    addAnnouncementButton.addEventListener('click', () => openForm(null));
    bannerCreateButton.addEventListener('click', () => openForm(null));
    createButton?.addEventListener('click', () => openForm(null));

    document.getElementById('draftButton').addEventListener('click', () => {
        const payload = readForm('draft');
        pendingAction = async () => {
            const endpoint = editingId ? `{{ url('/pembimbing/komunikasi/pengumuman') }}/${editingId}` : `{{ url('/pembimbing/komunikasi/pengumuman') }}`;
            const method = editingId ? 'PATCH' : 'POST';
            const response = await apiRequest(endpoint, method, payload);
            const announcement = response.announcement;
            const index = sent.findIndex((row) => row.id === announcement.id);
            if (index >= 0) sent[index] = announcement;
            else sent.unshift(announcement);
            selectedType = 'sent';
            selectedId = announcement.id;
            editingId = null;
        };
        document.getElementById('confirmContent').value = payload.content;
        document.getElementById('confirmSummary').innerHTML = `<div class="border rounded p-3"><strong>${payload.title}</strong><small class="text-muted d-block">Simpan sebagai draft</small></div>`;
        confirmModal.show();
    });

    document.getElementById('saveAnnouncementButton').addEventListener('click', () => {
        const payload = readForm('aktif');
        pendingAction = async () => {
            const endpoint = editingId ? `{{ url('/pembimbing/komunikasi/pengumuman') }}/${editingId}` : `{{ url('/pembimbing/komunikasi/pengumuman') }}`;
            const method = editingId ? 'PATCH' : 'POST';
            const response = await apiRequest(endpoint, method, payload);
            const announcement = response.announcement;
            const index = sent.findIndex((row) => row.id === announcement.id);
            if (index >= 0) sent[index] = announcement;
            else sent.unshift(announcement);
            selectedType = 'sent';
            selectedId = announcement.id;
            editingId = null;
        };
        document.getElementById('confirmContent').value = payload.content;
        document.getElementById('confirmSummary').innerHTML = `<div class="border rounded p-3"><strong>${payload.title}</strong><small class="text-muted d-block">Publikasikan ke peserta</small></div>`;
        confirmModal.show();
    });

    document.getElementById('receivedTableBody').addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = received.find((row) => row.id === Number(button.dataset.id));
        if (!item) return;
        selectedType = 'received';
        selectedId = item.id;
        if (button.dataset.action === 'detail') {
            renderDetail();
            return;
        }
        if (button.dataset.action === 'delete') {
            pendingAction = async () => {
                await apiRequest(`{{ url('/pembimbing/komunikasi/pengumuman') }}/${item.id}`, 'DELETE');
                received = received.filter((row) => row.id !== item.id);
                selectedId = received[0]?.id || sent[0]?.id || null;
            };
            document.getElementById('confirmContent').value = item.content;
            document.getElementById('confirmSummary').innerHTML = `<div class="border rounded p-3"><strong>${item.title}</strong><small class="text-muted d-block">Hapus dari daftar diterima</small></div>`;
            confirmModal.show();
        }
    });

    document.getElementById('sentTableBody').addEventListener('click', async (event) => {
        const button = event.target.closest('button[data-action]');
        if (!button) return;
        const item = sent.find((row) => row.id === Number(button.dataset.id));
        if (!item) return;
        selectedType = 'sent';
        selectedId = item.id;
        if (button.dataset.action === 'detail') {
            renderDetail();
            return;
        }
        if (button.dataset.action === 'edit') {
            openForm(item);
            return;
        }
        if (button.dataset.action === 'delete') {
            pendingAction = async () => {
                await apiRequest(`{{ url('/pembimbing/komunikasi/pengumuman') }}/${item.id}`, 'DELETE');
                sent = sent.filter((row) => row.id !== item.id);
                selectedId = sent[0]?.id || received[0]?.id || null;
            };
            document.getElementById('confirmContent').value = item.content;
            document.getElementById('confirmSummary').innerHTML = `<div class="border rounded p-3"><strong>${item.title}</strong><small class="text-muted d-block">Hapus pengumuman</small></div>`;
            confirmModal.show();
        }
    });

    document.getElementById('confirmAction').addEventListener('click', async () => {
        if (!pendingAction) return;
        try {
            await pendingAction();
            pendingAction = null;
            confirmModal.hide();
            modal.hide();
            renderAll();
        } catch (error) {
            document.getElementById('toastMessage').textContent = error.message || 'Gagal memproses pengumuman.';
            toast.show();
        }
    });

    applyFilterButton.addEventListener('click', () => renderAll());
    resetFilterButton.addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = 'semua';
        statusFilter.value = 'semua';
        targetFilter.value = 'semua';
        dateFilter.value = '';
        selectedType = 'received';
        selectedId = received[0]?.id || sent[0]?.id || null;
        renderAll();
    });
    [searchInput, categoryFilter, statusFilter, targetFilter, dateFilter].forEach((element) => {
        element.addEventListener('change', () => renderAll());
    });
    searchInput.addEventListener('keyup', (event) => {
        if (event.key === 'Enter') renderAll();
    });

    document.querySelectorAll('.stat-card[data-status-card]').forEach((card) => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach((item) => item.classList.remove('active'));
            card.classList.add('active');
            renderAll();
        });
    });

    refreshButton.addEventListener('click', () => {
        document.getElementById('announcementBadge').textContent = 'Diperbarui';
        renderAll();
    });

    renderAll();
});
</script>
@endpush
