<?php
/*
welcome_tlp.php
*
*
@Author: Emmanuel Natalis
@Open Source Software developer
@University of dare es salaam
*
*
A file which dispalys the welcome message to the user
$greet variable is already defined in the controller class
*/
$this->objEnterdate=$this->getObject('enterdate','happybirthday');
$this->objEnterdate->display_main_menu($greet);
/*
*This is an object of a block module
*/


?> 