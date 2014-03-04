<?php
//Table Name
$tablename = 'tbl_personalblocks';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold personal blocks, which are blocks that you can include in a blog.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'location' => array(
		'type'=>'text',
		'length'=> 20
		),
	'blockname' => array(
		'type'=>'text',
		'length'=> 150
		),
	'blockcontent' => array(
		'type'=>'clob'
		),
    'blocktype' => array(
        'type' => 'text',
        'length' => 15,
        'default' =>'personal'
        ),
    'sortorder' => array(
        'type' => 'integer',
        'length' => 4
        ),
    'context' => array(
        'type' => 'text',
        'length' => 32
        ),
	'active' => array(
        'type' => 'integer',
        'length' => 1,
        ),
	'datecreated' => array(
		'type' => 'timestamp',
		'notnull' => TRUE
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
		'type' => 'timestamp'
		)
	);
?>