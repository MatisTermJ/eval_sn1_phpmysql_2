<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="jeux.css">
    <title>Plus ou Moins</title>
</head>
<body>
    <?php
    $_SESSION['name'] = "link";
    require "header.php";
    ?>
<?php

session_start();

// Niveaux disponibles pour le jeu
$niveaux = [
    'facile'    => ['nom' => 'Facile',    'max' => 50,   'essais' => 10],
    'normale'   => ['nom' => 'Normale',   'max' => 100,  'essais' => 8],
    'difficile' => ['nom' => 'Difficile', 'max' => 500,  'essais' => 10],
    'expert'    => ['nom' => 'Expert',    'max' => 1000, 'essais' => 10],
];

$message = '';

// Retour au choix du niveau
if (isset($_POST['menu'])) {
    unset($_SESSION['jeu']);
}

// Démarrage d'une partie avec les paramètres du niveau choisi
if (isset($_POST['niveau']) && isset($niveaux[$_POST['niveau']])) {
    $niveau = $niveaux[$_POST['niveau']];

    $_SESSION['jeu'] = [
        'nom'        => $niveau['nom'],
        'max'        => $niveau['max'],
        'essais_max' => $niveau['essais'],
        'secret'     => rand(1, $niveau['max']),
        'essais'     => [],
        'fini'       => false,
        'gagne'      => false,
    ];
}

// Traitement d'une proposition
if (isset($_POST['proposer']) && isset($_SESSION['jeu']) && !$_SESSION['jeu']['fini']) {
    $jeu = $_SESSION['jeu'];
    $nb = isset($_POST['nb']) ? filter_var($_POST['nb'], FILTER_VALIDATE_INT) : false;

    if ($nb === false || $nb < 1 || $nb > $jeu['max']) {
        $message = 'Entrez un nombre entier entre 1 et ' . $jeu['max'];
    } else {
        if ($nb < $jeu['secret']) {
            $indice = "C'est plus !";
        } elseif ($nb > $jeu['secret']) {
            $indice = "C'est moins !";
        } else {
            $indice = 'Bravo, trouvé !';
            $jeu['fini'] = true;
            $jeu['gagne'] = true;
        }

        $jeu['essais'][] = ['nb' => $nb, 'indice' => $indice];
        $message = $indice;

        if (!$jeu['gagne'] && count($jeu['essais']) >= $jeu['essais_max']) {
            $jeu['fini'] = true;
            $message = 'Perdu ! Le nombre était ' . $jeu['secret'];
        }
    }

    $_SESSION['jeu'] = $jeu;
}
?>
<?php if (!isset($_SESSION['jeu'])): ?>

    <!-- Choix du niveau de difficulté -->
    <h1>Choisissez votre niveau</h1>

    <div class ="menu">
    <form method="post" action="">
        <label for="niveau">Niveau de difficulté :</label>
        <select name="niveau" id="niveau" required>
            <option value="">-- Sélectionnez un niveau --</option>
            <?php foreach ($niveaux as $cle => $niveau): ?>
                <option value="<?php echo $cle; ?>">
                    <?php echo $niveau['nom']; ?> (1 à <?php echo $niveau['max']; ?>, <?php echo $niveau['essais']; ?> essais)
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Jouer"></input>
    </form>
    </div>

<?php else: ?>

    <!-- Jeu du plus ou moins -->
    <?php $jeu = $_SESSION['jeu']; ?>

    <h1>Niveau <?php echo $jeu['nom']; ?> - entre 1 et <?php echo $jeu['max']; ?></h1>
    <div class="menu">
    <p>Essais restants : <?php echo $jeu['essais_max'] - count($jeu['essais']); ?> / <?php echo $jeu['essais_max']; ?></p>

    <h2 class="hint"><?php echo $message; ?></h2>

    <?php if (!$jeu['fini']): ?>
        <form method="post" action="">
            <input type="number" name="nb" placeholder="Votre nombre" min="1" max="<?php echo $jeu['max']; ?>" required>
            <input type="submit" name="proposer" value="Proposer"></button>
        </form>
    <?php endif; ?>
        <form method="post" action="">
        <button type="submit" name="menu">
            <?php echo $jeu['fini'] ? 'Rejouer' : 'Changer de niveau'; ?>
        </button>
    </form>
    </div>

    <div class="at_container">
        <?php foreach ($jeu['essais'] as $essai): ?>
            <div class ="attempt"><?php echo $essai['nb'] . ' : ' . $essai['indice']; ?></div>
        <?php endforeach; ?>
        </div>
<?php endif; ?>
</body>
</html>
