<?php

$tablename = 'tbl_efl_proposedessaytopics';

$options = array(
    'comment' => 'Table for tbl_efl_essaytopics',
    'collate' => 'utf8_general_ci',
    'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'userid' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
    ),
    'title'  => array(
        'type'  =>  'text',
        'length'=>  255
    ),
    'content' => array(
        'type' => 'text',
    ),
    'contextcode' =>  array(
        'type' => 'text',
        'lenght' => 32,
        'notnull' => TRUE
    ),
    'active' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE
    ),
    'multiplesubmit' => array(
        'type' => 'text',
        'length' => 1,
        'notnull' => TRUE
    )
   );
?>
