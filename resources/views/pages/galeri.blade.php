@extends('layouts.app')
@section('title', 'Galeri - ' . ($profil->nama_sanggar ?? 'Sanggar Mulya Bhakti'))

@section('content')
{{-- Hero Section --}}
<section class="page-hero documentation-hero" style="padding-top: calc(var(--nav-h) + 40px); padding-bottom: 40px; background: var(--bg-soft);">
    <div class="container" style="text-align: center;">
        <span class="badge">Galeri</span>
        <h1 style="font-family: var(--font-display); font-size: clamp(2rem, 4vw, 3rem); font-weight: 900; color: var(--dark); margin-bottom: 12px;">Galeri Dokumentasi</h1>
        <p style="color: var(--muted); font-size: 1.1rem;">Dokumentasi kegiatan dan arsip digital Sanggar Seni</p>
    </div>
</section>

{{-- Galeri Content --}}
<section class="galeri-section" style="padding: 60px 0 80px;">
    <div class="container">
        @php
            $items = isset($grouped['dokumentasi'])
                ? $grouped['dokumentasi']->where('aktif', true)
                : collect();
        @endphp

        @if($items->isEmpty())
        <div style="text-align: center; padding: 80px 20px; background: var(--bg-soft); border-radius: var(--radius);">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1" style="margin-bottom: 16px;">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
            </svg>
            <h3 style="font-family: var(--font-display); font-size: 1.5rem; color: var(--dark); margin-bottom: 8px;">Belum ada dokumentasi</h3>
            <p style="color: var(--muted);">Segera hadir dokumentasi kegiatan menarik lainnya</p>
        </div>
        @else
        <div class="galeri-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
            @foreach($items as $item)
            <div class="galeri-item"
                 onclick="openLightbox('{{ $item->tipe === 'foto' ? asset('storage/' . $item->file) : '' }}', '{{ $item->tipe }}', '{{ addslashes($item->judul ?? 'Media') }}')"
                 style="border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-sm); background: var(--white); transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s; cursor: pointer; position: relative;"
                 onmouseenter="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 12px 40px rgba(0,0,0,0.15)'; this.querySelector('.item-overlay').style.opacity='1'"
                 onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='var(--shadow-sm)'; this.querySelector('.item-overlay').style.opacity='0'">
                @if($item->tipe === 'foto')
                    <div style="position: relative; overflow: hidden; aspect-ratio: 1/1;">
                        <img src="{{ asset('storage/' . $item->file) }}"
                             alt="{{ $item->judul ?? 'Galeri' }}"
                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s;"
                             loading="lazy"
                             onmouseenter="this.style.transform='scale(1.08)'"
                             onmouseleave="this.style.transform='scale(1)'">
                        <div class="item-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%); opacity: 0; transition: opacity 0.3s; display: flex; align-items: center; justify-content: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; color: white;">
                                <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
                                </div>
                                <span style="font-size: 0.85rem; font-weight: 600;">Lihat</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="width: 100%; aspect-ratio: 1/1; background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%); display: flex; align-items: center; justify-content: center; flex-direction: column; color: white; position: relative; overflow: hidden;">
                        <div style="position: absolute; inset: 0; opacity: 0.3;">
                            <svg width="100%" height="100%" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <polygon points="50,20 85,35 85,65 50,80 15,65 15,35" fill="none" stroke="white" stroke-width="1"/>
                            </svg>
                        </div>
                        <div style="width: 72px; height: 72px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 20px rgba(198,93,46,0.5); position: relative; z-index: 1;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="white" stroke="none">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                        </div>
                        <span style="margin-top: 16px; font-weight: 600; font-size: 1rem; position: relative; z-index: 1;">{{ $item->judul ?? 'Video' }}</span>
                    </div>
                @endif
                @if($item->judul)
                <div style="padding: 16px; background: var(--white);">
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text); font-weight: 600;">{{ $item->judul }}</p>
                    @if($item->deskripsi)
                    <p style="margin: 6px 0 0; font-size: 0.8rem; color: var(--muted); line-height: 1.5;">{{ Str::limit($item->deskripsi, 60) }}</p>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <p style="margin-top: 24px; font-size: 0.9rem; color: var(--muted); text-align: center;">
            Menampilkan {{ $items->count() }} foto dokumentasi
        </p>
        @endif
    </div>
</section>

{{-- Lightbox Modal --}}
<div id="lightbox" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 1000; opacity: 0; transition: opacity 0.3s; align-items: center; justify-content: center; padding: 20px;" onclick="closeLightbox(event)">
    <button onclick="closeLightbox(event)" style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.1); border: none; color: white; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s; font-size: 1.5rem;" onmouseenter="this.style.background='rgba(255,255,255,0.2)'" onmouseleave="this.style.background='rgba(255,255,255,0.1)'">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <div id="lightbox-content" style="max-width: 90vw; max-height: 85vh; display: flex; flex-direction: column; align-items: center; opacity: 0; transform: scale(0.9); transition: all 0.3s;" onclick="event.stopPropagation()">
        <img id="lightbox-img" src="" alt="" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 8px; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
        <p id="lightbox-caption" style="color: white; margin-top: 16px; font-size: 1.1rem; font-weight: 500; text-align: center; opacity: 0.9;"></p>
    </div>
    <div style="position: absolute; bottom: 20px; display: flex; gap: 12px;">
        <button onclick="navigateLightbox(-1)" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.2)'" onmouseeleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button onclick="navigateLightbox(1)" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 48px; height: 48px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseenter="this.style.background='rgba(255,255,255,0.2)'" onmouseeleave="this.style.background='rgba(255,255,255,0.1)'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
    </div>
</div>

<script>
    // Lightbox functionality
    let currentImages = [];
    let currentIndex = 0;

    function openLightbox(src, type, title) {
        if (type !== 'foto' || !src) return;

        const lightbox = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        const caption = document.getElementById('lightbox-caption');
        const content = document.getElementById('lightbox-content');

        // Collect all visible images for navigation
        currentImages = [];
        document.querySelectorAll('.galeri-item img').forEach(el => {
            if (el.src) currentImages.push(el.src);
        });
        currentIndex = currentImages.indexOf(src);

        img.src = src;
        caption.textContent = title;
        lightbox.style.display = 'flex';

        // Trigger animation
        requestAnimationFrame(() => {
            lightbox.style.opacity = '1';
            content.style.opacity = '1';
            content.style.transform = 'scale(1)';
        });

        // Prevent body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(event) {
        if (event && event.target !== event.currentTarget) return;

        const lightbox = document.getElementById('lightbox');
        const content = document.getElementById('lightbox-content');

        lightbox.style.opacity = '0';
        content.style.opacity = '0';
        content.style.transform = 'scale(0.9)';

        setTimeout(() => {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    function navigateLightbox(direction) {
        if (currentImages.length === 0) return;

        currentIndex = (currentIndex + direction + currentImages.length) % currentImages.length;
        const img = document.getElementById('lightbox-img');

        img.style.opacity = '0';
        img.style.transform = direction > 0 ? 'translateX(30px)' : 'translateX(-30px)';

        setTimeout(() => {
            img.src = currentImages[currentIndex];
            img.style.opacity = '1';
            img.style.transform = 'translateX(0)';
        }, 150);
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'flex') {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') navigateLightbox(-1);
            if (e.key === 'ArrowRight') navigateLightbox(1);
        }
    });
</script>

<style>
    @media (max-width: 767px) {
        .documentation-hero { padding-top: calc(var(--nav-h) + 24px) !important; padding-bottom: 24px !important; }
        .documentation-hero h1 { font-size: clamp(1.65rem, 6vw, 2rem) !important; line-height: 1.3; }
        .documentation-hero p { font-size: .85rem !important; }
        .galeri-section { padding: 28px 0 48px !important; }
        .galeri-section .galeri-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }
        .galeri-section .galeri-item { min-width: 0; border-radius: 14px !important; border: 1px solid var(--border); }
        .galeri-section .galeri-item:last-child:nth-child(odd) { grid-column: 1 / -1; width: calc((100% - 10px) / 2); justify-self: center; }
        .galeri-section .galeri-item > div:last-child:not(:first-child) { padding: 10px !important; overflow-wrap: anywhere; }
        .galeri-section .galeri-item > div:last-child:not(:first-child) > p:first-child { font-size: .8rem !important; line-height: 1.45; }
        .galeri-section .galeri-item > div:last-child:not(:first-child) > p + p { font-size: .72rem !important; }
    }
</style>
@endsection
