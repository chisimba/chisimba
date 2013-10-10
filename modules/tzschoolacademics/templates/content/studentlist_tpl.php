<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$studentTable=$this->getObject('studentlist');
$studentTable->build($data);
$studentTable->show();
?>
