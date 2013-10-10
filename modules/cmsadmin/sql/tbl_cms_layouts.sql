<?php
//5ive definition
$tablename = 'tbl_cms_layouts';

//Options line for comments, encoding and character set
$options = array('comment' => 'cms layouts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'name' => array(
		'type' => 'text',
		'length' => 32
		),
    'imagename' => array(
		'type' => 'text',
		'length' => 32
		),
    'description' => array(
		'type' => 'text',
		'length' => 255
		)
);

// Other Indexes

$name = 'name';

$indexes = array(
                'fields' => array(
                	'name' => array()
                )
        );
?>
