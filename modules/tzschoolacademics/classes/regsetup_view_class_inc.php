<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Create the components for setting registration of different entities in the system
 *
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */
class regsetup_view extends object{
    private $lang;
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
        $this->loadClass('htmltable', 'htmlelements');
    }

    private function build(){
        $this->load();
         


        echo $this->getSubjectTeacherForm('subjects','subject_name','teacher','empl_id')->show().
        $this->getSubjectTeacherForm('class', 'class_name', 'teacher', 'empl_id')->show();
    }

    public function show(){
          
        $this->build();
    }
    private function getSubjectTeacherForm($fname,$fdropdownValueName,$sname,$sdropdownValueName){
        $table=new htmlTable();

        $stform=new form($fname.' '.$sname,  $this->uri(array('action'=>'set_'.$fname.'_'+$sname),'tzschoolacademics'));
        $topLabel=new label('<h2>'.$fname.' '.$sname.'</h2><br/>');
        $stform->addToForm($topLabel->show());

        $subjectLabel=new label($this->lang->languageText('mod_tzschoolacademics_'.$fname.'_label','tzschoolacademics'),$fname);
        $subjectField=new dropdown($fname);
        $this->registrar->_tableName='tbl_'.$fname;
        $subjectField->addFromDB($this->registrar->getAll(),$fdropdownValueName, 'puid');

        $teacherLabel=new label($this->lang->languageText('mod_tzschoolacademics_'.$sname.'_label','tzschoolacademics'),'teacher');
        $teacherField=new dropdown($sname);
        $this->registrar->_tableName='tbl_'.$sname;
        $teacherField->addFromDB($this->registrar->getAll(), $sdropdownValueName, 'puid');

        $saveSTbutton=new button('save',$this->lang->languageText('mod_tzschoolacademics_save_label','tzschoolacademics'));
        $saveSTbutton->setToSubmit();

        $table->startRow();
        $table->addCell($subjectLabel->show());
        $table->addCell($teacherLabel->show());
        $table->endRow();

        $table->startRow();
        $table->addCell($subjectField->show());
        $table->addCell($teacherField->show());
        $table->addCell($saveSTbutton->show());
        $table->endRow();

        $stform->addToForm($table->show());

        return $stform;
    }
}
?>
