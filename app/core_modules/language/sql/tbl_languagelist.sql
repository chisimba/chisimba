<?php
/*
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
*/
// Table Name
$tablename = 'tbl_languagelist';

//Options line for comments, encoding and character set
$options = array('commment' => 'Holds the list of languages that KEWL has', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'languageCode' => array(
		'type' => 'text',
		'length' => 100,
        'notnull' => TRUE
		),
    'languageName' => array(
		'type' => 'text',
		'length' => 100,
        'notnull' => TRUE
		)
    );

?>