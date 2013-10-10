<?php


/**
table for class
*/
    $tablename = 'tbl_status';
/**

*/

    $options = array('comment' => 'Table for saving status information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
/**

*/
$fields = array(
'id'=>array(
'type'=>'text',
'length'=>32
),
'status_name' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       )
);
?>