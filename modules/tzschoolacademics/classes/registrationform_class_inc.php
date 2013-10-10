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
abstract class registrationform extends object {
    public $lang;
    
    public function init(){
       $this->lang=$this->getObject('language', 'language');
      // $this->load();
    }

    public function load(){
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('datepicker', 'htmlelements');
    }

    abstract function build();

    public function getAction(){
        $action=$this->getParam('action',  'edit');
        if($action=='edit')
            $formAction=  $this->uri(array('action'=>  $this->getEditAction()),'tzschoolacademics');

        else
            $formAction=$this->uri (array('action'=>  $this->getAddAction()),'tzschoolacademics');
        return $formAction;
    }

    abstract function setValues($valuesArray=NULL);
    abstract function getEditAction();
    abstract function getAddAction();
    public function show(){
        $this->build();
    }
}
?>
