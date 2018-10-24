-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le :  jeu. 25 jan. 2018 à 00:37
-- Version du serveur :  5.7.19-log
-- Version de PHP :  7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `charge_cde_dev`
--

-- --------------------------------------------------------

--
-- Structure de la table `bl_series_starters`
--

CREATE TABLE `bl_series_starters` (
  `id` int(10) UNSIGNED NOT NULL,
  `serie_id` int(3) UNSIGNED NOT NULL,
  `bl_starter` int(6) UNSIGNED NOT NULL COMMENT 'Identifiant Série sur lequel commencer à compter',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bl_series_starters`
--

INSERT INTO `bl_series_starters` (`id`, `serie_id`, `bl_starter`, `created_at`) VALUES
(1, 723, 723001, '2017-12-17 18:12:52'),
(17, 910, 910001, '2017-12-18 00:31:59'),
(19, 959, 959001, '2017-12-18 00:33:10'),
(24, 923, 923001, '2017-12-18 11:51:15'),
(32, 723, 723002, '2017-12-18 18:10:46'),
(33, 923, 923002, '2017-12-18 18:11:54'),
(34, 923, 923003, '2017-12-18 18:11:59'),
(35, 723, 723003, '2017-12-18 18:19:25'),
(37, 723, 723005, '2017-12-18 18:44:21'),
(38, 723, 723006, '2017-12-19 15:05:08'),
(39, 723, 723007, '2017-12-19 20:54:50'),
(40, 723, 723008, '2017-12-19 20:55:04'),
(41, 723, 723009, '2017-12-19 20:57:51'),
(42, 723, 723010, '2017-12-19 20:58:18'),
(43, 723, 723011, '2017-12-19 21:12:24'),
(44, 723, 723012, '2017-12-19 21:16:13'),
(45, 723, 723013, '2017-12-19 21:17:05'),
(46, 723, 723014, '2017-12-19 21:18:47'),
(47, 723, 723015, '2017-12-19 21:18:51'),
(48, 723, 723016, '2017-12-19 21:23:06'),
(49, 723, 723017, '2017-12-19 21:24:59'),
(50, 723, 723018, '2017-12-19 21:25:40'),
(53, 723, 723021, '2017-12-19 21:44:07'),
(54, 723, 723022, '2017-12-22 14:03:50'),
(55, 723, 723023, '2017-12-22 14:56:08'),
(56, 723, 723024, '2017-12-22 17:02:44'),
(59, 723, 723027, '2017-12-22 18:44:08'),
(60, 723, 723028, '2017-12-22 18:45:56'),
(61, 723, 723029, '2017-12-26 22:34:50'),
(62, 910, 910004, '2017-12-26 22:38:18'),
(63, 723, 723030, '2017-12-26 22:40:52'),
(64, 723, 723031, '2018-01-07 13:59:35'),
(65, 723, 723032, '2018-01-07 14:11:16'),
(66, 723, 723033, '2018-01-07 14:23:02'),
(67, 723, 723034, '2018-01-07 17:04:33'),
(68, 723, 723035, '2018-01-07 17:11:59'),
(69, 723, 723036, '2018-01-07 17:17:55'),
(70, 723, 723037, '2018-01-07 18:36:08'),
(71, 723, 723038, '2018-01-07 18:36:50'),
(72, 723, 723039, '2018-01-07 18:48:17'),
(73, 723, 723040, '2018-01-07 19:07:58'),
(74, 723, 723041, '2018-01-07 19:18:24'),
(76, 723, 723043, '2018-01-11 22:05:42'),
(77, 723, 723044, '2018-01-11 22:06:53'),
(78, 723, 723045, '2018-01-11 22:07:15'),
(79, 723, 723046, '2018-01-11 22:08:07'),
(80, 723, 723047, '2018-01-11 22:08:55'),
(81, 723, 723048, '2018-01-11 22:10:04'),
(82, 723, 723049, '2018-01-11 22:10:17'),
(83, 723, 723050, '2018-01-13 13:23:47'),
(84, 923, 923004, '2018-01-20 22:35:12'),
(85, 923, 923005, '2018-01-20 23:46:18'),
(86, 910, 910005, '2018-01-20 23:46:22'),
(87, 910, 910006, '2018-01-20 23:47:02'),
(88, 723, 723051, '2018-01-24 23:46:41'),
(89, 949, 949001, '2018-01-24 23:46:47');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'Numéro de facture VME',
  `vendor_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Identifiant du vendeur',
  `serie_id` int(3) UNSIGNED NOT NULL,
  `bl_id` int(6) DEFAULT NULL COMMENT 'Numéro de BL',
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Client qui a passé commande',
  `client_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Référence commande chez le client',
  `quantity` int(10) UNSIGNED DEFAULT '0' COMMENT 'Quantité de pièces',
  `machine_ts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `depart_ts` date DEFAULT NULL,
  `sous_traitant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transport` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `processing_at` date DEFAULT NULL COMMENT 'Date de traitement de la commande par l''ilôt',
  `delivery_at` date DEFAULT NULL,
  `cas_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numéro de Cas',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de prise de la commande',
  `reception_at` datetime DEFAULT NULL COMMENT 'Date de réception par le client',
  `canceled_at` datetime DEFAULT NULL COMMENT 'Détermine si la commande est annulée'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `vendor_id`, `serie_id`, `bl_id`, `client_name`, `client_reference`, `quantity`, `machine_ts`, `depart_ts`, `sous_traitant`, `transport`, `processing_at`, `delivery_at`, `cas_type`, `created_at`, `reception_at`, `canceled_at`) VALUES
(123432, '10485311', 910, 910003, 'THYSS', 'ERZR', 10, '14', '2018-01-07', 'SNM', 'DHL', '2017-12-18', '2018-01-31', '5', '2017-12-18 11:55:26', NULL, '2017-12-18 11:58:03'),
(412547, '10485311', 723, 723020, 'AAAGDFG', 'KJJKJU', 100, '11', '2017-12-20', 'SAZE', 'FSF', '2017-12-19', '2017-12-27', '5', '2017-12-19 21:43:26', NULL, NULL),
(455636, '10485311', 723, 723019, 'AQDQ', 'KJGGGJ', 5, NULL, NULL, NULL, NULL, '2017-12-19', '2017-12-21', '5', '2017-12-19 21:35:33', NULL, NULL),
(544120, '10485311', 723, 723025, 'DUPONT', 'SSSFSS545', 5, '11', '2017-12-25', 'AAA', 'DDD', '2017-12-22', '2017-12-26', '5+24', '2017-12-22 17:17:11', NULL, '2017-12-22 18:32:58'),
(544128, '10485311', 723, 723042, 'AAZEAE', 'HHGDHCB', 0, '11', '2018-01-12', 'SDF', 'SSSQ', '2018-01-11', '2018-01-18', '5+24', '2018-01-11 22:03:39', NULL, NULL),
(544213, '10485311', 723, 723004, 'DUPONT', 'REF12345695', 4, '14', '2017-12-20', 'YAPAS', 'SURLEDOS', '2017-12-19', NULL, '5', '2017-12-18 18:43:56', NULL, NULL),
(588740, '10485311', 949, 949002, 'AQDQD', 'QAZEAZE', 9, '11', '2018-01-31', 'SDF', NULL, '2018-01-24', '2018-02-01', '5', '2018-01-24 23:47:16', NULL, NULL),
(588745, '10485311', 723, 723026, 'DUPONT', 'REF123', 9, '4', '2018-01-07', 'SNM', 'DHL', '2017-12-26', NULL, '15', '2017-12-22 18:43:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commandes_comments`
--

CREATE TABLE `commandes_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `bill_id` int(10) UNSIGNED NOT NULL,
  `commented_by` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comment_type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type de commentaire',
  `comment_text` text COLLATE utf8mb4_unicode_ci COMMENT 'Commentaire libre',
  `comment_date` date DEFAULT NULL COMMENT 'Commentaire nécessitant une date particulière',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes_comments`
--

INSERT INTO `commandes_comments` (`id`, `bill_id`, `commented_by`, `comment_type`, `comment_text`, `comment_date`, `created_at`) VALUES
(36, 123432, '10460284', 'approvisionnement', '5 pièces depuis le dépôt 001 Maurepas (Magasin)', '2017-12-18', '2017-12-18 11:55:26'),
(37, 123432, '10460284', 'approvisionnement', '5 pièces depuis le dépôt 001 Maurepas (Magasin)', '2017-12-18', '2017-12-18 11:55:26'),
(38, 123432, '10460284', 'annulation_commande', NULL, '2017-12-18', '2017-12-18 11:58:03'),
(42, 544213, '10485311', 'approvisionnement', '4 pièces depuis le dépôt 045 Bourges (Transfo)', '2017-12-19', '2017-12-18 18:43:56'),
(44, 455636, '10485311', 'approvisionnement', '5 pièces depuis le dépôt 001 Maurepas (Magasin)', '2017-12-19', '2017-12-19 21:35:33'),
(45, 412547, '10485311', 'approvisionnement', '100 pièces depuis le dépôt 045 Bourges (Transfo)', '2017-12-19', '2017-12-19 21:43:26'),
(46, 544120, '10485311', 'approvisionnement', '5 pièces depuis le dépôt 001 Maurepas (Magasin)', '2017-12-22', '2017-12-22 17:17:11'),
(47, 544120, '10485311', 'annulation_commande', 'parce que', '2017-12-22', '2017-12-22 18:32:58'),
(48, 588745, '10485311', 'approvisionnement', '9 pièces depuis le dépôt 013 Besançon', '2017-12-26', '2017-12-22 18:43:35'),
(49, 544128, '10485311', 'approvisionnement', '0 pièces depuis le dépôt 001 Maurepas (Magasin)', '2018-01-11', '2018-01-11 22:03:39'),
(50, 588740, '10485311', 'approvisionnement', '9 pièces depuis le dépôt 001 Maurepas (Magasin)', '2018-01-24', '2018-01-24 23:47:16');

-- --------------------------------------------------------

--
-- Structure de la table `commandes_processing`
--

CREATE TABLE `commandes_processing` (
  `id` int(10) UNSIGNED NOT NULL,
  `bill_id` int(10) UNSIGNED NOT NULL COMMENT 'Numéro de facture VME',
  `quantity` int(10) UNSIGNED NOT NULL COMMENT 'Quantité de pièces prélevées dans le dépôt',
  `from_depot` int(3) UNSIGNED ZEROFILL NOT NULL COMMENT 'Dépôt de prélèvement',
  `to_ilot` int(10) UNSIGNED NOT NULL,
  `processing_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes_processing`
--

INSERT INTO `commandes_processing` (`id`, `bill_id`, `quantity`, `from_depot`, `to_ilot`, `processing_at`) VALUES
(19, 123432, 5, 001, 5, '2017-12-18'),
(20, 123432, 5, 001, 5, '2017-12-18'),
(23, 544213, 4, 045, 1, '2017-12-19'),
(25, 455636, 5, 001, 1, '2017-12-19'),
(26, 412547, 100, 045, 1, '2017-12-19'),
(27, 544120, 5, 001, 8, '2017-12-22'),
(28, 588745, 9, 013, 8, '2017-12-26'),
(29, 544128, 0, 001, 8, '2018-01-11'),
(30, 588740, 9, 001, 6, '2018-01-24');

--
-- Déclencheurs `commandes_processing`
--
DELIMITER $$
CREATE TRIGGER `commandes_processing_AFTER_DELETE` AFTER DELETE ON `commandes_processing` FOR EACH ROW BEGIN
	UPDATE `commandes`
		SET `commandes`.`quantity` = (`commandes`.`quantity` - OLD.`quantity`)
	WHERE `commandes`.`id` = OLD.`bill_id`;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `commandes_processing_AFTER_INSERT` AFTER INSERT ON `commandes_processing` FOR EACH ROW BEGIN
	UPDATE `commandes`
		SET `commandes`.`quantity` = (`commandes`.`quantity` + NEW.`quantity`)
	WHERE `commandes`.`id` = NEW.`bill_id`;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `commandes_processing_AFTER_UPDATE` AFTER UPDATE ON `commandes_processing` FOR EACH ROW BEGIN
	UPDATE `commandes`
		SET `commandes`.`quantity` = (`commandes`.`quantity` - OLD.`quantity` + NEW.`quantity`)
	WHERE `commandes`.`id` = NEW.`bill_id`;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `comments_types`
--

CREATE TABLE `comments_types` (
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type de commentaire',
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Désignation du type de commentaire'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comments_types`
--

INSERT INTO `comments_types` (`type`, `label`) VALUES
('annulation_commande', 'Annulation commande'),
('approvisionnement', 'Approvisionnement'),
('cause_retard', 'Cause du retard'),
('date_cubage', 'Date de Cubage'),
('date_depart', 'Date de départ commande'),
('date_depart_prevue', 'Date de départ prévue'),
('date_depart_reportee', 'Date de départ reportée'),
('date_reception_client', 'Date de réception client'),
('date_relance', 'Date de relance'),
('transporteur', 'Transporteur');

-- --------------------------------------------------------

--
-- Structure de la table `depots`
--

CREATE TABLE `depots` (
  `id` int(3) UNSIGNED ZEROFILL NOT NULL COMMENT 'Numéro VME',
  `name` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom du dépôt',
  `open` tinyint(3) UNSIGNED DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `depots`
--

INSERT INTO `depots` (`id`, `name`, `open`) VALUES
(001, 'Maurepas (Magasin)', 1),
(010, 'Oyonnax', 1),
(013, 'Besançon', 1),
(020, 'Le Mans (Magasin)', 1),
(023, 'Le Mans (Call Center)', 1),
(045, 'Bourges (Transfo)', 1),
(046, 'Bourges (Agence)', 1),
(047, 'Bourges (Call Center)', 1),
(800, 'SNM', 1);

-- --------------------------------------------------------

--
-- Structure de la table `ilots`
--

CREATE TABLE `ilots` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nom de l''ilot dans VME',
  `location` int(3) UNSIGNED ZEROFILL NOT NULL COMMENT 'Dépôt physique où est situé l''ilôt',
  `label` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom courant pour désigner l''ilot',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Couleur permettant d''identifier ses évènements sur le planning',
  `dx` int(10) UNSIGNED DEFAULT '0',
  `dy` int(10) UNSIGNED DEFAULT '0',
  `dz` int(10) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ilots`
--

INSERT INTO `ilots` (`id`, `name`, `location`, `label`, `color`, `dx`, `dy`, `dz`) VALUES
(1, 'MB_SNM', 800, 'Micro-blocs', '#728fff', NULL, NULL, NULL),
(2, 'PP_020', 020, 'Petites pièces', '#ffe345', 550, 550, 160),
(3, 'MP_020', 020, 'Moyennes pièces', '#7dffb8', 1200, 1200, 400),
(4, 'GP_020', 020, 'Grandes pièces', '#daa3ff', NULL, NULL, NULL),
(5, 'PP BESANÇON', 013, 'Petites pièces', '#82ff08', 430, 430, 150),
(6, 'MP BESANÇON', 013, 'Moyennes pièces', '#9b2c3f', 800, 800, 243),
(7, 'GP BESANÇON', 013, 'Grandes pièces', '#3d0085', 1000, 1000, NULL),
(8, 'MB BESANÇON', 013, 'Micro-blocs', '#ff5870', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ilots_commandes_charge`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `ilots_commandes_charge` (
`id` int(10) unsigned
,`charge_id` int(10) unsigned
,`ilot_id` int(10) unsigned
,`original_quantity` int(10) unsigned
,`commandes_quantity` bigint(21)
,`remaining_quantity` bigint(21)
,`quantity_at` date
);

-- --------------------------------------------------------

--
-- Structure de la table `ilots_daily_charge`
--

CREATE TABLE `ilots_daily_charge` (
  `id` int(10) UNSIGNED NOT NULL,
  `ilot_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED DEFAULT '0' COMMENT 'Quantité de pièces pour la journée',
  `quantity_at` date DEFAULT NULL COMMENT 'Quantité disponible à cette date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ilots_daily_charge`
--

INSERT INTO `ilots_daily_charge` (`id`, `ilot_id`, `quantity`, `quantity_at`) VALUES
(1, 5, 100, '2017-12-18'),
(3, 1, 100, '2017-12-19'),
(4, 1, 100, '2017-12-18'),
(5, 6, 1, '2018-01-02'),
(7, 7, 12, '2018-01-25'),
(9, 6, 50, '2018-01-24');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ilots_processings`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `ilots_processings` (
`id` varchar(33)
,`from_table` varchar(4)
,`records_ids` text
,`planning_id` int(10) unsigned
,`charge_id` bigint(10) unsigned
,`ilot_id` int(10) unsigned
,`original_quantity` bigint(11) unsigned
,`commandes_quantity` bigint(21) unsigned
,`remaining_quantity` bigint(21)
,`quantity_at` date
,`orphan` int(1)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ilots_processing_charge`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `ilots_processing_charge` (
`id` decimal(33,0)
,`charge_id` int(11) unsigned
,`ilot_id` int(11) unsigned
,`original_quantity` bigint(20) unsigned
,`commandes_quantity` bigint(21)
,`remaining_quantity` bigint(21)
,`quantity_at` date
,`from_table` varchar(3)
);

-- --------------------------------------------------------

--
-- Structure de la table `plannings`
--

CREATE TABLE `plannings` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plannings`
--

INSERT INTO `plannings` (`id`, `slug`, `label`, `color`) VALUES
(1, 'tkmf-ateliers', 'tkMF Ateliers', '#1fa9ff'),
(2, 'snm', 'SNM', '#70ff5e');

-- --------------------------------------------------------

--
-- Structure de la table `plannings_charges`
--

CREATE TABLE `plannings_charges` (
  `id` int(11) UNSIGNED NOT NULL,
  `planning_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED DEFAULT '0',
  `quantity_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plannings_ilots`
--

CREATE TABLE `plannings_ilots` (
  `id` int(10) UNSIGNED NOT NULL,
  `planning_id` int(10) UNSIGNED NOT NULL,
  `ilot_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plannings_ilots`
--

INSERT INTO `plannings_ilots` (`id`, `planning_id`, `ilot_id`) VALUES
(2, 2, 1),
(3, 1, 2),
(4, 1, 3),
(5, 1, 4),
(6, 1, 6),
(7, 1, 7),
(10, 2, 8);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `plannings_processings`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `plannings_processings` (
`from_table` varchar(4)
,`id` mediumtext
,`original_quantity` bigint(11)
,`commandes_quantity` bigint(21) unsigned
,`remaining_quantity` bigint(21)
,`to_ilots` mediumtext
,`planning_id` int(11) unsigned
,`charge_id` bigint(20) unsigned
,`orphan` bigint(20)
,`quantity_at` date
);

-- --------------------------------------------------------

--
-- Structure de la table `plannings_series`
--

CREATE TABLE `plannings_series` (
  `id` int(10) UNSIGNED NOT NULL,
  `planning_id` int(10) UNSIGNED NOT NULL,
  `serie_id` int(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `plannings_series`
--

INSERT INTO `plannings_series` (`id`, `planning_id`, `serie_id`) VALUES
(1, 1, 910),
(2, 1, 923),
(3, 2, 723),
(4, 1, 949),
(5, 1, 959);

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE `series` (
  `id` int(3) UNSIGNED NOT NULL COMMENT 'Identifiant de la Série',
  `depot_id` int(3) UNSIGNED ZEROFILL NOT NULL COMMENT 'Dépôt de traitement',
  `label` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nom de la Série'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `series`
--

INSERT INTO `series` (`id`, `depot_id`, `label`) VALUES
(723, 800, NULL),
(910, 010, NULL),
(923, 023, NULL),
(949, 023, 'OXY'),
(959, 013, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'sAMAccountName',
  `vme_id` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Code Vendeur VME',
  `displayname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'guest' COMMENT 'Role de l''utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `vme_id`, `displayname`, `role`) VALUES
('10460284', 'F5', 'Cyril Ausseur', 'admin'),
('10485311', 'GS', 'Gaëtan Simon', 'dev'),
('jdoe', 'JD', 'John Doe', 'dev');

-- --------------------------------------------------------

--
-- Structure de la vue `ilots_commandes_charge`
--
DROP TABLE IF EXISTS `ilots_commandes_charge`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ilots_commandes_charge`  AS  select `cp`.`id` AS `id`,`idc`.`id` AS `charge_id`,`idc`.`ilot_id` AS `ilot_id`,`idc`.`quantity` AS `original_quantity`,cast(sum(`cp`.`quantity`) as signed) AS `commandes_quantity`,cast((cast(`idc`.`quantity` as signed) - cast(sum(`cp`.`quantity`) as signed)) as signed) AS `remaining_quantity`,`idc`.`quantity_at` AS `quantity_at` from ((`commandes_processing` `cp` left join `ilots_daily_charge` `idc` on((`cp`.`to_ilot` = `idc`.`ilot_id`))) left join `commandes` `cde` on((`cde`.`id` = `cp`.`bill_id`))) where ((`cp`.`to_ilot` = `idc`.`ilot_id`) and (`cp`.`processing_at` = `idc`.`quantity_at`) and isnull(`cde`.`canceled_at`)) group by `cp`.`to_ilot`,`cp`.`processing_at` order by `cp`.`id` ;

-- --------------------------------------------------------

--
-- Structure de la vue `ilots_processings`
--
DROP TABLE IF EXISTS `ilots_processings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ilots_processings`  AS  select concat(`iltp`.`from_table`,`iltp`.`ilot_id`,ifnull(`iltp`.`planning_id`,0),date_format(`iltp`.`quantity_at`,'%Y%m%d')) AS `id`,`iltp`.`from_table` AS `from_table`,`iltp`.`records_ids` AS `records_ids`,`iltp`.`planning_id` AS `planning_id`,`iltp`.`charge_id` AS `charge_id`,`iltp`.`ilot_id` AS `ilot_id`,cast(if(isnull(`iltp`.`charge_id`),0,`iltc`.`quantity`) as unsigned) AS `original_quantity`,`iltp`.`quantity` AS `commandes_quantity`,cast((if(isnull(`iltp`.`charge_id`),0,`iltc`.`quantity`) - `iltp`.`quantity`) as signed) AS `remaining_quantity`,`iltp`.`quantity_at` AS `quantity_at`,`iltp`.`orphan` AS `orphan` from (((select cast('cdep' as char(4) charset utf8mb4) AS `from_table`,group_concat(`cdep`.`id` separator ',') AS `records_ids`,`cdep`.`processing_at` AS `quantity_at`,cast(sum(`cdep`.`quantity`) as unsigned) AS `quantity`,`plni`.`planning_id` AS `planning_id`,`cdep`.`to_ilot` AS `ilot_id`,if(isnull(`iltc`.`id`),NULL,`iltc`.`id`) AS `charge_id`,isnull(`plni`.`planning_id`) AS `orphan` from ((`commandes_processing` `cdep` left join `ilots_daily_charge` `iltc` on(((`iltc`.`ilot_id` = `cdep`.`to_ilot`) and (`iltc`.`quantity_at` = `cdep`.`processing_at`)))) left join `plannings_ilots` `plni` on((`plni`.`ilot_id` = `cdep`.`to_ilot`))) group by `plni`.`planning_id`,`cdep`.`to_ilot`,`cdep`.`processing_at` order by `cdep`.`id`)) `iltp` left join `ilots_daily_charge` `iltc` on(((`iltc`.`ilot_id` = `iltp`.`ilot_id`) and (`iltc`.`quantity_at` = `iltp`.`quantity_at`)))) group by `iltp`.`planning_id`,`iltp`.`charge_id`,`iltp`.`ilot_id`,`iltp`.`quantity_at` ;

-- --------------------------------------------------------

--
-- Structure de la vue `ilots_processing_charge`
--
DROP TABLE IF EXISTS `ilots_processing_charge`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `ilots_processing_charge`  AS  select `ilots_commandes_charge`.`charge_id` AS `id`,`ilots_commandes_charge`.`charge_id` AS `charge_id`,`ilots_commandes_charge`.`ilot_id` AS `ilot_id`,`ilots_commandes_charge`.`original_quantity` AS `original_quantity`,`ilots_commandes_charge`.`commandes_quantity` AS `commandes_quantity`,`ilots_commandes_charge`.`remaining_quantity` AS `remaining_quantity`,`ilots_commandes_charge`.`quantity_at` AS `quantity_at`,'icc' AS `from_table` from `ilots_commandes_charge` union select `idc`.`id` AS `id`,`idc`.`id` AS `charge_id`,`idc`.`ilot_id` AS `ilot_id`,`idc`.`quantity` AS `original_quantity`,0 AS `commandes_quantity`,`idc`.`quantity` AS `remaining_quantity`,`idc`.`quantity_at` AS `quantity_at`,'idc' AS `from_table` from `ilots_daily_charge` `idc` where (not(`idc`.`id` in (select `irc`.`charge_id` from `ilots_commandes_charge` `irc`))) union select sum((0 - cast(`ccp`.`id` as signed))) AS `id`,NULL AS `charge_id`,`ccp`.`to_ilot` AS `ilot_id`,0 AS `original_quantity`,cast(sum(`ccp`.`quantity`) as signed) AS `commandes_quantity`,cast((cast(0 as signed) - cast(sum(`ccp`.`quantity`) as signed)) as signed) AS `remaining_quantity`,`ccp`.`processing_at` AS `processing_at`,'cp' AS `from_table` from (`commandes_processing` `ccp` left join `ilots_commandes_charge` `icc` on(((`icc`.`ilot_id` = `ccp`.`to_ilot`) and (`icc`.`quantity_at` = `ccp`.`processing_at`)))) where isnull(`icc`.`ilot_id`) group by `ccp`.`to_ilot`,`ccp`.`processing_at` ;

-- --------------------------------------------------------

--
-- Structure de la vue `plannings_processings`
--
DROP TABLE IF EXISTS `plannings_processings`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `plannings_processings`  AS  select `plannings_processings`.`from_table` AS `from_table`,`plannings_processings`.`from_ids` AS `id`,ifnull(`plnc`.`quantity`,0) AS `original_quantity`,cast(sum(`plannings_processings`.`quantity`) as unsigned) AS `commandes_quantity`,cast((ifnull(`plnc`.`quantity`,0) - sum(`plannings_processings`.`quantity`)) as signed) AS `remaining_quantity`,`plannings_processings`.`to_ilots` AS `to_ilots`,`plannings_processings`.`planning_id` AS `planning_id`,`plannings_processings`.`charge_id` AS `charge_id`,`plannings_processings`.`orphan` AS `orphan`,cast(`plannings_processings`.`processing_at` as date) AS `quantity_at` from (((select cast('cdep' as char(4) charset utf8mb4) AS `from_table`,to_base64(concat(`cdep`.`processing_at`,':',group_concat(`cdep`.`id` separator ','))) AS `from_ids`,cast(`cdep`.`processing_at` as date) AS `processing_at`,cast(sum(`cdep`.`quantity`) as unsigned) AS `quantity`,group_concat(`cdep`.`to_ilot` separator ',') AS `to_ilots`,`plni`.`planning_id` AS `planning_id`,if(isnull(`plni`.`planning_id`),NULL,`plnc`.`id`) AS `charge_id`,isnull(`plni`.`planning_id`) AS `orphan` from ((`commandes_processing` `cdep` left join `plannings_ilots` `plni` on((`plni`.`ilot_id` = `cdep`.`to_ilot`))) left join `plannings_charges` `plnc` on((`plnc`.`quantity_at` = `cdep`.`processing_at`))) group by `plnc`.`id`,`plni`.`planning_id`,`cdep`.`processing_at`)) `plannings_processings` left join `plannings_charges` `plnc` on((`plnc`.`id` = `plannings_processings`.`charge_id`))) group by `plannings_processings`.`charge_id`,`plannings_processings`.`planning_id`,`plannings_processings`.`processing_at` union select cast('plnc' as char(4) charset utf8mb4) AS `from_table`,to_base64(concat('plnc',`plnc`.`quantity_at`,':',`plnc`.`id`)) AS `from_ids`,`plnc`.`quantity` AS `original_quantity`,0 AS `commandes_quantity`,`plnc`.`quantity` AS `remaining_quantity`,NULL AS `to_ilots`,`plnc`.`planning_id` AS `planning_id`,`plnc`.`id` AS `charge_id`,0 AS `orphan`,`plnc`.`quantity_at` AS `quantity_at` from `plannings_charges` `plnc` ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bl_series_starters`
--
ALTER TABLE `bl_series_starters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bl_series_starters_series1_idx` (`serie_id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_id_UNIQUE` (`id`),
  ADD KEY `fk_commandes_users1_idx` (`vendor_id`),
  ADD KEY `fk_commandes_series1_idx` (`serie_id`),
  ADD KEY `transport` (`transport`),
  ADD KEY `sous_traitant` (`sous_traitant`),
  ADD KEY `machine_ts` (`machine_ts`),
  ADD KEY `depart_ts` (`depart_ts`),
  ADD KEY `delivery_at` (`delivery_at`);

--
-- Index pour la table `commandes_comments`
--
ALTER TABLE `commandes_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commandes_history_commandes1_idx` (`bill_id`),
  ADD KEY `fk_commandes_comments_comments_types1_idx` (`comment_type`),
  ADD KEY `fk_commandes_comments_users1_idx` (`commented_by`);

--
-- Index pour la table `commandes_processing`
--
ALTER TABLE `commandes_processing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commandes_supplying_commandes1_idx` (`bill_id`),
  ADD KEY `fk_commandes_supplying_depots1_idx` (`from_depot`),
  ADD KEY `fk_commandes_processing_ilots1_idx` (`to_ilot`);

--
-- Index pour la table `comments_types`
--
ALTER TABLE `comments_types`
  ADD PRIMARY KEY (`type`),
  ADD UNIQUE KEY `name_UNIQUE` (`type`);

--
-- Index pour la table `depots`
--
ALTER TABLE `depots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`);

--
-- Index pour la table `ilots`
--
ALTER TABLE `ilots`
  ADD PRIMARY KEY (`id`,`name`),
  ADD UNIQUE KEY `name_UNIQUE` (`name`),
  ADD KEY `fk_ilots_depots1_idx` (`location`);

--
-- Index pour la table `ilots_daily_charge`
--
ALTER TABLE `ilots_daily_charge`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ilots_charge_ilots1_idx` (`ilot_id`);

--
-- Index pour la table `plannings`
--
ALTER TABLE `plannings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug_2` (`slug`);

--
-- Index pour la table `plannings_charges`
--
ALTER TABLE `plannings_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plannings_charges_plannings1_idx` (`planning_id`);

--
-- Index pour la table `plannings_ilots`
--
ALTER TABLE `plannings_ilots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plannings_ilots_plannings1_idx` (`planning_id`),
  ADD KEY `fk_plannings_ilots_ilots1_idx` (`ilot_id`);

--
-- Index pour la table `plannings_series`
--
ALTER TABLE `plannings_series`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plannings_series_plannings1_idx` (`planning_id`),
  ADD KEY `fk_plannings_series_series1_idx` (`serie_id`);

--
-- Index pour la table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `fk_series_depots1_idx` (`depot_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `vme_id_UNIQUE` (`vme_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bl_series_starters`
--
ALTER TABLE `bl_series_starters`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT pour la table `commandes_comments`
--
ALTER TABLE `commandes_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `commandes_processing`
--
ALTER TABLE `commandes_processing`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `ilots`
--
ALTER TABLE `ilots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `ilots_daily_charge`
--
ALTER TABLE `ilots_daily_charge`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `plannings`
--
ALTER TABLE `plannings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `plannings_charges`
--
ALTER TABLE `plannings_charges`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plannings_ilots`
--
ALTER TABLE `plannings_ilots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `plannings_series`
--
ALTER TABLE `plannings_series`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bl_series_starters`
--
ALTER TABLE `bl_series_starters`
  ADD CONSTRAINT `fk_bl_series_starters_series1` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `fk_commandes_series1` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_commandes_users1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `commandes_comments`
--
ALTER TABLE `commandes_comments`
  ADD CONSTRAINT `fk_commandes_comments_comments_types1` FOREIGN KEY (`comment_type`) REFERENCES `comments_types` (`type`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_commandes_comments_users1` FOREIGN KEY (`commented_by`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_commandes_history_commandes1` FOREIGN KEY (`bill_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Contraintes pour la table `commandes_processing`
--
ALTER TABLE `commandes_processing`
  ADD CONSTRAINT `fk_commandes_processing_ilots1` FOREIGN KEY (`to_ilot`) REFERENCES `ilots` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_commandes_supplying_commandes1` FOREIGN KEY (`bill_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_commandes_supplying_depots1` FOREIGN KEY (`from_depot`) REFERENCES `depots` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `ilots`
--
ALTER TABLE `ilots`
  ADD CONSTRAINT `fk_ilots_depots1` FOREIGN KEY (`location`) REFERENCES `depots` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `ilots_daily_charge`
--
ALTER TABLE `ilots_daily_charge`
  ADD CONSTRAINT `fk_ilots_daily_charge_ilots1` FOREIGN KEY (`ilot_id`) REFERENCES `ilots` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `plannings_charges`
--
ALTER TABLE `plannings_charges`
  ADD CONSTRAINT `fk_plannings_charges_plannings1` FOREIGN KEY (`planning_id`) REFERENCES `plannings` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `plannings_ilots`
--
ALTER TABLE `plannings_ilots`
  ADD CONSTRAINT `fk_plannings_ilots_ilots1` FOREIGN KEY (`ilot_id`) REFERENCES `ilots` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_plannings_ilots_plannings1` FOREIGN KEY (`planning_id`) REFERENCES `plannings` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `plannings_series`
--
ALTER TABLE `plannings_series`
  ADD CONSTRAINT `fk_plannings_series_plannings1` FOREIGN KEY (`planning_id`) REFERENCES `plannings` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_plannings_series_series1` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `fk_series_depots1` FOREIGN KEY (`depot_id`) REFERENCES `depots` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
