@extends('admin.layouts.app')
@section('title', 'Pengaturan Akun Admin')
@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:24px;padding:14px 20px;background:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;color:#15803D;display:flex;gap:10px;align-items:center;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="page-header" style="margin-bottom:24px;">
    <div class="page-header-text">
        <h1 style="font-size:1.5rem;font-weight:700;color:var(--dark);">Pengaturan Akun Admin</h1>
        <p style="color:var(--muted);font-size:0.9rem;">Kelola kredensial login, identitas profil, dan keamanan akun administrator.</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(340px, 1fr));gap:24px;align-items:start;">

    {{-- ── KARTU 1: PROFIL ADMIN ── --}}
    <div class="card" style="background:#fff;border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;">
        <div class="card-header" style="padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--p)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span class="card-title" style="font-weight:700;font-size:1.05rem;color:var(--dark);">Data Profil Administrator</span>
        </div>
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ route('admin.account.profile') }}" enctype="multipart/form-data">
                @csrf

                {{-- AVATAR PREVIEW & UPLOAD --}}
                <div style="display:flex;align-items:center;gap:20px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px dashed var(--border);">
                    <div style="position:relative;">
                        @if($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="Avatar" id="previewFoto" style="width:76px;height:76px;border-radius:50%;object-fit:cover;border:2px solid var(--p);">
                        @else
                            <div id="previewAvatarDefault" style="width:76px;height:76px;border-radius:50%;background:var(--p-pale);color:var(--p);font-weight:800;font-size:1.6rem;display:flex;align-items:center;justify-content:center;border:2px solid var(--p);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img id="previewFoto" style="width:76px;height:76px;border-radius:50%;object-fit:cover;border:2px solid var(--p);display:none;">
                        @endif
                    </div>
                    <div>
                        <label style="display:inline-block;padding:8px 16px;background:#F3F4F6;border:1px solid var(--border);border-radius:8px;font-size:0.85rem;font-weight:600;color:var(--dark);cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                            <span>Ganti Foto Avatar</span>
                            <input type="file" name="foto" accept="image/*" style="display:none;" onchange="previewImage(this)">
                        </label>
                        <div style="font-size:0.75rem;color:var(--muted);margin-top:6px;">Format JPG, PNG, WEBP (Maks 2MB)</div>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">Nama Lengkap <span style="color:var(--error);">*</span></label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">
                    @error('name')
                        <span style="color:var(--error);font-size:0.78rem;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">Email Login <span style="color:var(--error);">*</span></label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">
                    <small style="color:var(--muted);font-size:0.78rem;">Email ini digunakan untuk masuk/login ke halaman Admin Panel.</small>
                    @error('email')
                        <span style="color:var(--error);font-size:0.78rem;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">No. WhatsApp / HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">
                    @error('no_hp')
                        <span style="color:var(--error);font-size:0.78rem;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom:24px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat')
                        <span style="color:var(--error);font-size:0.78rem;margin-top:4px;display:block;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:0.875rem;font-weight:600;border:none;cursor:pointer;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Simpan Perubahan Profil
                </button>
            </form>
        </div>
    </div>

    {{-- ── KARTU 2: GANTI KATA SANDI ── --}}
    <div class="card" style="background:#fff;border-radius:16px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;">
        <div class="card-header" style="padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--p)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span class="card-title" style="font-weight:700;font-size:1.05rem;color:var(--dark);">Ganti Kata Sandi</span>
        </div>
        <div class="card-body" style="padding:24px;">
            <form method="POST" action="{{ route('admin.account.password') }}">
                @csrf

                <div class="form-group" style="margin-bottom:18px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">Kata Sandi Saat Ini <span style="color:var(--error);">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="current_password" id="inputCurrentPass" class="form-control" required style="width:100%;padding:10px 42px 10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">
                        <button type="button" onclick="togglePass('inputCurrentPass')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;">
                            👁️
                        </button>
                    </div>
                    @if($errors->passwordUpdate->has('current_password'))
                        <span style="color:var(--error);font-size:0.78rem;margin-top:4px;display:block;">{{ $errors->passwordUpdate->first('current_password') }}</span>
                    @endif
                </div>

                <div class="form-group" style="margin-bottom:18px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">Kata Sandi Baru <span style="color:var(--error);">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="inputNewPass" class="form-control" required style="width:100%;padding:10px 42px 10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">
                        <button type="button" onclick="togglePass('inputNewPass')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;">
                            👁️
                        </button>
                    </div>
                    <small style="color:var(--muted);font-size:0.78rem;">Minimal 6 karakter.</small>
                    @if($errors->passwordUpdate->has('password'))
                        <span style="color:var(--error);font-size:0.78rem;margin-top:4px;display:block;">{{ $errors->passwordUpdate->first('password') }}</span>
                    @endif
                </div>

                <div class="form-group" style="margin-bottom:24px;">
                    <label style="display:block;font-size:0.875rem;font-weight:600;color:var(--dark);margin-bottom:6px;">Konfirmasi Kata Sandi Baru <span style="color:var(--error);">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="inputConfirmPass" class="form-control" required style="width:100%;padding:10px 42px 10px 14px;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;">
                        <button type="button" onclick="togglePass('inputConfirmPass')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:8px;font-size:0.875rem;font-weight:600;border:none;cursor:pointer;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Perbarui Kata Sandi
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = document.getElementById('previewFoto');
            var defaultAvatar = document.getElementById('previewAvatarDefault');
            img.src = e.target.result;
            img.style.display = 'block';
            if (defaultAvatar) {
                defaultAvatar.style.display = 'none';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePass(id) {
    var input = document.getElementById(id);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endpush

@endsection

