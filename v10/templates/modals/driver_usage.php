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
