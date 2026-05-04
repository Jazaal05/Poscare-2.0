<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Jadwal;
use App\Models\Imunisasi;
use App\Models\RiwayatPengukuran;
use App\Models\MasterVaksin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard
     * Menggantikan: pages/dashboard.php
     */
    public function index()
    {
        $user = Auth::user();
        $stats = $this->getStats();

        return view('dashboard.index', compact('user', 'stats'));
    }

    /**
     * API endpoint untuk statistik dashboard
     * Menggantikan: api_web/dashboard_stats.php
     */
    public function stats()
    {
        try {
            $stats     = $this->getStats();
            $chart     = $this->getChartData();
            $notif     = $this->getNotifications();

            return response()->json([
                'success' => true,
                'data'    => [
                    'stats'         => $stats,
                    'chart'         => $chart,
                    'notifications' => $notif,
                ],
                'message'   => 'Data statistik berhasil dimuat',
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =============================================
    // PRIVATE HELPERS
    // =============================================

    private function getStats(): array
    {
        $totalAnak = Anak::aktif()->count();

        // Hitung status gizi dari riwayat pengukuran terakhir
        $statusCounts = $this->hitungStatusGizi();

        $totalJadwal = Jadwal::count();

        return [
            'total_anak'      => $totalAnak,
            'gizi_baik'       => $statusCounts['gizi_baik'],
            'stunting'        => $statusCounts['stunting'],
            'risiko_stunting' => $statusCounts['risiko_stunting'],
            'jadwal_bulan_ini'=> $totalJadwal,
            'belum_diukur'    => $statusCounts['belum_diukur'],
        ];
    }

    private function getChartData(): array
    {
        $statusCounts = $this->hitungStatusGizi();

        return [
            'gizi_baik'           => $statusCounts['gizi_baik'],
            'stunting'            => $statusCounts['stunting'],
            'risiko_stunting'     => $statusCounts['risiko_stunting'],
            'gizi_kurang'         => $statusCounts['gizi_kurang'],
            'beresiko_gizi_kurang'=> $statusCounts['beresiko_gizi_kurang'],
            'beresiko_gizi_lebih' => $statusCounts['beresiko_gizi_lebih'],
            'gizi_lebih'          => $statusCounts['gizi_lebih'],
            'obesitas'            => $statusCounts['obesitas'],
        ];
    }

    private function getNotifications(): array
    {
        $totalAnak   = Anak::aktif()->count();
        $bulan       = now()->month;
        $tahun       = now()->year;

        // Hitung anak yang sudah pernah imunisasi (total keseluruhan, bukan hanya bulan ini)
        $anakSudahImunisasi = DB::table('imunisasi')
            ->whereNotNull('tanggal')
            ->distinct()
            ->count('anak_id');

        // Anak yang imunisasi bulan ini saja (untuk informasi tambahan)
        $immunizedThisMonth = DB::table('imunisasi')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereNotNull('tanggal')
            ->distinct()
            ->count('anak_id');

        $belumImunisasi = max(0, $totalAnak - $anakSudahImunisasi);
        $immunizationProgress = $totalAnak > 0
            ? round(($anakSudahImunisasi / $totalAnak) * 100)
            : 0;

        // Detail per vaksin - hitung anak yang BELUM mendapat vaksin tertentu
        $vaccines    = MasterVaksin::all();
        $vaccineList = [];

        foreach ($vaccines as $vaccine) {
            $key = strtolower(str_replace([' ', '-'], '_', $vaccine->nama_vaksin));

            // Hitung anak yang sudah dapat vaksin ini
            $vaccinated = DB::table('imunisasi')
                ->where('master_vaksin_id', $vaccine->id)
                ->whereNotNull('tanggal')
                ->distinct()
                ->count('anak_id');

            $notVaccinated = max(0, $totalAnak - $vaccinated);

            $vaccineList[] = [
                'key'           => $key,
                'label'         => strtoupper($vaccine->nama_vaksin),
                'not_vaccinated'=> $notVaccinated,
                'vaccinated'    => $vaccinated,
                'id'            => $vaccine->id,
            ];
        }

        $statusCounts    = $this->hitungStatusGizi();
        $needAttention   = $statusCounts['stunting'] + $statusCounts['gizi_kurang'];
        $daysUntilDeadline = now()->diffInDays(now()->endOfMonth());

        return [
            'immunization' => [
                'progress_percent'    => $immunizationProgress,
                'immunized_this_month'=> $immunizedThisMonth,
                'total_anak'          => $totalAnak,
                'need_immunization'   => $belumImunisasi,
                'anak_sudah_imunisasi'=> $anakSudahImunisasi,
                'vaccine_list'        => $vaccineList,
            ],
            'need_attention'  => $needAttention,
            'deadline_days'   => $daysUntilDeadline,
        ];
    }

    private function hitungStatusGizi(): array
    {
        $counts = [
            'gizi_baik'            => 0,
            'stunting'             => 0,
            'risiko_stunting'      => 0,
            'gizi_kurang'          => 0,
            'beresiko_gizi_kurang' => 0,
            'beresiko_gizi_lebih'  => 0,
            'gizi_lebih'           => 0,
            'obesitas'             => 0,
            'belum_diukur'         => 0,
        ];

        // Ambil pengukuran terakhir per anak
        $latestMeasurements = DB::table('riwayat_pengukuran as rp')
            ->join(DB::raw('(SELECT anak_id, MAX(id) as max_id FROM riwayat_pengukuran GROUP BY anak_id) as latest'), function ($join) {
                $join->on('rp.anak_id', '=', 'latest.anak_id')
                     ->on('rp.id', '=', 'latest.max_id');
            })
            ->join('anak', 'rp.anak_id', '=', 'anak.id')
            ->where('anak.is_deleted', 0)
            ->select('rp.overall_8')
            ->get();

        // Anak tanpa pengukuran
        $anakDenganPengukuran = $latestMeasurements->count();
        $totalAnak            = Anak::aktif()->count();
        $counts['belum_diukur'] = max(0, $totalAnak - $anakDenganPengukuran);

        foreach ($latestMeasurements as $row) {
            $kategori = strtolower(str_replace(' ', '_', $row->overall_8 ?? ''));

            switch ($kategori) {
                case 'gizi_baik':            $counts['gizi_baik']++;            break;
                case 'stunting':             $counts['stunting']++;             break;
                case 'risiko_stunting':
                case 'resiko_stunting':      $counts['risiko_stunting']++;      break;
                case 'gizi_kurang':          $counts['gizi_kurang']++;          break;
                case 'beresiko_gizi_kurang':
                case 'berisiko_gizi_kurang': $counts['beresiko_gizi_kurang']++; break;
                case 'beresiko_gizi_lebih':
                case 'berisiko_gizi_lebih':  $counts['beresiko_gizi_lebih']++;  break;
                case 'gizi_lebih':           $counts['gizi_lebih']++;           break;
                case 'obesitas':             $counts['obesitas']++;             break;
                default:                     $counts['belum_diukur']++;         break;
            }
        }

        return $counts;
    }
}
