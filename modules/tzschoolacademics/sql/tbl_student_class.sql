<?php
/* 
 -- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_student_class`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_student_class` (
  `tbl_student_reg_no` VARCHAR(20) NOT NULL ,
  `tbl_class_id` INT NOT NULL ,
  `tbl_academic_year_id` INT NOT NULL ,
  PRIMARY KEY (`tbl_student_reg_no`, `tbl_class_id`, `tbl_academic_year_id`) ,
  INDEX `fk_student_has_class_class1` (`tbl_class_id` ASC) ,
  INDEX `fk_student_has_class_student1` (`tbl_student_reg_no` ASC) ,
  INDEX `fk_student_class_academic_year1` (`tbl_academic_year_id` ASC) ,
  CONSTRAINT `fk_student_has_class_student1`
    FOREIGN KEY (`tbl_student_reg_no` )
    REFERENCES `ERD_SMIS`.`tbl_student` (`reg_no` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_has_class_class1`
    FOREIGN KEY (`tbl_class_id` )
    REFERENCES `ERD_SMIS`.`tbl_class` (`class_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_class_academic_year1`
    FOREIGN KEY (`tbl_academic_year_id` )
    REFERENCES `ERD_SMIS`.`tbl_academic_year` (`year_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
 */
  $tablename = 'tbl_student_class';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'tbl_student_reg_no' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

    'tbl_class_id' => array(
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

  $name = 'tbl_student_class_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_student_reg_no' => array(),
                    'tbl_class_id' => array(),
                    'tbl_academic_year_id' => array(),
                )
        );

?>
