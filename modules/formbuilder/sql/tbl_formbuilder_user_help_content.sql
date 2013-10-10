<?php
/*!
*\brief Table for holding all help text content for this module. This table
* gets all of it entries from the XML file defaultdata.xml when this module
* first gets installed.
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_user_help_content';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store the form builder user help manual',
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
'name' => array(
'type' => 'text',
'length' => 150,
'notnull' => TRUE
),
'type' => array(
'type' => 'text',
'length' => 150,
'notnull' => 1
),
'datecreated' => array(
'type' => 'timestamp'
),
'pagecontent' => array(
'type' => 'clob'
)
);
?>
