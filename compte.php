<?php
// compte.php — Page de profil de l'utilisateur connecté
require_once "auth.php";
require_once "db.php";

// On vérifie que l'utilisateur est connecté
exiger_connexion();

$uid = $_SESSION['user_id'];

// Récupération des infos de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$stmt->execute([':id' => $uid]);
$utilisateur = $stmt->fetch();

// Récupération de toutes ses critiques avec le titre du film
$stmt = $pdo->prepare("
    SELECT c.*, f.titre, f.annee, f.id AS film_id
    FROM critiques c
    JOIN films f ON f.id = c.film_id
    WHERE c.user_id = :uid
    ORDER BY c.date_creation DESC
");
$stmt->execute([':uid' => $uid]);
$mes_critiques = $stmt->fetchAll();

// Calcul de la note moyenne de l'utilisateur
$note_moy = 0;
if (count($mes_critiques) > 0) {
    $total = array_sum(array_column($mes_critiques, 'note'));
    $note_moy = round($total / count($mes_critiques), 1);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieScale - Mon Compte</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav.php"; ?>

<div class="page-header">
    <h1>Mon Compte</h1>
    <p>Bonjour <?= htmlspecialchars($_SESSION['user']) ?> !</p>
</div>

<main>

    <div class="grille-compte">

        <!-- Informations personnelles -->
        <div class="carte">
            <h2>Mes informations</h2>
            <p><strong>Pseudo :</strong> <?= htmlspecialchars($utilisateur['pseudo']) ?></p>
            <p style="margin-top: 6px;"><strong>E-mail :</strong> <?= htmlspecialchars($utilisateur['email']) ?></p>
            <p style="margin-top: 6px;"><strong>Rôle :</strong> <?= $utilisateur['role'] == 'admin' ? 'Administrateur' : 'Membre' ?></p>
        </div>

        <!-- Statistiques rapides -->
        <div class="carte">
            <h2>Mes statistiques</h2>
            <p><strong>Nombre de critiques :</strong> <?= count($mes_critiques) ?></p>
            <p style="margin-top: 6px;"><strong>Note moyenne donnée :</strong>
                <?= count($mes_critiques) > 0 ? $note_moy . '/10' : 'Pas encore de note' ?>
            </p>
        </div>

    </div>

    <!-- Liste de mes critiques -->
    <div class="carte">
        <h2>Mes critiques (<?= count($mes_critiques) ?>)</h2>

        <?php if (empty($mes_critiques)): ?>
            <p style="color: #aaaaaa; font-style: italic;">
                Vous n'avez pas encore écrit de critique.
            </p>
            <br>
            <a href="catalogue.php" class="btn btn-rouge">Voir le catalogue</a>

        <?php else: ?>
            <table class="tableau">
                <thead>
                    <tr>
                        <th>Film</th>
                        <th>Année</th>
                        <th>Ma note</th>
                        <th>Mon avis</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mes_critiques as $critique): ?>
                        <tr>
                            <td>
                                <a href="film.php?id=<?= $critique['film_id'] ?>">
                                    <?= htmlspecialchars($critique['titre']) ?>
                                </a>
                            </td>
                            <td><?= $critique['annee'] ?></td>
                            <td><strong style="color: #e50000;"><?= $critique['note'] ?>/10</strong></td>
                            <td style="color: #cccccc; font-style: italic; font-size: 13px;">
                                <?php if ($critique['texte']): ?>
                                    <?= htmlspecialchars(mb_substr($critique['texte'], 0, 60)) ?>
                                    <?= mb_strlen($critique['texte']) > 60 ? '...' : '' ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Bouton pour modifier la critique -->
                                <a href="film.php?id=<?= $critique['film_id'] ?>"
                                   class="btn btn-gris" style="font-size: 12px; padding: 5px 10px;">
                                    Modifier
                                </a>
                            </td>
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
