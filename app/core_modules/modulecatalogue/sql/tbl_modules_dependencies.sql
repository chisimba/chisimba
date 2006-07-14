<?php
/*
CREATE TABLE `tbl_modules_dependencies` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` varchar(50) default NULL,
  `dependency` varchar(50) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`dependency`)
) TYPE=InnoDB  AUTO_INCREMENT=1 ;
*/
// Table Name
$tablename = 'tbl_modules_dependencies';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'module_id' => array(
		'type' => 'text',
		'length' => 50
		),
    'dependency' => array(
		'type' => 'text'
        'length' => 50
		)
    );

//create other indexes here...

$name = 'modules_dependencies';

$indexes = array(
                'fields' => array(
                	'dependency' => array()
                )
        );
?>