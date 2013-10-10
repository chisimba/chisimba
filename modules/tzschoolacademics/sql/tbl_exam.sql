<?php
/* 
-- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_exam`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tbl_exam` (
  `id` INT(11)  NOT NULL AUTO_INCREMENT ,
  `exam_type` VARCHAR(30) NOT NULL ,
  `contribution` DOUBLE NOT NULL ,
  PRIMARY KEY (`id`) )
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;
 */

$tablename = 'tbl_exam';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'exam_type' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),
    'contribution' => array(
        'type' => 'float'

        )
    );


?>
