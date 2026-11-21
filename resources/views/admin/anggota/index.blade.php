@extends('admin.layouts.app')
@section('title','Kelola Anggota')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Kelola Anggota</h1>
        <p>Daftar seluruh anggota tetap dan anggota sementara Sanggar Mulya Bhakti.</p>
    </div>
    <div class="page-header-actions">
        {{-- Export PDF --}}
        <div style="position:relative;display:inline-block" id="export-wrap">
            <button onclick="toggleExport()" style="background:#F3F4F6;color:#3D3D3D;border:1px solid #E8E0D8;font-weight:700;padding:10px 18px;border-radius:50px;cursor:pointer;font-size:.875rem;display:flex;align-items:center;gap:6px">
                📥 Export ▾
            </button>
            <div id="export-dropdown" style="display:none;position:absolute;right:0;top:calc(100% + 6px);background:#fff;border:1px solid #E8E0D8;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.1);min-width:210px;z-index:100;overflow:hidden">
                <a href="{{ route('admin.anggota.pdf') }}" target="_blank" style="display:block;padding:10px 16px;font-size:.875rem;font-weight:600;color:#1A1A1A;text-decoration:none;border-bottom:1px solid #F5F3F1">🖨 PDF Semua Anggota</a>
                <a href="{{ route('admin.anggota.pdf', ['tipe'=>'anggota_tetap']) }}" target="_blank" style="display:block;padding:10px 16px;font-size:.875rem;font-weight:600;color:#1A1A1A;text-decoration:none;border-bottom:1px solid #F5F3F1">🖨 PDF Anggota Tetap</a>
                <a href="{{ route('admin.anggota.pdf', ['tipe'=>'pengunjung']) }}" target="_blank" style="display:block;padding:10px 16px;font-size:.875rem;font-weight:600;color:#1A1A1A;text-decoration:none;border-bottom:1px solid #F5F3F1">🖨 PDF Anggota Sementara</a>
                <a href="{{ route('admin.anggota.excel', ['tipe'=>'semua']) }}" style="display:block;padding:10px 16px;font-size:.875rem;font-weight:600;color:#1A1A1A;text-decoration:none;border-bottom:1px solid #F5F3F1">📊 Excel Semua</a>
                <a href="{{ route('admin.anggota.excel', ['tipe'=>'anggota_tetap']) }}" style="display:block;padding:10px 16px;font-size:.875rem;font-weight:600;color:#1A1A1A;text-decoration:none;border-bottom:1px solid #F5F3F1">📊 Excel Anggota Tetap</a>
                <a href="{{ route('admin.anggota.excel', ['tipe'=>'pengunjung']) }}" style="display:block;padding:10px 16px;font-size:.875rem;font-weight:600;color:#1A1A1A;text-decoration:none">📊 Excel Anggota Sementara</a>
            </div>
        </div>
        <a href="{{ route('admin.anggota.create') }}" class="btn btn-primary">+ Tambah Anggota</a>
    </div>
</div>

{{-- FILTER --}}
<form method="GET" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center">
    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}" style="max-width:240px">
    <select name="tipe" class="form-control" style="max-width:200px">
        <option value="">Semua Tipe</option>
        <option value="anggota_tetap" {{ request('tipe')==='anggota_tetap' ? 'selected':'' }}>🎭 Anggota Tetap</option>
        <option value="pengunjung"    {{ request('tipe')==='pengunjung'    ? 'selected':'' }}>🎯 Anggota Sementara</option>
    </select>
    <select name="status" class="form-control" style="max-width:150px">
        <option value="">Semua Status</option>
        <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
        <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Non-aktif</option>
    </select>
    <button type="submit" class="btn btn-secondary">Filter</button>
    @if(request()->anyFilled(['search','status','tipe']))
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="card">
    <div class="card-header">
        <span class="card-title">Total seluruh anggota: {{ $totalAnggota }}</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>NIS</th><th>Nama</th><th>Email</th><th>No. HP</th><th>Tipe</th><th>Terdaftar</th><th>Berlaku Hingga</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($anggota as $a)
                <tr>
                    <td style="font-size:.875rem; font-weight:600; color:#2E7D32;">{{ $a->nomor_induk ?? '-' }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($a->foto)
                                <img src="{{ preg_match('~^https?://~i', $a->foto) ? $a->foto : asset('storage/'.$a->foto) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">
                            @else
                                <div class="user-avatar" style="width:34px;height:34px;font-size:.8rem;flex-shrink:0">{{ strtoupper(substr($a->name,0,1)) }}</div>
                            @endif
                            <div>
                                <span style="font-weight:600">{{ $a->name }}</span>
                                @if($a->catatan_keanggotaan)
                                <div style="font-size:.75rem;color:#7A7A7A">{{ Str::limit($a->catatan_keanggotaan, 30) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.875rem">{{ $a->email }}
                        <div style="font-size:.75rem;color:var(--muted)">{{ $a->email_verified_at ? 'Email terverifikasi' : 'Email belum terverifikasi' }}</div>
                    </td>
                    <td style="font-size:.875rem">{{ $a->no_hp ?? '-' }}</td>
                    <td>
                        <span class="chip {{ $a->tipe_anggota_color }}">
                            {{ $a->tipe_anggota_label }}
                        </span>
                    </td>
                    <td style="font-size:.8rem;white-space:nowrap">{{ $a->created_at->format('d M Y') }}</td>
                    <td style="font-size:.8rem;white-space:nowrap;color:{{ $a->tgl_kadaluarsa && $a->tgl_kadaluarsa->copy()->endOfDay()->isPast() ? '#DC2626' : '#7A7A7A' }}">
                        {{ $a->tgl_kadaluarsa ? $a->tgl_kadaluarsa->format('d M Y') : '-' }}
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.anggota.toggle',$a->id) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="chip {{ $a->status==='aktif' ? 'chip--green' : 'chip--gray' }}"
                                style="border:none;cursor:pointer"
                                title="Klik untuk toggle status"
                                data-confirm="{{ $a->status === 'aktif' ? 'Nonaktifkan akun '.$a->name.'?' : '' }}"
                                onclick="return !this.dataset.confirm || confirm(this.dataset.confirm)">
                                {{ $a->status==='aktif' ? '● Aktif' : '○ Non-aktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="td-actions">
                        <a href="{{ route('admin.anggota.edit',$a->id) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.anggota.destroy',$a->id) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus anggota {{ $a->name }}? Tindakan ini tidak dapat dibatalkan." onclick="return confirm(this.dataset.confirm)">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9"><div class="empty-state"><h3>Tidak ada anggota ditemukan</h3><p>Coba ubah filter pencarian.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:0 16px">{{ $anggota->links() }}</div>
</div>

<script>
function toggleExport() {
    const d = document.getElementById('export-dropdown');
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('export-wrap');
    if (!wrap.contains(e.target)) {
        document.getElementById('export-dropdown').style.display = 'none';
    }
});
</script>
@endsection
