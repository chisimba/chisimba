<?php
//Table Name
$tablename = 'tbl_practicals';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of practicals created in a context', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
        'id' => array(
                'type' => 'text',
                'length' => 32,
                'notnull'=> 1,
                'default' => '',
        ),
        'name' => array(
                'type' => 'text',
                'length' => 150,
        ),
        'context' => array(
                'type' => 'text',
                'length' => 255
        ),
        'description' => array(
                'type' => 'clob',
        ),
        'userid' => array(
                'type' => 'text',
                'length' => 32,
                'notnull' => 1,
        ),
        'resubmit' => array(
                'type' => 'integer',
                'length'=> 4,
                'notnull' => 1,
                'default'=>'0',
        ),
        'format' => array(
                'type'=> 'integer',
                'length'=> 10,
        ),
        'mark' => array(
                'type'=> 'integer',
                'length'=> 10,
        ),
        'percentage' => array(
                'type' => 'integer',
                'length'=> 10,
                'notnull' => 1,
                'default' => '0'
        ),
        'closing_date' => array(
                'type' => 'timestamp',
        ),
        'opening_date' => array(
                'type' => 'timestamp',
        ),
        'last_modified' => array(
                'type' => 'timestamp',
        ),
        'updated' => array(
                'type' => 'timestamp',
                'length' => 14,
                'notnull' => 1,
        ),
        'assesment_type' => array(
                'type' => 'text',
                'length' => 1,
                'notnull' => 1,
                'default' => '0'
        ),
        'email_alert' => array(
                'type' => 'text',
                'length' => 1,
                'default' => '1'
        ),
        'filename_conversion' => array(
                'type' => 'text',
                'length' => 1,
                'default' => '1'
        ),
        'visibility' => array(
                'type' => 'text',
                'length' => 1,
                'default' => '1'
        ),
        'email_alert_onsubmit' => array(
                'type' => 'text',
                'length' => 1,
                'default' => '1'
        ),
        'usegroups' => array(
                'type' => 'text',
                'length' => 1,
                'default' => '1'
        ),
        'usegoals' => array(
                'type' => 'text',
                'length' => 1,
                'default' => '1'
        )
);
?>
