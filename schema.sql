-- ================================================
-- MovieScale — Base de données
-- À importer dans phpMyAdmin
-- ================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS movie_scale
    CHARACTER SET utf8
    COLLATE utf8_general_ci;

USE movie_scale;

-- ------------------------------------------------
-- Table des utilisateurs
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateurs (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    pseudo       VARCHAR(50)  NOT NULL UNIQUE,
    email        VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Compte admin par défaut
-- Login : admin | Mot de passe : admin123
INSERT INTO utilisateurs (pseudo, email, mot_de_passe, role) VALUES (
    'admin',
    'admin@moviescale.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);

-- ------------------------------------------------
-- Table des films
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS films (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    titre        VARCHAR(200) NOT NULL,
    realisateur  VARCHAR(150) DEFAULT NULL,
    annee        YEAR         DEFAULT NULL,
    genre        VARCHAR(100) DEFAULT NULL,
    synopsis     TEXT         DEFAULT NULL,
    affiche      VARCHAR(500) DEFAULT NULL,
    date_ajout   DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Quelques films d'exemple
INSERT INTO films (titre, realisateur, annee, genre, synopsis, affiche) VALUES
('Le Parrain', 'Francis Ford Coppola', 1972, 'Drame / Crime',
 'Vito Corleone dirige une famille mafieuse de New York. Son fils Michael va peu à peu prendre sa place.',
 'https://image.tmdb.org/t/p/w500/3bhkrj58Vtu7enYsLeMOfNvAH.jpg'),

('Inception', 'Christopher Nolan', 2010, 'Science-Fiction',
 'Dom Cobb est capable de s\'infiltrer dans les rêves pour voler des secrets. On lui propose une mission impossible : implanter une idée.',
 'https://image.tmdb.org/t/p/w500/edv5CZvWj09upOsy2Y6IwDhK8bt.jpg'),

('Parasite', 'Bong Joon-ho', 2019, 'Thriller',
 'La famille Ki-taek, sans emploi, infiltre peu à peu la vie d\'une famille riche de Séoul.',
 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg'),

('Interstellar', 'Christopher Nolan', 2014, 'Science-Fiction',
 'Des astronautes voyagent à travers un trou de ver pour sauver l\'humanité d\'une Terre mourante.',
 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg'),

('Pulp Fiction', 'Quentin Tarantino', 1994, 'Crime',
 'Plusieurs histoires de criminels à Los Angeles se croisent et s\'entremêlent.',
 'https://image.tmdb.org/t/p/w500/fIE3lAGcZDV1G6XM5KmuWnNsPp1.jpg');

-- ------------------------------------------------
-- Table des critiques
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS critiques (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    film_id       INT NOT NULL,
    user_id       INT NOT NULL,
    note          TINYINT NOT NULL,   -- note entre 1 et 10
    texte         TEXT DEFAULT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Un utilisateur ne peut mettre qu'une seule critique par film
    UNIQUE KEY une_critique_par_film (film_id, user_id),

    -- Clés étrangères
    FOREIGN KEY (film_id)  REFERENCES films(id)        ON DELETE CASCADE,
    FOREIGN KEY (user_id)  REFERENCES utilisateurs(id)  ON DELETE CASCADE
);
