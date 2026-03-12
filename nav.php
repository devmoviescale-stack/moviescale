<?php
// nav.php — Barre de navigation commune à toutes les pages
// On vérifie si l'utilisateur est connecté pour afficher les bons liens
?>
<nav class="navbar">
    <div class="logo">🎬 MovieScale</div>
    <ul>
        <li><a href="index.php" <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : '' ?>>Accueil</a></li>
        <li><a href="catalogue.php" <?= basename($_SERVER['PHP_SELF']) == 'catalogue.php' ? 'class="active"' : '' ?>>Catalogue</a></li>
        <li><a href="top.php" <?= basename($_SERVER['PHP_SELF']) == 'top.php' ? 'class="active"' : '' ?>>Top Films</a></li>

        <?php if (isset($_SESSION['user'])): ?>
            <!-- Liens visibles seulement si connecté -->
            <li><a href="compte.php" <?= basename($_SERVER['PHP_SELF']) == 'compte.php' ? 'class="active"' : '' ?>>Mon Compte</a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li><a href="admin.php" <?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'class="active"' : '' ?>>Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Déconnexion</a></li>
        <?php else: ?>
            <!-- Liens visibles si pas connecté -->
            <li><a href="inscription.php">S'inscrire</a></li>
        <?php endif; ?>
    </ul>
</nav>
