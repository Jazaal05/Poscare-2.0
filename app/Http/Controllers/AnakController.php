<?php

namespace App\Http\Controllers;

use App\Http\Requests\Anak\StoreAnakRequest;
use App\Http\Requests\Anak\StoreRegistrasiRequest;
use App\Http\Requests\Anak\UpdateAnakRequest;
use App\Models\Anak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnakController extends Controller
{
    // =============================================
    // INDEX - List Data Anak
    // Menggantikan: pages/data-anak.php + api_web/children_list.php
    // =============================================
    public function index()
    {
        return view('anak.index');
    }

    // =============================================
    // API: List Data Anak (JSON)
    // =============================================
    public function list(Request $request)
    {
        $user   = $request->user();
        $search = trim($request->get('q', ''));
        $limit  = min(100, max(10, (int) $request->get('limit', 50)));

        $query = Anak::aktif()->with('user');

        // Role-based filter
        if ($user->role !== 'admin') {
            // Ambil berdasarkan user_id ATAU nik_ibu (untuk akun lama yang belum terhubung)
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->nik) {
                    $q->orWhere('nik_ibu', $user->nik);
                }
            });
        }

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_anak', 'like', "%{$search}%")
                  ->orWhere('nik_anak', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%")
                  ->orWhere('nik_ibu', 'like', "%{$search}%")
                  ->orWhere('alamat_domisili', 'like', "%{$search}%");
            });
        }

        $children = $query->orderBy('id', 'desc')->limit($limit)->get();

        // Format data sama seperti children_list.php lama
        $data = $children->map(function ($anak) {
            $umurBulan = Carbon::parse($anak->tanggal_lahir)->diffInMonths(now());
            $tahun     = floor($umurBulan / 12);
            $bulan     = $umurBulan % 12;

            if ($tahun > 0 && $bulan > 0) $usia = "{$tahun} tahun {$bulan} bulan";
            elseif ($tahun > 0)            $usia = "{$tahun} tahun";
            else                           $usia = "{$bulan} bulan";

            return [
                'id'                           => $anak->id,
                'nik_anak'                     => $anak->nik_anak,
                'nama_anak'                    => $anak->nama_anak,
                'jenis_kelamin'                => $anak->jenis_kelamin,
                'tanggal_lahir'                => $anak->tanggal_lahir ? \Carbon\Carbon::parse($anak->tanggal_lahir)->format('Y-m-d') : null,
                'tempat_lahir'                 => $anak->tempat_lahir,
                'anak_ke'                      => $anak->anak_ke,
                'nama_ibu'                     => $anak->nama_ibu,
                'nik_ibu'                      => $anak->nik_ibu,
                'nama_ayah'                    => $anak->nama_ayah,
                'nik_ayah'                     => $anak->nik_ayah,
                'hp_kontak_ortu'               => $anak->hp_kontak_ortu,
                'alamat_domisili'              => $anak->alamat_domisili,
                'rt_rw'                        => $anak->rt_rw,
                'berat_badan'                  => $anak->berat_badan,
                'tinggi_badan'                 => $anak->tinggi_badan,
                'lingkar_kepala'               => $anak->lingkar_kepala,
                'status_gizi'                  => $anak->status_gizi ?? 'Belum diukur',
                'status_gizi_detail'           => $anak->status_gizi_detail,
                'tanggal_penimbangan_terakhir' => $anak->tanggal_penimbangan_terakhir,
                'umur_bulan'                   => $umurBulan,
                'usia'                         => $usia,
                'umur_display'                 => $usia,
                'user_id'                      => $anak->user_id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dimuat',
            'data'    => $data,
            'total'   => $data->count(),
        ]);
    }

    // =============================================
    // SHOW - Detail Anak
    // Menggantikan: api_web/children_detail.php
    // =============================================
    public function show(Request $request, $id)
    {
        $user  = $request->user();
        $query = Anak::aktif()->where('id', $id);

        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                if ($user->nik) {
                    $q->orWhere('nik_ibu', $user->nik);
                }
            });
        }

        $anak = $query->first();

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Data anak tidak ditemukan atau bukan milik Anda',
            ], 404);
        }

        // Hitung umur
        $birthDate  = Carbon::parse($anak->tanggal_lahir);
        $ageMonths  = $birthDate->diffInMonths(now());
        $ageYears   = floor($ageMonths / 12);
        $remaining  = $ageMonths % 12;
        $ageDisplay = $ageYears > 0
            ? "{$ageYears} tahun" . ($remaining > 0 ? " {$remaining} bulan" : '')
            : "{$ageMonths} bulan";

        // Data user/orang tua
        $userOrtu = $anak->user;

        // Pengukuran terakhir dari riwayat
        $pengukuranTerakhir = $anak->riwayatPengukuran()
            ->orderBy('tanggal_ukur', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        // Riwayat pengukuran (max 50)
        $riwayatPengukuran = $anak->riwayatPengukuran()
            ->orderBy('tanggal_ukur', 'desc')
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->map(fn($r) => [
                'id'           => $r->id,
                'tanggal_ukur' => $r->tanggal_ukur,
                'umur_bulan'   => (float) $r->umur_bulan,
                'bb_kg'        => (float) $r->bb_kg,
                'tb_pb_cm'     => (float) $r->tb_pb_cm,
                'lk_cm'        => $r->lk_cm ? (float) $r->lk_cm : null,
                'cara_ukur'    => $r->cara_ukur,
                'imt'          => $r->imt ? (float) $r->imt : null,
                'z_tbu'        => $r->z_tbu ? (float) $r->z_tbu : null,
                'z_bbu'        => $r->z_bbu ? (float) $r->z_bbu : null,
                'z_bbtb'       => $r->z_bbtb ? (float) $r->z_bbtb : null,
                'z_imtu'       => $r->z_imtu ? (float) $r->z_imtu : null,
                'kat_tbu'      => $r->kat_tbu,
                'kat_bbu'      => $r->kat_bbu,
                'kat_bbtb'     => $r->kat_bbtb,
                'overall_8'    => $r->overall_8,
            ]);

        // Riwayat imunisasi
        $riwayatImunisasi = $anak->imunisasi()
            ->with('masterVaksin')
            ->whereNotNull('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($i) => [
                'id'          => $i->id,
                'tanggal'     => $i->tanggal,
                'umur_bulan'  => $i->umur_bulan,
                'nama_vaksin' => $i->masterVaksin->nama_vaksin ?? '-',
            ]);

        // Build status gizi dari pengukuran terakhir
        $statusGizi       = 'Belum diukur';
        $statusGiziDetail = null;
        $beratBadan       = null;
        $tinggiBadan      = null;
        $lingkarKepala    = null;
        $caraUkur         = null;
        $tanggalPenimbangan = null;

        if ($pengukuranTerakhir) {
            $beratBadan       = (float) $pengukuranTerakhir->bb_kg;
            $tinggiBadan      = (float) $pengukuranTerakhir->tb_pb_cm;
            $lingkarKepala    = $pengukuranTerakhir->lk_cm ? (float) $pengukuranTerakhir->lk_cm : null;
            $caraUkur         = $pengukuranTerakhir->cara_ukur;
            $tanggalPenimbangan = $pengukuranTerakhir->tanggal_ukur;
            $statusGizi       = $pengukuranTerakhir->overall_8 ?? 'Belum diukur';
            $statusGiziDetail = [
                'zscore'   => [
                    'tbu'  => $pengukuranTerakhir->z_tbu,
                    'bbu'  => $pengukuranTerakhir->z_bbu,
                    'bbtb' => $pengukuranTerakhir->z_bbtb,
                    'imtu' => $pengukuranTerakhir->z_imtu,
                ],
                'kategori' => [
                    'tbu'  => $pengukuranTerakhir->kat_tbu,
                    'bbu'  => $pengukuranTerakhir->kat_bbu,
                    'bbtb' => $pengukuranTerakhir->kat_bbtb,
                ],
                'overall_8' => ['kategori' => $statusGizi],
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                           => $anak->id,
                'user_id'                      => $anak->user_id,
                'nik_anak'                     => $anak->nik_anak,
                'nama_anak'                    => $anak->nama_anak,
                'jenis_kelamin'                => $anak->jenis_kelamin,
                'tanggal_lahir'                => $anak->tanggal_lahir,
                'tempat_lahir'                 => $anak->tempat_lahir,
                'anak_ke'                      => $anak->anak_ke ?? 1,
                'umur_bulan'                   => $ageMonths,
                'umur_tahun'                   => $ageYears,
                'umur_display'                 => $ageDisplay,
                'alamat_domisili'              => $anak->alamat_domisili,
                'rt_rw'                        => $anak->rt_rw,
                'nama_kk'                      => $anak->nama_kk,
                'nama_ayah'                    => $anak->nama_ayah,
                'nama_ibu'                     => $anak->nama_ibu,
                'nik_ayah'                     => $anak->nik_ayah,
                'nik_ibu'                      => $anak->nik_ibu,
                'hp_kontak_ortu'               => $anak->hp_kontak_ortu,
                'username_ortu'                => $userOrtu?->username,
                'email_ortu'                   => $userOrtu?->email,
                'berat_badan'                  => $beratBadan,
                'tinggi_badan'                 => $tinggiBadan,
                'lingkar_kepala'               => $lingkarKepala,
                'cara_ukur'                    => $caraUkur,
                'status_gizi'                  => $statusGizi,
                'status_gizi_detail'           => $statusGiziDetail,
                'tanggal_penimbangan_terakhir' => $tanggalPenimbangan,
                'imunisasi_count'              => $riwayatImunisasi->count(),
                'riwayat_imunisasi'            => $riwayatImunisasi,
                'riwayat_pengukuran'           => $riwayatPengukuran,
            ],
        ]);
    }

    // =============================================
    // STORE - Registrasi Lengkap (Anak + Orang Tua Baru)
    // Menggantikan: api_web/registrasi_lengkap.php
    // =============================================
    public function store(StoreRegistrasiRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Normalisasi no HP
            $hp = preg_replace('/[^0-9]/', '', $data['no_hp_ibu']);
            if (str_starts_with($hp, '62')) $hp = '0' . substr($hp, 2);

            // Cek NIK ibu sudah ada di sistem
            $existingAnak = Anak::where('nik_ibu', $data['nik_ibu'])->first();
            if ($existingAnak) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Ibu {$data['nik_ibu']} sudah terdaftar atas nama {$existingAnak->nama_ibu}. Gunakan fitur 'Tambah Data Anak' untuk menambah anak berikutnya.",
                ], 409);
            }

            // Cek NIK anak duplikat
            if (Anak::where('nik_anak', $data['nik_anak'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Anak {$data['nik_anak']} sudah terdaftar dalam sistem.",
                ], 409);
            }

            // Validasi usia 0-60 bulan
            $birthDate   = Carbon::parse($data['tanggal_lahir']);
            $ageInMonths = $birthDate->diffInMonths(now());
            if ($ageInMonths > 60) {
                return response()->json([
                    'success' => false,
                    'message' => "Usia anak {$ageInMonths} bulan melebihi batas sistem (0-60 bulan / 0-5 tahun).",
                ], 422);
            }

            // Buat atau cari user orang tua
            $user = User::where('nik', $data['nik_ibu'])->first();
            if (!$user) {
                $user = User::create([
                    'username'     => 'ortu_' . $data['nik_ibu'],
                    'password'     => bcrypt('poscare123'),
                    'role'         => 'orangtua',
                    'nama_lengkap' => $data['nama_ibu'],
                    'nik'          => $data['nik_ibu'],
                    'no_telp'      => $hp,
                ]);
            } else {
                // Jika user sudah ada dan punya lansia, upgrade ke orangtua_lansia
                if ($user->role === 'wali_lansia') {
                    $user->update(['role' => 'orangtua_lansia']);
                }
            }

            // Insert anak
            $anak = Anak::create([
                'user_id'        => $user->id,
                'nik_anak'       => $data['nik_anak'],
                'nama_anak'      => trim($data['nama_anak']),
                'tanggal_lahir'  => $data['tanggal_lahir'],
                'tempat_lahir'   => trim($data['tempat_lahir']),
                'jenis_kelamin'  => $data['jenis_kelamin'],
                'anak_ke'        => 1,
                'nama_kk'        => trim($data['nama_kk']),
                'nama_ibu'       => trim($data['nama_ibu']),
                'nik_ibu'        => $data['nik_ibu'],
                'nama_ayah'      => trim($data['nama_ayah']),
                'nik_ayah'       => $data['nik_ayah'],
                'hp_kontak_ortu' => $hp,
                'alamat_domisili'=> trim($data['alamat']),
                'rt_rw'          => $data['rt_rw'] ?? null,
                'is_deleted'     => 0,
            ]);

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Data anak berhasil didaftarkan!',
                'anak_id'  => $anak->id,
                'data'     => ['id' => $anak->id, 'nama_anak' => $anak->nama_anak],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =============================================
    // STORE TAMBAH - Tambah Anak ke-2, ke-3 dst
    // Menggantikan: api_web/tambah_anak_baru.php
    // =============================================
    public function storeTambah(StoreAnakRequest $request)
    {
        DB::beginTransaction();
        try {
            $data      = $request->validated();
            $parentId  = (int) $data['parent_id'];

            // Cari data parent dari tabel anak berdasarkan user_id atau nik_ibu
            $parentAnak = Anak::where('user_id', $parentId)->first()
                ?? Anak::where('id', $parentId)->first();

            if (!$parentAnak) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data orang tua tidak ditemukan.',
                ], 404);
            }

            // Validasi parent harus sudah punya anak pertama
            $jumlahAnak = Anak::where('nik_ibu', $parentAnak->nik_ibu)->count();
            if ($jumlahAnak === 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Fitur ini hanya untuk anak ke-2 dst. Gunakan 'Registrasi Lengkap' untuk anak pertama.",
                ], 422);
            }

            // Validasi alamat parent lengkap
            if (empty($parentAnak->alamat_domisili) || strlen(trim($parentAnak->alamat_domisili)) < 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Alamat orang tua belum lengkap. Edit data anak pertama terlebih dahulu.',
                ], 422);
            }

            // Validasi usia 0-60 bulan
            $ageInMonths = Carbon::parse($data['tanggal_lahir'])->diffInMonths(now());
            if ($ageInMonths > 60) {
                return response()->json([
                    'success' => false,
                    'message' => "Usia anak {$ageInMonths} bulan melebihi batas sistem (0-60 bulan).",
                ], 422);
            }

            // Cek NIK anak duplikat
            if (Anak::where('nik_anak', $data['nik_anak'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => "NIK Anak {$data['nik_anak']} sudah terdaftar.",
                ], 409);
            }

            $anakKe = $jumlahAnak + 1;

            $anak = Anak::create([
                'user_id'        => $parentAnak->user_id,
                'nik_anak'       => $data['nik_anak'],
                'nama_anak'      => trim($data['nama_anak']),
                'tanggal_lahir'  => $data['tanggal_lahir'],
                'tempat_lahir'   => trim($data['tempat_lahir']),
                'jenis_kelamin'  => $data['jenis_kelamin'],
                'anak_ke'        => $anakKe,
                'nama_kk'        => $parentAnak->nama_kk,
                'nama_ibu'       => $parentAnak->nama_ibu,
                'nik_ibu'        => $parentAnak->nik_ibu,
                'nama_ayah'      => $parentAnak->nama_ayah,
                'nik_ayah'       => $parentAnak->nik_ayah,
                'hp_kontak_ortu' => $parentAnak->hp_kontak_ortu,
                'alamat_domisili'=> $parentAnak->alamat_domisili,
                'rt_rw'          => $parentAnak->rt_rw,
                'is_deleted'     => 0,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Anak ke-{$anakKe} berhasil ditambahkan!",
                'anak_id' => $anak->id,
                'data'    => ['id' => $anak->id, 'nama_anak' => $anak->nama_anak],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =============================================
    // UPDATE - Edit Data Anak
    // Menggantikan: api_web/children_update.php
    // =============================================
    public function update(UpdateAnakRequest $request, $id)
    {
        $user  = $request->user();
        $query = Anak::aktif()->where('id', $id);

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $anak = $query->first();

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Data anak tidak ditemukan atau bukan milik Anda.',
            ], 404);
        }

        $data = $request->validated();

        // Cek NIK anak duplikat (kecuali diri sendiri)
        if (isset($data['nik_anak'])) {
            $duplicate = Anak::where('nik_anak', $data['nik_anak'])
                ->where('id', '!=', $id)
                ->where('is_deleted', 0)
                ->exists();
            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'NIK Anak sudah digunakan oleh anak lain.',
                ], 409);
            }
        }

        // ── Simpan data pengukuran LAMA ke riwayat sebelum diganti ──────────
        // Sama persis dengan logika di children_update.php native
        $hasGrowthUpdate = isset($data['berat_badan']) || isset($data['tinggi_badan'])
            || isset($data['lingkar_kepala']) || isset($data['cara_ukur']);

        if ($hasGrowthUpdate) {
            $oldBB    = $anak->berat_badan;
            $oldTB    = $anak->tinggi_badan;
            $oldLK    = $anak->lingkar_kepala;
            $oldCara  = $anak->cara_ukur;
            $oldTgl   = $anak->tanggal_penimbangan_terakhir;

            // Hanya simpan jika ada data lama yang valid
            if ($oldBB && $oldBB > 0 && $oldTB && $oldTB > 0) {
                // Cek apakah data lama sudah ada di riwayat
                $alreadyExists = \App\Models\RiwayatPengukuran::where('anak_id', $id)
                    ->where('tanggal_ukur', $oldTgl ?? now()->toDateString())
                    ->where('bb_kg', $oldBB)
                    ->where('tb_pb_cm', $oldTB)
                    ->exists();

                if (!$alreadyExists) {
                    try {
                        $birthDate  = Carbon::parse($anak->tanggal_lahir);
                        $measureDate = Carbon::parse($oldTgl ?? now());
                        $umurHari   = $birthDate->diffInDays($measureDate);
                        $umurBulan  = round($umurHari / 30.4375, 2);
                        $caraUkur   = $oldCara ?? 'berdiri';
                        $imt        = $oldBB / (($oldTB / 100) ** 2);

                        // Hitung z-score untuk data lama
                        $who     = app(\App\Services\WhoService::class);
                        $zResult = $who->hitungStatusGiziLengkap(
                            $umurBulan, $oldBB, $oldTB,
                            $anak->jenis_kelamin, $caraUkur, $oldLK
                        );

                        if (!isset($zResult['error'])) {
                            $detail   = $zResult['status_gizi_detail'];
                            $zscore   = $detail['zscore'];
                            $axis     = $detail['axis'];
                            $overall8 = $detail['overall_8'];

                            \App\Models\RiwayatPengukuran::create([
                                'anak_id'        => $id,
                                'tanggal_ukur'   => $oldTgl ?? now()->toDateString(),
                                'umur_hari'      => $umurHari,
                                'umur_bulan'     => $umurBulan,
                                'bb_kg'          => $oldBB,
                                'tb_pb_cm'       => $oldTB,
                                'lk_cm'          => $oldLK,
                                'cara_ukur'      => $caraUkur,
                                'imt'            => round($imt, 2),
                                'z_tbu'          => $zscore['tbu'],
                                'z_bbu'          => $zscore['bbu'],
                                'z_bbtb'         => $zscore['bbtb'],
                                'z_imtu'         => $zscore['imtu'],
                                'kat_tbu'        => $axis['tbu']['label'],
                                'kat_bbu'        => $axis['bbu']['label'],
                                'kat_bbtb'       => $axis['adiposity']['label'],
                                'kat_imtu'       => $axis['adiposity']['label'],
                                'overall_8'      => $overall8['kategori'],
                                'overall_source' => 'WHO-2006',
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Gagal simpan riwayat tidak menghentikan proses update
                        \Log::warning("Gagal simpan riwayat lama saat update anak ID {$id}: " . $e->getMessage());
                    }
                }
            }

            // Tandai tanggal penimbangan terakhir diperbarui
            $data['tanggal_penimbangan_terakhir'] = now()->toDateString();
        }

        $anak->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data anak berhasil diperbarui!',
            'data'    => ['id' => $anak->id, 'nama_anak' => $anak->nama_anak],
        ]);
    }

    // =============================================
    // DESTROY - Soft Delete
    // Menggantikan: api_web/children_delete.php
    // =============================================
    public function destroy(Request $request, $id)
    {
        $user  = $request->user();
        $query = Anak::aktif()->where('id', $id);

        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $anak = $query->first();

        if (!$anak) {
            return response()->json([
                'success' => false,
                'message' => 'Data anak tidak ditemukan atau bukan milik Anda.',
            ], 404);
        }

        // Soft delete
        $anak->update(['is_deleted' => 1]);

        return response()->json([
            'success' => true,
            'message' => "Data anak {$anak->nama_anak} berhasil dihapus.",
        ]);
    }

    // =============================================
    // PARENTS LIST - Autocomplete Orang Tua
    // Menggantikan: api_web/parents_list.php
    // =============================================
    public function parentsList(Request $request)
    {
        $search = trim($request->get('search', ''));

        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'data'    => [],
                'message' => 'Masukkan minimal 2 karakter untuk mencari',
            ]);
        }

        // Cari dari tabel anak, group by nik_ibu (sama seperti parents_list.php lama)
        $parents = Anak::aktif()
            ->select('user_id', 'nama_ibu', 'nik_ibu', 'nama_ayah', 'nik_ayah', 'nama_kk', 'hp_kontak_ortu', 'alamat_domisili', 'rt_rw')
            ->where(function ($q) use ($search) {
                $q->where('nama_ibu', 'like', "%{$search}%")
                  ->orWhere('nik_ibu', 'like', "%{$search}%");
            })
            ->whereNotNull('nama_ibu')
            ->groupBy('nik_ibu', 'user_id', 'nama_ibu', 'nama_ayah', 'nik_ayah', 'nama_kk', 'hp_kontak_ortu', 'alamat_domisili', 'rt_rw')
            ->limit(20)
            ->get()
            ->map(function ($p) {
                $jumlahAnak = Anak::aktif()->where('nik_ibu', $p->nik_ibu)->count();
                return [
                    'user_id'     => $p->user_id,
                    'nama_ibu'    => $p->nama_ibu,
                    'nik_ibu'     => $p->nik_ibu,
                    'nama_ayah'   => $p->nama_ayah,
                    'nik_ayah'    => $p->nik_ayah,
                    'nama_kk'     => $p->nama_kk,
                    'no_hp'       => $p->hp_kontak_ortu,
                    'alamat'      => $p->alamat_domisili,
                    'rt_rw'       => $p->rt_rw,
                    'jumlah_anak' => $jumlahAnak,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $parents,
        ]);
    }

    // =============================================
    // GET CHILDREN COUNT — menggantikan api_web/get_children_count.php
    // Dipakai saat tambah anak baru untuk hitung urutan anak ke-berapa
    // =============================================
    public function getChildrenCount(Request $request)
    {
        $nikIbu   = trim($request->get('nik_ibu', ''));
        $parentId = $request->get('parent_id');

        $nikIbu_resolved = null;
        $namaIbu         = null;

        // Prioritas 1: nik_ibu langsung dari frontend
        if (!empty($nikIbu)) {
            $anak = Anak::where('nik_ibu', $nikIbu)->first();
            if ($anak) {
                $nikIbu_resolved = $nikIbu;
                $namaIbu         = $anak->nama_ibu;
            }
        }

        // Fallback: cari dari parent_id
        if (!$nikIbu_resolved && $parentId) {
            $parentIdInt = (int) $parentId;

            // Cari dari user_id
            $anak = Anak::where('user_id', $parentIdInt)->first();
            if ($anak) {
                $nikIbu_resolved = $anak->nik_ibu;
                $namaIbu         = $anak->nama_ibu;
            }

            // Cari dari anak.id
            if (!$nikIbu_resolved) {
                $anak = Anak::find($parentIdInt);
                if ($anak) {
                    $nikIbu_resolved = $anak->nik_ibu;
                    $namaIbu         = $anak->nama_ibu;
                }
            }
        }

        if (!$nikIbu_resolved) {
            return response()->json(['success' => false, 'message' => 'Data orang tua tidak ditemukan.'], 404);
        }

        $totalAnak = Anak::aktif()->where('nik_ibu', $nikIbu_resolved)->count();

        $children = Anak::aktif()
            ->where('nik_ibu', $nikIbu_resolved)
            ->orderBy('anak_ke')
            ->orderBy('tanggal_lahir')
            ->get(['id', 'nama_anak', 'tanggal_lahir', 'anak_ke']);

        return response()->json([
            'success'      => true,
            'total'        => $totalAnak,
            'next_anak_ke' => $totalAnak + 1,
            'nik_ibu'      => $nikIbu_resolved,
            'nama_ibu'     => $namaIbu,
            'children'     => $children,
            'message'      => "Ibu {$namaIbu} sudah punya {$totalAnak} anak. Anak baru akan jadi anak ke-" . ($totalAnak + 1) . '.',
        ]);
    }
}
