<?php
/*!
*
*\brief Table for holding all text input form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_textinput_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store text input entities for forms',
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
 'textinputformname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'textinputname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
), 'textvalue' => array(
 'type' => 'clob'
),
 'texttype' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => 1
),
 'textsize' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => 1
),
 'maskedinputchoice' => array(
 'type' => 'text',
 'length' => 30,
 'notnull' => 1
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
