<?
/*
CREATE TABLE `tbl_files_metadata_media` (
  `id` varchar(32) NOT NULL,
  `fileid` varchar(32) NOT NULL,
  `version` int(11) NOT NULL default '1',
  `width` int(11) default '0',
  `height` int(11) default '0',
  `playtime` varchar(30) default NULL,
  `format` varchar(30) default NULL,
  `framerate` int(11) default NULL,
  `bitrate` int(11) default NULL,
  `samplerate` int(11) default NULL,
  `title` varchar(255) default NULL,
  `artist` varchar(255) default NULL,
  `year` varchar(10) default NULL,
  `url` varchar(255) default NULL,
  `getid3info` text collate latin1_general_ci,
  `creatorid` varchar(25) NOT NULL,
  `datecreated` datetime NOT NULL,
  `modifierid` varchar(25) NOT NULL,
  `datemodified` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fileId` (`fileid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Table to hold metadata for media files';
;
*/
$tablename = 'tbl_files_metadata_media';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'fileid' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'width' => array(
		'type' => 'integer',
        'length' => 11,
        'default' => 0
		),
    'height' => array(
		'type' => 'integer',
        'length' => 11,
        'default' => 0
		),
    'playtime' => array(
		'type' => 'integer',
        'length' => 11,
        'default' => 0
		),
    'format' => array(
		'type' => 'text',
        'length' => 30
		),
    'framerate' => array(
		'type' => 'integer',
        'length' => 11
		),
    'bitrate' => array(
		'type' => 'integer',
        'length' => 11
		),
    'samplerate' => array(
		'type' => 'integer',
        'length' => 11
		),
    'title' => array(
		'type' => 'text',
        'length' => 255
		),
    'artist' => array(
		'type' => 'text',
        'length' => 255
		),
	'description' => array(
		'type' => 'text'
		),
    'year' => array(
		'type' => 'text',
        'length' => 10
		),
    'url' => array(
		'type' => 'text',
        'length' => 255
		),
    'getid3info' => array(
		'type' => 'text'
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

$name = 'index_tbl_files_metadata_media';

$indexes = array(
                'fields' => array(
                	'fileid' => array()
                )
        );

?>