<?php
session_start();


if (!isset($_SESSION['nb_cherche'])) {
    $_SESSION['nb_cherche'] = rand(1, 10);
}

$message = "";


if (isset($_POST['register']) && $_POST['nb'] != '') {
    $nb = (int) $_POST['nb'];

    if ($nb > $_SESSION['nb_cherche']) {
        $message = "Le nombre est plus petit";
    }
    elseif ($nb < $_SESSION['nb_cherche']) {
        $message = "Le nombre est plus grand";
    }
    else {
        $message = "Bravo, tu as trouvé !";

        unset($_SESSION['nb_cherche']);
    }
}
?>

<h1>Entrez un nombre</h1>
<form method="post" action="">
    <label>Nombre ? : </label>
    <input type="number" name="nb"><br/>
    <input type="submit" value="Valider" name="register">
</form>

<p><?php echo $message; ?></p>