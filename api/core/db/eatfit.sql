-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 05, 2023 at 02:43 PM
-- Server version: 8.0.31
-- PHP Version: 8.0.26
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
DROP PROCEDURE IF EXISTS `DeleteImages`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteImages` (IN `p_id_recipe` INT)   BEGIN
    -- Select and concatenate image id and path before deleting
SELECT CONCAT(idImage, '_', path) AS ImageID_Path
FROM recipes_images
WHERE idRecipe = p_id_recipe;

-- Now delete the images for the given recipe
DELETE FROM recipes_images
WHERE idRecipe = p_id_recipe;
END$$

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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `food_types`
--

DROP TABLE IF EXISTS `food_types`;
CREATE TABLE IF NOT EXISTS `food_types` (
  `idFoodType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`idFoodType`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`idRecipe`, `title`, `preparation_time`, `difficulty`, `instructions`, `calories`, `created_at`, `idUser`) VALUES
(1, 'Salade de quinoa aux légumes grillés', 30, 'facile', 'Commencez par préchauffer votre grill à feu moyen. Préparez les légumes en les coupant en lanières, rondelles ou dés, selon les indications pour chaque légume. Dans un bol, mélangez les légumes préparés avec 2 cuillères à soupe d\'huile d\'olive, du sel et du poivre. Veillez à bien enrober les légumes d\'huile et d\'assaisonnement. Placez les légumes sur la grille du grill préchauffé et laissez-les griller pendant environ 10 minutes, en les retournant régulièrement. Les légumes doivent devenir tendres et légèrement dorés, sans les brûler. Pendant ce temps, réchauffez 1 tasse de quinoa cuit selon les instructions sur l\'emballage. Une fois les légumes grillés prêts, retirez-les du grill et laissez-les refroidir légèrement. Dans un grand bol, mélangez le quinoa cuit avec les légumes grillés. Assaisonnez avec du sel et du poivre selon votre goût. Ajoutez des herbes fraîches comme de la coriandre ou du persil haché pour plus de saveur, si vous le souhaitez. Servez la salade de quinoa aux légumes grillés tiède ou à température ambiante.', 350, '2023-06-05 09:09:41', 1),
(3, 'Smoothie aux fruits tropicaux', 10, 'facile', 'Dans un mixeur, ajoutez 1 banane, 1 tasse d\'ananas frais coupé en morceaux, 1/2 tasse de mangue coupée en dés, 1/2 tasse de lait de coco et quelques glaçons. Mixez jusqu\'à obtenir une consistance lisse. Servez frais dans des verres. Vous pouvez décorer avec des tranches de fruits tropicaux si vous le souhaitez.', 200, '2023-06-05 09:11:11', 1),
(4, 'Salade de poulet grillé', 25, 'moyen', 'Assaisonnez 2 poitrines de poulet avec du sel, du poivre, du paprika et du jus de citron. Faites griller le poulet sur un barbecue chaud pendant environ 15-20 minutes, jusqu\'à ce qu\'il soit bien cuit. Laissez refroidir légèrement, puis coupez-le en tranches. Dans un grand bol, mélangez de la laitue, des tomates cerises coupées en deux, des concombres en tranches, des tranches d\'avocat et les tranches de poulet grillé. Ajoutez une vinaigrette de votre choix et mélangez bien. Servez immédiatement.', 400, '2023-06-05 09:11:26', 1),
(5, 'Tarte aux pommes', 45, 'difficile', 'Préparez la pâte en mélangeant 2 tasses de farine, 1/2 cuillère à café de sel et 1/2 tasse de beurre froid coupé en petits morceaux. Ajoutez de l\'eau froide petit à petit jusqu\'à obtenir une pâte homogène. Réfrigérez la pâte pendant 30 minutes. Préchauffez le four à 200°C. Épluchez et coupez en tranches 4 pommes. Étalez la pâte dans un moule à tarte, puis disposez les tranches de pommes sur la pâte. Saupoudrez de sucre et de cannelle selon votre goût. Cuisez la tarte au four pendant environ 30 minutes, jusqu\'à ce que la pâte soit dorée et les pommes tendres. Laissez refroidir avant de servir.', 300, '2023-06-05 09:11:56', 1),
(6, 'Wrap au poulet et aux légumes', 20, 'facile', 'Dans un bol, mélangez 1 tasse de poulet cuit et coupé en dés, 1/2 tasse de poivrons colorés en dés, 1/4 tasse de carottes râpées et 1/4 tasse de laitue hachée. Ajoutez 2 cuillères à soupe de mayonnaise légère et mélangez bien. Réchauffez une tortilla de blé entier au micro-ondes pendant quelques secondes. Étalez la préparation au poulet et aux légumes sur la tortilla et roulez-la fermement. Coupez le wrap en deux et servez.', 350, '2023-06-05 09:12:15', 1),
(7, 'Spaghetti à la bolognaise', 40, 'moyen', 'Faites chauffer de l\'huile d\'olive dans une grande poêle. Ajoutez 1 oignon et 2 gousses d\'ail hachés, et faites revenir jusqu\'à ce qu\'ils soient dorés. Ajoutez 500 g de viande hachée et faites-la cuire jusqu\'à ce qu\'elle soit bien brune. Ajoutez 1 boîte de tomates concassées, 2 cuillères à soupe de concentré de tomates, 1 cuillère à soupe d\'origan séché, du sel et du poivre. Laissez mijoter pendant 20 minutes. Pendant ce temps, faites cuire les spaghetti selon les instructions sur l\'emballage. Égouttez les spaghetti et mélangez-les avec la sauce bolognaise. Servez chaud avec du parmesan râpé.', 500, '2023-06-05 09:12:27', 1),
(8, 'Smoothie vert détox', 10, 'facile', 'Dans un mixeur, ajoutez 2 poignées d\'épinards frais, 1 banane, 1/2 concombre, 1 branche de céleri, le jus d\'un citron vert et 1 tasse d\'eau de coco. Mixez jusqu\'à obtenir une consistance lisse. Ajoutez quelques glaçons et mixez à nouveau. Versez le smoothie dans des verres et dégustez immédiatement.', 150, '2023-06-05 09:12:45', 1),
(9, 'Pâtes à la carbonara', 25, 'moyen', 'Faites cuire 200 g de spaghetti dans une casserole d\'eau bouillante salée selon les instructions sur l\'emballage. Pendant ce temps, dans une poêle, faites revenir 100 g de lardons jusqu\'à ce qu\'ils soient dorés. Dans un bol, battez 2 jaunes d\'œufs avec 50 g de parmesan râpé. Égouttez les pâtes cuites et réservez une petite quantité d\'eau de cuisson. Versez les pâtes chaudes dans la poêle avec les lardons et mélangez bien. Ajoutez progressivement le mélange d\'œufs et de parmesan, en remuant constamment pour éviter la formation de grumeaux. Si nécessaire, ajoutez un peu d\'eau de cuisson des pâtes pour obtenir une consistance crémeuse. Servez les pâtes à la carbonara immédiatement avec du parmesan supplémentaire.', 500, '2023-06-05 09:13:30', 1),
(10, 'Smoothie bowl aux baies', 10, 'facile', 'Dans un mixeur, combinez 1 tasse de baies mélangées (fraises, framboises, myrtilles), 1 banane, 1/2 tasse de yaourt grec nature, 1 cuillère à soupe de miel et 1/4 de tasse de lait d\'amande. Mixez jusqu\'à obtenir une consistance lisse et crémeuse. Versez le smoothie dans un bol. Garnissez-le de tranches de banane, de baies fraîches, de noix de coco râpée et de graines de chia. Dégustez à la cuillère.', 300, '2023-06-05 09:13:35', 1),
(11, 'Chili végétarien', 40, 'moyen', 'Dans une grande casserole, chauffez de l\'huile d\'olive. Ajoutez 1 oignon haché, 2 gousses d\'ail émincées, 1 poivron coupé en dés et faites revenir pendant quelques minutes. Ajoutez 1 boîte de haricots rouges, 1 boîte de haricots noirs, 1 boîte de maïs, 1 boîte de tomates concassées, 2 cuillères à soupe de poudre de chili, 1 cuillère à soupe de cumin, du sel et du poivre. Laissez mijoter pendant environ 30 minutes. Servez le chili végétarien chaud avec du riz, de la coriandre fraîche et des quartiers de citron vert.', 350, '2023-06-05 09:14:51', 2);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recipes_details_view`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `recipes_details_view`;
CREATE TABLE IF NOT EXISTS `recipes_details_view` (
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
,`average_rating` decimal(14,4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `recipes_foodtypes_view`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `recipes_foodtypes_view`;
CREATE TABLE IF NOT EXISTS `recipes_foodtypes_view` (
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes_images`
--

INSERT INTO `recipes_images` (`idImage`, `path`, `idRecipe`) VALUES
(1, '/default/lunch.jpg', 1),
(4, '/default/dessert.jpg', 5),
(10, '/default/dinner.jpg', 11),
(11, '247527.jpg', 10),
(12, 'téléchargement.jpg', 3),
(13, 'Le-smoothie-vert-a-tout-bon.jpg', 8),
(14, 'Wraps-de-poulet-et-legumes.jpg', 6),
(15, 'Lemon-Herb-Mediterranean-Chicken-Salad-208-957x675.jpg', 4),
(16, 'spaghetti-carbonara-2560x1920.jpg', 9),
(18, 'téléchargement (1).jpg', 7);

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
(1, 4),
(3, 1),
(4, 4),
(5, 6),
(6, 4),
(7, 5),
(8, 3),
(9, 5),
(10, 1),
(11, 5);

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
(1, 7, '60'),
(1, 5, '15'),
(1, 4, '15'),
(1, 2, '10'),
(5, 5, '40'),
(5, 1, '30'),
(5, 4, '20'),
(5, 7, '10'),
(11, 2, '20'),
(11, 4, '10'),
(11, 5, '40'),
(11, 7, '30'),
(10, 1, '20'),
(10, 2, '10'),
(10, 3, '5'),
(10, 4, '5'),
(10, 5, '60'),
(3, 3, '20'),
(3, 5, '80'),
(8, 1, '10'),
(8, 2, '5'),
(8, 3, '5'),
(8, 4, '5'),
(8, 5, '70'),
(8, 7, '5'),
(6, 2, '40'),
(6, 4, '20'),
(6, 5, '30'),
(6, 7, '10'),
(4, 2, '40'),
(4, 4, '20'),
(4, 5, '30'),
(4, 7, '10'),
(9, 2, '30'),
(9, 3, '5'),
(9, 4, '25'),
(9, 7, '40'),
(7, 2, '30'),
(7, 3, '5'),
(7, 4, '25'),
(7, 5, '20'),
(7, 7, '20');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `username`, `email`, `password`, `token`, `expiration`) VALUES
(1, 'Lucas', 'lucas@cfpt.ch', '$2y$10$CwaR1LC30.JLP3cmyHnp9uaBIGXV940EkTEqYeh.3nB5Dz0VFNA7O', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6Imx1Y2FzQGNmcHQuY2giLCJ1c2VybmFtZSI6Ikx1Y2FzIiwiZXhwIjoxNjg1OTgxNTE1fQ.aQksnQLWnQV7q0DPeatCI2mj5Y-cUcjorvCoMuybfIg', 1685981515),
(2, 'Jean', 'jean@cfpt.ch', '$2y$10$MdH9I55Is3xCcfbQ3kkmm./QeyWxb3CMLFErFr.GxtwQnf7aFNjJi', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImplYW5AY2ZwdC5jaCIsInVzZXJuYW1lIjoiSmVhbiIsImV4cCI6MTY4NTk2OTk3MX0.FB-pyXiWKI9XAsfDMqC3UFULSBTptYwBVEwIu1Je--M', 1685969971);

-- --------------------------------------------------------

--
-- Structure for view `recipes_details_view`
--
DROP TABLE IF EXISTS `recipes_details_view`;

DROP VIEW IF EXISTS `recipes_details_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_details_view`  AS SELECT `r`.`idRecipe` AS `recipe_id`, `r`.`title` AS `recipe_title`, `r`.`instructions` AS `recipe_instructions`, `r`.`preparation_time` AS `preparation_time`, `r`.`difficulty` AS `difficulty`, `r`.`calories` AS `calories`, `r`.`created_at` AS `created_at`, `u`.`username` AS `creator_username`, `u`.`idUser` AS `creator_id`, group_concat(distinct concat(`ri`.`idImage`,'_',`ri`.`path`) separator ', ') AS `image_paths`, group_concat(distinct `c`.`name` separator ', ') AS `categories`, `rf`.`foodtypes_with_percentages` AS `foodtypes_with_percentages`, coalesce(avg(`rr`.`score`),0) AS `average_rating` FROM ((((((`recipes` `r` left join `users` `u` on((`r`.`idUser` = `u`.`idUser`))) left join `recipes_images` `ri` on((`r`.`idRecipe` = `ri`.`idRecipe`))) left join `recipe_categories` `rc` on((`r`.`idRecipe` = `rc`.`idRecipe`))) left join `categories` `c` on((`rc`.`idCategory` = `c`.`idCategory`))) left join `recipes_foodtypes_view` `rf` on((`r`.`idRecipe` = `rf`.`idRecipe`))) left join `ratings` `rr` on((`r`.`idRecipe` = `rr`.`idRecipe`))) GROUP BY `r`.`idRecipe`  ;

-- --------------------------------------------------------

--
-- Structure for view `recipes_foodtypes_view`
--
DROP TABLE IF EXISTS `recipes_foodtypes_view`;

DROP VIEW IF EXISTS `recipes_foodtypes_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_foodtypes_view`  AS SELECT `rft`.`idRecipe` AS `idRecipe`, group_concat(concat(`ft`.`name`,' (',`rft`.`percentage`,'%)') separator ', ') AS `foodtypes_with_percentages` FROM (`recipe_food_types` `rft` join `food_types` `ft` on((`rft`.`idFoodType` = `ft`.`idFoodType`))) GROUP BY `rft`.`idRecipe`  ;

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
