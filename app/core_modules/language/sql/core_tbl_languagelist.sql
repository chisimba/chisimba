#
# Table structure for table `tbl_languagelist`
#

CREATE TABLE `tbl_languagelist` (
  `id` int(11) NOT NULL auto_increment,
  `languageCode` varchar(100) NOT NULL default '',
  `languageName` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=InnoDB  COMMENT='Holds the list of languages that KEWL has';

#
# Dumping data for table `tbl_languagelist`
#
INSERT INTO `tbl_languagelist` (languageCode, languageName) VALUES ('tbl_english', 'English');