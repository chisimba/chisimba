<?php
/**
 * Description of reportDbTable_class_inc
 *  The database access class for reports section of the academic module
 * @author fees group, Venance Mushy
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


class Inserter extends dbTable {
    public function init() {
        parent::tbl_classes;
        parent::tbl_fee;
        parent::tbl_payment;
        parent::tbl_status;
        parent::tbl_student2;
        parent::tbl_student_classes;
    }

    //this function inserts student info. eg names, class etc
    public function insert_payment_details() {

        $student=array(
                'firstname'=>  $this->getParam('firstname'),
                'lastname'=>  $this->getParam('lastname'),
                'class'=>  $this->getParam('class'),
                'stream'=>  $this->getParam('stream')
        );





        $fee=array(
                'amount_payable'=>  $this->getParam('amount_payable'),
                'description'=>  $this->getParam('description'),
                'year_fee' => $this->getParam('year_fee'),
        );




        $status=array(
                'statusname'=>  $this->getParam('statusname'),
        );





        $class=array(
                'class_name'=>  $this->getParam('class_name'),
                'class_stream'=>  $this->getParam('class_stream'),
                'tbl_fee_id' => null
        );





        $student_class=array(
                'tbl_student_id'=>null,
                'tbl_class_id'=> null,
        );





        $payment=array(
                'amount_paid' => $this->getParam('amount_paid'),
                'receipt_no'=>  $this->getParam('receipt_no'),
                'bank_name'=>  $this->getParam('bank_name'),
                'bank_branch'=>  $this->getParam('branch_name'),
                'date_paid'=>  $this->getParam('date_paid'),
                'installments'=>  $this->getParam('installment'),
                'tbl_student_id'=>null,
                'tbl_student_class' => null,
                'tbl_fee_id'=>null,
                'tbl_status_id'=>null
        );

//tbl_student
        $this->_tableName='tbl_student';
        $student_id = $this->insert($student);

        $student['tbl_student_id'] = $student_id;

//tbl_fee
        $this->_tableName='tbl_fee';
        $fee_id = $this->insert($fee);

        $class['tbl_fee_id'] = $fee_id;
        $payment['tbl_fee_id'] = $fee_id;

//tbl_status
        $this->_tableName='tbl_status';
        $status_id = $this->insert($status);

        $status['tbl_status_id'] = $status_id;

 //classes
        $this->_tableName='tbl_classes';
        $class_id = $this->insert($class);

        $class['tbl_class_id'] = $class_id;
        $student_class['tbl_class_id'] = $class_id;

 //tbl_student_class
        $this->_tableName='tbl_student_class';
        $student_class_id = $this->insert($student_class);

        $payment['tbl_student_class_id'] = $student_class_id;


//tbl_payment
        $this->_tableName='tbl_payment';
        $this->insert($payment);



    }


}
?>
