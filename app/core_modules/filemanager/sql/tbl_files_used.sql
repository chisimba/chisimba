<?php
/*
CREATE TABLE `tbl_file_usage` (
`id` VARCHAR( 32 ) NOT NULL ,
`fileid` VARCHAR( 32 ) NOT NULL ,
`module` VARCHAR( 255 ) NOT NULL ,
`tablename` VARCHAR( 255 ) NOT NULL ,
`columnname` VARCHAR( 255 ) NOT NULL ,
`recordid` VARCHAR( 32 ) NOT NULL ,
`context` VARCHAR( 32 ) NOT NULL ,
`workgroup` VARCHAR( 32 ) NOT NULL ,
`filelock` VARCHAR( 1 ) NOT NULL DEFAULT 'N',
`creatorid` VARCHAR( 25 ) NOT NULL ,
`datecreated` DATE NOT NULL ,
`timecreated` TIME NOT NULL ,
`modifierid` VARCHAR( 25 ) NOT NULL ,
`datemodified` DATE NOT NULL ,
`timemodified` TIME NOT NULL ,
PRIMARY KEY ( `id` ) ,
INDEX ( `fileid` )
) TYPE = MYISAM ;
;
*/
$tablename = 'tbl_files_used';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    'fileid' => array(
		'type' => 'text',
        'length' => 32
		),
    'context' => array(
		'type' => 'text',
        'length' => 32
		),
    'workgroup' => array(
		'type' => 'text',
        'length' => 32
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
		)
    );
//create other indexes here...

$name = 'index_tbl_files_used';

$indexes = array(
                'fields' => array(
                	'fileid' => array(),
                	'context' => array(),
                	'workgroup' => array()
                )
        );

?>