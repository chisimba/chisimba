<?php
/*!
*
*\brief Table for holding all all the publishing options. This includes what actions
* to preform when the form is submitted by the user of the form.
*
*/

/*!
* \brief Set the table name
*/
$tablename = 'tbl_formbuilder_publish_options';

/*!
\brief Options line for comments, encoding and character set
*/
$options = array (
 'comment' => 'table to store publishing options for forms',
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
 'formname' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => TRUE
),
 'publishoption' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => FALSE
),
 'siteurl' => array(
 'type' => 'text',
 'length' => 550,
 'notnull' => FALSE
),
 'chisimbamodule' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => FALSE
),
'chisimbaaction' => array(
 'type' => 'text',
 'length' => 150,
 'notnull' => FALSE
),
'chisimbaparameters' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => FALSE
),
'chisimbadiverterdelay' => array(
 'type' => 'text',
 'length' => 10,
 'notnull' => FALSE
)
);
?>
