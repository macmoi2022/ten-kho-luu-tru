// File chính điều phối, khởi tạo
import { userType as stateUserType, activeDataDir, setUserType, setActiveDataDir } from './modules/state.js';
import { customAlert, closeModal } from './modules/utils.js';
import { loadDriverList, loadDailyReports } from './modules/data.js';
import {
    renderDriverList,
    sortDriverList,
    handleLoadMore,
    addOrUpdateDriver,
    openDriverModal,
    closeDriverModal,
    editDriver,
    deleteDriver,
    toggleDriverDetails
} from './modules/drivers.js';
import {
    filterReportsByDate,
    openSummaryModal,
    handleAddNewRowClick,
    closeReportModal,
    editDailyReport,
    deleteDailyReport,
    toggleStickyPanel,
    handleFormInput,
    unlockDriverSelection,
    renderDriverSearchResults
} from './modules/reports.js';
import {
    loadUsers,
    openUserModal,
    handleUserFormSubmit,
    deleteUser,
    loadStats,
    toggleStatsCategory,
    saveVerificationSetting,
    loadVerificationSetting
} from './modules/admin.js';
import {
    showInvoice
} from './modules/invoice.js';
import {
    showPaymentModal,
    togglePaymentStatus,
    showVerificationQrModal,
    toggleVerificationStatus
} from './modules/payment.js';

// --- GÁN HÀM VÀO WINDOW ĐỂ HTML TRUY CẬP ---
window.openTab = openTab;
window.exportReportsToExcel = exportReportsToExcel;
window.exportStatsToExcel = exportStatsToExcel;
window.openDriverModal = openDriverModal;
window.closeDriverModal = closeDriverModal;
window.addOrUpdateDriver = addOrUpdateDriver;
window.editDriver = editDriver;
window.deleteDriver = deleteDriver;
window.toggleDriverDetails = toggleDriverDetails;
window.openSummaryModal = openSummaryModal;
window.handleAddNewRowClick = handleAddNewRowClick;
window.closeReportModal = closeReportModal;
window.editDailyReport = editDailyReport;
window.deleteDailyReport = deleteDailyReport;
window.showInvoice = showInvoice;
window.showPaymentModal = showPaymentModal;
window.togglePaymentStatus = togglePaymentStatus;
window.showVerificationQrModal = showVerificationQrModal;
window.toggleVerificationStatus = toggleVerificationStatus;
window.openUserModal = openUserModal;
window.deleteUser = deleteUser;
window.saveVerificationSetting = saveVerificationSetting;
window.toggleStatsCategory = toggleStatsCategory;
window.closeModal = closeModal;
window.toggleStickyPanel = toggleStickyPanel;

async function openTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    const clickedButton = document.querySelector(`.tab-button[onclick*="${tabId}"]`);
    if (clickedButton) clickedButton.classList.add('active');

    if (tabId === 'tab1') {
        const drivers = await loadDriverList();
        setDriverList(drivers);
        sortDriverList();
        renderDriverList();
    } else if (tabId === 'tab2') {
        await loadDataForCurrentVua();
    } else if (tabId === 'tab3' && stateUserType === 'quanly') {
        loadUsers();
        loadStats();
    }
}

async function loadDataForCurrentVua() {
    const drivers = await loadDriverList();
    setDriverList(drivers);
    const reports = await loadDailyReports();
    setDailyReports(reports);
    filterReportsByDate();
}

function loadBanksIntoDropdown() {
    const bankSelect = document.getElementById('bankName');
    bankSelect.innerHTML = '<option value="">Chọn Ngân Hàng</option>';
    const sortedBanks = banksData.sort((a, b) => a.shortName.localeCompare(b.shortName));
    sortedBanks.forEach(bank => {
        const option = document.createElement('option');
        option.value = bank.name;
        option.textContent = bank.shortName;
        bankSelect.appendChild(option);
    });
}

function exportReportsToExcel() {
    const selectedDate = document.getElementById('reportDate').value;
    if (!selectedDate) {
        return customAlert('Vui lòng chọn ngày để xuất báo cáo.');
    }
    const vuaSelected = (stateUserType === 'quanly') ? document.getElementById('vuaSelectorTab2').value : activeDataDir;
    if (!vuaSelected) {
        return customAlert('Lỗi: Không thể xác định được vựa để xuất báo cáo.');
    }
    window.location.href = `export_styled.php?type=nhaptoa&date=${encodeURIComponent(selectedDate)}&vua=${encodeURIComponent(vuaSelected)}`;
}

function exportStatsToExcel() {
    const selectedDate = document.getElementById('statsDate').value;
    if (!selectedDate) {
        return customAlert('Vui lòng chọn ngày để xuất thống kê.');
    }
    window.location.href = `export_styled.php?type=quanly&date=${encodeURIComponent(selectedDate)}`;
}

// --- KHỞI TẠO ỨNG DỤNG ---
document.addEventListener("DOMContentLoaded", async () => {
    // Truyền biến userType từ PHP (được đặt trong data attribute của body)
    const phpUserType = document.body.dataset.userType;
    setUserType(phpUserType);

    if (stateUserType === 'thuky_v7') {
        setActiveDataDir('Data_vua7/');
    } else if (stateUserType === 'thuky_v9') {
        setActiveDataDir('Data_vua9/');
    } else if (stateUserType === 'quanly') {
        setActiveDataDir(document.getElementById('vuaSelectorTab1').value);
    }

    await loadVerificationSetting();

    const reportForm = document.getElementById('dailyReportForm');
    allCalcColumns.forEach(id => {
        const input = document.getElementById(id);
        if(input) {
            const wrapper = document.createElement('div');
            wrapper.className = 'input-wrapper';
            const resultSpan = document.createElement('span');
            resultSpan.className = 'calculation-result';
            resultSpan.style.display = 'none';
            input.parentNode.replaceChild(wrapper, input);
            wrapper.appendChild(input);
            wrapper.appendChild(resultSpan);
        }
    });
    reportForm.addEventListener('input', handleFormInput);

    const driverSearchInput = document.getElementById('dailyReportDriverSearch');
    const driverSearchResults = document.getElementById('driverSearchResults');
    driverSearchInput.addEventListener('input', () => {
         renderDriverSearchResults(driverSearchInput.value);
    });
    driverSearchInput.addEventListener('focus', () => {
         renderDriverSearchResults(driverSearchInput.value);
    });
    document.addEventListener('click', (e) => {
        if (!driverSearchInput.contains(e.target) && !driverSearchResults.contains(e.target)) {
            driverSearchResults.classList.add('hidden');
        }
    });
    
    document.getElementById('changeDriverBtn').addEventListener('click', unlockDriverSelection);
    
    function handleFilterChange() {
        setDriversCurrentlyShown(DRIVERS_PER_PAGE);
        renderDriverList();
    }

    ['mobileDriverSearch', 'desktopDriverSearch'].forEach(id => {
        document.getElementById(id).addEventListener('input', handleFilterChange);
    });
    ['mobileFilterUnverified', 'desktopFilterUnverified'].forEach(id => {
        document.getElementById(id).addEventListener('change', handleFilterChange);
    });
    ['mobileLoadMoreBtn', 'desktopLoadMoreBtn'].forEach(id => {
        document.getElementById(id).addEventListener('click', handleLoadMore);
    });

    window.onclick = function(event) {
        const reportModal = document.getElementById('reportFormModal');
        if (event.target == reportModal) {
            closeReportModal();
            return;
        }
        document.querySelectorAll('.qr-modal, .invoice-modal, .driver-usage-modal, .summary-modal, .user-modal, .driver-form-modal').forEach(modal => {
            if (event.target == modal) {
                closeModal(modal.id);
            }
        });
    }

    loadBanksIntoDropdown();
    flatpickr("#reportDate",{dateFormat:"d/m/Y",locale:"vn",defaultDate:new Date,onChange:function(){filterReportsByDate()}});
    
    if (stateUserType === 'quanly') {
        const vuaSelector1 = document.getElementById('vuaSelectorTab1');
        const vuaSelector2 = document.getElementById('vuaSelectorTab2');
        
        vuaSelector1.addEventListener('change', async (e) => {
            setActiveDataDir(e.target.value);
            vuaSelector2.value = activeDataDir;
            const drivers = await loadDriverList();
            setDriverList(drivers);
            sortDriverList();
            renderDriverList();
        });
        vuaSelector2.addEventListener('change', (e) => {
            setActiveDataDir(e.target.value);
            vuaSelector1.value = activeDataDir;
            loadDataForCurrentVua();
        });

        flatpickr("#statsDate",{dateFormat:"d/m/Y",locale:"vn",defaultDate:new Date,onChange:function(){loadStats()}});
        document.getElementById('userForm').addEventListener('submit', handleUserFormSubmit);
    }

    openTab("tab1");
});