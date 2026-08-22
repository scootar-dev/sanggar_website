<?php
namespace App\Http\Controllers;
use App\Models\{
    PendaftaranTari,
    Kehadiran,
    Event,
    Tarian,
    User,
    UjianPendaftaran,
    RaporPagelaran
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD 
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {return redirect()->route('admin.dashboard');}
        if ($user->tipe_anggota === 'pengunjung') {return $this->guestDashboard($user);}

        /* ----- JADWAL AKTIF SAYA ----- */
        $jadwalAktif = PendaftaranTari::with(['tarian', 'jadwal'])
            ->where('user_id', $user->id)
            ->where('status', 'aktif')
            ->get();

        /* ----- KEHADIRAN BULAN INI ----- */
        $bulanIni = now()->format('Y-m');
        $kehadiranBulanIni = Kehadiran::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulanIni])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $totalLatihan = array_sum($kehadiranBulanIni);
        $hadir = (int) ($kehadiranBulanIni['hadir'] ?? 0);
        $izin  = (int) ($kehadiranBulanIni['izin'] ?? 0);
        $alpa  = (int) ($kehadiranBulanIni['alpa'] ?? 0);
        $persenHadir = $totalLatihan > 0
            ? round(($hadir / $totalLatihan) * 100)
            : 0;

        /* ----- EVENT MENDATANG ----- */
        $eventMendatang = Event::where('status', 'akan_datang')
            ->orderBy('tanggal')
            ->limit(3)
            ->get();

        /* ----- REKOMENDASI TARIAN ----- */
        $tarianRekomendasi = Tarian::where('aktif', true)
            ->whereNotIn('id',$jadwalAktif->pluck('tarian_id'))
            ->orderBy('urutan')
            ->limit(4)
            ->get();

        /* ----- ABSENSI TERAKHIR ----- */
        $absensiTerakhir = Kehadiran::with(['jadwal', 'tarian'])
            ->where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->limit(5)
            ->get();

        /* ----- TOTAL KEHADIRAN SEPANJANG WAKTU ----- */
        $totalKehadiranAll = Kehadiran::where('user_id', $user->id)->count();
        $totalHadirAll = Kehadiran::where('user_id', $user->id)
            ->where('status', 'hadir')
            ->count();

        /* ----- UJIAN MIDHANG SORE ----- */
        $ujianSaya = UjianPendaftaran::with(['event', 'tarian'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item) use ($user) {
                $item->rapor = RaporPagelaran::where(['event_id'  => $item->event_id, 'user_id'   => $user->id, 'tarian_id' => $item->tarian_id,])->first();return $item;});

        return view('pages.dashboard', compact('user','jadwalAktif','kehadiranBulanIni','totalLatihan','hadir','izin','alpa','persenHadir','eventMendatang','tarianRekomendasi','absensiTerakhir','totalKehadiranAll','totalHadirAll','ujianSaya'));
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD ANGGOTA SEMENTARA
    |--------------------------------------------------------------------------
    */
    private function guestDashboard($user)
    {
        // Semua sesi pendaftaran
        $sesiBooking = PendaftaranTari::with('tarian')
            ->where('user_id', $user->id)
            ->orderBy('tanggal_latihan', 'asc')
            ->get();

        // Statistik
        $totalSesiBooking = $sesiBooking->count();
        $totalHadir = Kehadiran::where('user_id', $user->id)
            ->where('status', 'hadir')
            ->count();
        $persenHadir = $totalSesiBooking > 0
            ? round(($totalHadir / $totalSesiBooking) * 100)
            : 0;

        // Event mendatang
        $eventMendatang = Event::where('status', 'akan_datang')
            ->orderBy('tanggal')
            ->limit(3)
            ->get();

        // Tarian aktif
        $tarianList = Tarian::where('aktif', true)
            ->orderBy('urutan')
            ->get();

        return view('pages.guest_dashboard', compact('user','sesiBooking','totalSesiBooking','totalHadir','persenHadir','eventMendatang','tarianList'));
    }

    /* ----- TAMBAH SESI BARU - ANGGOTA SEMENTARA ----- */
    public function storeSesi(Request $request)
    {
        $user = Auth::user();
        if ($user->tipe_anggota !== 'pengunjung') {return redirect()->route('dashboard');}
        $request->validate(['tarian_id' => 'required|exists:tarian,id',
            'tanggal'   => 'required|date|after_or_equal:today',
            'jam'       => 'required|string'], 
        [
            'tarian_id.required' => 'Harap pilih tarian.',
            'tanggal.required'   => 'Harap pilih tanggal.',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
            'jam.required'       => 'Harap pilih jam latihan.']);

        $requestedTarianId = $request->tarian_id;

        /* ----- DAFTAR PENDAFTARAN PADA JAM YANG SAMA ----- */
        $existingPendaftaran = PendaftaranTari::with('tarian')
            ->where('tanggal_latihan', $request->tanggal)
            ->where('jam_latihan', $request->jam)
            ->whereIn('status', ['aktif', 'pending'])
            ->get();

        /* ----- MAKSIMAL 2 JENIS TARIAN DALAM SATU JAM ----- */
        $uniqueTarianIds = $existingPendaftaran
            ->pluck('tarian_id')
            ->unique()
            ->values()
            ->toArray();
        if (!in_array($requestedTarianId, $uniqueTarianIds)) {
            if (count($uniqueTarianIds) >= 2) {
                $namaTarian = $existingPendaftaran
                    ->pluck('tarian.nama')
                    ->unique()
                    ->implode(' dan ');
                return back()->with('error',
                    "Maaf, seluruh tempat latihan pada {$request->tanggal} jam {$request->jam} sudah penuh oleh kelas {$namaTarian}. Silakan pilih jam lain, atau pilih salah satu tarian tersebut jika ingin bergabung.");}
        }

        /* ----- MAKSIMAL 5 ORANG DALAM SATU KELOMPOK ----- */
        $countOrangDiTarian = $existingPendaftaran
            ->where('tarian_id', $requestedTarianId)
            ->count();
        if ($countOrangDiTarian >= 5) {
            return back()->with('error',
                "Maaf, kelompok tari yang Anda pilih pada {$request->tanggal} jam {$request->jam} sudah mencapai batas maksimal (5 orang). Silakan pilih jam lain.");
        }

        /* ----- UPDATE TANGGAL KADALUARSA ----- */
        $tglBaru = \Carbon\Carbon::parse($request->tanggal)
            ->addDays(3)
            ->toDateString();
        if (is_null($user->tgl_kadaluarsa) || $tglBaru > $user->tgl_kadaluarsa) {
            User::where('id', $user->id)
                ->update(['tgl_kadaluarsa' => $tglBaru]);
        }

        /* ----- SIMPAN PENDAFTARAN ----- */
        PendaftaranTari::create([
            'user_id'         => $user->id,
            'tarian_id'       => $request->tarian_id,
            'tanggal_latihan' => $request->tanggal,
            'jam_latihan'     => $request->jam,
            'status'          => 'pending',
            'tanggal_daftar'  => now(),
            'catatan'         => null]);
        return back()->with('success',
            'Sesi latihan baru berhasil diajukan! Menunggu konfirmasi admin.');
    }

    /* ----- HALAMAN PROFIL ----- */
    public function editProfile()
    {
        $user = Auth::user();
        // Admin diarahkan ke dashboard admin
        if ($user->role === 'admin') { return redirect()->route('admin.dashboard'); }
        // Anggota sementara tidak dapat mengedit profil
        if ($user->tipe_anggota === 'pengunjung') {return redirect()
                ->route('dashboard')
                ->with('error','Anggota sementara tidak memerlukan pengaturan profil.');
        }

        /* ----- RIWAYAT TARIAN ----- */
        $riwayatTarian = PendaftaranTari::with(['tarian','jadwal'])->where('user_id', $user->id)->orderByDesc('tanggal_daftar')->get();
        $riwayatTarian = PendaftaranTari::with(['tarian','jadwal'])
            ->where('user_id', $user->id)
            ->orderByDesc('tanggal_daftar')
            ->get();
        return view('pages.member_profile', compact('user','riwayatTarian'));
    }

    /* ----- UPDATE PROFIL ------ */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'no_hp' => ['nullable','string','max:20'],
            'alamat' => ['nullable','string','max:500'],
            'foto' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048']],
            [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        /* ------ DATA PROFIL ------ */
        $data = [
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'no_hp'  => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ];

        /* ------ UPLOAD FOTO ------ */
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {Storage::disk('public')->delete($user->foto);}
            // Simpan foto baru
            $data['foto'] = $request
                ->file('foto')
                ->store('profil_anggota', 'public');
        }

        /* ------ UPDATE DATABASE ------ */
        User::where('id', $user->id)->update($data);
        return back()->with('success','Profil berhasil diperbarui!');
    }

    /* ------ CEK PASSWORD SAAT INI ------ */
    public function checkCurrentPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required','string']]);
        $user = Auth::user();
        $isValid = Hash::check($request->current_password,$user->password);
        return response()->json([
            'valid' => $isValid,
            'message' => $isValid
                ? 'Kata sandi benar'
                : 'Kata sandi salah']);
    }

    /* ------ UPDATE PASSWORD ------ */

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'current_password' => ['required','string'],
            'password' => ['required','string','min:8','confirmed','different:current_password']],
            [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        /* ------ CEK PASSWORD LAMA ------ */
        if (!Hash::check($validated['current_password'],$user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi salah'], 'password')->withInput();
        }

        /* ------ SIMPAN PASSWORD BARU ------ */
        User::where('id', $user->id)->update(['password' => Hash::make($validated['password'])]);   
        return back()->with('password_success','Kata sandi berhasil diubah!');
    }
}