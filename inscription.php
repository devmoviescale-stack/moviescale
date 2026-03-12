<?php
// inscription.php — Page d'inscription d'un nouvel utilisateur
session_start();
require_once "db.php";

$erreur  = "";
$succes  = "";

// Traitement du formulaire quand il est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Récupération des données du formulaire
    $pseudo   = trim($_POST['pseudo'] ?? "");
    $email    = trim($_POST['email'] ?? "");
    $pass     = $_POST['password'] ?? "";
    $confirm  = $_POST['confirm'] ?? "";

    // --- Vérifications ---

    if ($pseudo == "" || $email == "" || $pass == "") {
        $erreur = "Tous les champs sont obligatoires.";

    } elseif (strlen($pseudo) < 3) {
        $erreur = "Le pseudo doit faire au moins 3 caractères.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse e-mail n'est pas valide.";

    } elseif (strlen($pass) < 4) {
        $erreur = "Le mot de passe doit faire au moins 4 caractères.";

    } elseif ($pass != $confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";

    } else {
        // Vérification que le pseudo ou l'email n'est pas déjà pris
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE pseudo = :p OR email = :e LIMIT 1");
        $stmt->execute([':p' => $pseudo, ':e' => $email]);

        if ($stmt->fetch()) {
            $erreur = "Ce pseudo ou cet e-mail est déjà utilisé.";
        } else {
            // Hashage du mot de passe avant de l'enregistrer
            $hash = password_hash($pass, PASSWORD_BCRYPT);

            // Insertion en base de données
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (:p, :e, :h)");
            $stmt->execute([':p' => $pseudo, ':e' => $email, ':h' => $hash]);

            // Connexion automatique après inscription
            $_SESSION['user']    = $pseudo;
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['role']    = 'user';

            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MovieScale - Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "nav.php"; ?>

<div class="page-header">
    <h1>Créer un compte</h1>
    <p>Rejoignez la communauté MovieScale</p>
</div>

<main>
    <div class="carte" style="max-width: 450px; margin: 0 auto;">
        <h2>Inscription</h2>

        <!-- Affichage de l'erreur si il y en a une -->
        <?php if ($erreur): ?>
            <div class="msg-erreur"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Pseudo</label>
                <input type="text" name="pseudo"
                       value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>"
                       placeholder="Votre pseudo" required>
            </div>

            <div class="form-group">
                <label>Adresse e-mail</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="exemple@mail.com" required>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password"
                       placeholder="Minimum 4 caractères" required>
            </div>

            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="confirm"
                       placeholder="Retapez votre mot de passe" required>
            </div>

            <button type="submit" class="btn btn-rouge">Créer mon compte</button>
        </form>

        <p style="margin-top: 12px; font-size: 13px; color: #aaaaaa;">
            Déjà un compte ? <a href="index.php">Se connecter</a>
        </p>
    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>
