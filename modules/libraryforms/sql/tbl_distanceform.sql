<?php
//5ive definition
$tablename = 'tbl_distanceform';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for distance users','collate' =>'utf8_general_ci','character_set' =>'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
                

          'userid' => array(
		'type' => 'text',
		'length' => 32
		),               

         
         'surname'=> array(
                     'type' =>'text',
                     'length'=>50
                        ),
          
        'initials'=>array(
                    'type'=>'text',
                    'length'=>10
                    ),

       'title'=>array(
                    'type'=>'text',
                    'length'=>5
                    ),

        'studentno'=> array(
                     'type' =>'text',
                     'length'=>10
                                  
                      ),
          
        'postaladdress'=>array(
                    'type'=>'text',
                    'length'=>150                 
                     ),

        'physicaladdress'=> array(
                     'type' =>'text',
                     'length'=>150                   
                        ),
          
        'postalcode'=>array(
                    'type'=>'text',
                    'length'=>5
                                  
                     ),

        'postalcode2'=> array(
                     'type' =>'text',
                     'length'=>5
                     
                         ),
          
        'telnoh'=>array(
                    'type'=>'text',
                    'length'=>20
                   ),
         
        'telnow'=> array(
                     'type' =>'text',
                     'length'=>20
                      ),
          
        'cell'=>array(
                    'type'=>'text',
                    'length'=>15
                    

         ),
        'fax'=> array(
                     'type' =>'text',
                     'length'=>15
                     ),
          
        'emailaddress'=>array(
                    'type'=>'text',
                    'length'=>50
                    
                        ),

        'course'=> array(
                     'type' =>'text',
                     'length'=>50
                        ),
          
        'department'=>array(
                    'type'=>'text',
                    'length'=>50
                    
                     ),


        'supervisor'=> array(
                     'type' =>'text',
                     'length'=>30
                      ));
?>
