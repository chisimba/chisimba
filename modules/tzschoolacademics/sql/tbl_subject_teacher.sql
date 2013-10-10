<?php
/* 
 -- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_subject_teacher`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_subject_teacher` (
  `tbl_teacher_empl_id` VARCHAR(30) NOT NULL ,
  `tbl_subjects_id` INT NOT NULL ,
  `tbl_class_id` INT NOT NULL ,
  `tbl_academic_year_id` INT NOT NULL ,
  PRIMARY KEY (`tbl_teacher_empl_id`, `tbl_subjects_id`, `tbl_class_id`, `tbl_academic_year_id`) ,
  INDEX `fk_class_has_teacher_class1` (`tbl_class_id` ASC) ,
  INDEX `fk_class_has_teacher_subjects1` (`tbl_subjects_id` ASC) ,
  INDEX `fk_class_subject_teacher_teacher1` (`tbl_teacher_empl_id` ASC) ,
  INDEX `fk_class_subject_teacher_academic_year1` (`tbl_academic_year_id` ASC) ,
  CONSTRAINT `fk_class_has_teacher_class1`
    FOREIGN KEY (`tbl_class_id` )
    REFERENCES `ERD_SMIS`.`tbl_class` (`class_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class_has_teacher_subjects1`
    FOREIGN KEY (`tbl_subjects_id` )
    REFERENCES `ERD_SMIS`.`tbl_subjects` (`subject_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class_subject_teacher_teacher1`
    FOREIGN KEY (`tbl_teacher_empl_id` )
    REFERENCES `ERD_SMIS`.`tbl_teacher` (`empl_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_class_subject_teacher_academic_year1`
    FOREIGN KEY (`tbl_academic_year_id` )
    REFERENCES `ERD_SMIS`.`tbl_academic_year` (`year_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

 */

$tablename = 'tbl_subject_teacher';

$options = array('collate' => 'utf8_general_ci', 'character_set' => 'utf8');

  $fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),

    'tbl_teacher_empl_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

    'tbl_subjects_id' => array(
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

  $name = 'tbl_subject_teacher_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_teacher_empl_id' => array(),
                    'tbl_subjects_id' => array(),
                    'tbl_class_id' => array(),
                    'tbl_academic_year_id' => array(),
                )
        );


?>
