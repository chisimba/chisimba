<?php

// Table Name
$tablename = 'tbl_news_categories';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the categories under which news stories can be placed', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => 1
    ),
    'categoryname' => array (
        'type' => 'text',
        'length' =>50,
        'notnull' => 1
    ),
    'categoryorder' => array (
        'type' => 'integer',
        'length' => 10
    ),
    'defaultsticky' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
    ),
    'itemsorder' => array (
        'type' => 'text',
        'length' => 255,
    ),
    'itemsview' => array (
        'type' => 'text',
        'length' => 20,
    ),
    'introduction' => array (
        'type' => 'text',
    ),
    'usingbasic' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
    ),
    'basicview' => array (
        'type' => 'text',
        'length' => 20,
    ),
    'showintroduction' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'N'
    ),
    'blockonfrontpage' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
    ),
    'showrelateditems' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
    ),
    'ownrssfeed' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
    ),
    'showsocialbookmarking' => array (
        'type' => 'text',
        'length' => 1,
        'default' => 'Y'
    ),
    'pagination' => array (
        'type' => 'integer',
        'length' => 10,
        'notnull' => 1
    ),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_categories_idx';

$indexes = array(
                'fields' => array(
                    'categoryname' => array(),
                    'categoryorder' => array(),
                    'itemsorder' => array(),
                    'itemsview' => array(),
                    'usingbasic' => array(),
                    'basicview' => array(),
                    'showintroduction' => array(),
                    'blockonfrontpage' => array(),
                    'showrelateditems' => array(),
                    'ownrssfeed' => array(),
                    'showsocialbookmarking' => array(),
                )
        );




?>