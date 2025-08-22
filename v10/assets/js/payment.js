// Chứa logic thanh toán, QR code
import { driverList, dailyReports, currentlyPayingReportIndex } from './state.js';
import { setDriverList, setCurrentlyPayingReportIndex } from './state.js';
import { customAlert, closeModal, getBankBin, getBankShortName, normalizeName } from './utils.js';
import { saveDriverListToCSV, saveDailyReportsToCSV } from './data.js';
import { renderDriverList } from './drivers.js';
import { calculateTotalAmount, filterReportsByDate } from './reports.js';

export async function showVerificationQrModal(index) {
    const driver = driverList[index];
    if (!driver) return;

    const bankBin = getBankBin(driver.bankName);
    if (!bankBin) {
        return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${driver.bankName}.`);
    }

    const amount = 1;
    const description = `KTK ${normalizeName(driver.accountName).replace(/\s/g, '')}`.substring(0, 25);
    
    const qrContainer = document.getElementById('qrCodeContainer');
    qrContainer.innerHTML = '<p class="text-gray-600">Đang tạo mã QR...</p>';
    document.getElementById('qrModal').style.display = 'block';

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'generate_qr',
                bin: bankBin,
                accountNumber: driver.accountNumber,
                accountName: driver.accountName,
                amount: amount,
                description: description
            })
        });
        const result = await response.json();
        if (result.code === '00' && result.data.qrDataURL) {
            qrContainer.innerHTML = `<img src="${result.data.qrDataURL}" alt="VietQR Code" style="width: 256px; height: 256px; margin: 0 auto;">`;
        } else {
            qrContainer.innerHTML = `<p class="text-red-600 font-semibold">Lỗi tạo mã QR: ${result.desc}</p>`;
        }
    } catch (error) {
        console.error("Lỗi khi tạo mã QR:", error);
        qrContainer.innerHTML = `<p class="text-red-600 font-semibold">Không thể kết nối để tạo mã QR.</p>`;
    }

    document.getElementById('qrModalTitle').textContent = "Kiểm tra tài khoản";
    const infoDiv = document.getElementById('qrModalInfo');
    
    const isMobile = window.innerWidth <= 768;
    const instructionText = isMobile 
        ? "Chụp màn hình này, dùng app ngân hàng quét hình ảnh vừa chụp, nếu đúng tên chủ tài khoản là đúng."
        : "Dùng app ngân hàng quét QR, nếu đúng tên chủ tài khoản là đúng.";

    infoDiv.innerHTML = `<div class="text-left mb-4 p-3 bg-gray-50 rounded-lg border">
                            <p><strong>Lái:</strong> ${driver.driverNamePhone}</p>
                            <p><strong>Ngân hàng:</strong> ${getBankShortName(driver.bankName)}</p>
                            <p><strong>Số tài khoản:</strong> ${driver.accountNumber}</p>
                            <p><strong>Chủ tài khoản:</strong> ${driver.accountName}</p>
                         </div>
                         <div class="qr-instruction">${instructionText}</div>`;
    
    const actionsDiv = document.getElementById('qrModalActions');
    const buttonText = driver.isVerified ? "Bỏ đánh dấu chính xác" : "Đánh dấu chính xác";
    const buttonClass = driver.isVerified ? "bg-yellow-500 hover:bg-yellow-600" : "bg-green-600 hover:bg-green-700";
    actionsDiv.innerHTML = `
        <button class="${buttonClass} text-white font-bold py-2 px-4 rounded-lg" onclick="window.toggleVerificationStatus(${index})">${buttonText}</button>
        <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('qrModal')">Đóng</button>
    `;
}

export async function toggleVerificationStatus(index) {
    const driver = driverList[index];
    if (!driver) return;
    
    driver.isVerified = !driver.isVerified;
    setDriverList(driverList);
    
    await saveDriverListToCSV();
    
    renderDriverList();
    
    closeModal('qrModal');
    
    customAlert(driver.isVerified ? 'Đã đánh dấu tài khoản chính xác.' : 'Đã bỏ đánh dấu tài khoản.');
}


export async function showPaymentModal(reportIndex) {
    setCurrentlyPayingReportIndex(reportIndex);
    filterReportsByDate();

    const report = dailyReports[reportIndex];
    if (!report) return;
    
    if (report.isForSale) {
        customAlert("Đây là toa đi bán, không thể thanh toán.");
        return;
    }

    const driver = driverList.find(d => d.driverNamePhone === report.driver);
    if (!driver) {
        return customAlert(`Lỗi: Không tìm thấy thông tin ngân hàng cho Lái "${report.driver}". Vui lòng kiểm tra lại thẻ Thông Tin Lái.`);
    }
    if (!driver.isVerified) {
        return customAlert(`Lỗi: Tài khoản ngân hàng của Lái "${report.driver}" chưa được đánh dấu chính xác.`);
    }
    
    const amount = calculateTotalAmount(report);
    if (amount <= 0) {
        return customAlert('Số tiền thanh toán phải lớn hơn 0.');
    }
    
    const driverInfo = report.driver.toUpperCase().replace(/\s+/g, '');
    const dateParts = report.date.split('/');
    const dayMonth = dateParts[0] + dateParts[1];
    const toaInfo = 'Toa' + report.toa;
    const displayText = `HD68 ck ${driverInfo} ${dayMonth} ${toaInfo}`;
    const descriptionForApi = removeAccents(displayText);

    const bankBin = getBankBin(driver.bankName);
    
    const qrContainer = document.getElementById('qrCodeContainer');
    qrContainer.innerHTML = '<p class="text-gray-600">Đang tạo mã QR thanh toán...</p>';
    document.getElementById('qrModal').style.display = 'block';

    try {
        const response = await fetch('index.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'generate_qr',
                bin: bankBin,
                accountNumber: driver.accountNumber,
                accountName: driver.accountName,
                amount: amount,
                description: descriptionForApi
            })
        });
        const result = await response.json();
        if (result.code === '00' && result.data.qrDataURL) {
            qrContainer.innerHTML = `<img src="${result.data.qrDataURL}" alt="VietQR Code" style="width: 256px; height: 256px; margin: 0 auto;">`;
        } else {
            customAlert('Lỗi tạo mã QR: ' + result.desc);
            qrContainer.innerHTML = `<p class="text-red-600 font-semibold">Lỗi tạo mã QR: ${result.desc}</p>`;
        }
    } catch (error) {
        customAlert('Không thể kết nối để tạo mã QR.');
        qrContainer.innerHTML = `<p class="text-red-600 font-semibold">Không thể kết nối để tạo mã QR.</p>`;
    }

    document.getElementById('qrModalTitle').textContent = "Thanh toán tiền hàng";
    const infoDiv = document.getElementById('qrModalInfo');
    
    const reportInfoHtml = `
        <div class="text-left mb-4 p-3 bg-gray-50 rounded-lg border">
            <p><strong>Toa ${report.toa} - ${report.driver}</strong></p>
            <p><strong>Tổng KG:</strong> ${(calculateTotalKG(report) || 0).toLocaleString('vi-VN')} - <strong>Thành Tiền:</strong> ${amount.toLocaleString('vi-VN')}đ</p>
            ${report.Ghi_Chú ? `<p><strong>Ghi Chú:</strong> ${report.Ghi_Chú}</p>` : ''}
        </div>
    `;

    infoDiv.innerHTML = reportInfoHtml + `<p><strong>Ngân hàng:</strong> ${getBankShortName(driver.bankName)}</p><p><strong>Số tài khoản:</strong> ${driver.accountNumber}</p><p><strong>Chủ tài khoản:</strong> ${driver.accountName}</p><p><strong>Nội dung:</strong> ${displayText}</p>`;
    
    const actionsDiv = document.getElementById('qrModalActions');
    const toggleButtonText = report.isPaid ? "Bỏ đánh dấu đã chuyển khoản" : "Đánh dấu đã chuyển khoản";
    const toggleButtonClass = report.isPaid ? "bg-yellow-500 hover:bg-yellow-600" : "bg-green-600 hover:bg-green-700";

    actionsDiv.innerHTML = `
        <button class="${toggleButtonClass} text-white font-bold py-2 px-4 rounded-lg" onclick="window.togglePaymentStatus(${reportIndex})">${toggleButtonText}</button>
    `;
}

export async function togglePaymentStatus(reportIndex) {
    const report = dailyReports[reportIndex];
    if (!report) return;
    
    report.isPaid = !report.isPaid;
    await saveDailyReportsToCSV();

    if (report.isPaid) {
        const reportsForDay = dailyReports
            .filter(r => r.date === report.date)
            .sort((a, b) => (a.toa || 0) - (b.toa || 0));
        
        const currentIndexInDay = reportsForDay.findIndex(r => r === report);

        let nextUnpaidReport = null;
        if (currentIndexInDay !== -1) {
            for (let i = currentIndexInDay + 1; i < reportsForDay.length; i++) {
                if (!reportsForDay[i].isPaid && !reportsForDay[i].isForSale && !reportsForDay[i].noPayment) {
                    nextUnpaidReport = reportsForDay[i];
                    break;
                }
            }
        }

        if (nextUnpaidReport) {
            const nextUnpaidIndex = dailyReports.findIndex(r => r === nextUnpaidReport);
            if (nextUnpaidIndex !== -1) {
                showPaymentModal(nextUnpaidIndex);
            }
        } else {
            customAlert("Đã hoàn tất thanh toán cho các toa trong ngày.");
            closeModal('qrModal'); 
        }
    } else {
        closeModal('qrModal'); 
    }
}