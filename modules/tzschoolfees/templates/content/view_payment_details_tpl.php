<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor
 *
 *
*
 * @Author john richard
 * @Copyright (c) UCC
 * @Version 1.0
 * @Package smis fee module
 */
$details = $this->getObject('fee_display');


if(strcmp($option,'view')==0)
                {
echo $details->get_display_fee($regno);
}

else{


echo $details->student_reg_number_form();
//echo $details->get_display_fee();
}

?>