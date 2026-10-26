<?php
require 'Config.php';

$message = '';

if (isset($_POST['envoie'])) {
    $pseudo = isset($_POST['pseudo']) && is_string($_POST['pseudo'])
        ? trim($_POST['pseudo'])
        : '';
    $password = isset($_POST['password']) && is_string($_POST['password'])
        ? $_POST['password']
        : '';
    $email = isset($_POST['email']) && is_string($_POST['email'])
        ? trim($_POST['email'])
        : '';

    if ($pseudo === '' || $password === '' || $email === '') {
        $message = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $recupUser = $pdo->prepare(
                'SELECT id, name, email, password
                 FROM players
                 WHERE name = ? AND email = ?
                 LIMIT 1'
            );
            $recupUser->execute([$pseudo, $email]);
            $user = $recupUser->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['id'] = (int) $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];

                header('Location: Homepage.php');
                exit;
            }

            $message = 'Pseudo, e-mail ou mot de passe incorrect.';
        } catch (PDOException $exception) {
            error_log('Connexion impossible : ' . $exception->getMessage());
            $message = 'Une erreur est survenue pendant la connexion.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <a href="Homepage.php">Retour à l’accueil</a>
        <form method="POST" align="center">
            <input type="text" name="pseudo" placeholder="Pseudo" autocomplete="username" required><br>
            <input type="password" name="password" placeholder="Mot de passe" autocomplete="current-password" required><br>
            <input type="email" name="email" placeholder="Adresse e-mail" autocomplete="email" required><br>
            <input type="submit" name="envoie" value="Se connecter">
        </form>
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    </main>
    <footer>
    </footer>
</body>
</html>