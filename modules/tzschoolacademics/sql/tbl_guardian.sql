<?php

/*

-- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_guardian`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_guardian` (
  `id` VARCHAR(50) NOT NULL ,
  `firstname` VARCHAR(30) NOT NULL ,
  `lastname` VARCHAR(30) NOT NULL ,
  `relation` VARCHAR(30) NOT NULL ,
  `othername` VARCHAR(30) NOT NULL ,
  PRIMARY KEY (`id`) );


*/

$tablename = 'tbl_guardian';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'firstname' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

     'lastname' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

      'othernames' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

       'relation' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )
    );
?>