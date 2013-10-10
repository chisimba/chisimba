<?php
/* 
 -- -----------------------------------------------------
-- Table `ERD_SMIS`.`tbl_class_subjects`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `ERD_SMIS`.`tbl_class_subjects` (
  `tbl_class_id` INT NOT NULL ,
  `subjects_subj_id` INT NOT NULL ,
  PRIMARY KEY (`tbl_class_id`, `subjects_subj_id`) ,
  INDEX `class_has_subjects_FKIndex1` (`tbl_class_id` ASC) ,
  INDEX `class_has_subjects_FKIndex2` (`subjects_subj_id` ASC) ,
  CONSTRAINT `fk_96926d1c-5f5c-11e0-b737-0019d288e6dc`
    FOREIGN KEY (`tbl_class_id` )
    REFERENCES `ERD_SMIS`.`tbl_class` (`class_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_969303b2-5f5c-11e0-b737-0019d288e6dc`
    FOREIGN KEY (`subjects_subj_id` )
    REFERENCES `ERD_SMIS`.`tbl_subjects` (`subject_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
PACK_KEYS = 0
ROW_FORMAT = DEFAULT;
 */
 $tablename = 'tbl_class_subjects';

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

    'tbl_subject_id' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => TRUE
        )

    );

 $name = 'class_has_subjects_FKIndex1';

$indexes = array(
                'fields' => array(
                    'tbl_class_id' => array(),
                    'tbl_subject_id' => array(),
                )
        );
?>
