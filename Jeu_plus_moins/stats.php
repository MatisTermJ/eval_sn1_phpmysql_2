<?php
require 'Config.php';

if (!isset($_SESSION['id'])) {
    header('Location: Connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_matis.css">
    <title>Mes statistiques</title>
</head>
<body>
    <?php require "header.php"?>
    <div class="stat-container">
        <?php
        // Parties totales
        $dbh = $pdo->prepare('SELECT count(*) FROM games
                              JOIN players ON games.player_id = players.id
                              WHERE games.player_id = ?');
        $dbh->execute([$_SESSION['id']]);
        $nb_totale = $dbh->fetchColumn();
        // Parties gagnées
        $dbh = $pdo->prepare('SELECT count(*) FROM games
                              JOIN players ON games.player_id = players.id
                              WHERE games.player_id = ? AND games.result = "Victoire"');
        $dbh->execute([$_SESSION['id']]);
        $nb_victoire = $dbh->fetchColumn();
        // Moyenne
        $moyenne = $nb_totale > 0 ? ($nb_victoire / $nb_totale) * 100 : 0;
        ?>
        <div class="stats-cards">
            <div class="stat-card">
                <strong><?= (int) $nb_totale ?></strong>
                <span>parties jouées</span>
            </div>

            <div class="stat-card">
                <strong><?= (int) $nb_victoire ?></strong>
                <span>victoires</span>
            </div>

            <div class="stat-card">
                <strong><?= (int) $moyenne ?> %</strong>
                <span>de réussite</span>
            </div>
        </div>
    <table>
        <thead>
            <tr>
                <th>Difficulté</th>
                <th>Parties</th>
                <th>Victoires</th>
                <th>Moy. essais</th>
                <th>Meilleur</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $dbh = $pdo->prepare('SELECT levels.name FROM levels');
            $dbh->execute();
            $levels = $dbh->fetchAll();
            foreach($levels as $level){ ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($level['name'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td>
                        <?php
                            $dbh = $pdo->prepare('SELECT count(*) FROM games
                                    JOIN players ON games.player_id = players.id
                                    JOIN levels ON games.level_id = levels.id
                                    WHERE games.player_id = ? AND levels.name = ?');
                            $dbh->execute([$_SESSION['id'], $level['name']]);
                            $partie = $dbh->fetchColumn();
                            echo (int) $partie;
                            ?>
                    </td>
                    <td>
                        <?php
                            $dbh = $pdo->prepare('SELECT count(*) FROM games
                                    JOIN players ON games.player_id = players.id
                                    JOIN levels ON games.level_id = levels.id
                                    WHERE games.player_id = ? AND levels.name = ? AND games.result = "Victoire"');
                            $dbh->execute([$_SESSION['id'], $level['name']]);
                            $victoire = $dbh->fetchColumn();
                            echo (int) $victoire;
                            ?>
                    </td>
                    <td>
                        <?php
                            $dbh = $pdo->prepare('SELECT AVG(tries) FROM games
                                    JOIN players ON games.player_id = players.id
                                    JOIN levels ON games.level_id = levels.id
                                    WHERE games.player_id = ? AND levels.name = ? AND games.result = "Victoire"');
                            $dbh->execute([$_SESSION['id'], $level['name']]);
                            $moy = $dbh->fetchColumn();
                            if ($moy === null){
                                echo "-";
                            }else{
                                echo (float) $moy;
                            }
                            ?>
                    </td>
                    <td>
                    <?php
                    $dbh = $pdo->prepare('SELECT MIN(games.tries) FROM games
                            JOIN players ON games.player_id = players.id
                            JOIN levels ON games.level_id = levels.id
                            WHERE games.player_id = ?
                            AND levels.name = ?
                            AND games.result = "Victoire"');
                    $dbh->execute([$_SESSION['id'], $level['name']]);
                    $meilleur = $dbh->fetchColumn();

                    echo $meilleur === null ? "-" : (int) $meilleur;
                    ?>
                    </td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
    <h2>Dernières parties</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Niveau</th>
                <th>Essais</th>
                <th>Résultat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare('
                SELECT games.end, levels.name AS level_name, games.tries, games.result
                FROM games
                JOIN players ON games.player_id = players.id
                JOIN levels ON games.level_id = levels.id
                WHERE games.player_id = ?
                ORDER BY games.end DESC
                LIMIT 3
            ');
            $stmt->execute([$_SESSION['id']]);
            $dernieresParties = $stmt->fetchAll();
            if ($dernieresParties): ?>
                <?php foreach ($dernieresParties as $partie): ?>
                    <tr>
                        <td><?= htmlspecialchars($partie['end'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($partie['level_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $partie['tries'] ?></td>
                        <td><?= htmlspecialchars($partie['result'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">Aucune partie jouée</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    </div>
    
</body>
</html>