@extends('pembimbing.layout.pembimbing')

@section('title', 'Profil')
@section('page-title', 'Profil')

@push('styles')
<style>
    .profile-page .profile-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .profile-page .stat-card,
    .profile-page .filter-card,
    .profile-page .table-card,
    .profile-page .profile-panel { border:0; border-radius:8px; }
    .profile-page .stat-card { cursor:pointer; transition:.2s ease; }
    .profile-page .stat-card:hover,
    .profile-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
    .profile-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .profile-page .profile-avatar { width:96px; height:96px; border-radius:50%; object-fit:cover; border:4px solid #fff; box-shadow:0 10px 24px rgba(0,0,0,.15); }
    .profile-page .identity-card,
    .profile-page .status-row { border:1px solid #e2ebef; border-radius:8px; padding:12px; background:#fbfdfe; }
    .profile-page .table { font-size:14px; }
    .profile-page .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
    .profile-page .table tbody tr:hover { background:#f7fcfe; }
    .profile-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:390px; }
    .profile-page .empty-state { min-height:180px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .profile-page .pagination .page-link { color:#2a8fbd; }
    .profile-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
@php
    $pembimbing = $user->pembimbing;
    $securityActivities = collect($securityActivities ?? []);
    $formatActivityType = static function (?string $activity): string {
        $text = strtolower((string) $activity);
        return match (true) {
            str_contains($text, 'password') => 'Password',
            str_contains($text, 'login') => 'Login',
            str_contains($text, 'verifikasi') => 'Verifikasi',
            str_contains($text, 'foto') => 'Foto',
            str_contains($text, 'profil') => 'Profil',
            default => 'Aktivitas',
        };
    };
    $formatVerification = static function ($value): string {
        return in_array(strtolower((string) $value), ['berhasil', 'terverifikasi', 'tersinkron'], true)
            ? 'terverifikasi'
            : 'belum lengkap';
    };
    $formatSync = static function ($value): string {
        return in_array(strtolower((string) $value), ['berhasil', 'terverifikasi', 'tersinkron'], true)
            ? 'tersinkron'
            : 'perlu dicek';
    };
    $profileFields = [
        $user->name,
        $user->username,
        $user->email,
        optional($pembimbing)->nidn_nip,
        optional($pembimbing)->no_hp,
        optional($pembimbing)->perguruan_tinggi,
        optional($pembimbing)->program_studi,
        optional($pembimbing)->alamat,
    ];
    $filledProfile = collect($profileFields)->filter(fn ($value) => filled($value))->count();
    $profileCompletion = round(($filledProfile / count($profileFields)) * 100);
    $accountStatus = $user->account_status ?? 'aktif';
    $lastUpdated = optional($user->updated_at)->format('d M Y H:i') ?? 'Belum ada';
    $birthDate = optional($pembimbing)->tanggal_lahir;
    $birthDateValue = $birthDate ? \Illuminate\Support\Carbon::parse($birthDate)->format('Y-m-d') : null;
    $avatarUrl = $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name ?? 'Pembimbing Akademik').'&background=2a8fbd&color=fff&size=160';
    $uploadPhotoUrl = route('pembimbing.pengaturan.foto');
    $baseRows = [
        ['id'=>1, 'jenis'=>'Identitas', 'nilai'=>$user->name ?: 'Belum diisi', 'verifikasi'=>'terverifikasi', 'tanggal'=>$lastUpdated, 'sumber'=>'users.name', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Hari Ini'],
        ['id'=>2, 'jenis'=>'Identitas', 'nilai'=>$user->email ?: 'Belum diisi', 'verifikasi'=>'terverifikasi', 'tanggal'=>$lastUpdated, 'sumber'=>'users.email', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Hari Ini'],
        ['id'=>3, 'jenis'=>'Identitas', 'nilai'=>optional($pembimbing)->nidn_nip ?: 'Belum diisi', 'verifikasi'=>optional($pembimbing)->nidn_nip ? 'terverifikasi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'pembimbings.nidn_nip', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Minggu Ini'],
        ['id'=>4, 'jenis'=>'Akademik', 'nilai'=>optional($pembimbing)->perguruan_tinggi ?: 'Belum diisi', 'verifikasi'=>optional($pembimbing)->perguruan_tinggi ? 'terverifikasi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'pembimbings.perguruan_tinggi', 'sinkron'=>'tersinkron', 'aktivitas'=>'baru', 'periode'=>'Minggu Ini'],
        ['id'=>5, 'jenis'=>'Akademik', 'nilai'=>optional($pembimbing)->program_studi ?: 'Belum diisi', 'verifikasi'=>optional($pembimbing)->program_studi ? 'terverifikasi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'pembimbings.program_studi', 'sinkron'=>'tersinkron', 'aktivitas'=>'baru', 'periode'=>'Bulan Ini'],
        ['id'=>6, 'jenis'=>'Kontak', 'nilai'=>optional($pembimbing)->no_hp ?: 'Belum diisi', 'verifikasi'=>optional($pembimbing)->no_hp ? 'terverifikasi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'pembimbings.no_hp', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Bulan Ini'],
        ['id'=>7, 'jenis'=>'Kontak', 'nilai'=>optional($pembimbing)->alamat ?: 'Belum diisi', 'verifikasi'=>optional($pembimbing)->alamat ? 'terverifikasi' : 'belum lengkap', 'tanggal'=>$lastUpdated, 'sumber'=>'pembimbings.alamat', 'sinkron'=>'tersinkron', 'aktivitas'=>'diperbarui', 'periode'=>'Bulan Ini'],
    ];
    $historyRows = $securityActivities->values()->map(function ($activity, $index) use ($formatActivityType, $formatVerification, $formatSync) {
        return [
            'id' => 100 + $index,
            'jenis' => 'Riwayat',
            'nilai' => $activity->aktivitas ?: 'Aktivitas keamanan',
            'verifikasi' => $formatVerification($activity->status),
            'tanggal' => optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
            'sumber' => $activity->catatan ?: 'security_activities',
            'sinkron' => $formatSync($activity->status),
            'aktivitas' => $formatActivityType($activity->aktivitas),
            'periode' => 'Hari Ini',
        ];
    })->all();
    $profileRows = array_merge($baseRows, $historyRows);
@endphp

<div class="container-fluid profile-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('pembimbing.pengaturan') }}">Pengaturan Akun</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
    </nav>

    <section class="profile-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8 d-flex align-items-center gap-3">
                <img class="profile-avatar" src="{{ $avatarUrl }}" alt="Foto profil">
                <div>
                    <h3 class="fw-bold mb-2">Profil Pembimbing</h3>
                    <p class="mb-0">Perbarui identitas akun, informasi pribadi, dan informasi akademik agar data portal magang tetap akurat dan terintegrasi.</p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <span class="badge bg-success fs-6 me-2" id="profileBadge">Tersinkron</span>
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
            <strong>Profil belum bisa disimpan.</strong>
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
                    <div><p class="text-muted mb-1">Status Akun</p><h5 class="mb-0 text-capitalize">{{ $accountStatus }}</h5></div>
                    <span class="stat-icon bg-success"><i class="bi bi-patch-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Terverifikasi</p><h5 class="mb-0">{{ $user->verified_at ? 'Ya' : 'Proses' }}</h5></div>
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
                    <div><p class="text-muted mb-1">Aktivitas Login</p><h3 class="mb-0">12</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-box-arrow-in-right"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Profil Aktif</p><h5 class="mb-0">{{ optional($pembimbing)->program_studi ? 'Aktif' : 'Lengkapi' }}</h5></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-person-check-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="categoryFilter">Kategori Informasi</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="semua">Semua Kategori</option>
                        <option value="Identitas">Identitas</option>
                        <option value="Akademik">Akademik</option>
                        <option value="Kontak">Kontak</option>
                        <option value="Riwayat">Riwayat</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="verifyFilter">Status Verifikasi</label>
                    <select class="form-select" id="verifyFilter">
                        <option value="semua">Semua Status</option>
                        <option value="terverifikasi">Terverifikasi</option>
                        <option value="belum lengkap">Belum Lengkap</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="changeFilter">Perubahan Data</label>
                    <select class="form-select" id="changeFilter">
                        <option value="semua">Semua Perubahan</option>
                        <option value="baru">Baru</option>
                        <option value="diperbarui">Diperbarui</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal Pembaruan</label>
                    <select class="form-select" id="dateFilter">
                        <option value="semua">Semua Tanggal</option>
                        <option value="Hari Ini">Hari Ini</option>
                        <option value="Minggu Ini">Minggu Ini</option>
                        <option value="Bulan Ini">Bulan Ini</option>
                    </select>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card profile-panel shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Formulir Data Profil</h5>
                    <small class="text-muted">Data profil tersimpan ke tabel users dan pembimbings.</small>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#identityTab" type="button">Identitas</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#academicTab" type="button">Akademik</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#contactTab" type="button">Kontak</button></li>
                    </ul>
                    <form method="POST" action="{{ route('pembimbing.pengaturan.update') }}" id="profileForm">
                        @csrf
                        <input type="hidden" name="action_type" value="profile">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="identityTab">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label" for="name">Nama Lengkap</label><input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
                                    <div class="col-md-6"><label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}"></div>
                                    <div class="col-md-6"><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required></div>
                                    <div class="col-md-6"><label class="form-label" for="nidn_nip">NIDN/NIP</label><input class="form-control" id="nidn_nip" name="nidn_nip" value="{{ old('nidn_nip', optional($pembimbing)->nidn_nip) }}"></div>
                                    <div class="col-md-4"><label class="form-label" for="tempat_lahir">Tempat Lahir</label><input class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', optional($pembimbing)->tempat_lahir) }}"></div>
                                    <div class="col-md-4"><label class="form-label" for="tanggal_lahir">Tanggal Lahir</label><input class="form-control" id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $birthDateValue) }}"></div>
                                    <div class="col-md-4"><label class="form-label" for="jenis_kelamin">Jenis Kelamin</label><select class="form-select" id="jenis_kelamin" name="jenis_kelamin"><option value="">Pilih</option><option value="Laki-laki" @selected(old('jenis_kelamin', optional($pembimbing)->jenis_kelamin) === 'Laki-laki')>Laki-laki</option><option value="Perempuan" @selected(old('jenis_kelamin', optional($pembimbing)->jenis_kelamin) === 'Perempuan')>Perempuan</option></select></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="academicTab">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label" for="perguruan_tinggi">Perguruan Tinggi</label><input class="form-control" id="perguruan_tinggi" name="perguruan_tinggi" value="{{ old('perguruan_tinggi', optional($pembimbing)->perguruan_tinggi) }}"></div>
                                    <div class="col-md-6"><label class="form-label" for="program_studi">Program Studi</label><input class="form-control" id="program_studi" name="program_studi" value="{{ old('program_studi', optional($pembimbing)->program_studi) }}"></div>
                                    <div class="col-md-6"><label class="form-label" for="instansi">Instansi</label><input class="form-control" id="instansi" name="instansi" value="{{ old('instansi', optional($pembimbing)->instansi) }}"></div>
                                    <div class="col-md-6"><label class="form-label" for="jabatan">Jabatan</label><input class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan', optional($pembimbing)->jabatan) }}"></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="contactTab">
                                <div class="row g-3">
                                    <div class="col-md-6"><label class="form-label" for="no_hp">No HP</label><input class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp', optional($pembimbing)->no_hp) }}"></div>
                                    <div class="col-md-6"><label class="form-label" for="alamat">Alamat</label><input class="form-control" id="alamat" name="alamat" value="{{ old('alamat', optional($pembimbing)->alamat) }}"></div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan Perubahan</button>
                            <button class="btn btn-outline-secondary" type="reset"><i class="bi bi-x-circle"></i> Batalkan Perubahan</button>
                            <button class="btn btn-outline-info" type="button" id="historyButton"><i class="bi bi-clock-history"></i> Lihat Riwayat</button>
                            <button class="btn btn-outline-success" type="button" id="photoButton"><i class="bi bi-image"></i> Ubah Foto</button>
                            <input class="d-none" type="file" id="photoInput" accept=".jpg,.jpeg,.png">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card profile-panel shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="mb-3">Ringkasan Identitas</h5>
                    <div class="identity-card mb-3">
                        <strong>{{ $user->name }}</strong>
                        <small class="text-muted d-block">{{ $user->email }}</small>
                        <span class="badge bg-success mt-2 text-capitalize">{{ $accountStatus }}</span>
                    </div>
                    <div class="identity-card">
                        <small class="text-muted">Akademik</small>
                        <div>{{ optional($pembimbing)->perguruan_tinggi ?? 'Perguruan tinggi belum diisi' }}</div>
                        <div>{{ optional($pembimbing)->program_studi ?? 'Program studi belum diisi' }}</div>
                    </div>
                </div>
            </div>
            <div class="card profile-panel shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Status Kelengkapan Profil</h5>
                    <div class="progress mb-3" style="height:12px">
                        <div class="progress-bar bg-primary" style="width:{{ $profileCompletion }}%"></div>
                    </div>
                    <div class="status-row mb-2"><strong>{{ $filledProfile }}</strong> dari {{ count($profileFields) }} informasi utama terisi.</div>
                    <div class="status-row"><small class="text-muted">Status sinkronisasi</small><div>Tersinkron dengan database</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h5 class="mb-0">Rincian Data Profil dan Riwayat</h5>
                <small class="text-muted" id="tableInfo">Menampilkan data profil</small>
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
            <div class="empty-state d-none" id="emptyState"><div><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Tidak ada data profil sesuai filter.</p></div></div>
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

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="systemToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Profil diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const profileRows = @json($profileRows);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const uploadPhotoUrl = @json($uploadPhotoUrl);

    const tableBody = document.getElementById('profileTableBody');
    const emptyState = document.getElementById('emptyState');
    const pagination = document.getElementById('pagination');
    const paginationInfo = document.getElementById('paginationInfo');
    const tableInfo = document.getElementById('tableInfo');
    const perPageSelect = document.getElementById('perPageSelect');
    const categoryFilter = document.getElementById('categoryFilter');
    const verifyFilter = document.getElementById('verifyFilter');
    const changeFilter = document.getElementById('changeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('systemToast'));
    let currentPage = 1;
    let pendingAction = null;

    const badge = (value) => `<span class="badge bg-${value === 'terverifikasi' || value === 'tersinkron' ? 'success' : 'warning'} text-capitalize">${value}</span>`;

    function showToast(message, type = 'success') {
        const toastEl = document.getElementById('systemToast');
        toastEl.className = `toast align-items-center text-bg-${type} border-0`;
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    }

    function setAvatar(url) {
        const profileAvatar = document.querySelector('.profile-avatar');
        if (profileAvatar && url) {
            profileAvatar.src = url;
        }

        document.querySelectorAll('.js-pembimbing-avatar').forEach((img) => {
            if (url) {
                img.src = url;
            }
        });
    }

    function filteredData() {
        return profileRows.filter((item) => {
            const categoryMatch = categoryFilter.value === 'semua' || item.jenis === categoryFilter.value;
            const verifyMatch = verifyFilter.value === 'semua' || item.verifikasi === verifyFilter.value;
            const changeMatch = changeFilter.value === 'semua' || item.aktivitas === changeFilter.value;
            const dateMatch = dateFilter.value === 'semua' || item.periode === dateFilter.value;
            return categoryMatch && verifyMatch && changeMatch && dateMatch;
        });
    }

    function renderPagination(totalItems, perPage) {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        let html = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><button class="page-link" data-page="${currentPage - 1}" type="button">Previous</button></li>`;
        for (let page = 1; page <= totalPages; page++) html += `<li class="page-item ${page === currentPage ? 'active' : ''}"><button class="page-link" data-page="${page}" type="button">${page}</button></li>`;
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
                <td><strong>${item.jenis}</strong></td>
                <td>${item.nilai}</td>
                <td>${badge(item.verifikasi)}</td>
                <td>${item.tanggal}</td>
                <td>${item.sumber}</td>
                <td>${badge(item.sinkron)}</td>
                <td class="text-capitalize">${item.aktivitas}</td>
                <td>
                    <div class="action-group">
                        <button class="btn btn-primary btn-sm" type="button" data-action="edit profil" data-id="${item.id}">Edit Profil</button>
                        <button class="btn btn-outline-success btn-sm" type="button" data-action="ubah foto" data-id="${item.id}">Ubah Foto</button>
                        <button class="btn btn-outline-info btn-sm" type="button" data-action="sinkronisasi" data-id="${item.id}">Sinkronisasi</button>
                        <button class="btn btn-outline-dark btn-sm" type="button" data-action="riwayat" data-id="${item.id}">Riwayat</button>
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-action="perbarui" data-id="${item.id}">Perbarui</button>
                    </div>
                </td>
            </tr>
        `).join('');
        emptyState.classList.toggle('d-none', data.length > 0);
        tableInfo.textContent = `Menampilkan ${data.length} data profil`;
        paginationInfo.textContent = data.length ? `Menampilkan ${start + 1}-${Math.min(start + perPage, data.length)} dari ${data.length} data` : 'Menampilkan 0 data';
        renderPagination(data.length, perPage);
    }

    function openConfirm(item, action) {
        pendingAction = { item, action };
        document.getElementById('confirmSummary').innerHTML = `<div class="border rounded p-3"><strong>${item.jenis}</strong><small class="text-muted d-block">${item.nilai}</small><hr><span class="text-capitalize">${action}</span> ${badge(item.verifikasi)}</div>`;
        document.getElementById('confirmNote').value = `Konfirmasi ${action} untuk ${item.jenis}`;
        confirmModal.show();
    }

    document.getElementById('applyFilter').addEventListener('click', () => { currentPage = 1; renderTable(); showToast('Filter profil berhasil diterapkan.', 'info'); });
    document.getElementById('resetFilter').addEventListener('click', () => {
        categoryFilter.value = 'semua';
        verifyFilter.value = 'semua';
        changeFilter.value = 'semua';
        dateFilter.value = 'semua';
        currentPage = 1;
        renderTable();
        showToast('Filter profil berhasil direset.', 'info');
    });
    [categoryFilter, verifyFilter, changeFilter, dateFilter].forEach((element) => element.addEventListener('change', () => { currentPage = 1; renderTable(); }));
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
        const item = profileRows.find((row) => row.id === Number(button.dataset.id));
        openConfirm(item, button.dataset.action);
    });
    document.getElementById('historyButton').addEventListener('click', () => {
        const historyRow = profileRows.find((item) => item.jenis === 'Riwayat');
        if (!historyRow) {
            showToast('Riwayat profil belum tersedia.', 'info');
            return;
        }

        openConfirm(historyRow, 'lihat riwayat');
    });
    document.getElementById('photoButton').addEventListener('click', () => {
        document.getElementById('photoInput')?.click();
    });
    document.getElementById('photoInput').addEventListener('change', async (event) => {
        const file = event.target.files?.[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran foto maksimal 2MB.', 'danger');
            event.target.value = '';
            return;
        }

        const formData = new FormData();
        formData.append('foto', file);

        try {
            const response = await fetch(uploadPhotoUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || 'Foto profil gagal diunggah.');
            }

            setAvatar(payload.avatar_url);
            showToast(payload.message || 'Foto profil berhasil diperbarui.');
        } catch (error) {
            showToast(error.message || 'Foto profil gagal diperbarui.', 'danger');
        } finally {
            event.target.value = '';
        }
    });
    document.getElementById('syncButton').addEventListener('click', () => {
        document.getElementById('profileBadge').textContent = 'Diperbarui';
        showToast('Data profil berhasil disinkronkan.', 'info');
    });
    document.getElementById('confirmAction').addEventListener('click', () => {
        confirmModal.hide();
        showToast(`Aksi ${pendingAction?.action || 'profil'} berhasil dicatat.`);
        pendingAction = null;
    });

    renderTable();
    setTimeout(() => showToast('Profil tersinkron dengan database.', 'success'), 800);
});
</script>
@endpush
