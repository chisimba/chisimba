<?php
/**
* Database Table Phonebook
* @author Ewan Burns
* @author Godwin Du Plessis 
* @copyright 2007 University of the Western Cape
*/

//Chisimba definition
$tablename = 'tbl_phonebook';

//Options line for comments, encoding and character set
$options = array('comment' => 'Used to store your contact list', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
	),
	'userid' => array(
		'type' => 'integer',
		'length' => 32
	),
	'firstname' => array(
		'type' => 'text',
		'length' => 32
	),
	'lastname' => array(
		'type' => 'text',
		'length' => 32
	),
	'emailaddress' => array(
		'type' => 'text',
		'length' => 32
	),

	'landlinenumber' => array(
		'type' => 'text',
		'length' => 13
	),
	'cellnumber' => array(
		'type' => 'text',
		'length' => 32
	),
	'address' => array(
		'type' => 'text',
		'length' => 255
	),
	'updated' => array(
	'type' => 'timestamp',
	),
	  'modified' => array(
		'type' => 'timestamp',
	),
  
        'modified_by' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

	),
   
       'checked_out' => array(
		'type' => 'integer',
        'length' => 11,
        'unsigned' => TRUE,

        ), 


       'created_by' => array(
		'type' => 'text',

     
	),

	
);
?>
