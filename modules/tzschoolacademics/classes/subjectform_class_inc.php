<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of subjectform_class_inc
 *
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */
class subjectform extends object {
    public $lang;
    private $levelValue;
    private $nameValue;

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

        $form=new form('Subject',  $this->getAction());
        $topLabel=new label('<h2>Subject Information</h2><br/>');
        $form->addToForm($topLabel->show());

        $nameLabel=new label($this->lang->languageText('mod_tzschoolacademics_subjectname_label','tzschoolacademics'),'name');
        $nameField=new textinput('name');
        $form->addToForm($nameLabel->show().'<br>');
        $nameField->setValue($this->nameValue);
        $form->addToForm($nameField->show().'<br>');

        $levelLabel=new label($this->lang->languageText('mod_tzschoolacademics_level_label','tzschoolacademics'),'level');
        $levelField=new dropdown('level');
        $form->addToForm($levelLabel->show().'<br>');
        $levelField->addOption('O','O LEVEL');
        $levelField->addOption('A','A LEVEL');
        $levelField->setValue($this->levelValue);
        $form->addToForm($levelField->show().'<br>');

       $saveButton=new button('register',$this->lang->languageText('mod_tzschoolacademics_register_label','tzschoolacademics'));
       $saveButton->setToSubmit();
       $form->addToForm($saveButton->showDefault());
        
       echo $form->show();
    }
    
     public function getAction(){
        $action=$this->getParam('action',  'edit_subject');
        if($action=='edit_subject')
            $formAction=  $this->uri(array('action'=> 'edit_subject'),'tzschoolacademics');

        else
            $formAction=$this->uri (array('action'=>  'add_subject'),'tzschoolacademics');
        return $formAction;
    }
    public  function setValues($valuesArray=NULL){

    }

    public function show(){
        $this->build();
    }
}
?>
