<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Redirections - LUMEA</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:600,700&display=swap" rel="stylesheet">
    <style>
        /* Style unifié avec generateur de code */
        body {
            background-color: #30010b;
            font-family: 'Montserrat', Arial, sans-serif;
            color: #fff;
            margin: 0;
            padding: 40px 20px;
        }
        
        .logo-header {
            position: fixed;
            top: 20px;
            right: 30px;
        }
        
        .logo-header img {
            height: 100px;
        }
        
        .lumea-title {
            font-size: 3em;
            color: #fb0062;
            text-align: center;
            margin: 20px 0 40px;
            text-shadow: 0 2px 10px rgba(251, 0, 98, 0.5);
        }
        
        /* Conteneur tableau */
        .table-container {
            background: #ffd6e3;
            border-radius: 20px;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(251, 0, 98, 0.2);
            border: 2px solid #fb0062;
        }
        
        h1 {
            color: #fb0062;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }
        
        /* Style tableau */
        .redirection-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .redirection-table th {
            background: #fb0062;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .redirection-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ffebf1;
            vertical-align: middle;
        }
        
        .redirection-table tr:last-child td {
            border-bottom: none;
        }
        
        .redirection-table tr:nth-child(even) {
            background: #fff6fa;
        }
        
        /* Badges types */
        .type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .type-url { background: #e3f2fd; color: #0d47a1; }
        .type-text { background: #e8f5e9; color: #1b5e20; }
        .type-image { background: #f3e5f5; color: #4a148c; }
        
        /* QR Code */
        .qr-img {
            width: 60px;
            height: 60px;
            border: 2px solid #fb0062;
            border-radius: 10px;
            padding: 5px;
            background: white;
        }
        
        /* Boutons */
        .action-btn {
            background: #fb0062;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s;
        }
        
        .action-btn:hover {
            background: #d5004f;
            transform: translateY(-1px);
        }
        
        .new-btn {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            background: #fb0062;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 4px 15px rgba(251, 0, 98, 0.3);
            transition: all 0.3s;
        }
        
        .new-btn:hover {
            background: #d5004f;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(251, 0, 98, 0.4);
        }
    </style>
</head>
<body>

<div class="logo-header">
    <img src="LOGO/LOGO FINAAAAL.png" alt="Logo LUMEA">
</div>

<div class="lumea-title">LUMEA</div>

<div class="table-container">
    <h1>Liste des redirections</h1>
    
    <table class="redirection-table">
        <thead>
            <tr>
                <th>Client ID</th>
                <th>Type</th>
                <th>Contenu</th>
                <th>QR Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require 'connexion à la base.php';
            $stmt = $pdo->query("SELECT * FROM redirections ORDER BY created_at DESC");
            
            while ($row = $stmt->fetch()):
                $content = $row['type'] === 'image' 
                    ? basename($row['url'])
                    : (strlen($row['url']) > 30 ? substr($row['url'], 0, 30).'...' : $row['url']);
            ?>
            <tr>
                <td><strong><?= htmlspecialchars($row['client_id']) ?></strong></td>
                <td><span class="type-badge type-<?= $row['type'] ?>"><?= $row['type'] ?></span></td>
                <td title="<?= htmlspecialchars($row['url']) ?>"><?= htmlspecialchars($content) ?></td>
                <td><img src="qrcodes/<?= $row['client_id'] ?>.png" class="qr-img" alt="QR Code"></td>
                <td>
                    <form method="post" action="mise a jour redirection.php">
                        <input type="hidden" name="client_id" value="<?= $row['client_id'] ?>">
                        <button type="submit" class="action-btn">Modifier</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <a href="generateur de code.php" class="new-btn">+ Nouvelle Redirection</a>
</div>

</body>
</html>