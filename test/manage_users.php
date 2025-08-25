<?php
session_start();
header('Content-Type: application/json');

// Chỉ có quản lý mới có quyền truy cập
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'quanly') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập.']);
    exit;
}

$users_file = 'taikhoan.csv';

function read_users($file_path) {
    $users = [];
    if (!file_exists($file_path)) return $users;
    $file = fopen($file_path, 'r');
    fgetcsv($file); // Skip header
    while (($row = fgetcsv($file)) !== false) {
        if (count($row) >= 3) {
            $users[] = ['username' => $row[0], 'password' => $row[1], 'type' => $row[2]];
        }
    }
    fclose($file);
    return $users;
}

function write_users($file_path, $users) {
    $file = fopen($file_path, 'w');
    fputcsv($file, ['username', 'password', 'type']);
    foreach ($users as $user) {
        fputcsv($file, [$user['username'], $user['password'], $user['type']]);
    }
    fclose($file);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

$users = read_users($users_file);

switch ($action) {
    case 'load':
        // Không gửi password hash về client
        $safe_users = array_map(function($user) {
            unset($user['password']);
            return $user;
        }, $users);
        echo json_encode(['success' => true, 'data' => $safe_users]);
        break;

    case 'add':
        $new_user = $input['user'] ?? null;
        if ($new_user && !empty($new_user['username']) && !empty($new_user['password']) && !empty($new_user['type'])) {
            // Kiểm tra username đã tồn tại chưa
            foreach ($users as $user) {
                if ($user['username'] === $new_user['username']) {
                    echo json_encode(['success' => false, 'message' => 'Tên đăng nhập đã tồn tại.']);
                    exit;
                }
            }
            $new_user['password'] = password_hash($new_user['password'], PASSWORD_DEFAULT);
            $users[] = $new_user;
            write_users($users_file, $users);
            echo json_encode(['success' => true, 'message' => 'Thêm tài khoản thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ.']);
        }
        break;

    case 'update':
        $update_user = $input['user'] ?? null;
        $original_username = $input['original_username'] ?? '';
        if ($update_user && !empty($update_user['username']) && !empty($update_user['type']) && !empty($original_username)) {
            $found = false;
            foreach ($users as &$user) {
                if ($user['username'] === $original_username) {
                    $user['username'] = $update_user['username'];
                    $user['type'] = $update_user['type'];
                    // Chỉ cập nhật mật khẩu nếu có nhập mật khẩu mới
                    if (!empty($update_user['password'])) {
                        $user['password'] = password_hash($update_user['password'], PASSWORD_DEFAULT);
                    }
                    $found = true;
                    break;
                }
            }
            if ($found) {
                write_users($users_file, $users);
                echo json_encode(['success' => true, 'message' => 'Cập nhật tài khoản thành công.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Thông tin không hợp lệ.']);
        }
        break;

    case 'delete':
        $username_to_delete = $input['username'] ?? '';
        if ($username_to_delete === 'admin') {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa tài khoản admin.']);
            exit;
        }
        if (!empty($username_to_delete)) {
            $users = array_filter($users, function($user) use ($username_to_delete) {
                return $user['username'] !== $username_to_delete;
            });
            write_users($users_file, $users);
            echo json_encode(['success' => true, 'message' => 'Xóa tài khoản thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tên đăng nhập không hợp lệ.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);
        break;
}
?>
