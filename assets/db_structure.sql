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
  `id`                                  INT(11)      NOT NULL,
  `ris_id`                              INT(11) DEFAULT NULL,
  `titel`                               VARCHAR(200) NOT NULL,
  `typ`                                 VARCHAR(50)  NOT NULL,
  `antrags_nr`                          VARCHAR(50)  NOT NULL,
  `gestellt_am`                         DATE         NOT NULL,
  `bearbeitungsfrist`                   DATE         NOT NULL,
  `bearbeitungsfrist_benachrichtigung`  DATE    DEFAULT NULL,
  `fristverlaengerung`                  DATE    DEFAULT NULL,
  `fristverlaengerung_benachrichtigung` DATE    DEFAULT NULL,
  `status`                              VARCHAR(150) NOT NULL,
  `notiz`                               TEXT         NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `antraege_stadtraetinnen`
--

CREATE TABLE `antraege_stadtraetinnen` (
  `antrag_id`      INT(11) NOT NULL,
  `stadtraetin_id` INT(11) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `email`
--

CREATE TABLE IF NOT EXISTS `email` (
  `id`        INT(11)      NOT NULL,
  `toEmail`   VARCHAR(200)      DEFAULT NULL,
  `dateSent`  TIMESTAMP    NULL DEFAULT NULL,
  `subject`   VARCHAR(200)      DEFAULT NULL,
  `text`      MEDIUMTEXT,
  `messageId` VARCHAR(100) NOT NULL,
  `status`    SMALLINT(6)  NOT NULL,
  `error`     TEXT
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `antraege_tags`
--

CREATE TABLE `antraege_tags` (
  `antrag_id` INT(11) NOT NULL,
  `tag_id`    INT(11) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `stadtraetinnen`
--

CREATE TABLE `stadtraetinnen` (
  `id`                INT(11)      NOT NULL,
  `ris_id`            INT(11)      NOT NULL,
  `name`              VARCHAR(100) NOT NULL,
  `fraktionsmitglied` TINYINT(4)   NOT NULL DEFAULT '0'
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id`   INT(11)      NOT NULL,
  `name` VARCHAR(100) NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

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
ADD PRIMARY KEY (`antrag_id`, `stadtraetin_id`),
ADD KEY `stadtraetin_id` (`stadtraetin_id`);

--
-- Indexes for table `antraege_tags`
--
ALTER TABLE `antraege_tags`
ADD PRIMARY KEY (`antrag_id`, `tag_id`),
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
-- Indexes for table `emailLog`
--
ALTER TABLE `email`
ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `antraege`
--
ALTER TABLE `antraege`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 6;
--
-- AUTO_INCREMENT for table `stadtraetinnen`
--
ALTER TABLE `stadtraetinnen`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT = 4;
--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `emailLog`
--
ALTER TABLE `email`
MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;

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
