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
		<!-- Chọn chế độ kiểm tra tài khoản -->
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
