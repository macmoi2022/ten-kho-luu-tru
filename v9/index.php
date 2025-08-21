<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'generate_qr') {
        header('Content-Type: application/json');

        $clientId = '456b6df3-1ccf-4e4c-a114-c4609ee2abdb';
        $apiKey = 'd854ee9e-2d4f-4581-b25e-eeb112709f81';

        $data = [
            'acqId' => $input['bin'],
            'accountNo' => $input['accountNumber'],
            'accountName' => $input['accountName'],
            'amount' => $input['amount'],
            'addInfo' => $input['description'],
            'format' => 'text',
            'template' => 'qr_only'
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.vietqr.io/v2/generate',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array(
            'x-client-id: ' . $clientId,
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo json_encode(['code' => '99', 'desc' => 'cURL Error: ' . $err]);
        } else {
            echo $response;
        }
        exit();
    }
}
// Đặt thời gian session là 8 tiếng (8 * 3600 = 28800 giây)
ini_set('session.gc_maxlifetime', 28800);
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
$user_type = $_SESSION['type'];
$username = $_SESSION['username'];
?>
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
        body{font-family:'Inter',sans-serif;background-color:#f3f4f6;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;padding:2rem 1rem}.tab-content{display:none}.tab-content.active{display:block}.tab-button.active{background-color:#4f46e5;color:#fff}table{width:100%;border-collapse:collapse;margin-top:1.5rem}th,td{border:1px solid #e5e7eb;text-align:center;vertical-align:middle}th{background-color:#f9fafb;color:#4b5563}tr:nth-child(even){background-color:#f3f4f6}.input-group{display:flex;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem}.input-group div{flex:1 1 calc(50% - .5rem)}@media (min-width:768px){.input-group div{flex:1 1 calc(33.33% - .66rem)}}#dailyReportForm label, #driverForm label{display:block;font-weight:500;color:#374151;text-align:left}#dailyReportForm input[type=text], #dailyReportForm select, #driverForm input, #driverForm select {width:100%;padding:.75rem;border:1px solid #d1d5db;border-radius:.5rem;font-size:1rem;color:#1f2937;background-color:#fff;transition:border-color .2s ease-in-out,box-shadow .2s ease-in-out}#dailyReportForm input[type=text]:focus, #dailyReportForm select:focus, #driverForm input:focus, #driverForm select:focus{border-color:#6366f1;outline:0;box-shadow:0 0 0 3px rgba(99,102,241,.2)}#dailyReportForm input:disabled, #dailyReportForm select:disabled, #driverForm input:disabled, #driverForm select:disabled{background-color:#e5e7eb;cursor:not-allowed;}.thai-cell{background-color:#ffd966!important}.indo-cell{background-color:#dd7e6b!important}.td-thai{background-color:#ffd966}.td-indo{background-color:#dd7e6b}tr:nth-child(even) .td-thai{background-color:#ffd966}tr:nth-child(even) .td-indo{background-color:#dd7e6b}
        input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}input[type=number]{-moz-appearance:textfield}
        .flatpickr-input{text-align:center;padding:.35rem .35rem!important;border:2px solid #d1d5db!important;border-radius:.5rem!important;transition:all .2s ease-in-out!important;background-color:#f9fafb!important;max-width:135px!important;}.flatpickr-input:focus{border-color:#4f46e5!important;box-shadow:0 0 0 3px rgba(79,70,229,.2)!important;background-color:#fff!important}
        .verified-check{color:#16a34a;display:inline-block;margin-left:8px;font-weight:bold;text-shadow:-1px -1px 0 #fff, 1px -1px 0 #fff, -1px 1px 0 #fff, 1px 1px 0 #fff;}
        .qr-modal, .invoice-modal, .driver-usage-modal, .summary-modal, .user-modal, .deeplink-modal, .driver-form-modal, #reportFormModal {display:none;position:fixed;z-index:100;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgba(0,0,0,0.6);}
        .modal-content{height: fit-content;background-color:#fff;margin:auto;padding:24px;border:1px solid #888;width:90%;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.3);}
        .qr-modal .modal-content, .deeplink-modal .modal-content{max-width:400px;text-align:center; transform: translateZ(0);}
        .invoice-modal .modal-content{max-width:95%; width: auto; text-align:left; position: relative;}
        .driver-usage-modal .modal-content {max-width: 700px;}
        .summary-modal .modal-content {max-width: 800px;}
        .user-modal .modal-content {max-width: 500px;}
        .driver-form-modal .modal-content {max-width: 600px;}
        #reportFormModal .modal-content {max-width: 900px;}
        .close-modal{color:#aaa;float:right;font-size:28px;font-weight:bold;cursor:pointer;}.close-modal:hover,.close-modal:focus{color:#000;text-decoration:none;}
        .highlight-effect { animation: highlight-fade 2s ease-out; }
        @keyframes highlight-fade {
            from { background-color: #fef08a; }
            to { background-color: transparent; }
        }
        .paying-highlight {
            background-color: #dbeafe !important;
            transition: background-color 0.3s ease-in-out;
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
        #tab2 thead th, #tab3 thead th { position: -webkit-sticky; position: sticky; top: 0; z-index: 20; }
        #tab2 td, #tab2 th, #tab3 td, #tab3 th { white-space: nowrap; }
        #driverListTableContainer th, #driverListTableContainer td { white-space: nowrap; }
        
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
        #desktopDriverSearch { width: 100%; }
        
        .sticky-tabs {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 50;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .sticky-tabs .tab-button {
            flex-grow: 1;
            text-align: center;
        }

        .modal-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            flex-shrink: 0;
        }
        .modal-header-custom h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }
        .modal-header-custom .close-modal {
            font-size: 2rem;
            font-weight: 200;
            line-height: 1;
            padding: 0;
            margin: 0;
            float: none;
        }
        .modal-body-custom {
            overflow-y: auto;
            padding: 1rem;
            flex-grow: 1;
        }

        @media (max-width: 768px) {
            body {padding: 0rem}
			.modal-actions {display:none!important;}
            .p-6 {padding: .1rem !important;}
			.border {//border-width: 1px !important;}
            .mb-6 {margin-bottom: 1rem;}
            .mt-8 {margin: 1.5rem 0rem;}
            h1 {padding-top: 1rem}
            .mobile-hidden { display: none !important; }
            .tab-button {
                font-size: 0.95rem;
                padding-top: 0.9rem;
                padding-bottom: 0.9rem;
            }
            .responsive-admin-table th, .responsive-admin-table td {
                white-space: normal;
                font-size: 0.75rem;
                padding-left: 0.5rem;
                padding-right: 0.5rem;
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
            #mobileActionButtons {
                position: sticky;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 45;
                background-color: rgba(255, 255, 255, 0.95);
                padding: 0.75rem;
                margin-top: 1rem;
                border-top: 1px solid #e2e8f0;
                box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.08);
                justify-content: space-evenly;
            }
            #mobileActionButtons button {
                width: 45%;
                white-space: nowrap;
                font-size: clamp(0.75rem, 4vw, 0.9rem);
                text-align: center;
            }
            #tab2.active {
                padding-bottom: 90px !important;
            }
            /* START: CSS CHO POPUP TRÀN MÀN HÌNH DI ĐỘNG */
            .qr-modal .modal-content, .summary-modal .modal-content, .invoice-modal .modal-content, .driver-form-modal .modal-content, .user-modal .modal-content, .driver-usage-modal .modal-content, #reportFormModal .modal-content {
                margin: 0;
                width: 100%;
                height: 100%;
                max-width: 100%;
                max-height: 100%;
                border-radius: 0;
                border: none;
                display: flex;
                flex-direction: column;
                padding: 0;
                box-sizing: border-box;
                position: relative;
            }
            .modal-header-custom {
                padding: 0.75rem 1rem;
            }
            .modal-header-custom h3 {
                font-size: 1.1rem;
            }
            .modal-header-custom .close-modal {
                font-size: 2.5rem;
            }
            .modal-body-custom {
                padding: 1rem;
            }
            /* END: CSS CHO POPUP TRÀN MÀN HÌNH DI ĐỘNG */
        }
		@media (min-width: 640px) {	
				.sm\:w-1\/2 {width: 35% !important;}
				.pt-3 {padding-top: 1.75rem !important;}
		}
	
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

        /* --- INVOICE STYLES --- */
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
        .invoice-logo {
            width: 112px;
            height: 112px;
            object-fit: cover;
        }
		.invoice-calculation-grid {
			display: grid; grid-template-columns: auto 1fr;
			grid-template-rows: auto auto;
			align-items: center;
			justify-items: center;
			gap: 2px;
			height: 100%;
		}
        .invoice-calculation-numbers {
            text-align: center;
            grid-column: 2 / 3;
            border-bottom: 2px solid #4b5563;
            width: 100%;
            padding-bottom: 3mm !important;
            margin-bottom: 2px;
        }
        .invoice-calculation-numbers span {
            display: block;
            font-size: 0.8rem;
        }
		.invoice-single-number-cell {
			display: flex; align-items: center;
			justify-content: center;
			height: 100%;
		}
        #invoice-content-wrapper .invoice-table {
            table-layout: fixed;
            width: 100%;
        }
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
        .shrunk-invoice .invoice-calculation-grid { gap: 1px; }
        .shrunk-invoice .invoice-calculation-numbers { padding-bottom: 3mm !important; margin-bottom: 1px; border-bottom-width: 1px; }
        .shrunk-invoice .invoice-calculation-numbers span { font-size: 0.55rem; }
        .shrunk-invoice .invoice-single-number-cell { min-height: initial; }
        .shrunk-invoice .grand-total { font-size: 0.9rem; }
        .shrunk-invoice .grand-total .total-amount { font-size: 1.1rem; }
        .shrunk-invoice .signature { font-size: 0.8rem; margin-top: 2rem; }
        .quantity-cell-valign { vertical-align: bottom !important; }
        .quantity-cell-valign-middle { vertical-align: middle !important; }
        
        .invoice-calculation-grid .col-start-1 {
            margin-left: 2mm !important;
        }

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
        
        @media (max-width: 768px) {					
            .invoice-modal .modal-header .modal-actions {
                display: none;
            }
            #invoice-content-wrapper {
                -webkit-overflow-scrolling: touch;
            }
        }
        
        #driverSearchResults {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 110; /* Ensure it's above the modal content */
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
            background-color: #eef2ff;
        }
        .driver-search-item:not(:last-child) {
            border-bottom: 1px solid #e5e7eb;
        }
        .vua-selector {
            background-color: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            cursor: pointer;
        }
        #qrCodeContainer canvas, #qrCodeContainer img {
            margin: 0 auto;
        }
        .qr-instruction {
            margin-top: 1rem;
            padding: 0.75rem;
            background-color: #eef2ff;
            color: #4338ca;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="bg-white p-6 rounded-xl shadow-lg w-full max-w-7xl">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Vựa Mít Ngọc Anh HD68</h1>
            <div class="mt-3">
                <span class="text-gray-700 align-middle">Xin chào, <span class="font-bold"><?php echo htmlspecialchars($username); ?></span>!</span>
                <a href="logout.php" class="ml-1 inline-block bg-red-500 hover:bg-red-600 text-white font-bold text-sm py-1 px-2 rounded-lg shadow-md transition-transform transform hover:scale-105">
                    Đăng xuất
                </a>
            </div>
        </div>

        <div class="sticky-tabs flex flex-wrap justify-center mb-3 border-b border-gray-200">
            <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-sm sm:text-base font-bold text-gray-700 focus:outline-none transition-colors duration-200 active" onclick="openTab('tab1')">Thông Tin Lái</button>
            <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-sm sm:text-base font-bold text-gray-700 focus:outline-none transition-colors duration-200" onclick="openTab('tab2')">Nhập Toa</button>
            <?php if ($user_type === 'quanly'): ?>
            <button class="tab-button whitespace-nowrap px-4 sm:px-6 py-3 rounded-t-lg text-sm sm:text-base font-bold text-gray-700 focus:outline-none transition-colors duration-200" onclick="openTab('tab3')">Quản Lý</button>
            <?php endif; ?>
        </div>
        <div id="tab1" class="tab-content active p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-700">Mục Thông Tin Lái</h2>
                <?php if ($user_type === 'quanly'): ?>
                <div class="ml-auto">
                    <select id="vuaSelectorTab1" class="vua-selector">
                        <option value="Data_vua7/">Vựa 7</option>
                        <option value="Data_vua9/">Vựa 9</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <p class="text-gray-600 leading-relaxed mb-4 text-left">Quản lý thông tin chi tiết của các lái, bao gồm thông tin liên hệ và tài khoản ngân hàng.</p>
            
            <div class="flex justify-end mb-4">
                <button onclick="openDriverModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">Thêm thông tin lái</button>
            </div>

            <div class="hidden md:flex items-center gap-4 mb-4">
                <input type="search" id="desktopDriverSearch" placeholder="Tìm kiếm Tên và SĐT Lái..." class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                 <div class="flex items-center">
                    <input type="checkbox" id="desktopFilterUnverified" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="desktopFilterUnverified" class="ml-2 block text-sm font-medium text-gray-700 whitespace-nowrap">Chưa đánh dấu</label>
                </div>				
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
        </div>

        <div id="tab2" class="tab-content p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
             <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-700">Mục Nhập Toa</h2>
                <?php if ($user_type === 'quanly'): ?>
                <div class="ml-auto">
                    <select id="vuaSelectorTab2" class="vua-selector">
                        <option value="Data_vua7/">Vựa 7</option>
                        <option value="Data_vua9/">Vựa 9</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <p class="text-gray-600 leading-relaxed mb-4 text-left">Nhập và quản lý dữ liệu báo cáo hàng ngày của các lái.</p>
            
            <div class="mb-6 flex flex-wrap items-center gap-2">
                <label for="reportDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                <input type="text" id="reportDate" class="flatpickr-input">
				<?php if ($user_type === 'thuky_v7' || $user_type === 'thuky_v9'): ?>
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 whitespace-nowrap" onclick="exportReportsToExcel()">Xuất Excel</button>
				<?php endif; ?>
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
                <div class="flex items-center justify-between font-bold p-2 bg-gray-100 border-b border-gray-300 rounded-t-lg">
                    <div class="flex items-center">
                        <div class="w-[40px] text-center text-xs text-gray-500 uppercase">Toa</div>
                        <div class="text-xs text-gray-500 uppercase pl-2">Lái</div>
                    </div>
                </div>
                <div id="mobileReportList"></div>
            </div>

            <div id="mobileActionButtons" class="mt-4 flex justify-end gap-2">
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200" onclick="openSummaryModal()">Tổng Hợp Dữ Liệu</button>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200" onclick="handleAddNewRowClick()">Thêm Dòng Mới</button>
            </div>
        </div>

        <?php if ($user_type === 'quanly'): ?>
        <div id="tab3" class="tab-content p-6 bg-white rounded-b-xl border border-t-0 border-gray-200">
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Thống Kê Trong Ngày</h2>
                <div class="flex items-center gap-2 mb-4">
                    <label for="statsDate" class="font-semibold text-gray-700 whitespace-nowrap">Chọn Ngày:</label>
                    <input type="text" id="statsDate" class="flatpickr-input" placeholder="dd/mm/yyyy">
					<button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 whitespace-nowrap" onclick="exportStatsToExcel()">Xuất Excel</button>
                </div>
                <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full responsive-admin-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Loại Hàng</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Vựa 7 (KG)</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Vựa 9 (KG)</th>
                            </tr>
                        </thead>
                        <tbody id="statsTableBody" class="bg-white divide-y divide-gray-200">
                            </tbody>
                    </table>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-semibold text-gray-700">Quản Lý Tài Khoản</h2>
                </div>
                 <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 responsive-admin-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Loại Tài Khoản</th>
                                <th class="px-2 sm:px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                            </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-end">
                    <button onclick="openUserModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Thêm Tài Khoản</button>
                </div>
				<!-- Chọn chế độ kiểm tra tài khoản -->
				<div class="mt-8 pt-6 border-t border-gray-200">
					<h2 class="text-2xl font-semibold text-gray-700 mb-4">Thông tin ngân hàng</h2>
					<div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
						<p class="text-gray-500 mb-3">Phương thức kiểm tra thông tin ngân hàng của Lái.</p>
						<div class="flex flex-col sm:flex-row gap-4">
							<div class="flex items-center">
								<input id="verification_manual" name="verification_mode" type="radio" value="manual" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
								<label for="verification_manual" class="ml-3 block text-sm font-medium text-gray-700">
									Kiểm tra bằng tay
								</label>
							</div>
							<div class="flex items-center">
								<input id="verification_auto" name="verification_mode" type="radio" value="auto" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
								<label for="verification_auto" class="ml-3 block text-sm font-medium text-gray-700">
									Kiểm tra tự động
								</label>
							</div>
						</div>
						<div class="mt-4 flex justify-end">
							<button onclick="saveVerificationSetting()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">Lưu Cài Đặt</button>
						</div>
					</div>
				</div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="driverFormModal" class="driver-form-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 id="driver-form-title">Thêm/Chỉnh Sửa Thông Tin Lái</h3>
                <span class="close-modal" onclick="closeDriverModal()">&times;</span>
            </div>
            <div class="modal-body-custom">
                <form id="driverForm">
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
                        <button id="addUpdateButton" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200" onclick="addOrUpdateDriver()">Thêm vào danh sách</button>
                        <button id="cancelButton" type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200 hidden" onclick="closeDriverModal()">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="qrModal" class="qr-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 id="qrModalTitle">Quét mã để thanh toán</h3>
                <span class="close-modal" onclick="closeModal('qrModal')">&times;</span>
            </div>
            <div class="modal-body-custom">
                <div id="qrCodeContainer" class="mx-auto"></div>
                <div id="qrModalInfo" class="text-left my-4"></div>
                <div id="qrModalActions" class="flex justify-center gap-3"></div>
            </div>
        </div>
    </div>

    <div id="invoiceModal" class="invoice-modal">
        <div class="modal-content">
            <div class="modal-header-custom no-print">
                 <h3 id="invoiceModalTitle">Xem Hóa Đơn</h3>
                 <span class="close-modal" onclick="closeModal('invoiceModal')">&times;</span>
            </div>
            <div id="invoice-content-wrapper" class="modal-body-custom">
            </div>
        </div>
    </div>
    
    <div id="driverUsageModal" class="driver-usage-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 class="text-red-600">Không thể xóa Lái</h3>
                <span class="close-modal" onclick="closeModal('driverUsageModal')">&times;</span>
            </div>
            <div class="modal-body-custom">
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
    </div>

    <div id="summaryModal" class="summary-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3>Tổng Hợp Dữ Liệu</h3>
                <span class="close-modal" onclick="closeModal('summaryModal')">&times;</span>
            </div>
            <div id="summaryModalContent" class="modal-body-custom">
                <div class="overflow-x-auto rounded-lg shadow-sm border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-2.25/6">Loại Hàng</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-1.5/6">Tổng KG</th>
                                <th scope="col" class="px-3 py-2 text-xs font-bold text-gray-500 uppercase tracking-wider w-2.25/6">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody id="summaryTableBody" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div id="userModal" class="user-modal">
        <div class="modal-content">
            <div class="modal-header-custom">
                <h3 id="userModalTitle">Thêm Tài Khoản Mới</h3>
                <span class="close-modal" onclick="closeModal('userModal')">&times;</span>
            </div>
            <div class="modal-body-custom">
                <form id="userForm">
                    <input type="hidden" id="originalUsername" name="originalUsername">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 font-medium mb-2">Tên đăng nhập</label>
                        <input type="text" id="username" name="username" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Mật khẩu</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p id="passwordHelp" class="text-xs text-gray-500 mt-1">Để trống nếu không muốn thay đổi mật khẩu.</p>
                    </div>
                    <div class="mb-6">
                        <label for="userType" class="block text-gray-700 font-medium mb-2">Loại tài khoản</label>
                        <select id="userType" name="userType" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="thuky_v7">Thư ký Vựa 7</option>
                            <option value="thuky_v9">Thư ký Vựa 9</option>
                            <option value="quanly">Quản lý</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg" onclick="closeModal('userModal')">Hủy</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="reportFormModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-[100]" style="display: none;">
        <div class="modal-content bg-white w-full h-full md:w-auto md:h-auto md:max-w-5xl md:max-h-[90vh] rounded-lg shadow-xl flex flex-col">
            <div class="modal-header-custom">
                <h3 id="add-report-title">Thêm/Sửa Dữ Liệu Toa</h3>
                <span class="close-modal" onclick="closeReportModal()">&times;</span>
            </div>
            
            <div id="reportModalBody" class="modal-body-custom">
                <div id="dailyReportForm">
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

            <div id="sticky-totals-footer" class="p-2 border-t bg-gray-50 flex-shrink-0">
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
    // --- BIẾN TOÀN CỤC VÀ DỮ LIỆU ---
    const userType = '<?php echo $user_type; ?>';
    let activeDataDir = '';
    let reportBeingPaidIndex = null;
	let verificationMode = 'manual'; // 'manual' hoặc 'auto'
    let currentlyPayingReportIndex = null;
    
    const baseUrl = window.location.href.substring(0, window.location.href.lastIndexOf('/') + 1);
    let driverList = [];
    let editingIndexTab1 = -1;
    let dailyReports = [];
    let liveEditingReport = null;
    let isSaving = false;
    let saveTimeout;
    const DRIVERS_PER_PAGE = 10;
    let driversCurrentlyShown = DRIVERS_PER_PAGE;
	const banksData = [{"name":"Ngân hàng thương mại cổ phần An Bình","shortName":"ABBank","code":"970425"},{"name":"Ngân hàng thương mại cổ phần Á Châu","shortName":"ACB","code":"970416"},{"name":"Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam","shortName":"Agribank","code":"970405"},{"name":"Ngân hàng TNHH một thành viên ANZ (Việt Nam)","shortName":"ANZ","code":"970455"},{"name":"Ngân hàng thương mại cổ phần Bắc Á","shortName":"Bac A Bank","code":"970409"},{"name":"Ngân hàng thương mại cổ phần Bảo Việt","shortName":"BaoViet Bank","code":"970438"},{"name":"Ngân hàng thương mại cổ phần Đầu tư và Phát triển Việt Nam","shortName":"BIDV","code":"970418"},{"name":"Ngân hàng Nonghyup - chi nhánh Hà Nội","shortName":"Nonghyup","code":"801011"},{"name":"Ngân hàng số Cake by VPBank","shortName":"CAKE","code":"546034"},{"name":"Ngân hàng TNHH một thành viên CIMB Việt Nam","shortName":"CIMB","code":"422589"},{"name":"Ngân hàng Hợp tác xã Việt Nam","shortName":"Co-op Bank","code":"970446"},{"name":"Ngân hàng thương mại cổ phần Đông Á","shortName":"DongA Bank","code":"970406"},{"name":"Ngân hàng thương mại cổ phần Xuất Nhập khẩu Việt Nam","shortName":"Eximbank","code":"970431"},{"name":"Ngân hàng thương mại cổ phần Dầu khí toàn cầu","shortName":"GPBank","code":"970408"},{"name":"Ngân hàng thương mại cổ phần Phát triển Thành phố Hồ Chí Minh","shortName":"HDBank","code":"970437"},{"name":"Ngân hàng TNHH một thành viên Hong Leong Việt Nam","shortName":"Hong Leong Bank","code":"970442"},{"name":"Ngân hàng TNHH Indovina","shortName":"Indovina Bank","code":"970434"},{"name":"Ngân hàng Kookmin - Chi nhánh Hà Nội","shortName":"Kookmin HN","code":"970462"},{"name":"Ngân hàng Kookmin - Chi nhánh TP. Hồ Chí Minh","shortName":"Kookmin HCM","code":"970463"},{"name":"Ngân hàng thương mại cổ phần Kiên Long","shortName":"KienLong Bank","code":"970453"},{"name":"Ngân hàng số Liobank by OCB","shortName":"Liobank","code":"970448"},{"name":"Ngân hàng thương mại cổ phần Bưu điện Liên Việt","shortName":"LPBank","code":"970449"},{"name":"Ngân hàng thương mại cổ phần Quân đội","shortName":"MB Bank","code":"970422"},{"name":"Ngân hàng thương mại cổ phần Hàng hải Việt Nam","shortName":"MSB","code":"970426"},{"name":"Ngân hàng thương mại cổ phần Nam Á","shortName":"Nam A Bank","code":"970428"},{"name":"Ngân hàng thương mại cổ phần Quốc dân","shortName":"NCB","code":"970419"},{"name":"Ngân hàng số Ubank by VPBank","shortName":"Ubank","code":"546035"},{"name":"Ngân hàng thương mại cổ phần Phương Đông","shortName":"OCB","code":"970448"},{"name":"Ngân hàng TNHH một thành viên Public Việt Nam","shortName":"Public Bank","code":"970439"},{"name":"Ngân hàng thương mại cổ phần Xăng dầu Petrolimex","shortName":"PG Bank","code":"970430"},{"name":"Ngân hàng thương mại cổ phần Đại chúng Việt Nam","shortName":"PVcomBank","code":"970412"},{"name":"Ngân hàng thương mại cổ phần Sài Gòn Thương Tín","shortName":"Sacombank","code":"970403"},{"name":"Ngân hàng thương mại cổ phần Sài Gòn Công Thương","shortName":"Saigonbank","code":"970400"},{"name":"Ngân hàng thương mại cổ phần Sài Gòn","shortName":"SCB","code":"970429"},{"name":"Ngân hàng thương mại cổ phần Đông Nam Á","shortName":"SeABank","code":"970440"},{"name":"Ngân hàng thương mại cổ phần Sài Gòn - Hà Nội","shortName":"SHB","code":"970443"},{"name":"Ngân hàng TNHH một thành viên Shinhan Việt Nam","shortName":"Shinhan Bank","code":"970424"},{"name":"Ngân hàng thương mại cổ phần Kỹ Thương Việt Nam","shortName":"Techcombank","code":"970407"},{"name":"Ngân hàng số Timo","shortName":"Timo","code":"963388"},{"name":"Ngân hàng thương mại cổ phần Tiên Phong","shortName":"TPBank","code":"970423"},{"name":"Ngân hàng TNHH một thành viên United Overseas Bank (Việt Nam)","shortName":"UOB","code":"970458"},{"name":"Ngân hàng thương mại cổ phần Quốc tế Việt Nam","shortName":"VIB","code":"970441"},{"name":"Ngân hàng thương mại cổ phần Việt Á","shortName":"VietABank","code":"970427"},{"name":"Ngân hàng thương mại cổ phần Việt Nam Thương Tín","shortName":"VietBank","code":"970433"},{"name":"Ngân hàng thương mại cổ phần Ngoại thương Việt Nam","shortName":"Vietcombank","code":"970436"},{"name":"Ngân hàng thương mại cổ phần Công Thương Việt Nam","shortName":"VietinBank","code":"970415"},{"name":"Ngân hàng thương mại cổ phần Việt Nam Thịnh Vượng","shortName":"VPBank","code":"970432"},{"name":"Ngân hàng Liên doanh Việt - Nga","shortName":"VRB","code":"970421"},{"name":"Ngân hàng TNHH một thành viên Woori Việt Nam","shortName":"Woori Bank","code":"970457"},{"name":"Kho bạc Nhà nước","shortName":"KBNN","code":"799999"},{"name":"Ngân hàng Chính sách xã hội","shortName":"VBSP","code":"999888"},{"name":"Ngân hàng Phát triển Việt Nam","shortName":"VDB","code":"999666"},{"name":"Ngân hàng TNHH một thành viên Standard Chartered (Việt Nam)","shortName":"Standard Chartered","code":"970410"},{"name":"Ngân hàng TNHH một thành viên HSBC (Việt Nam)","shortName":"HSBC","code":"458761"},{"name":"Ngân hàng thương mại cổ phần các doanh nghiệp ngoài quốc doanh","shortName":"VPB","code":"970414"},{"name":"Ngân hàng thương mại cổ phần Đại Tín","shortName":"TRUSTBank","code":"970451"},{"name":"Ngân hàng Xây dựng","shortName":"CBBank","code":"970444"},{"name":"Ngân hàng TNHH một thành viên Đại Dương","shortName":"OceanBank","code":"970414"},{"name":"Ngân hàng TNHH một thành viên Dầu khí Toàn cầu","shortName":"GPBank","code":"970408"},{"name":"Ngân hàng First Bank","shortName":"First Bank","code":"970456"},{"name":"Ngân hàng TNHH MTV Shinhan Bank","shortName":"SVB","code":"970424"},{"name":"Industrial Bank of Korea - Ha Noi","shortName":"IBK HN","code":"970456"},{"name":"Industrial Bank of Korea - Ho Chi Minh","shortName":"IBK HCM","code":"970455"},{"name":"Ngân hàng Citibank N.A - Chi nhánh Hà Nội","shortName":"Citibank","code":"533948"},{"name":"Ngân hàng Sumitomo Mitsui Banking Corporation - Chi nhánh Hà Nội","shortName":"SMBC","code":"432618"},{"name":"Ngân hàng The Bank of Tokyo-Mitsubishi UFJ, Ltd. - Chi nhánh Hà Nội","shortName":"BTMU","code":"653118"},{"name":"Ngân hàng Mizuho - chi nhánh Hà Nội","shortName":"Mizuho","code":"636818"},{"name":"Ngân hàng Deutsche Bank AG - Chi nhánh TP. Hồ Chí Minh","shortName":"Deutsche Bank","code":"796500"},{"name":"Ngân hàng Maybank - Chi nhánh Hà Nội","shortName":"Maybank","code":"435018"},{"name":"Ngân hàng Commonwealth Bank of Australia - Chi nhánh TP. Hồ Chí Minh","shortName":"CBA","code":"652818"},{"name":"Ngân hàng The Bangkok Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"Bangkok Bank","code":"653018"},{"name":"Ngân hàng The Shanghai Commercial & Savings Bank, Ltd. - Chi nhánh Đồng Nai","shortName":"SCSB","code":"532858"},{"name":"Ngân hàng Malayan Banking Berhad","shortName":"Maybank","code":"435018"},{"name":"Ngân hàng Cathay United Bank","shortName":"Cathay United Bank","code":"970454"},{"name":"Ngân hàng China Construction Bank Corporation - Chi nhánh TP. Hồ Chí Minh","shortName":"CCBC","code":"653418"},{"name":"Ngân hàng JPMorgan Chase Bank, N.A. - Chi nhánh TP. Hồ Chí Minh","shortName":"JPM","code":"653318"},{"name":"Ngân hàng First Commercial Bank - Chi nhánh Hà Nội","shortName":"FCNB HN","code":"653518"},{"name":"Ngân hàng First Commercial Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"FCNB HCM","code":"653518"},{"name":"Ngân hàng The Hongkong and Shanghai Banking Corporation Limited","shortName":"HSBC","code":"458761"},{"name":"Ngân hàng BPCE IOM - Chi nhánh TP. Hồ Chí Minh","shortName":"BPCE IOM","code":"653618"},{"name":"Ngân hàng Bank of China - Chi nhánh TP. Hồ Chí Minh","shortName":"BOC","code":"653218"},{"name":"Ngân hàng Taipei Fubon Commercial Bank Co., Ltd. – Chi nhánh Hà Nội","shortName":"Fubon HN","code":"653818"},{"name":"Ngân hàng Taipei Fubon Commercial Bank Co., Ltd. – Chi nhánh TP. Hồ Chí Minh","shortName":"Fubon HCM","code":"653818"},{"name":"Ngân hàng Hua Nan Commercial Bank, Ltd. - Chi nhánh TP. Hồ Chí Minh","shortName":"HNCB","code":"653918"},{"name":"Ngân hàng Bank of Communications - Chi nhánh TP. Hồ Chí Minh","shortName":"BC","code":"654018"},{"name":"Ngân hàng CTBC Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"CTBC","code":"970454"},{"name":"Ngân hàng E.SUN Commercial Bank, Ltd., Dong Nai Branch","shortName":"ESUN","code":"654118"},{"name":"Ngân hàng Mega International Commercial Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"MICB","code":"654218"},{"name":"Ngân hàng SinoPac Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"SPB","code":"654318"},{"name":"Ngân hàng Taishin International Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"TIB","code":"654418"},{"name":"Ngân hàng Land Bank of Taiwan - Chi nhánh TP. Hồ Chí Minh","shortName":"LBT","code":"654518"},{"name":"Ngân hàng Taiwan Shin Kong Commercial Bank - Chi nhánh TP. Hồ Chí Minh","shortName":"TSKB","code":"654618"},{"name":"Ngân hàng Chang Hwa Commercial Bank, Ltd. - Chi nhánh TP. Hồ Chí Minh","shortName":"CHCB","code":"654718"},{"name":"Ngân hàng DBS Bank Ltd - Chi nhánh TP. Hồ Chí Minh","shortName":"DBS","code":"796501"},{"name":"Ngân hàng The Export-Import Bank of Korea - Chi nhánh Hà Nội","shortName":"KEBHana HN","code":"970461"},{"name":"Ngân hàng The Export-Import Bank of Korea - Chi nhánh TP. Hồ Chí Minh","shortName":"KEBHana HCM","code":"970461"},{"name":"Ngân hàng KEB Hana - Chi nhánh Hà Nội","shortName":"KEBHana HN","code":"970461"},{"name":"Ngân hàng KEB Hana - Chi nhánh TP. Hồ Chí Minh","shortName":"KEBHana HCM","code":"970461"},{"name":"Ngân hàng TNHH Một thành viên Bank of China (Hong Kong) - Chi nhánh TP. Hồ Chí Minh","shortName":"BOC HCM","code":"653218"},{"name":"Ngân hàng The Development Bank of Singapore Limited","shortName":"DBS","code":"796501"}];
    const quantityColumns = ["B1_Thái","B2_Thái","C1_Thái","C2_Thái","C3_Thái","D1_Thái","D2_Thái","E_Thái","Chợ_Thái","Xơ_Thái","A1_indo","A2_indo","B1_indo","B2_indo","B3_indo","C1_indo","C2_indo","Chợ_1_indo","Chợ_2_indo","Xơ_indo"];
    const priceColumns = quantityColumns.map(col => `Giá_${col}`);
    const allCalcColumns = [...quantityColumns, ...priceColumns];

    // --- CÁC HÀM TIỆN ÍCH ---
    function normalizeName(name) {
        if (!name) return '';
        return removeAccents(name).toUpperCase().trim();
    }

    function removeAccents(str) {
        if (!str) return '';
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
    }

    function getBankBin(bankName) {
        const bank = banksData.find(b => b.name === bankName);
        return bank ? bank.code : null;
    }
    
    function getBankShortName(fullName) {
        const bank = banksData.find(b => b.name === fullName);
        return bank ? bank.shortName : fullName;
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
            if (modalId === 'qrModal') {
                document.getElementById('qrCodeContainer').innerHTML = '';
                if (currentlyPayingReportIndex !== null) {
                    currentlyPayingReportIndex = null;
                    const expandedToa = getExpandedToaSet();
                    filterReportsByDate(expandedToa);
                }
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
    
    function recalculateDailyToaNumbers(date) {
        const reportsForDay = dailyReports.filter(r => r.date === date);

        reportsForDay.sort((a, b) => a.toa - b.toa);

        reportsForDay.forEach((report, index) => {
            report.toa = index + 1;
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
    
    function getExpandedToaSet() {
        const expandedToa = new Set();
        document.querySelectorAll('#mobileReportList .mobile-report-details.expanded').forEach(details => {
            const toaEl = details.closest('.mobile-report-item')?.querySelector('.toa');
            if (toaEl && toaEl.textContent) {
                expandedToa.add(toaEl.textContent);
            }
        });
        return expandedToa;
    }

    // --- HÀM HÓA ĐƠN ---
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
		
		if (!hasThaiData && !hasIndoData) {
			invoiceWrapper.innerHTML = `<div class="a4-sheet-portrait flex items-center justify-center"><p>Không có dữ liệu hàng hóa cho hóa đơn này.</p></div>`;
			document.getElementById('invoiceModalTitle').textContent = `Hóa Đơn`;
			document.getElementById('invoiceModal').style.display = 'block';
			return;
		}

		// --- LUÔN SỬ DỤNG BỐ CỤC KHỔ DỌC (PORTRAIT) ---

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
		
		const customerInfoHtml = `
			<div class="main-title text-center border-t-2 border-gray-800 pt-2">
				<h2 class="font-bold text-indigo-600 uppercase" style="padding-bottom: 5mm;">Hóa Đơn</h2>
			</div>
			<div class="customer-info">
				<p><strong>Ngày lập:</strong> ${report.date} - <strong>Toa:</strong> ${report.toa}</p>
				<p><strong>Khách hàng:</strong> ${report.driver.toUpperCase()}</p>
				${driver ? `<p><strong>STK:</strong> ${driver.accountNumber} - ${getBankShortName(driver.bankName)} - ${driver.accountName.toUpperCase()}</p>` : ''}
			</div>`;
		
		let thaiTableHtml = '';
		if (hasThaiData) {
			const hasCalculationThai = activeThaiColumns.some(col => report[col] && String(report[col]).includes('+'));
			let thaiTotalKg = 0;
			let thaiTotalAmount = 0;
			
			// --- Căn đều cột cho bảng hàng Thái ---
			const numThaiColumns = activeThaiColumns.length + 2; // +2 cho cột "Loại Hàng" và "Tổng Cộng"
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

			// --- Căn đều cột cho bảng hàng Indo ---
			const numIndoColumns = activeIndoColumns.length + 2; // +2 cho cột "Loại Hàng" và "Tổng Cộng"
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
		
		// --- TIẾN HÀNH CHUYỂN ĐỔI SANG HÌNH ẢNH ---
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
    
    
    async function showVerificationQrModal(index) {
        const driver = driverList[index];
        if (!driver) return;

        const bankBin = getBankBin(driver.bankName);
        if (!bankBin) {
            return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${driver.bankName}.`);
        }

        const amount = 1; // Số tiền nhỏ để xác thực
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
            <button class="${buttonClass} text-white font-bold py-2 px-4 rounded-lg" onclick="toggleVerificationStatus(${index})">${buttonText}</button>
            <button class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg" onclick="closeModal('qrModal')">Đóng</button>
        `;
    }

    async function toggleVerificationStatus(index) {
        const driver = driverList[index];
        if (!driver) return;
        
        driver.isVerified = !driver.isVerified;
        
        await saveDriverListToCSV();
        
        renderDriverList();
        
        closeModal('qrModal');
        
        customAlert(driver.isVerified ? 'Đã đánh dấu tài khoản chính xác.' : 'Đã bỏ đánh dấu tài khoản.');
    }


    async function showPaymentModal(reportIndex) {
        currentlyPayingReportIndex = reportIndex;
        const expandedToa = getExpandedToaSet();
        filterReportsByDate(expandedToa);

        const report = dailyReports[reportIndex];
        if (!report) return;
        const driver = driverList.find(d => d.driverNamePhone === report.driver);
        if (!driver) return customAlert(`Lỗi: Không tìm thấy thông tin ngân hàng cho Lái "${report.driver}". Vui lòng kiểm tra lại thẻ Thông Tin Lái.`);
        if (!driver.isVerified) return customAlert(`Lỗi: Tài khoản ngân hàng của Lái "${report.driver}" chưa được đánh dấu chính xác.`);
        
        const amount = calculateTotalAmount(report);
        if (amount <= 0) return customAlert('Số tiền thanh toán phải lớn hơn 0.');
        
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
            <button class="${toggleButtonClass} text-white font-bold py-2 px-4 rounded-lg" onclick="togglePaymentStatus(${reportIndex})">${toggleButtonText}</button>
        `;
    }

    async function togglePaymentStatus(reportIndex) {
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
                    if (!reportsForDay[i].isPaid) {
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

// Hàm cho nút "Xuất Excel" ở tab NHẬP TOA
function exportReportsToExcel() {
    const selectedDate = document.getElementById('reportDate').value;
    if (!selectedDate) {
        return customAlert('Vui lòng chọn ngày để xuất báo cáo.');
    }

    let vuaSelected = '';
    // Kiểm tra loại tài khoản để xác định đúng vựa cần xuất
    if (userType === 'quanly') {
        // Quản lý sẽ lấy giá trị từ menu dropdown
        const vuaSelector = document.getElementById('vuaSelectorTab2');
        if (vuaSelector) {
            vuaSelected = vuaSelector.value;
        }
    } else if (userType === 'thuky_v7') {
        // Thư ký vựa 7 sẽ luôn xuất dữ liệu của vựa 7
        vuaSelected = 'Data_vua7/';
    } else if (userType === 'thuky_v9') {
        // Thư ký vựa 9 sẽ luôn xuất dữ liệu của vựa 9
        vuaSelected = 'Data_vua9/';
    }

    // Nếu không xác định được vựa thì báo lỗi
    if (!vuaSelected) {
        return customAlert('Lỗi: Không thể xác định được vựa để xuất báo cáo.');
    }

    // Chuyển hướng đến file PHP để tạo và tải file Excel
    window.location.href = `export_styled.php?type=nhaptoa&date=${encodeURIComponent(selectedDate)}&vua=${encodeURIComponent(vuaSelected)}`;
}

// Hàm cho nút "Xuất Excel" ở tab QUẢN LÝ
function exportStatsToExcel() {
    const selectedDate = document.getElementById('statsDate').value;
    if (!selectedDate) {
        return customAlert('Vui lòng chọn ngày để xuất thống kê.');
    }
    
    // SỬA LỖI: Thêm encodeURIComponent để mã hóa ngày tháng đúng chuẩn
    window.location.href = `export_styled.php?type=quanly&date=${encodeURIComponent(selectedDate)}`;
}

    function openTab(tabId) {
		// 1. Chuyển đổi giao diện tab
		document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
		document.querySelectorAll('.tab-button').forEach(button => button.classList.remove('active'));
		document.getElementById(tabId).classList.add('active');
		const clickedButton = document.querySelector(`.tab-button[onclick*="${tabId}"]`);
		if (clickedButton) clickedButton.classList.add('active');

		// 2. Gọi hàm tải dữ liệu tương ứng cho tab đó
		// Phiên bản này không còn tự ý thay đổi activeDataDir nữa.
		if (tabId === 'tab1') {
			loadDriverList();
		} else if (tabId === 'tab2') {
			loadDataForCurrentVua();
		} else if (tabId === 'tab3' && userType === 'quanly') {
			loadUsers();
			loadStats();
		}
	}

    async function loadDataForCurrentVua() {
        await loadDriverList();
        await loadDailyReports();
    }

    function loadBanksIntoDropdown() {
        const bankSelect = document.getElementById('bankName');
        bankSelect.innerHTML = '<option value="">Chọn Ngân Hàng</option>';
        const sortedBanks = banksData.sort((a, b) => a.shortName.localeCompare(b.shortName));
        sortedBanks.forEach(bank => {
            const option = document.createElement('option');
            option.value = bank.name;
            option.textContent = bank.shortName;
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

		const button = document.getElementById('addUpdateButton');
		button.disabled = true;
		const originalButtonText = button.textContent;
		
		// --- BƯỚC 1: XÁC THỰC NGÂN HÀNG (NẾU CÓ) ---
		// Quy trình này CHỈ để xác định trạng thái isVerified và thông báo, không dừng việc lưu.
		let isVerifiedStatus = false;
		let verificationMessage = '';
		const isUpdating = editingIndexTab1 !== -1;
		const currentVerifiedStatus = isUpdating ? driverList[editingIndexTab1].isVerified : false;

		if (verificationMode === 'auto') {
			button.textContent = 'Đang kiểm tra...';
			const bankBin = getBankBin(bankName);
			if (!bankBin) {
				button.disabled = false;
				button.textContent = originalButtonText;
				return customAlert(`Lỗi: Không tìm thấy mã BIN cho ngân hàng ${bankName}.`);
			}

			try {
				const response = await fetch('verify_bank_account.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({ toBin: bankBin, toAccountNumber: accountNumber })
				});
				const result = await response.json();
				
				if (result.payload && result.payload.code === '624') {
					isVerifiedStatus = true;
					verificationMessage = "Thông tin ngân hàng chính xác. ";
				} else {
					isVerifiedStatus = false;
					verificationMessage = "Thông tin ngân hàng sai. ";
				}
			} catch (error) {
				button.disabled = false;
				button.textContent = originalButtonText;
				console.error("Lỗi khi gọi API xác thực:", error);
				customAlert("Lỗi kết nối khi xác thực. Dữ liệu sẽ được lưu nhưng chưa được đánh dấu.");
				// Dù lỗi kết nối, vẫn cho phép lưu ở chế độ thủ công
				isVerifiedStatus = currentVerifiedStatus; 
			}
		} else {
			isVerifiedStatus = currentVerifiedStatus;
		}

		button.textContent = originalButtonText;
		button.disabled = false;

		// --- BƯỚC 2: LƯU VÀ ĐỒNG BỘ (LUÔN ĐƯỢC THỰC THI) ---
		let syncMessage = '';
		
		if (isUpdating) { // Cập nhật lái cũ
			const oldDriverName = driverList[editingIndexTab1].driverNamePhone;
			const driverData = { 
				...driverList[editingIndexTab1], 
				driverNamePhone, bankName, accountNumber, accountName,
				isVerified: verificationMode === 'auto' ? isVerifiedStatus : currentVerifiedStatus
			};
			driverList[editingIndexTab1] = driverData;

			// Luôn kiểm tra và đồng bộ tên nếu có thay đổi
			if (oldDriverName !== driverNamePhone && oldDriverName) {
				let updatedReportsCount = 0;
				dailyReports.forEach(report => {
					if (report.driver === oldDriverName) {
						report.driver = driverNamePhone;
						updatedReportsCount++;
					}
				});
				if(updatedReportsCount > 0) {
					 await saveDailyReportsToCSV();
					 syncMessage = ` và đã đồng bộ tên cho ${updatedReportsCount} toa hàng`;
				}
			}
			customAlert(verificationMessage + 'Đã cập nhật thông tin lái' + syncMessage + '.');
			
		} else { // Thêm lái mới
			const isDuplicate = driverList.some(driver => 
				driver && typeof driver.driverNamePhone === 'string' && 
				driver.driverNamePhone.trim().toLowerCase() === driverNamePhone.toLowerCase()
			);
			if (isDuplicate) {
				return customAlert('Tên và SĐT Lái bạn nhập đã tồn tại trong danh sách');
			}
			const driverData = { driverNamePhone, bankName, accountNumber, accountName, isVerified: isVerifiedStatus };
			driverList.push(driverData);
			customAlert(verificationMessage + 'Đã thêm thông tin lái mới vào danh sách.');
		}
		
		await saveDriverListToCSV();
		sortDriverList();
		renderDriverList();
		filterReportsByDate();
		closeDriverModal();
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
		
		if (filteredList.length === 0) {
			if (isMobile) {
				document.getElementById('mobileDriverList').innerHTML = `<div class="text-center text-gray-500 py-4">Không có dữ liệu.</div>`;
			} else {
				document.getElementById('driverListTableBody').innerHTML = `<tr><td colspan="6" class="text-center text-gray-500 py-4">Không có dữ liệu.</td></tr>`;
			}
			document.getElementById('mobileLoadMoreContainer').style.display = 'none';
			document.getElementById('desktopLoadMoreContainer').style.display = 'none';
			return;
		}
		
		const listToRender = filteredList.slice(0, driversCurrentlyShown);

		if (isMobile) {
			const mobileList = document.getElementById('mobileDriverList');
			mobileList.innerHTML = "";
			listToRender.forEach((driver, displayIndex) => {
				const originalIndex = driverList.findIndex(d => d === driver);
				const item = document.createElement('div');
				item.className = 'mobile-driver-item';
				const verifiedIconHtml = driver.isVerified ? `<span class="verified-check">&#10004;</span>` : '';

				// Nút "Đánh dấu" luôn được tạo với class 'verify-button'
				const verifyButtonHtml = `<button class="bg-green-500 hover:bg-green-600 text-white font-bold rounded-md verify-button" onclick="event.stopPropagation(); showVerificationQrModal(${originalIndex})">Đánh dấu</button>`;

				item.innerHTML = `
					<div class="mobile-driver-main" onclick="toggleDriverDetails(this)">
						<span class="stt">${displayIndex + 1}</span>
						<span class="name">${driver.driverNamePhone}${verifiedIconHtml}</span>
						<span class="toggle-icon">▼</span>
					</div>
					<div class="mobile-driver-details">
						<div class="mobile-driver-info">
							<p><strong>Ngân Hàng:</strong> <span>${getBankShortName(driver.bankName)}</span></p>
							<p><strong>Số TK:</strong> <span>${driver.accountNumber}</span></p>
							<p><strong>Chủ TK:</strong> <span>${driver.accountName}</span></p>
						</div>
						<div class="mobile-driver-actions">
							${verifyButtonHtml}
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
				row.insertCell().textContent = getBankShortName(driver.bankName);
				row.insertCell().textContent = driver.accountNumber;
				row.insertCell().textContent = driver.accountName;
				const actionsCell = row.insertCell();
				actionsCell.className = 'flex gap-2 justify-center p-2';

				// Nút "Đánh dấu" luôn được tạo với class 'verify-button'
				const verifyButton = document.createElement('button');
				verifyButton.textContent = 'Đánh dấu';
				verifyButton.className = 'bg-green-500 hover:bg-green-600 text-white text-sm font-bold py-1 px-2 rounded-md whitespace-nowrap verify-button';
				verifyButton.onclick = () => showVerificationQrModal(originalIndex);
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

		// Gọi hàm cập nhật UI sau khi đã render xong danh sách
		applyVerificationModeUI();
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
        document.getElementById('driverForm').reset();
    }

    async function loadDriverList() {
        try {
            const response = await fetch(baseUrl + 'load_drivers.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ dataDir: activeDataDir })
            });
            if (!response.ok) throw new Error('Network response was not ok.');
            const result = await response.json();
            if (result && result.success) {
                driverList = result.data.map(d => ({...d, isVerified: d.isVerified === 'true'})) || [];
                sortDriverList();
                renderDriverList();
            } else {
                console.error("Lỗi khi tải danh sách lái:", result.message);
                driverList = [];
                renderDriverList(); 
            }
        } catch (error) {
            console.error("Lỗi khi fetch danh sách lái từ máy chủ:", error);
            driverList = [];
            renderDriverList();
        }
    }

    function openDriverModal(index = -1) {
        editingIndexTab1 = index;
        const modal = document.getElementById('driverFormModal');
        const title = modal.querySelector('#driver-form-title');
        const addUpdateButton = modal.querySelector('#addUpdateButton');
        const cancelButton = modal.querySelector('#cancelButton');

        clearDriverForm();

        if (index === -1) { // Chế độ thêm mới
            title.textContent = 'Thêm Thông Tin Lái';
            addUpdateButton.textContent = 'Thêm vào danh sách';
            addUpdateButton.classList.remove('bg-green-600', 'hover:bg-green-700');
            addUpdateButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            cancelButton.classList.add('hidden');
        } else { // Chế độ chỉnh sửa
            const driverToEdit = driverList[index];
            title.textContent = 'Sửa Thông Tin Lái';
            document.getElementById('driverNamePhone').value = driverToEdit.driverNamePhone;
            document.getElementById('bankName').value = driverToEdit.bankName;
            document.getElementById('accountNumber').value = driverToEdit.accountNumber;
            document.getElementById('accountName').value = driverToEdit.accountName;
            
            addUpdateButton.textContent = 'Cập nhật';
            addUpdateButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            addUpdateButton.classList.add('bg-green-600', 'hover:bg-green-700');
            cancelButton.classList.remove('hidden');
        }
        modal.style.display = 'flex';
    }

    function closeDriverModal() {
        document.getElementById('driverFormModal').style.display = 'none';
        editingIndexTab1 = -1;
    }

    function editDriver(index) {
        openDriverModal(index);
    }

    async function deleteDriver(index) {
        const driverToDelete = driverList[index];
        if (!driverToDelete) return;

        customAlert("Đang kiểm tra dữ liệu liên quan...");

        try {
            const response = await fetch(baseUrl + "check_driver_usage.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ driverNamePhone: driverToDelete.driverNamePhone, dataDir: activeDataDir })
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
                        driversCurrentlyShown = DRIVERS_PER_PAGE;
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
                arrow.style.transform = isCollapsed ? 'rotate(0deg)' : 'rotate(-90deg)';
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
    
    function openReportModal() {
        document.getElementById('reportFormModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeReportModal() {
        if (liveEditingReport && !liveEditingReport.driver) {
            const index = dailyReports.indexOf(liveEditingReport);
            if (index > -1) {
                dailyReports.splice(index, 1);
            }
        }
        
        const expandedToa = getExpandedToaSet();

        liveEditingReport = null;
        document.getElementById('reportFormModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        filterReportsByDate(expandedToa);
    }

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
            const newReport = { date: selectedDate, driver: selectedDriver, Ghi_Chú: '', isPaid: false };
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
    
    async function handleAddNewRowClick() {
        if (liveEditingReport) {
            await handleAutoSave(true);
        }
        resetFormForNewEntry();
        openReportModal();
        setTimeout(() => document.getElementById('dailyReportDriverSearch').focus(), 50);
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

    function renderDailyReportTable(reports, expandedToaToRestore = new Set()) {
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

                let paymentButtonHtml = '';
                if (userType === 'quanly') {
                    paymentButtonHtml = `<button class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-md flex items-center justify-center" onclick="event.stopPropagation(); showPaymentModal(${originalIndex})">Thanh Toán`;
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
                             <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); showInvoice(${originalIndex})">Hóa Đơn</button>
                             ${paymentButtonHtml}
                             <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); editDailyReport(dailyReports[${originalIndex}])">Sửa</button>
                             <button class="bg-red-500 hover:bg-red-600 text-white font-bold rounded-md" onclick="event.stopPropagation(); deleteDailyReport(dailyReports[${originalIndex}])">Xóa</button>
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
                let laiHtml = report.driver;
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

                if (userType === 'quanly') {
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
            const response = await fetch(baseUrl + 'load_reports.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ dataDir: activeDataDir })
            });
            if (!response.ok) throw new Error('Network response was not ok for reports.');
            const result = await response.json();
            if (result && result.success) {
                dailyReports = result.data.map(r => ({ ...r, isPaid: r.isPaid === 'true' })) || [];
                filterReportsByDate();
            } else {
                console.error("Lỗi khi tải báo cáo:", result.message);
                dailyReports = [];
                filterReportsByDate();
            }
        } catch (error) {
            console.error("Lỗi khi fetch báo cáo từ máy chủ:", error);
            dailyReports = [];
            filterReportsByDate();
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
        if (liveEditingReport && liveEditingReport !== reportToEdit) {
            await handleAutoSave(true);
        }
        liveEditingReport = reportToEdit;
        document.getElementById('add-report-title').textContent = `Sửa Dữ Liệu Toa ${reportToEdit.toa}`;
        bindFormToReport(reportToEdit);
        showAllCalculationPreviews();
        lockForm(false);
        document.getElementById('dailyReportDriverSearch').disabled = true;
        document.getElementById('changeDriverBtn').classList.remove('hidden');
        openReportModal();
    }

    async function deleteDailyReport(reportToDelete) {
        confirm('Bạn có chắc chắn muốn xóa dòng này?').then(async result => {
            if (result) {
                if (liveEditingReport === reportToDelete) {
                    closeReportModal();
                }
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

    function filterReportsByDate(expandedToaToRestore = new Set()) {
        const selectedDate = document.getElementById('reportDate').value;
        if (liveEditingReport && liveEditingReport.date !== selectedDate) {
            handleAutoSave(true).then(() => {
                closeReportModal();
            });
        }
        const filtered = dailyReports.filter(report => report.date === selectedDate);
        filtered.sort((a, b) => (a.toa || 0) - (b.toa || 0));
        renderDailyReportTable(filtered, expandedToaToRestore);
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

    function openSummaryModal() {
        document.getElementById('summaryModal').style.display = 'block';
    }
    
    async function saveDailyReportsToCSV() {
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

    async function saveDriverListToCSV() {
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

    // --- QUAN LY FUNCTIONS ---
	// --- HÀM MỚI ĐỂ QUẢN LÝ CÀI ĐẶT KIỂM TRA TÀI KHOẢN ---
	async function saveVerificationSetting() {
		const selectedMode = document.querySelector('input[name="verification_mode"]:checked').value;
		try {
			const response = await fetch('manage_settings.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ verification_mode: selectedMode })
			});
			const result = await response.json();
			if (result.success) {
				verificationMode = selectedMode;
				applyVerificationModeUI(); // Cập nhật UI ngay sau khi lưu
				customAlert('Đã lưu cài đặt thành công.');
			} else {
				customAlert('Lỗi: ' + result.message);
			}
		} catch (error) {
			customAlert('Lỗi kết nối khi lưu cài đặt.');
		}
	}

	async function loadVerificationSetting() {
		try {
			const response = await fetch('manage_settings.php');
			const settings = await response.json();
			if (settings && settings.verification_mode) {
				verificationMode = settings.verification_mode;
				const radioBtn = document.getElementById(`verification_${verificationMode}`);
				if (radioBtn) radioBtn.checked = true;
			}
		} catch (e) {
			console.error("Không thể tải file settings, sử dụng chế độ mặc định 'manual'.");
			verificationMode = 'manual';
			const radioBtn = document.getElementById('verification_manual');
			if (radioBtn) radioBtn.checked = true;
		}
		// Luôn gọi hàm cập nhật UI sau khi tải xong
		applyVerificationModeUI();
	}

	function applyVerificationModeUI() {
		const verifyButtons = document.querySelectorAll('.verify-button');
		if (verificationMode === 'auto') {
			verifyButtons.forEach(btn => btn.style.display = 'none');
		} else {
			verifyButtons.forEach(btn => btn.style.display = 'inline-block');
		}
	}
	// --- HẾT - HÀM MỚI ĐỂ QUẢN LÝ CÀI ĐẶT KIỂM TRA TÀI KHOẢN ---
    async function loadUsers() {
        try {
            const response = await fetch('manage_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'load' })
            });
            const result = await response.json();
            if (result.success) {
                renderUsersTable(result.data);
            } else {
                customAlert('Lỗi tải danh sách người dùng: ' + result.message);
            }
        } catch (error) {
            customAlert('Lỗi kết nối khi tải người dùng.');
        }
    }

    function renderUsersTable(users) {
        const tableBody = document.getElementById('usersTableBody');
        tableBody.innerHTML = '';
        users.forEach(user => {
            const row = tableBody.insertRow();
            
            const usernameCell = row.insertCell();
            usernameCell.className = 'px-2 sm:px-6 py-3 text-center whitespace-nowrap';
            usernameCell.textContent = user.username;

            const typeCell = row.insertCell();
            typeCell.className = 'px-2 sm:px-6 py-3 text-center whitespace-nowrap';
            typeCell.textContent = user.type;

            const actionsCell = row.insertCell();
            actionsCell.className = 'px-2 sm:px-6 py-3 text-center';
            
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'flex gap-2 justify-center';

            const editButton = document.createElement('button');
            editButton.textContent = 'Sửa';
            editButton.className = 'bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold py-1 px-2 rounded-md';
            editButton.onclick = () => openUserModal(user);
            buttonContainer.appendChild(editButton);

            if (user.username !== 'admin') {
                const deleteButton = document.createElement('button');
                deleteButton.textContent = 'Xóa';
                deleteButton.className = 'bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-1 px-2 rounded-md';
                deleteButton.onclick = () => deleteUser(user.username);
                buttonContainer.appendChild(deleteButton);
            }
            
            actionsCell.appendChild(buttonContainer);
        });
    }

    function openUserModal(user = null) {
        const modal = document.getElementById('userModal');
        const form = document.getElementById('userForm');
        const title = document.getElementById('userModalTitle');
        const passwordHelp = document.getElementById('passwordHelp');
        const usernameInput = document.getElementById('username');
        
        form.reset();

        if (user) {
            title.textContent = 'Sửa Tài Khoản';
            document.getElementById('originalUsername').value = user.username;
            usernameInput.value = user.username;
            usernameInput.disabled = true;
            document.getElementById('userType').value = user.type;
            document.getElementById('password').placeholder = "Để trống nếu không đổi";
            passwordHelp.style.display = 'block';
        } else {
            title.textContent = 'Thêm Tài Khoản Mới';
            usernameInput.disabled = false;
            document.getElementById('password').placeholder = "";
            passwordHelp.style.display = 'none';
        }
        modal.style.display = 'block';
    }

    async function handleUserFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const originalUsername = form.originalUsername.value;
        const user = {
            username: form.username.value,
            password: form.password.value,
            type: form.userType.value
        };

        const action = originalUsername ? 'update' : 'add';
        if (action === 'add' && !user.password) {
            customAlert('Vui lòng nhập mật khẩu cho tài khoản mới.');
            return;
        }

        try {
            const response = await fetch('manage_users.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, user, original_username: originalUsername })
            });
            const result = await response.json();
            if (result.success) {
                customAlert(result.message);
                closeModal('userModal');
                loadUsers();
            } else {
                customAlert('Lỗi: ' + result.message);
            }
        } catch (error) {
            customAlert('Lỗi kết nối khi lưu tài khoản.');
        }
    }

    async function deleteUser(username) {
        confirm(`Bạn có chắc muốn xóa tài khoản "${username}"?`).then(async confirmed => {
            if (confirmed) {
                try {
                    const response = await fetch('manage_users.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'delete', username })
                    });
                    const result = await response.json();
                     if (result.success) {
                        customAlert(result.message);
                        loadUsers();
                    } else {
                        customAlert('Lỗi: ' + result.message);
                    }
                } catch (error) {
                    customAlert('Lỗi kết nối khi xóa tài khoản.');
                }
            }
        });
    }

    async function loadStats() {
        const date = document.getElementById('statsDate').value;
        try {
            const response = await fetch('get_stats.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ date })
            });
            const result = await response.json();
            if (result.success) {
                renderStatsTable(result.data);
            } else {
                customAlert('Lỗi tải thống kê: ' + result.message);
            }
        } catch (error) {
            customAlert('Lỗi kết nối khi tải thống kê.');
        }
    }

    function renderStatsTable(stats) {
        const tableBody = document.getElementById('statsTableBody');
        tableBody.innerHTML = '';
        const vua7Data = stats.vua7;
        const vua9Data = stats.vua9;

        const createRow = (content, classes = '') => {
            const row = tableBody.insertRow();
            row.className = classes;
            row.innerHTML = content;
            return row;
        };

        const thaiTypeOrder = ["B1_Thái", "B2_Thái", "C1_Thái", "C2_Thái", "C3_Thái", "D1_Thái", "D2_Thái", "E_Thái", "Chợ_Thái", "Xơ_Thái"];
        const indoTypeOrder = ["A1_indo", "A2_indo", "B1_indo", "B2_indo", "B3_indo", "C1_indo", "C2_indo", "Chợ_1_indo", "Chợ_2_indo", "Xơ_indo"];

        let grandTotalAmount = 0;

        // --- HÀNG THÁI ---
        let totalThaiKg7 = 0, totalThaiKg9 = 0, totalThaiAmount = 0;
        thaiTypeOrder.forEach(type => {
            const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
            const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
            totalThaiKg7 += detail7.kg;
            totalThaiKg9 += detail9.kg;
            totalThaiAmount += (detail7.amount + detail9.amount);
        });
        grandTotalAmount += totalThaiAmount;

        const thaiHeaderRow = createRow(
            `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">
                <div class="flex items-center justify-center cursor-pointer" onclick="toggleStatsCategory('thai', this.closest('tr'))">
                    <span>HÀNG THÁI</span><span class="toggle-icon ml-2 transition-transform duration-300">▼</span>
                </div>
            </td>
            <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalThaiKg7 > 0 ? totalThaiKg7.toLocaleString('vi-VN') : ''}</td>
            <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalThaiKg9 > 0 ? totalThaiKg9.toLocaleString('vi-VN') : ''}</td>`,
            `stat-category-header`
        );
        
        thaiTypeOrder.forEach(type => {
            const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
            const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
            if (detail7.kg > 0 || detail9.kg > 0) {
                createRow(
                    `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${type.replace('_Thái', '')}</td>
                     <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail7.kg > 0 ? detail7.kg.toLocaleString('vi-VN') : ''}</td>
                     <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail9.kg > 0 ? detail9.kg.toLocaleString('vi-VN') : ''}</td>`,
                    'stat-detail-row stat-detail-thai'
                );
            }
        });
        if (totalThaiKg7 > 0 || totalThaiKg9 > 0) {
            createRow(
                `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">Tổng Thái</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalThaiKg7 > 0 ? totalThaiKg7.toLocaleString('vi-VN') : ''}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalThaiKg9 > 0 ? totalThaiKg9.toLocaleString('vi-VN') : ''}</td>`,
                'bg-gray-100 stat-detail-row stat-detail-thai'
            );
        }

        // --- HÀNG INDO ---
        let totalIndoKg7 = 0, totalIndoKg9 = 0, totalIndoAmount = 0;
        indoTypeOrder.forEach(type => {
            const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
            const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
            totalIndoKg7 += detail7.kg;
            totalIndoKg9 += detail9.kg;
            totalIndoAmount += (detail7.amount + detail9.amount);
        });
        grandTotalAmount += totalIndoAmount;
        
        const indoHeaderRow = createRow(
            `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">
                <div class="flex items-center justify-center cursor-pointer" onclick="toggleStatsCategory('indo', this.closest('tr'))">
                    <span>HÀNG INDO</span><span class="toggle-icon ml-2 transition-transform duration-300">▼</span>
                </div>
            </td>
            <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalIndoKg7 > 0 ? totalIndoKg7.toLocaleString('vi-VN') : ''}</td>
            <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold header-total-cell">${totalIndoKg9 > 0 ? totalIndoKg9.toLocaleString('vi-VN') : ''}</td>`,
            `stat-category-header`
        );

        indoTypeOrder.forEach(type => {
            const detail7 = vua7Data.details[type] || { kg: 0, amount: 0 };
            const detail9 = vua9Data.details[type] || { kg: 0, amount: 0 };
            if (detail7.kg > 0 || detail9.kg > 0) {
                createRow(
                    `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${type.replace('_indo', '')}</td>
                     <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail7.kg > 0 ? detail7.kg.toLocaleString('vi-VN') : ''}</td>
                     <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm">${detail9.kg > 0 ? detail9.kg.toLocaleString('vi-VN') : ''}</td>`,
                    'stat-detail-row stat-detail-indo'
                );
            }
        });
        if (totalIndoKg7 > 0 || totalIndoKg9 > 0) {
            createRow(
                `<td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">Tổng Indo</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalIndoKg7 > 0 ? totalIndoKg7.toLocaleString('vi-VN') : ''}</td>
                 <td class="px-2 sm:px-6 py-3 text-center whitespace-nowrap text-sm font-bold">${totalIndoKg9 > 0 ? totalIndoKg9.toLocaleString('vi-VN') : ''}</td>`,
                'bg-gray-100 stat-detail-row stat-detail-indo'
            );
        }

        // --- TỔNG CỘNG ---
        const grandTotalKg7 = totalThaiKg7 + totalIndoKg7;
        const grandTotalKg9 = totalThaiKg9 + totalIndoKg9;
        
        const totalKgRow = tableBody.insertRow();
        totalKgRow.className = `font-extrabold text-blue-800`;
        totalKgRow.innerHTML = `
            <td class="px-2 sm:px-6 py-4 text-center">TỔNG CỘNG (KG)</td>
            <td class="px-2 sm:px-6 py-4 text-center">${grandTotalKg7 > 0 ? grandTotalKg7.toLocaleString('vi-VN') : ''}</td>
            <td class="px-2 sm:px-6 py-4 text-center">${grandTotalKg9 > 0 ? grandTotalKg9.toLocaleString('vi-VN') : ''}</td>
        `;

        const totalAmountRow = tableBody.insertRow();
        totalAmountRow.className = `font-extrabold text-green-800`;
        totalAmountRow.innerHTML = `
            <td class="px-2 sm:px-6 py-4 text-center">TỔNG THÀNH TIỀN</td>
            <td class="px-2 sm:px-6 py-4 text-center" colspan="2">${grandTotalAmount > 0 ? grandTotalAmount.toLocaleString('vi-VN') + ' đ' : ''}</td>
        `;

        document.querySelectorAll('.stat-detail-row').forEach(row => row.style.display = 'none');
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
    function confirm(e){return new Promise(t=>{const o=document.getElementById("custom-confirm");o&&o.remove();const n=document.createElement("div");n.id="custom-confirm",n.className="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50",n.innerHTML=`<div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full" style="padding:1rem!important;"><p class="text-lg font-semibold text-gray-800 mb-4">${e}</p><div class="flex justify-end gap-3"><button id="confirm-cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg transition-colors duration-200">Hủy</button><button id="confirm-ok" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">OK</button></div></div>`,document.body.appendChild(n),document.getElementById("confirm-ok").onclick=()=>{n.remove(),t(!0)},document.getElementById("confirm-cancel").onclick=()=>{n.remove(),t(!1)}})}

	document.addEventListener("DOMContentLoaded", async () => {
		// ƯU TIÊN: Thiết lập thư mục dữ liệu (activeDataDir) ngay khi trang tải
		if (userType === 'thuky_v7') {
			activeDataDir = 'Data_vua7/';
		} else if (userType === 'thuky_v9') {
			activeDataDir = 'Data_vua9/';
		} else if (userType === 'quanly') {
			activeDataDir = document.getElementById('vuaSelectorTab1').value;
		}
		// THÊM VÀO ĐÂY: Tắt tự động điền cho toàn bộ trang
		document.querySelectorAll('input, form').forEach(element => {
			element.setAttribute('autocomplete', 'off');
		});
		// THÊM VÀO ĐÂY: Tự động tải lại danh sách khi thay đổi kích thước màn hình
		let isMobileView = window.innerWidth <= 768;
		window.addEventListener('resize', () => {
			const newIsMobileView = window.innerWidth <= 768;
			// Chỉ thực hiện khi có sự thay đổi giữa mobile và desktop
			if (newIsMobileView !== isMobileView) {
				isMobileView = newIsMobileView;
				
				// Kiểm tra xem tab nào đang được mở
				const activeTab = document.querySelector('.tab-content.active');
				if (activeTab) {
					if (activeTab.id === 'tab1') {
						// Nếu là tab "Thông tin lái", render lại danh sách lái
						renderDriverList();
					} else if (activeTab.id === 'tab2') {
						// Nếu là tab "Nhập Toa", render lại bảng báo cáo
						filterReportsByDate();
					}
				}
			}
		});		
		// SỬA LỖI: Tải cài đặt cho TẤT CẢ người dùng, không chỉ riêng quản lý
		await loadVerificationSetting(); 

		// ---- Các phần khởi tạo khác giữ nguyên ----
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
		window.onclick = function(event) {
			const reportModal = document.getElementById('reportFormModal');
			if (event.target == reportModal) {
				closeReportModal();
				return;
			}
			document.querySelectorAll('.qr-modal, .invoice-modal, .driver-usage-modal, .summary-modal, .user-modal, .deeplink-modal, .driver-form-modal').forEach(modal => {
				if (event.target == modal) {
					closeModal(modal.id);
				}
			});
			const isClickOutsideSearch = !driverSearchInput.contains(event.target) && !driverSearchResults.contains(event.target);
			if (isClickOutsideSearch) {
				 driverSearchResults.classList.add('hidden');
			}
		}

		loadBanksIntoDropdown();
		flatpickr("#reportDate",{dateFormat:"d/m/Y",locale:"vn",defaultDate:new Date,onChange:function(e,t,o){filterReportsByDate()}});
		
		if (userType === 'quanly') {
			const vuaSelector1 = document.getElementById('vuaSelectorTab1');
			const vuaSelector2 = document.getElementById('vuaSelectorTab2');
			
			vuaSelector1.addEventListener('change', (e) => {
				activeDataDir = e.target.value;
				vuaSelector2.value = activeDataDir;
				loadDriverList();
			});
			vuaSelector2.addEventListener('change', (e) => {
				activeDataDir = e.target.value;
				vuaSelector1.value = activeDataDir;
				loadDataForCurrentVua();
			});

			flatpickr("#statsDate",{dateFormat:"d/m/Y",locale:"vn",defaultDate:new Date,onChange:function(){loadStats()}});
			document.getElementById('userForm').addEventListener('submit', handleUserFormSubmit);
		}

		openTab("tab1");
	});
    </script>
</body>
</html>
