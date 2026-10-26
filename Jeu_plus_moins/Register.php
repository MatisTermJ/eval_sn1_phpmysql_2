
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
        $message = 'Vous n’avez pas tout renseigné.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Veuillez saisir une adresse e-mail valide.';
    } else {
        $verif = $pdo->prepare('SELECT id FROM players WHERE name = ? OR email = ?');
        $verif->execute([$pseudo, $email]);

        if ($verif->fetch()) {
            $message = 'Ce pseudo ou cet e-mail est déjà utilisé.';
        } else {
            try {
                $insert = $pdo->prepare(
                    'INSERT INTO players (name, email, password) VALUES (?, ?, ?)'
                );
                $insert->execute([
                    $pseudo,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                ]);

                $_SESSION['id'] = (int) $pdo->lastInsertId();
                $_SESSION['name'] = $pseudo;
                $_SESSION['email'] = $email;

                header('Location: Homepage.php');
                exit;
            } catch (PDOException $exception) {
                error_log('Inscription impossible : ' . $exception->getMessage());
                $message = 'Une erreur est survenue pendant la création du compte.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <button>
            <a href="homepage.php">retourner a la home page</a>
        </button>
        <form method="POST" align="center">
            <input type="text" name="pseudo" placeholder="Pseudo" autocomplete="username" required><br>
            <input type="password" name="password" placeholder="Mot de passe" autocomplete="new-password" required><br>
            <input type="email" name="email" placeholder="Adresse e-mail" autocomplete="email" required><br>
            <input type="submit" name="envoie">
        </form>
        <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    </main>
    <footer>
    </footer>
</body>
</html>
