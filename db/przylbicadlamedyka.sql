-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 29 Mar 2020, 12:31
-- Wersja serwera: 5.7.28
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
-- Struktura tabeli dla tabeli `cron_errors`
--

CREATE TABLE `cron_errors` (
  `id` int(11) NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `value` text NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `href` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `new` tinyint(1) NOT NULL DEFAULT '1',
  `nd` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Zrzut danych tabeli `notifications`
--

INSERT INTO `notifications` (`id`, `users_id`, `content`, `href`, `new`, `nd`, `created_at`) VALUES
(21, 'e0de1fa1ac28db86f693abf2b77ccec0', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 1, '2020-03-28 20:28:58'),
(22, '85ce1891a270769632e034f2725c0ef4', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 0, '2020-03-28 20:31:12'),
(23, 'bcf895987630ca37ae29fe67cf05fa88', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 0, '2020-03-28 20:34:06'),
(24, '3d47ff3b31367fbd71f8cf3ce297c3d0', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 1, '2020-03-28 20:35:26'),
(25, '4f6c31ed9ab1f3270faddbf7f3a22c2b', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 1, '2020-03-28 20:36:44'),
(26, 'f51caab55821966a4574745bd3096d95', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 1, '2020-03-28 20:37:41'),
(27, '44738023bb18f64165764ca7a33ac5f4', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 1, '2020-03-28 21:01:54'),
(28, '0d82ac6c22a75d189e1fb7bce6d04268', 'Aby uzyskać pełny dostęp do storny proszę potwierdzić adres e-mail', NULL, 1, 0, '2020-03-29 10:16:30');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `options`
--

CREATE TABLE `options` (
  `users_id` varchar(256) NOT NULL,
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `value` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Zrzut danych tabeli `options`
--

INSERT INTO `options` (`users_id`, `name`, `value`) VALUES
('0d82ac6c22a75d189e1fb7bce6d04268', 'login', 'd687512b62ee183a413af9ce9b2c2e52'),
('3d47ff3b31367fbd71f8cf3ce297c3d0', 'login', 'c89e723ac6dc02ae68dd51963e0d7a29'),
('44738023bb18f64165764ca7a33ac5f4', 'login', '338aaabdabcaf365453f9fb111d14633'),
('4f6c31ed9ab1f3270faddbf7f3a22c2b', 'login', '1888c29a3c6d1a5a8c9fc404dd1df5d2'),
('85ce1891a270769632e034f2725c0ef4', 'login', '9fef8456c3e54496a91d24393e70ec23'),
('bcf895987630ca37ae29fe67cf05fa88', 'login', 'd6f1269504af092a336bb541b477ad53'),
('e0de1fa1ac28db86f693abf2b77ccec0', 'login', 'f42e7bcf1124239df52f6b6a33a58796'),
('f51caab55821966a4574745bd3096d95', 'login', '85768e1d4b15347ed07e60f73b1e9d59');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `options_page`
--

CREATE TABLE `options_page` (
  `name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `value` text CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `privileges`
--

CREATE TABLE `privileges` (
  `users_id` varchar(256) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Zrzut danych tabeli `privileges`
--

INSERT INTO `privileges` (`users_id`, `level`) VALUES
('0d82ac6c22a75d189e1fb7bce6d04268', 1),
('3d47ff3b31367fbd71f8cf3ce297c3d0', 1),
('44738023bb18f64165764ca7a33ac5f4', 1),
('4f6c31ed9ab1f3270faddbf7f3a22c2b', 1),
('85ce1891a270769632e034f2725c0ef4', 1),
('bcf895987630ca37ae29fe67cf05fa88', 1),
('e0de1fa1ac28db86f693abf2b77ccec0', 1),
('f51caab55821966a4574745bd3096d95', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `users_id` varchar(256) NOT NULL,
  `latLng` varchar(128) DEFAULT NULL,
  `bascinet` int(4) DEFAULT NULL,
  `material` int(4) DEFAULT NULL,
  `comments` text,
  `frozen` tinyint(1) NOT NULL DEFAULT '0',
  `delivered` varchar(128) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Zrzut danych tabeli `requests`
--

INSERT INTO `requests` (`id`, `users_id`, `latLng`, `bascinet`, `material`, `comments`, `frozen`, `delivered`, `deleted`, `created_at`) VALUES
(1, '85ce1891a270769632e034f2725c0ef4', '50.0676309,19.93510910633446', 5, NULL, 'Czarny domek na podwórku', 0, '0', 0, '2020-03-28 20:40:13'),
(2, '4f6c31ed9ab1f3270faddbf7f3a22c2b', '50.05409755554864,19.948108792304996', 10, 5, NULL, 0, '0', 0, '2020-03-28 20:40:13'),
(3, 'f51caab55821966a4574745bd3096d95', '50.04386689441092,19.94748651981354', NULL, 15, NULL, 0, '0', 0, '2020-03-28 20:41:41'),
(4, '3d47ff3b31367fbd71f8cf3ce297c3d0', '50.0657567,19.9447989', 10, NULL, NULL, 0, '0', 0, '2020-03-28 20:41:41'),
(5, 'bcf895987630ca37ae29fe67cf05fa88', '50.0533265,19.9402514', 5, 12, 'Kod do bramki: 457362', 1, '0', 0, '2020-03-28 20:42:34'),
(6, 'e0de1fa1ac28db86f693abf2b77ccec0', '50.0720308,19.9324319', NULL, 7, NULL, 1, '0', 0, '2020-03-28 20:42:34');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` varchar(256) NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 NOT NULL,
  `first_name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `last_name` varchar(128) CHARACTER SET utf8 NOT NULL,
  `address` varchar(128) NOT NULL,
  `tel` varchar(64) NOT NULL,
  `password` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `salt` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `email`, `first_name`, `last_name`, `address`, `tel`, `password`, `salt`) VALUES
('0d82ac6c22a75d189e1fb7bce6d04268', 'lan91958@eoopy.com', 'test', 'test', '50.06148585,19.936414898451858', '123123123', 'df5904932da7cc72db781c13ffc271c0', '3052287694'),
('3d47ff3b31367fbd71f8cf3ce297c3d0', 'test3@test.pl', 'Piotr', 'Trzeciak', '50.0657567,19.9447989', '+48504653269', '209353f452f5b209beb9f53db2d1c9d6', '5401557223'),
('44738023bb18f64165764ca7a33ac5f4', 'tpt17214@eoopy.com', 'test', 'test', '50.06148585,19.936414898451858', '121212121', 'a4f2182a8f405e710acdcc943983f3ee', '2104727689'),
('4f6c31ed9ab1f3270faddbf7f3a22c2b', 'test4@test.pl', 'Jakub', 'Kosewski', '50.05409755554864,19.948108792304996', '+48765258965', '771c6550efdffc1ffe2329d1a4ac1609', '5792782892'),
('85ce1891a270769632e034f2725c0ef4', 'test@test.pl', 'Ignacy', 'Janiszewski', '50.0676309,19.93510910633446', '+48765985235', '893aaf317525d42670b24854ede98c6c', '6891666602'),
('bcf895987630ca37ae29fe67cf05fa88', 'test2@test.pl', 'Stanisław', 'Szufa', '50.0533265,19.9402514', '+48745265894', 'd53bc9932466111745866595f4e4c0a0', '2807912272'),
('e0de1fa1ac28db86f693abf2b77ccec0', 'k.janiszewski@poczta.fm', 'Test', 'Testowy', '50.0720308,19.9324319', '+48794626518', 'b32a0585b0ff35eed2b4c8b5d7508df9', '3198408249'),
('f51caab55821966a4574745bd3096d95', 'test5@test.pl', 'Krzysztof', 'Janiszewski', '50.04386689441092,19.94748651981354', '+48794626518', '58e972fabc13ec316f4df22a6e08b04c', '6090622407');

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
  `users_id` varchar(256) CHARACTER SET latin2 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('normal','google','facebook','') CHARACTER SET latin2 NOT NULL DEFAULT 'normal',
  `user_agent` text CHARACTER SET latin2,
  `ip` text CHARACTER SET latin2
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Zrzut danych tabeli `users_additional_info`
--

INSERT INTO `users_additional_info` (`users_id`, `created_at`, `type`, `user_agent`, `ip`) VALUES
('0d82ac6c22a75d189e1fb7bce6d04268', '2020-03-29 10:16:31', 'normal', '[macOS], [Chrome Generic], [Chrome], [Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [83.22.46.30]'),
('3d47ff3b31367fbd71f8cf3ce297c3d0', '2020-03-28 20:35:26', 'normal', '[Win10], [Chrome Generic], [Chrome], [Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [37.30.53.229]'),
('44738023bb18f64165764ca7a33ac5f4', '2020-03-28 21:01:54', 'normal', '[macOS], [Chrome Generic], [Chrome], [Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [83.22.46.30]'),
('4f6c31ed9ab1f3270faddbf7f3a22c2b', '2020-03-28 20:36:44', 'normal', '[Win10], [Chrome Generic], [Chrome], [Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [37.30.53.229]'),
('85ce1891a270769632e034f2725c0ef4', '2020-03-28 20:31:12', 'normal', '[Win10], [Chrome Generic], [Chrome], [Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [37.30.53.229]'),
('bcf895987630ca37ae29fe67cf05fa88', '2020-03-28 20:34:06', 'normal', '[Win10], [Chrome Generic], [Chrome], [Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [37.30.53.229]'),
('e0de1fa1ac28db86f693abf2b77ccec0', '2020-03-28 20:28:58', 'normal', '[Win10], [Chrome Generic], [Chrome], [Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [37.30.53.229]'),
('f51caab55821966a4574745bd3096d95', '2020-03-28 20:37:41', 'normal', '[Win10], [Chrome Generic], [Chrome], [Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.149 Safari/537.36]', '[], [], [], [37.30.53.229]');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `cron_errors`
--
ALTER TABLE `cron_errors`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT dla tabeli `cron_errors`
--
ALTER TABLE `cron_errors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT dla tabeli `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT dla tabeli `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ograniczenia dla zrzutów tabel
--

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
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

--
-- Ograniczenia dla tabeli `users_additional_info`
--
ALTER TABLE `users_additional_info`
  ADD CONSTRAINT `users_additional_info_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
