<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun admin.
     */
    public function index()
    {
        $user = Auth::user();
        return view('admin.account.index', compact('user'));
    }

    /**
     * Update data profil akun admin (nama, email, no_hp, alamat, foto).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'no_hp'  => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'foto'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required'  => 'Nama admin wajib diisi.',
            'email.required' => 'Email admin wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email ini sudah digunakan oleh akun lain.',
            'foto.image'     => 'File foto harus berupa gambar.',
            'foto.mimes'     => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max'       => 'Ukuran foto maksimal 2 MB.',
        ]);

        $data = [
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'no_hp'  => $validated['no_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            // Simpan foto baru
            $data['foto'] = $request->file('foto')->store('profil_admin', 'public');
        }

        User::where('id', $user->id)->update($data);

        return back()->with('success', 'Profil akun admin berhasil diperbarui!');
    }

    /**
     * Update kata sandi akun admin.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi tidak cocok.',
            'password.different'        => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        // Cek kecocokan password saat ini
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.'], 'passwordUpdate')->withInput();
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Kata sandi admin berhasil diperbarui!');
    }
}

