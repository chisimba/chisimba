<?php
/**
 * Description of fee_info_class_inc
 *  The database access class for reports section of the academic module
 * @author john richard
 * @package tzschoolfees
 * @date 2011-050-19
 */

//class to interct with dadtabses
class fee_info extends dbTable{

public $tableName;
//init ethod for da class fee info
    public function  init() {
parent::init('tbl_fee');
parent::init('tbl_payment');
parent::init('tbl_student2');
parent::init('tbl_status');
parent::init('tbl_student_classes');

}


/**
 * function to get fee status
 * and then return student fee whenever there z data otherwise it returns nothing
 */

function get_fee(){
    $this->_tableName = 'tbl_fee';
    $std_fee = $this->getAll();
    if($std_fee){
        return $std_fee;
}

else{

    return FALSE;
}

}//end of function to get fee ststus

/**
 * function to get all payment details and then return all result to the
 * 
 */

function get_all_payment($regno){
    $sql = "SELECT student_fname,student_mname,class_name,amount_paid,installments,amount_payable
         FROM tbl_student2,tbl_student_classes,tbl_fee,tbl_payment,tbl_classes
         WHERE tbl_student_classes.tbl_student_id='$regno' AND
               tbl_payment.tbl_fee_id = tbl_fee.puid AND
               tbl_student_classes.puid=tbl_payment.tbl_student_classes_id AND
               tbl_student2.puid=tbl_student_classes.tbl_student_id AND
               tbl_classes.puid=tbl_student_classes.tbl_class_id";

    

    $payment_details = $this->query($sql);

    if($payment_details){

        return $payment_details;


    }
else{

        return FALSE;
}


//function to validate student registration number
function validate_payment($reg_no) {
        $this->_tableName = 'tbl_student_classes';
        $filter = "Where tbl_student_id = '$reg_no'";
        $result = $this->getRecordCount($filter = $filter);
        if ($result == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


}


}//end of class fee info







?>
