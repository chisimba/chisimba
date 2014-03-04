<?php

$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','ext').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','ext').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','ext').'"/>';
$mainjs = '<script language="JavaScript" src="'.$this->getResourceUri('js/main.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $mainjs);

$objIcon= $this->newObject('geticon','htmlelements');

// we create a link for adding the students
$this->loadclass('link','htmlelements');
$addStudentUrl = str_replace("amp;", "", $this->uri(array('action'=>'saveStudent')));

// get the student information data from the database
$getStudentData = $this->objStudents->getStudentData();

$data = "[";
$numRows = count($getStudentData);
$count = 1;

$editName = new link();
$deleteName = new link();

// save the student information in a format that extjs grid will understand.
foreach($getStudentData as $row) {
    // this is the edit icon
    $editName->link("javascript: goEdit(\'".$row['id']."\')");
    $objIcon->setIcon('edit');
    $editName->link=$objIcon->show();

    // this is the delete icon
    $deleteName->link("javascript: goDelete(\'".$this->uri(array('action'=>'deletestudent','id'=>$row['id']))."\')");
    $objIcon->setIcon('delete');
    $deleteName->link=$objIcon->show();

    $data .= "[";
    $data .= "'".$row['firstname']." ".$row['lastname']."', '".$editName->show()."', '".$deleteName->show()."'";
    $data .= "]";

    if($count != $numRows) {
        $data .= ",";
    }
    $count++;
}

$data .= "]";

$mainjs = "/*!
 * Ext JS Library 3.1.1
 * Copyright(c) 2006-2010 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    var typeURL='".$editStudentUrl."',
        data = ".$data.";

    showGrid(typeURL, data);
});";

echo "<div id='buttons'></div><div id='grid-example'></div>";
echo "<script type='text/javascript'>".$mainjs."</script>";
$content .= '<div id="addtype-win" class="x-hidden"><div class="x-window-header"></div></div>';
echo $content;
?>