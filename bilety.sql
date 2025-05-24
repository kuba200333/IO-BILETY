-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Maj 07, 2025 at 06:11 PM
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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

CREATE TABLE `pracownicy` (
  `id_pracownika` int(11) NOT NULL,
  `imie` varchar(50) NOT NULL,
  `nazwisko` varchar(50) NOT NULL,
  `stanowisko` enum('Administrator','Kontroler','Kasjer') NOT NULL,
  `login` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sklady_pociagow`
--

CREATE TABLE `sklady_pociagow` (
  `id_skladu` int(11) NOT NULL,
  `id_pociagu` int(11) NOT NULL,
  `nazwa_skladu` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `stacje`
--

CREATE TABLE `stacje` (
  `id_stacji` int(11) NOT NULL,
  `nazwa` varchar(100) NOT NULL,
  `miasto` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `znizki`
--

CREATE TABLE `znizki` (
  `id_znizki` int(11) NOT NULL,
  `nazwa_znizki` varchar(255) NOT NULL,
  `wymiar_znizki` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD UNIQUE KEY `login` (`login`);

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
  MODIFY `id_biletu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ceny_odleglosci`
--
ALTER TABLE `ceny_odleglosci`
  MODIFY `id_ceny` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `odleglosci_miedzy_stacjami`
--
ALTER TABLE `odleglosci_miedzy_stacjami`
  MODIFY `id_odleglosci` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pasazerowie`
--
ALTER TABLE `pasazerowie`
  MODIFY `id_pasazera` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pociagi`
--
ALTER TABLE `pociagi`
  MODIFY `id_pociagu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id_pracownika` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rozklad_jazdy`
--
ALTER TABLE `rozklad_jazdy`
  MODIFY `id_rozkladu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sklady_pociagow`
--
ALTER TABLE `sklady_pociagow`
  MODIFY `id_skladu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stacje`
--
ALTER TABLE `stacje`
  MODIFY `id_stacji` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transakcje`
--
ALTER TABLE `transakcje`
  MODIFY `id_transakcji` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wagony`
--
ALTER TABLE `wagony`
  MODIFY `id_wagonu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `znizki`
--
ALTER TABLE `znizki`
  MODIFY `id_znizki` int(11) NOT NULL AUTO_INCREMENT;

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
