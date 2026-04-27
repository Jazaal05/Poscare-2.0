<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LansiaPengaturanController extends Controller
{
    const POSYANDU_NAMA    = 'Posyandu Bagor Wetan';
    const POSYANDU_ALAMAT  = 'Desa Sukomoro, Kec. Bagor Wetan, Kab. Nganjuk';

    public function index()
    {
        return view('lansia.pengaturan.index', [
            'user'           => Auth::user(),
            'posyanduNama'   => self::POSYANDU_NAMA,
            'posyanduAlamat' => self::POSYANDU_ALAMAT,
        ]);
    }
}
