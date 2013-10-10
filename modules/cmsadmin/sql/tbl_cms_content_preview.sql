<?php
$tablename = 'tbl_cms_content_preview';
$options = array('comment' => 'cms_contents', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,

		),
	'title' => array(
		'type' => 'text',
		'length' => 255
		),
	'introtext' => array(
		'type' => 'clob',
		),
    'body' => array(
		'type' => 'clob',
		),
    'published' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
    'hide_title' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
        ),
    'hide_user' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
        ),
    'hide_date' => array(
        'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
        ),
	'trash' => array(
		'type' => 'integer',
        'length' => 1,
        'notnull' => TRUE,
        'default' => '0'
		),
	'sectionid' => array(
		'type' => 'text',
		'length' => 32,

		),
	'post_lic' => array(
		'type' => 'text',
		'length' => 60,

		),
    'mask' => array(
		'type' => 'integer',
        'unsigned' => TRUE,

		),
	'created' => array(
		'type' => 'timestamp',

		),
    'created_by' => array(
		'type' => 'text',
        'length' => 32,

		),
 
    'groupid' => array(
		'type' => 'text',
        'length' => 32,

		),

    'created_by_alias' => array(
		'type' => 'text',
        'length' => 100
		),
    'modified' => array(
		'type' => 'timestamp',

		),
    'modified_by' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

		),
    'checked_out' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

		),
    'checked_out_time' => array(
		'type' => 'timestamp',

		),
    'publish_up' => array(
		'type' => 'timestamp',

		),
    'publish_down' => array(
		'type' => 'timestamp',

		),
    'images' => array(
		'type' => 'text',
		'length' => 255
		),
    'urls' => array(
		'type' => 'text',
		'length' => 255
		),
    'attribs' => array(
		'type' => 'text',
		'length' => 255
		),
    'version' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

		),
    'parentid' => array(
		'type' => 'text',
        'length' => 32,
        'unsigned' => TRUE,

		),
    'ordering' => array(
		'type' => 'integer',
        'length' => 11,

		),
    'metakey' => array(
		'type' => 'text',
		'length' => 255
		),
    'metadesc' => array(
		'type' => 'text',
		'length' => 255
		),
    'access' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

		),
    'hits' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

		),
	'start_publish' => array(
		'type' => 'timestamp',

		),
    'end_publish' => array(
		'type' => 'timestamp',

		),
	'public_access' => array(
		'type' => 'boolean',
		'default' => '1'
		)		
	
	);


//create other indexes here...

$name = 'idx_content';

$indexes = array(
                'fields' => array(
                	'sectionid' => array(),
                	'access' => array(),
                	'checked_out' => array(),
                    'published' => array(),
                	'mask' => array()
                )
        );
?>
