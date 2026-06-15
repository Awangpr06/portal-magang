@extends('admin.layout.admin')

@section('title', 'Verifikasi Akun')

@push('styles')
<style>
    .account-verification-page .page-title {
        font-weight: 700;
        color: #163342;
    }

    .account-verification-page .stat-card,
    .account-verification-page .filter-card,
    .account-verification-page .table-card {
        border: 0;
        border-radius: 8px;
    }

    .account-verification-page .stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: #fff;
    }

    .account-verification-page .table thead th {
        background: #f1f6f9;
        color: #3b5664;
        font-size: 14px;
        white-space: nowrap;
    }

    .account-verification-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .account-verification-page .empty-state {
        min-height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #6c757d;
    }
</style>
@endpush

@php
    $accountRoleLabel = fn ($role) => [
        'peserta' => 'Peserta Magang',
        'mentor' => 'Mentor',
        'pembimbing' => 'Pembimbing Akademik',
    ][$role] ?? ucfirst($role);

    $accountStatusClass = fn ($status) => [
        'menunggu' => 'bg-warning text-dark',
        'disetujui' => 'bg-success',
        'ditolak' => 'bg-danger',
    ][$status] ?? 'bg-secondary';

    $accountInstitution = function ($user) {
        if ($user->role === 'peserta') {
            return optional(optional($user->peserta)->perguruanTinggi)->nama_pt ?? '-';
        }

        if ($user->role === 'mentor') {
            return optional($user->mentor)->perguruan_tinggi
                ?? optional($user->mentor)->divisi
                ?? '-';
        }

        return optional($user->pembimbing)->perguruan_tinggi
            ?? optional($user->pembimbing)->instansi
            ?? '-';
    };
@endphp

@section('content')
<div class="container-fluid account-verification-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="page-title mb-1">Verifikasi Akun</h2>
            <p class="text-muted mb-0">
                Akun baru peserta, mentor, dan pembimbing harus disetujui admin sebelum bisa login.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Akun Menunggu</p>
                        <h3 class="mb-0">{{ $stats['menunggu'] }}</h3>
                    </div>
                    <span class="stat-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Disetujui</p>
                        <h3 class="mb-0">{{ $stats['disetujui'] }}</h3>
                    </div>
                    <span class="stat-icon bg-success"><i class="bi bi-check2-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Ditolak</p>
                        <h3 class="mb-0">{{ $stats['ditolak'] }}</h3>
                    </div>
                    <span class="stat-icon bg-danger"><i class="bi bi-x-circle"></i></span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card stat-card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Data</p>
                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    </div>
                    <span class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.verifikasi.akun') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="semua" @selected(request('status', 'menunggu') === 'semua')>Semua Status</option>
                            <option value="menunggu" @selected(request('status', 'menunggu') === 'menunggu')>Menunggu</option>
                            <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
                            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="semua" @selected(request('role') === 'semua')>Semua Role</option>
                            <option value="peserta" @selected(request('role') === 'peserta')>Peserta Magang</option>
                            <option value="mentor" @selected(request('role') === 'mentor')>Mentor</option>
                            <option value="pembimbing" @selected(request('role') === 'pembimbing')>Pembimbing Akademik</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <select class="form-select" id="tanggal" name="tanggal">
                            <option value="semua" @selected(request('tanggal') === 'semua')>Semua Tanggal</option>
                            <option value="hari ini" @selected(request('tanggal') === 'hari ini')>Hari Ini</option>
                            <option value="minggu ini" @selected(request('tanggal') === 'minggu ini')>Minggu Ini</option>
                            <option value="bulan ini" @selected(request('tanggal') === 'bulan ini')>Bulan Ini</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="search" class="form-label">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="search" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau institusi">
                        </div>
                    </div>

                    <div class="col-md-2 d-flex gap-2">
                        <button class="btn btn-primary w-50" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <a class="btn btn-outline-secondary w-50" href="{{ route('admin.verifikasi.akun') }}">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Data Akun</h5>
                    <p class="text-muted mb-0">{{ $accounts->total() }} akun ditemukan</p>
                </div>
            </div>

            @if($accounts->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Institusi</th>
                                <th>Email</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th width="260">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td class="fw-semibold">{{ $account->name }}</td>
                                    <td><span class="badge bg-primary">{{ $accountRoleLabel($account->role) }}</span></td>
                                    <td>{{ $accountInstitution($account) }}</td>
                                    <td>{{ $account->email }}</td>
                                    <td>{{ $account->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $accountStatusClass($account->account_status) }}">
                                            {{ ucfirst($account->account_status) }}
                                        </span>
                                        @if($account->account_status === 'ditolak' && $account->rejection_reason)
                                            <div class="small text-muted mt-1">{{ $account->rejection_reason }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <form method="POST" action="{{ route('admin.verifikasi.akun.setujui', $account) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-success btn-sm" type="submit" @disabled($account->account_status === 'disetujui')>
                                                    Setujui
                                                </button>
                                            </form>

                                            <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $account->id }}" @disabled($account->account_status === 'ditolak')>
                                                Tolak
                                            </button>
                                        </div>

                                        <div class="modal fade" id="rejectModal{{ $account->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form class="modal-content" method="POST" action="{{ route('admin.verifikasi.akun.tolak', $account) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Akun</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Berikan alasan penolakan untuk akun <strong>{{ $account->name }}</strong>.</p>
                                                        <label class="form-label" for="rejection_reason_{{ $account->id }}">Alasan Penolakan</label>
                                                        <textarea class="form-control" id="rejection_reason_{{ $account->id }}" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger">Tolak Akun</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $accounts->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div>
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <h6 class="mb-1">Data akun tidak ditemukan</h6>
                        <p class="mb-0">Belum ada akun dari database yang sesuai filter.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
