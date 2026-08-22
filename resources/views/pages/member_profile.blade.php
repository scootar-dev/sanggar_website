@extends('layouts.member')
@section('title', 'Profil Saya')
@section('content')

<section style="padding-top:20px; padding-bottom:60px;">
    {{-- HEADER --}}
    <div class="m-page-header">
        <span class="m-badge">Pengaturan</span>
        <h1>Profil Saya</h1>
        <p>Kelola data diri, informasi kontak, keamanan akun, dan riwayat tarian Anda di sini.</p>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div style="margin-bottom:20px;padding:14px 18px;border-radius:12px;background:#E8F5E9;border:1px solid #A5D6A7;color:#2E7D32;font-size:.85rem;font-weight:600;">✓ {{ session('success') }}</div>
    @endif

    {{-- PROFIL --}}
    <div style="background:#fff;border-radius:20px;border:1px solid var(--border);overflow:hidden;width:100%;">
        <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" style="padding:30px;">
            @csrf
            <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:40px;border-bottom:1px solid var(--border);padding-bottom:30px;">
                <div style="position:relative;">
                    <div style="width:140px;height:140px;background:var(--primary-pale);border-radius:50%;overflow:hidden;border:4px solid #fff;box-shadow:0 8px 20px rgba(0,0,0,.1);position:relative;">
                        @if($user->foto)
                            <img src="{{ asset('storage/'.$user->foto) }}" alt="Foto Profil" style="width:100%;height:100%;object-fit:cover;" id="fotoPreview">
                            <img src="" alt="Preview" style="width:100%;height:100%;object-fit:cover;display:none;position:absolute;top:0;left:0;" id="fotoPreviewNew">
                        @else
                            <div id="fotoPlaceholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--primary);color:#fff;font-size:3rem;font-weight:800;font-family:var(--font-display);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img src="" alt="Preview" style="width:100%;height:100%;object-fit:cover;display:none;position:absolute;top:0;left:0;" id="fotoPreviewNew">
                        @endif
                    </div>
                    <label for="fotoInput" 
                        style="position:absolute;bottom:5px;right:5px;width:36px;height:36px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:3px solid #fff;box-shadow:0 4px 10px rgba(0,0,0,.15);transition:all .2s;" 
                        onmouseover="this.style.transform='scale(1.1)'" 
                        onmouseout="this.style.transform='scale(1)'">

                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                        </svg>
                    </label>
                    <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/jpg, image/webp" style="display:none;" onchange="previewImage(this)">
                </div>
                <div style="text-align:center; margin-top:15px;">
                    <h4 style="margin:0; font-size:1.1rem; color:var(--dark);">
                        Foto Profil
                    </h4>
                    <p style="font-size:.8rem; color:var(--muted); margin-top:4px;">
                        Klik ikon pensil untuk mengubah foto
                    </p>
                    @error('foto')
                        <span style="color:#DC2626; font-size:.75rem; display:block; margin-top:8px;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px; margin-bottom:30px;">
                @if($user->nomor_induk)
                    <div style="grid-column:1 / -1; margin-bottom:0;">
                        <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">
                            Nomor Induk Siswa (NIS)
                        </label>
                        <input type="text" value="{{ $user->nomor_induk }}" 
                            readonly style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:1.1rem; font-weight:800; letter-spacing:2px; color:var(--primary); outline:none; background:#FDF0EA; text-align:center;">
                        <p style="font-size:.75rem; color:var(--muted); margin-top:6px; text-align:center;">
                            Nomor identitas ini digunakan untuk keperluan pendataan dan absensi.
                        </p>
                    </div>
                @endif
                <div style="margin-bottom:16px;">
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">
                        Nama Lengkap
                        <span style="color:#C65D2E">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    @error('name')
                        <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div>
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">
                        Alamat Email
                        <span style="color:#C65D2E">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    @error('email')
                        <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
                <div>
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890" 
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    @error('no_hp')
                        <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div>
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">
                        Alamat Lengkap
                    </label>
                    <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap..." 
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6; resize:vertical;">
                        {{ old('alamat', $user->alamat) }}
                    </textarea>
                    @error('alamat')
                        <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; padding-top:10px;">
                <button type="submit" style="background:var(--primary); color:#fff; font-family:var(--font-body); font-size:.95rem; font-weight:700; padding:14px 32px; border-radius:50px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(198,93,46,.3); transition:all .2s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(198,93,46,.4)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(198,93,46,.3)';">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>

        <div style="border-top:1px solid var(--border);"></div>

        <div style="padding:30px; background:#fff;">
            <div style="margin-bottom:24px;">
                <span style="display:inline-block; font-size:.7rem; font-weight:800; color:var(--primary); background:var(--primary-pale); padding:5px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px;">
                    Keamanan Akun
                </span>
                <h3 style="font-family:var(--font-display); font-size:1.4rem; font-weight:700; color:var(--dark); margin:0 0 5px 0;">
                    Ubah Kata Sandi
                </h3>
                <p style="font-size:.85rem; color:var(--muted); margin:0;">
                    Gunakan kata sandi yang aman dan mudah diingat.
                </p>
            </div>

            @if(session('password_success'))
                <div style="margin-bottom:20px; padding:14px 18px; border-radius:12px; background:#E8F5E9; border:1px solid #A5D6A7; color:#2E7D32; font-size:.85rem; font-weight:600;">
                    ✓ {{ session('password_success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('member.password.update') }}" id="passwordForm">
                @csrf
                <div style="margin-bottom:20px;">
                    <label style="font-size:.85rem;font-weight:700;color:var(--dark);display:block;margin-bottom:8px;">
                        Kata Sandi Saat Ini
                        <span style="color:#C65D2E">*</span>
                    </label>
                    <div style="position:relative;">
                        <input type="password"
                            name="current_password"
                            id="current_password"
                            autocomplete="current-password"
                            placeholder="Masukkan kata sandi saat ini"
                            style="width:100%; padding:12px 52px 12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6; transition:border-color .2s, box-shadow .2s;">
                        <button type="button" 
                            onclick="togglePassword('current_password', this)" 
                            style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted); display:flex; align-items:center; justify-content:center; padding:6px;">
                            <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    <div id="currentPasswordFeedback" style="display:none;font-size:.75rem;margin-top:6px;font-weight:600;"></div>
                    @error('current_password', 'password')
                        <span style="color:#DC2626;font-size:.75rem;margin-top:6px;display:block;">
                            {{ $message }}
                        </span>
                    @enderror
                </div>
                <div id="newPasswordSection" style="opacity:.55;transition:opacity .2s;">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:20px;">
                        <div>
                            <label style="font-size:.85rem;font-weight:700;color:var(--dark);display:block;margin-bottom:8px;">
                                Kata Sandi Baru
                                <span style="color:#C65D2E">*</span>
                            </label>
                            <div style="position:relative;">
                                <input type="password" 
                                    name="password" id="password" 
                                    autocomplete="new-password" 
                                    placeholder="Minimal 8 karakter" 
                                    disabled 
                                    style="width:100%;padding:12px 52px 12px 16px;border:1.5px solid var(--border);border-radius:12px;font-size:.9rem;outline:none;background:#FAF8F6;transition:border-color .2s;" >
                                <button
                                    type="button"
                                    onclick="togglePassword('password', this)"
                                    style="position:absolute; right:12px; top:50%;transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted); display:flex; align-items:center; justify-content:center; padding:6px;">
                                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                        <path d="M15 15l6 6"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                </button>
                            </div>

                            <div id="newPasswordFeedback" 
                                style="display:none; font-size:.75rem; margin-top:6px; font-weight:600;"></div>
                            @error('password', 'password')
                                <span style="color:#DC2626; font-size:.75rem; margin-top:6px; display:block;">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div>
                            <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">
                                Konfirmasi Kata Sandi Baru
                                <span style="color:#C65D2E">*</span>
                            </label>
                            <div style="position:relative;">
                                <input type="password" 
                                    name="password_confirmation" 
                                    id="password_confirmation" 
                                    autocomplete="new-password" 
                                    placeholder="Ketik ulang kata sandi baru" 
                                    disabled
                                    style="width:100%; padding:12px 52px 12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6; transition:border-color .2s;">
                                <button
                                    type="button"
                                    onclick="togglePassword('password_confirmation', this)"
                                    style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted); display:flex; align-items:center; justify-content:center; padding:6px;">
                                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="confirmationFeedback" style="display:none; font-size:.75rem;margin-top:6px;font-weight:600;"></div>
                            @error('password_confirmation', 'password')
                                <span style="color:#DC2626;font-size:.75rem;margin-top:6px;display:block;">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;margin-top:25px;">
                    <button type="submit" 
                        id="changePasswordButton" 
                        disabled
                        style="background:#BDBDBD;color:#fff;font-family:var(--font-body);font-size:.95rem;font-weight:700;padding:14px 32px;border-radius:50px;border:none;cursor:not-allowed;box-shadow:none;transition:all .2s;">
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- RIWAYAT TARIAN --}}
    <div style="margin-top:40px;background:#fff;border-radius:20px;border:1px solid var(--border);overflow:hidden;width:100%;">
        <div style="padding:24px 30px;border-bottom:1px solid var(--border);background:#FAF8F6;">
            <h3 style="font-family:var(--font-display);font-size:1.4rem;font-weight:700;color:var(--dark);margin:0;">
                Pengaturan Tarian
            </h3>
            <p style="font-size:.85rem;color:var(--muted);margin-top:4px;">
                Riwayat kelas dan tarian yang pernah Anda ikuti.
            </p>
        </div>
        <div style="padding:30px;">
            @if($riwayatTarian->isEmpty())
                <div style="text-align:center;padding:40px;background:var(--bg-soft);border-radius:16px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" style="margin-bottom:12px;">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <p style="font-weight:600;color:var(--dark);">Belum ada riwayat tarian</p>
                    <p style="font-size:.825rem;color:var(--muted);margin-top:4px;">
                        Daftarkan diri Anda pada kelas tari untuk mulai belajar.
                    </p>
                    <a href="{{ route('penjadwalan') }}" style="display:inline-block;margin-top:16px;color:var(--primary);font-weight:700;text-decoration:none;">
                        Pilih Kelas Tari →
                    </a>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:600px;">
                        <thead>
                            <tr style="text-align:left; border-bottom:2px solid var(--bg-soft);">
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">
                                    Nama Tarian
                                </th>
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">
                                    Jadwal
                                </th>
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">
                                    Tanggal Daftar
                                </th>
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; text-align:center;">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatTarian as $rt)
                                <tr style="border-bottom:1px solid var(--bg-soft); transition:background .2s;"
                                    onmouseover="this.style.background='#FAF8F6'"
                                    onmouseout="this.style.background='transparent'">
                                    <td style="padding:16px 15px;">
                                        <div style="display:flex; align-items:center; gap:12px;">
                                            <div style="width:40px; height:40px; background:var(--primary-pale); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="9" cy="7" r="4"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div style="font-weight:700; color:var(--dark); font-size:.9rem;">
                                                    {{ $rt->tarian->nama }}
                                                </div>
                                                <div style="font-size:.75rem; color:var(--muted);">
                                                    {{ ucfirst($rt->tarian->kategori) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding:16px 15px;">
                                        <div style="font-size:.85rem; color:var(--text);">
                                            {{ \Carbon\Carbon::parse($rt->tanggal_latihan)->locale('id')->translatedFormat('d F Y') }}
                                        </div>
                                        @php
                                            if ($rt->jam_latihan) {
                                                $jamLatihan = substr((string) $rt->jam_latihan, 0, 5);
                                            } elseif ($rt->jadwal) {
                                                $jamLatihan = substr((string) $rt->jadwal->jam_mulai, 0, 5);
                                            } else {
                                                $jamLatihan = '-';
                                            }
                                            $latihanRutin = str_contains(strtolower($rt->catatan ?? ''), 'latihan rutin');
                                        @endphp
                                        <div style="font-size:.75rem; color:var(--muted);">
                                            {{ $jamLatihan }}
                                            {{ $latihanRutin ? '(Latihan Rutin)' : '(Sesi Private)' }}
                                        </div>
                                    </td>
                                    <td style="padding:16px 15px;">
                                        <div style="font-size:.85rem; color:var(--text);">
                                            {{ $rt->tanggal_daftar->locale('id')->translatedFormat('d F Y') }}
                                        </div>
                                    </td>
                                    <td style="padding:16px 15px; text-align:center;">
                                        @if($rt->status === 'aktif')
                                            <span style="background:#E8F5E9; color:#2E7D32; font-size:.7rem; font-weight:800; padding:4px 12px; border-radius:20px; text-transform:uppercase;">
                                                Aktif
                                            </span>
                                        @else
                                            <span style="background:#F3F4F6; color:#6B7280; font-size:.7rem; font-weight:800; padding:4px 12px; border-radius:20px; text-transform:uppercase;">
                                                {{ $rt->status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>


{{-- JAVASCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ----- ELEMENT PASSWORD ----- */
    const currentPassword =
        document.getElementById('current_password');
    const password =
        document.getElementById('password');
    const passwordConfirmation =
        document.getElementById('password_confirmation');
    const newPasswordSection =
        document.getElementById('newPasswordSection');
    const changePasswordButton =
        document.getElementById('changePasswordButton');
    const currentPasswordFeedback =
        document.getElementById('currentPasswordFeedback');
    const newPasswordFeedback =
        document.getElementById('newPasswordFeedback');
    const confirmationFeedback =
        document.getElementById('confirmationFeedback');

    /* ----- STATUS PASSWORD ----- */
    let currentPasswordValid = false;

    /* ----- BORDER ----- */
    function setInputState(input, state) {
        if (!input) return;
        if (state === 'success') {
            input.style.borderColor = '#43A047';
            input.style.boxShadow ='0 0 0 3px rgba(67,160,71,.10)';
        } else if (state === 'error') {
            input.style.borderColor = '#DC2626';
            input.style.boxShadow ='0 0 0 3px rgba(220,38,38,.10)';
        } else {
            input.style.borderColor = 'var(--border)';
            input.style.boxShadow = 'none';
        }
    }

    /* ----- AKTIFKAN PASSWORD BARU ----- */
    function enableNewPassword() {
        password.disabled = false;
        passwordConfirmation.disabled = false;

        newPasswordSection.style.opacity = '1';
    }

    /* ----- NONAKTIFKAN PASSWORD BARU ----- */
    function disableNewPassword() {
        password.disabled = true;
        passwordConfirmation.disabled = true;
        password.value = '';
        passwordConfirmation.value = '';
        setInputState(password, 'normal');
        setInputState(passwordConfirmation, 'normal');
        newPasswordFeedback.style.display = 'none';
        confirmationFeedback.style.display = 'none';
        newPasswordSection.style.opacity = '.55';
        changePasswordButton.disabled = true;
        changePasswordButton.style.background = '#BDBDBD';
        changePasswordButton.style.cursor = 'not-allowed';
        changePasswordButton.style.boxShadow = 'none';
    }

    /* ----- CEK PASSWORD SAAT INI ----- */
    currentPassword.addEventListener('blur', async function () {
        const value = currentPassword.value.trim();
        currentPasswordValid = false;
        disableNewPassword();
        if (value === '') {
            currentPasswordFeedback.style.display = 'none';
            setInputState(currentPassword, 'normal');
            return;
        }
        currentPasswordFeedback.style.display = 'block';
        currentPasswordFeedback.style.color = '#6B7280';
        currentPasswordFeedback.textContent =
            'Memeriksa kata sandi...';
        setInputState(currentPassword, 'normal');
        try {
            const response = await fetch("{{ route('member.password.check') }}",
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content') || "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({current_password: value})
                }
            );
            const result = await response.json();
            if (result.valid) {
                currentPasswordValid = true;
                setInputState(currentPassword, 'success');
                currentPasswordFeedback.style.display = 'block';
                currentPasswordFeedback.style.color = '#2E7D32';
                currentPasswordFeedback.textContent =
                    '✓ Kata sandi benar';
                enableNewPassword();
            } else {
                currentPasswordValid = false;
                setInputState(currentPassword,'error');
                currentPasswordFeedback.style.display = 'block';
                currentPasswordFeedback.style.color ='#DC2626';
                currentPasswordFeedback.textContent =
                    '✕ Kata sandi salah';
                disableNewPassword();
            }
        } catch (error) {
            currentPasswordValid = false;
            setInputState(currentPassword,'error');
            currentPasswordFeedback.style.display = 'block';
            currentPasswordFeedback.style.color = '#DC2626';
            currentPasswordFeedback.textContent =
                '✕ Tidak dapat memeriksa kata sandi. Silakan coba lagi.';
            disableNewPassword();
            console.error(error);
        }
    });

    currentPassword.addEventListener('input', function () {
        currentPasswordValid = false;
        setInputState(currentPassword,'normal');
        currentPasswordFeedback.style.display = 'none';
        disableNewPassword();
    });


    /* ----- VALIDASI PASSWORD BARU ----- */
    password.addEventListener('input', function () {
        const value = password.value;
        if (value.length === 0) {
            newPasswordFeedback.style.display = 'none';
            setInputState(password,'normal');
            validatePasswordForm();
            return;
        }
        if (value.length < 8) {
            setInputState(password,'error');
            newPasswordFeedback.style.display = 'block';    
            newPasswordFeedback.style.color = '#DC2626';
            newPasswordFeedback.textContent = 
                '✕ Kata sandi minimal 8 karakter.';
        } else {
            setInputState(password,'success');
            newPasswordFeedback.style.display = 'block';
            newPasswordFeedback.style.color = '#2E7D32';
            newPasswordFeedback.textContent =
                '✓ Kata sandi memenuhi syarat.';
        }
        validateConfirmation();
        validatePasswordForm();
    });


    /* ----- VALIDASI KONFIRMASI ----- */
    passwordConfirmation.addEventListener('input',function () {
        validateConfirmation();
        validatePasswordForm();
    });
    function validateConfirmation() {
        const newPassword = password.value;
        const confirmation = passwordConfirmation.value;
        if (confirmation.length === 0) {
            confirmationFeedback.style.display ='none';
            setInputState(passwordConfirmation,'normal');
            return false;
        }
        if (
            newPassword.length >= 8 &&
            newPassword === confirmation
        ) {
            setInputState(passwordConfirmation,'success');
            confirmationFeedback.style.display ='block';
            confirmationFeedback.style.color = '#2E7D32';
            confirmationFeedback.textContent =
                '✓ Kata sandi cocok.';
            return true;
        } else {
            setInputState(passwordConfirmation,'error');
            confirmationFeedback.style.display ='block';
            confirmationFeedback.style.color = '#DC2626';
            confirmationFeedback.textContent =
                '✕ Kata sandi tidak cocok.';
            return false;
        }
    }

    function validatePasswordForm() {
        const validCurrent = currentPasswordValid;
        const validNew = password.value.length >= 8;
        const validConfirmation = password.value === passwordConfirmation.value && passwordConfirmation.value.length >= 8;
        const valid = validCurrent && validNew && validConfirmation;
        changePasswordButton.disabled =!valid;
        if (valid) {
            changePasswordButton.style.background ='var(--primary)';
            changePasswordButton.style.cursor ='pointer';
            changePasswordButton.style.boxShadow ='0 4px 12px rgba(198,93,46,.3)';
        } else {
            changePasswordButton.style.background ='#BDBDBD';
            changePasswordButton.style.cursor ='not-allowed';
            changePasswordButton.style.boxShadow ='none';
        }
    }


    document
        .getElementById('passwordForm')
        .addEventListener('submit', function (event) {
            if (!currentPasswordValid) {event.preventDefault();currentPassword.focus();
                return;
            }
            if (password.value.length < 8) {event.preventDefault();password.focus();
                return;
            }
            if (password.value !== passwordConfirmation.value) {
                event.preventDefault();passwordConfirmation.focus();
                return;
            }
        });

    window.togglePassword = function (inputId,btn) {
        const input = document.getElementById(inputId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {input.type = 'text';
            if (eyeOpen) {
                eyeOpen.style.display = 'none';}
            if (eyeClosed) {
                eyeClosed.style.display = 'block';}
        } else {input.type = 'password';
            if (eyeOpen) {
                eyeOpen.style.display = 'block';}
            if (eyeClosed) {
                eyeClosed.style.display = 'none';}
        }
    };

    /* ----- PREVIEW FOTO ----- */
    window.previewImage = function (input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const previewExisting = document.getElementById('fotoPreview');
                const previewNew = document.getElementById('fotoPreviewNew');
                const placeholder = document.getElementById('fotoPlaceholder');
                if (previewExisting) {
                    previewExisting.src = e.target.result;
                }
                if (previewNew) {
                    previewNew.src = e.target.result;
                    previewNew.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display ='none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
    disableNewPassword();
});
</script>
@endsection