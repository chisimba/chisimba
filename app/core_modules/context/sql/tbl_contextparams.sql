<?php
$tablename = 'tbl_contextparams';

$options = array('comment' => 'Context Information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'contextcode' => array(
        'type' => 'text',
        'length' => 255,
            'notnull' => TRUE
        ),
    'param' => array(
        'type' => 'text',
        'length' => 255,
            'notnull' => TRUE
        ),
        'value' => array(
        'type' => 'text',
        'length' => 255
        )
    );

$name = 'contextcode';

$indexes = array(
                'fields' => array(
                    'contextCode' => array()
                )
        );
?>