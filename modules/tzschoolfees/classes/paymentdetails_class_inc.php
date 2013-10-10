<?php
/* 
 * @author john richard
 * @package:smis fee module
 * @date 2011 05 06
 * and open the template in the editor.
*/
//class which shows student payment details
class paymentdetails extends Object {
    public $lang;
    public $validate;

    function  init() {

        $this->lang=$this->getObject('language','language');
        $this->validate = $this->getObject('validator','htmlelements');

    }
    //function to load clases
    function loadElements() {
        $this->loadClass('Validator', 'htmlelements');
        $this->loadClass('htmlTable','htmlelements');
        $this->loadClass('form','htmlelements');
        $this->loadClass('textinput','htmlelements');
        $this->loadClass('label','htmlelements');
        $this->loadClass('textarea','htmlelements');
        $this->loadClass('button','htmlelements');
        $this->loadClass('datepicker','htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('fieldset','htmlelements');
        
    }
//function for building a form
    function buildForm() {
//call function loadElemnts
        $this->loadElements();

//creating new objects

        $objform = new form('payment',  $this->getAction());
        $objform->method='post';
        

        $formlabel = new label('<h2>STUDENT PAYMENT DETAILS</h2><br>');

        $objform->addToForm($formlabel->show());

        $validate = $this->getObject('Validator', 'htmlelements');
//student full name

        $fname=new label('First name');
        $fnamefield = new textinput('fname');
     
        
         $lname=new label('Surname');
         $lnamefield = new textinput('middlename');

         $mname=new label('Other');
         $mnamefield = new textinput('mname');
        $namefield =


//student class
        $classlabel=new label('Class name');
        $classfield = new dropdown('class_name');
        $classfield->addOption('f1','FORM ONE');
        $classfield->addOption('f2','FORM TWO');
        $classfield->addOption('f3','FORM THREE');
        $classfield->addOption('f4','FORM FOUR');
        $classfield->addOption('f5','FORM FIVE');
        $classfield->addOption('f6','FORM SIX');

//student stream
        $streamlabel=new label('Stream: ');
        $streamfield = new dropdown('class_stream');
        $streamfield->addOption('a','A');
        $streamfield->addOption('b','B');
        $streamfield->addOption('c','C');
        $streamfield->addOption('d','D');
        $streamfield->addOption('e','E');
        $streamfield->addOption('f','F');

//Bank statement
        $banklabel = new label('Bank name: ');
        $bankfield = new dropdown('bank_name');
        $bankfield->addOption('crdb','CRDB');
        $bankfield->addOption('nmb','NMB');
        $bankfield->addOption('nbc', 'NBC');
        $bankfield->addOption('exim','EXIM BANK');

        $branchlabel = new label('Branch');
        $branchfield = new textinput('branch_name');

        $receiptLabel = new label('Receipt no#');
        $receiptfield = new textinput('receipt_no');



//amount paid by student
        $amountlabel=new label('Amount payable');
        $amountfield = new textinput('amount_paid');

 //desc.
         $descriptionlabel=new label('Description');
        $descriptionfield = new textinput('description');

 //Date when payment were done
        $datelabel = new label('Date ');
        $datepiki = $this->getObject('datepicker', 'htmlelements');
        $datepiki->setName('date_paid');

//installments by student
        $installmntlabel=new label('Installment');


        $installmentfield = new dropdown('installment');
        $installmentfield->addOption('full','ANNUAL');
        $installmentfield->addOption('install','TERM');


//complete payment
        $paymentlabel=new label('Amount');
        $paymentfield = new textinput('amount');



//amount payable by student
        $payablelabel=new label('Amount');
        $payablefield = new textinput('amount_paid');
      

        $saveButton=new button('register');
        $saveButton->setToSubmit();
        $saveButton->value='Save details';


        $objTable1 = new htmlTable();
        
  //first row
        $objTable1->startRow();
        
        $objTable1->addCell($fname->show().':');
        $objTable1->addCell($fnamefield->show());

        $objTable1->addCell($lname->show().':');
        $objTable1->addCell($lnamefield->show());

        $objTable1->endRow();
        
//second row
        $objTable1->startRow();

        $objTable1->addCell($classlabel->show().':');
        $objTable1->addCell($classfield->show());

        $objTable1->addCell($streamlabel->show().':');
        $objTable1->addCell($streamfield->show());


        $objTable1->endRow();
        
        $objfield1 = new fieldset();
        $objfield1->width=900;
        $objfield1->align='center';
        $objfield1->setLegend('STUDENT INFORMATION');

        $objfield1->addContent($objTable1->show());
        $objform->addToForm($objfield1->show());

        
//third row
        $objTable2 = new htmlTable();

        $objTable2->startRow();

        $objTable2->addCell($banklabel->show().':');
        $objTable2->addCell($bankfield->show());

        $objTable2->addCell($branchlabel->show().':');
        $objTable2->addCell($branchfield->show());

        
        $objTable2->endRow();


//fourth row
        $objTable2->startRow();

        $objTable2->addCell($paymentlabel->show().':');
        $objTable2->addCell($paymentfield->show());

        $objTable2->addCell($installmntlabel->show().':');
        $objTable2->addCell($installmentfield->show());
        
        $objTable2->endRow();

//fifth row
        $objTable2->startRow();

        $objTable2->addCell($amountlabel->show().':');
        $objTable2->addCell($amountfield->show());

        $objTable2->addCell($descriptionlabel->show().':');
        $objTable2->addCell($descriptionfield->show());

        $objTable2->endRow();

        $objTable2->startRow();

        $objTable2->addCell($receiptLabel->show());
        $objTable2->addCell($receiptfield->show());

        $objTable2->addCell($datelabel->show().':');
        $objTable2->addCell($datepiki->show());

       $objTable2->endRow();

        $objfield2 = new fieldset();
        $objfield2->width=900;
        $objfield2->align='center';
        $objfield2->setLegend('BANK DETAILS');

        $objfield2->addContent($objTable2->show());
        $objform->addToForm($objfield2->show());

//sixth row
        $objTable= new htmlTable();

//seventh row
        $objTable->startRow();

        $objTable->addCell('');
        $objTable->addCell('');

        $objTable->addCell('');
        $objTable->addCell('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$saveButton->show());
        
        $objTable->endRow();

        $objform->addToForm($objTable->show());
        
        $objfield = new fieldset();
        $objfield->width=900;
        $objfield->align='center';

        $objfield->addContent($objform->show());


        echo $objfield->show();

    }
    function getAction() {
        $action=$this->getParam('action','edit');
        if($action=='edit')
            $formAction=  $this->uri(array('action'=>'edit'),'tzschoolfees');

        else
            $formAction=$this->uri (array('action'=>'add'),'tzschoolfees');
        return $formAction;
    }

    public function checkForm() {
        $this->loadElements();

        $objformcheck = new form('checker');
        $objformcheck->action = $this->uri(array('action' => 'add'), 'tzschoolfees');
        $objformcheck->method='post';
        $objformcheck->setDisplayType(2);

        $formcheckLabel = new label('<h4>Enter registration number to check if student is in the system:</h4>');
        $objformcheck->addToForm($formcheckLabel->show());

        $objFormInput = new textinput('regno');
        $objFormInput->size = 7;
        $objCheckButton = new button('Check');
        $objCheckButton->setToSubmit();
        $objCheckButton->value='CHECK';

        $objTableCheck = new htmlTable();
        $objTableCheck->width = 100;
        $objTableCheck->border = 1;
        $objTableCheck->cellpadding = 1;

        $objTableCheck->startRow();

        $objTableCheck->addCell($objFormInput->show());
        $objTableCheck->addCell($objCheckButton->show());

        $objTableCheck->endRow();

        $objformcheck->addToForm($objTableCheck->show());

        echo $objformcheck->show();
    }

    public function setValues($valuesArray=NULL) {

    }
    function showAddForm() {

        return $this->buildForm();

    }

    function showCheckForm() {

        return $this->checkForm();

    }


}



?>
