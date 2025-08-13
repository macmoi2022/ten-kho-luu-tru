<?php
header('Content-Type: application/json');

$dataFolder = 'Data/';
$filePath = $dataFolder . 'driver_list.csv';

if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0777, true)) {
         echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục Data.', 'data' => []]);
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

fgetcsv($file); // Đọc dòng tiêu đề và bỏ qua

while (($row = fgetcsv($file)) !== false) {
    if (count($row) >= 5) { // Check for minimum number of columns
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