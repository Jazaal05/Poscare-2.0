<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use Carbon\Carbon;
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

        $data = $this->fetchData($category, $start, $end);
        return response()->json(['success' => true, 'data' => $data, 'total' => count($data)]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'category'   => 'nullable|in:lansia,pemeriksaan,pengobatan',
        ]);

        $category = $request->get('category', 'lansia');
        $data     = $this->fetchData($category, $request->start_date, $request->end_date);
        $headers  = $this->getHeaders($category);
        $fileName = "PosCare_Lansia_{$category}_{$request->start_date}_{$request->end_date}.xlsx";

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle(ucfirst($category));

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
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
            $cells = $this->buildRow($category, (array)$row, $num++);
            foreach ($cells as $i => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->setCellValue("{$col}{$rowNum}", $val);
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
            case 'pemeriksaan':
                $q = DB::table('pemeriksaan_lansia as p')
                    ->join('lansia as l', 'p.lansia_id', '=', 'l.id')
                    ->select(['l.nama_lengkap', 'l.nik', 'l.jenis_kelamin',
                              'p.tanggal_periksa', 'p.berat_badan', 'p.tinggi_badan',
                              'p.tekanan_darah', 'p.gula_darah', 'p.asam_urat', 'p.kolesterol', 'p.catatan'])
                    ->where('l.is_deleted', false);
                if ($start && $end) $q->whereBetween('p.tanggal_periksa', [$start, $end]);
                return $q->orderBy('p.tanggal_periksa', 'desc')->get()->toArray();

            case 'pengobatan':
                $q = DB::table('pengobatan_lansia as po')
                    ->join('lansia as l', 'po.lansia_id', '=', 'l.id')
                    ->select(['l.nama_lengkap', 'l.nik', 'po.tanggal',
                              'po.ada_keluhan', 'po.keluhan', 'po.obat_diberikan',
                              'po.vitamin_diberikan', 'po.catatan'])
                    ->where('l.is_deleted', false);
                if ($start && $end) $q->whereBetween('po.tanggal', [$start, $end]);
                return $q->orderBy('po.tanggal', 'desc')->get()->toArray();

            default: // lansia
                $q = DB::table('lansia as l')
                    ->select(['l.id', 'l.nik', 'l.nama_lengkap', 'l.jenis_kelamin',
                              'l.tanggal_lahir', 'l.tempat_lahir', 'l.alamat', 'l.rt_rw',
                              'l.no_hp', 'l.nama_wali', 'l.hubungan_wali',
                              DB::raw('TIMESTAMPDIFF(YEAR, l.tanggal_lahir, CURDATE()) as umur')])
                    ->where('l.is_deleted', false);
                return $q->orderBy('l.nama_lengkap')->get()->toArray();
        }
    }

    private function getHeaders(string $category): array
    {
        return match ($category) {
            'pemeriksaan' => ['No','Nama','NIK','JK','Tgl Periksa','BB (kg)','TB (cm)','Tekanan Darah','Gula Darah','Asam Urat','Kolesterol','Catatan'],
            'pengobatan'  => ['No','Nama','NIK','Tanggal','Ada Keluhan','Keluhan','Obat','Vitamin','Catatan'],
            default       => ['No','Nama Lengkap','NIK','JK','Tgl Lahir','Umur','Tempat Lahir','Alamat','RT/RW','No HP','Nama Wali','Hub. Wali'],
        };
    }

    private function buildRow(string $category, array $r, int $num): array
    {
        return match ($category) {
            'pemeriksaan' => [$num, $r['nama_lengkap']??'-', $r['nik']??'-', $r['jenis_kelamin']??'-',
                              $r['tanggal_periksa']??'-', $r['berat_badan']??'-', $r['tinggi_badan']??'-',
                              $r['tekanan_darah']??'-', $r['gula_darah']??'-', $r['asam_urat']??'-',
                              $r['kolesterol']??'-', $r['catatan']??'-'],
            'pengobatan'  => [$num, $r['nama_lengkap']??'-', $r['nik']??'-', $r['tanggal']??'-',
                              ($r['ada_keluhan'] ? 'Ya' : 'Tidak'),
                              is_string($r['keluhan']) ? implode(', ', json_decode($r['keluhan'], true) ?? []) : '-',
                              is_string($r['obat_diberikan']) ? implode(', ', json_decode($r['obat_diberikan'], true) ?? []) : '-',
                              is_string($r['vitamin_diberikan']) ? implode(', ', json_decode($r['vitamin_diberikan'], true) ?? []) : '-',
                              $r['catatan']??'-'],
            default       => [$num, $r['nama_lengkap']??'-', $r['nik']??'-', $r['jenis_kelamin']??'-',
                              $r['tanggal_lahir']??'-', $r['umur']??'-', $r['tempat_lahir']??'-',
                              $r['alamat']??'-', $r['rt_rw']??'-', $r['no_hp']??'-',
                              $r['nama_wali']??'-', $r['hubungan_wali']??'-'],
        };
    }
}
