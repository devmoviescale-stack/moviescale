<?php
// login.php — Traitement du formulaire de connexion
session_start();
require_once "db.php";

// On vérifie que la requête vient bien d'un formulaire POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Récupération des champs du formulaire
$login = trim($_POST['login'] ?? "");
$pass  = $_POST['password'] ?? "";

// Vérification que les champs ne sont pas vides
if ($login == "" || $pass == "") {
    $_SESSION['erreur'] = "Veuillez remplir tous les champs.";
    header("Location: index.php");
    exit;
}

// Recherche de l'utilisateur dans la base de données
// On accepte la connexion par pseudo OU par email
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE pseudo = :l OR email = :l2 LIMIT 1");
$stmt->execute([':l' => $login, ':l2' => $login]);
$utilisateur = $stmt->fetch();

// Vérification du mot de passe avec password_verify (mot de passe haché)
if ($utilisateur && password_verify($pass, $utilisateur['mot_de_passe'])) {
    // Connexion réussie : on enregistre les infos en session
    $_SESSION['user']    = $utilisateur['pseudo'];
    $_SESSION['user_id'] = $utilisateur['id'];
    $_SESSION['role']    = $utilisateur['role'];
    header("Location: index.php");
    exit;
}

// Identifiants incorrects
$_SESSION['erreur'] = "Identifiant ou mot de passe incorrect.";
header("Location: index.php");
exit;
