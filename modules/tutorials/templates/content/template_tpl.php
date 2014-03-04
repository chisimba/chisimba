<?php
/* -------------------- template for tutorials module ----------------*/

// security check-must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

/**
* @package tutorials
*/

/**
* Displays for the tutorials module
* @author Kevin Cyster
* */

if(!isset($answer)){
    $this->setLayoutTemplate('layout_tpl.php');
}else{
    $this->setVar('pageSuppressToolbar', TRUE);
}

echo $templateContent;
?>