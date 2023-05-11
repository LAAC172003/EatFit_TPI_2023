-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 11, 2023 at 08:57 AM
-- Server version: 8.0.31
-- PHP Version: 8.2.0

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
-- Structure for view `recipes_details`
--

DROP VIEW IF EXISTS `recipes_details`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `recipes_details`  AS SELECT `r`.`idRecipe` AS `recipe_id`, `r`.`title` AS `recipe_title`, `r`.`instructions` AS `recipe_instructions`, `r`.`preparation_time` AS `preparation_time`, `r`.`difficulty` AS `difficulty`,r.`instructions` as instructions, `r`.`calories` AS `calories`, `r`.`created_at` AS `created_at`, `u`.`username` AS `creator_username`, group_concat(distinct `ri`.`path` separator ', ') AS `image_paths`, group_concat(distinct `c`.`name` separator ', ') AS `categories`, group_concat(distinct `ft`.`name` separator ', ') AS `food_types` FROM ((((((`recipes` `r` left join `users` `u` on((`r`.`idUser` = `u`.`idUser`))) left join `recipes_images` `ri` on((`r`.`idRecipe` = `ri`.`idRecipe`))) left join `recipe_categories` `rc` on((`r`.`idRecipe` = `rc`.`idRecipe`))) left join `categories` `c` on((`rc`.`idCategory` = `c`.`idCategory`))) left join `recipe_food_types` `rft` on((`r`.`idRecipe` = `rft`.`idRecipe`))) left join `food_types` `ft` on((`rft`.`idFoodType` = `ft`.`idFoodType`))) GROUP BY `r`.`idRecipe`  ;

--
-- VIEW `recipes_details`
-- Data: None
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
