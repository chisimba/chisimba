-- phpMyAdmin SQL Dump
-- version 2.7.0-pl2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 15, 2006 at 10:42 AM
-- Server version: 5.0.18
-- PHP Version: 5.1.2-1.dotdeb.2
-- 
-- Database: `php5alpha`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_calendar`
-- 

CREATE TABLE `tbl_calendar` (
  `id` char(32) default NULL,
  `multiday_event` char(1) NOT NULL default '0',
  `eventdate` date NOT NULL default '0000-00-00',
  `multiday_event_start_id` text,
  `eventtile` char(100) default NULL,
  `eventdetails` text,
  `eventurl` char(100) default NULL,
  `userorcontext` char(1) default NULL,
  `context` char(32) default NULL,
  `workgroup` char(32) default NULL,
  `showusers` char(1) default NULL,
  `userFirstEntry` char(32) default NULL,
  `userLastModified` char(32) default NULL,
  `dateFirstEntry` date NOT NULL default '0000-00-00',
  `dateLastModified` date NOT NULL default '0000-00-00',
  `updated` date NOT NULL default '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='blag';

-- 
-- Dumping data for table `tbl_calendar`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context`
-- 

CREATE TABLE `tbl_context` (
  `id` varchar(32) NOT NULL default 'init',
  `contextCode` varchar(255) NOT NULL default 'init',
  `title` text NOT NULL,
  `menutext` varchar(255) default NULL,
  `about` text,
  `userid` varchar(255) NOT NULL default 'init',
  `dateCreated` date default NULL,
  `isClosed` int(11) default NULL,
  `isActive` int(11) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`,`contextCode`),
  KEY `contextCode` (`contextCode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='blag';

-- 
-- Dumping data for table `tbl_context`
-- 

INSERT INTO `tbl_context` (`id`, `contextCode`, `title`, `menutext`, `about`, `userid`, `dateCreated`, `isClosed`, `isActive`, `updated`) VALUES ('gen6Srv42Nme32_1', 'FOSS', 'Long Live the Code!', 'Long Live the Code!', 'wervqevqewv', '', '2006-03-02', NULL, 1, '2006-03-02 07:17:42');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_file`
-- 

CREATE TABLE `tbl_context_file` (
  `id` varchar(32) NOT NULL default 'init',
  `tbl_context_parentnodes_id` varchar(32) NOT NULL default 'init',
  `datatype` varchar(60) default NULL,
  `title` varchar(120) default NULL,
  `description` varchar(255) default NULL,
  `version` varchar(60) default NULL,
  `name` varchar(120) default NULL,
  `size` bigint(20) default NULL,
  `filedate` datetime default NULL,
  `path` varchar(255) default NULL,
  `category` varchar(32) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`,`tbl_context_parentnodes_id`),
  KEY `tbl_context_file_FKIndex1` (`tbl_context_parentnodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_file`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_filedata`
-- 

CREATE TABLE `tbl_context_filedata` (
  `id` varchar(32) NOT NULL default 'init',
  `tbl_context_file_tbl_context_parentnodes_id` varchar(32) NOT NULL default 'init',
  `tbl_context_file_id` varchar(32) NOT NULL default 'init',
  `filedata` blob,
  `segment` int(11) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `tbl_context_filedata_FKIndex1` (`tbl_context_file_id`,`tbl_context_file_tbl_context_parentnodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_filedata`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_nodes`
-- 

CREATE TABLE `tbl_context_nodes` (
  `id` varchar(32) NOT NULL default 'init',
  `tbl_context_parentnodes_id` varchar(32) NOT NULL default 'init',
  `parent_Node` varchar(32) default NULL,
  `prev_Node` varchar(32) default NULL,
  `next_Node` varchar(32) default NULL,
  `title` varchar(255) default NULL,
  `script` text,
  `sortindex` int(11) default '1',
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `tbl_context_nodes_FKIndex1` (`tbl_context_parentnodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_nodes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_page_content`
-- 

CREATE TABLE `tbl_context_page_content` (
  `id` varchar(32) NOT NULL default 'init',
  `tbl_context_nodes_id` varchar(32) NOT NULL default 'init',
  `menu_text` varchar(255) default NULL,
  `body` longtext,
  `fullname` varchar(255) default NULL,
  `description` mediumtext,
  `isIndexPage` varchar(20) default NULL,
  `ownerId` varchar(255) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `tbl_context_page_content_FKIndex1` (`tbl_context_nodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_page_content`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_parentnodes`
-- 

CREATE TABLE `tbl_context_parentnodes` (
  `id` varchar(32) NOT NULL default 'init',
  `tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode` varchar(255) NOT NULL default 'init',
  `tbl_context_parentnodes_has_tbl_context_tbl_context_id` varchar(32) NOT NULL default 'init',
  `userId` varchar(255) default NULL,
  `dateCreated` date default NULL,
  `datemodified` date default NULL,
  `menu_text` varchar(255) default NULL,
  `title` varchar(255) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `tbl_context_parentnodes_FKIndex1` (`tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode`,`tbl_context_parentnodes_has_tbl_context_tbl_context_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_parentnodes`
-- 

INSERT INTO `tbl_context_parentnodes` (`id`, `tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode`, `tbl_context_parentnodes_has_tbl_context_tbl_context_id`, `userId`, `dateCreated`, `datemodified`, `menu_text`, `title`, `updated`) VALUES ('gen6Srv42Nme32_1', 'FOSS', 'gen6Srv42Nme32_1', '1', '2006-03-02', '2006-03-02', 'Long Live the Code!', 'Long Live the Code!', '2006-03-02 07:17:42');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_parentnodes_has_tbl_context`
-- 

CREATE TABLE `tbl_context_parentnodes_has_tbl_context` (
  `tbl_context_contextCode` varchar(255) NOT NULL default 'init',
  `tbl_context_id` varchar(32) NOT NULL default 'init',
  `id` varchar(32) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`tbl_context_contextCode`,`tbl_context_id`),
  KEY `tbl_context_has_tbl_context_parentnodes_FKIndex1` (`tbl_context_id`,`tbl_context_contextCode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_parentnodes_has_tbl_context`
-- 

INSERT INTO `tbl_context_parentnodes_has_tbl_context` (`tbl_context_contextCode`, `tbl_context_id`, `id`, `updated`) VALUES ('FOSS', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_1', '2006-03-02 07:17:42');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_parentnodes_has_tbl_context_seq`
-- 

CREATE TABLE `tbl_context_parentnodes_has_tbl_context_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `tbl_context_parentnodes_has_tbl_context_seq`
-- 

INSERT INTO `tbl_context_parentnodes_has_tbl_context_seq` (`sequence`) VALUES (1);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_parentnodes_seq`
-- 

CREATE TABLE `tbl_context_parentnodes_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `tbl_context_parentnodes_seq`
-- 

INSERT INTO `tbl_context_parentnodes_seq` (`sequence`) VALUES (1);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_seq`
-- 

CREATE TABLE `tbl_context_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `tbl_context_seq`
-- 

INSERT INTO `tbl_context_seq` (`sequence`) VALUES (1);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_sharednodes`
-- 

CREATE TABLE `tbl_context_sharednodes` (
  `id` varchar(32) NOT NULL default 'init',
  `shared_nodeid` varchar(32) default NULL,
  `root_nodeid` varchar(32) NOT NULL default 'init',
  `nodeid` varchar(32) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_context_sharednodes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_context_temp`
-- 

CREATE TABLE `tbl_context_temp` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` varchar(32) default NULL,
  `filetype` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `size` int(255) default NULL,
  `filedata` longblob,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `tbl_context_temp`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_contextmodules`
-- 

CREATE TABLE `tbl_contextmodules` (
  `id` varchar(32) NOT NULL default 'init',
  `contextCode` varchar(32) NOT NULL default 'init',
  `moduleId` varchar(50) NOT NULL default 'init',
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`,`contextCode`,`moduleId`),
  KEY `tbl_contextmodules_FKIndex1` (`contextCode`),
  KEY `tbl_contextmodules_FKIndex2` (`moduleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_contextmodules`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_country`
-- 

CREATE TABLE `tbl_country` (
  `iso` char(2) NOT NULL default 'za',
  `name` varchar(80) default NULL,
  `printable_name` varchar(80) default NULL,
  `iso3` char(3) default NULL,
  `numcode` smallint(6) default NULL,
  `updated` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`iso`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- 
-- Dumping data for table `tbl_country`
-- 

INSERT INTO `tbl_country` (`iso`, `name`, `printable_name`, `iso3`, `numcode`, `updated`) VALUES ('AD', 'ANDORRA', 'Andorra', 'AND', 20, '2006-03-02 06:43:15'),
('AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, '2006-03-02 06:43:15'),
('AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, '2006-03-02 06:43:15'),
('AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, '2006-03-02 06:43:15'),
('AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, '2006-03-02 06:43:15'),
('AL', 'ALBANIA', 'Albania', 'ALB', 8, '2006-03-02 06:43:15'),
('AM', 'ARMENIA', 'Armenia', 'ARM', 51, '2006-03-02 06:43:15'),
('AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, '2006-03-02 06:43:15'),
('AO', 'ANGOLA', 'Angola', 'AGO', 24, '2006-03-02 06:43:15'),
('AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, '2006-03-02 06:43:15'),
('AR', 'ARGENTINA', 'Argentina', 'ARG', 32, '2006-03-02 06:43:15'),
('AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, '2006-03-02 06:43:15'),
('AT', 'AUSTRIA', 'Austria', 'AUT', 40, '2006-03-02 06:43:15'),
('AU', 'AUSTRALIA', 'Australia', 'AUS', 36, '2006-03-02 06:43:15'),
('AW', 'ARUBA', 'Aruba', 'ABW', 533, '2006-03-02 06:43:15'),
('AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, '2006-03-02 06:43:15'),
('BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, '2006-03-02 06:43:15'),
('BB', 'BARBADOS', 'Barbados', 'BRB', 52, '2006-03-02 06:43:15'),
('BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, '2006-03-02 06:43:15'),
('BE', 'BELGIUM', 'Belgium', 'BEL', 56, '2006-03-02 06:43:15'),
('BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, '2006-03-02 06:43:15'),
('BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, '2006-03-02 06:43:15'),
('BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, '2006-03-02 06:43:15'),
('BI', 'BURUNDI', 'Burundi', 'BDI', 108, '2006-03-02 06:43:15'),
('BJ', 'BENIN', 'Benin', 'BEN', 204, '2006-03-02 06:43:15'),
('BM', 'BERMUDA', 'Bermuda', 'BMU', 60, '2006-03-02 06:43:15'),
('BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, '2006-03-02 06:43:15'),
('BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, '2006-03-02 06:43:15'),
('BR', 'BRAZIL', 'Brazil', 'BRA', 76, '2006-03-02 06:43:15'),
('BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, '2006-03-02 06:43:15'),
('BT', 'BHUTAN', 'Bhutan', 'BTN', 64, '2006-03-02 06:43:15'),
('BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, '2006-03-02 06:43:15'),
('BW', 'BOTSWANA', 'Botswana', 'BWA', 72, '2006-03-02 06:43:15'),
('BY', 'BELARUS', 'Belarus', 'BLR', 112, '2006-03-02 06:43:15'),
('BZ', 'BELIZE', 'Belize', 'BLZ', 84, '2006-03-02 06:43:15'),
('CA', 'CANADA', 'Canada', 'CAN', 124, '2006-03-02 06:43:15'),
('CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, '2006-03-02 06:43:15'),
('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, '2006-03-02 06:43:15'),
('CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, '2006-03-02 06:43:15'),
('CG', 'CONGO', 'Congo', 'COG', 178, '2006-03-02 06:43:15'),
('CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, '2006-03-02 06:43:15'),
('CI', 'COTE D''IVOIRE', 'Cote D''Ivoire', 'CIV', 384, '2006-03-02 06:43:15'),
('CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, '2006-03-02 06:43:15'),
('CL', 'CHILE', 'Chile', 'CHL', 152, '2006-03-02 06:43:15'),
('CM', 'CAMEROON', 'Cameroon', 'CMR', 120, '2006-03-02 06:43:15'),
('CN', 'CHINA', 'China', 'CHN', 156, '2006-03-02 06:43:15'),
('CO', 'COLOMBIA', 'Colombia', 'COL', 170, '2006-03-02 06:43:15'),
('CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, '2006-03-02 06:43:15'),
('CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, '2006-03-02 06:43:15'),
('CU', 'CUBA', 'Cuba', 'CUB', 192, '2006-03-02 06:43:15'),
('CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, '2006-03-02 06:43:15'),
('CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, '2006-03-02 06:43:15'),
('CY', 'CYPRUS', 'Cyprus', 'CYP', 196, '2006-03-02 06:43:15'),
('CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, '2006-03-02 06:43:15'),
('DE', 'GERMANY', 'Germany', 'DEU', 276, '2006-03-02 06:43:15'),
('DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, '2006-03-02 06:43:15'),
('DK', 'DENMARK', 'Denmark', 'DNK', 208, '2006-03-02 06:43:15'),
('DM', 'DOMINICA', 'Dominica', 'DMA', 212, '2006-03-02 06:43:15'),
('DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, '2006-03-02 06:43:15'),
('DZ', 'ALGERIA', 'Algeria', 'DZA', 12, '2006-03-02 06:43:15'),
('EC', 'ECUADOR', 'Ecuador', 'ECU', 218, '2006-03-02 06:43:15'),
('EE', 'ESTONIA', 'Estonia', 'EST', 233, '2006-03-02 06:43:15'),
('EG', 'EGYPT', 'Egypt', 'EGY', 818, '2006-03-02 06:43:15'),
('EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, '2006-03-02 06:43:15'),
('ER', 'ERITREA', 'Eritrea', 'ERI', 232, '2006-03-02 06:43:15'),
('ES', 'SPAIN', 'Spain', 'ESP', 724, '2006-03-02 06:43:15'),
('ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, '2006-03-02 06:43:15'),
('FI', 'FINLAND', 'Finland', 'FIN', 246, '2006-03-02 06:43:15'),
('FJ', 'FIJI', 'Fiji', 'FJI', 242, '2006-03-02 06:43:15'),
('FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, '2006-03-02 06:43:15'),
('FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, '2006-03-02 06:43:15'),
('FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, '2006-03-02 06:43:15'),
('FR', 'FRANCE', 'France', 'FRA', 250, '2006-03-02 06:43:15'),
('GA', 'GABON', 'Gabon', 'GAB', 266, '2006-03-02 06:43:15'),
('GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, '2006-03-02 06:43:15'),
('GD', 'GRENADA', 'Grenada', 'GRD', 308, '2006-03-02 06:43:15'),
('GE', 'GEORGIA', 'Georgia', 'GEO', 268, '2006-03-02 06:43:15'),
('GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, '2006-03-02 06:43:15'),
('GH', 'GHANA', 'Ghana', 'GHA', 288, '2006-03-02 06:43:15'),
('GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, '2006-03-02 06:43:15'),
('GL', 'GREENLAND', 'Greenland', 'GRL', 304, '2006-03-02 06:43:15'),
('GM', 'GAMBIA', 'Gambia', 'GMB', 270, '2006-03-02 06:43:15'),
('GN', 'GUINEA', 'Guinea', 'GIN', 324, '2006-03-02 06:43:15'),
('GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, '2006-03-02 06:43:15'),
('GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, '2006-03-02 06:43:15'),
('GR', 'GREECE', 'Greece', 'GRC', 300, '2006-03-02 06:43:15'),
('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, '2006-03-02 06:43:15'),
('GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, '2006-03-02 06:43:15'),
('GU', 'GUAM', 'Guam', 'GUM', 316, '2006-03-02 06:43:15'),
('GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, '2006-03-02 06:43:15'),
('GY', 'GUYANA', 'Guyana', 'GUY', 328, '2006-03-02 06:43:15'),
('HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, '2006-03-02 06:43:15'),
('HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, '2006-03-02 06:43:15'),
('HN', 'HONDURAS', 'Honduras', 'HND', 340, '2006-03-02 06:43:15'),
('HR', 'CROATIA', 'Croatia', 'HRV', 191, '2006-03-02 06:43:15'),
('HT', 'HAITI', 'Haiti', 'HTI', 332, '2006-03-02 06:43:15'),
('HU', 'HUNGARY', 'Hungary', 'HUN', 348, '2006-03-02 06:43:15'),
('ID', 'INDONESIA', 'Indonesia', 'IDN', 360, '2006-03-02 06:43:15'),
('IE', 'IRELAND', 'Ireland', 'IRL', 372, '2006-03-02 06:43:15'),
('IL', 'ISRAEL', 'Israel', 'ISR', 376, '2006-03-02 06:43:15'),
('IN', 'INDIA', 'India', 'IND', 356, '2006-03-02 06:43:15'),
('IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, '2006-03-02 06:43:15'),
('IQ', 'IRAQ', 'Iraq', 'IRQ', 368, '2006-03-02 06:43:15'),
('IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, '2006-03-02 06:43:15'),
('IS', 'ICELAND', 'Iceland', 'ISL', 352, '2006-03-02 06:43:15'),
('IT', 'ITALY', 'Italy', 'ITA', 380, '2006-03-02 06:43:15'),
('JM', 'JAMAICA', 'Jamaica', 'JAM', 388, '2006-03-02 06:43:15'),
('JO', 'JORDAN', 'Jordan', 'JOR', 400, '2006-03-02 06:43:15'),
('JP', 'JAPAN', 'Japan', 'JPN', 392, '2006-03-02 06:43:15'),
('KE', 'KENYA', 'Kenya', 'KEN', 404, '2006-03-02 06:43:15'),
('KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, '2006-03-02 06:43:15'),
('KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, '2006-03-02 06:43:15'),
('KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, '2006-03-02 06:43:15'),
('KM', 'COMOROS', 'Comoros', 'COM', 174, '2006-03-02 06:43:15'),
('KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, '2006-03-02 06:43:15'),
('KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF', 'Korea, Democratic People''s Republic of', 'PRK', 408, '2006-03-02 06:43:16'),
('KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, '2006-03-02 06:43:16'),
('KW', 'KUWAIT', 'Kuwait', 'KWT', 414, '2006-03-02 06:43:16'),
('KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, '2006-03-02 06:43:16'),
('KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, '2006-03-02 06:43:16'),
('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'Lao People''s Democratic Republic', 'LAO', 418, '2006-03-02 06:43:16'),
('LB', 'LEBANON', 'Lebanon', 'LBN', 422, '2006-03-02 06:43:16'),
('LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, '2006-03-02 06:43:16'),
('LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, '2006-03-02 06:43:16'),
('LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, '2006-03-02 06:43:16'),
('LR', 'LIBERIA', 'Liberia', 'LBR', 430, '2006-03-02 06:43:16'),
('LS', 'LESOTHO', 'Lesotho', 'LSO', 426, '2006-03-02 06:43:16'),
('LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, '2006-03-02 06:43:16'),
('LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, '2006-03-02 06:43:16'),
('LV', 'LATVIA', 'Latvia', 'LVA', 428, '2006-03-02 06:43:16'),
('LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, '2006-03-02 06:43:16'),
('MA', 'MOROCCO', 'Morocco', 'MAR', 504, '2006-03-02 06:43:16'),
('MC', 'MONACO', 'Monaco', 'MCO', 492, '2006-03-02 06:43:16'),
('MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, '2006-03-02 06:43:16'),
('MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, '2006-03-02 06:43:16'),
('MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, '2006-03-02 06:43:16'),
('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, '2006-03-02 06:43:16'),
('ML', 'MALI', 'Mali', 'MLI', 466, '2006-03-02 06:43:16'),
('MM', 'MYANMAR', 'Myanmar', 'MMR', 104, '2006-03-02 06:43:16'),
('MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, '2006-03-02 06:43:16'),
('MO', 'MACAO', 'Macao', 'MAC', 446, '2006-03-02 06:43:16'),
('MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, '2006-03-02 06:43:16'),
('MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, '2006-03-02 06:43:16'),
('MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, '2006-03-02 06:43:16'),
('MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, '2006-03-02 06:43:16'),
('MT', 'MALTA', 'Malta', 'MLT', 470, '2006-03-02 06:43:16'),
('MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, '2006-03-02 06:43:16'),
('MV', 'MALDIVES', 'Maldives', 'MDV', 462, '2006-03-02 06:43:16'),
('MW', 'MALAWI', 'Malawi', 'MWI', 454, '2006-03-02 06:43:16'),
('MX', 'MEXICO', 'Mexico', 'MEX', 484, '2006-03-02 06:43:16'),
('MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, '2006-03-02 06:43:16'),
('MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, '2006-03-02 06:43:16'),
('NA', 'NAMIBIA', 'Namibia', 'NAM', 516, '2006-03-02 06:43:16'),
('NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, '2006-03-02 06:43:16'),
('NE', 'NIGER', 'Niger', 'NER', 562, '2006-03-02 06:43:16'),
('NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, '2006-03-02 06:43:16'),
('NG', 'NIGERIA', 'Nigeria', 'NGA', 566, '2006-03-02 06:43:16'),
('NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, '2006-03-02 06:43:16'),
('NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, '2006-03-02 06:43:16'),
('NO', 'NORWAY', 'Norway', 'NOR', 578, '2006-03-02 06:43:16'),
('NP', 'NEPAL', 'Nepal', 'NPL', 524, '2006-03-02 06:43:16'),
('NR', 'NAURU', 'Nauru', 'NRU', 520, '2006-03-02 06:43:16'),
('NU', 'NIUE', 'Niue', 'NIU', 570, '2006-03-02 06:43:16'),
('NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, '2006-03-02 06:43:16'),
('OM', 'OMAN', 'Oman', 'OMN', 512, '2006-03-02 06:43:16'),
('PA', 'PANAMA', 'Panama', 'PAN', 591, '2006-03-02 06:43:16'),
('PE', 'PERU', 'Peru', 'PER', 604, '2006-03-02 06:43:16'),
('PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, '2006-03-02 06:43:16'),
('PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, '2006-03-02 06:43:16'),
('PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, '2006-03-02 06:43:16'),
('PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, '2006-03-02 06:43:16'),
('PL', 'POLAND', 'Poland', 'POL', 616, '2006-03-02 06:43:16'),
('PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, '2006-03-02 06:43:16'),
('PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, '2006-03-02 06:43:16'),
('PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, '2006-03-02 06:43:16'),
('PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, '2006-03-02 06:43:16'),
('PT', 'PORTUGAL', 'Portugal', 'PRT', 620, '2006-03-02 06:43:16'),
('PW', 'PALAU', 'Palau', 'PLW', 585, '2006-03-02 06:43:16'),
('PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, '2006-03-02 06:43:16'),
('QA', 'QATAR', 'Qatar', 'QAT', 634, '2006-03-02 06:43:16'),
('RE', 'REUNION', 'Reunion', 'REU', 638, '2006-03-02 06:43:16'),
('RO', 'ROMANIA', 'Romania', 'ROM', 642, '2006-03-02 06:43:16'),
('RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, '2006-03-02 06:43:16'),
('RW', 'RWANDA', 'Rwanda', 'RWA', 646, '2006-03-02 06:43:16'),
('SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, '2006-03-02 06:43:16'),
('SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, '2006-03-02 06:43:16'),
('SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, '2006-03-02 06:43:16'),
('SD', 'SUDAN', 'Sudan', 'SDN', 736, '2006-03-02 06:43:16'),
('SE', 'SWEDEN', 'Sweden', 'SWE', 752, '2006-03-02 06:43:16'),
('SG', 'SINGAPORE', 'Singapore', 'SGP', 702, '2006-03-02 06:43:16'),
('SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, '2006-03-02 06:43:16'),
('SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, '2006-03-02 06:43:16'),
('SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, '2006-03-02 06:43:16'),
('SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, '2006-03-02 06:43:16'),
('SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, '2006-03-02 06:43:16'),
('SM', 'SAN MARINO', 'San Marino', 'SMR', 674, '2006-03-02 06:43:16'),
('SN', 'SENEGAL', 'Senegal', 'SEN', 686, '2006-03-02 06:43:16'),
('SO', 'SOMALIA', 'Somalia', 'SOM', 706, '2006-03-02 06:43:16'),
('SR', 'SURINAME', 'Suriname', 'SUR', 740, '2006-03-02 06:43:16'),
('ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, '2006-03-02 06:43:16'),
('SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, '2006-03-02 06:43:16'),
('SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, '2006-03-02 06:43:16'),
('SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, '2006-03-02 06:43:16'),
('TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, '2006-03-02 06:43:16'),
('TD', 'CHAD', 'Chad', 'TCD', 148, '2006-03-02 06:43:16'),
('TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, '2006-03-02 06:43:16'),
('TG', 'TOGO', 'Togo', 'TGO', 768, '2006-03-02 06:43:16'),
('TH', 'THAILAND', 'Thailand', 'THA', 764, '2006-03-02 06:43:16'),
('TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, '2006-03-02 06:43:16'),
('TK', 'TOKELAU', 'Tokelau', 'TKL', 772, '2006-03-02 06:43:16'),
('TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, '2006-03-02 06:43:16'),
('TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, '2006-03-02 06:43:16'),
('TN', 'TUNISIA', 'Tunisia', 'TUN', 788, '2006-03-02 06:43:16'),
('TO', 'TONGA', 'Tonga', 'TON', 776, '2006-03-02 06:43:16'),
('TR', 'TURKEY', 'Turkey', 'TUR', 792, '2006-03-02 06:43:16'),
('TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, '2006-03-02 06:43:16'),
('TV', 'TUVALU', 'Tuvalu', 'TUV', 798, '2006-03-02 06:43:16'),
('TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, '2006-03-02 06:43:16'),
('TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, '2006-03-02 06:43:16'),
('UA', 'UKRAINE', 'Ukraine', 'UKR', 804, '2006-03-02 06:43:16'),
('UG', 'UGANDA', 'Uganda', 'UGA', 800, '2006-03-02 06:43:16'),
('UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, '2006-03-02 06:43:16'),
('US', 'UNITED STATES', 'United States', 'USA', 840, '2006-03-02 06:43:16'),
('UY', 'URUGUAY', 'Uruguay', 'URY', 858, '2006-03-02 06:43:16'),
('UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, '2006-03-02 06:43:16'),
('VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, '2006-03-02 06:43:16'),
('VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, '2006-03-02 06:43:16'),
('VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, '2006-03-02 06:43:16'),
('VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, '2006-03-02 06:43:16'),
('VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, '2006-03-02 06:43:16'),
('VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, '2006-03-02 06:43:16'),
('VU', 'VANUATU', 'Vanuatu', 'VUT', 548, '2006-03-02 06:43:16'),
('WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, '2006-03-02 06:43:16'),
('WS', 'SAMOA', 'Samoa', 'WSM', 882, '2006-03-02 06:43:16'),
('YE', 'YEMEN', 'Yemen', 'YEM', 887, '2006-03-02 06:43:16'),
('YT', 'MAYOTTE', 'Mayotte', NULL, NULL, '2006-03-02 06:43:16'),
('ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, '2006-03-02 06:43:16'),
('ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, '2006-03-02 06:43:16'),
('ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, '2006-03-02 06:43:16');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_action`
-- 

CREATE TABLE `tbl_decisiontable_action` (
  `id` varchar(32) NOT NULL default 'init',
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table used to keep a list of actions.';

-- 
-- Dumping data for table `tbl_decisiontable_action`
-- 

INSERT INTO `tbl_decisiontable_action` (`id`, `name`) VALUES ('gen6Srv42Nme32_1', 'access'),
('gen6Srv42Nme32_10', 'step1'),
('gen6Srv42Nme32_11', 'step2'),
('gen6Srv42Nme32_12', 'add'),
('gen6Srv42Nme32_13', 'save'),
('gen6Srv42Nme32_14', 'edit'),
('gen6Srv42Nme32_15', 'delete'),
('gen6Srv42Nme32_16', 'restore'),
('gen6Srv42Nme32_17', 'savetool'),
('gen6Srv42Nme32_18', 'delete'),
('gen6Srv42Nme32_19', 'restoreperms'),
('gen6Srv42Nme32_2', 'adminchangepassword'),
('gen6Srv42Nme32_20', 'savemenu'),
('gen6Srv42Nme32_21', 'savepage'),
('gen6Srv42Nme32_22', 'updatemenus'),
('gen6Srv42Nme32_23', 'editnode'),
('gen6Srv42Nme32_24', 'delete'),
('gen6Srv42Nme32_25', 'addchildnode'),
('gen6Srv42Nme32_26', 'addnode'),
('gen6Srv42Nme32_27', 'lecturers_form'),
('gen6Srv42Nme32_28', 'students_form'),
('gen6Srv42Nme32_29', 'guest_form'),
('gen6Srv42Nme32_3', 'Edit'),
('gen6Srv42Nme32_30', 'manage_lect'),
('gen6Srv42Nme32_31', 'manage_stud'),
('gen6Srv42Nme32_32', 'manage_guest'),
('gen6Srv42Nme32_33', 'main'),
('gen6Srv42Nme32_34', 'create_action'),
('gen6Srv42Nme32_35', 'create_rule'),
('gen6Srv42Nme32_36', 'create_condition'),
('gen6Srv42Nme32_37', 'condition_form'),
('gen6Srv42Nme32_38', 'show_condition'),
('gen6Srv42Nme32_39', 'edit_main'),
('gen6Srv42Nme32_4', 'Add'),
('gen6Srv42Nme32_40', 'show_main'),
('gen6Srv42Nme32_41', 'generate_config'),
('gen6Srv42Nme32_42', 'update_perms'),
('gen6Srv42Nme32_5', 'newuser'),
('gen6Srv42Nme32_6', 'adduser'),
('gen6Srv42Nme32_7', 'listusers'),
('gen6Srv42Nme32_8', 'delete'),
('gen6Srv42Nme32_9', 'batchdelete');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_action_rule`
-- 

CREATE TABLE `tbl_decisiontable_action_rule` (
  `id` varchar(32) NOT NULL default 'init',
  `ruleId` varchar(32) NOT NULL default 'init',
  `actionId` varchar(32) NOT NULL default 'init',
  PRIMARY KEY  (`id`),
  KEY `action_rule_FKIndex1` (`actionId`),
  KEY `action_rule_FKIndex2` (`ruleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bridge table used to keep a list of rules and actions.';

-- 
-- Dumping data for table `tbl_decisiontable_action_rule`
-- 

INSERT INTO `tbl_decisiontable_action_rule` (`id`, `ruleId`, `actionId`) VALUES ('gen6Srv42Nme32_1', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_10', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_10'),
('gen6Srv42Nme32_11', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_11'),
('gen6Srv42Nme32_12', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_12'),
('gen6Srv42Nme32_13', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_13'),
('gen6Srv42Nme32_14', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_14'),
('gen6Srv42Nme32_15', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_15'),
('gen6Srv42Nme32_16', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_16'),
('gen6Srv42Nme32_17', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_17'),
('gen6Srv42Nme32_18', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_18'),
('gen6Srv42Nme32_19', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_19'),
('gen6Srv42Nme32_2', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_20', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_20'),
('gen6Srv42Nme32_21', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_21'),
('gen6Srv42Nme32_22', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_22'),
('gen6Srv42Nme32_23', 'gen6Srv42Nme32_4', 'gen6Srv42Nme32_23'),
('gen6Srv42Nme32_24', 'gen6Srv42Nme32_4', 'gen6Srv42Nme32_24'),
('gen6Srv42Nme32_25', 'gen6Srv42Nme32_4', 'gen6Srv42Nme32_25'),
('gen6Srv42Nme32_26', 'gen6Srv42Nme32_4', 'gen6Srv42Nme32_26'),
('gen6Srv42Nme32_27', 'gen6Srv42Nme32_5', 'gen6Srv42Nme32_23'),
('gen6Srv42Nme32_28', 'gen6Srv42Nme32_5', 'gen6Srv42Nme32_24'),
('gen6Srv42Nme32_29', 'gen6Srv42Nme32_5', 'gen6Srv42Nme32_25'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_30', 'gen6Srv42Nme32_5', 'gen6Srv42Nme32_26'),
('gen6Srv42Nme32_31', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_27'),
('gen6Srv42Nme32_32', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_28'),
('gen6Srv42Nme32_33', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_29'),
('gen6Srv42Nme32_34', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_30'),
('gen6Srv42Nme32_35', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_31'),
('gen6Srv42Nme32_36', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_32'),
('gen6Srv42Nme32_37', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_33'),
('gen6Srv42Nme32_38', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_27'),
('gen6Srv42Nme32_39', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_28'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_40', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_29'),
('gen6Srv42Nme32_41', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_30'),
('gen6Srv42Nme32_42', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_31'),
('gen6Srv42Nme32_43', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_32'),
('gen6Srv42Nme32_44', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_33'),
('gen6Srv42Nme32_45', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_34'),
('gen6Srv42Nme32_46', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_35'),
('gen6Srv42Nme32_47', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_36'),
('gen6Srv42Nme32_48', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_37'),
('gen6Srv42Nme32_49', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_38'),
('gen6Srv42Nme32_5', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_50', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_39'),
('gen6Srv42Nme32_51', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_40'),
('gen6Srv42Nme32_52', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_41'),
('gen6Srv42Nme32_53', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_42'),
('gen6Srv42Nme32_6', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_7', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_7'),
('gen6Srv42Nme32_8', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_8'),
('gen6Srv42Nme32_9', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_9');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_action_rule_seq`
-- 

CREATE TABLE `tbl_decisiontable_action_rule_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- 
-- Dumping data for table `tbl_decisiontable_action_rule_seq`
-- 

INSERT INTO `tbl_decisiontable_action_rule_seq` (`sequence`) VALUES (53);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_action_seq`
-- 

CREATE TABLE `tbl_decisiontable_action_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

-- 
-- Dumping data for table `tbl_decisiontable_action_seq`
-- 

INSERT INTO `tbl_decisiontable_action_seq` (`sequence`) VALUES (42);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_condition`
-- 

CREATE TABLE `tbl_decisiontable_condition` (
  `id` varchar(32) NOT NULL default 'init',
  `name` varchar(50) NOT NULL default 'init',
  `params` varchar(255) NOT NULL default 'init',
  `class` varchar(50) NOT NULL default 'init',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table used to keep a list of conditions and their properties';

-- 
-- Dumping data for table `tbl_decisiontable_condition`
-- 

INSERT INTO `tbl_decisiontable_condition` (`id`, `name`, `params`, `class`) VALUES ('gen6Srv42Nme32_1', 'AdminOnly', 'isAdmin', ''),
('gen6Srv42Nme32_2', 'isAdmin', 'setValue', ''),
('gen6Srv42Nme32_3', 'iscontextlecturer', 'isContextMember | Lecturers', ''),
('gen6Srv42Nme32_4', 'isContextAuthor', 'hasContextPermission | isAuthor', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_condition_seq`
-- 

CREATE TABLE `tbl_decisiontable_condition_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `tbl_decisiontable_condition_seq`
-- 

INSERT INTO `tbl_decisiontable_condition_seq` (`sequence`) VALUES (4);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_conditiontype`
-- 

CREATE TABLE `tbl_decisiontable_conditiontype` (
  `id` varchar(32) NOT NULL default 'init',
  `name` varchar(50) NOT NULL default 'init',
  `className` varchar(50) NOT NULL default 'init',
  `moduleName` varchar(50) NOT NULL default 'init'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table used to store condition type as used by the decisionta';

-- 
-- Dumping data for table `tbl_decisiontable_conditiontype`
-- 

INSERT INTO `tbl_decisiontable_conditiontype` (`id`, `name`, `className`, `moduleName`) VALUES ('gen6Srv42Nme32_1', 'setValue', 'condition', 'decisiontable'),
('gen6Srv42Nme32_2', 'isAdmin', 'contextcondition', 'contextpermissions'),
('gen6Srv42Nme32_3', 'dependsOnContext', 'contextcondition', 'contextpermissions'),
('gen6Srv42Nme32_4', 'isContextMember', 'contextcondition', 'contextpermissions'),
('gen6Srv42Nme32_5', 'isMember', 'contextcondition', 'contextpermissions'),
('gen6Srv42Nme32_6', 'hasPermission', 'contextcondition', 'contextpermissions'),
('gen6Srv42Nme32_7', 'hasContextPermission', 'contextcondition', 'contextpermissions');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_conditiontype_seq`
-- 

CREATE TABLE `tbl_decisiontable_conditiontype_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `tbl_decisiontable_conditiontype_seq`
-- 

INSERT INTO `tbl_decisiontable_conditiontype_seq` (`sequence`) VALUES (7);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_decisiontable`
-- 

CREATE TABLE `tbl_decisiontable_decisiontable` (
  `id` varchar(32) NOT NULL default 'init',
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table used to keep a list of decisiontables.';

-- 
-- Dumping data for table `tbl_decisiontable_decisiontable`
-- 

INSERT INTO `tbl_decisiontable_decisiontable` (`id`, `name`) VALUES ('gen6Srv42Nme32_1', 'useradmin'),
('gen6Srv42Nme32_10', 'stories'),
('gen6Srv42Nme32_11', 'moduleadmin'),
('gen6Srv42Nme32_12', 'help'),
('gen6Srv42Nme32_13', 'contextadmin'),
('gen6Srv42Nme32_14', 'groupadmin'),
('gen6Srv42Nme32_15', 'dublincoremetadata'),
('gen6Srv42Nme32_16', 'contextcalendar'),
('gen6Srv42Nme32_17', 'dbmanager'),
('gen6Srv42Nme32_2', 'sysconfig'),
('gen6Srv42Nme32_3', 'toolbar'),
('gen6Srv42Nme32_4', 'context'),
('gen6Srv42Nme32_5', 'contextgroups'),
('gen6Srv42Nme32_6', 'contextpermissions'),
('gen6Srv42Nme32_7', 'security'),
('gen6Srv42Nme32_8', 'splashscreen'),
('gen6Srv42Nme32_9', 'postlogin');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_decisiontable_action`
-- 

CREATE TABLE `tbl_decisiontable_decisiontable_action` (
  `id` varchar(32) NOT NULL default 'init',
  `actionId` varchar(32) NOT NULL default 'init',
  `decisiontableId` varchar(32) NOT NULL default 'init',
  PRIMARY KEY  (`id`),
  KEY `decisiontable_action_FKIndex1` (`decisiontableId`),
  KEY `decisiontable_action_FKIndex2` (`actionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bridge table used to keep a list of actions and decision tab';

-- 
-- Dumping data for table `tbl_decisiontable_decisiontable_action`
-- 

INSERT INTO `tbl_decisiontable_decisiontable_action` (`id`, `actionId`, `decisiontableId`) VALUES ('gen6Srv42Nme32_1', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_10', 'gen6Srv42Nme32_10', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_11', 'gen6Srv42Nme32_11', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_12', 'gen6Srv42Nme32_12', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_13', 'gen6Srv42Nme32_13', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_14', 'gen6Srv42Nme32_14', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_15', 'gen6Srv42Nme32_15', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_16', 'gen6Srv42Nme32_16', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_17', 'gen6Srv42Nme32_17', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_18', 'gen6Srv42Nme32_18', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_19', 'gen6Srv42Nme32_19', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_2', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_20', 'gen6Srv42Nme32_20', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_21', 'gen6Srv42Nme32_21', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_22', 'gen6Srv42Nme32_22', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_23', 'gen6Srv42Nme32_23', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_24', 'gen6Srv42Nme32_24', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_25', 'gen6Srv42Nme32_25', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_26', 'gen6Srv42Nme32_26', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_27', 'gen6Srv42Nme32_27', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_28', 'gen6Srv42Nme32_28', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_29', 'gen6Srv42Nme32_29', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_30', 'gen6Srv42Nme32_30', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_31', 'gen6Srv42Nme32_31', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_32', 'gen6Srv42Nme32_32', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_33', 'gen6Srv42Nme32_33', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_34', 'gen6Srv42Nme32_34', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_35', 'gen6Srv42Nme32_35', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_36', 'gen6Srv42Nme32_36', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_37', 'gen6Srv42Nme32_37', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_38', 'gen6Srv42Nme32_38', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_39', 'gen6Srv42Nme32_39', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_4', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_40', 'gen6Srv42Nme32_40', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_41', 'gen6Srv42Nme32_41', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_42', 'gen6Srv42Nme32_42', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_5', 'gen6Srv42Nme32_5', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_6', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_7', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_8', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_9', 'gen6Srv42Nme32_9', 'gen6Srv42Nme32_1');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_decisiontable_action_seq`
-- 

CREATE TABLE `tbl_decisiontable_decisiontable_action_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

-- 
-- Dumping data for table `tbl_decisiontable_decisiontable_action_seq`
-- 

INSERT INTO `tbl_decisiontable_decisiontable_action_seq` (`sequence`) VALUES (42);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_decisiontable_rule`
-- 

CREATE TABLE `tbl_decisiontable_decisiontable_rule` (
  `id` varchar(32) NOT NULL default 'init',
  `ruleId` varchar(32) NOT NULL default 'init',
  `decisiontableId` varchar(32) NOT NULL default 'init',
  PRIMARY KEY  (`id`),
  KEY `decisiontable_rule_FKIndex1` (`decisiontableId`),
  KEY `decisiontable_rule_FKIndex2` (`ruleId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bridge table used to keep a list of rules and decision table';

-- 
-- Dumping data for table `tbl_decisiontable_decisiontable_rule`
-- 

INSERT INTO `tbl_decisiontable_decisiontable_rule` (`id`, `ruleId`, `decisiontableId`) VALUES ('gen6Srv42Nme32_1', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_2', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_4', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_5', 'gen6Srv42Nme32_5', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_6', 'gen6Srv42Nme32_6', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_7', 'gen6Srv42Nme32_7', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_8', 'gen6Srv42Nme32_8', 'gen6Srv42Nme32_6');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_decisiontable_rule_seq`
-- 

CREATE TABLE `tbl_decisiontable_decisiontable_rule_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `tbl_decisiontable_decisiontable_rule_seq`
-- 

INSERT INTO `tbl_decisiontable_decisiontable_rule_seq` (`sequence`) VALUES (8);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_decisiontable_seq`
-- 

CREATE TABLE `tbl_decisiontable_decisiontable_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- 
-- Dumping data for table `tbl_decisiontable_decisiontable_seq`
-- 

INSERT INTO `tbl_decisiontable_decisiontable_seq` (`sequence`) VALUES (17);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_rule`
-- 

CREATE TABLE `tbl_decisiontable_rule` (
  `id` varchar(32) NOT NULL default 'init',
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table used to keep a list of rules.';

-- 
-- Dumping data for table `tbl_decisiontable_rule`
-- 

INSERT INTO `tbl_decisiontable_rule` (`id`, `name`) VALUES ('gen6Srv42Nme32_1', 'useradmin rule 1'),
('gen6Srv42Nme32_2', 'sysconfig rule 1'),
('gen6Srv42Nme32_3', 'toolbar rule 1'),
('gen6Srv42Nme32_4', 'context rule 1'),
('gen6Srv42Nme32_5', 'context rule 2'),
('gen6Srv42Nme32_6', 'contextgroups rule 1'),
('gen6Srv42Nme32_7', 'contextgroups rule 2'),
('gen6Srv42Nme32_8', 'contextpermissions rule 1');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_rule_condition`
-- 

CREATE TABLE `tbl_decisiontable_rule_condition` (
  `id` varchar(32) NOT NULL default 'init',
  `conditionId` varchar(32) NOT NULL default 'init',
  `ruleId` varchar(32) NOT NULL default 'init',
  PRIMARY KEY  (`id`),
  KEY `rule_condition_FKIndex1` (`ruleId`),
  KEY `rule_condition_FKIndex2` (`conditionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Bridge table used to keep a list of conditions and rules.';

-- 
-- Dumping data for table `tbl_decisiontable_rule_condition`
-- 

INSERT INTO `tbl_decisiontable_rule_condition` (`id`, `conditionId`, `ruleId`) VALUES ('gen6Srv42Nme32_1', 'gen6Srv42Nme32_1', 'gen6Srv42Nme32_1'),
('gen6Srv42Nme32_2', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_2'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_3'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_4'),
('gen6Srv42Nme32_5', 'gen6Srv42Nme32_3', 'gen6Srv42Nme32_5'),
('gen6Srv42Nme32_6', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_6'),
('gen6Srv42Nme32_7', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_7'),
('gen6Srv42Nme32_8', 'gen6Srv42Nme32_2', 'gen6Srv42Nme32_8');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_rule_condition_seq`
-- 

CREATE TABLE `tbl_decisiontable_rule_condition_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `tbl_decisiontable_rule_condition_seq`
-- 

INSERT INTO `tbl_decisiontable_rule_condition_seq` (`sequence`) VALUES (8);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_decisiontable_rule_seq`
-- 

CREATE TABLE `tbl_decisiontable_rule_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- 
-- Dumping data for table `tbl_decisiontable_rule_seq`
-- 

INSERT INTO `tbl_decisiontable_rule_seq` (`sequence`) VALUES (8);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_dublincoremetadata`
-- 

CREATE TABLE `tbl_dublincoremetadata` (
  `id` varchar(32) NOT NULL default 'init',
  `provider` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `enterdate` datetime default NULL,
  `oai_identifier` varchar(255) default NULL,
  `oai_set` varchar(255) default NULL,
  `datestamp` datetime default NULL,
  `deleted` enum('false','true') NOT NULL default 'false',
  `dc_title` text,
  `dc_subject` text,
  `dc_description` text,
  `dc_type` varchar(255) default NULL,
  `dc_source` varchar(255) default NULL,
  `dc_sourceurl` varchar(255) default NULL,
  `dc_relationship` varchar(255) default NULL,
  `dc_coverage` varchar(255) default NULL,
  `dc_creator` varchar(255) default NULL,
  `dc_publisher` varchar(255) default NULL,
  `dc_contributor` varchar(255) default NULL,
  `dc_rights` varchar(255) default NULL,
  `dc_date` varchar(20) default NULL,
  `dc_format` varchar(255) default NULL,
  `dc_identifier` varchar(255) default NULL,
  `dc_language` varchar(255) default NULL,
  `dc_audience` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_dublincoremetadata`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_english`
-- 

CREATE TABLE `tbl_english` (
  `code` varchar(50) NOT NULL default 'init',
  `Content` mediumtext,
  `isInNextGen` tinyint(1) default NULL,
  `dateCreated` datetime default '2004-06-23 19:46:00',
  `creatorUserId` varchar(25) default '1',
  `dateLastModified` datetime default NULL,
  `modifiedByUserId` varchar(25) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_english`
-- 

INSERT INTO `tbl_english` (`code`, `Content`, `isInNextGen`, `dateCreated`, `creatorUserId`, `dateLastModified`, `modifiedByUserId`) VALUES ('access_level', 'Access Level\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('alert_enteronlydigit', '"Please enter only digit characters in the ""chapter number"" field"\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('alert_titlenotvalidoption', 'The first ""title"" option is not a valid selection.  Please choose one of the other options\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_about', 'About\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_admin', 'Admin\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_assessment', 'Assessment\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_communicate', 'Communicate\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_course', 'Course\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_develop', 'Develop\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_learn', 'Learn\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_learning', 'Learn\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_organise', 'Organise\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_organisers', 'Organise\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_postgrad', 'Postgraduate\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_postlogin', 'Post Login Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_prelogin', 'Pre Login Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_preloginfooter', 'Pre Login Footer\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_site', 'Site\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('category_user', 'User\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('changes_failed', 'Changes have not been made. Database error.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('changes_made', 'Changes have been made.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('delete_user_confirm', 'Are you sure you want to delete all details for {USER}?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('error_emailnotsame', 'The email addresses that you have entered are not the same. Please retype both of them\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('error_languageitemmissing', 'Language item missing', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('error_no_userid', 'Error - no such userId!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('error_valueexists', 'Error', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('has_classes', 'Has Classes\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('has_controller', 'Has Controller\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('has_registration', 'Has Registration File\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('has_registration_file', 'Has Registration File\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('heading_customSearch', 'Custom Search\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('heading_ifguest', 'If you are a guest of [--INSTITUTIONNAME--]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('heading_ifyouatinstitute', 'If you are at [--INSTITUTIONNAME--]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('heading_registeryourself', 'Register yourself\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_contextadmin_about', 'The Course Administration is used for creating, modifying and deleting information resources.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_contextadmin_about_title', 'About Course Administration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_contextgroups_about', 'Used to manage the members of a [-CONTEXT-].\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_contextgroups_about_title', 'About [-CONTEXT-] groups\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_contextpermissions_about', 'It is an administrative tool to manage permissions using a decision table.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_contextpermissions_about_title', 'About the [-CONTEXT-] permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_groupadmin_about', 'It is an administrative tool used to manage site groups, roles, and [-CONTEXTS-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_groupadmin_about_title', 'About group administration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_moduleadmin_about', 'This module is for Site-Admin use only. Registration and Deregistration of NextGen modules is done here.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_moduleadmin_about_title', 'Module Administration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_permissions_about', 'It is an administrative tool used to manage access control lists for the site.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_permissions_about_title', 'About permissions', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_postlogin_about', 'About Post Login\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_postlogin_about_title', 'About Post Login\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_stories_about', 'The [-stories-] module is used to create text elements that can be used to display text in places such as the prelogin page and the postlogin page. It is a tool for site administrators.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_stories_about_title', 'About website [-stories-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_stories_overview_add', 'Fill in the form to add a [-story-] to the website page. Use the Category dropdown to select a page on the site.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_stories_title_add', 'Adding a [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_useradmin_about', 'SiteAdmin uses this module to add users, edit user details, or remove users. Non-admin users can edit their own details. Also used for self-registration by new users.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_useradmin_about_title', 'User Administration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_useradmin_overview_register', 'Login using or username and password. If you have forgotten your password click the ''forgot your password'' link. If you do not have a username and password, please register yourself on the site. <p>Click the icon next to the heading to view the extended help.</p>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('help_useradmin_title_register', 'Register Yourself\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('hyperlink_generaterandomnumber', 'Generate your own user ID number\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_enteratleast5digits', 'Please enter at least 5 digits in the NumericID field\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_enteratmost9digits', 'Please enter at most 9 digits in the NumericID field\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_enterfirstname', 'Please enter your first name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_entergenerateid', 'Please enter or generate a value for the NumericID field\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_entersurname', 'Please enter your surname\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_entervalidemail', 'Please enter a valid email address\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('instruction_entervalidusername', 'Please enter a valid username\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('is_registered', 'Is Registered\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('label_confirmemail', 'Confirm e-mail address\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('member_of_administrator', 'Administrative member\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('menu_userdetails', 'User Details\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('message_confirmemailmessage', 'Please double check email address\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('message_selfregister', 'Fill in the new details and make sure that your email address is accurate. A password will be created for you\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('message_usestaffnum', 'Staff use staff number\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('message_usestudentnum', 'Students use student number\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('message_whenclickregister', 'When you click the <b>Register</b> button you will be sent an email with your password.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('message_willfillinnumber', 'This will fill in the number for you at the left\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('modules_badkey', 'Bad Key\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_addevent', 'Add an Event\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_dateofevent', 'Date of Event\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_desc', 'This module holds the calendar classes that allows it to be shared between user, context and workgroup', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_editevent', 'Edit Event\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_eventaddconfirm', 'Event has been added\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_eventdeleteconfirm', 'Event has been deleted\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_eventdeleterequestconfirm', 'Are you sure you want to delete this event?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_eventdetails', 'Event Details\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_eventeditconfirm', 'Event has been updated\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_eventtitle', 'Title of Event\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_multieventdeleterequestconfirm', 'Are you sure you want to delete this multi-day event?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_name', 'Calendar Base Module', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_previousmonth', 'Go to Previous Month\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_relatedwebsite', 'Related Website\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_saveevent', 'Save Event\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_selectdate', 'Select a Date\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_calendarbase_someonescalendar', '[someone]''s Calendar\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_class_files', 'Class Files in Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_about', 'About\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_active', 'Active\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_addnewcontext', 'Add a New [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_assigments', 'Assigments\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_assignment', 'Manage Assignments and Tests\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_asstest', 'Assignment and Tests\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_calendar', 'Calendar\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_chat', 'Chat\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_confcourse', 'Configure [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_configmm', 'Configure Freemind\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_confplugins', 'Configure Plugins\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_content', 'Manage Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_contextadmin', '[-context-] Admin\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_contextcode', '[-context-] Code\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_contextmanagement', '[-context-] Management\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_courseadmin', '[-context-] Admin\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_deletecontent', 'Delete Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_deletecontentquest', 'Are you sure you want to delete all the content?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_deletecontext', 'Delete [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_deletequest', 'Are you sure you want to delete the whole course?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_desc', 'Context management.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_diary', 'Diary\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_downloadstaticcontent', 'Download Static Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_editcontext', 'Edit [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_error_length', 'Maximum of [-length-] characters allowed\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_err_required', 'Required Field!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_essays', 'Essays\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_exportcontent', ' Export Static Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_exportstatic', 'Export Static Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_faq', 'FAQ\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_folderpath', 'Select a folder to upload static content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_forum', 'Discussion Forum\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_glossary', 'Glossary\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_gotostatic', 'View Static Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_guests', 'Guests\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_help', 'Select a [-context-] to configure, edit or delete, or click the add link to add a new [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_help_about', ' Give a description of the [-context-] and what it entails. The About will also be shown as a welcome page when one enters a [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_help_isactive', 'The [-context-] will not be active. Only [-lecturers-] will have access to a [-context-] if it is marks as Inactive\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_help_isClosed', 'Users needs to be registered in a course in order to access a Closed Course. Where as an Open Course is open to all\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_im', 'Instant Messanger\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importcontent', 'Import Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importdeletecontent', 'Import new Content (Existing content will be deleted)\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importfail', 'Import Failed -- An Error occured while trying to upload Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importhelp', 'Select the first option to Import new Content (all existing content will be however be deleted). Select the second option for Import\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importintonode', 'Import into existing content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importselectnode', 'Select a Node under which the new Content must be Uploaded\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importsuccess', 'Content was successfully imported!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importto', 'Import new content under\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_importwarning', 'Warning --  Importing Content will delete ALL existing Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_inactive', 'Inactive\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_inbasket', 'In Basket\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_isactive', 'Is Active\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_isclosed', 'Is Closed\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_isopen', 'Is Open\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_lecturers', 'Lecturers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_managecontext', 'Manage [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_marks', 'Marks\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_menutext', 'Menu Text\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_menutext2', 'Menu text\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_name', 'ContextAdmin', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_notregistered', ' is not registered\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_organise', 'Manage Organisers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_organisers', 'Manage Organisers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_organizecontent', 'Organize Static Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_organizors', 'Organizors\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_outcomes', '[-context-] Outcomes\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_pbl', 'Problem Based Learning\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_readinglist', 'Reading List\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_returnadmin', ' Return to Admin Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_rubrics', 'Rubrics\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_save', 'Save\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_saved', 'Saved\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_staticcontent', 'Upload Static Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_staticwarning', 'Please make sure that file is of type zip\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_status', 'Status\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_students', 'Students\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_title', 'Title\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_toolbarname', '[-context-] Admin\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_unregisteredfeature', 'Feature not Registered\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_users', 'Manage Users\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_viewcontent', ' View Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_whatsnew', 'Whats New\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextadmin_worksheets', 'Work Sheets\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_desc', 'Used to manage the members of the current context.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_manageguests', 'Manage guests\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_managelects', 'Manage [-AUTHORS-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_managestuds', 'Manage [-READONLYS-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_name', 'Manage members', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_notInContext', 'Please join a [-CONTEXT-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_notLect', ' Please contact your site administrator.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_onlineguest', 'guests Online\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_onlinelect', '[-AUTHORS-] Online\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_onlinestud', '[-READONLYS-] Online\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_toolbarname', 'Manage members\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlGuest', 'guest list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlLecturers', '[-AUTHOR-] list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlManage', 'Manage members:&nbsp;[-TITLE-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlManageMembers', 'Manage [-TITLE-]:&nbsp;[-GROUPNAME-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlNotInContext', 'Sorry you are not in a [-CONTEXT-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlNotLect', ' Only [-AUTHORS-] allowed access.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlStudents', '[-READONLY-] list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextgroups_ttlUsers', 'user list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_desc', 'Context permissions management.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_errNotRegistered ', ' [-MODULE-] is not registered!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_hdrDecisiontable ', ' Decision table header\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblAbsolutePath', 'Absolute group path:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblACL', 'Access control list:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblAction', 'Action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblCondition', 'Condition\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblConditionType', 'Condition type:', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblControllerActions', 'Get actions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblCreateAction', 'Create new action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblCreateCondition', 'Create new condition\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblCreateRule', 'Create new rule\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblDependsOnContext', 'Depends on [-CONTEXT-]:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblGenerateConfig', 'Generate config\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblIsAdmin', 'Is Administrator\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblMustBeAdmin', 'The user must be and administrator.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblRelativeContextPath', 'Relative [-CONTEXT-] path:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblRules', 'Rules\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblSelectACL', '-- Select an access control list --\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblSelectContextACL', '-- Select an access control list for the context--\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblSelectContextGroup', '-- Select a [-CONTEXT-] group --\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblSelectGroup', '-- Select a group --\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblSelectModule', 'Select a module:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_lblUpdatePerms', 'Update permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_name', '[-CONTEXT-] permissions', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_NotSaved', 'Not saved!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_saved', 'Saved\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_toolbarname', '[-CONTEXT-] permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_ttlCondition', 'Condition\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_ttlContextPermissions', '[-CONTEXT-] permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_ttlDecTbl', 'Decision table\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_contextpermissions_ttlRegConf', 'Registration configuration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_addchild', 'Add a Sub Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_addchildnode', 'Add a Sub Page to :\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_addsibling', 'Add a Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_addsiblingnode', 'Add Page next to :\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_author', 'lecturer\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_authors', 'lecturers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_buddies', 'Buddies\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_calendar', 'Calendar\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_communicate', 'Communicate\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_configure', 'Configure [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_confplugins', 'Configure [-context-] Plugins for\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_content', 'Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_context', '[-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_contexts', 'contexts\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_convertpdf', 'Convert to PDF\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_course', 'Course\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_coursecontent', 'Back to Course Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_coursehome', 'Back to [-context-] Home\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_coursetosharefrom', '[-context-] to share from\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_delmessnode', 'Are you sure you want to delete this page?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_desc', 'Context management.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_duedates', 'Due Dates/Events\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_editnode', 'Edit a Node\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_entercourse', 'Enter Course', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('mod_context_errmenutextminlength', 'Menu Text should be less than 100 characters\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_error_nocourseselected', 'No [-context-] was selected\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_error_notincourse', ' This action requires you to be in a [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_error_title', 'Please Add a Title\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_error_title_length', 'Title should be less \\\\nthan 20 characters\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_errsuppmenutext', 'Please supply a Menu Text\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_errsupptitle', 'Please supply a Title\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_err_selectnode', 'Please select an item before going to the next step\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_help', ' Help\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_inactive', 'Inactive\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_inlobby', 'In Lobby\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_javascript', 'Java Script\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_javascript_help', 'The Java Script will form part of this page which will be interpreted by your browser when the page is displayed\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_joincontext', 'Join [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_last5post', ' Last 5 Forum Post\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_learning', 'Learning\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_lobby', 'Lobby\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_loggedinas', 'Currently Logged in as\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_loggedintocourse', 'Logged into [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_manage', 'Manage Course\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_managecontent', ' Manage Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_move', 'Move\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_mycal', 'My Calendar\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_mypersonalspace', 'My Personal Space\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_name', 'Context', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_nav_first', 'First Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_nav_last', 'Last Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_nav_next', 'Next Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_nav_prev', 'Previous Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_next', ' Next &raquo;\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_noduedates', 'No Due Dates/Events\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_onlinelect', 'Lecturers Online\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_onlinestud', 'Students Online\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_organizers', 'Organizers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_pagecontents', 'Page Contents\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_plugins', 'Configure Plugins\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_position', 'Position\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_readonly', 'student\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_selectchapter', 'Select a chapter that you want to share\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_selectcourse', 'Select a [-context-] from which you want to share from\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_selectposition', 'Select a postion under which you want to the shared Item to be displayed\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_selnode', 'Select a Item to be shared\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_sharedaddordelete', 'Add or Delete another Shared Items\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_sharedcontent', ' Shared Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_sharedfrom', 'Shared from\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_shareditem', 'Shared Item\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_sharenodeadded', 'Successfully saved !!!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_step', ' Step\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_step1info', 'Select a [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_step2info', 'Select a Chapter\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_step3info', 'Set the Position\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_succsave', 'Successfully saved at\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_toolbarname', '[-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_webpage', 'Webpage\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_welcome', 'Welcome to\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_context_whoson', 'Who''s Online\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_decisiontable_desc', 'Decision table management.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_decisiontable_name', 'Decision Table', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublincoremetadata_desc', 'Dublin Core Metadata', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublincoremetadata_name', 'Dublin Core Metadata', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_audience', 'Audience\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_contributor', 'Contributor\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_coverage', 'Coverage\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_creator', 'Creator\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_date', 'Date\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_dcm', 'Dublin Core Metadata\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_description', 'Description\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_format', 'Format\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_identifier', 'Identifier\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_language', 'Language\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_publisher', 'Publisher\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_relationship', 'Relationship\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_rights', 'Rights\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_source', 'Source\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_sourceurl', 'Source URL\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_subject', 'Subject\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_dublin_type', 'Type\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_error_passwd', 'Error - the password does not match! Please renter the password.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_back ', ' Back to Group Admin\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_btnInsert ', ' Insert\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_btnRemove ', ' Remove\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_delete ', ' Delete group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_desc', 'Group Administration', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_edit ', ' Edit group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_errNotAdmin ', ' Sorry only Administrators are allowed to use this page.<P>[-LINK-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrCreateGroup ', ' Create a new group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrDeleteGroup ', ' Delete the group and the group member list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrEditGroup ', ' Edit the group member list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrFirstName ', ' First name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrGroupDesc ', ' Group description\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrGroupList ', ' List of groups\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrGroupName ', ' Group name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrMemberList ', ' Members\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrSurname ', ' Surname\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrUsers ', ' Users\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hdrViewGroup ', ' View the group member list\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hlpCreateGroup', ' <B>Create a new group.</B><P>Required inputs:<UL><LI>Group name.<UL><LI>The unique name given to the group</LI></UL><LI>Group description.<UL><LI>A short description of the expect member list</LI></UL></UL>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hlpDeleteGroup', ' <B>Delete an existing group.</B><P>Available actions:<UL><LI>Edit.<UL><LI>Edit the members for this group</LI></UL><LI>Delete<UL><LI>Delete the group and the member list</LI></UL><LI>Cancel<UL><LI>Do nothing return to group administration main view.</LI></UL></UL>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hlpEditGroup', ' <B>Edit an existing group.</B><P>Available actions:<LI>Underconstruction</LI>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hlpGroupAdmin ', ' <B>This module maintains the list of groups and its members.</B><P>Administration tasks:<UL><LI>Adding new groups.<LI>Editing existing groups.<UL><LI>Adding group member.<LI>Removing group members.</UL><LI>Deleting existing groups.</UL>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_hlpViewGroup', ' <B>View an existing group.</B><P>Available actions:<LI>Underconstruction</LI>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblDescription ', ' Group description\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblInFolder ', ' Group members found in folder\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblInTotal ', ' Group members found in total\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblMembersFound ', ' Group members found\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblName ', ' Group name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblParent ', ' Parent/Home group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblUpdated ', ' Group was last updated.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblUpdatedBy ', ' Group was last updated by\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_lblUpdatedOn ', ' Group was last updated on\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_msgDeleted ', ' [-GROUPNAME-] was deleted successfully! [-TIMESTAMP-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_msgDeleteGroup ', ' ARE YOU SURE YOU WANT TO DELETE [-GROUPNAME-].\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_msgInvalidField ', ' [-FIELDNAME-] had an invalid entry.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_msgNotSaved ', ' Your changes have not been saved! [-TIMESTAMP-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_msgSaved ', ' Your changes have been saved successfully! [-TIMESTAMP-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_name', 'Group Administration', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_ttlCreateGroup ', ' Create a group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_ttlDeleteGroup ', ' Delete group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_ttlEditGroup ', ' Edit group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_ttlGroupAdmin ', ' Group Administration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_groupadmin_ttlViewGroup ', ' View group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_hasdependants', 'Error! Cannot delete with dependents\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_help_desc', 'Used to display help about a module feature', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_help_helpph', 'Help about [-PHRASE-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_help_name', 'Help', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_htmlelements_desc', 'Used as a support module for generating html tags', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_htmlelements_name', 'htmlelements', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_html_help_editor', '<h3>Setting Prefs for the Mozilla Rich Text Editing</h3> <p>To protect users'' private information, unprivileged scripts cannot invoke the Cut, Copy, and Paste commands in the text editor, so the  corresponding buttons on the Editing page will not work. To enable  these functions for purposes of the demo, you must modify your browser  preferences. <ol>  <li>Quit Mozilla. If you have Quick Launch running   (in Windows, an icon in the toolbar), quit that too.  <li>Find your Mozilla profile directory. On Windows, this is often   located in <code>c://WINNT/Profiles/<var>&lt;your Windows login&gt;</var>/Application Data/Mozilla</code>.<li>Open the user.js file from that directory in a text editor.   If there''s no <code class="filename">user.js</code> file, create one.  <li>   <p>Add these lines to user.js<br />   user_pref(''capability.policy.policynames'', ''allowclipboard'');<br /> user_pref(''capability.policy.allowclipboard.sites'', ''http://www.mozilla.org);<br /> user_pref(''capability.policy.allowclipboard.Clipboard.cutcopy'', ''allAccess'');<br /> user_pref(''capability.policy.allowclipboard.Clipboard.paste'', ''allAccess'');<br /></pre>  <li>Save the file, and restart Mozilla.<br /> The Clipboard buttons should now function. </ol><p><p>\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_language_desc', 'Used to allow multilingualisation option in KEWL.NextGen.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_language_name', 'KEWL.NextGen Language', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_asksure', 'Are you sure you want to register this module?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_authors', 'Authors\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_batch1', 'Batch Registration Menu\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_batch2', 'Batch Registration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_changepassword', 'Change Password for user {USER}\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_core', 'This is an essential module and should not be deleted.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_depend1', 'Dependencies\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_deregconfirm', 'Module MODULE has been deregistered.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_deregisterbatch1', 'Batch DeRegistration Menu\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_deregisterbatch2', 'Batch DeRegistration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_deregsure', 'Are you sure you want to deregister module MODULE?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_desc', 'Manage the modules on this KEWL.NextGen site.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_err_nofile', 'Error! Module MODULE has no ''register.conf'' file\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_go', 'Go to Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_info', 'General Information on Module ''MODULE''\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_info2', 'Module Info\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_menucat', 'Menu Category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_modname', 'Module Name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_name', 'Module Admin', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_needinfo', 'Cannot register module - needs info to create table {MODULE} first!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_needmodule', 'Cannot register module - needs module {MODULE} to be registered first!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_noselect', 'No modules were selected.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_notadmin', 'This module requires Site Administrator status, which you do not seem to have.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_notreg', 'Error. Module has NOT been installed.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_problem1', 'Problems detected\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_rdate', 'Release Date\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_regconfirm', 'Module MODULE has been registered.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_replacetext', 'Replace All\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_return', 'Return to ModuleAdmin\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_tables', 'Tables\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_textelementsfor', 'Text Elements for\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_textproblem', 'Module <b>[MODULE]</b> has invalid text defintion for ''[CODE]''\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_version', 'Version\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_moduleadmin_worddesc', 'Description\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_no_text', 'No Text elements\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_permissions_desc', 'Permissions management', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_permissions_hlpPermissions ', ' Help permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_permissions_name', 'Permissions', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_phrase_classfiles', 'Class Files in Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_announcements', 'Announcements\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_currentlyincontext', 'Currently in {context}\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_desc', 'Provides interface to KEWL.NextGen when the user has logged in.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_helphowto', 'You can click the help icon next to any button or link, or wherever it is displayed in order to get help for that function.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_helptitle', 'Getting help\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_messages', 'Messages\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_name', 'KEWL.NextGen Default post loging module', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_postlogin_news', 'News\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_contactadmin', 'Please contact your system administrator.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_contactadminaccess', 'Please contact your system administrator about access rights.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_desc', 'Redirect page', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_entercourse', 'Select a course from the list below.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_name', 'Redirect', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_noaction', 'You do not have permission to invoke the action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_nocourse', 'You need to be in a course to access\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_nopermission', 'You do not have permission to access\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_notregistered', 'has not been registered.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_nousercourses', 'You are not registered for any courses.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_register', 'Register\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_redirect_registermodule', 'Register the module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_desc', 'Manage the users on this KEWL.NextGen site.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_emailsysadmin', 'If you continue to have problems, please email the System Administrator\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_forgotpassword', 'Forgot your password?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_incorrectpassword', 'Incorrect Password\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_incorrectpasswordmessage', 'The password you have given is incorrect. Please try again or request a new password.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_javascriptwarning', 'This Site requires JavaScript to be enabled!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_name', 'Security', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_noaccount', 'Account doesn''t exist\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_noaccountmessage', 'The account you have tried to access does not exist - username is not valid. Please try another username or register to get access.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_no_ldap', 'Unable to connect to the LDAP server. Try again later.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_security_requestnewpassword', 'Request New Password\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_show_classes', 'Show Classes\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_add', 'Add a [-story-] on [-SITENAME-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_addalt', 'Add a new [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_addlabel', 'Add a [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_alsoavailable', 'Also available in:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_alwaysontop', 'Always on top\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_alwaysontopalt', '[-story-] is always on top\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_anothercat', 'View another category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_confirm', 'Confirm deletion of [-STORY-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_delalt', 'Delete this [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_desc', 'Manage the stories that appear on the KNG website.', 1, '2004-06-23 19:46:00', '1', NULL, NULL);
INSERT INTO `tbl_english` (`code`, `Content`, `isInNextGen`, `dateCreated`, `creatorUserId`, `dateLastModified`, `modifiedByUserId`) VALUES ('mod_stories_dpick', 'Pick the expiration date for this [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_edit', 'Edit a [-story-] on [-SITENAME-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_editalt', 'Edit this [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_editlabel', 'Edit [-story-] [-STORY-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_expired', 'This [-story-] has past its expiration date!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_expiredalt', '[-story-] has expired\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_help_main', 'The [-stories-] module is a means to include text output in another module. It is for use by site administrators to control text, for example on the post login page. At present, three text areas of the default postlogin page are controlled by [-stories-]. These have the categories postlogin, leftpostlogin, and footer. Use the [-stories-] module to edit the text of the left, central, and footer on the post login page.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_isactivealt', '[-story-] is active\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_isnotactivealt', '[-story-] is not active\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_mainleftside', 'Select the [-story-] you wish to edit or delete, or click the add icon to add a new [-story-].\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_name', '[-stories-]', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_news', 'News\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_notalwaysontopalt', '[-story-] is not always on top\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_notstickyalt', '[-story-] is not sticky\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_parentId', 'Parent Id\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_plaintext', 'Plain text editor\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_sticky', 'Sticky\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_stickyalt', '[-story-] is sticky\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_story', '[-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_title', 'Manage [-stories-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_toolbarname', '[-stories-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_translate', 'Translate this [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_translatelabel', 'Translate [-story-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_val_catnotnull', 'You need to select or enter a category as the category field cannot be empty.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_val_lang2chargt', 'Language code may not exceed 2 characters. Use the language selector to choose the language if you are unsure how to proceed.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_val_langnotnull', 'You need to select or enter a language as the language field cannot be empty.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_stories_wyswyg', 'WYSWYG mode editor\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_action', 'Action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_addnew', 'Add New Category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_cancel', 'Cancel\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_category', 'Category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_creatorid', 'Created By\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_datecreated', 'Date Created\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_datemodified', 'Date Modified\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_delalt', 'Delete\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_desc', 'Story categories management for KEWL.NextGen', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_lefteditist', 'Enter the story category and a title for it, keeping the title short as it will appear on a dropdown menu.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_leftinstructions', 'Select the story category that you wish to edit or remove and click the edit or delete icon, or chose the add icon to add a new story category.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_modifierid', 'Modified By\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_name', 'Story categories', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_title', 'Manage story categories\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategoryadmin_titleth', 'Title\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategory_addlabel', 'Add a story category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategory_confirm', 'Please confirm that you wish to delete the story category [-ITEM-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategory_editlabel', 'Edit story category [-CATEGORY-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_storycategory_exists', 'The category already exists\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_strings_desc', 'Used to provide strings functions to other modules', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_strings_exlink', 'External link', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_strings_name', 'Strings', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_action', 'Action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_adddisabled', 'The add feature is disabled. You need to set the add_disabled parameter to FALSE in the system configuration module. This should only be done by developers on a development machine.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_addiconalt', 'Add is disabled\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_addlabel', 'You can add a parameter to the selected module here, but that this feature should not be used except by developers during development. \n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_addtxt', 'Add a parameter\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_deleted', 'Parameter deleted\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_desc', 'Used to system configuration variables to be stored in the database', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_edlabel', 'You can change the parameter value by entering it in the form to the left. Please do not change anything here unless you are sure that this is what you wish to do. Incorrect data can cause the specific module to cease working.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_edtxt', 'Edit parameter\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_err_dupattempt', 'ERROR! Adding the entry to the system configuration would produce a duplicate configuration entry.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_err_keynotexist', 'ERROR! The key name does not exist for the module being looked up.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_firstep', 'Step 1\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_modtxt', 'Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_name', 'System configuration', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_noconfprop', 'This module has no configurable properties\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_nomoduleset', 'No module set\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_paramname', 'Parameter name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_paramvalue', 'Parameter value\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_reqadmin', 'Administrative rights are required to access the system configuration editor\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_secondstep', 'Step 2\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_selectsystem', 'Select system type\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_step1', 'Specify the module that you wish to configure by selecting it from the dropdown list of installed modules\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_step2', 'Choose the parameter that you wish to edit. If none are shown, then the module has no parameters at present. Note that this feature should not be used except by developers during development. \n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_testlabel', 'A label for use in testing, and which does not need translating.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_title', 'System configuration editor\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_unrecmo', 'Unrecognized mode\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_sysconfig_youcanadd', 'Add a new configuration parameter\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_action', 'Action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_addnewlink', 'Add New Link\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_administrator', 'Administrator\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_adminonly', 'Admin Only\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_alumnimanagement', 'Alumni Management\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_availacl', 'Available ACLs\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_availcongroup', 'Available [-context-] Groups\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_availgroup', 'Available Groups\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_bottom', 'Bottom\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_category', 'Category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_confmodulelinks', 'Configure Module Links\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_confperm', 'Configure Link Permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_content', 'Manage Content\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_dependscontext', 'Depends Context\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_desc', 'Navigation Bar', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_develop', 'For Developers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_developers', 'For Developers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_editlink', 'Edit Link\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_guest', 'Guest\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_icon', 'Icon\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_langcode', 'Language Code\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_leavecontext', 'Leave [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_lecturer', 'Lecturer\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_linkpermissions', 'Link Permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_linksettings', 'Link Settings\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_loggedin', 'Currently logged in as\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_lowermiddle', 'Lower Middle\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_menu', 'Menu\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_middle', 'Middle\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_modnotfound', 'Module [-module-] Not Found\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_module', 'Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_modulesettings', 'Module Settings\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_name', 'Site Admin', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_nolinks', 'No Links\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_organise', 'Manage Organisers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_organisers', 'Manage Organisers\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_page', 'Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_permissions', 'Permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_positioninmenu', 'Position in Menu\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_restoredefaultperms', 'Restore Default Permissions\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_restoredefaults', 'Restore Default Settings\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_role', 'Role\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectacl', 'Select an access control list (ACL)\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectcategory', 'Select Category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectcongroup', 'Select a [-context-] group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectedacl', 'Selected ACLs\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectedcongroup', 'Selected [-context-] Groups\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectedgroup', 'Selected Groups\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectgroup', 'Select a group\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_selectmodule', 'Select New Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_settosite', 'Display to everyone\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_sidemenu', 'Side Menu\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_site', 'Manage Site\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_siteadmin', 'Site Administration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_student', 'Student\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_toolbar', 'Toolbar\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_top', 'Top\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_uppermiddle', 'Upper Middle\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_users', 'Manage Users\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_toolbar_welcome', 'Welcome\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_adminNoDelete', ' you are a Site Admin, and cannot delete your own account\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_browsebysurname', 'Browse by Surname\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_changepassword', 'Change Password for user {USER}\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_changepassword2', 'Change Password\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_changepicture', 'Change Picture\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_cleanup', 'Cleanup Unused Accounts\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_deletesected', 'Delete Selected Users\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_desc', 'Manage the users on this KEWL.NextGen site.', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet1', 'Dear FIRSTNAME SURNAME\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet2', 'You have been added as a user to the KEWL NextGen system\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet3', 'This e-mail is automatically generated by the administration system on KEWL NextGen.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet4', 'Your user details on KEWL NextGen are as follows:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet5', 'The KEWL NextGen Registration System\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet6', 'KEWL NextGen Registration\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_greet7', 'To log in, go to :\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_help', 'Click here for help\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_ldapnochange', 'The username you entered is that of an LDAP account - you need to ask your institution''s system administrator to change your password.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_listallusers', 'List All Users\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_listingusersbysurname', 'Listing Users by Surname\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_name', 'User Admin', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_newuseradded', 'New User Added\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_nogd', 'Warning', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_nomatch', 'No match found. The username you entered either is not registered on the system, or the email address is different from the one you typed in. Click on your browser''s "Back" button to try again.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_otherinfo', 'Other Information\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_passwordreset', 'A new password has been generated for you, and has been sent to your email address. The old password will no longer work.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_register1', 'Register me now\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_resetpassword', 'Enter your username and email address, and a new password will be generated and emailed to you\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_searchforuser', 'Search for User\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_searchresultsfor', 'Search Results for\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_selfdelete0', 'Delete Login\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_selfdelete1', 'Login Deleted. This account has been removed from the system.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_showingallusers', 'Showing All Users\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_unusedaccounts', 'Unused Accounts\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_updatedetails', 'Update Details\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_useradmin_welcome', 'Welcome to KEWL NextGen!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_addtext', 'Add Missing Texts to Database\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_deregister', 'Deregister\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_module', 'Module\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_options', 'Options\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_register', 'Register\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_textelement', 'Text Elements\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('mod_word_textelements', 'Text Elements\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('need_email', 'No email address supplied. We need a valid email address for each user.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('need_password', 'No password supplied\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('need_username', 'No username supplied. Please add one.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('option_selectatitle', 'Select a title\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('Pagetext_emailaddress', 'E-mail Address\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrasebacktohomepage', 'Back to Home Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_confirmdelete', 'Confirm delete?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_confirmdeletion', 'Confirm Deletion\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_confirmlogout', 'Are you sure you want to logout?\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_creationdate', 'Creation Date\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_dateposted', 'Date Posted\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_emailaddress', 'Email Address\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_expirationdate', 'Expiration Date\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_finalstep', 'Final Step\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_firstname', 'First Name\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_goback', 'Go Back\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_goto_login', 'Go to Login Page\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_howcreated', 'Creation Method\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_id', 'Id\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_inactive_user', 'Sorry, that user account is inactive. Contact the SysAdmin if you need it re-enabled.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_invalid_login', 'The username and password you have entered are invalid\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_isactive', 'Active\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_joincourse', 'Join [-context-]\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_languagelist', 'Select Language', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('phrase_lastlogin', 'Last login\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_loggedinas', 'Login as\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_networkid', 'Network Id', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('phrase_notfound', 'Not Found!\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_numberoflogins', 'Number of logins\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_postedby', 'Posted by\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_reset_image', 'Reset Image To Default\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_selectcourse', 'Select course\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_selectskin', 'Select Skin', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('phrase_timeactive', 'Active time\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_unrecognizedaction', 'Unrecognised Action\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('phrase_upload_image', 'Change Online Image\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('step1', 'Step 1\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('step2', 'Step 2\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('step3', 'Step 3\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('step4', 'Step 4\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('step5', 'Step 5\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('step6', 'Step 6\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('text_selectall', 'Select All\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('text_selectnone', 'Select None\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_assocprof', 'Assoc Prof\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_dr', 'Dr\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_miss', 'Miss\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_mr', 'Mr\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_mrs', 'Mrs\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_ms', 'Ms\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_prof', 'Professor\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('title_rev', 'Rev\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('user details', 'Details for this user:\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('userid_taken', 'The userId you entered has already been allocated to a user.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('username_taken', 'The Username you asked for has already been given to another user. Please choose a different one.\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('user_added', 'The new user has been added\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('warning_pleasenote', 'Please note\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('warning_usernamenospaces', 'Usernames may not contain any spaces\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_abstract', 'Abstract\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_add', 'Add\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_author', 'Author\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_back', 'Back\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_cancel ', ' Cancel\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_category', 'Category\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_change', 'Change\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_close', 'Close\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_confirm', 'Confirm\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_country', 'Country\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_course', 'Course\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_create ', ' Create\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_current', 'Current\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_edit', 'Edit\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_female', 'Female\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_go', 'Go', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('word_help', 'Help\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_home ', ' Home\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_inlobby', 'Lobby\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_language', 'Language\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_leave', 'Leave\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_list', 'List\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_login', 'Login', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('word_logout', 'Logout', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('word_male', 'Male\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_new', 'New\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_no', 'No\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_ok', 'OK\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_old', 'Old\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_on', 'On\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_password', 'Password', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('word_problem', 'Problem\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_register', 'Register', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('word_save', 'Save\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_select', 'Select\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_sex', 'Sex\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_sincerely', 'Sincerely\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_story', 'Story\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_surname', 'Surname\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_title', 'Title\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_user', 'User\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_userid', 'UserId\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL),
('word_username', 'Username', 1, '2006-03-02 06:42:11', '1', NULL, NULL),
('word_yes', 'Yes\n', 1, '2004-06-23 19:46:00', '1', NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_groupadmin_group`
-- 

CREATE TABLE `tbl_groupadmin_group` (
  `id` varchar(32) NOT NULL default 'init',
  `parent_id` varchar(32) default NULL,
  `name` varchar(32) default NULL,
  `description` varchar(100) default NULL,
  `last_updated` datetime default NULL,
  `last_updated_by` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `ind_groups_FK` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The tbl_groupadmin_groups table is managed by the groupadmin';

-- 
-- Dumping data for table `tbl_groupadmin_group`
-- 

INSERT INTO `tbl_groupadmin_group` (`id`, `parent_id`, `name`, `description`, `last_updated`, `last_updated_by`) VALUES ('gen6Srv42Nme32_1', NULL, 'FOSS', 'Long Live the Code!', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_2', 'gen6Srv42Nme32_1', 'Lecturers', 'FOSS Lecturers', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_1', 'Students', 'FOSS Students', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_1', 'Guest', 'FOSS Guest', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_5', 'gen6Srv42Nme32_1', 'monkeys', 'monkey users', '2006-03-02 09:52:27', '1'),
('init_1', NULL, 'Site Admin', 'The site administration users group list', '2006-03-02 06:43:18', '1'),
('init_2', NULL, 'Lecturers', 'The site wide Lecturers user group', '2006-03-02 06:43:18', '1'),
('init_3', NULL, 'Students', 'The site wide Students user group', '2006-03-02 06:43:18', '1'),
('init_4', NULL, 'Guest', 'The site wide Guest user group', '2006-03-02 06:43:18', '1');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_groupadmin_group_seq`
-- 

CREATE TABLE `tbl_groupadmin_group_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `tbl_groupadmin_group_seq`
-- 

INSERT INTO `tbl_groupadmin_group_seq` (`sequence`) VALUES (5);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_groupadmin_groupuser`
-- 

CREATE TABLE `tbl_groupadmin_groupuser` (
  `id` varchar(32) NOT NULL default 'init',
  `group_id` varchar(32) NOT NULL default 'init',
  `user_id` varchar(32) NOT NULL default 'init',
  `last_updated` datetime default NULL,
  `last_updated_by` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `ind_groupuser_FK` (`group_id`),
  KEY `ind_usergroup_FK` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This is the bridge table between user and group table';

-- 
-- Dumping data for table `tbl_groupadmin_groupuser`
-- 

INSERT INTO `tbl_groupadmin_groupuser` (`id`, `group_id`, `user_id`, `last_updated`, `last_updated_by`) VALUES ('gen6Srv42Nme32_1', 'gen6Srv42Nme32_2', 'init_1', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_5', 'init_1', '2006-03-02 09:52:56', '1'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_4', 'init_1', '2006-03-02 12:28:04', '1'),
('init_1', 'init_1', 'init_1', '2006-03-02 06:43:18', '1');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_groupadmin_groupuser_seq`
-- 

CREATE TABLE `tbl_groupadmin_groupuser_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `tbl_groupadmin_groupuser_seq`
-- 

INSERT INTO `tbl_groupadmin_groupuser_seq` (`sequence`) VALUES (4);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_language_modules`
-- 

CREATE TABLE `tbl_language_modules` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` varchar(50) NOT NULL default 'init',
  `code` varchar(50) NOT NULL default 'init',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=78 ;

-- 
-- Dumping data for table `tbl_language_modules`
-- 

INSERT INTO `tbl_language_modules` (`id`, `module_id`, `code`) VALUES (77, 'moduleadmin', 'ModuleAdmin');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_languagelist`
-- 

CREATE TABLE `tbl_languagelist` (
  `id` int(11) NOT NULL auto_increment,
  `languageCode` varchar(100) NOT NULL default 'init',
  `languageName` varchar(100) NOT NULL default 'init',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds the list of languages that KEWL has' AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `tbl_languagelist`
-- 

INSERT INTO `tbl_languagelist` (`id`, `languageCode`, `languageName`) VALUES (1, 'tbl_english', 'English');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_languagetext`
-- 

CREATE TABLE `tbl_languagetext` (
  `code` varchar(50) NOT NULL default 'init',
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_languagetext`
-- 

INSERT INTO `tbl_languagetext` (`code`, `description`) VALUES ('access_level', 'access level'),
('alert_enteronlydigit', '"Please enter only digit characters in the ""chapter number"" field"'),
('alert_titlenotvalidoption', '"The first ""title"" option is not a valid selection.  Please choose one of the other options"'),
('category_about', 'The word About'),
('category_admin', 'The verb Admin'),
('category_assessment', 'The verb Assessment'),
('category_communicate', 'The verb Communicate'),
('category_course', 'The verb Course'),
('category_develop', 'The verb Develop'),
('category_learn', 'The verb Learn'),
('category_learning', 'The verb Learn'),
('category_organise', 'The verb Organise'),
('category_organisers', 'The verb Organise'),
('category_postgrad', 'The word Postgraduate'),
('category_postlogin', 'The phrase Post Login Page denoting a category'),
('category_prelogin', 'The phrase Pre Login Page denoting a category'),
('category_preloginfooter', 'The phrase re Login Footer denoting a category'),
('category_site', 'The verb Site'),
('category_user', 'The verb User'),
('changes_failed', 'Database change failed'),
('changes_made', 'Database change succeeded'),
('delete_user_confirm', 'confirm deletion'),
('error_emailnotsame', 'The email addresses that you have entered are not the same. Please retype both of them'),
('error_languageitemmissing', 'Use to output the error when a language item is missing'),
('error_no_userid', 'Error for invalid UserId'),
('error_valueexists', 'error message for missing value'),
('has_classes', 'Has Classes'),
('has_controller', 'Has Controller File'),
('has_registration', 'Has Registration File'),
('has_registration_file', 'Has Registration File'),
('heading_customSearch', 'Custom Search'),
('heading_ifguest', 'If you are a guest of [--INSTITUTIONNAME--]'),
('heading_ifyouatinstitute', 'If you are at [--INSTITUTIONNAME--]'),
('heading_registeryourself', 'Register yourself'),
('help_contextadmin_about', ''),
('help_contextadmin_about_title', ''),
('help_contextgroups_about', 'About help'),
('help_contextgroups_about_title', 'About title'),
('help_contextpermissions_about', 'About help'),
('help_contextpermissions_about_title', 'About title'),
('help_groupadmin_about', 'About help'),
('help_groupadmin_about_title', 'About title'),
('help_moduleadmin_about', 'help text'),
('help_moduleadmin_about_title', 'help text'),
('help_permissions_about', 'About help'),
('help_permissions_about_title', 'About title'),
('help_postlogin_about', ''),
('help_postlogin_about_title', ''),
('help_stories_about', 'Description of website stories for help'),
('help_stories_about_title', 'About website stories'),
('help_stories_overview_add', 'Help for adding a story'),
('help_stories_title_add', 'Title for adding a story'),
('help_useradmin_about', 'help text'),
('help_useradmin_about_title', 'help text'),
('help_useradmin_overview_register', 'help text'),
('help_useradmin_title_register', 'help text'),
('hyperlink_generaterandomnumber', 'Generate your own user ID number'),
('instruction_enteratleast5digits', 'Please enter at least 5 digits in the NumericID field'),
('instruction_enteratmost9digits', 'Please enter at most 9 digits in the NumericID field'),
('instruction_enterfirstname', 'Please enter your first name'),
('instruction_entergenerateid', 'Please enter or generate a value for the NumericID field'),
('instruction_entersurname', 'Please enter your surname'),
('instruction_entervalidemail', 'Please enter a valid email address'),
('instruction_entervalidusername', 'Please enter a valid username'),
('is_registered', 'Is Registered'),
('label_confirmemail', 'Confirm e-mail address'),
('member_of_administrator', 'Is administrative member'),
('menu_userdetails', 'The phrase User Details'),
('message_confirmemailmessage', 'Please double check email address'),
('message_selfregister', 'The message that is displayed on the self registration form'),
('message_usestaffnum', 'Staff use staff number'),
('message_usestudentnum', 'Instructions to students to use their student number'),
('message_whenclickregister', 'When you click the \\"Register\\" button  you will be sent an email with your password.'),
('message_willfillinnumber', 'This will fill in the number for you at the left'),
('modules_badkey', 'bad key'),
('mod_calendarbase_addevent', 'Title Add an Event'),
('mod_calendarbase_dateofevent', 'Date of Event'),
('mod_calendarbase_desc', 'This module holds the calendar classes that allows it to be shared between user, context and workgroup'),
('mod_calendarbase_editevent', 'Title Edit Event'),
('mod_calendarbase_eventaddconfirm', 'Event has been added'),
('mod_calendarbase_eventdeleteconfirm', 'Event has been deleted'),
('mod_calendarbase_eventdeleterequestconfirm', 'Request confirmation to delete event'),
('mod_calendarbase_eventdetails', 'Event Details'),
('mod_calendarbase_eventeditconfirm', 'Event has been updated'),
('mod_calendarbase_eventtitle', 'Title of Event'),
('mod_calendarbase_multieventdeleterequestconfirm', 'Request confirmation to delete a multiday event'),
('mod_calendarbase_name', 'Calendar Base Module'),
('mod_calendarbase_previousmonth', 'Go to Previous Month'),
('mod_calendarbase_relatedwebsite', 'Related Website'),
('mod_calendarbase_saveevent', 'Save Event'),
('mod_calendarbase_selectdate', 'Select a Date'),
('mod_calendarbase_someonescalendar', 'Phrase to identify who calendar belongs to'),
('mod_class_files', 'Class Files in Module'),
('mod_contextadmin_about', 'About'),
('mod_contextadmin_active', 'Active'),
('mod_contextadmin_addnewcontext', 'Add a New [-context-]'),
('mod_contextadmin_assigments', 'Assigments'),
('mod_contextadmin_assignment', 'Manage Assignments and Tests'),
('mod_contextadmin_asstest', 'Assignment and Tests'),
('mod_contextadmin_calendar', 'Calendar'),
('mod_contextadmin_chat', 'Chat'),
('mod_contextadmin_confcourse', 'Configure [-context-]'),
('mod_contextadmin_configmm', 'Configure Freemind'),
('mod_contextadmin_confplugins', 'Configure Plugins'),
('mod_contextadmin_content', 'Manage Content'),
('mod_contextadmin_contextadmin', '[-context-] Admin'),
('mod_contextadmin_contextcode', '[-context-] Code'),
('mod_contextadmin_contextmanagement', '[-context-] Management'),
('mod_contextadmin_courseadmin', '[-context-] Admin'),
('mod_contextadmin_deletecontent', 'Delete Content'),
('mod_contextadmin_deletecontentquest', 'Are you sure you want to delete all the content?'),
('mod_contextadmin_deletecontext', 'Delete [-context-]'),
('mod_contextadmin_deletequest', 'Are you sure you want to delete the whole course?'),
('mod_contextadmin_desc', 'Context management.'),
('mod_contextadmin_diary', 'Diary'),
('mod_contextadmin_downloadstaticcontent', 'Download Static Content'),
('mod_contextadmin_editcontext', 'Edit [-context-]'),
('mod_contextadmin_error_length', 'Maximum of [-length-] characters allowed'),
('mod_contextadmin_err_required', 'Required Field!'),
('mod_contextadmin_essays', 'Essays'),
('mod_contextadmin_exportcontent', 'Export Static Content'),
('mod_contextadmin_exportstatic', 'Export Static Content'),
('mod_contextadmin_faq', 'FAQ'),
('mod_contextadmin_folderpath', 'Select a folder to upload static content'),
('mod_contextadmin_forum', 'Forum'),
('mod_contextadmin_glossary', 'Glossary'),
('mod_contextadmin_gotostatic', 'View Static Content'),
('mod_contextadmin_guests', 'Guests'),
('mod_contextadmin_help', 'Adding Courses'),
('mod_contextadmin_help_about', 'About Help'),
('mod_contextadmin_help_isactive', 'Active Help'),
('mod_contextadmin_help_isClosed', ' Closed Help '),
('mod_contextadmin_im', 'Instant Messanger'),
('mod_contextadmin_importcontent', 'Import Content'),
('mod_contextadmin_importdeletecontent', ' Delete all content and  Import new Content'),
('mod_contextadmin_importfail', 'Import Failed -- An Error occured while trying to upload Content'),
('mod_contextadmin_importhelp', 'Import Help'),
('mod_contextadmin_importintonode', ' Import into existing content'),
('mod_contextadmin_importselectnode', 'Select a Node under which the new Content must be Uploaded'),
('mod_contextadmin_importsuccess', 'Content was successfully imported!'),
('mod_contextadmin_importto', 'Import new content under '),
('mod_contextadmin_importwarning', 'Warning --  Importing Content will delete ALL existing Content'),
('mod_contextadmin_inactive', 'Inactive'),
('mod_contextadmin_inbasket', 'In Basket'),
('mod_contextadmin_isactive', 'Is Active'),
('mod_contextadmin_isclosed', 'Is Closed'),
('mod_contextadmin_isopen', 'Is Open'),
('mod_contextadmin_lecturers', 'Lecturers'),
('mod_contextadmin_managecontext', 'Manage [-context-]'),
('mod_contextadmin_marks', 'Marks'),
('mod_contextadmin_menutext', 'Menu Text'),
('mod_contextadmin_menutext2', 'Label for the words Menu text'),
('mod_contextadmin_name', 'ContextAdmin'),
('mod_contextadmin_notregistered', ' is not registered'),
('mod_contextadmin_organise', 'Manage Organisers'),
('mod_contextadmin_organisers', 'Manage Organisers'),
('mod_contextadmin_organizecontent', 'Organize Content'),
('mod_contextadmin_organizors', 'Organizors'),
('mod_contextadmin_outcomes', '[-context-] Outcomes'),
('mod_contextadmin_pbl', 'Problem Based Learning'),
('mod_contextadmin_readinglist', 'Reading List'),
('mod_contextadmin_returnadmin', ' Return to Admin Page'),
('mod_contextadmin_rubrics', 'Rubrics'),
('mod_contextadmin_save', 'Save'),
('mod_contextadmin_saved', 'Saved'),
('mod_contextadmin_staticcontent', 'Upload static content'),
('mod_contextadmin_staticwarning', 'Please make sure that file is of type zip'),
('mod_contextadmin_status', 'Status'),
('mod_contextadmin_students', 'Students'),
('mod_contextadmin_title', 'Title'),
('mod_contextadmin_toolbarname', '[-context-] Admin'),
('mod_contextadmin_unregisteredfeature', 'Feature not Registered'),
('mod_contextadmin_users', 'Manage Users'),
('mod_contextadmin_viewcontent', ' View Content'),
('mod_contextadmin_whatsnew', 'Whats New'),
('mod_contextadmin_worksheets', 'Work Sheets'),
('mod_contextgroups_desc', 'Used to manage the members of the current context.'),
('mod_contextgroups_manageguests', 'Manage guests'),
('mod_contextgroups_managelects', 'Manage lecturers'),
('mod_contextgroups_managestuds', 'Manage students'),
('mod_contextgroups_name', 'Manage members'),
('mod_contextgroups_notInContext', 'The user should join a context'),
('mod_contextgroups_notLect', ' The user should contact the site administrator'),
('mod_contextgroups_onlineguest', 'Guests Online'),
('mod_contextgroups_onlinelect', 'Lecturers Online'),
('mod_contextgroups_onlinestud', 'Students Online'),
('mod_contextgroups_toolbarname', 'Toolbar name'),
('mod_contextgroups_ttlGuest', 'Guests members'),
('mod_contextgroups_ttlLecturers', 'Lecturers members'),
('mod_contextgroups_ttlManage', 'Title used on the manage member home page'),
('mod_contextgroups_ttlManageMembers', 'Title used on the manage member edit page'),
('mod_contextgroups_ttlNotInContext', 'The user is not in a context'),
('mod_contextgroups_ttlNotLect', 'The user is in a context but not an Author'),
('mod_contextgroups_ttlStudents', 'Students members'),
('mod_contextgroups_ttlUsers', 'Users'),
('mod_contextpermissions_desc', 'Context permissions management.'),
('mod_contextpermissions_errNotRegistered ', ' Requiered module not registered '),
('mod_contextpermissions_hdrDecisiontable ', ' Decision Table Demo '),
('mod_contextpermissions_lblAbsolutePath', 'Used by condition type'),
('mod_contextpermissions_lblACL', 'Used by condition type'),
('mod_contextpermissions_lblAction', 'Action'),
('mod_contextpermissions_lblCondition', 'Condition'),
('mod_contextpermissions_lblConditionType', 'Used by condition type'),
('mod_contextpermissions_lblControllerActions', 'Get controller actions'),
('mod_contextpermissions_lblCreateAction', 'Create an action link'),
('mod_contextpermissions_lblCreateCondition', 'Create new condition'),
('mod_contextpermissions_lblCreateRule', 'Create new rule'),
('mod_contextpermissions_lblDependsOnContext', 'Used by condition type'),
('mod_contextpermissions_lblGenerateConfig', 'Generate Config'),
('mod_contextpermissions_lblIsAdmin', 'Used by condition type'),
('mod_contextpermissions_lblMustBeAdmin', 'Used by condition type'),
('mod_contextpermissions_lblRelativeContextPath', 'Used by condition type'),
('mod_contextpermissions_lblRules', 'Rules'),
('mod_contextpermissions_lblSelectACL', 'Used by condition type'),
('mod_contextpermissions_lblSelectContextACL', 'Used by condition type'),
('mod_contextpermissions_lblSelectContextGroup', 'Used by condition type'),
('mod_contextpermissions_lblSelectGroup', 'Used by condition type'),
('mod_contextpermissions_lblSelectModule', 'Used by condition type'),
('mod_contextpermissions_lblUpdatePerms', 'Update Permissions'),
('mod_contextpermissions_name', '[-CONTEXT-] permissions'),
('mod_contextpermissions_NotSaved', 'Not Saved'),
('mod_contextpermissions_saved', 'Saved'),
('mod_contextpermissions_toolbarname', 'Toolbar name'),
('mod_contextpermissions_ttlCondition', 'Condition page title'),
('mod_contextpermissions_ttlContextPermissions', 'Page Title'),
('mod_contextpermissions_ttlDecTbl', 'Context permissiond decision table page title'),
('mod_contextpermissions_ttlRegConf', 'Registration configuration page title'),
('mod_context_addchild', 'Add Child Node'),
('mod_context_addchildnode', 'Add a Sub Page'),
('mod_context_addsibling', 'Add Sibling Node'),
('mod_context_addsiblingnode', 'Add Page'),
('mod_context_author', 'author'),
('mod_context_authors', 'authors'),
('mod_context_buddies', 'The word Buddies'),
('mod_context_calendar', 'Calendar'),
('mod_context_communicate', 'The word Communicate'),
('mod_context_configure', 'Configure [-context-]'),
('mod_context_confplugins', 'Configure [-context-] Plugins for'),
('mod_context_content', 'Content'),
('mod_context_context', '[-context-]'),
('mod_context_contexts', 'contexts'),
('mod_context_convertpdf', 'Convert to Portable Document Format(PDF)'),
('mod_context_course', 'Course'),
('mod_context_coursecontent', 'Course Content'),
('mod_context_coursehome', '[-context-] Home'),
('mod_context_coursetosharefrom', '[-context-] to share from'),
('mod_context_delmessnode', 'Are you sure you want to delete this page?'),
('mod_context_desc', 'Context management.'),
('mod_context_duedates', 'Due Dates/Events'),
('mod_context_editnode', 'Edit a Node'),
('mod_context_entercourse', 'The phrase Enter Course'),
('mod_context_errmenutextminlength', 'Menu Text should be less than 100 characters'),
('mod_context_error_nocourseselected', 'No [-context-] was selected'),
('mod_context_error_notincourse', 'This action requires you to be in a [-context-] '),
('mod_context_error_title', 'Please Add a Title'),
('mod_context_error_title_length', 'Title should be less \\\\nthan 20 characters'),
('mod_context_errsuppmenutext', 'Please supply a Menu Text'),
('mod_context_errsupptitle', 'Please supply a Title'),
('mod_context_err_selectnode', 'Please select an Item before going to the next step'),
('mod_context_help', 'Help '),
('mod_context_inactive', 'The word inActive'),
('mod_context_inlobby', 'In Lobby'),
('mod_context_javascript', 'Java Script'),
('mod_context_javascript_help', 'The Java Script Help'),
('mod_context_joincontext', 'The phrase Join Context'),
('mod_context_last5post', ' Last 5 Post'),
('mod_context_learning', 'The word Learning'),
('mod_context_lobby', 'The Word Lobby'),
('mod_context_loggedinas', 'Currently Logged in as '),
('mod_context_loggedintocourse', 'Logged into [-context-]'),
('mod_context_manage', 'Manage'),
('mod_context_managecontent', ' Manage Content '),
('mod_context_move', 'Move'),
('mod_context_mycal', 'My Calendar'),
('mod_context_mypersonalspace', 'The phrase My Personal Space'),
('mod_context_name', 'Context'),
('mod_context_nav_first', 'Go To First Page'),
('mod_context_nav_last', 'Go To Last Page'),
('mod_context_nav_next', 'Go To Next Page'),
('mod_context_nav_prev', 'Go To Previous Page'),
('mod_context_next', 'Next &raquo;'),
('mod_context_noduedates', 'No Due Dates/Events'),
('mod_context_onlinelect', 'lecturers Online'),
('mod_context_onlinestud', 'Students Online'),
('mod_context_organizers', 'Organizers'),
('mod_context_pagecontents', 'Page Contents'),
('mod_context_plugins', 'Change [-context-] Plugins'),
('mod_context_position', 'Position'),
('mod_context_readonly', 'readonly'),
('mod_context_selectchapter', 'Select a chapter that you want to share'),
('mod_context_selectcourse', 'Select a [-context-] from which you want to share from'),
('mod_context_selectposition', 'Select a postion under which you want to the shared Item to be displayed'),
('mod_context_selnode', 'Select a Item to be shared'),
('mod_context_sharedaddordelete', 'Add or Delete another Shared Items'),
('mod_context_sharedcontent', ' Shared Content '),
('mod_context_sharedfrom', 'Shared from'),
('mod_context_shareditem', ' Shared Item'),
('mod_context_sharenodeadded', 'Successfully saved !!!'),
('mod_context_step', 'Step '),
('mod_context_step1info', 'Select a [-context-]'),
('mod_context_step2info', 'Select a Chapter'),
('mod_context_step3info', 'Set the Position '),
('mod_context_succsave', 'Successfully saved at '),
('mod_context_toolbarname', 'context'),
('mod_context_webpage', 'The word Webpage'),
('mod_context_welcome', 'Welcom to '),
('mod_context_whoson', 'Who''s Online'),
('mod_decisiontable_desc', 'Decision table management.'),
('mod_decisiontable_name', 'Decision Table'),
('mod_dublincoremetadata_desc', 'Dublin Core Metadata'),
('mod_dublincoremetadata_name', 'Dublin Core Metadata'),
('mod_dublin_audience', 'Audience'),
('mod_dublin_contributor', 'Contributor'),
('mod_dublin_coverage', 'Coverage'),
('mod_dublin_creator', 'Creator'),
('mod_dublin_date', 'Date'),
('mod_dublin_dcm', 'Dublin Core Metadata'),
('mod_dublin_description', 'Description'),
('mod_dublin_format', 'Format'),
('mod_dublin_identifier', 'Identifier'),
('mod_dublin_language', 'Language'),
('mod_dublin_publisher', 'Publisher'),
('mod_dublin_relationship', 'Relationship'),
('mod_dublin_rights', 'Rights'),
('mod_dublin_source', 'Source'),
('mod_dublin_sourceurl', 'Source URL'),
('mod_dublin_subject', 'Subject'),
('mod_dublin_type', 'Type'),
('mod_error_passwd', 'password doesn''t match'),
('mod_groupadmin_back ', ' Back to group admin '),
('mod_groupadmin_btnInsert ', ' Insert '),
('mod_groupadmin_btnRemove ', ' Remove '),
('mod_groupadmin_delete ', ' Delete group '),
('mod_groupadmin_desc', 'Group Administration'),
('mod_groupadmin_edit ', ' Edit group '),
('mod_groupadmin_errNotAdmin ', ' Error message not an Administrator '),
('mod_groupadmin_hdrCreateGroup ', ' Create Group '),
('mod_groupadmin_hdrDeleteGroup ', ' Delete Group '),
('mod_groupadmin_hdrEditGroup ', ' Edit Group '),
('mod_groupadmin_hdrFirstName ', ' First name '),
('mod_groupadmin_hdrGroupDesc ', ' Group Description '),
('mod_groupadmin_hdrGroupList ', ' Group List '),
('mod_groupadmin_hdrGroupName ', ' Group Name '),
('mod_groupadmin_hdrMemberList ', ' List of all members '),
('mod_groupadmin_hdrSurname ', ' Surname '),
('mod_groupadmin_hdrUsers ', ' List of all users '),
('mod_groupadmin_hdrViewGroup ', ' View Group '),
('mod_groupadmin_hlpCreateGroup', ' Create group help '),
('mod_groupadmin_hlpDeleteGroup', ' Delete group help '),
('mod_groupadmin_hlpEditGroup', ' Edit group help '),
('mod_groupadmin_hlpGroupAdmin ', ' Group admin help '),
('mod_groupadmin_hlpViewGroup', ' View group help '),
('mod_groupadmin_lblDescription ', ' Description '),
('mod_groupadmin_lblInFolder ', ' In folder '),
('mod_groupadmin_lblInTotal ', ' In total '),
('mod_groupadmin_lblMembersFound ', ' Members found '),
('mod_groupadmin_lblName ', ' Name '),
('mod_groupadmin_lblParent ', ' Parent group '),
('mod_groupadmin_lblUpdated ', ' Updated '),
('mod_groupadmin_lblUpdatedBy ', ' Updated by '),
('mod_groupadmin_lblUpdatedOn ', ' Updated on '),
('mod_groupadmin_msgDeleted ', ' Deleted '),
('mod_groupadmin_msgDeleteGroup ', ' Delete a group confirm '),
('mod_groupadmin_msgInvalidField ', ' Invalid Field '),
('mod_groupadmin_msgNotSaved ', ' Not Saved '),
('mod_groupadmin_msgSaved ', ' Saved '),
('mod_groupadmin_name', 'Group Administration'),
('mod_groupadmin_ttlCreateGroup ', ' Create a group '),
('mod_groupadmin_ttlDeleteGroup ', ' Delete a group '),
('mod_groupadmin_ttlEditGroup ', ' Edit a groups members '),
('mod_groupadmin_ttlGroupAdmin ', ' Group Admin '),
('mod_groupadmin_ttlViewGroup ', ' View a groups members '),
('mod_hasdependants', 'cannot delete with dependents'),
('mod_help_desc', 'Used to display help about a module feature'),
('mod_help_helpph', 'Help about something'),
('mod_help_name', 'Help'),
('mod_htmlelements_desc', 'Used as a support module for generating html tags'),
('mod_htmlelements_name', 'htmlelements'),
('mod_html_help_editor', 'Copy and Paste Help'),
('mod_language_desc', 'Used to allow multilingualisation option in KEWL.NextGen.'),
('mod_language_name', 'KEWL.NextGen Language'),
('mod_moduleadmin_asksure', 'are you sure you want to register'),
('mod_moduleadmin_authors', 'Authors'),
('mod_moduleadmin_batch1', 'Batch Registration Menu'),
('mod_moduleadmin_batch2', 'Batch Registration'),
('mod_moduleadmin_changepassword', 'Change a user''s password'),
('mod_moduleadmin_core', 'Warning message for core modules'),
('mod_moduleadmin_depend1', 'Module Dependencies'),
('mod_moduleadmin_deregconfirm', 'text shown when module is deregisterd'),
('mod_moduleadmin_deregisterbatch1', 'Batch DeRegistration Menu'),
('mod_moduleadmin_deregisterbatch2', 'Batch DeRegistration'),
('mod_moduleadmin_deregsure', 'text shown when deregister option chosen'),
('mod_moduleadmin_desc', 'Manage the modules on this KEWL.NextGen site.'),
('mod_moduleadmin_err_nofile', 'no register file'),
('mod_moduleadmin_go', 'go to module'),
('mod_moduleadmin_info', 'Module Info'),
('mod_moduleadmin_info2', 'Module Info'),
('mod_moduleadmin_menucat', 'menu category'),
('mod_moduleadmin_modname', 'Module Name'),
('mod_moduleadmin_name', 'Module Admin'),
('mod_moduleadmin_needinfo', 'Error message for needed info'),
('mod_moduleadmin_needmodule', 'Error message for needed module'),
('mod_moduleadmin_noselect', 'No Modules Selected'),
('mod_moduleadmin_notadmin', 'error message to show non-admins'),
('mod_moduleadmin_notreg', 'general error message'),
('mod_moduleadmin_problem1', 'problem message'),
('mod_moduleadmin_rdate', 'Release Date'),
('mod_moduleadmin_regconfirm', 'text shown when module is registerd'),
('mod_moduleadmin_replacetext', 'text for replacing word'),
('mod_moduleadmin_return', 'Return to moduleadmin'),
('mod_moduleadmin_tables', 'tables'),
('mod_moduleadmin_textelementsfor', 'Text Elements for'),
('mod_moduleadmin_textproblem', 'error message for missing/invalid texts'),
('mod_moduleadmin_version', 'version'),
('mod_moduleadmin_worddesc', 'The word description'),
('mod_no_text', 'no text elements'),
('mod_permissions_desc', 'Permissions management'),
('mod_permissions_hlpPermissions ', ' Help Permissions '),
('mod_permissions_name', 'Permissions'),
('mod_phrase_classfiles', 'class files in module'),
('mod_postlogin_announcements', 'Announcements'),
('mod_postlogin_currentlyincontext', 'Text to show current context'),
('mod_postlogin_desc', 'Provides interface to KEWL.NextGen when the user has logged in.'),
('mod_postlogin_helphowto', 'Explaining how to use help'),
('mod_postlogin_helptitle', 'Title for the explanation of how to get help'),
('mod_postlogin_messages', 'Messages'),
('mod_postlogin_name', 'KEWL.NextGen Default post loging module'),
('mod_postlogin_news', 'News'),
('mod_redirect_contactadmin', 'The phrase Please contact your system administrator.'),
('mod_redirect_contactadminaccess', 'The phrase Please contact your system administrator about access rights.'),
('mod_redirect_desc', 'Redirect page'),
('mod_redirect_entercourse', 'The phrase Select a course from the list below.'),
('mod_redirect_name', 'Redirect'),
('mod_redirect_noaction', 'The phrase You do not have permission to invoke the action'),
('mod_redirect_nocourse', 'The phrase You need to be in a course to access'),
('mod_redirect_nopermission', 'The phrase You do not have permission to access'),
('mod_redirect_notregistered', 'The phrase has not been registered.'),
('mod_redirect_nousercourses', 'The phrase You are not registered for any courses.'),
('mod_redirect_register', 'The word Register'),
('mod_redirect_registermodule', 'The phrase Register the module'),
('mod_security_desc', 'Manage the users on this KEWL.NextGen site.'),
('mod_security_emailsysadmin', 'If you continue to have problems, please email the System Administrator'),
('mod_security_forgotpassword', 'I forgot my password'),
('mod_security_incorrectpassword', 'Incorrect Password'),
('mod_security_incorrectpasswordmessage', 'The password you have given is incorrect. Please try again or request a new password.'),
('mod_security_javascriptwarning', 'Warn to user that JavaScript has to be enabled'),
('mod_security_name', 'Security'),
('mod_security_noaccount', 'Account doesn''t exist'),
('mod_security_noaccountmessage', 'The account you have tried to access does not exist - username is not valid. Please try another username or register to get access.'),
('mod_security_no_ldap', 'no ldap error message'),
('mod_security_requestnewpassword', 'Request New Password'),
('mod_show_classes', 'show module classes'),
('mod_stories_add', 'Add story'),
('mod_stories_addalt', 'Label for add icon'),
('mod_stories_addlabel', 'Add a story category'),
('mod_stories_alsoavailable', 'Note that the story is also available in other languages'),
('mod_stories_alwaysontop', 'Is the story always on top (sticky)'),
('mod_stories_alwaysontopalt', 'Label for sticky icon'),
('mod_stories_anothercat', 'View another story category'),
('mod_stories_confirm', 'Confirm deletion of'),
('mod_stories_delalt', 'Label for delete icon'),
('mod_stories_desc', 'Manage the stories that appear on the KNG website.'),
('mod_stories_dpick', 'Pick the expiration date'),
('mod_stories_edit', 'Edit [-story-]'),
('mod_stories_editalt', 'Label for edit icon'),
('mod_stories_editlabel', 'Edit story category'),
('mod_stories_expired', 'Message to display for alt tag when story has expired'),
('mod_stories_expiredalt', 'Label for expired clock icon'),
('mod_stories_help_main', 'Main help block'),
('mod_stories_isactivealt', 'Label for isactive icon'),
('mod_stories_isnotactivealt', 'Label for isnotactive icon'),
('mod_stories_mainleftside', 'The instructions for the left side during view'),
('mod_stories_name', '[-stories-]'),
('mod_stories_news', 'news'),
('mod_stories_notalwaysontopalt', 'Label for non sticky icon'),
('mod_stories_notstickyalt', 'Label for non sticky icon'),
('mod_stories_parentId', 'The ID field of which this is a translation'),
('mod_stories_plaintext', 'Plain text mode'),
('mod_stories_sticky', 'Is the story sticky (stay on top)'),
('mod_stories_stickyalt', 'Label for sticky icon'),
('mod_stories_story', 'story'),
('mod_stories_title', 'Manage stories'),
('mod_stories_toolbarname', 'stories name'),
('mod_stories_translate', 'Translate this story'),
('mod_stories_translatelabel', 'Translate story'),
('mod_stories_val_catnotnull', 'Validation text if category is null'),
('mod_stories_val_lang2chargt', 'Validation if languate code is too long'),
('mod_stories_val_langnotnull', 'Validation text if language is null'),
('mod_stories_wyswyg', 'WYSWYG mode'),
('mod_storycategoryadmin_action', 'The word Action'),
('mod_storycategoryadmin_addnew', 'The phrase Add New Category'),
('mod_storycategoryadmin_cancel', 'The word Cancel'),
('mod_storycategoryadmin_category', 'The word Category'),
('mod_storycategoryadmin_creatorid', 'The phrase Created By'),
('mod_storycategoryadmin_datecreated', 'The phrase Date Created'),
('mod_storycategoryadmin_datemodified', 'The phrase Date Modified'),
('mod_storycategoryadmin_delalt', 'The word Delete'),
('mod_storycategoryadmin_desc', 'Story categories management for KEWL.NextGen'),
('mod_storycategoryadmin_lefteditist', 'Instructions to appear in the left frame during edit'),
('mod_storycategoryadmin_leftinstructions', 'Instructions to appear in the left frame'),
('mod_storycategoryadmin_modifierid', 'The phrase Modified By'),
('mod_storycategoryadmin_name', 'Story categories'),
('mod_storycategoryadmin_title', 'The title for the main page'),
('mod_storycategoryadmin_titleth', 'The word Title'),
('mod_storycategory_addlabel', 'Add a story category'),
('mod_storycategory_confirm', 'Instructions for delete popup'),
('mod_storycategory_editlabel', 'Edit story category'),
('mod_storycategory_exists', 'The category already exists'),
('mod_strings_desc', 'Used to provide strings functions to other modules'),
('mod_strings_exlink', 'Alt tag for external link'),
('mod_strings_name', 'Strings'),
('mod_sysconfig_action', 'The word Action'),
('mod_sysconfig_adddisabled', 'The add feature is disabled'),
('mod_sysconfig_addiconalt', 'Statement that the add feature is disabled'),
('mod_sysconfig_addlabel', 'Adding a parameter'),
('mod_sysconfig_addtxt', 'Text label for the add parameter step'),
('mod_sysconfig_deleted', 'Parameter deleted'),
('mod_sysconfig_desc', 'Used to system configuration variables to be stored in the database'),
('mod_sysconfig_edlabel', 'Text label for the edit'),
('mod_sysconfig_edtxt', 'Edit parameter'),
('mod_sysconfig_err_dupattempt', 'Message to display if an attempt is made to produce a duplicate configuration entry'),
('mod_sysconfig_err_keynotexist', 'Message to display if a lookup fails because the module and name combination are not found'),
('mod_sysconfig_firstep', 'Step 1'),
('mod_sysconfig_modtxt', 'Module'),
('mod_sysconfig_name', 'System configuration'),
('mod_sysconfig_noconfprop', 'This module has no configurable properties'),
('mod_sysconfig_nomoduleset', 'No module set'),
('mod_sysconfig_paramname', 'Parameter name'),
('mod_sysconfig_paramvalue', 'Parameter value'),
('mod_sysconfig_reqadmin', 'Instructions that admin rights are needed to edit configuration'),
('mod_sysconfig_secondstep', 'Step 2'),
('mod_sysconfig_selectsystem', 'Select system type'),
('mod_sysconfig_step1', 'Step 1'),
('mod_sysconfig_step2', 'Step 2'),
('mod_sysconfig_testlabel', 'A label for use in testing, and which does not need translating.'),
('mod_sysconfig_title', 'Module title'),
('mod_sysconfig_unrecmo', 'Unrecognized mode (where mode refers to edit mode, add mode, or delete mode'),
('mod_sysconfig_youcanadd', 'Add a new configuration parameter'),
('mod_toolbar_action', 'The word Action'),
('mod_toolbar_addnewlink', 'The phrase Add New Link'),
('mod_toolbar_administrator', 'The word Administrator'),
('mod_toolbar_adminonly', 'The phrase Admin Only'),
('mod_toolbar_alumnimanagement', 'Alumni Management'),
('mod_toolbar_availacl', 'Available Access Control Lists (ACLs)'),
('mod_toolbar_availcongroup', 'Available Context Groups'),
('mod_toolbar_availgroup', 'Available Groups'),
('mod_toolbar_bottom', 'The word Bottom'),
('mod_toolbar_category', 'The word Category'),
('mod_toolbar_confmodulelinks', 'The phrase Configure Module Links'),
('mod_toolbar_confperm', 'Configure Link Permissions'),
('mod_toolbar_content', 'The phrase Manage Content'),
('mod_toolbar_dependscontext', 'The phrase Depends Context'),
('mod_toolbar_desc', 'Navigation Bar'),
('mod_toolbar_develop', 'The phrase For Developers'),
('mod_toolbar_developers', 'The phrase For Developers'),
('mod_toolbar_editlink', 'The phrase Edit Link'),
('mod_toolbar_guest', 'The word Guest'),
('mod_toolbar_icon', 'The word Icon'),
('mod_toolbar_langcode', 'The phrase Language Code'),
('mod_toolbar_leavecontext', 'The phrase leave context'),
('mod_toolbar_lecturer', 'The word Lecturer'),
('mod_toolbar_linkpermissions', 'The phrase Link Permissions'),
('mod_toolbar_linksettings', 'The phrase Link Settings'),
('mod_toolbar_loggedin', 'The phrase Currently logged in as'),
('mod_toolbar_lowermiddle', 'Lower Middle'),
('mod_toolbar_menu', 'The word Menu'),
('mod_toolbar_middle', 'The word Middle'),
('mod_toolbar_modnotfound', 'Module [-module-] Not Found'),
('mod_toolbar_module', 'The word Module'),
('mod_toolbar_modulesettings', 'The phrase Module Settings'),
('mod_toolbar_name', 'Site Admin'),
('mod_toolbar_nolinks', 'The phrase No Links'),
('mod_toolbar_organise', 'The word Manage Organisers'),
('mod_toolbar_organisers', 'The word Manage Organisers'),
('mod_toolbar_page', 'The word Page'),
('mod_toolbar_permissions', 'The word Permissions'),
('mod_toolbar_positioninmenu', 'The phrase Position in Menu'),
('mod_toolbar_restoredefaultperms', 'Restore Default Permissions'),
('mod_toolbar_restoredefaults', 'Restore Default Settings'),
('mod_toolbar_role', 'The word Role'),
('mod_toolbar_selectacl', 'The phrase Select an access control list (ACL)'),
('mod_toolbar_selectcategory', 'The phrase Select Category'),
('mod_toolbar_selectcongroup', 'The phrase Select a context group'),
('mod_toolbar_selectedacl', 'Selected Access Control Lists (ACLs)'),
('mod_toolbar_selectedcongroup', 'Selected Context Groups'),
('mod_toolbar_selectedgroup', 'Selected Groups'),
('mod_toolbar_selectgroup', 'The phrase Select a group'),
('mod_toolbar_selectmodule', 'Select New Module'),
('mod_toolbar_settosite', 'Display to everyone'),
('mod_toolbar_sidemenu', 'The word Side Menu'),
('mod_toolbar_site', 'The phrase Manage Site'),
('mod_toolbar_siteadmin', 'The phrase Site Administration'),
('mod_toolbar_student', 'The word Student'),
('mod_toolbar_toolbar', 'The word Toolbar'),
('mod_toolbar_top', 'The word Top'),
('mod_toolbar_uppermiddle', 'Upper Middle'),
('mod_toolbar_users', 'The phrase Manage Users'),
('mod_toolbar_welcome', 'The word Welcome'),
('mod_useradmin_adminNoDelete', 'Admin cannot self-delete'),
('mod_useradmin_browsebysurname', 'Browse by Surname'),
('mod_useradmin_changepassword', 'Change a user''s password'),
('mod_useradmin_changepassword2', 'Change a user''s password'),
('mod_useradmin_changepicture', 'Change Picture'),
('mod_useradmin_cleanup', 'Cleanup unused accounts'),
('mod_useradmin_deletesected', 'delete selected'),
('mod_useradmin_desc', 'Manage the users on this KEWL.NextGen site.'),
('mod_useradmin_greet1', 'Dear user'),
('mod_useradmin_greet2', 'you have been added'),
('mod_useradmin_greet3', 'email sent'),
('mod_useradmin_greet4', 'your nextgen details'),
('mod_useradmin_greet5', 'nextgen Robot'),
('mod_useradmin_greet6', 'registration header'),
('mod_useradmin_greet7', 'login instructions'),
('mod_useradmin_help', 'Help for registration'),
('mod_useradmin_ldapnochange', 'LDAP users can''t change their password'),
('mod_useradmin_listallusers', 'List All Users'),
('mod_useradmin_listingusersbysurname', 'Listing Users by Surname'),
('mod_useradmin_name', 'User Admin'),
('mod_useradmin_newuseradded', 'New User Added'),
('mod_useradmin_nogd', 'warning for lack of GD support'),
('mod_useradmin_nomatch', 'The username and email addr do not match'),
('mod_useradmin_otherinfo', 'Other Info'),
('mod_useradmin_passwordreset', 'user has requested change of password'),
('mod_useradmin_register1', 'Register button'),
('mod_useradmin_resetpassword', 'reset password string'),
('mod_useradmin_searchforuser', 'Search for User'),
('mod_useradmin_searchresultsfor', 'Search Results for'),
('mod_useradmin_selfdelete0', 'delete own account'),
('mod_useradmin_selfdelete1', 'account self-deleted'),
('mod_useradmin_showingallusers', 'Showing All Users'),
('mod_useradmin_unusedaccounts', 'Unused Accounts'),
('mod_useradmin_updatedetails', 'Update Details'),
('mod_useradmin_welcome', 'welcome message'),
('mod_word_addtext', 'Add texts for module'),
('mod_word_deregister', 'Deregister module'),
('mod_word_module', 'the word module'),
('mod_word_options', 'Options'),
('mod_word_register', 'the word register for module'),
('mod_word_textelement', 'Text Element in module'),
('mod_word_textelements', 'Text Elements'),
('need_email', 'need to supply email addr'),
('need_password', 'need to supply password'),
('need_username', 'need to supply username'),
('option_selectatitle', 'Select a title'),
('Pagetext_emailaddress', 'Email Address'),
('phrasebacktohomepage', 'Back to Home Page'),
('phrase_confirmdelete', 'Confirm delete'),
('phrase_confirmdeletion', 'short sentence \\\\\\"confirm deletion\\\\\\"'),
('phrase_confirmlogout', 'Confirm from user that they want to logout'),
('phrase_creationdate', 'the date the account was created'),
('phrase_dateposted', 'date posted'),
('phrase_emailaddress', 'email address'),
('phrase_expirationdate', 'expire date'),
('phrase_finalstep', 'Final Step'),
('phrase_firstname', 'the phrase firstname'),
('phrase_goback', 'the phrase Go Back'),
('phrase_goto_login', 'go to login'),
('phrase_howcreated', 'how the account was created'),
('phrase_id', 'Id'),
('phrase_inactive_user', 'user account inactive'),
('phrase_invalid_login', 'invalid login or password'),
('phrase_isactive', 'if the account is active'),
('phrase_joincourse', 'The phrase Join Course'),
('phrase_languagelist', 'language selection'),
('phrase_lastlogin', 'last login'),
('phrase_loggedinas', 'word login as'),
('phrase_networkid', 'network id'),
('phrase_notfound', 'Not Found'),
('phrase_numberoflogins', 'Number of logins'),
('phrase_postedby', 'posted by'),
('phrase_reset_image', 'text for reset image'),
('phrase_selectcourse', 'Select course'),
('phrase_selectskin', 'skin selection'),
('phrase_timeactive', 'time activity'),
('phrase_unrecognizedaction', 'unrecognised Action'),
('phrase_upload_image', 'text for uploading images'),
('step1', 'Step 1'),
('step2', 'Step 2'),
('step3', 'Step 3'),
('step4', 'Step 4'),
('step5', 'Step 5'),
('step6', 'Step 6'),
('text_selectall', 'Select All'),
('text_selectnone', 'Select None'),
('title_assocprof', 'title assocprof'),
('title_dr', 'title dr'),
('title_miss', 'title Miss'),
('title_mr', 'title Mr'),
('title_mrs', 'title Mrs'),
('title_ms', 'title Ms'),
('title_prof', 'title prof'),
('title_rev', 'title rev'),
('user details', 'user details'),
('userid_taken', 'userid already used'),
('username_taken', 'username taken'),
('user_added', 'new user added'),
('warning_pleasenote', 'Please note'),
('warning_usernamenospaces', 'Usernames may not contain any spaces'),
('word_abstract', 'abstract'),
('word_add', 'the word add'),
('word_author', 'the word author'),
('word_back', 'the word back as in go back'),
('word_cancel ', ' The word Cancel '),
('word_category', 'Category'),
('word_change', 'the word change'),
('word_close', 'The word Close'),
('word_confirm', 'the word confirm'),
('word_country', 'the word country'),
('word_course', 'Course'),
('word_create ', ' The word Create '),
('word_current', 'the word current ie now'),
('word_edit', 'Edit'),
('word_female', 'Female'),
('word_go', 'the word go'),
('word_help', 'Help'),
('word_home ', ' The site home page '),
('word_inlobby', 'word lobby'),
('word_language', 'The word language'),
('word_leave', 'the word leave'),
('word_list', 'the word list'),
('word_login', 'the word login'),
('word_logout', 'the word logout'),
('word_male', 'Male'),
('word_new', 'the word new'),
('word_no', 'The word \\''No\\'''),
('word_ok', 'the word OK'),
('word_old', 'the word old'),
('word_on', 'the word on'),
('word_password', 'the word password'),
('word_problem', 'the word ''problem'''),
('word_register', 'the word register'),
('word_save', 'the word save'),
('word_select', 'the word select'),
('word_sex', 'the word sex'),
('word_sincerely', 'the word sincerely'),
('word_story', 'story'),
('word_surname', 'the word surname'),
('word_title', 'Title'),
('word_user', 'the word user'),
('word_userid', 'the word userId'),
('word_username', 'The word username'),
('word_yes', 'The word \\''Yes\\''');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_loggedinusers`
-- 

CREATE TABLE `tbl_loggedinusers` (
  `id` int(11) NOT NULL auto_increment,
  `userId` varchar(25) NOT NULL default '0',
  `ipAddress` varchar(100) NOT NULL default 'init',
  `sessionId` varchar(100) NOT NULL default 'init',
  `whenLoggedIn` datetime NOT NULL default '0000-00-00 00:00:00',
  `WhenLastActive` datetime NOT NULL default '0000-00-00 00:00:00',
  `isInvisible` tinyint(1) NOT NULL default '0',
  `coursecode` varchar(100) NOT NULL default 'init',
  `themeUsed` varchar(100) NOT NULL default 'init',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table is used to maintain state and enable communicatio' AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `tbl_loggedinusers`
-- 

INSERT INTO `tbl_loggedinusers` (`id`, `userId`, `ipAddress`, `sessionId`, `whenLoggedIn`, `WhenLastActive`, `isInvisible`, `coursecode`, `themeUsed`) VALUES (2, '1', '127.0.0.1', '0094b3f042e92a824017be81f434d461', '2006-03-12 16:52:25', '2006-03-12 17:14:40', 0, 'lobby', 'default');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_menu_category`
-- 

CREATE TABLE `tbl_menu_category` (
  `id` varchar(32) NOT NULL default 'init',
  `category` varchar(120) default NULL,
  `module` varchar(60) default NULL,
  `adminOnly` tinyint(4) NOT NULL default '0',
  `permissions` varchar(120) default NULL,
  `dependsContext` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_menu_category`
-- 

INSERT INTO `tbl_menu_category` (`id`, `category`, `module`, `adminOnly`, `permissions`, `dependsContext`) VALUES ('init@11412745932645', 'menu_admin-3', 'moduleadmin', 1, '', 0),
('init@11412745938922', 'admin', 'moduleadmin', 1, '', 0),
('init@11412745939250', 'page_admin_site', 'moduleadmin', 1, '', 0),
('init@11412745963014', 'page_admin_users', 'useradmin', 1, '', 0),
('init@11412745963707', 'menu_postlogin-2||mydetails||menu_userdetails', 'useradmin', 0, '|site|_con_', 0),
('init@11412745968308', 'menu_user-1||mydetails||menu_userdetails', 'useradmin', 0, '|site|_con_', 0),
('init@11412745984926', 'page_admin_users', 'groupadmin', 1, '', 0),
('init@11412745993079', 'page_admin_site', 'sysconfig', 1, '', 0),
('init@11412745993411', 'page_admin_site|editlinks|admin|mod_toolbar_confmodulelinks', 'toolbar', 1, '|Site Admin', 0),
('init@11412745993620', 'menu_postlogin-3|||admin|mod_toolbar_siteadmin', 'toolbar', 1, '|Site Admin', 0),
('init@11412745996811', 'page_admin_users', 'permissions', 1, '', 0),
('init@11412745999271', 'admin', 'toolbar', 1, '|Site Admin', 0),
('init@11412746015849', 'menu_context-1||content|go_to_course_content|mod_context_content', 'context', 0, '', 0),
('init@11412746024173', 'menu_context-2||courseadmin|configure|mod_contextadmin_contextmanagement', 'contextadmin', 0, '||_con_Lecturers', 0),
('init@11412746025909', 'admin', 'contextadmin', 0, '|Lecturers,Site Admin|_con_Lecturers', 0),
('init@11412746025987', 'page_admin_content', 'contextadmin', 1, '|Lecturers,Site Admin|_con_Lecturers', 0),
('init@11412746026948', 'page_lecturer_content|exportcontent|download|mod_contextadmin_exportstatic', 'contextadmin', 0, '|Lecturers,Site Admin|_con_Lecturers', 0),
('init@11412746028076', 'page_lecturer_content|importone|folder_up|mod_contextadmin_importcontent', 'contextadmin', 0, '|Lecturers,Site Admin|_con_Lecturers', 0),
('init@11412746031297', 'page_lecturer_users||groups', 'contextgroups', 0, '|Site Admin|_con_Lecturers', 1),
('init@11412746031986', 'page_admin_users||groups', 'contextgroups', 1, '|Site Admin|_con_Lecturers', 1),
('init@11412746035726', 'page_admin_users', 'contextpermissions', 1, '', 0),
('init@11412746036736', 'menu_context-3|||groups', 'contextgroups', 0, '|Site Admin|_con_Lecturers', 1),
('init@11412746044020', 'page_admin_site', 'storycategoryadmin', 1, '', 0),
('init@11412746049060', 'page_admin_site', 'stories', 1, '', 0),
('init@11412746051357', '', 'help', 0, '', 0),
('init@11412746058492', '', 'htmlelements', 0, '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_modules`
-- 

CREATE TABLE `tbl_modules` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` varchar(50) NOT NULL default '0',
  `module_authors` text,
  `module_releasedate` datetime default NULL,
  `module_version` varchar(20) default NULL,
  `module_path` varchar(255) default NULL,
  `isAdmin` tinyint(1) NOT NULL default '0',
  `isVisible` tinyint(1) NOT NULL default '1',
  `hasAdminPage` tinyint(1) default '1',
  `isContextAware` tinyint(1) NOT NULL default '0',
  `dependsContext` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

-- 
-- Dumping data for table `tbl_modules`
-- 

INSERT INTO `tbl_modules` (`id`, `module_id`, `module_authors`, `module_releasedate`, `module_version`, `module_path`, `isAdmin`, `isVisible`, `hasAdminPage`, `isContextAware`, `dependsContext`) VALUES (1, 'decisiontable', 'Jonathan Abrahams', '0000-00-00 00:00:00', '0.1', 'decisiontable', 1, 1, 1, 0, 0),
(2, 'redirect', 'Megan Watson', '0000-00-00 00:00:00', '1.0', 'redirect', 0, 0, 0, 0, 0),
(3, 'moduleadmin', 'James Scoble', '0000-00-00 00:00:00', '1.5', 'moduleadmin', 1, 1, 1, 0, 0),
(4, 'postlogin', 'Derek Keats, James Scoble', '0000-00-00 00:00:00', '0.2', 'postlogin', 0, 0, 0, 0, 0),
(5, 'security', 'Derek Keats, Sean Legassick, James Scoble', '0000-00-00 00:00:00', '1.5', 'security', 0, 0, 0, 0, 0),
(6, 'useradmin', 'James Scoble', '0000-00-00 00:00:00', '1.1', 'useradmin', 0, 1, 1, 0, 0),
(7, 'groupadmin', 'Jonathan Abrahams', '0000-00-00 00:00:00', '0.1', 'groupadmin', 1, 1, 1, 1, 0),
(8, 'permissions', 'Jonathan Abrahams', '0000-00-00 00:00:00', '1.1', 'permissions', 1, 1, 0, 1, 0),
(9, 'sysconfig', 'Derek Keats', '0000-00-00 00:00:00', '0.2', 'sysconfig', 1, 1, 0, 0, 0),
(10, 'toolbar', 'Megan Watson', '0000-00-00 00:00:00', '1.0', 'toolbar', 1, 1, 0, 0, 0),
(11, 'dublincoremetadata', 'Wesley Nitsckie', '0000-00-00 00:00:00', '1.3', 'dublincoremetadata', 0, 1, 0, 0, 0),
(12, 'context', 'Wesley Nitsckie, Shulam Mtegha', '0000-00-00 00:00:00', '1.1', 'context', 0, 1, 1, 0, 0),
(13, 'contextadmin', 'Wesley Nitsckie', '0000-00-00 00:00:00', '1.1', 'contextadmin', 0, 1, 1, 1, 0),
(14, 'contextgroups', 'Jonathan Abrahams', '0000-00-00 00:00:00', '1.0', 'contextgroups', 0, 1, 0, 1, 1),
(15, 'contextpermissions', 'Jonathan Abrahams', '0000-00-00 00:00:00', '0.1', 'contextpermissions', 1, 1, 1, 1, 0),
(16, 'storycategoryadmin', 'Derek Keats', '0000-00-00 00:00:00', '0.1', 'storycategoryadmin', 1, 0, 0, 0, 0),
(17, 'stories', 'Derek Keats', '0000-00-00 00:00:00', '1.0', 'stories', 1, 1, 0, 0, 0),
(18, 'language', 'Derek Keats,Prince Mbekwa', '0000-00-00 00:00:00', '1.0', 'language', 0, 1, 0, 0, 0),
(19, 'help', 'Derek Keats', '0000-00-00 00:00:00', '0.1a', 'help', 0, 0, 0, 0, 0),
(20, 'strings', 'Derek Keats', '0000-00-00 00:00:00', '0.1a', 'strings', 0, 0, 0, 0, 0),
(21, 'htmlelements', '', '0000-00-00 00:00:00', '0.1a', 'htmlelements', 0, 0, 0, 0, 0),
(22, 'calendarbase', 'Tohir Solomons', '0000-00-00 00:00:00', '1', 'calendarbase', 0, 1, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_modules_dependencies`
-- 

CREATE TABLE `tbl_modules_dependencies` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` varchar(50) default NULL,
  `dependency` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`dependency`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `tbl_modules_dependencies`
-- 

INSERT INTO `tbl_modules_dependencies` (`id`, `module_id`, `dependency`) VALUES (1, 'permissions', 'groupadmin'),
(2, 'contextadmin', 'context'),
(3, 'contextpermissions', 'groupadmin'),
(4, 'contextpermissions', 'permissions'),
(5, 'contextpermissions', 'context'),
(6, 'stories', 'sysconfig'),
(7, 'stories', 'storycategoryadmin');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_modules_owned_tables`
-- 

CREATE TABLE `tbl_modules_owned_tables` (
  `id` int(10) NOT NULL auto_increment,
  `kng_module` varchar(50) NOT NULL default '0',
  `tablename` varchar(150) NOT NULL default 'init',
  PRIMARY KEY  (`id`),
  KEY `tbl_kng_modules_owned_tables_FKIndex1` (`kng_module`),
  KEY `kng_module` (`kng_module`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- 
-- Dumping data for table `tbl_modules_owned_tables`
-- 

INSERT INTO `tbl_modules_owned_tables` (`id`, `kng_module`, `tablename`) VALUES (1, 'decisiontable', 'tbl_decisiontable_decisiontable'),
(2, 'decisiontable', 'tbl_decisiontable_action'),
(3, 'decisiontable', 'tbl_decisiontable_rule'),
(4, 'decisiontable', 'tbl_decisiontable_condition'),
(5, 'decisiontable', 'tbl_decisiontable_conditiontype'),
(6, 'decisiontable', 'tbl_decisiontable_action_rule'),
(7, 'decisiontable', 'tbl_decisiontable_rule_condition'),
(8, 'decisiontable', 'tbl_decisiontable_decisiontable_action'),
(9, 'decisiontable', 'tbl_decisiontable_decisiontable_rule'),
(10, 'useradmin', 'tbl_country'),
(11, 'groupadmin', 'tbl_groupadmin_group'),
(12, 'groupadmin', 'tbl_groupadmin_group_groupfk'),
(13, 'groupadmin', 'tbl_groupadmin_groupuser'),
(14, 'permissions', 'tbl_permissions_acl_description'),
(15, 'permissions', 'tbl_permissions_acl'),
(16, 'dublincoremetadata', 'tbl_dublincoremetadata'),
(17, 'context', 'tbl_context'),
(18, 'context', 'tbl_context_parentnodes_has_tbl_context'),
(19, 'context', 'tbl_context_parentnodes'),
(20, 'context', 'tbl_context_file'),
(21, 'context', 'tbl_context_filedata'),
(22, 'context', 'tbl_context_nodes'),
(23, 'context', 'tbl_context_page_content'),
(24, 'context', 'tbl_contextmodules'),
(25, 'context', 'tbl_context_temp'),
(26, 'context', 'tbl_context_sharednodes'),
(27, 'storycategoryadmin', 'tbl_storycategory'),
(28, 'stories', 'tbl_stories'),
(29, 'calendarbase', 'tbl_calendar'),
(30, 'calendarbase', 'tbl_calendar_event_attachment'),
(31, 'calendarbase', 'tbl_calendar_temp_attachment');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_permissions_acl`
-- 

CREATE TABLE `tbl_permissions_acl` (
  `id` varchar(32) NOT NULL default 'init',
  `acl_id` varchar(32) default NULL,
  `user_id` varchar(32) default NULL,
  `group_id` varchar(32) default NULL,
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_updated_by` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `ind_acl_FK` (`acl_id`),
  KEY `ind_groupuser_FK` (`group_id`),
  KEY `ind_usergroup_FK` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table stores access control list for permissions.';

-- 
-- Dumping data for table `tbl_permissions_acl`
-- 

INSERT INTO `tbl_permissions_acl` (`id`, `acl_id`, `user_id`, `group_id`, `last_updated`, `last_updated_by`) VALUES ('gen6Srv42Nme32_1', 'gen6Srv42Nme32_1', NULL, 'gen6Srv42Nme32_2', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_2', 'gen6Srv42Nme32_2', NULL, 'gen6Srv42Nme32_2', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_3', 'gen6Srv42Nme32_3', NULL, 'gen6Srv42Nme32_2', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_4', 'gen6Srv42Nme32_3', NULL, 'gen6Srv42Nme32_3', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_5', 'gen6Srv42Nme32_3', NULL, 'gen6Srv42Nme32_4', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_6', 'gen6Srv42Nme32_4', NULL, 'gen6Srv42Nme32_2', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_7', 'gen6Srv42Nme32_4', NULL, 'gen6Srv42Nme32_3', '2006-03-02 07:17:43', '1');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_permissions_acl_description`
-- 

CREATE TABLE `tbl_permissions_acl_description` (
  `id` varchar(32) NOT NULL default 'init',
  `name` varchar(100) default NULL,
  `description` varchar(100) default NULL,
  `last_updated` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_updated_by` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='This table stores access control list acl description for de';

-- 
-- Dumping data for table `tbl_permissions_acl_description`
-- 

INSERT INTO `tbl_permissions_acl_description` (`id`, `name`, `description`, `last_updated`, `last_updated_by`) VALUES ('gen6Srv42Nme32_1', 'FOSS_isAuthor', 'Access control list for Long Live the Code!', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_2', 'FOSS_isEditor', 'Access control list for Long Live the Code!', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_3', 'FOSS_isReader', 'Access control list for Long Live the Code!', '2006-03-02 07:17:43', '1'),
('gen6Srv42Nme32_4', 'FOSS_isPrivate', 'Access control list for Long Live the Code!', '2006-03-02 07:17:43', '1');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_permissions_acl_description_seq`
-- 

CREATE TABLE `tbl_permissions_acl_description_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `tbl_permissions_acl_description_seq`
-- 

INSERT INTO `tbl_permissions_acl_description_seq` (`sequence`) VALUES (4);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_permissions_acl_seq`
-- 

CREATE TABLE `tbl_permissions_acl_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `tbl_permissions_acl_seq`
-- 

INSERT INTO `tbl_permissions_acl_seq` (`sequence`) VALUES (7);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_stories`
-- 

CREATE TABLE `tbl_stories` (
  `id` varchar(32) NOT NULL default 'init',
  `category` varchar(32) NOT NULL default 'hidden',
  `isActive` tinyint(1) NOT NULL default '0',
  `parentId` varchar(32) default 'base',
  `language` char(2) default 'en',
  `title` varchar(255) default NULL,
  `abstract` text,
  `mainText` text,
  `dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `creatorId` varchar(25) default NULL,
  `expirationDate` datetime NOT NULL default '0000-00-00 00:00:00',
  `notificationDate` datetime default NULL,
  `isSticky` tinyint(1) NOT NULL default '0',
  `modified` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  `dateModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifierId` varchar(25) default NULL,
  `commentCount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Used to hold stories as elements of text for display';

-- 
-- Dumping data for table `tbl_stories`
-- 

INSERT INTO `tbl_stories` (`id`, `category`, `isActive`, `parentId`, `language`, `title`, `abstract`, `mainText`, `dateCreated`, `creatorId`, `expirationDate`, `notificationDate`, `isSticky`, `modified`, `dateModified`, `modifierId`, `commentCount`) VALUES ('dkeats_3', 'postlogin', 1, 'base', 'en', 'You have successfully logged in to KEWL.NextGen', 'You now have access to KEWL.NextGen''s rich set of features. You should really ask the system administrator for this site to change this message.', 'You now have access to KEWL.NextGen''s rich set of features. If you are\r\nlooking for documentation for users you can download it from\r\nhttp://kngforge.uwc.ac.za/. You can access the AVOIR project at\r\nhttp://avoir.uwc.ac.za/ or explore other documentation, source code,\r\netc at http://cvs.uwc.ac.za/<br>', '2005-04-20 12:04:34', '1', '2025-04-21 00:00:00', NULL, 1, '2005-04-20 12:23:34', '0000-00-00 00:00:00', NULL, 0),
('init_1', 'prelogin', 1, 'base', 'en', 'KEWL.NextGen default prelogin story', 'This is the KEWL.NextGen main prelogin story', '&nbsp;This is the default prelogin story. To edit this story, go to\r\nsite admin, and choose website stories, and edit this prelogin story.\r\nAlternatively, you can delete this story and add one or more additional\r\nstories to the prelogin category.<br>', '2005-04-20 12:04:42', '1', '2025-04-21 00:00:00', NULL, 1, '2005-04-20 12:11:12', '2005-04-20 12:04:12', '1', 0),
('init_2', 'prelogin', 1, 'base', 'en', 'Welcome to KEWL.NextGen', 'Welcome to KEWL.NextGen, an e-learning system produced by the African Virtual Open Initiatives and Resources (AVOIR) project.', '<p>KEWL.NextGen is an advanced e-learning system (sometimes referred to as a learning management system, a virtual learning environment) with features similar to common proprietary systems. It is free software (open source) released under the GNU GPL, and available for download from http://avoir.uwc.ac.za/ External link or by anonymous CVS checkout from the repository nextgen in /cvsroot on cvs.uwc.ac.za using the username and password anoncvs.</p><p>KEWL.NextGen was developed based on several years of experience in e-learning at the University of the Western Cape and partner institutions using its predecessor KEWL, and is under active development by a team of developers in 11 African higher education institutions.</p>', '2005-04-20 12:04:35', '1', '2020-04-21 00:00:00', NULL, 0, '2005-04-20 12:18:35', '0000-00-00 00:00:00', NULL, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_storycategory`
-- 

CREATE TABLE `tbl_storycategory` (
  `id` varchar(32) NOT NULL default 'init',
  `category` varchar(32) NOT NULL default 'init',
  `title` varchar(250) default NULL,
  `dateCreated` datetime default NULL,
  `creatorId` varchar(32) default NULL,
  `dateModified` datetime default NULL,
  `modifierId` varchar(32) default NULL,
  `modified` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Table to hold story categories';

-- 
-- Dumping data for table `tbl_storycategory`
-- 

INSERT INTO `tbl_storycategory` (`id`, `category`, `title`, `dateCreated`, `creatorId`, `dateModified`, `modifierId`, `modified`) VALUES ('init_1', 'postlogin', 'Post login stories', '2005-03-15 08:46:25', '1', '2005-03-15 10:05:08', '1', '2005-03-15 10:05:08'),
('init_2', 'prelogin', 'Prelogin public stories', '2005-03-15 09:34:35', '1', NULL, NULL, '2005-03-15 09:34:35'),
('init_3', 'preloginfooter', 'Story for prelogin footer', '2005-03-15 09:35:41', '1', NULL, NULL, '2005-03-15 09:35:41'),
('init_4', 'preloginfooter', 'Story for prelogin footer', '2005-03-15 09:37:12', '1', NULL, NULL, '2005-03-15 09:37:12');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_sysconfig_properties`
-- 

CREATE TABLE `tbl_sysconfig_properties` (
  `id` varchar(32) NOT NULL default 'init',
  `pmodule` varchar(25) NOT NULL default 'init',
  `pname` varchar(32) NOT NULL default 'init',
  `pvalue` varchar(255) default NULL,
  `creatorId` varchar(25) default NULL,
  `dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifierId` varchar(25) default NULL,
  `dateModified` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `tbl_sysconfig_properties`
-- 

INSERT INTO `tbl_sysconfig_properties` (`id`, `pmodule`, `pname`, `pvalue`, `creatorId`, `dateCreated`, `modifierId`, `dateModified`) VALUES ('gen6Srv42Nme32_1', '_site_', 'KEWL_SITEROOT_PATH', '/var/www/5ive/app/', NULL, '2006-03-02 06:43:12', NULL, NULL),
('gen6Srv42Nme32_10', '_site_', 'LDAP_USED', '', NULL, '2006-03-02 06:43:27', NULL, NULL),
('gen6Srv42Nme32_11', '_site_', 'KEWL_ALLOW_SELFREGISTER', '1', NULL, '2006-03-02 06:43:27', NULL, NULL),
('gen6Srv42Nme32_12', '_site_', 'KEWL_SITE_ROOT', '/5ive/app/', NULL, '2006-03-02 06:43:27', NULL, NULL),
('gen6Srv42Nme32_13', '_site_', 'KEWL_SITENAME', 'paul', NULL, '2006-03-02 06:43:28', NULL, NULL),
('gen6Srv42Nme32_14', '_site_', 'KEWL_POSTLOGIN_MODULE', 'postlogin', NULL, '2006-03-02 06:43:40', NULL, NULL),
('gen6Srv42Nme32_15', '_site_', 'KEWL_CONTENT_BASEPATH', '/var/www/5ive/app/usrfiles/', NULL, '2006-03-02 06:44:21', NULL, NULL),
('gen6Srv42Nme32_16', '_site_', 'KEWL_DEFAULT_SKIN', 'classroom', NULL, '2006-03-02 08:58:53', NULL, NULL),
('gen6Srv42Nme32_2', '_site_', 'KEWL_DEFAULT_LANGUAGE', 'english', NULL, '2006-03-02 06:43:12', NULL, NULL),
('gen6Srv42Nme32_21', '_site_', 'KEWL_ERROR_REPORTING', 'developer', NULL, '2006-03-02 09:40:48', '1', '2006-03-03 11:41:16'),
('gen6Srv42Nme32_22', '_site_', 'LDAP_USED', NULL, NULL, '2006-03-02 12:07:14', NULL, NULL),
('gen6Srv42Nme32_23', '_site_', 'LDAP_USED', NULL, NULL, '2006-03-02 12:27:14', NULL, NULL),
('gen6Srv42Nme32_24', '_site_', 'LDAP_USED', NULL, NULL, '2006-03-02 14:28:10', NULL, NULL),
('gen6Srv42Nme32_25', '_site_', 'LDAP_USED', NULL, NULL, '2006-03-03 07:34:07', NULL, NULL),
('gen6Srv42Nme32_26', '_site_', 'LDAP_USED', NULL, NULL, '2006-03-03 08:15:23', NULL, NULL),
('gen6Srv42Nme32_28', '_site_', 'LDAP_USED', NULL, NULL, '2006-03-03 08:43:29', NULL, NULL),
('gen6Srv42Nme32_29', '_site_', 'LDAP_USED', 'FALSE', NULL, '2006-03-03 12:13:13', NULL, NULL),
('gen6Srv42Nme32_3', '_site_', 'KEWL_SYSTEMTIMEOUT', '60', NULL, '2006-03-02 06:43:12', NULL, NULL),
('gen6Srv42Nme32_30', '_site_', 'KEWL_SITEEMAIL', 'pscott@uwc.ac.za', NULL, '2006-03-06 06:54:02', NULL, NULL),
('gen6Srv42Nme32_4', '_site_', 'KEWL_DEFAULT_LAYOUT_TEMPLATE', 'default_layout_tpl.php', NULL, '2006-03-02 06:43:12', NULL, NULL),
('gen6Srv42Nme32_5', 'sysconfig', 'add_disabled', 'FALSE', '1', '2006-03-02 06:43:19', NULL, NULL),
('gen6Srv42Nme32_6', 'contextadmin', 'wordcontext', 'Context', '1', '2006-03-02 06:43:22', NULL, NULL),
('gen6Srv42Nme32_8', '_site_', 'KEWL_PRELOGIN_MODULE', 'splashscreen', NULL, '2006-03-02 06:43:27', NULL, NULL),
('gen6Srv42Nme32_9', '_site_', 'KEWL_SKIN_ROOT', 'skins/', NULL, '2006-03-02 06:43:27', NULL, NULL),
('init_0', '_site_ ', 'KEWL_SERVERNAME ', 'gen6Srv42Nme32 ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_1', '_site_ ', 'KEWL_SERVERLOCATION ', 'ZA ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_10', '_site_ ', 'KEWL_SKIN_ROOT ', 'skins/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_11', '_site_ ', 'KEWL_DEFAULT_SKIN ', 'classroom ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_12', '_site_ ', 'KEWL_DEFAULT_LANGUAGE ', 'english ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_13', '_site_ ', 'KEWL_DEFAULT_LANGUAGE_ABBREV ', 'EN ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_14', '_site_ ', 'KEWL_BANNER_EXT ', 'jpg ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_15', '_site_ ', 'KEWL_POSTLOGIN_MODULE ', 'postlogin ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_16', '_site_ ', 'KEWL_PRELOGIN_MODULE ', 'splashscreen ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_17', '_site_ ', 'KEWL_DEFAULT_LAYOUT_TEMPLATE ', 'default_layout_tpl.php ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_18', '_site_ ', 'KEWL_LOGIN_TEMPLATE ', 'login_tpl.php ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_19', '_site_ ', 'KEWL_LOGGED_IN_TEMPLATE ', 'loggedin_tpl.php ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_2', '_site_ ', 'KEWL_SITENAME ', 'paul ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_20', '_site_ ', 'KEWL_SITEROOT_PATH ', '/var/www/5ive/app/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_21', '_site_ ', 'KEWL_TEMPLATE_PATH ', '/var/www/5ive/app/templates/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_22', '_site_ ', 'KEWL_CONTENT_BASEPATH ', '/var/www/5ive/app/usrfiles/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_23', '_site_ ', 'KEWL_CONTENT_PATH ', 'usrfiles/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_24', '_site_ ', 'KEWL_CONTENT_ROOT ', 'usrfiles ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_25', '_site_ ', 'KEWL_BLOGS_BASEPATH ', '/var/www/5ive/app/blog/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_26', '_site_ ', 'KEWL_ALLOW_SELFREGISTER ', '1 ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_27', '_site_ ', 'KEWL_ENABLE_LOGGING ', 'TRUE ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_28', '_site_ ', 'LDAP_USED ', ' ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_3', '_site_ ', 'KEWL_INSTITUTION_SHORTNAME ', 'paul ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_4', '_site_ ', 'KEWL_INSTITUTION_NAME ', 'paul ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_5', '_site_ ', 'KEWL_PROXY ', 'http://pscott:scott@cache.uwc.ac.za:8080 ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_6', '_site_ ', 'KEWL_SITEEMAIL ', 'pscott@uwc.ac.za ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_7', '_site_ ', 'KEWL_SYSTEMTIMEOUT ', '60 ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_8', '_site_ ', 'KEWL_SITE_ROOT ', '/5ive/app/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL),
('init_9', '_site_ ', 'KEWL_DEFAULTICONFOLDER ', '/icons/ ', '1 ', '2006-03-02 06:43:06', NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_sysconfig_properties_seq`
-- 

CREATE TABLE `tbl_sysconfig_properties_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- 
-- Dumping data for table `tbl_sysconfig_properties_seq`
-- 

INSERT INTO `tbl_sysconfig_properties_seq` (`sequence`) VALUES (30);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_userloginhistory`
-- 

CREATE TABLE `tbl_userloginhistory` (
  `id` varchar(32) NOT NULL default 'init',
  `userId` char(25) NOT NULL default '0',
  `lastLoginDateTime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED COMMENT='Used to hold the loginhistory of a user';

-- 
-- Dumping data for table `tbl_userloginhistory`
-- 

INSERT INTO `tbl_userloginhistory` (`id`, `userId`, `lastLoginDateTime`) VALUES ('gen6Srv42Nme32_1', '1', '2006-03-02 06:03:40'),
('gen6Srv42Nme32_10', '1', '2006-03-03 20:03:05'),
('gen6Srv42Nme32_11', '1', '2006-03-03 21:03:39'),
('gen6Srv42Nme32_12', '1', '2006-03-03 22:03:50'),
('gen6Srv42Nme32_13', '1', '2006-03-04 09:03:36'),
('gen6Srv42Nme32_14', '1', '2006-03-04 12:03:33'),
('gen6Srv42Nme32_15', '1', '2006-03-04 14:03:43'),
('gen6Srv42Nme32_16', '1', '2006-03-06 06:03:13'),
('gen6Srv42Nme32_17', '1', '2006-03-06 06:03:09'),
('gen6Srv42Nme32_18', '1', '2006-03-06 10:03:09'),
('gen6Srv42Nme32_19', '1', '2006-03-06 14:03:57'),
('gen6Srv42Nme32_2', '1', '2006-03-02 09:03:54'),
('gen6Srv42Nme32_20', '1', '2006-03-08 07:03:24'),
('gen6Srv42Nme32_21', '1', '2006-03-08 08:03:02'),
('gen6Srv42Nme32_22', '1', '2006-03-08 14:03:54'),
('gen6Srv42Nme32_23', '1', '2006-03-08 14:03:39'),
('gen6Srv42Nme32_24', '1', '2006-03-08 17:03:39'),
('gen6Srv42Nme32_25', '1', '2006-03-08 21:03:59'),
('gen6Srv42Nme32_26', '1', '2006-03-09 06:03:23'),
('gen6Srv42Nme32_27', '1', '2006-03-09 06:03:34'),
('gen6Srv42Nme32_28', '1', '2006-03-09 08:03:00'),
('gen6Srv42Nme32_29', '1', '2006-03-10 06:03:26'),
('gen6Srv42Nme32_3', '1', '2006-03-02 12:03:19'),
('gen6Srv42Nme32_30', '1', '2006-03-10 06:03:19'),
('gen6Srv42Nme32_31', '1', '2006-03-12 13:03:46'),
('gen6Srv42Nme32_32', '1', '2006-03-12 16:03:24'),
('gen6Srv42Nme32_4', '1', '2006-03-02 12:03:22'),
('gen6Srv42Nme32_5', '1', '2006-03-02 14:03:16'),
('gen6Srv42Nme32_6', '1', '2006-03-03 07:03:13'),
('gen6Srv42Nme32_7', '1', '2006-03-03 08:03:17'),
('gen6Srv42Nme32_8', '1', '2006-03-03 12:03:20'),
('gen6Srv42Nme32_9', '1', '2006-03-03 19:03:33');

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_userloginhistory_seq`
-- 

CREATE TABLE `tbl_userloginhistory_seq` (
  `sequence` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`sequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- 
-- Dumping data for table `tbl_userloginhistory_seq`
-- 

INSERT INTO `tbl_userloginhistory_seq` (`sequence`) VALUES (32);

-- --------------------------------------------------------

-- 
-- Table structure for table `tbl_users`
-- 

CREATE TABLE `tbl_users` (
  `id` varchar(32) NOT NULL default 'init',
  `userId` varchar(25) NOT NULL default '0',
  `username` varchar(25) NOT NULL default 'init',
  `title` varchar(25) NOT NULL default 'init',
  `firstName` varchar(50) NOT NULL default 'init',
  `surname` varchar(50) NOT NULL default 'init',
  `pass` varchar(100) NOT NULL default 'init',
  `creationDate` date NOT NULL default '0000-00-00',
  `emailAddress` varchar(100) NOT NULL default 'init',
  `logins` int(11) default '0',
  `sex` char(1) default NULL,
  `country` char(2) default NULL,
  `accesslevel` char(1) default '0',
  `isActive` char(1) default '1',
  `howCreated` varchar(32) default 'unknown',
  `updated` timestamp NULL default NULL,
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Primary user information';

-- 
-- Dumping data for table `tbl_users`
-- 

INSERT INTO `tbl_users` (`id`, `userId`, `username`, `title`, `firstName`, `surname`, `pass`, `creationDate`, `emailAddress`, `logins`, `sex`, `country`, `accesslevel`, `isActive`, `howCreated`, `updated`) VALUES ('init_1', '1', 'admin', 'Dr', 'Administrative', 'User', '86f7e437faa5a7fce15d1ddcb9eaeaea377667b8', '0000-00-00', 'admin@localhost.local', 32, 'M', 'ZA', '1', '1', 'install', '2006-03-04 14:59:44');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `tbl_context_file`
-- 
ALTER TABLE `tbl_context_file`
  ADD CONSTRAINT `tbl_context_file_ibfk_1` FOREIGN KEY (`tbl_context_parentnodes_id`) REFERENCES `tbl_context_parentnodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_context_filedata`
-- 
ALTER TABLE `tbl_context_filedata`
  ADD CONSTRAINT `tbl_context_filedata_ibfk_1` FOREIGN KEY (`tbl_context_file_id`, `tbl_context_file_tbl_context_parentnodes_id`) REFERENCES `tbl_context_file` (`id`, `tbl_context_parentnodes_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_context_nodes`
-- 
ALTER TABLE `tbl_context_nodes`
  ADD CONSTRAINT `tbl_context_nodes_ibfk_1` FOREIGN KEY (`tbl_context_parentnodes_id`) REFERENCES `tbl_context_parentnodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_context_page_content`
-- 
ALTER TABLE `tbl_context_page_content`
  ADD CONSTRAINT `tbl_context_page_content_ibfk_1` FOREIGN KEY (`tbl_context_nodes_id`) REFERENCES `tbl_context_nodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_context_parentnodes`
-- 
ALTER TABLE `tbl_context_parentnodes`
  ADD CONSTRAINT `tbl_context_parentnodes_ibfk_1` FOREIGN KEY (`tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode`, `tbl_context_parentnodes_has_tbl_context_tbl_context_id`) REFERENCES `tbl_context_parentnodes_has_tbl_context` (`tbl_context_contextCode`, `tbl_context_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_context_parentnodes_has_tbl_context`
-- 
ALTER TABLE `tbl_context_parentnodes_has_tbl_context`
  ADD CONSTRAINT `tbl_context_parentnodes_has_tbl_context_ibfk_1` FOREIGN KEY (`tbl_context_id`, `tbl_context_contextCode`) REFERENCES `tbl_context` (`id`, `contextCode`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_decisiontable_action_rule`
-- 
ALTER TABLE `tbl_decisiontable_action_rule`
  ADD CONSTRAINT `tbl_decisiontable_action_rule_ibfk_1` FOREIGN KEY (`actionId`) REFERENCES `tbl_decisiontable_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_decisiontable_action_rule_ibfk_2` FOREIGN KEY (`ruleId`) REFERENCES `tbl_decisiontable_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_decisiontable_decisiontable_action`
-- 
ALTER TABLE `tbl_decisiontable_decisiontable_action`
  ADD CONSTRAINT `tbl_decisiontable_decisiontable_action_ibfk_1` FOREIGN KEY (`decisiontableId`) REFERENCES `tbl_decisiontable_decisiontable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_decisiontable_decisiontable_action_ibfk_2` FOREIGN KEY (`actionId`) REFERENCES `tbl_decisiontable_action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_decisiontable_decisiontable_rule`
-- 
ALTER TABLE `tbl_decisiontable_decisiontable_rule`
  ADD CONSTRAINT `tbl_decisiontable_decisiontable_rule_ibfk_1` FOREIGN KEY (`decisiontableId`) REFERENCES `tbl_decisiontable_decisiontable` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_decisiontable_decisiontable_rule_ibfk_2` FOREIGN KEY (`ruleId`) REFERENCES `tbl_decisiontable_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_decisiontable_rule_condition`
-- 
ALTER TABLE `tbl_decisiontable_rule_condition`
  ADD CONSTRAINT `tbl_decisiontable_rule_condition_ibfk_1` FOREIGN KEY (`ruleId`) REFERENCES `tbl_decisiontable_rule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_decisiontable_rule_condition_ibfk_2` FOREIGN KEY (`conditionId`) REFERENCES `tbl_decisiontable_condition` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_groupadmin_group`
-- 
ALTER TABLE `tbl_groupadmin_group`
  ADD CONSTRAINT `tbl_groupadmin_group_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `tbl_groupadmin_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_groupadmin_groupuser`
-- 
ALTER TABLE `tbl_groupadmin_groupuser`
  ADD CONSTRAINT `tbl_groupadmin_groupuser_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `tbl_groupadmin_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_groupadmin_groupuser_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `tbl_permissions_acl`
-- 
ALTER TABLE `tbl_permissions_acl`
  ADD CONSTRAINT `tbl_permissions_acl_ibfk_1` FOREIGN KEY (`acl_id`) REFERENCES `tbl_permissions_acl_description` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_permissions_acl_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `tbl_groupadmin_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_permissions_acl_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
