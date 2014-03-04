<?php
/*!
*
*\brief Table for holding all text area form elements inserted by form designers
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_textarea_entity';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store text area entities for forms',
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
 'textareaformname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'textareaname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'textareavalue' => array(
 'type' => 'clob'
),
 'columnsize' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => 1
),
 'rowsize' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => 1
),
 'simpleoradvancedchoice' => array(
 'type' => 'text',
 'length' => 20,
 'notnull' => 1
),
 'toolbarchoice' => array(
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
