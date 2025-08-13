// assets/js/driver.js

// Biến cục bộ cho module này, được sử dụng để quản lý trạng thái
let driverList = [];
let editingIndexTab1 = -1;
let driversCurrentlyShown = 10;
const DRIVERS_PER_PAGE = 10;

// Sắp xếp danh sách lái xe theo tên
function sortDriverList() {
    driverList.sort((a, b) => {
        return a.driverNamePhone.localeCompare(b.driverNamePhone, 'vi', { sensitivity: 'base' });
    });
}

// Xóa trắng form nhập thông tin lái xe
function clearDriverForm() {
    document.getElementById('driverNamePhone').value = '';
    document.getElementById('bankName').value = '';
    document.getElementById('accountNumber').value = '';
    document.getElementById('accountName').value = '';
}

// Hủy bỏ chế độ chỉnh sửa thông tin
function cancelEdit() {
    editingIndexTab1 = -1;
    clearDriverForm();
    const addUpdateButton = document.getElementById('addUpdateButton');
    addUpdateButton.textContent = 'Thêm vào danh sách';
    addUpdateButton.classList.remove('bg-green-600', 'hover:bg-green-700');
    addUpdateButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
    document.getElementById('cancelButton').classList.add('hidden');
}

// Đổ danh sách ngân hàng vào dropdown
function loadBanksIntoDropdown() {
    const bankSelect = document.getElementById('bankName');
    bankSelect.innerHTML = '<option value="">Chọn Ngân Hàng</option>';
    const sortedBanks = banksData.sort((a, b) => a.name.localeCompare(b.name));
    sortedBanks.forEach(bank => {
        const option = document.createElement('option');
        option.value = bank.name;
        option.textContent = bank.name;
        bankSelect.appendChild(option);
    });
}

// Hiển thị danh sách lái xe lên giao diện (cả desktop và mobile)
function renderDriverList() {
    const isMobile = window.innerWidth <= 768;
    const searchTerm = (isMobile ? document.getElementById('mobileDriverSearch').value : document.getElementById('desktopDriverSearch').value) || '';
    const showOnlyUnverified = isMobile ? document.getElementById('mobileFilterUnverified').checked : document.getElementById('desktopFilterUnverified').checked;

    let filteredList = driverList;
    if (showOnlyUnverified) {
        filteredList = filteredList.filter(d => !d.isVerified);
    }
    if (searchTerm.trim().length > 0) {
        const lowercasedFilter = removeAccents(searchTerm.toLowerCase());
        filteredList = filteredList.filter(driver =>
            removeAccents((driver.driverNamePhone || '').toLowerCase()).includes(lowercasedFilter)
        );
    }
    
    const listToRender = filteredList.slice(0, driversCurrentlyShown);

    if (isMobile) {
        const mobileList = document.getElementById('mobileDriverList');
        mobileList.innerHTML = "";
        listToRender.forEach((driver, displayIndex) => {
            const originalIndex = driverList.findIndex(d => d === driver);
            const item = document.createElement('div');
            item.className = 'mobile-driver-item';
            const verifiedIconHtml = driver.isVerified ? `<span class="verified-check">&#10004;</span>` : '';
            item.innerHTML = `
                <div class="mobile-driver-main" onclick="toggleDriverDetails(this)">
                    <span class="stt">${displayIndex + 1}</span>
                    <span class="name">${driver.driverNamePhone}${verifiedIconHtml}</span>
                    <span class="toggle-icon">▼</span>
                </div>
                <div class="mobile-driver-details">
                    <div class="mobile-driver-info">
                        <p><strong>Ngân Hàng:</strong> <span>${driver.bankName}</span></p>
                        <p><strong>Số TK:</strong> <span>${driver.accountNumber}</span></p>
                        <p><strong>Chủ TK:</strong> <span>${driver.accountName}</span></p>
                    </div>
                    <div class="mobile-driver-actions">
                        <button class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); verifyBankAccountAPI(${originalIndex})">Kiểm tra STK</button>
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); editDriver(${originalIndex})">Sửa</button>
                        <button class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); deleteDriver(${originalIndex})">Xóa</button>
                    </div>
                </div>`;
            mobileList.appendChild(item);
        });
    } else { // Desktop view
        const tableBody = document.getElementById('driverListTableBody');
        tableBody.innerHTML = "";
        listToRender.forEach((driver, displayIndex) => {
            const originalIndex = driverList.findIndex(d => d === driver);
            const row = tableBody.insertRow();
            row.insertCell().textContent = displayIndex + 1;
            const nameCell = row.insertCell();
            nameCell.textContent = driver.driverNamePhone;
            if (driver.isVerified) {
                const checkIcon = document.createElement('span');
                checkIcon.className = 'verified-check';
                checkIcon.innerHTML = '&#10004;';
                checkIcon.title = 'Tài khoản đã được xác thực';
                nameCell.appendChild(checkIcon);
            }
            row.insertCell().textContent = driver.bankName;
            row.insertCell().textContent = driver.accountNumber;
            row.insertCell().textContent = driver.accountName;
            const actionsCell = row.insertCell();
            actionsCell.className = 'flex gap-2 justify-center p-2';
            actionsCell.innerHTML = `
                <button class="bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-1 px-2 rounded-md whitespace-nowrap" onclick="verifyBankAccountAPI(${originalIndex})">Kiểm tra STK</button>
                <button class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md" onclick="editDriver(${originalIndex})">Sửa</button>
                <button class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md" onclick="deleteDriver(${originalIndex})">Xóa</button>
            `;
        });
    }
    
    const loadMoreContainer = isMobile ? document.getElementById('mobileLoadMoreContainer') : document.getElementById('desktopLoadMoreContainer');
    loadMoreContainer.style.display = listToRender.length < filteredList.length ? 'flex' : 'none';
}

// Tải danh sách lái xe từ server
async function loadDriverList() {
    const result = await api.loadDrivers(activeDataDir);
    if (result && result.success) {
        driverList = result.data.map(d => ({...d, isVerified: d.isVerified === 'true'})) || [];
        sortDriverList();
    } else {
        console.error("Lỗi khi tải danh sách lái:", result.message);
        customAlert(result.message || 'Không thể tải danh sách lái.');
        driverList = [];
    }
    renderDriverList();
}

// Lưu danh sách lái xe lên server
async function saveDriverListToCSV() {
    const result = await api.saveDrivers(driverList, activeDataDir);
    if (!result.success) {
        customAlert(`Lỗi khi lưu danh sách lái: ${result.message}`);
    }
    return result.success;
}

// *** HÀM ĐÃ ĐƯỢC CẬP NHẬT ***
// Gọi API để xác thực thông tin tài khoản ngân hàng với payOS
async function verifyBankAccountAPI(index) {
    const driver = driverList[index];
    if (!driver) return;

    const bankBin = getBankBin(driver.bankName);
    if (!bankBin) {
        return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${driver.bankName}.`);
    }

    customAlert('Đang kiểm tra thông tin với payOS...');
    
    // Gọi API, frontend gửi 'bin', backend sẽ nhận và dùng nó làm 'bankCode'
    const result = await api.verifyBankAccount(bankBin, driver.accountNumber);

    if (result.success) {
        const apiAccountName = normalizeName(result.accountName);
        const inputAccountName = normalizeName(driver.accountName);

        if (apiAccountName === inputAccountName) {
            driver.isVerified = true;
            await saveDriverListToCSV();
            renderDriverList();
            customAlert('Thông tin ngân hàng chính xác (payOS).');
        } else {
            customAlert(`Tên không khớp. Tên đúng từ payOS: ${result.accountName}`);
        }
    } else {
        customAlert(`Lỗi xác thực payOS: ${result.message || 'Không thể xác thực tài khoản.'}`);
    }
}

// Thêm mới hoặc cập nhật thông tin một lái xe
async function addOrUpdateDriver() {
    const driverNamePhone = document.getElementById('driverNamePhone').value.trim();
    const bankName = document.getElementById('bankName').value;
    const accountNumber = document.getElementById('accountNumber').value.trim();
    const accountName = document.getElementById('accountName').value.trim();
    
    if (!driverNamePhone || !bankName || !accountNumber || !accountName) {
        return customAlert('Vui lòng điền đầy đủ tất cả các thông tin!');
    }
    if (!/\p{L}/u.test(driverNamePhone) || !/\d/.test(driverNamePhone)) {
        return customAlert('Vui lòng điền cả Tên và SĐT lái');
    }

    if (editingIndexTab1 === -1) { // Thêm mới
        const isDuplicate = driverList.some(d => d && d.driverNamePhone.toLowerCase() === driverNamePhone.toLowerCase());
        if (isDuplicate) return customAlert('Tên và SĐT Lái bạn nhập đã tồn tại.');
        
        driverList.push({ driverNamePhone, bankName, accountNumber, accountName, isVerified: false });
        customAlert('Đã thêm thông tin lái mới.');
    } else { // Cập nhật
        const oldDriverName = driverList[editingIndexTab1].driverNamePhone;
        driverList[editingIndexTab1] = { ...driverList[editingIndexTab1], driverNamePhone, bankName, accountNumber, accountName };

        if (oldDriverName.toLowerCase() !== driverNamePhone.toLowerCase()) {
            let updatedCount = 0;
            dailyReports.forEach(report => {
                if (report.driver === oldDriverName) {
                    report.driver = driverNamePhone;
                    updatedCount++;
                }
            });
            if (updatedCount > 0) {
                 await saveDailyReportsToCSV();
            }
        }
        customAlert('Đã cập nhật thông tin lái.');
    }
    
    const success = await saveDriverListToCSV();
    if (success) {
        sortDriverList();
        renderDriverList();
        filterReportsByDate();
        cancelEdit();
        clearDriverForm();
    }
}

// Bật chế độ chỉnh sửa thông tin cho một lái xe
function editDriver(index) {
    editingIndexTab1 = index;
    const driverToEdit = driverList[index];
    document.getElementById('driverNamePhone').value = driverToEdit.driverNamePhone;
    document.getElementById('bankName').value = driverToEdit.bankName;
    document.getElementById('accountNumber').value = driverToEdit.accountNumber;
    document.getElementById('accountName').value = driverToEdit.accountName;
    
    const addUpdateButton = document.getElementById('addUpdateButton');
    addUpdateButton.textContent = 'Cập nhật';
    addUpdateButton.classList.replace('bg-blue-600', 'bg-green-600');
    addUpdateButton.classList.replace('hover:bg-blue-700', 'hover:bg-green-700');
    document.getElementById('cancelButton').classList.remove('hidden');
    document.getElementById('driver-form-title').scrollIntoView({ behavior: 'smooth' });
}

// Xóa một lái xe khỏi danh sách
async function deleteDriver(index) {
    const driverToDelete = driverList[index];
    if (!driverToDelete) return;

    customAlert("Đang kiểm tra dữ liệu liên quan...");
    const result = await api.checkDriverUsage(driverToDelete.driverNamePhone, activeDataDir);

    if (!result.success) return customAlert(`Lỗi: ${result.message}`);

    if (result.canDelete) {
        const confirmed = await confirm(`Bạn có chắc chắn muốn xóa lái "${driverToDelete.driverNamePhone}" không?`);
        if (confirmed) {
            driverList.splice(index, 1);
            await saveDriverListToCSV();
            sortDriverList();
            renderDriverList();
            customAlert('Đã xóa thông tin lái.');
        }
    } else {
        showDriverUsageModal(result.reports, index);
    }
}

// Hiển thị modal thông báo lái xe đang được sử dụng
function showDriverUsageModal(conflictingReports, driverToDeleteIndex) {
    const modal = document.getElementById('driverUsageModal');
    const listContainer = document.getElementById('conflictingReportsList');
    listContainer.innerHTML = '';
    const otherDrivers = driverList.filter((_, i) => i !== driverToDeleteIndex);

    conflictingReports.forEach((report, idx) => {
        const reportCard = document.createElement('div');
        reportCard.className = 'report-card';
        
        let reportDetailsHtml = '';
        quantityColumns.forEach(qtyCol => {
            const qty = getValueFromExpression(report[qtyCol]);
            if (qty > 0) {
                const price = getValueFromExpression(report[`Giá_${qtyCol}`]);
                const itemName = qtyCol.replace(/_/g, ' ');
                reportDetailsHtml += `
                    <div class="report-item">
                        <span class="report-item-name">${itemName}</span>
                        <span class="report-item-details">${qty.toLocaleString('vi-VN')} kg &times; ${price.toLocaleString('vi-VN')}đ</span>
                    </div>`;
            }
        });

        const selectId = `new-driver-select-${idx}`;
        reportCard.innerHTML = `
            <div class="report-card-header">
                <h4>Toa ${report.toa} - Ngày: ${report.date}</h4>
                <div class="flex items-center">
                    <span class="font-semibold text-indigo-600 mr-3">Tổng: ${parseFloat(report.Thành_Tiền).toLocaleString('vi-VN')}đ</span>
                    <span class="toggle-arrow" style="transform: rotate(0deg);">▼</span>
                </div>
            </div>
            <div class="collapsible-section collapsed">
                <div class="report-card-body">${reportDetailsHtml || '<div class="text-gray-500 p-2">Không có chi tiết hàng.</div>'}</div>
                <div class="report-card-footer">
                    <label for="${selectId}" class="text-gray-700">Đổi tên lái khác vào toa:</label>
                    <select id="${selectId}" class="w-full p-2 border rounded">
                        <option value="">-- Chọn lái mới --</option>
                        ${otherDrivers.map(d => `<option value="${d.driverNamePhone}">${d.driverNamePhone}</option>`).join('')}
                    </select>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md self-end mt-2">Lưu Thay Đổi</button>
                </div>
            </div>`;
        
        listContainer.appendChild(reportCard);
        
        const header = reportCard.querySelector('.report-card-header');
        const collapsible = reportCard.querySelector('.collapsible-section');
        const arrow = header.querySelector('.toggle-arrow');
        header.addEventListener('click', () => {
            const isCollapsed = collapsible.classList.toggle('collapsed');
            arrow.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(180deg)';
        });
        collapsible.addEventListener('click', (e) => e.stopPropagation());
        reportCard.querySelector('button').addEventListener('click', () => {
            const newDriver = reportCard.querySelector('select').value;
            if (newDriver) updateReportDriver(report, newDriver, reportCard);
            else customAlert("Vui lòng chọn một lái mới để chuyển sang.");
        });
    });

    modal.style.display = 'block';
}

// Cập nhật lái xe cho một báo cáo cụ thể
async function updateReportDriver(originalReport, newDriverName, cardElement) {
    const reportIndex = dailyReports.findIndex(r => r.date === originalReport.date && r.toa == originalReport.toa && r.driver === originalReport.driver);

    if (reportIndex === -1) return customAlert("Lỗi: Không tìm thấy báo cáo gốc để cập nhật.");

    dailyReports[reportIndex].driver = newDriverName;
    
    await saveDailyReportsToCSV();

    cardElement.style.transition = 'opacity 0.5s, transform 0.5s';
    cardElement.style.opacity = '0';
    cardElement.style.transform = 'scale(0.95)';
    setTimeout(() => {
        cardElement.remove();
        if (!document.querySelector('#conflictingReportsList .report-card')) {
            closeModal('driverUsageModal');
            customAlert("Tất cả các toa đã được cập nhật. Bây giờ bạn có thể xóa Lái này.");
        }
    }, 500);
    
    customAlert(`Đã cập nhật lái cho toa ngày ${originalReport.date}.`);
    filterReportsByDate();
}