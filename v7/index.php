<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$user_type = $_SESSION['type'];
$username = $_SESSION['username'];

// Xác định thư mục dữ liệu mặc định dựa trên loại người dùng
$initial_data_dir = 'Data_vua7/'; // Mặc định cho quản lý và thư ký vựa 7
if ($user_type === 'thuky_v9') {
    $initial_data_dir = 'Data_vua9/';
}
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-7xl">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Vựa Mít Ngọc Anh HD68</h1>
            <div class="mt-3">
                <span class="text-gray-700 align-middle">Xin chào, <span class="font-bold"><?php echo htmlspecialchars($username); ?></span>!</span>
                <a href="logout.php" class="ml-4 inline-block bg-red-500 hover:bg-red-600 text-white font-bold text-sm py-1 px-2 rounded-lg shadow-md transition-transform transform hover:scale-105">
                    Đăng xuất
                </a>
            </div>
        </div>

        <div class="sticky-tabs flex flex-wrap justify-center mb-6 border-b border-gray-200">
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
            
            <div class="hidden md:flex justify-between items-center gap-4 mb-4">
                 <div class="flex items-center">
                    <input type="checkbox" id="desktopFilterUnverified" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="desktopFilterUnverified" class="ml-2 block text-sm font-medium text-gray-700 whitespace-nowrap">Chưa đánh dấu</label>
                </div>
                <input type="search" id="desktopDriverSearch" placeholder="Tìm kiếm lái..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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

            <h3 id="driver-form-title" class="text-xl font-semibold text-gray-700 mt-8 mb-4">Thêm/Chỉnh Sửa Thông Tin Lái</h3>
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
                <button id="addUpdateButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="addOrUpdateDriver()">Thêm vào danh sách</button>
                <button id="cancelButton" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200 hidden" onclick="cancelEdit()">Hủy</button>
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
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="reportDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                    <input type="text" id="reportDate" class="flatpickr-input w-full" placeholder="dd/mm/yyyy">
                    <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="exportReportsToExcel()">Xuất Excel</button>			
                </div>
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
                <div class="flex items-center font-bold p-2 bg-gray-100 border-b border-gray-300 rounded-t-lg">
                    <div class="w-[40px] text-center text-xs text-gray-500 uppercase">Toa</div>
                    <div class="flex-grow text-xs text-gray-500 uppercase pl-2">Lái</div>
                </div>
                <div id="mobileReportList"></div>
            </div>

            <div class="mt-4 flex justify-end gap-4">
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="openSummaryModal()">Tổng Hợp Dữ Liệu</button>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="handleAddNewRowClick()">Thêm Dòng Mới</button>
            </div>
            
            <h3 id="add-report-title" class="text-xl font-semibold text-gray-700 mt-8 mb-4 hidden">Thêm Dữ Liệu Báo Cáo</h3>
            <form id="dailyReportForm" class="hidden">
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div class="w-full">
                        <label for="dailyReportDriverSearch">Lái:</label>
                        <div class="relative">
                            <input type="text" id="dailyReportDriverSearch" name="dailyReportDriverSearch" placeholder="Tìm & chọn lái..." autocomplete="off" class="w-full focus:border-indigo-500 focus:ring-indigo-500">
                            <div id="driverSearchResults" class="hidden"></div>
                            <button type="button" id="changeDriverBtn" class="absolute top-1/2 right-3 -translate-y-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded-md hidden">Đổi</button>
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
            </form>
        </div>

        <?php if ($user_type === 'quanly'): ?>
        <div id="tab3" class="tab-content p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Thống Kê Trong Ngày</h2>
                <div class="flex items-center gap-2 mb-4">
                    <label for="statsDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                    <input type="text" id="statsDate" class="flatpickr-input" placeholder="dd/mm/yyyy">
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
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="sticky-totals-footer" class="fixed bottom-0 left-0 right-0 bg-white p-2 border-t shadow-lg z-40 hidden transition-transform duration-300 transform translate-y-full">
        <div class="max-w-4xl mx-auto flex justify-around text-center">
            <div>
                <label class="block text-xs font-medium text-gray-500">Tổng KG</label>
                <span id="sticky-total-kg" class="text-lg font-bold text-gray-800">0</span>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500">Thành Tiền</label>
                <span id="sticky-total-tien" class="text-lg font-bold text-indigo-600">0</span>
            </div>
        </div>
    </div>

    <div id="qrModal" class="qr-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('qrModal')">&times;</span>
            <h3 id="qrModalTitle" class="text-xl font-bold mb-4">Quét mã để thanh toán</h3>
            <img id="qrCodeImg" src="" alt="QR Code" class="mx-auto">
            <div id="qrModalInfo" class="text-left my-4"></div>
            <div id="qrModalActions" class="flex justify-end gap-3"></div>
        </div>
    </div>

    <div id="invoiceModal" class="invoice-modal">
        <div class="modal-content overflow-auto">
            <span class="close-modal no-print" onclick="closeModal('invoiceModal')">&times;</span>
            <div class="mb-4 no-print modal-header flex justify-between items-center">
                 <h3 id="invoiceModalTitle" class="text-xl font-bold modal-title">Xem Hóa Đơn</h3>
            </div>
            <div id="invoice-content-wrapper">
            </div>
        </div>
    </div>
    
    <div id="driverUsageModal" class="driver-usage-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('driverUsageModal')">&times;</span>
            <h3 class="text-xl font-bold mb-4 text-red-600">Không thể xóa Lái</h3>
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

    <div id="summaryModal" class="summary-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('summaryModal')">&times;</span>
            <h3 class="text-xl font-bold mb-4">Tổng Hợp Dữ Liệu</h3>
            <div id="summaryModalContent" class="max-h-[70vh] overflow-y-auto">
                <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Loại Hàng</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Tổng KG</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                 <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('summaryModal')">Đóng</button>
            </div>
        </div>
    </div>
    
    <div id="userModal" class="user-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('userModal')">&times;</span>
            <h3 id="userModalTitle" class="text-xl font-bold mb-4">Thêm Tài Khoản Mới</h3>
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


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        window.APP_CONFIG = {
            userType: '<?php echo $user_type; ?>',
            username: '<?php echo $username; ?>',
            initialDataDir: '<?php echo $initial_data_dir; ?>'
        };
    </script>

    <script src="assets/js/api.js"></script>
    <script src="assets/js/ui.js"></script>
    <script src="assets/js/driver.js"></script>
    <script src="assets/js/report.js"></script>
    <?php if ($user_type === 'quanly'): ?>
    <script src="assets/js/admin.js"></script>
    <?php endif; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>