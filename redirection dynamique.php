<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Redirection LUMEA</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:600,700&display=swap" rel="stylesheet">
</head>
<body>

<div class="logo-header">
  <img src="LOGO/LOGO FINAAAAL.png" alt="Logo LUMEA">
</div>

<div class="lumea-title">LUMEA</div>

<div class="container">
<?php
require 'connexion à la base.php';

if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];
    $stmt = $pdo->prepare("SELECT url, type FROM redirections WHERE client_id = ?");
    $stmt->execute([$client_id]);
    $row = $stmt->fetch();

    if ($row) {
        if ($row['type'] === 'url') {
            header("Location: " . $row['url']);
            exit;
        } elseif ($row['type'] === 'text') {
            echo "<h1>Contenu texte</h1>";
            echo "<div class='content-box'>" . nl2br(htmlspecialchars($row['url'])) . "</div>";
        } elseif ($row['type'] === 'image') {
            echo "<h1>Image</h1>";
            echo "<img src='" . htmlspecialchars($row['url']) . "' class='content-image'>";
        }
    } else {
        echo "<div class='error'>Aucune redirection trouvée</div>";
    }
} else {
    echo "<div class='error'>Paramètre client_id manquant</div>";
}
?>
</div>

</body>
</html>
