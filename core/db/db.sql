-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 03, 2023 at 10:58 AM
-- Server version: 5.7.40
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

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `idCategory` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`idCategory`)
  ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`idCategory`, `name`, `image_path`) VALUES
                                                                (1, 'Petit déjeuner', ''),
                                                                (2, 'Entrée', ''),
                                                                (3, 'Déjeuner', ''),
                                                                (4, 'Collation', ''),
                                                                (5, 'Dîner', ''),
                                                                (6, 'Dessert', '');

-- --------------------------------------------------------

--
-- Table structure for table `consumed_recipes`
--

DROP TABLE IF EXISTS `consumed_recipes`;
CREATE TABLE IF NOT EXISTS `consumed_recipes` (
  `idConsumedRecipe` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `consumption_date` date NOT NULL,
  `idUser` int(11) UNSIGNED NOT NULL,
  `idRecipe` int(11) UNSIGNED NOT NULL,
  `idCategory` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`idConsumedRecipe`),
  KEY `idUser` (`idUser`),
  KEY `idRecipe` (`idRecipe`),
  KEY `idCategory` (`idCategory`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `food_types`
--

DROP TABLE IF EXISTS `food_types`;
CREATE TABLE IF NOT EXISTS `food_types` (
  `idFoodType` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`idFoodType`)
  ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

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
  `idRating` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL,
  `comment` text,
  `idUser` int(11) UNSIGNED NOT NULL,
  `idRecipe` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`idRating`),
  KEY `idUser` (`idUser`),
  KEY `idRecipe` (`idRecipe`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `idRecipe` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `preparation_time` int(11) NOT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL,
  `instructions` text NOT NULL,
  `calories` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idUser` int(11) UNSIGNED NOT NULL,
  `idImage` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`idRecipe`),
  KEY `idUser` (`idUser`),
  KEY `idImage` (`idImage`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recipe_categories`
--

DROP TABLE IF EXISTS `recipe_categories`;
CREATE TABLE IF NOT EXISTS `recipe_categories` (
  `idRecipe` int(11) UNSIGNED NOT NULL,
  `idCategory` int(11) UNSIGNED NOT NULL,
  KEY `idRecipe` (`idRecipe`),
  KEY `idCategory` (`idCategory`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recipe_food_types`
--

DROP TABLE IF EXISTS `recipe_food_types`;
CREATE TABLE IF NOT EXISTS `recipe_food_types` (
  `idRecipe` int(11) UNSIGNED NOT NULL,
  `idFoodType` int(11) UNSIGNED NOT NULL,
  `percentage` decimal(11,0) NOT NULL,
  KEY `idRecipe` (`idRecipe`),
  KEY `idFoodType` (`idFoodType`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `recipe_images`
--

DROP TABLE IF EXISTS `recipe_images`;
CREATE TABLE IF NOT EXISTS `recipe_images` (
  `idImage` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`idImage`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `idUser` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(400) DEFAULT NULL,
  `expiration` int(11) DEFAULT NULL,
  PRIMARY KEY (`idUser`)
  ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consumed_recipes`
--
ALTER TABLE `consumed_recipes`
  ADD CONSTRAINT `consumed_recipes_ibfk_1` FOREIGN KEY (`idCategory`) REFERENCES `categories` (`idCategory`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`),
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`idImage`) REFERENCES `recipe_images` (`idImage`) ON DELETE CASCADE ON UPDATE CASCADE;

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
