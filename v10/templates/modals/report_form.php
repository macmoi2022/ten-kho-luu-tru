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

		<div id="sticky-totals-footer" class="p-2 border-t bg-gray-50 flex-shrink-0">
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
	</div>
</div>
