<?php
/* 
 -- -----------------------------------------------------
-- Table `tbl_term`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_term` (
  `id` INT NOT NULL ,
  `term_name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`)
ENGINE = InnoDB;

 */

$tablename = 'tbl_term';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'term_name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )
     );
?>
