<?php
//5ive definition
$tablename = 'tbl_mayibuyeform_researchform';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for researchform','collate' =>'utf8_general_ci','character_set' =>'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
      'userid'=> array(
	        'type' => 'text',
		'length' => 5
		),
                          
        'date'=>array(
                    'type'=>'text',
                    'length'=>150
                    ),

       'name'=>array(
                    'type'=>'text',
                    'length'=>50
                    ),

        'telno'=> array(
                     'type' =>'text',
                     'length'=>10
                      ),
          
        'faxno'=>array(
                    'type'=>'text',
                    'length'=>30
                                     
                     ),
                    
        'emailaddress'=>array(
                    'type'=>'text',
                    'length'=>150 
                     ));
?>
