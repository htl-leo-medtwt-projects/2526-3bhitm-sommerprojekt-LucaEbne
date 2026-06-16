-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db_server
-- Erstellungszeit: 16. Jun 2026 um 12:46
-- Server-Version: 9.4.0
-- PHP-Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `Sommerprojekt25/26`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `beaches`
--

CREATE TABLE `beaches` (
  `id` int NOT NULL,
  `island_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image_url` varchar(255) DEFAULT NULL,
  `sand_type` varchar(50) DEFAULT NULL,
  `water_color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `beaches`
--

INSERT INTO `beaches` (`id`, `island_id`, `name`, `description`, `image_url`, `sand_type`, `water_color`) VALUES
(1, 1, 'Navagio Beach', 'Zakynthos', './assets/img/beaches/beach-navagio.jpg', 'Vulkanisch', 'Türkis'),
(2, 3, 'Elafonissi', 'Crete', './assets/img/beaches/beach-elafonisi.jpg', 'Sand', 'Hellblau'),
(3, 2, 'Myrtos Beach', 'Kefalonia', './assets/img/beaches/beach-myrtos.jpg', 'Sand', 'Blau'),
(4, 1, 'Red Balos Lagoon', 'Crete', './assets/img/beaches/beach-balos.jpg', 'Sand', 'Blau');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `post_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES
(1, 10, 12, 'Sieht krank aus 😍', '2026-04-08 06:36:42'),
(2, 1, 3, 'Will auch dahin!', '2026-04-08 06:36:42'),
(3, 2, 1, 'Party Level 100 😂', '2026-04-08 06:36:42'),
(5, 10, 14, 'wow das schaut cool aus', '2026-06-02 07:02:22'),
(6, 10, 14, 'test', '2026-06-02 07:05:29'),
(7, 10, 12, 'wow', '2026-06-14 12:39:43');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favorite_islands`
--

CREATE TABLE `favorite_islands` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `island_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `favorite_islands`
--

INSERT INTO `favorite_islands` (`id`, `user_id`, `island_id`, `created_at`) VALUES
(12, 12, 1, '2026-06-15 06:11:48'),
(14, 14, 2, '2026-06-15 18:40:49');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `islands`
--

CREATE TABLE `islands` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `full-description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Greece'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `islands`
--

INSERT INTO `islands` (`id`, `name`, `description`, `full-description`, `image_url`, `country`) VALUES
(1, 'Santorini', 'Iconic sunsets, blue domes and dramatic caldera views.', 'Santorini is a volcanic island in the Cyclades group of the Greek islands. It is famous for its dramatic views, stunning sunsets from Oia, the white-washed houses, and its very own active volcano. Fira, the capital, clings to the rim of the caldera and offers panoramic views of the sea and volcanic islets. The island\'s unique geology has produced distinctive black, red, and white sand beaches.', './assets/img/islands/island-santorini.jpg\r\n', 'Greece'),
(2, 'Mykonos', 'Vibrant nightlife, windmills and crystal-clear beaches.', 'Mykonos is a vibrant island in the Cyclades group of the Greek islands. It is famous for its lively nightlife, iconic windmills, and charming white-washed houses set against the deep blue sea. Mykonos Town (Chora) is a maze of narrow streets filled with boutiques, cafes, and restaurants. The island also offers beautiful sandy beaches and a cosmopolitan atmosphere that attracts visitors from around the world.', './assets/img/islands/island-mykonos.jpg', 'Greece'),
(3, 'Crete', 'Ancient ruins, mountain gorges and legendary cuisine.', 'Crete is the largest of the Greek islands and lies at the crossroads of Europe, Asia, and Africa. It is known for its diverse landscapes, from rugged mountains to fertile plains and stunning beaches. Heraklion, the capital, is home to the ancient Palace of Knossos, a key site of the Minoan civilization. The island’s rich history, traditional villages, and renowned cuisine make it a destination of great cultural and natural variety.', './assets/img/islands/island-crete.jpg', 'Greece'),
(4, 'Rhodes', 'Medieval old town, ancient wonders and golden coastlines.', 'Rhodes is one of the largest islands in the Dodecanese group of the Greek islands. It is famous for its well-preserved medieval Old Town, a UNESCO World Heritage Site, and its sunny climate. The city of Rhodes features impressive fortifications built by the Knights of St. John. Beyond the historic sites, the island offers beautiful beaches, ancient ruins, and scenic villages surrounded by lush landscapes.', './assets/img/islands/island-rhodes.jpg', 'Greece');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `posts`
--

CREATE TABLE `posts` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `island_id` int DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `rating_food` tinyint DEFAULT NULL,
  `rating_beaches` tinyint DEFAULT NULL,
  `rating_nightlife` tinyint DEFAULT NULL,
  `rating_atmosphere` tinyint DEFAULT NULL,
  `food_highlights` text,
  `trip_highlights` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `island_id`, `title`, `content`, `image_url`, `rating`, `rating_food`, `rating_beaches`, `rating_nightlife`, `rating_atmosphere`, `food_highlights`, `trip_highlights`, `created_at`) VALUES
(1, 1, 1, 'Santorini Reise', 'Mega schöne Insel mit krassen Views!', './assets/img/islands/island-santorini.jpg\n', 5, 1, 5, 5, 5, 'Gyros, Seafood', 'Sunset in Oia', '2026-04-08 06:36:42'),
(2, 2, 2, 'Mykonos Party Trip', 'Heftigste Partys ever!', './assets/img/islands/island-mykonos.jpg\n', 4, 4, 4, 4, 4, 'Cocktails, Streetfood', 'Beach Clubs', '2026-04-08 06:36:42'),
(8, 12, 2, 'test', 'hdhdijajidj', '../../assets/uploads/cover_6a169c4f34067.png', 4, 4, 4, 4, 4, '', '', '2026-05-27 07:25:03'),
(9, 12, 1, 'hello', 'hqhjoqwrhjfbjkl', '../../assets/uploads/cover_6a17fe5241ebb.png', 4, 4, 4, 4, 4, '', '', '2026-05-28 08:35:30'),
(10, 12, 1, 'Santi-Test', 'noeu83804fijdv', '../../assets/uploads/cover_6a1d9ec9ac337.png', 4, 4, 4, 4, 4, 'Gyros, Skoli', 'Sunset in Ohio, Beach trip', '2026-06-01 15:01:29'),
(24, 14, 3, 'Sunset Trip', 'Beautiful beach', '../../assets/uploads/cover_6a30470ae753c.jpg', 4, 3, 5, 4, 5, 'Gyros, Skoli', 'Sunset in Ohio, Beach trip', '2026-06-15 18:40:10');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `post_images`
--

CREATE TABLE `post_images` (
  `id` int NOT NULL,
  `post_id` int DEFAULT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `post_images`
--

INSERT INTO `post_images` (`id`, `post_id`, `image_url`) VALUES
(1, 1, 'santorini1.jpg'),
(2, 1, 'santorini2.jpg'),
(3, 2, 'mykonos1.jpg'),
(4, 2, 'mykonos2.jpg'),
(12, 10, '../../assets/uploads/photo_6a1d9ec9b4b0a.png'),
(13, 10, '../../assets/uploads/photo_6a1d9ec9be1c4.jpg'),
(14, 10, '../../assets/uploads/photo_6a1d9ec9c5dda.jpg'),
(15, 10, '../../assets/uploads/photo_6a1d9ec9c5dda.jpg'),
(16, 10, '../../assets/uploads/photo_6a1d9ec9be1c4.jpg'),
(39, 24, '../../assets/uploads/photo_6a30470aeda9d.jpg'),
(40, 24, '../../assets/uploads/photo_6a30470af22f3.jpg'),
(41, 24, '../../assets/uploads/photo_6a30470b02b40.jpg'),
(42, 24, '../../assets/uploads/photo_6a30470b093d0.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bio` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile_picture`, `created_at`, `bio`) VALUES
(1, 'luca', 'luca@example.com', '123456', NULL, '2026-04-08 06:36:42', ''),
(2, 'anna', 'anna@example.com', '123456', NULL, '2026-04-08 06:36:42', ''),
(3, 'max', 'max@example.com', '123456', NULL, '2026-04-08 06:36:42', ''),
(4, 'Manuel', 'test@gmail.com', '$2y$10$UkZMNcQlKcDRJ0LQ5xUBR./avX1aGxK12y27T4b9Q3IDctyQmAiVa', NULL, '2026-05-04 17:40:59', ''),
(7, 'yur', 'test2@gmail.com', '$2y$10$IKR4eest4ATnyaJaRWNLbOF8uy22HQv2Y3QGf/t3Crw7Sm6FAC3xO', NULL, '2026-05-04 17:44:12', ''),
(8, 'test', 'testtest@gmail.com', '$2y$10$wlmPUfYcbZKwgBpIno64Qezy4gH8FSogfasDm9qp5gktPgrwg3fYu', '../../assets/uploads/food-souvlaki.jpg', '2026-05-05 06:11:43', ''),
(9, 'Test2', 'test3@gmail.com', '$2y$10$2EKDd0N8dMbyQxS6Lxb3Zut8Bg4ST4IcOekVZ0bxSzu6uYx7x7kDC', '../../assets/uploads/food-souvlaki.jpg', '2026-05-06 07:01:31', ''),
(11, 'Luca1', 'luca@gmail.com', '$2y$10$xdzqd6pRfPWdzIVUiT1SfeKYQ4ER7WCeUIHmA3A4G/vYcq8T.H76e', '../../assets/uploads/food-gyros.jpg', '2026-05-06 07:10:05', ''),
(12, 'Gänse12', 'g@gmail.com', '$2y$10$Ko.VT8bkLMK0qOhYsR4wJ.3MRiE5MpptGw/ZiOUzsaLKzGIYek4be', '../../assets/img/default-profile-img.png', '2026-05-13 10:03:06', 'hello'),
(14, 'Admin', 'admin@gmail.com', '$2y$10$VXOalBdq1o7VNaWZF2KM3.HWLISxBZvUFB.sYHnmuY4lMMNqWJ13u', '../../assets/uploads/task8_1603365958159.png', '2026-06-01 18:49:01', 'Hello'),
(17, 'Test6', 'test10@gmail.com', '$2y$10$woHs.TBqduYV4g8CbIA8DeetVkDZPHMiZrffdJF3oc7JdzKJLzl8e', '../../assets/uploads/task8_1603365958159.png', '2026-06-15 11:07:37', '');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `beaches`
--
ALTER TABLE `beaches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `island_id` (`island_id`);

--
-- Indizes für die Tabelle `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indizes für die Tabelle `favorite_islands`
--
ALTER TABLE `favorite_islands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`island_id`),
  ADD KEY `island_id` (`island_id`);

--
-- Indizes für die Tabelle `islands`
--
ALTER TABLE `islands`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `island_id` (`island_id`);

--
-- Indizes für die Tabelle `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `beaches`
--
ALTER TABLE `beaches`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT für Tabelle `favorite_islands`
--
ALTER TABLE `favorite_islands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT für Tabelle `islands`
--
ALTER TABLE `islands`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT für Tabelle `post_images`
--
ALTER TABLE `post_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `beaches`
--
ALTER TABLE `beaches`
  ADD CONSTRAINT `beaches_ibfk_1` FOREIGN KEY (`island_id`) REFERENCES `islands` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `favorite_islands`
--
ALTER TABLE `favorite_islands`
  ADD CONSTRAINT `favorite_islands_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_islands_ibfk_2` FOREIGN KEY (`island_id`) REFERENCES `islands` (`id`) ON DELETE CASCADE;

--
-- Constraints der Tabelle `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`island_id`) REFERENCES `islands` (`id`) ON DELETE SET NULL;

--
-- Constraints der Tabelle `post_images`
--
ALTER TABLE `post_images`
  ADD CONSTRAINT `post_images_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
