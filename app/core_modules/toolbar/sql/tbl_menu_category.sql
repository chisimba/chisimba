<?php
/*
CREATE TABLE `tbl_menu_category` ( 
    `id` varchar(32) NOT NULL, 
    `category` varchar(120), 
    `module` varchar(60), 
    `adminOnly` TINYINT NOT NULL Default 0,
    `permissions` varchar(120),
    `dependsContext` TINYINT NOT NULL Default 0,
    PRIMARY KEY (id)
    ) Type=InnoDB ;
*/

// Table Name
$tablename = 'tbl_menu_category';

//Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'category' => array(
		'type' => 'text',
		'length' => 120
		),
    'module' => array(
		'type' => 'text',
        'length' => 60
		),
    'adminOnly' => array(
		'type' => 'integer'
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		),
    'permissions' => array(
		'type' => 'text',
        'length' => 120
		),
    'dependsContext' => array(
		'type' => 'integer'
        'length' => 1,
        'notnull' => TRUE,
        'default' => 0
		),
    );
?>