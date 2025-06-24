<?php
require 'connexion à la base.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $type = $_POST['type'];
    $content = $_POST['content'];

    if ($type === 'image' && isset($_FILES['image_file'])) {
        $target = 'uploads/'.uniqid('img_').'.'.pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['image_file']['tmp_name'], $target);
        $content = $target;
    }

    $stmt = $pdo->prepare("UPDATE redirections SET url = ?, type = ? WHERE client_id = ?");
    $stmt->execute([$content, $type, $client_id]);

    // Regénérer le QR code
    require 'phpqrcode/qrlib.php';
    $qr_link = "https://votredomaine.com/redirection%20dynamique.php?client_id=".urlencode($client_id);
    QRcode::png($qr_link, "qrcodes/".$client_id.".png");

    header("Location: interface%20d%20admin.php");
    exit;
}
?>