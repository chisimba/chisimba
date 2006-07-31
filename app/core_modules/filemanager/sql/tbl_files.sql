<?
/*
CREATE TABLE `tbl_files` (
  `id` varchar(32)  NOT NULL,
  `userid` varchar(25)  NOT NULL,
  `filename` varchar(255)  NOT NULL,
  `datatype` varchar(10)  default NULL,
  `path` varchar(255)  NOT NULL,
  `description` varchar(255)  default NULL,
  `version` int(11) NOT NULL default '1',
  `filesize` int(11) NOT NULL,
  `mimetype` varchar(255)  NOT NULL,
  `category` varchar(255)  NOT NULL,
  `moduleuploaded` varchar(255)  default NULL,
  `creatorid` varchar(25)  NOT NULL,
  `datecreated` datetime NOT NULL,
  `modifierid` varchar(25)  NOT NULL,
  `datemodified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userId` (`userid`,`filename`,`version`,`filesize`,`mimetype`,`category`,`creatorid`,`modifierid`),
  KEY `datatype` (`datatype`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;
*/
$tablename = 'tbl_files';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'userid' => array(
		'type' => 'text',
		'length' => 25,
        'notnull' => TRUE
		),
	'filename' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
    'datatype' => array(
		'type' => 'text',
        'length' => 10
		),
    'path' => array(
		'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
		),
    'description' => array(
		'type' => 'text',
        'length' => 255
		),
    'version' => array(
		'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE,
        'default' => 1
		),
    'filesize' => array(
		'type' => 'integer',
        'length' => 11,
        'notnull' => TRUE
		),
    'mimetype' => array(
		'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
		),
    'category' => array(
		'type' => 'text',
        'length' => 255
		),
    'license' => array(
		'type' => 'text',
        'length' => 32
		),
    'moduleuploaded' => array(
		'type' => 'text',
        'length' => 255
		),
    'creatorid' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'datecreated' => array(
		'type' => 'date'
		),
    'timecreated' => array(
		'type' => 'time'
		),
    'modifierid' => array(
		'type' => 'text',
        'length' => 25,
        'notnull' => TRUE
		),
    'datemodified' => array(
		'type' => 'date'
		),
    'timemodified' => array(
		'type' => 'time'
		)
    );
//create other indexes here...

$name = 'index_tbl_files';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                	'filename' => array(),
                	'version' => array(),
                	'filesize' => array(),
                	'mimetype' => array(),
                	'category' => array(),
                	'creatorid' => array(),
                	'modifierid' => array()
                )
        );

?>