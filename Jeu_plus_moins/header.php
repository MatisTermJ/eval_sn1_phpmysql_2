<header class="site-header">
    <div class="container">
        <a class="logo" href="index.php">± Plus ou Moins<span>.</span></a>
        <nav class="nav">
            <a href="jeux.php">Jouer</a>
            <a href="Classement_bouton.php">Classement</a>
            <?php if (isset($_SESSION['id'], $_SESSION['name'])): ?>
                <a href="stats.php">Mes stats</a>
                <span><?php echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                <a href="Deconnexion.php">Se déconnecter</a>
            <?php else: ?>
                <a href="Connexion.php">Connexion</a>
                <a href="Register.php">Créer un compte</a>
            <?php endif; ?>
        </nav>
    </div>
</header>