<?php

//define table
$tablename = 'tbl_oer_products';
$options = array('comment'=>'Table to store Products','collate'=>'utf8_general_ci','character_set'=>'utf8');

//define fields
    $fields = array(
            'id' => array('type' => 'text', 'length' => 32, 'not null'),
            'parent_id' => array('type' => 'text', 'length' => 32),
            'institutionid' => array('type' => 'text', 'length' => 32),
            'groupid' => array('type' => 'text', 'length' => 32),
            'region' => array('type' => 'text', 'length' => 64),
            'country' => array('type' => 'text', 'length' =>5),
            'title' => array('type' => 'text', 'length' => 255, 'not null'),
            'alternative_title' => array('type' => 'text', 'length' => 255, 'not null'),
            'author' => array('type' => 'text', 'length' => 512),
            'othercontributors' => array('type' => 'text', 'length' => 512),
            'publisher' => array('type' => 'text', 'length' => 512),
            'language' => array('type' => 'text', 'length' => 5),
            'translation_of' => array('type' => 'text', 'length' => 32),
            'abstract' => array('type' => 'text'),
            'description' => array('type' => 'text'),            
            'oerresource' => array('type' => 'text', 'length' => 512),
            'provenonce' => array('type' => 'text', 'length' => 512),
            'accredited' => array('type' => 'text', 'length' => 5),
            'accreditation_body' => array('type' => 'text', 'length' => 255),
            'accreditation_date' => array('type' => 'text', 'length' => 255),
            'contacts' => array('type' => 'text'),
            'other_contributors' => array('type' => 'text'),
            'format' => array('type' => 'text', 'length' => 32),
            'coverage' => array('type' => 'text', 'length' => 255),
            'rights' => array('type' => 'text', 'length' => 512),
            'rights_holder' => array('type' => 'text', 'length' => 255),
            'relation' => array('type' => 'text', 'length' => 32),
            'relation_type' => array('type' => 'text', 'length' => 32),
            'status' => array('type' => 'text', 'length' => 255),
            'thumbnail' => array('type' => 'text', 'length' => 512),
             'themes'=>array('type' => 'text'),
             'keywords'=>array('type' => 'text'),
             'deleted' => array('type' => 'integer', 'length' => 1, 'default' => '0'),
        );
?>