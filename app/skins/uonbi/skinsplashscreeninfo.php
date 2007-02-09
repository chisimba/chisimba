<?php
/*

This file contains information on additional css, layout changes, etc to implement for the splashscreen
It gets included into showLoginScreen() method of the security controller

All functions, such as creating objects, using setVar, setVarByRef, etc. works
*/

$cssLayout =& $this->newObject('csslayout','htmlelements');

// Load the three column javascript fix into the head.
$cssLayout->putThreeColumnFixInHeader();
?>
