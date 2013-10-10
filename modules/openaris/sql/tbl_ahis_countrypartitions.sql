<?php

$tablename = 'tbl_ahis_partitioncategories';

$options = array('comment'=> 'table to store partition categories','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	 'countryid' => array(
		'type' => 'integer',
		'length' => 32,
        'notnull' => TRUE
		),
    'partitioncategory' => array(
		'type' => 'integer',
		'length' => 64,
        'notnull' => TRUE
		),
    'partitionlevel' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
      'description' => array(
		'type' => 'text',
		'length' => 108,
        'notnull' => TRUE
		),
      'partitionname' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
       'startdate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
	 'enddate' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
      'datecreated' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
      'createdby' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
      'datemodified' => array(
		'type' => 'date',
        'notnull' => TRUE
		),
      'modifiedby' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
    );

?>