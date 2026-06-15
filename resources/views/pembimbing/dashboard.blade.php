@extends('pembimbing.layout.pembimbing')

@section('title', 'Dashboard Pembimbing Akademik')
@section('page-title', 'Dashboard Pembimbing Akademik')

@section('content')
<div class="container-fluid">
    @php
        $dashboardStats = $dashboardStats ?? [];
        $dashboardAlerts = $dashboardAlerts ?? [];
        $students = $students ?? [];
    @endphp

    <section class="hero-panel p-4 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Selamat Datang, Pembimbing Akademik</h3>
                <p class="mb-0">Pantau mahasiswa bimbingan, validasi progres magang, dan tindak lanjuti laporan dari satu dashboard.</p>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        @forelse ($dashboardStats as $stat)
            <div class="col-md-6 col-xl-3">
                <div class="card stat-card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">{{ $stat['label'] }}</p>
                            <h3 class="mb-0">{{ $stat['value'] }}</h3>
                        </div>
                        <span class="stat-icon bg-{{ $stat['color'] }}"><i class="bi {{ $stat['icon'] }}"></i></span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">Belum ada data statistik pembimbing yang bisa ditampilkan.</div>
            </div>
        @endforelse
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Ringkasan Mahasiswa Bimbingan</h5>
                            <p class="text-muted mb-0">Progres magang dan status akademik terbaru.</p>
                        </div>
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('pembimbing.mahasiswa') }}">
                            <i class="bi bi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIM</th>
                                    <th>Penempatan</th>
                                    <th>Progres</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($students->take(4) as $student)
                                    <tr>
                                        <td class="fw-semibold">{{ $student['nama'] }}</td>
                                        <td>{{ $student['nim'] }}</td>
                                        <td>{{ $student['penempatan'] }}</td>
                                        <td>{{ $student['progress'] }}%</td>
                                        <td>
                                            <span class="badge
                                                @if ($student['status'] === 'aktif') bg-success
                                                @elseif ($student['status'] === 'perlu tindak lanjut') bg-danger
                                                @elseif ($student['status'] === 'belum aktif') bg-secondary
                                                @else bg-info text-dark
                                                @endif">
                                                {{ ucfirst($student['status']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Belum ada mahasiswa bimbingan yang terdaftar di database.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-soft shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Aksi Cepat</h5>
                    <div class="d-grid gap-2">
                        <a class="btn btn-primary text-start" href="{{ route('pembimbing.absensi') }}"><i class="bi bi-calendar-check me-2"></i> Cek Absensi</a>
                        <a class="btn btn-success text-start" href="{{ route('pembimbing.kegiatan') }}"><i class="bi bi-journal-check me-2"></i> Validasi Logbook</a>
                        <a class="btn btn-secondary text-start" href="{{ route('pembimbing.komunikasi') }}"><i class="bi bi-chat-dots me-2"></i> Buka Pesan</a>
                    </div>
                    <hr>
                    @forelse ($dashboardAlerts as $alert)
                        <div class="alert alert-warning mb-2">{{ $alert }}</div>
                    @empty
                        <div class="alert alert-success mb-0">Semua data pembimbing sudah sinkron dengan database.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
