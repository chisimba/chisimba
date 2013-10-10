<?php
/**
*
* SQL to generate tbl_registerinterest_records data
*
*/
// Table Name
$tablename = 'tbl_registerinterest_records';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of data for the registerinterest module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'userid' => array(
		'type' => 'text',
		'length' => 32
		),
	'interestid' => array(
		'type' => 'text',
                                                'length'=>32
		),
                        'id'=>array(
                                                'type'=>'text',
                                                'length'=>32
                                                ),
	);

?>