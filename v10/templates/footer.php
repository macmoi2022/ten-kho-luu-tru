<?php
    // Include tất cả các modal
    $modals = ['driver_form', 'qr', 'invoice', 'driver_usage', 'summary', 'user', 'report_form'];
    foreach ($modals as $modal) {
        include __DIR__ . "src/{$modal}.php";
    }
?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    // Truyền biến PHP sang JavaScript một cách an toàn
    const userType = '<?php echo json_encode($user_type); ?>';
</script>
<script src="assets/js/main.js"></script>
</body>
</html>