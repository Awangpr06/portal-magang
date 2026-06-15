<div class="card border-0 shadow-sm rounded-4">

    <div class="card-body">

        <div class="d-flex justify-content-between mb-4">

            <h5 class="fw-bold">
                Data dan Dokumen
            </h5>

            <a href="{{ route('admin.magang.dokumen') }}">
                Lihat Semua
            </a>

        </div>

        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Dokumen</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse(($adminRecentDocuments ?? collect()) as $document)
                    <tr>
                        <td>
                            <strong>{{ $document['nama'] }}</strong>
                            <div class="small text-muted">{{ $document['pemilik'] }}</div>
                        </td>

                        <td>
                            <span class="badge bg-{{ $document['status'] === 'disetujui' ? 'success' : ($document['status'] === 'ditolak' ? 'danger' : ($document['status'] === 'revisi' ? 'warning' : 'primary')) }}">
                                {{ ucfirst($document['status']) }}
                            </span>
                        </td>

                        <td>{{ $document['tanggal'] }}</td>

                        <td>
                            <button class="btn btn-outline-primary btn-sm" type="button">
                                Lihat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada dokumen tersimpan di database.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
