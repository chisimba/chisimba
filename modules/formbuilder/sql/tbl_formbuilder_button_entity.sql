<?php
/*!
*
*\brief Table for holding all button form elements.
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_button_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store button entities for forms',
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
 'buttonformname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'buttonname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'buttonlabel' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
'issettoresetorsubmit' => array(
 'type' => 'text',
 'length' => 40,
 'notnull' => 1
)
);
?>
