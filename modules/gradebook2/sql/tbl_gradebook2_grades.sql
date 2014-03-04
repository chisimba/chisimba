<?php
/**
 *
 * Table for storing learner grades
 *
 */

/* Set the table name */
 
$tablename = 'tbl_gradebook2_grades';

/* Options line for comments, encoding and character set */

   $options = array(
    'comment' => 'Table for tbl_gradebook2_grades',
    'collate' => 'utf8_general_ci',
    'character_set' => 'utf8');
/* Create the table fields */
   $fields = array(
    'id' => array(
       'type' => 'text',
       'length' => 32,
       'notnull' => 1
       ),
   'columnid' => array(
       'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
       ),
   'learnerid' => array(
       'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
       ),
   'totalgrade' => array(
       'type' => 'text',
       'length' => 32,
       'notnull' => 1
       )
);
?>
