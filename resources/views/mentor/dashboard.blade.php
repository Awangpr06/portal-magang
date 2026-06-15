@extends('mentor.layout.mentor')

@section('title', 'Dashboard Mentor')
@section('page-title', 'Dashboard Mentor')

@section('content')
@php
    $dashboard = $mentorDashboard ?? [];
    $dashboardStats = $dashboard['dashboardStats'] ?? [];
    $recentActivities = $dashboard['recentActivities'] ?? collect();
    $progressRows = $dashboard['progressRows'] ?? collect();
@endphp
<div class="container-fluid">
    <section class="hero-panel p-4 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h3 class="fw-bold mb-2">Selamat Datang, {{ auth()->user()->name ?? 'Mentor Lapangan' }}</h3>
                <p class="mb-2">Pantau peserta magang, validasi aktivitas harian, dan berikan evaluasi lapangan dari satu dashboard.</p>
                <p class="mb-0">Dashboard ini membantu mentor memonitor kehadiran, aktivitas, penugasan, dokumen, dan komunikasi peserta secara ringkas.</p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <img src="{{ asset('images/4.png') }}" class="hero-illustration" alt="Ilustrasi mentor memonitor peserta magang">
            </div>
        </div>
    </section>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-2">
            <a class="quick-action-card" href="{{ route('mentor.peserta') }}"><span class="menu-icon"><i class="bi bi-people"></i></span><strong>Lihat Peserta</strong></a>
        </div>
        <div class="col-md-6 col-xl-2">
            <a class="quick-action-card" href="{{ route('mentor.absensi') }}"><span class="menu-icon"><i class="bi bi-calendar-check"></i></span><strong>Cek Absensi</strong></a>
        </div>
        <div class="col-md-6 col-xl-2">
            <a class="quick-action-card" href="{{ route('mentor.penilaian') }}"><span class="menu-icon"><i class="bi bi-award"></i></span><strong>Input Nilai</strong></a>
        </div>
        <div class="col-md-6 col-xl-2">
            <a class="quick-action-card" href="{{ route('mentor.komunikasi') }}"><span class="menu-icon"><i class="bi bi-send"></i></span><strong>Kirim Pesan</strong></a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @forelse ($dashboardStats as $stat)
            <div class="col-md-6 col-xl-3">
                <div class="card stat-card shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">{{ $stat['label'] }}</p>
                            <h3 class="mb-0">{{ $stat['value'] }}</h3>
                        </div>
                        <span class="stat-icon bg-{{ $stat['color'] }}"><i class="bi bi-{{ $stat['icon'] }}"></i></span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border mb-0">Belum ada data mentor yang terhubung ke database.</div>
            </div>
        @endforelse
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="mb-1">Aktivitas Sistem Terbaru</h5>
                            <p class="text-muted mb-0">Aktivitas peserta magang yang perlu diketahui mentor.</p>
                        </div>
                        <a class="btn btn-outline-warning btn-sm" href="{{ route('mentor.monitoring') }}">
                            <i class="bi bi-arrow-right"></i> Lihat Semua
                        </a>
                    </div>
                    <div class="d-grid gap-2">
                        @forelse ($recentActivities as $activity)
                            @php
                                $activityStatus = $activity['status'] ?? 'aktif';
                                $activityBadge = match ($activityStatus) {
                                    'selesai' => ['label' => 'Selesai', 'class' => 'bg-success'],
                                    'perlu perhatian' => ['label' => 'Perlu Tindakan', 'class' => 'bg-danger'],
                                    'review' => ['label' => 'Tindak Lanjut', 'class' => 'bg-warning text-dark'],
                                    default => ['label' => 'Baru', 'class' => 'bg-info text-dark'],
                                };
                            @endphp
                            <div class="activity-row d-flex justify-content-between gap-3">
                                <div>
                                    <strong>{{ $activity['nama'] }}</strong>
                                    <div>{{ $activity['aktivitas'] }}</div>
                                    <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($activity['sort_at'] ?? now()->toDateTimeString())->diffForHumans() }}</small>
                                </div>
                                <span class="badge {{ $activityBadge['class'] }} align-self-start">{{ $activityBadge['label'] }}</span>
                            </div>
                        @empty
                            <div class="activity-row text-muted">Belum ada aktivitas terbaru dari peserta bimbingan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card table-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-1">Progress Peserta</h5>
                    <p class="text-muted mb-3">Kehadiran, aktivitas, penugasan, laporan, dan penilaian.</p>
                    <div class="d-grid gap-2">
                        @forelse ($progressRows as $row)
                            @php
                                $progressLabel = match ($row['status'] ?? 'aktif') {
                                    'selesai' => 'Selesai',
                                    'perlu perhatian' => 'Perlu Tindak Lanjut',
                                    'review' => 'Proses',
                                    default => ((int) ($row['progress'] ?? 0) >= 85 ? 'Sesuai Target' : 'Proses'),
                                };
                                $progressClass = (int) ($row['progress'] ?? 0) >= 85 ? 'bg-success' : (((int) ($row['progress'] ?? 0) >= 70) ? 'bg-warning text-dark' : 'bg-danger');
                                $barClass = (int) ($row['progress'] ?? 0) >= 85 ? 'bg-success' : (((int) ($row['progress'] ?? 0) >= 70) ? '' : 'bg-danger');
                            @endphp
                            <div class="progress-row">
                                <div class="d-flex justify-content-between"><strong>{{ $row['nama'] }}</strong><span>{{ $row['progress'] }}%</span></div>
                                <div class="progress my-2"><div class="progress-bar {{ $barClass }}" style="width:{{ $row['progress'] }}%"></div></div>
                                <span class="badge {{ $progressClass }}">{{ $progressLabel }}</span>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada data progress peserta.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
