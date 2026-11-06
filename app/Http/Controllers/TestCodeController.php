<?php

namespace App\Http\Controllers;

use App\Models\TestCode;
use Illuminate\Http\Request;

class TestCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = TestCode::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $testCodes = $query->latest()->paginate(10);
        $totalAktif = TestCode::where('status', 'Aktif')->count();
        $totalNonaktif = TestCode::where('status', 'Nonaktif')->count();

        return view('pages.test-code', compact('testCodes', 'totalAktif', 'totalNonaktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required|string|max:255',
            'kode'       => 'required|string|max:100|unique:test_code,kode',
            'keterangan' => 'nullable|string',
            'status'     => 'required|in:Aktif,Nonaktif',
        ]);

        TestCode::create([
            'nama'       => $request->nama,
            'kode'       => strtoupper($request->kode),
            'keterangan' => $request->keterangan,
            'status'     => $request->status,
        ]);

        return redirect()->route('test-code')->with('success', 'Data Test Code berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $testCode = TestCode::findOrFail($id);

        $request->validate([
            'nama'       => 'required|string|max:255',
            'kode'       => 'required|string|max:100|unique:test_code,kode,' . $id,
            'keterangan' => 'nullable|string',
            'status'     => 'required|in:Aktif,Nonaktif',
        ]);

        $testCode->update([
            'nama'       => $request->nama,
            'kode'       => strtoupper($request->kode),
            'keterangan' => $request->keterangan,
            'status'     => $request->status,
        ]);

        return redirect()->route('test-code')->with('success', 'Data Test Code berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $testCode = TestCode::findOrFail($id);
        $testCode->delete();

        return redirect()->route('test-code')->with('success', 'Data Test Code berhasil dihapus!');
    }
}
