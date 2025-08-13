<?php
session_start();

// Nếu đã đăng nhập, chuyển hướng đến trang chính
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$error_message = '';

function read_users($file_path) {
    $users = [];
    if (!file_exists($file_path)) {
        return $users;
    }
    $file = fopen($file_path, 'r');
    fgetcsv($file); // Bỏ qua dòng tiêu đề
    while (($row = fgetcsv($file)) !== false) {
        if (count($row) >= 3) {
            $users[$row[0]] = ['password' => $row[1], 'type' => $row[2]];
        }
    }
    fclose($file);
    return $users;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $users_file = 'taikhoan.csv';
    $users = read_users($users_file);

    if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['type'] = $users[$username]['type'];
        
        header('Location: index.php');
        exit;
    } else {
        $error_message = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Vựa Mít Ngọc Anh HD68</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen" style="background-image: url(logo.png)">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <div class="text-center mb-8">
            <img src="logo.png" alt="Logo" class="w-24 h-24 mx-auto rounded-full mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Vựa Mít Ngọc Anh HD68</h1>
            <p class="text-gray-600">Vui lòng đăng nhập để tiếp tục</p>
        </div>
        <form method="POST" action="login.php">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 font-medium mb-2">Tên đăng nhập</label>
                <input type="text" id="username" name="username" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error_message; ?></span>
                </div>
            <?php endif; ?>
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300">
                Đăng Nhập
            </button>
        </form>
    </div>
</body>
</html>
