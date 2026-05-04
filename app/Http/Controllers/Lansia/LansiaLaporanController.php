<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LansiaLaporanController extends Controller
{
    public function index()
    {
        return view('lansia.laporan.index');
    }

    // ── API: Statistik untuk dashboard laporan ────────────────
    public function stats()
    {
        $totalLansia = Lansia::aktif()->count();
        $totalLaki   = Lansia::aktif()->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Lansia::aktif()->where('jenis_kelamin', 'P')->count();
        $rataUsia    = round(Lansia::aktif()->get()->avg(fn($l) => $l->umur) ?? 0, 1);

        return response()->json([
            'success' => true,
            'data' => [
                'total_lansia'   => $totalLansia,
                'total_laki'     => $totalLaki,
                'total_perempuan'=> $totalPerempuan,
                'rata_usia'      => $rataUsia,
            ],
        ]);
    }

    // ── API: Export ke Excel ───────────────────────────────────
    public function exportExcel(Request $request)
    {
        $query = Lansia::aktif();

        // Filter berdasarkan tanggal pemeriksaan terakhir
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai) {
            $query->where('tanggal_pemeriksaan_terakhir', '>=', $request->tanggal_mulai);
        }

        if ($request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $query->where('tanggal_pemeriksaan_terakhir', '<=', $request->tanggal_akhir);
        }

        // Filter berdasarkan jenis kelamin
        if ($request->has('jenis_kelamin') && $request->jenis_kelamin) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }

        $data = $query->orderBy('nama_lengkap')->get();

        // Buat spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set judul
        $sheet->setCellValue('A1', 'LAPORAN DATA LANSIA');
        $sheet->mergeCells('A1:T1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set info tanggal export
        $sheet->setCellValue('A2', 'Tanggal Export: ' . date('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:T2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set header tabel
        $headers = [
            'No', 'Nama Lengkap', 'NIK', 'Jenis Kelamin', 'Tanggal Lahir', 'Tempat Lahir', 
            'Usia', 'Alamat', 'RT/RW', 'Nama KK', 'Nama Wali', 'NIK Wali', 'No HP Wali',
            'Berat Badan (kg)', 'Tinggi Badan (cm)', 'BMI', 'Tekanan Darah', 
            'Gula Darah (mg/dL)', 'Kolesterol (mg/dL)', 'Asam Urat (mg/dL)', 
            'Status Kesehatan', 'Tanggal Pemeriksaan'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A4:V4')->applyFromArray($headerStyle);

        // Set data
        $row = 5;
        $no = 1;
        foreach ($data as $lansia) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $lansia->nama_lengkap);
            $sheet->setCellValue('C' . $row, $lansia->nik_lansia ?: '-');
            $sheet->setCellValue('D' . $row, $lansia->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $lansia->tanggal_lahir ? $lansia->tanggal_lahir->format('d/m/Y') : '-');
            $sheet->setCellValue('F' . $row, $lansia->tempat_lahir ?: '-');
            $sheet->setCellValue('G' . $row, $lansia->umur . ' tahun');
            $sheet->setCellValue('H' . $row, $lansia->alamat_domisili ?: '-');
            $sheet->setCellValue('I' . $row, $lansia->rt_rw ?: '-');
            $sheet->setCellValue('J' . $row, $lansia->nama_kk ?: '-');
            $sheet->setCellValue('K' . $row, $lansia->nama_wali ?: '-');
            $sheet->setCellValue('L' . $row, $lansia->nik_wali ?: '-');
            $sheet->setCellValue('M' . $row, $lansia->hp_kontak_wali ?: '-');
            $sheet->setCellValue('N' . $row, $lansia->berat_badan ?: '-');
            $sheet->setCellValue('O' . $row, $lansia->tinggi_badan ?: '-');
            $sheet->setCellValue('P' . $row, $lansia->bmi ?: '-');
            $sheet->setCellValue('Q' . $row, $lansia->tekanan_darah ?: '-');
            $sheet->setCellValue('R' . $row, $lansia->gula_darah ?: '-');
            $sheet->setCellValue('S' . $row, $lansia->kolesterol ?: '-');
            $sheet->setCellValue('T' . $row, $lansia->asam_urat ?: '-');
            $sheet->setCellValue('U' . $row, $lansia->status_kesehatan ?: 'Belum diperiksa');
            $sheet->setCellValue('V' . $row, $lansia->tanggal_pemeriksaan_terakhir ? $lansia->tanggal_pemeriksaan_terakhir->format('d/m/Y') : '-');

            $row++;
        }

        // Style data
        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A4:V' . ($row - 1))->applyFromArray($dataStyle);

        // Auto size columns
        foreach (range('A', 'V') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height
        $sheet->getRowDimension('1')->setRowHeight(30);
        $sheet->getRowDimension('4')->setRowHeight(25);

        // Generate file
        $writer = new Xlsx($spreadsheet);
        $filename = 'Laporan_Lansia_' . date('Y-m-d_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
