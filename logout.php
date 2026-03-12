<?php
// logout.php — Déconnexion de l'utilisateur
session_start();

// On détruit toute la session
session_destroy();

// Redirection vers l'accueil
header("Location: index.php");
exit;
