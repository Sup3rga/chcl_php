-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 31 déc. 2021 à 02:03
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

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

--
-- Déchargement des données de la table `annee_academique`
--

INSERT INTO `annee_academique` (`id`, `academie`, `debut`, `fin`, `annee_debut`, `annee_fin`, `etat`) VALUES
(1, '2019-2020', '2019-10-04', '2020-08-11', 2019, 2020, 'F'),
(5, '2020-2021', '2020-10-05', '2021-08-27', 2020, 2021, 'O'),
(6, '2021-2022', '2021-10-02', '2022-08-04', 2021, 2022, 'F'),
(7, '2022-2023', '2022-10-03', '2023-08-16', 2022, 2023, 'F'),
(8, '1865-2024', '1865-01-16', '2024-10-16', 1865, 2024, 'F'),
(9, '2023-2025', '2023-10-01', '2025-10-07', 2023, 2025, 'F');

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `code`, `nom`, `niveau`, `session`, `coefficient`, `titulaire`, `suppleant`, `annee_academique`, `etat`) VALUES
(7, 'MTH-111', 'Analyse', 1, 1, 3, 4, 3, 1, 'E'),
(8, 'MTH-112', 'Algèbre', 1, 1, 3, 3, NULL, 1, 'E'),
(11, 'ANA-211', 'Introduction à l\'anatomie humaine', 7, 1, 3, 5, NULL, 1, 'E'),
(12, 'INF-011', 'Fondement des ordinateurs', 1, 1, 4, 8, NULL, 1, 'E'),
(13, 'FR-012', 'Français', 1, 1, 2, 9, NULL, 1, 'E'),
(14, 'INF-012', 'Informatique Bureautique', 1, 1, 3, 10, NULL, 1, 'E'),
(15, 'INF-102', 'Informatique Bureautique', 7, 1, 2, 10, NULL, 1, 'E'),
(16, 'INF-013', 'Informatique Bureautique', 29, 1, 2, 10, NULL, 1, 'E'),
(17, 'INF-014', 'Informatique Bureautique', 17, 1, 3, 8, NULL, 1, 'E'),
(18, 'FRA-013', 'Français', 7, 1, 2, 9, NULL, 1, 'E'),
(19, 'FRA-023', 'Français', 17, 1, 2, 9, NULL, 1, 'E'),
(20, 'WEB-001', 'Web Client 1', 3, 1, 4, 8, 3, 1, 'E'),
(21, 'ALG-001', 'Algorithme', 1, 2, 4, 8, 8, 1, 'E'),
(22, 'LNG-002', 'Anglais', 1, 2, 2, 4, NULL, 1, 'E'),
(23, 'MTD-001', 'Méthodologie', 1, 2, 2, 5, NULL, 1, 'E'),
(24, 'BIO-021', 'Biologie Cellulaire', 7, 2, 3, 11, NULL, 1, 'E'),
(25, 'MTD-002', 'Méthodologie', 7, 2, 2, 4, NULL, 1, 'E'),
(26, 'BX-001', 'Initiation à la peinture contemporaine', 17, 2, 3, 9, NULL, 1, 'E'),
(27, 'WEB-002', 'Web Client 2', 3, 2, 4, 8, 3, 1, 'E'),
(28, 'ECO-111', 'Initiation à l\'économie', 29, 2, 3, 11, NULL, 1, 'E'),
(29, 'SOC-111', 'Initiation à la sociologie', 29, 2, 3, 10, NULL, 1, 'E'),
(31, 'MOB-001', 'Programmation Android & iOS avec Flutter', 4, 1, 4, 8, 10, 5, 'E'),
(32, 'NEU-001', 'Introduction à la neurologie', 8, 1, 3, 12, NULL, 5, 'E'),
(33, 'ILN-345', 'Introduction à la neurologie', 6, 1, 1, 5, 12, 5, 'E'),
(34, 'JS-34', 'JSP', 6, 1, 5, 9, 4, 5, 'E');

--
-- Déchargement des données de la table `dispensation`
--

INSERT INTO `dispensation` (`id`, `jour`, `heure_debut`, `heure_fin`, `cours`, `tp`, `annee_academique`) VALUES
(25, 3, '14:00', '16:00', 14, 0, 1),
(26, 4, '11:00', '13:00', 14, 1, 1),
(28, 4, '08:00', '11:00', 16, 0, 1),
(29, 2, '09:00', '11:00', 11, 1, 1),
(30, 3, '09:00', '11:00', 15, 0, 1),
(31, 0, '08:00', '11:00', 12, 0, 1),
(32, 1, '10:00', '12:00', 7, 0, 1),
(33, 3, '12:00', '14:00', 7, 1, 1),
(34, 2, '09:00', '12:00', 8, 0, 1),
(35, 3, '09:00', '11:00', 8, 1, 1),
(36, 1, '08:00', '10:00', 13, 0, 1),
(39, 3, '14:00', '16:00', 17, 0, 1),
(40, 4, '11:00', '13:00', 17, 1, 1),
(45, 1, '10:00', '12:00', 18, 0, 1),
(46, 2, '08:00', '10:00', 19, 0, 1),
(47, 0, '13:00', '16:00', 20, 0, 1),
(48, 1, '09:00', '12:00', 20, 1, 1),
(49, 1, '08:00', '12:00', 21, 0, 1),
(50, 4, '08:00', '11:00', 21, 1, 1),
(51, 0, '08:00', '10:00', 22, 0, 1),
(52, 0, '10:30', '12:30', 23, 0, 1),
(53, 0, '08:00', '11:00', 24, 0, 1),
(54, 0, '10:30', '12:30', 25, 0, 1),
(55, 1, '08:00', '10:00', 25, 0, 1),
(56, 0, '08:00', '11:00', 26, 0, 1),
(57, 0, '13:00', '16:00', 27, 0, 1),
(58, 2, '09:00', '12:00', 27, 1, 1),
(59, 1, '10:00', '12:00', 28, 0, 1),
(60, 0, '08:00', '10:00', 29, 0, 1),
(61, 0, '14:00', '16:00', 7, 0, 5),
(62, 1, '13:00', '15:00', 7, 1, 5),
(63, 0, '09:00', '12:00', 14, 0, 5),
(64, 3, '14:00', '16:00', 14, 0, 5),
(65, 0, '08:00', '11:00', 20, 0, 5),
(66, 1, '09:00', '12:00', 20, 0, 5),
(71, 0, '08:00', '10:00', 32, 0, 5),
(72, 3, '13:00', '16:00', 31, 1, 5),
(73, 0, '13:00', '16:00', 31, 0, 5),
(74, 0, '08:00', '10:00', 33, 0, 5),
(75, 1, '10:15', '11:15', 33, 1, 5),
(76, 1, '09:00', '16:00', 34, 1, 5),
(77, 1, '13:00', '14:00', 21, 0, 5),
(78, 2, '09:00', '11:00', 21, 1, 5);

--
-- Déchargement des données de la table `etudiants`
--

INSERT INTO `etudiants` (`id`, `identite`, `niveau`, `personne_reference`, `telephone_reference`, `annee_academique`, `etat`) VALUES
(3, 13, 3, 'Pierre Cardin', '4345-4534', 1, 'A'),
(4, 14, 30, 'Lincoln Haricot Compère', '3233-4853', 1, 'A'),
(5, 15, 3, 'Jean Héléna', '4343-4323', 1, 'A'),
(6, 17, 1, 'Charles Maxime', '4332-3432', 1, 'A'),
(7, 19, 8, 'Homilus Compère', '3453-4343', 1, 'A'),
(8, 20, 8, 'Pierre Ricard', '4323-2323', 1, 'A'),
(10, 22, 18, 'Kruf Steel', '3452-3232', 1, 'A'),
(11, 23, 4, 'Jean Parvilus', '509 4892-2321', 1, 'A'),
(12, 25, 18, 'Joseph Emmanuel', '509 4892-2321', 1, 'A'),
(13, 26, 8, 'Orguens Michel', '509 3423-2421', 1, 'A'),
(18, 34, 17, 'Bertrand Dorgeron', '509 3456-2452', 5, 'A'),
(19, 35, 21, 'Jean Maceline', '509 4753-3421', 5, 'A'),
(20, 42, 3, 'pierre Etienne', '4867-8888', 5, 'A'),
(21, 44, 1, 'Jean MannÃ©', '509 4523-2123', 5, 'A'),
(22, 45, 26, 'Pierre Colin', '509 3243-2421', 5, 'A'),
(23, 46, 27, 'Jean Antony', '509 4320-2341', 5, 'A');

--
-- Déchargement des données de la table `filieres`
--

INSERT INTO `filieres` (`id`, `nom`, `annee_academique`) VALUES
(5, 'FacultÃ© de Sciences Informatiques', 1),
(6, 'FacultÃ© de Science de la SantÃ©', 1),
(12, 'FacultÃ© des Beaux-Arts', 1),
(14, 'FacultÃ© des Sciences Agronomiques et d\'Halieutiques', 1),
(15, 'FacultÃ© de Psychologie', 1),
(16, 'FacultÃ© des Sciences economiques et Sociales', 1),
(17, 'Sciences de l\'Environnement', 1),
(18, 'Sages Femmes', 5);

--
-- Déchargement des données de la table `hierarchie`
--

INSERT INTO `hierarchie` (`id`, `notation`, `effectif`, `affectation`, `valeur`) VALUES
(4, 'Doyen', 1, 5, 3),
(6, 'Doyen', 1, 14, 3),
(9, 'Directeur aux Affaires Académiques', 1, NULL, 4),
(10, 'secretaire', 1, 5, 1);

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`id`, `fichier`, `date_upload`) VALUES
(30, '1640888352.jpeg', '2021-12-30 13:19:17'),
(31, '1640888843.jpeg', '2021-12-30 13:27:28'),
(32, '1640889885.jpeg', '2021-12-30 13:44:51'),
(33, '1640889969.jpeg', '2021-12-30 13:46:14'),
(34, '1640894791.jpeg', '2021-12-30 15:06:35'),
(35, '1640894983.jpeg', '2021-12-30 15:09:48'),
(36, '1640895438.jpeg', '2021-12-30 15:17:23'),
(37, '1640895676.jpeg', '2021-12-30 15:21:21'),
(38, '1640914898.jpeg', '2021-12-30 20:41:46');

--
-- Déchargement des données de la table `individu`
--

INSERT INTO `individu` (`id`, `code`, `nom`, `prenom`, `sexe`, `adresse`, `email`, `lieu_naissance`, `date_naissance`, `nif`, `ninu`, `poste`, `telephone`, `photo`, `memo`) VALUES
(1, NULL, 'Infinite', 'Core', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 38, NULL),
(4, 'MM-433-23', 'Michel', 'Marc', 'M', 'Limonade', 'msquare@ymail.com', 'Cap-Haitien', '1978-10-24', '121-132-243-3', '6352342123', 6, '4789-3343', NULL, ''),
(5, 'ME-431-53', 'Magny', 'Eva', 'F', 'Trou-du-Nord', 'maeva@gmail.com', 'Fonds des Nègres', '1992-10-12', '203-242-543-1', '1242532453', NULL, '4982-2342', NULL, 'Une belle personne...'),
(6, 'BA-061-21', 'BÃ©lizaire', 'Alexa', 'F', 'Cap-Haitien', 'lexaire@ymail.com', 'Cap-Haitien', '1989-06-22', '232-234-232-3', '1232112121', NULL, '3434-2342', 35, 'Une personne sÃ©rieuse !'),
(12, 'FM-063-21', 'FranÃ§ois', 'Mathieu', 'M', 'Village EKAM, Caracol', 'francmathieu@gmail.com', 'Cap-Haitien', '1991-05-12', '343-452-242-4', '1223435343', NULL, '3454-3434', 36, ''),
(13, 'PR-254-53', 'Pierre', 'Richardson', 'M', 'Rue 15 L, Cap-Haitien, Haiti', 'richariche@gmail.com', 'Cap-Haitien', '2001-07-13', '234-323-425-4', '1234323445', NULL, '3442-2323', NULL, ''),
(14, 'LJ-399-43', 'Lincoln', 'Jessly', 'M', 'Village EKAM, Caracol, Haiti', 'jess@gmail.com', 'Plaine du Nord', '1997-09-15', '230-330-239-9', '1232332234', NULL, '3252-4358', NULL, ''),
(15, 'SA-064-21', 'SÃ©mexant', 'Amandine', 'F', 'Madeline', 'amande@gmail.com', 'Cap-Haitien', '1999-04-11', '432-234-321-3', '1233423343', NULL, '4432-3232', NULL, ''),
(16, 'LD-432-44', 'Larose', 'Duckens', 'M', 'Port-au-Prince', 'dc.larose@gmail.com', 'Cap-Haitien', '1998-10-14', '343-232-343-2', '1323433334', NULL, '3453-2233', NULL, ''),
(17, 'CD-322-34', 'Charles', 'Didier', 'M', 'Babiole, Cap-Haitien', 'didi.carlitos@gmail.com', 'Plaine-du-Nord', '2002-03-10', '232-323-232-2', '1234343434', NULL, '3443-4232', 30, ''),
(18, 'JE-059-21', 'Jean-Louis', 'Elie', 'M', 'Trou-du-Nord', 'elie.jlouis@gmail.com', 'Port-Margot', '1985-05-10', '334-134-564-3', '1234567434', 4, '3553-4345', NULL, ''),
(19, 'HD-323-34', 'Homilus', 'Diane', 'F', 'Champin, Cap-Haitien', 'ho.diane@gmail.com', 'Pignon', '1999-06-29', '232-322-332-3', '1234323234', NULL, '4334-3432', NULL, ''),
(20, 'PL-232-32', 'Pierre', 'Lindor', 'M', 'Madeline, Cap-Haitien', 'lindor.pierre@gmail.com', 'Limbé', '2001-08-15', '322-122-323-2', '1232323232', NULL, '3432-2325', NULL, ''),
(22, 'SL-452-53', 'Steel', 'Lexa', 'F', 'Morne-Rouge, Plaine-du-Nord', 'steelexa.uv70@gmail.com', 'Fonds des Ngres', '2002-04-14', '232-443-245-2', '1343434345', NULL, '3453-2324', 32, ''),
(23, 'JP-050-20', 'Jean', 'Rodney Salnave', 'M', 'Madeline,Cap-Haitien', 'salrod.95@gmail.com', 'Grande-Rivière du Nord', '2001-10-16', '203-232-232-2', '1423242342', NULL, '509 4323-2422', NULL, NULL),
(25, 'JR-051-20', 'Joseph', 'Rose-Carline', 'F', 'Champin, Cap-Haitien', 'carlinarrose@gmail.com', 'Ranquitte, Haiti', '1999-03-23', '204-231-341-2', '1362423421', NULL, '509 4789-2342', NULL, 'Elle a précisé qu\'elle voulait faire partie des élites de la peinture'),
(26, 'OM-052-20', 'Orguens', 'Mathias', 'M', 'Rue Thozin, Limonade', 'mathaus117@gmail.com', 'Cap-Haitien, Haiti', '2000-07-13', '204-233-232-4', '1242334432', NULL, '509 3723-2322', NULL, NULL),
(27, 'MKS-053-0', 'Michael', 'Kate Sampers', 'F', 'Cap-Haitien', 'katie.mic@gmail.com', 'Louisiane', '1990-04-14', '221-321-231-3', '1224323123', NULL, '3423-3221', NULL, ''),
(28, NULL, 'Syndick', 'Wilky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'FD-056-21', 'Fernand', 'Delva', 'M', 'Cap-Haitien', 'delva89@gmail.com', 'Port-de-Paix', '2001-05-17', '234-345-531-3', '1342243423', NULL, '509 3543-3563', 33, NULL),
(35, 'DC-057-21', 'Duverneau', 'Cassandra', 'F', 'Limonade', 'cassandre.duvert@gmail.com', 'Cap-Haitien', '2001-07-22', '343-353-213-5', '1242354294', NULL, '509 3242-2532', 31, ''),
(38, 'BM-062-21', 'Blancourt', 'MÃ©lissa', 'F', '34, rue 21 R, Cap-Haitien', 'monalissa709@ymail.com', 'Kenscoff', '1992-11-13', '231-123-221-2', '1233323334', NULL, '3453-2433', 37, ''),
(39, NULL, 'ARISTILE', 'Guesly', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, NULL, 'Pierre', 'Augustin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL),
(41, NULL, 'ARISTILE', 'Guesly', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'JPF-060-1', 'Joseph', 'Petit-frere', 'M', '#3, cite du peuple, cap-haitien', 'joseph@gmail.com', 'cap-haitien', '2000-01-17', '123-456-789-0', '1234567890', NULL, '3444-7088', NULL, ''),
(43, NULL, 'Syndick', 'Wilky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'JA-003-22', 'Jean', 'Ali', 'M', 'Morne-Rouge', 'alija@gmail.com', 'LimbÃ©', '2000-04-12', '321-232-123-3', '1213421242', NULL, '509 4323-3211', NULL, NULL),
(45, 'PM-024-22', 'Pierre', 'Mackendy', 'M', 'Limonade', 'mackpi70@gmail.com', 'Milot', '1999-09-23', '321-323-232-4', '1232242123', NULL, '509 4734-2341', NULL, NULL),
(46, 'JA-037-22', 'Jean', 'Annie', 'F', 'Caracol', 'annie.jean@gmail.com', 'Cap-Haitien', '1999-07-07', '331-244-422-3', '1323342124', NULL, '509 4782-3221', NULL, NULL);

--
-- Déchargement des données de la table `niveau`
--

INSERT INTO `niveau` (`id`, `filiere`, `notation`, `annee`) VALUES
(1, 5, 'EUF', 1),
(3, 5, 'L1', 2),
(4, 5, 'L2', 3),
(6, 5, 'L3', 4),
(7, 6, 'DCEM1', 1),
(8, 6, 'DCEM2', 2),
(9, 6, 'DCEM3', 3),
(11, 6, 'Externat', 4),
(14, 6, 'Internat', 5),
(15, 6, 'Résident', 6),
(16, 6, 'Social', 7),
(17, 12, 'EUF', 1),
(18, 12, 'L1', 2),
(19, 12, 'L2', 3),
(20, 12, 'L3', 4),
(21, 14, 'EUF', 1),
(22, 14, 'L1', 2),
(23, 14, 'L2', 3),
(24, 14, 'L3', 4),
(25, 15, 'EUF', 1),
(26, 15, 'L1', 2),
(27, 15, 'L2', 3),
(28, 15, 'L3', 4),
(29, 16, 'EUF', 1),
(30, 16, 'L1', 2),
(31, 16, 'L2', 3),
(32, 16, 'L3', 4),
(33, 17, 'EUF', 1),
(34, 17, 'L1', 2),
(35, 17, 'L2', 3),
(36, 17, 'L3', 4);

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `session`, `id_etu`, `id_cours`, `note`, `annee_academique`) VALUES
(1, 1, 3, 7, 70, 1),
(2, 1, 5, 7, 78, 1),
(3, 1, 3, 8, 53, 1),
(4, 1, 5, 8, 75, 1),
(5, 1, 3, 12, 75, 1),
(6, 1, 5, 12, 77, 1),
(7, 1, 3, 13, 70, 1),
(8, 1, 5, 13, 65, 1),
(9, 1, 6, 7, 75, 1),
(10, 1, 6, 8, 65, 1),
(11, 1, 6, 12, 30, 1),
(12, 1, 6, 13, 80, 1),
(13, 1, 3, 14, 68, 1),
(14, 1, 5, 14, 75, 1),
(15, 1, 6, 14, 80, 1),
(16, 1, 7, 11, 78, 1),
(17, 1, 8, 11, 56, 1),
(18, 1, 7, 15, 67, 1),
(19, 1, 8, 15, 72, 1),
(20, 1, 4, 16, 70, 1),
(21, 1, 13, 11, 67, 1),
(22, 1, 13, 15, 70, 1),
(23, 1, 7, 18, 89, 1),
(24, 1, 8, 18, 77, 1),
(25, 1, 13, 18, 70, 1),
(26, 1, 10, 17, 80, 1),
(27, 1, 12, 17, 79, 1),
(28, 1, 10, 19, 83, 1),
(29, 1, 12, 19, 75, 1),
(30, 1, 11, 20, 85, 1),
(31, 2, 3, 21, 80, 1),
(32, 2, 5, 21, 75, 1),
(33, 2, 6, 21, 67, 1),
(34, 2, 3, 22, 65, 1),
(35, 2, 5, 22, 68, 1),
(36, 2, 6, 22, 57, 1),
(37, 2, 3, 23, 70, 1),
(38, 2, 5, 23, 68, 1),
(39, 2, 6, 23, 70, 1),
(40, 2, 7, 24, 72, 1),
(41, 2, 8, 24, 69, 1),
(42, 2, 13, 24, 68, 1),
(43, 2, 7, 25, 75, 1),
(44, 2, 8, 25, 77, 1),
(45, 2, 13, 25, 70, 1),
(46, 2, 11, 27, 70, 1),
(47, 2, 10, 26, 85, 1),
(48, 2, 12, 26, 75, 1),
(49, 2, 4, 28, 75, 1),
(50, 2, 4, 29, 63, 1),
(51, 1, 3, 20, 75, 5),
(52, 1, 5, 20, 78, 5),
(53, 1, 6, 7, 69, 5),
(54, 1, 6, 14, 78, 5),
(55, 1, 20, 20, 70, 5);

--
-- Déchargement des données de la table `professeurs`
--

INSERT INTO `professeurs` (`id`, `identite`, `niveau_etude`, `status_matrimoniale`, `salaire`, `etat`, `annee_academique`) VALUES
(3, 4, 'Master', 'Marié(e)', 90000, 'M', 1),
(4, 5, 'Master', 'Célibataire', 60000, 'A', 1),
(5, 6, 'Master', 'CÃ©libataire', 75000, 'A', 1),
(8, 12, 'Master', 'MariÃ©(e)', 56000, 'A', 1),
(9, 16, 'Licence', 'Célibataire', 70000, 'A', 1),
(10, 18, 'Master', 'Marié(e)', 60000, 'A', 1),
(11, 27, 'Doctorat', 'Marié(e)', 80000, 'M', 1),
(12, 38, 'Doctorat', 'MariÃ©(e)', 92000, 'A', 5);

--
-- Déchargement des données de la table `sessiontrace`
--

INSERT INTO `sessiontrace` (`id`, `user`, `token`, `last_seen`, `online`, `permanent`) VALUES
(2, 1, '&6@F9710971', '2021-12-30 20:52:38', 1, 0);

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `identite`, `pseudo`, `passcode`, `etat`, `access`, `date_creation`) VALUES
(1, 1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'actif', '0,1,2,3,4,5,6,7,8,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44', '2021-10-27 11:26:33');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
