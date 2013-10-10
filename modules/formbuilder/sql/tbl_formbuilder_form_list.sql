<?php
/*!
*
* \brief Table for holding all metadata for all the forms built inside the
* form builder module.
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_form_list';

/*!
* \brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store a list of forms and their details',
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
 'name' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'label' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'details' => array(
 'type' => 'clob',
),
'author' => array(
 'type' => 'text',
 'length' => 60,
 'notnull' => 1
),
'submissionemailaddress' => array(
 'type' => 'text',
 'length' => 100,
 'notnull' => 1
),
'submissionoption' => array(
 'type' => 'text',
 'length' => 60,
 'notnull' => 1
),
'searchclobmetadata' => array(
 'type' => 'text',
 'length' => 250,
 'notnull' => 1
),
 'created' => array(
 'type' => 'timestamp',
 'notnull' => TRUE
)
);
?>
