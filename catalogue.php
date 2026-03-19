<?php
// catalogue.php — Affiche tous les films disponibles
require_once "auth.php";
require_once "db.php";

// Recherche simple par titre
$recherche = trim($_GET['q'] ?? "");

if ($recherche != "") {
    // Si l'utilisateur a tapé quelque chose dans la recherche
    $stmt = $pdo->prepare("
        SELECT f.*, ROUND(AVG(c.note), 1) AS note_moyenne, COUNT(c.id) AS nb_critiques
        FROM films f
        LEFT JOIN critiques c ON c.film_id = f.id
        WHERE f.titre LIKE :q
        GROUP BY f.id
        ORDER BY f.titre ASC
    ");
    $stmt->execute([':q' => '%' . $recherche . '%']);
} else {
    // Sinon on affiche tous les films
    $stmt = $pdo->query("
        SELECT f.*, ROUND(AVG(c.note), 1) AS note_moyenne, COUNT(c.id) AS nb_critiques
        FROM films f
        LEFT JOIN critiques c ON c.film_id = f.id
        GROUP BY f.id
        ORDER BY f.titre ASC
    ");
}

$films = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieScale - Catalogue</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav.php"; ?>

<div class="page-header">
    <h1>Catalogue de caca films</h1>
    <p><?= count($films) ?> film(s) disponible(s)</p>
</div>

<main>

    <!-- Barre de recherche -->
    <div class="carte">
        <form method="GET" style="display: flex; gap: 10px; align-items: flex-end;">
            <div class="form-group" style="flex: 1; margin: 0;">
                <label>Rechercher un film</label>
                <input type="text" name="q"
                       value="<?= htmlspecialchars($recherche) ?>"
                       placeholder="Tapez un titre...">
            </div>
            <button type="submit" class="btn btn-rouge">Rechercher</button>
            <?php if ($recherche): ?>
                <a href="catalogue.php" class="btn btn-gris">Effacer</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Liste des films -->
    <div class="carte">
        <h2>Films</h2>

        <?php if (empty($films)): ?>
            <p style="color: #aaaaaa;">Aucun film trouvé.</p>
        <?php else: ?>
            <div class="grille-films">
                <?php foreach ($films as $film): ?>
                    <a href="film.php?id=<?= $film['id'] ?>" class="film-card">

                        <!-- Affiche du film -->
                        <?php if ($film['affiche']): ?>
                            <img src="<?= htmlspecialchars($film['affiche']) ?>"
                                 alt="<?= htmlspecialchars($film['titre']) ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="pas-affiche">🎬</div>
                        <?php endif; ?>

                        <div class="infos">
                            <h3><?= htmlspecialchars($film['titre']) ?></h3>
                            <span class="annee"><?= $film['annee'] ?></span>
                            <!-- Note moyenne si il y a des critiques -->
                            <?php if ($film['note_moyenne']): ?>
                                <span class="note"><?= $film['note_moyenne'] ?>/10</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include "footer.php"; ?>

</body>
</html>
