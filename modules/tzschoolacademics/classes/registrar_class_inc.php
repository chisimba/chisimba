<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class registers system users and perform registration setup
 *
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */
class registrar extends dbTable{

public function init(){
parent::init('tbl_subjects');
parent::init('tbl_class');
parent::init('tbl_subject');
parent::init('tbl_teacher');
parent::init('tbl_guardian');
parent::init('tbl_contact');
}

    public function registerStudent(){
                $guardian=array(
                  'firstname'=>  $this->getParam('gfirstname'),
                  'lastname'=>  $this->getParam('glastname'),
                  'othernames'=>  $this->getParam('gothernames'),
                  'relation'=>  $this->getParam('relation')
                );
                
                $student=array(
                  'firstname'=>  $this->getParam('firstname'),
                  'lastname'=>  $this->getParam('lastname'),
                  'othernames'=>  $this->getParam('othernames'),
                  'gender'=>  $this->getParam('gender'),
                  'religion'=>  $this->getParam('religion'),
                  'birthdate'=>$this->getParam('calendardate'),
                  'reg_no'=>  null,
                  'tbl_guardian_id'=>null
                );
                $contacts=array(
                  'email'=>$this->getParam('email'),
                  'fax'=>$this->getParam('fax'),
                  'phone_number'=>$this->getParam('telephone'),
                 // 'location'=>$this->getParam('location'),
                  'address'=>$this->getParam('address'),
                  'tbl_guardian_id'=>null
                );
                $this->_tableName='tbl_guardian';
                $guardianId=$this->insert($guardian);

                $student['tbl_guardian_id']=$guardianId;
                $contacts['tbl_guardian_id']=$guardianId;

                $this->_tableName='tbl_contact';
                $testRegNum=$this->insert($contacts);
                $student['reg_no']=$testRegNum;

                $this->_tableName='tbl_student';
                $this->insert($student);

    }

    public function registerClass(){
                $class=array(
                  'class_name'=>  $this->getParam('name'),
                  'level'=>  $this->getParam('level'),
                  'stream'=>  $this->getParam('stream'),
                  'tbl_major_id'=>  $this->getParam('major')
                );
                $this->_tableName='tbl_class';
                $this->insert($class);
    }

    public function registerTeacher(){
                $teacher=array(
                  'firstname'=>  $this->getParam('firstname'),
                  'lastname'=>  $this->getParam('lastname'),
                  'othername'=>  $this->getParam('othernames'),
                  'empl_id'=>  $this->getParam('employeeid'),
                    'rank'=>  $this->getParam('rank')
                );
                $this->_tableName='tbl_teacher';
                $this->insert($teacher);
    }

    public function registerSubject(){
                $subject=array(
                  'subject_name'=>  $this->getParam('name'),
                //  'level'=>  $this->getParam('level')
                );
                $this->_tableName='tbl_subjects';
              //  echo $subject['subject_name'];
                $this->insert($subject);
    }

}
?>
