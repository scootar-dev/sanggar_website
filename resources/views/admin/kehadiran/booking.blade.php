@extends('admin.layouts.app')
@section('title', 'Absensi Sesi Booking')
@section('content')
<h1>Absensi Sesi Booking</h1>
<form method="GET" style="margin:20px 0">
    <label>Tanggal <input type="date" name="tanggal" value="{{ $tanggal }}" required></label>
    <button class="btn btn-primary">Tampilkan</button>
</form>
@if($errors->any())<div role="alert">{{ $errors->first() }}</div>@endif
@if(session('success'))<p role="status">{{ session('success') }}</p>@endif
<form method="POST" action="{{ route('admin.kehadiran.booking.store') }}">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
    <table class="table" style="width:100%">
        <thead><tr><th>Anggota</th><th>Tarian</th><th>Jam</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($bookings as $booking)
            <tr><td>{{ $booking->user->name }}</td><td>{{ $booking->tarian->nama }}</td><td>{{ $booking->jam_latihan }}</td>
            <td><select name="kehadiran[{{ $booking->id }}]" required>
                <option value="">Pilih status</option>
                @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'alpa' => 'Alpa'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('kehadiran.'.$booking->id, $existing->get($booking->id)?->status) === $value)>{{ $label }}</option>
                @endforeach
            </select></td></tr>
        @empty
            <tr><td colspan="4">Tidak ada booking aktif pada tanggal ini.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($bookings->isNotEmpty())<button class="btn btn-primary" style="margin-top:20px">Simpan kehadiran</button>@endif
</form>
@endsection
