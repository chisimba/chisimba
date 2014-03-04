<?php
// Table Name
$tablename = 'tbl_news_locations';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the various locations for linking stories to location', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'location' => array (
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
	),
    'location_parent' => array (
		'type' => 'text',
		'length' => 32,
		'notnull' => 1,
		'default' => 'root'
	),
	'location_type' => array (
		'type' => 'text',
		'length' => 32
	),
    'latitude' => array (
		'type' => 'text',
		'length' => 100,
	),
    'longitude' => array (
		'type' => 'text',
		'length' => 100,
	),
	'latituderad' => array (
		'type' => 'text',
		'length' => 100,
	),
    'longituderad' => array (
		'type' => 'text',
		'length' => 100,
	),
    'zoomlevel' => array (
		'type' => 'integer'
	),
	'latlongcenter' => array (
		'type' => 'text',
		'length' => 255,
	),
	'latlongcenterbounds' => array (
		'type' => 'text',
		'length' => 255,
	),
	'lft' => array (
		'type' => 'integer'
	),
	'rght' => array (
		'type' => 'integer'
	),
	'level' => array (
		'type' => 'integer'
	),
    'locationimage' => array (
		'type' => 'text',
		'length' => 32,
	),
    'datecreated' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
    'creatorid' => array (
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
	),
    'datemodified' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
    'modifierid' => array (
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
	),

);
//create other indexes here...
$name = 'tbl_news_tbl_news_locations_idx';

$indexes = array(
                'fields' => array(
                	'location' => array(),
                	'location_parent' => array(),
                	'lft' => array(),
                	'rght' => array(),
                	'locationimage' => array(),
                	'datecreated' => array(),
                	'datemodified' => array(),
                )
        );
?>