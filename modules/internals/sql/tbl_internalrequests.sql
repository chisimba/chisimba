<?php
// Table Name
$tablename = 'tbl_internalrequests';


//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of data for the registerinterest module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
        'id'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'userid'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'leaveid'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'days'=>array(
                'type'=>'text',
                'length'=>20
        ),
        'status'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'comments'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'startdate'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'enddate'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'requestdate'=>array(
            'type'=>'text',
            'length'=>32
        )
);
?>