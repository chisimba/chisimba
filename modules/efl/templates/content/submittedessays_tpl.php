<?php


/**
 * This class displays a  an ext js based form listing the essays accessible
 * by the current user
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
$list = '<script language="JavaScript" src="'.$this->getResourceUri('js/submittedessaylist.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);
$this->appendArrayVar('headerParams', $list);

//where we render the 'popup' window
$renderSurface='';
$titleid=$this->getparam('essayid');
$essaytitle = $this->essays->getTitle($titleid);
$this->loadClass('htmlheading', 'htmlelements');

//page header
$essayTitle = new htmlheading($essaytitle[0]['title'], 4);
$essayText='
          <p>
          <b>
          Listing of submitted essays
          </b>
          </p>
         ';
//load class
$this->loadclass('link','htmlelements');
$objIcon= $this->newObject('geticon','htmlelements');

$listButton = new button('add','Back to essay list');
$listButton->setId('list-essay');

$btns='';

$btns.=$listButton->show().'&nbsp;&nbsp;';

$content = $message;
$content= '<div id="grouping-grid">'.$essayTitle->show().$essayText.$btns.'<br /><br /></div>';

$essayId = $this->getParam('essayid');
//data grid from db
if($this->objUser->isAdmin()) {
    $dbdata=$this->essays->getSubmittedEssays($essayId);
}
else {
    $dbdata=$this->essays->getSubmittedEssays($essayId, $this->objUser->userId());
}
$total=count($dbdata);
/*$objAltConfig = $this->getObject('altconfig','config');
$modPath=$objAltConfig->getModulePath();
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$modPath);
$codebase="http://" . $_SERVER['HTTP_HOST'].'/'.$resourcePath.'/efl/resources/';*/

$count = 1;
$numRows = count($dbdata);
$titleid=$this->getparam('essayid');
$essaytitle = $this->essays->getTitle($titleid);

foreach($dbdata as $row) {
    $essaydata=$this->essays->getTitle($row['essayid']);
    $detailsLink=new link($this->uri(array('action'=>'markessay','essayid'=>$row['essayid'])));
    $detailsLink->link=$row['userid'];

    $data.="[";
    //$data.="'<a href=\"".$codebase."jefla.jnlp\">".$row['userid']."</a>',";
    $data.="'". $detailsLink->show()."',";
    $data.="'".$row['userid']."',";
    $data.="'".$row['submitdate']."']";
    if($count < $numRows) {
        $data.=",";
    }

    $count++;

}

$lastChar = $data[strlen($data)-1];
$len=strlen($data);
if($lastChar == ',') {
    $data=substr($data, 0, (strlen ($data)) - (strlen (strrchr($data,','))));
}
$submitUrl = $this->uri(array('action' => 'saveschedule'));

$title='Title';
$dateCreated='Date Created';
$details='Details';

$owner='Owner';
$edit='Edit';


$mainjs = "/*!realtime
                 * Ext JS Library 3.0.0
                 * Copyright(c) 2006-2009 Ext JS, LLC
                 * licensing@extjs.com
                 * http://www.extjs.com/license
                 */
                Ext.onReady(function(){

                    Ext.QuickTips.init();
                       var data=[$data];
                       showSubmittedEssays(data);
                   });
    ";

$content.= "<script type=\"text/javascript\">".$mainjs."</script>";
$listessayurl= $this->uri(array('action'=>'home'));
$listessayjs = 'jQuery(document).ready(function() {
 jQuery("#list-essay").click(function() {

window.location=\''.str_replace('amp;','', $listessayurl).'\';
});
});
';
$addessay.= "<script type=\"text/javascript\">".$listessayjs."</script>";


// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$objEditForm = $this->getObject('editform', 'efl');
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$leftSideColumn = $postLoginMenu->show();
$cssLayout->setLeftColumnContent($leftSideColumn);

$rightSideColumn='<div id="gtx"></div><div style="padding:10px;">';

//Add the table to the centered layer
$rightSideColumn .=$addessay.$content;
$rightSideColumn.= '</div>';
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
echo $cssLayout->show()
?>
