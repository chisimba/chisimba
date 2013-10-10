<?php
// Table Name
$tablename = 'tbl_simpleregistrationcontent';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table holding the content details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
    'type' => 'text',
    'length' => 32
    ),
    'event_timevenue' => array(
    'type' => 'text'

    ),
    'event_content' => array(
    'type' => 'text'

    ),
    'event_lefttitle1' => array(
    'type' => 'text'
    ),
    'event_lefttitle2' => array(
    'type' => 'text'

    ),
    'event_emailcontact' => array(
    'type' => 'text',
    'length' => 512
    ),
    'event_emailsubject' => array(
    'type' => 'text',
    'length' => 512
    ),
   'event_emailname' => array(
    'type' => 'text',
    'length' => 512
    ),
    'event_emailcontent' => array(
    'type' => 'text'
    ),
   'event_emailattachments' => array(
    'type' => 'text'
    ),
    'event_footer' => array(
    'type' => 'text'
    ),
    'event_id' => array(
    'type' => 'text',
    'length' => 128
    ),
 'event_staffreg' => array(
    'type' => 'text',
    'length' => 12
    ),
 'event_visitorreg' => array(
    'type' => 'text',
    'length' => 12
    )
    );



?>
