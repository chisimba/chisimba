<?php
// Table Name
$tablename = 'tbl_leaverecords';

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
        'daysleft' => array(
                'type' => 'text',
                'length' => 32
        ),
        'totaldaystaken'=>array(
                'type'=>'text',
                'length'=>32
        )
);
?>