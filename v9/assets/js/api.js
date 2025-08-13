// assets/js/api.js
const baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);

async function fetchData(url, data) {
    try {
        const response = await fetch(baseUrl + url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (!response.ok) {
            console.error("Server responded with an error:", response.status, await response.text());
            throw new Error(`Lỗi máy chủ khi gọi ${url}.`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Lỗi fetch cho ${url}:`, error);
        // Trả về một đối tượng lỗi chuẩn hóa
        return { success: false, message: error.message };
    }
}

const api = {
    // Driver related APIs
    loadDrivers: (dataDir) => fetchData('load_drivers.php', { dataDir }),
    saveDrivers: (drivers, dataDir) => fetchData('save_drivers.php', { drivers, dataDir }),
    checkDriverUsage: (driverNamePhone, dataDir) => fetchData('check_driver_usage.php', { driverNamePhone, dataDir }),
    verifyBankAccount: (bin, accountNumber) => fetchData('verify_bank.php', { bin, accountNumber }),

    // Report related APIs
    loadReports: (dataDir) => fetchData('load_reports.php', { dataDir }),
    saveReports: (reports, dataDir) => fetchData('save_reports.php', { reports, dataDir }),
    
    // User/Admin related APIs
    loadUsers: () => fetchData('manage_users.php', { action: 'load' }),
    saveUser: (action, user, original_username) => fetchData('manage_users.php', { action, user, original_username }),
    deleteUser: (username) => fetchData('manage_users.php', { action: 'delete', username }),
    loadStats: (date) => fetchData('get_stats.php', { date }),
};