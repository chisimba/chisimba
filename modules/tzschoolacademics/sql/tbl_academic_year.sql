<?php

/*
 *table for academic years of a secondary school
 *
 *  
 -- -----------------------------------------------------
-- Table `tbl_academic_year`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tbl_academic_year` (
  `id` INT NOT NULL ,
  `year_name` VARCHAR(20) NOT NULL ,
  PRIMARY KEY (`id`) ,

ENGINE = InnoDB;

 */

$tablename = 'tbl_academic_year';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'year_name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )
     );
?>
