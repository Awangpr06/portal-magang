<div class="banner p-5">

    <div class="row align-items-center">

        <div class="col-lg-8">

            <h2 class="fw-bold">
                Selamat datang, {{ auth()->user()->name ?? 'Super Admin' }}
            </h2>

            <p class="mt-3">
                Kelola data portal magang berdasarkan data yang tersimpan di database. Saat ini ada
                {{ $adminStats['active_participants'] ?? 0 }} peserta aktif,
                {{ $adminStats['total_campuses'] ?? 0 }} perguruan tinggi,
                dan {{ $adminStats['waiting_users'] ?? 0 }} akun menunggu verifikasi.
            </p>

        </div>

        <div class="col-lg-4 text-center">

            <img src="{{ asset('images/work.png') }}"
                 width="220">

        </div>

    </div>

</div>
