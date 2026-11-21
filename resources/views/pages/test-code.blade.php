@extends('layouts.app')
@section('title', 'Test Code Database — Sanggar Mulya Bhakti')
@section('content')

{{-- HERO SECTION --}}
<section class="page-hero" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: #fff; padding: 60px 0 50px 0; position: relative; overflow: hidden;">
    <div style="position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; pointer-events: none;"></div>
    <div style="position: absolute; bottom: -80px; left: -80px; width: 350px; height: 350px; background: rgba(255,255,255,0.03); border-radius: 50%; pointer-events: none;"></div>

    <div class="container" style="position: relative; z-index: 2;">

        </div>
    </div>
</section>

{{-- MAIN CONTENT --}}
<section style="padding: 50px 0; background-color: #f8fafc; min-height: 60vh;">
    <div class="container">

        {{-- ALERT SUCCESS --}}
        @if(session('success'))
            <div style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 16px 20px; border-radius: 12px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #10b981;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
            </div>
        @endif

        {{-- STAT CARDS --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 35px;">
            <div style="background: #ffffff; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px;">
                <div style="width: 54px; height: 54px; border-radius: 14px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 500;">Total Test Code</span>
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin: 4px 0 0 0;">{{ $testCodes->total() }}</h3>
                </div>
            </div>

            <div style="background: #ffffff; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px;">
                <div style="width: 54px; height: 54px; border-radius: 14px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 500;">Status Aktif</span>
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #15803d; margin: 4px 0 0 0;">{{ $totalAktif }}</h3>
                </div>
            </div>

            <div style="background: #ffffff; padding: 24px; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px;">
                <div style="width: 54px; height: 54px; border-radius: 14px; background: #fee2e2; color: #b91c1c; display: flex; align-items: center; justify-content: center;">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span style="font-size: 0.875rem; color: #64748b; font-weight: 500;">Status Nonaktif</span>
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #b91c1c; margin: 4px 0 0 0;">{{ $totalNonaktif }}</h3>
                </div>
            </div>
        </div>

        {{-- SEARCH & FILTER BAR --}}
        <div style="background: #ffffff; padding: 20px; border-radius: 16px; border: 1px solid #e2e8f0; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <form action="{{ route('test-code') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px; position: relative;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau keterangan..." style="width: 100%; padding: 12px 16px 12px 42px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 0.95rem; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#4338ca'" onblur="this.style.borderColor='#cbd5e1'">
                    <svg width="20" height="20" fill="none" stroke="#94a3b8" viewBox="0 0 24 24" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" style="background: #4338ca; color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('test-code') }}" style="background: #f1f5f9; color: #475569; text-decoration: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; display: flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- DATA TABLE CONTAINER --}}
        <div style="background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0;">
                    Daftar Data Test Code (Tabel <code style="font-size:0.9rem; color:#4338ca;">test_code</code>)
                </h3>
                <span style="font-size: 0.85rem; color: #64748b;">
                    Menampilkan {{ $testCodes->count() }} dari {{ $testCodes->total() }} data
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th style="padding: 16px 24px; width: 60px;">ID</th>
                            <th style="padding: 16px 24px;">Kode</th>
                            <th style="padding: 16px 24px;">Nama Test</th>
                            <th style="padding: 16px 24px;">Keterangan</th>
                            <th style="padding: 16px 24px;">Status</th>
                            <th style="padding: 16px 24px;">Waktu Dibuat</th>
                            <th style="padding: 16px 24px; text-align: center; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($testCodes as $item)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 16px 24px; font-weight: 600; color: #64748b;">#{{ $item->id }}</td>
                                <td style="padding: 16px 24px;">
                                    <span style="background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-weight: 700; font-size: 0.9rem; border: 1px solid #c7d2fe;">
                                        {{ $item->kode }}
                                    </span>
                                </td>
                                <td style="padding: 16px 24px; font-weight: 600; color: #1e293b;">
                                    {{ $item->nama }}
                                </td>
                                <td style="padding: 16px 24px; color: #64748b; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $item->keterangan ?? '-' }}
                                </td>
                                <td style="padding: 16px 24px;">
                                    @if($item->status === 'Aktif')
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #22c55e;"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #ef4444;"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 16px 24px; color: #64748b; font-size: 0.85rem;">
                                    {{ $item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </td>
                                <td style="padding: 16px 24px; text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 8px;">
                                        <button type="button" onclick="openEditModal({{ json_encode($item) }})" style="background: #e0f2fe; color: #0369a1; border: none; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Edit Data">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" onclick="openDeleteModal({{ $item->id }}, '{{ $item->nama }}')" style="background: #fee2e2; color: #b91c1c; border: none; width: 34px; height: 34px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Hapus Data">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 40px; text-align: center; color: #94a3b8;">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-bottom: 12px; color: #cbd5e1;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p style="font-weight: 600; font-size: 1.05rem; color: #475569; margin: 0 0 4px 0;">Belum Ada Data Test Code</p>
                                    <p style="font-size: 0.9rem; margin: 0;">Silakan klik tombol "Tambah Data Test Code" untuk menambahkan data baru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($testCodes->hasPages())
                <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    {{ $testCodes->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</section>

{{-- MODAL TAMBAH DATA --}}
<div id="createModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="background: #4338ca; color: white; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0;">Tambah Data Test Code</h3>
            <button type="button" onclick="closeCreateModal()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.5rem; line-height: 1;">&times;</button>
        </div>
        <form action="{{ route('test-code.store') }}" method="POST" style="padding: 24px;">
            @csrf
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Kode Test <span style="color: #ef4444;">*</span></label>
                <input type="text" name="kode" required placeholder="Contoh: TEST-101" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; text-transform: uppercase;">
            </div>
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama Test <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Uji Coba Fitur X" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Tambahkan deskripsi atau catatan pengujian..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; resize: vertical;"></textarea>
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Status <span style="color: #ef4444;">*</span></label>
                <select name="status" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: white;">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeCreateModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #4338ca; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT DATA --}}
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; width: 100%; max-width: 500px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="background: #0369a1; color: white; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0;">Edit Data Test Code</h3>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.5rem; line-height: 1;">&times;</button>
        </div>
        <form id="editForm" method="POST" style="padding: 24px;">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Kode Test <span style="color: #ef4444;">*</span></label>
                <input type="text" id="edit_kode" name="kode" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; text-transform: uppercase;">
            </div>
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama Test <span style="color: #ef4444;">*</span></label>
                <input type="text" id="edit_nama" name="nama" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
            </div>
            <div style="margin-bottom: 18px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Keterangan</label>
                <textarea id="edit_keterangan" name="keterangan" rows="3" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; resize: vertical;"></textarea>
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Status <span style="color: #ef4444;">*</span></label>
                <select id="edit_status" name="status" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; background: white;">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeEditModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #0369a1; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL HAPUS DATA --}}
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; width: 100%; max-width: 440px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="padding: 24px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #fee2e2; color: #b91c1c; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0 0 8px 0;">Konfirmasi Hapus</h3>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0 0 20px 0;">
                Apakah Anda yakin ingin menghapus data test code <strong id="delete_target_name" style="color: #1e293b;"></strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div style="display: flex; justify-content: center; gap: 12px;">
                    <button type="button" onclick="closeDeleteModal()" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Batal</button>
                    <button type="submit" style="background: #dc2626; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">Ya, Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<script>
function openCreateModal() {
    document.getElementById('createModal').style.display = 'flex';
}
function closeCreateModal() {
    document.getElementById('createModal').style.display = 'none';
}

function openEditModal(item) {
    document.getElementById('editForm').action = "/test-code/" + item.id;
    document.getElementById('edit_kode').value = item.kode;
    document.getElementById('edit_nama').value = item.nama;
    document.getElementById('edit_keterangan').value = item.keterangan || '';
    document.getElementById('edit_status').value = item.status;
    document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openDeleteModal(id, name) {
    document.getElementById('deleteForm').action = "/test-code/" + id;
    document.getElementById('delete_target_name').innerText = name;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modals when clicking outside contents
window.onclick = function(event) {
    const createM = document.getElementById('createModal');
    const editM = document.getElementById('editModal');
    const deleteM = document.getElementById('deleteModal');
    if (event.target === createM) closeCreateModal();
    if (event.target === editM) closeEditModal();
    if (event.target === deleteM) closeDeleteModal();
}
</script>

@endsection