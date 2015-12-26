-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 26, 2015 at 04:59 PM
-- Server version: 5.7.10
-- PHP Version: 7.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `str_antraege`
--

-- --------------------------------------------------------

--
-- Table structure for table `antraege`
--

CREATE TABLE `antraege` (
  `id` int(11) NOT NULL,
  `ris_id` int(11) DEFAULT NULL,
  `titel` varchar(150) NOT NULL,
  `antrags_nr` varchar(50) NOT NULL,
  `gestellt_am` date NOT NULL,
  `bearbeitungsfrist` date NOT NULL,
  `fristverlaengerung` date DEFAULT NULL,
  `status` varchar(150) NOT NULL,
  `notiz` text NOT NULL,
  `benachrichtigung` date DEFAULT NULL
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
  `name` varchar(100) NOT NULL
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `stadtraetinnen`
--
ALTER TABLE `stadtraetinnen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
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
