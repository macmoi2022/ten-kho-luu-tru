<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['reports']) || !is_array($data['reports'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$reports = $data['reports'];
$dataFolder = 'Data/';

if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục Data.']);
        exit;
    }
}

$existingFiles = glob($dataFolder . 'daily_reports_*.csv');
$existingFiles = $existingFiles ? array_flip($existingFiles) : [];

$reportsByDate = [];
foreach ($reports as $report) {
    if (empty($report['date'])) continue;
    $fileNameDate = str_replace('/', '-', $report['date']); 
    $reportsByDate[$fileNameDate][] = $report;
}

$headers = [
    'Toa', 'Ngày', 'Lái', 'Ghi_Chú',
    'B1_Thái', 'Giá_B1_Thái', 'B2_Thái', 'Giá_B2_Thái', 'C1_Thái', 'Giá_C1_Thái', 'C2_Thái', 'Giá_C2_Thái', 'C3_Thái', 'Giá_C3_Thái',
    'D1_Thái', 'Giá_D1_Thái', 'D2_Thái', 'Giá_D2_Thái', 'E_Thái', 'Giá_E_Thái', 'Chợ_Thái', 'Giá_Chợ_Thái', 'Xơ_Thái', 'Giá_Xơ_Thái',
    'A1_indo', 'Giá_A1_indo', 'A2_indo', 'Giá_A2_indo', 'B1_indo', 'Giá_B1_indo', 'B2_indo', 'Giá_B2_indo', 'B3_indo', 'Giá_B3_indo',
    'C1_indo', 'Giá_C1_indo', 'C2_indo', 'Giá_C2_indo', 'Chợ_1_indo', 'Giá_Chợ_1_indo', 'Chợ_2_indo', 'Giá_Chợ_2_indo', 'Xơ_indo', 'Giá_Xơ_indo',
    'Tổng_KG', 'Thành_Tiền'
];
$quantityColumns = [
    'B1_Thái', 'B2_Thái', 'C1_Thái', 'C2_Thái', 'C3_Thái', 'D1_Thái', 'D2_Thái', 'E_Thái', 'Chợ_Thái', 'Xơ_Thái',
    'A1_indo', 'A2_indo', 'B1_indo', 'B2_indo', 'B3_indo', 'C1_indo', 'C2_indo', 'Chợ_1_indo', 'Chợ_2_indo', 'Xơ_indo'
];

foreach ($reportsByDate as $dateKey => $dateReports) {
    $filePath = $dataFolder . 'daily_reports_' . $dateKey . '.csv';
    
    if (isset($existingFiles[$filePath])) {
        unset($existingFiles[$filePath]);
    }

    $file = fopen($filePath, 'w');
    if ($file === false) continue;

    fputcsv($file, $headers);

    foreach ($dateReports as $index => $report) {
        $reportDateKey = str_replace('/', '-', $report['date']);
        if ($reportDateKey !== $dateKey) continue;
        
        $toaNumber = $index + 1;

        $rowData = [];
        $rowData[] = $toaNumber;
        $rowData[] = $report['date'] ?? '';
        $rowData[] = $report['driver'] ?? '';
        $rowData[] = $report['Ghi_Chú'] ?? '';

        foreach ($quantityColumns as $qtyCol) {
            $priceCol = "Giá_" . $qtyCol;
            $rowData[] = $report[$qtyCol] ?? '';
            $rowData[] = $report[$priceCol] ?? '';
        }

        $rowData[] = $report['Tổng_KG'] ?? '';
        $rowData[] = $report['Thành_Tiền'] ?? '';
        fputcsv($file, $rowData);
    }
    fclose($file);
}

foreach (array_keys($existingFiles) as $fileToDelete) {
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
    }
}

echo json_encode(['success' => true, 'message' => 'Đã lưu báo cáo thành công.']);
?>