<?php

$tablename = 'tbl_contextcontent_page_comment';

// Options line for comments, encoding and character set
$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'userid' => array(
        'type' => 'text',
        'length' => 32
        ),
    'datecreated' => array(
        'type' => 'timestamp',
        'notnull' => TRUE
        ),
    'pageid' => array(
        'type' => 'text',
        'length' => 32
        ),
	'comment' => array(
        'type' => 'clob'
        ),
    );
//create other indexes here...

$name = 'pageidx';

$indexes = array(
    'fields' => array(
        'pageid' => array()
    )
);
?>
