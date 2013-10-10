<?php

$tablename                = 'tbl_rpx_identifiers';
$options                  = array();
$options['comment']       = 'RPX User Identifiers';
$options['collate']       = 'utf8_general_ci';
$options['character_set'] = 'utf8';

$fields = array(
    'id'         => array('type' => 'text', 'length' => 32 ),
    'userid'     => array('type' => 'text', 'length' => 25 ),
    'identifier' => array('type' => 'text', 'length' => 512)
    );
