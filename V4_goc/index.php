<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vựa Mít Ngọc Anh HD68</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{font-family:'Inter',sans-serif;background-color:#f3f4f6;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;padding:2rem 1rem}.tab-content{display:none}.tab-content.active{display:block}.tab-button.active{background-color:#4f46e5;color:#fff}table{width:100%;border-collapse:collapse;margin-top:1.5rem}th,td{border:1px solid #e5e7eb;text-align:center;vertical-align:middle}th{background-color:#f9fafb;color:#4b5563}tr:nth-child(even){background-color:#f3f4f6}.input-group{display:flex;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem}.input-group div{flex:1 1 calc(50% - .5rem)}@media (min-width:768px){.input-group div{flex:1 1 calc(33.33% - .66rem)}}#dailyReportForm label, #tab1 label{display:block;font-weight:500;color:#374151;text-align:left}#dailyReportForm input[type=text], #dailyReportForm select, #tab1 input, #tab1 select {width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:.5rem;font-size:1rem;color:#1f2937;background-color:#fff;transition:border-color .2s ease-in-out,box-shadow .2s ease-in-out}#dailyReportForm input[type=text]:focus, #dailyReportForm select:focus, #tab1 input:focus, #tab1 select:focus{border-color:#6366f1;outline:0;box-shadow:0 0 0 3px rgba(99,102,241,.2)}#dailyReportForm input:disabled, #dailyReportForm select:disabled, #tab1 input:disabled, #tab1 select:disabled{background-color:#e5e7eb;cursor:not-allowed;}.thai-cell{background-color:#ffd966!important}.indo-cell{background-color:#dd7e6b!important}.td-thai{background-color:#ffd966}.td-indo{background-color:#dd7e6b}tr:nth-child(even) .td-thai{background-color:#ffd966}tr:nth-child(even) .td-indo{background-color:#dd7e6b}
        input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}input[type=number]{-moz-appearance:textfield}
        .flatpickr-input{text-align:center;padding:.35rem .35rem!important;border:2px solid #d1d5db!important;border-radius:.5rem!important;transition:all .2s ease-in-out!important;background-color:#f9fafb!important;max-width:135px!important;}.flatpickr-input:focus{border-color:#4f46e5!important;box-shadow:0 0 0 3px rgba(79,70,229,.2)!important;background-color:#fff!important}
        .verified-check{color:#16a34a;display:inline-block;margin-left:8px;font-weight:bold;text-shadow:-1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;}
        .qr-modal, .invoice-modal, .driver-usage-modal, .summary-modal {display:none;position:fixed;z-index:100;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.6);}
        .modal-content{background-color:#fff;margin:5% auto;padding:24px;border:1px solid #888;width:90%;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.3);}
        .qr-modal .modal-content{max-width:400px;text-align:center;}
        .invoice-modal .modal-content{max-width:95%; width: auto; text-align:left; position: relative;}
        .driver-usage-modal .modal-content {max-width: 700px;}
        .summary-modal .modal-content {max-width: 800px;}
        .close-modal{color:#aaa;float:right;font-size:28px;font-weight:bold;cursor:pointer;}.close-modal:hover,.close-modal:focus{color:#000;text-decoration:none;}
        .highlight-effect { animation: highlight-fade 2s ease-out; }
        @keyframes highlight-fade {
            from { background-color: #fef08a; }
            to { background-color: transparent; }
        }
        .input-wrapper { position: relative; }
        .calculation-result {
            position: absolute;
            bottom: -22px;
            right: 0;
            background-color: #4f46e5;
            color: white;
            font-size: 0.875rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            z-index: 2;
            pointer-events: none;
            animation: pop-in 0.3s ease-out;
        }
        @keyframes pop-in {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        #dailyReportTableContainer { max-height: 70vh; overflow: auto; }
        #tab2 thead th { position: -webkit-sticky; position: sticky; top: 0; z-index: 20; }
        #tab2 td, #tab2 th { white-space: nowrap; }
        #driverListTableContainer th, #driverListTableContainer td { white-space: nowrap; }
        
        /* Mobile Accordion Report Styles */
        .mobile-report-item { border: 1px solid #e5e7eb; border-radius: 0.5rem; margin-bottom: 0.5rem; background-color: #fff; }
        .mobile-report-main { display: flex; align-items: center; padding: 0.75rem; cursor: pointer; }
        .mobile-report-main .toa { font-weight: 600; color: #4f46e5; width: 40px; text-align: center; flex-shrink: 0; }
        .mobile-report-main .lai { font-weight: 500; color: #1f2937; flex-grow: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .mobile-report-details { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; border-top: 1px solid #f3f4f6;}
        .mobile-report-details.expanded { max-height: 500px; }
        .mobile-report-actions { display: flex; justify-content: space-around; padding: 0.5rem; background-color: #f9fafb;}
        .mobile-report-actions button { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .mobile-report-ghi-chu { padding: 0.75rem; font-size: 0.875rem; color: #4b5563; word-wrap: break-word; white-space: normal; border-top: 1px solid #f3f4f6;}
        .mobile-report-ghi-chu strong { color: #1f2937; }
        .mobile-report-toggle-icon { margin-left: auto; padding-left: 10px; font-size: 1.1rem; color: #6b7280; transition: transform 0.3s ease-in-out; }
        
        /* Mobile Driver List Styles */
        .mobile-driver-item { border: 1px solid #e5e7eb; border-radius: 0.5rem; margin-bottom: 0.5rem; background-color: #fff; }
        .mobile-driver-main { display: flex; align-items: center; padding: 0.75rem; cursor: pointer; }
        .mobile-driver-main .stt { font-weight: 600; color: #4f46e5; width: 40px; text-align: center; flex-shrink: 0; }
        .mobile-driver-main .name { font-weight: 500; color: #1f2937; flex-grow: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .mobile-driver-main .toggle-icon { margin-left: auto; padding-left: 10px; font-size: 1.1rem; color: #6b7280; transition: transform 0.3s ease-in-out; }
        .mobile-driver-details { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; border-top: 1px solid #f3f4f6; }
        .mobile-driver-details.expanded { max-height: 500px; }
        .mobile-driver-info { padding: 0.75rem; font-size: 0.875rem; }
        .mobile-driver-info p { margin-bottom: 0.5rem; color: #4b5563; }
        .mobile-driver-info strong { color: #1f2937; min-width: 100px; display: inline-block;}
        .mobile-driver-actions { display: flex; justify-content: space-around; padding: 0.75rem; background-color: #f9fafb; border-top: 1px solid #e2e8f0; }
        .mobile-driver-actions button { font-size: 0.875rem; padding: 0.3rem 0.8rem; }	
        #desktopDriverSearch { width: 30%; }
        @media (max-width: 768px) {
            body {padding: 0rem}
			.modal-actions {display:none!important;}
            .p-6 {padding: .1rem !important;}
			.border {border-width: 1px !important;}
            .mb-6 {margin-bottom: 1rem;}
            .mt-8 {margin: 1.5rem 0rem;}
            h1 {padding-top: 1rem}
            .mobile-hidden { display: none !important; }
            #dailyReportForm { padding-bottom: 70px !important; }
            .sticky-tabs {
                position: -webkit-sticky;
                position: sticky;
                top: 0;
                z-index: 50;
                background-color: white;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
        }
		@media (min-width: 640px) {	
				.sm\:w-1\/2 {width: 35% !important;}
				.pt-3 {padding-top: 1.75rem !important;}
		}
	
        /* Styles for Driver Usage Modal Conflict List */
        .report-card {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .report-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            cursor: pointer;
            background-color: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .report-card-header:hover {
            background-color: #f1f5f9;
        }
        .report-card-header h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
        }
        .report-card-header .toggle-arrow {
            font-size: 1.1rem;
            color: #718096;
            transition: transform 0.3s ease-in-out;
        }
        .collapsible-section {
            max-height: 1000px;
            transition: max-height 0.4s ease-in-out, padding 0.4s ease-in-out, border-top-width 0.1s linear;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
        .collapsible-section.collapsed {
            max-height: 0;
            overflow: hidden;
            padding-top: 0;
            padding-bottom: 0;
            border-top-width: 0px;
        }
        .report-card-body {
            padding: 0.5rem 1rem;
        }
        .report-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0.25rem;
            font-size: 0.875rem;
            border-bottom: 1px dashed #edf2f7;
        }
        .report-item:last-child {
            border-bottom: none;
        }
        .report-item-name {
            color: #4a5568;
        }
        .report-item-details {
            color: #1a202c;
            font-weight: 500;
        }
        .report-card-footer {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 1rem;
            background-color: #f7fafc;
            border-top: 1px solid #e2e8f0;
        }
        .report-card-footer label {
            font-weight: 500;
            font-size: 0.875rem;
        }
        .report-card-footer select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #cbd5e0;
            border-radius: 0.375rem;
            background-color: #fff;
        }
        .report-card-footer button {
            align-self: flex-end;
        }

        /* --- INVOICE STYLES (NEW & OLD) --- */

        /* A4 Landscape (Original) */
        .a4-sheet-landscape {
            background: white;
            width: 297mm;
            min-height: 210mm;
            display: flex;
            flex-direction: column;
            margin: 0 auto;
            margin-bottom: 1rem;
            padding: 10mm;
            box-shadow: 0 0 0.5cm rgba(0,0,0,0.2);
            position: relative;
        }
        
        /* A4 Portrait (New) */
        .a4-sheet-portrait {
            background: white;
            width: 210mm;
            min-height: 297mm;
            display: flex;
            flex-direction: column;
            margin: 2rem auto;
            padding: 12mm;
            box-shadow: 0 0 0.5cm rgba(0,0,0,0.2);
        }

        .invoice-header, .invoice-footer {
            flex-shrink: 0;
        }
        .invoice-content {
            flex-grow: 1;
        }
        .invoice-logo {
            width: 112px;
            height: 112px;
            object-fit: cover;
        }
        .invoice-calculation-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            grid-template-rows: auto auto;
            align-items: center;
            justify-items: center;
            gap: 2px;
            height: 100%;
            min-height: 60px;
        }
        .invoice-calculation-numbers {
            text-align: center;
            grid-column: 2 / 3;
            border-bottom: 2px solid #4b5563;
            width: 100%;
            padding-bottom: 3mm !important; /* FIX: Added !important */
            margin-bottom: 2px;
        }
        .invoice-calculation-numbers span {
            display: block;
            font-size: 0.8rem;
        }
        .invoice-single-number-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 60px;
        }
        #invoice-content-wrapper .invoice-table {
            table-layout: fixed;
            width: 100%;
        }
        /* FIX: Apply padding bottom to landscape cells and ensure middle alignment */
        .a4-sheet-landscape .invoice-table th,
        .a4-sheet-landscape .invoice-table td {
            padding-bottom: 5mm !important;
            vertical-align: middle;
        }
        #invoice-content-wrapper .has-calculation td {
            vertical-align: middle; 
        }
        .print-break-after { 
            page-break-after: always; 
        }

        /* STYLES FOR SHRUNK PORTRAIT INVOICE */
        .shrunk-invoice .invoice-logo {
            width: 80px; height: 80px;
        }
        .shrunk-invoice .invoice-header h1 .text-xl { font-size: 1rem; }
        .shrunk-invoice .invoice-header h1 .text-4xl { font-size: 1.875rem; }
        .shrunk-invoice .invoice-header .text-xs { font-size: 0.65rem; }
        .shrunk-invoice .main-title h2 { font-size: 2.5rem; margin: 0.5rem 0; }
        .shrunk-invoice .customer-info { font-size: 0.7rem; margin-bottom: 1rem; }
        .shrunk-invoice .table-heading { font-size: 0.9rem; }
        .shrunk-invoice .invoice-table-wrapper { width: 100%; overflow-x: auto; }
        .shrunk-invoice .invoice-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.6rem;
            table-layout: fixed;
        }
        .shrunk-invoice .invoice-table th, 
        .shrunk-invoice .invoice-table td {
            border: 1px solid #d1d5db;
            padding-bottom: 3mm;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .shrunk-invoice .invoice-table th { font-weight: 600; }
        .shrunk-invoice .invoice-table th:first-child,
        .shrunk-invoice .invoice-table td:first-child { width: 8%; }
        .shrunk-invoice .invoice-table th:last-child,
        .shrunk-invoice .invoice-table td:last-child { width: 9%; }
        .shrunk-invoice .invoice-calculation-grid { gap: 1px; min-height: 45px; }
        .shrunk-invoice .invoice-calculation-numbers { padding-bottom: 3mm !important; margin-bottom: 1px; border-bottom-width: 1px; }
        .shrunk-invoice .invoice-calculation-numbers span { font-size: 0.55rem; }
        .shrunk-invoice .invoice-single-number-cell { min-height: 45px; }
        .shrunk-invoice .grand-total { font-size: 0.9rem; }
        .shrunk-invoice .grand-total .total-amount { font-size: 1.1rem; }
        .shrunk-invoice .signature { font-size: 0.8rem; margin-top: 2rem; }

        /* NEW: Styles for conditional vertical alignment */
        .quantity-cell-valign { vertical-align: bottom !important; }
        .quantity-cell-valign-middle { vertical-align: middle !important; }
        
        .invoice-calculation-grid .col-start-1 {
            margin-left: 2mm !important;
        }

        /* PRINTING STYLES */
        @media print {
            body > *:not(.invoice-modal) {
                display: none;
            }
            .invoice-modal {
                position: static; width: 100%; height: auto;
                overflow: visible; background: none; display: block !important;
            }
            .invoice-modal .modal-content {
                border: none; padding: 0; box-shadow: none;
                width: 100%; max-width: 100%; height: auto; max-height: none;
            }
            .no-print { display: none !important; }
            .a4-sheet-landscape, .a4-sheet-portrait {
                box-shadow: none; margin: 0; width: 100%;
                page-break-after: always;
            }
            .a4-sheet-landscape { height: 100vh; padding: 10mm; }
            .a4-sheet-portrait { min-height: 297mm; padding: 12mm; }
            .a4-sheet-landscape:last-child, .a4-sheet-portrait:last-child {
                page-break-after: auto;
            }
        }
        
        /* FIX: Mobile Invoice Modal Styles */
        @media (max-width: 768px) {					
            .invoice-modal .modal-content {
                padding-top: 40px; /* Make space for the close button */
            }
            .invoice-modal .modal-header {
                flex-direction: column;
                align-items: stretch; /* Make items full-width */
                gap: 1rem;
                text-align: center;
            }
            .invoice-modal .modal-header .modal-title {
                font-size: 1.25rem;
                margin-right: 0;
            }
            .invoice-modal .modal-header .modal-actions {
                display: none; /* Hide actions on mobile as per original design */
            }
            .invoice-modal .close-modal {
                position: absolute;
                top: 8px;
                right: 12px;
                font-size: 32px;
                float: none;
            }
            #invoice-content-wrapper {
                -webkit-overflow-scrolling: touch;
            }
        }
        
        /* NEW: Driver Search Styles */
        #driverSearchResults {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 30;
            background-color: white;
            border: 1px solid #d1d5db;
            border-top: none;
            border-radius: 0 0 0.5rem 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .driver-search-item {
            padding: 0.75rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .driver-search-item:hover, .driver-search-item.highlighted {
            background-color: #eef2ff; /* indigo-100 */
        }
        .driver-search-item:not(:last-child) {
            border-bottom: 1px solid #e5e7eb; /* gray-200 */
        }

    </style>
</head>
<body>
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-7xl">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Vựa Mít Ngọc Anh HD68</h1>

        <div class="sticky-tabs flex flex-nowrap justify-center mb-6 border-b border-gray-200">
            <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-base sm:text-lg font-bold text-gray-700 focus:outline-none transition-colors duration-200 active" onclick="openTab('tab1')">Thông Tin Lái</button>
            <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-base sm:text-lg font-bold text-gray-700 focus:outline-none transition-colors duration-200" onclick="openTab('tab2')">Báo Cáo Hàng Ngày</button>
        </div>

        <div id="tab1" class="tab-content active p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Mục Thông Tin Lái</h2>
            <p class="text-gray-600 leading-relaxed mb-4 text-left">Quản lý thông tin chi tiết của các lái, bao gồm thông tin liên hệ và tài khoản ngân hàng.</p>
            
            <div class="hidden md:flex justify-between items-center gap-4 mb-4">
                 <div class="flex items-center">
                    <input type="checkbox" id="desktopFilterUnverified" name="desktopFilterUnverified" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="desktopFilterUnverified" class="ml-2 block text-sm font-medium text-gray-700 whitespace-nowrap">Chưa đánh dấu</label>
                </div>
                <input type="search" id="desktopDriverSearch" placeholder="Tìm kiếm lái..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div id="driverListTableContainer" class="overflow-x-auto rounded-lg shadow-sm border border-gray-200 hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Stt</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên và SĐT Lái</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Ngân Hàng</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Số Tài Khoản</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tên Tài Khoản</th>
                            <th scope="col" class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="driverListTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="desktopLoadMoreContainer" class="hidden md:flex justify-center mt-4">
                <button id="desktopLoadMoreBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Xem Thêm</button>
            </div>

            <div id="mobileDriverListContainer" class="mt-4 md:hidden">
                <div class="flex items-center justify-between font-bold p-2 bg-gray-100 border-b border-gray-300 rounded-t-lg">
                    <div class="flex items-center">
                        <div class="w-[40px] text-center text-xs text-gray-500 uppercase">Stt</div>
                        <div class="text-xs text-gray-500 uppercase pl-2">Lái</div>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="mobileFilterUnverified" name="mobileFilterUnverified" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="mobileFilterUnverified" class="ml-2 block text-sm font-medium text-gray-700 whitespace-nowrap">Chưa đánh dấu</label>
                    </div>
                </div>
                <div class="p-2 bg-gray-100 border-b border-gray-300">
                    <input type="search" id="mobileDriverSearch" placeholder="Tìm kiếm lái..." class="w-full px-2 py-1 text-sm font-normal border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div id="mobileDriverList"></div>
                <div id="mobileLoadMoreContainer" class="flex md:hidden justify-center mt-4">
                    <button id="mobileLoadMoreBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Xem Thêm</button>
                </div>
            </div>

            <h3 id="driver-form-title" class="text-xl font-semibold text-gray-700 mt-8 mb-4">Thêm/Chỉnh Sửa Thông Tin Lái</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="driverNamePhone">Tên và SĐT Lái:</label>
                    <input type="text" id="driverNamePhone" name="driverNamePhone" placeholder="Nhập tối đa 21 kí tự" maxlength="21">
                </div>
                <div>
                    <label for="bankName">Ngân Hàng:</label>
                    <select id="bankName" name="bankName"><option value="">Chọn Ngân Hàng</option></select>
                </div>
                <div>
                    <label for="accountNumber">Số Tài Khoản:</label>
                    <input type="number" id="accountNumber" name="accountNumber" placeholder="Số Tài Khoản">
                </div>
                <div>
                    <label for="accountName">Tên Tài Khoản:</label>
                    <input type="text" id="accountName" name="accountName" placeholder="Tên Chủ Tài Khoản">
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button id="addUpdateButton" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="addOrUpdateDriver()">Thêm vào danh sách</button>
                <button id="cancelButton" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200 hidden" onclick="cancelEdit()">Hủy</button>
            </div>
        </div>

        <div id="tab2" class="tab-content p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Mục Báo Cáo Hàng Ngày</h2>
            <p class="text-gray-600 leading-relaxed mb-4 text-left">Nhập và quản lý dữ liệu báo cáo hàng ngày của các lái.</p>
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <label for="reportDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                    <input type="text" id="reportDate" class="flatpickr-input w-full" placeholder="dd/mm/yyyy">
                    <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="exportReportsToExcel()">Xuất Excel</button>			
                </div>
            </div>

            <div id="dailyReportTableContainer" class="rounded-lg shadow-sm border border-gray-200 mt-8 hidden md:block">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-16">Toa</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-48">Lái</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Tổng KG</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Thành Tiền</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Ghi Chú</th>
                            <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="dailyReportTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
            
            <div class="mt-8 md:hidden">
                <div class="flex items-center font-bold p-2 bg-gray-100 border-b border-gray-300 rounded-t-lg">
                    <div class="w-[40px] text-center text-xs text-gray-500 uppercase">Toa</div>
                    <div class="flex-grow text-xs text-gray-500 uppercase pl-2">Lái</div>
                </div>
                <div id="mobileReportList"></div>
            </div>

            <div class="mt-4 flex justify-end gap-4">
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="openSummaryModal()">Tổng Hợp Dữ Liệu</button>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="handleAddNewRowClick()">Thêm Dòng Mới</button>
            </div>
            
            <h3 id="add-report-title" class="text-xl font-semibold text-gray-700 mt-8 mb-4 hidden">Thêm Dữ Liệu Báo Cáo</h3>
            <div id="dailyReportForm" class="hidden">
                <div class="grid grid-cols-1 gap-4 mb-6">
                    <div class="w-full">
                        <label for="dailyReportDriverSearch">Lái:</label>
                        <div class="relative">
                            <input type="text" id="dailyReportDriverSearch" name="dailyReportDriverSearch" placeholder="Tìm & chọn lái..." autocomplete="off" class="w-full focus:border-indigo-500 focus:ring-indigo-500">
                            <div id="driverSearchResults" class="hidden"></div>
                            <button id="changeDriverBtn" class="absolute top-1/2 right-3 -translate-y-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-3 rounded-md hidden">Đổi</button>
                        </div>
                    </div>
                    <div class="w-full">
                        <label for="Ghi_Chú">Ghi Chú:</label>
                        <input type="text" id="Ghi_Chú" placeholder="Ghi chú" maxlength="110" disabled>
                    </div>
                </div>
                <div class="input-group">
                    <div><label for="B1_Thái">B1_Thái:</label><input type="text" id="B1_Thái" class="thai-cell" placeholder="1+2+3..." disabled></div><div><label for="Giá_B1_Thái">Giá_B1_Thái:</label><input type="text" id="Giá_B1_Thái" disabled></div><div><label for="B2_Thái">B2_Thái:</label><input type="text" id="B2_Thái" class="thai-cell" disabled></div><div><label for="Giá_B2_Thái">Giá_B2_Thái:</label><input type="text" id="Giá_B2_Thái" disabled></div><div><label for="C1_Thái">C1_Thái:</label><input type="text" id="C1_Thái" class="thai-cell" disabled></div><div><label for="Giá_C1_Thái">Giá_C1_Thái:</label><input type="text" id="Giá_C1_Thái" disabled></div><div><label for="C2_Thái">C2_Thái:</label><input type="text" id="C2_Thái" class="thai-cell" disabled></div><div><label for="Giá_C2_Thái">Giá_C2_Thái:</label><input type="text" id="Giá_C2_Thái" disabled></div><div><label for="C3_Thái">C3_Thái:</label><input type="text" id="C3_Thái" class="thai-cell" disabled></div><div><label for="Giá_C3_Thái">Giá_C3_Thái:</label><input type="text" id="Giá_C3_Thái" disabled></div><div><label for="D1_Thái">D1_Thái:</label><input type="text" id="D1_Thái" class="thai-cell" disabled></div><div><label for="Giá_D1_Thái">Giá_D1_Thái:</label><input type="text" id="Giá_D1_Thái" disabled></div><div><label for="D2_Thái">D2_Thái:</label><input type="text" id="D2_Thái" class="thai-cell" disabled></div><div><label for="Giá_D2_Thái">Giá_D2_Thái:</label><input type="text" id="Giá_D2_Thái" disabled></div><div><label for="E_Thái">E_Thái:</label><input type="text" id="E_Thái" class="thai-cell" disabled></div><div><label for="Giá_E_Thái">Giá_E_Thái:</label><input type="text" id="Giá_E_Thái" disabled></div><div><label for="Chợ_Thái">Chợ_Thái:</label><input type="text" id="Chợ_Thái" class="thai-cell" disabled></div><div><label for="Giá_Chợ_Thái">Giá_Chợ_Thái:</label><input type="text" id="Giá_Chợ_Thái" disabled></div><div><label for="Xơ_Thái">Xơ_Thái:</label><input type="text" id="Xơ_Thái" class="thai-cell" disabled></div><div><label for="Giá_Xơ_Thái">Giá_Xơ_Thái:</label><input type="text" id="Giá_Xơ_Thái" disabled></div>
                    <div><label for="A1_indo">A1_indo:</label><input type="text" id="A1_indo" class="indo-cell" disabled></div><div><label for="Giá_A1_indo">Giá_A1_indo:</label><input type="text" id="Giá_A1_indo" disabled></div><div><label for="A2_indo">A2_indo:</label><input type="text" id="A2_indo" class="indo-cell" disabled></div><div><label for="Giá_A2_indo">Giá_A2_indo:</label><input type="text" id="Giá_A2_indo" disabled></div><div><label for="B1_indo">B1_indo:</label><input type="text" id="B1_indo" class="indo-cell" disabled></div><div><label for="Giá_B1_indo">Giá_B1_indo:</label><input type="text" id="Giá_B1_indo" disabled></div><div><label for="B2_indo">B2_indo:</label><input type="text" id="B2_indo" class="indo-cell" disabled></div><div><label for="Giá_B2_indo">Giá_B2_indo:</label><input type="text" id="Giá_B2_indo" disabled></div><div><label for="B3_indo">B3_indo:</label><input type="text" id="B3_indo" class="indo-cell" disabled></div><div><label for="Giá_B3_indo">Giá_B3_indo:</label><input type="text" id="Giá_B3_indo" disabled></div><div><label for="C1_indo">C1_indo:</label><input type="text" id="C1_indo" class="indo-cell" disabled></div><div><label for="Giá_C1_indo">Giá_C1_indo:</label><input type="text" id="Giá_C1_indo" disabled></div><div><label for="C2_indo">C2_indo:</label><input type="text" id="C2_indo" class="indo-cell" disabled></div><div><label for="Giá_C2_indo">Giá_C2_indo:</label><input type="text" id="Giá_C2_indo" disabled></div>
                    <div><label for="Chợ_1_indo">Chợ_1_indo:</label><input type="text" id="Chợ_1_indo" class="indo-cell" disabled></div><div><label for="Giá_Chợ_1_indo">Giá_Chợ_1_indo:</label><input type="text" id="Giá_Chợ_1_indo" disabled></div>
                    <div><label for="Chợ_2_indo">Chợ_2_indo:</label><input type="text" id="Chợ_2_indo" class="indo-cell" disabled></div><div><label for="Giá_Chợ_2_indo">Giá_Chợ_2_indo:</label><input type="text" id="Giá_Chợ_2_indo" disabled></div>
                    <div><label for="Xơ_indo">Xơ_indo:</label><input type="text" id="Xơ_indo" class="indo-cell" disabled></div><div><label for="Giá_Xơ_indo">Giá_Xơ_indo:</label><input type="text" id="Giá_Xơ_indo" disabled></div>
                    <div class="w-full hidden"><label for="Tổng_KG_display">Tổng KG:</label><input type="text" id="Tổng_KG_display" readonly class="bg-gray-100 text-gray-700 font-bold"></div>
                    <div class="w-full hidden"><label for="Thành_Tiền_display">Thành Tiền:</label><input type="text" id="Thành_Tiền_display" readonly class="bg-gray-100 text-gray-700 font-bold"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div id="sticky-totals-footer" class="fixed bottom-0 left-0 right-0 bg-white p-2 border-t shadow-lg z-40 hidden transition-transform duration-300 transform translate-y-full">
        <div class="max-w-4xl mx-auto flex justify-around text-center">
            <div>
                <label class="block text-xs font-medium text-gray-500">Tổng KG</label>
                <span id="sticky-total-kg" class="text-lg font-bold text-gray-800">0</span>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500">Thành Tiền</label>
                <span id="sticky-total-tien" class="text-lg font-bold text-indigo-600">0</span>
            </div>
        </div>
    </div>

    <div id="qrModal" class="qr-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('qrModal')">&times;</span>
            <h3 id="qrModalTitle" class="text-xl font-bold mb-4">Quét mã để thanh toán</h3>
            <img id="qrCodeImg" src="" alt="QR Code" class="mx-auto">
            <div id="qrModalInfo" class="text-left my-4"></div>
            <div id="qrModalActions" class="flex justify-end gap-3"></div>
        </div>
    </div>

    <div id="invoiceModal" class="invoice-modal">
        <div class="modal-content overflow-auto">
            <span class="close-modal no-print" onclick="closeModal('invoiceModal')">&times;</span>
            <div class="mb-4 no-print modal-header flex justify-between items-center">
                 <h3 id="invoiceModalTitle" class="text-xl font-bold modal-title">Xem Hóa Đơn</h3>
            </div>
            <div id="invoice-content-wrapper">
                </div>
        </div>
    </div>
    
    <div id="driverUsageModal" class="driver-usage-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('driverUsageModal')">&times;</span>
            <h3 class="text-xl font-bold mb-4 text-red-600">Không thể xóa Lái</h3>
            <div id="driverUsageInfo" class="text-left my-4 space-y-2 text-sm">
                 <p class="text-gray-800 font-semibold">- Lái này đang được sử dụng trong các toa hàng dưới đây.</p>
                 <p class="text-gray-700">- Bạn phải chuyển các toa này cho một lái khác trước khi có thể xóa.</p>
                 <p class="text-gray-700">- Bấm vào từng toa để xem chi tiết và đổi lái.</p>
            </div>
            <div id="conflictingReportsList" class="my-4 max-h-[60vh] overflow-y-auto p-1 bg-gray-50 rounded-lg">
                </div>
            <div class="flex justify-end gap-3 mt-4">
                 <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('driverUsageModal')">Đóng</button>
            </div>
        </div>
    </div>

    <div id="summaryModal" class="summary-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('summaryModal')">&times;</span>
            <h3 class="text-xl font-bold mb-4">Tổng Hợp Dữ Liệu</h3>
            <div id="summaryModalContent" class="max-h-[70vh] overflow-y-auto">
                <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Loại Hàng</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Tổng KG</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                 <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('summaryModal')">Đóng</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    // --- BIẾN TOÀN CỤC VÀ DỮ LIỆU ---
    const baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
    let driverList = [];
    let editingIndexTab1 = -1;
    let dailyReports = [];
    let liveEditingReport = null; // Report object being edited live
    let isSaving = false; // Debounce flag for saving
    let saveTimeout; // Timer for auto-saving
    let tomSelect; // Tom Select instance
    const DRIVERS_PER_PAGE = 10;
    let driversCurrentlyShown = DRIVERS_PER_PAGE;
    const banksData = [{"name":"ACB","code":"970416"},{"name":"Agribank","code":"970405"},{"name":"BAC A BANK","code":"970409"},{"name":"BaoViet Bank","code":"970438"},{"name":"BIDV","code":"970418"},{"name":"Cake by VPBank","code":"546034"},{"name":"CIMB","code":"422589"},{"name":"DongA Bank","code":"970406"},{"name":"Eximbank","code":"970431"},{"name":"GPBank","code":"970408"},{"name":"HDBank","code":"970437"},{"name":"Hong Leong Bank","code":"970442"},{"name":"Indovina Bank","code":"970434"},{"name":"KienLong Bank","code":"970452"},{"name":"LPBank","code":"970449"},{"name":"MB BANK","code":"970422"},{"name":"MSB","code":"970426"},{"name":"Nam A Bank","code":"970428"},{"name":"NCB","code":"970419"},{"name":"OCB","code":"970448"},{"name":"PBVN","code":"970439"},{"name":"PG Bank","code":"970430"},{"name":"PVcomBank","code":"970412"},{"name":"Sacombank","code":"970403"},{"name":"Saigonbank","code":"970400"},{"name":"SCB","code":"970429"},{"name":"SeABank","code":"970440"},{"name":"SHB","code":"970443"},{"name":"Shinhan Bank","code":"970424"},{"name":"Timo","code":"963388"},{"name":"TPBank","code":"970423"},{"name":"UOB","code":"970458"},{"name":"VIB","code":"970441"},{"name":"VietABank","code":"970427"},{"name":"VietBank","code":"970433"},{"name":"Vietcombank","code":"970436"},{"name":"VietinBank","code":"970415"},{"name":"VPBank","code":"970432"},{"name":"VRB","code":"970421"}];
    const quantityColumns = ["B1_Thái","B2_Thái","C1_Thái","C2_Thái","C3_Thái","D1_Thái","D2_Thái","E_Thái","Chợ_Thái","Xơ_Thái","A1_indo","A2_indo","B1_indo","B2_indo","B3_indo","C1_indo","C2_indo","Chợ_1_indo","Chợ_2_indo","Xơ_indo"];
    const priceColumns = quantityColumns.map(col => `Giá_${col}`);
    const allCalcColumns = [...quantityColumns, ...priceColumns];

    // --- CÁC HÀM TIỆN ÍCH ---
    function removeAccents(str) {
        if (!str) return '';
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
    }

    function getBankBin(bankName) {
        const bank = banksData.find(b => removeAccents(b.name.toLowerCase()) === removeAccents(bankName.toLowerCase()));
        return bank ? bank.code : null;
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
            if (modalId === 'qrModal') document.getElementById('qrCodeImg').src = "";
            if (modalId === 'invoiceModal') {
                 document.getElementById('invoice-content-wrapper').innerHTML = '';
            }
        }
    }
    
    function recalculateDailyToaNumbers(date) {
        const reportsForDay = dailyReports.filter(r => r.date === date);

        // Separate existing reports from new ones
        const existingReports = reportsForDay.filter(r => r.toa);
        const newReports = reportsForDay.filter(r => !r.toa);

        // Sort only the existing reports to correctly fill gaps from deletions
        existingReports.sort((a, b) => a.toa - b.toa);

        // Renumber all existing reports sequentially from 1
        existingReports.forEach((report, index) => {
            report.toa = index + 1;
        });

        // Assign the next available numbers to the new reports
        let nextToa = existingReports.length + 1;
        newReports.forEach(report => {
            report.toa = nextToa;
            nextToa++;
        });
    }

    function updateCalculationPreview(inputElement) {
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

    function showAllCalculationPreviews() {
        allCalcColumns.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                updateCalculationPreview(input);
            }
        });
    }

    // --- HÀM HÓA ĐƠN ---
    function generateQuantityCellHtml(expression, isShrunk = false, useComplexLayout = true) {
        const sum = getValueFromExpression(expression);
        
        if (!useComplexLayout) {
            return `<div class="invoice-single-number-cell"><div>${sum > 0 ? sum.toLocaleString('vi-VN') : ''}</div></div>`;
        }

        const gridClass = isShrunk ? 'invoice-calculation-grid' : 'invoice-calculation-grid';
        const hasCalculation = expression && expression.includes('+') && expression.trim() !== String(sum);

        if (!hasCalculation) {
            return `
                <div class="${gridClass}" style="grid-template-rows: 1fr; align-items: end; justify-items: center;">
                     <div style="grid-column: 1 / 3;">
                        ${sum > 0 ? sum.toLocaleString('vi-VN') : ''}
                     </div>
                </div>
            `;
        }
        
        const numbersHtml = expression.split('+').map(num => `<span>${num.trim()}</span>`).join('');
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

    async function showInvoice(reportIndex) {
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
        let allPagesHtml = '';

        const anyCellHasCalculation = [...activeThaiColumns, ...activeIndoColumns].some(col =>
            report[col] && report[col].includes('+')
        );
        const quantityValignClass = anyCellHasCalculation ? 'quantity-cell-valign' : 'quantity-cell-valign-middle';

        if (hasThaiData && hasIndoData) {
            const portraitHeaderHtml = `
                <header class="invoice-header flex justify-between items-center mb-2">
                    <div class="flex items-center">
                        <img src="${logoUrl}" alt="Logo" class="invoice-logo rounded-md mr-4" onerror="this.onerror=null; this.src='https://placehold.co/80x80/cccccc/ffffff?text=LOGO';">
                    </div>
                    <div class="text-right">
                        <h1 class="font-extrabold text-gray-900 leading-tight">
                            <span class="text-xl">Vựa Mít</span><br>
                            <span class="text-4xl">NGỌC ANH HD68</span>
                        </h1>
                        <div class="text-xs text-gray-600 mt-1">
                            <p>CS1: Cầu Ông Hưng - Cái Bè - Tiền Giang</p>
                            <p>CS2: QL30 - Cầu Rạch Giồng - Cái Bè - Tiền Giang</p>
                            <p>ĐT: 0941.017.878 (A Phong) - 0901.422.658 (A Huy)</p>
                        </div>
                    </div>
                </header>`;

            const customerInfoHtml = `
                <div class="main-title text-center border-t-2 border-gray-800 pt-2">
                    <h2 class="font-bold text-indigo-600 uppercase" style="padding-bottom: 5mm;">Hóa Đơn</h2>
                </div>
                <div class="customer-info">
                    <p><strong>Ngày lập:</strong> ${report.date}</p>
                    <p><strong>Khách hàng:</strong> ${report.driver}</p>
                    ${driver ? `<p><strong>STK:</strong> ${driver.accountNumber} - ${driver.bankName} - ${driver.accountName}</p>` : ''}
                </div>`;
            
            let thaiTotalKg = 0;
            let thaiTotalAmount = 0;
            let thaiHeader = `<th>Loại Hàng</th>`;
            let thaiQtyRow = `<td class="font-medium">Số lượng</td>`;
            let thaiPriceRow = `<td class="font-medium">Đơn giá</td>`;
            let thaiAmountRow = `<td class="font-bold">Thành tiền</td>`;
            activeThaiColumns.forEach(col => {
                const qty = getValueFromExpression(report[col]);
                const price = getValueFromExpression(report[`Giá_${col}`]);
                const amount = qty * price;
                thaiTotalKg += qty;
                thaiTotalAmount += amount;
                thaiHeader += `<th>${col.replace('_Thái', ' Thái')}</th>`;
                thaiQtyRow += `<td class="${quantityValignClass}">${generateQuantityCellHtml(report[col], true, anyCellHasCalculation)}</td>`;
                thaiPriceRow += `<td>${price > 0 ? price.toLocaleString('vi-VN') : ''}</td>`;
                thaiAmountRow += `<td class="font-semibold">${amount > 0 ? amount.toLocaleString('vi-VN') : ''}</td>`;
            });
            thaiHeader += `<th class="font-bold">Tổng Cộng (Thái)</th>`;
            thaiQtyRow += `<td class="font-bold">${thaiTotalKg.toLocaleString('vi-VN')}</td>`;
            thaiPriceRow += `<td></td>`;
            thaiAmountRow += `<td class="font-bold">${thaiTotalAmount.toLocaleString('vi-VN')}</td>`;
            const hasCalculationThai = activeThaiColumns.some(col => report[col] && report[col].includes('+'));
            
            const thaiTableHtml = `
                <div class="mb-4">
                    <h3 class="table-heading font-bold text-gray-800 mb-1 pl-2 border-l-4 border-yellow-400" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT THÁI</h3>
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

            let indoTotalKg = 0;
            let indoTotalAmount = 0;
            let indoHeader = `<th>Loại Hàng</th>`;
            let indoQtyRow = `<td class="font-medium">Số lượng</td>`;
            let indoPriceRow = `<td class="font-medium">Đơn giá</td>`;
            let indoAmountRow = `<td class="font-bold">Thành tiền</td>`;
            activeIndoColumns.forEach(col => {
                const qty = getValueFromExpression(report[col]);
                const price = getValueFromExpression(report[`Giá_${col}`]);
                const amount = qty * price;
                indoTotalKg += qty;
                indoTotalAmount += amount;
                indoHeader += `<th>${col.replace('_indo', ' Indo')}</th>`;
                indoQtyRow += `<td class="${quantityValignClass}">${generateQuantityCellHtml(report[col], true, anyCellHasCalculation)}</td>`;
                indoPriceRow += `<td>${price > 0 ? price.toLocaleString('vi-VN') : ''}</td>`;
                indoAmountRow += `<td class="font-semibold">${amount > 0 ? amount.toLocaleString('vi-VN') : ''}</td>`;
            });
            indoHeader += `<th class="font-bold">Tổng Cộng (Indo)</th>`;
            indoQtyRow += `<td class="font-bold">${indoTotalKg.toLocaleString('vi-VN')}</td>`;
            indoPriceRow += `<td></td>`;
            indoAmountRow += `<td class="font-bold">${indoTotalAmount.toLocaleString('vi-VN')}</td>`;
            const hasCalculationIndo = activeIndoColumns.some(col => report[col] && report[col].includes('+'));

            const indoTableHtml = `
                <div class="mb-4">
                    <h3 class="table-heading font-bold text-gray-800 mb-1 pl-2 border-l-4 border-red-400" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT INDO</h3>
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

            const portraitFooterHtml = `
                <footer class="invoice-footer mt-auto pt-4">
                    <div class="flex justify-end">
                        <div class="w-full md:w-2/3 grand-total">
                             <div class="flex items-center py-2 border-t-2 border-b-2 border-gray-800" style="padding-bottom: 5mm;">
                                <span class="font-bold text-gray-800 whitespace-nowrap mr-auto">TỔNG CỘNG HÓA ĐƠN:</span>
                                <span class="total-amount font-bold text-indigo-600 whitespace-nowrap text-right">${(parseFloat(report.Thành_Tiền) || 0).toLocaleString('vi-VN')} VND</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                         <div class="signature text-center">
                            <p class="font-semibold text-gray-800 mb-12">Người lập hóa đơn</p>
                        </div>
                    </div>
                </footer>`;

            allPagesHtml = `
                <div class="a4-sheet-portrait shrunk-invoice">
                    ${portraitHeaderHtml}
                    <main class="invoice-content">
                        ${customerInfoHtml}
                        ${thaiTableHtml}
                        ${indoTableHtml}
                    </main>
                    ${portraitFooterHtml}
                </div>`;

        } else {
            const headerHtml = `
                <div class="invoice-header flex justify-between items-center mb-4">
                    <div class="flex items-center">
                        <img src="${logoUrl}" alt="Logo" class="invoice-logo rounded-md mr-4" onerror="this.onerror=null; this.src='https://placehold.co/140x140/cccccc/ffffff?text=LOGO';">
                    </div>
                    <div class="text-right">
                        <h1 class="font-extrabold text-gray-900 leading-tight">
                            <span class="text-xl">Vựa Mít</span><br>
                            <span class="text-4xl">NGỌC ANH HD68</span>
                        </h1>
                        <div class="text-xs text-gray-600 mt-2">
                            <p>CS1: Cầu Ông Hưng - Cái Bè - Tiền Giang</p>
                            <p>CS2: QL30 - Cầu Rạch Giồng - Cái Bè - Tiền Giang</p>
                            <p>ĐT: 0941.017.878 (A Phong) - 0901.422.658 (A Huy)</p>
                        </div>
                    </div>
                </div>`;
            
            const customerInfoAndTitleHtml = `
                <div class="border-t-2 border-gray-800 pt-3 mb-4">
                    <h2 class="text-center text-5xl font-bold text-indigo-600 uppercase" style="padding-bottom: 5mm;">Hóa Đơn</h2>
                </div>
                <div class="mb-6 text-sm">
                    <div class="mb-2">
                        <h3 class="font-semibold text-gray-800 mb-1">Ngày lập hóa đơn: ${report.date}</h3>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Khách hàng:</h3>
                        <p class="text-gray-700">${report.driver}</p>
                        ${driver ? `<p class="text-gray-700">STK: ${driver.accountNumber} - ${driver.bankName} - ${driver.accountName}</p>` : ''}
                    </div>
                </div>`;
            
            const finalSectionHtml = `
                <div class="flex justify-end mt-8 print-break-inside-avoid">
                    <div class="w-full">
                        <div class="flex items-center py-2 border-t-2 border-b-2 border-gray-800" style="padding-bottom: 5mm;">
                            <span class="text-lg font-bold text-gray-800 whitespace-nowrap mr-auto">TỔNG CỘNG HÓA ĐƠN:</span>
                            <span class="text-2xl font-bold text-indigo-600 whitespace-nowrap text-right">${(parseFloat(report.Thành_Tiền) || 0).toLocaleString('vi-VN')} VND</span>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-4 print-break-inside-avoid">
                    <div class="flex justify-end">
                        <div class="text-center">
                            <p class="font-semibold text-gray-800 mb-12">Người lập hóa đơn</p>
                        </div>
                    </div>
                </div>`;

            if (hasThaiData) {
                let thaiTotalKg = 0;
                let thaiTotalAmount = 0;
                let header = `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Loại Hàng</th>`;
                let qtyRow = `<td class="p-1 font-medium border-2 border-gray-200 text-center">Số lượng</td>`;
                let priceRow = `<td class="p-2 font-medium border-2 border-gray-200 text-center align-middle">Đơn giá</td>`;
                let amountRow = `<td class="p-2 font-bold border-2 border-gray-200 text-center align-middle">Thành tiền</td>`;
                activeThaiColumns.forEach(col => {
                    const qty = getValueFromExpression(report[col]);
                    const price = getValueFromExpression(report[`Giá_${col}`]);
                    const amount = qty * price;
                    thaiTotalKg += qty;
                    thaiTotalAmount += amount;
                    header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">${col.replace('_Thái', ' Thái')}</th>`;
                    qtyRow += `<td class="p-1 text-gray-700 border-2 border-gray-200 ${quantityValignClass}">${generateQuantityCellHtml(report[col], false, anyCellHasCalculation)}</td>`;
                    priceRow += `<td class="p-2 text-gray-700 border-2 border-gray-200 text-center align-middle">${price.toLocaleString('vi-VN')}</td>`;
                    amountRow += `<td class="p-2 text-gray-800 font-semibold border-2 border-gray-200 text-center align-middle">${amount.toLocaleString('vi-VN')}</td>`;
                });
                header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Tổng Cộng (Thái)</th>`;
                qtyRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center">${thaiTotalKg.toLocaleString('vi-VN')}</td>`;
                priceRow += `<td class="border-2 border-gray-200 align-middle"></td>`;
                amountRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center align-middle">${thaiTotalAmount.toLocaleString('vi-VN')}</td>`;
                const hasCalculationThai = activeThaiColumns.some(col => report[col] && report[col].includes('+'));

                allPagesHtml += `
                    <div class="a4-sheet-landscape ${hasIndoData ? 'print-break-after' : ''}">
                        ${headerHtml}
                        <div class="invoice-content">
                            ${customerInfoAndTitleHtml}
                            <div class="mb-8 print-break-inside-avoid">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 pl-2 border-l-4 border-yellow-400" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT THÁI</h3>
                                <table class="invoice-table border-collapse text-sm">
                                    <thead><tr class="bg-gray-100">${header}</tr></thead>
                                    <tbody>
                                        <tr class="${hasCalculationThai ? 'has-calculation' : ''}">${qtyRow}</tr>
                                        <tr>${priceRow}</tr>
                                        <tr class="bg-gray-50">${amountRow}</tr>
                                    </tbody>
                                </table>
                            </div>
                            ${!hasIndoData ? finalSectionHtml : ''}
                        </div>
                    </div>`;
            }

            if (hasIndoData) {
                let indoTotalKg = 0;
                let indoTotalAmount = 0;
                let header = `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Loại Hàng</th>`;
                let qtyRow = `<td class="p-1 font-medium border-2 border-gray-200 text-center">Số lượng</td>`;
                let priceRow = `<td class="p-2 font-medium border-2 border-gray-200 text-center align-middle">Đơn giá</td>`;
                let amountRow = `<td class="p-2 font-bold border-2 border-gray-200 text-center align-middle">Thành tiền</td>`;
                activeIndoColumns.forEach(col => {
                    const qty = getValueFromExpression(report[col]);
                    const price = getValueFromExpression(report[`Giá_${col}`]);
                    const amount = qty * price;
                    indoTotalKg += qty;
                    indoTotalAmount += amount;
                    header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">${col.replace('_indo', ' Indo')}</th>`;
                    qtyRow += `<td class="p-1 text-gray-700 border-2 border-gray-200 ${quantityValignClass}">${generateQuantityCellHtml(report[col], false, anyCellHasCalculation)}</td>`;
                    priceRow += `<td class="p-2 text-gray-700 border-2 border-gray-200 text-center align-middle">${price.toLocaleString('vi-VN')}</td>`;
                    amountRow += `<td class="p-2 text-gray-800 font-semibold border-2 border-gray-200 text-center align-middle">${amount.toLocaleString('vi-VN')}</td>`;
                });
                header += `<th class="p-2 font-semibold text-gray-700 uppercase border-2 border-gray-200 text-center align-middle">Tổng Cộng (Indo)</th>`;
                qtyRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center">${indoTotalKg.toLocaleString('vi-VN')}</td>`;
                priceRow += `<td class="border-2 border-gray-200 align-middle"></td>`;
                amountRow += `<td class="p-2 text-gray-800 font-bold border-2 border-gray-200 text-center align-middle">${indoTotalAmount.toLocaleString('vi-VN')}</td>`;
                const hasCalculationIndo = activeIndoColumns.some(col => report[col] && report[col].includes('+'));

                allPagesHtml += `
                    <div class="a4-sheet-landscape">
                        ${!hasThaiData ? headerHtml : ''}
                        <div class="invoice-content">
                            ${!hasThaiData ? customerInfoAndTitleHtml : ''}
                            <div class="mb-8 print-break-inside-avoid">
                                <h3 class="text-lg font-bold text-gray-800 mb-2 pl-2 border-l-4 border-[#E69B8A]" style="padding-bottom: 5mm;">BẢNG KÊ HÀNG MÍT INDO</h3>
                                <table class="invoice-table border-collapse text-sm">
                                    <thead><tr class="bg-gray-100">${header}</tr></thead>
                                    <tbody>
                                        <tr class="${hasCalculationIndo ? 'has-calculation' : ''}">${qtyRow}</tr>
                                        <tr>${priceRow}</tr>
                                        <tr class="bg-gray-50">${amountRow}</tr>
                                    </tbody>
                                </table>
                            </div>
                            ${finalSectionHtml}
                        </div>
                    </div>`;
            }
        }
        
        if (!hasThaiData && !hasIndoData) {
            allPagesHtml = `<div class="a4-sheet-landscape"><div class="invoice-content flex items-center justify-center"><p>Không có dữ liệu hàng hóa cho hóa đơn này.</p></div></div>`;
        }

        // --- NEW LOGIC: Always convert to image ---
        
        invoiceWrapper.innerHTML = '<div class="text-center p-8"><p class="font-semibold text-gray-700">Đang tạo hình ảnh hóa đơn...</p></div>';
        document.getElementById('invoiceModalTitle').textContent = `Hóa Đơn - ${report.driver} - ${report.date}`;
        document.getElementById('invoiceModal').style.display = 'block';

        const tempContainer = document.createElement('div');
        tempContainer.innerHTML = allPagesHtml;
        const invoiceSheet = tempContainer.querySelector('.a4-sheet-portrait, .a4-sheet-landscape');

        if (!invoiceSheet) {
            invoiceWrapper.innerHTML = '<p class="text-center p-8 text-red-600">Lỗi: Không thể tạo nội dung hóa đơn.</p>';
            return;
        }

        document.body.appendChild(invoiceSheet);
        invoiceSheet.style.position = 'absolute';
        invoiceSheet.style.left = '-9999px';

        await new Promise(resolve => setTimeout(resolve, 50));

        try {
            const canvas = await html2canvas(invoiceSheet, { scale: 2, useCORS: true });
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
    
    function showDriverQR(index) {
        const driver = driverList[index];
        if (!driver) return;
        const bankBin = getBankBin(driver.bankName);
        if (!bankBin) return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${driver.bankName}.`);
        const amount = 1000;
        const description = removeAccents("Thanh toan thu 1000d");
        const accountName = driver.accountName;
        const qrApiUrl = `https://api.vietqr.io/image/${bankBin}-${driver.accountNumber}-qr_only.png?amount=${amount}&addInfo=${encodeURIComponent(description)}&accountName=${encodeURIComponent(accountName)}`;
        document.getElementById('qrModalTitle').textContent = "Thông tin ngân hàng đã chính xác?";
        document.getElementById('qrCodeImg').src = qrApiUrl;
        const infoDiv = document.getElementById('qrModalInfo');
        infoDiv.innerHTML = `<p><strong>Ngân hàng:</strong> ${driver.bankName}</p><p><strong>Số tài khoản:</strong> ${driver.accountNumber}</p><p><strong>Chủ tài khoản:</strong> ${driver.accountName}</p><p><strong>Số tiền:</strong> ${amount.toLocaleString('vi-VN')}đ</p><p><strong>Nội dung:</strong> Thanh toan thu 1000d</p>`;
        const actionsDiv = document.getElementById('qrModalActions');
        let cancelButtonText = driver.isVerified ? "Hủy đánh dấu chính xác" : "Hủy";
        let buttonsHTML = `<button class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg" onclick="cancelAndUnverifyDriver(${index})">${cancelButtonText}</button>`;
        if (!driver.isVerified) {
            buttonsHTML += `<button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg" onclick="verifyDriverAccount(${index})">Đánh dấu chính xác</button>`;
        }
        actionsDiv.innerHTML = buttonsHTML;
        document.getElementById('qrModal').style.display = 'block';
    }

    async function cancelAndUnverifyDriver(index) {
        const driver = driverList[index];
        if (driver && driver.isVerified) {
            driver.isVerified = false;
            await saveDriverListToCSV();
            renderDriverList();
            filterReportsByDate();
            customAlert('Đã hủy đánh dấu thông tin chính xác');
        }
        closeModal('qrModal');
    }

    async function verifyDriverAccount(index) {
        driverList[index].isVerified = true;
        await saveDriverListToCSV();
        renderDriverList();
        filterReportsByDate();
        closeModal('qrModal');
        customAlert('Đã đánh dấu thông tin chính xác');
    }

    function showPaymentModal(reportIndex) {
        const report = dailyReports[reportIndex];
        if (!report) return;
        const driver = driverList.find(d => d.driverNamePhone === report.driver);
        if (!driver) return customAlert(`Lỗi: Không tìm thấy thông tin ngân hàng cho Lái "${report.driver}". Vui lòng kiểm tra lại thẻ Thông Tin Lái.`);
        const bankBin = getBankBin(driver.bankName);
        if (!bankBin) return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${driver.bankName}.`);
        const amount = parseFloat(report.Thành_Tiền) || 0;
        if (amount <= 0) return customAlert('Số tiền thanh toán phải lớn hơn 0.');
        
        const driverNameUppercase = report.driver.toUpperCase();
        const formattedDate = report.date.replace(/\//g, '.');
        const displayText = `HD68 ck ${driverNameUppercase} ${formattedDate}`;
        const descriptionForApi = removeAccents(displayText);

        const accountName = driver.accountName;
        const qrApiUrl = `https://api.vietqr.io/image/${bankBin}-${driver.accountNumber}-qr_only.png?amount=${amount}&addInfo=${encodeURIComponent(descriptionForApi)}&accountName=${encodeURIComponent(accountName)}`;
        document.getElementById('qrModalTitle').textContent = "Thanh toán tiền hàng";
        document.getElementById('qrCodeImg').src = qrApiUrl;
        const infoDiv = document.getElementById('qrModalInfo');
        
        const reportInfoHtml = `
            <div class="text-left mb-4 p-3 bg-gray-50 rounded-lg border">
                <p><strong>Toa ${report.toa} - ${report.driver}</strong></p>
                <p><strong>Tổng KG:</strong> ${(parseFloat(report['Tổng_KG']) || 0).toLocaleString('vi-VN')} - <strong>Thành Tiền:</strong> ${amount.toLocaleString('vi-VN')}đ</p>
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

    async function togglePaymentStatus(reportIndex) {
        const report = dailyReports[reportIndex];
        if (!report) return;
        report.isPaid = !report.isPaid;
        await saveDailyReportsToCSV();
        filterReportsByDate();
        closeModal('qrModal');
    }

    function exportDriversToExcel() {
        if (driverList.length === 0) {
            customAlert('Không có dữ liệu lái để xuất.');
            return;
        }
        const dataToExport = driverList.map((driver, index) => ({
            'STT': index + 1,
            'Tên và SĐT Lái': driver.driverNamePhone,
            'Ngân Hàng': driver.bankName,
            'Số Tài Khoản': driver.accountNumber,
            'Tên Tài Khoản': driver.accountName,
            'Xác thực': driver.isVerified ? 'Đã xác thực' : 'Chưa xác thực'
        }));

        const ws = XLSX.utils.json_to_sheet(dataToExport);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'DanhSachLai');
        XLSX.writeFile(wb, 'danh_sach_lai.xlsx');
    }

    function exportReportsToExcel() {
        const selectedDate = document.getElementById('reportDate').value;
        const filteredReports = dailyReports.filter(report => report.date === selectedDate);

        if (filteredReports.length === 0) {
            customAlert('Không có dữ liệu báo cáo cho ngày đã chọn để xuất.');
            return;
        }

        const headerRow = [
            'Toa', 'Lái', 
            'B1 Thái', 'Giá B1 Thái', 'B2 Thái', 'Giá B2 Thái', 'C1 Thái', 'Giá C1 Thái', 'C2 Thái', 'Giá C2 Thái', 'C3 Thái', 'Giá C3 Thái', 'D1 Thái', 'Giá D1 Thái', 'D2 Thái', 'Giá D2 Thái', 'E Thái', 'Giá E Thái', 'Chợ Thái', 'Giá Chợ Thái', 'Xơ Thái', 'Giá Xơ Thái',
            'A1 indo', 'Giá A1 indo', 'A2 indo', 'Giá A2 indo', 'B1 indo', 'Giá B1 indo', 'B2 indo', 'Giá B2 indo', 'B3 indo', 'Giá B3 indo', 'C1 indo', 'Giá C1 indo', 'C2 indo', 'Giá C2 indo', 'Chợ 1 indo', 'Giá Chợ 1 indo', 'Chợ 2 indo', 'Giá Chợ 2 indo', 'Xơ indo', 'Giá Xơ indo',
            'Tổng KG', 'Thành Tiền', 'Ghi Chú'
        ];

        const dataRows = filteredReports.map((report) => {
            let rowData = [
                report.toa,
                report.driver
            ];
            quantityColumns.forEach(qtyCol => {
                rowData.push(getValueFromExpression(report[qtyCol]));
                rowData.push(getValueFromExpression(report[`Giá_${qtyCol}`]));
            });
            rowData.push(report.Tổng_KG);
            rowData.push(report.Thành_Tiền);
            rowData.push(report.Ghi_Chú);
            return rowData;
        });

        const totals = { 'Tổng_KG': 0, 'Thành_Tiền': 0 };
        quantityColumns.forEach(col => {
            totals[col] = 0;
        });

        filteredReports.forEach(report => {
            quantityColumns.forEach(col => {
                totals[col] += getValueFromExpression(report[col]);
            });
            totals['Tổng_KG'] += (parseFloat(report['Tổng_KG']) || 0);
            totals['Thành_Tiền'] += (parseFloat(report['Thành_Tiền']) || 0);
        });

        const totalRow = ['TỔNG CỘNG', ''];
        quantityColumns.forEach(qtyCol => {
            totalRow.push(totals[qtyCol]);
            totalRow.push(''); 
        });
        totalRow.push(totals['Tổng_KG']);
        totalRow.push(totals['Thành_Tiền']);
        totalRow.push(''); 

        const dataToExport = [headerRow, ...dataRows, totalRow];
        const ws = XLSX.utils.aoa_to_sheet(dataToExport);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'BaoCao');
        const safeDate = selectedDate.replace(/\//g, '-');
        XLSX.writeFile(wb, `bao_cao_ngay_${safeDate}.xlsx`);
    }

    function openTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        const clickedButton = document.querySelector(`.tab-button[onclick*="${tabId}"]`);
        if (clickedButton) clickedButton.classList.add('active');

        if (tabId === 'tab2') {
            filterReportsByDate();
        }
    }

    async function loadDataFromServer() {
        customAlert("Đang tải dữ liệu từ máy chủ...");
        await loadDriverList();
        await loadDailyReports();
        customAlert("Tải dữ liệu thành công!");
    }

    function loadBanksIntoDropdown() {
        const bankSelect = document.getElementById('bankName');
        bankSelect.innerHTML = '<option value="">Chọn Ngân Hàng</option>';
        const sortedBanks = banksData.sort((a, b) => a.name.localeCompare(b.name));
        sortedBanks.forEach(bank => {
            const option = document.createElement('option');
            option.value = bank.name;
            option.textContent = bank.name;
            bankSelect.appendChild(option);
        });
    }

    function sortDriverList() {
        driverList.sort((a, b) => {
            return a.driverNamePhone.localeCompare(b.driverNamePhone, 'vi', { sensitivity: 'base' });
        });
    }

    async function addOrUpdateDriver() {
        const driverNamePhone = document.getElementById('driverNamePhone').value.trim();
        const bankName = document.getElementById('bankName').value;
        const accountNumber = document.getElementById('accountNumber').value.trim();
        const accountName = document.getElementById('accountName').value.trim();
        
        if (!driverNamePhone || !bankName || !accountNumber || !accountName) {
            return customAlert('Vui lòng điền đầy đủ tất cả các thông tin!');
        }

        const hasLetter = /\p{L}/u.test(driverNamePhone);
        const hasNumber = /\d/.test(driverNamePhone);
        if (!hasLetter || !hasNumber) {
            return customAlert('Vui lòng điền Tên và SĐT lái');
        }
        
        let highlightIndex;

        if (editingIndexTab1 === -1) {
            const isDuplicate = driverList.some(driver => driver.driverNamePhone.trim().toLowerCase() === driverNamePhone.toLowerCase());
            if (isDuplicate) {
                return customAlert('Tên và SĐT Lái bạn nhập đã tồn tại trong danh sách');
            }
            driverList.push({ driverNamePhone, bankName, accountNumber, accountName, isVerified: false });
            customAlert('Đã thêm thông tin lái mới vào danh sách.');
        } else {
            const oldDriverName = driverList[editingIndexTab1].driverNamePhone;
            const newDriverData = { ...driverList[editingIndexTab1], driverNamePhone, bankName, accountNumber, accountName };
            driverList[editingIndexTab1] = newDriverData;

            if (oldDriverName !== driverNamePhone && oldDriverName) {
                customAlert('Tên lái đã thay đổi. Đang đồng bộ dữ liệu...');
                let updatedReportsCount = 0;
                dailyReports.forEach(report => {
                    if (report.driver === oldDriverName) {
                        report.driver = driverNamePhone;
                        updatedReportsCount++;
                    }
                });
                
                if(updatedReportsCount > 0) {
                    await saveDailyReportsToCSV();
                }

                customAlert(`Đã cập nhật thông tin lái và đồng bộ ${updatedReportsCount} báo cáo liên quan.`);
            } else {
                customAlert('Đã cập nhật thông tin lái.');
            }
            cancelEdit();
        }
        
        await saveDriverListToCSV();
        sortDriverList();
        
        highlightIndex = driverList.findIndex(d => d.driverNamePhone === driverNamePhone && d.accountNumber === accountNumber);

        renderDriverList();
        filterReportsByDate();
        clearDriverForm();

        if (highlightIndex !== -1) {
            setTimeout(() => {
                const isMobile = window.innerWidth <= 768;
                let itemToHighlight = null;

                if (isMobile) {
                    const mobileListContainer = document.getElementById('mobileDriverList');
                    if(mobileListContainer && mobileListContainer.children[highlightIndex]) {
                        itemToHighlight = mobileListContainer.children[highlightIndex];
                    }
                } else {
                    const tableBody = document.getElementById('driverListTableBody');
                    if(tableBody && tableBody.rows[highlightIndex]) {
                       itemToHighlight = tableBody.rows[highlightIndex];
                    }
                }

                if (itemToHighlight) {
                    itemToHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    itemToHighlight.classList.add('highlight-effect');
                    
                    setTimeout(() => {
                        itemToHighlight.classList.remove('highlight-effect');
                    }, 2000);
                }
            }, 100);
        }
    }

    function renderDriverList() {
        const isMobile = window.innerWidth <= 768;
        
        const searchTerm = isMobile ? document.getElementById('mobileDriverSearch').value : document.getElementById('desktopDriverSearch').value;
        const showOnlyUnverified = isMobile ? document.getElementById('mobileFilterUnverified').checked : document.getElementById('desktopFilterUnverified').checked;

        let filteredList = driverList;
        if (showOnlyUnverified) {
            filteredList = filteredList.filter(d => !d.isVerified);
        }
        if (searchTerm.trim().length > 0) {
            const lowercasedFilter = removeAccents(searchTerm.toLowerCase());
            filteredList = filteredList.filter(driver =>
                removeAccents(driver.driverNamePhone.toLowerCase()).includes(lowercasedFilter)
            );
        }
        
        const listToRender = filteredList.slice(0, driversCurrentlyShown);

        if (isMobile) {
            const mobileList = document.getElementById('mobileDriverList');
            mobileList.innerHTML = ""; // Clear only on initial render, not on load more
            listToRender.forEach((driver, displayIndex) => {
                const originalIndex = driverList.findIndex(d => d === driver);
                const item = document.createElement('div');
                item.className = 'mobile-driver-item';
                const verifiedIconHtml = driver.isVerified ? `<span class="verified-check">&#10004;</span>` : '';

                item.innerHTML = `
                    <div class="mobile-driver-main" onclick="toggleDriverDetails(this)">
                        <span class="stt">${displayIndex + 1}</span>
                        <span class="name">${driver.driverNamePhone}${verifiedIconHtml}</span>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div class="mobile-driver-details">
                        <div class="mobile-driver-info">
                            <p><strong>Ngân Hàng:</strong> <span>${driver.bankName}</span></p>
                            <p><strong>Số TK:</strong> <span>${driver.accountNumber}</span></p>
                            <p><strong>Chủ TK:</strong> <span>${driver.accountName}</span></p>
                        </div>
                        <div class="mobile-driver-actions">
                            <button class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); showDriverQR(${originalIndex})">Đánh dấu</button>
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); editDriver(${originalIndex})">Sửa</button>
                            <button class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); deleteDriver(${originalIndex})">Xóa</button>
                        </div>
                    </div>
                `;
                mobileList.appendChild(item);
            });
        } else {
            const tableBody = document.getElementById('driverListTableBody');
            tableBody.innerHTML = "";
            listToRender.forEach((driver, displayIndex) => {
                const originalIndex = driverList.findIndex(d => d === driver);
                const row = tableBody.insertRow();
                row.insertCell().textContent = displayIndex + 1;
                const nameCell = row.insertCell();
                nameCell.textContent = driver.driverNamePhone;
                if (driver.isVerified) {
                    const checkIcon = document.createElement('span');
                    checkIcon.className = 'verified-check';
                    checkIcon.innerHTML = '&#10004;';
                    checkIcon.title = 'Tài khoản đã được xác thực';
                    nameCell.appendChild(checkIcon);
                }
                row.insertCell().textContent = driver.bankName;
                row.insertCell().textContent = driver.accountNumber;
                row.insertCell().textContent = driver.accountName;
                const actionsCell = row.insertCell();
                actionsCell.className = 'flex gap-2 justify-center p-2';
                const verifyButton = document.createElement('button');
                verifyButton.textContent = 'Đánh dấu';
                verifyButton.className = 'bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-1 px-2 rounded-md whitespace-nowrap';
                verifyButton.onclick = () => showDriverQR(originalIndex);
                actionsCell.appendChild(verifyButton);
                const editButton = document.createElement('button');
                editButton.textContent = 'Sửa';
                editButton.className = 'bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md';
                editButton.onclick = () => editDriver(originalIndex);
                actionsCell.appendChild(editButton);
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Xóa';
                deleteButton.className = 'bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md';
                deleteButton.onclick = () => deleteDriver(originalIndex);
                actionsCell.appendChild(deleteButton);
            });
        }
        
        const loadMoreContainer = isMobile ? document.getElementById('mobileLoadMoreContainer') : document.getElementById('desktopLoadMoreContainer');
        if (listToRender.length < filteredList.length) {
            loadMoreContainer.style.display = 'flex';
        } else {
            loadMoreContainer.style.display = 'none';
        }
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

    function clearDriverForm() {
        document.getElementById('driverNamePhone').value = '';
        document.getElementById('bankName').value = '';
        document.getElementById('accountNumber').value = '';
        document.getElementById('accountName').value = '';
    }

    async function loadDriverList() {
        try {
            const response = await fetch(baseUrl + 'load_drivers.php');
            if (!response.ok) throw new Error('Network response was not ok.');
            const result = await response.json();
            if (result && result.success) {
                driverList = result.data.map(d => ({...d, isVerified: d.isVerified === 'true'})) || [];
                sortDriverList();
                renderDriverList();
            } else {
                console.error("Lỗi khi tải danh sách lái:", result.message);
                driverList = [];
            }
        } catch (error) {
            console.error("Lỗi khi fetch danh sách lái từ máy chủ:", error);
            driverList = [];
            renderDriverList();
        }
    }

    function editDriver(index) {
        document.getElementById('add-report-title').classList.remove('hidden');
        document.getElementById('dailyReportForm').classList.remove('hidden');
        editingIndexTab1 = index;
        const driverToEdit = driverList[index];
        document.getElementById('driverNamePhone').value = driverToEdit.driverNamePhone;
        document.getElementById('bankName').value = driverToEdit.bankName;
        document.getElementById('accountNumber').value = driverToEdit.accountNumber;
        document.getElementById('accountName').value = driverToEdit.accountName;
        const addUpdateButton = document.getElementById('addUpdateButton');
        addUpdateButton.textContent = 'Cập nhật';
        addUpdateButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        addUpdateButton.classList.add('bg-green-600', 'hover:bg-green-700');
        document.getElementById('cancelButton').classList.remove('hidden');
        document.getElementById('driver-form-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function cancelEdit() {
        editingIndexTab1 = -1;
        clearDriverForm();
        const addUpdateButton = document.getElementById('addUpdateButton');
        addUpdateButton.textContent = 'Thêm vào danh sách';
        addUpdateButton.classList.remove('bg-green-600', 'hover:bg-green-700');
        addUpdateButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
        document.getElementById('cancelButton').classList.add('hidden');
    }

    async function deleteDriver(index) {
        const driverToDelete = driverList[index];
        if (!driverToDelete) return;

        customAlert("Đang kiểm tra dữ liệu liên quan...");

        try {
            const response = await fetch(baseUrl + "check_driver_usage.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ driverNamePhone: driverToDelete.driverNamePhone })
            });

            if (!response.ok) throw new Error('Lỗi máy chủ khi kiểm tra.');
            
            const result = await response.json();
            
            if (!result.success) throw new Error(result.message);

            if (result.canDelete) {
                confirm(`Bạn có chắc chắn muốn xóa lái "${driverToDelete.driverNamePhone}" không?`).then(async confirmed => {
                    if (confirmed) {
                        driverList.splice(index, 1);
                        await saveDriverListToCSV();
                        sortDriverList();
                        driversCurrentlyShown = DRIVERS_PER_PAGE; // Reset pagination
                        renderDriverList();
                        customAlert('Đã xóa thông tin lái khỏi danh sách.');
                    }
                });
            } else {
                showDriverUsageModal(result.reports, index);
            }

        } catch (error) {
            console.error("Lỗi khi kiểm tra việc sử dụng của lái:", error);
            customAlert("Lỗi: " + error.message);
        }
    }
    
    async function updateReportDriver(originalReport, newDriverName, cardElement) {
        const reportIndex = dailyReports.findIndex(r => 
            r.date === originalReport.date &&
            r.toa == originalReport.toa &&
            r.driver === originalReport.driver
        );

        if (reportIndex === -1) {
            customAlert("Lỗi: Không tìm thấy báo cáo gốc để cập nhật.");
            return;
        }

        dailyReports[reportIndex].driver = newDriverName;
        
        await saveDailyReportsToCSV();

        cardElement.style.transition = 'opacity 0.5s, transform 0.5s';
        cardElement.style.opacity = '0';
        cardElement.style.transform = 'scale(0.95)';
        setTimeout(() => {
            cardElement.remove();
            const listContainer = document.getElementById('conflictingReportsList');
            if (!listContainer.querySelector('.report-card')) {
                closeModal('driverUsageModal');
                customAlert("Tất cả các toa đã được cập nhật. Bây giờ bạn có thể xóa Lái này.");
            }
        }, 500);
        
        customAlert(`Đã cập nhật lái cho toa ngày ${originalReport.date}.`);
        
        filterReportsByDate();
    }

    function showDriverUsageModal(conflictingReports, driverToDeleteIndex) {
        const modal = document.getElementById('driverUsageModal');
        const listContainer = document.getElementById('conflictingReportsList');
        listContainer.innerHTML = '';

        const otherDrivers = driverList.filter((_, i) => i !== driverToDeleteIndex);

        conflictingReports.forEach((report, idx) => {
            const reportCard = document.createElement('div');
            reportCard.className = 'report-card';

            let reportDetailsHtml = '';
            quantityColumns.forEach(qtyCol => {
                const qty = getValueFromExpression(report[qtyCol]);
                if (qty > 0) {
                    const price = getValueFromExpression(report[`Giá_${qtyCol}`]);
                    const itemName = qtyCol.replace(/_/g, ' ');
                    reportDetailsHtml += `
                        <div class="report-item">
                            <span class="report-item-name">${itemName}</span>
                            <span class="report-item-details">${qty.toLocaleString('vi-VN')} kg &times; ${price.toLocaleString('vi-VN')}đ</span>
                        </div>
                    `;
                }
            });

            const selectId = `new-driver-select-${idx}`;
            reportCard.innerHTML = `
                <div class="report-card-header">
                    <h4>Toa ${report.toa} - Ngày: ${report.date}</h4>
                    <div class="flex items-center">
                        <span class="font-semibold text-indigo-600 mr-3">Tổng: ${parseFloat(report.Thành_Tiền).toLocaleString('vi-VN')}đ</span>
                        <span class="toggle-arrow" style="transform: rotate(-90deg);">▼</span>
                    </div>
                </div>
                <div class="collapsible-section collapsed">
                    <div class="report-card-body">
                        ${reportDetailsHtml || '<div class="text-gray-500 p-2">Không có chi tiết hàng.</div>'}
                    </div>
                    <div class="report-card-footer">
                        <label for="${selectId}" class="text-gray-700">Đổi tên lái khác vào toa:</label>
                        <select id="${selectId}">
                            <option value="">-- Chọn lái mới --</option>
                            ${otherDrivers.map(d => `<option value="${d.driverNamePhone}">${d.driverNamePhone}</option>`).join('')}
                        </select>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">Lưu Thay Đổi</button>
                    </div>
                </div>
            `;
            
            listContainer.appendChild(reportCard);
            
            const header = reportCard.querySelector('.report-card-header');
            const collapsible = reportCard.querySelector('.collapsible-section');
            const arrow = header.querySelector('.toggle-arrow');

            header.addEventListener('click', () => {
                const isCollapsed = collapsible.classList.toggle('collapsed');
                arrow.style.transform = isCollapsed ? 'rotate(-90deg)' : 'rotate(0deg)';
            });

            reportCard.querySelector('.collapsible-section').addEventListener('click', (e) => {
                e.stopPropagation();
            });

            reportCard.querySelector('.report-card-footer button').addEventListener('click', () => {
                const selectEl = reportCard.querySelector('select');
                const newDriver = selectEl.value;
                if (!newDriver) {
                    customAlert("Vui lòng chọn một Lái mới để chuyển sang.");
                    return;
                }
                updateReportDriver(report, newDriver, reportCard);
            });
        });

        modal.style.display = 'block';
    }

    // --- DAILY REPORT FUNCTIONS ---
    
    // NEW: Driver Search UI Functions
    function renderDriverSearchResults(searchTerm = '') {
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
    
    function handleDriverSelection(selectedDriver) {
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
            const newReport = { date: selectedDate, driver: selectedDriver, Ghi_Chú: '' };
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
            recalculateDailyToaNumbers(selectedDate);
            liveEditingReport = newReport;
            
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


    function getValueFromExpression(value) {
        if (typeof value === 'string' && value.includes('+')) {
            return value.split('+').reduce((sum, part) => sum + (Number(part) || 0), 0);
        }
        return parseFloat(value) || 0;
    }

    function calculateRowTotals() {
        let totalKG = 0;
        let totalAmount = 0;
        allCalcColumns.forEach(colId => {
            const element = document.getElementById(colId);
            if(element && quantityColumns.includes(colId)){
                totalKG += getValueFromExpression(element.value);
            }
        });
         quantityColumns.forEach(qtyColId => {
            const qtyEl = document.getElementById(qtyColId);
            const priceEl = document.getElementById(`Giá_${qtyColId}`);
            if(qtyEl && priceEl) {
                const qty = getValueFromExpression(qtyEl.value);
                const price = getValueFromExpression(priceEl.value);
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
    
    function scrollToReportForm() {
        document.getElementById('add-report-title').scrollIntoView({ behavior: 'smooth' });
        if (!liveEditingReport) {
            document.getElementById('dailyReportDriverSearch').focus();
        }
    }

    async function handleAddNewRowClick() {
        document.getElementById('add-report-title').classList.remove('hidden');
        document.getElementById('dailyReportForm').classList.remove('hidden');
        if (liveEditingReport) {
            await handleAutoSave(true);
        }
        resetFormForNewEntry();
        scrollToReportForm();
    }

    function lockForm(shouldLock) {
        const form = document.getElementById('dailyReportForm');
        const inputs = form.querySelectorAll('#Ghi_Chú, .input-group input');
        inputs.forEach(input => {
            input.disabled = shouldLock;
        });
    }

    function bindFormToReport(report) {
        document.getElementById('dailyReportDriverSearch').value = report.driver;
        document.getElementById('Ghi_Chú').value = report.Ghi_Chú || '';
        allCalcColumns.forEach(col => {
            const input = document.getElementById(col);
            if (input) {
                input.value = report[col] || '';
            }
        });
        calculateRowTotals();
    }

    function updateLiveReportFromForm() {
        if (!liveEditingReport) return;
        liveEditingReport.driver = document.getElementById('dailyReportDriverSearch').value;
        liveEditingReport.Ghi_Chú = document.getElementById('Ghi_Chú').value;
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
    
    function unlockDriverSelection() {
        const changeBtn = document.getElementById('changeDriverBtn');
        const searchInput = document.getElementById('dailyReportDriverSearch');
        
        lockForm(true);
        
        searchInput.disabled = false;
        changeBtn.classList.add('hidden');
        searchInput.focus();
        searchInput.select();
    }

    async function handleAutoSave(silent = false) {
        if (!liveEditingReport || isSaving) return;
        
        isSaving = true;
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
        isSaving = false;
    }

    function handleFormInput(e) {
        if (e.target.id === 'dailyReportDriverSearch') {
            return;
        }

        clearTimeout(saveTimeout);
        if (liveEditingReport) {
            saveTimeout = setTimeout(handleAutoSave, 1000);
        }

        const input = e.target;
        const isCalcInput = allCalcColumns.includes(input.id);
        if (isCalcInput) {
            let value = input.value;
            const sanitizedValue = value.replace(/[^0-9\+]/g, '');
            if (value !== sanitizedValue) {
                input.value = sanitizedValue;
            }
            updateCalculationPreview(input);
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

    function renderDailyReportTable(reports) {
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
            const mobileContainer = document.getElementById('mobileReportList');
            mobileContainer.innerHTML = '';
            reports.forEach((report) => {
                const originalIndex = dailyReports.findIndex(r => r === report);
                const item = document.createElement('div');
                item.className = 'mobile-report-item';
                const ghiChuHtml = report.Ghi_Chú ? `<div class="mobile-report-ghi-chu"><strong>Ghi chú:</strong> ${report.Ghi_Chú}</div>` : '';
                
                const driver = driverList.find(d => d.driverNamePhone === report.driver);
                const verifiedIconHtml = (driver && driver.isVerified) ? `<span class="verified-check">&#10004;</span>` : '';

                let paymentButtonHtml = `<button class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-md flex items-center justify-center" onclick="event.stopPropagation(); showPaymentModal(${originalIndex})">Thanh Toán`;
                if (report.isPaid) {
                     paymentButtonHtml += ` <span class="verified-check">&#10004;</span>`;
                }
                paymentButtonHtml += `</button>`;
                
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
                                    <span class="font-semibold text-gray-800">${(parseFloat(report['Tổng_KG']) || 0).toLocaleString('vi-VN')}</span>
                                </div>
                                <div class="text-center">
                                    <span class="block text-xs text-gray-500">Thành Tiền</span>
                                    <span class="font-semibold text-indigo-600">${(parseFloat(report['Thành_Tiền']) || 0).toLocaleString('vi-VN')}</span>
                                </div>
                            </div>
                        </div>
                        <span class="mobile-report-toggle-icon self-start ml-2">▼</span>
                    </div>
                    <div class="mobile-report-details">
                        <div class="mobile-report-actions">
                             <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); showInvoice(${originalIndex})">Hóa Đơn</button>
                             ${paymentButtonHtml}
                             <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); editDailyReport(dailyReports[${originalIndex}])">Sửa</button>
                             <button class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); deleteDailyReport(dailyReports[${originalIndex}])">Xóa</button>
                        </div>
                        ${ghiChuHtml}
                    </div>
                `;
                mobileContainer.appendChild(item);
            });
        } else {
            const tableBody = document.getElementById('dailyReportTableBody');
            tableBody.innerHTML = '';
            
            reports.forEach((report) => {
                const originalIndex = dailyReports.findIndex(r => r === report);
                const row = tableBody.insertRow();

                row.insertCell().textContent = report.toa || '?';
                
                const laiCell = row.insertCell();
                const driver = driverList.find(d => d.driverNamePhone === report.driver);
                let laiHtml = report.driver;
                if (driver && driver.isVerified) {
                    laiHtml += ` <span class="verified-check" title="Tài khoản đã được xác thực">&#10004;</span>`;
                }
                laiCell.innerHTML = laiHtml;
                
                row.insertCell().textContent = (parseFloat(report['Tổng_KG']) || 0).toLocaleString('vi-VN');

                const thanhTienCell = row.insertCell();
                thanhTienCell.textContent = (parseFloat(report['Thành_Tiền']) || 0).toLocaleString('vi-VN');
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

                const paymentButton = document.createElement('button');
                paymentButton.innerHTML = 'Thanh Toán';
                if (report.isPaid) {
                    paymentButton.innerHTML += ' <span class="verified-check">&#10004;</span>';
                }
                paymentButton.className = 'bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-bold py-1 px-2 rounded-md flex items-center';
                paymentButton.onclick = () => showPaymentModal(originalIndex);
                buttonContainer.appendChild(paymentButton);
                
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
            });
        }
        updateSummaryTable(reports);
    }
    
    function resetFormForNewEntry() {
        liveEditingReport = null;
        const searchInput = document.getElementById('dailyReportDriverSearch');
        searchInput.value = '';
        document.getElementById('Ghi_Chú').value = '';
        
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

    async function loadDailyReports() {
        try {
            const response = await fetch(baseUrl + 'load_reports.php');
            if (!response.ok) throw new Error('Network response was not ok for reports.');
            const result = await response.json();
            if (result && result.success) {
                dailyReports = result.data.map(r => ({ ...r, isPaid: r.isPaid === 'true' })) || [];
            } else {
                console.error("Lỗi khi tải báo cáo:", result.message);
                dailyReports = [];
            }
        } catch (error) {
            console.error("Lỗi khi fetch báo cáo từ máy chủ:", error);
            dailyReports = [];
        }
    }

    function calculateTotalKG(report) {
        return quantityColumns.reduce((total, col) => total + getValueFromExpression(report[col]), 0);
    }

    function calculateTotalAmount(report) {
        return quantityColumns.reduce((total, col) => {
            const qty = getValueFromExpression(report[col]);
            const price = getValueFromExpression(report[`Giá_${col}`]);
            return total + (qty * price);
        }, 0);
    }

    function clearDailyReportForm() {
        document.getElementById('dailyReportDriverSearch').value = '';
        document.getElementById('Ghi_Chú').value = '';
        const numberInputs = document.querySelector('#dailyReportForm .input-group');
        const allNumberDivs = numberInputs.querySelectorAll('input');
        allNumberDivs.forEach(input => input.value = '');
        
        const calculationResults = document.querySelectorAll('#dailyReportForm .calculation-result');
        calculationResults.forEach(span => span.style.display = 'none');
        calculateRowTotals();
    }

    async function editDailyReport(reportToEdit) {
        document.getElementById('add-report-title').classList.remove('hidden');
        document.getElementById('dailyReportForm').classList.remove('hidden');
        if (liveEditingReport) {
            if (liveEditingReport === reportToEdit) {
                customAlert("Bạn đang sửa toa này");
                return;
            }
            await handleAutoSave(true);
        }
        liveEditingReport = reportToEdit;
        document.getElementById('add-report-title').textContent = `Sửa Dữ Liệu Toa ${reportToEdit.toa}`;
        bindFormToReport(reportToEdit);
        showAllCalculationPreviews();
        lockForm(false);
        document.getElementById('dailyReportDriverSearch').disabled = true;
        document.getElementById('changeDriverBtn').classList.remove('hidden');
        document.getElementById('add-report-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function cancelDailyReportEdit() {
        liveEditingReport = null;
        clearDailyReportForm();
        lockForm(true);
        unlockDriverSelection();
        document.getElementById('add-report-title').textContent = 'Thêm Dữ Liệu Báo Cáo';
    }

    async function deleteDailyReport(reportToDelete) {
        confirm('Bạn có chắc chắn muốn xóa dòng này?').then(async result => {
            if (result) {
                const indexToDelete = dailyReports.findIndex(report => report === reportToDelete);
                if (indexToDelete > -1) {
                    const dateOfDeletedReport = dailyReports[indexToDelete].date;
                    dailyReports.splice(indexToDelete, 1);
                    recalculateDailyToaNumbers(dateOfDeletedReport);
                    
                    const saveSuccess = await saveDailyReportsToCSV();
                    if(saveSuccess) {
                       customAlert('Đã xóa báo cáo.');
                       filterReportsByDate(); 
                    } else {
                       customAlert('Lỗi: Không thể xóa, vui lòng kiểm tra lại mạng.');
                       await loadDailyReports();
                       filterReportsByDate();
                    }
                }
            }
        });
    }

    function filterReportsByDate() {
        const selectedDate = document.getElementById('reportDate').value;
        const filtered = dailyReports.filter(report => report.date === selectedDate);
        filtered.sort((a, b) => (a.toa || 0) - (b.toa || 0)); // Sort by toa number
        renderDailyReportTable(filtered);
    }

    function updateSummaryTable(reports) {
        const summaryTableBody = document.getElementById('summaryTableBody');
        summaryTableBody.innerHTML = '';
        if (reports.length === 0) {
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
        reports.forEach(report => {
            quantityColumns.forEach(qtyCol => {
                const kg = getValueFromExpression(report[qtyCol]);
                const price = getValueFromExpression(report[`Giá_${qtyCol}`]);
                const amount = kg * price;
                if (qtyCol.includes('_Thái')) {
                    thaiTotals.types[qtyCol].kg += kg;
                    thaiTotals.types[qtyCol].amount += amount;
                    thaiTotals.totalKg += kg;
                    thaiTotals.totalAmount += amount;
                } else if (qtyCol.includes('_indo')) {
                    indoTotals.types[qtyCol].kg += kg;
                    indoTotals.types[qtyCol].amount += amount;
                    indoTotals.totalKg += kg;
                    indoTotals.totalAmount += amount;
                }
            });
        });
        const createRow = (name, kg, amount, isHeader = false, isTotal = false) => {
            const row = summaryTableBody.insertRow();
            if (isHeader) {
                const cell = row.insertCell();
                cell.colSpan = 3;
                cell.textContent = name;
                cell.className = 'text-left font-bold text-lg bg-gray-200 p-3';
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

    function openSummaryModal() {
        document.getElementById('summaryModal').style.display = 'block';
    }
    
    async function saveDailyReportsToCSV() {
        try {
            const response = await fetch(baseUrl + "save_reports.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ reports: dailyReports })
            });
            if (!response.ok) return false;
            const result = await response.json();
            return result.success;
        } catch (error) {
            console.error("Lỗi khi gửi dữ liệu đến máy chủ:", error);
            return false;
        }
    }

    async function saveDriverListToCSV() {
        if (driverList.length === 0) console.log("Danh sách lái trống, đang lưu tệp rỗng.");
        const fileName = "driver_list.csv";
        try {
            const response = await fetch(baseUrl + "save_drivers.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ drivers: driverList, fileName: fileName })
            });
            const result = await response.json();
            if (!result.success) customAlert(`Lỗi khi lưu danh sách lái: ${result.message}`);
        } catch (error) {
            console.error("Lỗi khi gửi dữ liệu lái đến máy chủ:", error);
            customAlert("Không thể kết nối đến máy chủ để lưu danh sách lái.");
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
    function confirm(e){return new Promise(t=>{const o=document.getElementById("custom-confirm");o&&o.remove();const n=document.createElement("div");n.id="custom-confirm",n.className="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50",n.innerHTML=`<div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full"><p class="text-lg font-semibold text-gray-800 mb-4">${e}</p><div class="flex justify-end gap-3"><button id="confirm-cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors duration-200">Hủy</button><button id="confirm-ok" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">OK</button></div></div>`,document.body.appendChild(n),document.getElementById("confirm-ok").onclick=()=>{n.remove(),t(!0)},document.getElementById("confirm-cancel").onclick=()=>{n.remove(),t(!1)}})}
    
    document.addEventListener("DOMContentLoaded",async()=>{
        const reportForm = document.getElementById('dailyReportForm');
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

        reportForm.addEventListener('input', handleFormInput);
        
        // --- NEW DRIVER SEARCH EVENT LISTENERS ---
        const driverSearchInput = document.getElementById('dailyReportDriverSearch');
        const driverSearchResults = document.getElementById('driverSearchResults');

        driverSearchInput.addEventListener('input', () => {
             renderDriverSearchResults(driverSearchInput.value);
        });
        driverSearchInput.addEventListener('focus', () => {
             renderDriverSearchResults(driverSearchInput.value);
        });
        document.addEventListener('click', (e) => {
            if (!driverSearchInput.contains(e.target) && !driverSearchResults.contains(e.target)) {
                driverSearchResults.classList.add('hidden');
            }
        });
        
        document.getElementById('changeDriverBtn').addEventListener('click', unlockDriverSelection);
        
        function handleFilterChange() {
            driversCurrentlyShown = DRIVERS_PER_PAGE;
            renderDriverList();
        }

        ['mobileDriverSearch', 'desktopDriverSearch'].forEach(id => {
            const searchInput = document.getElementById(id);
            if(searchInput) searchInput.addEventListener('input', handleFilterChange);
        });
        
        ['mobileFilterUnverified', 'desktopFilterUnverified'].forEach(id => {
            const filterCheckbox = document.getElementById(id);
            if(filterCheckbox) filterCheckbox.addEventListener('change', handleFilterChange);
        });

        function handleLoadMore() {
            driversCurrentlyShown += DRIVERS_PER_PAGE;
            renderDriverList();
        }
        
        ['mobileLoadMoreBtn', 'desktopLoadMoreBtn'].forEach(id => {
            const loadMoreBtn = document.getElementById(id);
            if(loadMoreBtn) loadMoreBtn.addEventListener('click', handleLoadMore);
        });

        const observer = new IntersectionObserver((entries) => {
            const footer = document.getElementById('sticky-totals-footer');
            if (entries[0].isIntersecting) {
                footer.classList.remove('hidden', 'translate-y-full');
            } else {
                footer.classList.add('translate-y-full');
                setTimeout(() => footer.classList.add('hidden'), 300);
            }
        }, { threshold: 0.1 });
        observer.observe(reportForm);

        window.onclick = function(event) {
            const qrModal = document.getElementById('qrModal');
            const invoiceModal = document.getElementById('invoiceModal');
            const driverUsageModal = document.getElementById('driverUsageModal');
            const summaryModal = document.getElementById('summaryModal');
            // Check if the click is outside the modals AND outside the search components
            const isClickOutsideSearch = !driverSearchInput.contains(event.target) && !driverSearchResults.contains(event.target);
            
            if (event.target == qrModal) closeModal('qrModal');
            if (event.target == invoiceModal) closeModal('invoiceModal');
            if (event.target == driverUsageModal) closeModal('driverUsageModal');
            if (event.target == summaryModal) closeModal('summaryModal');
            if (isClickOutsideSearch) {
                 driverSearchResults.classList.add('hidden');
            }
        }

        await loadDataFromServer();
        loadBanksIntoDropdown();
        lockForm(true); // Lock number fields initially
        document.getElementById('dailyReportDriverSearch').disabled = true; // Start with driver search disabled
        flatpickr("#reportDate",{dateFormat:"d/m/Y",locale:"vn",defaultDate:new Date,onChange:function(e,t,o){filterReportsByDate()}});
        openTab("tab1");
    });
    </script>
</body>
</html>