-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 23, 2016 at 01:46 PM
-- Server version: 5.7.10
-- PHP Version: 7.0.2-1~dotdeb+8.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `stadtratsantraege`
--

-- --------------------------------------------------------

--
-- Table structure for table `antraege`
--

CREATE TABLE `antraege` (
  `id` int(11) NOT NULL,
  `ris_id` int(11) DEFAULT NULL,
  `titel` varchar(200) NOT NULL,
  `typ` varchar(50) NOT NULL,
  `antrags_nr` varchar(50) NOT NULL,
  `gestellt_am` date NOT NULL,
  `bearbeitungsfrist` date NOT NULL,
  `bearbeitungsfrist_benachrichtigung` date DEFAULT NULL,
  `fristverlaengerung` date DEFAULT NULL,
  `fristverlaengerung_benachrichtigung` date DEFAULT NULL,
  `erledigt_am` date DEFAULT NULL,
  `status` varchar(150) NOT NULL,
  `status_override` varchar(150) NOT NULL,
  `notiz` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `antraege_initiatorinnen`
--

CREATE TABLE `antraege_initiatorinnen` (
  `antrag_id` int(11) NOT NULL,
  `stadtraetin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `antraege_stadtraetinnen`
--

CREATE TABLE `antraege_stadtraetinnen` (
  `antrag_id` int(11) NOT NULL,
  `stadtraetin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `antraege_tags`
--

CREATE TABLE `antraege_tags` (
  `antrag_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `stadtraetinnen`
--

CREATE TABLE `stadtraetinnen` (
  `id` int(11) NOT NULL,
  `ris_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `fraktionsmitglied` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `antraege`
--
ALTER TABLE `antraege`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ris_id` (`ris_id`);

--
-- Indexes for table `antraege_initiatorinnen`
--
ALTER TABLE `antraege_initiatorinnen`
  ADD PRIMARY KEY (`antrag_id`,`stadtraetin_id`),
  ADD KEY `stadtraetin_id` (`stadtraetin_id`);

--
-- Indexes for table `antraege_stadtraetinnen`
--
ALTER TABLE `antraege_stadtraetinnen`
  ADD PRIMARY KEY (`antrag_id`,`stadtraetin_id`),
  ADD KEY `stadtraetin_id` (`stadtraetin_id`);

--
-- Indexes for table `antraege_tags`
--
ALTER TABLE `antraege_tags`
  ADD PRIMARY KEY (`antrag_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `stadtraetinnen`
--
ALTER TABLE `stadtraetinnen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ris_id` (`ris_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antraege`
--
ALTER TABLE `antraege`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1832;
--
-- AUTO_INCREMENT for table `stadtraetinnen`
--
ALTER TABLE `stadtraetinnen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `antraege_stadtraetinnen`
--
ALTER TABLE `antraege_stadtraetinnen`
  ADD CONSTRAINT `antraege_stadtraetinnen_ibfk_1` FOREIGN KEY (`antrag_id`) REFERENCES `antraege` (`id`),
  ADD CONSTRAINT `antraege_stadtraetinnen_ibfk_2` FOREIGN KEY (`stadtraetin_id`) REFERENCES `stadtraetinnen` (`id`);

--
-- Constraints for table `antraege_tags`
--
ALTER TABLE `antraege_tags`
  ADD CONSTRAINT `antraege_tags_ibfk_1` FOREIGN KEY (`antrag_id`) REFERENCES `antraege` (`id`),
  ADD CONSTRAINT `antraege_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);
