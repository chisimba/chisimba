<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of studentlist_class_inc
 *
 * @author Boniface Chacha <bonifacechacha@gmail.com>
 */
class studentlist extends object{

    private  $lang;
    private  $studentTable;

    public function init(){

        $this->lang=$this->getObject('language', 'language');
        $this->studentTable=$this->getObject('htmltable','htmlelements');

    }

    private  function load(){

    }

    private  function setHeader(){
        $this->studentTable->startHeaderRow();
        $this->studentTable->addHeaderCell('Registration Number');
        $this->studentTable->addHeaderCell('Firstname');
        $this->studentTable->addHeaderCell('Lastname');
        $this->studentTable->addHeaderCell('Othernames');
        $this->studentTable->addHeaderCell('Gender');
        $this->studentTable->addHeaderCell('Birthdate');
        $this->studentTable->addHeaderCell('Religion');
        $this->studentTable->endHeaderRow();
    }

    public  function build($data=NULL){
        $this->load();
        $this->setHeader();

        foreach ($data as $value){

        $nameLink=$this->getObject('link', 'htmlelements');
        $nameLink->link($this->uri(array('action'=>'edit','id'=>$value['id'])));
        $nameLink->link=$value['reg_number'];

        $this->studentTable->startRow();
        $this->studentTable->addCell($nameLink->show());

        $this->studentTable->addCell($value['firstname']);

        $this->studentTable->addCell($value['lastname']);
        $this->studentTable->addCell($value['othernames']);
   //     $this->studentTable->addCell($delMark->show());
        $this->studentTable->addCell($value['gender']);
        $this->studentTable->addCell($value['birthdate']);
        $this->studentTable->addCell($value['religion']);
        $this->studentTable->endRow();

        }
    }

    public function show(){
        echo $this->studentTable->show();


    }

}
?>
