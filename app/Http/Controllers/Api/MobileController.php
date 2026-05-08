<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseApiController;
use App\Models\MasterVaksin;
use App\Models\Anak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * MobileController
 * Endpoint mobile yang tidak tercakup controller lain:
 * - Profil user (get & update)
 * - FCM token
 * - Notifikasi
 * - Data BBU WHO
 * - Vaksin (count, next, history)
 */
class MobileController extends BaseApiController
{
    // ─── PROFIL ──────────────────────────────────────────────────────────────

    /**
     * GET /api/profile
     * Menggantikan: get_userprofile_mobile.php
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'id'                => $user->id,
            'username'          => $user->username,
            'email'             => $user->email,
            'no_telp'           => $user->no_telp,
            'nik'               => $user->nik,
            'nama_lengkap'      => $user->nama_lengkap,
            'profile_image_url' => $user->profile_image_url,
            'level'             => $user->role,
            'role'              => $user->role,
        ], 'Detail pengguna berhasil dimuat.');
    }

    /**
     * POST /api/profile/update
     * Menggantikan: update_profile_mobile.php
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama_lengkap'         => 'sometimes|string|max:255',
            'username'             => 'sometimes|string|unique:users,username,' . $user->id,
            'email'                => 'sometimes|email|unique:users,email,' . $user->id,
            'no_telp'              => 'sometimes|string|max:20',
            'profile_image_base64' => 'sometimes|string',
        ]);

        $updateData = $request->only(['nama_lengkap', 'username', 'email', 'no_telp']);

        // Handle upload foto base64
        if ($request->filled('profile_image_base64')) {
            $base64 = $request->profile_image_base64;

            if (str_contains($base64, ';base64,')) {
                [, $base64] = explode(';base64,', $base64);
            }

            $imageData = base64_decode($base64);
            if ($imageData === false) {
                return $this->errorResponse('Base64 tidak valid.', 400);
            }

            $fileName  = 'profile_' . $user->id . '_' . time() . '.jpg';
            $uploadDir = public_path('uploads/profile_images/');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            file_put_contents($uploadDir . $fileName, $imageData);
            $updateData['profile_image_url'] = url('uploads/profile_images/' . $fileName);
        }

        $user->update($updateData);

        return $this->successResponse(null, 'Profil berhasil diperbarui.');
    }

    /**
     * PUT /api/password
     * Ganti password dari mobile
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:6',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return $this->errorResponse('Password lama tidak sesuai', 400);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return $this->successResponse(null, 'Password berhasil diubah.');
    }

    // ─── FCM TOKEN ────────────────────────────────────────────────────────────

    /**
     * POST /api/fcm-token
     * Menggantikan: poscare_save_fcm_token.php
     */
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $request->user()->update(['fcm_token' => $request->fcm_token]);

        return $this->successResponse(null, 'FCM token berhasil disimpan.');
    }

    // ─── NOTIFIKASI ───────────────────────────────────────────────────────────

    /**
     * GET /api/notifikasi
     * Menggantikan: poscare_get_notifikasi.php
     */
    public function getNotifikasi()
    {
        $data = DB::table('notifikasi')
            ->select('id', 'judul', 'pesan', 'tipe', 'is_read', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return $this->successResponse($data, 'Notifikasi berhasil dimuat.');
    }

    /**
     * GET /api/notifikasi/unread
     * Menggantikan: poscare_count_unread_notifikasi.php
     */
    public function unreadCount()
    {
        $unread = DB::table('notifikasi')->where('is_read', 0)->count();

        return $this->successResponse(['unread' => $unread], 'Berhasil.');
    }

    /**
     * POST /api/notifikasi/mark-read
     * Menggantikan: poscare_mark_read_notifikasi.php
     */
    public function markRead(Request $request)
    {
        $id = $request->input('id');

        if ($id) {
            DB::table('notifikasi')->where('id', $id)->update(['is_read' => 1]);
        } else {
            DB::table('notifikasi')->where('is_read', 0)->update(['is_read' => 1]);
        }

        return $this->successResponse(null, 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    // ─── DATA BBU WHO ─────────────────────────────────────────────────────────

    /**
     * GET /api/bbu?gender=L&z_score=-2
     * Menggantikan: poscare_get_bbu.php
     */
    public function getBbu(Request $request)
    {
        $request->validate([
            'gender'  => 'required|string|in:L,P',
            'z_score' => 'required|numeric',
        ]);

        $data = DB::table('who_zscore_bbu')
            ->select('usia_bulan', 'berat_badan_kg')
            ->where('jenis_kelamin', $request->gender)
            ->where('z_score', $request->z_score)
            ->orderBy('usia_bulan', 'asc')
            ->get();

        return $this->successResponse($data, 'Data standar BB/U berhasil dimuat.');
    }

    // ─── VAKSIN & IMUNISASI ───────────────────────────────────────────────────

    /**
     * GET /api/vaksin/count
     * Menggantikan: poscare_get_all_vaccines_count_mobile.php
     */
    public function getVaccinesCount()
    {
        $total = MasterVaksin::count();

        return $this->successResponse(
            ['total_vaccines' => $total],
            'Jumlah total vaksin berhasil dimuat.'
        );
    }

    /**
     * GET /api/vaksin/next?id_balita=1
     * Menggantikan: poscare_get_next_vaccine_mobile.php
     */
    public function getNextVaccine(Request $request)
    {
        $request->validate([
            'id_balita' => 'required|integer|exists:anak,id',
        ]);

        $anak      = Anak::aktif()->findOrFail($request->id_balita);
        $umurBulan = Carbon::parse($anak->tanggal_lahir)->diffInMonths(now());

        $next = MasterVaksin::where('usia_minimal_bulan', '<=', $umurBulan)
            ->whereNotIn('id', function ($q) use ($request) {
                $q->select('master_vaksin_id')
                  ->from('imunisasi')
                  ->where('anak_id', $request->id_balita);
            })
            ->orderBy('usia_minimal_bulan', 'asc')
            ->first();

        if ($next) {
            return $this->successResponse([
                'nextVaccine' => [
                    'id'                  => $next->id,
                    'nama_vaksin'         => $next->nama_vaksin,
                    'usia_minimal_bulan'  => $next->usia_minimal_bulan,
                    'usia_maksimal_bulan' => $next->usia_maksimal_bulan,
                    'keterangan'          => $next->keterangan,
                ],
            ], 'Vaksin berikutnya ditemukan.');
        }

        return $this->successResponse(
            ['nextVaccine' => null],
            'Semua vaksin wajib sudah diberikan.'
        );
    }

    /**
     * GET /api/vaksin/history?id_balita=1
     * Menggantikan: poscare_get_vaccine_history_mobile.php
     */
    public function getVaccineHistory(Request $request)
    {
        $request->validate([
            'id_balita' => 'required|integer|exists:anak,id',
        ]);

        $history = DB::table('imunisasi as i')
            ->join('master_vaksin as mv', 'i.master_vaksin_id', '=', 'mv.id')
            ->select(
                DB::raw('CAST(i.id AS CHAR) as imunisasi_id'),
                'i.tanggal',
                'i.umur_bulan',
                'mv.nama_vaksin',
                'mv.keterangan'
            )
            ->where('i.anak_id', $request->id_balita)
            ->whereNotNull('i.tanggal')
            ->orderBy('i.tanggal', 'desc')
            ->get();

        return $this->successResponse(
            $history,
            $history->isEmpty()
                ? 'Anak ini belum memiliki riwayat imunisasi.'
                : 'Riwayat vaksin berhasil dimuat.'
        );
    }
}
