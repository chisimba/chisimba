<?php
/*
 -- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_subjects`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_subjects` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `subject_name` VARCHAR(30) NOT NULL ,
  PRIMARY KEY (`id`) )
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */

$tablename = 'tbl_subjects';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'subject_name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )

    );
?>
