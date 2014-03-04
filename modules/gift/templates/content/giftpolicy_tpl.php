<?php

//load class
$this->loadclass('link','htmlelements');


$backbutton = new button('back', "Back");
$uri = $this->uri(array('action' => 'back'));
$backbutton->setOnClick('javascript: window.location=\'' . $uri . '\'');


$acceptbutton = new button('accept', "I understand the Gift Policy");
$uri = $this->uri(array('action' => 'acceptpolicy'));
$acceptbutton->setOnClick('javascript: window.location=\'' . $uri . '\'');



$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$policyURL=$objSysConfig->getValue('GIFT_POLICY_URL', 'gift');




$policy='
    <strong>This is the first time you are adding a gift. Please review the gift policy before proceeding.</strong><br/>
<a href="'.$policyURL.'">Click here to view the policy</a>';



// Add the table to the centered layer and a message of database functionality
echo '
    <fieldset><legend>Review Gift Policy</legend>'. $policy.'<div id="grouping-grid"><br /></div>'.$backbutton->show().'&nbsp;&nbsp;'.$acceptbutton->show().'</fieldset>';


?>
