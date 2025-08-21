<?php
/**
 * File: verify_bank_account.php
 * Chức năng: Nhận thông tin tài khoản từ client, gọi API Payout của payOS để kiểm tra.
 */

header('Content-Type: application/json');

// --- HÀM TẠO CHỮ KÝ (Copy từ file payos_payout_test.php) ---
function createSignature(array $data, string $key): string
{
    ksort($data);
    $dataToSign = http_build_query($data, '', '&', PHP_QUERY_RFC3986);
    return hash_hmac('sha256', $dataToSign, $key);
}

// --- CẤU HÌNH API PAYOS ---
// !!! QUAN TRỌNG: Hãy thay thế bằng key thật của bạn !!!
$PAYOS_CLIENT_ID = 'e9c669ec-29e8-4a22-854e-898a881092d5';
$PAYOS_API_KEY = '6e45cc64-1f71-478e-937a-06f1bca158ba';
$PAYOS_CHECKSUM_KEY = '144291fec300dc04fcc509041528d23a9ed7e9d7c5eb2109af7e7c87919eacbc';
$PAYOS_API_URL = 'https://api-merchant.payos.vn/v1/payouts';

// --- LẤY DỮ LIỆU TỪ YÊU CẦU ---
$json_data = file_get_contents('php://input');
$post_data = json_decode($json_data, true);

if (!$post_data || !isset($post_data['toBin']) || !isset($post_data['toAccountNumber'])) {
    echo json_encode(['error' => true, 'message' => 'Dữ liệu đầu vào không hợp lệ.']);
    exit;
}

// --- CHUẨN BỊ DỮ LIỆU GỬI ĐI ---
$referenceId = 'VERIFY_' . time() . rand(100, 999);
$data = [
    'amount' => 1, // Số tiền tối thiểu để kiểm tra, sẽ không thành công nếu số dư không đủ
    'description' => 'Xac thuc tai khoan',
    'toBin' => $post_data['toBin'],
    'toAccountNumber' => $post_data['toAccountNumber'],
    'referenceId' => $referenceId
];

// Tạo chữ ký
$signature = createSignature($data, $PAYOS_CHECKSUM_KEY);

// --- GỬI YÊU CẦU BẰNG cURL ---
$ch = curl_init($PAYOS_API_URL);

$headers = [
    'x-client-id: ' . $PAYOS_CLIENT_ID,
    'x-api-key: ' . $PAYOS_API_KEY,
    'x-signature: ' . $signature,
    'x-idempotency-key: ' . $referenceId,
    'Content-Type: application/json'
];

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response_body = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// --- XỬ LÝ KẾT QUẢ VÀ TRẢ VỀ ---
if ($curl_error) {
    echo json_encode(['error' => true, 'message' => 'Lỗi cURL: ' . $curl_error]);
    exit;
}

$response_data = json_decode($response_body, true);

// Trả về toàn bộ payload từ payOS để JavaScript xử lý
echo json_encode([
    'error' => false,
    'http_status' => $http_code,
    'payload' => $response_data
]);
exit;
?>