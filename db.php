<?php
// db.php — Connexion à la base de données MySQL
// À modifier selon ta configuration WAMP/XAMPP

$hote    = "localhost";
$base    = "movie_scale";
$user    = "root";
$pass    = ""; // Laisser vide sur WAMP par défaut

try {
    // Connexion avec PDO
    $pdo = new PDO(
        "mysql:host=$hote;dbname=$base;charset=utf8",
        $user,
        $pass
    );
    // Afficher les erreurs SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Récupérer les résultats en tableau associatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si la connexion échoue, on arrête tout
    die("Erreur de connexion : " . $e->getMessage());
}
