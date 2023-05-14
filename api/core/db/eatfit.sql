-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 14, 2023 at 04:45 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eatfit`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `delete_recipe`$$
$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `insert_unique_image_name`$$
$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `idCategory` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`idCategory`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`idCategory`, `name`, `image_path`) VALUES
(1, 'Petit déjeuner', '/default/breakfast.jpg'),
(2, 'Entrée', '/default/appetizer.jpg'),
(3, 'Collation', '/default/snack.jpg'),
(4, 'Déjeuner', '/default/lunch.jpg'),
(5, 'Dîner', '/default/dinner.jpg'),
(6, 'Dessert', '/default/dessert.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `consumed_recipes`
--

DROP TABLE IF EXISTS `consumed_recipes`;
CREATE TABLE IF NOT EXISTS `consumed_recipes` (
  `idConsumedRecipe` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `consumption_date` timestamp NOT NULL,
  `idUser` int UNSIGNED NOT NULL,
  `idRecipe` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idConsumedRecipe`),
  KEY `idUser` (`idUser`),
  KEY `idRecipe` (`idRecipe`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `food_types`
--

DROP TABLE IF EXISTS `food_types`;
CREATE TABLE IF NOT EXISTS `food_types` (
  `idFoodType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`idFoodType`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `food_types`
--

INSERT INTO `food_types` (`idFoodType`, `name`) VALUES
(1, 'Produits sucrés'),
(2, 'Protéines'),
(3, 'Produits laitiers'),
(4, 'Matières grasses'),
(5, 'Fruits et légumes'),
(6, 'Féculents'),
(7, 'Produits céréaliers et légumineuses'),
(9, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `idRating` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `score` int NOT NULL,
  `comment` text,
  `idUser` int UNSIGNED NOT NULL,
  `idRecipe` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idRating`),
  KEY `idUser` (`idUser`),
  KEY `idRecipe` (`idRecipe`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `idRecipe` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `preparation_time` int NOT NULL,
  `difficulty` enum('facile','moyen','difficile') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `instructions` text NOT NULL,
  `calories` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idUser` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idRecipe`),
  KEY `idUser` (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`idRecipe`, `title`, `preparation_time`, `difficulty`, `instructions`, `calories`, `created_at`, `idUser`) VALUES
(28, 'test', 3, 'moyen', 'sadasd', 3, '2023-05-14 13:33:11', 4),
(33, 'edfsdfdfs', 23, 'facile', 'adasd', 3, '2023-05-14 13:48:27', 4);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recipes_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `recipes_details`;
CREATE TABLE IF NOT EXISTS `recipes_details` (
`calories` int
,`categories` text
,`created_at` timestamp
,`creator_id` int unsigned
,`creator_username` varchar(255)
,`difficulty` enum('facile','moyen','difficile')
,`foodtypes_with_percentages` text
,`image_paths` text
,`preparation_time` int
,`recipe_id` int unsigned
,`recipe_instructions` text
,`recipe_title` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recipes_foodtypes`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `recipes_foodtypes`;
CREATE TABLE IF NOT EXISTS `recipes_foodtypes` (
`foodtypes_with_percentages` text
,`idRecipe` int unsigned
);

-- --------------------------------------------------------

--
-- Table structure for table `recipes_images`
--

DROP TABLE IF EXISTS `recipes_images`;
CREATE TABLE IF NOT EXISTS `recipes_images` (
  `idImage` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `idRecipe` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idImage`),
  KEY `idRecipe` (`idRecipe`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes_images`
--

INSERT INTO `recipes_images` (`idImage`, `path`, `idRecipe`) VALUES
(31, '/default/dessert.jpg', 28),
(34, '/default/lunch.jpg', 33);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_categories`
--

DROP TABLE IF EXISTS `recipe_categories`;
CREATE TABLE IF NOT EXISTS `recipe_categories` (
  `idRecipe` int UNSIGNED NOT NULL,
  `idCategory` int UNSIGNED NOT NULL,
  KEY `idRecipe` (`idRecipe`),
  KEY `idCategory` (`idCategory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipe_categories`
--

INSERT INTO `recipe_categories` (`idRecipe`, `idCategory`) VALUES
(28, 6),
(33, 4);

-- --------------------------------------------------------

--
-- Table structure for table `recipe_food_types`
--

DROP TABLE IF EXISTS `recipe_food_types`;
CREATE TABLE IF NOT EXISTS `recipe_food_types` (
  `idRecipe` int UNSIGNED NOT NULL,
  `idFoodType` int UNSIGNED NOT NULL,
  `percentage` decimal(11,0) NOT NULL,
  KEY `idRecipe` (`idRecipe`),
  KEY `idFoodType` (`idFoodType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipe_food_types`
--

INSERT INTO `recipe_food_types` (`idRecipe`, `idFoodType`, `percentage`) VALUES
(28, 1, '100'),
(33, 1, '100');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `idUser` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(400) DEFAULT NULL,
  `expiration` int DEFAULT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `username`, `email`, `password`, `token`, `expiration`) VALUES
(4, 'Lucas', 'test@gmail.com2', '$2y$10$C3b4OlhAzMTnTayJLaO.W.qQifGNKAqgRGOSq3FmW1CUBxg3.C0ti', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InRlc3RAZ21haWwuY29tMiIsInVzZXJuYW1lIjoiTHVjYXMiLCJleHAiOiIxNjg0MDg3NzU2In0.TN-BoXWQ4tC7jWRD1p9v_V4G3PVNu2-BHJfkb4fXBCk', 1684087756),
(5, 'test', 'test@gmail.com', '$2y$10$0KyIW582r/Z2RbLMEhYw2emmoy7nweNoir0sSJLfF/D7aieW1RbOW', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InRlc3RAZ21haWwuY29tIiwidXNlcm5hbWUiOiJ0ZXN0IiwiZXhwIjoxNjg0MDgzNjE0fQ.PW8fQ7Nmr6UtQyCaj1Nq69nKMIEfEBQdzFAgvspPKj4', 1684083614),
(6, 'Lucas Almeida Costa', 'tes22t@gmail.com', '$2y$10$CeITDZ1yEmcWM/Na1BwXfu2efmprKScWQFfjE2QKoMq0e7QLA/llW', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InRlczIydEBnbWFpbC5jb20iLCJ1c2VybmFtZSI6Ikx1Y2FzIEFsbWVpZGEgQ29zdGEiLCJleHAiOjE2ODQwODQ5ODJ9.t-3eudbKW4db3vNOwQQoSdHuiF5slPK4Y_14JkkhBGY', 1684084982);

-- --------------------------------------------------------

--
-- Structure for view `recipes_details`
--
DROP TABLE IF EXISTS `recipes_details`;

DROP VIEW IF EXISTS `recipes_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_details`  AS SELECT `r`.`idRecipe` AS `recipe_id`, `r`.`title` AS `recipe_title`, `r`.`instructions` AS `recipe_instructions`, `r`.`preparation_time` AS `preparation_time`, `r`.`difficulty` AS `difficulty`, `r`.`calories` AS `calories`, `r`.`created_at` AS `created_at`, `u`.`username` AS `creator_username`, `u`.`idUser` AS `creator_id`, group_concat(distinct `ri`.`path` separator ', ') AS `image_paths`, group_concat(distinct `c`.`name` separator ', ') AS `categories`, `rf`.`foodtypes_with_percentages` AS `foodtypes_with_percentages` FROM (((((`recipes` `r` left join `users` `u` on((`r`.`idUser` = `u`.`idUser`))) left join `recipes_images` `ri` on((`r`.`idRecipe` = `ri`.`idRecipe`))) left join `recipe_categories` `rc` on((`r`.`idRecipe` = `rc`.`idRecipe`))) left join `categories` `c` on((`rc`.`idCategory` = `c`.`idCategory`))) left join `recipes_foodtypes` `rf` on((`r`.`idRecipe` = `rf`.`idRecipe`))) GROUP BY `r`.`idRecipe``idRecipe`  ;

-- --------------------------------------------------------

--
-- Structure for view `recipes_foodtypes`
--
DROP TABLE IF EXISTS `recipes_foodtypes`;

DROP VIEW IF EXISTS `recipes_foodtypes`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_foodtypes`  AS SELECT `rft`.`idRecipe` AS `idRecipe`, group_concat(concat(`ft`.`name`,' (',`rft`.`percentage`,'%)') separator ', ') AS `foodtypes_with_percentages` FROM (`recipe_food_types` `rft` join `food_types` `ft` on((`rft`.`idFoodType` = `ft`.`idFoodType`))) GROUP BY `rft`.`idRecipe``idRecipe`  ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consumed_recipes`
--
ALTER TABLE `consumed_recipes`
  ADD CONSTRAINT `consumed_recipes_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `consumed_recipes_ibfk_3` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipes_images`
--
ALTER TABLE `recipes_images`
  ADD CONSTRAINT `recipes_images_ibfk_1` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipe_categories`
--
ALTER TABLE `recipe_categories`
  ADD CONSTRAINT `recipe_categories_ibfk_1` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_categories_ibfk_2` FOREIGN KEY (`idCategory`) REFERENCES `categories` (`idCategory`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipe_food_types`
--
ALTER TABLE `recipe_food_types`
  ADD CONSTRAINT `recipe_food_types_ibfk_1` FOREIGN KEY (`idFoodType`) REFERENCES `food_types` (`idFoodType`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_food_types_ibfk_2` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
