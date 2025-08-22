<?php
// 1. Xử lý các yêu cầu API POST trước khi hiển thị HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['action']) && $input['action'] === 'generate_qr') {
        require_once __DIR__ . 'src/generate_qr.php';
        exit();
    }
}

// 2. Include phần header (bắt đầu session, kiểm tra đăng nhập, thẻ <head>)
require_once __DIR__ . 'templates/header.php';

?>
<body data-user-type="<?php echo htmlspecialchars($user_type); ?>">
<div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-7xl">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Vựa Mít Ngọc Anh HD68</h1>
        <div class="mt-3">
            <span class="text-gray-700 align-middle">Xin chào, <span class="font-bold"><?php echo htmlspecialchars($username); ?></span>!</span>
            <a href="logout.php" class="ml-1 inline-block bg-red-500 hover:bg-red-600 text-white font-bold text-sm py-1 px-2 rounded-lg shadow-md transition-transform transform hover:scale-105">
                Đăng xuất
            </a>
        </div>
    </div>

    <div class="sticky-tabs flex flex-wrap justify-center mb-3 border-b border-gray-200">
        <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-sm sm:text-base font-bold text-gray-700 focus:outline-none transition-colors duration-200 active" onclick="openTab('tab1')">Thông Tin Lái</button>
        <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-sm sm:text-base font-bold text-gray-700 focus:outline-none transition-colors duration-200" onclick="openTab('tab2')">Nhập Toa</button>
        <?php if ($user_type === 'quanly'): ?>
        <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-sm sm:text-base font-bold text-gray-700 focus:outline-none transition-colors duration-200" onclick="openTab('tab3')">Quản Lý</button>
        <?php endif; ?>
    </div>

    <?php
        include __DIR__ . 'templates/tabs/tab_drivers.php';
        include __DIR__ . 'templates/tabs/tab_reports.php';
        if ($user_type === 'quanly') {
            include __DIR__ . 'templates/tabs/tab_admin.php';
        }
    ?>

</div> <?php
// 4. Include phần footer (modals và các thẻ script)
require_once __DIR__ . 'templates/footer.php';
?>