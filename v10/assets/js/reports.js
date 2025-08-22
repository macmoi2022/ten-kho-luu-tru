// Logic cho tab Nhập Toa và popup sửa toa
import {
    dailyReports,
    liveEditingReport,
    isSaving,
    saveTimeout,
    quantityColumns,
    priceColumns,
    allCalcColumns,
    driverList,
    currentlyPayingReportIndex,
    userType
} from './state.js';
import {
    setDailyReports,
    setLiveEditingReport,
    setIsSaving,
    setSaveTimeout
} from './state.js';
import {
    customAlert,
    confirm,
    getValueFromExpression,
    showAllCalculationPreviews,
    updateCalculationPreview,
    getExpandedToaSet,
    closeModal,
    removeAccents
} from './utils.js';
import {
    saveDailyReportsToCSV
} from './data.js';
import {
    showInvoice
} from './invoice.js';
import {
    showPaymentModal
} from './payment.js';

export function openReportModal() {
    document.getElementById('reportFormModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

export function closeReportModal() {
    if (liveEditingReport && !liveEditingReport.driver) {
        const index = dailyReports.indexOf(liveEditingReport);
        if (index > -1) {
            dailyReports.splice(index, 1);
            setDailyReports(dailyReports);
        }
    }
    const expandedToa = getExpandedToaSet();
    setLiveEditingReport(null);
    document.getElementById('reportFormModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    filterReportsByDate(expandedToa);
}

export function renderDriverSearchResults(searchTerm = '') {
    const searchResultsContainer = document.getElementById('driverSearchResults');
    const searchInput = document.getElementById('dailyReportDriverSearch');
    const lowerCaseSearchTerm = removeAccents(searchTerm.toLowerCase());

    const filteredDrivers = driverList.filter(driver =>
        removeAccents(driver.driverNamePhone.toLowerCase()).includes(lowerCaseSearchTerm)
    );

    searchResultsContainer.innerHTML = '';

    if (filteredDrivers.length > 0) {
        filteredDrivers.forEach(driver => {
            const item = document.createElement('div');
            item.className = 'driver-search-item';
            item.textContent = driver.driverNamePhone;
            item.dataset.driverName = driver.driverNamePhone;
            item.addEventListener('click', () => {
                handleDriverSelection(driver.driverNamePhone);
            });
            searchResultsContainer.appendChild(item);
        });
        searchResultsContainer.classList.remove('hidden');
    } else {
        searchResultsContainer.classList.add('hidden');
    }
}

export function handleDriverSelection(selectedDriver) {
    if (isSaving) return;

    const searchInput = document.getElementById('dailyReportDriverSearch');
    const searchResultsContainer = document.getElementById('driverSearchResults');

    searchInput.value = selectedDriver;
    searchResultsContainer.classList.add('hidden');

    const selectedDate = document.getElementById('reportDate').value;

    if (liveEditingReport) {
        const anotherReportHasDriver = dailyReports.some(report =>
            report !== liveEditingReport &&
            report.date === selectedDate &&
            report.driver === selectedDriver
        );
        if (anotherReportHasDriver) {
            setTimeout(() => customAlert("Lái này đã có toa, hãy ghi chú nếu cần"), 100);
        }
        liveEditingReport.driver = selectedDriver;
        filterReportsByDate();
        handleAutoSave(true);
        searchInput.disabled = true;
        document.getElementById('changeDriverBtn').classList.remove('hidden');
        lockForm(false);
    } else {
        const driverHasReportToday = dailyReports.some(report =>
            report.date === selectedDate &&
            report.driver === selectedDriver
        );
        if (driverHasReportToday) {
            setTimeout(() => customAlert("Lái này đã có toa, hãy ghi chú nếu cần"), 100);
        }
        document.getElementById('add-report-title').textContent = 'Thêm Dữ Liệu Báo Cáo';
        const newReport = {
            date: selectedDate,
            driver: selectedDriver,
            Ghi_Chú: '',
            isPaid: false,
            noPayment: false,
            isForSale: false,
            adjustment: '',
            catBatHang: ''
        };
        const reportsForDay = dailyReports.filter(r => r.date === selectedDate);

        if (reportsForDay.length > 0) {
            const lastReportForDay = reportsForDay[reportsForDay.length - 1];
            priceColumns.forEach(priceCol => {
                if (lastReportForDay[priceCol] !== undefined && lastReportForDay[priceCol] !== null) {
                    newReport[priceCol] = lastReportForDay[priceCol];
                }
            });
            customAlert("Đã tự động điền giá theo toa cuối cùng của ngày.");
        }
        quantityColumns.forEach(col => {
            if (newReport[col] === undefined) newReport[col] = '';
            if (newReport[`Giá_${col}`] === undefined) newReport[`Giá_${col}`] = '';
        });
        dailyReports.push(newReport);
        setDailyReports(dailyReports);
        recalculateDailyToaNumbers(selectedDate);
        setLiveEditingReport(newReport);

        bindFormToReport(newReport);
        showAllCalculationPreviews();
        lockForm(false);
        searchInput.disabled = true;
        filterReportsByDate();

        document.getElementById('changeDriverBtn').classList.remove('hidden');
        document.getElementById('Ghi_Chú').focus();
        handleAutoSave(true);
    }
}

export function recalculateDailyToaNumbers(date) {
    const reportsForDay = dailyReports.filter(r => r.date === date);

    reportsForDay.sort((a, b) => a.toa - b.toa);

    reportsForDay.forEach((report, index) => {
        report.toa = index + 1;
    });
}

export function calculateRowTotals() {
    let totalKG = 0;
    let totalAmount = 0;
    allCalcColumns.forEach(colId => {
        const element = document.getElementById(colId);
        if (element && quantityColumns.includes(colId)) {
            totalKG += getValueFromExpression(element.value);
        }
    });
    quantityColumns.forEach(qtyColId => {
        const qtyEl = document.getElementById(qtyColId);
        const priceEl = document.getElementById(`Giá_${qtyColId}`);
        if (qtyEl && priceEl) {
            const qty = getValueFromExpression(qtyEl.value);
            const price = getValueFromExpression(priceEl.value);
            totalAmount += qty * price;
        }
    });

    const adjustment = getValueFromExpression(document.getElementById('adjustment').value || '0');
    totalAmount += adjustment;

    if (document.getElementById('noPayment').checked) {
        totalAmount = 0;
    }

    const tongKgDisplay = totalKG > 0 ? totalKG.toLocaleString('vi-VN') : '0';
    const thanhTienDisplay = totalAmount.toLocaleString('vi-VN');

    document.getElementById('sticky-total-kg').textContent = tongKgDisplay;
    document.getElementById('sticky-total-tien').textContent = thanhTienDisplay;
}

export async function handleAddNewRowClick() {
    if (liveEditingReport) {
        await handleAutoSave(true);
    }
    resetFormForNewEntry();
    openReportModal();
    setTimeout(() => document.getElementById('dailyReportDriverSearch').focus(), 50);
}

function lockForm(shouldLock) {
    const form = document.getElementById('dailyReportForm');
    const inputs = form.querySelectorAll('#catBatHang, .input-group input, #noPayment, #isForSale, #adjustment, #Ghi_Chú');
    inputs.forEach(input => {
        input.disabled = shouldLock;
    });
}

function bindFormToReport(report) {
    const driverDisplayName = report.isForSale ? `${report.driver} (TOA BÁN)` : report.driver;
    document.getElementById('dailyReportDriverSearch').value = driverDisplayName;
    document.getElementById('catBatHang').value = report.catBatHang || '';
    document.getElementById('Ghi_Chú').value = report.Ghi_Chú || '';
    document.getElementById('noPayment').checked = report.noPayment || false;
    document.getElementById('isForSale').checked = report.isForSale || false;
    document.getElementById('adjustment').value = report.adjustment || '';

    allCalcColumns.forEach(col => {
        const input = document.getElementById(col);
        if (input) {
            input.value = report[col] || '';
        }
    });
    calculateRowTotals();
}

export function updateLiveReportFromForm() {
    if (!liveEditingReport) return;

    let driverName = document.getElementById('dailyReportDriverSearch').value.replace(' (TOA BÁN)', '');
    liveEditingReport.driver = driverName;
    liveEditingReport.catBatHang = document.getElementById('catBatHang').value;
    liveEditingReport.Ghi_Chú = document.getElementById('Ghi_Chú').value;
    liveEditingReport.noPayment = document.getElementById('noPayment').checked;
    liveEditingReport.isForSale = document.getElementById('isForSale').checked;
    liveEditingReport.adjustment = document.getElementById('adjustment').value;

    const numberInputs = document.querySelector('#dailyReportForm .input-group');
    allCalcColumns.forEach(col => {
        const input = numberInputs.querySelector(`#${col}`);
        if (input) {
            liveEditingReport[col] = input.value;
        }
    });
    liveEditingReport['Tổng_KG'] = calculateTotalKG(liveEditingReport);
    liveEditingReport['Thành_Tiền'] = calculateTotalAmount(liveEditingReport);
}

export function unlockDriverSelection() {
    const changeBtn = document.getElementById('changeDriverBtn');
    const searchInput = document.getElementById('dailyReportDriverSearch');

    lockForm(true);

    searchInput.disabled = false;
    changeBtn.classList.add('hidden');
    searchInput.focus();
    searchInput.select();
}

export async function handleAutoSave(silent = false) {
    if (!liveEditingReport || isSaving) return;

    setIsSaving(true);
    updateLiveReportFromForm();

    const success = await saveDailyReportsToCSV();
    if (success) {
        if (!silent) {
            customAlert("Dữ liệu đã được lưu tự động.");
        }
        filterReportsByDate();
    } else {
        customAlert("Không thể lưu, vui lòng kiểm tra lại mạng");
    }
    setIsSaving(false);
}

export function handleFormInput(e) {
    if (e.target.id === 'dailyReportDriverSearch') {
        return;
    }

    clearTimeout(saveTimeout);
    if (liveEditingReport) {
        setSaveTimeout(setTimeout(handleAutoSave, 1500));
    }

    const input = e.target;
    const isCalcInput = allCalcColumns.includes(input.id) || input.id === 'adjustment';
    if (isCalcInput) {
        let value = input.value;
        const sanitizedValue = value.replace(/[^0-9\+\-]/g, '');
        if (value !== sanitizedValue) {
            input.value = sanitizedValue;
        }
        if(input.id !== 'adjustment') {
             updateCalculationPreview(input);
        }
    }
    calculateRowTotals();
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

export function renderDailyReportTable(reports, expandedToaToRestore = new Set()) {
    const isMobile = window.innerWidth <= 768;

    if (reports.length === 0) {
        if (isMobile) {
            document.getElementById('mobileReportList').innerHTML = `<div class="text-center text-gray-500 py-4">Không có dữ liệu.</div>`;
        } else {
            document.getElementById('dailyReportTableBody').innerHTML = `<tr><td colspan="6" class="text-center text-gray-500 py-4">Không có dữ liệu.</td></tr>`;
        }
        updateSummaryTable(reports);
        return;
    }

    if (isMobile) {
        const mobileContainer = document.getElementById('mobileReportList');
        mobileContainer.innerHTML = '';
        
        reports.forEach((report) => {
            const originalIndex = dailyReports.findIndex(r => r === report);
            const isHighlighted = originalIndex === currentlyPayingReportIndex;
            const item = document.createElement('div');
            item.className = 'mobile-report-item' + (isHighlighted ? ' paying-highlight' : '');
            const ghiChuHtml = report.Ghi_Chú ? `<div class="mobile-report-ghi-chu"><strong>Ghi chú:</strong> ${report.Ghi_Chú}</div>` : '';
            
            const driver = driverList.find(d => d.driverNamePhone === report.driver);
            const verifiedIconHtml = (driver && driver.isVerified) ? `<span class="verified-check">&#10004;</span>` : '';
            const driverDisplayName = report.isForSale ? `${report.driver} (TOA BÁN)` : report.driver;

            let paymentButtonHtml = '';
            if (userType === 'quanly' && !report.isForSale) {
                paymentButtonHtml = `<button class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-md flex items-center justify-center" onclick="event.stopPropagation(); window.showPaymentModal(${originalIndex})">Thanh Toán`;
                if (report.isPaid) {
                     paymentButtonHtml += ` <span class="verified-check">&#10004;</span>`;
                }
                paymentButtonHtml += `</button>`;
            }
            
            const totalKg = calculateTotalKG(report);
            const totalAmount = calculateTotalAmount(report);

            item.innerHTML = `
                <div class="mobile-report-main" onclick="toggleReportDetails(this)">
                    <div class="flex-grow">
                        <div class="flex items-center">
                            <span class="toa">${report.toa || '?'}</span>
                            <span class="lai">${driverDisplayName}${verifiedIconHtml}</span>
                        </div>
                        <div class="flex justify-around text-sm mt-2 pt-2 border-t border-gray-100">
                            <div class="text-center">
                                <span class="block text-xs text-gray-500">Tổng KG</span>
                                <span class="font-semibold text-gray-800">${totalKg.toLocaleString('vi-VN')}</span>
                            </div>
                            <div class="text-center">
                                <span class="block text-xs text-gray-500">Thành Tiền</span>
                                <span class="font-semibold text-indigo-600">${totalAmount.toLocaleString('vi-VN')}</span>
                            </div>
                        </div>
                    </div>
                    <span class="mobile-report-toggle-icon self-start ml-2">▼</span>
                </div>
                <div class="mobile-report-details">
                    <div class="mobile-report-actions">
                         <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); window.showInvoice(${originalIndex})">Hóa Đơn</button>
                         ${paymentButtonHtml}
                         <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); window.editDailyReport(dailyReports[${originalIndex}])">Sửa</button>
                         <button class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); window.deleteDailyReport(dailyReports[${originalIndex}])">Xóa</button>
                    </div>
                    ${ghiChuHtml}
                </div>
            `;

            if (expandedToaToRestore.has(String(report.toa))) {
                const details = item.querySelector('.mobile-report-details');
                const icon = item.querySelector('.mobile-report-toggle-icon');
                if(details && icon) {
                    details.classList.add('expanded');
                    icon.style.transform = 'rotate(180deg)';
                }
            }
            
            mobileContainer.appendChild(item);

            if (isHighlighted) {
                setTimeout(() => {
                    item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        });
    } else {
        const tableBody = document.getElementById('dailyReportTableBody');
        tableBody.innerHTML = '';
        
        reports.forEach((report) => {
            const originalIndex = dailyReports.findIndex(r => r === report);
            const isHighlighted = originalIndex === currentlyPayingReportIndex;
            const row = tableBody.insertRow();
            row.className = isHighlighted ? 'paying-highlight' : '';

            row.insertCell().textContent = report.toa || '?';
            
            const laiCell = row.insertCell();
            const driver = driverList.find(d => d.driverNamePhone === report.driver);
            const driverDisplayName = report.isForSale ? `${report.driver} (TOA BÁN)` : report.driver;
            let laiHtml = driverDisplayName;
            if (driver && driver.isVerified) {
                laiHtml += ` <span class="verified-check" title="Tài khoản đã được xác thực">&#10004;</span>`;
            }
            laiCell.innerHTML = laiHtml;
            
            const totalKg = calculateTotalKG(report);
            const totalAmount = calculateTotalAmount(report);

            row.insertCell().textContent = totalKg.toLocaleString('vi-VN');

            const thanhTienCell = row.insertCell();
            thanhTienCell.textContent = totalAmount.toLocaleString('vi-VN');
            thanhTienCell.classList.add('font-semibold', 'text-indigo-600');

            const ghiChuCell = row.insertCell();
            ghiChuCell.textContent = report.Ghi_Chú || '';
            ghiChuCell.style.whiteSpace = 'normal';
            ghiChuCell.style.maxWidth = '250px';

            const actionsCell = row.insertCell();
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'flex gap-2 justify-center p-2';
            
            const invoiceButton = document.createElement('button');
            invoiceButton.textContent = 'Hóa Đơn';
            invoiceButton.className = 'bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            invoiceButton.onclick = () => showInvoice(originalIndex);
            buttonContainer.appendChild(invoiceButton);

            if (userType === 'quanly' && !report.isForSale) {
                const paymentButton = document.createElement('button');
                paymentButton.innerHTML = 'Thanh Toán';
                if (report.isPaid) {
                    paymentButton.innerHTML += ' <span class="verified-check">&#10004;</span>';
                }
                paymentButton.className = 'bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-bold py-1 px-2 rounded-md flex items-center';
                paymentButton.onclick = () => showPaymentModal(originalIndex);
                buttonContainer.appendChild(paymentButton);
            }
            
            const editButton = document.createElement('button');
            editButton.textContent = 'Sửa';
            editButton.className = 'bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            editButton.onclick = () => editDailyReport(report);
            buttonContainer.appendChild(editButton);
            
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Xóa';
            deleteButton.className = 'bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            deleteButton.onclick = () => deleteDailyReport(report);
            buttonContainer.appendChild(deleteButton);
            actionsCell.appendChild(buttonContainer);

            if (isHighlighted) {
                setTimeout(() => {
                    row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        });
    }
    updateSummaryTable(reports);
}

function resetFormForNewEntry() {
    setLiveEditingReport(null);
    const searchInput = document.getElementById('dailyReportDriverSearch');
    searchInput.value = '';
    document.getElementById('catBatHang').value = '';
    document.getElementById('Ghi_Chú').value = '';
    document.getElementById('noPayment').checked = false;
    document.getElementById('isForSale').checked = false;
    document.getElementById('adjustment').value = '';
    
    const numberInputs = document.querySelector('#dailyReportForm .input-group');
    const allNumberDivs = numberInputs.querySelectorAll('input');
    allNumberDivs.forEach(input => input.value = '');

    const calculationResults = document.querySelectorAll('#dailyReportForm .calculation-result');
    calculationResults.forEach(span => span.style.display = 'none');
    document.getElementById('add-report-title').textContent = 'Thêm Dữ Liệu Báo Cáo';
    
    lockForm(true);
    searchInput.disabled = false;
    document.getElementById('changeDriverBtn').classList.add('hidden');
    calculateRowTotals();
}

export async function editDailyReport(reportToEdit) {
    if (liveEditingReport && liveEditingReport !== reportToEdit) {
        await handleAutoSave(true);
    }
    setLiveEditingReport(reportToEdit);
    document.getElementById('add-report-title').textContent = `Sửa Dữ Liệu Toa ${reportToEdit.toa}`;
    bindFormToReport(reportToEdit);
    showAllCalculationPreviews();
    lockForm(false);
    document.getElementById('dailyReportDriverSearch').disabled = true;
    document.getElementById('changeDriverBtn').classList.remove('hidden');
    openReportModal();
}

export async function deleteDailyReport(reportToDelete) {
    confirm('Bạn có chắc chắn muốn xóa dòng này?').then(async result => {
        if (result) {
            if (liveEditingReport === reportToDelete) {
                closeReportModal();
            }
            const indexToDelete = dailyReports.findIndex(report => report === reportToDelete);
            if (indexToDelete > -1) {
                const dateOfDeletedReport = dailyReports[indexToDelete].date;
                dailyReports.splice(indexToDelete, 1);
                setDailyReports(dailyReports);
                recalculateDailyToaNumbers(dateOfDeletedReport);
                
                const saveSuccess = await saveDailyReportsToCSV();
                if(saveSuccess) {
                   customAlert('Đã xóa báo cáo.');
                   filterReportsByDate(); 
                } else {
                   customAlert('Lỗi: Không thể xóa, vui lòng kiểm tra lại mạng.');
                   const reports = await loadDailyReports();
                   setDailyReports(reports);
                   filterReportsByDate();
                }
            }
        }
    });
}

function updateSummaryTable(reports) {
    const summaryTableBody = document.getElementById('summaryTableBody');
    summaryTableBody.innerHTML = '';
    
    const filteredReports = reports.filter(r => !r.isForSale);

    if (filteredReports.length === 0) {
        const row = summaryTableBody.insertRow();
        const cell = row.insertCell();
        cell.colSpan = 3;
        cell.textContent = 'Không có dữ liệu để tổng hợp.';
        cell.className = 'text-center text-gray-500 py-4';
        return;
    }
    const thaiTotals = { totalKg: 0, totalAmount: 0, types: {} };
    const indoTotals = { totalKg: 0, totalAmount: 0, types: {} };
    quantityColumns.forEach(col => {
        if (col.includes('_Thái')) {
            if (!thaiTotals.types[col]) thaiTotals.types[col] = { kg: 0, amount: 0 };
        } else if (col.includes('_indo')) {
            if (!indoTotals.types[col]) indoTotals.types[col] = { kg: 0, amount: 0 };
        }
    });
    filteredReports.forEach(report => {
        const totalAmountForReport = calculateTotalAmount(report);
        let accumulatedAmount = 0;

        quantityColumns.forEach(qtyCol => {
            const kg = getValueFromExpression(report[qtyCol]);
            const price = getValueFromExpression(report[`Giá_${qtyCol}`]);
            const amount = kg * price;
            
            if (qtyCol.includes('_Thái')) {
                thaiTotals.types[qtyCol].kg += kg;
                if (!report.noPayment) thaiTotals.types[qtyCol].amount += amount;
            } else if (qtyCol.includes('_indo')) {
                indoTotals.types[qtyCol].kg += kg;
                if (!report.noPayment) indoTotals.types[qtyCol].amount += amount;
            }
        });

        const adjustment = getValueFromExpression(report.adjustment || '0');
        if (!report.noPayment) {
            if(adjustment > 0) {
                 thaiTotals.totalAmount += adjustment; 
            } else {
                 indoTotals.totalAmount += adjustment;
            }
        }
    });

    thaiTotals.totalKg = Object.values(thaiTotals.types).reduce((acc, curr) => acc + curr.kg, 0);
    indoTotals.totalKg = Object.values(indoTotals.types).reduce((acc, curr) => acc + curr.kg, 0);
    thaiTotals.totalAmount = Object.values(thaiTotals.types).reduce((acc, curr) => acc + curr.amount, 0) + (thaiTotals.totalAmount > 0 ? thaiTotals.totalAmount - Object.values(thaiTotals.types).reduce((acc, curr) => acc + curr.amount, 0) : 0);
    indoTotals.totalAmount = Object.values(indoTotals.types).reduce((acc, curr) => acc + curr.amount, 0) + (indoTotals.totalAmount < 0 ? indoTotals.totalAmount - Object.values(indoTotals.types).reduce((acc, curr) => acc + curr.amount, 0) : 0);


    const createRow = (name, kg, amount, isHeader = false, isTotal = false) => {
        const row = summaryTableBody.insertRow();
        if (isHeader) {
            const cell = row.insertCell();
            cell.colSpan = 3;
            cell.textContent = name;
            cell.className = 'text-center font-bold text-lg bg-gray-200 p-3';
        } else {
            const nameCell = row.insertCell();
            const kgCell = row.insertCell();
            const amountCell = row.insertCell();
            nameCell.textContent = name;
            kgCell.textContent = kg.toLocaleString('vi-VN');
            amountCell.textContent = amount.toLocaleString('vi-VN');
            if (isTotal) {
                nameCell.classList.add('font-bold');
                kgCell.classList.add('font-bold');
                amountCell.classList.add('font-bold');
                row.classList.add('bg-gray-100');
            }
        }
    };
    const thaiTypeOrder = ["B1_Thái", "B2_Thái", "C1_Thái", "C2_Thái", "C3_Thái", "D1_Thái", "D2_Thái", "E_Thái", "Chợ_Thái", "Xơ_Thái"];
    const indoTypeOrder = ["A1_indo", "A2_indo", "B1_indo", "B2_indo", "B3_indo", "C1_indo", "C2_indo", "Chợ_1_indo", "Chợ_2_indo", "Xơ_indo"];
    createRow('HÀNG THÁI', 0, 0, true);
    thaiTypeOrder.forEach(type => {
        if (thaiTotals.types[type] && thaiTotals.types[type].kg > 0) {
            createRow(type.replace('_Thái', ''), thaiTotals.types[type].kg, thaiTotals.types[type].amount);
        }
    });
    createRow('Tổng Thái', thaiTotals.totalKg, thaiTotals.totalAmount, false, true);
    createRow('HÀNG INDO', 0, 0, true);
    indoTypeOrder.forEach(type => {
        if (indoTotals.types[type] && indoTotals.types[type].kg > 0) {
            createRow(type.replace('_indo', ''), indoTotals.types[type].kg, indoTotals.types[type].amount);
        }
    });
    createRow('Tổng Indo', indoTotals.totalKg, indoTotals.totalAmount, false, true);
    const grandTotalKg = thaiTotals.totalKg + indoTotals.totalKg;
    const grandTotalAmount = thaiTotals.totalAmount + indoTotals.totalAmount;
    createRow('TỔNG CỘNG', grandTotalKg, grandTotalAmount, false, true);
    const lastRow = summaryTableBody.rows[summaryTableBody.rows.length - 1];
    lastRow.classList.add('bg-blue-200', 'text-blue-800');
}

export function openSummaryModal() {
    document.getElementById('summaryModal').style.display = 'block';
}

export function toggleStickyPanel() {
    const panel = document.getElementById('reportOptionsPanel');
    const button = document.getElementById('toggleStickyButton');
    panel.classList.toggle('expanded');
    if (panel.classList.contains('expanded')) {
        button.textContent = '︾';
    } else {
        button.textContent = '︽';
    }
}