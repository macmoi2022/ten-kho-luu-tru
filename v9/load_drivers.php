<?php // load_drivers.php

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

$filePath = $dataFolder . 'driver_list.csv';

if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0777, true)) {
         echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục ' . $dataFolder, 'data' => []]);
         exit;
    }
}

if (!file_exists($filePath)) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

$drivers = [];
$file = fopen($filePath, 'r');

if ($file === false) {
    echo json_encode(['success' => false, 'message' => 'Không thể mở tệp để đọc.', 'data' => []]);
    exit;
}

fgetcsv($file);

while (($row = fgetcsv($file)) !== false) {
    if (count($row) >= 5) {
        $drivers[] = [
            'driverNamePhone' => $row[0] ?? '',
            'bankName' => $row[1] ?? '',
            'accountNumber' => $row[2] ?? '',
            'accountName' => $row[3] ?? '',
            'isVerified' => $row[4] ?? 'false'
        ];
    }
}

fclose($file);

echo json_encode(['success' => true, 'data' => $drivers]);
?>