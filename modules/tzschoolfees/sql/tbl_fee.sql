<?php


/**
table for class
*/
    $tablename = 'tbl_fee';
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
'amount_payable' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ),
   'description' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       ),
 'year_fee' => array(
       'type' => 'text',
       'length' => 25,
       'notnull' => TRUE
       )

);
?>