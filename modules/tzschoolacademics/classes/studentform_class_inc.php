<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This form will save the registration of new student
 * and also for editing the details of the registered students
 *
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */
class studentform extends object{
   public $lang;
   
   private $fNameValue='';
   private $lNameValue='';
   private $oNameValue='';
   private $dobValue='2011-08-01';
   private $genderValue='M';
   private $religionValue='';
   private $gfNameValue='';
   private $glNameValue='';
   private $goNameValue='';
   private $addressValue='';
   private $locationValue='';
   private $nationalityValue='';
   private $telephoneValue='';
   private $emailValue='';
   private $faxValue='';
   private $relationValue='';
   
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

    private function build(){
        $this->load();
        
        $form=new form('Student',  $this->getAction());
        $topLabel=new label('<h2>Student Information</h2><br/>');
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

        $genderLabel=new label($this->lang->languageText('mod_tzschoolacademics_gender_label','tzschoolacademics'),'gender');
        $genderField=new dropdown('gender');
        $form->addToForm($genderLabel->show().'<br>');
        $genderField->addOption('M','MALE');
        $genderField->addOption('F','FEMALE');        
        $genderField->setValue($this->genderValue);
        $form->addToForm($genderField->show().'<br>');

        $dobLabel=new label($this->lang->languageText('mod_tzschoolacademics_dob_label','tzschoolacademics'),'dob');
        $dobField=$this->getObject('datepicker', 'htmlelements');
       // $dobField->setName('dob');
        $form->addToForm($dobLabel->show().'<br>');
       
        $dobField->setDefaultDate($this->dobValue);
        $form->addToForm($dobField->show().'<br>');

        $religionLabel=new label($this->lang->languageText('mod_tzschoolacademics_religion_label','tzschoolacademics'),'religion');
        $religionField=new textinput('religion');
        $form->addToForm($religionLabel->show().'<br>');
        $religionField->setValue($this->religionValue);
        $form->addToForm($religionField->show().'<br>');

        $classLabel=new label($this->lang->languageText('mod_tzschoolacademics_class_label','tzschoolacademics'),'class');
        $classField=new dropdown('class');
        $form->addToForm($classLabel->show().'<br>');
        $classField->addFromDB(null, null, null, null);
        $classField->setValue($this->classValue);
        $form->addToForm($classField->show().'<br>');

        $yosLabel=new label($this->lang->languageText('mod_tzschoolacademics_yearofstudy_label','tzschoolacademics'),'yearofstudy');
        $yosField=new dropdown('yos');
        $form->addToForm($yosLabel->show().'<br>');
        $yosField->addFromDB(null, null, null, null);
        $yosField->setValue($this->yosValue);
        $form->addToForm($yosField->show().'<br>');

        $middleLabel=new label('<h2>Guardian Information</h2><br/>');
        $form->addToForm($middleLabel->show());
      
        $gfNameField=new textinput('gfirstname');
        $form->addToForm($fNameLabel->show());
        $gfNameField->setValue($this->gfNameValue);
        $form->addToForm($gfNameField->show());

        $glNameField=new textinput('glastname');
        $form->addToForm($lNameLabel->show());
        $glNameField->setValue($this->glNameValue);
        $form->addToForm($glNameField->show());

        $goNameField=new textinput('gothernames');
        $form->addToForm($oNameLabel->show());
        $goNameField->setValue($this->goNameValue);
        $form->addToForm($goNameField->show().'<br>');

        $relationLabel=new label($this->lang->languageText('mod_tzschoolacademics_relation_label','tzschoolacademics'),'relation');
        $relationField=new textinput('relation');
        $form->addToForm($relationLabel->show().'<br>');
        $relationField->setValue($this->relationValue);
        $form->addToForm($relationField->show().'<br/>');

        $nationalityLabel=new label($this->lang->languageText('mod_tzschoolacademics_nationality_label','tzschoolacademics'),'nationality');
       // $nationalityField=new dropdown();
        $nationalityField=  $this->getObject('countries', 'utilities');
       // $nationalityField->getDropDown();
        $form->addToForm($nationalityLabel->show().'<br/>');
       // $nationalityField->setValue($this->nationalityValue);
        $nationality=$nationalityField->getDropDown('country',$this->nationalityValue);
       // $nationality->name='nationality';
        $form->addToForm( $nationality.'<br>');

        $addressLabel=new label($this->lang->languageText('mod_tzschoolacademics_address_label','tzschoolacademics'),'address');
        $addressField=new textinput('address');
        $form->addToForm($addressLabel->show().'<br>');
        $addressField->setValue($this->addressValue);
        $form->addToForm($addressField->show().'<br>');

        $locationLabel=new label($this->lang->languageText('mod_tzschoolacademics_location_label','tzschoolacademics'),'location');
        $locationField=new textinput('location');
        $form->addToForm($locationLabel->show().'<br>');
        $locationField->setValue($this->locationValue);
        $form->addToForm($locationField->show().'<br>');

    
        $telephoneLabel=new label($this->lang->languageText('mod_tzschoolacademics_telephone_label','tzschoolacademics'),'telephone');
        $telephoneField=new textinput('telephone');
        $form->addToForm($telephoneLabel->show().'<br>');
        $telephoneField->setValue($this->telephoneValue);
        $form->addToForm($telephoneField->show().'<br>');
        
        $emailLabel=new label($this->lang->languageText('mod_tzschoolacademics_email_label','tzschoolacademics'),'email');
        $emailField=new textinput('email');
        $form->addToForm($emailLabel->show().'<br>');
        $emailField->setValue($this->emailValue);
        $form->addToForm($emailField->show().'<br>');

        $faxLabel=new label($this->lang->languageText('mod_tzschoolacademics_fax_label','tzschoolacademics'),'fax');
        $faxField=new textinput('fax');
        $form->addToForm($faxLabel->show().'<br>');
        $faxField->setValue($this->faxValue);
        $form->addToForm($faxField->show().'<br>');

        $saveButton=new button('register',$this->lang->languageText('mod_tzschoolacademics_register_label','tzschoolacademics'));
       // $saveButton=new button('save','Submit');
       $saveButton->setToSubmit();
        $form->addToForm($saveButton->showDefault());

        echo $form->show();
    }

        private function getAction(){
        $action=$this->getParam('action','edit_student');
        if($action=='edit_student')
            $formAction=  $this->uri(array('action'=>'edit_student'),'tzschoolacademics');

        else
            $formAction=$this->uri (array('action'=>'add_student'),'tzschoolacademics');
        return $formAction;
    }

        public function setValues($valuesArray=NULL){
            
        }

        public function show(){
        $this->build();
    }

}
?>
