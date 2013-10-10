<?php
// Table Name
$tablename = 'tbl_leaves';

//Options line for comments, encoding and character set
$options = array('comment' => 'Storage of data for the registerinterest module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
        'id'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'name'=>array(
                'type'=>'text',
                'length'=>32
        ),
        'numberofdays' => array(
                'type' => 'text',
                'length' => 32
        )
);
?>