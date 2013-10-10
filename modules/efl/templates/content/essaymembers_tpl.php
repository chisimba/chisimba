<?php
/* @author
 * @copyright  2009 AVOIR
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/session.css').'"/>';
$schedulejs = '<script language="JavaScript" src="'.$this->getResourceUri('js/essaymembers.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);
$this->appendArrayVar('headerParams', $schedulejs);

//load html elements
$this->loadClass('htmlheading', 'htmlelements');

//page header
$header = new htmlheading( 'List of members currently connected to this essay title', 4);

$content = '<div id="grid-example"></div>';

//get table contents
$user = $this->objUser->getAll();
$firstName = $this->objUser->getfirstName($row['userid']);

$surname = $this->objUser->getsurname($row['userid']);

$data = "";

foreach($user as $row) {
    $data .="[";
    $data.="'".$firstName."',";
    $data.="'".$surname."'";
    $data.="],";
    
}

$lastChar = $data[strlen($data)-1];
$len=strlen($data);
if($lastChar == ',') {
    $data=substr($data, 0, (strlen ($data)) - (strlen (strrchr($data,','))));
}
$submitUrl = $this->uri(array('action' => 'saveschedule'));


$mainjs = "
                Ext.onReady(function(){

                    Ext.QuickTips.init();
                       var data=[$data];
                       //showEssays(data);

                       showMyGrid(data);
                   });
    ";
$content .= "<script type=\"text/javascript\">".$mainjs."</script>";

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$leftSideColumn = $postLoginMenu->show();
$cssLayout->setLeftColumnContent($leftSideColumn);

$rightSideColumn='<div id="gtx"></div><div style="padding:10px;">';

//Add the table to the centered layer
$rightSideColumn .= $header->show().$content;
$rightSideColumn.= '</div>';
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);


echo $cssLayout->show()

?>
