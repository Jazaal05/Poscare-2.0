<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    // =============================================
    // API: List data laporan (JSON untuk preview)
    // =============================================
    public function list(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');
        $category  = $request->get('category', 'all');

        $allowedCategories = ['all', 'anak', 'imunisasi', 'pertumbuhan', 'stunting'];
        if (!in_array($category, $allowedCategories)) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak valid'], 400);
        }

        try {
            $data = $this->fetchData($category, $startDate, $endDate);

            return response()->json([
                'success'   => true,
                'data'      => $data,
                'total'     => count($data),
                'category'  => $category,
                'timestamp' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // =============================================
    // EXPORT EXCEL (.xlsx menggunakan PhpSpreadsheet)
    // =============================================
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'category'   => 'nullable|in:all,anak,imunisasi,pertumbuhan,stunting',
        ]);

        $startDate = $request->start_date;
        $endDate   = $request->end_date;
        $category  = $request->get('category', 'all');

        $categoryLabel = [
            'all'         => 'Semua_Data',
            'anak'        => 'Data_Anak',
            'imunisasi'   => 'Data_Imunisasi',
            'pertumbuhan' => 'Data_Pertumbuhan',
            'stunting'    => 'Data_Stunting',
        ];

        $data    = $this->fetchData($category, $startDate, $endDate);
        $headers = $this->getHeaders($category);
        $fileName = "PosCare_{$categoryLabel[$category]}_{$startDate}_{$endDate}.xlsx";

        // Buat spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($category));

        // Style header
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '246BCE']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Tulis header
        foreach ($headers as $colIdx => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
            $sheet->setCellValue("{$col}1", $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);

        // Tulis data
        $rowNum = 2;
        $num    = 1;
        foreach ($data as $row) {
            $cells = $this->buildRow($category, $row, $num++);
            foreach ($cells as $colIdx => $value) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue("{$col}{$rowNum}", $value);
            }
            // Zebra striping
            if ($rowNum % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }
            $rowNum++;
        }

        // Freeze header row
        $sheet->freezePane('A2');

        // Auto-filter
        $sheet->setAutoFilter("A1:{$lastCol}1");

        // Stream ke browser
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    // =============================================
    // PRIVATE HELPERS
    // =============================================

    private function fetchData(string $category, ?string $startDate, ?string $endDate): array
    {
        switch ($category) {
            case 'all':
            case 'anak':
                return $this->fetchAnak($startDate, $endDate);

            case 'imunisasi':
                return $this->fetchImunisasi($startDate, $endDate);

            case 'pertumbuhan':
                return $this->fetchPertumbuhan($startDate, $endDate);

            case 'stunting':
                return $this->fetchStunting($startDate, $endDate);

            default:
                return [];
        }
    }

    private function fetchAnak(?string $startDate, ?string $endDate): array
    {
        $query = DB::table('anak as a')
            ->select([
                'a.id', 'a.nama_anak', 'a.nik_anak', 'a.jenis_kelamin',
                'a.tanggal_lahir', 'a.tempat_lahir', 'a.anak_ke',
                'a.berat_badan', 'a.tinggi_badan', 'a.lingkar_kepala',
                'a.tanggal_penimbangan_terakhir', 'a.status_gizi',
                'a.nama_ibu', 'a.nik_ibu', 'a.nama_ayah', 'a.nik_ayah',
                'a.hp_kontak_ortu', 'a.nama_kk', 'a.alamat_domisili', 'a.rt_rw',
                DB::raw('TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) as umur_bulan'),
            ])
            ->where('a.is_deleted', 0);

        // Filter tanggal berdasarkan tanggal_penimbangan_terakhir jika diisi
        // Jika tidak ada data penimbangan di periode itu, tetap tampilkan semua anak
        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('a.tanggal_penimbangan_terakhir', [$startDate, $endDate])
                  ->orWhereNull('a.tanggal_penimbangan_terakhir');
            });
        }

        return $query->orderBy('a.nama_anak')->get()->toArray();
    }

    private function fetchImunisasi(?string $startDate, ?string $endDate): array
    {
        $query = DB::table('imunisasi as i')
            ->join('anak as a', 'i.anak_id', '=', 'a.id')
            ->leftJoin('master_vaksin as mv', 'i.master_vaksin_id', '=', 'mv.id')
            ->select([
                'a.nama_anak', 'a.nik_anak', 'a.jenis_kelamin', 'a.tanggal_lahir',
                'mv.nama_vaksin',
                'i.tanggal as tanggal_imunisasi',
                DB::raw('TIMESTAMPDIFF(MONTH, a.tanggal_lahir, i.tanggal) as umur_saat_imunisasi'),
            ])
            ->where('a.is_deleted', 0)
            ->whereNotNull('i.tanggal');

        if ($startDate && $endDate) {
            $query->whereBetween('i.tanggal', [$startDate, $endDate]);
        }

        return $query->orderBy('i.tanggal', 'desc')->orderBy('a.nama_anak')->get()->toArray();
    }

    private function fetchPertumbuhan(?string $startDate, ?string $endDate): array
    {
        $query = DB::table('riwayat_pengukuran as rp')
            ->join('anak as a', 'rp.anak_id', '=', 'a.id')
            ->select([
                'a.nama_anak', 'a.nik_anak', 'a.jenis_kelamin', 'a.tanggal_lahir',
                'rp.tanggal_ukur', 'rp.umur_bulan',
                'rp.bb_kg', 'rp.tb_pb_cm', 'rp.lk_cm', 'rp.cara_ukur',
                'rp.z_bbu', 'rp.z_tbu', 'rp.z_bbtb', 'rp.z_imtu',
                'rp.overall_8 as status_gizi',
                'a.nama_ibu', 'a.alamat_domisili',
            ])
            ->where('a.is_deleted', 0);

        if ($startDate && $endDate) {
            $query->whereBetween('rp.tanggal_ukur', [$startDate, $endDate]);
        }

        return $query->orderBy('rp.tanggal_ukur', 'desc')->orderBy('a.nama_anak')->get()->toArray();
    }

    private function fetchStunting(?string $startDate, ?string $endDate): array
    {
        $query = DB::table('anak as a')
            ->select([
                'a.nama_anak', 'a.nik_anak', 'a.jenis_kelamin', 'a.tanggal_lahir',
                'a.tinggi_badan', 'a.berat_badan', 'a.status_gizi',
                'a.tanggal_penimbangan_terakhir',
                DB::raw('TIMESTAMPDIFF(MONTH, a.tanggal_lahir, CURDATE()) as umur_bulan'),
                'a.nama_ibu', 'a.nik_ibu', 'a.hp_kontak_ortu', 'a.alamat_domisili', 'a.rt_rw',
            ])
            ->where('a.is_deleted', 0)
            ->where(function ($q) {
                $q->where('a.status_gizi', 'like', '%stunting%')
                  ->orWhere('a.status_gizi', 'like', '%pendek%')
                  ->orWhere('a.status_gizi', 'like', '%kurang%');
            });

        // Filter tanggal berdasarkan created_at atau penimbangan terakhir
        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('a.tanggal_penimbangan_terakhir', [$startDate, $endDate])
                  ->orWhereNull('a.tanggal_penimbangan_terakhir');
            });
        }

        return $query->orderBy('a.status_gizi')->orderBy('a.tanggal_penimbangan_terakhir', 'desc')->get()->toArray();
    }

    private function getHeaders(string $category): array
    {
        return match ($category) {
            'imunisasi'   => ['No', 'Nama Anak', 'NIK', 'JK', 'Tanggal Lahir', 'Nama Vaksin', 'Tanggal Imunisasi', 'Umur Saat Imunisasi (bln)'],
            'pertumbuhan' => ['No', 'Nama Anak', 'NIK', 'JK', 'Tanggal Lahir', 'Tanggal Ukur', 'Umur (bln)', 'BB (kg)', 'TB (cm)', 'LK (cm)', 'Z BB/U', 'Z TB/U', 'Z BB/TB', 'Z IMT/U', 'Status Gizi'],
            'stunting'    => ['No', 'Nama Anak', 'NIK', 'Umur (bln)', 'TB (cm)', 'BB (kg)', 'Status Gizi', 'Nama Ibu', 'No HP', 'Alamat'],
            default       => ['No', 'Nama Anak', 'NIK', 'JK', 'Tanggal Lahir', 'Umur (bln)', 'BB (kg)', 'TB (cm)', 'LK (cm)', 'Status Gizi', 'Nama Ibu', 'Nama Ayah', 'Alamat'],
        };
    }

    private function buildRow(string $category, object $row, int $num): array
    {
        $r = (array) $row;

        return match ($category) {
            'imunisasi' => [
                $num,
                $r['nama_anak'] ?? '-',
                $r['nik_anak'] ?? '-',
                $r['jenis_kelamin'] ?? '-',
                $r['tanggal_lahir'] ?? '-',
                $r['nama_vaksin'] ?? '-',
                $r['tanggal_imunisasi'] ?? '-',
                $r['umur_saat_imunisasi'] ?? '-',
            ],
            'pertumbuhan' => [
                $num,
                $r['nama_anak'] ?? '-',
                $r['nik_anak'] ?? '-',
                $r['jenis_kelamin'] ?? '-',
                $r['tanggal_lahir'] ?? '-',
                $r['tanggal_ukur'] ?? '-',
                $r['umur_bulan'] ?? '-',
                $r['bb_kg'] ?? '-',
                $r['tb_pb_cm'] ?? '-',
                $r['lk_cm'] ?? '-',
                $r['z_bbu'] ?? '-',
                $r['z_tbu'] ?? '-',
                $r['z_bbtb'] ?? '-',
                $r['z_imtu'] ?? '-',
                $r['status_gizi'] ?? '-',
            ],
            'stunting' => [
                $num,
                $r['nama_anak'] ?? '-',
                $r['nik_anak'] ?? '-',
                $r['umur_bulan'] ?? '-',
                $r['tinggi_badan'] ?? '-',
                $r['berat_badan'] ?? '-',
                $r['status_gizi'] ?? '-',
                $r['nama_ibu'] ?? '-',
                $r['hp_kontak_ortu'] ?? '-',
                $r['alamat_domisili'] ?? '-',
            ],
            default => [
                $num,
                $r['nama_anak'] ?? '-',
                $r['nik_anak'] ?? '-',
                $r['jenis_kelamin'] ?? '-',
                $r['tanggal_lahir'] ?? '-',
                $r['umur_bulan'] ?? '-',
                $r['berat_badan'] ?? '-',
                $r['tinggi_badan'] ?? '-',
                $r['lingkar_kepala'] ?? '-',
                $r['status_gizi'] ?? 'Belum diukur',
                $r['nama_ibu'] ?? '-',
                $r['nama_ayah'] ?? '-',
                $r['alamat_domisili'] ?? '-',
            ],
        };
    }
}
