<?php
/*!
*
* \brief Table for holding all the form element metadata. Each form element is given a
*  ID or a form element indentifier. Entries also shows which form element belongs
* to which form in which order.
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_form_elements';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store all form element names and their orders for all forms',
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
 'formname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'formelementtpye' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
 'formelementname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1

),
'formelementorder' => array(
 'type' => 'integer',
 'notnull' => 1,
'auto_increment' => 1
)
);
?>
