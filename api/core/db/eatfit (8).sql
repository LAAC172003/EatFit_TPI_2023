
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP DATABASE IF EXISTS eatfit;
DROP USER IF EXISTS 'eatfit'@'localhost';
CREATE USER 'eatfit'@'localhost' IDENTIFIED BY 'EatFit';
GRANT ALL PRIVILEGES ON eatfit.* TO 'eatfit'@'localhost';
CREATE DATABASE IF NOT EXISTS `eatfit` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `eatfit`;
DELIMITER $$
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


DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `idCategory` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  PRIMARY KEY (`idCategory`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;


INSERT INTO `categories` (`idCategory`, `name`, `image_path`) VALUES
(1, 'Breakfast', ''),
(2, 'Appetizer', ''),
(3, 'Lunch', ''),
(4, 'Snack', ''),
(5, 'Dinner', ''),
(6, 'Dessert', '');


DROP TABLE IF EXISTS `consumed_recipes`;
CREATE TABLE IF NOT EXISTS `consumed_recipes` (
  `idConsumedRecipe` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `consumption_date` date NOT NULL,
  `idUser` int UNSIGNED NOT NULL,
  `idRecipe` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idConsumedRecipe`),
  KEY `idUser` (`idUser`),
  KEY `idRecipe` (`idRecipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `food_types`;
CREATE TABLE IF NOT EXISTS `food_types` (
  `idFoodType` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`idFoodType`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
CREATE TABLE IF NOT EXISTS `recipes` (
  `idRecipe` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `preparation_time` int NOT NULL,
  `difficulty` enum('easy','medium','hard') NOT NULL,
  `instructions` text NOT NULL,
  `calories` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `idUser` int UNSIGNED NOT NULL,
  PRIMARY KEY (`idRecipe`),
  KEY `idUser` (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`idRecipe`, `title`, `preparation_time`, `difficulty`, `instructions`, `calories`, `created_at`, `idUser`) VALUES
(2, 'Pasta Carbonara', 30, 'medium', 'Cook spaghetti al dente. In a pan, cook chopped bacon until crispy. In a bowl, mix eggs and grated cheese. Combine pasta, bacon and egg mixture, stirring quickly to create a creamy sauce. Season with salt and pepper.', 540, '2023-05-10 11:12:22', 1),
(3, 'Blueberry Muffins', 40, 'medium', 'Preheat oven to 180C. In a large bowl, combine flour, sugar, baking powder and salt. In another bowl, beat eggs, milk and melted butter. Stir wet mixture into dry ingredients. Fold in blueberries. Spoon batter into muffin cups. Bake for 20-25 minutes.', 180, '2023-05-10 11:12:30', 1),
(4, 'Grilled Salmon', 15, 'easy', 'Preheat grill to medium heat. Season salmon fillets with salt, pepper and a drizzle of olive oil. Grill for 4-5 minutes on each side.', 350, '2023-05-10 11:12:36', 1),
(5, 'Fruit Salad', 10, 'easy', 'Chop a variety of fruits such as strawberries, bananas, oranges, and apples. Mix all fruits in a large bowl and refrigerate before serving.', 80, '2023-05-10 11:13:16', 1),
(6, 'Apple Pie', 90, 'medium', 'Preheat oven to 180C. Roll out dough into a pie dish. In a bowl, combine sliced apples, sugar, cinnamon, and flour. Pour apple mixture into pie dish, cover with another layer of dough. Bake for 45-50 minutes.', 320, '2023-05-10 11:14:50', 1),
(7, 'Tomato Soup', 40, 'easy', 'Sauté chopped onions and garlic in a pot. Add chopped tomatoes and vegetable stock. Simmer for 30 minutes. Blend until smooth. Season with salt and pepper.', 120, '2023-05-10 11:15:20', 1),
(8, 'Cheese Omelette', 10, 'easy', 'Beat eggs in a bowl. Melt butter in a non-stick pan, pour in eggs. When eggs start to set, sprinkle grated cheese on top. Fold omelette in half and serve.', 220, '2023-05-10 11:17:05', 1),
(9, 'Vegetable Stir Fry', 30, 'easy', 'Heat oil in a wok, add chopped vegetables such as bell peppers, broccoli, and carrots. Stir fry for a few minutes. Add soy sauce and serve with rice.', 180, '2023-05-10 11:17:14', 1),
(10, 'Garlic Shrimp', 20, 'medium', 'In a pan, sauté garlic in olive oil. Add shrimp and cook until they turn pink. Sprinkle with chopped parsley and serve.', 200, '2023-05-10 11:17:48', 1),
(11, 'Chicken Salad', 20, 'easy', 'Take a large bowl, add chopped lettuce, cucumbers, tomatoes, and grilled chicken. Mix well with olive oil, salt and pepper.', 280, '2023-05-10 11:26:34', 1),
(12, 'Oatmeal Bowl', 10, 'easy', '1. Heat the almond milk and oats in a saucepan until boiling. \n2. Reduce heat and stir in chia seeds, maple syrup, and cinnamon. \n3. Simmer until the mixture thickens, stirring occasionally. \n4. Serve in a bowl with fresh fruit and nuts on top.', 350, '2023-05-10 11:29:00', 1),
(13, 'Caprese Salad', 15, 'easy', '1. Cut the tomatoes and mozzarella into slices. \n2. Arrange them on a plate with basil leaves. \n3. Drizzle with olive oil and balsamic vinegar. \n4. Season with salt and pepper to taste.', 200, '2023-05-10 11:29:09', 1),
(14, 'Grilled Chicken Sandwich', 30, 'medium', '1. Season the chicken breasts with salt and pepper. \n2. Grill the chicken on medium-high heat until cooked through, about 8 minutes per side. \n3. Toast the bread and spread with mayonnaise. \n4. Assemble the sandwich with lettuce, tomato, and grilled chicken.', 500, '2023-05-10 11:29:13', 1),
(15, 'Trail Mix Snack', 5, 'easy', '1. Mix together almonds, dried cranberries, raisins, and dark chocolate chips. \n2. Store in an airtight container until ready to eat.', 250, '2023-05-10 11:29:17', 1),
(16, 'Oatmeal with Berries', 10, 'easy', 'In a small saucepan, bring water to a boil. Stir in oats and reduce heat to low. Cook for 5 minutes, stirring occasionally. Serve with berries on top.', 150, '2023-05-10 11:30:14', 1),
(17, 'Avocado Toast', 5, 'easy', 'Toast bread. Mash avocado with lime juice and spread on toast. Top with cherry tomatoes and a sprinkle of salt and pepper.', 220, '2023-05-10 11:30:20', 1),
(18, 'Hummus and Veggie Wraps', 15, 'easy', 'Spread hummus on a whole wheat tortilla. Add chopped veggies such as cucumber, carrot, and bell pepper. Roll up and enjoy.', 300, '2023-05-10 11:30:39', 1),
(19, 'Spicy Peanut Noodles', 30, 'medium', 'Cook spaghetti according to package instructions. Meanwhile, mix peanut butter, soy sauce, chili paste, and vinegar in a bowl. Add cooked spaghetti and chopped scallions. Toss well and serve.', 400, '2023-05-10 11:30:46', 1),
(20, 'Pancakes aux myrtilles', 30, 'easy', 'Mélanger la farine, le sucre et la levure dans un grand bol. Ajouter le lait, l\'œuf et l\'huile et mélanger jusqu\'à obtenir une pâte lisse. Incorporer délicatement les myrtilles. Faire chauffer une poêle à feu moyen et y verser des louches de pâte. Cuire jusqu\'à ce que des bulles se forment à la surface, puis retourner et cuire l\'autre côté. Servir chaud avec du sirop d\'érable ou du miel.', 420, '2023-05-10 11:31:26', 1),
(21, 'Omelette aux légumes', 20, 'easy', 'Dans un bol, battre les œufs avec une pincée de sel et de poivre. Dans une poêle, faire revenir des champignons, des poivrons et des épinards avec un peu d\'huile d\'olive. Ajouter les œufs battus et cuire jusqu\'à ce que le dessus soit pris. Retourner l\'omelette et cuire l\'autre côté. Servir chaud avec une tranche de pain grillé.', 280, '2023-05-10 11:31:39', 1),
(22, 'Salade de poulet grillé', 20, 'easy', 'Prenez un grand bol, ajoutez de la laitue hachée, des concombres, des tomates et du poulet grillé. Mélangez bien avec de l\'huile d\'olive, du sel et du poivre.', 280, '2023-05-10 11:35:02', 1);

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
(2, 'pasta1.jpg', 2),
(3, 'muffins1.jpg', 3),
(4, 'salmon1.jpg', 4),
(5, 'fruitsalad1.jpg', 5),
(6, 'applepie.jpg', 6),
(7, 'tomatosoup.jpg', 7),
(8, 'omelette.jpg', 8),
(9, 'stirfry.jpg', 9),
(10, 'shrimp.jpg', 10),
(12, 'path', 2),
(13, 'salad1.jpg', 11),
(14, 'oatmeal_bowl.jpg', 12),
(15, 'caprese_salad.jpg', 13),
(16, 'grilled_chicken_sandwich.jpg', 14),
(17, 'trail_mix.jpg', 15),
(18, 'oatmeal.jpg', 16),
(19, 'avocado_toast.jpg', 17),
(20, 'hummus_wrap.jpg', 18),
(21, 'peanut_noodles.jpg', 19),
(22, 'pancakes.jpg', 20),
(23, 'omelette.jpg', 21),
(24, 'salad1.jpg', 22);

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
(2, 5),
(3, 4),
(4, 5),
(5, 1),
(6, 6),
(7, 3),
(8, 1),
(9, 5),
(10, 2),
(11, 3),
(12, 1),
(13, 2),
(14, 3),
(15, 4),
(16, 1),
(17, 1),
(18, 3),
(19, 5),
(20, 1),
(21, 1),
(22, 3);

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
(2, 6, '45'),
(2, 2, '40'),
(2, 4, '15'),
(3, 1, '60'),
(3, 7, '30'),
(3, 4, '10'),
(4, 2, '60'),
(4, 4, '35'),
(4, 5, '5'),
(5, 5, '95'),
(5, 1, '5'),
(6, 1, '70'),
(6, 5, '20'),
(6, 4, '10'),
(7, 5, '70'),
(7, 7, '20'),
(7, 4, '10'),
(8, 2, '50'),
(8, 3, '30'),
(8, 4, '20'),
(9, 5, '70'),
(9, 6, '20'),
(9, 4, '10'),
(10, 2, '70'),
(10, 5, '20'),
(10, 4, '10'),
(11, 2, '50'),
(11, 5, '45'),
(11, 4, '5'),
(12, 2, '10'),
(12, 5, '40'),
(12, 6, '50'),
(13, 3, '50'),
(13, 5, '50'),
(14, 2, '50'),
(14, 4, '25'),
(14, 5, '25'),
(15, 1, '30'),
(15, 2, '20'),
(15, 4, '50'),
(16, 5, '40'),
(16, 6, '30'),
(16, 3, '20'),
(16, 4, '10'),
(17, 5, '50'),
(17, 4, '40'),
(17, 6, '10'),
(18, 5, '70'),
(18, 6, '20'),
(18, 4, '10'),
(19, 6, '50'),
(19, 2, '30'),
(19, 4, '10'),
(19, 5, '10'),
(20, 1, '50'),
(20, 3, '25'),
(20, 7, '25'),
(21, 2, '50'),
(21, 5, '30'),
(21, 4, '20'),
(22, 2, '50'),
(22, 5, '45'),
(22, 4, '5');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`idUser`, `username`, `email`, `password`, `token`, `expiration`) VALUES
(1, 'Lucas Almeida Costa', 'test@gmail.com', '$2y$10$p6Oyt7Eqg/FplYInpwbfPu1Ihl5Cv.nd.NKvZGOPX8eURs1Meroje', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6InRlc3RAZ21haWwuY29tIiwidXNlcm5hbWUiOiJMdWNhcyBBbG1laWRhIENvc3RhIiwiZXhwIjoxNjgzNzMxNTIyfQ.6vcAvZ0ryzCZLr78JbtXbsaN-jrUhPFs_cMVweMguOY', 1683731522);


ALTER TABLE `consumed_recipes`
  ADD CONSTRAINT `consumed_recipes_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `consumed_recipes_ibfk_3` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `recipes_images`
  ADD CONSTRAINT `recipes_images_ibfk_1` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `recipe_categories`
  ADD CONSTRAINT `recipe_categories_ibfk_1` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_categories_ibfk_2` FOREIGN KEY (`idCategory`) REFERENCES `categories` (`idCategory`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `recipe_food_types`
  ADD CONSTRAINT `recipe_food_types_ibfk_1` FOREIGN KEY (`idFoodType`) REFERENCES `food_types` (`idFoodType`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recipe_food_types_ibfk_2` FOREIGN KEY (`idRecipe`) REFERENCES `recipes` (`idRecipe`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

