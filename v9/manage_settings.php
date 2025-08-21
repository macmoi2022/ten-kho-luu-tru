<?php
header('Content-Type: application/json');
$settings_file = 'settings.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Đọc file settings hiện tại
    $settings = [];
    if (file_exists($settings_file)) {
        $settings = json_decode(file_get_contents($settings_file), true);
    }

    // Cập nhật setting
    if (isset($input['verification_mode'])) {
        $settings['verification_mode'] = $input['verification_mode'];
    }

    // Lưu lại file
    if (file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true, 'message' => 'Đã lưu cài đặt.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể lưu cài đặt.']);
    }
} else { // GET request để tải settings
    if (file_exists($settings_file)) {
        echo file_get_contents($settings_file);
    } else {
        // Trả về giá trị mặc định nếu file không tồn tại
        echo json_encode(['verification_mode' => 'manual']);
    }
}
?>