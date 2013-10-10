<?php

$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','ext').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','ext').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','ext').'"/>';
$groupjs = '<script language="JavaScript" src="'.$this->getResourceUri('js/group.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $groupjs);

$objIcon= $this->newObject('geticon','htmlelements');

// we create a link for adding the Groups
$this->loadclass('link','htmlelements');
$addGroupUrl = str_replace("amp;", "", $this->uri(array('action'=>'savegroup')));
$editGroupUrl = str_replace("amp;", "", $this->uri(array('action'=>'editgroup')));

// get the group information data from the database
$getGroupData = $this->objGroup->getGroupData();

$data = "[";
$numRows = count($getGroupData);
$count = 1;

$editGroup = new link();
$deleteGroup = new link();

// save the Group information in a format that extjs grid will understand.
foreach($getGroupData as $row) {
    // this is the edit icon
    $editGroup->link("javascript: goEdit(\'".$editGroupUrl."\',\'".$row['id']."\',\'".$row['groupname']."\')");
    $objIcon->setIcon('edit');
    $editGroup->link=$objIcon->show();

    // this is the delete icon
    $deleteGroup->link("javascript: goDelete(\'".$this->uri(array('action'=>'deletegroup','id'=>$row['id']))."\')");
    $objIcon->setIcon('delete');
    $deleteGroup->link=$objIcon->show();

    $data .= "[";
    $data .= "'".$row['groupname']."', '".$editGroup->show()."', '".$deleteGroup->show()."','".$row['date_created']."'";
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
    var typeURL='".$addGroupUrl."',
        data = ".$data.";

    showGrid(typeURL, data);
});";

echo "<div id='buttons'></div><div id='grid-example'></div>";
echo "<script type='text/javascript'>".$mainjs."</script>";
$content .= '<div id="addtype-win" class="x-hidden"><div class="x-window-header"></div></div>';
echo $content;
?>