<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


//Returns only the HTML form's. 
//Meant for response to AJAX calls

$id = $this->getParam('id', '');
$type = $this->getParam('type', '');
switch($type){
    case 'classform':
		$moduleName = $this->getParam('modulename', '');
        echo $this->objUi->getClassListForm($moduleName);
    break;

    default:
        
    break;
}

?>
