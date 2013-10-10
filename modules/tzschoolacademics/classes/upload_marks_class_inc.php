<?php

if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class upload_marks extends object {

    public $db_access;

    public function init() {

        $this->db_access = $this->newObject('marksdb', 'tzschoolacademics');
    }

    /*
      below function instantiates the form elements of the htmlelements class
     */

    public function loadElements() {

        //Load the form class
        $this->loadClass('form', 'htmlelements');
        //Load the textinput class
        $this->loadClass('textinput', 'htmlelements');
        //Load the textarea class
        //Load the label class
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        //Load the button object
        $this->loadClass('button', 'htmlelements');

        //load the dropdown class

        $this->loadClass('dropdown', 'htmlelements');
    }

    public function buildUploadForm() {

        $this->loadElements();

        $objform = new form('uploadmarks');
        $objform->action=$this->uri(array('action' =>'upload'),'academic');
        $objform->setDisplayType(2);
        $objform->setEncType($encType = 'multipart/form-data');
        //-----------------------------------------------------------------------------------
        $objlabel = new label('class');
        $objdropdown = new dropdown('class');
        $displayclass = $this->db_access->load_classes();
        foreach ($displayclass as $row) {
            $objdropdown->addOption($value = $row['puid'], $label = $row['class_name'] . $row['stream']);
        }
        //---------------------------------------------------------------------------------------------
        $objsubjlabel = new label('subject');
        $objsubjdropdown = new dropdown('subject');
        $displaysubject = $this->db_access->load_subjects();

        foreach ($displaysubject as $row) {
            $objsubjdropdown->addOption($value = $row['puid'], $label = $row['subject_name']);
        }
        //-------------------------------------------------------------------------------------------------
        $objacalabel = new label('academic year');
        $objacadropdown = new dropdown('academic_year');
        $displayacademic_year = $this->db_access->load_academic_year();
        foreach ($displayacademic_year as $row) {
            $objacadropdown->addOption($row['puid'], $row['year_name']);
        }
        //------------------------------------------------------------------------------------------------
        $objtermlabel = new label('term');
        $objtermdropdown = new dropdown('term');
        $displayterm = $this->db_access->load_term();

        foreach ($displayterm as $row) {
            $objtermdropdown->addOption($row['puid'], $row['term_name']);
        }
        //------------------------------------------------------------------------------------------------------
        $objexamtypelabel = new label('Exam');

        $objexamtypedropdown = new dropdown('exam');
        $displayexam = $this->db_access->load_exam_type();

        foreach ($displayexam as $row) {
            $objexamtypedropdown->addOption($row['puid'], $row['exam_type']);
        }
        
        $objectuploadlabel = new label('browse result file');
        $fileinput = new textinput('file', $value=null, 'file', $size=null);

      //------------------------------------------------------------------------------------------------------------

     /* creates a new button object
      */

        $objsubmit = new button('upload', 'upload');
        $objsubmit->setToSubmit();
        $objsubmit->setToReset();
      //----------------------------------------------------------------------------------------------------------------

        $objform->addToForm($objlabel->show());

        $objform->addToForm($objdropdown->show());

        $objform->addToForm($objsubjlabel->show());
        $objform->addToForm($objsubjdropdown->show());

        $objform->addToForm($objacalabel->show());
        $objform->addToForm($objacadropdown->show());

        $objform->addToForm($objtermlabel->show());
        $objform->addToForm($objtermdropdown->show());

        $objform->addToForm($objexamtypelabel->show());
        $objform->addToForm($objexamtypedropdown->show());

        $objform->addToForm( $objectuploadlabel->show());
        $objform->addToForm($fileinput->show());

        $objform->addToForm($objsubmit->show());
 


        return $objform->show();
    }

}

?>
