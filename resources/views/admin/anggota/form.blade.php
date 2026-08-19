@extends('admin.layouts.app')
@section('title', $mode==='create' ? 'Tambah Anggota' : 'Edit Anggota')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>{{ $mode==='create' ? 'Tambah Anggota Baru' : 'Edit: '.$anggota->name }}</h1>
    </div>
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<form method="POST"
      action="{{ $mode==='create' ? route('admin.anggota.store') : route('admin.anggota.update',$anggota->id) }}"
      enctype="multipart/form-data">
@csrf
@if($mode==='edit') @method('PUT') @endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">

    {{-- ═══════════════ KOLOM KIRI ═══════════════ --}}
    <div>
        {{-- Foto Profil --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span class="card-title">Foto Profil</span></div>
            <div class="card-body" style="display:flex;align-items:center;gap:20px">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;overflow:hidden;border:1px solid var(--border)">
                    @if($anggota->foto)
                        <img src="{{ asset('storage/'.$anggota->foto) }}" style="width:100%;height:100%;object-fit:cover" id="previewFoto">
                    @else
                        <div id="placeholderFoto" style="font-size:2rem;font-weight:900;color:var(--muted)">?</div>
                        <img src="" id="previewFoto" style="width:100%;height:100%;object-fit:cover;display:none">
                    @endif
                </div>
                <div style="flex:1">
                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <p style="font-size:.75rem;color:var(--muted);margin-top:4px">Rekomendasi: Persegi 1:1, Max 2MB</p>
                </div>
            </div>
        </div>

        {{-- Data Utama --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span class="card-title">Data Utama</span></div>
            <div class="card-body">
                @if($errors->any())
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;margin-bottom:20px;color:#DC2626;font-size:.875rem">
                    <ul style="padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                @if($mode === 'edit' && $anggota->nomor_induk)
                <div class="form-group" style="margin-bottom:16px">
                    <label>Nomor Induk Siswa (NIS)</label>
                    <input type="text" class="form-control" value="{{ $anggota->nomor_induk }}" readonly style="background: #F3F4F6; color: #111827; font-weight: 700; letter-spacing: 1px;">
                    <p style="font-size:.75rem;color:var(--muted);margin-top:4px">Di-generate otomatis oleh sistem (YYMMDDXXX).</p>
                </div>
                @endif

                <div class="form-group" style="margin-bottom:16px">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name',$anggota->name) }}" required>
                </div>
                <div class="form-group" style="margin-bottom:16px">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$anggota->email) }}" required>
                </div>
                <div class="form-group" style="margin-bottom:16px">
                    <label>No. HP / WhatsApp</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp',$anggota->no_hp) }}">
                </div>
                <div class="form-group" style="margin-bottom:16px">
                    <label>Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat',$anggota->alamat) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════ KOLOM KANAN ═══════════════ --}}
    <div>
        {{-- Keanggotaan --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span class="card-title">Keanggotaan</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                    <div class="form-group">
                        <label>Tipe Keanggotaan <span class="required">*</span></label>
                        <select name="tipe_anggota" id="tipe_anggota" class="form-control" onchange="togglePrivateFields()">
                            <option value="anggota_tetap" {{ old('tipe_anggota',$anggota->tipe_anggota ?? 'anggota_tetap')==='anggota_tetap' ? 'selected' : '' }}>🎭 Anggota Tetap</option>
                            <option value="pengunjung" {{ old('tipe_anggota',$anggota->tipe_anggota)==='pengunjung' ? 'selected' : '' }}>🎯 Anggota Sementara</option>
                        </select>
                        <p style="font-size:.75rem;color:var(--muted);margin-top:4px">
                            <strong>Anggota Tetap</strong>: Bergabung jangka panjang.<br>
                            <strong>Anggota Sementara</strong>: Belajar tari tertentu, aktif hingga tanggal tertentu.
                        </p>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif"    {{ old('status',$anggota->status)==='aktif'    ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status',$anggota->status)==='nonaktif' ? 'selected' : '' }}>Non-aktif</option>
                        </select>
                    </div>
                </div>

                {{-- Field khusus sementara --}}
                <div id="private-fields" style="display:{{ old('tipe_anggota',$anggota->tipe_anggota ?? '')==='pengunjung' ? 'block' : 'none' }}">
                    <div style="background:#FDF8F5;border-radius:12px;border:1px solid #F5EAE2;padding:16px;margin-bottom:16px">
                        <p style="font-size:.8rem;font-weight:700;color:#C65D2E;margin-bottom:12px">📋 Info Anggota Sementara</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                            <div class="form-group">
                                <label>Berlaku Hingga</label>
                                <input type="date" name="tgl_kadaluarsa" class="form-control" value="{{ old('tgl_kadaluarsa',$anggota->tgl_kadaluarsa ? $anggota->tgl_kadaluarsa->format('Y-m-d') : '') }}">
                            </div>
                            <div class="form-group">
                                <label>Catatan</label>
                                <input type="text" name="catatan_keanggotaan" class="form-control" value="{{ old('catatan_keanggotaan',$anggota->catatan_keanggotaan) }}" placeholder="Mis: Belajar Tari Topeng 10 pertemuan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-header"><span class="card-title">Keamanan</span></div>
            <div class="card-body">
                <div class="form-group" style="margin-bottom:16px">
                    <label>Password {{ $mode==='edit' ? '(kosongkan jika tidak diubah)' : '' }} <span class="required">{{ $mode==='create' ? '*' : '' }}</span></label>
                    <input type="password" name="password" class="form-control" {{ $mode==='create' ? 'required' : '' }} placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password {{ $mode==='edit' ? '(kosongkan jika tidak diubah)' : '' }} <span class="required">{{ $mode==='create' ? '*' : '' }}</span></label>
                    <input type="password" name="password_confirmation" class="form-control" {{ $mode==='create' ? 'required' : '' }} placeholder="Ulangi password">
                </div>
            </div>
        </div>

        {{-- Tarian yang Diikuti (hanya tampil saat edit) --}}
        @if($mode === 'edit' && isset($pendaftaranTari))
        <div class="card" style="margin-bottom:20px">
            <div class="card-header">
                <span class="card-title">🎭 Tarian yang Diikuti</span>
                @if($pendaftaranTari->count() > 0)
                    <span style="background:#ECFDF5;color:#059669;font-size:.7rem;font-weight:800;padding:4px 10px;border-radius:100px;margin-left:8px">{{ $pendaftaranTari->count() }} aktif</span>
                @endif
            </div>
            <div class="card-body">
                @if($pendaftaranTari->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:12px">
                        @foreach($pendaftaranTari as $pt)
                        <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#FAFAFA;border-radius:12px;border:1px solid #F0F0F0;transition:all 0.2s ease;" onmouseover="this.style.borderColor='#C65D2E';this.style.background='#FFFBF8'" onmouseout="this.style.borderColor='#F0F0F0';this.style.background='#FAFAFA'">
                            @if($pt->tarian && $pt->tarian->foto)
                                <img src="{{ asset('storage/'.$pt->tarian->foto) }}" style="width:44px;height:44px;border-radius:10px;object-fit:cover;flex-shrink:0">
                            @else
                                <div style="width:44px;height:44px;border-radius:10px;background:#FFF7ED;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <span style="font-size:1.2rem">💃</span>
                                </div>
                            @endif
                            <div style="flex:1;min-width:0">
                                <div style="font-weight:800;font-size:.9rem;color:#111827;margin-bottom:2px">{{ $pt->tarian->nama ?? 'Tarian tidak ditemukan' }}</div>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                    @if($pt->tarian && $pt->tarian->kategori)
                                        <span style="font-size:.7rem;font-weight:700;color:#C65D2E;background:#FFF7ED;padding:2px 8px;border-radius:6px">{{ $pt->tarian->kategori }}</span>
                                    @endif
                                    @if($pt->tanggal_latihan)
                                        <span style="font-size:.7rem;color:#6B7280;font-weight:600">📅 {{ \Carbon\Carbon::parse($pt->tanggal_latihan)->format('d M Y') }}</span>
                                    @endif
                                    @if($pt->tanggal_daftar)
                                        <span style="font-size:.7rem;color:#9CA3AF;font-weight:600">Daftar: {{ $pt->tanggal_daftar->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            <span style="font-size:.7rem;font-weight:800;color:#059669;background:#ECFDF5;padding:4px 10px;border-radius:100px;flex-shrink:0">Aktif</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center;padding:28px 20px;color:#9CA3AF">
                        <div style="font-size:2.5rem;margin-bottom:10px;opacity:0.5">🎭</div>
                        <p style="font-weight:700;font-size:.9rem;margin-bottom:4px;color:#6B7280">Belum ada tarian</p>
                        <p style="font-size:.8rem">Anggota ini belum mendaftar kelas tarian apapun.</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Tombol Aksi --}}
<div style="margin-top:20px;display:flex;gap:12px">
    <button type="submit" class="btn btn-primary">💾 {{ $mode==='create' ? 'Tambah Anggota' : 'Simpan Perubahan' }}</button>
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Batal</a>
</div>
</form>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewFoto');
            const placeholder = document.getElementById('placeholderFoto');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePrivateFields() {
    const tipe = document.getElementById('tipe_anggota').value;
    const privateFields = document.getElementById('private-fields');
    privateFields.style.display = (tipe === 'pengunjung') ? 'block' : 'none';
}
</script>

<style>
/* Responsif: stack ke 1 kolom di layar kecil */
@media (max-width: 900px) {
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

@endsection