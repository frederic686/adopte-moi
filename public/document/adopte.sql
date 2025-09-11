-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 11 sep. 2025 à 15:48
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `adopte`
--

-- --------------------------------------------------------

--
-- Structure de la table `animal`
--

DROP TABLE IF EXISTS `animal`;
CREATE TABLE IF NOT EXISTS `animal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `eleveur_id` int NOT NULL,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `race` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexe` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_publication` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `type_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6AAB231F489D1B5F` (`eleveur_id`),
  KEY `IDX_6AAB231FC54C8C93` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `animal`
--

INSERT INTO `animal` (`id`, `eleveur_id`, `nom`, `description`, `race`, `sexe`, `age`, `photo`, `date_publication`, `type_id`) VALUES
(3, 4, 'filou', 'chien de grande taille magnifique et beau', 'mixe', 'male', 8, '/uploads/animals/chien-style-68a87fdf5e4c42.01587281.png', '2025-08-22 14:34:07', 1),
(4, 4, 'boubou', 'oiseuai magnifique chante bien', 'petite', 'femelle', 15, '/uploads/animals/oiseau-68a88028450e17.52964512.png', '2025-08-22 14:35:20', 3),
(5, 5, 'minou', 'chat super gentille saura vous aimer', 'mixte', 'male', 5, '/uploads/animals/chatmingnon-68a880c9d06bf4.42888434.png', '2025-08-22 14:38:01', 2),
(6, 5, 'miaou miaou', 'chat jeune tres adorable', 'goutiere', 'male', 4, '/uploads/animals/chatmingnon-68afffd8a7f368.84528100.png', '2025-08-22 14:39:27', 2),
(7, 5, 'lapinou', 'gentille lapin grand beau magnifiquez', 'marron', 'male', 16, '/uploads/animals/telechargement-1-68a881ce35d357.47880470.jpg', '2025-08-22 14:42:22', 4),
(10, 9, 'Cole', 'Golden Retriver de 5 ans', 'Golden Retriever', 'male', 5, '/uploads/animals/cole-68ae0d0893f108.11779780.jpg', '2025-08-26 19:37:44', 1),
(11, 6, 'parot', 'jolie peroquet tres jovial', 'mixte', 'femelle', 12, '/uploads/animals/perroquet-68b006f2078625.71710952.png', '2025-08-28 07:36:18', 3),
(13, 4, 'toutou', 'bertger allemande tres beau', 'berger allemand', 'male', 15, '/uploads/animals/sitesdefaultfilesstylessquare-medium-440x440public2022-09german20shepherd-68be96372a7cf7.53216551.jpg', '2025-09-08 08:39:19', 1);

-- --------------------------------------------------------

--
-- Structure de la table `demande_adoption`
--

DROP TABLE IF EXISTS `demande_adoption`;
CREATE TABLE IF NOT EXISTS `demande_adoption` (
  `id` int NOT NULL AUTO_INCREMENT,
  `animal_id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_AB87FF6B8E962C16` (`animal_id`),
  KEY `IDX_AB87FF6BFB88E14F` (`utilisateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `demande_adoption`
--

INSERT INTO `demande_adoption` (`id`, `animal_id`, `utilisateur_id`, `message`, `statut`, `date`) VALUES
(2, 7, 1, 'je voudrais le adopter', 'annulee', '2025-08-23 09:22:33'),
(4, 10, 7, 'tres beau chien je peut le adopter', 'en_attente', '2025-08-26 19:42:57'),
(5, 10, 1, 'trop mignon je peut  le adopter', 'en_attente', '2025-08-26 19:43:59'),
(8, 11, 12, 'oiseau magnfique je veut adpoter', 'acceptee', '2025-09-08 07:40:42');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250821132842', '2025-08-21 13:29:17', 277),
('DoctrineMigrations\\Version20250821215805', '2025-08-21 21:58:12', 80),
('DoctrineMigrations\\Version20250821220628', '2025-08-21 22:06:55', 117);

-- --------------------------------------------------------

--
-- Structure de la table `friandise`
--

DROP TABLE IF EXISTS `friandise`;
CREATE TABLE IF NOT EXISTS `friandise` (
  `id` int NOT NULL AUTO_INCREMENT,
  `animal_id` int NOT NULL,
  `envoyeur_id` int NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_160949918E962C16` (`animal_id`),
  KEY `IDX_160949914795A786` (`envoyeur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `friandise`
--

INSERT INTO `friandise` (`id`, `animal_id`, `envoyeur_id`, `type`) VALUES
(3, 7, 1, '🥕 Carotte'),
(5, 6, 1, '🐟 Poisson'),
(10, 5, 1, '🍬 Friandise'),
(11, 10, 1, '🍬 Friandise'),
(12, 11, 12, '🍬 Friandise'),
(13, 3, 12, 'os');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `type`
--

DROP TABLE IF EXISTS `type`;
CREATE TABLE IF NOT EXISTS `type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categorie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `type`
--

INSERT INTO `type` (`id`, `categorie`) VALUES
(1, 'chien'),
(2, 'chat'),
(3, 'oiseau'),
(4, 'lapin');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ville` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `ville`, `photo`) VALUES
(1, 'fre@user.fr', '[\"ROLE_USER\"]', '$2y$13$G1rkd/a4E4K15otOLglGrOQjTV2rGKCAbH8/ciGMJHPvti9ksfTBC', 'fred', 'paris', NULL),
(4, 'fre@eleveur.fr', '[\"ROLE_ELEVEUR\"]', '$2y$13$icNoGo0XApPf7lkgBc1ED.aPPVcOwrEm5kTJ5En5/yq.GVNdx93kO', 'freleveur', 'lyon', NULL),
(5, 'titi@eleveur.fr', '[\"ROLE_ELEVEUR\"]', '$2y$13$VYp88wwEs.4wsv0XgBa22O7daciykmC/LmyeGUAobVxjAxH4Z4Mzq', 'titi', 'paris', NULL),
(6, 'toto@eleveur.fr', '[\"ROLE_ELEVEUR\"]', '$2y$13$e3Rgx6.DWhH./MwD49Us3e9v0JVdMtKsfzBtl0PI.vCb.Pro.zSuu', 'toto', 'lille', '/uploads/avatars/photo-by-damon-zaidmus-68a98c33413875.90858932.png'),
(7, 'toto@user.fr', '[\"ROLE_USER\"]', '$2y$13$b1oUnQ./znu8hXoL.BgiTedGOvWEbiJycMvOxnT.FKyUZ0RCT90o.', 'totouser', 'jura', '/uploads/avatars/photo-by-diana-simumpande-68a98d10f07175.47106430.png'),
(8, 'sylvainvalmyjr@gmail.fr', '[\"ROLE_ELEVEUR\"]', '$2y$13$Sk.0PymHOP0uOVjj7mOioeLhCWYBwKNAne5qDUX1c/UPoBf2giasG', 'Valmy', 'Chevilly-Larue', NULL),
(9, 'sylvain@gmail.com', '[\"ROLE_ELEVEUR\"]', '$2y$13$V.0KV835YnFOvoICccVs0.nNqGwNCAzLYcJaTh8qOFZJemkQZMBCW', 'sylvain', 'Chevilly-Larue', NULL),
(10, 'teste@eleveur.fr', '[\"ROLE_ELEVEUR\"]', '$2y$13$0/p3vqnkhZeCX7s4qKaNE.vpD5x95wuASL5sb/mXnBgV2b.GBExYW', 'teste', 'teste', NULL),
(11, 'test@user.fr', '[\"ROLE_USER\"]', '$2y$13$Xa/OXE.x0m0UtPrk7scseuYhoMLHE59233JKVHE9NDKQ00dAlOiSK', 'testeur', 'paris', NULL),
(12, 'teste@user.fr', '[\"ROLE_USER\"]', '$2y$13$P4Z44OfxL5RW5tFyHCCQVeHNYockV3TjEQGU2XZpazdeshIX8ifom', 'tes', 'tes', NULL);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `animal`
--
ALTER TABLE `animal`
  ADD CONSTRAINT `FK_6AAB231F489D1B5F` FOREIGN KEY (`eleveur_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_6AAB231FC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `type` (`id`);

--
-- Contraintes pour la table `demande_adoption`
--
ALTER TABLE `demande_adoption`
  ADD CONSTRAINT `FK_AB87FF6B8E962C16` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_AB87FF6BFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `friandise`
--
ALTER TABLE `friandise`
  ADD CONSTRAINT `FK_160949914795A786` FOREIGN KEY (`envoyeur_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_160949918E962C16` FOREIGN KEY (`animal_id`) REFERENCES `animal` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
