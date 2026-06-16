<?php

use App\Http\Controllers\Admin\AccountVerificationController;
use App\Http\Controllers\Admin\AccessRoleController;
use App\Http\Controllers\Admin\AccountSettingController;
use App\Http\Controllers\Admin\DashboardActionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\Announcement;
use App\Models\Activity;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\Certificate;
use App\Models\Conversation;
use App\Models\Document;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\Message;
use App\Models\Report;
use App\Models\Internship;
use App\Models\Mentor;
use App\Models\Pembimbing;
use App\Models\PerguruanTinggi;
use App\Models\Peserta;
use App\Models\SecurityActivity;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\ProfileController;
use App\Services\Communication\CommunicationService;
use App\Services\Admin\CooperationDocumentService;
use App\Services\Admin\AdminDataService;
use App\Http\Controllers\CommunicationController;
use App\Services\Mentor\MentorDashboardDataService;
use App\Http\Controllers\Peserta\NotificationPreferenceController;
use App\Http\Controllers\Peserta\AttendanceController;
use App\Services\Pembimbing\PembimbingDataService;
use App\Services\Peserta\PesertaDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/dashboard/peserta', [DashboardActionController::class, 'storePeserta'])
        ->name('admin.dashboard.peserta.store');
    Route::post('/admin/dashboard/perguruan-tinggi', [DashboardActionController::class, 'storePerguruanTinggi'])
        ->name('admin.dashboard.perguruan-tinggi.store');
    Route::patch('/admin/pengguna/{user}/status', [DashboardActionController::class, 'updateUserStatus'])
        ->name('admin.pengguna.status.update');

    Route::get('/admin/verifikasi-administrasi', function () {
        return view('admin.verifikasi.index');
    })->name('admin.verifikasi.index');

    Route::get('/admin/verifikasi-administrasi/akun', [AccountVerificationController::class, 'index'])
        ->name('admin.verifikasi.akun');
    Route::patch('/admin/verifikasi-administrasi/akun/{user}/setujui', [AccountVerificationController::class, 'approve'])
        ->name('admin.verifikasi.akun.setujui');
    Route::patch('/admin/verifikasi-administrasi/akun/{user}/tolak', [AccountVerificationController::class, 'reject'])
        ->name('admin.verifikasi.akun.tolak');

    Route::get('/admin/verifikasi-administrasi/riwayat', function () {
        return view('admin.verifikasi.riwayat');
    })->name('admin.verifikasi.riwayat');

    Route::get('/admin/manajemen-pengguna', function () {
        return view('admin.pengguna.index');
    })->name('admin.pengguna.index');

    Route::get('/admin/manajemen-pengguna/peserta-magang', function () {
        $context = app(AdminDataService::class)->context();

        return view('admin.pengguna.peserta', [
            'adminParticipants' => $context['adminParticipants'] ?? collect(),
            'adminCampuses' => $context['adminCampuses'] ?? collect(),
        ]);
    })->name('admin.pengguna.peserta');

    Route::get('/admin/manajemen-pengguna/mentor', function () {
        $context = app(AdminDataService::class)->context();

        return view('admin.pengguna.mentor', [
            'adminMentors' => $context['adminMentors'] ?? collect(),
        ]);
    })->name('admin.pengguna.mentor');
    Route::post('/admin/manajemen-pengguna/mentor', [DashboardActionController::class, 'storeMentor'])
        ->name('admin.pengguna.mentor.store');

    Route::get('/admin/manajemen-pengguna/pembimbing-akademik', function () {
        $context = app(AdminDataService::class)->context();

        return view('admin.pengguna.pembimbing', [
            'adminAdvisors' => $context['adminAdvisors'] ?? collect(),
        ]);
    })->name('admin.pengguna.pembimbing');
    Route::post('/admin/manajemen-pengguna/pembimbing-akademik', [DashboardActionController::class, 'storePembimbing'])
        ->name('admin.pengguna.pembimbing.store');

    Route::get('/admin/manajemen-perguruan-tinggi', function () {
        $campuses = PerguruanTinggi::query()->latest()->get();
        $cooperationService = app(CooperationDocumentService::class);
        $cooperationDocuments = Document::query()
            ->where('kategori', 'Dokumen Kerja Sama')
            ->latest()
            ->get();
        $cooperationRows = $cooperationService->rows();
        $cooperationStats = $cooperationService->stats($cooperationRows);

        $campusStats = [
            'total' => $campuses->count(),
            'new_this_month' => $campuses->filter(fn (PerguruanTinggi $campus) => (bool) $campus->created_at?->isCurrentMonth())->count(),
        ];

        $cooperationStats['new_this_month'] = $cooperationDocuments
            ->filter(fn (Document $document) => (bool) $document->created_at?->isCurrentMonth())
            ->count();

        return view('admin.perguruan_tinggi.index', compact(
            'campusStats',
            'cooperationStats',
        ));
    })->name('admin.perguruan-tinggi.index');

    Route::get('/admin/manajemen-perguruan-tinggi/data', function () {
        return view('admin.perguruan_tinggi.data');
    })->name('admin.perguruan-tinggi.data');

    Route::get('/admin/manajemen-perguruan-tinggi/kerja-sama', function () {
        $cooperationService = app(CooperationDocumentService::class);
        $cooperationDocuments = Document::query()
            ->with(['user.peserta.perguruanTinggi'])
            ->where('kategori', 'Dokumen Kerja Sama')
            ->latest()
            ->get();

        $cooperationUploads = $cooperationDocuments->map(function (Document $document) {
            $participant = $document->user?->peserta;

            return [
                'id' => $document->id,
                'user_id' => $document->user_id,
                'nama' => $document->nama_dokumen,
                'pemilik' => $document->user?->name ?? '-',
                'nim' => $participant?->nim ?? '-',
                'kampus' => $participant?->perguruanTinggi?->nama_pt ?? '-',
                'jenis' => strtoupper((string) $document->jenis_dokumen),
                'jenis_key' => $document->jenis_dokumen,
                'file' => $document->file,
                'status' => strtolower((string) $document->status),
                'catatan' => $document->catatan ?: '-',
                'uploaded_at' => optional($document->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'size' => $document->ukuran_file ? number_format($document->ukuran_file / 1024, 1) . ' KB' : '-',
            ];
        });

        $cooperationStats = [
            'total' => $cooperationUploads->count(),
            'menunggu' => $cooperationUploads->where('status', 'menunggu')->count(),
            'disetujui' => $cooperationUploads->where('status', 'disetujui')->count(),
            'ditolak' => $cooperationUploads->where('status', 'ditolak')->count(),
            'revisi' => $cooperationUploads->where('status', 'revisi')->count(),
        ];

        $cooperationRows = $cooperationService->rows();
        $cooperationTableStats = $cooperationService->stats($cooperationRows);
        $cooperationCampuses = $cooperationRows->pluck('kampus')->filter()->unique()->sort()->values();
        $cooperationTypes = $cooperationRows->pluck('jenis')->filter()->unique()->sort()->values();
        $cooperationPeriods = $cooperationRows->pluck('masa')->filter()->unique()->sort()->values();

        return view('admin.perguruan_tinggi.kerjasama', compact(
            'cooperationUploads',
            'cooperationStats',
            'cooperationRows',
            'cooperationTableStats',
            'cooperationCampuses',
            'cooperationTypes',
            'cooperationPeriods',
        ));
    })->name('admin.perguruan-tinggi.kerjasama');

    Route::post('/admin/manajemen-perguruan-tinggi/kerja-sama', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kampus' => ['required', 'string', 'max:255'],
            'jenis_dokumen' => ['required', 'in:mou,pks,addendum,surat_kerja_sama'],
            'nomor_dokumen' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf');
        $directory = 'dokumen-kerjasama/admin/'.$user->id;
        $fileName = Str::slug($validated['nomor_dokumen'], '-') ?: ('kerjasama-'.$user->id);
        $path = $file->storeAs($directory, $fileName.'.'.$extension, 'public');

        $document = Document::create([
            'user_id' => $user->id,
            'nama_dokumen' => $validated['judul'],
            'kategori' => 'Dokumen Kerja Sama',
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'jenis_file' => strtoupper($extension),
            'file' => $path,
            'ukuran_file' => $file->getSize(),
            'status' => 'menunggu',
            'catatan' => 'Mitra: '.$validated['kampus'].'; Nomor: '.$validated['nomor_dokumen'],
        ]);

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Menambahkan dokumen kerja sama baru melalui halaman Data Kerja Sama.',
        ]);

        $row = app(CooperationDocumentService::class)->rows()->firstWhere('id', $document->id);

        return response()->json([
            'message' => 'Data kerja sama berhasil ditambahkan ke database.',
            'document' => $row,
        ]);
    })->name('admin.perguruan-tinggi.kerjasama.store');

    Route::patch('/admin/manajemen-perguruan-tinggi/kerja-sama/{document}', function (Request $request, Document $document) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);
        abort_unless($document->kategori === 'Dokumen Kerja Sama', 404);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kampus' => ['required', 'string', 'max:255'],
            'jenis_dokumen' => ['required', 'in:mou,pks,addendum,surat_kerja_sama'],
            'nomor_dokumen' => ['required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $existingPath = $document->file;
        $nextFilePath = $existingPath;
        $nextExtension = strtolower((string) ($document->jenis_file ?: pathinfo((string) $existingPath, PATHINFO_EXTENSION) ?: 'pdf'));

        if ($request->hasFile('file')) {
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->delete($existingPath);
            }

            $file = $request->file('file');
            $nextExtension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf');
            $directory = 'dokumen-kerjasama/admin/'.$user->id;
            $fileName = Str::slug($validated['nomor_dokumen'], '-') ?: ('kerjasama-'.$document->id);
            $nextFilePath = $file->storeAs($directory, $fileName.'.'.$nextExtension, 'public');
        } elseif ($existingPath) {
            $currentFileName = pathinfo((string) $existingPath, PATHINFO_FILENAME);
            $currentDirectory = trim(str_replace('\\', '/', dirname((string) $existingPath)), '.');
            $currentDirectory = $currentDirectory === '/' ? '' : $currentDirectory;
            $expectedFileName = Str::slug($validated['nomor_dokumen'], '-') ?: $currentFileName;
            $expectedRelativePath = ($currentDirectory !== '' ? $currentDirectory.'/' : '').$expectedFileName.'.'.$nextExtension;

            if ($expectedRelativePath !== $existingPath && Storage::disk('public')->exists($existingPath)) {
                Storage::disk('public')->move($existingPath, $expectedRelativePath);
                $nextFilePath = $expectedRelativePath;
            }
        }

        $document->update([
            'nama_dokumen' => $validated['judul'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'jenis_file' => strtoupper($nextExtension),
            'file' => $nextFilePath,
            'ukuran_file' => $request->hasFile('file') ? $request->file('file')->getSize() : $document->ukuran_file,
            'catatan' => 'Mitra: '.$validated['kampus'].'; Nomor: '.$validated['nomor_dokumen'],
        ]);

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memperbarui dokumen kerja sama pada halaman Data Kerja Sama.',
        ]);

        $row = app(CooperationDocumentService::class)->rows()->firstWhere('id', $document->id);

        return response()->json([
            'message' => 'Data kerja sama berhasil diperbarui di database.',
            'document' => $row,
        ]);
    })->name('admin.perguruan-tinggi.kerjasama.update');

    Route::delete('/admin/manajemen-perguruan-tinggi/kerja-sama/{document}', function (Request $request, Document $document) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);
        abort_unless($document->kategori === 'Dokumen Kerja Sama', 404);

        if ($document->file && Storage::disk('public')->exists($document->file)) {
            Storage::disk('public')->delete($document->file);
        }

        $name = $document->nama_dokumen;
        $document->delete();

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Menghapus dokumen kerja sama pada halaman Data Kerja Sama.',
        ]);

        return response()->json([
            'message' => 'Dokumen kerja sama "'.$name.'" berhasil dihapus.',
        ]);
    })->name('admin.perguruan-tinggi.kerjasama.destroy');

    Route::get('/admin/manajemen-perguruan-tinggi/kerja-sama/{document}/download', function (Request $request, Document $document) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);
        abort_unless($document->kategori === 'Dokumen Kerja Sama', 404);

        $fileName = basename($document->file ?: $document->nama_dokumen);
        $mimeType = strtolower((string) $document->jenis_file) === 'pdf'
            ? 'application/pdf'
            : 'application/octet-stream';

        if ($document->file && Storage::disk('public')->exists($document->file)) {
            return Storage::disk('public')->download($document->file, $fileName);
        }

        $fallbackContent = implode("\n", [
            'Portal Magang LLDIKTI Wilayah V Yogyakarta',
            'Dokumen kerja sama: '.$document->nama_dokumen,
            'Jenis dokumen: '.str_replace('_', ' ', $document->jenis_dokumen),
            'Status: '.$document->status,
            'Catatan: '.($document->catatan ?: '-'),
        ]);

        return response()->streamDownload(function () use ($fallbackContent) {
            echo $fallbackContent;
        }, $fileName, [
            'Content-Type' => $mimeType,
        ]);
    })->name('admin.perguruan-tinggi.kerjasama.download');

    Route::patch('/admin/manajemen-perguruan-tinggi/kerja-sama/{document}/review', function (Request $request, Document $document) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);
        abort_unless($document->kategori === 'Dokumen Kerja Sama', 404);

        $validated = $request->validate([
            'status' => ['required', 'in:disetujui,ditolak,revisi'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $document->update([
            'status' => strtolower($validated['status']),
            'catatan' => ($validated['catatan'] ?? null) ?: match ($validated['status']) {
                'disetujui' => 'Disetujui oleh admin.',
                'ditolak' => 'Ditolak oleh admin.',
                default => 'Perlu revisi sebelum disetujui.',
            },
        ]);

        if ($document->user) {
            Notification::create([
                'user_id' => $document->user->id,
                'judul' => 'Dokumen kerja sama diperbarui',
                'pesan' => 'Dokumen kerja sama "'.$document->nama_dokumen.'" telah '.$validated['status'].' oleh admin.',
                'dibaca' => false,
            ]);
        }

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memvalidasi dokumen kerja sama milik '.$document->user?->name.' menjadi '.$validated['status'].'.',
        ]);

        if ($request->expectsJson()) {
            $row = app(CooperationDocumentService::class)->rows()->firstWhere('id', $document->id);

            return response()->json([
                'message' => 'Status dokumen kerja sama berhasil diperbarui.',
                'document' => $row,
            ]);
        }

        return back()->with('success', 'Status dokumen kerja sama berhasil diperbarui.');
    })->name('admin.perguruan-tinggi.kerjasama.review');

    Route::get('/admin/manajemen-magang', function () {
        $context = app(AdminDataService::class)->context();
        $today = now()->startOfDay();

        $activeParticipants = Peserta::query()
            ->where('status', 'aktif')
            ->with(['internship'])
            ->get();

        $attendanceToday = Attendance::query()
            ->whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();

        $reportsCount = Report::query()->count();

        $activePlacements = Internship::query()
            ->whereIn('status', ['berjalan', 'aktif'])
            ->count();

        $runningPeriods = $activeParticipants
            ->map(fn (Peserta $peserta) => $peserta->program_magang)
            ->filter()
            ->unique()
            ->count();

        $magangStats = [
            'total_participants' => $activeParticipants->count(),
            'attendance_today' => $attendanceToday,
            'reports_count' => $reportsCount,
            'active_placements' => $activePlacements,
            'running_periods' => $runningPeriods,
        ];

        $previewRows = collect()
            ->concat(collect($context['adminAttendances'] ?? [])->take(3)->map(function (array $attendance) {
                return [
                    'id' => 'att-'.$attendance['id'],
                    'nama' => $attendance['nama'] ?? '-',
                    'kategori' => 'Absensi',
                    'periode' => $attendance['periode'] ?? '-',
                    'tanggal' => $attendance['tanggal'] ?? '-',
                    'detail' => trim(($attendance['masuk'] ?? '-') . ' - ' . ($attendance['keluar'] ?? '-') . ' | ' . ($attendance['keterangan'] ?? '-')),
                    'status' => strtolower((string) ($attendance['status'] ?? 'valid')),
                    'sort_at' => now()->timestamp,
                ];
            }))
            ->concat(collect($context['adminMagangActivities'] ?? [])->take(3)->map(function (array $activity) {
                return [
                    'id' => 'act-'.$activity['id'],
                    'nama' => $activity['nama'] ?? '-',
                    'kategori' => 'Kegiatan',
                    'periode' => $activity['periode'] ?? '-',
                    'tanggal' => $activity['tanggal'] ?? '-',
                    'detail' => $activity['kegiatan'] ?? '-',
                    'status' => strtolower((string) ($activity['status'] ?? 'berlangsung')),
                    'sort_at' => now()->timestamp,
                ];
            }))
            ->concat(collect($context['adminMagangReports'] ?? [])->take(3)->map(function (array $report) {
                return [
                    'id' => 'rep-'.$report['id'],
                    'nama' => $report['nama'] ?? '-',
                    'kategori' => 'Laporan',
                    'periode' => $report['periode'] ?? '-',
                    'tanggal' => $report['tanggal'] ?? '-',
                    'detail' => $report['judul'] ?? '-',
                    'status' => strtolower((string) ($report['status_raw'] ?? $report['status'] ?? 'review')),
                    'sort_at' => now()->timestamp,
                ];
            }))
            ->concat(collect($context['adminPlacements'] ?? [])->take(2)->map(function (array $placement) {
                return [
                    'id' => 'pls-'.$placement['id'],
                    'nama' => $placement['nama'] ?? '-',
                    'kategori' => 'Penempatan',
                    'periode' => $placement['periode'] ?? '-',
                    'tanggal' => $placement['tanggal'] ?? '-',
                    'detail' => ($placement['penempatan'] ?? '-') . ' - ' . ($placement['instansi'] ?? '-'),
                    'status' => strtolower((string) ($placement['status'] ?? 'berlangsung')),
                    'sort_at' => now()->timestamp,
                ];
            }))
            ->values();

        return view('admin.magang.index', [
            'magangStats' => $magangStats,
            'adminMagangPreview' => $previewRows,
        ]);
    })->name('admin.magang.index');

    Route::get('/admin/manajemen-magang/absensi', function () {
        $adminAttendances = app(AdminDataService::class)->context()['adminAttendances'] ?? collect();

        return view('admin.magang.absensi', compact('adminAttendances'));
    })->name('admin.magang.absensi');

    Route::get('/admin/manajemen-magang/dokumen', function () {
        return view('admin.magang.dokumen');
    })->name('admin.magang.dokumen');

    Route::get('/admin/manajemen-magang/dokumen/{user}/{jenisDokumen}/download', function (User $user, string $jenisDokumen) {
        $document = Document::query()
            ->where('user_id', $user->id)
            ->where('jenis_dokumen', $jenisDokumen)
            ->firstOrFail();

        $fileName = basename($document->file ?: $document->nama_dokumen);
        $mimeType = strtolower((string) $document->jenis_file) === 'pdf'
            ? 'application/pdf'
            : 'application/octet-stream';

        if ($document->file && Storage::disk('public')->exists($document->file)) {
            return Storage::disk('public')->download($document->file, $fileName);
        }

        $fallbackContent = implode("\n", [
            'Portal Magang LLDIKTI Wilayah V Yogyakarta',
            'Dokumen peserta: '.$user->name,
            'Jenis dokumen: '.$document->nama_dokumen,
            'Jenis data: '.str_replace('_', ' ', $document->jenis_dokumen),
            'Status: '.$document->status,
            'Catatan: '.($document->catatan ?: '-'),
        ]);

        return response()->streamDownload(function () use ($fallbackContent) {
            echo $fallbackContent;
        }, $fileName, [
            'Content-Type' => $mimeType,
        ]);
    })->name('admin.magang.dokumen.download');

    Route::get('/admin/manajemen-magang/kegiatan', function () {
        $context = app(AdminDataService::class)->context();

        return view('admin.magang.kegiatan', [
            'adminMagangActivities' => $context['adminMagangActivities'] ?? collect(),
            'adminMagangParticipants' => $context['adminParticipants'] ?? collect(),
        ]);
    })->name('admin.magang.kegiatan');

    Route::post('/admin/manajemen-magang/kegiatan', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'peserta_id' => ['required', 'integer', 'exists:pesertas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date'],
            'status' => ['nullable', 'in:belum_dikerjakan,aktif,selesai,terlambat'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $peserta = Peserta::query()
            ->with(['user', 'internship.mentor.user'])
            ->whereKey($validated['peserta_id'])
            ->firstOrFail();

        $filePath = $request->file('file')->store('assignments/admin/'.$user->id, 'public');

        $assignment = Assignment::create([
            'peserta_id' => $peserta->id,
            'mentor_id' => $peserta->internship?->mentor_id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['catatan'] ?? null,
            'prioritas' => $validated['kategori'] ?? 'Administrasi',
            'deadline' => $validated['deadline'],
            'status' => $validated['status'] ?? 'belum_dikerjakan',
            'progress' => $validated['progress'] ?? 0,
            'file_hasil' => $filePath,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        if ($peserta->user) {
            Notification::create([
                'user_id' => $peserta->user_id,
                'judul' => 'Kegiatan baru diterima',
                'pesan' => 'Anda menerima kegiatan baru: '.$assignment->judul,
                'dibaca' => false,
            ]);
        }

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Menambahkan kegiatan peserta magang baru melalui halaman Kegiatan Magang.',
        ]);

        $row = app(AdminDataService::class)->context()['adminMagangActivities']->firstWhere('id', $assignment->id);

        return response()->json([
            'message' => 'Kegiatan berhasil disimpan ke database.',
            'activity' => $row,
        ]);
    })->name('admin.magang.kegiatan.store');

    Route::patch('/admin/manajemen-magang/kegiatan/{assignment}', function (Request $request, Assignment $assignment) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'peserta_id' => ['required', 'integer', 'exists:pesertas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date'],
            'status' => ['nullable', 'in:belum_dikerjakan,aktif,selesai,terlambat'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $peserta = Peserta::query()
            ->with(['user', 'internship.mentor.user'])
            ->whereKey($validated['peserta_id'])
            ->firstOrFail();

        if ($request->hasFile('file')) {
            if ($assignment->file_hasil && Storage::disk('public')->exists($assignment->file_hasil)) {
                Storage::disk('public')->delete($assignment->file_hasil);
            }

            $assignment->file_hasil = $request->file('file')->store('assignments/admin/'.$user->id, 'public');
        }

        $assignment->peserta_id = $peserta->id;
        $assignment->mentor_id = $peserta->internship?->mentor_id;
        $assignment->judul = $validated['judul'];
        $assignment->deskripsi = $validated['catatan'] ?? $assignment->deskripsi;
        $assignment->prioritas = $validated['kategori'] ?? $assignment->prioritas ?? 'Administrasi';
        $assignment->deadline = $validated['deadline'];
        $assignment->status = $validated['status'] ?? $assignment->status ?? 'belum_dikerjakan';
        $assignment->progress = $validated['progress'] ?? $assignment->progress ?? 0;
        $assignment->catatan = $validated['catatan'] ?? $assignment->catatan;
        $assignment->save();

        if ($peserta->user) {
            Notification::create([
                'user_id' => $peserta->user_id,
                'judul' => 'Kegiatan diperbarui',
                'pesan' => 'Kegiatan '.$assignment->judul.' sudah diperbarui oleh admin.',
                'dibaca' => false,
            ]);
        }

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memperbarui kegiatan peserta magang pada halaman Kegiatan Magang.',
        ]);

        $row = app(AdminDataService::class)->context()['adminMagangActivities']->firstWhere('id', $assignment->id);

        return response()->json([
            'message' => 'Kegiatan berhasil diperbarui di database.',
            'activity' => $row,
        ]);
    })->name('admin.magang.kegiatan.update');

    Route::delete('/admin/manajemen-magang/kegiatan/{assignment}', function (Request $request, Assignment $assignment) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        if ($assignment->file_hasil && Storage::disk('public')->exists($assignment->file_hasil)) {
            Storage::disk('public')->delete($assignment->file_hasil);
        }

        $judul = $assignment->judul;
        $assignment->delete();

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Menghapus kegiatan peserta magang dari halaman Kegiatan Magang.',
        ]);

        return response()->json([
            'message' => 'Kegiatan "'.$judul.'" berhasil dihapus.',
        ]);
    })->name('admin.magang.kegiatan.destroy');

    Route::get('/admin/manajemen-magang/laporan', function () {
        $context = app(AdminDataService::class)->context();
        $reports = collect($context['adminMagangReports'] ?? [])
            ->filter(fn (array $report) => strtolower((string) ($report['jenis_raw'] ?? 'berkala')) !== 'akhir')
            ->values();

        return view('admin.magang.laporan', [
            'pageTitle' => 'Laporan Berkala',
            'adminMagangReports' => $reports,
        ]);
    })->name('admin.magang.laporan');

    Route::get('/admin/manajemen-magang/laporan-akhir', function () {
        $context = app(AdminDataService::class)->context();
        $reports = collect($context['adminMagangReports'] ?? [])
            ->filter(function (array $report) {
                $jenis = strtolower((string) ($report['jenis_raw'] ?? 'berkala'));
                $status = strtolower((string) ($report['status_raw'] ?? $report['status'] ?? 'pending'));
                $mentorNote = filled($report['catatan_mentor'] ?? null);
                $pembimbingNote = filled($report['catatan_pembimbing'] ?? null);

                return $jenis === 'akhir'
                    && in_array($status, ['approved', 'disetujui', 'selesai'], true)
                    && $mentorNote
                    && $pembimbingNote;
            })
            ->values();

        return view('admin.magang.laporan', [
            'pageTitle' => 'Laporan Akhir',
            'adminMagangReports' => $reports,
        ]);
    })->name('admin.magang.laporan-akhir');

    Route::patch('/admin/manajemen-magang/laporan/{report}/approve', function (Request $request, Report $report) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $report->loadMissing(['peserta.user', 'peserta.internship', 'peserta.assessments']);

        $jenisRaw = strtolower(trim((string) ($report->jenis ?? 'berkala')));
        abort_unless(str_contains($jenisRaw, 'akhir') || str_contains($jenisRaw, 'final'), 422, 'Hanya laporan akhir yang dapat disetujui.');

        abort_unless(filled($report->catatan_mentor) && filled($report->catatan_pembimbing), 422, 'Laporan belum disetujui mentor dan pembimbing.');
        abort_unless(in_array(strtolower((string) ($report->status ?? '')), ['approved', 'disetujui', 'selesai'], true), 422, 'Status laporan belum siap disetujui admin.');

        if (Schema::hasColumn('reports', 'admin_approved_at')) {
            $report->admin_approved_at = now();
        }

        $report->status = 'approved';
        $report->save();

        return response()->json([
            'message' => 'Laporan berhasil disetujui admin.',
            'status' => 'approved',
            'status_label' => 'Disetujui',
            'admin_approved_at' => optional($report->admin_approved_at)->toDateTimeString(),
        ]);
    })->name('admin.magang.laporan.approve');

    Route::post('/admin/manajemen-magang/laporan/{report}/sertifikat', function (Request $request, Report $report) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $report->loadMissing([
            'peserta.user',
            'peserta.internship',
            'peserta.assessments',
        ]);

        $peserta = $report->peserta;
        abort_unless($peserta, 404);

        $internship = $peserta->internship;
        $latestAssessment = $peserta->assessments()
            ->latest()
            ->first();

        abort_unless($report->status && in_array($report->status, ['approved', 'disetujui'], true), 422, 'Laporan harus disetujui terlebih dahulu.');
        abort_unless(! Schema::hasColumn('reports', 'admin_approved_at') || filled($report->admin_approved_at), 422, 'Laporan harus disetujui admin terlebih dahulu.');
        abort_unless($internship && ($internship->status === 'selesai' || ($internship->tanggal_selesai && now()->startOfDay()->greaterThanOrEqualTo($internship->tanggal_selesai->copy()->startOfDay()))), 422, 'Status magang peserta belum selesai.');

        $validated = $request->validate([
            'certificate' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $predikat = 'TBA';
        if ($latestAssessment && in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true) && (float) $latestAssessment->nilai_akhir > 0) {
            $predikat = match (true) {
                (float) $latestAssessment->nilai_akhir >= 90 => 'A+',
                (float) $latestAssessment->nilai_akhir >= 85 => 'A',
                (float) $latestAssessment->nilai_akhir >= 80 => 'AB',
                (float) $latestAssessment->nilai_akhir >= 75 => 'B+',
                (float) $latestAssessment->nilai_akhir >= 70 => 'B',
                (float) $latestAssessment->nilai_akhir >= 65 => 'BC',
                (float) $latestAssessment->nilai_akhir >= 60 => 'C',
                default => 'D',
            };
        }

        $certificate = Certificate::firstOrNew([
            'peserta_id' => $peserta->id,
            'jenis' => 'Magang',
        ]);

        if ($certificate->exists && $certificate->file && Storage::disk('public')->exists($certificate->file)) {
            Storage::disk('public')->delete($certificate->file);
        }

        $file = $validated['certificate'];
        $fileName = Str::slug($peserta->user?->name ?? 'peserta').'-sertifikat-'.now()->format('YmdHis').'.pdf';
        $filePath = $file->storeAs('sertifikat/'.$peserta->id, $fileName, 'public');

        if (! $certificate->exists || blank($certificate->nomor)) {
            $certificate->nomor = sprintf('LLDIKTI-V/MG/%s/%05d', now()->format('Y'), $peserta->id);
        }

        $certificate->fill([
            'peserta_id' => $peserta->id,
            'jenis' => 'Magang',
            'periode' => $peserta->program_magang ?: ($internship->instansi ?: 'Magang'),
            'predikat' => $predikat,
            'status' => 'terbit',
            'tanggal_terbit' => now()->toDateString(),
            'file' => $filePath,
        ]);
        $certificate->save();

        Notification::create([
            'user_id' => $peserta->user_id,
            'judul' => 'Sertifikat magang diterbitkan',
            'pesan' => 'Sertifikat magang Anda sudah diunggah oleh admin dan siap diunduh.',
            'dibaca' => false,
        ]);

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengunggah sertifikat magang untuk peserta '.$peserta->user?->name.'.',
        ]);

        return response()->json([
            'message' => 'Sertifikat berhasil diunggah.',
            'certificate' => [
                'nomor' => $certificate->nomor,
                'predikat' => $certificate->predikat,
                'status' => $certificate->status,
                'tanggal_terbit' => optional($certificate->tanggal_terbit)->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y'),
                'file_name' => basename($filePath),
                'download_url' => Storage::disk('public')->url($filePath),
            ],
        ]);
    })->name('admin.magang.laporan.sertifikat.store');

    Route::get('/admin/manajemen-magang/penempatan', function () {
        return view('admin.magang.penempatan');
    })->name('admin.magang.penempatan');

    Route::get('/admin/manajemen-magang/penempatan/lookup', function (Request $request) {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
        ]);

        $peserta = Peserta::query()
            ->with(['user', 'perguruanTinggi', 'internship.mentor.user'])
            ->where('nim', $validated['nim'])
            ->first();

        if (! $peserta) {
            return response()->json([
                'message' => 'Data peserta dengan NIM tersebut tidak ditemukan.',
            ], 404);
        }

        $internship = $peserta->internship;

        return response()->json([
            'peserta' => [
                'id' => $peserta->id,
                'nama' => $peserta->user?->name ?? '-',
                'nim' => $peserta->nim ?? '-',
                'prodi' => $peserta->jurusan ?? '-',
                'fakultas' => $peserta->fakultas ?? '-',
                'perguruan_tinggi' => $peserta->perguruanTinggi?->nama_pt ?? '-',
                'program_magang' => $peserta->program_magang ?? '-',
                'pembimbing_akademik' => $peserta->pembimbing_akademik ?? '-',
            ],
            'penempatan' => $internship ? [
                'id' => $internship->id,
                'mentor_id' => $internship->mentor_id,
                'mentor_nama' => $internship->mentor?->user?->name ?? '-',
                'posisi' => $internship->posisi ?: ($internship->divisi ?: '-'),
                'penempatan' => $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-')),
                'periode' => $peserta->program_magang ?? '-',
                'tanggal_penempatan' => optional($internship->tanggal_mulai)->format('Y-m-d') ?? '',
                'nip_mentor' => $internship->mentor?->nip ?? '',
                'email_mentor' => $internship->mentor?->user?->email ?? '',
            ] : null,
        ]);
    })->name('admin.magang.penempatan.lookup');

    Route::post('/admin/manajemen-magang/penempatan', function (Request $request) {
        $validated = $request->validate([
            'nim' => ['required', 'string', 'max:50'],
            'posisi' => ['required', 'string', 'max:255'],
            'periode' => ['required', 'string', 'max:100'],
            'tanggal_penempatan' => ['required', 'date'],
            'nip_mentor' => ['required', 'string', 'max:50'],
            'email_mentor' => ['nullable', 'email', 'max:255'],
        ]);

        $peserta = Peserta::query()
            ->with(['user', 'perguruanTinggi', 'internship'])
            ->where('nim', $validated['nim'])
            ->firstOrFail();

        $mentor = Mentor::query()
            ->with('user')
            ->where('nip', $validated['nip_mentor'])
            ->first();

        if (! $mentor) {
            return response()->json([
                'message' => 'Mentor dengan NIP tersebut tidak ditemukan.',
            ], 422);
        }

        if (filled($validated['email_mentor']) && strcasecmp((string) $mentor->user?->email, (string) $validated['email_mentor']) !== 0) {
            return response()->json([
                'message' => 'Email mentor tidak sesuai dengan NIP yang dipilih.',
            ], 422);
        }

        $pembimbing = Pembimbing::query()
            ->with('user')
            ->whereHas('user', function ($query) use ($peserta) {
                $query->where('name', $peserta->pembimbing_akademik);
            })
            ->first()
            ?? Pembimbing::query()->with('user')->oldest()->first();

        if (! $pembimbing) {
            return response()->json([
                'message' => 'Data pembimbing tidak ditemukan. Lengkapi data pembimbing terlebih dahulu.',
            ], 422);
        }

        $startDate = Carbon::parse($validated['tanggal_penempatan']);
        $endDate = $peserta->tanggal_selesai_magang
            ? $peserta->tanggal_selesai_magang->copy()
            : $startDate->copy()->addMonths(6);

        $internship = DB::transaction(function () use ($peserta, $mentor, $pembimbing, $validated, $startDate, $endDate) {
            return Internship::updateOrCreate(
                ['peserta_id' => $peserta->id],
                [
                    'mentor_id' => $mentor->id,
                    'pembimbing_id' => $pembimbing->id,
                    'instansi' => 'LLDIKTI Wilayah V Yogyakarta',
                    'unit_kerja' => 'LLDIKTI Wilayah V Yogyakarta',
                    'posisi' => $validated['posisi'],
                    'lokasi' => 'LLDIKTI Wilayah V Yogyakarta',
                    'tanggal_mulai' => $startDate->toDateString(),
                    'tanggal_selesai' => $endDate->toDateString(),
                    'divisi' => $validated['posisi'],
                    'status' => 'berjalan',
                    'deskripsi' => 'Penempatan peserta dibuat melalui menu admin manajemen magang.',
                ]
            );
        });

        $peserta->update([
            'program_magang' => $validated['periode'],
            'tanggal_mulai_magang' => $startDate->toDateString(),
            'tanggal_selesai_magang' => $endDate->toDateString(),
        ]);

        return response()->json([
            'message' => 'Penempatan berhasil disimpan ke database.',
            'placement_id' => $internship->id,
        ]);
    })->name('admin.magang.penempatan.store');

    Route::delete('/admin/manajemen-magang/penempatan/{internship}', function (Internship $internship) {
        $internship->delete();

        return response()->json([
            'message' => 'Penempatan berhasil dihapus dari database.',
        ]);
    })->name('admin.magang.penempatan.destroy');

    Route::get('/admin/manajemen-magang/periode', function () {
        return view('admin.magang.periode');
    })->name('admin.magang.periode');

    Route::get('/admin/manajemen-magang/penilaian', function () {
        $user = request()->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $assessments = Assessment::query()
            ->with(['peserta.user', 'peserta.perguruanTinggi', 'mentor.user', 'pembimbing.user'])
            ->latest()
            ->get();

        $parseComponents = function (?string $raw, float $fallbackFinal = 0): array {
            $decoded = json_decode((string) $raw, true);
            $components = is_array($decoded) ? $decoded : [];

            $presence = (int) ($components['presence'] ?? 0);
            $activity = (int) ($components['activity'] ?? 0);
            $report = (int) ($components['report'] ?? 0);
            $attitude = (int) ($components['attitude'] ?? ($fallbackFinal > 0 ? max(0, min(100, (int) round($fallbackFinal * 0.95))) : 0));
            $competency = (int) ($components['competency'] ?? round($fallbackFinal));

            return [
                'raw' => $raw ?: '-',
                'presence' => $presence,
                'activity' => $activity,
                'report' => $report,
                'attitude' => $attitude,
                'competency' => $competency,
            ];
        };

        $assessmentRows = $assessments->map(function (Assessment $assessment) use ($parseComponents) {
            $jenis = strtolower((string) $assessment->jenis);
            $status = strtolower((string) $assessment->status);
            $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);
            $components = $parseComponents($assessment->komponen, $finalScore);

            return [
                'id' => $assessment->id,
                'peserta_id' => $assessment->peserta_id,
                'peserta' => $assessment->peserta?->user?->name ?? '-',
                'nim' => $assessment->peserta?->nim ?? '-',
                'prodi' => $assessment->peserta?->jurusan ?? '-',
                'kampus' => $assessment->peserta?->perguruanTinggi?->nama_pt ?? '-',
                'jenis' => $jenis,
                'jenis_label' => $jenis === 'pembimbing' ? 'Pembimbing Akademik' : 'Mentor',
                'mentor_id' => $assessment->mentor_id,
                'pembimbing_id' => $assessment->pembimbing_id,
                'penilai' => $assessment->mentor?->user?->name ?? $assessment->pembimbing?->user?->name ?? '-',
                'penilai_role' => $assessment->mentor_id ? 'mentor' : ($assessment->pembimbing_id ? 'pembimbing' : '-'),
                'periode' => $assessment->periode ?: ($assessment->peserta?->program_magang ?? '-'),
                'komponen' => $components['raw'],
                'komponen_presence' => $components['presence'],
                'komponen_activity' => $components['activity'],
                'komponen_report' => $components['report'],
                'komponen_attitude' => $components['attitude'],
                'komponen_competency' => $components['competency'],
                'bobot' => (int) $assessment->bobot,
                'nilai' => number_format((float) $assessment->nilai, 2, '.', ''),
                'nilai_akhir' => number_format((float) $assessment->nilai_akhir, 2, '.', ''),
                'status' => $status,
                'status_label' => match ($status) {
                    'final', 'selesai', 'disetujui' => 'final',
                    'revisi' => 'revisi',
                    default => 'draft',
                },
                'catatan' => $assessment->catatan ?: '-',
                'tanggal' => optional($assessment->created_at)->translatedFormat('d M Y') ?? '-',
                'waktu' => optional($assessment->updated_at)->translatedFormat('d M Y H:i') ?? '-',
            ];
        })->values();

        $participants = Peserta::query()
            ->with(['user', 'perguruanTinggi'])
            ->latest()
            ->get()
            ->map(fn (Peserta $peserta) => [
                'id' => $peserta->id,
                'nama' => $peserta->user?->name ?? '-',
                'nim' => $peserta->nim ?? '-',
                'prodi' => $peserta->jurusan ?? '-',
                'kampus' => $peserta->perguruanTinggi?->nama_pt ?? '-',
                'periode' => $peserta->program_magang ?? '-',
            ])->values();

        $mentors = Mentor::query()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn (Mentor $mentor) => [
                'id' => $mentor->id,
                'nama' => $mentor->user?->name ?? '-',
                'nip' => $mentor->nip ?? '-',
                'email' => $mentor->user?->email ?? '-',
            ])->values();

        $pembimbings = Pembimbing::query()
            ->with('user')
            ->latest()
            ->get()
            ->map(fn (Pembimbing $pembimbing) => [
                'id' => $pembimbing->id,
                'nama' => $pembimbing->user?->name ?? '-',
                'nidn_nip' => $pembimbing->nidn_nip ?? '-',
                'email' => $pembimbing->user?->email ?? '-',
            ])->values();

        $stats = [
            'total' => $assessmentRows->count(),
            'mentor' => $assessmentRows->where('jenis', 'mentor')->count(),
            'pembimbing' => $assessmentRows->where('jenis', 'pembimbing')->count(),
            'final' => $assessmentRows->whereIn('status', ['final', 'selesai', 'disetujui'])->count(),
            'draft' => $assessmentRows->where('status', 'draft')->count(),
        ];

        return view('admin.magang.penilaian', compact(
            'assessmentRows',
            'participants',
            'mentors',
            'pembimbings',
            'stats'
        ));
    })->name('admin.magang.penilaian');

    Route::post('/admin/manajemen-magang/penilaian', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'peserta_id' => ['required', 'exists:pesertas,id'],
            'jenis' => ['required', 'in:mentor,pembimbing'],
            'mentor_id' => ['nullable', 'exists:mentors,id'],
            'pembimbing_id' => ['nullable', 'exists:pembimbings,id'],
            'periode' => ['nullable', 'string', 'max:255'],
            'komponen' => ['required', 'string', 'max:255'],
            'bobot' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_akhir' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,review,final,selesai,disetujui,revisi'],
            'catatan' => ['nullable', 'string'],
        ]);

        Assessment::create([
            'peserta_id' => $validated['peserta_id'],
            'mentor_id' => $validated['jenis'] === 'mentor' ? $validated['mentor_id'] : null,
            'pembimbing_id' => $validated['jenis'] === 'pembimbing' ? $validated['pembimbing_id'] : null,
            'jenis' => $validated['jenis'],
            'periode' => $validated['periode'] ?: null,
            'komponen' => $validated['komponen'],
            'bobot' => $validated['bobot'],
            'nilai' => $validated['nilai'],
            'nilai_akhir' => $validated['nilai_akhir'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?: null,
        ]);

        return back()->with('success', 'Penilaian berhasil ditambahkan.');
    })->name('admin.magang.penilaian.store');

    Route::patch('/admin/manajemen-magang/penilaian/{assessment}', function (Request $request, Assessment $assessment) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'peserta_id' => ['required', 'exists:pesertas,id'],
            'jenis' => ['required', 'in:mentor,pembimbing'],
            'mentor_id' => ['nullable', 'exists:mentors,id'],
            'pembimbing_id' => ['nullable', 'exists:pembimbings,id'],
            'periode' => ['nullable', 'string', 'max:255'],
            'komponen' => ['required', 'string', 'max:255'],
            'bobot' => ['required', 'integer', 'min:0', 'max:100'],
            'nilai' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_akhir' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'in:draft,review,final,selesai,disetujui,revisi'],
            'catatan' => ['nullable', 'string'],
        ]);

        $assessment->update([
            'peserta_id' => $validated['peserta_id'],
            'mentor_id' => $validated['jenis'] === 'mentor' ? $validated['mentor_id'] : null,
            'pembimbing_id' => $validated['jenis'] === 'pembimbing' ? $validated['pembimbing_id'] : null,
            'jenis' => $validated['jenis'],
            'periode' => $validated['periode'] ?: null,
            'komponen' => $validated['komponen'],
            'bobot' => $validated['bobot'],
            'nilai' => $validated['nilai'],
            'nilai_akhir' => $validated['nilai_akhir'],
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?: null,
        ]);

        return back()->with('success', 'Penilaian berhasil diperbarui.');
    })->name('admin.magang.penilaian.update');

    Route::delete('/admin/manajemen-magang/penilaian/{assessment}', function (Request $request, Assessment $assessment) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $assessment->delete();

        return back()->with('success', 'Penilaian berhasil dihapus.');
    })->name('admin.magang.penilaian.destroy');

    Route::get('/admin/komunikasi', function (Request $request) {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'super_admin'], true), 403);

        $selectedConversation = null;
        $conversationId = (int) $request->query('conversation');

        if ($conversationId) {
            $selectedConversation = Conversation::query()
                ->with(['peserta.user', 'mentor.user', 'pembimbing.user', 'admin', 'messages.sender'])
                ->whereKey($conversationId)
                ->first();

            $allowed = $selectedConversation
                && (
                    $selectedConversation->admin_id === $user->id
                    || $user->role === 'super_admin'
                );

            if (! $allowed) {
                $selectedConversation = null;
            }
        }

        if ($selectedConversation) {
            app(\App\Services\Communication\CommunicationService::class)->markRead($user, $selectedConversation);
        }

        return view('admin.komunikasi.index', [
            'activeTab' => 'semua',
            'communicationData' => app(\App\Services\Communication\CommunicationService::class)->forUser($user, $selectedConversation),
        ]);
    })->name('admin.komunikasi.index');

    $announcementRecipientsForRole = function (string $role): array {
        return User::query()
            ->where('role', $role)
            ->pluck('id')
            ->all();
    };

    $isPublishedAnnouncement = function (Announcement $announcement): bool {
        return in_array(strtolower((string) $announcement->status), ['published', 'dipublikasikan', 'active', 'aktif'], true);
    };

    $syncAnnouncementRecipients = function (Announcement $announcement, string $role) use ($announcementRecipientsForRole, $isPublishedAnnouncement): void {
        if ($isPublishedAnnouncement($announcement)) {
            $announcement->readers()->sync($announcementRecipientsForRole($role));
            return;
        }

        $announcement->readers()->detach();
    };

    $backfillAnnouncementRecipients = function (string $role) use ($announcementRecipientsForRole, $isPublishedAnnouncement): void {
        $recipientIds = $announcementRecipientsForRole($role);

        if (empty($recipientIds)) {
            return;
        }

        Announcement::query()
            ->whereRaw('LOWER(COALESCE(kategori, "")) = ?', [strtolower($role)])
            ->get()
            ->each(function (Announcement $announcement) use ($recipientIds, $isPublishedAnnouncement): void {
                if ($isPublishedAnnouncement($announcement)) {
                    $announcement->readers()->sync($recipientIds);
                }
            });
    };

    Route::get('/admin/komunikasi/pesan', [CommunicationController::class, 'admin'])
        ->name('admin.komunikasi.pesan');

    Route::patch('/admin/komunikasi/{conversation}/status', function (Request $request, Conversation $conversation) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);
        abort_unless($conversation->admin_id === $user->id || $user?->role === 'super_admin', 403);

        $validated = $request->validate([
            'status' => ['required', 'in:aktif,arsip'],
        ]);

        $conversation->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => $validated['status'] === 'arsip'
                ? 'Percakapan berhasil diarsipkan.'
                : 'Percakapan berhasil divalidasi.',
            'conversation_id' => $conversation->id,
            'status' => $conversation->status,
        ]);
    })->name('admin.komunikasi.conversation.status');

    Route::delete('/admin/komunikasi/{conversation}', function (Request $request, Conversation $conversation) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);
        abort_unless($conversation->admin_id === $user->id || $user?->role === 'super_admin', 403);

        $conversation->messages()->delete();
        $conversation->delete();

        return response()->json([
            'message' => 'Percakapan berhasil dihapus.',
            'conversation_id' => $conversation->id,
        ]);
    })->name('admin.komunikasi.conversation.destroy');

    Route::get('/admin/komunikasi/pengumuman', function () use ($backfillAnnouncementRecipients) {
        $backfillAnnouncementRecipients('peserta');
        $backfillAnnouncementRecipients('mentor');
        $backfillAnnouncementRecipients('pembimbing');

        $announcementRows = Announcement::query()
            ->with('author')
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $announcementData = $announcementRows->map(function (Announcement $announcement) {
            $status = strtolower((string) ($announcement->status ?? 'draft'));
            $date = $announcement->tanggal ?? $announcement->created_at;
            $timeGroup = 'bulan ini';

            if ($date) {
                $date = Carbon::parse($date);
                $timeGroup = $date->isToday()
                    ? 'hari ini'
                    : ($date->greaterThanOrEqualTo(now()->subDays(7)) ? 'minggu ini' : 'bulan ini');
            }

            return [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => Str::title((string) ($announcement->kategori ?? 'Peserta')),
                'author' => $announcement->author?->name ?? 'Super Admin',
                'date' => $date ? Carbon::parse($date)->translatedFormat('d M Y') : '-',
                'time' => $timeGroup,
                'status' => in_array($status, ['published', 'dipublikasikan', 'active', 'aktif'], true)
                    ? 'dipublikasikan'
                    : (in_array($status, ['scheduled', 'terjadwal'], true) ? 'terjadwal' : (in_array($status, ['archived', 'arsip', 'diarsipkan'], true) ? 'diarsipkan' : 'draft')),
                'views' => $announcement->readers()->count(),
                'content' => $announcement->isi ?? '-',
                'is_database' => true,
            ];
        })->values();

        return view('admin.komunikasi.pengumuman', [
            'announcementData' => $announcementData,
        ]);
    })->name('admin.komunikasi.pengumuman');

    Route::post('/admin/komunikasi/pengumuman', function (Request $request) use ($syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', \Illuminate\Validation\Rule::in(['peserta', 'mentor', 'pembimbing'])],
            'isi' => ['required', 'string'],
            'status' => ['required', 'in:draft,dipublikasikan,terjadwal,diarsipkan'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $announcement = Announcement::create([
            'user_id' => $user->id,
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'isi' => $validated['isi'],
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
        ]);

        $syncAnnouncementRecipients($announcement, $validated['kategori']);

        return response()->json([
            'message' => 'Pengumuman berhasil disimpan.',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => Str::title((string) $announcement->kategori),
                'author' => $announcement->author?->name ?? $user->name,
                'date' => optional($announcement->tanggal)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'time' => optional($announcement->tanggal)->isToday() ? 'hari ini' : 'bulan ini',
                'status' => $announcement->status,
                'views' => 0,
                'content' => $announcement->isi,
                'is_database' => true,
            ],
        ]);
    })->name('admin.komunikasi.pengumuman.store');

    Route::patch('/admin/komunikasi/pengumuman/{announcement}', function (Request $request, Announcement $announcement) use ($syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', \Illuminate\Validation\Rule::in(['peserta', 'mentor', 'pembimbing'])],
            'isi' => ['required', 'string'],
            'status' => ['required', 'in:draft,dipublikasikan,terjadwal,diarsipkan'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $announcement->update([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'isi' => $validated['isi'],
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'] ?? $announcement->tanggal,
            'user_id' => $announcement->user_id ?: $user->id,
        ]);

        $syncAnnouncementRecipients($announcement, $validated['kategori']);

        return response()->json([
            'message' => 'Pengumuman berhasil diperbarui.',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => Str::title((string) $announcement->kategori),
                'author' => $announcement->author?->name ?? $user->name,
                'date' => optional($announcement->tanggal)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'time' => optional($announcement->tanggal)->isToday() ? 'hari ini' : 'bulan ini',
                'status' => $announcement->status,
                'views' => $announcement->readers()->count(),
                'content' => $announcement->isi,
                'is_database' => true,
            ],
        ]);
    })->name('admin.komunikasi.pengumuman.update');

    Route::delete('/admin/komunikasi/pengumuman/{announcement}', function (Request $request, Announcement $announcement) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $announcement->delete();

        return response()->json([
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    })->name('admin.komunikasi.pengumuman.destroy');

    Route::get('/admin/komunikasi/notifikasi', function (Request $request) {
        $user = $request->user();
        abort_unless($user && in_array($user->role, ['admin', 'super_admin'], true), 403);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $notificationRows = Notification::query()
            ->with('user')
            ->latest('created_at')
            ->latest('id')
            ->get();

        $notificationData = $notificationRows->map(function (Notification $notification) {
            $payload = [];
            try {
                $decoded = json_decode((string) $notification->pesan, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            } catch (\Throwable $e) {
                $payload = [];
            }

            $user = $notification->user;
            $target = $payload['target'] ?? match ($user?->role) {
                'peserta' => 'Peserta Magang',
                'mentor' => 'Mentor',
                'pembimbing' => 'Pembimbing Akademik',
                'admin', 'super_admin' => 'Admin',
                default => 'Semua Pengguna',
            };

            $title = (string) ($notification->judul ?? 'Notifikasi Sistem');
            $message = (string) ($payload['message'] ?? $notification->pesan ?? '-');
            $category = match (true) {
                ! blank($payload['category'] ?? null) => (string) $payload['category'],
                str_contains(strtolower($title), 'verifikasi') || str_contains(strtolower($message), 'verifikasi') => 'Verifikasi',
                str_contains(strtolower($title), 'magang') || str_contains(strtolower($message), 'magang') => 'Magang',
                str_contains(strtolower($title), 'dokumen') || str_contains(strtolower($message), 'dokumen') => 'Administrasi',
                default => 'Sistem',
            };

            $date = $notification->created_at;
            $timeGroup = $date
                ? ($date->isToday()
                    ? 'hari ini'
                    : ($date->greaterThanOrEqualTo(now()->subDays(7)) ? 'minggu ini' : 'bulan ini'))
                : 'bulan ini';

            return [
                'id' => $notification->id,
                'title' => $title,
                'category' => $category,
                'target' => $target,
                'date' => $date?->translatedFormat('d M Y') ?? '-',
                'time' => $timeGroup,
                'sender' => $user?->name ?? 'Sistem',
                'message' => $message,
                'read' => (bool) $notification->dibaca,
            ];
        })->values();

        $preferences = $user
            ? $user->notificationPreference
            : null;

        $notificationPreferences = [
            ['key' => 'Pesan', 'active' => (bool) ($preferences?->pesan ?? true)],
            ['key' => 'Laporan', 'active' => (bool) ($preferences?->laporan ?? true)],
            ['key' => 'Penugasan', 'active' => (bool) ($preferences?->penugasan ?? true)],
            ['key' => 'Absensi', 'active' => (bool) ($preferences?->absensi ?? true)],
            ['key' => 'Pengumuman', 'active' => (bool) ($preferences?->pengumuman ?? true)],
            ['key' => 'Email', 'active' => (bool) ($preferences?->email ?? true)],
        ];

        return view('admin.komunikasi.notifikasi', [
            'notificationData' => $notificationData,
            'notificationPreferences' => $notificationPreferences,
        ]);
    })->name('admin.komunikasi.notifikasi');

    Route::post('/admin/komunikasi/notifikasi', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'target' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string'],
            'dibaca' => ['nullable', 'boolean'],
        ]);

        $notification = Notification::create([
            'user_id' => $user->id,
            'judul' => $validated['judul'],
            'pesan' => json_encode([
                'category' => $validated['kategori'],
                'target' => $validated['target'],
                'message' => $validated['message'],
            ], JSON_UNESCAPED_UNICODE),
            'dibaca' => (bool) ($validated['dibaca'] ?? false),
        ]);

        return response()->json([
            'message' => 'Notifikasi berhasil disimpan.',
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->judul,
                'category' => $validated['kategori'],
                'target' => $validated['target'],
                'date' => optional($notification->created_at)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'time' => optional($notification->created_at)->isToday() ? 'hari ini' : 'bulan ini',
                'sender' => $user->name,
                'message' => $validated['message'],
                'read' => (bool) $notification->dibaca,
            ],
        ]);
    })->name('admin.komunikasi.notifikasi.store');

    Route::patch('/admin/komunikasi/notifikasi/{notification}', function (Request $request, Notification $notification) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'target' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string'],
            'dibaca' => ['nullable', 'boolean'],
        ]);

        $notification->update([
            'judul' => $validated['judul'],
            'pesan' => json_encode([
                'category' => $validated['kategori'],
                'target' => $validated['target'],
                'message' => $validated['message'],
            ], JSON_UNESCAPED_UNICODE),
            'dibaca' => (bool) ($validated['dibaca'] ?? $notification->dibaca),
        ]);

        return response()->json([
            'message' => 'Notifikasi berhasil diperbarui.',
            'notification' => [
                'id' => $notification->id,
                'title' => $notification->judul,
                'category' => $validated['kategori'],
                'target' => $validated['target'],
                'date' => optional($notification->created_at)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'time' => optional($notification->created_at)->isToday() ? 'hari ini' : 'bulan ini',
                'sender' => $user->name,
                'message' => $validated['message'],
                'read' => (bool) $notification->dibaca,
            ],
        ]);
    })->name('admin.komunikasi.notifikasi.update');

    Route::delete('/admin/komunikasi/notifikasi/{notification}', function (Request $request, Notification $notification) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $notification->delete();

        return response()->json([
            'message' => 'Notifikasi berhasil dihapus.',
        ]);
    })->name('admin.komunikasi.notifikasi.destroy');

    Route::patch('/admin/komunikasi/notifikasi/preferensi', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'pesan' => ['required', 'boolean'],
            'laporan' => ['required', 'boolean'],
            'penugasan' => ['required', 'boolean'],
            'absensi' => ['required', 'boolean'],
            'pengumuman' => ['required', 'boolean'],
            'email' => ['required', 'boolean'],
        ]);

        $preference = NotificationPreference::updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return response()->json([
            'message' => 'Preferensi notifikasi berhasil disimpan.',
            'preference' => $preference,
        ]);
    })->name('admin.komunikasi.notifikasi.preference.update');

    Route::get('/admin/laporan-monitoring', function () {
        return view('admin.laporan_monitoring.index', ['activeTab' => 'dashboard']);
    })->name('admin.laporan-monitoring.index');

    Route::get('/admin/laporan-monitoring/rekap-absensi', function () {
        $context = app(AdminDataService::class)->context();
        $reviews = \App\Models\AttendanceRecapReview::query()
            ->get()
            ->keyBy('peserta_id');

        $attendanceRecaps = collect($context['adminAttendanceRecaps'] ?? collect())
            ->map(function (array $recap) use ($reviews) {
                $review = $reviews->get($recap['peserta_id'] ?? null);

                return array_merge($recap, [
                    'review_status' => $review?->status ?? 'draft',
                    'review_note' => $review?->catatan ?? '-',
                    'reviewed_at' => optional($review?->reviewed_at)->translatedFormat('d F Y H:i') ?? '-',
                ]);
            })
            ->values();

        return view('admin.laporan_monitoring.rekap_absensi', [
            'adminAttendanceRecaps' => $attendanceRecaps,
        ]);
    })->name('admin.laporan-monitoring.rekap-absensi');

    Route::get('/admin/laporan-monitoring/rekap-absensi/{peserta}/unduh', function (\Illuminate\Http\Request $request, \App\Models\Peserta $peserta) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $attendanceRows = \App\Models\Attendance::query()
            ->where('peserta_id', $peserta->id)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="rekap-absensi-'.$peserta->nim.'.csv"',
        ];

        return response()->streamDownload(function () use ($peserta, $attendanceRows) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['NIM', 'Nama', 'Tanggal', 'Masuk', 'Pulang', 'Status', 'Keterangan']);

            foreach ($attendanceRows as $attendance) {
                fputcsv($output, [
                    $peserta->nim,
                    $peserta->user?->name ?? '-',
                    optional($attendance->tanggal)->translatedFormat('d M Y') ?? '-',
                    $attendance->jam_masuk?->format('H:i') ?? '-',
                    $attendance->jam_pulang?->format('H:i') ?? '-',
                    str_replace('_', ' ', (string) $attendance->status),
                    $attendance->keterangan ?: '-',
                ]);
            }

            fclose($output);
        }, 'rekap-absensi-'.$peserta->nim.'.csv', $headers);
    })->name('admin.laporan-monitoring.rekap-absensi.download');

    Route::get('/admin/laporan-monitoring/rekap-absensi/ekspor', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $context = app(AdminDataService::class)->context();
        $reviews = \App\Models\AttendanceRecapReview::query()->get()->keyBy('peserta_id');
        $recaps = collect($context['adminAttendanceRecaps'] ?? collect())->map(function (array $recap) use ($reviews) {
            $review = $reviews->get($recap['peserta_id'] ?? null);

            return array_merge($recap, [
                'review_status' => $review?->status ?? 'draft',
                'review_note' => $review?->catatan ?? '-',
            ]);
        });

        return response()->streamDownload(function () use ($recaps) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['NIM', 'Nama', 'Program Studi', 'Penempatan', 'Hadir', 'Izin', 'Tidak Hadir', 'Kehadiran', 'Status', 'Review', 'Catatan']);

            foreach ($recaps as $recap) {
                fputcsv($output, [
                    $recap['nim'] ?? '-',
                    $recap['nama'] ?? '-',
                    $recap['prodi'] ?? '-',
                    $recap['penempatan'] ?? '-',
                    $recap['present'] ?? 0,
                    $recap['permit'] ?? 0,
                    $recap['absent'] ?? 0,
                    ($recap['attendance'] ?? 0) . '%',
                    $recap['status'] ?? '-',
                    $recap['review_status'] ?? 'draft',
                    $recap['review_note'] ?? '-',
                ]);
            }

            fclose($output);
        }, 'rekap-absensi-semua.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('admin.laporan-monitoring.rekap-absensi.export');

    Route::post('/admin/laporan-monitoring/rekap-absensi/{peserta}/validasi', function (\Illuminate\Http\Request $request, \App\Models\Peserta $peserta) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = \App\Models\AttendanceRecapReview::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'reviewed_by' => $user->id,
                'status' => 'divalidasi',
                'catatan' => ($validated['catatan'] ?? null) ?: 'Rekap absensi telah divalidasi admin.',
                'reviewed_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Rekap absensi berhasil divalidasi.',
            'review' => [
                'peserta_id' => $review->peserta_id,
                'status' => $review->status,
                'catatan' => $review->catatan,
                'reviewed_at' => optional($review->reviewed_at)->translatedFormat('d F Y H:i') ?? now()->translatedFormat('d F Y H:i'),
            ],
        ]);
    })->name('admin.laporan-monitoring.rekap-absensi.validasi');

    Route::patch('/admin/laporan-monitoring/rekap-absensi/{peserta}', function (\Illuminate\Http\Request $request, \App\Models\Peserta $peserta) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:draft,divalidasi,perlu_tinjauan'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = \App\Models\AttendanceRecapReview::updateOrCreate(
            ['peserta_id' => $peserta->id],
            [
                'reviewed_by' => $user->id,
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?: null,
                'reviewed_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Review rekap absensi berhasil diperbarui.',
            'review' => [
                'peserta_id' => $review->peserta_id,
                'status' => $review->status,
                'catatan' => $review->catatan ?? '-',
                'reviewed_at' => optional($review->reviewed_at)->translatedFormat('d F Y H:i') ?? now()->translatedFormat('d F Y H:i'),
            ],
        ]);
    })->name('admin.laporan-monitoring.rekap-absensi.update');

    Route::delete('/admin/laporan-monitoring/rekap-absensi/{peserta}', function (\Illuminate\Http\Request $request, \App\Models\Peserta $peserta) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $review = \App\Models\AttendanceRecapReview::query()->where('peserta_id', $peserta->id)->first();
        if ($review) {
            $review->delete();
        }

        return response()->json([
            'message' => 'Review rekap absensi berhasil dihapus.',
            'peserta_id' => $peserta->id,
        ]);
    })->name('admin.laporan-monitoring.rekap-absensi.delete');

    Route::get('/admin/laporan-monitoring/rekap-kegiatan', function () {
        return view('admin.laporan_monitoring.rekap_kegiatan');
    })->name('admin.laporan-monitoring.rekap-kegiatan');

    Route::get('/admin/laporan-monitoring/statistik-pengguna', function () {
        $context = app(AdminDataService::class)->context();

        return view('admin.laporan_monitoring.statistik_pengguna', [
            'adminParticipants' => $context['adminParticipants'] ?? collect(),
            'adminMentors' => $context['adminMentors'] ?? collect(),
            'adminAdvisors' => $context['adminAdvisors'] ?? collect(),
            'adminUsers' => $context['adminUsers'] ?? collect(),
        ]);
    })->name('admin.laporan-monitoring.statistik-pengguna');

    Route::get('/admin/laporan-monitoring/statistik-pengguna/ekspor', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $context = app(AdminDataService::class)->context();
        $users = collect($context['adminUsers'] ?? collect());

        return response()->streamDownload(function () use ($users) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Nama', 'Username', 'Email', 'Peran', 'Instansi', 'Status', 'Tanggal Terdaftar', 'Terakhir Aktif']);

            foreach ($users as $item) {
                fputcsv($output, [
                    $item['nama'] ?? '-',
                    $item['username'] ?? '-',
                    $item['email'] ?? '-',
                    $item['role_label'] ?? ucfirst((string) ($item['role'] ?? '-')),
                    $item['instansi'] ?? '-',
                    $item['status'] ?? '-',
                    $item['tanggal'] ?? '-',
                    $item['verified_at'] ?? '-',
                ]);
            }

            fclose($output);
        }, 'statistik-pengguna.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('admin.laporan-monitoring.statistik-pengguna.export');

    Route::get('/admin/laporan-monitoring/statistik-pengguna/unduh', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $context = app(AdminDataService::class)->context();
        $users = collect($context['adminUsers'] ?? collect());

        return response()->streamDownload(function () use ($users) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Nama', 'Username', 'Email', 'Peran', 'Instansi', 'Status']);

            foreach ($users as $item) {
                fputcsv($output, [
                    $item['nama'] ?? '-',
                    $item['username'] ?? '-',
                    $item['email'] ?? '-',
                    $item['role_label'] ?? ucfirst((string) ($item['role'] ?? '-')),
                    $item['instansi'] ?? '-',
                    $item['status'] ?? '-',
                ]);
            }

            fclose($output);
        }, 'laporan-statistik-pengguna.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('admin.laporan-monitoring.statistik-pengguna.download');

    Route::patch('/admin/laporan-monitoring/statistik-pengguna/{user}/status', [DashboardActionController::class, 'toggleMonitoredUserStatus'])
        ->name('admin.laporan-monitoring.statistik-pengguna.status');

    Route::patch('/admin/laporan-monitoring/statistik-pengguna/{user}', [DashboardActionController::class, 'updateMonitoredUser'])
        ->name('admin.laporan-monitoring.statistik-pengguna.update');

    Route::delete('/admin/laporan-monitoring/statistik-pengguna/{user}', [DashboardActionController::class, 'destroyMonitoredUser'])
        ->name('admin.laporan-monitoring.statistik-pengguna.destroy');

    Route::get('/admin/laporan-monitoring/statistik-perguruan-tinggi/ekspor', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $context = app(AdminDataService::class)->context();
        $campuses = collect($context['adminCampuses'] ?? collect());

        return response()->streamDownload(function () use ($campuses) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Nama PT', 'Jenis PT', 'Provinsi', 'Status', 'Mahasiswa', 'Program Studi', 'Instansi Mitra', 'Terakhir Aktif']);

            foreach ($campuses as $item) {
                fputcsv($output, [
                    $item['nama'] ?? '-',
                    $item['jenis'] ?? '-',
                    $item['provinsi'] ?? '-',
                    $item['status'] ?? '-',
                    $item['mahasiswa_count'] ?? 0,
                    $item['program_studi_count'] ?? 0,
                    $item['instansi_mitra_count'] ?? 0,
                    $item['terakhir_aktif'] ?? '-',
                ]);
            }

            fclose($output);
        }, 'statistik-perguruan-tinggi.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('admin.laporan-monitoring.statistik-perguruan-tinggi.export');

    Route::get('/admin/laporan-monitoring/statistik-perguruan-tinggi/unduh', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $context = app(AdminDataService::class)->context();
        $campuses = collect($context['adminCampuses'] ?? collect());

        return response()->streamDownload(function () use ($campuses) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Nama PT', 'Jenis PT', 'Provinsi', 'Status', 'Mahasiswa']);

            foreach ($campuses as $item) {
                fputcsv($output, [
                    $item['nama'] ?? '-',
                    $item['jenis'] ?? '-',
                    $item['provinsi'] ?? '-',
                    $item['status'] ?? '-',
                    $item['mahasiswa_count'] ?? 0,
                ]);
            }

            fclose($output);
        }, 'laporan-statistik-perguruan-tinggi.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('admin.laporan-monitoring.statistik-perguruan-tinggi.download');

    Route::patch('/admin/laporan-monitoring/statistik-perguruan-tinggi/{campus}/status', [DashboardActionController::class, 'toggleCampusMonitoringStatus'])
        ->name('admin.laporan-monitoring.statistik-perguruan-tinggi.status');

    Route::patch('/admin/laporan-monitoring/statistik-perguruan-tinggi/{campus}', [DashboardActionController::class, 'updateCampusMonitoring'])
        ->name('admin.laporan-monitoring.statistik-perguruan-tinggi.update');

    Route::delete('/admin/laporan-monitoring/statistik-perguruan-tinggi/{campus}', [DashboardActionController::class, 'destroyCampusMonitoring'])
        ->name('admin.laporan-monitoring.statistik-perguruan-tinggi.destroy');

    Route::get('/admin/laporan-monitoring/statistik-perguruan-tinggi', function () {
        $context = app(AdminDataService::class)->context();

        return view('admin.laporan_monitoring.statistik_perguruan_tinggi', [
            'adminCampuses' => $context['adminCampuses'] ?? collect(),
        ]);
    })->name('admin.laporan-monitoring.statistik-perguruan-tinggi');

    Route::get('/admin/pengaturan', function () {
        return view('admin.pengaturan.index');
    })->name('admin.pengaturan.index');

    Route::get('/admin/pengaturan/profil-akun', function () {
        $user = request()->user()->loadMissing(['securityActivities' => fn ($query) => $query->latest()->limit(20)]);

        $profileHistories = $user->securityActivities->map(function ($activity) {
            $text = strtolower((string) $activity->aktivitas);
            $createdAt = $activity->created_at;
            $dateLabel = match (true) {
                $createdAt?->isToday() => 'Hari Ini',
                $createdAt?->isCurrentWeek() => 'Minggu Ini',
                $createdAt?->isCurrentMonth() => 'Bulan Ini',
                default => 'Sebelumnya',
            };

            $category = match (true) {
                str_contains($text, 'login'), str_contains($text, 'password'), str_contains($text, 'sandi'), str_contains($text, 'keamanan') => 'Keamanan',
                str_contains($text, 'email'), str_contains($text, 'telepon'), str_contains($text, 'nama'), str_contains($text, 'alamat') => 'Identitas',
                str_contains($text, 'zona waktu'), str_contains($text, 'tema'), str_contains($text, 'preferensi') => 'Preferensi',
                str_contains($text, 'verifikasi') => 'Verifikasi',
                default => 'Aktivitas',
            };

            return [
                'time' => optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'category' => $category,
                'detail' => $activity->catatan ?: $activity->aktivitas,
                'device' => $activity->perangkat ?: ($activity->browser ?: '-'),
                'location' => $activity->ip_address ?: '-',
                'status' => $activity->status ?: 'berhasil',
                'date' => $dateLabel,
            ];
        })->values();

        return view('admin.pengaturan.profil', compact('user', 'profileHistories'));
    })->name('admin.pengaturan.profil');

    Route::post('/admin/pengaturan/profil-akun', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ])->save();

        $deviceLabel = str_contains(strtolower((string) $request->userAgent()), 'mobile') ? 'Mobile' : (str_contains(strtolower((string) $request->userAgent()), 'tablet') ? 'Tablet' : 'Desktop');

        \App\Models\SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memperbarui profil akun admin.',
            'perangkat' => $deviceLabel,
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Nama, username, email, telepon, dan alamat diperbarui.',
        ]);

        return back()->with('success', 'Profil akun admin berhasil diperbarui.');
    })->name('admin.pengaturan.update');

    Route::get('/admin/pengaturan/profil-akun/unduh', [AccountSettingController::class, 'downloadProfile'])
        ->name('admin.pengaturan.profil.download');
    Route::get('/admin/pengaturan/profil-akun/histori/unduh', [AccountSettingController::class, 'exportProfileHistory'])
        ->name('admin.pengaturan.profil.history.export');
    Route::post('/admin/pengaturan/profil-akun/reset', [AccountSettingController::class, 'resetProfile'])
        ->name('admin.pengaturan.profil.reset');
    Route::post('/admin/pengaturan/profil-akun/verifikasi-ulang', [AccountSettingController::class, 'requestProfileReview'])
        ->name('admin.pengaturan.profil.verify');

    Route::post('/admin/pengaturan/foto', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $oldPhoto = $user->foto;
        $fotoPath = $request->file('foto')->store('foto-admin', 'public');

        $user->forceFill([
            'foto' => $fotoPath,
        ])->save();

        $deviceLabel = str_contains(strtolower((string) $request->userAgent()), 'mobile') ? 'Mobile' : (str_contains(strtolower((string) $request->userAgent()), 'tablet') ? 'Tablet' : 'Desktop');

        \App\Models\SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengubah foto profil admin.',
            'perangkat' => $deviceLabel,
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Foto profil berhasil diganti.',
        ]);

        if ($oldPhoto && $oldPhoto !== $fotoPath && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return response()->json([
            'message' => 'Foto profil admin berhasil diperbarui.',
            'avatar_url' => $user->fresh()->avatar_url,
            'foto' => $fotoPath,
        ]);
    })->name('admin.pengaturan.foto');

    Route::get('/admin/pengaturan/ubah-password', function () {
        $user = request()->user()->loadMissing(['securityActivities' => fn ($query) => $query->latest()->limit(20)]);

        $securityHistories = $user->securityActivities->map(function ($activity) {
            $text = strtolower((string) $activity->aktivitas);
            $type = match (true) {
                str_contains($text, 'password') => 'Password',
                str_contains($text, 'login') => 'Login',
                str_contains($text, 'verifikasi') => 'Verifikasi',
                str_contains($text, 'perangkat') => 'Perangkat',
                default => 'Aktivitas',
            };

            return [
                'jenis' => $type,
                'perangkat' => $activity->perangkat ?: '-',
                'browser' => $activity->browser ?: '-',
                'lokasi' => $activity->ip_address ?: '-',
                'waktu' => optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'status' => $activity->status ?: 'berhasil',
                'metode' => $activity->catatan ?: $activity->aktivitas,
                'aktivitas' => $activity->aktivitas,
            ];
        })->values();

        $passwordChangedAt = $user->password_changed_at ?? $user->updated_at;
        $twoFactorEnabled = (bool) $user->two_factor_enabled;

        return view('admin.pengaturan.password', compact('user', 'securityHistories', 'passwordChangedAt', 'twoFactorEnabled'));
    })->name('admin.pengaturan.password');

    Route::post('/admin/pengaturan/ubah-password', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'password_changed_at' => now(),
        ])->save();

        \App\Models\SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengubah password akun admin.',
            'perangkat' => str_contains(strtolower((string) $request->userAgent()), 'mobile') ? 'Mobile' : (str_contains(strtolower((string) $request->userAgent()), 'tablet') ? 'Tablet' : 'Desktop'),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Password akun berhasil diperbarui.',
        ]);

        return back()->with('success', 'Password admin berhasil diperbarui.');
    })->name('admin.pengaturan.password.update');

    Route::get('/admin/pengaturan/ubah-password/histori/unduh', [AccountSettingController::class, 'exportPasswordHistory'])
        ->name('admin.pengaturan.password.history.export');
    Route::post('/admin/pengaturan/ubah-password/2fa', [AccountSettingController::class, 'toggleTwoFactor'])
        ->name('admin.pengaturan.password.2fa');
    Route::post('/admin/pengaturan/ubah-password/logout-perangkat', [AccountSettingController::class, 'logoutDevices'])
        ->name('admin.pengaturan.password.logout-devices');
    Route::post('/admin/pengaturan/ubah-password/verifikasi-keamanan', [AccountSettingController::class, 'verifySecurity'])
        ->name('admin.pengaturan.password.verify-security');

    Route::get('/admin/pengaturan/hak-akses', [AccessRoleController::class, 'index'])
        ->name('admin.pengaturan.hak-akses');
    Route::post('/admin/pengaturan/hak-akses', [AccessRoleController::class, 'store'])
        ->name('admin.pengaturan.hak-akses.store');
    Route::patch('/admin/pengaturan/hak-akses/{accessRole}', [AccessRoleController::class, 'update'])
        ->name('admin.pengaturan.hak-akses.update');
    Route::patch('/admin/pengaturan/hak-akses/{accessRole}/status', [AccessRoleController::class, 'updateStatus'])
        ->name('admin.pengaturan.hak-akses.status');
    Route::delete('/admin/pengaturan/hak-akses/{accessRole}', [AccessRoleController::class, 'destroy'])
        ->name('admin.pengaturan.hak-akses.destroy');

    Route::get('/mentor/dashboard', function () {
        return view('mentor.dashboard', app(MentorDashboardDataService::class)->forUser(auth()->user()));
    })->name('mentor.dashboard');

    Route::get('/mentor/peserta-bimbingan', function () {
        $mentor = auth()->user()?->mentor;
        $mentorIds = collect([$mentor?->id, auth()->id()])
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values()
            ->all();
        $today = Carbon::today();

        $internships = $mentor
            ? Internship::query()
                ->with([
                    'peserta.user.documents',
                    'peserta.perguruanTinggi',
                    'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'peserta.assignments' => fn ($query) => $query->orderByDesc('deadline')->orderByDesc('id'),
                    'mentor.user',
                ])
                ->whereIn('mentor_id', $mentorIds)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get()
            : collect();

        $pesertaBimbingan = $internships->map(function (Internship $internship) use ($today) {
            $peserta = $internship->peserta;
            $attendances = $peserta?->attendances ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();
            $reports = $peserta?->reports ?? collect();
            $assignments = $peserta?->assignments ?? collect();

            $latestAttendance = $attendances->first();
            $latestLogbook = $logbooks->first();
            $latestReport = $reports->first();
            $latestAssignment = $assignments->first();

            $attendanceTotal = $attendances->count();
            $attendanceValid = $attendances->whereIn('status', ['hadir', 'terlambat'])->count();
            $attendancePercent = $attendanceTotal > 0
                ? (int) round(($attendanceValid / $attendanceTotal) * 100)
                : 0;

            $start = $internship->tanggal_mulai ?: $peserta?->tanggal_mulai_magang;
            $end = $internship->tanggal_selesai ?: $peserta?->tanggal_selesai_magang;
            if ($start && ! $start instanceof Carbon) {
                $start = Carbon::parse($start);
            }
            if ($end && ! $end instanceof Carbon) {
                $end = Carbon::parse($end);
            }

            $totalDays = ($start && $end) ? max($start->diffInDays($end), 1) : 0;
            $elapsedDays = ($start && $totalDays > 0) ? max(0, min($totalDays, $start->diffInDays($today))) : 0;
            $progress = $totalDays > 0 ? (int) round(($elapsedDays / $totalDays) * 100) : 0;

            if ($internship->status === 'selesai') {
                $progress = 100;
            }

            $status = match ($internship->status) {
                'pending' => 'review',
                'selesai' => 'selesai',
                'berjalan' => $attendancePercent < 75 ? 'perlu perhatian' : 'aktif',
                default => 'aktif',
            };

            $latestActivity = collect([
                filled($latestReport?->judul) ? 'Laporan: '.$latestReport->judul.' ('.str_replace('_', ' ', $latestReport->status ?? '-').')' : null,
                filled($latestLogbook?->kegiatan) ? 'Logbook: '.$latestLogbook->kegiatan : null,
                filled($latestAssignment?->judul) ? 'Tugas: '.$latestAssignment->judul.' ('.str_replace('_', ' ', $latestAssignment->status ?? '-').')' : null,
                $latestAttendance ? 'Absensi: '.str_replace('_', ' ', $latestAttendance->status).' '.optional($latestAttendance->tanggal)->format('d M Y') : null,
            ])->filter()->first() ?? 'Belum ada aktivitas tercatat';

            $history = $latestReport?->catatan_mentor
                ?: $latestLogbook?->deskripsi
                ?: $latestAssignment?->catatan
                ?: $latestActivity;

            return [
                'id' => $internship->id,
                'peserta_id' => $peserta?->id,
                'user_id' => $peserta?->user_id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'kampus' => $peserta?->perguruanTinggi?->nama_pt ?? '-',
                'periode' => $peserta?->program_magang ?? '-',
                'divisi' => $internship->divisi ?: '-',
                'penempatan' => $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: '-')),
                'pa' => $peserta?->pembimbing_akademik ?? '-',
                'status' => $status,
                'progress' => $progress,
                'hadir' => $attendancePercent,
                'aktivitas' => $latestActivity,
                'dokumen' => $peserta?->user?->documents?->count() ?? 0,
                'histori' => $history,
                'foto' => $peserta?->user?->foto ? asset('storage/'.$peserta->user->foto) : null,
                'instansi' => $internship->instansi ?: 'LLDIKTI Wilayah V Yogyakarta',
                'lokasi' => $internship->lokasi ?: 'LLDIKTI Wilayah V Yogyakarta',
            ];
        })->values();

        $mentorSummary = [
            'total' => $pesertaBimbingan->count(),
            'aktif' => $pesertaBimbingan->where('status', 'aktif')->count(),
            'review' => $pesertaBimbingan->where('status', 'review')->count(),
            'selesai' => $pesertaBimbingan->where('status', 'selesai')->count(),
            'perlu_perhatian' => $pesertaBimbingan->where('status', 'perlu perhatian')->count(),
            'rata_progress' => $pesertaBimbingan->count() ? (int) round($pesertaBimbingan->avg('progress')) : 0,
            'rata_hadir' => $pesertaBimbingan->count() ? (int) round($pesertaBimbingan->avg('hadir')) : 0,
        ];

        return view('mentor.peserta', compact('pesertaBimbingan', 'mentorSummary'));
    })->name('mentor.peserta');

    Route::get('/mentor/monitoring', function () {
        return view('mentor.monitoring', app(MentorDashboardDataService::class)->monitoringForUser(auth()->user()));
    })->name('mentor.monitoring');

    Route::get('/mentor/laporan', function (Request $request) {
        $user = $request->user()->loadMissing(['mentor']);
        abort_unless($user->mentor, 403);

        $reports = Report::query()
            ->with(['peserta.user', 'peserta.internship', 'reviewer'])
            ->whereHas('peserta.internship.mentor', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get()
            ->map(function (Report $report) use ($user) {
                $jenisRaw = strtolower(trim((string) ($report->jenis ?? 'berkala')));
                $isFinalReport = str_contains($jenisRaw, 'akhir') || str_contains($jenisRaw, 'final');

                $createdAt = $report->created_at instanceof Carbon
                    ? $report->created_at
                    : Carbon::parse($report->created_at ?? now());
                $updatedAt = $report->updated_at instanceof Carbon
                    ? $report->updated_at
                    : Carbon::parse($report->updated_at ?? $createdAt);

                $status = match (strtolower((string) ($report->status ?? 'pending'))) {
                    'approved', 'disetujui' => 'disetujui',
                    'revisi', 'rejected', 'ditolak' => 'perlu revisi',
                    default => 'menunggu review',
                };

                if ($status === 'menunggu review' && filled($report->catatan_mentor)) {
                    $status = 'review selesai';
                }

                $reviewDays = max(0, $createdAt->diffInDays($updatedAt));

                return [
                    'id' => $report->id,
                    'nama' => $report->peserta?->user?->name ?? '-',
                    'nim' => $report->peserta?->nim ?? '-',
                    'prodi' => $report->peserta?->jurusan ?? '-',
                    'penempatan' => $report->peserta?->internship?->divisi
                        ?: $report->peserta?->internship?->unit_kerja
                        ?: $report->peserta?->internship?->posisi
                        ?: '-',
                    'jenis_raw' => $isFinalReport ? 'akhir' : 'berkala',
                    'jenis' => $isFinalReport ? 'Laporan Akhir' : 'Laporan Berkala',
                    'judul' => $report->judul ?: 'Laporan Magang',
                    'unggah' => optional($createdAt)->translatedFormat('d M Y') ?? '-',
                    'unggah_raw' => $createdAt->toDateString(),
                    'period' => $report->periode ?: ($report->peserta?->program_magang ?? '-'),
                    'status' => $status,
                    'catatan' => $report->catatan_mentor ?: $report->catatan ?: 'Belum ada catatan mentor.',
                    'catatan_mentor' => $report->catatan_mentor ?: '-',
                    'catatan_pembimbing' => $report->catatan_pembimbing ?: '-',
                    'reviewer' => $report->reviewer?->name ?? $user->name ?? '-',
                    'download_url' => route('reports.download', $report),
                    'revisi' => $status === 'disetujui' ? 100 : ($status === 'perlu revisi' ? 72 : ($status === 'review selesai' ? 88 : 0)),
                    'reviewTime' => $reviewDays > 0 ? $reviewDays : 0,
                ];
            })
            ->values();

    return view('mentor.laporan', [
            'reportItems' => $reports,
        ]);
    })->name('mentor.laporan');

    Route::get('/mentor/laporan/export', function (Request $request) {
        $user = $request->user()->loadMissing(['mentor']);
        abort_unless($user->mentor, 403);

        $rows = Report::query()
            ->with(['peserta.user', 'peserta.internship', 'reviewer'])
            ->whereHas('peserta.internship.mentor', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get()
            ->map(function (Report $report) use ($user) {
                $jenisRaw = strtolower(trim((string) ($report->jenis ?? 'berkala')));
                $isFinalReport = str_contains($jenisRaw, 'akhir') || str_contains($jenisRaw, 'final');
                $createdAt = $report->created_at instanceof Carbon
                    ? $report->created_at
                    : Carbon::parse($report->created_at ?? now());
                $updatedAt = $report->updated_at instanceof Carbon
                    ? $report->updated_at
                    : Carbon::parse($report->updated_at ?? $createdAt);

                return [
                    'id' => $report->id,
                    'nama' => $report->peserta?->user?->name ?? '-',
                    'nim' => $report->peserta?->nim ?? '-',
                    'prodi' => $report->peserta?->jurusan ?? '-',
                    'judul' => $report->judul ?: 'Laporan Magang',
                    'jenis' => $isFinalReport ? 'Laporan Akhir' : 'Laporan Berkala',
                    'penempatan' => $report->peserta?->internship?->divisi
                        ?: $report->peserta?->internship?->unit_kerja
                        ?: $report->peserta?->internship?->posisi
                        ?: '-',
                    'unggah' => optional($createdAt)->translatedFormat('d M Y H:i') ?? '-',
                    'status' => ucfirst(str_replace('_', ' ', (string) ($report->status ?: 'draft'))),
                    'catatan_mentor' => $report->catatan_mentor ?: '-',
                    'catatan_pembimbing' => $report->catatan_pembimbing ?: '-',
                    'reviewer' => $report->reviewer?->name ?? $user->name ?? '-',
                    'review_days' => max(0, $createdAt->diffInDays($updatedAt)),
                ];
            });

        $filename = 'laporan-mentor-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nama', 'NIM', 'Prodi', 'Judul', 'Jenis', 'Penempatan', 'Unggah', 'Status', 'Reviewer', 'Hari Review', 'Catatan Mentor', 'Catatan Pembimbing']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['id'],
                    $row['nama'],
                    $row['nim'],
                    $row['prodi'],
                    $row['judul'],
                    $row['jenis'],
                    $row['penempatan'],
                    $row['unggah'],
                    $row['status'],
                    $row['reviewer'],
                    $row['review_days'],
                    $row['catatan_mentor'],
                    $row['catatan_pembimbing'],
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    })->name('mentor.laporan.export');

    Route::get('/mentor/monitoring/laporan', function () {
        return redirect()->route('mentor.laporan');
    })->name('mentor.monitoring.laporan');

    Route::patch('/mentor/laporan/{report}', function (Request $request, Report $report) {
        $user = $request->user()->loadMissing(['mentor']);
        abort_unless($user->mentor, 403);
        abort_unless($report->peserta?->internship?->mentor?->user_id === $user->id, 403);

        $validated = $request->validate([
            'action' => ['required', 'in:review,approve,reject'],
            'catatan' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($report, $validated, $user) {
            if (Schema::hasColumn('reports', 'catatan_mentor')) {
                $report->catatan_mentor = $validated['catatan'];
            }

            if (Schema::hasColumn('reports', 'reviewer_id')) {
                $report->reviewer_id = $user->id;
            }

            $report->status = match ($validated['action']) {
                'approve' => 'approved',
                'reject' => 'revisi',
                default => 'pending',
            };

            $report->save();
        });

        Notification::create([
            'user_id' => $report->peserta?->user_id,
            'judul' => 'Review laporan mentor diperbarui',
            'pesan' => 'Laporan '.$report->judul.' telah mendapat catatan dari mentor.',
            'dibaca' => false,
        ]);

        $payload = [
            'id' => $report->id,
            'catatan_mentor' => $report->catatan_mentor,
            'status' => match ($validated['action']) {
                'approve' => 'disetujui',
                'reject' => 'perlu revisi',
                default => filled($report->catatan_mentor) ? 'review selesai' : 'menunggu review',
            },
        ];

        return $request->expectsJson()
            ? response()->json($payload)
            : back()->with('success', 'Laporan berhasil diperbarui.');
    })->name('mentor.laporan.update');

    $buildMentorAttendanceData = function (): array {
        return app(MentorDashboardDataService::class)->monitoringForUser(auth()->user());
    };

    $resolveMentorAssignmentContext = function (?User $user): array {
        if (! $user) {
            abort(403);
        }

        $user->loadMissing('mentor');
        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $mentorIds = collect([$mentor->id, $mentor->user_id])
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->values()
            ->all();

        abort_unless(! empty($mentorIds), 403);

        return [$mentor, $mentorIds];
    };

    $resolveMentorAssignment = function (?User $user, Assignment $assignment) use ($resolveMentorAssignmentContext) {
        [$mentor, $mentorIds] = $resolveMentorAssignmentContext($user);

        $assignment = Assignment::query()
            ->with(['peserta.user', 'peserta.internship'])
            ->whereKey($assignment->id)
            ->whereHas('peserta.internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
            ->firstOrFail();

        return [$mentor, $mentorIds, $assignment];
    };

    Route::get('/mentor/monitoring/absensi', function () use ($buildMentorAttendanceData) {
        return view('mentor.monitoring.absensi', $buildMentorAttendanceData());
    })->name('mentor.monitoring.absensi');

    Route::get('/mentor/monitoring/penugasan', function () {
        return view('mentor.monitoring.penugasan', app(MentorDashboardDataService::class)->monitoringForUser(auth()->user()));
    })->name('mentor.monitoring.penugasan');

    Route::post('/mentor/monitoring/penugasan', function (Request $request) use ($resolveMentorAssignmentContext) {
        [$mentor, $mentorIds] = $resolveMentorAssignmentContext($request->user());

        $validated = $request->validate([
            'peserta_id' => ['required', 'integer', 'exists:pesertas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date'],
            'status' => ['nullable', 'in:belum_dikerjakan,aktif,selesai,terlambat'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $peserta = Peserta::query()
            ->with(['user', 'internship'])
            ->whereKey($validated['peserta_id'])
            ->whereHas('internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
            ->firstOrFail();

        $filePath = $request->file('file')->store('assignments', 'public');

        $assignment = Assignment::create([
            'peserta_id' => $peserta->id,
            'mentor_id' => $mentor->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['catatan'] ?? null,
            'prioritas' => $validated['kategori'] ?? 'Administrasi',
            'deadline' => $validated['deadline'],
            'status' => $validated['status'] ?? 'belum_dikerjakan',
            'progress' => $validated['progress'] ?? 0,
            'file_hasil' => $filePath,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        if ($peserta->user) {
            Notification::create([
                'user_id' => $peserta->user_id,
                'judul' => 'Penugasan baru diterima',
                'pesan' => 'Anda menerima penugasan baru: '.$assignment->judul,
                'dibaca' => false,
            ]);
        }

        return response()->json([
            'message' => 'Penugasan berhasil disimpan.',
            'id' => $assignment->id,
        ]);
    })->name('mentor.monitoring.penugasan.store');

    Route::patch('/mentor/monitoring/penugasan/{assignment}', function (Request $request, Assignment $assignment) use ($resolveMentorAssignment) {
        [$mentor, $mentorIds, $assignment] = $resolveMentorAssignment($request->user(), $assignment);

        $validated = $request->validate([
            'peserta_id' => ['required', 'integer', 'exists:pesertas,id'],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deadline' => ['required', 'date'],
            'status' => ['nullable', 'in:belum_dikerjakan,aktif,selesai,terlambat'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $peserta = Peserta::query()
            ->with(['user', 'internship'])
            ->whereKey($validated['peserta_id'])
            ->whereHas('internship', fn ($query) => $query->whereIn('mentor_id', $mentorIds))
            ->firstOrFail();

        if ($request->hasFile('file')) {
            if ($assignment->file_hasil && Storage::disk('public')->exists($assignment->file_hasil)) {
                Storage::disk('public')->delete($assignment->file_hasil);
            }

            $assignment->file_hasil = $request->file('file')->store('assignments', 'public');
        }

        $assignment->peserta_id = $peserta->id;
        $assignment->mentor_id = $mentor->id;
        $assignment->judul = $validated['judul'];
        $assignment->deskripsi = $validated['catatan'] ?? $assignment->deskripsi;
        $assignment->prioritas = $validated['kategori'] ?? $assignment->prioritas ?? 'Administrasi';
        $assignment->deadline = $validated['deadline'];
        $assignment->status = $validated['status'] ?? $assignment->status ?? 'belum_dikerjakan';
        $assignment->progress = $validated['progress'] ?? $assignment->progress ?? 0;
        $assignment->catatan = $validated['catatan'] ?? $assignment->catatan;
        $assignment->save();

        if ($peserta->user) {
            Notification::create([
                'user_id' => $peserta->user_id,
                'judul' => 'Penugasan diperbarui',
                'pesan' => 'Penugasan '.$assignment->judul.' sudah diperbarui oleh mentor.',
                'dibaca' => false,
            ]);
        }

        return response()->json([
            'message' => 'Penugasan berhasil diperbarui.',
            'id' => $assignment->id,
        ]);
    })->name('mentor.monitoring.penugasan.update');

    Route::get('/mentor/monitoring/penugasan/{assignment}/submission/download', function (Request $request, Assignment $assignment) use ($resolveMentorAssignment) {
        [, , $assignment] = $resolveMentorAssignment($request->user(), $assignment);

        abort_unless(filled($assignment->file_pengumpulan) && Storage::disk('public')->exists($assignment->file_pengumpulan), 404);

        return Storage::disk('public')->download($assignment->file_pengumpulan, basename((string) $assignment->file_pengumpulan));
    })->name('mentor.monitoring.penugasan.submission.download');

    Route::get('/mentor/monitoring/penugasan/{assignment}/download', function (Request $request, Assignment $assignment) use ($resolveMentorAssignment) {
        [, , $assignment] = $resolveMentorAssignment($request->user(), $assignment);

        abort_unless(filled($assignment->file_hasil) && Storage::disk('public')->exists($assignment->file_hasil), 404);

        return Storage::disk('public')->download($assignment->file_hasil, basename((string) $assignment->file_hasil));
    })->name('mentor.monitoring.penugasan.download');

    Route::delete('/mentor/monitoring/penugasan/{assignment}', function (Request $request, Assignment $assignment) use ($resolveMentorAssignment) {
        [, , $assignment] = $resolveMentorAssignment($request->user(), $assignment);

        if ($assignment->file_hasil && Storage::disk('public')->exists($assignment->file_hasil)) {
            Storage::disk('public')->delete($assignment->file_hasil);
        }

        $assignment->delete();

        return response()->json([
            'message' => 'Penugasan berhasil dihapus.',
        ]);
    })->name('mentor.monitoring.penugasan.destroy');

    Route::get('/mentor/monitoring/status-magang', function () {
        return view('mentor.monitoring.status', app(MentorDashboardDataService::class)->monitoringForUser(auth()->user()));
    })->name('mentor.monitoring.status');

    Route::match(['post', 'patch'], '/mentor/monitoring/{internship}/aksi', function (Request $request, Internship $internship) {
        $user = $request->user()->loadMissing('mentor');
        abort_unless($user->mentor, 403);
        abort_unless((int) $internship->mentor_id === (int) $user->mentor->id, 403);

        $validated = $request->validate([
            'action' => ['required', 'in:review aktivitas,berikan catatan'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $internship->loadMissing([
            'peserta.user',
            'peserta.reports' => fn ($query) => $query->orderByDesc('updated_at')->orderByDesc('id'),
            'peserta.assignments' => fn ($query) => $query->orderByDesc('updated_at')->orderByDesc('id'),
        ]);

        $peserta = $internship->peserta;
        abort_unless($peserta?->user, 404);

        $note = trim((string) ($validated['note'] ?? ''));
        $latestReport = $peserta->reports?->first();
        $latestAssignment = $peserta->assignments?->first();

        DB::transaction(function () use ($user, $validated, $peserta, $latestReport, $latestAssignment, $note) {
            Activity::create([
                'user_id' => $user->id,
                'aktivitas' => $validated['action'] === 'review aktivitas'
                    ? 'Memberikan tindak lanjut aktivitas kepada '.$peserta->user?->name.'.'
                    : 'Menambahkan catatan monitoring untuk '.$peserta->user?->name.'.',
            ]);

            if ($validated['action'] === 'review aktivitas' && $latestAssignment) {
                $latestAssignment->status = 'review';
                if (filled($note)) {
                    $latestAssignment->catatan = $note;
                }
                $latestAssignment->save();
            }

            if ($validated['action'] === 'berikan catatan' && $latestReport) {
                $latestReport->catatan_mentor = filled($note)
                    ? $note
                    : ($latestReport->catatan_mentor ?: 'Catatan monitoring ditambahkan oleh mentor.');
                if (in_array(strtolower((string) $latestReport->status), ['draft', 'pending', 'menunggu', 'review'], true)) {
                    $latestReport->status = 'review';
                }
                $latestReport->reviewer_id = $user->id;
                $latestReport->save();
            }

            Notification::create([
                'user_id' => $peserta->user_id,
                'judul' => $validated['action'] === 'review aktivitas'
                    ? 'Tindak lanjut aktivitas dari mentor'
                    : 'Catatan monitoring dari mentor',
                'pesan' => filled($note)
                    ? $note
                    : ($validated['action'] === 'review aktivitas'
                        ? 'Mentor memberikan tindak lanjut atas aktivitas Anda.'
                        : 'Mentor menambahkan catatan monitoring pada data Anda.'),
                'dibaca' => false,
            ]);
        });

        return response()->json([
            'message' => $validated['action'] === 'review aktivitas'
                ? 'Tindak lanjut aktivitas berhasil disimpan.'
                : 'Catatan monitoring berhasil disimpan.',
            'status' => $validated['action'] === 'review aktivitas' ? 'perlu review' : 'proses',
            'detail' => $note ?: ($validated['action'] === 'review aktivitas'
                ? 'Tindak lanjut aktivitas dicatat.'
                : 'Catatan monitoring ditambahkan.'),
        ]);
    })->name('mentor.monitoring.action');

    Route::get('/mentor/absensi', function () use ($buildMentorAttendanceData) {
        return view('mentor.monitoring.absensi', $buildMentorAttendanceData());
    })->name('mentor.absensi');

    Route::get('/mentor/penilaian', function () {
        $user = request()->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $internships = Internship::query()
            ->with([
                'peserta.user',
                'peserta.perguruanTinggi',
                'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.assessments' => fn ($query) => $query->orderByDesc('updated_at')->orderByDesc('id'),
            ])
            ->where('mentor_id', $mentor->id)
            ->latest('updated_at')
            ->latest('id')
            ->get();

        $assessments = $internships
            ->flatMap(function (Internship $internship) {
                return $internship->peserta?->assessments ?? collect();
            })
            ->sortByDesc('updated_at')
            ->sortByDesc('id')
            ->values();

        $assessmentRowsData = $assessments->map(function (Assessment $assessment) {
            $peserta = $assessment->peserta;
            $internship = $peserta?->internship;
            $attendances = $peserta?->attendances ?? collect();
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();

            $start = $peserta?->tanggal_mulai_magang?->copy() ?? $internship?->tanggal_mulai?->copy();
            $end = $peserta?->tanggal_selesai_magang?->copy() ?? $internship?->tanggal_selesai?->copy();
            $activity = 0;
            if ($start && $end) {
                $duration = max(1, $start->diffInDays($end));
                $elapsed = max(0, min($duration, $start->diffInDays(now())));
                $activity = (int) round(($elapsed / $duration) * 100);
            }

            $presence = $attendances->count() > 0
                ? (int) round(($attendances->whereIn('status', ['hadir', 'terlambat'])->count() / $attendances->count()) * 100)
                : 0;

            $latestReport = $reports->first();
            $reportScore = match (true) {
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['approved', 'disetujui', 'selesai'], true) => 100,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['review', 'revisi'], true) => 75,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['pending', 'menunggu', 'draft'], true) => 50,
                default => 0,
            };

            $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);
            $status = match (true) {
                in_array(strtolower((string) $assessment->status), ['final', 'selesai', 'disetujui'], true) => 'sudah dinilai',
                in_array(strtolower((string) $assessment->status), ['revisi', 'ditolak'], true) => 'perlu revisi',
                default => $finalScore > 0 ? 'sudah dinilai' : 'belum dinilai',
            };

            return [
                'id' => $assessment->id,
                'peserta_id' => $peserta?->id,
                'name' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'study' => $peserta?->jurusan ?? '-',
                'period' => $assessment->periode ?: ($peserta?->program_magang ?? '-'),
                'instansi' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'location' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'presence' => $presence,
                'activity' => $activity,
                'report' => $reportScore,
                'attitude' => (int) ($assessment->komponen ? (json_decode((string) $assessment->komponen, true)['attitude'] ?? 0) : 0),
                'competency' => (int) ($assessment->komponen ? (json_decode((string) $assessment->komponen, true)['competency'] ?? 0) : 0),
                'final' => (int) round($finalScore),
                'grade' => match (true) {
                    $finalScore >= 90 => 'A',
                    $finalScore >= 85 => 'B+',
                    $finalScore >= 75 => 'B',
                    $finalScore >= 70 => 'C+',
                    $finalScore > 0 => 'C',
                    default => '-',
                },
                'status' => $status,
                'note' => $assessment->catatan
                    ?: $latestReport?->catatan_mentor
                    ?: $latestReport?->catatan
                    ?: $logbooks->first()?->deskripsi
                    ?: 'Belum ada catatan penilaian.',
            ];
        })->values();

        $studyOptionsData = $assessmentRowsData->pluck('study')->filter()->unique()->values();
        $instansiOptionsData = $assessmentRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $assessmentRowsData->pluck('period')->filter()->unique()->values();

        return view('mentor.penilaian.index', compact(
            'assessmentRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));
    })->name('mentor.penilaian');

    Route::get('/mentor/penilaian/input-nilai', function () {
        $user = request()->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $internships = Internship::query()
            ->with([
                'peserta.user',
                'peserta.perguruanTinggi',
                'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.assessments' => fn ($query) => $query->orderByDesc('updated_at')->orderByDesc('id'),
            ])
            ->where('mentor_id', $mentor->id)
            ->latest('updated_at')
            ->latest('id')
            ->get();

        $scoreInputRowsData = $internships->map(function (Internship $internship) use ($mentor) {
            $peserta = $internship->peserta;
            $attendances = $peserta?->attendances ?? collect();
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();
            $latestAssessment = $peserta?->assessments
                ? $peserta->assessments->where('mentor_id', $mentor->id)->sortByDesc('updated_at')->sortByDesc('id')->first()
                : null;
            $statusRaw = strtolower((string) ($latestAssessment?->status ?? 'belum dinilai'));
            $storedComponents = json_decode((string) ($latestAssessment?->komponen ?? ''), true);
            $storedComponents = is_array($storedComponents) ? $storedComponents : [];

            $presence = $attendances->count() > 0
                ? (int) round(($attendances->whereIn('status', ['hadir', 'terlambat'])->count() / $attendances->count()) * 100)
                : 0;

            $activity = 0;
            $start = $peserta?->tanggal_mulai_magang?->copy() ?? $internship->tanggal_mulai?->copy();
            $end = $peserta?->tanggal_selesai_magang?->copy() ?? $internship->tanggal_selesai?->copy();
            if ($start && $end) {
                $duration = max(1, $start->diffInDays($end));
                $elapsed = max(0, min($duration, $start->diffInDays(now())));
                $activity = (int) round(($elapsed / $duration) * 100);
            }

            $latestReport = $reports->first();
            $reportScore = match (true) {
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['approved', 'disetujui', 'selesai'], true) => 100,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['review', 'revisi'], true) => 75,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['pending', 'menunggu', 'draft'], true) => 50,
                default => 0,
            };
            $finalScore = (float) ($latestAssessment?->nilai_akhir ?: $latestAssessment?->nilai ?: 0);

            return [
                'id' => $internship->id,
                'assessment_id' => $latestAssessment?->id,
                'peserta_id' => $peserta?->id,
                'mentor_id' => $mentor->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'instansi' => $internship->divisi ?: ($internship->unit_kerja ?: ($internship->posisi ?: 'LLDIKTI Wilayah V Yogyakarta')),
                'periode' => $latestAssessment?->periode ?: ($peserta?->program_magang ?? '-'),
                'status' => in_array($statusRaw, ['final', 'selesai', 'disetujui'], true)
                    ? 'sudah dinilai'
                    : (in_array($statusRaw, ['review'], true)
                        ? 'review'
                        : (in_array($statusRaw, ['draft', 'revisi', 'ditolak'], true) ? 'draft' : 'belum dinilai')),
                'disiplin' => (int) ($storedComponents['presence'] ?? $presence),
                'kinerja' => (int) ($storedComponents['activity'] ?? $activity),
                'laporan' => (int) ($storedComponents['report'] ?? $reportScore),
                'attitude' => (int) ($storedComponents['attitude'] ?? ($finalScore > 0 ? max(0, min(100, (int) round($finalScore * 0.95))) : 0)),
                'competency' => (int) ($storedComponents['competency'] ?? round($finalScore)),
                'final' => (int) round($finalScore),
                'grade' => match (true) {
                    $finalScore >= 90 => 'A',
                    $finalScore >= 85 => 'B+',
                    $finalScore >= 75 => 'B',
                    $finalScore >= 70 => 'C+',
                    $finalScore > 0 => 'C',
                    default => '-',
                },
                'note' => $latestAssessment?->catatan
                    ?: $latestReport?->catatan_mentor
                    ?: $latestReport?->catatan
                    ?: $logbooks->first()?->deskripsi
                    ?: 'Belum ada catatan penilaian.',
            ];
        })->values();

        $studyOptionsData = $scoreInputRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $scoreInputRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $scoreInputRowsData->pluck('periode')->filter()->unique()->values();

        return view('mentor.penilaian.input-nilai', compact(
            'scoreInputRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));

        $scoreInputRowsData = $assessments->map(function (Assessment $assessment) {
            $peserta = $assessment->peserta;
            $internship = $peserta?->internship;
            $attendances = $peserta?->attendances ?? collect();
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();
            $statusRaw = strtolower((string) ($assessment->status ?? 'belum dinilai'));
            $storedComponents = json_decode((string) $assessment->komponen, true);
            $storedComponents = is_array($storedComponents) ? $storedComponents : [];

            $presence = $attendances->count() > 0
                ? (int) round(($attendances->whereIn('status', ['hadir', 'terlambat'])->count() / $attendances->count()) * 100)
                : 0;

            $activity = 0;
            $start = $peserta?->tanggal_mulai_magang?->copy() ?? $internship?->tanggal_mulai?->copy();
            $end = $peserta?->tanggal_selesai_magang?->copy() ?? $internship?->tanggal_selesai?->copy();
            if ($start && $end) {
                $duration = max(1, $start->diffInDays($end));
                $elapsed = max(0, min($duration, $start->diffInDays(now())));
                $activity = (int) round(($elapsed / $duration) * 100);
            }

            $latestReport = $reports->first();
            $reportScore = match (true) {
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['approved', 'disetujui', 'selesai'], true) => 100,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['review', 'revisi'], true) => 75,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['pending', 'menunggu', 'draft'], true) => 50,
                default => 0,
            };

            $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);

            return [
                'id' => $assessment->id,
                'nama' => $peserta?->user?->name ?? '-',
                'name' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'study' => $peserta?->jurusan ?? '-',
                'instansi' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'periode' => $assessment->periode ?: ($peserta?->program_magang ?? '-'),
                'status' => in_array($statusRaw, ['final', 'selesai', 'disetujui'], true)
                    ? 'sudah dinilai'
                    : (in_array($statusRaw, ['review'], true)
                        ? 'review'
                        : (in_array($statusRaw, ['draft', 'revisi', 'ditolak'], true) ? 'draft' : 'belum dinilai')),
                'disiplin' => (int) ($storedComponents['presence'] ?? $presence),
                'kinerja' => (int) ($storedComponents['activity'] ?? $activity),
                'laporan' => (int) ($storedComponents['report'] ?? $reportScore),
                'attitude' => (int) ($storedComponents['attitude'] ?? ($finalScore > 0 ? max(0, min(100, (int) round($finalScore * 0.95))) : 0)),
                'competency' => (int) ($storedComponents['competency'] ?? round($finalScore)),
                'nilai' => (int) round($finalScore),
                'grade' => match (true) {
                    $finalScore >= 90 => 'A',
                    $finalScore >= 85 => 'B+',
                    $finalScore >= 75 => 'B',
                    $finalScore >= 70 => 'C+',
                    $finalScore > 0 => 'C',
                    default => '-',
                },
                'note' => $assessment->catatan
                    ?: $latestReport?->catatan
                    ?: $logbooks->first()?->deskripsi
                    ?: 'Belum ada catatan penilaian.',
            ];
        })->values();

        $studyOptionsData = $scoreInputRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $scoreInputRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $scoreInputRowsData->pluck('periode')->filter()->unique()->values();

        return view('mentor.penilaian.input-nilai', compact(
            'scoreInputRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));
    })->name('mentor.penilaian.input');

    Route::match(['post', 'patch'], '/mentor/penilaian/input-nilai/save', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $validated = $request->validate([
            'assessment_id' => ['nullable', 'exists:assessments,id'],
            'peserta_id' => ['required', 'exists:pesertas,id'],
            'periode' => ['nullable', 'string', 'max:255'],
            'presence' => ['required', 'integer', 'min:0', 'max:100'],
            'activity' => ['required', 'integer', 'min:0', 'max:100'],
            'report' => ['required', 'integer', 'min:0', 'max:100'],
            'attitude' => ['required', 'integer', 'min:0', 'max:100'],
            'competency' => ['required', 'integer', 'min:0', 'max:100'],
            'final' => ['required', 'integer', 'min:0', 'max:100'],
            'grade' => ['required', 'string', 'max:10'],
            'note' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,sudah dinilai'],
        ]);

        $assessment = ! empty($validated['assessment_id'])
            ? Assessment::query()->whereKey($validated['assessment_id'])->where('mentor_id', $mentor->id)->first()
            : null;

        $payload = [
            'peserta_id' => (int) $validated['peserta_id'],
            'mentor_id' => $mentor->id,
            'pembimbing_id' => null,
            'jenis' => 'mentor',
            'periode' => $validated['periode'] ?: null,
            'komponen' => json_encode([
                'presence' => (int) $validated['presence'],
                'activity' => (int) $validated['activity'],
                'report' => (int) $validated['report'],
                'attitude' => (int) $validated['attitude'],
                'competency' => (int) $validated['competency'],
                'grade' => $validated['grade'],
            ], JSON_UNESCAPED_UNICODE),
            'bobot' => 100,
            'nilai' => (float) $validated['final'],
            'nilai_akhir' => (float) $validated['final'],
            'status' => $validated['status'] === 'sudah dinilai' ? 'final' : 'draft',
            'catatan' => $validated['note'] ?: null,
        ];

        $assessment = $assessment ? tap($assessment)->update($payload) : Assessment::create($payload);

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'assessment' => [
                'id' => $assessment->id,
                'assessment_id' => $assessment->id,
                'peserta_id' => $assessment->peserta_id,
                'presence' => (int) $validated['presence'],
                'activity' => (int) $validated['activity'],
                'report' => (int) $validated['report'],
                'attitude' => (int) $validated['attitude'],
                'competency' => (int) $validated['competency'],
                'final' => (int) $validated['final'],
                'grade' => $validated['grade'],
                'status' => $validated['status'],
                'note' => $validated['note'] ?: '-',
            ],
        ]);
    })->name('mentor.penilaian.input.save');

    Route::patch('/mentor/penilaian/input-nilai/{assessment}', function (Request $request, Assessment $assessment) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor && $assessment->mentor_id === $mentor->id, 403);

        $validated = $request->validate([
            'presence' => ['required', 'integer', 'min:0', 'max:100'],
            'activity' => ['required', 'integer', 'min:0', 'max:100'],
            'report' => ['required', 'integer', 'min:0', 'max:100'],
            'attitude' => ['required', 'integer', 'min:0', 'max:100'],
            'competency' => ['required', 'integer', 'min:0', 'max:100'],
            'final' => ['required', 'integer', 'min:0', 'max:100'],
            'grade' => ['required', 'string', 'max:10'],
            'note' => ['nullable', 'string', 'max:65535'],
            'status' => ['required', 'in:draft,sudah dinilai'],
        ]);

        $components = [
            'presence' => (int) $validated['presence'],
            'activity' => (int) $validated['activity'],
            'report' => (int) $validated['report'],
            'attitude' => (int) $validated['attitude'],
            'competency' => (int) $validated['competency'],
            'grade' => $validated['grade'],
        ];

        $assessment->update([
            'komponen' => json_encode($components, JSON_UNESCAPED_UNICODE),
            'bobot' => 100,
            'nilai' => (float) $validated['final'],
            'nilai_akhir' => (float) $validated['final'],
            'status' => $validated['status'] === 'sudah dinilai' ? 'final' : 'draft',
            'catatan' => $validated['note'] ?: null,
        ]);

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'assessment' => [
                'id' => $assessment->id,
                'assessment_id' => $assessment->id,
                'peserta_id' => $assessment->peserta_id,
                'presence' => (int) $validated['presence'],
                'activity' => (int) $validated['activity'],
                'report' => (int) $validated['report'],
                'attitude' => (int) $validated['attitude'],
                'competency' => (int) $validated['competency'],
                'final' => (int) $validated['final'],
                'grade' => $validated['grade'],
                'status' => $validated['status'],
                'note' => $validated['note'] ?: '-',
            ],
        ]);
    })->name('mentor.penilaian.input.update');

    Route::get('/mentor/penilaian/rekap-nilai', function () {
        $user = request()->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $assessments = Assessment::query()
            ->with([
                'peserta.user',
                'peserta.internship',
                'peserta.perguruanTinggi',
            ])
            ->where('mentor_id', $mentor->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $rekapRowsData = $assessments->map(function (Assessment $assessment) {
            $peserta = $assessment->peserta;
            $internship = $peserta?->internship;
            $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);
            $statusRaw = strtolower((string) $assessment->status);
            $components = json_decode((string) $assessment->komponen, true);
            $components = is_array($components) ? $components : [];

            return [
                'id' => $assessment->id,
                'assessment_id' => $assessment->id,
                'peserta_id' => $peserta?->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'instansi' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'periode' => $assessment->periode ?: ($peserta?->program_magang ?? '-'),
                'presence' => (int) ($components['presence'] ?? 0),
                'activity' => (int) ($components['activity'] ?? 0),
                'report' => (int) ($components['report'] ?? 0),
                'attitude' => (int) ($components['attitude'] ?? 0),
                'competency' => (int) ($components['competency'] ?? 0),
                'final' => (int) round($finalScore),
                'grade' => match (true) {
                    $finalScore >= 90 => 'A',
                    $finalScore >= 85 => 'B+',
                    $finalScore >= 75 => 'B',
                    $finalScore >= 70 => 'C+',
                    $finalScore > 0 => 'C',
                    default => '-',
                },
                'status' => in_array($statusRaw, ['final', 'selesai', 'disetujui'], true)
                    ? 'sudah dinilai'
                    : (in_array($statusRaw, ['draft', 'revisi', 'ditolak'], true) ? 'draft' : 'belum dinilai'),
                'note' => $assessment->catatan ?: 'Belum ada catatan penilaian.',
                'updated' => optional($assessment->updated_at)->format('d M Y H:i') ?? '-',
            ];
        })->values();

        $studyOptionsData = $rekapRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $rekapRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $rekapRowsData->pluck('periode')->filter()->unique()->values();

        return view('mentor.penilaian.rekap-nilai', compact(
            'rekapRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));
    })->name('mentor.penilaian.rekap');

    Route::get('/mentor/penilaian/export/{assessment?}', function (Request $request, ?Assessment $assessment = null) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $query = Assessment::query()
            ->with([
                'peserta.user',
                'peserta.internship',
                'peserta.perguruanTinggi',
            ])
            ->where('mentor_id', $mentor->id);

        if ($assessment) {
            abort_unless((int) $assessment->mentor_id === (int) $mentor->id, 403);
            $query->whereKey($assessment->id);
        }

        $assessments = $query->latest('updated_at')->latest('id')->get();
        $format = strtolower($request->query('format', 'csv'));

        if ($format === 'pdf') {
            return Pdf::loadView('pdf.penilaian', [
                'assessments' => $assessments,
                'downloadedBy' => $user->name,
                'mentorName' => $mentor->user?->name ?? $user->name,
                'scopeLabel' => $assessment ? 'Satu Penilaian' : 'Rekap Semua Penilaian',
            ])
                ->setPaper('a4', 'portrait')
                ->download(Str::slug($assessment?->peserta?->user?->name ?? 'rekap-penilaian').'.pdf');
        }

        $filename = $assessment
            ? 'penilaian-'.Str::slug($assessment->peserta?->user?->name ?? 'peserta').'-'.now()->format('Ymd_His').'.csv'
            : 'rekap-penilaian-mentor-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($assessments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nama', 'NIM', 'Prodi', 'Periode', 'Kehadiran', 'Aktivitas', 'Laporan', 'Sikap', 'Kompetensi', 'Nilai Akhir', 'Grade', 'Status', 'Catatan']);

            foreach ($assessments as $assessmentItem) {
                $peserta = $assessmentItem->peserta;
                $components = json_decode((string) $assessmentItem->komponen, true);
                $components = is_array($components) ? $components : [];
                $finalScore = (float) ($assessmentItem->nilai_akhir ?: $assessmentItem->nilai ?: 0);

                fputcsv($handle, [
                    $assessmentItem->id,
                    $peserta?->user?->name ?? '-',
                    $peserta?->nim ?? '-',
                    $peserta?->jurusan ?? '-',
                    $assessmentItem->periode ?: ($peserta?->program_magang ?? '-'),
                    (int) ($components['presence'] ?? 0),
                    (int) ($components['activity'] ?? 0),
                    (int) ($components['report'] ?? 0),
                    (int) ($components['attitude'] ?? 0),
                    (int) ($components['competency'] ?? 0),
                    (int) round($finalScore),
                    match (true) {
                        $finalScore >= 90 => 'A',
                        $finalScore >= 85 => 'B+',
                        $finalScore >= 75 => 'B',
                        $finalScore >= 70 => 'C+',
                        $finalScore > 0 => 'C',
                        default => '-',
                    },
                    $assessmentItem->status,
                    $assessmentItem->catatan ?: '-',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    })->name('mentor.penilaian.export');

    Route::get('/mentor/komunikasi', function (Request $request) {
        abort_unless($request->user()?->role === 'mentor', 403);

        return view('mentor.komunikasi.index', [
            'communicationData' => app(\App\Services\Communication\CommunicationService::class)->forUser($request->user()),
        ]);
    })->name('mentor.komunikasi');

    Route::get('/mentor/komunikasi/export', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentorId = $user->mentor?->id;
        abort_unless($mentorId, 403);

        $rows = Conversation::query()
            ->with(['peserta.user', 'mentor.user', 'admin', 'messages.sender'])
            ->where('mentor_id', $mentorId)
            ->latest('last_message_at')
            ->latest('id')
            ->get()
            ->map(function (Conversation $conversation) use ($user) {
                $thread = $conversation->messages->sortBy('created_at')->values();
                $latest = $thread->last();

                return [
                    'id' => $conversation->id,
                    'topik' => $conversation->topik ?: 'Percakapan',
                    'pengguna' => $conversation->peserta?->user?->name
                        ?? $conversation->admin?->name
                        ?? $conversation->mentor?->user?->name
                        ?? 'Kontak',
                    'status' => $conversation->status ?: 'aktif',
                    'terakhir' => optional($conversation->last_message_at ?? $latest?->created_at)->translatedFormat('d M Y H:i') ?? '-',
                    'belum_dibaca' => $thread->where('sender_id', '!=', $user->id)->whereNull('dibaca_pada')->count(),
                    'pesan_terakhir' => Str::limit($latest?->pesan ?? 'Belum ada pesan', 120),
                ];
            });

        $filename = 'komunikasi-mentor-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Topik', 'Pengguna', 'Status', 'Terakhir', 'Belum Dibaca', 'Pesan Terakhir']);

            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row['id'],
                    $row['topik'],
                    $row['pengguna'],
                    $row['status'],
                    $row['terakhir'],
                    $row['belum_dibaca'],
                    $row['pesan_terakhir'],
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    })->name('mentor.komunikasi.export');

    Route::patch('/mentor/komunikasi/{conversation}/status', function (Request $request, Conversation $conversation) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);
        abort_unless($user->mentor && (int) $conversation->mentor_id === (int) $user->mentor->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:aktif,arsip'],
        ]);

        $conversation->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => $validated['status'] === 'arsip'
                ? 'Percakapan berhasil diarsipkan.'
                : 'Percakapan berhasil diaktifkan kembali.',
            'conversation_id' => $conversation->id,
            'status' => $conversation->status,
        ]);
    })->name('mentor.komunikasi.conversation.status');

    Route::redirect('/mentor/komunikasi/percakapan', '/mentor/komunikasi/pesan')->name('mentor.komunikasi.percakapan');

    Route::get('/mentor/komunikasi/pesan', [CommunicationController::class, 'mentor'])
        ->name('mentor.komunikasi.pesan');

    Route::get('/mentor/komunikasi/pesan/peserta/{peserta}', function (Request $request, Peserta $peserta) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $mentor = $user->mentor;
        abort_unless($mentor, 403);

        $peserta->loadMissing('user', 'internship');
        abort_unless((int) ($peserta->internship?->mentor_id ?? 0) === (int) $mentor->id, 403);

        $conversation = Conversation::firstOrCreate(
            [
                'peserta_id' => $peserta->id,
                'mentor_id' => $mentor->id,
                'pembimbing_id' => null,
                'admin_id' => null,
            ],
            [
                'topik' => 'Pesan Mentor',
                'status' => 'aktif',
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('mentor.komunikasi.pesan', ['conversation' => $conversation->id]);
    })->name('mentor.komunikasi.pesan.peserta');

    Route::get('/mentor/komunikasi/pengumuman', function (Request $request) use ($backfillAnnouncementRecipients) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $backfillAnnouncementRecipients('mentor');

        $userId = $user->id;

        $receivedRows = Announcement::query()
            ->with('author')
            ->where(function ($query) use ($userId) {
                $query->whereHas('readers', fn ($readerQuery) => $readerQuery->where('users.id', $userId))
                    ->orWhere(function ($fallbackQuery) {
                        $fallbackQuery->whereRaw('LOWER(COALESCE(kategori, "")) IN (?, ?)', ['pembimbing', 'pembimbing akademik'])
                            ->whereHas('author', fn ($authorQuery) => $authorQuery->whereIn('role', ['admin', 'super_admin']));
                    });
            })
            ->whereHas('author', fn ($query) => $query->whereIn('role', ['admin', 'super_admin']))
            ->whereRaw("LOWER(COALESCE(status, '')) IN ('published', 'dipublikasikan', 'active', 'aktif')")
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $sentRows = Announcement::query()
            ->with('author')
            ->where('user_id', $userId)
            ->whereRaw('LOWER(kategori) = ?', ['peserta'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $normalizeAnnouncement = function (Announcement $announcement, string $type) {
            $status = strtolower((string) ($announcement->status ?? 'draft'));
            $date = $announcement->tanggal ?? $announcement->created_at;
            $dateValue = $date ? Carbon::parse($date) : now();

            $normalizedStatus = match (true) {
                in_array($status, ['published', 'dipublikasikan', 'active', 'aktif'], true) => 'aktif',
                in_array($status, ['scheduled', 'terjadwal'], true) => 'terjadwal',
                in_array($status, ['archived', 'arsip', 'diarsipkan'], true) => 'berakhir',
                default => 'draft',
            };

            return [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => Str::title((string) ($announcement->kategori ?? 'Peserta')),
                'date' => $dateValue->format('Y-m-d'),
                'date_label' => $dateValue->translatedFormat('d M Y'),
                'target' => 'Peserta',
                'status' => $normalizedStatus,
                'read' => $announcement->readers()->count(),
                'priority' => match ($normalizedStatus) {
                    'aktif' => 'Tinggi',
                    'terjadwal' => 'Sedang',
                    'berakhir' => 'Rendah',
                    default => 'Rendah',
                },
                'schedule' => $dateValue->format('Y-m-d\TH:i'),
                'schedule_label' => $dateValue->format('d M Y H:i'),
                'content' => $announcement->isi ?? '-',
                'author' => $announcement->author?->name ?? '-',
                'type' => $type,
            ];
        };

        $receivedAnnouncementData = $receivedRows->map(fn (Announcement $announcement) => $normalizeAnnouncement($announcement, 'received'))->values();
        $sentAnnouncementData = $sentRows->map(fn (Announcement $announcement) => $normalizeAnnouncement($announcement, 'sent'))->values();

        return view('mentor.komunikasi.pengumuman', [
            'receivedAnnouncementData' => $receivedAnnouncementData,
            'sentAnnouncementData' => $sentAnnouncementData,
        ]);
    })->name('mentor.komunikasi.pengumuman');

    Route::post('/mentor/komunikasi/pengumuman', function (Request $request) use ($syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'status' => ['required', 'in:draft,aktif,terjadwal,berakhir'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $announcement = Announcement::create([
            'user_id' => $user->id,
            'judul' => $validated['judul'],
            'kategori' => 'peserta',
            'isi' => $validated['isi'],
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
        ]);

        $syncAnnouncementRecipients($announcement, 'peserta');

        return response()->json([
            'message' => 'Pengumuman berhasil disimpan.',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => 'Peserta',
                'date' => optional($announcement->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d'),
                'date_label' => optional($announcement->tanggal)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'target' => 'Peserta',
                'status' => $announcement->status,
                'read' => 0,
                'priority' => $announcement->status === 'aktif' ? 'Tinggi' : 'Sedang',
                'schedule' => optional($announcement->tanggal)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'),
                'schedule_label' => optional($announcement->tanggal)->translatedFormat('d M Y H:i') ?? now()->translatedFormat('d M Y H:i'),
                'content' => $announcement->isi,
                'author' => $user->name,
                'type' => 'sent',
            ],
        ]);
    })->name('mentor.komunikasi.pengumuman.store');

    Route::patch('/mentor/komunikasi/pengumuman/{announcement}', function (Request $request, Announcement $announcement) use ($syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);
        abort_unless((int) $announcement->user_id === (int) $user->id, 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'status' => ['required', 'in:draft,aktif,terjadwal,berakhir'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $announcement->update([
            'judul' => $validated['judul'],
            'kategori' => 'peserta',
            'isi' => $validated['isi'],
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'] ?? $announcement->tanggal,
        ]);

        $syncAnnouncementRecipients($announcement, 'peserta');

        return response()->json([
            'message' => 'Pengumuman berhasil diperbarui.',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => 'Peserta',
                'date' => optional($announcement->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d'),
                'date_label' => optional($announcement->tanggal)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'target' => 'Peserta',
                'status' => $announcement->status,
                'read' => $announcement->readers()->count(),
                'priority' => $announcement->status === 'aktif' ? 'Tinggi' : 'Sedang',
                'schedule' => optional($announcement->tanggal)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'),
                'schedule_label' => optional($announcement->tanggal)->translatedFormat('d M Y H:i') ?? now()->translatedFormat('d M Y H:i'),
                'content' => $announcement->isi,
                'author' => $user->name,
                'type' => 'sent',
            ],
        ]);
    })->name('mentor.komunikasi.pengumuman.update');

    Route::delete('/mentor/komunikasi/pengumuman/{announcement}', function (Request $request, Announcement $announcement) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        $isOwnAnnouncement = (int) $announcement->user_id === (int) $user->id;

        if ($isOwnAnnouncement) {
            $announcement->delete();
        } else {
            $announcement->readers()->detach($user->id);
        }

        return response()->json([
            'message' => 'Pengumuman berhasil dihapus.',
            'type' => $isOwnAnnouncement ? 'sent' : 'received',
            'announcement_id' => $announcement->id,
        ]);
    })->name('mentor.komunikasi.pengumuman.destroy');

    Route::get('/mentor/notifikasi', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $notificationRows = Notification::query()
            ->with('user')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->latest('id')
            ->get();

        $notificationData = $notificationRows->map(function (Notification $notification) {
            $payload = [];

            try {
                $decoded = json_decode((string) $notification->pesan, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            } catch (\Throwable $e) {
                $payload = [];
            }

            $title = (string) ($notification->judul ?? $payload['title'] ?? 'Notifikasi Sistem');
            $message = (string) ($payload['message'] ?? $notification->pesan ?? '-');
            $source = (string) ($payload['source'] ?? $payload['sender'] ?? 'Sistem');
            $category = match (true) {
                ! blank($payload['category'] ?? null) => (string) $payload['category'],
                str_contains(strtolower($title), 'verifikasi') || str_contains(strtolower($message), 'verifikasi') => 'Verifikasi',
                str_contains(strtolower($title), 'magang') || str_contains(strtolower($message), 'magang') => 'Magang',
                str_contains(strtolower($title), 'dokumen') || str_contains(strtolower($message), 'dokumen') => 'Administrasi',
                str_contains(strtolower($title), 'pengumuman') || str_contains(strtolower($message), 'pengumuman') => 'Pengumuman',
                str_contains(strtolower($title), 'pesan') || str_contains(strtolower($message), 'pesan') => 'Komunikasi',
                default => 'Sistem',
            };
            $date = $notification->created_at;
            $timeGroup = $date
                ? ($date->isToday()
                    ? 'hari ini'
                    : ($date->greaterThanOrEqualTo(now()->subDays(7)) ? 'minggu ini' : 'bulan ini'))
                : 'bulan ini';
            $status = strtolower((string) ($payload['status'] ?? ''));
            $status = in_array($status, ['belum dibaca', 'dibaca', 'selesai', 'arsip'], true)
                ? $status
                : ((bool) $notification->dibaca ? 'dibaca' : 'belum dibaca');
            $priority = (string) ($payload['priority'] ?? match (true) {
                str_contains(strtolower($title), 'menunggu') || str_contains(strtolower($message), 'menunggu') => 'Tinggi',
                str_contains(strtolower($title), 'penting') || str_contains(strtolower($message), 'penting') => 'Tinggi',
                str_contains(strtolower($title), 'baru') || str_contains(strtolower($message), 'baru') => 'Sedang',
                default => 'Rendah',
            });

            return [
                'id' => $notification->id,
                'title' => $title,
                'category' => $category,
                'source' => $source,
                'time' => $timeGroup,
                'date' => $date?->format('Y-m-d') ?? now()->format('Y-m-d'),
                'status' => $status,
                'priority' => $priority,
                'follow' => (string) ($payload['follow'] ?? match ($category) {
                    'Verifikasi' => 'Tinjau Verifikasi',
                    'Magang' => 'Lihat Aktivitas',
                    'Administrasi' => 'Buka Dokumen',
                    'Pengumuman' => 'Buka Pengumuman',
                    'Komunikasi' => 'Balas Pesan',
                    default => 'Lihat Detail',
                }),
                'content' => $message,
            ];
        })->values();

        $notificationPreferences = [
            ['key' => 'Pesan', 'active' => (bool) ($user->notificationPreference?->pesan ?? true)],
            ['key' => 'Laporan', 'active' => (bool) ($user->notificationPreference?->laporan ?? true)],
            ['key' => 'Penugasan', 'active' => (bool) ($user->notificationPreference?->penugasan ?? true)],
            ['key' => 'Absensi', 'active' => (bool) ($user->notificationPreference?->absensi ?? true)],
            ['key' => 'Pengumuman', 'active' => (bool) ($user->notificationPreference?->pengumuman ?? true)],
            ['key' => 'Email', 'active' => (bool) ($user->notificationPreference?->email ?? true)],
        ];

        return view('mentor.komunikasi.notifikasi', [
            'notificationData' => $notificationData,
            'notificationPreferences' => $notificationPreferences,
        ]);
    })->name('mentor.notifikasi');

    Route::patch('/mentor/notifikasi/{notification}', function (Request $request, Notification $notification) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);
        abort_unless((int) $notification->user_id === (int) $user->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:dibaca,selesai,arsip'],
        ]);

        $payload = [];
        try {
            $decoded = json_decode((string) $notification->pesan, true, 512, JSON_THROW_ON_ERROR);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        } catch (\Throwable $e) {
            $payload = ['message' => (string) $notification->pesan];
        }

        $payload['status'] = $validated['status'];
        $payload['category'] = $payload['category'] ?? 'Sistem';
        $payload['source'] = $payload['source'] ?? ($notification->user?->name ?? 'Sistem');
        $payload['priority'] = $payload['priority'] ?? ($validated['status'] === 'arsip' ? 'Rendah' : ($validated['status'] === 'selesai' ? 'Sedang' : 'Tinggi'));

        $notification->update([
            'dibaca' => true,
            'pesan' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'message' => 'Notifikasi berhasil diperbarui.',
            'notification_id' => $notification->id,
            'status' => $validated['status'],
        ]);
    })->name('mentor.notifikasi.update');

    Route::patch('/mentor/notifikasi/mark-all', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json([
            'message' => 'Semua notifikasi berhasil ditandai dibaca.',
        ]);
    })->name('mentor.notifikasi.mark-all');

    Route::delete('/mentor/notifikasi/{notification}', function (Request $request, Notification $notification) {
        $user = $request->user();
        abort_unless($user?->role === 'mentor', 403);
        abort_unless((int) $notification->user_id === (int) $user->id, 403);

        $notification->delete();

        return response()->json([
            'message' => 'Notifikasi berhasil dihapus.',
            'notification_id' => $notification->id,
        ]);
    })->name('mentor.notifikasi.destroy');

    Route::get('/mentor/pengaturan', function () {
        $user = request()->user()->loadMissing('mentor');

        return view('mentor.pengaturan.index', compact('user'));
    })->name('mentor.pengaturan');

    Route::get('/mentor/pengaturan/profil', function () {
        $user = auth()->user()?->loadMissing([
            'mentor',
            'verifiedBy',
            'securityActivities' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('mentor.pengaturan.profil', [
            'user' => $user,
            'securityActivities' => $user?->securityActivities ?? collect(),
        ]);
    })->name('mentor.pengaturan.profil');

    Route::post('/mentor/pengaturan/profil', function (Request $request) {
        $user = $request->user()->loadMissing('mentor');

        abort_unless($user->role === 'mentor' && $user->mentor, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => ['nullable', 'string', 'max:100'],
            'jenis_kelamin' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['required', 'string', 'max:255'],
            'divisi' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'instansi' => ['nullable', 'string', 'max:255'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
        ])->save();

        $mentorPayload = [
            'nip' => $validated['nip'] ?? null,
            'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
            'jabatan' => $validated['jabatan'],
            'divisi' => $validated['divisi'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'] ?? null,
            'instansi' => $validated['instansi'] ?? null,
        ];

        $user->mentor()->updateOrCreate(
            ['user_id' => $user->id],
            $mentorPayload
        );

        SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Memperbarui profil mentor.',
            'perangkat' => $request->userAgent(),
            'browser' => $request->header('User-Agent'),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => collect([
                'identitas' => $validated['name'],
                'username' => $validated['username'] ?? $user->username,
                'email' => $validated['email'],
                'nip' => $validated['nip'] ?? null,
                'jabatan' => $validated['jabatan'],
                'divisi' => $validated['divisi'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'] ?? null,
                'instansi' => $validated['instansi'] ?? null,
            ])->filter()->map(fn ($value, $key) => ucfirst(str_replace('_', ' ', $key)).': '.$value)->implode(' | '),
        ]);

        return back()->with('success', 'Profil mentor berhasil diperbarui.');
    })->name('mentor.pengaturan.update');

    Route::post('/mentor/pengaturan/foto', function (Request $request) {
        $user = $request->user()->loadMissing('mentor');

        abort_unless($user->role === 'mentor' && $user->mentor, 403);

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $oldPhoto = $user->foto;
        $fotoPath = $request->file('foto')->store('foto-mentor', 'public');

        $user->forceFill(['foto' => $fotoPath])->save();

        if ($oldPhoto && $oldPhoto !== $fotoPath && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return back()->with('success', 'Foto profil mentor berhasil diperbarui.');
    })->name('mentor.pengaturan.foto');

    Route::get('/mentor/pengaturan/ubah-password', function () {
        $user = request()->user()->loadMissing(['securityActivities' => fn ($query) => $query->latest()->limit(20)]);

        $securityHistories = $user->securityActivities->map(function ($activity) {
            $text = strtolower((string) $activity->aktivitas);
            $type = match (true) {
                str_contains($text, 'password') => 'Password',
                str_contains($text, 'login') => 'Login',
                str_contains($text, 'verifikasi') => 'Verifikasi',
                str_contains($text, 'perangkat') => 'Perangkat',
                default => 'Aktivitas',
            };

            return [
                'jenis' => $type,
                'perangkat' => $activity->perangkat ?: '-',
                'browser' => $activity->browser ?: '-',
                'lokasi' => $activity->ip_address ?: '-',
                'waktu' => optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'status' => $activity->status ?: 'berhasil',
                'metode' => $activity->catatan ?: $activity->aktivitas,
                'aktivitas' => $activity->aktivitas,
            ];
        })->values();

        return view('mentor.pengaturan.ubah-password', compact('user', 'securityHistories'));
    })->name('mentor.pengaturan.password');

    Route::post('/mentor/pengaturan/ubah-password', function (Request $request) {
        $user = $request->user()->loadMissing('mentor');

        abort_unless($user->role === 'mentor', 403);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ])->save();

        \App\Models\SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengubah password akun mentor.',
            'perangkat' => str_contains(strtolower((string) $request->userAgent()), 'mobile') ? 'Mobile' : (str_contains(strtolower((string) $request->userAgent()), 'tablet') ? 'Tablet' : 'Desktop'),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Password mentor berhasil diperbarui.',
        ]);

        return back()->with('success', 'Password mentor berhasil diperbarui.');
    })->name('mentor.pengaturan.password.update');

    Route::get('/peserta/dashboard', function () {
        $user = request()->user()->loadMissing([
            'peserta.perguruanTinggi',
            'documents',
            'activities',
            'notifications',
        ]);

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $peserta = $user->peserta;
        $perguruanTinggi = $peserta?->perguruanTinggi;
        $internship = Internship::query()
            ->with(['mentor.user', 'pembimbing.user'])
            ->where('peserta_id', $peserta->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
        $mentor = $internship?->mentor;
        $pembimbing = $internship?->pembimbing;
        $mentorUser = $mentor?->user;
        $pembimbingUser = $pembimbing?->user;

        $documents = $user->documents->sortByDesc('created_at')->values();
        $reports = $peserta
            ? $peserta->reports()->with('reviewer')->latest()->get()
            : collect();
        $activities = $user->activities->sortByDesc('created_at')->values();
        $notifications = $user->notifications->sortByDesc('created_at')->values();
        $placementInstitution = $internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta';
        $placementDivision = $internship?->divisi ?? $internship?->unit_kerja ?? $internship?->posisi ?? '-';
        $placementUnit = $placementDivision;
        $placementName = $placementDivision !== '-'
            ? $placementInstitution . ' - ' . $placementDivision
            : $placementInstitution;
        $periodText = $internship?->tanggal_mulai && $internship?->tanggal_selesai
            ? $internship->tanggal_mulai->translatedFormat('d M Y') . ' - ' . $internship->tanggal_selesai->translatedFormat('d M Y')
            : (($peserta?->tanggal_mulai_magang && $peserta?->tanggal_selesai_magang)
                ? $peserta->tanggal_mulai_magang->translatedFormat('d M Y') . ' - ' . $peserta->tanggal_selesai_magang->translatedFormat('d M Y')
                : 'Periode belum tersedia');
        $placementStatus = $internship?->status ?? $peserta?->status ?? 'belum tersedia';
        $placementStatusBadge = match ($placementStatus) {
            'berjalan', 'aktif' => 'bg-success',
            'selesai' => 'bg-secondary',
            'menunggu' => 'bg-warning text-dark',
            default => 'bg-info text-dark',
        };
        $daysLeft = $internship?->tanggal_selesai
            ? max(now()->startOfDay()->diffInDays($internship->tanggal_selesai->copy()->startOfDay(), false), 0)
            : null;
        $documentTotal = $documents->count();
        $documentApproved = $documents->whereIn('status', ['disetujui', 'approved', 'terverifikasi', 'valid', 'verified'])->count();
        $documentWaiting = $documents->whereIn('status', ['menunggu', 'pending'])->count();
        $documentRevision = $documents->whereIn('status', ['revisi', 'rejected', 'ditolak'])->count();
        $reportTotal = $reports->count();
        $reportApproved = $reports->whereIn('status', ['approved', 'disetujui'])->count();
        $reportWaiting = $reports->whereIn('status', ['pending', 'menunggu'])->count();
        $reportRevision = $reports->whereIn('status', ['revisi', 'rejected', 'ditolak'])->count();
        $documentProgress = $documentTotal > 0 ? (int) round(($documentApproved / $documentTotal) * 100) : 0;
        $reportProgress = $reportTotal > 0 ? (int) round(($reportApproved / $reportTotal) * 100) : 0;
        $internshipProgress = ($internship && $internship->tanggal_mulai && $internship->tanggal_selesai)
            ? min(100, (int) round((max($internship->tanggal_mulai->diffInDays(now()), 0) / max($internship->tanggal_mulai->diffInDays($internship->tanggal_selesai), 1)) * 100))
            : 0;

        return view('peserta.dashboard', [
            'user' => $user,
            'peserta' => $peserta,
            'perguruanTinggi' => $perguruanTinggi,
            'internship' => $internship,
            'mentorUser' => $mentorUser,
            'pembimbingUser' => $pembimbingUser,
            'documents' => $documents,
            'reports' => $reports,
            'activities' => $activities,
            'notifications' => $notifications,
            'placementInstitution' => $placementInstitution,
            'placementUnit' => $placementUnit,
            'placementName' => $placementName,
            'periodText' => $periodText,
            'placementStatus' => $placementStatus,
            'placementStatusBadge' => $placementStatusBadge,
            'daysLeft' => $daysLeft,
            'documentProgress' => $documentProgress,
            'reportProgress' => $reportProgress,
            'internshipProgress' => $internshipProgress,
            'stats' => [
                'notification_unread' => $notifications->where('dibaca', false)->count(),
                'document_total' => $documentTotal,
                'document_approved' => $documentApproved,
                'document_waiting' => $documentWaiting,
                'document_revision' => $documentRevision,
                'report_total' => $reportTotal,
                'report_approved' => $reportApproved,
                'report_waiting' => $reportWaiting,
                'report_revision' => $reportRevision,
                'internship_progress' => $internshipProgress,
                'days_left' => $daysLeft,
            ],
        ]);
    })->name('peserta.dashboard');

    Route::get('/peserta/data-magang', function () {
        $user = request()->user()->loadMissing([
            'peserta.perguruanTinggi',
            'peserta.internship.mentor.user',
            'peserta.internship.pembimbing.user',
        ]);

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $context = app(PesertaDataService::class)->forUser($user);

        $peserta = $context['peserta'] ?? $user->peserta;
        $perguruanTinggi = $context['perguruanTinggi'] ?? $peserta?->perguruanTinggi;
        $internship = $context['internship'] ?? $peserta?->internship;
        $mentor = $context['mentor'] ?? $internship?->mentor;
        $pembimbing = $context['pembimbing'] ?? $internship?->pembimbing;
        $mentorUser = $context['mentorUser'] ?? $mentor?->user;
        $pembimbingUser = $context['pembimbingUser'] ?? $pembimbing?->user;
        $placementInstitution = $internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta';
        $placementUnit = $internship?->divisi ?? $internship?->unit_kerja ?? $internship?->posisi ?? '-';
        $placementName = $placementUnit !== '-'
            ? $placementInstitution . ' - ' . $placementUnit
            : $placementInstitution;
        $placementStatus = $internship?->status ?? 'belum tersedia';

        return view('peserta.data-magang.index', [
            'user' => $context['user'] ?? $user,
            'peserta' => $peserta,
            'perguruanTinggi' => $perguruanTinggi,
            'internship' => $internship,
            'mentor' => $mentor,
            'mentorUser' => $mentorUser,
            'pembimbingUser' => $pembimbingUser,
            'placementInstitution' => $placementInstitution,
            'placementUnit' => $placementUnit,
            'placementName' => $placementName,
            'placementStatus' => $placementStatus,
            'userName' => $user->name,
            'avatar' => $user->avatar_url ?? null,
            'stats' => $context['stats'] ?? [],
        ]);
    })->name('peserta.data-magang');

    Route::get('/peserta/data-magang/profil-peserta', function () {
        $context = app(PesertaDataService::class)->forUser(request()->user());

        return view('peserta.data-magang.profil', [
            'pesertaContext' => $context,
        ]);
    })->name('peserta.data-magang.profil');

    Route::get('/peserta/data-magang/penempatan', function () {
        $user = request()->user()->loadMissing([
            'peserta.perguruanTinggi',
            'peserta.internship.mentor.user',
            'peserta.internship.pembimbing.user',
            'peserta.internships.mentor.user',
            'peserta.internships.pembimbing.user',
        ]);

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $context = app(PesertaDataService::class)->forUser($user);

        $peserta = $context['peserta'] ?? $user->peserta;
        $internship = $context['internship'] ?? $peserta?->internship;
        $mentor = $context['mentor'] ?? $internship?->mentor;
        $pembimbing = $context['pembimbing'] ?? $internship?->pembimbing;
        $mentorName = $mentor?->user?->name ?? '-';
        $mentorEmail = $mentor?->user?->email ?? '-';
        $mentorJabatan = $mentor?->jabatan ?? '-';
        $pembimbingName = $pembimbing?->user?->name ?? '-';
        $placementInstitution = $internship?->instansi ?? $peserta?->perguruanTinggi?->nama_pt ?? 'LLDIKTI Wilayah V Yogyakarta';
        $placementPosition = $internship?->posisi ?? $internship?->divisi ?? $internship?->unit_kerja ?? '-';
        $placementStatus = $internship?->status ?? 'belum tersedia';
        $placementStatusBadge = match ($placementStatus) {
            'berjalan', 'aktif' => 'bg-success',
            'selesai' => 'bg-secondary',
            'menunggu' => 'bg-warning text-dark',
            default => 'bg-info text-dark',
        };
        $startDate = $internship?->tanggal_mulai;
        $endDate = $internship?->tanggal_selesai;
        $periodText = $startDate && $endDate
            ? $startDate->translatedFormat('d M Y') . ' - ' . $endDate->translatedFormat('d M Y')
            : '-';
        $placementName = $placementPosition !== '-'
            ? $placementInstitution . ' - ' . $placementPosition
            : $placementInstitution;
        $internshipProgress = $startDate && $endDate
            ? min(100, (int) round((max($startDate->diffInDays(now()), 0) / max($startDate->diffInDays($endDate), 1)) * 100))
            : 0;
        $placementRecords = collect($context['internships'] ?? []);
        if ($placementRecords->isEmpty() && $internship) {
            $placementRecords = collect([$internship]);
        }

        $placementSummary = [
            ['label' => 'Instansi', 'value' => $placementInstitution, 'icon' => 'bi-building', 'badge' => 'bg-secondary'],
            ['label' => 'Posisi', 'value' => $placementPosition, 'icon' => 'bi-diagram-3', 'badge' => $placementStatus === 'selesai' ? 'bg-secondary' : 'bg-primary'],
            ['label' => 'Mentor', 'value' => $mentor?->user?->name ?? 'Belum ditentukan', 'icon' => 'bi-person-badge', 'badge' => 'bg-warning'],
            ['label' => 'Periode', 'value' => $periodText, 'icon' => 'bi-calendar-range', 'badge' => 'bg-info text-dark'],
            ['label' => 'Status', 'value' => ucfirst($placementStatus), 'icon' => 'bi-patch-check', 'badge' => $placementStatusBadge],
        ];

        $placementHistory = [];
        if ($internship) {
            $placementHistory[] = [
                'title' => 'Penempatan ditetapkan',
                'meta' => optional($internship->created_at)->translatedFormat('d M Y, H:i') . ' WIB - Sistem',
            ];

            if ($internship->updated_at && (! $internship->created_at || $internship->updated_at->ne($internship->created_at))) {
                $placementHistory[] = [
                    'title' => 'Penempatan diperbarui',
                    'meta' => optional($internship->updated_at)->translatedFormat('d M Y, H:i') . ' WIB - Sistem',
                ];
            }
        }

        return view('peserta.data-magang.penempatan', [
            'user' => $user,
            'userName' => $user->name,
            'avatar' => $user->avatar_url ?? null,
            'peserta' => $peserta,
            'internship' => $internship,
            'mentor' => $mentor,
            'mentorUser' => $mentor?->user,
            'mentorName' => $mentorName,
            'mentorEmail' => $mentorEmail,
            'mentorJabatan' => $mentorJabatan,
            'pembimbingUser' => $pembimbing?->user,
            'pembimbingName' => $pembimbingName,
            'placementInstitution' => $placementInstitution,
            'placementPosition' => $placementPosition,
            'placementStatus' => $placementStatus,
            'placementStatusBadge' => $placementStatusBadge,
            'periodText' => $periodText,
            'placementName' => $placementName,
            'internshipProgress' => $internshipProgress,
            'placementSummary' => $placementSummary,
            'placementHistory' => $placementHistory,
            'placementRecords' => $placementRecords,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    })->name('peserta.data-magang.penempatan');

    Route::get('/peserta/data-magang/penempatan/download', function (Request $request) {
        $user = $request->user()->loadMissing([
            'peserta.perguruanTinggi',
            'peserta.internship.mentor.user',
            'peserta.internship.pembimbing.user',
        ]);

        $peserta = $user->peserta;
        $internship = $peserta?->internship;
        $placementInstitution = $internship?->instansi ?? $peserta?->perguruanTinggi?->nama_pt ?? 'LLDIKTI Wilayah V Yogyakarta';
        $placementPosition = $internship?->posisi ?? $internship?->divisi ?? $internship?->unit_kerja ?? '-';
        $mentorName = $internship?->mentor?->user?->name ?? '-';
        $pembimbingName = $internship?->pembimbing?->user?->name ?? '-';

        abort_unless($user && $user->role === 'peserta', 403);
        abort_if(! $internship, 404, 'Data penempatan belum tersedia.');

        $placementName = $placementPosition !== '-'
            ? $placementInstitution . ' - ' . $placementPosition
            : $placementInstitution;
        $periodText = $internship?->tanggal_mulai && $internship?->tanggal_selesai
            ? $internship->tanggal_mulai->translatedFormat('d M Y') . ' - ' . $internship->tanggal_selesai->translatedFormat('d M Y')
            : '-';
        $filename = 'penempatan-'.Str::slug($user->name ?? 'peserta').'.txt';

        $content = [
            'Ringkasan Penempatan Peserta',
            'Nama: '.($user->name ?? '-'),
            'Email: '.($user->email ?? '-'),
            'Instansi: '.$placementInstitution,
            'Posisi: '.$placementPosition,
            'Mentor: '.$mentorName,
            'Status: '.ucfirst((string) ($internship?->status ?? 'belum tersedia')),
            'Pembimbing Akademik: '.$pembimbingName,
            'Periode: '.$periodText,
        ];

        return response()->streamDownload(function () use ($content) {
            echo implode(PHP_EOL.PHP_EOL, $content);
        }, $filename, ['Content-Type' => 'text/plain; charset=UTF-8']);
    })->name('peserta.data-magang.penempatan.download');

    Route::get('/peserta/aktivitas-magang', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());

        return view('peserta.aktivitas-magang.index', compact('pesertaContext'));
    })->name('peserta.aktivitas-magang');

    Route::get('/peserta/aktivitas-magang/absensi', [AttendanceController::class, 'index'])
        ->name('peserta.aktivitas-magang.absensi');
    Route::post('/peserta/aktivitas-magang/absensi/masuk', [AttendanceController::class, 'checkIn'])
        ->name('peserta.aktivitas-magang.absensi.masuk');
    Route::post('/peserta/aktivitas-magang/absensi/pulang', [AttendanceController::class, 'checkOut'])
        ->name('peserta.aktivitas-magang.absensi.pulang');
    Route::post('/peserta/aktivitas-magang/absensi/izin', [AttendanceController::class, 'permit'])
        ->name('peserta.aktivitas-magang.absensi.izin');
    Route::post('/peserta/aktivitas-magang/absensi/sakit', [AttendanceController::class, 'sick'])
        ->name('peserta.aktivitas-magang.absensi.sakit');

    Route::get('/peserta/aktivitas-magang/penugasan', function () {
        $user = request()->user()->loadMissing([
            'peserta.perguruanTinggi',
            'peserta.internship.mentor.user',
            'peserta.internship.pembimbing.user',
        ]);

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $pesertaContext = app(PesertaDataService::class)->forUser($user);
        $assignments = $user->peserta
            ? $user->peserta->assignments()->with('mentor.user')->latest('deadline')->latest()->get()
            : collect();

        $assignmentRows = $assignments->values()->map(function ($assignment, $index) {
            $priorityKey = strtolower((string) ($assignment->prioritas ?? 'sedang'));
            $statusKey = strtolower((string) ($assignment->status ?? ''));

            $priorityMeta = match ($priorityKey) {
                'tinggi', 'high' => ['label' => 'Tinggi', 'class' => 'danger'],
                'rendah', 'low' => ['label' => 'Rendah', 'class' => 'info text-dark'],
                default => ['label' => 'Sedang', 'class' => 'warning text-dark'],
            };

            $statusMeta = match ($statusKey) {
                'selesai', 'done', 'completed', 'disetujui' => ['label' => 'Selesai', 'class' => 'success'],
                'terlambat', 'late' => ['label' => 'Terlambat', 'class' => 'danger'],
                'review', 'finalisasi' => ['label' => 'Review', 'class' => 'info text-dark'],
                default => ['label' => $statusKey !== '' ? ucfirst($statusKey) : 'Dikerjakan', 'class' => 'warning text-dark'],
            };

            $filePath = $assignment->file_hasil ?? null;
            $submissionPath = $assignment->file_pengumpulan ?? null;

            return [
                'id' => $assignment->id,
                'no' => $index + 1,
                'judul' => $assignment->judul ?: 'Penugasan',
                'deskripsi' => $assignment->deskripsi ?: '-',
                'prioritas_label' => $priorityMeta['label'],
                'prioritas_class' => $priorityMeta['class'],
                'pemberi' => $assignment->mentor?->user?->name ?? '-',
                'deadline' => optional($assignment->deadline)->translatedFormat('d M Y') ?? '-',
                'deadline_raw' => optional($assignment->deadline)->format('Y-m-d'),
                'status_label' => $statusMeta['label'],
                'status_class' => $statusMeta['class'],
                'progress' => (int) ($assignment->progress ?? 0),
                'catatan' => $assignment->catatan ?: '-',
                'created_at_label' => optional($assignment->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'updated_at_label' => optional($assignment->updated_at)->translatedFormat('d M Y H:i') ?? '-',
                'file_name' => $filePath ? basename((string) $filePath) : null,
                'file_url' => $filePath ? Storage::disk('public')->url($filePath) : null,
                'submission_name' => $submissionPath ? basename((string) $submissionPath) : null,
                'submission_url' => $submissionPath ? Storage::disk('public')->url($submissionPath) : null,
                'submission_uploaded_at' => optional($assignment->submitted_at ?? $assignment->updated_at)->translatedFormat('d M Y H:i') ?? '-',
                'download_url' => $filePath ? route('peserta.aktivitas-magang.penugasan.download', $assignment) : null,
            ];
        });

        return view('peserta.aktivitas-magang.penugasan', array_merge(
            $pesertaContext,
            [
                'pesertaContext' => $pesertaContext,
                'assignments' => $assignments,
                'assignmentRows' => $assignmentRows,
            ]
        ));
    })->name('peserta.aktivitas-magang.penugasan');

    Route::get('/peserta/aktivitas-magang/penugasan/{assignment}/download', function (Request $request, Assignment $assignment) {
        $user = $request->user()->loadMissing('peserta');

        abort_unless($user->role === 'peserta' && $user->peserta, 403);
        abort_unless((int) $assignment->peserta_id === (int) $user->peserta->id, 403);
        abort_unless(filled($assignment->file_hasil) && Storage::disk('public')->exists($assignment->file_hasil), 404);

        return Storage::disk('public')->download($assignment->file_hasil, basename((string) $assignment->file_hasil));
    })->name('peserta.aktivitas-magang.penugasan.download');

    Route::post('/peserta/aktivitas-magang/penugasan/{assignment}/unggah', function (Request $request, Assignment $assignment) {
        $user = $request->user()->loadMissing('peserta.internship.mentor.user');

        abort_unless($user->role === 'peserta' && $user->peserta, 403);
        abort_unless((int) $assignment->peserta_id === (int) $user->peserta->id, 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        if ($assignment->file_pengumpulan && Storage::disk('public')->exists($assignment->file_pengumpulan)) {
            Storage::disk('public')->delete($assignment->file_pengumpulan);
        }

        $assignment->file_pengumpulan = $request->file('file')->store('assignments/submissions', 'public');
        $assignment->submitted_at = now();
        $assignment->status = 'selesai';
        $assignment->progress = 100;

        $assignment->save();

        if ($assignment->mentor?->user_id) {
            Notification::create([
                'user_id' => $assignment->mentor->user_id,
                'judul' => 'Hasil penugasan peserta dikirim',
                'pesan' => $user->name.' sudah mengunggah hasil penugasan "'.$assignment->judul.'".',
                'dibaca' => false,
            ]);
        }

        Notification::create([
            'user_id' => $user->id,
            'judul' => 'Penugasan berhasil dikirim',
            'pesan' => 'File hasil penugasan "'.$assignment->judul.'" sudah tersimpan di database.',
            'dibaca' => false,
        ]);

        return back()->with('success', 'Hasil penugasan berhasil diunggah dan tersimpan di database.');
    })->name('peserta.aktivitas-magang.penugasan.upload');

    Route::get('/peserta/aktivitas-magang/riwayat-harian', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());

        return view('peserta.aktivitas-magang.riwayat', array_merge(['pesertaContext' => $pesertaContext], $pesertaContext));
    })->name('peserta.aktivitas-magang.riwayat');

    Route::get('/peserta/dokumen', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());
        $documents = $pesertaContext['user']
            ? $pesertaContext['user']->documents()->latest()->get()
            : collect();

        return view('peserta.dokumen.index', array_merge($pesertaContext, [
            'documents' => $documents,
            'activeDocument' => 'Semua Dokumen',
        ]));
    })->name('peserta.dokumen');

    Route::get('/peserta/dokumen/kerjasama', function (Request $request) {
        $user = $request->user()->loadMissing('peserta');
        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $pesertaContext = app(PesertaDataService::class)->forUser($user);
        $kerjasamaDocuments = $user->documents()
            ->where('kategori', 'Dokumen Kerja Sama')
            ->latest()
            ->get();

        return view('peserta.dokumen.kerjasama', array_merge($pesertaContext, [
            'user' => $user,
            'kerjasamaDocuments' => $kerjasamaDocuments,
            'activeDocument' => 'Dokumen Kerjasama',
        ]));
    })->name('peserta.dokumen.kerjasama');

    Route::post('/peserta/dokumen/kerjasama', function (Request $request) {
        $user = $request->user()->loadMissing('peserta');

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $validated = $request->validate([
            'jenis_dokumen' => ['required', 'in:mou,pks,addendum,surat_kerja_sama'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $labelMap = [
            'mou' => 'MoU',
            'pks' => 'PKS',
            'addendum' => 'Addendum',
            'surat_kerja_sama' => 'Surat Kerja Sama',
        ];

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf');
        $directory = 'dokumen-kerjasama/'.$user->id;
        $fileName = $validated['jenis_dokumen'].'-'.$user->id.'.'.$extension;
        $path = $file->storeAs($directory, $fileName, 'public');

        Document::updateOrCreate(
            [
                'user_id' => $user->id,
                'kategori' => 'Dokumen Kerja Sama',
                'jenis_dokumen' => $validated['jenis_dokumen'],
            ],
            [
                'nama_dokumen' => $labelMap[$validated['jenis_dokumen']].' '.($user->peserta?->nim ?? $user->id).'.'.$extension,
                'jenis_file' => strtoupper($extension),
                'file' => $path,
                'ukuran_file' => $file->getSize(),
                'status' => 'menunggu',
                'catatan' => 'Menunggu validasi admin.',
            ]
        );

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengunggah dokumen kerja sama peserta melalui menu dokumen kerja sama.',
        ]);

        return back()->with('success', 'Dokumen kerja sama berhasil diunggah dan menunggu validasi admin.');
    })->name('peserta.dokumen.kerjasama.store');

    Route::get('/peserta/dokumen/pendukung', function (Request $request) {
        $user = $request->user()->loadMissing('peserta');
        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $pesertaContext = app(PesertaDataService::class)->forUser($user);
        $documents = $user->documents()
            ->where('kategori', 'Dokumen Pendukung')
            ->latest()
            ->get();

        return view('peserta.dokumen.pendukung', array_merge($pesertaContext, [
            'user' => $user,
            'documents' => $documents,
            'activeDocument' => 'Dokumen Pendukung',
        ]));
    })->name('peserta.dokumen.pendukung');

    Route::post('/peserta/dokumen/pendukung', function (Request $request) {
        $user = $request->user()->loadMissing('peserta');

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $documentFields = [
            'proposal' => 'Proposal',
            'ktm' => 'KTM',
            'transkip' => 'Transkip',
            'cv' => 'CV',
            'surat_pengantar' => 'Surat Pengantar',
            'sertifikat_pendukung' => 'Sertifikat Pendukung',
        ];

        $validated = $request->validate([
            'proposal' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'ktm' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'transkip' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'surat_pengantar' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'sertifikat_pendukung' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ]);

        $uploaded = 0;

        DB::transaction(function () use ($request, $user, $documentFields, &$uploaded) {
            foreach ($documentFields as $field => $label) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                $file = $request->file($field);
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf');
                $directory = 'dokumen-peserta/'.$user->id;
                $fileName = $field.'-'.$user->id.'.'.$extension;
                $path = $file->storeAs($directory, $fileName, 'public');

                Document::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'jenis_dokumen' => $field,
                    ],
                    [
                        'nama_dokumen' => $label.' '.($user->peserta?->nim ?? $user->id).'.'.$extension,
                        'kategori' => 'Dokumen Pendukung',
                        'jenis_file' => strtoupper($extension),
                        'file' => $path,
                        'ukuran_file' => $file->getSize(),
                        'status' => 'disetujui',
                        'catatan' => null,
                    ]
                );

                $uploaded++;
            }

            if ($uploaded > 0) {
                Activity::create([
                    'user_id' => $user->id,
                    'aktivitas' => 'Mengunggah dokumen pendukung peserta melalui menu dokumen.',
                ]);
            }
        });

        if ($uploaded === 0) {
            return back()->withErrors(['upload' => 'Pilih minimal satu file untuk diunggah.']);
        }

        return back()->with('success', $uploaded.' dokumen berhasil diunggah dan tersimpan di database.');
    })->name('peserta.dokumen.pendukung.store');

    Route::delete('/peserta/dokumen/pendukung/{document}', function (Request $request, Document $document) {
        $user = $request->user()->loadMissing('peserta');

        abort_unless($user->role === 'peserta' && $user->peserta, 403);
        abort_unless((int) $document->user_id === (int) $user->id, 403);
        abort_unless(strtolower((string) $document->kategori) === 'dokumen pendukung', 404);

        if ($document->file && Storage::disk('public')->exists($document->file)) {
            Storage::disk('public')->delete($document->file);
        }

        $document->delete();

        Activity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Menghapus dokumen pendukung peserta melalui menu dokumen.',
        ]);

        return back()->with('success', 'Dokumen pendukung berhasil dihapus.');
    })->name('peserta.dokumen.pendukung.destroy');

    Route::get('/peserta/dokumen/status', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());
        $kerjasamaDocuments = $pesertaContext['user']
            ? $pesertaContext['user']->documents()
                ->where('kategori', 'Dokumen Kerja Sama')
                ->latest()
                ->get()
            : collect();

        return view('peserta.dokumen.status', array_merge($pesertaContext, [
            'kerjasamaDocuments' => $kerjasamaDocuments,
            'activeDocument' => 'Status Dokumen',
        ]));
    })->name('peserta.dokumen.status');

    Route::get('/peserta/laporan', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());
        $reportItems = collect($pesertaContext['reports'] ?? collect());
        $request = request();

        $normalize = static fn ($value) => strtolower(trim((string) $value));
        $jenisFilter = $normalize($request->query('jenis', 'semua'));
        $statusFilter = $normalize($request->query('status', 'semua'));
        $periodeFilter = $normalize($request->query('periode', 'semua'));
        $searchQuery = $normalize($request->query('q', ''));

        $reportItems = $reportItems->filter(function (Report $report) use ($normalize, $jenisFilter, $statusFilter, $periodeFilter, $searchQuery) {
            $statusKey = $normalize($report->status ?? 'draft');
            $jenisKey = $normalize($report->jenis ?? 'berkala');
            $periodeKey = $normalize($report->periode ?? optional($report->created_at)->translatedFormat('F Y') ?? '');
            $searchTarget = $normalize(implode(' ', array_filter([
                $report->judul ?? '',
                $report->periode ?? '',
                $report->catatan ?? '',
                $report->catatan_mentor ?? '',
                $report->catatan_pembimbing ?? '',
                $report->reviewer?->name ?? '',
            ])));

            $jenisMatch = $jenisFilter === 'semua'
                || ($jenisFilter === 'akhir' && str_contains($jenisKey, 'akhir'))
                || ($jenisFilter === 'berkala' && ! str_contains($jenisKey, 'akhir') && ! str_contains($jenisKey, 'final'));

            $statusMatch = match ($statusFilter) {
                'semua' => true,
                'disetujui' => in_array($statusKey, ['approved', 'disetujui'], true),
                'menunggu' => in_array($statusKey, ['pending', 'menunggu'], true),
                'revisi' => in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true),
                'draft' => blank($report->status) || $statusKey === 'draft',
                default => true,
            };

            $periodeMatch = $periodeFilter === 'semua' || $periodeFilter === $periodeKey;
            $searchMatch = blank($searchQuery) || str_contains($searchTarget, $searchQuery);

            return $jenisMatch && $statusMatch && $periodeMatch && $searchMatch;
        })->values();

        $reportPeriodOptions = collect($pesertaContext['reports'] ?? collect())
            ->map(function (Report $report) {
                return trim((string) ($report->periode ?: optional($report->created_at)->translatedFormat('F Y') ?: ''));
            })
            ->filter()
            ->unique()
            ->values();

        return view('peserta.laporan.index', $pesertaContext + [
            'reports' => $reportItems,
            'reportPeriodOptions' => $reportPeriodOptions,
            'activeReport' => 'Semua Laporan',
        ]);
    })->name('peserta.laporan');

    Route::get('/peserta/laporan/ekspor', function (Request $request) {
        $pesertaContext = app(PesertaDataService::class)->forUser($request->user());
        $reportItems = collect($pesertaContext['reports'] ?? collect());

        $normalize = static fn ($value) => strtolower(trim((string) $value));
        $jenisFilter = $normalize($request->query('jenis', 'semua'));
        $statusFilter = $normalize($request->query('status', 'semua'));
        $periodeFilter = $normalize($request->query('periode', 'semua'));
        $searchQuery = $normalize($request->query('q', ''));

        $reportItems = $reportItems->filter(function (Report $report) use ($normalize, $jenisFilter, $statusFilter, $periodeFilter, $searchQuery) {
            $statusKey = $normalize($report->status ?? 'draft');
            $jenisKey = $normalize($report->jenis ?? 'berkala');
            $periodeKey = $normalize($report->periode ?? optional($report->created_at)->translatedFormat('F Y') ?? '');
            $searchTarget = $normalize(implode(' ', array_filter([
                $report->judul ?? '',
                $report->periode ?? '',
                $report->catatan ?? '',
                $report->catatan_mentor ?? '',
                $report->catatan_pembimbing ?? '',
                $report->reviewer?->name ?? '',
            ])));

            $jenisMatch = $jenisFilter === 'semua'
                || ($jenisFilter === 'akhir' && str_contains($jenisKey, 'akhir'))
                || ($jenisFilter === 'berkala' && ! str_contains($jenisKey, 'akhir') && ! str_contains($jenisKey, 'final'));

            $statusMatch = match ($statusFilter) {
                'semua' => true,
                'disetujui' => in_array($statusKey, ['approved', 'disetujui'], true),
                'menunggu' => in_array($statusKey, ['pending', 'menunggu'], true),
                'revisi' => in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true),
                'draft' => blank($report->status) || $statusKey === 'draft',
                default => true,
            };

            $periodeMatch = $periodeFilter === 'semua' || $periodeFilter === $periodeKey;
            $searchMatch = blank($searchQuery) || str_contains($searchTarget, $searchQuery);

            return $jenisMatch && $statusMatch && $periodeMatch && $searchMatch;
        })->values();

        $filename = 'rekap-laporan-peserta-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($reportItems) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['No', 'Judul', 'Jenis', 'Periode', 'Durasi', 'Tanggal Dibuat', 'Status', 'Status Mentor', 'Status Pembimbing', 'Status Admin', 'Penilai', 'Catatan Mentor', 'Catatan Pembimbing', 'File']);

            foreach ($reportItems as $index => $report) {
                $statusKey = strtolower((string) ($report->status ?? 'draft'));
                $jenisLabel = strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir' ? 'Laporan Akhir' : 'Laporan Berkala';
                $mentorStatus = filled($report->catatan_mentor)
                    ? (in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true) ? 'Perlu Revisi' : 'Disetujui')
                    : 'Menunggu';
                $pembimbingStatusKey = strtolower((string) ($report->pembimbing_review_status ?? 'menunggu review'));
                $pembimbingStatus = match ($pembimbingStatusKey) {
                    'disetujui' => 'Disetujui',
                    'perlu revisi', 'revisi', 'rejected', 'ditolak' => 'Perlu Revisi',
                    default => 'Menunggu',
                };
                $adminStatus = strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir'
                    ? (filled($report->admin_approved_at) ? 'Disetujui Admin' : 'Menunggu Admin')
                    : '-';
                $penilai = $report->reviewer?->name ?? $report->peserta?->internship?->mentor?->user?->name ?? $report->peserta?->internship?->pembimbing?->user?->name ?? '-';

                fputcsv($handle, [
                    $index + 1,
                    $report->judul ?: 'Laporan Magang',
                    $jenisLabel,
                    $report->periode ?: '-',
                    $report->durasi_jam ? $report->durasi_jam.' jam' : '-',
                    optional($report->created_at)->translatedFormat('d M Y') ?? '-',
                    $statusKey,
                    $mentorStatus,
                    $pembimbingStatus,
                    $adminStatus,
                    $penilai,
                    $report->catatan_mentor ?: '-',
                    $report->catatan_pembimbing ?: '-',
                    $report->file ?: '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('peserta.laporan.export');

    Route::post('/peserta/laporan/input', function (Request $request) {
        $user = $request->user()->loadMissing('peserta.internship.mentor.user', 'peserta.internship.pembimbing.user');
        $peserta = $user->peserta;

        abort_unless($peserta, 403);

        $validated = $request->validate([
            'report_id' => ['nullable', 'integer', 'exists:reports,id'],
            'judul' => ['required_without:report_id', 'nullable', 'string', 'max:255'],
            'jenis' => ['required_without:report_id', 'nullable', 'in:berkala,akhir'],
            'periode' => ['required_without:report_id', 'nullable', 'string', 'max:255'],
            'durasi_jam' => ['nullable', 'integer', 'min:1', 'max:24'],
            'catatan' => ['required_without:report_id', 'nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240', 'required_without:report_id'],
            'submission_action' => ['nullable', 'in:draft,submit'],
        ]);

        $report = DB::transaction(function () use ($request, $peserta, $validated, $user) {
            $existingReport = ! empty($validated['report_id'])
                ? $peserta->reports()->whereKey($validated['report_id'])->first()
                : null;

            if (! empty($validated['report_id']) && ! $existingReport) {
                abort(404);
            }

            $file = request()->file('file');
            $path = $existingReport?->file;

            if ($file) {
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'pdf');
                $directory = 'laporan-peserta/'.$user->id;
                $baseTitle = $validated['judul'] ?? $existingReport?->judul ?? 'laporan-revisi';
                $fileName = Str::slug($baseTitle).'-'.$user->id.'.'.$extension;
                $path = $file->storeAs($directory, $fileName, 'public');
            }

            if (! $path) {
                abort(422, 'File laporan harus diunggah.');
            }

            $jenisInput = $request->input('jenis', $existingReport?->jenis ?? 'berkala');
            $jenisInput = strtolower(trim((string) $jenisInput));
            $jenisInput = str_contains($jenisInput, 'akhir') || str_contains($jenisInput, 'final')
                ? 'akhir'
                : 'berkala';

            $report = $existingReport ?? new Report(['peserta_id' => $peserta->id]);
            $reportData = [
                'judul' => $validated['judul'] ?? $existingReport?->judul ?? 'Laporan Magang',
                'file' => $path,
                'status' => 'pending',
            ];

            if (Schema::hasColumn('reports', 'jenis')) {
                $reportData['jenis'] = $jenisInput;
            }

            if (Schema::hasColumn('reports', 'periode')) {
                $reportData['periode'] = $validated['periode'] ?? $existingReport?->periode ?? null;
            }

            if (Schema::hasColumn('reports', 'durasi_jam')) {
                $reportData['durasi_jam'] = $validated['durasi_jam'] ?? $existingReport?->durasi_jam ?? null;
            }

            if (Schema::hasColumn('reports', 'catatan')) {
                $reportData['catatan'] = $validated['catatan'] ?? $existingReport?->catatan ?? null;
            }

            if (Schema::hasColumn('reports', 'reviewer_id')) {
                $reportData['reviewer_id'] = $peserta->internship?->mentor?->user_id ?? $peserta->internship?->pembimbing?->user_id;
            }

            $submissionAction = $validated['submission_action'] ?? 'submit';
            $reportData['status'] = $submissionAction === 'draft' ? 'draft' : 'pending';

            $report->fill($reportData);
            $report->peserta_id = $peserta->id;
            $report->save();

            Activity::create([
                'user_id' => $user->id,
                'aktivitas' => $existingReport
                    ? ($submissionAction === 'draft'
                        ? 'Menyimpan draft laporan: '.$report->judul.'.'
                        : 'Memperbarui laporan: '.$report->judul.'.')
                    : ($submissionAction === 'draft'
                        ? 'Menyimpan draft laporan baru: '.$report->judul.'.'
                        : 'Mengunggah laporan baru: '.$report->judul.'.'),
            ]);

            if ($submissionAction !== 'draft') {
                foreach ([
                    $peserta->internship?->mentor?->user,
                    $peserta->internship?->pembimbing?->user,
                ] as $reviewUser) {
                    if (! $reviewUser) {
                        continue;
                    }

                    Notification::create([
                        'user_id' => $reviewUser->id,
                        'judul' => 'Laporan peserta perlu review',
                        'pesan' => $report->judul.' dari '.$user->name.' baru saja dikirim.',
                        'dibaca' => false,
                    ]);
                }
            }

            return $report;
        });

        $reportKindLabel = strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir'
            ? 'laporan akhir'
            : 'laporan berkala';

        return redirect()
            ->route('peserta.laporan.riwayat')
            ->with('success', 'Laporan berhasil disimpan sebagai '.$reportKindLabel.' dan ditampilkan di riwayat laporan.');
    })->name('peserta.laporan.input.store');

    Route::get('/reports/{report}/unduh', function (Request $request, Report $report) {
        $user = $request->user()->loadMissing(
            'peserta.internship.mentor.user',
            'peserta.internship.pembimbing.user',
            'mentor',
            'pembimbing'
        );

        $canAccess = in_array($user->role, ['admin', 'super_admin'], true);

        if ($user->role === 'peserta') {
            $canAccess = $report->peserta?->user_id === $user->id;
        } elseif ($user->role === 'mentor') {
            $canAccess = $report->peserta?->internship?->mentor?->user_id === $user->id;
        } elseif ($user->role === 'pembimbing') {
            $canAccess = $report->peserta?->internship?->pembimbing?->user_id === $user->id;
        }

        abort_unless($canAccess, 403);

        return Pdf::loadView('pdf.laporan', [
            'report' => $report->loadMissing('peserta.user', 'peserta.internship.mentor.user', 'peserta.internship.pembimbing.user'),
            'downloadedBy' => $user->name,
            'roleLabel' => match ($user->role) {
                'mentor' => 'Mentor',
                'pembimbing' => 'Pembimbing Akademik',
                'peserta' => 'Peserta',
                default => 'Admin',
            },
        ])
            ->setPaper('a4', 'portrait')
            ->download(Str::slug($report->judul ?: 'laporan').'.pdf');
    })->name('reports.download');

    Route::get('/peserta/laporan/input', function (Request $request) {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());
        $reportToEdit = null;
        $selectedJenis = $request->string('jenis')->toString() ?: 'berkala';

        if ($request->filled('report_id') && auth()->user()?->peserta) {
            $reportToEdit = auth()->user()->peserta->reports()->whereKey($request->integer('report_id'))->first();
            $selectedJenis = $request->filled('jenis')
                ? $request->string('jenis')->toString()
                : ($reportToEdit?->jenis ?? $selectedJenis);
        }

        return view('peserta.laporan.input', $pesertaContext + [
            'reportToEdit' => $reportToEdit,
            'selectedJenis' => $selectedJenis,
        ]);
    })->name('peserta.laporan.input');

    Route::get('/peserta/laporan/riwayat', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());
        $reportItems = collect($pesertaContext['reports'] ?? collect());
        $request = request();

        $normalize = static fn ($value) => strtolower(trim((string) $value));
        $jenisFilter = $normalize($request->query('jenis', 'semua'));
        $statusFilter = $normalize($request->query('status', 'semua'));
        $periodeFilter = $normalize($request->query('periode', 'semua'));
        $searchQuery = $normalize($request->query('q', ''));

        $reportItems = $reportItems->filter(function (Report $report) use ($normalize, $jenisFilter, $statusFilter, $periodeFilter, $searchQuery) {
            $statusKey = $normalize($report->status ?? 'draft');
            $jenisKey = $normalize($report->jenis ?? 'berkala');
            $periodeKey = $normalize($report->periode ?? optional($report->created_at)->translatedFormat('F Y') ?? '');
            $searchTarget = $normalize(implode(' ', array_filter([
                $report->judul ?? '',
                $report->periode ?? '',
                $report->catatan ?? '',
                $report->catatan_mentor ?? '',
                $report->catatan_pembimbing ?? '',
                $report->reviewer?->name ?? '',
            ])));

            $jenisMatch = $jenisFilter === 'semua'
                || ($jenisFilter === 'akhir' && str_contains($jenisKey, 'akhir'))
                || ($jenisFilter === 'berkala' && ! str_contains($jenisKey, 'akhir') && ! str_contains($jenisKey, 'final'));

            $statusMatch = match ($statusFilter) {
                'semua' => true,
                'disetujui' => in_array($statusKey, ['approved', 'disetujui'], true),
                'menunggu' => in_array($statusKey, ['pending', 'menunggu'], true),
                'revisi' => in_array($statusKey, ['revisi', 'rejected', 'ditolak'], true),
                'draft' => blank($report->status) || $statusKey === 'draft',
                default => true,
            };

            $periodeMatch = $periodeFilter === 'semua' || $periodeFilter === $periodeKey;
            $searchMatch = blank($searchQuery) || str_contains($searchTarget, $searchQuery);

            return $jenisMatch && $statusMatch && $periodeMatch && $searchMatch;
        })->values();

        $reportPeriodOptions = collect($pesertaContext['reports'] ?? collect())
            ->map(function (Report $report) {
                return trim((string) ($report->periode ?: optional($report->created_at)->translatedFormat('F Y') ?: ''));
            })
            ->filter()
            ->unique()
            ->values();

        return view('peserta.laporan.riwayat', $pesertaContext + [
            'reports' => $reportItems,
            'reportPeriodOptions' => $reportPeriodOptions,
            'activeReport' => 'Riwayat Laporan',
        ]);
    })->name('peserta.laporan.riwayat');

    Route::get('/peserta/penilaian', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());
        $assessmentRows = collect($pesertaContext['assessments'] ?? [])
            ->sortByDesc('created_at')
            ->values()
            ->map(function (Assessment $assessment) use ($pesertaContext) {
                $jenis = strtolower((string) ($assessment->jenis ?? ''));
                $status = strtolower((string) ($assessment->status ?? 'draft'));

                return [
                    'id' => $assessment->id,
                    'jenis' => $jenis,
                    'jenis_label' => match ($jenis) {
                        'pembimbing' => 'Pembimbing Akademik',
                        'mentor' => 'Mentor',
                        default => filled($jenis) ? ucfirst($jenis) : 'Penilaian',
                    },
                    'penilai' => $assessment->mentor?->user?->name
                        ?? $assessment->pembimbing?->user?->name
                        ?? '-',
                    'periode' => $assessment->periode ?: ($pesertaContext['peserta']?->program_magang ?? '-'),
                    'komponen' => $assessment->komponen ?: '-',
                    'bobot' => (int) $assessment->bobot,
                    'nilai' => number_format((float) $assessment->nilai, 2, ',', '.'),
                    'nilai_akhir' => number_format((float) $assessment->nilai_akhir, 2, ',', '.'),
                    'status' => $status,
                    'status_label' => match ($status) {
                        'final', 'selesai', 'disetujui' => 'Dinilai',
                        'revisi' => 'Revisi',
                        'draft' => 'Draft',
                        default => filled($status) ? ucfirst($status) : 'Draft',
                    },
                    'status_class' => match ($status) {
                        'final', 'selesai', 'disetujui' => 'success',
                        'revisi' => 'warning text-dark',
                        'draft' => 'secondary',
                        default => 'secondary',
                    },
                    'catatan' => $assessment->catatan ?: '-',
                    'tanggal' => optional($assessment->created_at)->translatedFormat('d M Y') ?? '-',
                ];
            });

        return view('peserta.penilaian.index', $pesertaContext + [
            'assessmentRows' => $assessmentRows,
            'activeAssessment' => 'Ringkasan Penilaian',
        ]);
    })->name('peserta.penilaian');

    Route::get('/peserta/penilaian/rekap-nilai', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());

        return view('peserta.penilaian.rekap', $pesertaContext);
    })->name('peserta.penilaian.rekap');

    Route::get('/peserta/penilaian/sertifikat', function () {
        $pesertaContext = app(PesertaDataService::class)->forUser(auth()->user());

        return view('peserta.penilaian.sertifikat', $pesertaContext);
    })->name('peserta.penilaian.sertifikat');

    Route::get('/peserta/penilaian/sertifikat/pdf', function (Request $request) {
        $user = $request->user()->loadMissing('peserta.internship.mentor.user', 'peserta.internship.pembimbing.user');
        abort_unless($user->peserta, 403);

        $certificate = $user->peserta->certificates()
            ->latest('tanggal_terbit')
            ->latest()
            ->first();

        if ($certificate && $certificate->file && Storage::disk('public')->exists($certificate->file)) {
            return Storage::disk('public')->download($certificate->file, basename($certificate->file));
        }

        abort_unless($certificate, 404, 'Sertifikat belum diterbitkan.');

        $latestAssessment = $user->peserta->assessments()
            ->latest()
            ->first();

        $nilaiAkhir = (float) ($latestAssessment?->nilai_akhir ?? 0);
        $predikat = $certificate->predikat ?: match (true) {
            $nilaiAkhir >= 90 => 'A+',
            $nilaiAkhir >= 85 => 'A',
            $nilaiAkhir >= 80 => 'AB',
            $nilaiAkhir >= 75 => 'B+',
            $nilaiAkhir >= 70 => 'B',
            $nilaiAkhir >= 65 => 'BC',
            $nilaiAkhir >= 60 => 'C',
            default => 'D',
        };

        return Pdf::loadView('pdf.sertifikat', [
            'user' => $user,
            'peserta' => $user->peserta,
            'mentorName' => $user->peserta?->internship?->mentor?->user?->name ?? '-',
            'pembimbingName' => $user->peserta?->internship?->pembimbing?->user?->name ?? '-',
            'periode' => $user->peserta?->program_magang ?? 'Magang',
            'predikat' => $predikat,
            'nomor' => $certificate->nomor,
            'tanggalTerbit' => optional($certificate->tanggal_terbit)->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y'),
        ])
            ->setPaper('a4', 'landscape')
            ->download('sertifikat-'.Str::slug($user->name ?? 'peserta').'.pdf');
    })->name('peserta.penilaian.sertifikat.pdf');

    Route::get('/peserta/komunikasi', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'peserta', 403);
        $pesertaContext = app(PesertaDataService::class)->forUser($user);

        $selectedConversation = null;
        $conversationId = (int) $request->query('conversation');

        if ($conversationId) {
            $selectedConversation = Conversation::query()
                ->with(['peserta.user', 'mentor.user', 'pembimbing.user', 'admin', 'messages.sender'])
                ->whereKey($conversationId)
                ->first();

            if ($selectedConversation && $selectedConversation->peserta?->user_id !== $user->id) {
                $selectedConversation = null;
            }

            if ($selectedConversation) {
                app(CommunicationService::class)->markRead($user, $selectedConversation);
            }
        }

        return view('peserta.komunikasi.index', [
            'activeCommunication' => 'Semua Komunikasi',
            'pesertaContext' => $pesertaContext,
            'communicationData' => app(CommunicationService::class)->forUser($user, $selectedConversation),
        ]);
    })->name('peserta.komunikasi');

    Route::get('/peserta/komunikasi/pesan', [CommunicationController::class, 'peserta'])
        ->name('peserta.komunikasi.pesan');

    Route::get('/peserta/komunikasi/pengumuman', function () {
        $role = 'peserta';
        $roleLabel = 'Peserta';

        $announcementRows = Announcement::query()
            ->with('author')
            ->whereRaw('LOWER(kategori) = ?', [$role])
            ->whereRaw("LOWER(COALESCE(status, '')) IN ('published', 'dipublikasikan', 'active', 'aktif')")
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $audienceCount = User::query()->where('role', $role)->count();

        $announcementData = $announcementRows->map(function (Announcement $announcement) use ($roleLabel, $audienceCount) {
            $status = strtolower((string) ($announcement->status ?? 'draft'));
            $date = $announcement->tanggal ?? $announcement->created_at;
            $dateValue = $date ? Carbon::parse($date) : now();
            $timeGroup = $dateValue->isToday()
                ? 'Hari Ini'
                : ($dateValue->greaterThanOrEqualTo(now()->subDays(7)) ? 'Minggu Ini' : 'Bulan Ini');

            $normalizedStatus = match (true) {
                in_array($status, ['published', 'dipublikasikan', 'active', 'aktif'], true) => 'aktif',
                in_array($status, ['scheduled', 'terjadwal'], true) => 'terjadwal',
                in_array($status, ['archived', 'arsip', 'diarsipkan'], true) => 'berakhir',
                default => 'draft',
            };

            return [
                'id' => $announcement->id,
                'judul' => $announcement->judul,
                'kategori' => $roleLabel,
                'tujuan' => $roleLabel,
                'publikasi' => $dateValue->translatedFormat('d M Y H:i'),
                'status' => $normalizedStatus,
                'dibaca' => $announcement->readers()->count(),
                'penerima' => $audienceCount,
                'batas' => $dateValue->translatedFormat('d M Y'),
                'tanggal' => $timeGroup,
                'isi' => $announcement->isi ?? '-',
            ];
        })->values();

        return view('peserta.komunikasi.pengumuman', [
            'announcementData' => $announcementData,
        ]);
    })->name('peserta.komunikasi.pengumuman');

    Route::get('/peserta/komunikasi/notifikasi', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'peserta', 403);
        $user->loadMissing(['notificationPreference']);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $notifications = Notification::query()
            ->with('user')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->latest('id')
            ->get();

        return view('peserta.komunikasi.notifikasi', [
            'notifications' => $notifications,
            'notificationPreference' => $user->notificationPreference,
        ]);
    })->name('peserta.komunikasi.notifikasi');
    Route::patch('/peserta/komunikasi/notifikasi/preferensi', [NotificationPreferenceController::class, 'update'])
        ->name('peserta.komunikasi.notifikasi.preferensi');

    Route::post('/komunikasi/kirim', [CommunicationController::class, 'send'])->name('komunikasi.send');
    Route::post('/komunikasi/{conversation}/balas', [CommunicationController::class, 'reply'])->name('komunikasi.reply');
    Route::post('/komunikasi/{conversation}/baca', [CommunicationController::class, 'markRead'])->name('komunikasi.read');

    Route::get('/peserta/pengaturan', function () {
        $user = request()->user()->loadMissing(['peserta.perguruanTinggi', 'peserta.internship', 'mentor', 'notifications', 'documents']);

        return view('peserta.pengaturan.index', [
            'user' => $user,
            'activeSetting' => 'Ringkasan Akun',
        ]);
    })->name('peserta.pengaturan');

    Route::get('/peserta/pengaturan/profil-akun', function () {
        $user = request()->user()->loadMissing(['peserta.perguruanTinggi', 'peserta.internship', 'mentor', 'verifiedBy']);

        return view('peserta.pengaturan.profil', compact('user'));
    })->name('peserta.pengaturan.profil');

    Route::post('/peserta/pengaturan/profil-akun', function (Request $request) {
        $user = $request->user()->loadMissing(['peserta.perguruanTinggi', 'peserta.internship', 'mentor']);

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'nim' => ['required', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'max:30'],
            'jurusan' => ['required', 'string', 'max:255'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'program_magang' => ['nullable', 'string', 'max:255'],
            'pembimbing_akademik' => ['nullable', 'string', 'max:255'],
            'semester' => ['required', 'string', 'max:50'],
            'no_hp' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
        ])->save();

        $user->peserta()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nim' => $validated['nim'],
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'jurusan' => $validated['jurusan'],
                'fakultas' => $validated['fakultas'] ?? null,
                'program_magang' => $validated['program_magang'] ?? null,
                'pembimbing_akademik' => $validated['pembimbing_akademik'] ?? null,
                'semester' => $validated['semester'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
            ]
        );

        return back()->with('success', 'Profil peserta berhasil diperbarui.');
    })->name('peserta.pengaturan.update');

    Route::post('/peserta/pengaturan/foto', function (Request $request) {
        $user = $request->user()->loadMissing('peserta');

        abort_unless($user->role === 'peserta' && $user->peserta, 403);

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $oldPhoto = $user->foto;
        $fotoPath = $request->file('foto')->store('foto-peserta', 'public');

        $user->forceFill(['foto' => $fotoPath])->save();

        if ($oldPhoto && $oldPhoto !== $fotoPath && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        $payload = [
            'message' => 'Foto profil peserta berhasil diperbarui.',
            'avatar_url' => $user->fresh()->avatar_url,
            'foto' => $fotoPath,
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return back()->with('success', $payload['message']);
    })->name('peserta.pengaturan.foto');

    Route::get('/peserta/pengaturan/ubah-password', function () {
        $user = request()->user()->loadMissing(['securityActivities' => fn ($query) => $query->latest()->limit(20)]);

        $securityHistories = $user->securityActivities->map(function ($activity) {
            $text = strtolower((string) $activity->aktivitas);
            $type = match (true) {
                str_contains($text, 'password') => 'Password',
                str_contains($text, 'login') => 'Login',
                str_contains($text, 'verifikasi') => 'Verifikasi',
                str_contains($text, 'perangkat') => 'Perangkat',
                default => 'Aktivitas',
            };

            return [
                'jenis' => $type,
                'perangkat' => $activity->perangkat ?: '-',
                'browser' => $activity->browser ?: '-',
                'lokasi' => $activity->ip_address ?: '-',
                'waktu' => optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
                'status' => $activity->status ?: 'berhasil',
                'metode' => $activity->catatan ?: $activity->aktivitas,
                'aktivitas' => $activity->aktivitas,
            ];
        })->values();

        return view('peserta.pengaturan.password', compact('user', 'securityHistories'));
    })->name('peserta.pengaturan.password');

    Route::post('/peserta/pengaturan/ubah-password', function (Request $request) {
        $user = $request->user()->loadMissing('peserta');

        abort_unless($user->role === 'peserta', 403);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ])->save();

        \App\Models\SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengubah password akun peserta.',
            'perangkat' => str_contains(strtolower((string) $request->userAgent()), 'mobile') ? 'Mobile' : (str_contains(strtolower((string) $request->userAgent()), 'tablet') ? 'Tablet' : 'Desktop'),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Password peserta berhasil diperbarui.',
        ]);

        return back()->with('success', 'Password peserta berhasil diperbarui.');
    })->name('peserta.pengaturan.password.update');

    Route::get('/pembimbing/dashboard', function () {
        return view('pembimbing.dashboard', app(PembimbingDataService::class)->forUser(auth()->user()));
    })->name('pembimbing.dashboard');

    Route::get('/pembimbing/mahasiswa-bimbingan', function () {
        $pembimbing = auth()->user()->pembimbing;
        $today = Carbon::today();

        $internships = $pembimbing
            ? Internship::query()
                ->with([
                    'peserta.user',
                    'peserta.perguruanTinggi',
                    'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                    'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                    'peserta.assessments' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                ])
                ->where('pembimbing_id', $pembimbing->id)
                ->orderByDesc('updated_at')
                ->get()
            : collect();

        $students = $internships->map(function (Internship $internship) use ($today) {
            $peserta = $internship->peserta;
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();
            $attendances = $peserta?->attendances ?? collect();
            $assessments = $peserta?->assessments ?? collect();

            $tanggalMulai = $peserta?->tanggal_mulai_magang?->copy() ?? $internship->tanggal_mulai?->copy();
            $tanggalSelesai = $peserta?->tanggal_selesai_magang?->copy() ?? $internship->tanggal_selesai?->copy();
            $durasi = ($tanggalMulai && $tanggalSelesai) ? max(1, $tanggalMulai->diffInDays($tanggalSelesai)) : 0;
            $berjalan = ($tanggalMulai && $durasi > 0) ? max(0, min($durasi, $tanggalMulai->diffInDays($today))) : 0;
            $progress = $durasi > 0 ? (int) round(($berjalan / $durasi) * 100) : 0;

            $attendanceValid = $attendances->whereIn('status', ['hadir', 'terlambat']);
            $attendanceAbsent = $attendances->whereIn('status', ['alpa', 'tidak_hadir']);
            $attendanceLate = $attendances->where('status', 'terlambat');
            $attendancePercent = $attendances->count() > 0
                ? (int) round(($attendanceValid->count() / $attendances->count()) * 100)
                : 0;

            $latestReport = $reports->first();
            $latestLogbook = $logbooks->first();
            $latestAssessment = $assessments->first();

            $status = 'aktif';

            if ($internship->status === 'selesai' || ($tanggalSelesai && $today->greaterThanOrEqualTo($tanggalSelesai))) {
                $status = 'selesai';
            } elseif (($peserta?->status ?? 'aktif') !== 'aktif' || $internship->status === 'pending') {
                $status = 'belum aktif';
            } elseif (
                ($attendancePercent > 0 && $attendancePercent < 70)
                || in_array($latestReport?->status, ['review', 'revisi', 'pending', 'draft'], true)
                || in_array($latestLogbook?->status, ['pending', 'revisi', 'ditolak'], true)
                || ($latestAssessment && ! in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true))
            ) {
                $status = 'perlu tindak lanjut';
            }

            $laporanStatus = match ($latestReport?->status) {
                'approved', 'disetujui', 'selesai' => 'selesai',
                'review', 'revisi', 'pending', 'draft' => 'review',
                'rejected', 'ditolak' => 'belum',
                default => 'belum',
            };

            $nilaiStatus = ($latestAssessment && in_array($latestAssessment->status, ['final', 'selesai', 'disetujui'], true))
                || (($latestAssessment?->nilai_akhir ?? 0) > 0)
                ? 'selesai'
                : 'belum';

            $absensiStatus = $attendances->count() === 0
                ? 'Belum lengkap'
                : (
                    $attendanceAbsent->count() > 0
                        ? 'Tidak hadir '.$attendanceAbsent->count().' kali'
                        : (
                            $attendanceLate->count() > 0
                                ? 'Terlambat '.$attendanceLate->count().' kali'
                                : 'Hadir '.$attendanceValid->count().' hari'
                        )
                );

            $logbookStatus = $logbooks->count() > 0 && in_array($latestLogbook?->status, ['approved', 'disetujui', 'selesai'], true)
                ? 'Lengkap'
                : ($logbooks->count() > 0 ? 'Perlu cek' : 'Belum ada');

            $catatan = $latestAssessment?->catatan
                ?? $latestReport?->catatan
                ?? $latestLogbook?->deskripsi
                ?? $internship->deskripsi
                ?? 'Data bimbingan belum memiliki catatan terbaru.';

            return [
                'id' => $internship->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'lokasi' => $internship->instansi ?: ($internship->lokasi ?? '-'),
                'periode' => $peserta?->program_magang ?? '-',
                'status' => $status,
                'progress' => $progress,
                'laporan' => $laporanStatus,
                'nilai' => $nilaiStatus,
                'absensi' => $absensiStatus,
                'logbook' => $logbookStatus,
                'catatan' => $catatan,
            ];
        })->values();

        return view('pembimbing.mahasiswa', [
            'studentsData' => $students,
        ]);
    })->name('pembimbing.mahasiswa');

    Route::post('/pembimbing/mahasiswa-bimbingan/{internship}/pesan', function (Request $request, Internship $internship) {
        $pembimbing = $request->user()->pembimbing;

        abort_unless($pembimbing, 403);
        abort_unless($internship->pembimbing_id === $pembimbing->id, 403);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'string', 'max:255'],
        ]);

        $peserta = $internship->peserta()->with('user')->first();
        abort_unless($peserta?->user, 404);

        DB::transaction(function () use ($validated, $pembimbing, $peserta, $internship) {
            $conversation = Conversation::firstOrCreate(
                [
                    'peserta_id' => $peserta->id,
                    'mentor_id' => null,
                    'pembimbing_id' => $pembimbing->id,
                    'admin_id' => null,
                ],
                [
                    'topik' => 'Bimbingan Mahasiswa',
                    'status' => 'aktif',
                    'last_message_at' => now(),
                ]
            );

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $pembimbing->user_id,
                'pesan' => $validated['message'],
                'lampiran' => $validated['attachment'] ?: null,
            ]);

            $conversation->update([
                'topik' => $validated['subject'],
                'status' => 'aktif',
                'last_message_at' => now(),
            ]);

            Activity::create([
                'user_id' => $pembimbing->user_id,
                'aktivitas' => 'Mengirim pesan ke peserta '.$peserta->user->name.' pada menu mahasiswa bimbingan.',
            ]);

            $internship->touch();
        });

        return back()->with('success', 'Pesan berhasil dikirim ke peserta.');
    })->name('pembimbing.mahasiswa.pesan.store');

    Route::get('/pembimbing/monitoring', function () {
        return view('pembimbing.monitoring.index', app(PembimbingDataService::class)->monitoringOverviewForUser(auth()->user()));
    })->name('pembimbing.monitoring.index');

    Route::get('/pembimbing/monitoring/kegiatan-mahasiswa', function () {
        return view('pembimbing.monitoring.kegiatan', app(PembimbingDataService::class)->monitoringActivitiesForUser(auth()->user()));
    })->name('pembimbing.monitoring.kegiatan');

    Route::get('/pembimbing/monitoring/absensi', function () {
        $context = app(PembimbingDataService::class)->forUser(auth()->user());
        $internships = $context['internships'] ?? collect();
        $today = now('Asia/Jakarta');

        $attendanceRows = $internships->map(function (Internship $internship) use ($today) {
            $peserta = $internship->peserta;
            $attendances = $peserta?->attendances?->sortByDesc('tanggal')->sortByDesc('id')->values() ?? collect();

            $hadir = $attendances->where('status', 'hadir')->count();
            $terlambat = $attendances->where('status', 'terlambat')->count();
            $tidakHadir = $attendances->whereIn('status', ['alpa', 'tidak_hadir'])->count();
            $izin = $attendances->where('status', 'izin')->count();
            $sakit = $attendances->where('status', 'sakit')->count();
            $valid = $hadir + $terlambat;
            $persen = $attendances->count()
                ? (int) round(($valid / $attendances->count()) * 100)
                : 0;

            $todayAttendance = $attendances->first(fn (Attendance $attendance) => $attendance->tanggal?->isSameDay($today));
            $todayStatus = match ($todayAttendance?->status) {
                'hadir' => 'hadir',
                'terlambat' => 'terlambat',
                'izin', 'sakit' => 'dipantau',
                'alpa', 'tidak_hadir' => 'tidak hadir',
                default => $tidakHadir > 0 ? 'tidak hadir' : ($terlambat > 0 ? 'terlambat' : ($persen >= 90 ? 'hadir' : 'dipantau')),
            };

            $status = match (true) {
                $todayStatus === 'terlambat' => 'terlambat',
                $todayStatus === 'tidak hadir' => 'tidak hadir',
                $tidakHadir > 0 => 'tidak hadir',
                $terlambat > 0 && $persen < 90 => 'terlambat',
                $persen >= 90 => 'hadir',
                default => 'dipantau',
            };

            $penempatan = $internship->divisi
                ?: $internship->unit_kerja
                ?: $internship->posisi
                ?: '-';

            return [
                'id' => $internship->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'penempatan' => $penempatan,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'tidakHadir' => $tidakHadir,
                'absen' => $tidakHadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'percent' => $persen,
                'persen' => $persen,
                'status' => $status,
                'today' => $todayStatus,
                'periode' => $peserta?->program_magang ?? '-',
                'catatan' => $todayAttendance?->keterangan
                    ?: $attendances->first()?->keterangan
                    ?: $internship->deskripsi
                    ?: 'Belum ada catatan absensi terbaru.',
            ];
        })->values();

        $attendancePlacementOptions = $attendanceRows
            ->pluck('penempatan')
            ->filter(fn ($value) => filled($value) && $value !== '-')
            ->unique()
            ->values();

        return view('pembimbing.monitoring.absensi', compact('attendanceRows', 'attendancePlacementOptions'));
    })->name('pembimbing.monitoring.absensi');

    Route::get('/pembimbing/monitoring/kerjasama', function () {
        return view('pembimbing.monitoring.kerjasama', app(PembimbingDataService::class)->cooperationDataForUser(auth()->user()));
    })->name('pembimbing.monitoring.kerjasama');

    Route::get('/pembimbing/monitoring/status-magang', function () {
        return view('pembimbing.monitoring.progress', app(PembimbingDataService::class)->statusMagangForUser(auth()->user()));
    })->name('pembimbing.monitoring.status');

    Route::get('/pembimbing/monitoring/laporan', function () {
        $user = request()->user()->loadMissing('pembimbing');

        $reportItems = Report::query()
            ->with(['peserta.user', 'peserta.internship', 'reviewer'])
            ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get()
            ->map(function (Report $report) {
                $jenisRaw = strtolower(trim((string) ($report->jenis ?? 'berkala')));
                $isFinalReport = str_contains($jenisRaw, 'akhir') || str_contains($jenisRaw, 'final');
                $status = match ($report->status) {
                    'approved', 'disetujui' => 'disetujui',
                    'pending', 'menunggu', 'review' => 'menunggu review',
                    'revisi', 'rejected', 'ditolak' => 'perlu revisi',
                    default => 'draft',
                };

                return [
                    'id' => $report->id,
                    'nama' => $report->peserta?->user?->name ?? '-',
                    'nim' => $report->peserta?->nim ?? '-',
                    'prodi' => $report->peserta?->jurusan ?? '-',
                    'penempatan' => $report->peserta?->internship?->divisi
                        ?: $report->peserta?->internship?->unit_kerja
                        ?: $report->peserta?->internship?->posisi
                        ?: '-',
                    'jenis_raw' => $isFinalReport ? 'akhir' : 'berkala',
                    'jenis' => $isFinalReport ? 'Laporan Akhir' : 'Laporan Berkala',
                    'judul' => $report->judul ?: 'Laporan Magang',
                    'unggah' => optional($report->created_at)->translatedFormat('d M Y') ?? '-',
                    'period' => $report->periode ?: ($report->peserta?->program_magang ?? '-'),
                    'status' => $status,
                    'catatan' => $report->catatan_pembimbing ?: $report->catatan ?: 'Belum ada catatan.',
                    'reviewDate' => filled($report->catatan_pembimbing) || in_array($status, ['disetujui', 'perlu revisi'], true)
                        ? optional($report->updated_at)->translatedFormat('d M Y') ?? '-'
                        : '-',
                    'dokumen' => basename((string) $report->file),
                    'revisi' => $status === 'disetujui' ? 100 : ($status === 'perlu revisi' ? 72 : 0),
                    'download_url' => route('reports.download', $report),
                    'catatan_mentor' => $report->catatan_mentor ?: '-',
                ];
            })
            ->values();

        return view('pembimbing.review.laporan', compact('reportItems'));
    })->name('pembimbing.monitoring.laporan');

    Route::get('/pembimbing/monitoring/progress', function () {
        return view('pembimbing.monitoring.progress');
    })->name('pembimbing.monitoring.progress');

    Route::get('/pembimbing/monitoring/logbook', function () {
        return view('pembimbing.monitoring.logbook');
    })->name('pembimbing.monitoring.logbook');

    Route::get('/pembimbing/review', function () {
        $user = request()->user()->loadMissing('pembimbing');

        $reportItems = Report::query()
            ->with(['peserta.user', 'peserta.internship', 'reviewer'])
            ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get()
            ->map(function (Report $report) {
                $status = match ($report->status) {
                    'approved', 'disetujui' => 'disetujui',
                    'pending', 'menunggu', 'review' => 'menunggu review',
                    'revisi', 'rejected', 'ditolak' => 'direvisi',
                    default => 'draft',
                };

                return [
                    'id' => $report->id,
                    'nama' => $report->peserta?->user?->name ?? '-',
                    'nim' => $report->peserta?->nim ?? '-',
                    'prodi' => $report->peserta?->jurusan ?? '-',
                    'penempatan' => $report->peserta?->internship?->divisi
                        ?: $report->peserta?->internship?->unit_kerja
                        ?: $report->peserta?->internship?->posisi
                        ?: '-',
                    'jenis_raw' => strtolower((string) ($report->jenis ?? 'berkala')),
                    'jenis' => strtolower((string) ($report->jenis ?? 'berkala')) === 'akhir' ? 'Laporan Akhir' : 'Laporan Berkala',
                    'judul' => $report->judul ?: 'Laporan Magang',
                    'unggah' => optional($report->created_at)->translatedFormat('d M Y') ?? '-',
                    'period' => $report->periode ?: ($report->peserta?->program_magang ?? '-'),
                    'status' => $status,
                    'catatan' => $report->catatan_pembimbing ?: $report->catatan ?: 'Belum ada catatan.',
                    'reviewDate' => filled($report->catatan_pembimbing) || in_array($status, ['disetujui', 'direvisi'], true)
                        ? optional($report->updated_at)->translatedFormat('d M Y') ?? '-'
                        : '-',
                    'dokumen' => basename((string) $report->file),
                ];
            })
            ->values();

        return view('pembimbing.review.index', compact('reportItems'));
    })->name('pembimbing.review.index');

    Route::get('/pembimbing/review/laporan', function () {
        $user = request()->user()->loadMissing('pembimbing');

        $reportItems = Report::query()
            ->with(['peserta.user', 'peserta.internship', 'reviewer'])
            ->whereHas('peserta.internship.pembimbing', fn ($query) => $query->where('user_id', $user->id))
            ->latest()
            ->get()
            ->map(function (Report $report) {
                $jenisRaw = strtolower(trim((string) ($report->jenis ?? 'berkala')));
                $isFinalReport = str_contains($jenisRaw, 'akhir') || str_contains($jenisRaw, 'final');
                $status = match ($report->status) {
                    'approved', 'disetujui' => 'disetujui',
                    'pending', 'menunggu' => 'menunggu review',
                    'revisi', 'rejected', 'ditolak' => 'revisi',
                    default => 'draft',
                };

                if ($status === 'menunggu review' && filled($report->catatan_pembimbing)) {
                    $status = 'review selesai';
                }

                return [
                    'id' => $report->id,
                    'nama' => $report->peserta?->user?->name ?? '-',
                    'nim' => $report->peserta?->nim ?? '-',
                    'prodi' => $report->peserta?->jurusan ?? '-',
                    'penempatan' => $report->peserta?->internship?->divisi
                        ?: $report->peserta?->internship?->unit_kerja
                        ?: $report->peserta?->internship?->posisi
                        ?: '-',
                    'jenis_raw' => $isFinalReport ? 'akhir' : 'berkala',
                    'judul' => $report->judul,
                    'unggah' => optional($report->created_at)->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y'),
                    'period' => $report->periode ?: ($report->peserta?->program_magang ?? '-'),
                    'status' => $report->pembimbing_review_status ?: $status,
                    'catatan' => $report->catatan_pembimbing ?: $report->catatan ?: 'Menunggu review.',
                    'catatan_mentor' => $report->catatan_mentor ?: '-',
                    'catatan_pembimbing' => $report->catatan_pembimbing ?: '-',
                    'pembimbing_review_status' => $report->pembimbing_review_status ?: 'menunggu review',
                    'download_url' => route('reports.download', $report),
                    'revisi' => $status === 'disetujui' ? 100 : ($status === 'perlu revisi' ? 72 : 0),
                    'dokumen' => $report->file,
                    'reviewTime' => $status === 'disetujui' ? 2 : ($status === 'revisi' ? 1 : 0),
                ];
            })
            ->values();

        return view('pembimbing.review.laporan', compact('reportItems'));
    })->name('pembimbing.review.laporan');

    Route::patch('/pembimbing/review/laporan/{report}', function (Request $request, Report $report) {
        $user = $request->user()->loadMissing('pembimbing');
        abort_unless($user->pembimbing, 403);
        abort_unless($report->peserta?->internship?->pembimbing?->user_id === $user->id, 403);

        $validated = $request->validate([
            'action' => ['required', 'in:review,approve,reject'],
            'catatan' => ['required', 'string'],
        ]);

        if (Schema::hasColumn('reports', 'catatan_pembimbing')) {
            $report->catatan_pembimbing = $validated['catatan'];
        }
        if (Schema::hasColumn('reports', 'pembimbing_review_status')) {
            $report->pembimbing_review_status = match ($validated['action']) {
                'approve' => 'disetujui',
                'reject' => 'perlu revisi',
                default => 'menunggu review',
            };
        }

        $report->status = match ($validated['action']) {
            'approve' => 'approved',
            'reject' => 'revisi',
            default => 'pending',
        };
        $report->save();

        Notification::create([
            'user_id' => $report->peserta?->user_id,
            'judul' => 'Review pembimbing akademik diperbarui',
            'pesan' => 'Laporan '.$report->judul.' sudah mendapat catatan dari pembimbing akademik.',
            'dibaca' => false,
        ]);

        $payload = [
            'id' => $report->id,
            'catatan_pembimbing' => $report->catatan_pembimbing,
            'pembimbing_review_status' => $report->pembimbing_review_status ?: 'menunggu review',
            'status' => $report->pembimbing_review_status ?: ($validated['action'] === 'review' ? 'menunggu review' : $report->status),
        ];

        return $request->expectsJson()
            ? response()->json($payload)
            : back()->with('success', 'Review pembimbing akademik berhasil disimpan.');
    })->name('pembimbing.review.laporan.update');

    Route::get('/pembimbing/review/riwayat', function () {
        return view('pembimbing.review.riwayat');
    })->name('pembimbing.review.riwayat');

    Route::get('/pembimbing/penilaian', function () {
        $user = request()->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $pembimbing = $user->pembimbing;
        abort_unless($pembimbing, 403);

        $assessments = Assessment::query()
            ->with([
                'peserta.user',
                'peserta.perguruanTinggi',
                'peserta.internship',
                'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
            ])
            ->when($pembimbing?->id, fn ($query, $pembimbingId) => $query->where('pembimbing_id', $pembimbingId))
            ->latest()
            ->get();

        $scoreInputRowsData = $assessments->map(function (Assessment $assessment) use ($pembimbing) {
            $peserta = $assessment->peserta;
            $internship = $peserta?->internship;
            $attendances = $peserta?->attendances ?? collect();
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();
            $latestAssessment = $assessment;
            $statusRaw = strtolower((string) ($latestAssessment?->status ?? 'belum dinilai'));
            $storedComponents = json_decode((string) ($latestAssessment?->komponen ?? ''), true);
            $storedComponents = is_array($storedComponents) ? $storedComponents : [];

            $presence = $attendances->count() > 0
                ? (int) round(($attendances->whereIn('status', ['hadir', 'terlambat'])->count() / $attendances->count()) * 100)
                : 0;

            $activity = 0;
            $start = $peserta?->tanggal_mulai_magang?->copy() ?? $internship?->tanggal_mulai?->copy();
            $end = $peserta?->tanggal_selesai_magang?->copy() ?? $internship?->tanggal_selesai?->copy();
            if ($start && $end) {
                $duration = max(1, $start->diffInDays($end));
                $elapsed = max(0, min($duration, $start->diffInDays(now())));
                $activity = (int) round(($elapsed / $duration) * 100);
            }

            $latestReport = $reports->first();
            $reportScore = match (true) {
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['approved', 'disetujui', 'selesai'], true) => 100,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['review', 'revisi'], true) => 75,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['pending', 'menunggu', 'draft'], true) => 50,
                default => 0,
            };

            $finalScore = (float) ($latestAssessment?->nilai_akhir ?: $latestAssessment?->nilai ?: 0);

            return [
                'id' => $assessment->id,
                'assessment_id' => $assessment->id,
                'peserta_id' => $peserta?->id,
                'pembimbing_id' => $pembimbing->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'instansi' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'periode' => $latestAssessment?->periode ?: ($peserta?->program_magang ?? '-'),
                'status' => in_array($statusRaw, ['final', 'selesai', 'disetujui'], true)
                    ? 'sudah dinilai'
                    : (in_array($statusRaw, ['review'], true)
                        ? 'review'
                        : (in_array($statusRaw, ['draft', 'revisi', 'ditolak'], true) ? 'draft' : 'belum dinilai')),
                'disiplin' => (int) ($storedComponents['presence'] ?? $presence),
                'kinerja' => (int) ($storedComponents['activity'] ?? $activity),
                'laporan' => (int) ($storedComponents['report'] ?? $reportScore),
                'attitude' => (int) ($storedComponents['attitude'] ?? ($finalScore > 0 ? max(0, min(100, (int) round($finalScore * 0.95))) : 0)),
                'competency' => (int) ($storedComponents['competency'] ?? round($finalScore)),
                'nilai' => (int) round($finalScore),
                'grade' => match (true) {
                    $finalScore >= 90 => 'A',
                    $finalScore >= 85 => 'B+',
                    $finalScore >= 75 => 'B',
                    $finalScore >= 70 => 'C+',
                    $finalScore > 0 => 'C',
                    default => '-',
                },
                'note' => $latestAssessment?->catatan
                    ?: $latestReport?->catatan_pembimbing
                    ?: $latestReport?->catatan
                    ?: $logbooks->first()?->deskripsi
                    ?: 'Belum ada catatan penilaian.',
            ];
        })->values();

        $studyOptionsData = $scoreInputRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $scoreInputRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $scoreInputRowsData->pluck('periode')->filter()->unique()->values();

        return view('pembimbing.penilaian.input', compact(
            'scoreInputRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));

        $assessmentRowsData = $assessments->map(function (Assessment $assessment) {
            $peserta = $assessment->peserta;
            $internship = $peserta?->internship;
            $attendances = $peserta?->attendances ?? collect();
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();

            $start = $peserta?->tanggal_mulai_magang?->copy() ?? $internship?->tanggal_mulai?->copy();
            $end = $peserta?->tanggal_selesai_magang?->copy() ?? $internship?->tanggal_selesai?->copy();
            $activity = 0;
            if ($start && $end) {
                $duration = max(1, $start->diffInDays($end));
                $elapsed = max(0, min($duration, $start->diffInDays(now())));
                $activity = (int) round(($elapsed / $duration) * 100);
            }

            $presence = $attendances->count() > 0
                ? (int) round(($attendances->whereIn('status', ['hadir', 'terlambat'])->count() / $attendances->count()) * 100)
                : 0;

            $latestReport = $reports->first();
            $reportScore = match (true) {
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['approved', 'disetujui', 'selesai'], true) => 100,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['review', 'revisi'], true) => 75,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['pending', 'menunggu', 'draft'], true) => 50,
                default => 0,
            };

            $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);
            $status = match (true) {
                in_array(strtolower((string) $assessment->status), ['final', 'selesai', 'disetujui'], true) => 'sudah dinilai',
                in_array(strtolower((string) $assessment->status), ['revisi', 'ditolak'], true) => 'perlu revisi',
                default => $finalScore > 0 ? 'sudah dinilai' : 'belum dinilai',
            };

            return [
                'id' => $assessment->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'instansi' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'periode' => $assessment->periode ?: ($peserta?->program_magang ?? '-'),
                'presence' => $presence,
                'activity' => $activity,
                'report' => $reportScore,
                'final' => (int) round($finalScore),
                'status' => $status,
                'note' => $assessment->catatan
                    ?: $latestReport?->catatan
                    ?: $logbooks->first()?->deskripsi
                    ?: 'Belum ada catatan penilaian.',
            ];
        })->values();

        $studyOptionsData = $assessmentRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $assessmentRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $assessmentRowsData->pluck('periode')->filter()->unique()->values();

        return view('pembimbing.penilaian.index', compact(
            'assessmentRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));
    })->name('pembimbing.penilaian.index');

    Route::get('/pembimbing/penilaian/input-nilai', function () {
        $user = request()->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $pembimbing = $user->pembimbing;
        abort_unless($pembimbing, 403);

        $internships = Internship::query()
            ->with([
                'peserta.user',
                'peserta.perguruanTinggi',
                'peserta.internship',
                'peserta.attendances' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.reports' => fn ($query) => $query->orderByDesc('created_at')->orderByDesc('id'),
                'peserta.logbooks' => fn ($query) => $query->orderByDesc('tanggal')->orderByDesc('id'),
                'peserta.assessments' => fn ($query) => $query->orderByDesc('updated_at')->orderByDesc('id'),
            ])
            ->where('pembimbing_id', $pembimbing->id)
            ->latest('updated_at')
            ->latest('id')
            ->get();

        $scoreInputRowsData = $internships->map(function (Internship $internship) use ($pembimbing) {
            $peserta = $internship->peserta;
            $attendances = $peserta?->attendances ?? collect();
            $reports = $peserta?->reports ?? collect();
            $logbooks = $peserta?->logbooks ?? collect();
            $latestAssessment = $peserta?->assessments
                ? $peserta->assessments->where('pembimbing_id', $pembimbing->id)->sortByDesc('updated_at')->sortByDesc('id')->first()
                : null;
            $statusRaw = strtolower((string) ($latestAssessment?->status ?? 'belum dinilai'));
            $komponen = [];
            if (! empty($latestAssessment?->komponen)) {
                $decodedKomponen = json_decode((string) $latestAssessment->komponen, true);
                $komponen = is_array($decodedKomponen) ? $decodedKomponen : [];
            }

            $presence = $attendances->count() > 0
                ? (int) round(($attendances->whereIn('status', ['hadir', 'terlambat'])->count() / $attendances->count()) * 100)
                : 0;

            $activity = 0;
            $start = $peserta?->tanggal_mulai_magang?->copy() ?? $internship?->tanggal_mulai?->copy();
            $end = $peserta?->tanggal_selesai_magang?->copy() ?? $internship?->tanggal_selesai?->copy();
            if ($start && $end) {
                $duration = max(1, $start->diffInDays($end));
                $elapsed = max(0, min($duration, $start->diffInDays(now())));
                $activity = (int) round(($elapsed / $duration) * 100);
            }

            $latestReport = $reports->first();
            $reportScore = match (true) {
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['approved', 'disetujui', 'selesai'], true) => 100,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['review', 'revisi'], true) => 75,
                in_array(strtolower((string) ($latestReport?->status ?? '')), ['pending', 'menunggu', 'draft'], true) => 50,
                default => 0,
            };

            $finalScore = (float) ($latestAssessment?->nilai_akhir ?: $latestAssessment?->nilai ?: 0);

            return [
                'id' => $internship->id,
                'assessment_id' => $latestAssessment?->id,
                'peserta_id' => $peserta?->id,
                'pembimbing_id' => $pembimbing->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'instansi' => $internship->divisi
                    ?: $internship->unit_kerja
                    ?: $internship->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'periode' => $latestAssessment?->periode ?: ($peserta?->program_magang ?? '-'),
                'status' => in_array($statusRaw, ['final', 'selesai', 'disetujui'], true)
                    ? 'sudah dinilai'
                    : (in_array($statusRaw, ['review'], true)
                        ? 'review'
                        : (in_array($statusRaw, ['draft', 'revisi', 'ditolak'], true) ? 'draft' : 'belum dinilai')),
                'disiplin' => isset($komponen['presence']) ? (int) $komponen['presence'] : $presence,
                'kinerja' => isset($komponen['activity']) ? (int) $komponen['activity'] : $activity,
                'laporan' => isset($komponen['report']) ? (int) $komponen['report'] : $reportScore,
                'attitude' => isset($komponen['attitude'])
                    ? (int) $komponen['attitude']
                    : ($finalScore > 0 ? max(0, min(100, (int) round($finalScore * 0.95))) : 0),
                'competency' => isset($komponen['competency']) ? (int) $komponen['competency'] : (int) round($finalScore),
                'nilai' => (int) round($latestAssessment?->nilai_akhir ?? $latestAssessment?->nilai ?? 0),
                'grade' => match (true) {
                    (($latestAssessment?->nilai_akhir ?? $latestAssessment?->nilai ?? 0) >= 90) => 'A',
                    (($latestAssessment?->nilai_akhir ?? $latestAssessment?->nilai ?? 0) >= 85) => 'B+',
                    (($latestAssessment?->nilai_akhir ?? $latestAssessment?->nilai ?? 0) >= 75) => 'B',
                    (($latestAssessment?->nilai_akhir ?? $latestAssessment?->nilai ?? 0) >= 70) => 'C+',
                    (($latestAssessment?->nilai_akhir ?? $latestAssessment?->nilai ?? 0) > 0) => 'C',
                    default => '-',
                },
                'note' => $latestAssessment?->catatan
                    ?: $latestReport?->catatan
                    ?: $logbooks->first()?->deskripsi
                    ?: 'Belum ada catatan penilaian.',
            ];
        })->values();

        $studyOptionsData = $scoreInputRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $scoreInputRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $scoreInputRowsData->pluck('periode')->filter()->unique()->values();

        return view('pembimbing.penilaian.input', compact(
            'scoreInputRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));
    })->name('pembimbing.penilaian.input');

    Route::match(['post', 'patch'], '/pembimbing/penilaian/input-nilai/save', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $pembimbing = $user->pembimbing;
        abort_unless($pembimbing, 403);

        $validated = $request->validate([
            'assessment_id' => ['nullable', 'exists:assessments,id'],
            'peserta_id' => ['required', 'exists:pesertas,id'],
            'periode' => ['nullable', 'string', 'max:255'],
            'presence' => ['required', 'integer', 'min:0', 'max:100'],
            'activity' => ['required', 'integer', 'min:0', 'max:100'],
            'report' => ['required', 'integer', 'min:0', 'max:100'],
            'attitude' => ['required', 'integer', 'min:0', 'max:100'],
            'competency' => ['required', 'integer', 'min:0', 'max:100'],
            'final' => ['required', 'integer', 'min:0', 'max:100'],
            'grade' => ['required', 'string', 'max:10'],
            'note' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,sudah dinilai'],
        ]);

        $assessment = ! empty($validated['assessment_id'])
            ? Assessment::query()->whereKey($validated['assessment_id'])->where('pembimbing_id', $pembimbing->id)->first()
            : null;

        $payload = [
            'peserta_id' => (int) $validated['peserta_id'],
            'mentor_id' => null,
            'pembimbing_id' => $pembimbing->id,
            'jenis' => 'pembimbing',
            'periode' => $validated['periode'] ?: null,
            'komponen' => json_encode([
                'presence' => (int) $validated['presence'],
                'activity' => (int) $validated['activity'],
                'report' => (int) $validated['report'],
                'attitude' => (int) $validated['attitude'],
                'competency' => (int) $validated['competency'],
                'grade' => $validated['grade'],
            ], JSON_UNESCAPED_UNICODE),
            'bobot' => 100,
            'nilai' => (float) $validated['final'],
            'nilai_akhir' => (float) $validated['final'],
            'status' => $validated['status'] === 'sudah dinilai' ? 'final' : 'draft',
            'catatan' => $validated['note'] ?: null,
        ];

        $assessment = $assessment ? tap($assessment)->update($payload) : Assessment::create($payload);

        return response()->json([
            'message' => 'Penilaian berhasil disimpan.',
            'assessment' => [
                'id' => $assessment->id,
                'assessment_id' => $assessment->id,
                'peserta_id' => $assessment->peserta_id,
                'presence' => (int) $validated['presence'],
                'activity' => (int) $validated['activity'],
                'report' => (int) $validated['report'],
                'attitude' => (int) $validated['attitude'],
                'competency' => (int) $validated['competency'],
                'final' => (int) $validated['final'],
                'grade' => $validated['grade'],
                'status' => $validated['status'],
                'note' => $validated['note'] ?: '-',
            ],
        ]);
    })->name('pembimbing.penilaian.input.save');

    Route::get('/pembimbing/penilaian/rekap-nilai', function () {
        $user = request()->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $pembimbing = $user->pembimbing;
        abort_unless($pembimbing, 403);

        $assessments = Assessment::query()
            ->with([
                'peserta.user',
                'peserta.internship',
                'peserta.perguruanTinggi',
            ])
            ->where('pembimbing_id', $pembimbing->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $rekapRowsData = $assessments->map(function (Assessment $assessment) {
            $peserta = $assessment->peserta;
            $internship = $peserta?->internship;
            $finalScore = (float) ($assessment->nilai_akhir ?: $assessment->nilai ?: 0);
            $statusRaw = strtolower((string) $assessment->status);
            $components = json_decode((string) $assessment->komponen, true);
            $components = is_array($components) ? $components : [];

            return [
                'id' => $assessment->id,
                'nama' => $peserta?->user?->name ?? '-',
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'instansi' => $internship?->divisi
                    ?: $internship?->unit_kerja
                    ?: $internship?->posisi
                    ?: 'LLDIKTI Wilayah V Yogyakarta',
                'periode' => $assessment->periode ?: ($peserta?->program_magang ?? '-'),
                'mentor' => (int) ($components['mentor'] ?? 0),
                'dosen' => (int) ($components['dosen'] ?? 0),
                'final' => (int) round($finalScore),
                'grade' => match (true) {
                    $finalScore >= 90 => 'A',
                    $finalScore >= 85 => 'B+',
                    $finalScore >= 75 => 'B',
                    $finalScore >= 70 => 'C+',
                    $finalScore > 0 => 'C',
                    default => '-',
                },
                'status' => in_array($statusRaw, ['final', 'selesai', 'disetujui'], true)
                    ? 'final'
                    : (in_array($statusRaw, ['draft', 'revisi', 'ditolak'], true) ? 'draft' : 'menunggu validasi'),
                'note' => $assessment->catatan ?: 'Belum ada catatan penilaian.',
                'updated' => optional($assessment->updated_at)->format('d M Y H:i') ?? '-',
            ];
        })->values();

        $studyOptionsData = $rekapRowsData->pluck('prodi')->filter()->unique()->values();
        $instansiOptionsData = $rekapRowsData->pluck('instansi')->filter()->unique()->values();
        $periodOptionsData = $rekapRowsData->pluck('periode')->filter()->unique()->values();

        return view('pembimbing.penilaian.rekap', compact(
            'rekapRowsData',
            'studyOptionsData',
            'instansiOptionsData',
            'periodOptionsData'
        ));
    })->name('pembimbing.penilaian.rekap');

    Route::get('/pembimbing/absensi', function () {
        return view('pembimbing.menu', [
            'title' => 'Monitoring Absensi',
            'description' => 'Lihat kehadiran mahasiswa bimbingan dan tindak lanjuti absensi yang membutuhkan perhatian.',
            'type' => 'attendance',
        ]);
    })->name('pembimbing.absensi');

    Route::get('/pembimbing/kegiatan', function () {
        return view('pembimbing.menu', [
            'title' => 'Kegiatan dan Logbook',
            'description' => 'Review aktivitas harian, catatan logbook, dan progres kerja mahasiswa di tempat magang.',
            'type' => 'activities',
        ]);
    })->name('pembimbing.kegiatan');

    Route::get('/pembimbing/laporan', function () {
        return view('pembimbing.menu', [
            'title' => 'Laporan Magang',
            'description' => 'Periksa laporan mingguan, laporan akhir, dan status revisi mahasiswa bimbingan.',
            'type' => 'reports',
        ]);
    })->name('pembimbing.laporan');

    Route::get('/pembimbing/komunikasi', function () {
        $user = request()->user()->loadMissing('pembimbing');
        $pembimbing = $user->pembimbing;
        $internshipsForContact = $pembimbing
            ? $pembimbing->internships()->with(['peserta.user'])->latest('id')->get()
            : collect();

        $conversations = $pembimbing
            ? $pembimbing->conversations()
                ->with(['peserta.user', 'messages.sender'])
                ->orderByDesc('last_message_at')
                ->latest()
                ->get()
            : collect();

        $communications = $conversations->map(function (Conversation $conversation) use ($user) {
            $peserta = $conversation->peserta;
            $pesertaUser = $peserta?->user;
            $thread = $conversation->messages->sortBy('created_at')->values();
            $latestMessage = $thread->last();
            $latestAt = $latestMessage?->created_at;
            $dateGroup = 'Bulan Ini';

            if ($latestAt) {
                if ($latestAt->isToday()) {
                    $dateGroup = 'Hari Ini';
                } elseif ($latestAt->greaterThanOrEqualTo(now()->subDays(7))) {
                    $dateGroup = 'Minggu Ini';
                }
            }

            $latestIncoming = $thread->where('sender_id', '!=', $user->id)->last();
            $latestOutgoing = $thread->where('sender_id', $user->id)->last();
            $isOutgoing = $latestOutgoing && (! $latestIncoming || $latestOutgoing->created_at->greaterThanOrEqualTo($latestIncoming->created_at));
            $status = $thread->where('sender_id', '!=', $user->id)->whereNull('dibaca_pada')->count() > 0 ? 'belum dibaca' : 'dibaca';

            return [
                'id' => $conversation->id,
                'conversation_id' => $conversation->id,
                'pengirim' => $isOutgoing ? $user->name : ($pesertaUser?->name ?? '-'),
                'penerima' => $isOutgoing ? ($pesertaUser?->name ?? '-') : $user->name,
                'partner_name' => $pesertaUser?->name ?? '-',
                'topik' => $conversation->topik ?: 'Pesan Pembimbing',
                'isi' => Str::limit($latestMessage?->pesan ?? 'Belum ada pesan', 120),
                'tanggal' => $latestAt?->format('d M Y H:i') ?? '-',
                'status' => $status,
                'kategori' => match (true) {
                    Str::contains(Str::lower($conversation->topik ?? ''), 'absensi') => 'Tindak Lanjut',
                    Str::contains(Str::lower($conversation->topik ?? ''), 'kegiatan') => 'Koordinasi',
                    Str::contains(Str::lower($conversation->topik ?? ''), 'logbook') => 'Konsultasi',
                    Str::contains(Str::lower($conversation->topik ?? ''), 'progress') => 'Tindak Lanjut',
                    default => 'Konsultasi',
                },
                'tanggalKategori' => $dateGroup,
                'aktivitas' => $latestAt?->diffForHumans() ?? '-',
                'role' => $isOutgoing ? 'Pembimbing' : 'Mahasiswa',
                'aktif' => $conversation->status === 'aktif',
                'thread' => $thread->map(function ($message) use ($user, $pesertaUser) {
                    return [
                        'direction' => $message->sender_id === $user->id ? 'out' : 'in',
                        'text' => $message->pesan,
                        'time' => $message->created_at?->format('H:i') ?? '-',
                    ];
                })->values(),
                'unread_count' => $thread->where('sender_id', '!=', $user->id)->whereNull('dibaca_pada')->count(),
            ];
        })->values();

        $announcements = Schema::hasTable('announcements')
            ? Announcement::query()
                ->latest('tanggal')
                ->latest()
                ->get()
            : collect();

        $communicationsData = $communications;
        $messageMap = $communications->mapWithKeys(fn ($item) => [$item['id'] => $item['thread']->all()]);
        $contactOptions = $communications
            ->pluck('penerima')
            ->filter()
            ->unique()
            ->values();
        $studentContacts = $internshipsForContact
            ->map(function (Internship $internship) {
                $peserta = $internship->peserta;
                $pesertaUser = $peserta?->user;

                if (! $peserta || ! $pesertaUser) {
                    return null;
                }

                return [
                    'id' => $peserta->id,
                    'recipient_type' => 'peserta',
                    'recipient_id' => $peserta->id,
                    'name' => $pesertaUser->name ?? '-',
                    'nim' => $peserta->nim ?? '-',
                    'prodi' => $peserta->jurusan ?? '-',
                    'label' => trim(($pesertaUser->name ?? '-') . ' - ' . ($peserta->nim ?? '-')),
                ];
            })
            ->filter()
            ->unique('id')
            ->values();

        $summaryStats = [
            'conversation' => $communications->count(),
            'today' => $communications->where('tanggalKategori', 'Hari Ini')->count(),
            'unread' => $communications->sum('unread_count'),
            'active' => $communications->where('aktif', true)->count(),
            'announcement' => $announcements->where('status', 'aktif')->count(),
            'latest' => $communications->where('tanggalKategori', 'Hari Ini')->count(),
        ];

        return view('pembimbing.komunikasi.index', [
            'communicationsData' => $communicationsData,
            'messageMap' => $messageMap,
            'contactOptions' => $contactOptions,
            'studentContacts' => $studentContacts,
            'summaryStats' => $summaryStats,
            'activeCommunicationId' => $communications->first()['id'] ?? null,
        ]);
    })->name('pembimbing.komunikasi');

    Route::get('/pembimbing/komunikasi/pesan', function (Request $request) {
        $user = request()->user()->loadMissing('pembimbing');
        $pembimbing = $user->pembimbing;
        $selectedConversation = null;
        $conversationId = (int) $request->query('conversation');

        if ($conversationId && $pembimbing) {
            $selectedConversation = Conversation::query()
                ->with(['peserta.user', 'messages.sender'])
                ->whereKey($conversationId)
                ->first();

            $allowed = $selectedConversation && (int) $selectedConversation->pembimbing_id === (int) $pembimbing->id;

            if (! $allowed) {
                $selectedConversation = null;
            }
        }

        if ($selectedConversation) {
            app(\App\Services\Communication\CommunicationService::class)->markRead($user, $selectedConversation);
        }

        $internshipsForContact = $pembimbing
            ? $pembimbing->internships()->with(['peserta.user'])->latest('id')->get()
            : collect();

        $conversations = $pembimbing
            ? $pembimbing->conversations()
                ->with(['peserta.user', 'messages.sender'])
                ->orderByDesc('last_message_at')
                ->latest()
                ->get()
            : collect();

        $messagesData = $conversations->map(function (Conversation $conversation) use ($user) {
            $peserta = $conversation->peserta;
            $pesertaUser = $peserta?->user;
            $thread = $conversation->messages
                ->sortBy('created_at')
                ->values();
            $latestMessage = $thread->last();

            $partnerName = $pesertaUser?->name ?? '-';
            $latestSenderName = $latestMessage?->sender?->name ?? ($partnerName !== '-' ? $partnerName : '-');
            $latestIsOutgoing = $latestMessage?->sender_id === $user->id;
            $unreadCount = $thread
                ->where('sender_id', '!=', $user->id)
                ->whereNull('dibaca_pada')
                ->count();
            $incomingCount = $thread->where('sender_id', '!=', $user->id)->count();
            $sentCount = $thread->where('sender_id', $user->id)->count();
            $latestAt = $latestMessage?->created_at;
            $dateLabel = 'Bulan Ini';

            if ($latestAt) {
                if ($latestAt->isToday()) {
                    $dateLabel = 'Hari Ini';
                } elseif ($latestAt->greaterThanOrEqualTo(now()->subDays(7))) {
                    $dateLabel = 'Minggu Ini';
                }
            }

            $category = match (true) {
                Str::contains(Str::lower($conversation->topik ?? ''), 'absensi') => 'Tindak Lanjut',
                Str::contains(Str::lower($conversation->topik ?? ''), 'kegiatan') => 'Koordinasi',
                Str::contains(Str::lower($conversation->topik ?? ''), 'logbook') => 'Konsultasi',
                Str::contains(Str::lower($conversation->topik ?? ''), 'progress') => 'Arahan',
                default => 'Konsultasi',
            };

            return [
                'id' => $conversation->id,
                'conversation_id' => $conversation->id,
                'peserta_id' => $peserta?->id,
                'pengirim' => $latestSenderName,
                'penerima' => $latestIsOutgoing ? $partnerName : $user->name,
                'recipient_name' => $partnerName,
                'subjek' => $conversation->topik ?: 'Pesan Pembimbing',
                'ringkasan' => Str::limit($latestMessage?->pesan ?? 'Belum ada pesan', 120),
                'waktu' => $latestAt?->format('d M Y H:i') ?? '-',
                'status' => $unreadCount > 0 ? 'belum dibaca' : 'dibaca',
                'kategori' => $category,
                'tanggal' => $dateLabel,
                'aktivitas' => $latestAt?->diffForHumans() ?? '-',
                'arah' => $latestIsOutgoing ? 'terkirim' : 'masuk',
                'aktif' => $conversation->status === 'aktif',
                'nama' => $partnerName,
                'nim' => $peserta?->nim ?? '-',
                'prodi' => $peserta?->jurusan ?? '-',
                'thread' => $thread->map(function ($message) use ($user) {
                    return [
                        'type' => $message->sender_id === $user->id ? 'out' : 'in',
                        'text' => $message->pesan,
                        'time' => $message->created_at?->format('H:i') ?? '-',
                    ];
                })->values(),
                'unread_count' => $unreadCount,
                'incoming_count' => $incomingCount,
                'sent_count' => $sentCount,
            ];
        })->values();

        $threadMap = $messagesData->mapWithKeys(fn ($item) => [$item['id'] => $item['thread']->all()]);
        $studentOptions = $messagesData
            ->pluck('nama')
            ->filter()
            ->unique()
            ->values();
        $studentContacts = $internshipsForContact
            ->map(function (Internship $internship) {
                $peserta = $internship->peserta;
                $pesertaUser = $peserta?->user;

                if (! $peserta || ! $pesertaUser) {
                    return null;
                }

                return [
                    'id' => $peserta->id,
                    'recipient_type' => 'peserta',
                    'recipient_id' => $peserta->id,
                    'name' => $pesertaUser->name ?? '-',
                    'nim' => $peserta->nim ?? '-',
                    'prodi' => $peserta->jurusan ?? '-',
                    'label' => trim(($pesertaUser->name ?? '-') . ' - ' . ($peserta->nim ?? '-')),
                ];
            })
            ->filter()
            ->unique('id')
            ->values();

        $messageStats = [
            'incoming' => $messagesData->sum('incoming_count'),
            'sent' => $messagesData->sum('sent_count'),
            'unread' => $messagesData->sum('unread_count'),
            'active' => $messagesData->count(),
            'today' => $messagesData->where('tanggal', 'Hari Ini')->count(),
        ];

        $firstConversation = $selectedConversation
            ? $messagesData->firstWhere('id', $selectedConversation->id)
            : $messagesData->first();

        return view('pembimbing.komunikasi.pesan', [
            'messagesData' => $messagesData,
            'threadMap' => $threadMap,
            'studentOptions' => $studentOptions,
            'studentContacts' => $studentContacts,
            'messageStats' => $messageStats,
            'activeConversationId' => $firstConversation['id'] ?? null,
        ]);
    })->name('pembimbing.komunikasi.pesan');

    Route::post('/pembimbing/komunikasi/pesan/{conversation}/balas', function (Request $request, Conversation $conversation) {
        $pembimbing = $request->user()->pembimbing;

        abort_unless($pembimbing, 403);
        abort_unless($conversation->pembimbing_id === $pembimbing->id, 403);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:150'],
        ]);

        $peserta = $conversation->peserta()->with('user')->first();
        abort_unless($peserta?->user, 404);

        DB::transaction(function () use ($validated, $conversation, $pembimbing, $peserta) {
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $pembimbing->user_id,
                'pesan' => $validated['message'],
                'lampiran' => $validated['attachment'] ?: null,
            ]);

            $conversation->update([
                'topik' => $validated['subject'] ?: $conversation->topik,
                'status' => 'aktif',
                'last_message_at' => now(),
            ]);

            Activity::create([
                'user_id' => $pembimbing->user_id,
                'aktivitas' => 'Mengirim pesan ke peserta '.$peserta->user->name.' pada menu pesan pembimbing.',
            ]);
        });

        $conversation->load(['peserta.user', 'messages.sender']);
        $latestMessage = $conversation->messages->sortBy('created_at')->last();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim.',
                'conversation' => [
                    'id' => $conversation->id,
                    'status' => $conversation->status,
                    'last_message_at' => optional($conversation->last_message_at)->format('d M Y H:i'),
                    'topik' => $conversation->topik,
                ],
                'latest_message' => [
                    'type' => 'out',
                    'text' => $latestMessage?->pesan,
                    'time' => $latestMessage?->created_at?->format('H:i') ?? now()->format('H:i'),
                ],
            ]);
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    })->name('pembimbing.komunikasi.pesan.reply');

    Route::get('/pembimbing/komunikasi/pengumuman', function (Request $request) use ($backfillAnnouncementRecipients, $syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $backfillAnnouncementRecipients('pembimbing');

        $userId = $user->id;

        $receivedRows = Announcement::query()
            ->with('author')
            ->where(function ($query) use ($userId) {
                $query->whereHas('readers', fn ($readerQuery) => $readerQuery->where('users.id', $userId))
                    ->orWhere(function ($fallbackQuery) {
                        $fallbackQuery->whereRaw('LOWER(COALESCE(kategori, "")) IN (?, ?)', ['pembimbing', 'pembimbing akademik'])
                            ->whereHas('author', fn ($authorQuery) => $authorQuery->whereIn('role', ['admin', 'super_admin']));
                    });
            })
            ->whereHas('author', fn ($query) => $query->whereIn('role', ['admin', 'super_admin']))
            ->whereRaw("LOWER(COALESCE(status, '')) IN ('published', 'dipublikasikan', 'active', 'aktif')")
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $sentRows = Announcement::query()
            ->with('author')
            ->where('user_id', $userId)
            ->whereRaw('LOWER(kategori) = ?', ['peserta'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $normalizeAnnouncement = function (Announcement $announcement, string $type) {
            $status = strtolower((string) ($announcement->status ?? 'draft'));
            $date = $announcement->tanggal ?? $announcement->created_at;
            $dateValue = $date ? Carbon::parse($date) : now();
            $status = match (true) {
                in_array($status, ['published', 'dipublikasikan', 'active', 'aktif'], true) => 'aktif',
                in_array($status, ['scheduled', 'terjadwal'], true) => 'terjadwal',
                in_array($status, ['archived', 'arsip', 'diarsipkan'], true) => 'berakhir',
                default => 'draft',
            };

            return [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => Str::title((string) ($announcement->kategori ?? 'Peserta')),
                'date' => $dateValue->format('Y-m-d'),
                'date_label' => $dateValue->translatedFormat('d M Y'),
                'target' => 'Peserta',
                'status' => $status,
                'read' => $announcement->readers()->count(),
                'priority' => match ($status) {
                    'aktif' => 'Tinggi',
                    'terjadwal' => 'Sedang',
                    'berakhir' => 'Rendah',
                    default => 'Rendah',
                },
                'schedule' => $dateValue->format('Y-m-d\TH:i'),
                'schedule_label' => $dateValue->format('d M Y H:i'),
                'content' => $announcement->isi ?? '-',
                'author' => $announcement->author?->name ?? '-',
                'type' => $type,
            ];
        };

        $receivedAnnouncementData = $receivedRows->map(fn (Announcement $announcement) => $normalizeAnnouncement($announcement, 'received'))->values();
        $sentAnnouncementData = $sentRows->map(fn (Announcement $announcement) => $normalizeAnnouncement($announcement, 'sent'))->values();

        $announcementData = $receivedAnnouncementData
            ->merge($sentAnnouncementData)
            ->values()
            ->map(function (array $item) {
                return [
                    'id' => $item['id'],
                    'judul' => $item['title'],
                    'kategori' => $item['category'],
                    'tujuan' => $item['target'],
                    'publikasi' => $item['schedule_label'] ?? $item['date_label'] ?? now()->translatedFormat('d M Y H:i'),
                    'status' => $item['status'],
                    'dibaca' => $item['read'],
                    'penerima' => $item['type'] === 'received'
                        ? User::query()->where('role', 'pembimbing')->count()
                        : User::query()->where('role', 'peserta')->count(),
                    'batas' => $item['date_label'],
                    'tanggal' => $item['type'] === 'received' || $item['type'] === 'sent'
                        ? (Carbon::parse($item['date'])->isToday()
                            ? 'Hari Ini'
                            : (Carbon::parse($item['date'])->greaterThanOrEqualTo(now()->subDays(7)) ? 'Minggu Ini' : 'Bulan Ini'))
                        : 'Bulan Ini',
                    'isi' => $item['content'],
                ];
            });

        return view('pembimbing.komunikasi.pengumuman', [
            'receivedAnnouncementData' => $receivedAnnouncementData,
            'sentAnnouncementData' => $sentAnnouncementData,
            'announcementData' => $announcementData,
        ]);
    })->name('pembimbing.komunikasi.pengumuman');

    Route::post('/pembimbing/komunikasi/pengumuman', function (Request $request) use ($syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'status' => ['required', 'in:draft,aktif,terjadwal,berakhir'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $announcement = Announcement::create([
            'user_id' => $user->id,
            'judul' => $validated['judul'],
            'kategori' => 'peserta',
            'isi' => $validated['isi'],
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'] ?? now()->toDateString(),
        ]);

        $syncAnnouncementRecipients($announcement, 'peserta');

        return response()->json([
            'message' => 'Pengumuman berhasil disimpan.',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => 'Peserta',
                'date' => optional($announcement->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d'),
                'date_label' => optional($announcement->tanggal)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'target' => 'Peserta',
                'status' => $announcement->status,
                'read' => 0,
                'priority' => $announcement->status === 'aktif' ? 'Tinggi' : 'Sedang',
                'schedule' => optional($announcement->tanggal)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'),
                'schedule_label' => optional($announcement->tanggal)->translatedFormat('d M Y H:i') ?? now()->translatedFormat('d M Y H:i'),
                'content' => $announcement->isi,
                'author' => $user->name,
                'type' => 'sent',
            ],
        ]);
    })->name('pembimbing.komunikasi.pengumuman.store');

    Route::patch('/pembimbing/komunikasi/pengumuman/{announcement}', function (Request $request, Announcement $announcement) use ($syncAnnouncementRecipients) {
        $user = $request->user();
        abort_unless($user?->role === 'pembimbing', 403);
        abort_unless((int) $announcement->user_id === (int) $user->id, 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'isi' => ['required', 'string'],
            'status' => ['required', 'in:draft,aktif,terjadwal,berakhir'],
            'tanggal' => ['nullable', 'date'],
        ]);

        $announcement->update([
            'judul' => $validated['judul'],
            'kategori' => 'peserta',
            'isi' => $validated['isi'],
            'status' => $validated['status'],
            'tanggal' => $validated['tanggal'] ?? $announcement->tanggal,
        ]);

        $syncAnnouncementRecipients($announcement, 'peserta');

        return response()->json([
            'message' => 'Pengumuman berhasil diperbarui.',
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->judul,
                'category' => 'Peserta',
                'date' => optional($announcement->tanggal)->format('Y-m-d') ?? now()->format('Y-m-d'),
                'date_label' => optional($announcement->tanggal)->translatedFormat('d M Y') ?? now()->translatedFormat('d M Y'),
                'target' => 'Peserta',
                'status' => $announcement->status,
                'read' => $announcement->readers()->count(),
                'priority' => $announcement->status === 'aktif' ? 'Tinggi' : 'Sedang',
                'schedule' => optional($announcement->tanggal)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'),
                'schedule_label' => optional($announcement->tanggal)->translatedFormat('d M Y H:i') ?? now()->translatedFormat('d M Y H:i'),
                'content' => $announcement->isi,
                'author' => $user->name,
                'type' => 'sent',
            ],
        ]);
    })->name('pembimbing.komunikasi.pengumuman.update');

    Route::delete('/pembimbing/komunikasi/pengumuman/{announcement}', function (Request $request, Announcement $announcement) {
        $user = $request->user();
        abort_unless($user?->role === 'pembimbing', 403);

        $isOwnAnnouncement = (int) $announcement->user_id === (int) $user->id;

        if ($isOwnAnnouncement) {
            $announcement->delete();
        } else {
            $announcement->readers()->detach($user->id);
        }

        return response()->json([
            'message' => 'Pengumuman berhasil dihapus.',
            'type' => $isOwnAnnouncement ? 'sent' : 'received',
            'announcement_id' => $announcement->id,
        ]);
    })->name('pembimbing.komunikasi.pengumuman.destroy');

    Route::get('/pembimbing/komunikasi/notifikasi', function (Request $request) {
        $user = $request->user();
        abort_unless($user?->role === 'pembimbing', 403);

        Notification::query()
            ->where('user_id', $user->id)
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        $notificationRows = Notification::query()
            ->with('user')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->latest('id')
            ->get();

        $notificationData = $notificationRows->map(function (Notification $notification) {
            $payload = [];

            try {
                $decoded = json_decode((string) $notification->pesan, true, 512, JSON_THROW_ON_ERROR);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            } catch (\Throwable $e) {
                $payload = [];
            }

            $title = (string) ($notification->judul ?? $payload['title'] ?? 'Notifikasi Sistem');
            $message = (string) ($payload['message'] ?? $notification->pesan ?? '-');
            $source = (string) ($payload['source'] ?? $payload['sender'] ?? 'Sistem');
            $category = match (true) {
                ! blank($payload['category'] ?? null) => (string) $payload['category'],
                str_contains(strtolower($title), 'pesan') || str_contains(strtolower($message), 'pesan') => 'Komunikasi',
                str_contains(strtolower($title), 'laporan') || str_contains(strtolower($message), 'laporan') => 'Laporan',
                str_contains(strtolower($title), 'dokumen') || str_contains(strtolower($message), 'dokumen') => 'Dokumen',
                str_contains(strtolower($title), 'agenda') || str_contains(strtolower($message), 'agenda') => 'Agenda',
                default => 'Aktivitas Mahasiswa',
            };
            $date = $notification->created_at;
            $timeGroup = $date
                ? ($date->isToday()
                    ? 'Hari Ini'
                    : ($date->greaterThanOrEqualTo(now()->subDays(7)) ? 'Minggu Ini' : 'Bulan Ini'))
                : 'Bulan Ini';
            $status = strtolower((string) ($payload['status'] ?? ''));
            $status = in_array($status, ['baru', 'dibaca', 'penting', 'arsip'], true)
                ? $status
                : ((bool) $notification->dibaca ? 'dibaca' : 'baru');
            $priority = (string) ($payload['priority'] ?? match (true) {
                str_contains(strtolower($title), 'penting') || str_contains(strtolower($message), 'penting') => 'tinggi',
                str_contains(strtolower($title), 'menunggu') || str_contains(strtolower($message), 'menunggu') => 'tinggi',
                str_contains(strtolower($title), 'baru') || str_contains(strtolower($message), 'baru') => 'sedang',
                default => 'rendah',
            });
            $follow = (string) ($payload['follow'] ?? match ($category) {
                'Komunikasi' => 'balas pesan',
                'Laporan' => 'tinjau laporan',
                'Dokumen' => 'cek dokumen',
                'Agenda' => 'cek agenda',
                default => 'tindak lanjut',
            });

            return [
                'id' => $notification->id,
                'title' => $title,
                'jenis' => $category,
                'pengirim' => $source,
                'ringkasan' => $message,
                'tanggal' => $date?->format('d M Y H:i') ?? now()->format('d M Y H:i'),
                'status' => $status,
                'prioritas' => $priority,
                'tindak' => $follow,
                'periode' => $timeGroup,
                'detail' => $message,
            ];
        })->values();

        $notificationPreferences = [
            ['key' => 'Pesan', 'active' => (bool) ($user->notificationPreference?->pesan ?? true)],
            ['key' => 'Laporan', 'active' => (bool) ($user->notificationPreference?->laporan ?? true)],
            ['key' => 'Penugasan', 'active' => (bool) ($user->notificationPreference?->penugasan ?? true)],
            ['key' => 'Absensi', 'active' => (bool) ($user->notificationPreference?->absensi ?? true)],
            ['key' => 'Pengumuman', 'active' => (bool) ($user->notificationPreference?->pengumuman ?? true)],
            ['key' => 'Email', 'active' => (bool) ($user->notificationPreference?->email ?? true)],
        ];

        return view('pembimbing.komunikasi.notifikasi', [
            'notificationData' => $notificationData,
            'notificationPreferences' => $notificationPreferences,
        ]);
    })->name('pembimbing.komunikasi.notifikasi');

    Route::get('/pembimbing/pengaturan', function () {
        $user = request()->user()->load('pembimbing');

        return view('pembimbing.pengaturan.index', compact('user'));
    })->name('pembimbing.pengaturan');

    Route::get('/pembimbing/pengaturan/profil', function () {
        $user = request()->user()->loadMissing([
            'pembimbing',
            'securityActivities' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('pembimbing.pengaturan.profil', [
            'user' => $user,
            'securityActivities' => $user->securityActivities ?? collect(),
        ]);
    })->name('pembimbing.pengaturan.profil');

    Route::post('/pembimbing/pengaturan/foto', function (Request $request) {
        $user = $request->user();
        abort_unless(in_array($user?->role, ['pembimbing', 'admin', 'super_admin'], true), 403);

        $validated = $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $oldPhoto = $user->foto;
        $fotoPath = $request->file('foto')->store('foto-pembimbing', 'public');

        $user->forceFill([
            'foto' => $fotoPath,
        ])->save();

        if ($oldPhoto && $oldPhoto !== $fotoPath && Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return response()->json([
            'message' => 'Foto profil pembimbing akademik berhasil diperbarui.',
            'avatar_url' => $user->fresh()->avatar_url,
            'foto' => $fotoPath,
        ]);
    })->name('pembimbing.pengaturan.foto');

    Route::get('/pembimbing/pengaturan/ubah-password', function () {
        $user = request()->user()->load('pembimbing');

        return view('pembimbing.pengaturan.password', compact('user'));
    })->name('pembimbing.pengaturan.password');

    Route::post('/pembimbing/pengaturan', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $actionType = $request->input('action_type', 'profile');

        if ($actionType === 'password') {
            $validated = $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            ]);

            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            ]);

            return back()->with('success', 'Password akun berhasil diperbarui.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'alpha_dash', \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)],
            'nidn_nip' => ['nullable', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'max:30'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'perguruan_tinggi' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
        ]);

        $pembimbingData = collect($validated)->only([
            'nidn_nip',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'instansi',
            'jabatan',
            'no_hp',
            'alamat',
            'perguruan_tinggi',
            'program_studi',
        ])->toArray();

        $pembimbingData['instansi'] = $pembimbingData['instansi'] ?: '-';
        $pembimbingData['jabatan'] = $pembimbingData['jabatan'] ?: '-';
        $pembimbingData['no_hp'] = $pembimbingData['no_hp'] ?: '-';

        $user->pembimbing()->updateOrCreate(
            ['user_id' => $user->id],
            $pembimbingData
        );

        return back()->with('success', 'Pengaturan akun berhasil disimpan.');
    })->name('pembimbing.pengaturan.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/debug-admin-dashboard', function () {
    try {
        if (!auth()->check()) {
            return response()->json(['message' => 'not authenticated'], 401);
        }

        return response()->json([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? null,
            'user_role' => auth()->user()->role ?? null,
            'default_db' => config('database.default'),
            'db_host' => config('database.connections.pgsql.host'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->take(5)->values(),
        ], 500);
    }
});

Route::get('/debug-admin-context', function () {
    try {
        $result = [
            'auth_check' => auth()->check(),
            'user_id' => auth()->id(),
            'user_role' => auth()->user()->role ?? null,
            'has_admin_data_service' => class_exists(\App\Services\AdminDataService::class),
        ];

        if (class_exists(\App\Services\AdminDataService::class)) {
            $context = app(\App\Services\AdminDataService::class)->context();
            $result['context_keys'] = array_keys($context);
            $result['context_preview'] = array_slice($context, 0, 5, true);
        }

        return response()->json($result);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->take(5)->values(),
        ], 500);
    }
});

Route::get('/debug-admin-view', function () {
    try {
        return response()->json([
            'rendered' => view('admin.dashboard.index')->render()
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->take(5)->values(),
        ], 500);
    }
});

Route::get('/debug-admin-dashboard-error', function () {
    try {
        $serviceExists = class_exists(\App\Services\Admin\AdminDataService::class);

        $context = app(\App\Services\Admin\AdminDataService::class)->context();

        return response()->json([
            'status' => 'admin_service_ok',
            'service_exists' => $serviceExists,
            'context_keys' => array_keys($context),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'admin_service_error',
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

Route::get('/debug-admin-view-error', function () {
    try {
        return view('admin.dashboard')->render();
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'admin_view_error',
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

// === VERCEL SAFE ADMIN ROUTES START ===
Route::middleware(['auth'])->group(function () {
    $ctx = function () {
        return app(\App\Services\Admin\AdminDataService::class)->context();
    };

    $safeView = function (string $view, array $data = [], string $title = 'Halaman Admin') {
        try {
            if (! view()->exists($view)) {
                throw new \InvalidArgumentException("View [$view] tidak ditemukan.");
            }

            return response(view($view, $data)->render());
        } catch (\Throwable $e) {
            $message = e($e->getMessage());
            $file = e($e->getFile());
            $line = e((string) $e->getLine());

            return response("
                <!doctype html>
                <html lang='id'>
                <head>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <title>{$title}</title>
                    <style>
                        body{font-family:Arial,sans-serif;background:#f5f7fb;margin:0;padding:40px;color:#111827}
                        .box{max-width:900px;margin:auto;background:#fff;border-radius:14px;padding:28px;box-shadow:0 10px 30px rgba(15,23,42,.08)}
                        h1{margin-top:0;color:#1f2937}
                        pre{background:#111827;color:#f9fafb;padding:16px;border-radius:10px;white-space:pre-wrap;overflow:auto}
                        a{display:inline-block;margin-top:16px;background:#2563eb;color:#fff;padding:10px 14px;border-radius:8px;text-decoration:none}
                    </style>
                </head>
                <body>
                    <div class='box'>
                        <h1>{$title}</h1>
                        <p>Halaman ini berhasil dibuka, tetapi tampilan asli masih memiliki bagian yang perlu disesuaikan untuk hosting.</p>
                        <pre>{$message}
File: {$file}
Line: {$line}</pre>
                        <a href='/admin/dashboard'>Kembali ke Dashboard</a>
                    </div>
                </body>
                </html>
            ", 200);
        }
    };

    Route::get('/admin/verifikasi-administrasi', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.verifikasi.index', $c, 'Verifikasi Administrasi');
    })->name('admin.verifikasi.index');

    Route::get('/admin/verifikasi-administrasi/riwayat', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.verifikasi.riwayat', [
            'adminVerificationHistories' => $c['adminVerificationHistories'] ?? collect(),
            'adminVerificationHistoryStats' => $c['adminVerificationHistoryStats'] ?? [
                'total' => 0,
                'disetujui' => 0,
                'ditolak' => 0,
                'hari_ini' => 0,
            ],
        ], 'Riwayat Verifikasi');
    })->name('admin.verifikasi.riwayat');

    Route::get('/admin/manajemen-magang', function () use ($ctx, $safeView) {
        $c = $ctx();

        $magangStats = [
            'total_participants' => collect($c['adminParticipants'] ?? [])->count(),
            'attendance_today' => collect($c['adminAttendances'] ?? [])->count(),
            'reports_count' => collect($c['adminMagangReports'] ?? [])->count(),
            'active_placements' => collect($c['adminPlacements'] ?? [])->count(),
            'running_periods' => collect($c['adminParticipants'] ?? [])->pluck('program_magang')->filter()->unique()->count(),
        ];

        $preview = collect()
            ->concat(collect($c['adminAttendances'] ?? [])->take(3))
            ->concat(collect($c['adminMagangActivities'] ?? [])->take(3))
            ->concat(collect($c['adminMagangReports'] ?? [])->take(3))
            ->concat(collect($c['adminPlacements'] ?? [])->take(3))
            ->values();

        return $safeView('admin.magang.index', [
            'magangStats' => $magangStats,
            'adminMagangPreview' => $preview,
        ], 'Manajemen Magang');
    })->name('admin.magang.index');

    Route::get('/admin/manajemen-magang/absensi', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.absensi', [
            'adminAttendances' => $c['adminAttendances'] ?? collect(),
        ], 'Absensi Magang');
    })->name('admin.magang.absensi');

    Route::get('/admin/manajemen-magang/dokumen', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.dokumen', [
            'adminDocumentParticipants' => $c['adminDocumentParticipants'] ?? collect(),
            'adminDocumentTypes' => $c['adminDocumentTypes'] ?? collect(),
            'adminDocumentStats' => $c['adminDocumentStats'] ?? [],
        ], 'Dokumen Magang');
    })->name('admin.magang.dokumen');

    Route::get('/admin/manajemen-magang/kegiatan', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.kegiatan', [
            'adminMagangActivities' => $c['adminMagangActivities'] ?? collect(),
            'adminMagangParticipants' => $c['adminParticipants'] ?? collect(),
        ], 'Kegiatan Magang');
    })->name('admin.magang.kegiatan');

    Route::get('/admin/manajemen-magang/laporan', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.laporan', [
            'pageTitle' => 'Laporan Berkala',
            'adminMagangReports' => $c['adminMagangReports'] ?? collect(),
        ], 'Laporan Berkala');
    })->name('admin.magang.laporan');

    Route::get('/admin/manajemen-magang/laporan-akhir', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.laporan', [
            'pageTitle' => 'Laporan Akhir',
            'adminMagangReports' => $c['adminMagangReports'] ?? collect(),
        ], 'Laporan Akhir');
    })->name('admin.magang.laporan-akhir');

    Route::get('/admin/manajemen-magang/penempatan', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.penempatan', [
            'adminPlacements' => $c['adminPlacements'] ?? collect(),
            'adminParticipants' => $c['adminParticipants'] ?? collect(),
            'adminMentors' => $c['adminMentors'] ?? collect(),
            'adminAdvisors' => $c['adminAdvisors'] ?? collect(),
        ], 'Penempatan Magang');
    })->name('admin.magang.penempatan');

    Route::get('/admin/manajemen-magang/periode', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.periode', $c, 'Periode Magang');
    })->name('admin.magang.periode');

    Route::get('/admin/manajemen-magang/penilaian', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.magang.penilaian', [
            'adminParticipants' => $c['adminParticipants'] ?? collect(),
            'adminMentors' => $c['adminMentors'] ?? collect(),
            'adminAdvisors' => $c['adminAdvisors'] ?? collect(),
        ], 'Penilaian Magang');
    })->name('admin.magang.penilaian');

    Route::get('/admin/manajemen-pengguna', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.pengguna.index', [
            'adminUsers' => $c['adminUsers'] ?? collect(),
        ], 'Manajemen Pengguna');
    })->name('admin.pengguna.index');

    Route::get('/admin/manajemen-pengguna/peserta-magang', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.pengguna.peserta', [
            'adminParticipants' => $c['adminParticipants'] ?? collect(),
            'adminCampuses' => $c['adminCampuses'] ?? collect(),
        ], 'Peserta Magang');
    })->name('admin.pengguna.peserta');

    Route::get('/admin/manajemen-pengguna/mentor', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.pengguna.mentor', [
            'adminMentors' => $c['adminMentors'] ?? collect(),
        ], 'Mentor');
    })->name('admin.pengguna.mentor');

    Route::get('/admin/manajemen-pengguna/pembimbing-akademik', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.pengguna.pembimbing', [
            'adminAdvisors' => $c['adminAdvisors'] ?? collect(),
        ], 'Pembimbing Akademik');
    })->name('admin.pengguna.pembimbing');

    Route::get('/admin/manajemen-perguruan-tinggi', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.perguruan_tinggi.index', [
            'adminCampuses' => $c['adminCampuses'] ?? collect(),
            'campusStats' => [
                'total' => collect($c['adminCampuses'] ?? [])->count(),
                'aktif' => collect($c['adminCampuses'] ?? [])->where('status', 'aktif')->count(),
                'menunggu' => collect($c['adminCampuses'] ?? [])->where('status', 'menunggu')->count(),
                'ditolak' => collect($c['adminCampuses'] ?? [])->where('status', 'ditolak')->count(),
            ],
            'cooperationStats' => [
                'total' => 0,
                'aktif' => 0,
                'menunggu' => 0,
                'berakhir' => 0,
            ],
        ], 'Manajemen Perguruan Tinggi');
    })->name('admin.perguruan-tinggi.index');

    Route::get('/admin/manajemen-perguruan-tinggi/data', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.perguruan_tinggi.data', [
            'adminCampuses' => $c['adminCampuses'] ?? collect(),
        ], 'Data Perguruan Tinggi');
    })->name('admin.perguruan-tinggi.data');

    Route::get('/admin/laporan-monitoring', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.laporan_monitoring.index', [
            'activeTab' => 'overview',
            'adminStats' => $c['adminStats'] ?? [],
            'adminRecentActivities' => $c['adminRecentActivities'] ?? collect(),
            'adminMagangReports' => $c['adminMagangReports'] ?? collect(),
        ], 'Laporan Monitoring');
    })->name('admin.laporan-monitoring.index');

    Route::get('/admin/laporan-monitoring/rekap-absensi', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.laporan_monitoring.rekap_absensi', [
            'adminAttendanceRecaps' => $c['adminAttendanceRecaps'] ?? collect(),
        ], 'Rekap Absensi');
    })->name('admin.laporan-monitoring.rekap-absensi');

    Route::get('/admin/laporan-monitoring/rekap-kegiatan', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.laporan_monitoring.rekap_kegiatan', [
            'adminMagangActivities' => $c['adminMagangActivities'] ?? collect(),
        ], 'Rekap Kegiatan');
    })->name('admin.laporan-monitoring.rekap-kegiatan');

    Route::get('/admin/laporan-monitoring/statistik-pengguna', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.laporan_monitoring.statistik_pengguna', [
            'adminUsers' => $c['adminUsers'] ?? collect(),
            'adminParticipants' => $c['adminParticipants'] ?? collect(),
            'adminMentors' => $c['adminMentors'] ?? collect(),
            'adminAdvisors' => $c['adminAdvisors'] ?? collect(),
        ], 'Statistik Pengguna');
    })->name('admin.laporan-monitoring.statistik-pengguna');

    Route::get('/admin/laporan-monitoring/statistik-perguruan-tinggi', function () use ($ctx, $safeView) {
        $c = $ctx();

        return $safeView('admin.laporan_monitoring.statistik_perguruan_tinggi', [
            'adminCampuses' => $c['adminCampuses'] ?? collect(),
        ], 'Statistik Perguruan Tinggi');
    })->name('admin.laporan-monitoring.statistik-perguruan-tinggi');

    Route::get('/admin/pengaturan', function () use ($ctx, $safeView) {
        return $safeView('admin.pengaturan.index', $ctx(), 'Pengaturan');
    })->name('admin.pengaturan.index');
});
// === VERCEL SAFE ADMIN ROUTES END ===
