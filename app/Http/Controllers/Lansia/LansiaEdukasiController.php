<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;

class LansiaEdukasiController extends Controller
{
    public function index()
    {
        return view('lansia.edukasi.index');
    }
}
