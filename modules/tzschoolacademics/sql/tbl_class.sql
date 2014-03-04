<?php
/* 
 * table to store a list of classes in a secondary school
 *
-- -----------------------------------------------------
-- Table `tbl_class`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tbl_class` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `tbl_major_id` INT NULL ,
  `class_name` VARCHAR(30) NOT NULL ,
  `level` CHAR(20) NOT NULL ,
  `stream` CHAR(20) NULL ,
  PRIMARY KEY (`class_id`) ,
  INDEX `class_FKIndex1` (`tbl_major_id` ASC) ,
  CONSTRAINT `fk_9693af4c-5f5c-11e0-b737-0019d288e6dc`
    FOREIGN KEY (`tbl_major_id` )
    REFERENCES `ERD_SMIS`.`tbl_major` (`id` )
    ON DELETE SET NULL
    ON UPDATE CASCADE)
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;

 */
$tablename = 'tbl_class';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'tbl_major_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

     'class_name' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

      'level' => array(
        'type' => 'text',
        'length' => 20,
        'notnull' => TRUE
        ),

      'stream' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )

    );

  $name = 'tbl_class_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_major_id' => array()
                )
        );

?>
