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
