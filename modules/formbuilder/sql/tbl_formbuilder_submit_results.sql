<?php
/*!
*
*\brief Table for holding all submit results for all forms. This includes the form
* element name and its value filled in by the user and which form the form element
* belongs to.
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_submit_results';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store all the submit results of all built forms',
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
 'type' => 'integer',
 'notnull' => TRUE
),
 'submitnumber' => array(
 'type' => 'integer',
 'notnull' => TRUE
),
 'formelementtype' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'formelementname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'formelementvalue' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => 1
),
'useridofformsubmitter' => array(
 'type' => 'text',
 'length' => 100,
 'notnull' => 1
),
'timeofsubmission' => array(
 'type' => 'timestamp',
 'notnull' => TRUE
)
);
?>
