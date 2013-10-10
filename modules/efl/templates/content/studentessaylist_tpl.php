<?php
/**
 * This module is for a simple essay marking tool to be used used as part of the
 * assessment tools.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *

 * @author
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
$schedulejs = '<script language="JavaScript" src="'.$this->getResourceUri('js/arraygrid.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);
$this->appendArrayVar('headerParams', $schedulejs);

$this->loadclass('button', 'htmlelements');

echo "Essay Table Updated Succesfully";

$content = $message;

$content .= '<div id="grid-example"></div>';

//data grid from db
$dbdata=$this->objStudentEssays->getstudentEssays($essayid);
//$dbdata=$this->essays->getEssays($this->objUser->userid());
$essaytitle =$this->essays->getTitle($essayid);

$total=count($dbdata);
$data="";
foreach($dbdata as $row) {
    //$essaytitle=$this->essays->getTitle($essayid);
    $essay['essayid']=$row['essayid'];
    $essaysubmit['submitdate']=$row['submitdate'];
    $data.="[";
    $data.= "'".$essaytitle[0]['title']."',";
    $data.= "'".$row['submitdate']."'";
    $data.="],";

}

/*echo "<pre>"; print_r($dbdata); echo"</pre>";*/

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

$content.= "<script type=\"text/javascript\">".$mainjs."</script>";
$addessayurl= $this->uri(array('action'=>'addessay'));
$addessayjs = 'jQuery(document).ready(function() {
 jQuery("#add-essay").click(function() {

window.location=\''.str_replace('amp;','', $addessayurl).'\';
});
});
';

$backbuttonurl= $this->uri(array('action'=>'home'));

$backbutton = new button('Back','Back');
$backbutton->setId('back-button');
$backbutton->setOnClick($backbuttonurl);

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);


$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$leftSideColumn = $postLoginMenu->show();
$cssLayout->setLeftColumnContent($leftSideColumn);

$rightSideColumn ='<div id="gtx"></div><div style="padding:10px;">';

//Add the table to the centered layer
$rightSideColumn .= $content.$backbutton->show();
$rightSideColumn.= '</div>';
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

echo $cssLayout->show();
?>
