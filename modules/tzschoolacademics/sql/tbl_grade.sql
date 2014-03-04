<?php
/* 
-- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_grade`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_grade` (
  `id` INT(11)  NOT NULL AUTO_INCREMENT ,
  `grade_name` CHAR(30) NOT NULL ,
  `min_value` DOUBLE NOT NULL ,
  `max_value` DOUBLE NOT NULL ,
  `remarks` VARCHAR(30) NOT NULL ,
  `level` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) )
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */

$tablename = 'tbl_grade';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'grade_name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

    'min_value' => array(
        'type' => 'float'
        ),

      'max_value' => array(
        'type' => 'float'
        ),

    'remarks' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

    'level' => array(
        'type' => 'text',
        'length' => 20,
        'notnull' => TRUE
        )
    );


?>
