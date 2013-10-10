<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * @author john richard
 * @package tzschoolfees
 * @date 2011-05-19
 */


//class to display all  fee information from database
class fee_display extends object{

    public $objfee;

    //constructor function used to create object for fee infomation class
    
public function  init() {
    $this->objfee = $this->getObject('fee_info', 'tzschoolfees');

    }

    
    //function to load html elements
    public function load_elements(){
        //load html table class
        $this->loadClass('htmlTable','htmlelements');
        //load text input class
        $this->loadClass('textinput', 'htmlelements');
        //load label class
        $this->loadClass('label', 'htmlelements');
        //load button class
        $this->loadClass('button', 'htmlelements');
        //load form class
        $this->loadClass('form', 'htmlelements');


    }
    //function to display all fee information

    function student_reg_number_form(){
        $this->load_elements();
        $htmltable1 = new htmlTable();
        $htmltable1->width='50%';
        $htmltable1->cellpadding='2px';
        $newform = new form();
        $newform->name='Student Regeistration Number';
        $newform->action=$this->uri(array('action' => 'view_details'), 'tzschoolfees');
        $objreg = new textinput('reg_no');
        $objRegLabel = new label('Student Reg #');

        $submit = new button('submit');
        $submit->setToSubmit();
        $submit->value = 'view payment details';

        $htmltable1->startRow();
        $htmltable1->addCell($objRegLabel->show());
        $htmltable1->addCell($objreg->show());
        $htmltable1->endRow();
        
        $newform->addToForm($htmltable1->show());
        $newform->addToForm($submit->show());
        echo $newform->show();



    }
    function get_display_fee($reg_no){
        $this->load_elements();
        //$validate_fee =$this->objfee->validate_payment($reg_no);
        
       // if($validate_fee){
        $objtable = new htmlTable('fee information');
       // $objtable->border =2;
        $feeAray = $this->objfee->get_all_payment($reg_no);

        //table header
       $objtable->startHeaderRow();
       
       $objtable->addHeaderCell('CLASS NAME');
       $objtable->addHeaderCell('AMOUNT PAID');
       $objtable->addHeaderCell('INSTALLMENTS');
       $objtable->addHeaderCell('AMOUNT PAYABLE');
            $objtable->endHeaderRow();
$tz = 'TZsh';
            
        foreach($feeAray as $row){
            //$regno = $row['puid'];
           
            $name = $row['student_fname'].' '.$row['student_mname'];
            $classname = $row['class_name'];
            $amount_paid = $row['amount_paid'];
            $installments = $row['installments'];
            $amount_payable = $row['amount_payable'];

            $objtable->startRow();
            
            $objtable->addCell($classname);
            $objtable->addCell($tz.' '.$amount_paid);
            $objtable->addCell($installments);
            $objtable->addCell($tz.' '.$amount_payable);


        }
        $result_heading='<h2><u><i>STUDENT PAYMENT DETAILS</i></u></h2>';
       $result_heading .="<p><b>Student Name:</b> " . $name . "</p></br>";
                $result_heading .="<p><b>Registration #:</b> " . '' . "</p><br>";
                echo $result_heading.$objtable->show();
        
        




   // }
    }



}

?>
