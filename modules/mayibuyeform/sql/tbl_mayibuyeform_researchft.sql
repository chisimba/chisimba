<?php
//5ive definition
$tablename = 'tbl_mayibuyeform_researchft';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for researchft','collate' =>'utf8_general_ci','character_set' =>'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
                              
    'nameofsignotory'=>array(
                    'type'=>'text',
                    'length'=>50
                    ),

    'jobtitle'=>array(
                     'type' =>'text',
                     'length'=>10
                      ),
     
   'nameoforganization'=>array(
                    'type'=>'text',
                    'length'=>30
			),


       	'postaladdress'=>array(
			'type'=>'text',
			'length'=>30
		),

	'physicaladdress'=>array(
			'type'=>'text',
			'length'=>30
		),

	'vatnum'=>array(
			'type'=>'text',
			'length'=>30

		),

	'jobno'=>array(
			'type'=>'text',
			'length'=>30

		),

	'telephone'=>array(
			'type'=>'text',
			'length'=>30
		),
			
			
	'faxnumber'=>array(
			'type'=>'text',
			'length'=>30
		),

	'email'=>array(
			'type'=>'text',
			'length'=>30  
          
                     ));
?>
