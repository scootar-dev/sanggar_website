@extends('layouts.member')
@section('title', 'Pengumuman dan Perkembangan')
@section('content')
<div class="member-progress">
    <header class="m-page-header"><span class="m-badge">Area Anggota</span><h1>Pengumuman dan Perkembangan</h1></header>
    <section class="progress-panel">
    <h2>Pengumuman sanggar</h2>
    @forelse($pengumuman as $item)
        <article style="padding:16px;margin:12px 0;background:#fff;border:1px solid #ddd;border-radius:12px">
            <h3>{{ $item->judul }}</h3><small>{{ $item->created_at->format('d/m/Y H:i') }}</small>
            <p style="white-space:pre-wrap">{{ $item->konten }}</p>
        </article>
    @empty<p>Belum ada pengumuman.</p>@endforelse
    {{ $pengumuman->links() }}
    </section>
    <section class="progress-panel">
    <h2>Lencana kelulusan</h2>
    @forelse($lencana as $item)
        <span style="display:inline-block;padding:10px;margin:5px;background:#e8f5e9;border-radius:12px">Lulus {{ $item->tarian->nama ?? 'Tarian' }}</span>
    @empty<p>Belum ada lencana kelulusan.</p>@endforelse
    </section>
    <section class="progress-panel">
    <h2>Rapor saya</h2>
    @if($rapor->isNotEmpty())<p>Total penilaian: {{ $rapor->count() }} · Rata-rata nilai akhir: {{ number_format($rapor->avg('nilai_akhir'), 1) }}</p>@endif
    @forelse($rapor as $item)
        <article style="padding:16px;margin:12px 0;background:#fff;border:1px solid #ddd;border-radius:12px">
            <h3>{{ $item->event->nama ?? 'Event' }} — {{ $item->tarian->nama ?? 'Tarian' }}</h3>
            <p>Nilai akhir: {{ $item->nilai_akhir }} · {{ $item->predikat }} · {{ $item->lulus ? 'Lulus' : 'Belum lulus' }}</p>
            <dl>
                @foreach(['teknik', 'hafalan', 'ekspresi', 'penampilan', 'kehadiran'] as $komponen)
                    <dt>{{ ucfirst($komponen) }}</dt><dd>{{ $item->{'nilai_'.$komponen} }}</dd>
                @endforeach
            </dl>
            <p style="white-space:pre-wrap">{{ $item->catatan }}</p>
            @if($item->lulus && $item->event?->kategori === 'midhang_sore')<a href="{{ route('ujian.sertifikat', $item->id) }}">Unduh sertifikat</a>@endif
        </article>
    @empty<p>Belum ada rapor.</p>@endforelse
    </section>
</div>
<style>
    .member-progress { min-width:0; }
    .member-progress .progress-panel { background:#fff; border:1px solid var(--border); border-radius:18px; padding:20px; margin-bottom:16px; box-shadow:0 4px 16px rgba(70,40,20,.03); overflow-wrap:anywhere; }
    .member-progress .progress-panel > h2 { font-family:var(--font-display); font-size:1.15rem; color:var(--dark); margin-bottom:14px; }
    .member-progress .progress-panel > p { font-size:.85rem; line-height:1.7; color:var(--muted); }
    .member-progress article h3 { font-size:.95rem; line-height:1.5; }
    .member-progress article small { display:block; margin:6px 0 10px; color:var(--muted); }
    .member-progress article p { font-size:.85rem; line-height:1.7; }
    .member-progress article dl { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:8px 16px; background:var(--bg); padding:14px; border-radius:12px; margin:12px 0; font-size:.85rem; }
    .member-progress article dd { font-weight:700; color:var(--primary); }
    .member-progress article a { display:inline-flex; align-items:center; min-height:44px; padding:10px 14px; margin-top:12px; background:var(--primary-pale); border-radius:10px; color:var(--primary); font-size:.8rem; font-weight:700; text-decoration:none; }
    @media (max-width:767px) {
        .member-progress .m-page-header h1 { font-size:clamp(1.35rem,5.5vw,1.75rem); line-height:1.3; }
        .member-progress .progress-panel { padding:14px; border-radius:16px; margin-bottom:12px; }
        .member-progress .progress-panel > h2 { font-size:1.05rem; }
        .member-progress article { padding:12px !important; }
    }
</style>
@endsection
