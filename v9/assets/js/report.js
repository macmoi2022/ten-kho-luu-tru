// assets/js/report.js

// Biến cục bộ
let dailyReports = [];
let liveEditingReport = null;

// Tính tổng KG của một báo cáo
function calculateTotalKG(report) {
    if (!report) return 0;
    return quantityColumns.reduce((total, col) => total + getValueFromExpression(report[col]), 0);
}

// Tính thành tiền của một báo cáo
function calculateTotalAmount(report) {
    if (!report) return 0;
    return quantityColumns.reduce((total, col) => {
        const qty = getValueFromExpression(report[col]);
        const price = getValueFromExpression(report[`Giá_${col}`]);
        return total + (qty * price);
    }, 0);
}

// Tính tổng KG và thành tiền trên form đang nhập
function calculateRowTotals() {
    let totalKG = 0;
    let totalAmount = 0;
    quantityColumns.forEach(qtyColId => {
        const qtyEl = document.getElementById(qtyColId);
        const priceEl = document.getElementById(`Giá_${qtyColId}`);
        if(qtyEl && priceEl) {
            const qty = getValueFromExpression(qtyEl.value);
            const price = getValueFromExpression(priceEl.value);
            totalKG += qty;
            totalAmount += qty * price;
        }
    });

    const tongKgDisplay = totalKG > 0 ? totalKG.toLocaleString('vi-VN') : '0';
    const thanhTienDisplay = totalAmount > 0 ? totalAmount.toLocaleString('vi-VN') : '0';

    document.getElementById('Tổng_KG_display').value = tongKgDisplay;
    document.getElementById('Thành_Tiền_display').value = thanhTienDisplay;
    document.getElementById('sticky-total-kg').textContent = tongKgDisplay;
    document.getElementById('sticky-total-tien').textContent = thanhTienDisplay;
}

// Tải báo cáo từ server
async function loadDailyReports() {
    const result = await api.loadReports(activeDataDir);
    if (result && result.success) {
        dailyReports = result.data.map(r => ({ ...r, isPaid: r.isPaid === 'true' })) || [];
    } else {
        console.error("Lỗi khi tải báo cáo:", result.message);
        customAlert(result.message || 'Không thể tải báo cáo.');
        dailyReports = [];
    }
    filterReportsByDate();
}

// Lưu báo cáo lên server
async function saveDailyReportsToCSV() {
    if (isSaving) return false;
    isSaving = true;

    const reportsToSave = dailyReports.map(report => {
        const reportCopy = { ...report };
        reportCopy['Tổng_KG'] = calculateTotalKG(reportCopy);
        reportCopy['Thành_Tiền'] = calculateTotalAmount(reportCopy);
        reportCopy.isPaid = report.isPaid ? 'true' : 'false'; 
        return reportCopy;
    });

    const result = await api.saveReports(reportsToSave, activeDataDir);
    isSaving = false;

    if (!result.success) {
        customAlert(result.message || 'Lỗi không xác định khi lưu báo cáo.');
    }
    
    return result.success;
}

// Hiển thị báo cáo lên bảng (sửa lại cách gán sự kiện cho nút)
function renderDailyReportTable(reports) {
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
        const mobileContainer = document.getElementById('mobileReportList');
        mobileContainer.innerHTML = '';
        reports.forEach((report, index) => {
            const originalIndex = dailyReports.findIndex(r => r === report);
            const item = document.createElement('div');
            item.className = 'mobile-report-item';
            const ghiChuHtml = report.Ghi_Chú ? `<div class="mobile-report-ghi-chu"><strong>Ghi chú:</strong> ${report.Ghi_Chú}</div>` : '';
            
            const driver = driverList.find(d => d.driverNamePhone === report.driver);
            const verifiedIconHtml = (driver && driver.isVerified) ? `<span class="verified-check">&#10004;</span>` : '';

            let paymentButtonHtml = `<button data-index="${originalIndex}" class="btn-show-payment bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-md flex items-center justify-center">Thanh Toán`;
            if (report.isPaid) {
                 paymentButtonHtml += ` <span class="verified-check">&#10004;</span>`;
            }
            paymentButtonHtml += `</button>`;
            
            const totalKg = calculateTotalKG(report);
            const totalAmount = calculateTotalAmount(report);

            item.innerHTML = `
                <div class="mobile-report-main" onclick="toggleReportDetails(this)">
                    <div class="flex-grow">
                        <div class="flex items-center">
                            <span class="toa">${report.toa || '?'}</span>
                            <span class="lai">${report.driver}${verifiedIconHtml}</span>
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
                         <button data-index="${originalIndex}" class="btn-show-invoice bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md">Hóa Đơn</button>
                         ${paymentButtonHtml}
                         <button data-index="${originalIndex}" class="btn-edit-report bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md">Sửa</button>
                         <button data-index="${originalIndex}" class="btn-delete-report bg-red-500 hover:bg-red-600 text-white font-bold rounded-md">Xóa</button>
                    </div>
                    ${ghiChuHtml}
                </div>`;
            mobileContainer.appendChild(item);
        });
    } else { // Desktop
        const tableBody = document.getElementById('dailyReportTableBody');
        tableBody.innerHTML = '';
        reports.forEach((report, index) => {
            const originalIndex = dailyReports.findIndex(r => r === report);
            const row = tableBody.insertRow();

            row.insertCell().textContent = report.toa || '?';
            
            const laiCell = row.insertCell();
            const driver = driverList.find(d => d.driverNamePhone === report.driver);
            laiCell.innerHTML = `${report.driver} ${(driver && driver.isVerified) ? '<span class="verified-check" title="Tài khoản đã được xác thực">&#10004;</span>' : ''}`;
            
            row.insertCell().textContent = calculateTotalKG(report).toLocaleString('vi-VN');
            const thanhTienCell = row.insertCell();
            thanhTienCell.textContent = calculateTotalAmount(report).toLocaleString('vi-VN');
            thanhTienCell.className = 'font-semibold text-indigo-600';
            const ghiChuCell = row.insertCell();
            ghiChuCell.textContent = report.Ghi_Chú || '';
            ghiChuCell.className = 'whitespace-normal max-w-xs';
            
            const actionsCell = row.insertCell();
            actionsCell.className = 'p-2';
            let paymentButtonHtml = `<button data-index="${originalIndex}" class="btn-show-payment bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-bold py-1 px-2 rounded-md flex items-center">Thanh Toán${report.isPaid ? ' <span class="verified-check ml-1">&#10004;</span>' : ''}</button>`;
            actionsCell.innerHTML = `
                <div class="flex gap-2 justify-center">
                    <button data-index="${originalIndex}" class="btn-show-invoice bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold py-1 px-2 rounded-md">Hóa Đơn</button>
                    ${paymentButtonHtml}
                    <button data-index="${originalIndex}" class="btn-edit-report bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md">Sửa</button>
                    <button data-index="${originalIndex}" class="btn-delete-report bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md">Xóa</button>
                </div>`;
        });
    }
}


// Lọc và hiển thị báo cáo theo ngày đã chọn
function filterReportsByDate() {
    const selectedDate = document.getElementById('reportDate').value;
    const filtered = dailyReports.filter(report => report.date === selectedDate);
    filtered.sort((a, b) => (a.toa || 0) - (b.toa || 0));
    renderDailyReportTable(filtered);
    updateSummaryTable(filtered);
}

// Xử lý sự kiện khi người dùng nhập liệu vào form
function handleFormInput(e) {
    if (e.target.id === 'dailyReportDriverSearch') return;

    clearTimeout(saveTimeout);
    if (liveEditingReport) {
        saveTimeout = setTimeout(handleAutoSave, 1500);
    }

    const input = e.target;
    if (allCalcColumns.includes(input.id)) {
        input.value = input.value.replace(/[^0-9\+]/g, '');
        updateCalculationPreview(input);
    }
    calculateRowTotals();
}

// Cập nhật giá trị xem trước của phép tính
function updateCalculationPreview(inputElement) {
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

// Tự động lưu báo cáo đang chỉnh sửa
async function handleAutoSave(silent = false) {
    if (!liveEditingReport || isSaving) return;
    updateLiveReportFromForm();
    const success = await saveDailyReportsToCSV();
    if (success) {
        if (!silent) customAlert("Đã lưu tự động.");
        filterReportsByDate();
    } else {
        customAlert("Lỗi: Không thể tự động lưu.");
    }
}

// Cập nhật đối tượng liveEditingReport từ dữ liệu form
function updateLiveReportFromForm() {
    if (!liveEditingReport) return;
    liveEditingReport.driver = document.getElementById('dailyReportDriverSearch').value;
    liveEditingReport.Ghi_Chú = document.getElementById('Ghi_Chú').value;
    allCalcColumns.forEach(col => {
        const input = document.getElementById(col);
        if (input) liveEditingReport[col] = input.value;
    });
}

// Mở form để thêm dòng mới
async function handleAddNewRowClick() {
    document.getElementById('add-report-title').classList.remove('hidden');
    document.getElementById('dailyReportForm').classList.remove('hidden');
    if (liveEditingReport) {
        await handleAutoSave(true);
    }
    resetFormForNewEntry();
    document.getElementById('add-report-title').scrollIntoView({ behavior: 'smooth' });
}

// Đặt lại form về trạng thái ban đầu
function resetFormForNewEntry() {
    liveEditingReport = null;
    const form = document.getElementById('dailyReportForm');
    if(form) form.reset();
    document.querySelectorAll('#dailyReportForm .calculation-result').forEach(span => span.style.display = 'none');
    document.getElementById('add-report-title').textContent = 'Thêm Dữ Liệu Báo Cáo';
    lockForm(true);
    document.getElementById('dailyReportDriverSearch').disabled = false;
    document.getElementById('changeDriverBtn').classList.add('hidden');
    calculateRowTotals();
    document.getElementById('dailyReportDriverSearch').focus();
}

// Khóa/Mở khóa các trường nhập liệu trên form
function lockForm(shouldLock) {
    document.querySelectorAll('#Ghi_Chú, .input-group input').forEach(input => {
        input.disabled = shouldLock;
    });
}

// Điền dữ liệu từ một báo cáo vào form
function bindFormToReport(report) {
    document.getElementById('dailyReportDriverSearch').value = report.driver;
    document.getElementById('Ghi_Chú').value = report.Ghi_Chú || '';
    allCalcColumns.forEach(col => {
        const input = document.getElementById(col);
        if (input) {
            input.value = report[col] || '';
            updateCalculationPreview(input);
        }
    });
    calculateRowTotals();
}

// Chỉnh sửa một báo cáo đã có
async function editDailyReport(reportToEdit) {
    document.getElementById('add-report-title').classList.remove('hidden');
    document.getElementById('dailyReportForm').classList.remove('hidden');
    if (liveEditingReport && liveEditingReport !== reportToEdit) {
        await handleAutoSave(true);
    }
    liveEditingReport = reportToEdit;
    document.getElementById('add-report-title').textContent = `Sửa Dữ Liệu Toa ${reportToEdit.toa}`;
    bindFormToReport(reportToEdit);
    lockForm(false);
    document.getElementById('dailyReportDriverSearch').disabled = true;
    document.getElementById('changeDriverBtn').classList.remove('hidden');
    document.getElementById('add-report-title').scrollIntoView({ behavior: 'smooth' });
}

// Xóa một báo cáo
async function deleteDailyReport(reportToDelete) {
    const confirmed = await confirm('Bạn có chắc chắn muốn xóa dòng này?');
    if (confirmed) {
        const indexToDelete = dailyReports.findIndex(report => report === reportToDelete);
        if (indexToDelete > -1) {
            const dateOfDeletedReport = dailyReports[indexToDelete].date;
            dailyReports.splice(indexToDelete, 1);
            recalculateDailyToaNumbers(dateOfDeletedReport);
            
            const saveSuccess = await saveDailyReportsToCSV();
            if(saveSuccess) {
               customAlert('Đã xóa báo cáo.');
               if (liveEditingReport === reportToDelete) resetFormForNewEntry();
               filterReportsByDate();
            } else {
               customAlert('Lỗi: Không thể xóa, đang tải lại dữ liệu.');
               await loadDailyReports();
            }
        }
    }
}

function recalculateDailyToaNumbers(date) {
    dailyReports.filter(r => r.date === date)
        .sort((a, b) => a.toa - b.toa)
        .forEach((report, index) => {
            report.toa = index + 1;
        });
}

// Hiển thị kết quả tìm kiếm lái xe
function renderDriverSearchResults(searchTerm = '') {
    const searchResultsContainer = document.getElementById('driverSearchResults');
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
            item.addEventListener('click', () => handleDriverSelection(driver.driverNamePhone));
            searchResultsContainer.appendChild(item);
        });
        searchResultsContainer.classList.remove('hidden');
    } else {
        searchResultsContainer.classList.add('hidden');
    }
}

// Xử lý khi chọn một lái xe từ danh sách tìm kiếm
function handleDriverSelection(selectedDriver) {
    const searchInput = document.getElementById('dailyReportDriverSearch');
    document.getElementById('driverSearchResults').classList.add('hidden');
    searchInput.value = selectedDriver;
    
    if (liveEditingReport) {
        liveEditingReport.driver = selectedDriver;
    } else {
        const selectedDate = document.getElementById('reportDate').value;
        const newReport = { date: selectedDate, driver: selectedDriver, Ghi_Chú: '', isPaid: false };
        quantityColumns.forEach(col => { newReport[col] = ''; newReport[`Giá_${col}`] = ''; });
        
        const lastReportForDay = dailyReports.filter(r => r.date === selectedDate).pop();
        if (lastReportForDay) {
            priceColumns.forEach(priceCol => {
                newReport[priceCol] = lastReportForDay[priceCol] || '';
            });
            customAlert("Đã tự động điền giá theo toa cuối cùng.");
        }
        
        dailyReports.push(newReport);
        recalculateDailyToaNumbers(selectedDate);
        liveEditingReport = newReport;
    }

    bindFormToReport(liveEditingReport);
    lockForm(false);
    searchInput.disabled = true;
    document.getElementById('changeDriverBtn').classList.remove('hidden');
    document.getElementById('Ghi_Chú').focus();
    handleAutoSave(true);
    filterReportsByDate();
}

// Mở khóa ô tìm kiếm lái để đổi lái
function unlockDriverSelection() {
    lockForm(true);
    const searchInput = document.getElementById('dailyReportDriverSearch');
    searchInput.disabled = false;
    document.getElementById('changeDriverBtn').classList.add('hidden');
    searchInput.focus();
    searchInput.select();
}

// *** HÀM TẠO HÓA ĐƠN ĐẦY ĐỦ ***
function generateQuantityCellHtml(expression, isShrunk = false, useComplexLayout = true) {
    const sum = getValueFromExpression(expression);
    if (!useComplexLayout) {
        return `<div class="invoice-single-number-cell"><div>${sum > 0 ? sum.toLocaleString('vi-VN') : ''}</div></div>`;
    }
    const gridClass = isShrunk ? 'invoice-calculation-grid' : 'invoice-calculation-grid';
    const hasCalculation = expression && String(expression).includes('+') && String(expression).trim() !== String(sum);
    if (!hasCalculation) {
        return `<div class="${gridClass}" style="grid-template-rows: 1fr; align-items: end; justify-items: center;"><div style="grid-column: 1 / 3;">${sum > 0 ? sum.toLocaleString('vi-VN') : ''}</div></div>`;
    }
    const numbersHtml = String(expression).split('+').map(num => `<span>${num.trim()}</span>`).join('');
    const numbersClass = isShrunk ? 'invoice-calculation-numbers' : 'invoice-calculation-numbers';
    return `<div class="${gridClass}"><div class="col-start-1">+</div><div class="${numbersClass}">${numbersHtml}</div><div class="col-start-1">=</div><div class="col-start-2">${sum.toLocaleString('vi-VN')}</div></div>`;
}

async function showInvoice(reportIndex) {
    const report = dailyReports[reportIndex];
    if (!report) return;
    const invoiceWrapper = document.getElementById('invoice-content-wrapper');
    const driver = driverList.find(d => d.driverNamePhone === report.driver);
    const logoUrl = 'logo.jpg';
    const thaiColumns = quantityColumns.filter(c => c.includes('_Thái'));
    const indoColumns = quantityColumns.filter(c => c.includes('_indo'));
    const activeThaiColumns = thaiColumns.filter(col => getValueFromExpression(report[col]) > 0);
    const activeIndoColumns = indoColumns.filter(col => getValueFromExpression(report[col]) > 0);
    const hasThaiData = activeThaiColumns.length > 0;
    const hasIndoData = activeIndoColumns.length > 0;
    let allPagesHtml = '';
    const hasCalculationThai = activeThaiColumns.some(col => report[col] && String(report[col]).includes('+'));
    const hasCalculationIndo = activeIndoColumns.some(col => report[col] && String(report[col]).includes('+'));

    if (hasThaiData && hasIndoData) {
        const portraitHeaderHtml = `<header class="invoice-header flex justify-between items-center mb-2"><div class="flex items-center"><img src="${logoUrl}" alt="Logo" class="invoice-logo rounded-md mr-4" onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/ffffff?text=LOGO';"></div><div class="text-right"><h1 class="font-extrabold text-gray-900 leading-tight"><span class="text-xl">Vựa Mít</span><br><span class="text-4xl">NGỌC ANH HD68</span></h1><div class="text-xs text-gray-600 mt-1"><p>CS1: Cầu Ông Hưng - Cái Bè - Tiền Giang</p><p>CS2: QL30 - Cầu Rạch Giồng - Cái Bè - Tiền Giang</p><p>ĐT: 0941.017.878 (A Phong) - 0901.422.658 (A Huy)</p></div></div></header>`;
        const customerInfoHtml = `<div class="main-title text-center border-t-2 border-gray-800 pt-2"><h2 class="font-bold text-indigo-600 uppercase" style="padding-bottom: 5mm;">Hóa Đơn</h2></div><div class="customer-info"><p><strong>Ngày lập:</strong> ${report.date}</p><p><strong>Khách hàng:</strong> ${report.driver}</p>${driver ? `<p><strong>STK:</strong> ${driver.accountNumber} - ${driver.bankName} - ${driver.accountName}</p>` : ''}</div>`;
        let thaiTotalKg = 0, thaiTotalAmount = 0, thaiHeader = `<th>Loại Hàng</th>`, thaiQtyRow = `<td class="font-medium">Số lượng</td>`, thaiPriceRow = `<td class="font-medium">Đơn giá</td>`, thaiAmountRow = `<td class="font-bold">Thành tiền</td>`;
        const quantityValignClassThai = hasCalculationThai ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';
        activeThaiColumns.forEach(col => { const qty = getValueFromExpression(report[col]), price = getValueFromExpression(report[`Giá_${col}`]), amount = qty * price; thaiTotalKg += qty; thaiTotalAmount += amount; thaiHeader += `<th>${col.replace('_Thái', ' Thái')}</th>`; thaiQtyRow += `<td class="${quantityValignClassThai}">${generateQuantityCellHtml(report[col], true, hasCalculationThai)}</td>`; thaiPriceRow += `<td>${price > 0 ? price.toLocaleString('vi-VN') : ''}</td>`; thaiAmountRow += `<td class="font-semibold">${amount > 0 ? amount.toLocaleString('vi-VN') : ''}</td>`; });
        thaiHeader += `<th class="font-bold">Tổng Cộng (Thái)</th>`; thaiQtyRow += `<td class="font-bold">${thaiTotalKg.toLocaleString('vi-VN')}</td>`; thaiPriceRow += `<td></td>`; thaiAmountRow += `<td class="font-bold">${thaiTotalAmount.toLocaleString('vi-VN')}</td>`;
        const thaiTableHtml = `<div class="mb-4"><h3 class="table-heading font-bold text-gray-800 mb-1 pl-2 border-l-4 border-yellow-400" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT THÁI</h3><div class="invoice-table-wrapper"><table class="invoice-table"><thead><tr class="bg-gray-100">${thaiHeader}</tr></thead><tbody><tr class="${hasCalculationThai ? 'has-calculation' : ''}">${thaiQtyRow}</tr><tr>${thaiPriceRow}</tr><tr class="bg-gray-50">${thaiAmountRow}</tr></tbody></table></div></div>`;
        let indoTotalKg = 0, indoTotalAmount = 0, indoHeader = `<th>Loại Hàng</th>`, indoQtyRow = `<td class="font-medium">Số lượng</td>`, indoPriceRow = `<td class="font-medium">Đơn giá</td>`, indoAmountRow = `<td class="font-bold">Thành tiền</td>`;
        const quantityValignClassIndo = hasCalculationIndo ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';
        activeIndoColumns.forEach(col => { const qty = getValueFromExpression(report[col]), price = getValueFromExpression(report[`Giá_${col}`]), amount = qty * price; indoTotalKg += qty; indoTotalAmount += amount; indoHeader += `<th>${col.replace('_indo', ' Indo')}</th>`; indoQtyRow += `<td class="${quantityValignClassIndo}">${generateQuantityCellHtml(report[col], true, hasCalculationIndo)}</td>`; indoPriceRow += `<td>${price > 0 ? price.toLocaleString('vi-VN') : ''}</td>`; indoAmountRow += `<td class="font-semibold">${amount > 0 ? amount.toLocaleString('vi-VN') : ''}</td>`; });
        indoHeader += `<th class="font-bold">Tổng Cộng (Indo)</th>`; indoQtyRow += `<td class="font-bold">${indoTotalKg.toLocaleString('vi-VN')}</td>`; indoPriceRow += `<td></td>`; indoAmountRow += `<td class="font-bold">${indoTotalAmount.toLocaleString('vi-VN')}</td>`;
        const indoTableHtml = `<div class="mb-4"><h3 class="table-heading font-bold text-gray-800 mb-1 pl-2 border-l-4 border-red-400" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT INDO</h3><div class="invoice-table-wrapper"><table class="invoice-table"><thead><tr class="bg-gray-100">${indoHeader}</tr></thead><tbody><tr class="${hasCalculationIndo ? 'has-calculation' : ''}">${indoQtyRow}</tr><tr>${indoPriceRow}</tr><tr class="bg-gray-50">${indoAmountRow}</tr></tbody></table></div></div>`;
        const portraitFooterHtml = `<footer class="invoice-footer mt-auto pt-4"><div class="flex justify-end"><div class="w-full md:w-2/3 grand-total"><div class="flex items-center py-2 border-t-2 border-b-2 border-gray-800" style="padding-bottom: 5mm;"><span class="font-bold text-gray-800 whitespace-nowrap mr-auto">TỔNG CỘNG HÓA ĐƠN:</span><span class="total-amount font-bold text-indigo-600 whitespace-nowrap text-right">${(parseFloat(report.Thành_Tiền) || 0).toLocaleString('vi-VN')} VND</span></div></div></div><div class="flex justify-end"><div class="signature text-center"><p class="font-semibold text-gray-800 mb-12">Người lập hóa đơn</p></div></div></footer>`;
        allPagesHtml = `<div class="a4-sheet-portrait shrunk-invoice">${portraitHeaderHtml}<main class="invoice-content">${customerInfoHtml}${thaiTableHtml}${indoTableHtml}</main>${portraitFooterHtml}</div>`;
    } else {
        const headerHtml = `<div class="invoice-header flex justify-between items-center mb-4"><div class="flex items-center"><img src="${logoUrl}" alt="Logo" class="invoice-logo rounded-md mr-4" onerror="this.onerror=null; this.src='https://placehold.co/140x140/cccccc/ffffff?text=LOGO';"></div><div class="text-right"><h1 class="font-extrabold text-gray-900 leading-tight"><span class="text-xl">Vựa Mít</span><br><span class="text-4xl">NGỌC ANH HD68</span></h1><div class="text-xs text-gray-600 mt-2"><p>CS1: Cầu Ông Hưng - Cái Bè - Tiền Giang</p><p>CS2: QL30 - Cầu Rạch Giồng - Cái Bè - Tiền Giang</p><p>ĐT: 0941.017.878 (A Phong) - 0901.422.658 (A Huy)</p></div></div></div>`;
        const customerInfoAndTitleHtml = `<div class="border-t-2 border-gray-800 pt-3 mb-4"><h2 class="text-center text-5xl font-bold text-indigo-600 uppercase" style="padding-bottom: 5mm;">Hóa Đơn</h2></div><div class="mb-6 text-sm"><div class="mb-2"><h3 class="font-semibold text-gray-800 mb-1">Ngày lập hóa đơn: ${report.date}</h3></div><div><h3 class="font-semibold text-gray-800 mb-1">Khách hàng:</h3><p class="text-gray-700">${report.driver}</p>${driver ? `<p class="text-gray-700">STK: ${driver.accountNumber} - ${driver.bankName} - ${driver.accountName}</p>` : ''}</div></div>`;
        const finalSectionHtml = `<div class="flex justify-end mt-8 print-break-inside-avoid"><div class="w-full"><div class="flex items-center py-2 border-t-2 border-b-2 border-gray-800" style="padding-bottom: 5mm;"><span class="text-lg font-bold text-gray-800 whitespace-nowrap mr-auto">TỔNG CỘNG HÓA ĐƠN:</span><span class="text-2xl font-bold text-indigo-600 whitespace-nowrap text-right">${(parseFloat(report['Thành_Tiền']) || 0).toLocaleString('vi-VN')} VND</span></div></div></div><div class="mt-8 pt-4 print-break-inside-avoid"><div class="flex justify-end"><div class="text-center"><p class="font-semibold text-gray-800 mb-12">Người lập hóa đơn</p></div></div></div>`;
        if (hasThaiData) {
            let thaiTotalKg = 0, thaiTotalAmount = 0, header = `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Loại Hàng</th>`, qtyRow = `<td class="p-1 font-medium border-2 border-gray-200 text-center">Số lượng</td>`, priceRow = `<td class="p-2 font-medium border-2 border-gray-200 text-center align-middle">Đơn giá</td>`, amountRow = `<td class="p-2 font-bold border-2 border-gray-200 text-center align-middle">Thành tiền</td>`;
            const quantityValignClass = hasCalculationThai ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';
            activeThaiColumns.forEach(col => { const qty = getValueFromExpression(report[col]), price = getValueFromExpression(report[`Giá_${col}`]), amount = qty * price; thaiTotalKg += qty; thaiTotalAmount += amount; header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">${col.replace('_Thái', ' Thái')}</th>`; qtyRow += `<td class="p-1 text-gray-700 border-2 border-gray-200 ${quantityValignClass}">${generateQuantityCellHtml(report[col], false, hasCalculationThai)}</td>`; priceRow += `<td class="p-2 text-gray-700 border-2 border-gray-200 text-center align-middle">${price.toLocaleString('vi-VN')}</td>`; amountRow += `<td class="p-2 text-gray-800 font-semibold border-2 border-gray-200 text-center align-middle">${amount.toLocaleString('vi-VN')}</td>`; });
            header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Tổng Cộng (Thái)</th>`; qtyRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center">${thaiTotalKg.toLocaleString('vi-VN')}</td>`; priceRow += `<td class="border-2 border-gray-200 align-middle"></td>`; amountRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center align-middle">${thaiTotalAmount.toLocaleString('vi-VN')}</td>`;
            allPagesHtml += `<div class="a4-sheet-landscape ${hasIndoData ? 'print-break-after' : ''}">${headerHtml}<div class="invoice-content">${customerInfoAndTitleHtml}<div class="mb-8 print-break-inside-avoid"><h3 class="text-lg font-bold text-gray-800 mb-2 pl-2 border-l-4 border-yellow-400" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT THÁI</h3><table class="invoice-table border-collapse text-sm"><thead><tr class="bg-gray-100">${header}</tr></thead><tbody><tr class="${hasCalculationThai ? 'has-calculation' : ''}">${qtyRow}</tr><tr>${priceRow}</tr><tr class="bg-gray-50">${amountRow}</tr></tbody></table></div>${!hasIndoData ? finalSectionHtml : ''}</div></div>`;
        }
        if (hasIndoData) {
            let indoTotalKg = 0, indoTotalAmount = 0, header = `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Loại Hàng</th>`, qtyRow = `<td class="p-1 font-medium border-2 border-gray-200 text-center">Số lượng</td>`, priceRow = `<td class="p-2 font-medium border-2 border-gray-200 text-center align-middle">Đơn giá</td>`, amountRow = `<td class="p-2 font-bold border-2 border-gray-200 text-center align-middle">Thành tiền</td>`;
            const quantityValignClass = hasCalculationIndo ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';
            activeIndoColumns.forEach(col => { const qty = getValueFromExpression(report[col]), price = getValueFromExpression(report[`Giá_${col}`]), amount = qty * price; indoTotalKg += qty; indoTotalAmount += amount; header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">${col.replace('_indo', ' Indo')}</th>`; qtyRow += `<td class="p-1 text-gray-700 border-2 border-gray-200 ${quantityValignClass}">${generateQuantityCellHtml(report[col], false, hasCalculationIndo)}</td>`; priceRow += `<td class="p-2 text-gray-700 border-2 border-gray-200 text-center align-middle">${price.toLocaleString('vi-VN')}</td>`; amountRow += `<td class="p-2 text-gray-800 font-semibold border-2 border-gray-200 text-center align-middle">${amount.toLocaleString('vi-VN')}</td>`; });
            header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Tổng Cộng (Indo)</th>`; qtyRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center">${indoTotalKg.toLocaleString('vi-VN')}</td>`; priceRow += `<td class="border-2 border-gray-200 align-middle"></td>`; amountRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center align-middle">${indoTotalAmount.toLocaleString('vi-VN')}</td>`;
            allPagesHtml += `<div class="a4-sheet-landscape">${!hasThaiData ? headerHtml : ''}<div class="invoice-content">${!hasThaiData ? customerInfoAndTitleHtml : ''}<div class="mb-8 print-break-inside-avoid"><h3 class="text-lg font-bold text-gray-800 mb-2 pl-2 border-l-4 border-[#E69B8A]" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT INDO</h3><table class="invoice-table border-collapse text-sm"><thead><tr class="bg-gray-100">${header}</tr></thead><tbody><tr class="${hasCalculationIndo ? 'has-calculation' : ''}">${qtyRow}</tr><tr>${priceRow}</tr><tr class="bg-gray-50">${amountRow}</tr></tbody></table></div>${finalSectionHtml}</div></div>`;
        }
    }

    if (!hasThaiData && !hasIndoData) {
        allPagesHtml = `<div class="a4-sheet-landscape"><div class="invoice-content flex items-center justify-center"><p>Không có dữ liệu hàng hóa cho hóa đơn này.</p></div></div>`;
    }
    
    invoiceWrapper.innerHTML = '<div class="text-center p-8"><p class="font-semibold text-gray-700">Đang tạo hình ảnh hóa đơn...</p></div>';
    document.getElementById('invoiceModalTitle').textContent = `Hóa Đơn - ${report.driver} - ${report.date}`;
    document.getElementById('invoiceModal').style.display = 'block';

    const tempContainer = document.createElement('div');
    tempContainer.innerHTML = allPagesHtml;
    const invoiceSheet = tempContainer.querySelector('.a4-sheet-portrait, .a4-sheet-landscape');
    if (!invoiceSheet) {
        return invoiceWrapper.innerHTML = '<p class="text-center p-8 text-red-600">Lỗi: Không thể tạo nội dung hóa đơn.</p>';
    }
    document.body.appendChild(invoiceSheet);
    invoiceSheet.style.cssText = 'position: absolute; left: -9999px;';

    await new Promise(resolve => setTimeout(resolve, 50));

    try {
        const canvas = await html2canvas(invoiceSheet, { scale: 2, useCORS: true });
        const imageUrl = canvas.toDataURL('image/jpeg', 0.95);
        const instructionText = window.innerWidth <= 768 ? 'Ấn giữ vào hóa đơn để lưu hoặc chia sẻ.' : 'Chuột phải vào hóa đơn để sao chép (Copy Image) và dán vào nơi cần gửi.';
        invoiceWrapper.innerHTML = `<div class="text-center p-2"><img src="${imageUrl}" alt="Hóa đơn" class="w-full h-auto border rounded-lg shadow-md" style="max-width: 800px; margin: 0 auto;"><p class="mt-4 text-gray-600 font-semibold">${instructionText}</p></div>`;
    } catch (error) {
        console.error("Lỗi khi tạo ảnh hóa đơn:", error);
        invoiceWrapper.innerHTML = '<div class="text-center p-8"><p class="text-red-600">Không thể tạo ảnh hóa đơn.</p></div>';
    } finally {
        document.body.removeChild(invoiceSheet);
    }
}

// Hiển thị modal thanh toán QR code
function showPaymentModal(reportIndex) {
    const report = dailyReports[reportIndex];
    if (!report) return;
    const driver = driverList.find(d => d.driverNamePhone === report.driver);
    if (!driver) {
        return customAlert(`Lỗi: Không tìm thấy thông tin ngân hàng cho Lái "${report.driver}".`);
    }
    if (!driver.isVerified) {
        return customAlert(`Lỗi: Tài khoản ngân hàng của Lái "${report.driver}" chưa được xác thực.`);
    }
    
    const amount = calculateTotalAmount(report);
    if (amount <= 0) return customAlert('Số tiền thanh toán phải lớn hơn 0.');
    
    const displayText = `HD68 ck ${report.driver.toUpperCase()} ${report.date.replace(/\//g, '.')}`;
    const descriptionForApi = removeAccents(displayText);
    const bankBin = getBankBin(driver.bankName);
    const qrApiUrl = `https://api.vietqr.io/image/${bankBin}-${driver.accountNumber}-qr_only.png?amount=${amount}&addInfo=${encodeURIComponent(descriptionForApi)}&accountName=${encodeURIComponent(driver.accountName)}`;
    
    document.getElementById('qrModalTitle').textContent = "Thanh toán tiền hàng";
    document.getElementById('qrCodeImg').src = qrApiUrl;
    
    const infoDiv = document.getElementById('qrModalInfo');
    const reportInfoHtml = `
        <div class="text-left mb-4 p-3 bg-gray-50 rounded-lg border">
            <p><strong>Toa ${report.toa} - ${report.driver}</strong></p>
            <p><strong>Tổng KG:</strong> ${(calculateTotalKG(report) || 0).toLocaleString('vi-VN')} - <strong>Thành Tiền:</strong> ${amount.toLocaleString('vi-VN')}đ</p>
            ${report.Ghi_Chú ? `<p><strong>Ghi Chú:</strong> ${report.Ghi_Chú}</p>` : ''}
        </div>
    `;
    infoDiv.innerHTML = reportInfoHtml + `<p><strong>Ngân hàng:</strong> ${driver.bankName}</p><p><strong>Số tài khoản:</strong> ${driver.accountNumber}</p><p><strong>Chủ tài khoản:</strong> ${driver.accountName}</p><p><strong>Nội dung:</strong> ${displayText}</p>`;
    
    const actionsDiv = document.getElementById('qrModalActions');
    const toggleButtonText = report.isPaid ? "Bỏ đánh dấu đã chuyển khoản" : "Đánh dấu đã chuyển khoản";
    const toggleButtonClass = report.isPaid ? "bg-yellow-500 hover:bg-yellow-600" : "bg-green-600 hover:bg-green-700";
    actionsDiv.innerHTML = `
        <button class="${toggleButtonClass} text-white font-bold py-2 px-4 rounded-lg" onclick="togglePaymentStatus(${reportIndex})">${toggleButtonText}</button>
        <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('qrModal')">Đóng</button>
    `;

    document.getElementById('qrModal').style.display = 'block';
}

// Thay đổi trạng thái thanh toán
async function togglePaymentStatus(reportIndex) {
    const report = dailyReports[reportIndex];
    if (!report) return;
    report.isPaid = !report.isPaid;
    await saveDailyReportsToCSV();
    filterReportsByDate();
    closeModal('qrModal');
}

// Xuất dữ liệu ra file Excel
function exportReportsToExcel() {
    const selectedDate = document.getElementById('reportDate').value;
    const filteredReports = dailyReports.filter(report => report.date === selectedDate);
    if (filteredReports.length === 0) return customAlert('Không có dữ liệu để xuất.');

    const headerRow = ['Toa', 'Lái', 'B1 Thái', 'Giá B1 Thái', 'B2 Thái', 'Giá B2 Thái', 'C1 Thái', 'Giá C1 Thái', 'C2 Thái', 'Giá C2 Thái', 'C3 Thái', 'Giá C3 Thái', 'D1 Thái', 'Giá D1 Thái', 'D2 Thái', 'Giá D2 Thái', 'E Thái', 'Giá E Thái', 'Chợ Thái', 'Giá Chợ Thái', 'Xơ Thái', 'Giá Xơ Thái', 'A1 indo', 'Giá A1 indo', 'A2 indo', 'Giá A2 indo', 'B1 indo', 'Giá B1 indo', 'B2 indo', 'Giá B2 indo', 'B3 indo', 'Giá B3 indo', 'C1 indo', 'Giá C1 indo', 'C2 indo', 'Giá C2 indo', 'Chợ 1 indo', 'Giá Chợ 1 indo', 'Chợ 2 indo', 'Giá Chợ 2 indo', 'Xơ indo', 'Giá Xơ indo', 'Tổng KG', 'Thành Tiền', 'Ghi Chú', 'Thanh Toán'];
    const dataRows = filteredReports.map(report => {
        let rowData = [report.toa, report.driver];
        quantityColumns.forEach(qtyCol => {
            rowData.push(getValueFromExpression(report[qtyCol]));
            rowData.push(getValueFromExpression(report[`Giá_${qtyCol}`]));
        });
        rowData.push(calculateTotalKG(report), calculateTotalAmount(report), report.Ghi_Chú, report.isPaid ? 'Đã CK' : '');
        return rowData;
    });
    const totals = { 'Tổng_KG': 0, 'Thành_Tiền': 0 };
    quantityColumns.forEach(col => { totals[col] = 0; });
    filteredReports.forEach(report => {
        quantityColumns.forEach(col => { totals[col] += getValueFromExpression(report[col]); });
        totals['Tổng_KG'] += (calculateTotalKG(report) || 0);
        totals['Thành_Tiền'] += (calculateTotalAmount(report) || 0);
    });
    const totalRow = ['TỔNG CỘNG', ''];
    quantityColumns.forEach(qtyCol => { totalRow.push(totals[qtyCol], ''); });
    totalRow.push(totals['Tổng_KG'], totals['Thành_Tiền'], '', '');
    const dataToExport = [headerRow, ...dataRows, totalRow];
    const ws = XLSX.utils.aoa_to_sheet(dataToExport);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'BaoCao');
    XLSX.writeFile(wb, `bao_cao_ngay_${selectedDate.replace(/\//g, '-')}.xlsx`);
}

// Cập nhật bảng tổng hợp
function updateSummaryTable(reports) {
    const summaryTableBody = document.getElementById('summaryTableBody');
    summaryTableBody.innerHTML = '';
    if (reports.length === 0) {
        return summaryTableBody.innerHTML = `<tr><td colspan="3" class="text-center text-gray-500 py-4">Không có dữ liệu để tổng hợp.</td></tr>`;
    }
    const totals = {};
    quantityColumns.forEach(col => { totals[col] = { kg: 0, amount: 0 }; });
    let grandTotalKg = 0, grandTotalAmount = 0;
    
    reports.forEach(report => {
        quantityColumns.forEach(qtyCol => {
            const kg = getValueFromExpression(report[qtyCol]);
            if (kg > 0) {
                const price = getValueFromExpression(report[`Giá_${qtyCol}`]);
                const amount = kg * price;
                totals[qtyCol].kg += kg;
                totals[qtyCol].amount += amount;
            }
        });
    });
    
    const typeOrder = [...quantityColumns.filter(c => c.includes('_Thái')), ...quantityColumns.filter(c => c.includes('_indo'))];
    let html = '';
    typeOrder.forEach(type => {
        if(totals[type] && totals[type].kg > 0) {
            html += `<tr><td>${type.replace(/_/g, ' ')}</td><td>${totals[type].kg.toLocaleString('vi-VN')}</td><td>${totals[type].amount.toLocaleString('vi-VN')}</td></tr>`;
            grandTotalKg += totals[type].kg;
            grandTotalAmount += totals[type].amount;
        }
    });
    html += `<tr class="bg-blue-100 font-bold"><td class="text-lg">TỔNG CỘNG</td><td class="text-lg">${grandTotalKg.toLocaleString('vi-VN')}</td><td class="text-lg">${grandTotalAmount.toLocaleString('vi-VN')}</td></tr>`;
    summaryTableBody.innerHTML = html;
}

// Mở modal tổng hợp
function openSummaryModal() {
    document.getElementById('summaryModal').style.display = 'block';
}