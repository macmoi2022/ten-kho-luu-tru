<?php // load_reports.php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$dataFolder = $input['dataDir'] ?? '';

// --- Security Check ---
$allowed = false;
$userType = $_SESSION['type'];
if ($userType === 'quanly' && ($dataFolder === 'Data_vua7/' || $dataFolder === 'Data_vua9/')) {
    $allowed = true;
} elseif ($userType === 'thuky_v7' && $dataFolder === 'Data_vua7/') {
    $allowed = true;
} elseif ($userType === 'thuky_v9' && $dataFolder === 'Data_vua9/') {
    $allowed = true;
}

if (!$allowed || empty($dataFolder)) {
    echo json_encode(['success' => false, 'message' => 'Access denied or invalid directory.']);
    exit;
}
// --- End Security Check ---

if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0777, true)) {
         echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục ' . $dataFolder, 'data' => []]);
         exit;
    }
}

$allReports = [];
// *** FIX: Changed keys to match the CSV headers written by save_reports.php ***
$jsKeys = [
    'Toa', 'Ngày', 'Lái', 'Ghi_Chú',
    'B1_Thái', 'Giá_B1_Thái', 'B2_Thái', 'Giá_B2_Thái', 'C1_Thái', 'Giá_C1_Thái', 'C2_Thái', 'Giá_C2_Thái', 'C3_Thái', 'Giá_C3_Thái',
    'D1_Thái', 'Giá_D1_Thái', 'D2_Thái', 'Giá_D2_Thái', 'E_Thái', 'Giá_E_Thái', 'Chợ_Thái', 'Giá_Chợ_Thái', 'Xơ_Thái', 'Giá_Xơ_Thái',
    'A1_indo', 'Giá_A1_indo', 'A2_indo', 'Giá_A2_indo', 'B1_indo', 'Giá_B1_indo', 'B2_indo', 'Giá_B2_indo', 'B3_indo', 'Giá_B3_indo',
    'C1_indo', 'Giá_C1_indo', 'C2_indo', 'Giá_C2_indo', 'Chợ_1_indo', 'Giá_Chợ_1_indo', 'Chợ_2_indo', 'Giá_Chợ_2_indo', 'Xơ_indo', 'Giá_Xơ_indo',
    'Tổng_KG', 'Thành_Tiền', 'isPaid',
    'khongThanhToan', 'toaDiBan', 'tuyChonTien', 'ghiChuTuyChon'
];

$reportFiles = glob($dataFolder . 'daily_reports_*.csv');

if (empty($reportFiles)) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

foreach ($reportFiles as $filePath) {
    $file = fopen($filePath, 'r');
    if ($file === false) continue;

    $headers = fgetcsv($file); 
    if ($headers === false) {
        fclose($file);
        continue;
    }
    $headerMap = array_flip(array_map('trim', $headers));


    while (($row = fgetcsv($file)) !== false) {
        $report = [];
        // Map data from CSV row to a report object using the header map
        foreach($jsKeys as $key){
            if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                 $report[$key] = $row[$headerMap[$key]];
            } else {
                // Set default values for columns that might be missing in old files
                if ($key === 'isPaid' || $key === 'khongThanhToan' || $key === 'toaDiBan') {
                    $report[$key] = 'false';
                } else {
                    $report[$key] = '';
                }
            }
        }
        // Rename keys to match what javascript expects (camelCase and lowercase)
        $jsReport = [];
        $jsReport['toa'] = $report['Toa'] ?? '';
        $jsReport['date'] = $report['Ngày'] ?? '';
        $jsReport['driver'] = $report['Lái'] ?? '';
        
        foreach($report as $key => $value) {
            if (!in_array($key, ['Toa', 'Ngày', 'Lái'])) {
                $jsReport[$key] = $value;
            }
        }
        $allReports[] = $jsReport;
    }
    fclose($file);
}

usort($allReports, function($a, $b) {
    $dateA = DateTime::createFromFormat('d/m/Y', $a['date']);
    $dateB = DateTime::createFromFormat('d/m/Y', $b['date']);
    if ($dateA == $dateB) {
        return (int)$a['toa'] - (int)$b['toa'];
    }
    return $dateA < $dateB ? -1 : 1;
});

echo json_encode(['success' => true, 'data' => $allReports]);
?>
