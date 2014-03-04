<?php
$tablename = 'tbl_form_elements';
$options = array('comment' => 'Form elements that can contain many elements per form_id', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'form_id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'name' => array(
        'type' => 'text',
        'length' => 50
    ),
    'title' => array(
        'type' => 'text',
        'length' => 50
    ),
    'description' => array(
        'type' => 'text',
        'length' => 250
    ),
    'type' => array(
        'type' => 'text',
        'length' => 50
    ),
    'width' => array(
        'type' => 'text',
        'length' => 50
    ),
    'height' => array(
        'type' => 'text',
        'length' => 50
    ),
    'css_class' => array(
        'type' => 'text',
        'length' => 255
    ),
    'script' => array(
        'type' => 'clob',
    ),
    'body' => array(
        'type' => 'clob'
    )
);
?>
