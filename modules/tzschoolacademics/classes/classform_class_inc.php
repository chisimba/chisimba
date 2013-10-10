<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classform_class_inc
 *
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */
class classform extends object{
    public $lang;
    private $streamValue;
    private $majorValue;
    private $levelValue;
    private $nameValue;
    private $registrar;

    public function init(){
       $this->lang=$this->getObject('language', 'language');
       $this->registrar=$this->getObject('registrar');
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
       
        $form=new form('Class',  $this->getAction());
        $topLabel=new label('<h2>Class Information</h2><br/>');
        $form->addToForm($topLabel->show());

        $nameLabel=new label($this->lang->languageText('mod_tzschoolacademics_classname_label','tzschoolacademics'),'name');
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
        
        $streamLabel=new label($this->lang->languageText('mod_tzschoolacademics_class_stream_label','tzschoolacademics'),'name');
        $streamField=new textinput('stream');
        $form->addToForm($streamLabel->show().'<br>');
        $streamField->setValue($this->streamValue);
        $form->addToForm($streamField->show().'<br>');

        $majorLabel=new label($this->lang->languageText('mod_tzschoolacademics_major_label','tzschoolacademics'),'major');
        $majorField=new dropdown('major');
        $form->addToForm($majorLabel->show().'<br>');
        $this->registrar->_tableName='tbl_major';
        $majorField->addFromDB($this->registrar->getAll(), 'name', 'puid');
        $majorField->setValue($this->majorValue);
        $form->addToForm($majorField->show().'<br>');

        $saveButton=new button('register',$this->lang->languageText('mod_tzschoolacademics_register_label','tzschoolacademics'));
        $saveButton->setToSubmit();
        $form->addToForm($saveButton->showDefault());

       echo $form->show();
    }

     public function getAction(){
        $action=$this->getParam('action',  'edit_class');
        if($action=='edit_class')
            $formAction=  $this->uri(array('action'=> 'edit_class'),'tzschoolacademics');

        else
            $formAction=$this->uri (array('action'=>  'add_class'),'tzschoolacademics');
        return $formAction;
    }
    public  function setValues($valuesArray=NULL){

    }

    public function show(){
        $this->build();
    }
}
?>
