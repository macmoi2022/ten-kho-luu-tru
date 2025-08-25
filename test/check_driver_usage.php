<?php // check_driver_usage.php kiểm tra trước khi xóa lái buôn

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$dataFolder = $input['dataDir'] ?? '';
$driverNamePhone = $input['driverNamePhone'] ?? '';

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

if (!$allowed || empty($dataFolder) || empty($driverNamePhone)) {
    echo json_encode(['success' => false, 'message' => 'Access denied or invalid data.']);
    exit;
}
// --- End Security Check ---

$conflictingReports = [];
$datesToCheck = [
    date('d-m-Y'),
    date('d-m-Y', strtotime('-1 day')),
    date('d-m-Y', strtotime('-2 days'))
];

// *** FIX: Changed keys to match the CSV headers ***
$csvKeys = [
    'Toa', 'Ngày', 'Lái', 'Ghi_Chú',
    'B1_Thái', 'Giá_B1_Thái', 'B2_Thái', 'Giá_B2_Thái', 'C1_Thái', 'Giá_C1_Thái', 'C2_Thái', 'Giá_C2_Thái', 'C3_Thái', 'Giá_C3_Thái',
    'D1_Thái', 'Giá_D1_Thái', 'D2_Thái', 'Giá_D2_Thái', 'E_Thái', 'Giá_E_Thái', 'Chợ_Thái', 'Giá_Chợ_Thái', 'Xơ_Thái', 'Giá_Xơ_Thái',
    'A1_indo', 'Giá_A1_indo', 'A2_indo', 'Giá_A2_indo', 'B1_indo', 'Giá_B1_indo', 'B2_indo', 'Giá_B2_indo', 'B3_indo', 'Giá_B3_indo',
    'C1_indo', 'Giá_C1_indo', 'C2_indo', 'Giá_C2_indo', 'Chợ_1_indo', 'Giá_Chợ_1_indo', 'Chợ_2_indo', 'Giá_Chợ_2_indo', 'Xơ_indo', 'Giá_Xơ_indo',
    'Tổng_KG', 'Thành_Tiền', 'isPaid'
];


foreach ($datesToCheck as $date) {
    $filePath = $dataFolder . 'daily_reports_' . str_replace('/', '-', $date) . '.csv';

    if (file_exists($filePath)) {
        $file = fopen($filePath, 'r');
        if ($file === false) continue;

        $headers = fgetcsv($file); // Bỏ qua dòng tiêu đề
        if ($headers === false) {
            fclose($file);
            continue;
        }
        $headerMap = array_flip(array_map('trim', $headers));


        while (($row = fgetcsv($file)) !== false) {
             $report = [];
             // Map data using the header map
             foreach($headerMap as $headerName => $index) {
                 if(isset($row[$index])) {
                     $report[$headerName] = $row[$index];
                 }
             }

             // Check using the correct, capitalized key 'Lái'
             if (isset($report['Lái']) && $report['Lái'] === $driverNamePhone) {
                // Rename keys for javascript before sending
                $jsReport = [];
                $jsReport['toa'] = $report['Toa'] ?? '';
                $jsReport['date'] = $report['Ngày'] ?? '';
                $jsReport['driver'] = $report['Lái'] ?? '';
                foreach($report as $key => $value) {
                    if (!in_array($key, ['Toa', 'Ngày', 'Lái'])) {
                        $jsReport[$key] = $value;
                    }
                }
                $conflictingReports[] = $jsReport;
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
