<?php // save_drivers.php

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$dataFolder = $input['dataDir'] ?? '';
$drivers = $input['drivers'] ?? null;
$fileName = 'driver_list.csv';

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

if (!$allowed || empty($dataFolder) || !is_array($drivers)) {
    echo json_encode(['success' => false, 'message' => 'Access denied or invalid data.']);
    exit;
}
// --- End Security Check ---

if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục ' . $dataFolder]);
        exit;
    }
}

$filePath = $dataFolder . $fileName;
$file = fopen($filePath, 'w');

if ($file === false) {
    echo json_encode(['success' => false, 'message' => 'Không thể mở tệp để ghi.']);
    exit;
}

$headers = ['Tên và SĐT Lái', 'Ngân Hàng', 'Số Tài Khoản', 'Tên Tài Khoản', 'Xác thực'];
fputcsv($file, $headers);

foreach ($drivers as $driver) {
    $rowData = [];
    $rowData[] = $driver['driverNamePhone'] ?? '';
    $rowData[] = $driver['bankName'] ?? '';
    $rowData[] = $driver['accountNumber'] ?? '';
    $rowData[] = $driver['accountName'] ?? '';
    $rowData[] = ($driver['isVerified'] ?? false) ? 'true' : 'false';
    fputcsv($file, $rowData);
}

fclose($file);

echo json_encode(['success' => true, 'message' => 'Đã lưu danh sách Lái thành công.']);
?>