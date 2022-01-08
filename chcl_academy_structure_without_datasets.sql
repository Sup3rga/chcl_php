-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 19 nov. 2021 à 20:56
-- Version du serveur :  10.4.14-MariaDB
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chcl_academy`
--

-- --------------------------------------------------------

--
-- Structure de la table `annee_academique`
--

CREATE TABLE `annee_academique` (
  `id` int(11) NOT NULL,
  `academie` varchar(9) NOT NULL,
  `debut` date NOT NULL,
  `fin` date DEFAULT NULL,
  `annee_debut` int(11) NOT NULL,
  `annee_fin` int(11) NOT NULL,
  `etat` enum('O','F') NOT NULL DEFAULT 'F'
) ;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `code` varchar(9) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `niveau` int(11) NOT NULL,
  `session` int(11) NOT NULL,
  `coefficient` int(11) NOT NULL,
  `titulaire` int(11) NOT NULL,
  `suppleant` int(11) DEFAULT NULL,
  `annee_academique` int(11) NOT NULL,
  `etat` enum('E','D','S','N') NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table `dispensation`
--

CREATE TABLE `dispensation` (
  `id` int(11) NOT NULL,
  `jour` int(11) NOT NULL,
  `heure_debut` varchar(8) NOT NULL,
  `heure_fin` varchar(8) NOT NULL,
  `cours` int(11) NOT NULL,
  `tp` tinyint(1) NOT NULL,
  `annee_academique` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `etudiants`
--

CREATE TABLE `etudiants` (
  `id` int(11) NOT NULL,
  `identite` int(11) NOT NULL,
  `niveau` int(11) NOT NULL,
  `personne_reference` varchar(255) NOT NULL,
  `telephone_reference` varchar(15) NOT NULL,
  `annee_academique` int(11) NOT NULL,
  `etat` enum('A','E','T','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

CREATE TABLE `filieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `annee_academique` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `hierarchie`
--

CREATE TABLE `hierarchie` (
  `id` int(11) NOT NULL,
  `notation` varchar(255) NOT NULL,
  `effectif` int(11) NOT NULL,
  `affectation` int(11) DEFAULT NULL,
  `valeur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `date_upload` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `individu`
--

CREATE TABLE `individu` (
  `id` int(11) NOT NULL,
  `code` varchar(9) DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `sexe` enum('F','M') DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `lieu_naissance` varchar(255) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `nif` varchar(15) DEFAULT NULL,
  `ninu` varchar(20) DEFAULT NULL,
  `poste` int(100) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `photo` int(11) DEFAULT NULL,
  `memo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `individu`
--

INSERT INTO `individu` (`id`, `code`, `nom`, `prenom`, `sexe`, `adresse`, `email`, `lieu_naissance`, `date_naissance`, `nif`, `ninu`, `poste`, `telephone`, `photo`, `memo`) VALUES
(41, NULL, 'Core', 'Infinite', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `niveau`
--

CREATE TABLE `niveau` (
  `id` int(11) NOT NULL,
  `filiere` int(11) NOT NULL,
  `notation` varchar(50) NOT NULL,
  `annee` int(11) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `session` int(11) NOT NULL,
  `id_etu` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `note` double NOT NULL,
  `annee_academique` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `professeurs`
--

CREATE TABLE `professeurs` (
  `id` int(11) NOT NULL,
  `identite` int(11) NOT NULL,
  `niveau_etude` enum('Licence','Master','Doctorat') NOT NULL,
  `status_matrimoniale` varchar(50) NOT NULL,
  `salaire` double NOT NULL,
  `etat` enum('A','E','C','M') NOT NULL DEFAULT 'M',
  `annee_academique` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `identite` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `passcode` text NOT NULL,
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `access` varchar(255) NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `identite`, `pseudo`, `passcode`, `etat`, `access`, `date_creation`) VALUES
(9, 41, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'actif', '0,1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,40,41,42,43,44', '2021-11-19 14:38:22');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annee_academique`
--
ALTER TABLE `annee_academique`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `academie` (`academie`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `uk_nom_niveau` (`nom`,`niveau`),
  ADD KEY `fk_titulaire` (`titulaire`),
  ADD KEY `fk_suppleant` (`suppleant`),
  ADD KEY `fk_niveau` (`niveau`),
  ADD KEY `fk_annee` (`annee_academique`);

--
-- Index pour la table `dispensation`
--
ALTER TABLE `dispensation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cours` (`cours`),
  ADD KEY `fk_cours_aca` (`annee_academique`);

--
-- Index pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identite` (`identite`),
  ADD KEY `niveau` (`niveau`),
  ADD KEY `annee_academique` (`annee_academique`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `fk_fac_year` (`annee_academique`);

--
-- Index pour la table `hierarchie`
--
ALTER TABLE `hierarchie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_duo` (`notation`,`affectation`),
  ADD KEY `affectation` (`affectation`);

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `individu`
--
ALTER TABLE `individu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nif` (`nif`),
  ADD UNIQUE KEY `ninu` (`ninu`),
  ADD UNIQUE KEY `telephone` (`telephone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `photo` (`photo`),
  ADD KEY `fk_hierarchie` (`poste`);

--
-- Index pour la table `niveau`
--
ALTER TABLE `niveau`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_duo_annee` (`filiere`,`annee`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_etu_cours_annee` (`id_etu`,`id_cours`,`annee_academique`),
  ADD KEY `id_cours` (`id_cours`),
  ADD KEY `annee_academique` (`annee_academique`);

--
-- Index pour la table `professeurs`
--
ALTER TABLE `professeurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identite` (`identite`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_pseudo` (`pseudo`),
  ADD UNIQUE KEY `identite` (`identite`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annee_academique`
--
ALTER TABLE `annee_academique`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dispensation`
--
ALTER TABLE `dispensation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT pour la table `etudiants`
--
ALTER TABLE `etudiants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `hierarchie`
--
ALTER TABLE `hierarchie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `individu`
--
ALTER TABLE `individu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `niveau`
--
ALTER TABLE `niveau`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT pour la table `professeurs`
--
ALTER TABLE `professeurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `fk_annee` FOREIGN KEY (`annee_academique`) REFERENCES `annee_academique` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_niveau` FOREIGN KEY (`niveau`) REFERENCES `niveau` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_suppleant` FOREIGN KEY (`suppleant`) REFERENCES `professeurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_titulaire` FOREIGN KEY (`titulaire`) REFERENCES `professeurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dispensation`
--
ALTER TABLE `dispensation`
  ADD CONSTRAINT `dispensation_ibfk_1` FOREIGN KEY (`cours`) REFERENCES `cours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cours_aca` FOREIGN KEY (`annee_academique`) REFERENCES `annee_academique` (`id`);

--
-- Contraintes pour la table `etudiants`
--
ALTER TABLE `etudiants`
  ADD CONSTRAINT `etudiants_ibfk_1` FOREIGN KEY (`identite`) REFERENCES `individu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etudiants_ibfk_2` FOREIGN KEY (`niveau`) REFERENCES `niveau` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `etudiants_ibfk_3` FOREIGN KEY (`annee_academique`) REFERENCES `annee_academique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD CONSTRAINT `fk_fac_year` FOREIGN KEY (`annee_academique`) REFERENCES `annee_academique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `hierarchie`
--
ALTER TABLE `hierarchie`
  ADD CONSTRAINT `hierarchie_ibfk_1` FOREIGN KEY (`affectation`) REFERENCES `filieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `individu`
--
ALTER TABLE `individu`
  ADD CONSTRAINT `fk_hierarchie` FOREIGN KEY (`poste`) REFERENCES `hierarchie` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `individu_ibfk_1` FOREIGN KEY (`photo`) REFERENCES `images` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `niveau`
--
ALTER TABLE `niveau`
  ADD CONSTRAINT `niveau_ibfk_1` FOREIGN KEY (`filiere`) REFERENCES `filieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`id_etu`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`id_cours`) REFERENCES `cours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_3` FOREIGN KEY (`annee_academique`) REFERENCES `annee_academique` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `professeurs`
--
ALTER TABLE `professeurs`
  ADD CONSTRAINT `fk_identite_prof` FOREIGN KEY (`identite`) REFERENCES `individu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `professeurs_ibfk_1` FOREIGN KEY (`identite`) REFERENCES `individu` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`identite`) REFERENCES `individu` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
