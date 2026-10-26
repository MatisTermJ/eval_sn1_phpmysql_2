<?php
require 'Config.php';

if (!isset($_SESSION['id'])) {
    header('Location: Connexion.php');
    exit;
}

$niveaux = [
    'facile'    => ['id' => 1, 'nom' => 'Facile',    'max' => 50,   'essais' => 10],
    'normale'   => ['id' => 2, 'nom' => 'Normale',   'max' => 100,  'essais' => 8],
    'difficile' => ['id' => 3, 'nom' => 'Difficile', 'max' => 500,  'essais' => 10],
    'expert'    => ['id' => 4, 'nom' => 'Expert',    'max' => 1000, 'essais' => 10],
];

$message = '';
$erreurEnregistrement = '';

if (isset($_POST['menu'])) {
    unset($_SESSION['jeu']);
}

if (isset($_POST['niveau']) && is_string($_POST['niveau']) && isset($niveaux[$_POST['niveau']])) {
    $niveau = $niveaux[$_POST['niveau']];

    $_SESSION['jeu'] = [
        'level_id'   => $niveau['id'],
        'nom'        => $niveau['nom'],
        'max'        => $niveau['max'],
        'essais_max' => $niveau['essais'],
        'secret'     => random_int(1, $niveau['max']),
        'essais'     => [],
        'fini'       => false,
        'gagne'      => false,
        'debut'      => date('Y-m-d H:i:s'),
        'enregistre' => false,
    ];
}

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

if (isset($_SESSION['jeu']) && $_SESSION['jeu']['fini'] && empty($_SESSION['jeu']['enregistre'])) {
    $jeu = $_SESSION['jeu'];
    $jeu['fin'] = isset($jeu['fin']) ? $jeu['fin'] : date('Y-m-d H:i:s');

    try {
        $requete = $pdo->prepare(
            'INSERT INTO games (player_id, level_id, tries, result, `begin`, `end`)
             VALUES (:player_id, :level_id, :tries, :result, :begin, :end)'
        );
        $requete->execute([
            ':player_id' => (int) $_SESSION['id'],
            ':level_id'  => $jeu['level_id'],
            ':tries'     => count($jeu['essais']),
            ':result'    => $jeu['gagne'] ? 'Victoire' : 'Défaite',
            ':begin'     => $jeu['debut'],
            ':end'       => $jeu['fin'],
        ]);

        $_SESSION['jeu']['enregistre'] = true;
        $_SESSION['jeu']['fin'] = $jeu['fin'];
    } catch (PDOException $exception) {
        error_log('Enregistrement de partie impossible : ' . $exception->getMessage());
        $erreurEnregistrement = 'La partie est terminée, mais son enregistrement a échoué. Actualisez la page pour réessayer.';
    }
}
?>
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
    <?php require 'header.php'; ?>

<?php if (!isset($_SESSION['jeu'])): ?>

    <h1>Choisissez votre niveau</h1>

    <div class="menu">
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
            <input type="submit" value="Jouer">
        </form>
    </div>

<?php else: ?>

    <?php $jeu = $_SESSION['jeu']; ?>

    <h1>Niveau <?php echo $jeu['nom']; ?> - entre 1 et <?php echo $jeu['max']; ?></h1>
    <div class="menu">
        <p>Essais restants : <?php echo $jeu['essais_max'] - count($jeu['essais']); ?> / <?php echo $jeu['essais_max']; ?></p>

        <h2 class="hint"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></h2>

        <?php if ($erreurEnregistrement !== ''): ?>
            <p role="alert"><?php echo htmlspecialchars($erreurEnregistrement, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif ($jeu['fini']): ?>
            <p>Partie enregistrée.</p>
        <?php endif; ?>

        <?php if (!$jeu['fini']): ?>
            <form method="post" action="">
                <input type="number" name="nb" placeholder="Votre nombre" min="1" max="<?php echo $jeu['max']; ?>" required>
                <input type="submit" name="proposer" value="Proposer">
            </form>
        <?php endif; ?>

        <form method="post" action="">
            <button type="submit" name="menu">
                <?php echo $jeu['fini'] ? 'Rejouer' : 'Changer de niveau'; ?>
            </button>
        </form>
    </div>

    <h3>Vos essais</h3>
    <div class="at_container">
        <?php foreach ($jeu['essais'] as $essai): ?>
            <div class="attempt"><?php echo $essai['nb'] . ' : ' . htmlspecialchars($essai['indice'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</body>
</html>
