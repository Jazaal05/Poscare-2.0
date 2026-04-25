<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;

class LansiaJadwalController extends Controller
{
    public function index()
    {
        return view('lansia.jadwal.index');
    }
}
