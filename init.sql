CREATE DATABASE IF NOT EXISTS ecoride;
USE ecoride;

-- 1. Création des tables

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(100),
    email VARCHAR(100),
    mot_de_passe VARCHAR(255),
    credit INT DEFAULT 20
);

ALTER TABLE utilisateurs
ADD COLUMN role ENUM('chauffeur', 'passager', 'les deux') NOT NULL DEFAULT 'passager';

CREATE TABLE IF NOT EXISTS covoiturages   
 id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL
    lieu_arrivee VARCHAR(100),
    lieu_depart VARCHAR(100),
    date_depart DATETIME,
    date_arrive DATETIME,
    prix DECIMAL(6,2),
    places_disponibles INT,
    type_vehicule VARCHAR(100),
    est_ecologique BOOLEAN,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    marque VARCHAR(100),
    modele VARCHAR(100),
    couleur VARCHAR(50),
    immatriculation VARCHAR(50),
    energie VARCHAR(50),
    date_premiere_immat DATE NOT NULL,
    places_disponibles INT NOT NULL DEFAULT 0,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    animaux_acceptes BOOLEAN,
    fumeur BOOLEAN,
    musique BOOLEAN,
    discussion BOOLEAN,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    note INT,
    commentaire TEXT,
    date_avis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conducteur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    covoiturage_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturages(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS participations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    covoiturage_id INT NOT NULL,
    date_participation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (utilisateur_id, covoiturage_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (covoiturage_id) REFERENCES covoiturages(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    depart VARCHAR(255) NOT NULL,
    arrivee VARCHAR(255) NOT NULL,
    prix DECIMAL(8,2) NOT NULL,
    prix_net DECIMAL(8,2) NOT NULL,
    commission DECIMAL(8,2) NOT NULL DEFAULT 2.00,
    vehicule_id INT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    places_disponibles INT NOT NULL DEFAULT 1,
    date_trajet DATETIME NOT NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
);

-- 2. Insertion des données

INSERT INTO utilisateurs (id, pseudo, email, mot_de_passe)
VALUES (1, 'JeanDupont', 'jean.dupont@example.com', 'motdepasse123');

INSERT INTO vehicules (utilisateur_id, marque, modele, couleur, immatriculation, energie, date_premiere_immat, places_disponibles)
VALUES (1, 'Peugeot', '208', 'Bleu', 'AB-123-CD', 'Essence', '2020-01-01', 5);

INSERT INTO preferences (utilisateur_id, animaux_acceptes, fumeur, musique, discussion)
VALUES (1, TRUE, FALSE, TRUE, TRUE);

INSERT INTO covoiturages (utilisateur_id, lieu_depart, lieu_arrivee, date_depart, date_arrivee, prix, places_disponibles, type_vehicule, est_ecologique)
VALUES (1, 'Paris', 'Lyon', '2025-06-01 08:00:00', '2025-06-01 12:00:00', 25.00, 3, 'Berline', TRUE);

INSERT INTO avis (conducteur_id, note, commentaire)
VALUES (1, 5, 'Très bon conducteur, ponctuel et sympathique !');

INSERT INTO reservations (covoiturage_id, utilisateur_id)
VALUES (1, 1);
