<?php
/* 
 -- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_result`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tbl_result` (
  `tbl_student_reg_no` VARCHAR(20) NOT NULL ,
  `score` DOUBLE NOT NULL ,
  `tbl_subjects_id` INT NOT NULL ,
  `tbl_exam_id` INT(11)  NOT NULL ,
  `tbl_academic_year_id` INT NOT NULL ,
  `tbl_term_id` INT NOT NULL ,
  PRIMARY KEY (`tbl_student_reg_no`, `tbl_subjects_id`, `tbl_exam_id`, `tbl_academic_year_id`, `tbl_term_id`) ,
  INDEX `result_FKIndex2` (`tbl_student_reg_no` ASC) ,
  INDEX `fk_result_subjects1` (`tbl_subjects_id` ASC) ,
  INDEX `fk_result_exam1` (`tbl_exam_id` ASC) ,
  INDEX `fk_result_academic_year1` (`tbl_academic_year_id` ASC) ,
  INDEX `fk_result_term1` (`tbl_term_id` ASC) ,
  CONSTRAINT `fk_9697bfba-5f5c-11e0-b737-0019d288e6dc`
    FOREIGN KEY (`tbl_student_reg_no` )
    REFERENCES `ERD_SMIS`.`tbl_student` (`reg_no` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_result_subjects1`
    FOREIGN KEY (`tbl_subjects_id` )
    REFERENCES `ERD_SMIS`.`tbl_subjects` (`subject_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_result_exam1`
    FOREIGN KEY (`tbl_exam_id` )
    REFERENCES `ERD_SMIS`.`tbl_exam` (`exam_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_result_academic_year1`
    FOREIGN KEY (`tbl_academic_year_id` )
    REFERENCES `ERD_SMIS`.`tbl_academic_year` (`year_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_result_term1`
    FOREIGN KEY (`tbl_term_id` )
    REFERENCES `ERD_SMIS`.`tbl_term` (`term_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;
 */
$tablename = 'tbl_result';

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

     'tbl_subjects_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

       'tbl_exam_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

       'tbl_academic_year_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

     'tbl_term_id' => array(
        'type' => 'text',
        'length' => 32,
        'notnull' => TRUE
        ),

      'score' => array(
        'type' => 'float',
        'notnull' => TRUE
        )
     );


   $name = 'tbl_result_FKIndex2';

$indexes = array(
                'fields' => array(
                    'tbl_student_reg_no' => array(),
                    'tbl_subjects_id' => array(),
                    'tbl_exam_id' => array(),
                    'tbl_academic_year_id' => array(),
                    'tbl_term_id' => array(),
                )
        );

?>
