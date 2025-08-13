// assets/js/main.js

// --- BIẾN TOÀN CỤC VÀ TRẠNG THÁI ---
let activeDataDir = window.APP_CONFIG.initialDataDir;
const userType = window.APP_CONFIG.userType;
const username = window.APP_CONFIG.username;

let isSaving = false;
let saveTimeout;
const banksData = [{"name":"ACB","code":"970416"},{"name":"Agribank","code":"970405"},{"name":"BAC A BANK","code":"970409"},{"name":"BaoViet Bank","code":"970438"},{"name":"BIDV","code":"970418"},{"name":"Cake by VPBank","code":"546034"},{"name":"CIMB","code":"422589"},{"name":"DongA Bank","code":"970406"},{"name":"Eximbank","code":"970431"},{"name":"GPBank","code":"970408"},{"name":"HDBank","code":"970437"},{"name":"Hong Leong Bank","code":"970442"},{"name":"Indovina Bank","code":"970434"},{"name":"KienLong Bank","code":"970452"},{"name":"LPBank","code":"970449"},{"name":"MB BANK","code":"970422"},{"name":"MSB","code":"970426"},{"name":"Nam A Bank","code":"970428"},{"name":"NCB","code":"970419"},{"name":"OCB","code":"970448"},{"name":"PBVN","code":"970439"},{"name":"PG Bank","code":"970430"},{"name":"PVcomBank","code":"970412"},{"name":"Sacombank","code":"970403"},{"name":"Saigonbank","code":"970400"},{"name":"SCB","code":"970429"},{"name":"SeABank","code":"970440"},{"name":"SHB","code":"970443"},{"name":"Shinhan Bank","code":"970424"},{"name":"Timo","code":"963388"},{"name":"TPBank","code":"970423"},{"name":"UOB","code":"970458"},{"name":"VIB","code":"970441"},{"name":"VietABank","code":"970427"},{"name":"VietBank","code":"970433"},{"name":"Vietcombank","code":"970436"},{"name":"VietinBank","code":"970415"},{"name":"VPBank","code":"970432"},{"name":"VRB","code":"970421"}];
const quantityColumns = ["B1_Thái","B2_Thái","C1_Thái","C2_Thái","C3_Thái","D1_Thái","D2_Thái","E_Thái","Chợ_Thái","Xơ_Thái","A1_indo","A2_indo","B1_indo","B2_indo","B3_indo","C1_indo","C2_indo","Chợ_1_indo","Chợ_2_indo","Xơ_indo"];
const priceColumns = quantityColumns.map(col => `Giá_${col}`);
const allCalcColumns = [...quantityColumns, ...priceColumns];

// --- CÁC HÀM TIỆN ÍCH CHUNG ---
function normalizeName(name) {
    if (!name) return '';
    return removeAccents(name).toUpperCase().trim();
}

function removeAccents(str) {
    if (!str) return '';
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
}

function getBankBin(bankName) {
    const bank = banksData.find(b => removeAccents(b.name.toLowerCase()) === removeAccents(bankName.toLowerCase()));
    return bank ? bank.code : null;
}

function getValueFromExpression(value) {
    if (typeof value === 'string' && value.includes('+')) {
        return value.split('+').reduce((sum, part) => sum + (Number(part) || 0), 0);
    }
    return parseFloat(value) || 0;
}

async function loadDataForCurrentVua() {
    await loadDriverList();
    await loadDailyReports();
}

// *** HÀM ĐÃ SỬA LỖI ***
// Gán sự kiện cho các nút bấm một cách tập trung
function initializeEventListeners() {
    // Tab
    document.querySelectorAll('.tab-button').forEach(button => {
        const tabId = button.getAttribute('onclick').match(/'([^']+)'/)[1];
        button.addEventListener('click', (e) => {
            e.preventDefault(); // Ngăn hành vi mặc định
            openTab(tabId)
        });
    });

    // --- Tab 1: Driver ---
    document.getElementById('addUpdateButton').addEventListener('click', addOrUpdateDriver);
    document.getElementById('cancelButton').addEventListener('click', cancelEdit);
    function handleDriverFilterChange() {
        driversCurrentlyShown = DRIVERS_PER_PAGE;
        renderDriverList();
    }
    ['mobileDriverSearch', 'desktopDriverSearch'].forEach(id => document.getElementById(id)?.addEventListener('input', handleDriverFilterChange));
    ['mobileFilterUnverified', 'desktopFilterUnverified'].forEach(id => document.getElementById(id)?.addEventListener('change', handleDriverFilterChange));
    ['mobileLoadMoreBtn', 'desktopLoadMoreBtn'].forEach(id => document.getElementById(id)?.addEventListener('click', () => {
        driversCurrentlyShown += DRIVERS_PER_PAGE;
        renderDriverList();
    }));

    // --- Tab 2: Report ---
    document.querySelector('button[onclick="exportReportsToExcel()"]').addEventListener('click', exportReportsToExcel);
    document.querySelector('button[onclick="handleAddNewRowClick()"]').addEventListener('click', handleAddNewRowClick);
    document.querySelector('button[onclick="openSummaryModal()"]').addEventListener('click', openSummaryModal);
    document.getElementById('dailyReportForm').addEventListener('input', handleFormInput);
    document.getElementById('changeDriverBtn').addEventListener('click', unlockDriverSelection);
    const driverSearchInput = document.getElementById('dailyReportDriverSearch');
    driverSearchInput.addEventListener('input', () => renderDriverSearchResults(driverSearchInput.value));
    driverSearchInput.addEventListener('focus', () => renderDriverSearchResults(driverSearchInput.value));

    // Lắng nghe sự kiện click tập trung cho các nút trong Tab 2
    const tab2Content = document.getElementById('tab2');
    tab2Content.addEventListener('click', function(event) {
        const target = event.target.closest('button');
        if (!target) return;

        const index = target.dataset.index;
        if (index === undefined) return;

        const report = dailyReports[index];
        if (!report) return;

        if (target.classList.contains('btn-show-invoice')) {
            showInvoice(index);
        } else if (target.classList.contains('btn-show-payment')) {
            showPaymentModal(index);
        } else if (target.classList.contains('btn-edit-report')) {
            editDailyReport(report);
        } else if (target.classList.contains('btn-delete-report')) {
            deleteDailyReport(report);
        }
    });

    // --- Tab 3: Admin ---
    if (userType === 'quanly') {
        document.getElementById('userForm')?.addEventListener('submit', handleUserFormSubmit);
        document.querySelector('button[onclick="openUserModal()"]')?.addEventListener('click', () => openUserModal());
        flatpickr("#statsDate", { dateFormat: "d/m/Y", locale: "vn", defaultDate: new Date(), onChange: loadStats });
        
        const vuaSelector1 = document.getElementById('vuaSelectorTab1');
        const vuaSelector2 = document.getElementById('vuaSelectorTab2');
        vuaSelector1.addEventListener('change', (e) => {
            activeDataDir = e.target.value;
            vuaSelector2.value = activeDataDir;
            loadDriverList();
        });
        vuaSelector2.addEventListener('change', (e) => {
            activeDataDir = e.target.value;
            vuaSelector1.value = activeDataDir;
            loadDataForCurrentVua();
        });
    }

    // --- General Listeners ---
    flatpickr("#reportDate", { dateFormat: "d/m/Y", locale: "vn", defaultDate: new Date(), onChange: filterReportsByDate });
    
    document.addEventListener('click', (e) => {
        document.querySelectorAll('.qr-modal, .invoice-modal, .driver-usage-modal, .summary-modal, .user-modal').forEach(modal => {
            if (e.target == modal) closeModal(modal.id);
        });
        const searchResultsContainer = document.getElementById('driverSearchResults');
        if (!driverSearchInput.contains(e.target) && !searchResultsContainer.contains(e.target)) {
            searchResultsContainer.classList.add('hidden');
        }
    });
}

// --- KHỞI TẠO KHI TRANG TẢI XONG ---
document.addEventListener("DOMContentLoaded", () => {
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

    loadBanksIntoDropdown();
    initializeEventListeners();
    
    openTab("tab1");
});