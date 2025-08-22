// --- BIẾN TOÀN CỤC VÀ DỮ LIỆU ---
export let userType = '';
export let activeDataDir = '';
export let verificationMode = 'manual'; // 'manual' hoặc 'auto'
export let currentlyPayingReportIndex = null;

export const baseUrl = '';
export let driverList = [];
export let editingIndexTab1 = -1;
export let dailyReports = [];
export let liveEditingReport = null;
export let isSaving = false;
export let saveTimeout;

export const DRIVERS_PER_PAGE = 10;
export let driversCurrentlyShown = DRIVERS_PER_PAGE;

export const banksData = [
{"name":"Ngân hàng thương mại cổ phần An Bình","shortName":"ABBank","code":"970425"},
{"name":"Ngân hàng thương mại cổ phần Á Châu","shortName":"ACB","code":"970416"},
{"name":"Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam","shortName":"Agribank","code":"970405"},
{"name":"Ngân hàng TNHH một thành viên ANZ (Việt Nam)","shortName":"ANZ","code":"970455"},
{"name":"Ngân hàng thương mại cổ phần Bắc Á","shortName":"Bac A Bank","code":"970409"},
{"name":"Ngân hàng thương mại cổ phần Bảo Việt","shortName":"BaoViet Bank","code":"970438"},
{"name":"Ngân hàng thương mại cổ phần Đầu tư và Phát triển Việt Nam","shortName":"BIDV","code":"970418"},
{"name":"Ngân hàng Nonghyup - chi nhánh Hà Nội","shortName":"Nonghyup","code":"801011"},
{"name":"Ngân hàng số Cake by VPBank","shortName":"CAKE","code":"546034"},
{"name":"Ngân hàng TNHH một thành viên CIMB Việt Nam","shortName":"CIMB","code":"422589"},
{"name":"Ngân hàng Hợp tác xã Việt Nam","shortName":"Co-op Bank","code":"970446"},
{"name":"Ngân hàng thương mại cổ phần Đông Á","shortName":"DongA Bank","code":"970406"},
{"name":"Ngân hàng thương mại cổ phần Xuất Nhập khẩu Việt Nam","shortName":"Eximbank","code":"970431"},
{"name":"Ngân hàng thương mại cổ phần Dầu khí toàn cầu","shortName":"GPBank","code":"970408"},
{"name":"Ngân hàng thương mại cổ phần Phát triển Thành phố Hồ Chí Minh","shortName":"HDBank","code":"970437"},
{"name":"Ngân hàng TNHH một thành viên Hong Leong Việt Nam","shortName":"Hong Leong Bank","code":"970442"},
{"name":"Ngân hàng TNHH Indovina","shortName":"Indovina Bank","code":"970434"},
{"name":"Ngân hàng Kookmin - Chi nhánh Hà Nội","shortName":"Kookmin HN","code":"970462"},
{"name":"Ngân hàng Kookmin - Chi nhánh TP. Hồ Chí Minh","shortName":"Kookmin HCM","code":"970463"},
{"name":"Ngân hàng thương mại cổ phần Kiên Long","shortName":"KienLong Bank","code":"970453"},
{"name":"Ngân hàng số Liobank by OCB","shortName":"Liobank","code":"970448"},
{"name":"Ngân hàng thương mại cổ phần Bưu điện Liên Việt","shortName":"LPBank","code":"970449"},
{"name":"Ngân hàng thương mại cổ phần Quân đội","shortName":"MB Bank","code":"970422"},
{"name":"Ngân hàng thương mại cổ phần Hàng hải Việt Nam","shortName":"MSB","code":"970426"},
{"name":"Ngân hàng thương mại cổ phần Nam Á","shortName":"Nam A Bank","code":"970428"},
{"name":"Ngân hàng thương mại cổ phần Quốc dân","shortName":"NCB","code":"970419"},
{"name":"Ngân hàng số Ubank by VPBank","shortName":"Ubank","code":"546035"},
{"name":"Ngân hàng thương mại cổ phần Phương Đông","shortName":"OCB","code":"970448"},
{"name":"Ngân hàng TNHH một thành viên Public Việt Nam","shortName":"Public Bank","code":"970439"},
{"name":"Ngân hàng thương mại cổ phần Xăng dầu Petrolimex","shortName":"PG Bank","code":"970430"},
{"name":"Ngân hàng thương mại cổ phần Đại chúng Việt Nam","shortName":"PVcomBank","code":"970412"},
{"name":"Ngân hàng thương mại cổ phần Sài Gòn Thương Tín","shortName":"Sacombank","code":"970403"},
{"name":"Ngân hàng thương mại cổ phần Sài Gòn Công Thương","shortName":"Saigonbank","code":"970400"},
{"name":"Ngân hàng thương mại cổ phần Sài Gòn","shortName":"SCB","code":"970429"},
{"name":"Ngân hàng thương mại cổ phần Đông Nam Á","shortName":"SeABank","code":"970440"},
{"name":"Ngân hàng thương mại cổ phần Sài Gòn - Hà Nội","shortName":"SHB","code":"970443"},
{"name":"Ngân hàng TNHH một thành viên Shinhan Việt Nam","shortName":"Shinhan Bank","code":"970424"},
{"name":"Ngân hàng thương mại cổ phần Kỹ Thương Việt Nam","shortName":"Techcombank","code":"970407"},
{"name":"Ngân hàng số Timo","shortName":"Timo","code":"963388"},
{"name":"Ngân hàng thương mại cổ phần Tiên Phong","shortName":"TPBank","code":"970423"},
{"name":"Ngân hàng TNHH một thành viên United Overseas Bank (Việt Nam)","shortName":"UOB","code":"970458"},
{"name":"Ngân hàng thương mại cổ phần Quốc tế Việt Nam","shortName":"VIB","code":"970441"},
{"name":"Ngân hàng thương mại cổ phần Việt Á","shortName":"VietABank","code":"970427"},
{"name":"Ngân hàng thương mại cổ phần Việt Nam Thương Tín","shortName":"VietBank","code":"970433"},
{"name":"Ngân hàng thương mại cổ phần Ngoại thương Việt Nam","shortName":"Vietcombank","code":"970436"},
{"name":"Ngân hàng thương mại cổ phần Công Thương Việt Nam","shortName":"VietinBank","code":"970415"},
{"name":"Ngân hàng thương mại cổ phần Việt Nam Thịnh Vượng","shortName":"VPBank","code":"970432"},
{"name":"Ngân hàng Liên doanh Việt - Nga","shortName":"VRB","code":"970421"},
{"name":"Ngân hàng TNHH một thành viên Woori Việt Nam","shortName":"Woori Bank","code":"970457"},
{"name":"Kho bạc Nhà nước","shortName":"KBNN","code":"799999"},
{"name":"Ngân hàng Chính sách xã hội","shortName":"VBSP","code":"999888"},
{"name":"Ngân hàng Phát triển Việt Nam","shortName":"VDB","code":"999666"},
{"name":"Ngân hàng TNHH một thành viên Standard Chartered (Việt Nam)","shortName":"Standard Chartered","code":"970410"},
{"name":"Ngân hàng TNHH một thành viên HSBC (Việt Nam)","shortName":"HSBC","code":"458761"},
{"name":"Ngân hàng thương mại cổ phần các doanh nghiệp ngoài quốc doanh","shortName":"VPB","code":"970414"},
{"name":"Ngân hàng thương mại cổ phần Đại Tín","shortName":"TRUSTBank","code":"970451"},
{"name":"Ngân hàng Xây dựng","shortName":"CBBank","code":"970444"},
{"name":"Ngân hàng TNHH một thành viên Đại Dương","shortName":"OceanBank","code":"970414"},
{"name":"Ngân hàng TNHH một thành viên Dầu khí Toàn cầu","shortName":"GPBank","code":"970408"},
{"name":"Ngân hàng First Bank","shortName":"First Bank","code":"970456"},
{"name":"Ngân hàng TNHH MTV Shinhan Bank","shortName":"SVB","code":"970424"},
{"name":"Industrial Bank of Korea - Ha Noi","shortName":"IBK HN","code":"970456"},
{"name":"Industrial Bank of Korea - Ho Chi Minh","shortName":"IBK HCM","code":"970455"},
{"name":"Ngân hàng Citibank N.A - Chi nhánh Hà Nội","shortName":"Citibank","code":"533948"},
{"name":"Ngân hàng Sumitomo Mitsui Banking Corporation - Chi nhánh Hà Nội","shortName":"SMBC","code":"432618"},
{"name":"Ngân hàng The Bank of Tokyo-Mitsubishi UFJ, Ltd. - Chi nhánh Hà Nội","shortName":"BTMU","code":"653118"},
{"name":"Ngân hàng Mizuho - chi nhánh Hà Nội","shortName":"Mizuho","code":"636818"},
{"name":"Ngân hàng Deutsche Bank AG - Chi nhánh TP. Hồ Chí Minh","shortName":"Deutsche Bank","code":"796500"},
{"name":"Ngân hàng Maybank - Chi nhánh Hà Nội","shortName":"Maybank","code":"435018"},
{"name":"Ngân hàng Commonwealth Bank of Australia - Chi nhánh TP. Hồ Chí Minh","shortName":"CBA","code":"652818"},
{"name":"Ngân hàng The Bangkok Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"Bangkok Bank","code":"653018"},
{"name":"Ngân hàng The Shanghai Commercial & Savings Bank, Ltd. - Chi nhánh Đồng Nai","shortName":"SCSB","code":"532858"},
{"name":"Ngân hàng Malayan Banking Berhad","shortName":"Maybank","code":"435018"},
{"name":"Ngân hàng Cathay United Bank","shortName":"Cathay United Bank","code":"970454"},
{"name":"Ngân hàng China Construction Bank Corporation - Chi nhánh TP. Hồ Chí Minh","shortName":"CCBC","code":"653418"},
{"name":"Ngân hàng JPMorgan Chase Bank, N.A. - Chi nhánh TP. Hồ Chí Minh","shortName":"JPM","code":"653318"},
{"name":"Ngân hàng First Commercial Bank - Chi nhánh Hà Nội","shortName":"FCNB HN","code":"653518"},
{"name":"Ngân hàng First Commercial Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"FCNB HCM","code":"653518"},
{"name":"Ngân hàng The Hongkong and Shanghai Banking Corporation Limited","shortName":"HSBC","code":"458761"},
{"name":"Ngân hàng BPCE IOM - Chi nhánh TP. Hồ Chí Minh","shortName":"BPCE IOM","code":"653618"},
{"name":"Ngân hàng Bank of China - Chi nhánh TP. Hồ Chí Minh","shortName":"BOC","code":"653218"},
{"name":"Ngân hàng Taipei Fubon Commercial Bank Co., Ltd. – Chi nhánh Hà Nội","shortName":"Fubon HN","code":"653818"},
{"name":"Ngân hàng Taipei Fubon Commercial Bank Co., Ltd. – Chi nhánh TP. Hồ Chí Minh","shortName":"Fubon HCM","code":"653818"},
{"name":"Ngân hàng Hua Nan Commercial Bank, Ltd. - Chi nhánh TP. Hồ Chí Minh","shortName":"HNCB","code":"653918"},
{"name":"Ngân hàng Bank of Communications - Chi nhánh TP. Hồ Chí Minh","shortName":"BC","code":"654018"},
{"name":"Ngân hàng CTBC Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"CTBC","code":"970454"},
{"name":"Ngân hàng E.SUN Commercial Bank, Ltd., Dong Nai Branch","shortName":"ESUN","code":"654118"},
{"name":"Ngân hàng Mega International Commercial Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"MICB","code":"654218"},
{"name":"Ngân hàng SinoPac Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"SPB","code":"654318"},
{"name":"Ngân hàng Taishin International Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"TIB","code":"654418"},
{"name":"Ngân hàng Land Bank of Taiwan - Chi nhánh TP. Hồ Chí Minh","shortName":"LBT","code":"654518"},
{"name":"Ngân hàng Taiwan Shin Kong Commercial Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"TSKB","code":"654618"},
{"name":"Ngân hàng Chang Hwa Commercial Bank, Ltd. - Chi nhánh TP. Hồ Chí Minh","shortName":"CHCB","code":"654718"},
{"name":"Ngân hàng DBS Bank Ltd - Chi nhánh TP. Hồ Chí Minh","shortName":"DBS","code":"796501"},
{"name":"Ngân hàng The Export-Import Bank of Korea - Chi nhánh Hà Nội","shortName":"KEBHana HN","code":"970461"},
{"name":"Ngân hàng The Export-Import Bank of Korea - Chi nhánh TP. Hồ Chí Minh","shortName":"KEBHana HCM","code":"970461"},
{"name":"Ngân hàng KEB Hana - Chi nhánh Hà Nội","shortName":"KEBHana HN","code":"970461"},
{"name":"Ngân hàng KEB Hana - Chi nhánh TP. Hồ Chí Minh","shortName":"KEBHana HCM","code":"970461"},
{"name":"Ngân hàng TNHH Một thành viên Bank of China (Hong Kong) - Chi nhánh TP. Hồ Chí Minh","shortName":"BOC HCM","code":"653218"},
{"name":"Ngân hàng The Development Bank of Singapore Limited","shortName":"DBS","code":"796501"}
];


export const quantityColumns = ["B1_Thái","B2_Thái","C1_Thái","C2_Thái","C3_Thái","D1_Thái","D2_Thái","E_Thái","Chợ_Thái","Xơ_Thái","A1_indo","A2_indo","B1_indo","B2_indo","B3_indo","C1_indo","C2_indo","Chợ_1_indo","Chợ_2_indo","Xơ_indo"];
export const priceColumns = quantityColumns.map(col => `Giá_${col}`);
export const allCalcColumns = [...quantityColumns, ...priceColumns];

// Functions to update state
export function setUserType(type) { userType = type; }
export function setActiveDataDir(dir) { activeDataDir = dir; }
export function setVerificationMode(mode) { verificationMode = mode; }
export function setCurrentlyPayingReportIndex(index) { currentlyPayingReportIndex = index; }
export function setDriverList(list) { driverList = list; }
export function setEditingIndexTab1(index) { editingIndexTab1 = index; }
export function setDailyReports(reports) { dailyReports = reports; }
export function setLiveEditingReport(report) { liveEditingReport = report; }
export function setIsSaving(status) { isSaving = status; }
export function setSaveTimeout(timeout) { saveTimeout = timeout; }
export function setDriversCurrentlyShown(count) { driversCurrentlyShown = count; }