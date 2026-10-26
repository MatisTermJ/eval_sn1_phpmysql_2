<?php
if (!isset($_SESSION['niveau'])) {
    $_SESSION['niveau'] = rand(1, 10);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $niveau = isset($_POST['niveau']) ? $_POST['niveau'] : null;
    $niveaux_valides = ['facile', 'normale', 'difficile', 'expert'];
    if (!in_array($niveau, $niveaux_valides, true)) {
        echo "<p style='color:red;'>Niveau invalide. Veuillez choisir un niveau correct.</p>";
    } else {
        // Choix du niveau avec switch
        switch ($niveau) {
            case 'facile':
                echo "<p>Vous avez choisi le niveau <strong>Facile</strong>. En avant pour un jeu tranquille !</p>";
                // Ici, on peut définir des paramètres du jeu
                $nombre_essaie = 10;
                break;

            case 'normale':
                echo "<p>Vous avez choisi le niveau <strong>Normale</strong>. Préparez-vous à un défi équilibré.</p>";
                $nombre_essaie = 8;
                break;

            case 'difficile':
                echo "<p>Vous avez choisi le niveau <strong>Difficile</strong>. Bonne chance, vous en aurez besoin !</p>";
                $nombre_essaie = 10;
                break;
            
            case 'expert':
                echo "<p>Vous avez choisi le niveau <strong>Expert</strong>. Bonne chance, vous en aurez besoin !</p>";
                $nombre_essaie = 10;
                break;
        }

        // Exemple d'utilisation des paramètres
        echo "<p>Nombre d'essaies : {$nombre_essaie}</p>";}}
?>

<!-- Formulaire HTML -->
<form method="post">
    <label for="niveau">Choisissez votre niveau :</label>
    <select name="niveau" id="niveau" required>
        <option value="">-- Sélectionnez --</option>
        <option value="facile">Facile</option>
        <option value="normale">Normale</option>
        <option value="difficile">Difficile</option>
        <option value="expert">Expert</option>
    </select>
    <button type="submit">Valider</button>
</form>