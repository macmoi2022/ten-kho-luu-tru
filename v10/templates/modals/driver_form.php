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
