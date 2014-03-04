<?php


/**
table for class 
*/
    $tablename = 'tbl_classes';
/**

*/

    $options = array('comment' => 'Table for saving class information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
/**
   
*/


$fields = array(
'id'=>array(
'type'=>'text',
'length'=>32
),
'class_name' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ),
'tbl_fee_id'=>array(
'type' => 'text',
'length'=>32
),
   'class_stream' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ),

);

$name = 'tbl_classes_Fkindex1';
$indexes = array(
        $fields = array(
          'tbl_fee_id'=>array()
)
);

?>