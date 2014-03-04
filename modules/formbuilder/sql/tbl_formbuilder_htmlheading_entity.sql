<?php
/*!
*
*\brief Table for holding all html heading form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_htmlheading_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store HTML Heading entities for forms',
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
 'headingname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'heading' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'size' => array(
 'type' => 'integer',
 'notnull' => TRUE
),
 'alignment' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => 1
)
);
?>
