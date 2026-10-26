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
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plus ou moins</title>
</head>
<body>

<?php if (!isset($_SESSION['jeu'])): ?>

    <!-- Choix du niveau de difficulté -->
    <h1>Choisissez votre niveau</h1>

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
        <button type="submit">Jouer</button>
    </form>

<?php else: ?>

    <!-- Jeu du plus ou moins -->
    <?php $jeu = $_SESSION['jeu']; ?>

    <h1>Niveau <?php echo $jeu['nom']; ?> - entre 1 et <?php echo $jeu['max']; ?></h1>
    <p>Essais restants : <?php echo $jeu['essais_max'] - count($jeu['essais']); ?> / <?php echo $jeu['essais_max']; ?></p>

    <h2><?php echo $message; ?></h2>

    <?php if (!$jeu['fini']): ?>
        <form method="post" action="">
            <input type="number" name="nb" placeholder="Votre nombre" min="1" max="<?php echo $jeu['max']; ?>" required>
            <button type="submit" name="proposer">Proposer</button>
        </form>
    <?php endif; ?>

    <h3>Vos essais</h3>
    <ul>
        <?php foreach ($jeu['essais'] as $essai): ?>
            <li><?php echo $essai['nb'] . ' : ' . $essai['indice']; ?></li>
        <?php endforeach; ?>
    </ul>

    <form method="post" action="">
        <button type="submit" name="menu">
            <?php echo $jeu['fini'] ? 'Rejouer' : 'Changer de niveau'; ?>
        </button>
    </form>

<?php endif; ?>

</body>
</html>

<!-- enregistrement de la partie -->
<?php
// id de la persone
// temps debut de la pertie
// temps fin de la partie
// niveau de la partie
// nombre d'essais
// resultat de la partie (gagné ou perdu)
// enregistrement dans une base de données