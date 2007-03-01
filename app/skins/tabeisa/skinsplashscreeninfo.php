<?php
/*

This file contains information on additional css, layout changes, etc to implement for the splashscreen
It gets included into showLoginScreen() method of the security controller

All functions, such as creating objects, using setVar, setVarByRef, etc. works
*/


$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('enableTopLinks', TRUE);
$this->setVar('bodyParams', 'class="splashscreen"');

?>
