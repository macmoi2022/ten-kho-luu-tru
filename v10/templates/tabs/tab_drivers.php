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