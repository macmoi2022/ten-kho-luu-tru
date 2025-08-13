<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['driverNamePhone'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$driverNamePhone = $data['driverNamePhone'];
$dataFolder = 'Data/';
$conflictingReports = [];

$datesToCheck = [
    date('d-m-Y'),
    date('d-m-Y', strtotime('-1 day')),
    date('d-m-Y', strtotime('-2 days'))
];

$jsKeys = [
    'toa', 'date', 'driver', 'Ghi_Chú',
    'B1_Thái', 'Giá_B1_Thái', 'B2_Thái', 'Giá_B2_Thái', 'C1_Thái', 'Giá_C1_Thái', 'C2_Thái', 'Giá_C2_Thái', 'C3_Thái', 'Giá_C3_Thái',
    'D1_Thái', 'Giá_D1_Thái', 'D2_Thái', 'Giá_D2_Thái', 'E_Thái', 'Giá_E_Thái', 'Chợ_Thái', 'Giá_Chợ_Thái', 'Xơ_Thái', 'Giá_Xơ_Thái',
    'A1_indo', 'Giá_A1_indo', 'A2_indo', 'Giá_A2_indo', 'B1_indo', 'Giá_B1_indo', 'B2_indo', 'Giá_B2_indo', 'B3_indo', 'Giá_B3_indo',
    'C1_indo', 'Giá_C1_indo', 'C2_indo', 'Giá_C2_indo', 'Chợ_1_indo', 'Giá_Chợ_1_indo', 'Chợ_2_indo', 'Giá_Chợ_2_indo', 'Xơ_indo', 'Giá_Xơ_indo',
    'Tổng_KG', 'Thành_Tiền'
];

foreach ($datesToCheck as $date) {
    $filePath = $dataFolder . 'daily_reports_' . $date . '.csv';

    if (file_exists($filePath)) {
        $file = fopen($filePath, 'r');
        if ($file === false) continue;

        fgetcsv($file); // Bỏ qua dòng tiêu đề

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) === count($jsKeys)) {
                $report = array_combine($jsKeys, $row);
                if (isset($report['driver']) && $report['driver'] === $driverNamePhone) {
                    $conflictingReports[] = $report;
                }
            }
        }
        fclose($file);
    }
}

if (empty($conflictingReports)) {
    echo json_encode(['success' => true, 'canDelete' => true]);
} else {
    echo json_encode(['success' => true, 'canDelete' => false, 'reports' => $conflictingReports]);
}
?>