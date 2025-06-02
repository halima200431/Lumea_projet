<?php
session_start();
if (!isset($_SESSION['qr_image'])) {
    http_response_code(404);
    exit('QR code non trouvé.');
}
header('Content-Type: image/png');
header('Content-Disposition: attachment; filename="qrcode.png"');
echo base64_decode($_SESSION['qr_image']);
exit;
?>