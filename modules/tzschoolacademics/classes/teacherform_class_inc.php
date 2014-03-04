<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This form will save the registration of new entities
 * and also for editing the details of the registered entities
 * 
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */

class teacherform extends object{

    public $lang;
   private $fNameValue='';
   private $lNameValue='';
   private $oNameValue='';
   private $rankValue;
   private $idValue;

    public function init(){
       $this->lang=$this->getObject('language', 'language');
    }

    private function load(){
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('datepicker', 'htmlelements');
    }

    private  function build(){
        $this->load();

         $form=new form('Teacher',  $this->getAction());
        $topLabel=new label('<h2>Teacher Information</h2><br/>');
        $form->addToForm($topLabel->show());
        $fNameLabel=new label($this->lang->languageText('mod_tzschoolacademics_fname_label','tzschoolacademics'),'firstname');
        $fNameField=new textinput('firstname');
        $form->addToForm($fNameLabel->show());
        $fNameField->setValue($this->fNameValue);
        $form->addToForm($fNameField->show());

        $lNameLabel=new label($this->lang->languageText('mod_tzschoolacademics_lname_label','tzschoolacademics'),'lastname');
        $lNameField=new textinput('lastname');
        $form->addToForm($lNameLabel->show());
        $lNameField->setValue($this->lNameValue);
        $form->addToForm($lNameField->show());

        $oNameLabel=new label($this->lang->languageText('mod_tzschoolacademics_oname_label','tzschoolacademics'),'othernames');
        $oNameField=new textinput('othernames');
        $form->addToForm($oNameLabel->show());
        $oNameField->setValue($this->oNameValue);
        $form->addToForm($oNameField->show().'<br>');

        $idLabel=new label($this->lang->languageText('mod_tzschoolacademics_employeeid_label','tzschoolacademics'),'employeeid');
        $idField=new textinput('employeeid');
        $form->addToForm($idLabel->show().'<br>');
        $idField->setValue($this->idValue);
        $form->addToForm($idField->show().'<br>');

        $rankLabel=new label($this->lang->languageText('mod_tzschoolacademics_rank_label','tzschoolacademics'),'rank');
        $rankField=new dropdown('rank');
        $form->addToForm($rankLabel->show().'<br>');
        $rankField->addFromDB(null, null, null, null);
        $rankField->setValue($this->rankValue);
        $form->addToForm($rankField->show().'<br>');

       $saveButton=new button('register',$this->lang->languageText('mod_tzschoolacademics_register_label','tzschoolacademics'));
       // $saveButton=new button('save','Submit');
       $saveButton->setToSubmit();
        $form->addToForm($saveButton->showDefault());
        echo $form->show();
    }

     public function getAction(){
        $action=$this->getParam('action',  'edit_teacher');
        if($action=='edit_teacher')
            $formAction=  $this->uri(array('action'=> 'edit_teacher'),'tzschoolacademics');

        else
            $formAction=$this->uri (array('action'=>  'add_teacher'),'tzschoolacademics');
        return $formAction;
    }
    public  function setValues($valuesArray=NULL){

    }

    public function show(){
        $this->build();
    }
    
}
?>
