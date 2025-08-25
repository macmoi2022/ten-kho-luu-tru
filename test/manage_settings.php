<?php
session_start(); // Bắt đầu session để truy cập biến $_SESSION
header('Content-Type: application/json');
$settings_file = 'settings.json';

// Chỉ kiểm tra quyền khi người dùng thực hiện lưu cài đặt (phương thức POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- FIX: Bổ sung kiểm tra session và quyền của người dùng ---
    if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'quanly') {
        // Trả về lỗi nếu không phải là quản lý hoặc chưa đăng nhập
        echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập. Vui lòng đăng nhập lại.']);
        exit;
    }
    // --- KẾT THÚC FIX ---

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
} else { // GET request để tải settings (không cần kiểm tra quyền)
    if (file_exists($settings_file)) {
        echo file_get_contents($settings_file);
    } else {
        // Trả về giá trị mặc định nếu file không tồn tại
        echo json_encode(['verification_mode' => 'manual']);
    }
}
?>
