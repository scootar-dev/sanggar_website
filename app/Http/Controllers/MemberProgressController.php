<?php

namespace App\Http\Controllers;

use App\Models\{Pengumuman, RaporPagelaran};
use Illuminate\Http\Request;

class MemberProgressController extends Controller
{
    public function index(Request $request)
    {
        $rapor = RaporPagelaran::with(['event', 'tarian', 'pelatih'])->where('user_id', $request->user()->id)->latest()->get();
        $pengumuman = Pengumuman::latest()->paginate(10);
        $lencana = $rapor->where('lulus', true)->unique('tarian_id');
        return view('pages.member_progress', compact('rapor', 'pengumuman', 'lencana'));
    }
}
