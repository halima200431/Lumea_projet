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
  session_start();
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  require 'db.php';
  require 'phpqrcode/qrlib.php';

  
  $contenu = '';
  $code_id = '';
  $mode = $_POST['mode'] ?? 'fixe';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $contenu = $_POST['contenu'];
      $mode = $_POST['mode'];

      if ($mode === 'fixe') {
          // QR code encode le texte directement (non modifiable)
          $qr_content = $contenu;
          // Pas de base de données ici
      } else {
          // Mode dynamique : QR code encode l'URL, texte modifiable
          if (isset($_POST['update']) && $_POST['update'] === 'yes') {
              $code_id = $_POST['code_id'];
              $stmt = $pdo->prepare("UPDATE qr_codes SET contenu = ?, date_modification = NOW() WHERE code_id = ?");
              $stmt->execute([$contenu, $code_id]);
              echo "<p>✅ Contenu mis à jour !</p>";
          } else {
              $code_id = uniqid('qr_', true);
              $stmt = $pdo->prepare("INSERT INTO qr_codes (code_id, contenu) VALUES (?, ?)");
              $stmt->execute([$code_id, $contenu]);
          }
          $url = "http://192.168.1.42/qr.php?id=$code_id";
          $qr_content = $url;
          $_SESSION['last_code_id'] = $code_id;
      }

      // Générer le QR code en image PNG dans un buffer
      ob_start();
      QRcode::png($qr_content, null, QR_ECLEVEL_L, 6);
      $imageData = ob_get_contents();
      ob_end_clean();

      // Afficher l'image en base64
      $base64 = base64_encode($imageData);
      $_SESSION['qr_image'] = $base64;

      echo "<div id='qrcode' style='text-align:center; margin-top:2rem;'>";
      echo "<img src='data:image/png;base64,$base64' alt='QR Code'><br>";
      echo "<a href='download_qr.php' download>
              <button type='button' style='margin-top:1rem;'>Télécharger le QR code</button>
            </a>";
      echo "</div>";

      if ($mode === 'dynamique') {
          echo "<p>QR généré vers : <a href='$url' target='_blank'>$url</a></p>";
      }
  } elseif (isset($_SESSION['last_code_id']) && $mode === 'dynamique') {
      $code_id = $_SESSION['last_code_id'];
      $stmt = $pdo->prepare("SELECT contenu FROM qr_codes WHERE code_id = ?");
      $stmt->execute([$code_id]);
      $data = $stmt->fetch();
      if ($data) {
          $contenu = $data['contenu'];
          $url = "http://192.168.1.42/qr.php?id=$code_id";
          $qr_content = $url;
          ob_start();
          QRcode::png($qr_content, null, QR_ECLEVEL_L, 6);
          $imageData = ob_get_contents();
          ob_end_clean();
          $base64 = base64_encode($imageData);
          echo "<div id='qrcode' style='text-align:center; margin-top:2rem;'>";
          echo "<img src='data:image/png;base64,$base64' alt='QR Code'>";
          echo "</div>";
          echo "<p>QR généré vers : <a href='$url' target='_blank'>$url</a></p>";
      }
  }
  ?>

  <form method="post">
    <label for="contenu">Texte à associer :</label>
    <textarea name="contenu" id="contenu" rows="4" required><?php echo htmlspecialchars($contenu); ?></textarea>
    <div style="margin-bottom:1rem;">
      <label>
        <input type="radio" name="mode" value="fixe" <?php if($mode==='fixe') echo 'checked'; ?>> QR code texte fixe (non modifiable)
      </label>
      <label>
        <input type="radio" name="mode" value="dynamique" <?php if($mode==='dynamique') echo 'checked'; ?>> QR code dynamique (modifiable)
      </label>
    </div>
    <?php if ($mode === 'dynamique' && $code_id): ?>
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
