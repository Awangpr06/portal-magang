<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use App\Models\Pembimbing;
use App\Models\PerguruanTinggi;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        if ($request->query('role') === 'pembimbing') {
            return view('auth.register-pembimbing');
        }

        if ($request->query('role') === 'peserta') {
            return view('auth.register-peserta');
        }

        if ($request->query('role') === 'mentor') {
            return view('auth.register-mentor');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->input('role') === 'pembimbing') {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'nidn_nip' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:255'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
                'no_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string'],
                'perguruan_tinggi' => ['required', 'string', 'max:255'],
                'program_studi' => ['required', 'string', 'max:255'],
                'jabatan' => ['required', 'string', 'max:255'],
                'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $fotoPath = $request->file('foto')?->store('foto-pembimbing', 'public');

            $user = DB::transaction(function () use ($validated, $fotoPath) {
                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'pembimbing',
                    'foto' => $fotoPath,
                    'account_status' => 'menunggu',
                ]);

                Pembimbing::create([
                    'user_id' => $user->id,
                    'nidn_nip' => $validated['nidn_nip'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'instansi' => $validated['perguruan_tinggi'],
                    'perguruan_tinggi' => $validated['perguruan_tinggi'],
                    'program_studi' => $validated['program_studi'],
                    'jabatan' => $validated['jabatan'],
                ]);

                event(new Registered($user));

                return $user;
            });

            return redirect()
                ->route('register', ['role' => 'pembimbing'])
                ->with('registration_success', [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                ]);
        }

        if ($request->input('role') === 'peserta') {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'nim' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:255'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date'],
                'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
                'no_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string'],
                'perguruan_tinggi' => ['required', 'string', 'max:255'],
                'program_studi' => ['required', 'string', 'max:255'],
                'fakultas' => ['required', 'string', 'max:255'],
                'program_magang' => ['required', 'string', 'max:255'],
                'pembimbing_akademik' => ['required', 'string', 'max:255'],
                'tanggal_mulai_magang' => ['required', 'date'],
                'tanggal_selesai_magang' => ['required', 'date', 'after_or_equal:tanggal_mulai_magang'],
                'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $fotoPath = $request->file('foto')?->store('foto-peserta', 'public');

            $user = DB::transaction(function () use ($validated, $fotoPath) {
                $perguruanTinggi = PerguruanTinggi::firstOrCreate(
                    ['nama_pt' => $validated['perguruan_tinggi']],
                    [
                        'alamat' => $validated['alamat'],
                        'telepon' => $validated['no_hp'],
                        'email' => $validated['email'],
                    ]
                );

                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'peserta',
                    'foto' => $fotoPath,
                    'account_status' => 'menunggu',
                ]);

                Peserta::create([
                    'user_id' => $user->id,
                    'perguruan_tinggi_id' => $perguruanTinggi->id,
                    'nim' => $validated['nim'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'jurusan' => $validated['program_studi'],
                    'fakultas' => $validated['fakultas'],
                    'program_magang' => $validated['program_magang'],
                    'pembimbing_akademik' => $validated['pembimbing_akademik'],
                    'tanggal_mulai_magang' => $validated['tanggal_mulai_magang'],
                    'tanggal_selesai_magang' => $validated['tanggal_selesai_magang'],
                    'semester' => '-',
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'status' => 'pending',
                ]);

                event(new Registered($user));

                return $user;
            });

            return redirect()
                ->route('register', ['role' => 'peserta'])
                ->with('registration_success', [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                ]);
        }

        if ($request->input('role') === 'mentor') {
            $validated = $request->validate([
                'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'nip' => ['required', 'string', 'max:50'],
                'name' => ['required', 'string', 'max:255'],
                'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
                'no_hp' => ['required', 'string', 'max:30'],
                'alamat' => ['required', 'string'],
                'perguruan_tinggi' => ['required', 'in:LLDIKTI Wilayah V Yogyakarta'],
                'jabatan' => ['required', 'string', 'max:255'],
                'divisi' => ['required', 'string', 'max:255'],
                'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $fotoPath = $request->file('foto')?->store('foto-mentor', 'public');

            $user = DB::transaction(function () use ($validated, $fotoPath) {
                $user = User::create([
                    'name' => $validated['name'],
                    'username' => $validated['username'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'role' => 'mentor',
                    'foto' => $fotoPath,
                    'account_status' => 'menunggu',
                ]);

                Mentor::create([
                    'user_id' => $user->id,
                    'nip' => $validated['nip'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'jabatan' => $validated['jabatan'],
                    'divisi' => $validated['divisi'],
                    'no_hp' => $validated['no_hp'],
                    'alamat' => $validated['alamat'],
                    'perguruan_tinggi' => $validated['perguruan_tinggi'],
                ]);

                event(new Registered($user));

                return $user;
            });

            return redirect()
                ->route('register', ['role' => 'mentor'])
                ->with('registration_success', [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:mentor,peserta,pembimbing'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->input('role', 'peserta'),
            'account_status' => 'menunggu',
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil. Akun Anda sedang menunggu verifikasi admin.');
    }
}
