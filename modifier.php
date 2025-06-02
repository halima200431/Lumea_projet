<?php
require 'db.php';

$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
    $contenu = $_POST['contenu'];
    $stmt = $pdo->prepare("UPDATE qr_codes SET contenu = ?, date_modification = NOW() WHERE code_id = ?");
    $stmt->execute([$contenu, $id]);
    echo "<p>Contenu mis à jour. <a href='qr.php?id=$id'>Voir</a></p>";
    exit;
}

if ($id) {
    $stmt = $pdo->prepare("SELECT contenu FROM qr_codes WHERE code_id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier le contenu</title>
  <style>
    body { font-family: Arial; background: #ffe6f0; padding: 2rem; color: #800040; }
    .container { background: #fff0f5; padding: 2rem; border-radius: 15px; max-width: 500px; margin: auto; }
    textarea, button { width: 100%; margin-top: 1rem; padding: 0.5rem; }
    button { background: #ff66b2; color: white; border: none; border-radius: 8px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Modifier le contenu</h1>
    <form method="post">
      <textarea name="contenu" rows="4"><?php echo htmlspecialchars($data['contenu'] ?? '') ?></textarea>
      <button type="submit">Mettre à jour</button>
    </form>
  </div>
</body>
</html>
