<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;

// --- CÁC HÀM TRỢ GIÚP ---

function getReports($vuaDir, $filterDate_dmy) {
    try {
        $dateObj = DateTime::createFromFormat('d/m/Y', $filterDate_dmy);
        if ($dateObj === false) return [];
        $dateForFilename = $dateObj->format('d-m-Y');
    } catch (Exception $e) {
        return [];
    }

    $filePath = $vuaDir . 'daily_reports_' . $dateForFilename . '.csv';
    if (!file_exists($filePath)) return [];

    $reports = [];
    $fileContent = file_get_contents($filePath);
    $fileContent = str_replace("\xEF\xBB\xBF", '', $fileContent);
    $lines = explode(PHP_EOL, $fileContent);
    
    if (count($lines) < 2) return [];

    $raw_header = str_getcsv(array_shift($lines));
    $header = [];
    foreach ($raw_header as $h) {
        $header[] = strtolower(trim($h));
    }
    
    foreach ($lines as $line) {
        if (trim($line) === '') continue;
        $row = str_getcsv($line);
        if (count($header) == count($row)) {
            $reports[] = array_combine($header, $row);
        }
    }
    
    usort($reports, fn($a, $b) => (int)($a['toa'] ?? 0) <=> (int)($b['toa'] ?? 0));
    return $reports;
}

function getValue($expression) {
    if (strpos($expression, '+') !== false) {
        return array_sum(explode('+', $expression));
    }
    return is_numeric($expression) ? (float)$expression : 0;
}

function createReportSheet(Spreadsheet &$spreadsheet, array $reports, int $sheetIndex, string $sheetName) {
    if ($sheetIndex === 0) {
        $sheet = $spreadsheet->getSheet(0);
    } else {
        $sheet = $spreadsheet->createSheet();
    }
    $sheet->setTitle($sheetName);

    $quantityColumns = ["b1_thái","b2_thái","c1_thái","c2_thái","c3_thái","d1_thái","d2_thái","e_thái","chợ_thái","xơ_thái","a1_indo","a2_indo","b1_indo","b2_indo","b3_indo","c1_indo","c2_indo","chợ_1_indo","chợ_2_indo","xơ_indo"];
    $headerForDisplay = ['Toa', 'Lái'];
    foreach ($quantityColumns as $col) {
        $headerForDisplay[] = ucwords(str_replace(['_', 'indo'], [' ', 'Indo'], $col));
        $headerForDisplay[] = 'Giá ' . ucwords(str_replace(['_', 'indo'], [' ', 'Indo'], $col));
    }
    $headerForDisplay = array_merge($headerForDisplay, ['Tổng KG', 'Thành Tiền', 'Ghi Chú', 'Thanh Toán']);
    $sheet->fromArray($headerForDisplay, NULL, 'A1');

    $headerStyle = ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false]];
    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
    
    $currentRow = 2;
    $totals = array_fill_keys(array_merge($quantityColumns, ['tổng_kg', 'thành_tiền']), 0);

    if (empty($reports)) {
        $sheet->setCellValue('A2', 'Không có dữ liệu cho ngày này.');
    } else {
        foreach ($reports as $report) {
            $totalKg = 0; $totalAmount = 0;
            
            // CẬP NHẬT: In hoa chữ cho cột Lái
            $lai_data = $report['lái'] ?? '';
            $rowData = [$report['toa'] ?? '', mb_strtoupper($lai_data, 'UTF-8')];
            
            foreach ($quantityColumns as $qc) {
                $qty = getValue($report[$qc] ?? 0);
                $price = getValue($report['giá_'.$qc] ?? 0);
                $rowData[] = $qty ?: '';
                $rowData[] = $price ?: '';
                $totalKg += $qty;
                $totalAmount += ($qty * $price);
                $totals[$qc] += $qty;
            }
            $totals['tổng_kg'] += $totalKg;
            $totals['thành_tiền'] += $totalAmount;
            $rowData = array_merge($rowData, [$totalKg ?: '', $totalAmount ?: '', $report['ghi_chú'] ?? '', (($report['ispaid'] ?? 'false') == 'true' ? 'Đã CK' : '')]);
            $sheet->fromArray($rowData, NULL, 'A' . $currentRow++);
        }
        
        $totalRowData = ['TỔNG CỘNG', ''];
        foreach ($quantityColumns as $qc) {
            $totalRowData[] = $totals[$qc] ?: '';
            $totalRowData[] = '';
        }
        $totalRowData = array_merge($totalRowData, [$totals['tổng_kg'] ?: '', $totals['thành_tiền'] ?: '', '', '']);
        $sheet->fromArray($totalRowData, NULL, 'A' . $currentRow);
        
        $totalRowStyle = ['font' => ['bold' => true, 'size' => 11], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1D5DB']]];
        $sheet->getStyle('A'.$currentRow.':'.$sheet->getHighestColumn().$currentRow)->applyFromArray($totalRowStyle);
    }

    $thaiColorStyle = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DFC57B']]];
    $indoColorStyle = ['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B37146']]];
    $boldStyle = ['font' => ['bold' => true]];
    $numberFormatStyle = ['numberFormat' => ['formatCode' => '#,##0']];
    $highestDataRow = $currentRow > 2 && !empty($reports) ? $currentRow - 1 : 0;

    if ($highestDataRow >= 2) {
        $colIndex = 3;
        foreach ($quantityColumns as $colName) {
            $qtyColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $cellRangeToColor = $qtyColumnLetter . '2:' . $qtyColumnLetter . $highestDataRow;
            if (strpos($colName, '_thái') !== false) {
                $sheet->getStyle($cellRangeToColor)->applyFromArray($thaiColorStyle);
            } elseif (strpos($colName, '_indo') !== false) {
                $sheet->getStyle($cellRangeToColor)->applyFromArray($indoColorStyle);
            }
            $colIndex += 2;
        }

        $sheet->getStyle('A2:B' . $highestDataRow)->applyFromArray($boldStyle);
        $tongKgColIndex = count($headerForDisplay) - 3;
        $thanhTienColIndex = count($headerForDisplay) - 2;
        $tongKgColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($tongKgColIndex);
        $thanhTienColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($thanhTienColIndex);
        $sheet->getStyle($tongKgColLetter . '2:' . $thanhTienColLetter . $highestDataRow)->applyFromArray($boldStyle);
        
        $startNumericCol = 'C';
        $endNumericColIndex = count($headerForDisplay) - 2;
        $endNumericColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endNumericColIndex);
        $sheet->getStyle($startNumericCol . '2:' . $endNumericColLetter . $currentRow)->applyFromArray($numberFormatStyle);
    }
    
    $sheet->getColumnDimension('A')->setWidth(6);
    $sheet->getColumnDimension('B')->setAutoSize(true);
    $startDataColIndex = 3;
    $endDataColIndex = count($headerForDisplay) - 2;
    for ($i = $startDataColIndex; $i <= $endDataColIndex; $i++) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
        $sheet->getColumnDimension($colLetter)->setWidth(12);
    }
    $ghiChuColumnIndex = count($headerForDisplay) - 1;
    $ghiChuColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ghiChuColumnIndex);
    $sheet->getColumnDimension($ghiChuColumnLetter)->setWidth(45);
    $thanhToanColumnIndex = count($headerForDisplay);
    $thanhToanColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($thanhToanColumnIndex);
    $sheet->getColumnDimension($thanhToanColumnLetter)->setWidth(12);
    $sheet->freezePane('C2');

    $allBordersStyle = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $currentRow)->applyFromArray($allBordersStyle);
    for ($i = 2; $i <= $currentRow; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(16.5);
    }
}

// Hàm tạo sheet Tổng Kết
function createSummarySheet(Spreadsheet &$spreadsheet, array $reports7, array $reports9) {
    $sheet = $spreadsheet->createSheet()->setTitle('Tổng Kết');
    $header = ['Loại Hàng', 'Vựa 7 (KG)', 'Vựa 9 (KG)'];
    $sheet->fromArray($header, NULL, 'A1');
    
    $headerStyle = ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4B5563']]];
    $thaiCategoryStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DFC57B']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
    $indoCategoryStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B37146']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]];
    $totalStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']]];
    $grandTotalStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']]];
    $numberFormatStyle = ['numberFormat' => ['formatCode' => '#,##0']];
    
    $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);
    
    $stats = ['vua7' => ['details' => [], 'totalAmount' => 0], 'vua9' => ['details' => [], 'totalAmount' => 0]];
    $allReports = ['vua7' => $reports7, 'vua9' => $reports9];
    $quantityColumns = ["b1_thái","b2_thái","c1_thái","c2_thái","c3_thái","d1_thái","d2_thái","e_thái","chợ_thái","xơ_thái","a1_indo","a2_indo","b1_indo","b2_indo","b3_indo","c1_indo","c2_indo","chợ_1_indo","chợ_2_indo","xơ_indo"];

    foreach ($allReports as $vuaKey => $reports) {
        foreach ($quantityColumns as $col) $stats[$vuaKey]['details'][$col] = ['kg' => 0];
        foreach ($reports as $report) {
            foreach ($quantityColumns as $col) {
                $qty = getValue($report[$col] ?? 0);
                $price = getValue($report['giá_'.$col] ?? 0);
                $stats[$vuaKey]['details'][$col]['kg'] += $qty;
                $stats[$vuaKey]['totalAmount'] += ($qty * $price);
            }
        }
    }
    
    $currentRow = 2;
    $thaiTypeOrder = ["b1_thái", "b2_thái", "c1_thái", "c2_thái", "c3_thái", "d1_thái", "d2_thái", "e_thái", "chợ_thái", "xơ_thái"];
    $indoTypeOrder = ["a1_indo", "a2_indo", "b1_indo", "b2_indo", "b3_indo", "c1_indo", "c2_indo", "chợ_1_indo", "chợ_2_indo", "xơ_indo"];
    
    $sheet->setCellValue('A'.$currentRow, 'HÀNG THÁI')->mergeCells('A'.$currentRow.':C'.$currentRow)->getStyle('A'.$currentRow)->applyFromArray($thaiCategoryStyle); $currentRow++;
    $totalThaiKg7 = 0; $totalThaiKg9 = 0;
    foreach ($thaiTypeOrder as $type) {
        $kg7 = $stats['vua7']['details'][$type]['kg'] ?? 0; $totalThaiKg7 += $kg7;
        $kg9 = $stats['vua9']['details'][$type]['kg'] ?? 0; $totalThaiKg9 += $kg9;
        if ($kg7 > 0 || $kg9 > 0) {
            $sheet->fromArray([ucwords(str_replace('_thái', ' Thái', $type)), $kg7 ?: '', $kg9 ?: ''], NULL, 'A'.$currentRow++);
        }
    }
    $sheet->fromArray(['Tổng Thái', $totalThaiKg7 ?: '', $totalThaiKg9 ?: ''], NULL, 'A'.$currentRow)->getStyle('A'.$currentRow.':C'.$currentRow)->applyFromArray($totalStyle); $currentRow++;
    
    $sheet->setCellValue('A'.$currentRow, 'HÀNG INDO')->mergeCells('A'.$currentRow.':C'.$currentRow)->getStyle('A'.$currentRow)->applyFromArray($indoCategoryStyle); $currentRow++;
    $totalIndoKg7 = 0; $totalIndoKg9 = 0;
    foreach ($indoTypeOrder as $type) {
        $kg7 = $stats['vua7']['details'][$type]['kg'] ?? 0; $totalIndoKg7 += $kg7;
        $kg9 = $stats['vua9']['details'][$type]['kg'] ?? 0; $totalIndoKg9 += $kg9;
        if ($kg7 > 0 || $kg9 > 0) {
            $sheet->fromArray([ucwords(str_replace('_indo', ' Indo', $type)), $kg7 ?: '', $kg9 ?: ''], NULL, 'A'.$currentRow++);
        }
    }
    $sheet->fromArray(['Tổng Indo', $totalIndoKg7 ?: '', $totalIndoKg9 ?: ''], NULL, 'A'.$currentRow)->getStyle('A'.$currentRow.':C'.$currentRow)->applyFromArray($totalStyle); $currentRow++;
    
    $currentRow++;
    $sheet->fromArray(['TỔNG CỘNG (KG)', ($totalThaiKg7 + $totalIndoKg7) ?: '', ($totalThaiKg9 + $totalIndoKg9) ?: ''], NULL, 'A'.$currentRow)->getStyle('A'.$currentRow.':C'.$currentRow)->applyFromArray($grandTotalStyle); $currentRow++;
    $sheet->fromArray(['TỔNG THÀNH TIỀN', (($stats['vua7']['totalAmount'] ?? 0) + ($stats['vua9']['totalAmount'] ?? 0))], NULL, 'A'.$currentRow)->mergeCells('B'.$currentRow.':C'.$currentRow)->getStyle('A'.$currentRow.':C'.$currentRow)->applyFromArray($grandTotalStyle);
    
    $sheet->getStyle('B2:C'.$currentRow)->applyFromArray($numberFormatStyle);
    $sheet->getStyle('B'.$currentRow.':C'.$currentRow)->getNumberFormat()->setFormatCode('#,##0" đ"');

    foreach (range('A', 'C') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    $allBordersStyle = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
    $sheet->getStyle('A1:C' . $currentRow)->applyFromArray($allBordersStyle);
    for ($i = 2; $i <= $currentRow; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(16.5);
    }
}

// --- LOGIC CHÍNH ---
$date = urldecode($_GET['date'] ?? date('d/m/Y'));
$type = $_GET['type'] ?? 'quanly';
$vua = urldecode($_GET['vua'] ?? '');

$spreadsheet = new Spreadsheet();

if ($type === 'quanly') {
    $reports7 = getReports('Data_vua7/', $date);
    $reports9 = getReports('Data_vua9/', $date);
    createReportSheet($spreadsheet, $reports7, 0, 'Báo Cáo Vựa 7');
    createReportSheet($spreadsheet, $reports9, 1, 'Báo Cáo Vựa 9');
    createSummarySheet($spreadsheet, $reports7, $reports9);
    $spreadsheet->setActiveSheetIndex(0);
} else {
    if (!empty($vua) && in_array($vua, ['Data_vua7/', 'Data_vua9/'])) {
        $vuaName = ($vua === 'Data_vua7/') ? 'Vựa 7' : 'Vựa 9';
        $reports = getReports($vua, $date);
        createReportSheet($spreadsheet, $reports, 0, 'Báo Cáo ' . $vuaName);
    }
}

$filename = 'Thong_ke_' . str_replace('/', '-', $date) . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;