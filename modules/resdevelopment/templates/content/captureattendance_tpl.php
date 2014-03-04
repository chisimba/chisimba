<?php

$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','ext').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','ext').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','ext').'"/>';
$attendancejs = '<script language="JavaScript" src="'.$this->getResourceUri('js/attendance.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $attendancejs);

$objIcon= $this->newObject('geticon','htmlelements');

// we create a link for adding the attendance
$this->loadclass('link','htmlelements');
$captureAttendanceUrl = str_replace("amp;", "", $this->uri(array('action'=>'saveattendance')));
$editAttendanceUrl = str_replace("amp;", "", $this->uri(array('action'=>'editattendance')));

// get the attendance information data from the database
$getAttendanceData = $this->objAttendance->getAttendanceData();

$data = "[";
$numRows = count($getAttendanceData);
$count = 1;

$editAttendance = new link();
$deleteAttendance = new link();

// save the attendance information in a format that extjs grid will understand.
foreach($getAttendanceData as $row) {

    // this is the edit icon
    $editAttendance->link("javascript: goEdit(\'".$editAttendanceUrl."\',\'".$row['id']."\',\'".$row['attendance']."\')");
    $objIcon->setIcon('edit');
    $editAttendance->link=$objIcon->show();

    // this is the delete icon
    $deleteAttendance->link("javascript: goDelete(\'".$this->uri(array('action'=>'deleteattendance','id'=>$row['id']))."\')");
    $objIcon->setIcon('delete');
    $deleteAttendance->link=$objIcon->show();

    $data .= "[";
    $data .= "' ".$row['attendance']."', '".$editAttendance->show()."', '".$deleteAttendance->show()."'";
    $data .= "]";

    if($count != $numRows) {
        $data .= ",";
    }
    $count++;
}

$data .= "]";

$attendancejs = "/*!
 * Ext JS Library 3.1.1
 * Copyright(c) 2006-2010 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    var typeURL='".$captureAttendanceUrl."',
        data = ".$data.";

    showGrid(typeURL, data);
});";

echo "<div id='buttons'></div><div id='grid-example'></div>";
echo "<script type='text/javascript'>".$attendancejs."</script>";
$content .= '<div id="addtype-win" class="x-hidden"><div class="x-window-header"></div></div>';
echo $content;


?>