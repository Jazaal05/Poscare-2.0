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

    // ── API: List lansia (for kunjungan page) ──────────────────
    public function list(Request $request)
    {
        $search = trim($request->get('q', ''));
        $limit  = (int) $request->get('limit', 200);

        $query = Lansia::aktif();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_lansia', 'like', "%{$search}%")
                  ->orWhere('nama_wali', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('id', 'desc')->limit($limit)->get()->map(function ($l) {
            return [
                'id'                => $l->id,
                'nama_lansia'       => $l->nama_lengkap ?: 'Tanpa Nama', // Fallback jika NULL
                'nik_lansia'        => $l->nik_lansia ?: '-',
                'jenis_kelamin'     => $l->jenis_kelamin,
                'tanggal_lahir'     => $l->tgl_lahir?->format('Y-m-d'),
                'tempat_lahir'      => $l->tempat_lahir ?: '-',
                'usia'              => $l->umur_display ?: '-',
                'berat_badan'       => $l->berat_badan,
                'tinggi_badan'      => $l->tinggi_badan,
                'tekanan_darah'     => $l->tekanan_darah,
                'gula_darah'        => $l->gula_darah,
                'kolesterol'        => $l->kolesterol,
                'asam_urat'         => $l->asam_urat,
                'status_kesehatan'  => $l->status_kesehatan ?: 'Belum Diperiksa',
                'alamat_domisili'   => $l->alamat_domisili ?: '-',
                'rt_rw'             => $l->rt_rw ?: '-',
                'nama_wali'         => $l->nama_wali ?: '-',
                'nik_wali'          => $l->nik_wali ?: '-',
                'hp_kontak_wali'    => $l->hp_kontak_wali ?: '-',
                'nama_kk'           => $l->nama_kk ?: '-',
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ── API: Show single lansia ────────────────────────────────
    public function show($id)
    {
        $lansia = Lansia::aktif()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                          => $lansia->id,
                'nama_lansia'                 => $lansia->nama_lengkap,
                'nik_lansia'                  => $lansia->nik_lansia,
                'jenis_kelamin'               => $lansia->jenis_kelamin,
                'tanggal_lahir'               => $lansia->tgl_lahir?->format('Y-m-d'),
                'tempat_lahir'                => $lansia->tempat_lahir,
                'umur_display'                => $lansia->umur_display,
                'berat_badan'                 => $lansia->berat_badan,
                'tinggi_badan'                => $lansia->tinggi_badan,
                'tekanan_darah'               => $lansia->tekanan_darah,
                'gula_darah'                  => $lansia->gula_darah,
                'kolesterol'                  => $lansia->kolesterol,
                'asam_urat'                   => $lansia->asam_urat,
                'status_kesehatan'            => $lansia->status_kesehatan,
                'status_detail_array'         => $lansia->status_detail_array,
                'alamat_domisili'             => $lansia->alamat_domisili,
                'rt_rw'                       => $lansia->rt_rw,
                'nama_wali'                   => $lansia->nama_wali,
                'nik_wali'                    => $lansia->nik_wali,
                'hp_kontak_wali'              => $lansia->hp_kontak_wali,
                'nama_kk'                     => $lansia->nama_kk,
                'tanggal_pemeriksaan_terakhir'=> $lansia->tanggal_pemeriksaan_terakhir?->format('d/m/Y'),
            ],
        ]);
    }

    // ── API: Store kunjungan baru ──────────────────────────────
    public function store(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'lansia_id'         => 'required|exists:lansia,id',
            'tanggal_kunjungan' => 'required|date',
            // Pengukuran fisik
            'berat_badan'       => 'nullable|numeric|min:30|max:150',
            'tinggi_badan'      => 'nullable|numeric|min:100|max:200',
            'tekanan_darah'     => 'nullable|string|max:20',
            // Cek darah
            'gula_darah'        => 'nullable|numeric|min:50|max:500',
            'kolesterol'        => 'nullable|numeric|min:100|max:400',
            'asam_urat'         => 'nullable|numeric|min:1|max:15',
            // Keluhan
            'ada_keluhan'       => 'nullable|boolean',
            'keluhan'           => 'nullable|string',
            // Pengobatan
            'obat_diberikan'    => 'nullable|array',
            'vitamin_diberikan' => 'nullable|array',
            'catatan_bidan'     => 'nullable|string',
        ]);

        // Ambil data lansia untuk jenis kelamin (untuk hitung status asam urat)
        $lansia = Lansia::findOrFail($data['lansia_id']);

        // Hitung status otomatis
        $data['status_tensi']      = KunjunganLansia::hitungStatusTensi($data['tekanan_darah'] ?? null);
        $data['status_gula']       = KunjunganLansia::hitungStatusGula($data['gula_darah'] ?? null);
        $data['status_kolesterol'] = KunjunganLansia::hitungStatusKolesterol($data['kolesterol'] ?? null);
        $data['status_asam_urat']  = KunjunganLansia::hitungStatusAsamUrat($data['asam_urat'] ?? null, $lansia->jenis_kelamin);

        // Set dicatat_oleh
        $data['dicatat_oleh'] = Auth::id();

        // Simpan kunjungan
        $kunjungan = KunjunganLansia::create($data);

        // Update data lansia dengan data terbaru dari kunjungan
        $updateLansia = [];
        if (isset($data['berat_badan'])) $updateLansia['berat_badan'] = $data['berat_badan'];
        if (isset($data['tinggi_badan'])) $updateLansia['tinggi_badan'] = $data['tinggi_badan'];
        if (isset($data['tekanan_darah'])) $updateLansia['tekanan_darah'] = $data['tekanan_darah'];
        if (isset($data['gula_darah'])) $updateLansia['gula_darah'] = $data['gula_darah'];
        if (isset($data['kolesterol'])) $updateLansia['kolesterol'] = $data['kolesterol'];
        if (isset($data['asam_urat'])) $updateLansia['asam_urat'] = $data['asam_urat'];
        $updateLansia['tanggal_pemeriksaan_terakhir'] = $data['tanggal_kunjungan'];

        // Tentukan status kesehatan berdasarkan kondisi
        if ($data['status_tensi'] === 'hipertensi1' || $data['status_tensi'] === 'hipertensi2') {
            $updateLansia['status_kesehatan'] = 'Hipertensi';
        } elseif ($data['status_gula'] === 'tinggi' || $data['status_gula'] === 'sangat_tinggi') {
            $updateLansia['status_kesehatan'] = 'Diabetes';
        } elseif ($data['status_kolesterol'] === 'tinggi') {
            $updateLansia['status_kesehatan'] = 'Kolesterol Tinggi';
        } elseif ($data['status_asam_urat'] === 'tinggi') {
            $updateLansia['status_kesehatan'] = 'Asam Urat Tinggi';
        } else {
            $updateLansia['status_kesehatan'] = 'Normal';
        }

        $lansia->update($updateLansia);

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil disimpan!',
            'data'    => ['id' => $kunjungan->id],
        ], 201);
    }

    // ── API: Update kunjungan ──────────────────────────────────
    public function update(Request $request, $id)
    {
        $kunjungan = KunjunganLansia::findOrFail($id);

        // Validasi input
        $data = $request->validate([
            'tanggal_kunjungan' => 'sometimes|date',
            // Pengukuran fisik
            'berat_badan'       => 'nullable|numeric|min:30|max:150',
            'tinggi_badan'      => 'nullable|numeric|min:100|max:200',
            'tekanan_darah'     => 'nullable|string|max:20',
            // Cek darah
            'gula_darah'        => 'nullable|numeric|min:50|max:500',
            'kolesterol'        => 'nullable|numeric|min:100|max:400',
            'asam_urat'         => 'nullable|numeric|min:1|max:15',
            // Keluhan
            'ada_keluhan'       => 'nullable|boolean',
            'keluhan'           => 'nullable|string',
            // Pengobatan
            'obat_diberikan'    => 'nullable|array',
            'vitamin_diberikan' => 'nullable|array',
            'catatan_bidan'     => 'nullable|string',
        ]);

        // Ambil data lansia untuk jenis kelamin
        $lansia = Lansia::findOrFail($kunjungan->lansia_id);

        // Hitung ulang status jika ada perubahan
        if (isset($data['tekanan_darah'])) {
            $data['status_tensi'] = KunjunganLansia::hitungStatusTensi($data['tekanan_darah']);
        }
        if (isset($data['gula_darah'])) {
            $data['status_gula'] = KunjunganLansia::hitungStatusGula($data['gula_darah']);
        }
        if (isset($data['kolesterol'])) {
            $data['status_kolesterol'] = KunjunganLansia::hitungStatusKolesterol($data['kolesterol']);
        }
        if (isset($data['asam_urat'])) {
            $data['status_asam_urat'] = KunjunganLansia::hitungStatusAsamUrat($data['asam_urat'], $lansia->jenis_kelamin);
        }

        // Update kunjungan
        $kunjungan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil diperbarui!',
        ]);
    }

    // ── API: Delete kunjungan ──────────────────────────────────
    public function destroy($id)
    {
        $kunjungan = KunjunganLansia::findOrFail($id);
        $kunjungan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil dihapus!',
        ]);
    }

    // ── API: Riwayat kunjungan per lansia (untuk mobile) ──────
    // GET /api/lansia/{lansiaId}/kunjungan
    public function riwayat($lansiaId)
    {
        $data = KunjunganLansia::where('lansia_id', $lansiaId)
            ->orderBy('tanggal_kunjungan', 'desc')
            ->get()
            ->map(function ($k) {
                return [
                    'id'                 => $k->id,
                    'lansia_id'          => $k->lansia_id,
                    'tanggal_kunjungan'  => $k->tanggal_kunjungan,
                    'berat_badan'        => $k->berat_badan,
                    'tekanan_darah'      => $k->tekanan_darah,
                    'status_tensi'       => $k->status_tensi,
                    'gula_darah'         => $k->gula_darah,
                    'status_gula'        => $k->status_gula,
                    'kolesterol'         => $k->kolesterol,
                    'status_kolesterol'  => $k->status_kolesterol,
                    'asam_urat'          => $k->asam_urat,
                    'status_asam_urat'   => $k->status_asam_urat,
                    'ada_keluhan'        => (int) $k->ada_keluhan,
                    'keluhan'            => $k->keluhan,
                    'obat_diberikan'     => $k->obat_diberikan ?? [],
                    'vitamin_diberikan'  => $k->vitamin_diberikan ?? [],
                    'catatan_bidan'      => $k->catatan_bidan,
                ];
            });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ── API: Kunjungan Selanjutnya (Update Data Lansia + Catat Kunjungan) ──
    public function kunjunganSelanjutnya(Request $request, $lansiaId)
    {
        $lansia = Lansia::aktif()->findOrFail($lansiaId);

        // Validasi input
        $data = $request->validate([
            // Data lansia yang bisa diupdate
            'nama_lengkap'      => 'sometimes|string|max:255',
            'nik_lansia'        => 'nullable|string|size:16|unique:lansia,nik_lansia,' . $lansiaId,
            'alamat_domisili'   => 'nullable|string',
            'rt_rw'             => 'nullable|string|max:10',
            'nama_wali'         => 'nullable|string|max:255',
            'nik_wali'          => 'nullable|string|size:16',
            'hp_kontak_wali'    => 'nullable|string|max:20',
            
            // Data kunjungan
            'tanggal_kunjungan' => 'required|date',
            'berat_badan'       => 'nullable|numeric|min:30|max:150',
            'tinggi_badan'      => 'nullable|numeric|min:100|max:200',
            'tekanan_darah'     => 'nullable|string|max:20',
            'gula_darah'        => 'nullable|numeric|min:50|max:500',
            'kolesterol'        => 'nullable|numeric|min:100|max:400',
            'asam_urat'         => 'nullable|numeric|min:1|max:15',
            'ada_keluhan'       => 'nullable|boolean',
            'keluhan'           => 'nullable|string',
            'obat_diberikan'    => 'nullable|array',
            'vitamin_diberikan' => 'nullable|array',
            'catatan_bidan'     => 'nullable|string',
        ]);

        // Update data lansia jika ada perubahan
        $updateLansia = [];
        if (isset($data['nama_lengkap'])) $updateLansia['nama_lengkap'] = $data['nama_lengkap'];
        if (isset($data['nik_lansia'])) $updateLansia['nik_lansia'] = $data['nik_lansia'];
        if (isset($data['alamat_domisili'])) $updateLansia['alamat_domisili'] = $data['alamat_domisili'];
        if (isset($data['rt_rw'])) $updateLansia['rt_rw'] = $data['rt_rw'];
        if (isset($data['nama_wali'])) $updateLansia['nama_wali'] = $data['nama_wali'];
        if (isset($data['nik_wali'])) $updateLansia['nik_wali'] = $data['nik_wali'];
        if (isset($data['hp_kontak_wali'])) $updateLansia['hp_kontak_wali'] = $data['hp_kontak_wali'];

        // Update data kesehatan dari kunjungan
        if (isset($data['berat_badan'])) $updateLansia['berat_badan'] = $data['berat_badan'];
        if (isset($data['tinggi_badan'])) $updateLansia['tinggi_badan'] = $data['tinggi_badan'];
        if (isset($data['tekanan_darah'])) $updateLansia['tekanan_darah'] = $data['tekanan_darah'];
        if (isset($data['gula_darah'])) $updateLansia['gula_darah'] = $data['gula_darah'];
        if (isset($data['kolesterol'])) $updateLansia['kolesterol'] = $data['kolesterol'];
        if (isset($data['asam_urat'])) $updateLansia['asam_urat'] = $data['asam_urat'];
        $updateLansia['tanggal_pemeriksaan_terakhir'] = $data['tanggal_kunjungan'];

        // Simpan data kunjungan
        $kunjunganData = [
            'lansia_id'         => $lansiaId,
            'tanggal_kunjungan' => $data['tanggal_kunjungan'],
            'berat_badan'       => $data['berat_badan'] ?? null,
            'tinggi_badan'      => $data['tinggi_badan'] ?? null,
            'tekanan_darah'     => $data['tekanan_darah'] ?? null,
            'gula_darah'        => $data['gula_darah'] ?? null,
            'kolesterol'        => $data['kolesterol'] ?? null,
            'asam_urat'         => $data['asam_urat'] ?? null,
            'ada_keluhan'       => $data['ada_keluhan'] ?? false,
            'keluhan'           => $data['keluhan'] ?? null,
            'obat_diberikan'    => $data['obat_diberikan'] ?? [],
            'vitamin_diberikan' => $data['vitamin_diberikan'] ?? [],
            'catatan_bidan'     => $data['catatan_bidan'] ?? null,
            'dicatat_oleh'      => Auth::id(),
        ];

        // Hitung status otomatis
        $kunjunganData['status_tensi']      = KunjunganLansia::hitungStatusTensi($kunjunganData['tekanan_darah']);
        $kunjunganData['status_gula']       = KunjunganLansia::hitungStatusGula($kunjunganData['gula_darah']);
        $kunjunganData['status_kolesterol'] = KunjunganLansia::hitungStatusKolesterol($kunjunganData['kolesterol']);
        $kunjunganData['status_asam_urat']  = KunjunganLansia::hitungStatusAsamUrat($kunjunganData['asam_urat'], $lansia->jenis_kelamin);

        // Tentukan status kesehatan
        if ($kunjunganData['status_tensi'] === 'hipertensi1' || $kunjunganData['status_tensi'] === 'hipertensi2') {
            $updateLansia['status_kesehatan'] = 'Hipertensi';
        } elseif ($kunjunganData['status_gula'] === 'tinggi' || $kunjunganData['status_gula'] === 'sangat_tinggi') {
            $updateLansia['status_kesehatan'] = 'Diabetes';
        } elseif ($kunjunganData['status_kolesterol'] === 'tinggi') {
            $updateLansia['status_kesehatan'] = 'Kolesterol Tinggi';
        } elseif ($kunjunganData['status_asam_urat'] === 'tinggi') {
            $updateLansia['status_kesehatan'] = 'Asam Urat Tinggi';
        } else {
            $updateLansia['status_kesehatan'] = 'Normal';
        }

        // Simpan kunjungan
        $kunjungan = KunjunganLansia::create($kunjunganData);

        // Update data lansia
        if (!empty($updateLansia)) {
            $lansia->update($updateLansia);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data kunjungan berhasil disimpan dan data lansia diperbarui!',
            'data'    => ['id' => $kunjungan->id],
        ], 201);
    }
}
