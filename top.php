<?php
// top.php — Classement des films par note moyenne
require_once "auth.php";
require_once "db.php";

// On récupère les films qui ont au moins une critique, triés par note
$stmt = $pdo->query("
    SELECT f.*, ROUND(AVG(c.note), 1) AS note_moyenne, COUNT(c.id) AS nb_critiques
    FROM films f
    JOIN critiques c ON c.film_id = f.id
    GROUP BY f.id
    ORDER BY note_moyenne DESC, nb_critiques DESC
    LIMIT 20
");
$classement = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieScale - Top Films</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav.php"; ?>

<div class="page-header">
    <h1>🏆 Top Films</h1>
    <p>Classement par note moyenne de la communauté</p>
</div>

<main>
    <div class="carte">
        <h2>Classement général</h2>

        <?php if (empty($classement)): ?>
            <p style="color: #aaaaaa; font-style: italic;">
                Aucun film n'a encore été noté. Soyez le premier !
            </p>
            <br>
            <a href="catalogue.php" class="btn btn-rouge">Voir le catalogue</a>

        <?php else: ?>
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>Affiche</th>
                        <th>Titre</th>
                        <th>Réalisateur</th>
                        <th>Année</th>
                        <th>Note</th>
                        <th>Critiques</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classement as $position => $film): ?>
                        <tr>
                            <td>
                                <!-- Médailles pour le podium -->
                                <?php
                                if ($position == 0) echo "🥇";
                                elseif ($position == 1) echo "🥈";
                                elseif ($position == 2) echo "🥉";
                                else echo "#" . ($position + 1);
                                ?>
                            </td>
                            <td>
                                <?php if ($film['affiche']): ?>
                                    <img src="<?= htmlspecialchars($film['affiche']) ?>"
                                         style="height: 50px; border-radius: 4px;" alt="">
                                <?php else: ?>
                                    🎬
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="film.php?id=<?= $film['id'] ?>">
                                    <?= htmlspecialchars($film['titre']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($film['realisateur'] ?? '—') ?></td>
                            <td><?= $film['annee'] ?? '—' ?></td>
                            <td><strong style="color: #e50000;"><?= $film['note_moyenne'] ?>/10</strong></td>
                            <td><?= $film['nb_critiques'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
