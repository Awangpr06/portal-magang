<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 22px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            margin: 0;
            background: #fff;
        }
        .frame {
            border: 8px solid #d7b56d;
            border-radius: 12px;
            padding: 28px;
            min-height: 520px;
            background: linear-gradient(135deg, #fffdf7, #f3fbfd);
            position: relative;
        }
        .frame::before {
            content: "";
            position: absolute;
            inset: 16px;
            border: 1px solid rgba(42, 143, 189, 0.28);
            border-radius: 8px;
            pointer-events: none;
        }
        .brand {
            text-align: center;
            font-size: 12px;
            color: #475569;
            margin-bottom: 18px;
        }
        .title {
            text-align: center;
            font-size: 28px;
            margin: 0 0 10px 0;
            letter-spacing: 1px;
        }
        .seal {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: #2a8fbd;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 38px;
            margin: 0 auto 16px;
        }
        .body {
            text-align: center;
            max-width: 760px;
            margin: 0 auto;
            line-height: 1.6;
        }
        .name {
            font-size: 24px;
            font-weight: bold;
            margin: 8px 0 10px;
        }
        .meta {
            margin-top: 22px;
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .meta-row {
            display: table-row;
        }
        .meta-label, .meta-value {
            display: table-cell;
            padding: 6px 8px;
            font-size: 12px;
        }
        .meta-label {
            width: 170px;
            color: #64748b;
            text-align: left;
        }
        .meta-value {
            text-align: left;
        }
        .footer {
            position: absolute;
            left: 28px;
            right: 28px;
            bottom: 22px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            font-size: 11px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="brand">Portal Magang LLDIKTI Wilayah V Yogyakarta</div>
        <div class="seal">&#9733;</div>
        <h1 class="title">Sertifikat Magang</h1>
        <div class="body">
            <p>Diberikan kepada</p>
            <div class="name">{{ $user->name ?? '-' }}</div>
            <p>atas penyelesaian program magang sebagai <strong>{{ $peserta?->program_magang ?? 'Peserta Magang' }}</strong></p>
            <p>di bawah bimbingan <strong>{{ $mentorName }}</strong> dan <strong>{{ $pembimbingName }}</strong>.</p>

            <div class="meta">
                <div class="meta-row"><div class="meta-label">Nomor Sertifikat</div><div class="meta-value">{{ $nomor }}</div></div>
                <div class="meta-row"><div class="meta-label">Periode</div><div class="meta-value">{{ $periode }}</div></div>
                <div class="meta-row"><div class="meta-label">Predikat</div><div class="meta-value">{{ $predikat }}</div></div>
                <div class="meta-row"><div class="meta-label">NIM</div><div class="meta-value">{{ $peserta?->nim ?? '-' }}</div></div>
            </div>
        </div>
        <div class="footer">
            <div>Dokumen diterbitkan secara otomatis oleh sistem.</div>
            <div>{{ $tanggalTerbit ?? now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>
</body>
</html>
