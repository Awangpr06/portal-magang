<h4 class="fw-bold mb-4">
    Ringkasan Status Magang
</h4>

<div class="row">

    <div class="col-md-3 mb-4">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <i class="bi bi-people-fill fs-1 text-primary"></i>

                <h3 class="mt-3">{{ $adminStats['active_participants'] ?? 0 }}</h3>

                <p>Total Peserta Aktif</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <i class="bi bi-building fs-1 text-success"></i>

                <h3 class="mt-3">{{ $adminStats['total_campuses'] ?? 0 }}</h3>

                <p>Total Perguruan Tinggi</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <i class="bi bi-clock-history fs-1 text-warning"></i>

                <h3 class="mt-3">{{ $adminStats['waiting_users'] ?? 0 }}</h3>

                <p>Proses Verifikasi</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-dashboard shadow-sm">
            <div class="card-body">
                <i class="bi bi-file-earmark-text-fill fs-1 text-danger"></i>

                <h3 class="mt-3">{{ $adminStats['total_reports'] ?? 0 }}</h3>

                <p>Total Laporan Masuk</p>
            </div>
        </div>
    </div>

</div>
