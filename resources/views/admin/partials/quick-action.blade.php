<div class="row">

    <div class="col-md-4 mb-3">
        <button class="btn btn-primary w-100 p-3 rounded-4" type="button" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
            <i class="bi bi-person-plus-fill"></i>
            Tambah Peserta
        </button>
    </div>

    <div class="col-md-4 mb-3">
        <button class="btn btn-success w-100 p-3 rounded-4" type="button" data-bs-toggle="modal" data-bs-target="#addCampusModal">
            <i class="bi bi-building-add"></i>
            Tambah Perguruan Tinggi
        </button>
    </div>

    <div class="col-md-4 mb-3">
        <a class="btn btn-warning w-100 p-3 rounded-4" href="{{ route('admin.verifikasi.index') }}">
            <i class="bi bi-check2-square"></i>
            Verifikasi
        </a>
    </div>

</div>

<div class="modal fade" id="addParticipantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.dashboard.peserta.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <h6 class="fw-bold mb-3">Data Akun dan Identitas</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="participantUsername">Username</label>
                                    <input class="form-control" id="participantUsername" name="username" value="{{ old('username') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantEmail">Email</label>
                                    <input class="form-control" id="participantEmail" name="email" type="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantPassword">Password</label>
                                    <input class="form-control" id="participantPassword" name="password" type="password" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantPasswordConfirmation">Ulangi Password</label>
                                    <input class="form-control" id="participantPasswordConfirmation" name="password_confirmation" type="password" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantNim">NIM/NPM</label>
                                    <input class="form-control" id="participantNim" name="nim" value="{{ old('nim') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantName">Nama Lengkap</label>
                                    <input class="form-control" id="participantName" name="name" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantBirthPlace">Tempat Lahir</label>
                                    <input class="form-control" id="participantBirthPlace" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantBirthDate">Tanggal Lahir</label>
                                    <input class="form-control" id="participantBirthDate" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Jenis Kelamin</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin" value="Laki-laki" @checked(old('jenis_kelamin') === 'Laki-laki') required>
                                            <span class="form-check-label">Laki-laki</span>
                                        </label>
                                        <label class="form-check mb-0">
                                            <input class="form-check-input" type="radio" name="jenis_kelamin" value="Perempuan" @checked(old('jenis_kelamin') === 'Perempuan') required>
                                            <span class="form-check-label">Perempuan</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantPhone">Nomor Telepon</label>
                                    <input class="form-control" id="participantPhone" name="no_hp" value="{{ old('no_hp') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="participantAddress">Alamat Lengkap</label>
                                    <textarea class="form-control" id="participantAddress" name="alamat" rows="2" required>{{ old('alamat') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="border rounded-3 p-3 h-100 bg-light">
                            <h6 class="fw-bold mb-3">Data Akademik dan Magang</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="participantCampus">Perguruan Tinggi</label>
                                    <input class="form-control" id="participantCampus" name="perguruan_tinggi" value="{{ old('perguruan_tinggi') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantStudyProgram">Program Studi</label>
                                    <input class="form-control" id="participantStudyProgram" name="program_studi" value="{{ old('program_studi') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantFaculty">Fakultas</label>
                                    <input class="form-control" id="participantFaculty" name="fakultas" value="{{ old('fakultas') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantProgram">Program Magang</label>
                                    <input class="form-control" id="participantProgram" name="program_magang" value="{{ old('program_magang') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="participantAdvisor">Pembimbing Akademik</label>
                                    <input class="form-control" id="participantAdvisor" name="pembimbing_akademik" value="{{ old('pembimbing_akademik') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantStart">Mulai Magang</label>
                                    <input class="form-control" id="participantStart" name="tanggal_mulai_magang" type="date" value="{{ old('tanggal_mulai_magang') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="participantEnd">Selesai Magang</label>
                                    <input class="form-control" id="participantEnd" name="tanggal_selesai_magang" type="date" value="{{ old('tanggal_selesai_magang') }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="participantPhoto">Foto Profil</label>
                                    <input class="form-control" id="participantPhoto" name="foto" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                    <div class="form-text">Format: jpg, jpeg, png. Maksimal 2MB.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Peserta</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addCampusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.dashboard.perguruan-tinggi.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Perguruan Tinggi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="campusName">Nama Perguruan Tinggi</label>
                        <input class="form-control" id="campusName" name="nama_pt" value="{{ old('nama_pt') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="campusFaculty">Fakultas</label>
                        <input class="form-control" id="campusFaculty" name="fakultas" value="{{ old('fakultas') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="campusStudyProgram">Program Studi</label>
                        <input class="form-control" id="campusStudyProgram" name="program_studi" value="{{ old('program_studi') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="campusType">Jenis</label>
                        <select class="form-select" id="campusType" name="jenis" required>
                            <option value="Negeri" @selected(old('jenis') === 'Negeri')>Negeri</option>
                            <option value="Swasta" @selected(old('jenis') === 'Swasta')>Swasta</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="campusRegion">Provinsi</label>
                        <select class="form-select" id="campusRegion" name="provinsi" required>
                            <option value="DI Yogyakarta" @selected(old('provinsi') === 'DI Yogyakarta')>DI Yogyakarta</option>
                            <option value="Jawa Tengah" @selected(old('provinsi') === 'Jawa Tengah')>Jawa Tengah</option>
                            <option value="Jawa Timur" @selected(old('provinsi') === 'Jawa Timur')>Jawa Timur</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="campusEmail">Email</label>
                        <input class="form-control" id="campusEmail" name="email" type="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="campusPic">Nama PIC</label>
                        <input class="form-control" id="campusPic" name="pic" value="{{ old('pic') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="campusPicNip">NIP PIC</label>
                        <input class="form-control" id="campusPicNip" name="pic_nip" value="{{ old('pic_nip') }}" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan Perguruan Tinggi</button>
            </div>
        </form>
    </div>
</div>
