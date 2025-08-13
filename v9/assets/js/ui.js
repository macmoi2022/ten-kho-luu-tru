// assets/js/ui.js

function openTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    const clickedButton = document.querySelector(`.tab-button[onclick*="${tabId}"]`);
    if (clickedButton) clickedButton.classList.add('active');

    const currentTabVuaSelector = document.getElementById(`vuaSelector${tabId.slice(-1)}`);
    if(currentTabVuaSelector) {
        activeDataDir = currentTabVuaSelector.value;
    }

    // Tải dữ liệu tương ứng khi chuyển tab
    if (tabId === 'tab1') {
        loadDriverList();
    } else if (tabId === 'tab2') {
        loadDataForCurrentVua();
    } else if (tabId === 'tab3' && userType === 'quanly') {
        loadUsers();
        loadStats();
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
        if (modalId === 'qrModal') document.getElementById('qrCodeImg').src = "";
        if (modalId === 'invoiceModal') {
             document.getElementById('invoice-content-wrapper').innerHTML = '';
        }
        if (modalId === 'userModal') {
            document.getElementById('userForm').reset();
            document.getElementById('passwordHelp').style.display = 'none';
            document.getElementById('username').disabled = false;
        }
    }
}

function customAlert(message) {
    const existingAlert = document.getElementById("custom-alert");
    if (existingAlert) {
        existingAlert.remove();
    }
    const alertDiv = document.createElement("div");
    alertDiv.id = "custom-alert";
    alertDiv.className = "fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-indigo-600 text-white p-4 rounded-lg shadow-lg z-[100] transition-all duration-300 ease-in-out opacity-0 scale-95 w-11/12 max-w-sm text-center";
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    setTimeout(() => {
        alertDiv.classList.remove("opacity-0", "scale-95");
        alertDiv.classList.add("opacity-100", "scale-100");
    }, 10);
    setTimeout(() => {
        alertDiv.classList.remove("opacity-100", "scale-100");
        alertDiv.classList.add("opacity-0", "scale-95");
        alertDiv.addEventListener("transitionend", () => alertDiv.remove(), { once: true });
    }, 3000);
}

function confirm(message){
    return new Promise(resolve =>{
        const existingConfirm = document.getElementById("custom-confirm");
        if(existingConfirm) existingConfirm.remove();
        const dialog = document.createElement("div");
        dialog.id = "custom-confirm";
        dialog.className = "fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50";
        dialog.innerHTML = `<div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full"><p class="text-lg font-semibold text-gray-800 mb-4">${message}</p><div class="flex justify-end gap-3"><button id="confirm-cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors duration-200">Hủy</button><button id="confirm-ok" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">OK</button></div></div>`;
        document.body.appendChild(dialog);
        document.getElementById("confirm-ok").onclick = () => { dialog.remove(); resolve(true); };
        document.getElementById("confirm-cancel").onclick = () => { dialog.remove(); resolve(false); };
    });
}

function toggleDriverDetails(mainRowElement) {
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

function toggleReportDetails(mainRowElement) {
    const details = mainRowElement.nextElementSibling;
    const icon = mainRowElement.querySelector('.mobile-report-toggle-icon');
    if (details && details.classList.contains('mobile-report-details')) {
        const isExpanding = !details.classList.contains('expanded');
        details.classList.toggle('expanded');
        if (icon) {
            icon.style.transform = isExpanding ? 'rotate(180deg)' : 'rotate(0deg)';
        }
    }
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