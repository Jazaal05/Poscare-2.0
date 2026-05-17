<?php

namespace App\Http\Controllers\Lansia;

use App\Http\Controllers\Controller;
use App\Models\Lansia;
use App\Models\KunjunganLansia;
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

    // ── API: Statistik ringkas ─────────────────────────────────
    public function stats()
    {
        $totalLansia    = Lansia::aktif()->count();
        $totalLaki      = Lansia::aktif()->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Lansia::aktif()->where('jenis_kelamin', 'P')->count();
        $rataUsia       = round(Lansia::aktif()->get()->avg(fn($l) => $l->umur) ?? 0, 1);

        return response()->json([
            'success' => true,
            'data' => [
                'total_lansia'    => $totalLansia,
                'total_laki'      => $totalLaki,
                'total_perempuan' => $totalPerempuan,
                'rata_usia'       => $rataUsia,
            ],
        ]);
    }

    // ── Export Excel: 12 sheet per bulan ──────────────────────
    public function exportExcel(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default

        $namaBulan = [
            1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
            4  => 'April',     5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',      8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',   11 => 'November',  12 => 'Desember',
        ];

        // Header kolom kunjungan
        $headers = [
            'No', 'Nama Lansia', 'NIK', 'Jenis Kelamin', 'Usia',
            'Tanggal Kunjungan',
            'Berat Badan (kg)', 'Tinggi Badan (cm)', 'Tekanan Darah',
            'Status Tensi', 'Gula Darah (mg/dL)', 'Status Gula',
            'Kolesterol (mg/dL)', 'Status Kolesterol',
            'Asam Urat (mg/dL)', 'Status Asam Urat',
            'Ada Keluhan', 'Keluhan', 'Obat Diberikan', 'Vitamin Diberikan',
            'Catatan Bidan',
        ];

        $labelTensi = [
            'normal'        => 'Normal',
            'prehipertensi' => 'Prehipertensi',
            'hipertensi1'   => 'Hipertensi I',
            'hipertensi2'   => 'Hipertensi II',
        ];
        $labelGula = [
            'rendah'       => 'Rendah',
            'normal'       => 'Normal',
            'tinggi'       => 'Tinggi',
            'sangat_tinggi'=> 'Sangat Tinggi',
        ];
        $labelKolesterol = [
            'normal' => 'Normal',
            'batas'  => 'Batas',
            'tinggi' => 'Tinggi',
        ];
        $labelAsamUrat = [
            'normal' => 'Normal',
            'tinggi' => 'Tinggi',
        ];

        foreach ($namaBulan as $bulan => $nama) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($nama);

            // ── Judul sheet ──────────────────────────────────
            $lastCol = 'U'; // kolom terakhir (21 kolom)
            $sheet->setCellValue('A1', "LAPORAN KUNJUNGAN LANSIA - {$nama} {$tahun}");
            $sheet->mergeCells("A1:{$lastCol}1");
            $sheet->getStyle('A1')->applyFromArray([
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getRowDimension(1)->setRowHeight(28);

            $sheet->setCellValue('A2', 'Diekspor pada: ' . date('d/m/Y H:i'));
            $sheet->mergeCells("A2:{$lastCol}2");
            $sheet->getStyle('A2')->applyFromArray([
                'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // ── Header kolom ─────────────────────────────────
            $col = 'A';
            foreach ($headers as $h) {
                $sheet->setCellValue($col . '3', $h);
                $col++;
            }
            $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getRowDimension(3)->setRowHeight(22);

            // ── Data kunjungan bulan ini ──────────────────────
            $kunjungan = KunjunganLansia::with('lansia')
                ->whereMonth('tanggal_kunjungan', $bulan)
                ->whereYear('tanggal_kunjungan', $tahun)
                ->orderBy('tanggal_kunjungan')
                ->get();

            if ($kunjungan->isEmpty()) {
                // Baris kosong dengan keterangan
                $sheet->setCellValue('A4', 'Tidak ada data kunjungan pada bulan ini');
                $sheet->mergeCells("A4:{$lastCol}4");
                $sheet->getStyle('A4')->applyFromArray([
                    'font'      => ['italic' => true, 'color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                ]);
            } else {
                $row = 4;
                $no  = 1;
                foreach ($kunjungan as $k) {
                    $l = $k->lansia;
                    $obat    = is_array($k->obat_diberikan)    ? implode(', ', $k->obat_diberikan)    : ($k->obat_diberikan    ?: '-');
                    $vitamin = is_array($k->vitamin_diberikan) ? implode(', ', $k->vitamin_diberikan) : ($k->vitamin_diberikan ?: '-');

                    $sheet->setCellValue('A' . $row, $no++);
                    $sheet->setCellValue('B' . $row, $l?->nama_lengkap ?: '-');
                    $sheet->setCellValue('C' . $row, $l?->nik_lansia   ?: '-');
                    $sheet->setCellValue('D' . $row, $l?->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan');
                    $sheet->setCellValue('E' . $row, $l ? ($l->umur . ' tahun') : '-');
                    $sheet->setCellValue('F' . $row, $k->tanggal_kunjungan?->format('d/m/Y') ?: '-');
                    $sheet->setCellValue('G' . $row, $k->berat_badan    ?: '-');
                    $sheet->setCellValue('H' . $row, $k->tinggi_badan   ?: '-');
                    $sheet->setCellValue('I' . $row, $k->tekanan_darah  ?: '-');
                    $sheet->setCellValue('J' . $row, $labelTensi[$k->status_tensi]           ?? '-');
                    $sheet->setCellValue('K' . $row, $k->gula_darah     ?: '-');
                    $sheet->setCellValue('L' . $row, $labelGula[$k->status_gula]             ?? '-');
                    $sheet->setCellValue('M' . $row, $k->kolesterol     ?: '-');
                    $sheet->setCellValue('N' . $row, $labelKolesterol[$k->status_kolesterol] ?? '-');
                    $sheet->setCellValue('O' . $row, $k->asam_urat      ?: '-');
                    $sheet->setCellValue('P' . $row, $labelAsamUrat[$k->status_asam_urat]    ?? '-');
                    $sheet->setCellValue('Q' . $row, $k->ada_keluhan ? 'Ya' : 'Tidak');
                    $sheet->setCellValue('R' . $row, $k->keluhan        ?: '-');
                    $sheet->setCellValue('S' . $row, $obat);
                    $sheet->setCellValue('T' . $row, $vitamin);
                    $sheet->setCellValue('U' . $row, $k->catatan_bidan  ?: '-');

                    // Warna baris selang-seling
                    $bg = ($no % 2 === 0) ? 'F0FDF4' : 'FFFFFF';
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                }
            }

            // Auto size semua kolom
            foreach (range('A', $lastCol) as $c) {
                $sheet->getColumnDimension($c)->setAutoSize(true);
            }
        }

        // Set sheet aktif ke Januari
        $spreadsheet->setActiveSheetIndex(0);

        $writer   = new Xlsx($spreadsheet);
        $filename = "Laporan_Kunjungan_Lansia_{$tahun}.xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'laporan_lansia_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
