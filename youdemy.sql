-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2025 at 10:01 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `youdemy`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(5, 'Économie'),
(4, 'Géographie'),
(3, 'Informatique'),
(1, 'Mathématiques'),
(2, 'Physique');

-- --------------------------------------------------------

--
-- Table structure for table `cours`
--

CREATE TABLE `cours` (
  `id` int NOT NULL,
  `titre` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `contenu` text NOT NULL,
  `categorie_id` int DEFAULT NULL,
  `enseignant_id` int DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cours`
--

INSERT INTO `cours` (`id`, `titre`, `description`, `contenu`, `categorie_id`, `enseignant_id`, `date_creation`) VALUES
(1, 'Introduction à la Microéconomie', 'Les bases de l offre et de la demande, et les mécanismes de marché.', 'video', 5, 6, '2025-01-15 08:49:01'),
(2, 'Statistiques pour la Gestion', 'Méthodes statistiques appliquées à la gestion et l\'analyse de données.', 'video', 5, 6, '2025-01-15 08:49:01'),
(3, 'Programmation en Python', 'Initiation à la programmation avec Python pour les débutants.', 'video', 3, 7, '2025-01-15 08:49:01'),
(4, 'Cartographie et SIG', 'Utilisation des systèmes d\'information géographique pour la cartographie.', 'video', 3, 7, '2025-01-15 08:49:01'),
(5, 'Physique des Particules', 'Exploration des concepts fondamentaux de la physique des particules.', 'video', 2, 8, '2025-01-15 08:49:01'),
(6, 'Économie Internationale', 'Analyse des échanges commerciaux et des politiques économiques mondiales.', 'video', 5, 6, '2025-01-15 08:49:01'),
(7, 'Calcul Intégral et Différentiel', 'Principes avancés des mathématiques pour l ingénierie.', 'video', 1, 8, '2025-01-15 08:49:01'),
(8, 'Développement Web Full Stack', 'Introduction au développement dapplications web modernes.', 'video', 3, 7, '2025-01-15 08:49:01'),
(9, 'Géographie Urbaine', 'Étude des dynamiques des villes et des espaces urbains.', 'video', 4, 8, '2025-01-15 08:49:01'),
(10, 'Mécanique Quantique', 'Introduction aux concepts de la mécanique quantique moderne.', 'video', 1, 8, '2025-01-15 08:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `cours_tags`
--

CREATE TABLE `cours_tags` (
  `cours_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` int NOT NULL,
  `etudiant_id` int DEFAULT NULL,
  `cours_id` int DEFAULT NULL,
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `etudiant_id`, `cours_id`, `date_inscription`) VALUES
(1, 4, 6, '2025-01-15 14:38:04'),
(2, 4, 2, '2025-01-15 14:41:41'),
(3, 4, 1, '2025-01-16 08:35:44'),
(4, 9, 6, '2025-01-16 08:44:36'),
(5, 9, 2, '2025-01-16 08:52:43'),
(6, 9, 1, '2025-01-16 09:03:05'),
(7, 9, 4, '2025-01-16 09:07:38');

-- --------------------------------------------------------

--
-- Table structure for table `statistiques`
--

CREATE TABLE `statistiques` (
  `id` int NOT NULL,
  `nombre_total_cours` int DEFAULT '0',
  `nombre_total_utilisateurs` int DEFAULT '0',
  `date_calcul` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('etudiant','enseignant','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` enum('actif','inactif') NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `password`, `role`, `status`, `date_creation`) VALUES
(1, 'admin', 'admin@admin.com', '20252025', 'admin', 'actif', '2025-01-13 09:14:59'),
(4, 'fetti', 'fetti@gmail.com', '$2y$10$PFijC8lc2bD/rITZJ3/2huKIjJYc0erYL0XaSv8wrZiFDQTBvbo/C', 'etudiant', 'actif', '2025-01-14 10:12:41'),
(6, 'amine', 'amine@gmail.com', '$2y$10$RwBU2Q4QpVe6xlHxaVgbP.akWkDMBkWvPId8/HA.DBLCOT8l7VG3q', 'enseignant', 'actif', '2025-01-14 17:44:42'),
(7, 'rachida', 'rachida@gmail.com', '$2y$10$hqV6XdxdlAizPruDXgc9iuqe8DvnINfzWHZY9F1cy60DQDPXKmaDm', 'enseignant', 'actif', '2025-01-14 18:20:36'),
(8, 'omar', 'omar@gmail.com', '$2y$10$wLCkPEKwgVGdhGkjO.wvc.hzr7P4xAGfBwR97zgOVPDyy65yEFAX.', 'enseignant', 'actif', '2025-01-14 18:35:12'),
(9, 'ayoub', 'ayoub@gmailcom', '$2y$10$Y/RYiuxFz4JF5mCSAJI8fuiV6nIL6dfCgAxtQpbN0CZ8fUOt3W/kq', 'etudiant', 'actif', '2025-01-16 08:44:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enseignant_id` (`enseignant_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Indexes for table `cours_tags`
--
ALTER TABLE `cours_tags`
  ADD PRIMARY KEY (`cours_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etudiant_id` (`etudiant_id`),
  ADD KEY `cours_id` (`cours_id`);

--
-- Indexes for table `statistiques`
--
ALTER TABLE `statistiques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `statistiques`
--
ALTER TABLE `statistiques`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cours_ibfk_2` FOREIGN KEY (`enseignant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cours_ibfk_3` FOREIGN KEY (`enseignant_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `cours_ibfk_4` FOREIGN KEY (`enseignant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cours_ibfk_5` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cours_tags`
--
ALTER TABLE `cours_tags`
  ADD CONSTRAINT `cours_tags_ibfk_1` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cours_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`cours_id`) REFERENCES `cours` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
