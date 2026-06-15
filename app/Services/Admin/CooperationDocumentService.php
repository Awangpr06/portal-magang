<?php

namespace App\Services\Admin;

use App\Models\Document;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CooperationDocumentService
{
    public function rows(): Collection
    {
        return Document::query()
            ->with(['user.peserta.perguruanTinggi'])
            ->where('kategori', 'Dokumen Kerja Sama')
            ->latest()
            ->get()
            ->map(fn (Document $document) => $this->mapDocument($document))
            ->values();
    }

    public function stats(Collection $rows): array
    {
        return [
            'total' => $rows->count(),
            'aktif' => $rows->where('status', 'aktif')->count(),
            'berakhir' => $rows->where('status', 'berakhir')->count(),
            'menunggu' => $rows->where('status', 'menunggu')->count(),
            'nonaktif' => $rows->where('status', 'nonaktif')->count(),
        ];
    }

    private function mapDocument(Document $document): array
    {
        $text = $this->extractText($document);
        $lines = $this->extractLines($text);
        $jenis = $this->documentTypeLabel($document);
        $number = $this->extractLabelValue($lines, [
            'Nomor Dokumen',
            'Nomor',
            'No. Dokumen',
            'No.',
        ]) ?? $this->fallbackDocumentNumber($document);
        $title = $this->extractLabelValue($lines, [
            'Judul Kerja Sama',
            'Judul',
            'Perihal',
            'Tentang',
        ]) ?? $this->guessTitle($lines, $document);
        $campus = $this->extractCampus($lines, $document);
        $startDate = $this->extractDateValue($lines, [
            'Tanggal Mulai',
            'Berlaku Mulai',
            'Mulai Berlaku',
            'Tanggal Efektif',
        ]);
        $endDate = $this->extractDateValue($lines, [
            'Tanggal Berakhir',
            'Berlaku Sampai',
            'Sampai',
            'Tanggal Selesai',
            'Berakhir',
        ]);
        $status = $this->determineStatus($document, $startDate, $endDate);

        return [
            'id' => $document->id,
            'user_id' => $document->user_id,
            'kampus' => $campus,
            'jenis' => $jenis,
            'jenis_key' => $document->jenis_dokumen,
            'dokumen' => $number,
            'judul' => $title,
            'mulai' => $this->formatDateOrDash($startDate ?? $document->created_at),
            'berakhir' => $this->formatDateOrDash($endDate),
            'status' => $status,
            'masa' => $this->determinePeriodLabel($startDate, $endDate, $status),
            'file' => $document->file,
            'nama_dokumen' => $document->nama_dokumen,
            'catatan' => $document->catatan ?: '-',
            'raw_text' => $text,
        ];
    }

    private function extractText(Document $document): string
    {
        $path = $document->file ? Storage::disk('public')->path($document->file) : null;

        if (! $path || ! is_file($path)) {
            return '';
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'docx' && class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();

            if ($zip->open($path) === true) {
                $xml = $zip->getFromName('word/document.xml') ?: '';
                $zip->close();

                if ($xml !== '') {
                    $xml = str_replace(['</w:p>', '</w:tr>', '</w:tc>'], "\n", $xml);
                    $xml = strip_tags($xml);
                    $xml = html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    $xml = preg_replace("/[ \t]+/", ' ', $xml) ?: $xml;
                    $xml = preg_replace("/\R{3,}/", "\n\n", $xml) ?: $xml;

                    return trim($xml);
                }
            }
        }

        if (in_array($extension, ['txt', 'text', 'md'], true)) {
            return trim((string) file_get_contents($path));
        }

        return '';
    }

    private function extractLines(string $text): array
    {
        return collect(preg_split('/\R+/', $text) ?: [])
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
    }

    private function extractLabelValue(array $lines, array $labels): ?string
    {
        foreach ($lines as $line) {
            foreach ($labels as $label) {
                $pattern = '/^'.preg_quote($label, '/').'\s*[:\-]?\s*(.+)$/iu';

                if (preg_match($pattern, $line, $matches)) {
                    return trim($matches[1]);
                }

                if (Str::contains(Str::lower($line), Str::lower($label))) {
                    $parts = preg_split('/[:\-]/', $line, 2);

                    if (is_array($parts) && count($parts) === 2) {
                        return trim((string) $parts[1]);
                    }
                }
            }
        }

        return null;
    }

    private function extractDateValue(array $lines, array $labels): ?Carbon
    {
        foreach ($lines as $line) {
            foreach ($labels as $label) {
                $pattern = '/^'.preg_quote($label, '/').'\s*[:\-]?\s*(.+)$/iu';

                if (preg_match($pattern, $line, $matches)) {
                    $date = $this->parseDateString($matches[1]);

                    if ($date) {
                        return $date;
                    }
                }
            }
        }

        return null;
    }

    private function parseDateString(string $value): ?Carbon
    {
        $normalized = Str::of($value)
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();

        $normalized = strtr(Str::lower($normalized), [
            'januari' => 'January',
            'februari' => 'February',
            'maret' => 'March',
            'april' => 'April',
            'mei' => 'May',
            'juni' => 'June',
            'juli' => 'July',
            'agustus' => 'August',
            'september' => 'September',
            'oktober' => 'October',
            'november' => 'November',
            'desember' => 'December',
        ]);

        try {
            return Carbon::parse($normalized);
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractCampus(array $lines, Document $document): string
    {
        $campus = $this->extractLabelValue($lines, [
            'Perguruan Tinggi',
            'Nama Perguruan Tinggi',
            'Instansi',
            'Mitra',
            'Nama PT',
        ]);

        if ($campus) {
            return $campus;
        }

        foreach ($lines as $line) {
            if (preg_match('/\b(universitas|institut|sekolah tinggi|politeknik|akademi)\b/iu', $line)) {
                return trim($line);
            }
        }

        if (filled($document->catatan) && preg_match('/(?:Mitra|Perguruan Tinggi|Kampus)\s*:\s*([^;]+)/iu', (string) $document->catatan, $matches)) {
            return trim((string) $matches[1]);
        }

        return $document->user?->peserta?->perguruanTinggi?->nama_pt ?? '-';
    }

    private function guessTitle(array $lines, Document $document): string
    {
        foreach ($lines as $line) {
            $normalized = trim($line);
            if ($normalized === '') {
                continue;
            }

            if (preg_match('/\b(mou|pks|kerja sama|perjanjian)\b/iu', $normalized) && mb_strlen($normalized) <= 120) {
                return $normalized;
            }
        }

        $fileName = $document->nama_dokumen ?: basename((string) $document->file);
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);

        return Str::of($fileName)
            ->replace(['_', '-'], ' ')
            ->squish()
            ->title()
            ->toString();
    }

    private function fallbackDocumentNumber(Document $document): string
    {
        if (filled($document->catatan) && preg_match('/Nomor\s*:\s*([^;]+)/iu', (string) $document->catatan, $matches)) {
            return trim((string) $matches[1]);
        }

        $source = basename((string) $document->file) ?: ($document->nama_dokumen ?: '');
        $source = pathinfo($source, PATHINFO_FILENAME);

        return Str::upper(Str::of($source)->replace(' ', '')->toString()) ?: ('DOC-'.$document->id);
    }

    private function documentTypeLabel(Document $document): string
    {
        return match (strtolower((string) $document->jenis_dokumen)) {
            'mou' => 'MoU',
            'pks' => 'PKS',
            'addendum' => 'Addendum',
            'surat_kerja_sama' => 'Surat Kerja Sama',
            default => ucfirst(str_replace('_', ' ', (string) $document->jenis_dokumen)),
        };
    }

    private function determineStatus(Document $document, ?Carbon $startDate, ?Carbon $endDate): string
    {
        $status = strtolower((string) $document->status);

        if (in_array($status, ['menunggu', 'pending'], true)) {
            return 'menunggu';
        }

        if (in_array($status, ['revisi', 'ditolak', 'rejected'], true)) {
            return 'nonaktif';
        }

        if ($endDate && $endDate->copy()->startOfDay()->lt(now()->startOfDay())) {
            return 'berakhir';
        }

        if ($startDate || $endDate || in_array($status, ['disetujui', 'approved', 'terverifikasi', 'valid', 'verified'], true)) {
            return 'aktif';
        }

        return 'menunggu';
    }

    private function determinePeriodLabel(?Carbon $startDate, ?Carbon $endDate, string $status): string
    {
        if ($endDate && $endDate->copy()->startOfDay()->lt(now()->startOfDay())) {
            return 'kedaluwarsa';
        }

        if ($endDate && now()->startOfDay()->diffInDays($endDate->copy()->startOfDay(), false) <= 30) {
            return 'akan berakhir';
        }

        if ($status === 'menunggu') {
            return 'menunggu verifikasi';
        }

        return 'masih berlaku';
    }

    private function formatDateOrDash(?Carbon $date): string
    {
        if (! $date) {
            return '-';
        }

        return $date->copy()->translatedFormat('d F Y');
    }
}
