@extends('layouts.member')
@section('title', 'Pengumuman dan Perkembangan')
@section('content')
<div style="padding:24px">
    <h1>Pengumuman dan Perkembangan</h1>
    <h2 style="margin-top:24px">Pengumuman sanggar</h2>
    @forelse($pengumuman as $item)
        <article style="padding:16px;margin:12px 0;background:#fff;border:1px solid #ddd;border-radius:12px">
            <h3>{{ $item->judul }}</h3><small>{{ $item->created_at->format('d/m/Y H:i') }}</small>
            <p style="white-space:pre-wrap">{{ $item->konten }}</p>
        </article>
    @empty<p>Belum ada pengumuman.</p>@endforelse
    {{ $pengumuman->links() }}
    <h2 style="margin-top:24px">Lencana kelulusan</h2>
    @forelse($lencana as $item)
        <span style="display:inline-block;padding:10px;margin:5px;background:#e8f5e9;border-radius:12px">Lulus {{ $item->tarian->nama ?? 'Tarian' }}</span>
    @empty<p>Belum ada lencana kelulusan.</p>@endforelse
    <h2 style="margin-top:24px">Rapor saya</h2>
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
</div>
@endsection
