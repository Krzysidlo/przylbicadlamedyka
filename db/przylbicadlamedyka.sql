-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 03 Kwi 2020, 21:25
-- Wersja serwera: 8.0.19-0ubuntu0.19.10.3
-- Wersja PHP: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `przylbicadlamedyka`
--
CREATE DATABASE IF NOT EXISTS `przylbicadlamedyka` DEFAULT CHARACTER SET latin2 COLLATE latin2_general_ci;
USE `przylbicadlamedyka`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `activities`
--

CREATE TABLE `activities` (
  `id` int NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `type` enum('action','notification') NOT NULL,
  `requests_id` int DEFAULT NULL,
  `frozen_id` varchar(128) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `address`
--

CREATE TABLE `address` (
  `users_id` varchar(256) NOT NULL,
  `pin_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `city` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `street` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `building` int NOT NULL,
  `flat` int DEFAULT NULL,
  `location` varchar(256) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cron_errors`
--

CREATE TABLE `cron_errors` (
  `id` int NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `frozen`
--

CREATE TABLE `frozen` (
  `id` int NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `date` timestamp NOT NULL,
  `requests_id` int NOT NULL,
  `delivered` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `hos_mag`
--

CREATE TABLE `hos_mag` (
  `id` int NOT NULL,
  `pins_id` int NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `quantity` int NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `href` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `new` tinyint(1) NOT NULL DEFAULT '1',
  `nd` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `options`
--

CREATE TABLE `options` (
  `users_id` varchar(256) NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `options_page`
--

CREATE TABLE `options_page` (
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pins`
--

CREATE TABLE `pins` (
  `id` int NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `latLng` varchar(256) NOT NULL,
  `type` enum('hospital','magazine') NOT NULL,
  `bascinet` int DEFAULT NULL,
  `material` int DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `privileges`
--

CREATE TABLE `privileges` (
  `users_id` varchar(256) NOT NULL,
  `level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `requests`
--

CREATE TABLE `requests` (
  `id` int NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `latLng` varchar(128) DEFAULT NULL,
  `bascinet` int DEFAULT NULL,
  `material` int DEFAULT NULL,
  `comments` text,
  `delivered` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` varchar(256) NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `first_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_name` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tel` varchar(64) NOT NULL,
  `password` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `salt` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Wyzwalacze `users`
--
DELIMITER $$
CREATE TRIGGER `Default privelege` AFTER INSERT ON `users` FOR EACH ROW INSERT INTO `privileges` VALUES(NEW.id, 1) ON DUPLICATE KEY UPDATE `users_id` = `users_id`
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users_additional_info`
--

CREATE TABLE `users_additional_info` (
  `users_id` varchar(256) CHARACTER SET latin2 COLLATE latin2_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('normal','google','facebook','') CHARACTER SET latin2 COLLATE latin2_general_ci NOT NULL DEFAULT 'normal',
  `user_agent` text CHARACTER SET latin2 COLLATE latin2_general_ci,
  `ip` text CHARACTER SET latin2 COLLATE latin2_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `requests_id` (`requests_id`);

--
-- Indeksy dla tabeli `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`users_id`);

--
-- Indeksy dla tabeli `cron_errors`
--
ALTER TABLE `cron_errors`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `frozen`
--
ALTER TABLE `frozen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `request_id` (`requests_id`);

--
-- Indeksy dla tabeli `hos_mag`
--
ALTER TABLE `hos_mag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pins_id` (`pins_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indeksy dla tabeli `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indeksy dla tabeli `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`users_id`,`name`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `users_id_2` (`users_id`);

--
-- Indeksy dla tabeli `options_page`
--
ALTER TABLE `options_page`
  ADD PRIMARY KEY (`name`);

--
-- Indeksy dla tabeli `pins`
--
ALTER TABLE `pins`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`users_id`);

--
-- Indeksy dla tabeli `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users_additional_info`
--
ALTER TABLE `users_additional_info`
  ADD PRIMARY KEY (`users_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `cron_errors`
--
ALTER TABLE `cron_errors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `frozen`
--
ALTER TABLE `frozen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `hos_mag`
--
ALTER TABLE `hos_mag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `pins`
--
ALTER TABLE `pins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`requests_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `frozen`
--
ALTER TABLE `frozen`
  ADD CONSTRAINT `frozen_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `frozen_ibfk_2` FOREIGN KEY (`requests_id`) REFERENCES `requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `hos_mag`
--
ALTER TABLE `hos_mag`
  ADD CONSTRAINT `hos_mag_ibfk_1` FOREIGN KEY (`pins_id`) REFERENCES `pins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hos_mag_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `privileges`
--
ALTER TABLE `privileges`
  ADD CONSTRAINT `privileges_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ograniczenia dla tabeli `users_additional_info`
--
ALTER TABLE `users_additional_info`
  ADD CONSTRAINT `users_additional_info_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
