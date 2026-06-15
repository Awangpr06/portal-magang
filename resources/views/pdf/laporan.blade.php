<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px 32px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .subtitle {
            color: #475569;
            margin: 0;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .meta td {
            vertical-align: top;
            padding: 5px 0;
        }
        .label {
            width: 180px;
            color: #64748b;
        }
        .box {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #0f172a;
        }
        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            font-size: 10px;
        }
        .pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 11px;
            font-weight: bold;
        }
        .muted {
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Laporan Magang</p>
        <p class="subtitle">Dokumen PDF resmi hasil ekspor dari sistem Portal Magang</p>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Judul</td>
            <td>: {{ $report->judul }}</td>
        </tr>
        <tr>
            <td class="label">Peserta</td>
            <td>: {{ $report->peserta?->user?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIM</td>
            <td>: {{ $report->peserta?->nim ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Laporan</td>
            <td>: {{ strtolower($report->jenis ?? 'berkala') === 'akhir' ? 'Laporan Akhir' : 'Laporan Berkala' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td>: {{ $report->periode ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Durasi Jam</td>
            <td>: {{ $report->durasi_jam ? $report->durasi_jam.' jam' : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>: <span class="pill">{{ ucfirst(str_replace('_', ' ', $report->status ?? 'draft')) }}</span></td>
        </tr>
        <tr>
            <td class="label">Diunduh Oleh</td>
            <td>: {{ $downloadedBy }} ({{ $roleLabel }})</td>
        </tr>
    </table>

    <div class="box">
        <p class="section-title">Ringkasan Laporan</p>
        <div>{!! nl2br(e($report->catatan ?: 'Tidak ada ringkasan laporan.')) !!}</div>
    </div>

    <div class="box">
        <p class="section-title">Catatan Mentor</p>
        <div>{!! nl2br(e($report->catatan_mentor ?: '-')) !!}</div>
    </div>

    <div class="box">
        <p class="section-title">Catatan Pembimbing Akademik</p>
        <div>{!! nl2br(e($report->catatan_pembimbing ?: '-')) !!}</div>
    </div>

    <div class="box">
        <p class="section-title">Lampiran Asli</p>
        <div class="muted">{{ $report->file ?: '-' }}</div>
        <div class="muted" style="margin-top:4px;">Jika lampiran asli masih diperlukan, file sumber tetap tersimpan di sistem.</div>
    </div>

    <div class="footer">
        Dokumen ini dibuat otomatis pada {{ now()->translatedFormat('d F Y H:i') }} WIB.
    </div>
</body>
</html>
