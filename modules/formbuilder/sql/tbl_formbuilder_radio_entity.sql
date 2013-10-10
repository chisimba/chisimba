<?php
/*!
*
*\brief Table for holding all radio form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_radio_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store radio entities for forms',
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
 'radioname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'radiooptionlabel' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'radiooptionvalue' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
'defaultvalue' => array(
 'type' => 'boolean',
 'notnull'
),
 'breakspace' => array(
 'type' => 'text',
 'length' => 50,
'notnull'=>0
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
