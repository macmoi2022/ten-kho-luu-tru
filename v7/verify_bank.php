<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$accountNumber = $input['accountNumber'] ?? '';
$bankCode = $input['bin'] ?? ''; // Frontend sends it as 'bin', payOS uses it as 'bankCode'

if (empty($accountNumber) || empty($bankCode)) {
    echo json_encode(['success' => false, 'message' => 'Số tài khoản và mã ngân hàng là bắt buộc.']);
    exit;
}

// --- payOS Credentials ---
$PAYOS_CLIENT_ID = '75de7561-abcb-425f-9788-94fae2f46673';
$PAYOS_API_KEY = 'cfdb295e-5125-40c7-9436-c2de1773ef92';
$PAYOS_CHECKSUM_KEY = 'ab70d60ec5691e3055e9b2621037880ff0744f204f6426b161b962088f3eedc4';

// 1. Chuẩn bị dữ liệu để tạo chữ ký
$orderCode = time() . rand(1000, 9999);
$amount = 1;
$description = 'AccountVerification'; // Sửa: Bỏ khoảng trắng để tránh lỗi encoding

$data_to_sign = [
    "accountNumber" => $accountNumber,
    "amount" => $amount,
    "bankCode" => $bankCode,
    "description" => $description,
    "orderCode" => $orderCode
];

// 2. Sắp xếp dữ liệu theo thứ tự alphabet của key
ksort($data_to_sign);

// 3. Tạo chuỗi dữ liệu từ mảng
$data_string = http_build_query($data_to_sign);

// 4. Tạo chữ ký HMAC-SHA256
$signature = hash_hmac('sha256', $data_string, $PAYOS_CHECKSUM_KEY);

// 5. Chuẩn bị toàn bộ payload để gửi đi
$payload = $data_to_sign;
$payload['signature'] = $signature;
$payload_json = json_encode($payload);

// 6. Thực hiện gọi API bằng cURL
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api-merchant.payos.vn/v2/payouts/request",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $payload_json,
    CURLOPT_HTTPHEADER => [
        "x-client-id: " . $PAYOS_CLIENT_ID,
        "x-api-key: " . $PAYOS_API_KEY,
        "Content-Type: application/json"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// *** TÍNH NĂNG MỚI: Ghi log để gỡ lỗi ***
// Ghi lại payload đã gửi và phản hồi nhận được vào file log
$log_content = "Time: " . date('Y-m-d H:i:s') . "\n";
$log_content .= "Payload Sent: " . $payload_json . "\n";
$log_content .= "Response Received: " . $response . "\n";
$log_content .= "cURL Error: " . $err . "\n";
$log_content .= "----------------------------------------\n";
file_put_contents('payos_debug.log', $log_content, FILE_APPEND);


if ($err) {
    echo json_encode(['success' => false, 'message' => "Lỗi cURL: " . $err]);
    exit;
}

// 7. Xử lý phản hồi từ payOS
$response_data = json_decode($response, true);

if (isset($response_data['code']) && $response_data['code'] === '00') {
    // Thành công
    echo json_encode([
        'success' => true,
        'accountName' => $response_data['data']['accountName'] ?? 'KHONG TIM THAY TEN'
    ]);
} else {
    // Thất bại
    echo json_encode([
        'success' => false,
        'message' => $response_data['desc'] ?? 'Lỗi không xác định từ payOS.'
    ]);
}
?>