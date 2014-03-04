<?php
//Table Name
$tablename = 'tbl_textblock';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold textblocks to appear pages that support blocks', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'blocktext' => array(
		'type'=>'clob'
		),
	'title' => array(
		'type'=>'text',
		'length'=> 250
		),
	'blockid' => array(
		'type'=>'text',
		'length'=> 10
		),
    'show_title' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE,
        'default' => 'g'
        ),
	'css_id' => array(
		'type'=>'text',
		'length'=> 250
		),
	'css_class' => array(
		'type'=>'text',
		'length'=> 250
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datemodified' => array(
		'type'=> 'timestamp'
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32 	
		),
	'modified' => array(
		'type' => 'timestamp',
		//'length' => 14,
		'notnull' => TRUE
		)
	);
?>
