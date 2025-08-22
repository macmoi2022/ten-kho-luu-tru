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
