<?php
$tablename = 'tbl_form_subrecords';
$options = array('comment' => 'Form records that can contain data from many elements per record_id', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'record_id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'name' => array(
        'type' => 'text',
        'length' => 32
    ),
    'value' => array(
        'type' => 'clob'
    ),
    'attributes' => array(
        'type' => 'text',
        'length' => 32
    )
);
?>
