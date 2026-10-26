<?php
require 'Config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Plus ou Moins</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <?php if (isset($_SESSION['id'], $_SESSION['name'])): ?>
            <p>Bonjour <?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>, vous êtes connecté.</p>
            <a href="jeux.php">Jouer</a>
            <a href="Deconnexion.php">Se déconnecter</a>
        <?php else: ?>
            <a href="Register.php">Créer un compte</a>
            <a href="Connexion.php">Se connecter</a>
        <?php endif; ?>
    </main>
    <footer>
    </footer>
</body>
</html>