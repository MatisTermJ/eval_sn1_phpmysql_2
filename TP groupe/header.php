<header class="site-header">
    <div class="container">
        <a class="logo" href="index.php">± Plus ou Moins<span>.</span></a>
        <nav class="nav">
            <a href="index.php">Jouer</a>
            <a href="#">Classement</a>
            <?php
            if(isset($_SESSION['name'])){
                echo "<a href='#'>Mes stats</a>
                      <p>{$_SESSION['name']} | <a href='#'>Se déconnecter</a></p>";
            }else{
                echo "<a href='#'>Connexion</a>
                <a href='#'>Créer un compte</a>";
            } ?>
        </nav>
    </div>
</header>