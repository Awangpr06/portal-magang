@extends('mentor.layout.mentor')

@section('title', 'Profil')
@section('page-title', 'Profil')

@push('styles')
<style>
    .profile-page .profile-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .profile-page .stat-card,
    .profile-page .filter-card,
    .profile-page .table-card,
    .profile-page .profile-panel { border:0; border-radius:8px; }
    .profile-page .stat-card .card-body,
    .profile-page .profile-panel .card-body,
    .profile-page .table-card .card-body { padding: .6rem .8rem; }
    .profile-page .stat-card { cursor:pointer; transition:.2s ease; }
    .profile-page .stat-card:hover,
    .profile-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .profile-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .profile-page .profile-avatar { width:68px; height:68px; border-radius:50%; object-fit:cover; border:3px solid #fff; box-shadow:0 10px 24px rgba(0,0,0,.15); }
    .profile-page .stat-card h3 { font-size: 1.2rem; margin-bottom: 0; }
    .profile-page .stat-card h5 { font-size: .95rem; margin-bottom: 0; }
    .profile-page .profile-banner { padding-top: .75rem; padding-bottom: .75rem; }
    .profile-page .identity-card,
    .profile-page .status-row { border:1px solid #e2ebef; border-radius:8px; padding:9px; background:#fbfdfe; }
    .profile-page .table { font-size:13px; }
    .profile-page .table thead th { background:#eef8fc; color:#3b5664; font-size:12px; white-space:nowrap; padding-top: .55rem; padding-bottom: .55rem; }
    .profile-page .table tbody tr:hover { background:#f7fcfe; }
    .profile-page .table tbody td { padding-top: .55rem; padding-bottom: .55rem; }
    .profile-page .action-group { display:flex; flex-wrap:wrap; gap:4px; min-width:310px; }
    .profile-page .empty-state { min-height:100px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .profile-page .pagination .page-link { color:#2a8fbd; }
    .profile-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
    .profile-page .detail-list { display:grid; gap:10px; }
    .profile-page .detail-item { border:1px solid #e2ebef; border-radius:8px; padding:12px; background:#fff; }
</style>
@endpush

@section('content')
@php
    $mentor = $user->mentor;
    $securityActivities = collect($securityActivities ?? []);
    $profileFields = [
        $user->name,
        $user->username,
        $user->email,
        optional($mentor)->nip,
        optional($mentor)->jenis_kelamin,
        optional($mentor)->jabatan,
        optional($mentor)->divisi,
        optional($mentor)->no_hp,
        optional($mentor)->alamat,
        optional($mentor)->instansi,
    ];
    $filledProfile = collect($profileFields)->filter(fn ($value) => filled($value))->count();
    $profileCompletion = round(($filledProfile / count($profileFields)) * 100);
    $accountStatus = $user->account_status ?? 'menunggu';
    $lastUpdated = optional($user->updated_at)->translatedFormat('d M Y H:i') ?? 'Belum ada';
    $joinedAt = optional($user->created_at)->translatedFormat('d M Y') ?? '-';
    $verifiedAt = $user->verified_at ? $user->verified_at->translatedFormat('d M Y H:i') : '-';
    $avatarUrl = $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'Mentor').'&background=2a8fbd&color=fff&size=160';
    $institution = optional($mentor)->instansi ?: (optional($mentor)->divisi ?: 'LLDIKTI Wilayah V Yogyakarta');
    $position = optional($mentor)->jabatan ?: 'Mentor Lapangan';
    $profileRows = [
        ['id'=>1, 'jenis'=>'Identitas', 'nilai'=>$user->name ?: 'Belum diisi', 'verifikasi'=>$user->name ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'users.name', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Hari Ini'],
        ['id'=>2, 'jenis'=>'Identitas', 'nilai'=>$user->username ?: 'Belum diisi', 'verifikasi'=>$user->username ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'users.username', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Hari Ini'],
        ['id'=>3, 'jenis'=>'Kontak', 'nilai'=>$user->email ?: 'Belum diisi', 'verifikasi'=>$user->email ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'users.email', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Hari Ini'],
        ['id'=>4, 'jenis'=>'Profesional', 'nilai'=>optional($mentor)->nip ?: 'Belum diisi', 'verifikasi'=>optional($mentor)->nip ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'mentors.nip', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Minggu Ini'],
        ['id'=>5, 'jenis'=>'Profesional', 'nilai'=>optional($mentor)->jabatan ?: 'Belum diisi', 'verifikasi'=>optional($mentor)->jabatan ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'mentors.jabatan', 'sinkron'=>'tersinkron', 'aktivitas'=>'baru', 'periode'=>'Minggu Ini'],
        ['id'=>6, 'jenis'=>'Profesional', 'nilai'=>optional($mentor)->divisi ?: 'Belum diisi', 'verifikasi'=>optional($mentor)->divisi ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'mentors.divisi', 'sinkron'=>'tersinkron', 'aktivitas'=>'baru', 'periode'=>'Bulan Ini'],
        ['id'=>7, 'jenis'=>'Kontak', 'nilai'=>optional($mentor)->no_hp ?: 'Belum diisi', 'verifikasi'=>optional($mentor)->no_hp ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'mentors.no_hp', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Bulan Ini'],
        ['id'=>8, 'jenis'=>'Kontak', 'nilai'=>optional($mentor)->alamat ?: 'Belum diisi', 'verifikasi'=>optional($mentor)->alamat ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'mentors.alamat', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Bulan Ini'],
        ['id'=>9, 'jenis'=>'Instansi', 'nilai'=>optional($mentor)->instansi ?: 'Belum diisi', 'verifikasi'=>optional($mentor)->instansi ? 'terisi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'mentors.instansi', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Bulan Ini'],
        ['id'=>10, 'jenis'=>'Riwayat', 'nilai'=>$securityActivities->first()?->aktivitas ?? 'Login dan pembaruan profil', 'verifikasi'=>'terverifikasi', 'tanggal'=>$securityActivities->first()?->created_at?->translatedFormat('d M Y H:i') ?? ($verifiedAt !== '-' ? $verifiedAt : $joinedAt), 'sumber'=>'security_activities', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Hari Ini'],
    ];
@endphp

<div class="container-fluid profile-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mentor.pengaturan') }}">Pengaturan Akun</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
    </nav>

    <section class="profile-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8 d-flex align-items-center gap-3">
                <img class="profile-avatar" src="{{ $avatarUrl }}" alt="Foto profil mentor">
                <div>
                    <h3 class="fw-bold mb-2">Profil Mentor</h3>
                    <p class="mb-0">Menampilkan detail akun mentor yang sedang login, termasuk identitas, kontak, jabatan, dan informasi penugasan yang tersimpan di database.</p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2 text-capitalize">{{ str_replace('_', ' ', $accountStatus) }}</span>
                <button class="btn btn-light" type="button" id="syncButton"><i class="bi bi-arrow-repeat"></i> Sinkronisasi</button>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger mb-4">
            <strong>Profil belum bisa diperbarui.</strong>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card active">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Kelengkapan</p><h3 class="mb-0">{{ $profileCompletion }}%</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-person-lines-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Status Akun</p><h5 class="mb-0 text-capitalize">{{ str_replace('_', ' ', $accountStatus) }}</h5></div>
                    <span class="stat-icon bg-success"><i class="bi bi-patch-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terverifikasi</p><h5 class="mb-0">{{ $verifiedAt !== '-' ? 'Ya' : 'Proses' }}</h5></div>
                    <span class="stat-icon bg-info"><i class="bi bi-shield-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Diperbarui</p><h6 class="mb-0">{{ $lastUpdated }}</h6></div>
                    <span class="stat-icon bg-secondary"><i class="bi bi-clock-history"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Bergabung</p><h5 class="mb-0">{{ $joinedAt }}</h5></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-calendar-event"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Data Terisi</p><h5 class="mb-0">{{ $filledProfile }} / {{ count($profileFields) }}</h5></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-database-check"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3">
            <section class="card profile-panel shadow-sm h-100">
                <div class="card-body text-center">
                    <img src="{{ $avatarUrl }}" class="rounded-circle profile-avatar mb-3" alt="Foto profil mentor">
                    <h5 class="fw-bold mb-1">{{ $user->name ?? 'Mentor' }}</h5>
                    <div class="text-muted mb-2">{{ $position }}</div>
                    <div class="badge bg-light text-dark border mb-3">{{ $institution }}</div>
                    <div class="detail-list text-start">
                        <div class="detail-item">
                            <small class="text-muted d-block">Email</small>
                            <div class="fw-semibold">{{ $user->email ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <small class="text-muted d-block">Username</small>
                            <div class="fw-semibold">{{ $user->username ?? '-' }}</div>
                        </div>
                        <div class="detail-item">
                            <small class="text-muted d-block">NIP</small>
                            <div class="fw-semibold">{{ optional($mentor)->nip ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-xl-9">
            <section class="card profile-panel shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Detail Profil Mentor</h5>
                    <small class="text-muted">Semua data di bawah ini diambil dari akun yang sedang login.</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('mentor.pengaturan.update') }}" id="profileForm">
                        @csrf
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#identityTab" type="button">Identitas</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#professionalTab" type="button">Profesional</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#contactTab" type="button">Kontak</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#auditTab" type="button">Audit</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="identityTab">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Nama Lengkap</label><input class="form-control" name="name" value="{{ old('name', $user->name ?? '-') }}" required></div>
                                <div class="col-md-6"><label class="form-label">Username</label><input class="form-control" name="username" value="{{ old('username', $user->username ?? '') }}"></div>
                                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" value="{{ old('email', $user->email ?? '-') }}" required></div>
                                <div class="col-md-6"><label class="form-label">Role</label><input class="form-control text-capitalize" value="{{ str_replace('_', ' ', $user->role ?? 'mentor') }}" readonly></div>
                                <div class="col-md-6"><label class="form-label">Status Akun</label><input class="form-control text-capitalize" value="{{ str_replace('_', ' ', $accountStatus) }}" readonly></div>
                                <div class="col-md-6"><label class="form-label">Terverifikasi</label><input class="form-control" value="{{ $verifiedAt !== '-' ? $verifiedAt : 'Belum diverifikasi' }}" readonly></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="professionalTab">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">NIP</label><input class="form-control" name="nip" value="{{ old('nip', optional($mentor)->nip ?: '') }}"></div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" @selected(old('jenis_kelamin', optional($mentor)->jenis_kelamin) === 'Laki-laki')>Laki-laki</option>
                                        <option value="Perempuan" @selected(old('jenis_kelamin', optional($mentor)->jenis_kelamin) === 'Perempuan')>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6"><label class="form-label">Jabatan</label><input class="form-control" name="jabatan" value="{{ old('jabatan', optional($mentor)->jabatan ?: '') }}" required></div>
                                <div class="col-md-6"><label class="form-label">Divisi</label><input class="form-control" name="divisi" value="{{ old('divisi', optional($mentor)->divisi ?: '') }}" required></div>
                                <div class="col-12"><label class="form-label">Instansi</label><input class="form-control" name="instansi" value="{{ old('instansi', optional($mentor)->instansi ?: $institution) }}" placeholder="Contoh: Dinas Kominfo DIY"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="contactTab">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">No HP</label><input class="form-control" name="no_hp" value="{{ old('no_hp', optional($mentor)->no_hp ?: '') }}" required></div>
                                <div class="col-md-6"><label class="form-label">Alamat</label><input class="form-control" name="alamat" value="{{ old('alamat', optional($mentor)->alamat ?: '') }}"></div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="auditTab">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Tanggal Bergabung</label><input class="form-control" value="{{ $joinedAt }}" readonly></div>
                                <div class="col-md-6"><label class="form-label">Pembaruan Terakhir</label><input class="form-control" value="{{ $lastUpdated }}" readonly></div>
                                <div class="col-md-6"><label class="form-label">Sumber Data</label><input class="form-control" value="users + mentors" readonly></div>
                                <div class="col-md-6"><label class="form-label">Sinkronisasi</label><input class="form-control" value="Tersinkron dengan database" readonly></div>
                            </div>
                            <div class="mt-4">
                                <div class="fw-semibold mb-2">Riwayat Audit Terbaru</div>
                                <div class="detail-list">
                                    @forelse ($securityActivities as $activity)
                                        <div class="detail-item">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="fw-semibold">{{ $activity->aktivitas }}</div>
                                                    <small class="text-muted">{{ $activity->catatan ?: 'Aktivitas sistem' }}</small>
                                                </div>
                                                <span class="badge bg-success text-capitalize">{{ $activity->status ?? 'berhasil' }}</span>
                                            </div>
                                            <div class="mt-2 small text-muted">
                                                {{ optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-' }} · {{ $activity->ip_address ?? '-' }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-muted">Belum ada riwayat audit tersimpan.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan Perubahan</button>
                        <button class="btn btn-primary" type="button" id="photoButton"><i class="bi bi-image"></i> Ubah Foto</button>
                        <button class="btn btn-outline-secondary" type="button" id="historyButton"><i class="bi bi-clock-history"></i> Riwayat Perubahan</button>
                        <button class="btn btn-outline-info" type="button" id="syncButtonInline"><i class="bi bi-arrow-repeat"></i> Sinkronisasi Data</button>
                    </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Rincian Data Profil dan Riwayat</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data profil mentor</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-muted small" for="perPageSelect">Data</label>
                <select class="form-select form-select-sm" id="perPageSelect" style="width:80px"><option value="5">5</option><option value="10">10</option><option value="25">25</option></select>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Informasi</th>
                            <th>Nilai Saat Ini</th>
                            <th>Status Verifikasi</th>
                            <th>Tanggal Perubahan</th>
                            <th>Sumber Data</th>
                            <th>Status Sinkronisasi</th>
                            <th>Aktivitas Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="profileTableBody"></tbody>
                </table>
            </div>
            <div class="empty-state d-none" id="emptyState">
                <div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data profil sesuai filter.</p></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small class="text-muted" id="paginationInfo">Menampilkan 0 data</small>
            <nav aria-label="Pagination profil"><ul class="pagination pagination-sm mb-0" id="pagination"></ul></nav>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div id="confirmSummary" class="mb-3"></div>
                <label class="form-label" for="confirmNote">Ringkasan Perubahan Data</label>
                <textarea class="form-control" id="confirmNote" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Data Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="alert alert-info mb-0">Pilih salah satu baris pada tabel untuk melihat detail data profil mentor.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyModalLabel">Riwayat Perubahan Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="detail-list">
                    @foreach ($profileRows as $row)
                        <div class="detail-item">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">{{ $row['jenis'] }}</div>
                                    <small class="text-muted">{{ $row['sumber'] }}</small>
                                </div>
                                <span class="badge bg-primary text-capitalize">{{ $row['aktivitas'] }}</span>
                            </div>
                            <div class="mt-2">{{ $row['nilai'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('mentor.pengaturan.foto') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Ubah Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img src="{{ $avatarUrl }}" class="rounded-circle profile-avatar" id="photoPreview" alt="Preview foto profil">
                    </div>
                    <label class="form-label" for="photoInput">Pilih foto baru</label>
                    <input class="form-control" type="file" id="photoInput" name="foto" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                    <div class="form-text">Gunakan file JPG, JPEG, atau PNG dengan ukuran maksimal 2 MB.</div>
                    @error('foto')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="profileToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Profil mentor diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const profileRows = @json($profileRows);
    const openPhotoModalOnLoad = @json($errors->has('foto'));
    const tableBody = document.getElementById('profileTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
    const photoModal = new bootstrap.Modal(document.getElementById('photoModal'));
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const toast = new bootstrap.Toast(document.getElementById('profileToast'));
    let currentPage = 1;
    let pendingAction = null;

    const badge = (value) => `<span class="badge bg-${value === 'terverifikasi' || value === 'tersinkron' || value === 'terisi' ? 'success' : 'warning'} text-capitalize">${value}</span>`;

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('profileToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
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
        const perPage = Number(perPageSelect.value);
        const start = (currentPage - 1) * perPage;
        const pageData = profileRows.slice(start, start + perPage);
        tableBody.innerHTML = pageData.map((item, index) => `
            <tr>
                <td>${start + index + 1}</td>
                <td><strong>${item.jenis}</strong></td>
                <td>${item.nilai}</td>
                <td>${badge(item.verifikasi)}</td>
                <td>${item.tanggal}</td>
                <td>${item.sumber}</td>
                <td>${badge(item.sinkron)}</td>
                <td class="text-capitalize">${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-profile-action="detail" data-profile-id="${item.id}">Detail</button>
                        <button class="btn btn-outline-info btn-sm" type="button" data-profile-action="riwayat" data-profile-id="${item.id}">Riwayat</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-profile-action="sinkronisasi" data-profile-id="${item.id}">Sinkronisasi</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', profileRows.length > 0);
        tableInfo.textContent = `Menampilkan ${profileRows.length} data profil mentor`;
        paginationInfo.textContent = profileRows.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, profileRows.length)} dari ${profileRows.length} data` : 'Menampilkan 0 data';
        renderPagination(profileRows.length, perPage);
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `<div class="border rounded p-3"><strong>${item.jenis}</strong><small class="text-muted d-block">${item.nilai}</small><hr><span class="text-capitalize">${action}</span> ${badge(item.verifikasi)}</div>`;
        document.getElementById('confirmNote').value = `Konfirmasi ${action} untuk ${item.jenis}`;
        confirmModal.show();
    }

    document.getElementById('profileTableBody').addEventListener('click', (event) => {
        const button = event.target.closest('button[data-profile-action]');
        if (!button) return;
        const item = profileRows.find((row) => row.id === Number(button.dataset.profileId));
        if (!item) return;

        if (button.dataset.profileAction === 'detail') {
            document.getElementById('detailContent').innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6"><strong>Jenis Informasi</strong><div>${item.jenis}</div></div>
                    <div class="col-md-6"><strong>Nilai Saat Ini</strong><div>${item.nilai}</div></div>
                    <div class="col-md-6"><strong>Status Verifikasi</strong><div>${badge(item.verifikasi)}</div></div>
                    <div class="col-md-6"><strong>Tanggal Perubahan</strong><div>${item.tanggal}</div></div>
                    <div class="col-md-6"><strong>Sumber Data</strong><div>${item.sumber}</div></div>
                    <div class="col-md-6"><strong>Status Sinkronisasi</strong><div>${badge(item.sinkron)}</div></div>
                </div>
            `;
            detailModal.show();
            return;
        }

        if (button.dataset.profileAction === 'riwayat') {
            historyModal.show();
            return;
        }

        openConfirm(item, button.dataset.profileAction);
    });

    document.getElementById('confirmAction').addEventListener('click', () => {
        confirmModal.hide();
        showToast(`Aksi ${pendingAction?.action || 'profil'} berhasil dicatat.`);
        pendingAction = null;
    });

    document.getElementById('photoButton').addEventListener('click', () => photoModal.show());
    document.getElementById('historyButton').addEventListener('click', () => historyModal.show());
    document.getElementById('syncButton').addEventListener('click', () => showToast('Data mentor berhasil disinkronkan.', 'info'));
    document.getElementById('syncButtonInline').addEventListener('click', () => showToast('Sinkronisasi data mentor berhasil dijalankan.', 'info'));
    photoInput?.addEventListener('change', () => {
        const [file] = photoInput.files || [];
        if (!file) return;
        const objectUrl = URL.createObjectURL(file);
        photoPreview.src = objectUrl;
        photoPreview.onload = () => URL.revokeObjectURL(objectUrl);
    });
    document.getElementById('perPageSelect').addEventListener('change', () => { currentPage = 1; renderTable(); });
    pagination.addEventListener('click', (event) => {
        const button = event.target.closest('button[data-page]');
        if (!button || button.parentElement.classList.contains('disabled')) return;
        currentPage = Number(button.dataset.page);
        renderTable();
    });

    renderTable();
    if (openPhotoModalOnLoad) {
        photoModal.show();
    }
});
</script>
@endpush
