<?php
/*
-- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_teacher`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_teacher` (
  `empl_id` VARCHAR(30) NOT NULL ,
  `firstname` VARCHAR(30) NOT NULL ,
  `lastname` VARCHAR(30) NOT NULL ,
  `othernames` VARCHAR(50) NULL ,
  `rank` CHAR(20) NULL ,
  PRIMARY KEY (`empl_id`) ,
  UNIQUE INDEX `empl_id_UNIQUE` (`empl_id` ASC) )
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */

$tablename = 'tbl_teacher';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'empl_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
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

       'othername' => array(
        'type' => 'text',
        'length' => 50
        ),

       'rank' => array(
        'type' => 'text',
        'length' => 20,
        )
      );

?>
