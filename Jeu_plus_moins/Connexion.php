<?php
    require 'config.php';
    if(isset($_POST['envoie'])){
        if(!empty($_POST['pseudo']) AND !empty($_POST['password']) AND !empty($_POST['email'])){
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $mdp = $_POST['password'];
            $email = htmlspecialchars($_POST['email']);
           
            $recupUser = $pdo->prepare('SELECT * FROM register WHERE pseudo = ? AND email = ?');
            $recupUser->execute(array($pseudo,$email));

            $user = $recupUser->fetch();

            if($user && password_verify($mdp, $user['password'])){
                $_SESSION['pseudo'] = $pseudo;
                $_SESSION['password'] = ($mdp);
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $user['id'];
                header('Location: homepage.php');
            }else{
                echo "Ascreugneugneu";
            }
        }else{
            echo "Vous n'avez pas remplie tout les champs.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>tchitcho</title>
    <link rel="stylesheet" href="style.css">
</head>
<body >
    <header>
    </header>
    <main>
        <button>
            <a href="homepage.php">retourner a la home page</a>
        </button>
        <form method="POST" align="center">
            <input type="text" name="pseudo" placeholder="Tanoxy" autocomplete="off"><br>
            <input type="password" name="password" placeholder="PassWord" autocomplete="off"><br>
            <input type="email" name="email" placeholder="Tanoxy@gmail.com" autocomplete="off"><br>
            <input type="submit" name="envoie">
        </form>
    </main>
    <footer>
    </footer>
</body>
</html>