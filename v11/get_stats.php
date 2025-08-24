<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'quanly') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$date = $input['date'] ?? date('d-m-Y');
$date_key = str_replace('/', '-', $date);

function get_daily_summary($data_folder, $date_key) {
    $summary = [
        'total_kg' => 0,
        'total_amount' => 0,
        'total_kg_thai' => 0,
        'total_kg_indo' => 0,
        'details' => []
    ];

    $file_path = $data_folder . 'daily_reports_' . $date_key . '.csv';

    if (!file_exists($file_path)) {
        return $summary;
    }

    $file = fopen($file_path, 'r');
    if ($file === false) return $summary;

    $headers = fgetcsv($file);
    if ($headers === false) {
        fclose($file);
        return $summary;
    }
    
    $header_map = array_flip(array_map('trim', $headers));

    $quantity_columns = [
        'B1_Thái', 'B2_Thái', 'C1_Thái', 'C2_Thái', 'C3_Thái', 'D1_Thái', 'D2_Thái', 'E_Thái', 'Chợ_Thái', 'Xơ_Thái',
        'A1_indo', 'A2_indo', 'B1_indo', 'B2_indo', 'B3_indo', 'C1_indo', 'C2_indo', 'Chợ_1_indo', 'Chợ_2_indo', 'Xơ_indo'
    ];

    while (($row = fgetcsv($file)) !== false) {
        // Bỏ qua "toa đi bán"
        $isToaDiBan = isset($header_map['toaDiBan']) && isset($row[$header_map['toaDiBan']]) && $row[$header_map['toaDiBan']] === 'true';
        if ($isToaDiBan) {
            continue;
        }

        $isKhongThanhToan = isset($header_map['khongThanhToan']) && isset($row[$header_map['khongThanhToan']]) && $row[$header_map['khongThanhToan']] === 'true';

        $row_total_kg = isset($header_map['Tổng_KG']) ? (float)($row[$header_map['Tổng_KG']] ?? 0) : 0;
        $row_total_amount = $isKhongThanhToan ? 0 : (isset($header_map['Thành_Tiền']) ? (float)($row[$header_map['Thành_Tiền']] ?? 0) : 0);

        $summary['total_kg'] += $row_total_kg;
        $summary['total_amount'] += $row_total_amount;

        $baseAmount = 0;
        foreach ($quantity_columns as $col_name) {
             if (isset($header_map[$col_name]) && isset($header_map['Giá_' . $col_name])) {
                $kg_expression = $row[$header_map[$col_name]] ?? '0';
                $kg = 0;
                if (strpos($kg_expression, '+') !== false) {
                    $parts = explode('+', $kg_expression);
                    foreach ($parts as $part) { $kg += (float)$part; }
                } else {
                    $kg = (float)$kg_expression;
                }
                $price = (float)($row[$header_map['Giá_' . $col_name]] ?? 0);
                $baseAmount += $kg * $price;
            }
        }
        
        $ratio = ($baseAmount > 0) ? ($row_total_amount / $baseAmount) : 0;

        foreach ($quantity_columns as $col_name) {
            if (isset($header_map[$col_name]) && isset($header_map['Giá_' . $col_name])) {
                $kg_expression = $row[$header_map[$col_name]] ?? '0';
                $kg = 0;
                if (strpos($kg_expression, '+') !== false) {
                    $parts = explode('+', $kg_expression);
                    foreach ($parts as $part) { $kg += (float)$part; }
                } else {
                    $kg = (float)$kg_expression;
                }

                if ($kg > 0) {
                    $price = (float)($row[$header_map['Giá_' . $col_name]] ?? 0);
                    $amount = ($kg * $price) * $ratio;

                    if (!isset($summary['details'][$col_name])) {
                        $summary['details'][$col_name] = ['kg' => 0, 'amount' => 0];
                    }
                    $summary['details'][$col_name]['kg'] += $kg;
                    $summary['details'][$col_name]['amount'] += $amount;

                    if (strpos($col_name, '_Thái') !== false) {
                        $summary['total_kg_thai'] += $kg;
                    } elseif (strpos($col_name, '_indo') !== false) {
                        $summary['total_kg_indo'] += $kg;
                    }
                }
            }
        }
    }
    fclose($file);
    return $summary;
}

$stats = [
    'vua7' => get_daily_summary('Data_vua7/', $date_key),
    'vua9' => get_daily_summary('Data_vua9/', $date_key)
];

echo json_encode(['success' => true, 'data' => $stats]);
?>
