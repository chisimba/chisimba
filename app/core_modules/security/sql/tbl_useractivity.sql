<?php
// Table Name
$tablename = 'tbl_useractivity';

//Options line for comments, encoding and character set
$options = array('comment' => 'Records users activity', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'module' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'action' => array(
        'type' => 'text',
        'length' =>32

        ),
'contextcode'=> array(
        'type' => 'text',
        'length' => 32

        ),
    	'createdon' => array(
		'type' => 'timestamp',
	),
);


?>
