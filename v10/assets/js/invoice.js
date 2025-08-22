// Chứa logic tạo hóa đơn
import { dailyReports, driverList, quantityColumns } from './state.js';
import { getValueFromExpression, getBankShortName } from './utils.js';
import { calculateTotalAmount } from './reports.js';

function generateQuantityCellHtml(expression, isShrunk = false, useComplexLayout = true) {
    const sum = getValueFromExpression(expression);
    
    if (!useComplexLayout) {
        return `<div class="invoice-single-number-cell"><div>${sum > 0 ? sum.toLocaleString('vi-VN') : ''}</div></div>`;
    }

    const gridClass = isShrunk ? 'invoice-calculation-grid' : 'invoice-calculation-grid';
    const hasCalculation = expression && String(expression).includes('+') && String(expression).trim() !== String(sum);

    if (!hasCalculation) {
        return `
            <div class="${gridClass}" style="grid-template-rows: 1fr; align-items: end; justify-items: center;">
                 <div style="grid-column: 1 / 3;">
                    ${sum > 0 ? sum.toLocaleString('vi-VN') : ''}
                 </div>
            </div>
        `;
    }
    
    const numbersHtml = String(expression).split('+').map(num => `<span>${num.trim()}</span>`).join('');
    const numbersClass = isShrunk ? 'invoice-calculation-numbers' : 'invoice-calculation-numbers';

    return `
        <div class="${gridClass}">
            <div class="col-start-1">+</div>
            <div class="${numbersClass}">${numbersHtml}</div>
            <div class="col-start-1">=</div>
            <div class="col-start-2">${sum.toLocaleString('vi-VN')}</div>
        </div>
    `;
}

export async function showInvoice(reportIndex) {
    const report = dailyReports[reportIndex];
    if (!report) return;

    const invoiceWrapper = document.getElementById('invoice-content-wrapper');
    const driver = driverList.find(d => d.driverNamePhone === report.driver);
    const logoUrl = 'logo.png';

    const thaiColumns = quantityColumns.filter(c => c.includes('_Thái'));
    const indoColumns = quantityColumns.filter(c => c.includes('_indo'));

    const activeThaiColumns = thaiColumns.filter(col => getValueFromExpression(report[col]) > 0);
    const activeIndoColumns = indoColumns.filter(col => getValueFromExpression(report[col]) > 0);

    const hasThaiData = activeThaiColumns.length > 0;
    const hasIndoData = activeIndoColumns.length > 0;
    
    if (!hasThaiData && !hasIndoData) {
        invoiceWrapper.innerHTML = `<div class="a4-sheet-portrait flex items-center justify-center"><p>Không có dữ liệu hàng hóa cho hóa đơn này.</p></div>`;
        document.getElementById('invoiceModalTitle').textContent = `Hóa Đơn`;
        document.getElementById('invoiceModal').style.display = 'block';
        return;
    }

    const portraitHeaderHtml = `
        <header class="invoice-header flex justify-between items-center mb-2">
            <div class="text-center">
                <img src="${logoUrl}" alt="Logo" class="invoice-logo rounded-md" onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/ffffff?text=LOGO';">
            </div>
            <div class="text-right">
                <h1 class="font-extrabold text-gray-900 leading-tight">
                    <span class="text-xl">Vựa Mít</span><br>
                    <span class="text-4xl">NGỌC ANH HD68</span>
                </h1>
                <div class="text-xs text-gray-600 mt-1">
                    <p>CS1: QL1 - Cầu Ông Hưng - Cái Bè - Tiền Giang</p>
                    <p>CS2: QL30 - Cầu Rạch Giồng - Cái Bè - Tiền Giang</p>
                    <p>ĐT: 0941.017.878 (A Phong) - 0901.422.658 (A Huy)</p>
                </div>
            </div>
        </header>`;
    
    const customerDisplayName = report.isForSale ? `${report.driver} (TOA BÁN)` : report.driver;
    const customerInfoHtml = `
        <div class="main-title text-center border-t-2 border-gray-800 pt-2">
            <h2 class="font-bold text-indigo-600 uppercase" style="padding-bottom: 5mm;">Hóa Đơn</h2>
        </div>
        <div class="customer-info">
            <p><strong>Ngày lập:</strong> ${report.date} - <strong>Toa:</strong> ${report.toa}</p>
            <p><strong>Khách hàng:</strong> ${customerDisplayName.toUpperCase()}</p>
            ${driver ? `<p><strong>STK:</strong> ${driver.accountNumber} - ${getBankShortName(driver.bankName)} - ${driver.accountName.toUpperCase()}</p>` : ''}
        </div>`;
    
    let thaiTableHtml = '';
    if (hasThaiData) {
        const hasCalculationThai = activeThaiColumns.some(col => report[col] && String(report[col]).includes('+'));
        let thaiTotalKg = 0;
        let thaiTotalAmount = 0;
        
        const numThaiColumns = activeThaiColumns.length + 2; 
        const thaiColumnWidth = (100 / numThaiColumns).toFixed(2);

        let thaiHeader = `<th style="width: ${thaiColumnWidth}%;">Loại Hàng</th>`;
        let thaiQtyRow = `<td class="font-medium">Số lượng</td>`;
        let thaiPriceRow = `<td class="font-medium">Đơn giá</td>`;
        let thaiAmountRow = `<td class="font-bold">Thành tiền</td>`;
        const quantityValignClassThai = hasCalculationThai ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';
        
        activeThaiColumns.forEach(col => {
            const qty = getValueFromExpression(report[col]);
            const price = getValueFromExpression(report[`Giá_${col}`]);
            const amount = qty * price;
            thaiTotalKg += qty;
            thaiTotalAmount += amount;
            thaiHeader += `<th style="width: ${thaiColumnWidth}%;">${col.replace('_Thái', ' Thái')}</th>`;
            thaiQtyRow += `<td class="${quantityValignClassThai}">${generateQuantityCellHtml(report[col], true, hasCalculationThai)}</td>`;
            thaiPriceRow += `<td>${price > 0 ? price.toLocaleString('vi-VN') : ''}</td>`;
            thaiAmountRow += `<td class="font-semibold">${amount > 0 ? amount.toLocaleString('vi-VN') : ''}</td>`;
        });
        thaiHeader += `<th class="font-bold" style="width: ${thaiColumnWidth}%;">Tổng Cộng (Thái)</th>`;
        thaiQtyRow += `<td class="font-bold">${thaiTotalKg.toLocaleString('vi-VN')}</td>`;
        thaiPriceRow += `<td></td>`;
        thaiAmountRow += `<td class="font-bold">${thaiTotalAmount.toLocaleString('vi-VN')}</td>`;
        
        thaiTableHtml = `
            <div class="mb-4">
                <h3 class="table-heading font-bold text-gray-800 mb-1 pl-2 border-l-4 border-yellow-400" style="padding-bottom: 4mm;">BẢNG KÊ HÀNG MÍT THÁI</h3>
                <div class="invoice-table-wrapper">
                    <table class="invoice-table">
                        <thead><tr class="bg-gray-100">${thaiHeader}</tr></thead>
                        <tbody>
                            <tr class="${hasCalculationThai ? 'has-calculation' : ''}">${thaiQtyRow}</tr>
                            <tr>${thaiPriceRow}</tr>
                            <tr class="bg-gray-50">${thaiAmountRow}</tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
    }

    let indoTableHtml = '';
    if (hasIndoData) {
        const hasCalculationIndo = activeIndoColumns.some(col => report[col] && String(report[col]).includes('+'));
        let indoTotalKg = 0;
        let indoTotalAmount = 0;

        const numIndoColumns = activeIndoColumns.length + 2; 
        const indoColumnWidth = (100 / numIndoColumns).toFixed(2);

        let indoHeader = `<th style="width: ${indoColumnWidth}%;">Loại Hàng</th>`;
        let indoQtyRow = `<td class="font-medium">Số lượng</td>`;
        let indoPriceRow = `<td class="font-medium">Đơn giá</td>`;
        let indoAmountRow = `<td class="font-bold">Thành tiền</td>`;
        const quantityValignClassIndo = hasCalculationIndo ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';

        activeIndoColumns.forEach(col => {
            const qty = getValueFromExpression(report[col]);
            const price = getValueFromExpression(report[`Giá_${col}`]);
            const amount = qty * price;
            indoTotalKg += qty;
            indoTotalAmount += amount;
            indoHeader += `<th style="width: ${indoColumnWidth}%;">${col.replace('_indo', ' Indo')}</th>`;
            indoQtyRow += `<td class="${quantityValignClassIndo}">${generateQuantityCellHtml(report[col], true, hasCalculationIndo)}</td>`;
            indoPriceRow += `<td>${price > 0 ? price.toLocaleString('vi-VN') : ''}</td>`;
            indoAmountRow += `<td class="font-semibold">${amount > 0 ? amount.toLocaleString('vi-VN') : ''}</td>`;
        });
        indoHeader += `<th class="font-bold" style="width: ${indoColumnWidth}%;">Tổng Cộng (Indo)</th>`;
        indoQtyRow += `<td class="font-bold">${indoTotalKg.toLocaleString('vi-VN')}</td>`;
        indoPriceRow += `<td></td>`;
        indoAmountRow += `<td class="font-bold">${indoTotalAmount.toLocaleString('vi-VN')}</td>`;

        indoTableHtml = `
            <div class="mb-4">
                <h3 class="table-heading font-bold text-gray-800 mb-1 pl-2 border-l-4 border-red-400" style="padding-bottom: 4mm;">BẢNG KÊ HÀNG MÍT INDO</h3>
                <div class="invoice-table-wrapper">
                    <table class="invoice-table">
                        <thead><tr class="bg-gray-100">${indoHeader}</tr></thead>
                        <tbody>
                            <tr class="${hasCalculationIndo ? 'has-calculation' : ''}">${indoQtyRow}</tr>
                            <tr>${indoPriceRow}</tr>
                            <tr class="bg-gray-50">${indoAmountRow}</tr>
                        </tbody>
                    </table>
                </div>
            </div>`;
    }

    const portraitFooterHtml = `
        <footer class="invoice-footer pt-4">
            <div class="flex justify-end">
                <div class="w-full grand-total">
                     <div class="flex justify-between items-center py-2 border-t-2 border-b-2 border-gray-800" style="padding-bottom: 5mm;">
                        <span class="font-bold text-gray-800 whitespace-nowrap">TỔNG CỘNG HÓA ĐƠN:</span>
                        <span class="total-amount font-bold text-indigo-600 whitespace-nowrap text-right">${(calculateTotalAmount(report)).toLocaleString('vi-VN')} VND</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end">
                 <div class="signature text-center">
                    <p class="font-semibold text-gray-800 mb-12">Người lập hóa đơn</p>
                </div>
            </div>
        </footer>`;

    const allPagesHtml = `
        <div class="a4-sheet-portrait shrunk-invoice">
            ${portraitHeaderHtml}
            <main class="invoice-content">
                ${customerInfoHtml}
                ${thaiTableHtml}
                ${indoTableHtml}
            </main>
            ${portraitFooterHtml}
        </div>`;
    
    invoiceWrapper.innerHTML = '<div class="text-center p-8"><p class="font-semibold text-gray-700">Đang tạo hình ảnh hóa đơn...</p></div>';
    document.getElementById('invoiceModalTitle').textContent = `Hóa Đơn`;
    document.getElementById('invoiceModal').style.display = 'block';

    const tempContainer = document.createElement('div');
    tempContainer.innerHTML = allPagesHtml;
    const invoiceSheet = tempContainer.querySelector('.a4-sheet-portrait');

    if (!invoiceSheet) {
        invoiceWrapper.innerHTML = '<p class="text-center p-8 text-red-600">Lỗi: Không thể tạo nội dung hóa đơn.</p>';
        return;
    }

    document.body.appendChild(invoiceSheet);
    invoiceSheet.style.position = 'absolute';
    invoiceSheet.style.left = '-9999px';

    await new Promise(resolve => setTimeout(resolve, 50));

    try {
        const canvas = await html2canvas(invoiceSheet, { scale: 4, useCORS: true });
        const imageUrl = canvas.toDataURL('image/jpeg', 0.95);
        
        const isMobile = window.innerWidth <= 768;
        const instructionText = isMobile
            ? 'Ấn giữ vào hóa đơn để lưu hoặc chia sẻ.'
            : 'Chuột phải vào hóa đơn để sao chép (Copy Image) và dán vào nơi cần gửi.';

        invoiceWrapper.innerHTML = `
            <div class="text-center p-2">
                <img src="${imageUrl}" alt="Hóa đơn" class="w-full h-auto border rounded-lg shadow-md" style="max-width: 800px; margin: 0 auto;">
                <p class="mt-4 text-gray-600 font-semibold">${instructionText}</p>
            </div>
        `;

    } catch (error) {
        console.error("Lỗi khi tạo ảnh hóa đơn:", error);
        invoiceWrapper.innerHTML = '<div class="text-center p-8"><p class="text-red-600">Không thể tạo ảnh hóa đơn.</p></div>';
    } finally {
        document.body.removeChild(invoiceSheet);
    }
}