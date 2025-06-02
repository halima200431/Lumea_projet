<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>QR Code Dynamique - Rose</title>
  <script src="https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #ffe6f0;
      color: #800040;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 2rem;
    }
    .container {
      background: #fff0f5;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 0 15px #ffb6c1;
      width: 100%;
      max-width: 500px;
    }
    label, textarea, button {
      width: 100%;
      margin-bottom: 1rem;
    }
    button {
      background-color: #ff66b2;
      color: white;
      border: none;
      padding: 0.75rem;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background-color: #ff3399;
    }
    #qrcode {
      margin-top: 2rem;
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <h1>QR Code Dynamique</h1>

  <?php
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require 'db.php';
  $contenu = '';
  $code_id = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['update']) && $_POST['update'] === 'yes') {
          $code_id = $_POST['code_id'];
          $contenu = $_POST['contenu'];
          $stmt = $pdo->prepare("UPDATE qr_codes SET contenu = ?, date_modification = NOW() WHERE code_id = ?");
          $stmt->execute([$contenu, $code_id]);
          echo "<p>✅ Contenu mis à jour !</p>";
      } else {
          $contenu = $_POST['contenu'];
          $code_id = uniqid('qr_', true);
          $stmt = $pdo->prepare("INSERT INTO qr_codes (code_id, contenu) VALUES (?, ?)");
          $stmt->execute([$code_id, $contenu]);
      }

      $url = "http://localhost/qr.php?id=$code_id";
      echo "<div id='qrcode'></div>";
      echo "<script>
        const target = document.getElementById('qrcode');
        QRCode.toCanvas(target, '$url', function (err, canvas) {
            if (err) console.error(err);
            else console.log('QR généré');
        });
      </script>";
      echo "<p>QR généré vers : <a href='$url' target='_blank'>$url</a></p>";
  }
  ?>

  <form method="post">
    <label for="contenu">Texte à associer :</label>
    <textarea name="contenu" id="contenu" rows="4" required><?php echo htmlspecialchars($contenu); ?></textarea>
    <?php if ($code_id): ?>
      <input type="hidden" name="code_id" value="<?php echo $code_id; ?>">
      <input type="hidden" name="update" value="yes">
      <button type="submit">Mettre à jour</button>
    <?php else: ?>
      <button type="submit">Générer</button>
    <?php endif; ?>
  </form>
</div>
</body>
</html>
