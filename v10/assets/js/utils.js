import { allCalcColumns, banksData } from './state.js';

// --- CÁC HÀM TIỆN ÍCH ---
export function normalizeName(name) {
    if (!name) return '';
    return removeAccents(name).toUpperCase().trim();
}

export function removeAccents(str) {
    if (!str) return '';
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
}

export function getBankBin(bankName) {
    const bank = banksData.find(b => b.name === bankName);
    return bank ? bank.code : null;
}

export function getBankShortName(fullName) {
    const bank = banksData.find(b => b.name === fullName);
    return bank ? bank.shortName : fullName;
}

export function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
        if (modalId === 'qrModal') {
            document.getElementById('qrCodeContainer').innerHTML = '';
        }
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

export function getValueFromExpression(value) {
    if (typeof value === 'string' && value.includes('+')) {
        return value.split('+').reduce((sum, part) => sum + (Number(part) || 0), 0);
    }
    return parseFloat(value) || 0;
}


export function updateCalculationPreview(inputElement) {
    const isCalcInput = allCalcColumns.includes(inputElement.id);
    if (!isCalcInput) return;
    const resultSpan = inputElement.nextElementSibling;
    if (!resultSpan || !resultSpan.classList.contains('calculation-result')) return;
    const value = inputElement.value;
    if (value.includes('+') && /[0-9]/.test(value)) {
        const sum = getValueFromExpression(value);
        resultSpan.textContent = '= ' + sum.toLocaleString('vi-VN');
        resultSpan.style.display = 'block';
    } else {
        resultSpan.style.display = 'none';
    }
}

export function showAllCalculationPreviews() {
    allCalcColumns.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            updateCalculationPreview(input);
        }
    });
}

export function getExpandedToaSet() {
    const expandedToa = new Set();
    document.querySelectorAll('#mobileReportList .mobile-report-details.expanded').forEach(details => {
        const toaEl = details.closest('.mobile-report-item')?.querySelector('.toa');
        if (toaEl && toaEl.textContent) {
            expandedToa.add(toaEl.textContent);
        }
    });
    return expandedToa;
}


export function customAlert(message) {
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

export function confirm(e){return new Promise(t=>{const o=document.getElementById("custom-confirm");o&&o.remove();const n=document.createElement("div");n.id="custom-confirm",n.className="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50",n.innerHTML=`<div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full" style="padding:1rem!important;"><p class="text-lg font-semibold text-gray-800 mb-4">${e}</p><div class="flex justify-end gap-3"><button id="confirm-cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors duration-200">Hủy</button><button id="confirm-ok" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">OK</button></div></div>`,document.body.appendChild(n),document.getElementById("confirm-ok").onclick=()=>{n.remove(),t(!0)},document.getElementById("confirm-cancel").onclick=()=>{n.remove(),t(!1)}})}