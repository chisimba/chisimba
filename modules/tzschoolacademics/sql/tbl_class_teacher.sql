<?php
/*
-- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_class_teacher`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_class_teacher` (
  `tbl_academic_year_id` INT NOT NULL ,
  `tbl_teacher_empl_id` VARCHAR(30) NOT NULL ,
  `tbl_class_id` INT NOT NULL ,
  PRIMARY KEY (`tbl_academic_year_id`, `tbl_teacher_empl_id`, `tbl_class_id`) ,
  INDEX `fk_class_teacher_teacher1` (`tbl_teacher_empl_id` ASC) ,
  INDEX `fk_class_teacher_class1` (`tbl_class_id` ASC) ,
  CONSTRAINT `fk_class_teacher_academic_year1`
    FOREIGN KEY (`tbl_academic_year_id` )
    REFERENCES `ERD_SMIS`.`tbl_academic_year` (`year_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class_teacher_teacher1`
    FOREIGN KEY (`tbl_teacher_empl_id` )
    REFERENCES `ERD_SMIS`.`tbl_teacher` (`empl_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class_teacher_class1`
    FOREIGN KEY (`tbl_class_id` )
    REFERENCES `ERD_SMIS`.`tbl_class` (`class_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

 */
$tablename = 'tbl_class_teacher';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

  $fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

      'tbl_class_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

    'tbl_teacher_empl_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

      'tbl_academic_year_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        )

    );

  $name = 'tbl_class_teacher_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_teacher_empl_id' => array(),
                    'tbl_class_id' => array(),
                    'tbl_academic_year_id' => array(),
                )
        );

?>
