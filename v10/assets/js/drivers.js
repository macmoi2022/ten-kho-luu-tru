import { driverList, dailyReports, editingIndexTab1, verificationMode, driversCurrentlyShown, DRIVERS_PER_PAGE } from './state.js';
import { setDriverList, setEditingIndexTab1, setDailyReports, setDriversCurrentlyShown } from './state.js';
import { customAlert, confirm, removeAccents, getBankShortName, getBankBin, closeModal } from './utils.js';
import { saveDriverListToCSV, saveDailyReportsToCSV, checkDriverUsage } from './data.js';
import { filterReportsByDate } from './reports.js';
import { showVerificationQrModal } from './payment.js';

export function sortDriverList() {
    driverList.sort((a, b) => {
        return a.driverNamePhone.localeCompare(b.driverNamePhone, 'vi', { sensitivity: 'base' });
    });
}

export function renderDriverList() {
    const isMobile = window.innerWidth <= 768;
    
    const searchTerm = isMobile ? document.getElementById('mobileDriverSearch').value : document.getElementById('desktopDriverSearch').value;
    const showOnlyUnverified = isMobile ? document.getElementById('mobileFilterUnverified').checked : document.getElementById('desktopFilterUnverified').checked;

    let filteredList = driverList;
    if (showOnlyUnverified) {
        filteredList = filteredList.filter(d => !d.isVerified);
    }
    if (searchTerm.trim().length > 0) {
        const lowercasedFilter = removeAccents(searchTerm.toLowerCase());
        filteredList = filteredList.filter(driver =>
            removeAccents(driver.driverNamePhone.toLowerCase()).includes(lowercasedFilter)
        );
    }
    
    if (filteredList.length === 0) {
        if (isMobile) {
            document.getElementById('mobileDriverList').innerHTML = `<div class="text-center text-gray-500 py-4">Không có dữ liệu.</div>`;
        } else {
            document.getElementById('driverListTableBody').innerHTML = `<tr><td colspan="6" class="text-center text-gray-500 py-4">Không có dữ liệu.</td></tr>`;
        }
        document.getElementById('mobileLoadMoreContainer').style.display = 'none';
        document.getElementById('desktopLoadMoreContainer').style.display = 'none';
        return;
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

            const verifyButtonHtml = `<button class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-md verify-button" onclick="event.stopPropagation(); window.showVerificationQrModal(${originalIndex})">Đánh dấu</button>`;

            item.innerHTML = `
                <div class="mobile-driver-main" onclick="window.toggleDriverDetails(this)">
                    <span class="stt">${displayIndex + 1}</span>
                    <span class="name">${driver.driverNamePhone}${verifiedIconHtml}</span>
                    <span class="toggle-icon">▼</span>
                </div>
                <div class="mobile-driver-details">
                    <div class="mobile-driver-info">
                        <p><strong>Ngân Hàng:</strong> <span>${getBankShortName(driver.bankName)}</span></p>
                        <p><strong>Số TK:</strong> <span>${driver.accountNumber}</span></p>
                        <p><strong>Chủ TK:</strong> <span>${driver.accountName}</span></p>
                    </div>
                    <div class="mobile-driver-actions">
                        ${verifyButtonHtml}
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); window.editDriver(${originalIndex})">Sửa</button>
                        <button class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); window.deleteDriver(${originalIndex})">Xóa</button>
                    </div>
                </div>
            `;
            mobileList.appendChild(item);
        });
    } else {
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
            row.insertCell().textContent = getBankShortName(driver.bankName);
            row.insertCell().textContent = driver.accountNumber;
            row.insertCell().textContent = driver.accountName;
            const actionsCell = row.insertCell();
            actionsCell.className = 'flex gap-2 justify-center p-2';

            const verifyButton = document.createElement('button');
            verifyButton.textContent = 'Đánh dấu';
            verifyButton.className = 'bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-1 px-2 rounded-md whitespace-nowrap verify-button';
            verifyButton.onclick = () => showVerificationQrModal(originalIndex);
            actionsCell.appendChild(verifyButton);

            const editButton = document.createElement('button');
            editButton.textContent = 'Sửa';
            editButton.className = 'bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            editButton.onclick = () => editDriver(originalIndex);
            actionsCell.appendChild(editButton);
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Xóa';
            deleteButton.className = 'bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            deleteButton.onclick = () => deleteDriver(originalIndex);
            actionsCell.appendChild(deleteButton);
        });
    }
    
    const loadMoreContainer = isMobile ? document.getElementById('mobileLoadMoreContainer') : document.getElementById('desktopLoadMoreContainer');
    if (listToRender.length < filteredList.length) {
        loadMoreContainer.style.display = 'flex';
    } else {
        loadMoreContainer.style.display = 'none';
    }

    applyVerificationModeUI();
}

export function handleLoadMore() {
    setDriversCurrentlyShown(driversCurrentlyShown + DRIVERS_PER_PAGE);
    renderDriverList();
}


export function toggleDriverDetails(mainRowElement) {
    const details = mainRowElement.nextElementSibling;
    const icon = mainRowElement.querySelector('.toggle-icon');
    if (details && details.classList.contains('mobile-driver-details')) {
        const isExpanding = !details.classList.contains('expanded');
        details.classList.toggle('expanded');
        if (icon) {
            icon.style.transform = isExpanding ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    }
}

export function openDriverModal(index = -1) {
    setEditingIndexTab1(index);
    const modal = document.getElementById('driverFormModal');
    const title = modal.querySelector('#driver-form-title');
    const addUpdateButton = modal.querySelector('#addUpdateButton');
    const cancelButton = modal.querySelector('#cancelButton');

    document.getElementById('driverForm').reset();

    if (index === -1) {
        title.textContent = 'Thêm Thông Tin Lái';
        addUpdateButton.textContent = 'Thêm vào danh sách';
        addUpdateButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        addUpdateButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
        cancelButton.classList.add('hidden');
    } else {
        const driverToEdit = driverList[index];
        title.textContent = 'Sửa Thông Tin Lái';
        document.getElementById('driverNamePhone').value = driverToEdit.driverNamePhone;
        document.getElementById('bankName').value = driverToEdit.bankName;
        document.getElementById('accountNumber').value = driverToEdit.accountNumber;
        document.getElementById('accountName').value = driverToEdit.accountName;
        
        addUpdateButton.textContent = 'Cập nhật';
        addUpdateButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        addUpdateButton.classList.add('bg-green-600', 'hover:bg-green-700');
        cancelButton.classList.remove('hidden');
    }
    modal.style.display = 'flex';
}

export function closeDriverModal() {
    document.getElementById('driverFormModal').style.display = 'none';
    setEditingIndexTab1(-1);
}

export function editDriver(index) {
    openDriverModal(index);
}

export async function deleteDriver(index) {
    const driverToDelete = driverList[index];
    if (!driverToDelete) return;

    customAlert("Đang kiểm tra dữ liệu liên quan...");
    const result = await checkDriverUsage(driverToDelete.driverNamePhone);
    if (!result) return;

    if (result.canDelete) {
        confirm(`Bạn có chắc chắn muốn xóa lái "${driverToDelete.driverNamePhone}" không?`).then(async confirmed => {
            if (confirmed) {
                driverList.splice(index, 1);
                setDriverList(driverList);
                await saveDriverListToCSV();
                sortDriverList();
                setDriversCurrentlyShown(DRIVERS_PER_PAGE);
                renderDriverList();
                customAlert('Đã xóa thông tin lái khỏi danh sách.');
            }
        });
    } else {
        showDriverUsageModal(result.reports, index);
    }
}

async function updateReportDriver(originalReport, newDriverName, cardElement) {
    const reportIndex = dailyReports.findIndex(r => 
        r.date === originalReport.date &&
        r.toa == originalReport.toa &&
        r.driver === originalReport.driver
    );

    if (reportIndex === -1) {
        customAlert("Lỗi: Không tìm thấy báo cáo gốc để cập nhật.");
        return;
    }

    dailyReports[reportIndex].driver = newDriverName;
    setDailyReports(dailyReports);
    
    await saveDailyReportsToCSV();

    cardElement.style.transition = 'opacity 0.5s, transform 0.5s';
    cardElement.style.opacity = '0';
    cardElement.style.transform = 'scale(0.95)';
    setTimeout(() => {
        cardElement.remove();
        const listContainer = document.getElementById('conflictingReportsList');
        if (!listContainer.querySelector('.report-card')) {
            closeModal('driverUsageModal');
            customAlert("Tất cả các toa đã được cập nhật. Bây giờ bạn có thể xóa Lái này.");
        }
    }, 500);
    
    customAlert(`Đã cập nhật lái cho toa ngày ${originalReport.date}.`);
    
    filterReportsByDate();
}

function showDriverUsageModal(conflictingReports, driverToDeleteIndex) {
    const modal = document.getElementById('driverUsageModal');
    const listContainer = document.getElementById('conflictingReportsList');
    listContainer.innerHTML = '';

    const otherDrivers = driverList.filter((_, i) => i !== driverToDeleteIndex);

    conflictingReports.forEach((report, idx) => {
        const reportCard = document.createElement('div');
        reportCard.className = 'report-card';
        // ... (rest of the function is complex UI generation, keep it as is)
    });

    modal.style.display = 'block';
}


export async function addOrUpdateDriver() {
    const driverNamePhone = document.getElementById('driverNamePhone').value.trim();
    const bankName = document.getElementById('bankName').value;
    const accountNumber = document.getElementById('accountNumber').value.trim();
    const accountName = document.getElementById('accountName').value.trim();
    
    if (!driverNamePhone || !bankName || !accountNumber || !accountName) {
        return customAlert('Vui lòng điền đầy đủ tất cả các thông tin!');
    }

    const hasLetter = /\p{L}/u.test(driverNamePhone);
    const hasNumber = /\d/.test(driverNamePhone);
    if (!hasLetter || !hasNumber) {
        return customAlert('Vui lòng điền Tên và SĐT lái');
    }

    const button = document.getElementById('addUpdateButton');
    button.disabled = true;
    const originalButtonText = button.textContent;
    
    let isVerifiedStatus = false;
    let verificationMessage = '';
    const isUpdating = editingIndexTab1 !== -1;
    const currentVerifiedStatus = isUpdating ? driverList[editingIndexTab1].isVerified : false;

    if (verificationMode === 'auto') {
        button.textContent = 'Đang kiểm tra...';
        const bankBin = getBankBin(bankName);
        if (!bankBin) {
            button.disabled = false;
            button.textContent = originalButtonText;
            return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${bankName}.`);
        }

        try {
            const response = await fetch('verify_bank_account.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ toBin: bankBin, toAccountNumber: accountNumber })
            });
            const result = await response.json();
            
            if (result.payload && result.payload.code === '624') {
                isVerifiedStatus = true;
                verificationMessage = "Thông tin ngân hàng chính xác. ";
            } else {
                isVerifiedStatus = false;
                verificationMessage = "Thông tin ngân hàng sai. ";
            }
        } catch (error) {
            button.disabled = false;
            button.textContent = originalButtonText;
            console.error("Lỗi khi gọi API xác thực:", error);
            customAlert("Lỗi kết nối khi xác thực. Dữ liệu sẽ được lưu nhưng chưa được đánh dấu.");
            isVerifiedStatus = currentVerifiedStatus; 
        }
    } else {
        isVerifiedStatus = currentVerifiedStatus;
    }

    button.textContent = originalButtonText;
    button.disabled = false;

    let syncMessage = '';
    
    if (isUpdating) {
        const oldDriverName = driverList[editingIndexTab1].driverNamePhone;
        const driverData = { 
            ...driverList[editingIndexTab1], 
            driverNamePhone, bankName, accountNumber, accountName,
            isVerified: verificationMode === 'auto' ? isVerifiedStatus : currentVerifiedStatus
        };
        driverList[editingIndexTab1] = driverData;

        if (oldDriverName !== driverNamePhone && oldDriverName) {
            let updatedReportsCount = 0;
            dailyReports.forEach(report => {
                if (report.driver === oldDriverName) {
                    report.driver = driverNamePhone;
                    updatedReportsCount++;
                }
            });
            if(updatedReportsCount > 0) {
                 await saveDailyReportsToCSV();
                 syncMessage = ` và đã đồng bộ tên cho ${updatedReportsCount} toa hàng`;
            }
        }
        customAlert(verificationMessage + 'Đã cập nhật thông tin lái' + syncMessage + '.');
        
    } else {
        const isDuplicate = driverList.some(driver => 
            driver && typeof driver.driverNamePhone === 'string' && 
            driver.driverNamePhone.trim().toLowerCase() === driverNamePhone.toLowerCase()
        );
        if (isDuplicate) {
            return customAlert('Tên và SĐT Lái bạn nhập đã tồn tại trong danh sách');
        }
        const driverData = { driverNamePhone, bankName, accountNumber, accountName, isVerified: isVerifiedStatus };
        driverList.push(driverData);
        customAlert(verificationMessage + 'Đã thêm thông tin lái mới vào danh sách.');
    }
    
    setDriverList(driverList);
    await saveDriverListToCSV();
    sortDriverList();
    renderDriverList();
    filterReportsByDate();
    closeDriverModal();
}

export function applyVerificationModeUI() {
    const verifyButtons = document.querySelectorAll('.verify-button');
    if (verificationMode === 'auto') {
        verifyButtons.forEach(btn => btn.style.display = 'none');
    } else {
        verifyButtons.forEach(btn => btn.style.display = 'inline-block');
    }
}