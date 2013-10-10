<?php
/**
* Database Table Announcements
* @author Tohir Solomons
* @copyright 2008 University of the Western Cape
*/

//Chisimba definition
$tablename = 'tbl_announcements_context';

//Options line for comments, encoding and character set
$options = array('comment' => 'Allow one announcement to be delivered to multiple contexts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'announcementid' => array(
        'type' => 'text',
        'length' => 32,
    ),

    'contextid' => array(
        'type' => 'text',
        'length' => 32,
    )
    
);

//create other indexes here...
$name = 'tbl_announcements_context_idx';

$indexes = array(
                'fields' => array(
                    'announcementid' => array(),
                    'contextid' => array(),
                )
        );

?>