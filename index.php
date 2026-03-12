<?php
// index.php — Page d'accueil avec formulaire de connexion
require_once "auth.php";
require_once "db.php";

// Récupération du message d'erreur de connexion si il y en a un
$erreur = $_SESSION['erreur'] ?? "";
unset($_SESSION['erreur']);

// On récupère les 4 derniers films ajoutés pour les afficher sur l'accueil
$stmt = $pdo->query("SELECT * FROM films ORDER BY id DESC LIMIT 4");
$derniers_films = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieScale - Accueil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav.php"; ?>

<div class="page-header">
    <h1>🎬 Bienvenue sur MovieScale</h1>
    <p>Notez et critiquez vos films préférés</p>
</div>

<main>

    <!-- Section connexion ou message de bienvenue -->
    <?php if (isset($_SESSION['user'])): ?>
        <!-- Si connecté : message de bienvenue -->
        <div class="carte">
            <h2>Bonjour, <?= htmlspecialchars($_SESSION['user']) ?> 👋</h2>
            <p>Vous êtes connecté. Explorez le catalogue et donnez votre avis !</p>
            <br>
            <a href="catalogue.php" class="btn btn-rouge">Voir le catalogue</a>
        </div>

    <?php else: ?>
        <!-- Si pas connecté : formulaire de connexion -->
        <div class="carte" style="max-width: 420px; margin: 0 auto 20px;">
            <h2>Connexion</h2>

            <?php if ($erreur): ?>
                <div class="msg-erreur"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Identifiant ou e-mail</label>
                    <input type="text" name="login" placeholder="Votre identifiant" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Votre mot de passe" required>
                </div>
                <button type="submit" class="btn btn-rouge">Se connecter</button>
            </form>

            <p style="margin-top: 12px; font-size: 13px; color: #aaaaaa;">
                Pas encore de compte ? <a href="inscription.php">S'inscrire</a>
            </p>
        </div>
    <?php endif; ?>

    <!-- Derniers films ajoutés -->
    <div class="carte">
        <h2>Derniers films ajoutés</h2>
        <div class="grille-films">
            <?php foreach ($derniers_films as $film): ?>
                <a href="film.php?id=<?= $film['id'] ?>" class="film-card">
                    <!-- Affichage de l'affiche ou d'un emoji si pas d'image -->
                    <?php if ($film['affiche']): ?>
                        <img src="<?= htmlspecialchars($film['affiche']) ?>" alt="<?= htmlspecialchars($film['titre']) ?>">
                    <?php else: ?>
                        <div class="pas-affiche">🎬</div>
                    <?php endif; ?>

                    <div class="infos">
                        <h3><?= htmlspecialchars($film['titre']) ?></h3>
                        <span class="annee"><?= $film['annee'] ?></span>
                    </div>
                </a>
            <?php endforeach; ?>

            <?php if (empty($derniers_films)): ?>
                <p style="color: #aaaaaa; font-size: 14px;">Aucun film pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php include "footer.php"; ?>

</body>
</html>
