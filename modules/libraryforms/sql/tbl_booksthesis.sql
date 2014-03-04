<?php
//5ive definition
$tablename = 'tbl_booksthesis';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for books thesis only','collate' =>'utf8_general_ci','character_set' =>'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),

	'userid' => array(
		'type' => 'text',
		'length' => 32
		),
                                
         
        'bauthor'=>array(
                    'type'=>'text',
                    'length'=>150
                     ),


        'btitle'=> array(
                     'type' =>'text',
                     'length'=>100
                     
                         ),

       'bplace'=>array(
                    'type'=>'text',
                    'length'=>100
                      ),

        'bpublisher'=> array(
                     'type' =>'text',
                     'length'=>100
                     ),
          
        'bdate'=>array(
                    'type'=>'text',
                    'length'=>30                                     
                     ),

        'bedition'=> array(
                     'type' =>'text',
                     'length'=>30 
                      ),
                  
        'bisbn'=>array(
                    'type'=>'text',
                    'length'=>150
                    ),
                 
         
        'bseries'=> array(
                     'type' =>'text',
                     'length'=>150
                     
			),
                    

        'bcopy'=> array(
                     'type' =>'text',
                     'length'=>50
                     
                         ),

        'btitlepages'=> array(
                     'type' =>'text',
                     'length'=>150
                     
                         ),


        'bpages'=> array(
                     'type' =>'text',
                     'length'=>50
                     
                         ),

        'bthesis'=> array(
                     'type' =>'text',
                     'length'=>150
                    
                        ),
    

        'bname'=> array(
                     'type' =>'text',
                     'length'=>150
                     
                         ),

        'baddress'=> array(
                     'type' =>'text',
                     'length'=>150
                    
                         ),

        'bcell'=>array(
                    'type'=>'text',
                    'length'=>15
                    
			),
         
        'bfax'=> array(
                     'type' =>'text',
                     'length'=>20
                     ),
          
        'btel'=>array(
                    'type'=>'text',
                    'length'=>20
                    
                        ),
           
          
        'btelw'=>array(
                    'type'=>'text',
                    'length'=>20
                    
                        ),

   
        'bemailaddress'=>array(
                    'type'=>'text',
                    'length'=>30
                    ),
       
          
        'bentitynum'=>array(
                    'type'=>'text',
                    'length'=>30
                     ),
          
        'bstudentno'=>array(
                    'type'=>'text',
                    'length'=>10
                   ),
               
         
        'bcourse'=> array(
                     'type' =>'text',
                     'length'=>50
                       ),

         'blocal'=>array(
                    'type'=>'text',
                     'length'=>20
		),

        'bpostgrad'=>array(
                       'type'=>'text',
                        'length'=>20
                
           ));       


?>
