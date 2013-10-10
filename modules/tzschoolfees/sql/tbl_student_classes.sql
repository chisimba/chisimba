<?php


/**
table for class
*/
    $tablename = 'tbl_student_classes';
/**

*/

    $options = array('comment' => 'Table for saving student class information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');
/**

*/


$fields = array(
'id'=>array(
'type'=>'text',
'length'=>32
),
'tbl_student_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
'tbl_class_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

);

  $name = 'tbl_student_class_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_student_id' => array(),
                    'tbl_class_id' => array(),
                )
        );
?>