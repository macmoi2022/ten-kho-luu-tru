<?php
// Đặt tiêu đề Content-Type để cho trình duyệt biết phản hồi là JSON
header('Content-Type: application/json');

// Lấy dữ liệu JSON từ yêu cầu POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Kiểm tra xem dữ liệu có hợp lệ không
if (json_last_error() !== JSON_ERROR_NONE || !isset($data['drivers']) || !is_array($data['drivers']) || !isset($data['fileName'])) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

$drivers = $data['drivers'];
$fileName = $data['fileName'];

// Định nghĩa đường dẫn thư mục 'Data'
$dataFolder = 'Data/';

// Tạo thư mục 'Data' nếu nó chưa tồn tại
if (!is_dir($dataFolder)) {
    if (!mkdir($dataFolder, 0777, true)) { // 0777 là quyền đầy đủ, có thể điều chỉnh cho an toàn hơn
        echo json_encode(['success' => false, 'message' => 'Không thể tạo thư mục Data.']);
        exit;
    }
}

$filePath = $dataFolder . $fileName;

// Mở tệp CSV để ghi
// 'w' để ghi đè nếu tệp đã tồn tại, hoặc tạo mới nếu chưa có
$file = fopen($filePath, 'w');

if ($file === false) {
    echo json_encode(['success' => false, 'message' => 'Không thể mở tệp để ghi.']);
    exit;
}

// Định nghĩa các tiêu đề cột cho CSV
$headers = [
    'Tên và SĐT Lái', 'Ngân Hàng', 'Số Tài Khoản', 'Tên Tài Khoản', 'Xác thực'
];

// Ghi tiêu đề vào tệp CSV
fputcsv($file, $headers);

// Ghi từng hàng dữ liệu vào tệp CSV
foreach ($drivers as $driver) {
    $rowData = [];
    $rowData[] = $driver['driverNamePhone'] ?? '';
    $rowData[] = $driver['bankName'] ?? '';
    $rowData[] = $driver['accountNumber'] ?? '';
    $rowData[] = $driver['accountName'] ?? '';
    $rowData[] = ($driver['isVerified'] ?? false) ? 'true' : 'false'; // Lưu trạng thái xác thực
    fputcsv($file, $rowData);
}

// Đóng tệp
fclose($file);

echo json_encode(['success' => true, 'message' => 'Đã lưu danh sách Lái thành công.']);
?>