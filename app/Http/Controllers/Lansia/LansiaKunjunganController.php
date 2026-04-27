<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\KunjunganLansia;
use App\Models\Lansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LansiaKunjunganController extends Controller
{
    // Daftar obat & vitamin default
    const DAFTAR_OBAT = [
        'Paracetamol', 'Amlodipin', 'Metformin', 'Captopril',
        'Simvastatin', 'Antasida', 'Vitamin B Complex', 'Asam Mefenamat',
        'Glibenklamid', 'Furosemid', 'Amlodipine', 'Lisinopril',
    ];

    const DAFTAR_VITAMIN = [
        'Vitamin C', 'Vitamin D', 'Vitamin B12', 'Kalsium',
        'Asam Folat', 'Zinc', 'Vitamin E', 'Omega-3', 'Multivitamin',
    ];

    public function index()
    {
        return view('lansia.kunjungan.index');
    }

    // ── API: List kunjungan ────────────────────────────────────
    public function list(Request $request)
    {
        $search = trim($request->get('q', ''));
        $query  = KunjunganLansia::with('lansia')
            ->whereHas('lansia', fn($q) => $q->where('is_deleted', false));

        if ($search) {
            $query->whereHas('lansia', fn($q) => $q->where('nama_lengkap', 'like', "%{$search}%"));
        }

        $data = $query->orderBy('tanggal_kunjungan', 'desc')->limit(200)->get()->map(fn($k) => [
            'id'               => $k->id,
            'lansia_id'        => $k->lansia_id,
            'nama_lansia'      => $k->lansia?->nama_lengkap,
            'jk_lansia'        => $k->lansia?->jenis_kelamin,
            'umur_lansia'      => $k->lansia?->umur,
            'tanggal_kunjungan'=> $k->tanggal_kunjungan?->format('Y-m-d'),
            // Fisik
            'berat_badan'      => $k->berat_badan,
            'tekanan_darah'    => $k->tekanan_darah,
            'status_tensi'     => $k->status_tensi,
            // Darah
            'gula_darah'       => $k->gula_darah,
            'status_gula'      => $k->status_gula,
            'kolesterol'       => $k->kolesterol,
            'status_kolesterol'=> $k->status_kolesterol,
            'asam_urat'        => $k->asam_urat,
            'status_asam_urat' => $k->status_asam_urat,
            // Pengobatan
            'ada_keluhan'      => $k->ada_keluhan,
            'keluhan'          => $k->keluhan,
            'obat_diberikan'   => $k->obat_diberikan ?? [],
            'vitamin_diberikan'=> $k->vitamin_diberikan ?? [],
            'catatan_bidan'    => $k->catatan_bidan,
            // Status keseluruhan
            'ada_masalah'      => $this->adaMasalah($k),
        ]);

        return response()->json([
            'success'        => true,
            'data'           => $data,
            'daftar_obat'    => self::DAFTAR_OBAT,
            'daftar_vitamin' => self::DAFTAR_VITAMIN,
        ]);
    }

    // ── API: Riwayat kunjungan per lansia ──────────────────────
    public function riwayat($lansiaId)
    {
        $lansia = Lansia::aktif()->findOrFail($lansiaId);
        $data   = KunjunganLansia::where('lansia_id', $lansiaId)
            ->orderBy('tanggal_kunjungan', 'desc')
            ->limit(20)
            ->get()
            ->map(fn($k) => [
                'id'               => $k->id,
                'tanggal_kunjungan'=> $k->tanggal_kunjungan?->format('Y-m-d'),
                'berat_badan'      => $k->berat_badan,
                'tekanan_darah'    => $k->tekanan_darah,
                'status_tensi'     => $k->status_tensi,
                'gula_darah'       => $k->gula_darah,
                'status_gula'      => $k->status_gula,
                'kolesterol'       => $k->kolesterol,
                'status_kolesterol'=> $k->status_kolesterol,
                'asam_urat'        => $k->asam_urat,
                'status_asam_urat' => $k->status_asam_urat,
                'ada_keluhan'      => $k->ada_keluhan,
                'keluhan'          => $k->keluhan,
                'obat_diberikan'   => $k->obat_diberikan ?? [],
                'vitamin_diberikan'=> $k->vitamin_diberikan ?? [],
                'catatan_bidan'    => $k->catatan_bidan,
                'ada_masalah'      => $this->adaMasalah($k),
            ]);

        return response()->json([
            'success' => true,
            'lansia'  => ['id' => $lansia->id, 'nama' => $lansia->nama_lengkap, 'jk' => $lansia->jenis_kelamin],
            'data'    => $data,
        ]);
    }

    // ── API: Store kunjungan baru ──────────────────────────────
    public function store(Request $request)
    {
        $data = $request->validate([
            'lansia_id'        => 'required|integer|exists:lansia,id',
            'tanggal_kunjungan'=> 'required|date|before_or_equal:today',
            'berat_badan'      => 'nullable|numeric|min:20|max:200',
            'tekanan_darah'    => 'nullable|string|max:20',
            'gula_darah'       => 'nullable|numeric|min:0|max:1000',
            'kolesterol'       => 'nullable|numeric|min:0|max:1000',
            'asam_urat'        => 'nullable|numeric|min:0|max:30',
            'ada_keluhan'      => 'required|boolean',
            'keluhan'          => 'nullable|string',
            'obat_diberikan'   => 'nullable|array',
            'vitamin_diberikan'=> 'nullable|array',
            'catatan_bidan'    => 'nullable|string',
        ]);

        // Ambil jenis kelamin untuk kalkulasi asam urat
        $lansia = Lansia::find($data['lansia_id']);
        $jk     = $lansia?->jenis_kelamin ?? 'L';

        // Hitung status otomatis
        $data['status_tensi']     = KunjunganLansia::hitungStatusTensi($data['tekanan_darah'] ?? null);
        $data['status_gula']      = KunjunganLansia::hitungStatusGula($data['gula_darah'] ?? null);
        $data['status_kolesterol']= KunjunganLansia::hitungStatusKolesterol($data['kolesterol'] ?? null);
        $data['status_asam_urat'] = KunjunganLansia::hitungStatusAsamUrat($data['asam_urat'] ?? null, $jk);
        $data['dicatat_oleh']     = Auth::id();

        $kunjungan = KunjunganLansia::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Kunjungan berhasil dicatat!',
            'data'    => ['id' => $kunjungan->id],
        ], 201);
    }

    // ── API: Update kunjungan ──────────────────────────────────
    public function update(Request $request, $id)
    {
        $kunjungan = KunjunganLansia::findOrFail($id);
        $data = $request->validate([
            'tanggal_kunjungan'=> 'sometimes|date|before_or_equal:today',
            'berat_badan'      => 'nullable|numeric|min:20|max:200',
            'tekanan_darah'    => 'nullable|string|max:20',
            'gula_darah'       => 'nullable|numeric|min:0|max:1000',
            'kolesterol'       => 'nullable|numeric|min:0|max:1000',
            'asam_urat'        => 'nullable|numeric|min:0|max:30',
            'ada_keluhan'      => 'sometimes|boolean',
            'keluhan'          => 'nullable|string',
            'obat_diberikan'   => 'nullable|array',
            'vitamin_diberikan'=> 'nullable|array',
            'catatan_bidan'    => 'nullable|string',
        ]);

        $lansia = $kunjungan->lansia;
        $jk     = $lansia?->jenis_kelamin ?? 'L';

        if (isset($data['tekanan_darah'])) $data['status_tensi']      = KunjunganLansia::hitungStatusTensi($data['tekanan_darah']);
        if (isset($data['gula_darah']))    $data['status_gula']       = KunjunganLansia::hitungStatusGula($data['gula_darah']);
        if (isset($data['kolesterol']))    $data['status_kolesterol']  = KunjunganLansia::hitungStatusKolesterol($data['kolesterol']);
        if (isset($data['asam_urat']))     $data['status_asam_urat']   = KunjunganLansia::hitungStatusAsamUrat($data['asam_urat'], $jk);

        $kunjungan->update($data);
        return response()->json(['success' => true, 'message' => 'Kunjungan berhasil diperbarui!']);
    }

    // ── API: Delete ────────────────────────────────────────────
    public function destroy($id)
    {
        KunjunganLansia::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Data kunjungan berhasil dihapus!']);
    }

    // ── Helper: cek apakah ada kondisi tidak normal ────────────
    private function adaMasalah(KunjunganLansia $k): bool
    {
        return in_array($k->status_tensi, ['hipertensi1', 'hipertensi2'])
            || in_array($k->status_gula, ['tinggi', 'sangat_tinggi', 'rendah'])
            || $k->status_kolesterol === 'tinggi'
            || $k->status_asam_urat === 'tinggi';
    }
}
