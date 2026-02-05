// Base de données des films
const films = [
    {id: 1, titre: "Inception", genre: "sf", annee: 2010, note: 4.8, critiques: 42},
    {id: 2, titre: "The Dark Knight", genre: "action", annee: 2008, note: 4.7, critiques: 58},
    {id: 3, titre: "Interstellar", genre: "sf", annee: 2014, note: 4.6, critiques: 35},
    {id: 4, titre: "Pulp Fiction", genre: "action", annee: 1994, note: 4.5, critiques: 31},
    {id: 5, titre: "The Shining", genre: "horreur", annee: 1980, note: 4.3, critiques: 28},
    {id: 6, titre: "Shaun of the Dead", genre: "comedie", annee: 2004, note: 4.2, critiques: 22},
    {id: 7, titre: "Pan's Labyrinth", genre: "fantastique", annee: 2006, note: 4.4, critiques: 19},
    {id: 8, titre: "The Matrix", genre: "sf", annee: 1999, note: 4.7, critiques: 45},
    {id: 9, titre: "Get Out", genre: "horreur", annee: 2017, note: 4.5, critiques: 26},
    {id: 10, titre: "The Grand Budapest Hotel", genre: "comedie", annee: 2014, note: 4.3, critiques: 18}
];

let utilisateurConnecte = false;
let nomUtilisateur = '';
let filmSelectionne = null;
let genreActuel = 'all';
let anneeActuelle = 'all';
let noteMinimale = 0;
let ordreActuel = 'note';
let filmsFiltres = [...films];

// Afficher les films au chargement
window.onload = function() {
    afficherFilms();
    configurerRecherche();
    verifierSession();
};

// Vérifier si un utilisateur est déjà connecté (session sauvegardée)
function verifierSession() {
    const savedUser = localStorage.getItem('moviescale_user');
    if (savedUser) {
        utilisateurConnecte = true;
        nomUtilisateur = savedUser;
        
        // Mettre à jour l'interface
        document.getElementById('user-name').textContent = nomUtilisateur;
        document.getElementById('user-icon').textContent = '👤';
        
        // Afficher/masquer les options du menu
        document.getElementById('login-option').style.display = 'none';
        document.getElementById('register-option').style.display = 'none';
        document.getElementById('profile-option').style.display = 'block';
        document.getElementById('favorites-option').style.display = 'block';
        document.getElementById('reviews-option').style.display = 'block';
        document.getElementById('settings-option').style.display = 'block';
        document.getElementById('logout-option').style.display = 'block';
    }
}

// ========== GESTION DU MENU UTILISATEUR ==========
function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.classList.toggle('active');
    
    // Fermer le menu si on clique ailleurs
    document.addEventListener('click', function(e) {
        const userDropdown = document.querySelector('.user-dropdown');
        if (!userDropdown.contains(e.target)) {
            menu.classList.remove('active');
        }
    });
}

// ========== MENU MOBILE ==========
function toggleMobileMenu() {
    const menu = document.querySelector('.navbar-menu');
    menu.classList.toggle('mobile-active');
}

// ========== AUTHENTIFICATION ==========
function afficherConnexion() {
    document.getElementById('auth-modal').classList.add('active');
    basculerVersConnexion();
}

function afficherInscription() {
    document.getElementById('auth-modal').classList.add('active');
    basculerVersInscription();
}

function fermerAuthModal() {
    document.getElementById('auth-modal').classList.remove('active');
    document.getElementById('login-form').reset();
    document.getElementById('register-form').reset();
    document.getElementById('forgot-form').reset();
}

function basculerVersConnexion() {
    document.getElementById('login-box').style.display = 'block';
    document.getElementById('register-box').style.display = 'none';
    document.getElementById('forgot-box').style.display = 'none';
}

function basculerVersInscription() {
    document.getElementById('login-box').style.display = 'none';
    document.getElementById('register-box').style.display = 'block';
    document.getElementById('forgot-box').style.display = 'none';
}

function afficherMotDePasseOublie() {
    document.getElementById('login-box').style.display = 'none';
    document.getElementById('register-box').style.display = 'none';
    document.getElementById('forgot-box').style.display = 'block';
    return false;
}

// Connexion via réseaux sociaux
function connexionGoogle() {
    afficherNotification('🔍 Connexion avec Google en cours...', 'info');
    // Ici, vous intégreriez l'API Google OAuth
}

function connexionFacebook() {
    afficherNotification('📘 Connexion avec Facebook en cours...', 'info');
    // Ici, vous intégreriez l'API Facebook Login
}

// Afficher CGU et politique de confidentialité
function afficherCGU() {
    afficherNotification('📄 Conditions Générales d\'Utilisation', 'info');
    return false;
}

function afficherConfidentialite() {
    afficherNotification('🔒 Politique de Confidentialité', 'info');
    return false;
}

// Validation du mot de passe en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('register-password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.querySelector('.strength-bar');
            
            let strength = 0;
            
            // Critères de force
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            // Mettre à jour la barre
            strengthBar.className = 'strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('medium');
            } else {
                strengthBar.classList.add('strong');
            }
        });
    }
});

// Connexion
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const rememberMe = document.getElementById('remember-me').checked;
    
    if (email && password) {
        // Simulation de connexion
        utilisateurConnecte = true;
        nomUtilisateur = email.split('@')[0]; // Utiliser la partie avant @ comme nom
        
        // Sauvegarder dans localStorage si "se souvenir de moi"
        if (rememberMe) {
            localStorage.setItem('moviescale_user', nomUtilisateur);
        }
        
        // Mettre à jour l'interface utilisateur
        document.getElementById('user-name').textContent = nomUtilisateur;
        document.getElementById('user-icon').textContent = '👤';
        
        // Afficher/masquer les options du menu
        document.getElementById('login-option').style.display = 'none';
        document.getElementById('register-option').style.display = 'none';
        document.getElementById('profile-option').style.display = 'block';
        document.getElementById('favorites-option').style.display = 'block';
        document.getElementById('reviews-option').style.display = 'block';
        document.getElementById('settings-option').style.display = 'block';
        document.getElementById('logout-option').style.display = 'block';
        
        fermerAuthModal();
        
        // Animation de bienvenue
        afficherNotification('✨ Bienvenue ' + nomUtilisateur + ' !', 'success');
    }
});

// Inscription
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const prenom = document.getElementById('register-prenom').value;
    const nom = document.getElementById('register-nom').value;
    const username = document.getElementById('register-username').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm').value;
    const dateNaissance = document.getElementById('register-date').value;
    const accepteTermes = document.getElementById('register-terms').checked;
    
    // Validations
    if (!accepteTermes) {
        afficherNotification('⚠️ Vous devez accepter les conditions d\'utilisation', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        afficherNotification('⚠️ Les mots de passe ne correspondent pas', 'error');
        return;
    }
    
    if (password.length < 8) {
        afficherNotification('⚠️ Le mot de passe doit contenir au moins 8 caractères', 'error');
        return;
    }
    
    // Validation de l'âge
    if (dateNaissance) {
        const birthDate = new Date(dateNaissance);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age < 13) {
            afficherNotification('⚠️ Vous devez avoir au moins 13 ans', 'error');
            return;
        }
    }
    
    // Validation du format email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        afficherNotification('⚠️ Format d\'email invalide', 'error');
        return;
    }
    
    // Simulation de création de compte
    utilisateurConnecte = true;
    nomUtilisateur = username;
    
    // Mettre à jour l'interface
    document.getElementById('user-name').textContent = username;
    document.getElementById('user-icon').textContent = '👤';
    
    // Afficher/masquer les options du menu
    document.getElementById('login-option').style.display = 'none';
    document.getElementById('register-option').style.display = 'none';
    document.getElementById('profile-option').style.display = 'block';
    document.getElementById('favorites-option').style.display = 'block';
    document.getElementById('reviews-option').style.display = 'block';
    document.getElementById('settings-option').style.display = 'block';
    document.getElementById('logout-option').style.display = 'block';
    
    fermerAuthModal();
    
    // Message de bienvenue personnalisé
    afficherNotification('🎉 Compte créé avec succès ! Bienvenue ' + prenom + ' !', 'success');
});

// Mot de passe oublié
document.getElementById('forgot-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('forgot-email').value;
    
    // Validation email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        afficherNotification('⚠️ Format d\'email invalide', 'error');
        return;
    }
    
    // Simulation d'envoi d'email
    afficherNotification('📧 Un email de réinitialisation a été envoyé à ' + email, 'success');
    
    setTimeout(() => {
        basculerVersConnexion();
    }, 2000);
});

// Déconnexion
function deconnexion() {
    utilisateurConnecte = false;
    nomUtilisateur = '';
    
    // Supprimer la session sauvegardée
    localStorage.removeItem('moviescale_user');
    
    // Réinitialiser l'interface
    document.getElementById('user-name').textContent = 'Connexion';
    document.getElementById('user-icon').textContent = '👤';
    
    // Afficher/masquer les options du menu
    document.getElementById('login-option').style.display = 'block';
    document.getElementById('register-option').style.display = 'block';
    document.getElementById('profile-option').style.display = 'none';
    document.getElementById('favorites-option').style.display = 'none';
    document.getElementById('reviews-option').style.display = 'none';
    document.getElementById('settings-option').style.display = 'none';
    document.getElementById('logout-option').style.display = 'none';
    
    document.getElementById('login-form').reset();
    afficherNotification('👋 À bientôt !', 'info');
}

// ========== NOTIFICATIONS ==========
function afficherNotification(message, type = 'info') {
    // Créer l'élément de notification
    const notif = document.createElement('div');
    notif.className = 'notification ' + type;
    notif.textContent = message;
    notif.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: linear-gradient(135deg, rgb(130,0,0), rgb(180,0,0));
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.5);
        z-index: 3000;
        animation: slideInRight 0.4s ease;
        font-size: 1.1em;
    `;
    
    if (type === 'success') {
        notif.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
    } else if (type === 'error') {
        notif.style.background = 'linear-gradient(135deg, #dc3545, #c82333)';
    }
    
    document.body.appendChild(notif);
    
    // Ajouter l'animation CSS si elle n'existe pas
    if (!document.querySelector('#notification-style')) {
        const style = document.createElement('style');
        style.id = 'notification-style';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notif.style.animation = 'slideOutRight 0.4s ease';
        setTimeout(() => notif.remove(), 400);
    }, 3000);
}

// ========== FONCTIONS DU MENU UTILISATEUR ==========
function afficherProfil() {
    afficherNotification('📊 Profil de ' + nomUtilisateur, 'info');
}

function afficherFavoris() {
    afficherNotification('❤️ Vos films favoris', 'info');
}

function afficherMesCritiques() {
    afficherNotification('✍️ Vos critiques', 'info');
}

function afficherParametres() {
    afficherNotification('⚙️ Paramètres', 'info');
}

function afficherMesFilms() {
    afficherNotification('🎬 Votre collection', 'info');
}

function afficherAPropos() {
    afficherNotification('ℹ️ À propos de Movie Scale', 'info');
}

// ========== FILTRAGE ET TRI ==========
function appliquerFiltres() {
    filmsFiltres = films.filter(film => {
        // Filtre par genre
        const genreOk = genreActuel === 'all' || film.genre === genreActuel;
        
        // Filtre par année
        let anneeOk = true;
        if (anneeActuelle !== 'all') {
            const annee = film.annee;
            switch(anneeActuelle) {
                case '2020s': anneeOk = annee >= 2020; break;
                case '2010s': anneeOk = annee >= 2010 && annee < 2020; break;
                case '2000s': anneeOk = annee >= 2000 && annee < 2010; break;
                case '1990s': anneeOk = annee >= 1990 && annee < 2000; break;
                case '1980s': anneeOk = annee >= 1980 && annee < 1990; break;
            }
        }
        
        // Filtre par note
        const noteOk = film.note >= noteMinimale;
        
        return genreOk && anneeOk && noteOk;
    });
    
    trierFilms(ordreActuel);
}

function filtrerGenre(genre) {
    genreActuel = genre;
    document.getElementById('genre-select').value = genre;
    appliquerFiltres();
}

function filtrerGenreSelect(genre) {
    genreActuel = genre;
    appliquerFiltres();
}

function filtrerAnnee(periode) {
    anneeActuelle = periode;
    appliquerFiltres();
}

function filtrerNote(note) {
    noteMinimale = parseFloat(note);
    appliquerFiltres();
}

function trierFilms(ordre) {
    ordreActuel = ordre;
    
    switch(ordre) {
        case 'note':
            filmsFiltres.sort((a, b) => b.note - a.note);
            break;
        case 'note-asc':
            filmsFiltres.sort((a, b) => a.note - b.note);
            break;
        case 'annee':
            filmsFiltres.sort((a, b) => b.annee - a.annee);
            break;
        case 'annee-asc':
            filmsFiltres.sort((a, b) => a.annee - b.annee);
            break;
        case 'titre':
            filmsFiltres.sort((a, b) => a.titre.localeCompare(b.titre));
            break;
        case 'titre-desc':
            filmsFiltres.sort((a, b) => b.titre.localeCompare(a.titre));
            break;
        case 'critiques':
            filmsFiltres.sort((a, b) => b.critiques - a.critiques);
            break;
    }
    
    afficherFilms();
}

function trierFilmsSelect(ordre) {
    trierFilms(ordre);
}

// ========== AFFICHAGE DES FILMS ==========
function afficherFilms() {
    const container = document.getElementById('film-liste');
    container.innerHTML = '';
    
    if (filmsFiltres.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #888;">
                <h3 style="font-size: 2em; margin-bottom: 15px;">🎬 Aucun film trouvé</h3>
                <p style="font-size: 1.2em;">Essayez de modifier vos filtres</p>
            </div>
        `;
        return;
    }

    filmsFiltres.forEach(film => {
        const etoiles = '⭐'.repeat(Math.round(film.note));
        
        const filmCard = `
            <div class="film-card">
                <h4>${film.titre}</h4>
                <p class="genre">${film.genre.toUpperCase()} • ${film.annee}</p>
                <p class="note">${etoiles} ${film.note}/5</p>
                <p class="critique-count">${film.critiques} critiques</p>
                <button onclick="voirCritiques('${film.titre}')">📖 Critiques</button>
                <button onclick="ouvrirModalNotation('${film.titre}')">⭐ Noter</button>
            </div>
        `;
        container.innerHTML += filmCard;
    });
}

// ========== RECHERCHE ==========
function configurerRecherche() {
    const searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("input", function () {
        const searchValue = searchInput.value.toLowerCase();
        
        if (searchValue === '') {
            appliquerFiltres();
            return;
        }
        
        filmsFiltres = films.filter(film => 
            film.titre.toLowerCase().includes(searchValue)
        );
        
        afficherFilms();
    });
}

// ========== CHANGEMENT DE VUE ==========
function changerVue(vue) {
    const container = document.getElementById('film-liste');
    const gridBtn = document.getElementById('grid-btn');
    const listBtn = document.getElementById('list-btn');
    
    if (vue === 'grid') {
        container.classList.remove('list-view');
        container.classList.add('grid-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        container.classList.remove('grid-view');
        container.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    }
}

// ========== DÉTAILS DES FILMS ==========
function voirDetails(id) {
    const film = films.find(f => f.id === id);
    if (film) {
        afficherNotification('🎬 Détails de : ' + film.titre, 'info');
        // Ici vous pouvez ouvrir une modal avec plus de détails
    }
}

function voirCritiques(titre) {
    afficherNotification('📖 Critiques de : ' + titre, 'info');
}

// ========== NOTATION DES FILMS ==========
function ouvrirModalNotation(titre) {
    if (!utilisateurConnecte) {
        afficherNotification('⚠️ Connectez-vous pour noter un film !', 'error');
        setTimeout(() => afficherConnexion(), 1000);
        return;
    }
    
    filmSelectionne = titre;
    document.getElementById('modal-titre').textContent = 'Noter : ' + titre;
    document.getElementById('modal-notation').classList.add('active');
    document.getElementById('note-value').textContent = '0';
}

function fermerModal() {
    document.getElementById('modal-notation').classList.remove('active');
    
    // Réinitialiser les étoiles
    const etoiles = document.querySelectorAll('.etoile');
    etoiles.forEach(e => e.classList.remove('active'));
    document.getElementById('critique').value = '';
    document.getElementById('note-value').textContent = '0';
}

function noterFilm(note) {
    const etoiles = document.querySelectorAll('.etoile');
    etoiles.forEach((e, index) => {
        if (index < note) {
            e.classList.add('active');
        } else {
            e.classList.remove('active');
        }
    });
    document.getElementById('note-value').textContent = note;
}

function publierCritique() {
    const critique = document.getElementById('critique').value;
    const noteDonnee = document.querySelectorAll('.etoile.active').length;
    
    if (noteDonnee === 0) {
        afficherNotification('⚠️ Veuillez donner une note !', 'error');
        return;
    }
    
    // La critique est optionnelle maintenant
    let message = '✨ Critique publiée avec succès !\n\n';
    message += '🎬 Film : ' + filmSelectionne + '\n';
    message += '⭐ Note : ' + noteDonnee + '/5';
    
    if (critique) {
        message += '\n📝 Votre avis a été pris en compte';
    }
    
    afficherNotification('✅ Critique publiée !', 'success');
    fermerModal();
}

// Fermer les modals en cliquant en dehors
window.addEventListener('click', function(event) {
    const modalNotation = document.getElementById('modal-notation');
    const modalAuth = document.getElementById('auth-modal');
    
    if (event.target === modalNotation) {
        fermerModal();
    }
    if (event.target === modalAuth) {
        fermerAuthModal();
    }
});

// ========== NAVIGATION FLUIDE ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ========== INITIALISATION ==========
console.log('🎬 Movie Scale chargé avec succès !');
console.log('📊 ' + films.length + ' films disponibles');
