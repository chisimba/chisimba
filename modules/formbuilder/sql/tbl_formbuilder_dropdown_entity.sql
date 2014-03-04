<?php
/*!
*
*\brief Table for holding all checkbox form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_dropdown_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store dropdown entities for forms',
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
 'dropdownname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'ddoptionlabel' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'ddoptionvalue' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
'defaultvalue' => array(
 'type' => 'boolean',
 'notnull'
),
 'label' => array(
 'type' => 'text',
 'length' => 550,
 'notnull' => 0
),
 'labelorientation' => array(
 'type' => 'text',
 'length' => 20,
 'notnull' => 0
)
);
?>
