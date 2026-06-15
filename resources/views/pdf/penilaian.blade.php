<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 24px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .title {
            margin: 0;
            font-size: 18px;
            color: #0f172a;
        }
        .subtitle {
            margin: 4px 0 0;
            color: #475569;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .meta td {
            padding: 4px 0;
            vertical-align: top;
        }
        .label {
            width: 165px;
            color: #64748b;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th,
        .table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        .table th {
            background: #eef8fc;
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 10px;
            font-weight: bold;
        }
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Rekap Nilai Peserta Magang</h1>
        <p class="subtitle">{{ $scopeLabel ?? 'Rekap penilaian mentor' }}</p>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Mentor</td>
            <td>: {{ $mentorName ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Diunduh Oleh</td>
            <td>: {{ $downloadedBy ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Unduh</td>
            <td>: {{ now()->translatedFormat('d F Y H:i') }} WIB</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>NIM</th>
                <th>Prodi</th>
                <th>Periode</th>
                <th>Kehadiran</th>
                <th>Aktivitas</th>
                <th>Laporan</th>
                <th>Sikap</th>
                <th>Kompetensi</th>
                <th>Nilai Akhir</th>
                <th>Grade</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assessments as $index => $assessment)
                @php
                    $peserta = $assessment->peserta;
                    $components = json_decode((string) $assessment->komponen, true);
                    $components = is_array($components) ? $components : [];
                    $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);
                    $grade = match (true) {
                        $finalScore >= 90 => 'A',
                        $finalScore >= 85 => 'B+',
                        $finalScore >= 75 => 'B',
                        $finalScore >= 70 => 'C+',
                        $finalScore > 0 => 'C',
                        default => '-',
                    };
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $peserta?->user?->name ?? '-' }}</td>
                    <td>{{ $peserta?->nim ?? '-' }}</td>
                    <td>{{ $peserta?->jurusan ?? '-' }}</td>
                    <td>{{ $assessment->periode ?: ($peserta?->program_magang ?? '-') }}</td>
                    <td>{{ (int) ($components['presence'] ?? 0) }}</td>
                    <td>{{ (int) ($components['activity'] ?? 0) }}</td>
                    <td>{{ (int) ($components['report'] ?? 0) }}</td>
                    <td>{{ (int) ($components['attitude'] ?? 0) }}</td>
                    <td>{{ (int) ($components['competency'] ?? 0) }}</td>
                    <td>{{ (int) round($finalScore) }}</td>
                    <td><span class="badge">{{ $grade }}</span></td>
                    <td>{{ ucfirst(str_replace('_', ' ', (string) $assessment->status)) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem Portal Magang LLDIKTI Wilayah V Yogyakarta.
    </div>
</body>
</html>
