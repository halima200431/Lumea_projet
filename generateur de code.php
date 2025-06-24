<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Générateur QR Code - LUMEA</title>
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
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require 'connexion à la base.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            if (empty($_POST['client_id'])) throw new Exception("Client ID requis");
            
            $client_id = trim($_POST['client_id']);
            $type = $_POST['type'];
            $value = '';

            switch ($type) {
                case 'url':
                    if (empty($_POST['url'])) throw new Exception("URL requise");
                    $value = filter_var($_POST['url'], FILTER_VALIDATE_URL);
                    if (!$value) throw new Exception("URL invalide");
                    break;
                    
                case 'text':
                    if (empty($_POST['text'])) throw new Exception("Texte requis");
                    $value = htmlspecialchars($_POST['text']);
                    break;
                    
                case 'image':
                    if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
                        throw new Exception("Image requise");
                    }
                    $target = 'uploads/'.uniqid('img_').'.'.pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
                    if (!is_dir('uploads')) mkdir('uploads', 0755, true);
                    move_uploaded_file($_FILES['image_file']['tmp_name'], $target);
                    $value = $target;
                    break;
                    
                default: throw new Exception("Type invalide");
            }

            $stmt = $pdo->prepare("REPLACE INTO redirections (client_id, url, type) VALUES (?, ?, ?)");
            $stmt->execute([$client_id, $value, $type]);

            require_once 'phpqrcode/qrlib.php';
            if (!is_dir('qrcodes')) mkdir('qrcodes', 0755, true);
            
            $qr_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/redirection%20dynamique.php?client_id=" . urlencode($client_id);
            $qr_file = "qrcodes/{$client_id}.png";
            
            QRcode::png($qr_link, $qr_file, QR_ECLEVEL_L, 3, 1);

            echo '<h1>QR Code généré</h1>
                  <div class="result-box">
                      <img src="'.$qr_file.'" class="qr-image">
                      <div class="qr-details">
                          <div class="detail-row">
                              <span class="detail-label">Client ID:</span>
                              <span class="detail-value">'.htmlspecialchars($client_id).'</span>
                          </div>
                          <div class="detail-row">
                              <span class="detail-label">Type:</span>
                              <span class="detail-value">'.htmlspecialchars($type).'</span>
                          </div>
                      </div>
                      <div class="action-buttons">
                          <a href="generateur%20de%20code.php" class="action-btn new-btn">Nouveau QR</a>
                          <a href="interface%20d%20admin.php" class="action-btn view-btn">Voir tous</a>
                      </div>
                  </div>';
            exit;

        } catch (Exception $e) {
            echo '<div class="error-message">'.$e->getMessage().'</div>';
        }
    }
    ?>

    <h1>Générateur de QR Code</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Client ID:</label>
            <input type="text" name="client_id" required placeholder="ex: music123">
        </div>
        
        <div class="form-group">
            <label>Type:</label>
            <select name="type" id="typeSelect" required>
                <option value="url">URL</option>
                <option value="text">Texte</option>
                <option value="image">Image</option>
            </select>
        </div>
        
        <div class="form-group" id="urlField">
            <label>URL:</label>
            <input type="url" name="url" placeholder="https://exemple.com">
        </div>
        
        <div class="form-group" id="textField" style="display:none;">
            <label>Texte:</label>
            <textarea name="text" placeholder="Votre texte ici..."></textarea>
        </div>
        
        <div class="form-group" id="imageField" style="display:none;">
            <label>Image:</label>
            <input type="file" name="image_file" accept="image/*">
        </div>
        
        <button type="submit" class="generate-btn">Générer</button>
    </form>
</div>

<script>
document.getElementById('typeSelect').addEventListener('change', function() {
    const type = this.value;
    document.getElementById('urlField').style.display = type === 'url' ? 'block' : 'none';
    document.getElementById('textField').style.display = type === 'text' ? 'block' : 'none';
    document.getElementById('imageField').style.display = type === 'image' ? 'block' : 'none';
});
</script>

</body>
</html>