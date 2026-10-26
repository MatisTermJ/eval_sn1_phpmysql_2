<?php
session_start();

//Les niveaux disponibles 
$niveaux = [
    'facile'    => ['nom' => 'Facile',    'max' => 50,   'essais' => 10],
    'normal'    => ['nom' => 'Normal',    'max' => 100,  'essais' => 8],
    'difficile' => ['nom' => 'Difficile', 'max' => 500,  'essais' => 10],
    'expert'    => ['nom' => 'Expert',    'max' => 1000, 'essais' => 10],
];

$message = "";

//Retour au menu
if (isset($_POST['menu'])) {
    unset($_SESSION['jeu']);   // on efface la partie en cours
}

//Démarrage d'une partie (clic sur "Jouer")
if (isset($_POST['niveau']) && isset($niveaux[$_POST['niveau']])) {
    $n = $niveaux[$_POST['niveau']];

    $_SESSION['jeu'] = [
        'nom'        => $n['nom'],
        'max'        => $n['max'],
        'essais_max' => $n['essais'],
        'secret'     => rand(1, $n['max']),  // nombre à deviner
        'essais'     => [],                  // historique : [nombre, indice]
        'fini'       => false,
        'gagne'      => false,
    ];
}

// Traitement d'une proposition 
if (isset($_POST['proposer']) && isset($_SESSION['jeu']) && !$_SESSION['jeu']['fini']) {
    $jeu = $_SESSION['jeu'];   // copie de travail

    // filter_var renvoie false si ce n'est pas un entier valide
    $nb = filter_var($_POST['nb'], FILTER_VALIDATE_INT);

    if ($nb === false || $nb < 1 || $nb > $jeu['max']) {
        // proposition invalide : on ne compte pas l'essai
        $message = "Entrez un nombre entier entre 1 et " . $jeu['max'];
    }
    else {
        if ($nb < $jeu['secret']) {
            $indice = "C'est plus !";
        }
        elseif ($nb > $jeu['secret']) {
            $indice = "C'est moins !";
        }
        else {
            $indice = "Bravo, trouvé !";
            $jeu['fini']  = true;
            $jeu['gagne'] = true;
        }

        // on ajoute l'essai à l'historique
        $jeu['essais'][] = ['nb' => $nb, 'indice' => $indice];
        $message = $indice;

        // plus d'essais et pas trouvé : perdu
        if (!$jeu['gagne'] && count($jeu['essais']) >= $jeu['essais_max']) {
            $jeu['fini'] = true;
            $message = "Perdu ! Le nombre était " . $jeu['secret'];
        }
    }

    $_SESSION['jeu'] = $jeu;   // on remet la copie dans la session
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plus ou moins</title>
</head>
<body>

<?php if (!isset($_SESSION['jeu'])): ?>

    <!--ÉCRAN 1 : MENU-->
    <h1>Choisissez votre niveau</h1>

    <?php foreach ($niveaux as $cle => $n): ?>
        <form method="post" action="">
            <h2><?php echo $n['nom']; ?></h2>
            <p>
                Entre 1 et <?php echo $n['max']; ?><br>
                <?php echo $n['essais']; ?> essais
            </p>
            <!-- le bouton envoie la clé du niveau (facile, normal...) -->
            <button type="submit" name="niveau" value="<?php echo $cle; ?>">Jouer</button>
        </form>
        <hr>
    <?php endforeach; ?>

<?php else: ?>

    <!--ÉCRAN 2 : JEU-->
    <?php $jeu = $_SESSION['jeu']; ?>

    <h1>Niveau <?php echo $jeu['nom']; ?> - entre 1 et <?php echo $jeu['max']; ?></h1>
    <p>Essais restants : <?php echo $jeu['essais_max'] - count($jeu['essais']); ?> / <?php echo $jeu['essais_max']; ?></p>

    <h2><?php echo $message; ?></h2>

    <?php if (!$jeu['fini']): ?>
        <!-- formulaire affiché seulement si la partie continue -->
        <form method="post" action="">
            <input type="number" name="nb" placeholder="Votre nombre" min="1" max="<?php echo $jeu['max']; ?>">
            <button type="submit" name="proposer">Proposer</button>
        </form>
    <?php endif; ?>

    <h3>Vos essais</h3>
    <ul>
        <?php foreach ($jeu['essais'] as $essai): ?>
            <li><?php echo $essai['nb'] . " : " . $essai['indice']; ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- permet de quitter ou de rejouer -->
    <form method="post" action="">
        <button type="submit" name="menu">
            <?php echo $jeu['fini'] ? "Rejouer" : "Changer de niveau"; ?>
        </button>
    </form>

<?php endif; ?>

</body>
</html>