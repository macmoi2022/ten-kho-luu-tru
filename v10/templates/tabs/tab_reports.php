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
