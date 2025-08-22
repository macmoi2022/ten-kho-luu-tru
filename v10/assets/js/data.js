import { activeDataDir, baseUrl, driverList, dailyReports } from './state.js';
import { customAlert } from './utils.js';

export async function loadDriverList() {
    try {
        const response = await fetch(baseUrl + 'load_drivers.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ dataDir: activeDataDir })
        });
        if (!response.ok) throw new Error('Network response was not ok.');
        const result = await response.json();
        if (result && result.success) {
            return result.data.map(d => ({...d, isVerified: d.isVerified === 'true'})) || [];
        } else {
            console.error("Lỗi khi tải danh sách lái:", result.message);
            return [];
        }
    } catch (error) {
        console.error("Lỗi khi fetch danh sách lái từ máy chủ:", error);
        return [];
    }
}

export async function saveDriverListToCSV() {
    try {
        const response = await fetch(baseUrl + "save_drivers.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ drivers: driverList, dataDir: activeDataDir })
        });
        const result = await response.json();
        if (!result.success) customAlert(`Lỗi khi lưu danh sách lái: ${result.message}`);
    } catch (error) {
        console.error("Lỗi khi gửi dữ liệu lái đến máy chủ:", error);
        customAlert("Không thể kết nối đến máy chủ để lưu danh sách lái.");
    }
}

export async function loadDailyReports() {
    try {
        const response = await fetch(baseUrl + 'load_reports.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ dataDir: activeDataDir })
        });
        if (!response.ok) throw new Error('Network response was not ok for reports.');
        const result = await response.json();
        if (result && result.success) {
            return result.data.map(r => ({ ...r, isPaid: r.isPaid === 'true' })) || [];
        } else {
            console.error("Lỗi khi tải báo cáo:", result.message);
            return [];
        }
    } catch (error) {
        console.error("Lỗi khi fetch báo cáo từ máy chủ:", error);
        return [];
    }
}

export async function saveDailyReportsToCSV() {
    try {
        const reportsToSave = dailyReports.map(report => {
            const reportCopy = { ...report };
            reportCopy.isPaid = report.isPaid ? 'true' : 'false';
            delete reportCopy['Tổng_KG'];
            delete reportCopy['Thành_Tiền'];
            return reportCopy;
        });

        const response = await fetch(baseUrl + "save_reports.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ reports: reportsToSave, dataDir: activeDataDir })
        });

        if (!response.ok) {
            console.error("Server responded with an error:", response.status, await response.text());
            return false;
        }
        const result = await response.json();
        return result.success;
    } catch (error) {
        console.error("Lỗi khi gửi dữ liệu đến máy chủ:", error);
        return false;
    }
}

export async function checkDriverUsage(driverNamePhone) {
    try {
        const response = await fetch(baseUrl + "check_driver_usage.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ driverNamePhone, dataDir: activeDataDir })
        });
        if (!response.ok) throw new Error('Lỗi máy chủ khi kiểm tra.');
        return await response.json();
    } catch (error) {
        console.error("Lỗi khi kiểm tra việc sử dụng của lái:", error);
        customAlert("Lỗi: " + error.message);
        return { success: false, message: error.message };
    }
}