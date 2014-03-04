<?php
//Table Name
$tablename = 'tbl_practicals_learningoutcomes';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of practicals that have learning outcomes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
        'id' => array(
                'type' => 'text',
                'length' => 32,
                'notnull'=> 1,
                'default' => '',
        ),

        'practical_id' => array(
                'type' => 'text',
                'length' => 32,
                'default' => '',
        ),

        'learningoutcome_id' => array(
                'type' => 'text',
                'length' => 32,
                'default' => '',
        )
);
?>
