-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 29, 2025 at 10:31 AM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bilety`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `bilety`
--

CREATE TABLE `bilety` (
  `id_biletu` int(11) NOT NULL,
  `id_pasazera` int(11) NOT NULL,
  `id_pociagu` int(11) NOT NULL,
  `id_stacji_start` int(11) NOT NULL,
  `id_stacji_koniec` int(11) NOT NULL,
  `miejsce` varchar(5) NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `data_podrozy` date NOT NULL,
  `kod_qr` varchar(255) NOT NULL,
  `id_wagonu` int(11) NOT NULL,
  `id_znizki` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bilety`
--

INSERT INTO `bilety` (`id_biletu`, `id_pasazera`, `id_pociagu`, `id_stacji_start`, `id_stacji_koniec`, `miejsce`, `cena`, `data_podrozy`, `kod_qr`, `id_wagonu`, `id_znizki`) VALUES
(1, 1, 1, 1, 22, '25', 129.99, '2025-03-19', 'ABC123XYZ', 6, 1),
(26, 2, 1, 1, 12, '1', 64.99, '2025-03-19', '2617cb21600294ecb4365ba34d15cfbf', 1, 3),
(27, 2, 1, 1, 3, '64', 20.00, '2025-03-19', '39226dd2fde3ed1fcc0aaaa4f2212670', 2, 1),
(28, 2, 1, 7, 18, '72', 63.92, '2025-03-20', '2459ad06d65c6e70ec09fa53c412b39d', 3, 6),
(29, 2, 1, 1, 3, '6', 4.00, '2025-03-20', 'b5456c7330d7467f7b8e25fad8cbb7f5', 5, 5),
(30, 2, 1, 1, 3, '6', 9.90, '2025-03-20', 'bde91bcdaa6f2b8e3ba10bb91b309b2c', 5, 2),
(31, 2, 1, 1, 21, '4', 85.00, '2025-03-20', '48a32d24b16828a3c42ee872acc007e1', 4, 1),
(32, 2, 2, 26, 28, '6', 7.50, '2025-03-20', '12e37986bbd449fa2d096217e1edd576', 5, 5),
(33, 2, 1, 1, 3, '12', 0.01, '2025-05-05', '533deff0ea15d5bc2aa86189f5fc4dd1', 1, 2),
(34, 2, 1, 1, 3, '3', 27.00, '2025-05-14', '22d3ef86e129d13210d660a02457b56b', 2, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `ceny_odleglosci`
--

CREATE TABLE `ceny_odleglosci` (
  `id_ceny` int(11) NOT NULL,
  `odleglosc_min` int(11) NOT NULL,
  `odleglosc_max` int(11) NOT NULL,
  `cena` decimal(6,2) NOT NULL,
  `klasa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ceny_odleglosci`
--

INSERT INTO `ceny_odleglosci` (`id_ceny`, `odleglosc_min`, `odleglosc_max`, `cena`, `klasa`) VALUES
(1, 0, 40, 15.00, 2),
(2, 41, 45, 17.00, 2),
(3, 46, 50, 19.00, 2),
(4, 51, 55, 20.00, 2),
(5, 56, 60, 23.00, 2),
(6, 61, 65, 24.00, 2),
(7, 66, 70, 25.00, 2),
(8, 71, 75, 26.00, 2),
(9, 76, 80, 27.00, 2),
(10, 81, 85, 28.00, 2),
(11, 86, 90, 29.00, 2),
(12, 91, 95, 31.00, 2),
(13, 96, 100, 33.00, 2),
(14, 101, 105, 34.00, 2),
(15, 106, 110, 35.00, 2),
(16, 111, 115, 36.00, 2),
(17, 116, 120, 37.00, 2),
(18, 121, 125, 38.00, 2),
(19, 126, 130, 40.00, 2),
(20, 131, 135, 41.00, 2),
(21, 136, 140, 42.00, 2),
(22, 141, 145, 43.00, 2),
(23, 146, 150, 45.00, 2),
(24, 151, 155, 46.00, 2),
(25, 156, 160, 47.00, 2),
(26, 161, 165, 49.00, 2),
(27, 166, 170, 50.00, 2),
(28, 171, 175, 51.00, 2),
(29, 176, 180, 52.00, 2),
(30, 181, 185, 53.00, 2),
(31, 186, 190, 54.00, 2),
(32, 191, 195, 55.00, 2),
(33, 196, 200, 57.00, 2),
(34, 201, 210, 58.00, 2),
(35, 211, 220, 59.00, 2),
(36, 221, 230, 60.00, 2),
(37, 231, 240, 61.00, 2),
(38, 241, 250, 62.00, 2),
(39, 251, 260, 63.00, 2),
(40, 261, 270, 64.00, 2),
(41, 271, 280, 66.00, 2),
(42, 281, 290, 67.00, 2),
(43, 291, 300, 68.00, 2),
(44, 301, 310, 69.00, 2),
(45, 311, 320, 70.00, 2),
(46, 321, 340, 71.00, 2),
(47, 341, 360, 72.00, 2),
(48, 361, 380, 73.00, 2),
(49, 381, 400, 75.00, 2),
(50, 401, 420, 76.00, 2),
(51, 421, 440, 77.00, 2),
(52, 441, 460, 78.00, 2),
(53, 461, 480, 79.00, 2),
(54, 481, 500, 80.00, 2),
(55, 501, 520, 81.00, 2),
(56, 521, 540, 82.00, 2),
(57, 541, 560, 84.00, 2),
(58, 561, 580, 85.00, 2),
(59, 581, 600, 86.00, 2),
(60, 601, 620, 87.00, 2),
(61, 621, 640, 88.00, 2),
(62, 641, 660, 89.00, 2),
(63, 661, 680, 90.00, 2),
(64, 681, 700, 92.00, 2),
(65, 701, 720, 93.00, 2),
(66, 721, 740, 94.00, 2),
(67, 741, 760, 95.00, 2),
(68, 761, 780, 96.00, 2),
(69, 781, 800, 97.00, 2),
(70, 801, 820, 98.00, 2),
(71, 821, 840, 99.00, 2),
(72, 841, 880, 101.00, 2),
(73, 881, 920, 102.00, 2),
(74, 921, 960, 103.00, 2),
(75, 961, 1000, 104.00, 2),
(76, 0, 40, 23.00, 1),
(77, 41, 45, 24.00, 1),
(78, 46, 50, 26.00, 1),
(79, 51, 55, 27.00, 1),
(80, 56, 60, 29.00, 1),
(81, 61, 65, 32.00, 1),
(82, 66, 70, 33.00, 1),
(83, 71, 75, 34.00, 1),
(84, 76, 80, 36.00, 1),
(85, 81, 85, 37.00, 1),
(86, 86, 90, 38.00, 1),
(87, 91, 95, 41.00, 1),
(88, 96, 100, 43.00, 1),
(89, 101, 105, 44.00, 1),
(90, 106, 110, 46.00, 1),
(91, 111, 115, 47.00, 1),
(92, 116, 120, 49.00, 1),
(93, 121, 125, 51.00, 1),
(94, 126, 130, 52.00, 1),
(95, 131, 135, 53.00, 1),
(96, 136, 140, 55.00, 1),
(97, 141, 145, 57.00, 1),
(98, 146, 150, 59.00, 1),
(99, 151, 155, 61.00, 1),
(100, 156, 160, 62.00, 1),
(101, 161, 165, 63.00, 1),
(102, 166, 170, 66.00, 1),
(103, 171, 175, 67.00, 1),
(104, 176, 180, 68.00, 1),
(105, 181, 185, 70.00, 1),
(106, 186, 190, 71.00, 1),
(107, 191, 195, 72.00, 1),
(108, 196, 200, 73.00, 1),
(109, 201, 210, 76.00, 1),
(110, 211, 220, 77.00, 1),
(111, 221, 230, 78.00, 1),
(112, 231, 240, 80.00, 1),
(113, 241, 250, 81.00, 1),
(114, 251, 260, 82.00, 1),
(115, 261, 270, 85.00, 1),
(116, 271, 280, 86.00, 1),
(117, 281, 290, 87.00, 1),
(118, 291, 300, 88.00, 1),
(119, 301, 310, 90.00, 1),
(120, 311, 320, 92.00, 1),
(121, 321, 340, 93.00, 1),
(122, 341, 360, 95.00, 1),
(123, 361, 380, 96.00, 1),
(124, 381, 400, 97.00, 1),
(125, 401, 420, 99.00, 1),
(126, 421, 440, 101.00, 1),
(127, 441, 460, 102.00, 1),
(128, 461, 480, 103.00, 1),
(129, 481, 500, 105.00, 1),
(130, 501, 520, 106.00, 1),
(131, 521, 540, 107.00, 1),
(132, 541, 560, 110.00, 1),
(133, 561, 580, 111.00, 1),
(134, 581, 600, 112.00, 1),
(135, 601, 620, 114.00, 1),
(136, 621, 640, 115.00, 1),
(137, 641, 660, 116.00, 1),
(138, 661, 680, 118.00, 1),
(139, 681, 700, 120.00, 1),
(140, 701, 720, 121.00, 1),
(141, 721, 740, 122.00, 1),
(142, 741, 760, 124.00, 1),
(143, 761, 780, 125.00, 1),
(144, 781, 800, 127.00, 1),
(145, 801, 820, 129.00, 1),
(146, 821, 840, 130.00, 1),
(147, 841, 880, 131.00, 1),
(148, 881, 920, 132.00, 1),
(149, 921, 960, 134.00, 1),
(150, 961, 1000, 136.00, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `odleglosci_miedzy_stacjami`
--

CREATE TABLE `odleglosci_miedzy_stacjami` (
  `id_odleglosci` int(11) NOT NULL,
  `id_pociagu` int(11) NOT NULL,
  `id_stacji_poczatek` int(11) NOT NULL,
  `id_stacji_koniec` int(11) NOT NULL,
  `odleglosc_km` decimal(6,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `odleglosci_miedzy_stacjami`
--

INSERT INTO `odleglosci_miedzy_stacjami` (`id_odleglosci`, `id_pociagu`, `id_stacji_poczatek`, `id_stacji_koniec`, `odleglosc_km`) VALUES
(1, 1, 1, 2, 21.7),
(2, 1, 2, 3, 33.9),
(3, 1, 3, 4, 20.9),
(4, 1, 4, 5, 24.2),
(5, 1, 5, 6, 32.0),
(6, 1, 6, 7, 53.5),
(7, 1, 7, 8, 21.7),
(8, 1, 8, 9, 6.3),
(9, 1, 9, 10, 17.3),
(10, 1, 10, 11, 18.6),
(11, 1, 11, 12, 38.7),
(12, 1, 12, 13, 13.1),
(13, 1, 13, 14, 36.3),
(14, 1, 14, 15, 22.5),
(15, 1, 15, 16, 28.6),
(16, 1, 16, 17, 49.8),
(17, 1, 17, 18, 45.4),
(18, 1, 18, 19, 26.2),
(19, 1, 19, 20, 51.2),
(20, 1, 20, 21, 3.1),
(21, 1, 21, 22, 0.0),
(48, 2, 23, 24, 13.8),
(49, 2, 24, 25, 32.4),
(50, 2, 25, 26, 23.2),
(51, 2, 26, 1, 27.5),
(53, 2, 1, 27, 15.3),
(54, 2, 27, 28, 25.2),
(55, 2, 28, 29, 31.1),
(56, 2, 29, 30, 23.0),
(57, 2, 30, 31, 32.6),
(58, 2, 31, 32, 24.9),
(59, 2, 32, 33, 32.9),
(60, 2, 33, 12, 42.9),
(61, 2, 12, 34, 58.0),
(62, 2, 34, 35, 31.7),
(63, 2, 35, 36, 45.4),
(64, 2, 36, 37, 42.1),
(65, 2, 37, 38, 46.4),
(66, 2, 38, 39, 70.7),
(67, 2, 39, 40, 43.1),
(68, 2, 40, 41, 72.9),
(69, 2, 41, 42, 15.9),
(70, 2, 42, 43, 13.9),
(71, 2, 43, 44, 25.1),
(72, 2, 44, 45, 12.2),
(73, 3, 23, 24, 13.8),
(74, 3, 24, 26, 64.6),
(75, 3, 26, 1, 27.5),
(76, 3, 1, 27, 15.3),
(77, 3, 27, 28, 25.2),
(78, 3, 28, 31, 86.7),
(79, 3, 31, 12, 100.7),
(80, 3, 12, 21, 276.2),
(81, 3, 21, 22, 5.0),
(82, 3, 22, 46, 5.0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pasazerowie`
--

CREATE TABLE `pasazerowie` (
  `id_pasazera` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(15) NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasazerowie`
--

INSERT INTO `pasazerowie` (`id_pasazera`, `imie`, `nazwisko`, `email`, `telefon`, `login`, `haslo`) VALUES
(1, 'Jan', 'Kowalski', 'jan.kowalski@example.com', '500123456', 'kowalski_jan', '$2y$10$KOC5.TbWKqNaWsxPUhYCVOd2HgGDOWw8OkuM29Pn1/4B4UOpj3z9C'),
(2, 'Dariusz', 'Nowak', '', '', 'dariusz_nowak', '$2y$10$URssYzJbgFIUMkH7KeLls.98EPhDb.LrsDOQafJ.E8.Z6lmP/2BEO'),
(3, 'Łukasz', 'Radliński', 'lradlinski@lradlinski.pl', '522852752', 'lradlinski', '$2y$10$Di2CGGNkprIeHM9Vi5SufextNkyDpgx7Q.rf7zd5mUpiVR9zCxsUW');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pociagi`
--

CREATE TABLE `pociagi` (
  `id_pociagu` int(11) NOT NULL,
  `numer_pociagu` int(6) NOT NULL,
  `typ` enum('TLK','IC','EIC','EIP') NOT NULL,
  `nazwa` text DEFAULT NULL,
  `od` date NOT NULL,
  `do` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pociagi`
--

INSERT INTO `pociagi` (`id_pociagu`, `numer_pociagu`, `typ`, `nazwa`, `od`, `do`) VALUES
(1, 81170, 'IC', 'UZNAM', '2025-03-09', '2025-06-30'),
(2, 83172, 'IC', 'PRZEMYŚLANIN', '2025-03-09', '2025-06-30'),
(3, 8102, 'EIP', '', '2025-05-06', '2025-08-31');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

CREATE TABLE `pracownicy` (
  `id_pracownika` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `stanowisko` enum('Administrator','Pracownik','Kierownik') NOT NULL,
  `telefon` int(11) NOT NULL,
  `email` text NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `id_kierownika` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pracownicy`
--

INSERT INTO `pracownicy` (`id_pracownika`, `imie`, `nazwisko`, `stanowisko`, `telefon`, `email`, `login`, `haslo`, `id_kierownika`) VALUES
(1, 'Marek', 'Nowak', 'Pracownik', 562875465, 'marek_nowak@marek_nowak.pl', 'marek_nowak', '$2y$10$KOC5.TbWKqNaWsxPUhYCVOd2HgGDOWw8OkuM29Pn1/4B4UOpj3z9C', NULL),
(3, 'Jakub', 'Wierciński', 'Administrator', 452685425, 'wiercinski_jakub@wiercinski_jakub.pl', 'wiercinski_jakub', '$2y$10$KOC5.TbWKqNaWsxPUhYCVOd2HgGDOWw8OkuM29Pn1/4B4UOpj3z9C', 3);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `rozklad_jazdy`
--

CREATE TABLE `rozklad_jazdy` (
  `id_rozkladu` int(11) NOT NULL,
  `id_pociagu` int(11) NOT NULL,
  `id_stacji` int(11) NOT NULL,
  `godzina_przyjazdu` time DEFAULT NULL,
  `godzina_odjazdu` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rozklad_jazdy`
--

INSERT INTO `rozklad_jazdy` (`id_rozkladu`, `id_pociagu`, `id_stacji`, `godzina_przyjazdu`, `godzina_odjazdu`) VALUES
(1, 1, 1, NULL, '23:53:00'),
(2, 1, 2, '00:09:00', '00:10:00'),
(3, 1, 3, '00:29:00', '00:30:00'),
(4, 1, 4, '00:43:00', '00:44:00'),
(5, 1, 5, '01:01:00', '01:02:00'),
(6, 1, 6, '01:24:00', '01:25:00'),
(7, 1, 7, '01:54:00', '01:55:00'),
(8, 1, 8, '02:09:00', '02:10:00'),
(9, 1, 9, '02:16:00', '02:17:00'),
(10, 1, 10, '02:29:00', '02:30:00'),
(11, 1, 11, '02:41:00', '02:42:00'),
(12, 1, 12, '03:11:00', '03:16:00'),
(13, 1, 13, '03:27:00', '03:28:00'),
(14, 1, 14, '03:50:00', '03:51:00'),
(15, 1, 15, '04:05:00', '04:06:00'),
(16, 1, 16, '04:23:00', '04:24:00'),
(17, 1, 17, '04:40:00', '04:41:00'),
(18, 1, 18, '05:10:00', '05:11:00'),
(19, 1, 19, '05:36:00', '05:37:00'),
(20, 1, 20, '05:52:00', '05:53:00'),
(21, 1, 21, '06:25:00', '06:26:00'),
(22, 1, 22, '06:30:00', NULL),
(23, 2, 23, NULL, '18:58:00'),
(24, 2, 24, '19:12:00', '19:14:00'),
(25, 2, 25, '19:35:00', '19:36:00'),
(26, 2, 26, '19:55:00', '19:56:00'),
(28, 2, 1, '20:27:00', '20:32:00'),
(29, 2, 27, '20:46:00', '20:47:00'),
(30, 2, 28, '21:04:00', '21:05:00'),
(31, 2, 29, '21:23:00', '21:24:00'),
(32, 2, 30, '21:39:00', '21:40:00'),
(33, 2, 31, '21:53:00', '21:54:00'),
(34, 2, 32, '22:10:00', '22:11:00'),
(35, 2, 33, '22:21:00', '22:22:00'),
(36, 2, 12, '22:41:00', '23:11:00'),
(37, 2, 34, '23:37:00', '23:38:00'),
(38, 2, 35, '23:55:00', '23:56:00'),
(39, 2, 36, '00:16:00', '00:17:00'),
(40, 2, 37, '00:28:00', '00:29:00'),
(41, 2, 38, '00:43:00', '00:44:00'),
(42, 2, 39, '01:02:00', '01:10:00'),
(43, 2, 40, '03:24:00', '03:26:00'),
(44, 2, 41, '04:06:00', '04:08:00'),
(45, 2, 42, '05:42:00', '05:43:00'),
(46, 2, 43, '05:55:00', '05:56:00'),
(47, 2, 44, '06:06:00', '06:08:00'),
(48, 2, 45, '06:40:00', NULL),
(49, 3, 23, '00:00:00', '06:25:00'),
(50, 3, 24, '06:35:00', '06:36:00'),
(51, 3, 26, '07:12:00', '07:13:00'),
(52, 3, 1, '07:39:00', '07:40:00'),
(53, 3, 27, '07:56:00', '07:57:00'),
(54, 3, 28, '08:11:00', '08:12:00'),
(55, 3, 31, '08:51:00', '08:52:00'),
(56, 3, 12, '09:20:00', '09:32:00'),
(57, 3, 21, '11:52:00', '11:53:00'),
(58, 3, 22, '11:57:00', '11:59:00'),
(59, 3, 46, '12:16:00', '00:00:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sklady_pociagow`
--

CREATE TABLE `sklady_pociagow` (
  `id_skladu` int(11) NOT NULL,
  `id_pociagu` int(11) NOT NULL,
  `nazwa_skladu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sklady_pociagow`
--

INSERT INTO `sklady_pociagow` (`id_skladu`, `id_pociagu`, `nazwa_skladu`) VALUES
(1, 1, 'Skład IC 81170'),
(3, 2, 'Skład IC 83172'),
(4, 3, 'EIP 8102');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stacje`
--

CREATE TABLE `stacje` (
  `id_stacji` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `miasto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stacje`
--

INSERT INTO `stacje` (`id_stacji`, `nazwa`, `miasto`) VALUES
(1, 'Szczecin Główny', 'Szczecin'),
(2, 'Gryfino', 'Gryfino'),
(3, 'Chojna', 'Chojna'),
(4, 'Mieszkowice', 'Mieszkowice'),
(5, 'Kostrzyn', 'Kostrzyn'),
(6, 'Rzepin', 'Rzepin'),
(7, 'Świebodzin', 'Świebodzin'),
(8, 'Zbąszynek', 'Zbąszynek'),
(9, 'Zbąszyń', 'Zbąszyń'),
(10, 'Nowy Tomyśl', 'Nowy Tomyśl'),
(11, 'Opalenica', 'Opalenica'),
(12, 'Poznań Główny', 'Poznań'),
(13, 'Swarzędz', 'Swarzędz'),
(14, 'Września', 'Września'),
(15, 'Słupca', 'Słupca'),
(16, 'Konin', 'Konin'),
(17, 'Koło', 'Koło'),
(18, 'Kutno', 'Kutno'),
(19, 'Łowicz Główny', 'Łowicz'),
(20, 'Sochaczew', 'Sochaczew'),
(21, 'Warszawa Zachodnia', 'Warszawa'),
(22, 'Warszawa Centralna', 'Warszawa'),
(23, 'Świnoujście', 'Świnoujście'),
(24, 'Międzyzdroje', 'Międzyzdroje'),
(25, 'Wysoka Kamieńska', 'Wysoka Kamieńska'),
(26, 'Goleniów', 'Goleniów'),
(27, 'Szczecin Dąbie', 'Szczecin'),
(28, 'Stargard', 'Stargard'),
(29, 'Choszczno', 'Choszczno'),
(30, 'Dobiegniew', 'Dobiegniew'),
(31, 'Krzyż', 'Krzyż'),
(32, 'Wronki', 'Wronki'),
(33, 'Szamotuły', 'Szamotuły'),
(34, 'Kościan', 'Kościan'),
(35, 'Leszno', 'Leszno'),
(36, 'Rawicz', 'Rawicz'),
(37, 'Żmigród', 'Żmigród'),
(38, 'Oborniki Śląskie', 'Oborniki Śląskie'),
(39, 'Wrocław Główny', 'Wrocław'),
(40, 'Kędzierzyn-Koźle', 'Kędzierzyn-Koźle'),
(41, 'Rybnik', 'Rybnik'),
(42, 'Jaworzno Szczakowa', 'Jaworzno'),
(43, 'Trzebinia', 'Trzebinia'),
(44, 'Krzeszowice', 'Krzeszowice'),
(45, 'Kraków Główny', 'Kraków'),
(46, 'Warszawa Wschodnia', 'Warszawa');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `transakcje`
--

CREATE TABLE `transakcje` (
  `id_transakcji` int(11) NOT NULL,
  `id_biletu` int(11) NOT NULL,
  `id_pasazera` int(11) NOT NULL,
  `kwota` decimal(10,2) NOT NULL,
  `metoda_platnosci` enum('BLIK','Karta','Przelew') NOT NULL,
  `status` enum('Zakończona','Oczekująca','Anulowana') NOT NULL DEFAULT 'Oczekująca',
  `data_transakcji` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transakcje`
--

INSERT INTO `transakcje` (`id_transakcji`, `id_biletu`, `id_pasazera`, `kwota`, `metoda_platnosci`, `status`, `data_transakcji`) VALUES
(1, 1, 1, 129.99, 'Karta', 'Zakończona', '2025-03-19 16:45:25'),
(4, 26, 2, 64.99, 'BLIK', '', '2025-03-19 22:00:48'),
(5, 27, 2, 20.00, 'Karta', '', '2025-03-19 22:52:31'),
(6, 28, 2, 63.92, 'Karta', '', '2025-03-19 23:02:56'),
(7, 29, 2, 4.00, 'Karta', '', '2025-03-20 13:53:59'),
(8, 30, 2, 9.90, 'Karta', '', '2025-03-20 14:57:25'),
(9, 31, 2, 85.00, 'BLIK', '', '2025-03-20 15:06:11'),
(10, 32, 2, 7.50, 'BLIK', '', '2025-03-20 18:39:17'),
(11, 33, 2, 0.01, 'BLIK', '', '2025-05-05 11:20:46'),
(12, 34, 2, 27.00, 'Karta', '', '2025-05-14 13:53:03');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wagony`
--

CREATE TABLE `wagony` (
  `id_wagonu` int(11) NOT NULL,
  `id_skladu` int(11) NOT NULL,
  `numer_wagonu` int(11) NOT NULL,
  `typ` enum('Sypialny','Przedziałowy','Bezprzedziałowy','Restauracyjny') NOT NULL,
  `klasa` int(1) NOT NULL,
  `liczba_miejsc` int(11) NOT NULL,
  `miejsce_od` int(11) NOT NULL,
  `miejsce_do` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wagony`
--

INSERT INTO `wagony` (`id_wagonu`, `id_skladu`, `numer_wagonu`, `typ`, `klasa`, `liczba_miejsc`, `miejsce_od`, `miejsce_do`) VALUES
(1, 1, 1, 'Sypialny', 1, 60, 1, 60),
(2, 1, 2, 'Sypialny', 1, 60, 61, 120),
(3, 1, 3, 'Przedziałowy', 2, 72, 1, 72),
(4, 1, 4, 'Przedziałowy', 2, 72, 73, 144),
(5, 1, 5, 'Przedziałowy', 2, 72, 145, 216),
(6, 1, 6, 'Bezprzedziałowy', 1, 72, 1, 72),
(7, 1, 7, 'Bezprzedziałowy', 2, 72, 73, 144),
(8, 3, 1, 'Sypialny', 1, 60, 1, 60),
(9, 3, 2, 'Sypialny', 1, 60, 61, 120),
(10, 3, 3, 'Przedziałowy', 2, 72, 1, 72),
(11, 3, 4, 'Przedziałowy', 2, 72, 73, 144),
(12, 3, 5, 'Przedziałowy', 1, 72, 1, 72),
(13, 3, 6, 'Bezprzedziałowy', 2, 72, 73, 144),
(14, 3, 7, '', 2, 72, 145, 216),
(15, 4, 1, 'Bezprzedziałowy', 1, 45, 1, 45),
(16, 4, 2, 'Bezprzedziałowy', 2, 56, 46, 102),
(17, 4, 3, 'Bezprzedziałowy', 2, 2, 103, 104);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `znizki`
--

CREATE TABLE `znizki` (
  `id_znizki` int(11) NOT NULL,
  `nazwa_znizki` varchar(255) NOT NULL,
  `wymiar_znizki` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `znizki`
--

INSERT INTO `znizki` (`id_znizki`, `nazwa_znizki`, `wymiar_znizki`) VALUES
(1, 'Normalny', 0.00),
(2, 'Student (do 26 lat)', 50.00),
(3, 'Senior 60+', 25.00),
(4, 'Uczeń (do 24 lat)', 37.00),
(5, 'Dziecko/ współpracownik pracownika kolei', 80.00),
(6, 'Pracownik kolei, usługa transportowa', 99.00),
(7, 'Doktorant (do 35 lat)', 51.00),
(8, 'Działacz opozycji antykomunistycznej/ osoba represjonowana z powodów politycznych', 51.00),
(9, 'Dzieci i młodzież z niepełnosprawnością', 78.00),
(10, 'Dziecko do lat 4', 100.00),
(11, 'Dziecko do rozpoczęcia szkoły', 37.00),
(12, 'Emeryt/ rencista (tylko 2 razy w roku- ważne z zaświadczeniem!)', 37.00),
(13, 'Inwalida wojenny 9I grupa inwalidów)', 78.00),
(14, 'Inwalida wojenny (II i III grupa inwalidów)', 37.00),
(15, 'Karta Dużej Rodziny (KDR Rodzic)', 37.00),
(16, 'Karta Polaka', 37.00),
(17, 'Kombatant będący inwalidą wojennym lub wojskowym I grupy', 78.00),
(18, 'Kombatant będący inwalidą wojennym lub wojskowym  II i III grupy', 51.00),
(19, 'Międzynarodowa Karta Zniżek FIP', 50.00),
(20, 'Nauczyciel szkolny lub akademicki', 33.00),
(21, 'Niewidoma ofiara działań wojennych niezdolna do samodzielnej egzystencji', 78.00),
(22, 'Niewidoma ofiara działań wojennych niezdolna do pracy', 37.00),
(23, 'Niewidomy- niezdolny do samodzielnej egzystencji', 93.00),
(24, 'Niewidomy- stopień umiarkowany', 37.00),
(25, 'Opiekun dziecka niepełnosprawnego', 78.00),
(26, 'Opiekun inwalidy wojennego/ wojskowego I grupy', 95.00),
(27, 'Opiekun/ przewodnik niepełnosprawnego I grupy/ niewidomego', 95.00),
(28, 'Osoba niezdolna do samodzielnej egzystencji z wyjątkiem niewidomych', 49.00),
(29, 'Weteran poszkodowany', 37.00),
(30, 'Żołnierz niezawodowy', 78.00),
(31, 'Emeryt/ rencista kolejowy, usługa transportowa', 99.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zwroty`
--

CREATE TABLE `zwroty` (
  `id_zwrotu` int(11) NOT NULL,
  `id_biletu` int(11) NOT NULL,
  `id_pasazera` int(11) NOT NULL,
  `status` enum('Zaakceptowany','Odrzucony','Oczekujący') NOT NULL DEFAULT 'Oczekujący',
  `data_zwrotu` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_pracownika` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `bilety`
--
ALTER TABLE `bilety`
  ADD PRIMARY KEY (`id_biletu`),
  ADD UNIQUE KEY `kod_qr` (`kod_qr`),
  ADD KEY `id_pasazera` (`id_pasazera`),
  ADD KEY `id_pociagu` (`id_pociagu`),
  ADD KEY `id_stacji_start` (`id_stacji_start`),
  ADD KEY `id_stacji_koniec` (`id_stacji_koniec`),
  ADD KEY `id_wagonu` (`id_wagonu`),
  ADD KEY `fk_bilety_znizki` (`id_znizki`);

--
-- Indeksy dla tabeli `ceny_odleglosci`
--
ALTER TABLE `ceny_odleglosci`
  ADD PRIMARY KEY (`id_ceny`);

--
-- Indeksy dla tabeli `odleglosci_miedzy_stacjami`
--
ALTER TABLE `odleglosci_miedzy_stacjami`
  ADD PRIMARY KEY (`id_odleglosci`),
  ADD KEY `id_pociagu` (`id_pociagu`),
  ADD KEY `id_stacji_poczatek` (`id_stacji_poczatek`),
  ADD KEY `id_stacji_koniec` (`id_stacji_koniec`);

--
-- Indeksy dla tabeli `pasazerowie`
--
ALTER TABLE `pasazerowie`
  ADD PRIMARY KEY (`id_pasazera`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefon` (`telefon`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indeksy dla tabeli `pociagi`
--
ALTER TABLE `pociagi`
  ADD PRIMARY KEY (`id_pociagu`),
  ADD UNIQUE KEY `numer_pociagu` (`numer_pociagu`);

--
-- Indeksy dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`id_pracownika`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `id_kierownika` (`id_kierownika`);

--
-- Indeksy dla tabeli `rozklad_jazdy`
--
ALTER TABLE `rozklad_jazdy`
  ADD PRIMARY KEY (`id_rozkladu`),
  ADD KEY `id_pociagu` (`id_pociagu`),
  ADD KEY `id_stacji` (`id_stacji`);

--
-- Indeksy dla tabeli `sklady_pociagow`
--
ALTER TABLE `sklady_pociagow`
  ADD PRIMARY KEY (`id_skladu`),
  ADD KEY `id_pociagu` (`id_pociagu`);

--
-- Indeksy dla tabeli `stacje`
--
ALTER TABLE `stacje`
  ADD PRIMARY KEY (`id_stacji`),
  ADD UNIQUE KEY `nazwa` (`nazwa`);

--
-- Indeksy dla tabeli `transakcje`
--
ALTER TABLE `transakcje`
  ADD PRIMARY KEY (`id_transakcji`),
  ADD KEY `id_biletu` (`id_biletu`),
  ADD KEY `id_pasazera` (`id_pasazera`);

--
-- Indeksy dla tabeli `wagony`
--
ALTER TABLE `wagony`
  ADD PRIMARY KEY (`id_wagonu`),
  ADD KEY `id_skladu` (`id_skladu`);

--
-- Indeksy dla tabeli `znizki`
--
ALTER TABLE `znizki`
  ADD PRIMARY KEY (`id_znizki`);

--
-- Indeksy dla tabeli `zwroty`
--
ALTER TABLE `zwroty`
  ADD PRIMARY KEY (`id_zwrotu`),
  ADD KEY `id_biletu` (`id_biletu`),
  ADD KEY `id_pasazera` (`id_pasazera`),
  ADD KEY `id_pracownika` (`id_pracownika`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bilety`
--
ALTER TABLE `bilety`
  MODIFY `id_biletu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `ceny_odleglosci`
--
ALTER TABLE `ceny_odleglosci`
  MODIFY `id_ceny` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `odleglosci_miedzy_stacjami`
--
ALTER TABLE `odleglosci_miedzy_stacjami`
  MODIFY `id_odleglosci` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `pasazerowie`
--
ALTER TABLE `pasazerowie`
  MODIFY `id_pasazera` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pociagi`
--
ALTER TABLE `pociagi`
  MODIFY `id_pociagu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id_pracownika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rozklad_jazdy`
--
ALTER TABLE `rozklad_jazdy`
  MODIFY `id_rozkladu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `sklady_pociagow`
--
ALTER TABLE `sklady_pociagow`
  MODIFY `id_skladu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `stacje`
--
ALTER TABLE `stacje`
  MODIFY `id_stacji` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `transakcje`
--
ALTER TABLE `transakcje`
  MODIFY `id_transakcji` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wagony`
--
ALTER TABLE `wagony`
  MODIFY `id_wagonu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `znizki`
--
ALTER TABLE `znizki`
  MODIFY `id_znizki` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `zwroty`
--
ALTER TABLE `zwroty`
  MODIFY `id_zwrotu` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bilety`
--
ALTER TABLE `bilety`
  ADD CONSTRAINT `bilety_ibfk_1` FOREIGN KEY (`id_pasazera`) REFERENCES `pasazerowie` (`id_pasazera`) ON DELETE CASCADE,
  ADD CONSTRAINT `bilety_ibfk_2` FOREIGN KEY (`id_pociagu`) REFERENCES `pociagi` (`id_pociagu`) ON DELETE CASCADE,
  ADD CONSTRAINT `bilety_ibfk_3` FOREIGN KEY (`id_stacji_start`) REFERENCES `stacje` (`id_stacji`) ON DELETE CASCADE,
  ADD CONSTRAINT `bilety_ibfk_4` FOREIGN KEY (`id_stacji_koniec`) REFERENCES `stacje` (`id_stacji`) ON DELETE CASCADE,
  ADD CONSTRAINT `bilety_ibfk_5` FOREIGN KEY (`id_wagonu`) REFERENCES `wagony` (`id_wagonu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bilety_znizki` FOREIGN KEY (`id_znizki`) REFERENCES `znizki` (`id_znizki`);

--
-- Constraints for table `odleglosci_miedzy_stacjami`
--
ALTER TABLE `odleglosci_miedzy_stacjami`
  ADD CONSTRAINT `odleglosci_miedzy_stacjami_ibfk_1` FOREIGN KEY (`id_pociagu`) REFERENCES `pociagi` (`id_pociagu`),
  ADD CONSTRAINT `odleglosci_miedzy_stacjami_ibfk_2` FOREIGN KEY (`id_stacji_poczatek`) REFERENCES `stacje` (`id_stacji`),
  ADD CONSTRAINT `odleglosci_miedzy_stacjami_ibfk_3` FOREIGN KEY (`id_stacji_koniec`) REFERENCES `stacje` (`id_stacji`);

--
-- Constraints for table `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD CONSTRAINT `pracownicy_ibfk_1` FOREIGN KEY (`id_kierownika`) REFERENCES `pracownicy` (`id_pracownika`);

--
-- Constraints for table `rozklad_jazdy`
--
ALTER TABLE `rozklad_jazdy`
  ADD CONSTRAINT `rozklad_jazdy_ibfk_1` FOREIGN KEY (`id_pociagu`) REFERENCES `pociagi` (`id_pociagu`) ON DELETE CASCADE,
  ADD CONSTRAINT `rozklad_jazdy_ibfk_2` FOREIGN KEY (`id_stacji`) REFERENCES `stacje` (`id_stacji`) ON DELETE CASCADE;

--
-- Constraints for table `sklady_pociagow`
--
ALTER TABLE `sklady_pociagow`
  ADD CONSTRAINT `sklady_pociagow_ibfk_1` FOREIGN KEY (`id_pociagu`) REFERENCES `pociagi` (`id_pociagu`) ON DELETE CASCADE;

--
-- Constraints for table `transakcje`
--
ALTER TABLE `transakcje`
  ADD CONSTRAINT `transakcje_ibfk_1` FOREIGN KEY (`id_biletu`) REFERENCES `bilety` (`id_biletu`) ON DELETE CASCADE,
  ADD CONSTRAINT `transakcje_ibfk_2` FOREIGN KEY (`id_pasazera`) REFERENCES `pasazerowie` (`id_pasazera`) ON DELETE CASCADE;

--
-- Constraints for table `wagony`
--
ALTER TABLE `wagony`
  ADD CONSTRAINT `wagony_ibfk_1` FOREIGN KEY (`id_skladu`) REFERENCES `sklady_pociagow` (`id_skladu`) ON DELETE CASCADE;

--
-- Constraints for table `zwroty`
--
ALTER TABLE `zwroty`
  ADD CONSTRAINT `zwroty_ibfk_1` FOREIGN KEY (`id_biletu`) REFERENCES `bilety` (`id_biletu`) ON DELETE CASCADE,
  ADD CONSTRAINT `zwroty_ibfk_2` FOREIGN KEY (`id_pasazera`) REFERENCES `pasazerowie` (`id_pasazera`) ON DELETE CASCADE,
  ADD CONSTRAINT `zwroty_ibfk_3` FOREIGN KEY (`id_pracownika`) REFERENCES `pracownicy` (`id_pracownika`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
