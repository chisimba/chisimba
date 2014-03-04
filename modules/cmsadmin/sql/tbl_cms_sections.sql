<?php

$tablename = 'tbl_cms_sections';

$options = array('comment' => 'CMS table used to store sections', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'rootid' => array(
		'type' => 'text',
		'length' => 32
		),
	'parentid' => array(
		'type' => 'text',
		'length' => 32
		),
	'title' => array(
		'type' => 'text',
		'length' => 50
		),
	'menutext' => array(
		'type' => 'text',
		'length' => 255
		),
	'description' => array(
		'type' => 'clob'
		),
	'published' => array(
		'type' => 'integer',
		'length' => 255,

		),
	'show_title' => array(
		'type' => 'text',
		'length' => 1,
		'notnull' => FALSE,
		'default' => 'g'
		),
	'show_user' => array(
		'type' => 'text',
		'length' => 1,
		'notnull' => FALSE,
		'default' => 'g'
		),
	'show_date' => array(
		'type' => 'text',
		'length' => 1,
		'notnull' => FALSE,
		'default' => 'g'
		),
	'show_introduction' => array(
		'type' => 'text',
		'length' => 1,
		'notnull' => FALSE,
		'default' => 'g'
		),
	'numpagedisplay' => array(
		'type' => 'integer',
		'length' => 11

		),
   'checked_out' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

		),
    'checked_out_time' => array(
		'type' => 'timestamp',

		),
    'ordering' => array(
		'type' => 'integer',
        'length' => 11,

		),
    'ordertype' => array(
		'type' => 'text',
        'length' => 100,

		),
    'access' => array(
		'type' => 'integer',
        'length' => 3,
        'unsigned' => TRUE,

		),
	'trash' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'nodelevel' => array(
		'type' => 'integer',
        'length' => 11,

		),
	'params' => array(
		'type' => 'text',
		'length' => 255
		),
	'layout' => array(
		'type' => 'text',
        'length' => 32
		),
    'link' => array(
         'type' => 'text',
         'length' => 255
		),
		
	'userid' => array(
		'type' => 'text',
		'length' => 255,
        'notnull' => TRUE
		),
	
	'userids' => array(
		'type' => 'text',
		'length' => 255
		),
	
	'groupid' => array(
		'type' => 'text',
		'length' => 255
		),

	'datecreated' => array(
		'type' => 'timestamp'
		),
	'lastupdatedby' => array(
        'type' => 'text',
		'length' => 32        
        ),
    'updated' => array(
        'type' => 'timestamp'
        ),
    'startdate' => array(
		'type' => 'timestamp'
		),
	'finishdate' => array(
		'type' => 'timestamp'
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255
                ),
	'public_access' => array(
		'type' => 'boolean',
		'default' => '1'
		)		
	);
	$name = 'contextcode';

$name = 'idx_cms_sections';

$indexes = array(
                'fields' => array(
                	'id' => array()
                )
        );
?>
