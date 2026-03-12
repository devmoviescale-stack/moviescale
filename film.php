<?php
// film.php — Page de détail d'un film + formulaire de critique
require_once "auth.php";
require_once "db.php";

// Récupération de l'identifiant du film depuis l'URL
$id = (int)($_GET['id'] ?? 0);

// Si l'id est invalide, on retourne au catalogue
if ($id == 0) {
    header("Location: catalogue.php");
    exit;
}

// Récupération des infos du film + sa note moyenne
$stmt = $pdo->prepare("
    SELECT f.*, ROUND(AVG(c.note), 1) AS note_moyenne, COUNT(c.id) AS nb_critiques
    FROM films f
    LEFT JOIN critiques c ON c.film_id = f.id
    WHERE f.id = :id
    GROUP BY f.id
");
$stmt->execute([':id' => $id]);
$film = $stmt->fetch();

// Si le film n'existe pas
if (!$film) {
    header("Location: catalogue.php");
    exit;
}

// On vérifie si l'utilisateur a déjà posté une critique pour ce film
$ma_critique = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM critiques WHERE film_id = :fid AND user_id = :uid");
    $stmt->execute([':fid' => $id, ':uid' => $_SESSION['user_id']]);
    $ma_critique = $stmt->fetch();
}

$message = "";
$type_msg = "";

// Traitement du formulaire de critique
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {

    $note  = (int)($_POST['note'] ?? 0);
    $texte = trim($_POST['texte'] ?? "");

    // Validation de la note
    if ($note < 1 || $note > 10) {
        $message  = "La note doit être entre 1 et 10.";
        $type_msg = "erreur";

    } else {
        if ($ma_critique) {
            // L'utilisateur modifie sa critique existante
            $stmt = $pdo->prepare("UPDATE critiques SET note = :n, texte = :t WHERE id = :id");
            $stmt->execute([':n' => $note, ':t' => $texte, ':id' => $ma_critique['id']]);
            $message = "Votre critique a été mise à jour.";
        } else {
            // Nouvelle critique
            $stmt = $pdo->prepare("INSERT INTO critiques (film_id, user_id, note, texte) VALUES (:fid, :uid, :n, :t)");
            $stmt->execute([':fid' => $id, ':uid' => $_SESSION['user_id'], ':n' => $note, ':t' => $texte]);
            $message = "Votre critique a été publiée !";
        }
        $type_msg = "succes";

        // Rechargement de la page pour afficher les changements
        header("Location: film.php?id=$id");
        exit;
    }
}

// Récupération de toutes les critiques du film
$stmt = $pdo->prepare("
    SELECT c.*, u.pseudo
    FROM critiques c
    JOIN utilisateurs u ON u.id = c.user_id
    WHERE c.film_id = :id
    ORDER BY c.date_creation DESC
");
$stmt->execute([':id' => $id]);
$critiques = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieScale - <?= htmlspecialchars($film['titre']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav.php"; ?>

<main>

    <!-- Informations du film -->
    <div class="carte">
        <div class="film-detail">

            <!-- Affiche -->
            <?php if ($film['affiche']): ?>
                <img src="<?= htmlspecialchars($film['affiche']) ?>"
                     alt="<?= htmlspecialchars($film['titre']) ?>">
            <?php endif; ?>

            <!-- Infos texte -->
            <div class="infos-film">
                <p><a href="catalogue.php">← Retour au catalogue</a></p>
                <br>
                <h1><?= htmlspecialchars($film['titre']) ?></h1>

                <?php if ($film['realisateur']): ?>
                    <p><strong>Réalisateur :</strong> <?= htmlspecialchars($film['realisateur']) ?></p>
                <?php endif; ?>

                <?php if ($film['annee']): ?>
                    <p><strong>Année :</strong> <?= $film['annee'] ?></p>
                <?php endif; ?>

                <?php if ($film['genre']): ?>
                    <p><strong>Genre :</strong> <?= htmlspecialchars($film['genre']) ?></p>
                <?php endif; ?>

                <?php if ($film['synopsis']): ?>
                    <p style="margin-top: 10px; color: #cccccc; line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($film['synopsis'])) ?>
                    </p>
                <?php endif; ?>

                <!-- Note globale -->
                <?php if ($film['note_moyenne']): ?>
                    <div class="gros-score"><?= $film['note_moyenne'] ?>/10</div>
                    <p style="font-size: 13px; color: #aaaaaa;"><?= $film['nb_critiques'] ?> critique(s)</p>
                <?php else: ?>
                    <p style="margin-top: 15px; color: #aaaaaa; font-style: italic;">Pas encore de note</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Formulaire pour écrire une critique -->
    <div class="carte">
        <h2><?= $ma_critique ? 'Modifier ma critique' : 'Écrire une critique' ?></h2>

        <?php if ($message): ?>
            <div class="msg-<?= $type_msg ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
            <!-- Formulaire visible seulement si connecté -->
            <form method="POST">
                <div class="form-group">
                    <label>Ma note (de 1 à 10)</label>
                    <input type="number" name="note" min="1" max="10"
                           value="<?= $ma_critique['note'] ?? '' ?>"
                           placeholder="Ex: 8" required style="max-width: 100px;">
                </div>

                <div class="form-group">
                    <label>Mon avis (facultatif)</label>
                    <textarea name="texte" placeholder="Donnez votre avis sur ce film..."><?= htmlspecialchars($ma_critique['texte'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-rouge">
                    <?= $ma_critique ? 'Mettre à jour' : 'Publier' ?>
                </button>
            </form>

        <?php else: ?>
            <!-- Message si pas connecté -->
            <p style="color: #aaaaaa;">
                Vous devez être <a href="index.php">connecté</a> pour écrire une critique.
            </p>
        <?php endif; ?>
    </div>

    <!-- Liste des critiques -->
    <div class="carte">
        <h2>Critiques (<?= count($critiques) ?>)</h2>

        <?php if (empty($critiques)): ?>
            <p style="color: #aaaaaa; font-style: italic;">Aucune critique pour ce film.</p>

        <?php else: ?>
            <div class="liste-critiques">
                <?php foreach ($critiques as $critique): ?>
                    <div class="critique-item">
                        <div class="critique-header">
                            <span class="auteur">@<?= htmlspecialchars($critique['pseudo']) ?></span>
                            <span class="note-badge"><?= $critique['note'] ?>/10</span>
                        </div>
                        <?php if ($critique['texte']): ?>
                            <p class="texte">"<?= nl2br(htmlspecialchars($critique['texte'])) ?>"</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include "footer.php"; ?>

</body>
</html>
