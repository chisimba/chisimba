<?php
/**
 *
 * Table for storing weighted columns
 *
 */

/* Set the table name */
 
$tablename = 'tbl_gradebook2_weightedcolumn';

/* Options line for comments, encoding and character set */

   $options = array(
    'comment' => 'Table for tbl_gradebook2_weightedcolumn',
    'collate' => 'utf8_general_ci',
    'character_set' => 'utf8');
/* Create the table fields */
   $fields = array(
    'id' => array(
       'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
       ),
   'userid' => array(
       'type' => 'text',
       'length' => 32,
       'notnull' => TRUE
       ),
   'column_name' => array(
       'type' => 'text',
       'length' => 64,
       'notnull' => TRUE
       ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
        'notnull' => TRUE
        ),
   'display_name' => array(
       'type' => 'text',
       'length' => 64,
       'notnull' => TRUE
       ),
   'description' => array(
       'type' => 'text',
       'notnull' => TRUE
       ),
   'primary_display' => array(
       'type' => 'text',
       'length' => 64,
       'notnull' => true
       ),
   'secondary_display' => array(
       'type' => 'text',
       'length' => 64,
       'notnull' => true
       ),
   'grading_period' => array(
       'type' => 'date',
       'notnull' => true
       ),
   'creationdate' => array(
       'type' => 'timestamp',
       'notnull' => true
       ),
   'include_weighted_grade' => array(
       'type' => 'boolean',
       'notnull' => true
       ),
   'running_total' => array(
       'type' => 'boolean'
       ),
   'show_grade_center_calc' => array(
       'type' => 'boolean',
       'notnull' => true
       ),
   'show_in_mygrades' => array(
       'type' => 'boolean',
       'notnull' => true
       ),
   'show_statistics' => array(
       'type' => 'boolean',
       'notnull' => true
       )
);
?>
