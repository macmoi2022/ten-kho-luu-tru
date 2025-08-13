// assets/js/admin.js

// Tải danh sách người dùng từ server
async function loadUsers() {
    const result = await api.loadUsers();
    if (result.success) {
        renderUsersTable(result.data);
    } else {
        customAlert('Lỗi tải danh sách người dùng: ' + result.message);
    }
}

// Hiển thị danh sách người dùng ra bảng
function renderUsersTable(users) {
    const tableBody = document.getElementById('usersTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = '';
    users.forEach(user => {
        const row = tableBody.insertRow();
        row.className = 'text-center';
        row.insertCell().textContent = user.username;
        row.insertCell().textContent = user.type;
        const actionsCell = row.insertCell();
        actionsCell.className = 'px-2 sm:px-6 py-3 text-center';
        
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'flex gap-2 justify-center';

        const editButton = document.createElement('button');
        editButton.textContent = 'Sửa';
        editButton.className = 'bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md';
        editButton.onclick = () => openUserModal(user);
        buttonContainer.appendChild(editButton);

        if (user.username !== 'admin') {
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Xóa';
            deleteButton.className = 'bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            deleteButton.onclick = () => deleteUser(user.username);
            buttonContainer.appendChild(deleteButton);
        }
        
        actionsCell.appendChild(buttonContainer);
    });
}

// Mở modal để thêm/sửa người dùng
function openUserModal(user = null) {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.getElementById('userModalTitle');
    const passwordHelp = document.getElementById('passwordHelp');
    const usernameInput = document.getElementById('username');
    
    form.reset();

    if (user) {
        title.textContent = 'Sửa Tài Khoản';
        form.originalUsername.value = user.username;
        usernameInput.value = user.username;
        usernameInput.disabled = true; // Không cho sửa username
        form.userType.value = user.type;
        form.password.placeholder = "Để trống nếu không đổi";
        passwordHelp.style.display = 'block';
    } else {
        title.textContent = 'Thêm Tài Khoản Mới';
        form.originalUsername.value = '';
        usernameInput.disabled = false;
        form.password.placeholder = "Nhập mật khẩu";
        passwordHelp.style.display = 'none';
    }
    modal.style.display = 'block';
}

// Xử lý việc gửi form thêm/sửa người dùng
async function handleUserFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const originalUsername = form.originalUsername.value;
    const user = {
        username: form.username.value.trim(),
        password: form.password.value,
        type: form.userType.value
    };

    if (!user.username) return customAlert("Tên đăng nhập không được để trống.");

    const action = originalUsername ? 'update' : 'add';
    if (action === 'add' && !user.password) {
        return customAlert('Vui lòng nhập mật khẩu cho tài khoản mới.');
    }

    const result = await api.saveUser(action, user, originalUsername);
    if (result.success) {
        customAlert(result.message);
        closeModal('userModal');
        loadUsers();
    } else {
        customAlert('Lỗi: ' + result.message);
    }
}

// Xóa một người dùng
async function deleteUser(username) {
    const confirmed = await confirm(`Bạn có chắc muốn xóa tài khoản "${username}"?`);
    if (confirmed) {
        const result = await api.deleteUser(username);
        if (result.success) {
            customAlert(result.message);
            loadUsers();
        } else {
            customAlert('Lỗi: ' + result.message);
        }
    }
}

// Tải dữ liệu thống kê từ server
async function loadStats() {
    const date = document.getElementById('statsDate').value;
    const result = await api.loadStats(date);
    if (result.success) {
        renderStatsTable(result.data);
    } else {
        customAlert('Lỗi tải thống kê: ' + result.message);
    }
}

// Hiển thị dữ liệu thống kê ra bảng
function renderStatsTable(stats) {
    const tableBody = document.getElementById('statsTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = '';
    const vua7Data = stats.vua7;
    const vua9Data = stats.vua9;

    const createRow = (content, classes = '') => {
        const row = tableBody.insertRow();
        row.className = classes;
        row.innerHTML = content;
        return row;
    };

    const thaiTypeOrder = ["B1_Thái", "B2_Thái", "C1_Thái", "C2_Thái", "C3_Thái", "D1_Thái", "D2_Thái", "E_Thái", "Chợ_Thái", "Xơ_Thái"];
    const indoTypeOrder = ["A1_indo", "A2_indo", "B1_indo", "B2_indo", "B3_indo", "C1_indo", "C2_indo", "Chợ_1_indo", "Chợ_2_indo", "Xơ_indo"];
    let grandTotalAmount = vua7Data.total_amount + vua9Data.total_amount;

    // --- HÀNG THÁI ---
    const totalThaiKg7 = vua7Data.total_kg_thai;
    const totalThaiKg9 = vua9Data.total_kg_thai;
    const hasThaiData = totalThaiKg7 > 0 || totalThaiKg9 > 0;
    
    createRow(
        `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">
            <div class="flex items-center justify-center cursor-pointer" onclick="toggleStatsCategory('thai', this.closest('tr'))">
                <span>HÀNG THÁI</span><span class="toggle-icon ml-2 transition-transform duration-300">▼</span>
            </div>
        </td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalThaiKg7 > 0 ? totalThaiKg7.toLocaleString('vi-VN') : ''}</td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalThaiKg9 > 0 ? totalThaiKg9.toLocaleString('vi-VN') : ''}</td>`,
        `stat-category-header ${hasThaiData ? 'bg-yellow-100' : ''}`
    );
    
    thaiTypeOrder.forEach(type => {
        const detail7 = vua7Data.details[type] || { kg: 0 };
        const detail9 = vua9Data.details[type] || { kg: 0 };
        if (detail7.kg > 0 || detail9.kg > 0) {
            createRow(
                `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${type.replace('_Thái', '')}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail7.kg > 0 ? detail7.kg.toLocaleString('vi-VN') : ''}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail9.kg > 0 ? detail9.kg.toLocaleString('vi-VN') : ''}</td>`,
                'stat-detail-row stat-detail-thai'
            );
        }
    });

    // --- HÀNG INDO ---
    const totalIndoKg7 = vua7Data.total_kg_indo;
    const totalIndoKg9 = vua9Data.total_kg_indo;
    const hasIndoData = totalIndoKg7 > 0 || totalIndoKg9 > 0;
    
    createRow(
        `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">
            <div class="flex items-center justify-center cursor-pointer" onclick="toggleStatsCategory('indo', this.closest('tr'))">
                <span>HÀNG INDO</span><span class="toggle-icon ml-2 transition-transform duration-300">▼</span>
            </div>
        </td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalIndoKg7 > 0 ? totalIndoKg7.toLocaleString('vi-VN') : ''}</td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalIndoKg9 > 0 ? totalIndoKg9.toLocaleString('vi-VN') : ''}</td>`,
        `stat-category-header ${hasIndoData ? 'bg-red-100' : ''}`
    );

    indoTypeOrder.forEach(type => {
        const detail7 = vua7Data.details[type] || { kg: 0 };
        const detail9 = vua9Data.details[type] || { kg: 0 };
        if (detail7.kg > 0 || detail9.kg > 0) {
            createRow(
                `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${type.replace('_indo', '')}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail7.kg > 0 ? detail7.kg.toLocaleString('vi-VN') : ''}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail9.kg > 0 ? detail9.kg.toLocaleString('vi-VN') : ''}</td>`,
                'stat-detail-row stat-detail-indo'
            );
        }
    });

    // --- TỔNG CỘNG ---
    const grandTotalKg7 = vua7Data.total_kg;
    const grandTotalKg9 = vua9Data.total_kg;
    
    createRow(
        `<td class="px-2 sm:px-6 py-4 text-center">TỔNG CỘNG (KG)</td>
        <td class="px-2 sm:px-6 py-4 text-center">${grandTotalKg7 > 0 ? grandTotalKg7.toLocaleString('vi-VN') : ''}</td>
        <td class="px-2 sm:px-6 py-4 text-center">${grandTotalKg9 > 0 ? grandTotalKg9.toLocaleString('vi-VN') : ''}</td>`,
        'bg-blue-100 font-extrabold text-blue-800'
    );
    createRow(
        `<td class="px-2 sm:px-6 py-4 text-center">TỔNG THÀNH TIỀN</td>
        <td class="px-2 sm:px-6 py-4 text-center" colspan="2">${grandTotalAmount > 0 ? grandTotalAmount.toLocaleString('vi-VN') + ' đ' : ''}</td>`,
        'bg-green-100 font-extrabold text-green-800'
    );

    document.querySelectorAll('.stat-detail-row').forEach(row => row.style.display = 'none');
}