<?php
// register.php

// 1. Configuration de la base de données
$host     = "localhost";
$dbname   = "moviescale";
$user     = "root";
$password = ""; // à adapter

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données.");
}

// 2. Récupération et nettoyage des données POST
$prenom          = trim($_POST['prenom'] ?? '');
$nom             = trim($_POST['nom'] ?? '');
$username        = trim($_POST['username'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password_plain  = $_POST['password'] ?? '';
$confirm         = $_POST['confirm_password'] ?? '';
$date_naissance  = $_POST['date_naissance'] ?? '';
$terms           = isset($_POST['terms']);

// Fonction utilitaire pour rediriger avec message
function redirect_with_message(string $msg): void {
    $msg = urlencode($msg);
    header("Location: movie_scale.html?msg=$msg#auth-modal");
    exit;
}

// 3. Validations serveur
if (!$terms) {
    redirect_with_message("Vous devez accepter les conditions d'utilisation.");
}

if (empty($prenom) || empty($nom) || empty($username) || empty($email) || empty($password_plain)) {
    redirect_with_message("Veuillez remplir tous les champs obligatoires.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirect_with_message("Format d'email invalide.");
}

if ($password_plain !== $confirm) {
    redirect_with_message("Les mots de passe ne correspondent pas.");
}

if (strlen($password_plain) < 8) {
    redirect_with_message("Le mot de passe doit contenir au moins 8 caractères.");
}

// Vérification de l'âge
if (!empty($date_naissance)) {
    $birth = DateTime::createFromFormat('Y-m-d', $date_naissance);
    if ($birth) {
        $today = new DateTime();
        $age = $today->diff($birth)->y;
        if ($age < 13) {
            redirect_with_message("Vous devez avoir au moins 13 ans.");
        }
    }
}

// 4. Vérifier si l'email ou le username existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
$stmt->execute([
    ':email'    => $email,
    ':username' => $username,
]);
if ($stmt->fetch()) {
    redirect_with_message("Cet email ou ce nom d'utilisateur est déjà utilisé.");
}

// 5. Hasher le mot de passe
$password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

// 6. Insérer l'utilisateur
$stmt = $pdo->prepare("
    INSERT INTO users (prenom, nom, username, email, password, date_naissance, created_at)
    VALUES (:prenom, :nom, :username, :email, :password, :date_naissance, NOW())
");
$stmt->execute([
    ':prenom'         => $prenom,
    ':nom'            => $nom,
    ':username'       => $username,
    ':email'          => $email,
    ':password'       => $password_hash,
    ':date_naissance' => $date_naissance ?: null,
]);

// 7. (Optionnel) démarrer une session et connecter automatiquement
session_start();
$_SESSION['user_id']       = $pdo->lastInsertId();
$_SESSION['username']      = $username;
$_SESSION['prenom']        = $prenom;
$_SESSION['moviescale_ok'] = true;

// 8. Redirection vers la page d'accueil avec message de succès
redirect_with_message("Compte créé avec succès, bienvenue $prenom !");