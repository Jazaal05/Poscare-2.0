<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LansiaLaporanController extends Controller
{
    public function index()
    {
        return view('lansia.laporan.index');
    }

    public function list(Request $request)
    {
        $start    = $request->get('start_date');
        $end      = $request->get('end_date');
        $category = $request->get('category', 'lansia');
        $data     = $this->fetchData($category, $start, $end);
        return response()->json(['success' => true, 'data' => $data, 'total' => count($data)]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'category'   => 'nullable|in:lansia,kunjungan,tidak_normal',
        ]);

        $category = $request->get('category', 'lansia');
        $data     = $this->fetchData($category, $request->start_date, $request->end_date);
        $headers  = $this->getHeaders($category);
        $catMap   = ['lansia' => 'Data_Lansia', 'kunjungan' => 'Data_Kunjungan', 'tidak_normal' => 'Kondisi_Tidak_Normal'];
        $fileName = "PosCare_Lansia_{$catMap[$category]}_{$request->start_date}_{$request->end_date}.xlsx";

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($category));

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        foreach ($headers as $i => $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}1", $h);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray($headerStyle);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$lastCol}1");

        $rowNum = 2;
        $num    = 1;
        foreach ($data as $row) {
            $cells = $this->buildRow($category, (array) $row, $num++);
            foreach ($cells as $i => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$col}{$rowNum}", $val);
            }
            // Zebra striping
            if ($rowNum % 2 === 0) {
                $sheet->getStyle("A{$rowNum}:{$lastCol}{$rowNum}")
                    ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F0FDF4');
            }
            $rowNum++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        return response()->stream(fn() => $writer->save('php://output'), 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function fetchData(string $category, ?string $start, ?string $end): array
    {
        switch ($category) {
            case 'kunjungan':
                $q = DB::table('kunjungan_lansia as k')
                    ->join('lansia as l', 'k.lansia_id', '=', 'l.id')
                    ->select([
                        'l.nama_lengkap', 'l.nik', 'l.jenis_kelamin',
                        'k.tanggal_kunjungan', 'k.berat_badan',
                        'k.tekanan_darah', 'k.status_tensi',
                        'k.gula_darah', 'k.status_gula',
                        'k.kolesterol', 'k.status_kolesterol',
                        'k.asam_urat', 'k.status_asam_urat',
                        'k.ada_keluhan', 'k.keluhan',
                        'k.obat_diberikan', 'k.vitamin_diberikan',
                        'k.catatan_bidan',
                    ])
                    ->where('l.is_deleted', false);
                if ($start && $end) $q->whereBetween('k.tanggal_kunjungan', [$start, $end]);
                return $q->orderBy('k.tanggal_kunjungan', 'desc')->get()->toArray();

            case 'tidak_normal':
                $q = DB::table('kunjungan_lansia as k')
                    ->join('lansia as l', 'k.lansia_id', '=', 'l.id')
                    ->select([
                        'l.nama_lengkap', 'l.nik', 'l.no_hp', 'l.alamat',
                        'k.tanggal_kunjungan',
                        'k.tekanan_darah', 'k.status_tensi',
                        'k.gula_darah', 'k.status_gula',
                        'k.kolesterol', 'k.status_kolesterol',
                        'k.asam_urat', 'k.status_asam_urat',
                        'k.keluhan', 'k.obat_diberikan',
                    ])
                    ->where('l.is_deleted', false)
                    ->where(function ($q) {
                        $q->whereIn('k.status_tensi', ['hipertensi1', 'hipertensi2'])
                          ->orWhereIn('k.status_gula', ['tinggi', 'sangat_tinggi'])
                          ->orWhere('k.status_kolesterol', 'tinggi')
                          ->orWhere('k.status_asam_urat', 'tinggi');
                    });
                if ($start && $end) $q->whereBetween('k.tanggal_kunjungan', [$start, $end]);
                return $q->orderBy('k.tanggal_kunjungan', 'desc')->get()->toArray();

            default: // lansia
                $q = DB::table('lansia as l')
                    ->select(['l.nik', 'l.nama_lengkap', 'l.jenis_kelamin', 'l.tanggal_lahir',
                              'l.tempat_lahir', 'l.alamat', 'l.rt_rw', 'l.no_hp',
                              'l.nama_wali', 'l.hubungan_wali',
                              DB::raw('TIMESTAMPDIFF(YEAR, l.tanggal_lahir, CURDATE()) as umur')])
                    ->where('l.is_deleted', false);
                return $q->orderBy('l.nama_lengkap')->get()->toArray();
        }
    }

    private function getHeaders(string $category): array
    {
        return match ($category) {
            'kunjungan'    => ['No','Nama','NIK','JK','Tgl Kunjungan','BB (kg)','Tensi','Status Tensi','GD (mg/dL)','Status GD','Kol (mg/dL)','Status Kol','AU (mg/dL)','Status AU','Ada Keluhan','Keluhan','Obat','Vitamin','Catatan'],
            'tidak_normal' => ['No','Nama','NIK','No HP','Alamat','Tgl Kunjungan','Tensi','Status Tensi','GD','Status GD','Kol','Status Kol','AU','Status AU','Keluhan','Obat'],
            default        => ['No','Nama Lengkap','NIK','JK','Tgl Lahir','Umur','Tempat Lahir','Alamat','RT/RW','No HP','Nama Wali','Hub. Wali'],
        };
    }

    private function buildRow(string $category, array $r, int $num): array
    {
        $decodeJson = fn($v) => is_string($v) ? implode(', ', json_decode($v, true) ?? []) : '-';

        return match ($category) {
            'kunjungan' => [
                $num, $r['nama_lengkap']??'-', $r['nik']??'-', $r['jenis_kelamin']??'-',
                $r['tanggal_kunjungan']??'-', $r['berat_badan']??'-',
                $r['tekanan_darah']??'-', $r['status_tensi']??'-',
                $r['gula_darah']??'-', $r['status_gula']??'-',
                $r['kolesterol']??'-', $r['status_kolesterol']??'-',
                $r['asam_urat']??'-', $r['status_asam_urat']??'-',
                ($r['ada_keluhan'] ? 'Ya' : 'Tidak'), $r['keluhan']??'-',
                $decodeJson($r['obat_diberikan']??null),
                $decodeJson($r['vitamin_diberikan']??null),
                $r['catatan_bidan']??'-',
            ],
            'tidak_normal' => [
                $num, $r['nama_lengkap']??'-', $r['nik']??'-', $r['no_hp']??'-', $r['alamat']??'-',
                $r['tanggal_kunjungan']??'-',
                $r['tekanan_darah']??'-', $r['status_tensi']??'-',
                $r['gula_darah']??'-', $r['status_gula']??'-',
                $r['kolesterol']??'-', $r['status_kolesterol']??'-',
                $r['asam_urat']??'-', $r['status_asam_urat']??'-',
                $r['keluhan']??'-', $decodeJson($r['obat_diberikan']??null),
            ],
            default => [
                $num, $r['nama_lengkap']??'-', $r['nik']??'-', $r['jenis_kelamin']??'-',
                $r['tanggal_lahir']??'-', $r['umur']??'-', $r['tempat_lahir']??'-',
                $r['alamat']??'-', $r['rt_rw']??'-', $r['no_hp']??'-',
                $r['nama_wali']??'-', $r['hubungan_wali']??'-',
            ],
        };
    }
}
