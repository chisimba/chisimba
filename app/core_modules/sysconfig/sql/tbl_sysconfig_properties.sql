<?

/*
CREATE TABLE `tbl_sysconfig_properties` (
  `id` varchar(32) NOT NULL default '',
  `pmodule` varchar(25) NOT NULL default '',
  `pname` varchar(32) NOT NULL default '',
  `pvalue` varchar(255) NOT NULL default '',
  `creatorId` varchar(25) default NULL,
  `dateCreated` datetime NOT NULL default '0000-00-00 00:00:00',
  `modifierId` varchar(25) default NULL,
  `dateModified` datetime default NULL,
  PRIMARY KEY  (`id`)
) TYPE=InnoDB ;
*/

// Table Name
$tablename = 'tbl_sysconfig_properties';

//Options line for comments, encoding and character set
$options = array('comment' => 'system properties', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
	'pmodule' => array(
		'type' => 'text',
		'length' => 25,


		),
    'pname' => array(
		'type' => 'text',
        'length' => 32,


		),
    'pvalue' => array(
		'type' => 'text',
        'length' => 32,


		),
    'creatorId' => array(
		'type' => 'text',
        'length' => 25
		),
    'dateCreated' => array(
		'type' => 'date',
        'notnull' => TRUE,
        'default' => '0000-00-00 00:00:00'
		),
    'modifierId' => array(
		'type' => 'text',
        'length' => 25
		),
    'dateModified' => array(
		'type' => 'date'
		)
    );


?>
