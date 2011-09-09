-- --------------------------------------------------------

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wow_character`
--

DROP TABLE IF EXISTS `wow_character`;
CREATE TABLE `wow_character` (
  `locale` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  `realm` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `wdo` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  UNIQUE KEY `locale` (`locale`,`region`,`realm`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wow_character_thumbnail`
--

DROP TABLE IF EXISTS `wow_character_thumbnail`;
CREATE TABLE `wow_character_thumbnail` (
  `region` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `image` blob NOT NULL,
  `wdo` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  UNIQUE KEY `locale` (`region`,`filename`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wow_icon`
--

DROP TABLE IF EXISTS `wow_icon`;
CREATE TABLE `wow_icon` (
  `region` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `image` blob NOT NULL,
  `wdo` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`region`,`icon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wow_item`
--

DROP TABLE IF EXISTS `wow_item`;
CREATE TABLE `wow_item` (
  `id` int(10) unsigned NOT NULL,
  `locale` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `qid` int(4) NOT NULL,
  `cid` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `rlvl` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `ilvl` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `wdo` text COLLATE utf8_unicode_ci NOT NULL,
  `bonding` int(4) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`locale`,`region`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wow_item_equipped`
--

DROP TABLE IF EXISTS `wow_item_equipped`;
CREATE TABLE `wow_item_equipped` (
  `id` int(10) unsigned NOT NULL,
  `locale` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `qid` int(4) NOT NULL,
  `cid` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `rlvl` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `ilvl` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `xmlinfo` text COLLATE utf8_unicode_ci NOT NULL,
  `xmltooltip` text COLLATE utf8_unicode_ci NOT NULL,
  `bonding` int(4) NOT NULL,
  `realm` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `character` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slot` int(4) NOT NULL,
  `wdo` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wow_realm`
--

DROP TABLE IF EXISTS `wow_realm`;
CREATE TABLE `wow_realm` (
  `region` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `population` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `queue` tinyint(3) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `wdo` text COLLATE utf8_unicode_ci NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  PRIMARY KEY (`region`,`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------
