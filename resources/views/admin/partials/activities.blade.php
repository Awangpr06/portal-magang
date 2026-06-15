<div class="card border-0 shadow-sm rounded-4">

    <div class="card-body">

        <div class="d-flex justify-content-between mb-4">

            <h5 class="fw-bold">
                Aktivitas Magang Terbaru
            </h5>

            <a href="{{ route('admin.magang.kegiatan') }}">
                Lihat Semua
            </a>

        </div>

        <div class="activity-list">

            @forelse(($adminRecentActivities ?? collect()) as $activity)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <strong>{{ $activity['nama'] }}</strong>
                            <span class="badge bg-primary text-capitalize ms-2">
                                {{ str_replace('_', ' ', $activity['role']) }}
                            </span>
                            <p class="mb-1">{{ $activity['aktivitas'] }}</p>
                            <small class="text-muted">{{ $activity['waktu'] }}</small>
                        </div>
                        <span class="badge bg-light text-dark">{{ $activity['tanggal'] }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">
                    Belum ada aktivitas tersimpan di database.
                </div>
            @endforelse

        </div>

    </div>

</div>
