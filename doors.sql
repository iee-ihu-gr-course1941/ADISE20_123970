-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Φιλοξενητής: 127.0.0.1:3306
-- Χρόνος δημιουργίας: 06 Ιαν 2021 στις 21:45:28
-- Έκδοση διακομιστή: 10.3.14-MariaDB
-- Έκδοση PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `doors`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `board`
--

DROP TABLE IF EXISTS `board`;
CREATE TABLE IF NOT EXISTS `board` (
  `board_id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `board_data` text NOT NULL,
  `die_1` int(1) NOT NULL DEFAULT 0,
  `die_2` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`board_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `game`
--

DROP TABLE IF EXISTS `game`;
CREATE TABLE IF NOT EXISTS `game` (
  `game_id` int(11) NOT NULL AUTO_INCREMENT,
  `player_1_id` int(11) NOT NULL,
  `player_2_id` int(11) DEFAULT NULL,
  `status` enum('game created','game started','player 1 turn','player 2 turn','game ended','game aborted') NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`game_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `token` varchar(16) NOT NULL,
  `last_action` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `user`
--

INSERT INTO `user` (`user_id`, `username`, `password`, `avatar`, `token`, `last_action`) VALUES
(1, 'george', '1234', 'george.png', '5da8d24f5040a646', '2021-01-06 20:21:22'),
(2, 'kostas', '1234', 'kostas.png', '111a64d14312f159', '2021-01-06 17:15:16');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
