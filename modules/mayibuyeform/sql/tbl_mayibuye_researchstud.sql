<?php
//5ive definition
$tablename = 'tbl_mayibuye_researchformstud';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for researchformstud','collate' =>'utf8_general_ci','character_set' =>'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
      'userid'=> array(
	        'type' => 'text',
		'length' => 5
		),

	'nameofresgn'=>array(
		 'type'=>'text',
		  'length'=>50
		),
	
	'jobtitle2'=>array(
		 'type'=>'text',
		  'length'=>50
		),	

	'organizationname'=>array(
		 'type'=>'text',
		  'length'=>50	
		),

          'postalddress2'=>array(
		 'type'=>'text',
		  'length'=>60
		),

	 'tell'=>array(
		 'type'=>'text',
		  'length'=>20	
		),

	 'fax'=>array(
		 'type'=>'text',
		  'length'=>20
		));
	?>
