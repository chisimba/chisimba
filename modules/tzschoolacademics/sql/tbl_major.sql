<?php
/* 
-- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_major`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_major` (
  `id` INT NOT NULL ,
  `name` VARCHAR(30) NULL ,
  PRIMARY KEY (`id`) )
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */
$tablename = 'tbl_major';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

    );
?>
