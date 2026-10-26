<?php
    require 'Config.php';
    try{
        $query = $dbh->query("SELECT id, Joueur, Essaie, Durée, Date FROM classement_difficile ORDER BY durée ASC");
        $parties = $query->fetchAll(PDO::FETCH_ASSOC);
    }catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}



?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <main>
             <button>
                <a href="Classement_bouton.php">Retour</a>
            </button>
            <table>
                <thead>
                    <th>Joueur</th>
                    <th>Essaie</th>
                    <th>Durée</th>
                    <th>Date</th>
                </thead>
            </table>
             <tbody>
            <?php if (!empty($parties)): ?>
                <?php foreach ($parties as $partie): ?>
                    <table>
                    <tr >
                        <td class="Tableau"><?php echo ($partie['Joueur']); ?></td>
                        <td class="Tableau"><?php echo ($partie['Essaie']); ?></td>
                        <td class="Tableau"><?php echo ($partie['Durée']); ?></td>
                        <td class="Tableau"><?php echo ($partie['Date']); ?></td><br>
                    </tr>
                    </table>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Aucune donnée enregistrée pour le moment.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
        </main>
    </body>
    </html>