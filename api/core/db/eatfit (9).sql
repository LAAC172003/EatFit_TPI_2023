-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 15, 2023 at 01:20 PM
-- Server version: 8.0.31
-- PHP Version: 8.2.0

DROP DATABASE IF EXISTS eatfit;
DROP USER IF EXISTS 'eatfit'@'localhost';
CREATE USER 'eatfit'@'localhost' IDENTIFIED BY 'EatFit';
GRANT ALL PRIVILEGES ON eatfit.* TO 'eatfit'@'localhost';
CREATE DATABASE IF NOT EXISTS `eatfit` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `eatfit`;

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
CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_recipe` (IN `p_id_recipe` INT(11))   BEGIN
    SELECT
        CONCAT(idImage, '_', path)
    FROM
        recipes_images
    WHERE
        idRecipe = p_id_recipe ;
    DELETE
FROM
    recipes
WHERE
    idRecipe = p_id_recipe ; END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `insert_unique_image_name`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `insert_unique_image_name` (`path` VARCHAR(255), `id_recipe` INT) RETURNS VARCHAR(255) CHARSET utf8mb4  BEGIN
    DECLARE
        unique_image_name VARCHAR(255);
    INSERT INTO recipes_images(path, idRecipe)
VALUES(path, id_recipe);
    RETURN CONCAT(LAST_INSERT_ID(), '_', path);
END$$

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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `food_types`
--

DROP TABLE IF EXISTS `food_types`;
CREATE TABLE IF NOT EXISTS `food_types` (
  `idFoodType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`idFoodType`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;

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
(7, 'Produits céréaliers et légumineuses');

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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`idRecipe`, `title`, `preparation_time`, `difficulty`, `instructions`, `calories`, `created_at`, `idUser`) VALUES
(37, 'Pancakes aux myrtilles', 30, 'moyen', 'Dans un saladier, mélangez la farine, le sucre, la levure et le sel. Dans un autre récipient, battez les œufs, le lait et le beurre fondu. Incorporez les ingrédients secs aux ingrédients humides. Ajoutez les myrtilles. Faites chauffer une poêle légèrement huilée. Versez une louche de pâte pour chaque pancake. Retournez quand des bulles se forment à la surface. Servez chaud avec du sirop d\'érable.', 250, '2023-05-15 11:01:26', 7),
(38, 'Salade César au poulet grillé', 25, 'facile', 'Faites griller le poulet et coupez-le en tranches. Lavez la laitue et séchez-la. Dans un grand saladier, mélangez la laitue, le poulet, des croûtons et du parmesan râpé. Arrosez de sauce César et mélangez bien. Servez immédiatement.', 350, '2023-05-15 11:01:52', 7),
(39, 'Pâtes à la Carbonara', 20, 'facile', 'Faites cuire les pâtes selon les instructions sur l\'emballage. Pendant ce temps, faites revenir les lardons dans une poêle jusqu\'à ce qu\'ils soient croustillants. Dans un bol, battez les œufs, ajoutez le parmesan râpé et poivrez. Égouttez les pâtes, puis mélangez-les avec les lardons. Hors du feu, ajoutez le mélange d\'œufs et remuez rapidement pour obtenir une sauce crémeuse. Servez immédiatement.', 450, '2023-05-15 11:03:43', 7),
(40, 'Pizza Margherita', 30, 'facile', 'Préchauffez le four à 220°C. Étalez la pâte à pizza sur une plaque de cuisson. Étalez une couche de sauce tomate sur la pâte. Disposez les tranches de mozzarella sur la sauce tomate. Ajoutez quelques feuilles de basilic. Enfournez pendant environ 15 minutes ou jusqu\'à ce que la croûte soit dorée et le fromage fondu. Servez chaud.', 300, '2023-05-15 11:03:55', 7),
(41, 'Smoothie aux fruits rouges', 10, 'facile', 'Dans un blender, ajoutez les fruits rouges congelés, le yaourt nature, le lait et le miel. Mixez jusqu\'à obtention d\'une consistance lisse. Versez le smoothie dans des verres et servez frais.', 200, '2023-05-15 11:04:02', 7),
(42, 'Salade de quinoa aux légumes', 30, 'facile', 'Faites cuire le quinoa selon les instructions sur l\'emballage. Laissez refroidir. Dans un saladier, mélangez le quinoa refroidi, les légumes coupés en dés, les herbes fraîches hachées et les graines de votre choix. Assaisonnez avec du jus de citron, de l\'huile d\'olive, du sel et du poivre. Mélangez bien et servez frais.', 350, '2023-05-15 11:04:30', 7),
(43, 'Sauté de poulet aux légumes', 25, 'facile', 'Coupez le poulet en dés et faites-le sauter dans une poêle avec un peu d\'huile d\'olive jusqu\'à ce qu\'il soit bien cuit. Ajoutez les légumes coupés en julienne (comme les carottes, les poivrons et les courgettes) et faites-les sauter pendant quelques minutes. Assaisonnez avec du sel, du poivre, de l\'ail en poudre et du gingembre en poudre. Servez chaud.', 400, '2023-05-15 11:04:36', 7),
(44, 'Tarte aux pommes', 40, 'moyen', 'Préchauffez le four à 180°C. Étalez la pâte brisée dans un moule à tarte. Disposez les tranches de pommes sur la pâte en les superposant légèrement. Saupoudrez de sucre et de cannelle. Enfournez pendant environ 30 minutes ou jusqu\'à ce que la pâte soit dorée et les pommes tendres. Servez tiède ou froid.', 250, '2023-05-15 11:04:53', 7),
(45, 'Omelette aux légumes', 15, 'facile', 'Battez les œufs dans un bol. Dans une poêle antiadhésive, faites chauffer un peu d\'huile d\'olive. Ajoutez les légumes de votre choix, tels que des poivrons, des champignons et des épinards, et faites-les sauter pendant quelques minutes. Versez les œufs battus sur les légumes et faites cuire l\'omelette jusqu\'à ce qu\'elle soit prise. Repliez-la en deux et servez chaud.', 300, '2023-05-15 11:05:22', 7),
(46, 'Soupe à la tomate', 35, 'facile', 'Dans une casserole, faites revenir des oignons et de l\'ail hachés dans de l\'huile d\'olive. Ajoutez des tomates concassées en conserve, du bouillon de légumes, du basilic frais haché et du sel. Laissez mijoter pendant environ 20 minutes. Mixez la soupe jusqu\'à obtenir une consistance lisse. Réchauffez-la avant de servir.', 150, '2023-05-15 11:05:50', 7),
(47, 'Smoothie vert détox', 10, 'facile', 'Dans un blender, mixez une poignée d\'épinards frais, un concombre pelé et coupé en morceaux, un kiwi pelé, une banane et un peu d\'eau. Ajoutez du jus de citron et du miel selon vos goûts. Mixez jusqu\'à obtenir une consistance lisse. Versez le smoothie dans des verres et servez frais.', 200, '2023-05-15 11:06:06', 7);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recipes_details`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `recipes_details`;
CREATE TABLE IF NOT EXISTS `recipes_details` (
`recipe_id` int unsigned
,`recipe_title` varchar(255)
,`recipe_instructions` text
,`preparation_time` int
,`difficulty` enum('facile','moyen','difficile')
,`calories` int
,`created_at` timestamp
,`creator_username` varchar(255)
,`creator_id` int unsigned
,`image_paths` text
,`categories` text
,`foodtypes_with_percentages` text
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recipes_foodtypes`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `recipes_foodtypes`;
CREATE TABLE IF NOT EXISTS `recipes_foodtypes` (
`idRecipe` int unsigned
,`foodtypes_with_percentages` text
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes_images`
--

INSERT INTO `recipes_images` (`idImage`, `path`, `idRecipe`) VALUES
(37, '/default/breakfast.jpg', 37),
(38, '/default/lunch.jpg', 38),
(39, '/default/lunch.jpg', 39),
(40, '/default/dinner.jpg', 40),
(41, '/default/snack.jpg', 41),
(42, '/default/lunch.jpg', 42),
(43, '/default/dinner.jpg', 43),
(44, '/default/dessert.jpg', 44),
(45, '/default/lunch.jpg', 45),
(46, '/default/dinner.jpg', 46),
(47, '/default/snack.jpg', 47);

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
(37, 1),
(38, 4),
(39, 4),
(40, 5),
(41, 3),
(42, 4),
(43, 5),
(44, 6),
(45, 4),
(46, 5),
(47, 3);

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
(37, 1, '30'),
(37, 2, '15'),
(37, 4, '20'),
(37, 5, '15'),
(37, 6, '20'),
(38, 1, '0'),
(38, 2, '30'),
(38, 4, '30'),
(38, 5, '20'),
(38, 6, '0'),
(38, 7, '20'),
(39, 1, '0'),
(39, 2, '20'),
(39, 4, '30'),
(39, 5, '10'),
(39, 6, '40'),
(39, 7, '0'),
(40, 1, '0'),
(40, 2, '10'),
(40, 4, '20'),
(40, 5, '10'),
(40, 6, '40'),
(40, 7, '20'),
(41, 1, '40'),
(41, 2, '10'),
(41, 4, '10'),
(41, 5, '30'),
(41, 6, '0'),
(41, 7, '10'),
(42, 1, '0'),
(42, 2, '10'),
(42, 4, '10'),
(42, 5, '40'),
(42, 6, '20'),
(42, 7, '20'),
(43, 1, '0'),
(43, 2, '30'),
(43, 4, '20'),
(43, 5, '40'),
(43, 6, '0'),
(43, 7, '10'),
(44, 1, '40'),
(44, 2, '0'),
(44, 4, '20'),
(44, 5, '20'),
(44, 6, '20'),
(44, 7, '0'),
(45, 1, '0'),
(45, 2, '20'),
(45, 4, '20'),
(45, 5, '40'),
(45, 6, '0'),
(45, 7, '20'),
(46, 1, '0'),
(46, 2, '30'),
(46, 4, '10'),
(46, 5, '40'),
(46, 6, '10'),
(46, 7, '10'),
(47, 1, '10'),
(47, 2, '0'),
(47, 4, '40'),
(47, 5, '40'),
(47, 6, '0'),
(47, 7, '10');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `username`, `email`, `password`, `token`, `expiration`) VALUES
(7, 'Example', 'example@cfpt.ch', '$2y$10$s0AaZ.bai596MKSYBen72uEeDCyYygyyalyAJlwbsTx5YLRORXdpe', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImV4YW1wbGVAY2ZwdC5jaCIsInVzZXJuYW1lIjoiRXhhbXBsZSIsImV4cCI6MTY4NDE2MjAwMX0.99y87W8g7EnQivYmQeuBkJpLrCF1djJXwAkv7CVVm6c', 1684162001);

-- --------------------------------------------------------

--
-- Structure for view `recipes_details`
--
DROP TABLE IF EXISTS `recipes_details`;

DROP VIEW IF EXISTS `recipes_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_details`  AS SELECT `r`.`idRecipe` AS `recipe_id`, `r`.`title` AS `recipe_title`, `r`.`instructions` AS `recipe_instructions`, `r`.`preparation_time` AS `preparation_time`, `r`.`difficulty` AS `difficulty`, `r`.`calories` AS `calories`, `r`.`created_at` AS `created_at`, `u`.`username` AS `creator_username`, `u`.`idUser` AS `creator_id`, group_concat(distinct `ri`.`path` separator ', ') AS `image_paths`, group_concat(distinct `c`.`name` separator ', ') AS `categories`, `rf`.`foodtypes_with_percentages` AS `foodtypes_with_percentages` FROM (((((`recipes` `r` left join `users` `u` on((`r`.`idUser` = `u`.`idUser`))) left join `recipes_images` `ri` on((`r`.`idRecipe` = `ri`.`idRecipe`))) left join `recipe_categories` `rc` on((`r`.`idRecipe` = `rc`.`idRecipe`))) left join `categories` `c` on((`rc`.`idCategory` = `c`.`idCategory`))) left join `recipes_foodtypes` `rf` on((`r`.`idRecipe` = `rf`.`idRecipe`))) GROUP BY `r`.`idRecipe`  ;

-- --------------------------------------------------------

--
-- Structure for view `recipes_foodtypes`
--
DROP TABLE IF EXISTS `recipes_foodtypes`;

DROP VIEW IF EXISTS `recipes_foodtypes`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_foodtypes`  AS SELECT `rft`.`idRecipe` AS `idRecipe`, group_concat(concat(`ft`.`name`,' (',`rft`.`percentage`,'%)') separator ', ') AS `foodtypes_with_percentages` FROM (`recipe_food_types` `rft` join `food_types` `ft` on((`rft`.`idFoodType` = `ft`.`idFoodType`))) GROUP BY `rft`.`idRecipe`  ;

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
