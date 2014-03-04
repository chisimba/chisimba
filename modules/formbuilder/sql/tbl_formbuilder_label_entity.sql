<?php
/*!
*
*\brief Table for holding all label form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_label_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store label entities for forms',
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
 'labelname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'label' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'breakspace' => array(
 'type' => 'text',
 'length' => 50,
'notnull'=>0
)
);
?>
