<?php
/**
* @copyright (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package decisiontable
* @subpackage SQL
* @version 0.1
* @since 04 Febuary 2005
* @author Jonathan Abrahams
* @filesource
*/
/*
$sqldata[] ="CREATE TABLE tbl_decisiontable_action (
  id VARCHAR(32) NOT NULL,
  name VARCHAR(50) NULL,
  PRIMARY KEY(id)
) TYPE=InnoDB COMMENT='Table used to keep a list of actions.';";
*/
// Table Name
$tablename = 'tbl_decisiontable_action';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table used to keep a list of actions.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 50
	)
    );


?>
