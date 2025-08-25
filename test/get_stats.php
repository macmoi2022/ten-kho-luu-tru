<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'quanly') {
    echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$date = $input['date'] ?? date('d/m/Y');
$date_key = str_replace('/', '-', $date);

// Hàm tính giá trị từ chuỗi có phép tính "+", "-"
function getValueFromExpression($value) {
    if (!is_string($value) || trim($value) === '') {
        return 0.0;
    }
    // Dọn dẹp chuỗi, thay thế các toán tử trùng lặp
    $cleanedValue = preg_replace('/\s+/', '', $value);
    $cleanedValue = str_replace('--', '+', $cleanedValue);
    $cleanedValue = str_replace('++', '+', $cleanedValue);
    $cleanedValue = str_replace('-+', '-', $cleanedValue);
    $cleanedValue = str_replace('+-', '-', $cleanedValue);

    // Tìm tất cả các số (có thể có dấu +/- ở đầu)
    preg_match_all('/[+-]?[0-9.]+/', $cleanedValue, $matches);
    
    if (empty($matches[0])) {
        return 0.0;
    }

    // Tính tổng các số tìm được
    $sum = 0.0;
    foreach ($matches[0] as $part) {
        $sum += (float)$part;
    }
    return $sum;
}

function get_daily_summary($data_folder, $date_key) {
    $summary = [
        'total_kg' => 0,
        'total_amount' => 0,
        'details' => []
    ];

    $file_path = $data_folder . 'daily_reports_' . $date_key . '.csv';

    if (!file_exists($file_path)) {
        return $summary;
    }

    $file = fopen($file_path, 'r');
    if ($file === false) return $summary;

    $headers_raw = fgetcsv($file);
    if ($headers_raw === false) {
        fclose($file);
        return $summary;
    }
    
    $headers = array_map('trim', $headers_raw);
    
    $quantity_columns = [
        'B1_Thái', 'B2_Thái', 'C1_Thái', 'C2_Thái', 'C3_Thái', 'D1_Thái', 'D2_Thái', 'E_Thái', 'Chợ_Thái', 'Xơ_Thái',
        'A1_indo', 'A2_indo', 'B1_indo', 'B2_indo', 'B3_indo', 'C1_indo', 'C2_indo', 'Chợ_1_indo', 'Chợ_2_indo', 'Xơ_indo'
    ];

    while (($row_data = fgetcsv($file)) !== false) {
        // Đảm bảo số lượng phần tử khớp với header
        if (count($row_data) != count($headers)) continue;
        
        $report = array_combine($headers, $row_data);

        // Bỏ qua "toa đi bán"
        if (($report['toaDiBan'] ?? 'false') === 'true') {
            continue;
        }

        // Tính toán lại Thành Tiền cho mỗi toa hợp lệ
        $baseAmount = 0;
        foreach ($quantity_columns as $col_name) {
            $qty = getValueFromExpression($report[$col_name] ?? '0');
            $price = getValueFromExpression($report['Giá_' . $col_name] ?? '0');
            $baseAmount += $qty * $price;
        }

        $adjustment = getValueFromExpression($report['tuyChonTien'] ?? '0');
        $finalAmount = $baseAmount + $adjustment;
        
        // Nếu là "không thanh toán", thành tiền = 0
        if (($report['khongThanhToan'] ?? 'false') === 'true') {
            $finalAmount = 0;
        }

        // Cộng dồn vào tổng của vựa
        $summary['total_amount'] += $finalAmount;

        // Tính toán tổng KG và chi tiết KG
        $totalKgThisRow = 0;
        foreach ($quantity_columns as $col_name) {
            $kg = getValueFromExpression($report[$col_name] ?? '0');
            $totalKgThisRow += $kg;
            if (!isset($summary['details'][$col_name])) {
                $summary['details'][$col_name] = ['kg' => 0];
            }
            $summary['details'][$col_name]['kg'] += $kg;
        }
        $summary['total_kg'] += $totalKgThisRow;
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
