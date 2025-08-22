// Chứa các hàm dành cho tab Quản Lý
import { customAlert, confirm } from './utils.js';
import { verificationMode, setVerificationMode } from './state.js';
import { applyVerificationModeUI } from './drivers.js';

async function loadUsers() {
    try {
        const response = await fetch('manage_users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'load' })
        });
        const result = await response.json();
        if (result.success) {
            renderUsersTable(result.data);
        } else {
            customAlert('Lỗi tải danh sách người dùng: ' + result.message);
        }
    } catch (error) {
        customAlert('Lỗi kết nối khi tải người dùng.');
    }
}

function renderUsersTable(users) {
    const tableBody = document.getElementById('usersTableBody');
    tableBody.innerHTML = '';
    users.forEach(user => {
        const row = tableBody.insertRow();
        
        const usernameCell = row.insertCell();
        usernameCell.className = 'px-2 sm:px-6 py-3 text-center whitespace-nowrap';
        usernameCell.textContent = user.username;

        const typeCell = row.insertCell();
        typeCell.className = 'px-2 sm:px-6 py-3 text-center whitespace-nowrap';
        typeCell.textContent = user.type;

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

function openUserModal(user = null) {
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.getElementById('userModalTitle');
    const passwordHelp = document.getElementById('passwordHelp');
    const usernameInput = document.getElementById('username');
    
    form.reset();

    if (user) {
        title.textContent = 'Sửa Tài Khoản';
        document.getElementById('originalUsername').value = user.username;
        usernameInput.value = user.username;
        usernameInput.disabled = true;
        document.getElementById('userType').value = user.type;
        document.getElementById('password').placeholder = "Để trống nếu không đổi";
        passwordHelp.style.display = 'block';
    } else {
        title.textContent = 'Thêm Tài Khoản Mới';
        usernameInput.disabled = false;
        document.getElementById('password').placeholder = "";
        passwordHelp.style.display = 'none';
    }
    modal.style.display = 'block';
}

async function handleUserFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const originalUsername = form.originalUsername.value;
    const user = {
        username: form.username.value,
        password: form.password.value,
        type: form.userType.value
    };

    const action = originalUsername ? 'update' : 'add';
    if (action === 'add' && !user.password) {
        customAlert('Vui lòng nhập mật khẩu cho tài khoản mới.');
        return;
    }

    try {
        const response = await fetch('manage_users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action, user, original_username: originalUsername })
        });
        const result = await response.json();
        if (result.success) {
            customAlert(result.message);
            closeModal('userModal');
            loadUsers();
        } else {
            customAlert('Lỗi: ' + result.message);
        }
    } catch (error) {
        customAlert('Lỗi kết nối khi lưu tài khoản.');
    }
}

async function deleteUser(username) {
    confirm(`Bạn có chắc muốn xóa tài khoản "${username}"?`).then(async confirmed => {
        if (confirmed) {
            try {
                const response = await fetch('manage_users.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', username })
                });
                const result = await response.json();
                 if (result.success) {
                    customAlert(result.message);
                    loadUsers();
                } else {
                    customAlert('Lỗi: ' + result.message);
                }
            } catch (error) {
                customAlert('Lỗi kết nối khi xóa tài khoản.');
            }
        }
    });
}

async function loadStats() {
    const date = document.getElementById('statsDate').value;
    try {
        const response = await fetch('get_stats.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ date })
        });
        const result = await response.json();
        if (result.success) {
            renderStatsTable(result.data);
        } else {
            customAlert('Lỗi tải thống kê: ' + result.message);
        }
    } catch (error) {
        customAlert('Lỗi kết nối khi tải thống kê.');
    }
}

function renderStatsTable(stats) {
    const tableBody = document.getElementById('statsTableBody');
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

    let grandTotalAmount = 0;

    let totalThaiKg7 = 0, totalThaiKg9 = 0, totalThaiAmount = 0;
    thaiTypeOrder.forEach(type => {
        const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
        const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
        totalThaiKg7 += detail7.kg;
        totalThaiKg9 += detail9.kg;
        totalThaiAmount += (detail7.amount + detail9.amount);
    });
    grandTotalAmount += totalThaiAmount;

    const thaiHeaderRow = createRow(
        `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">
            <div class="flex items-center justify-center cursor-pointer" onclick="toggleStatsCategory('thai', this.closest('tr'))">
                <span>HÀNG THÁI</span><span class="toggle-icon ml-2 transition-transform duration-300">▼</span>
            </div>
        </td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalThaiKg7 > 0 ? totalThaiKg7.toLocaleString('vi-VN') : ''}</td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalThaiKg9 > 0 ? totalThaiKg9.toLocaleString('vi-VN') : ''}</td>`,
        `stat-category-header`
    );
    
    thaiTypeOrder.forEach(type => {
        const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
        const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
        if (detail7.kg > 0 || detail9.kg > 0) {
            createRow(
                `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${type.replace('_Thái', '')}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail7.kg > 0 ? detail7.kg.toLocaleString('vi-VN') : ''}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail9.kg > 0 ? detail9.kg.toLocaleString('vi-VN') : ''}</td>`,
                'stat-detail-row stat-detail-thai'
            );
        }
    });
    if (totalThaiKg7 > 0 || totalThaiKg9 > 0) {
        createRow(
            `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">Tổng Thái</td>
             <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalThaiKg7 > 0 ? totalThaiKg7.toLocaleString('vi-VN') : ''}</td>
             <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalThaiKg9 > 0 ? totalThaiKg9.toLocaleString('vi-VN') : ''}</td>`,
            'bg-gray-100 stat-detail-row stat-detail-thai'
        );
    }

    let totalIndoKg7 = 0, totalIndoKg9 = 0, totalIndoAmount = 0;
    indoTypeOrder.forEach(type => {
        const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
        const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
        totalIndoKg7 += detail7.kg;
        totalIndoKg9 += detail9.kg;
        totalIndoAmount += (detail7.amount + detail9.amount);
    });
    grandTotalAmount += totalIndoAmount;
    
    const indoHeaderRow = createRow(
        `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">
            <div class="flex items-center justify-center cursor-pointer" onclick="toggleStatsCategory('indo', this.closest('tr'))">
                <span>HÀNG INDO</span><span class="toggle-icon ml-2 transition-transform duration-300">▼</span>
            </div>
        </td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalIndoKg7 > 0 ? totalIndoKg7.toLocaleString('vi-VN') : ''}</td>
        <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalIndoKg9 > 0 ? totalIndoKg9.toLocaleString('vi-VN') : ''}</td>`,
        `stat-category-header`
    );

    indoTypeOrder.forEach(type => {
        const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
        const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
        if (detail7.kg > 0 || detail9.kg > 0) {
            createRow(
                `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${type.replace('_indo', '')}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail7.kg > 0 ? detail7.kg.toLocaleString('vi-VN') : ''}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail9.kg > 0 ? detail9.kg.toLocaleString('vi-VN') : ''}</td>`,
                'stat-detail-row stat-detail-indo'
            );
        }
    });
    if (totalIndoKg7 > 0 || totalIndoKg9 > 0) {
        createRow(
            `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">Tổng Indo</td>
             <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalIndoKg7 > 0 ? totalIndoKg7.toLocaleString('vi-VN') : ''}</td>
             <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalIndoKg9 > 0 ? totalIndoKg9.toLocaleString('vi-VN') : ''}</td>`,
            'bg-gray-100 stat-detail-row stat-detail-indo'
        );
    }

    const grandTotalKg7 = totalThaiKg7 + totalIndoKg7;
    const grandTotalKg9 = totalThaiKg9 + totalIndoKg9;
    
    const totalKgRow = tableBody.insertRow();
    totalKgRow.className = `font-extrabold text-blue-800`;
    totalKgRow.innerHTML = `
        <td class="px-2 sm:px-6 py-4 text-center">TỔNG CỘNG (KG)</td>
        <td class="px-2 sm:px-6 py-4 text-center">${grandTotalKg7 > 0 ? grandTotalKg7.toLocaleString('vi-VN') : ''}</td>
        <td class="px-2 sm:px-6 py-4 text-center">${grandTotalKg9 > 0 ? grandTotalKg9.toLocaleString('vi-VN') : ''}</td>
    `;

    const totalAmountRow = tableBody.insertRow();
    totalAmountRow.className = `font-extrabold text-green-800`;
    totalAmountRow.innerHTML = `
        <td class="px-2 sm:px-6 py-4 text-center">TỔNG THÀNH TIỀN</td>
        <td class="px-2 sm:px-6 py-4 text-center" colspan="2">${grandTotalAmount > 0 ? grandTotalAmount.toLocaleString('vi-VN') + ' đ' : ''}</td>
    `;

    document.querySelectorAll('.stat-detail-row').forEach(row => row.style.display = 'none');
}

function toggleStatsCategory(category, headerRow) {
    const icon = headerRow.querySelector('.toggle-icon');
    const isExpanded = headerRow.classList.toggle('expanded');
    
    if (icon) {
        icon.style.transform = isExpanded ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    const detailRows = document.querySelectorAll(`.stat-detail-${category}`);
    detailRows.forEach(row => {
        row.style.display = isExpanded ? 'table-row' : 'none';
    });

    const totalCells = headerRow.querySelectorAll('.header-total-cell');
    totalCells.forEach(cell => {
        cell.style.display = isExpanded ? 'none' : 'table-cell';
    });
    
    const firstCell = headerRow.querySelector('td:first-child');
    if (isExpanded) {
        firstCell.setAttribute('colspan', '3');
    } else {
        firstCell.removeAttribute('colspan');
    }
}

async function saveVerificationSetting() {
    const selectedMode = document.querySelector('input[name="verification_mode"]:checked').value;
    try {
        const response = await fetch('manage_settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ verification_mode: selectedMode })
        });
        const result = await response.json();
        if (result.success) {
            setVerificationMode(selectedMode);
            applyVerificationModeUI();
            customAlert('Đã lưu cài đặt thành công.');
        } else {
            customAlert('Lỗi: ' + result.message);
        }
    } catch (error) {
        customAlert('Lỗi kết nối khi lưu cài đặt.');
    }
}

async function loadVerificationSetting() {
    try {
        const response = await fetch('manage_settings.php');
        const settings = await response.json();
        if (settings && settings.verification_mode) {
            setVerificationMode(settings.verification_mode);
            const radioBtn = document.getElementById(`verification_${verificationMode}`);
            if (radioBtn) radioBtn.checked = true;
        }
    } catch (e) {
        console.error("Không thể tải file settings, sử dụng chế độ mặc định 'manual'.");
        setVerificationMode('manual');
        const radioBtn = document.getElementById('verification_manual');
        if (radioBtn) radioBtn.checked = true;
    }
    applyVerificationModeUI();
}

export {
    loadUsers,
    openUserModal,
    handleUserFormSubmit,
    deleteUser,
    loadStats,
    toggleStatsCategory,
    saveVerificationSetting,
    loadVerificationSetting
};