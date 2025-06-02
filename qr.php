<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT contenu FROM qr_codes WHERE code_id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        echo "<h1>Contenu associé :</h1>";
        echo "<p>" . htmlspecialchars($data['contenu']) . "</p>";
    } else {
        echo "<p>ID inconnu</p>";
    }
} else {
    echo "<p>Aucun ID fourni</p>";
}

$qr_content = $contenu; // Le QR code encode le texte directement
?>
