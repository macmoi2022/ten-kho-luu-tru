<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'generate_qr') {
        header('Content-Type: application/json');

        $clientId = '456b6df3-1ccf-4e4c-a114-c4609ee2abdb';
        $apiKey = 'd854ee9e-2d4f-4581-b25e-eeb112709f81';

        $data = [
            'acqId' => $input['bin'],
            'accountNo' => $input['accountNumber'],
            'accountName' => $input['accountName'],
            'amount' => $input['amount'],
            'addInfo' => $input['description'],
            'format' => 'text',
            'template' => 'qr_only'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.vietqr.io/v2/generate',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'x-client-id: ' . $clientId,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(['code' => '99', 'desc' => 'cURL Error: ' . $err]);
        } else {
            echo $response;
        }
        exit();
    }
}
// Đặt thời gian session là 8 tiếng (8 * 3600 = 28800 giây)
ini_set('session.gc_maxlifetime', 28800);
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$user_type = $_SESSION['type'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vựa Mít Ngọc Anh HD68</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
        <div id="tab1" class="tab-content active p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-700">Mục Thông Tin Lái</h2>
                <?php if ($user_type === 'quanly'): ?>
                <div class="ml-auto">
                    <select id="vuaSelectorTab1" class="vua-selector">
                        <option value="Data_vua7/">Vựa 7</option>
                        <option value="Data_vua9/">Vựa 9</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <p class="text-gray-600 leading-relaxed mb-4 text-left">Quản lý thông tin chi tiết của các lái, bao gồm thông tin liên hệ và tài khoản ngân hàng.</p>
            
            <div class="flex justify-end mb-4">
                <button onclick="openDriverModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">Thêm thông tin lái</button>
            </div>

            <div class="hidden md:flex items-center gap-4 mb-4">
                <input type="search" id="desktopDriverSearch" placeholder="Tìm kiếm Tên và SĐT Lái..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                 <div class="flex items-center">
                    <input type="checkbox" id="desktopFilterUnverified" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="desktopFilterUnverified" class="ml-2 block text-sm font-medium text-gray-700 whitespace-nowrap">Chưa đánh dấu</label>
                </div>				
            </div>

            <div id="driverListTableContainer" class="overflow-x-auto rounded-lg shadow-sm border border-gray-200 hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Stt</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên và SĐT Lái</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngân Hàng</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Số Tài Khoản</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên Tài Khoản</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="driverListTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="desktopLoadMoreContainer" class="hidden md:flex justify-center mt-4">
                <button id="desktopLoadMoreBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Xem Thêm</button>
            </div>

            <div id="mobileDriverListContainer" class="mt-4 md:hidden">
                <div class="flex items-center justify-between font-bold p-2 bg-gray-100 border-b border-gray-300 rounded-t-lg">
                    <div class="flex items-center">
                        <div class="w-[40px] text-center text-xs text-gray-500 uppercase">Stt</div>
                        <div class="text-xs text-gray-500 uppercase pl-2">Lái</div>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="mobileFilterUnverified" name="mobileFilterUnverified" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="mobileFilterUnverified" class="ml-2 block text-sm font-medium text-gray-700 whitespace-nowrap">Chưa đánh dấu</label>
                    </div>
                </div>
                <div class="p-2 bg-gray-100 border-b border-gray-300">
                    <input type="search" id="mobileDriverSearch" placeholder="Tìm kiếm lái..." class="w-full px-2 py-1 text-sm font-normal border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div id="mobileDriverList"></div>
                <div id="mobileLoadMoreContainer" class="flex md:hidden justify-center mt-4">
                    <button id="mobileLoadMoreBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Xem Thêm</button>
                </div>
            </div>
        </div>

        <div id="tab2" class="tab-content p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
             <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-700">Mục Nhập Toa</h2>
                <?php if ($user_type === 'quanly'): ?>
                <div class="ml-auto">
                    <select id="vuaSelectorTab2" class="vua-selector">
                        <option value="Data_vua7/">Vựa 7</option>
                        <option value="Data_vua9/">Vựa 9</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <p class="text-gray-600 leading-relaxed mb-4 text-left">Nhập và quản lý dữ liệu báo cáo hàng ngày của các lái.</p>
            
            <div class="mb-6 flex flex-wrap items-center gap-2">
                <label for="reportDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                <input type="text" id="reportDate" class="flatpickr-input">
				<?php if ($user_type === 'thuky_v7' || $user_type === 'thuky_v9'): ?>
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 whitespace-nowrap" onclick="exportReportsToExcel()">Xuất Excel</button>
				<?php endif; ?>
            </div>

            <div id="dailyReportTableContainer" class="rounded-lg shadow-sm border border-gray-200 mt-8 hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Toa</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Lái</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tổng KG</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Thành Tiền</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Ghi Chú</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dailyReportTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            
            <div class="mt-8 md:hidden">
                <div class="flex items-center justify-between font-bold p-2 bg-gray-100 border-b border-gray-300 rounded-t-lg">
                    <div class="flex items-center">
                        <div class="w-[40px] text-center text-xs text-gray-500 uppercase">Toa</div>
                        <div class="text-xs text-gray-500 uppercase pl-2">Lái</div>
                    </div>
                </div>
                <div id="mobileReportList"></div>
            </div>

            <div id="mobileActionButtons" class="mt-4 flex justify-end gap-2">
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200" onclick="openSummaryModal()">Tổng Hợp Dữ Liệu</button>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200" onclick="handleAddNewRowClick()">Thêm Dòng Mới</button>
            </div>
        </div>

        <?php if ($user_type === 'quanly'): ?>
        <div id="tab3" class="tab-content p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Thống Kê Trong Ngày</h2>
                <div class="flex items-center gap-2 mb-4">
                    <label for="statsDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                    <input type="text" id="statsDate" class="flatpickr-input" placeholder="dd/mm/yyyy">
					<button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 whitespace-nowrap" onclick="exportStatsToExcel()">Xuất Excel</button>
                </div>
                <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full responsive-admin-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Loại Hàng</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Vựa 7 (KG)</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Vựa 9 (KG)</th>
                            </tr>
                        </thead>
                        <tbody id="statsTableBody" class="bg-white divide-y divide-gray-200">
                            </tbody>
                    </table>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-700">Quản Lý Tài Khoản</h2>
                </div>
                 <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 responsive-admin-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Loại Tài Khoản</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                            </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="openUserModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Thêm Tài Khoản</button>
                </div>
				<div class="mt-8 pt-6 border-t border-gray-200">
					<h2 class="text-2xl font-semibold text-gray-700 mb-4">Thông tin ngân hàng</h2>
					<div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
						<p class="text-gray-500 mb-3">Phương thức kiểm tra thông tin ngân hàng của Lái.</p>
						<div class="flex flex-col sm:flex-row gap-4">
							<div class="flex items-center">
								<input id="verification_manual" name="verification_mode" type="radio" value="manual" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
								<label for="verification_manual" class="ml-3 block text-sm font-medium text-gray-700">
									Kiểm tra bằng tay
								</label>
							</div>
							<div class="flex items-center">
								<input id="verification_auto" name="verification_mode" type="radio" value="auto" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
								<label for="verification_auto" class="ml-3 block text-sm font-medium text-gray-700">
									Kiểm tra tự động
								</label>
							</div>
						</div>
						<div class="mt-4 flex justify-end">
							<button onclick="saveVerificationSetting()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">Lưu Cài Đặt</button>
						</div>
					</div>
				</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="driverFormModal" class="driver-form-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 id="driver-form-title">Thêm/Chỉnh Sửa Thông Tin Lái</h3>
                <span class="close-modal" onclick="closeDriverModal()">&times;</span>
            </div>
            <div class="modal-body-custom">
                <form id="driverForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="driverNamePhone">Tên và SĐT Lái:</label>
                            <input type="text" id="driverNamePhone" name="driverNamePhone" placeholder="Nhập tối đa 21 kí tự" maxlength="21">
                        </div>
                        <div>
                            <label for="bankName">Ngân Hàng:</label>
                            <select id="bankName" name="bankName"><option value="">Chọn Ngân Hàng</option></select>
                        </div>
                        <div>
                            <label for="accountNumber">Số Tài Khoản:</label>
                            <input type="number" id="accountNumber" name="accountNumber" placeholder="Số Tài Khoản">
                        </div>
                        <div>
                            <label for="accountName">Tên Tài Khoản:</label>
                            <input type="text" id="accountName" name="accountName" placeholder="Tên Chủ Tài Khoản">
                        </div>
                    </div>
                    <div class="flex justify-end gap-4 mt-6">
                        <button id="addUpdateButton" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="addOrUpdateDriver()">Thêm vào danh sách</button>
                        <button id="cancelButton" type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200 hidden" onclick="closeDriverModal()">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="qrModal" class="qr-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 id="qrModalTitle">Quét mã để thanh toán</h3>
                <span class="close-modal" onclick="closeModal('qrModal')">&times;</span>
            </div>
            <div class="modal-body-custom">
                <div id="qrCodeContainer" class="mx-auto"></div>
                <div id="qrModalInfo" class="text-left my-4"></div>
                <div id="qrModalActions" class="flex justify-center gap-3"></div>
            </div>
        </div>
    </div>

    <div id="invoiceModal" class="invoice-modal">
        <div class="modal-content">
            <div class="modal-header-custom no-print">
                 <h3 id="invoiceModalTitle">Xem Hóa Đơn</h3>
                 <span class="close-modal" onclick="closeModal('invoiceModal')">&times;</span>
            </div>
            <div id="invoice-content-wrapper" class="modal-body-custom">
            </div>
        </div>
    </div>
    
    <div id="driverUsageModal" class="driver-usage-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 class="text-red-600">Không thể xóa Lái</h3>
                <span class="close-modal" onclick="closeModal('driverUsageModal')">&times;</span>
            </div>
            <div class="modal-body-custom">
                <div id="driverUsageInfo" class="text-left my-4 space-y-2 text-sm">
                     <p class="text-gray-800 font-semibold">- Lái này đang được sử dụng trong các toa hàng dưới đây.</p>
                     <p class="text-gray-700">- Bạn phải chuyển các toa này cho một lái khác trước khi có thể xóa.</p>
                     <p class="text-gray-700">- Bấm vào từng toa để xem chi tiết và đổi lái.</p>
                </div>
                <div id="conflictingReportsList" class="my-4 max-h-[60vh] overflow-y-auto p-1 bg-gray-50 rounded-lg">
                </div>
                <div class="flex justify-end gap-3 mt-4">
                     <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('driverUsageModal')">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div id="summaryModal" class="summary-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3>Tổng Hợp Dữ Liệu</h3>
                <span class="close-modal" onclick="closeModal('summaryModal')">&times;</span>
            </div>
            <div id="summaryModalContent" class="modal-body-custom">
                <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-2.25/6">Loại Hàng</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1.5/6">Tổng KG</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-2.25/6">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="userModal" class="user-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 id="userModalTitle">Thêm Tài Khoản Mới</h3>
                <span class="close-modal" onclick="closeModal('userModal')">&times;</span>
            </div>
            <div class="modal-body-custom">
                <form id="userForm">
                    <input type="hidden" id="originalUsername" name="originalUsername">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-medium mb-2">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p id="passwordHelp" class="text-xs text-gray-500 mt-1">Để trống nếu không muốn thay đổi mật khẩu.</p>
                    </div>
                    <div class="mb-6">
                        <label for="userType" class="block text-gray-700 font-medium mb-2">Loại tài khoản</label>
                        <select id="userType" name="userType" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="thuky_v7">Thư ký Vựa 7</option>
                            <option value="thuky_v9">Thư ký Vựa 9</option>
                            <option value="quanly">Quản lý</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg" onclick="closeModal('userModal')">Hủy</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="reportFormModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-[100]" style="display: none;">
        <div class="modal-content bg-white w-full h-full md:w-auto md:h-auto md:max-w-5xl md:max-h-[90vh] rounded-lg shadow-xl flex flex-col">
            <div class="modal-header-custom">
                <h3 id="add-report-title">Thêm/Sửa Dữ Liệu Toa</h3>
                <span class="close-modal" onclick="closeReportModal()">&times;</span>
            </div>
            
            <div id="reportModalBody" class="modal-body-custom">
                <div id="dailyReportForm">
                    <div class="grid grid-cols-1 gap-4 mb-6">
                        <div class="w-full">
                            <label for="dailyReportDriverSearch">Lái:</label>
                            <div class="relative">
                                <input type="text" id="dailyReportDriverSearch" name="dailyReportDriverSearch" placeholder="Tìm & chọn lái..." autocomplete="off" class="w-full focus:border-indigo-500 focus:ring-indigo-500">
                                <div id="driverSearchResults" class="hidden"></div>
                                <button id="changeDriverBtn" class="absolute top-1/2 right-3 -translate-y-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded-md hidden">Đổi</button>
                            </div>
                        </div>
                        <div class="w-full">
                            <label for="Ghi_Chú">Ghi Chú:</label>
                            <input type="text" id="Ghi_Chú" placeholder="Ghi chú" maxlength="110" disabled>
                        </div>
                    </div>
                    <div class="input-group">
                        <div><label for="B1_Thái">B1_Thái:</label><input type="text" id="B1_Thái" class="thai-cell" placeholder="1+2+3..." disabled></div><div><label for="Giá_B1_Thái">Giá_B1_Thái:</label><input type="text" id="Giá_B1_Thái" disabled></div><div><label for="B2_Thái">B2_Thái:</label><input type="text" id="B2_Thái" class="thai-cell" disabled></div><div><label for="Giá_B2_Thái">Giá_B2_Thái:</label><input type="text" id="Giá_B2_Thái" disabled></div><div><label for="C1_Thái">C1_Thái:</label><input type="text" id="C1_Thái" class="thai-cell" disabled></div><div><label for="Giá_C1_Thái">Giá_C1_Thái:</label><input type="text" id="Giá_C1_Thái" disabled></div><div><label for="C2_Thái">C2_Thái:</label><input type="text" id="C2_Thái" class="thai-cell" disabled></div><div><label for="Giá_C2_Thái">Giá_C2_Thái:</label><input type="text" id="Giá_C2_Thái" disabled></div><div><label for="C3_Thái">C3_Thái:</label><input type="text" id="C3_Thái" class="thai-cell" disabled></div><div><label for="Giá_C3_Thái">Giá_C3_Thái:</label><input type="text" id="Giá_C3_Thái" disabled></div><div><label for="D1_Thái">D1_Thái:</label><input type="text" id="D1_Thái" class="thai-cell" disabled></div><div><label for="Giá_D1_Thái">Giá_D1_Thái:</label><input type="text" id="Giá_D1_Thái" disabled></div><div><label for="D2_Thái">D2_Thái:</label><input type="text" id="D2_Thái" class="thai-cell" disabled></div><div><label for="Giá_D2_Thái">Giá_D2_Thái:</label><input type="text" id="Giá_D2_Thái" disabled></div><div><label for="E_Thái">E_Thái:</label><input type="text" id="E_Thái" class="thai-cell" disabled></div><div><label for="Giá_E_Thái">Giá_E_Thái:</label><input type="text" id="Giá_E_Thái" disabled></div><div><label for="Chợ_Thái">Chợ_Thái:</label><input type="text" id="Chợ_Thái" class="thai-cell" disabled></div><div><label for="Giá_Chợ_Thái">Giá_Chợ_Thái:</label><input type="text" id="Giá_Chợ_Thái" disabled></div><div><label for="Xơ_Thái">Xơ_Thái:</label><input type="text" id="Xơ_Thái" class="thai-cell" disabled></div><div><label for="Giá_Xơ_Thái">Giá_Xơ_Thái:</label><input type="text" id="Giá_Xơ_Thái" disabled></div>
                        <div><label for="A1_indo">A1_indo:</label><input type="text" id="A1_indo" class="indo-cell" disabled></div><div><label for="Giá_A1_indo">Giá_A1_indo:</label><input type="text" id="Giá_A1_indo" disabled></div><div><label for="A2_indo">A2_indo:</label><input type="text" id="A2_indo" class="indo-cell" disabled></div><div><label for="Giá_A2_indo">Giá_A2_indo:</label><input type="text" id="Giá_A2_indo" disabled></div><div><label for="B1_indo">B1_indo:</label><input type="text" id="B1_indo" class="indo-cell" disabled></div><div><label for="Giá_B1_indo">Giá_B1_indo:</label><input type="text" id="Giá_B1_indo" disabled></div><div><label for="B2_indo">B2_indo:</label><input type="text" id="B2_indo" class="indo-cell" disabled></div><div><label for="Giá_B2_indo">Giá_B2_indo:</label><input type="text" id="Giá_B2_indo" disabled></div><div><label for="B3_indo">B3_indo:</label><input type="text" id="B3_indo" class="indo-cell" disabled></div><div><label for="Giá_B3_indo">Giá_B3_indo:</label><input type="text" id="Giá_B3_indo" disabled></div><div><label for="C1_indo">C1_indo:</label><input type="text" id="C1_indo" class="indo-cell" disabled></div><div><label for="Giá_C1_indo">Giá_C1_indo:</label><input type="text" id="Giá_C1_indo" disabled></div><div><label for="C2_indo">C2_indo:</label><input type="text" id="C2_indo" class="indo-cell" disabled></div><div><label for="Giá_C2_indo">Giá_C2_indo:</label><input type="text" id="Giá_C2_indo" disabled></div>
                        <div><label for="Chợ_1_indo">Chợ_1_indo:</label><input type="text" id="Chợ_1_indo" class="indo-cell" disabled></div><div><label for="Giá_Chợ_1_indo">Giá_Chợ_1_indo:</label><input type="text" id="Giá_Chợ_1_indo" disabled></div>
                        <div><label for="Chợ_2_indo">Chợ_2_indo:</label><input type="text" id="Chợ_2_indo" class="indo-cell" disabled></div><div><label for="Giá_Chợ_2_indo">Giá_Chợ_2_indo:</label><input type="text" id="Giá_Chợ_2_indo" disabled></div>
                        <div><label for="Xơ_indo">Xơ_indo:</label><input type="text" id="Xơ_indo" class="indo-cell" disabled></div><div><label for="Giá_Xơ_indo">Giá_Xơ_indo:</label><input type="text" id="Giá_Xơ_indo" disabled></div>
                        <div class="w-full hidden"><label for="Tổng_KG_display">Tổng KG:</label><input type="text" id="Tổng_KG_display" readonly class="bg-gray-100 text-gray-700 font-bold"></div>
                        <div class="w-full hidden"><label for="Thành_Tiền_display">Thành Tiền:</label><input type="text" id="Thành_Tiền_display" readonly class="bg-gray-100 text-gray-700 font-bold"></div>
                    </div>
                </div>
            </div>

            <!-- MODIFIED: Sticky Footer with Expandable Options -->
            <div id="sticky-footer-container" class="border-t bg-gray-50 flex-shrink-0">
                <!-- The new expandable options panel -->
                <div id="sticky-options-panel" class="p-4 border-b border-gray-200 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <!-- Options checkboxes -->
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input id="khongThanhToan" name="khongThanhToan" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="khongThanhToan" class="ml-2 block text-sm font-medium text-gray-700">Không thanh toán toa này</label>
                            </div>
                            <div class="flex items-center">
                                <input id="toaDiBan" name="toaDiBan" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label for="toaDiBan" class="ml-2 block text-sm font-medium text-gray-700">Toa mít đi bán</label>
                            </div>
                        </div>
                        <!-- Money adjustment and notes -->
                        <div class="space-y-2">
                            <div>
                                <label for="tuyChonTien" class="text-sm font-medium text-gray-700">Tùy chọn +/- tiền cho toa:</label>
                                <div class="input-wrapper">
                                    <input type="text" id="tuyChonTien" name="tuyChonTien" class="mt-1 w-full" placeholder="+50000 or -10000">
                                    <span class="calculation-result"></span>
                                </div>
                            </div>
                            <div>
                                <label for="ghiChuTuyChon" class="text-sm font-medium text-gray-700">Ghi chú tùy chọn:</label>
                                <input type="text" id="ghiChuTuyChon" name="ghiChuTuyChon" class="mt-1 w-full" placeholder="Ghi chú cho phần tùy chọn tiền">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- The original totals bar with the new toggle button -->
                <div id="sticky-totals-bar" class="p-2">
                    <div class="max-w-4xl mx-auto flex justify-around items-center text-center">
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Tổng KG</label>
                            <span id="sticky-total-kg" class="text-lg font-bold text-gray-800">0</span>
                        </div>
                        <button id="sticky-options-toggle" class="p-2 rounded-full hover:bg-gray-200 transition-colors">
                            <span class="text-2xl font-bold text-gray-600">︽</span>
                        </button>
                        <div>
                            <label class="block text-xs font-medium text-gray-500">Thành Tiền</label>
                            <span id="sticky-total-tien" class="text-lg font-bold text-indigo-600">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MODIFICATION -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const userType = '<?php echo $user_type; ?>';
    </script>
    <script src="main.js"></script>
</body>
</html>
