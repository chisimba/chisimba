<?php
/*!
*
*\brief Table for holding all checkbox form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_datepicker_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store date picker drop down menu entities for forms',
 'character_set' => 'utfs');


/*!
* \brief Create the table fields.
*/
$fields = array(
 'id' => array(
 'type' => 'text',
 'length' => 32,
 'notnull' => 1
),
 'formnumber' => array(
'type' => 'text',
 'length' => 10,
 'notnull' => 1
),
 'datepickername' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'datepickervalue' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'defaultdate' => array(
 'type' => 'text',
 'length' => 50,
'notnull'=>0
),
 'dateformat' => array(
 'type' => 'text',
 'length' => 20,
'notnull'=>0
)
);
?>
