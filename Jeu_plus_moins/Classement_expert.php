<?php
require 'Config.php';

try {
    $query = $pdo->prepare(
        'SELECT players.name AS joueur,
                games.tries AS essais,
                TIMESTAMPDIFF(SECOND, games.`begin`, games.`end`) AS duree,
                games.`end` AS date_partie
         FROM games
         INNER JOIN players ON games.player_id = players.id
         WHERE games.level_id = ?
         ORDER BY duree ASC, games.tries ASC'
    );
    $query->execute([4]);
    $parties = $query->fetchAll();
} catch (PDOException $exception) {
    error_log('Lecture du classement expert impossible : ' . $exception->getMessage());
    $parties = [];
    $erreur = 'Impossible de charger le classement pour le moment.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement expert</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require 'header.php'; ?>
    <main>
        <a href="Classement_bouton.php">Retour</a>
        <h1>Classement expert</h1>
        <table>
            <thead>
                <tr>
                    <th>Joueur</th>
                    <th>Essais</th>
                    <th>Durée</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($erreur)): ?>
                    <tr><td colspan="4"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></td></tr>
                <?php elseif ($parties === []): ?>
                    <tr><td colspan="4">Aucune partie enregistrée pour ce niveau.</td></tr>
                <?php else: ?>
                    <?php foreach ($parties as $partie): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($partie['joueur'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo (int) $partie['essais']; ?></td>
                            <td><?php echo (int) $partie['duree']; ?> s</td>
                            <td><?php echo htmlspecialchars($partie['date_partie'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
