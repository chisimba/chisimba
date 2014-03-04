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
$schedulejs = '<script language="JavaScript" src="'.$this->getResourceUri('js/essaylist.js').'" type="text/javascript"></script>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);
$this->appendArrayVar('headerParams', $schedulejs);

//where we render the 'popup' window
$renderSurface='';
$essayTitle='<h2>Essays</h2>';
if($this->objUser->isAdmin()){
$essayTitle.='
          <p>Here you will find a listing of essays owned by you or of
          which you are a member.<br/>

         Select one to view the essays. You can start your own essay by clicking on the
         <font color="green"><b>Add Essay</b></font> button.
         </p>
         ';
}
else{
    $essayTitle.='
          <p>Here you will find a listing of essays owned by you or of
          which you are a member. Select one to view the essays.</p>';
    }
//load class
$this->loadclass('link','htmlelements');
$objIcon= $this->newObject('geticon','htmlelements');

$addButton = new button('add','Add essay');
$addButton->setId('add-essay');

$btns='';
if($this->objUser->isAdmin()) {
    $btns.=$addButton->show().'&nbsp;&nbsp;';
}
$content = $message;
$content= '<div id="grouping-grid">'.$essayTitle.$btns.'<br /><br /></div>';

//data grid from db
$dbdata=$this->essays->getEssays($this->objUser->userid());

$objAltConfig = $this->getObject('altconfig','config');
$modPath=$objAltConfig->getModulePath();
$replacewith="";
$docRoot=$_SERVER['DOCUMENT_ROOT'];
$resourcePath=str_replace($docRoot,$replacewith,$modPath);
$codebase="http://" . $_SERVER['HTTP_HOST'].'/'.$resourcePath.'/efl/resources/';

$total=count($dbdata);
$data="";
foreach($dbdata as $row) {
    
    $essaydata['title']=$row['title'];
    
    $deleteLink=new link($this->uri(array('action'=>'deleteessay','essayid'=>$row['id'])));
    $objIcon->setIcon('delete');
    $delValJS="deleteessay(\'".$row['essayid']."\');return false;";
    $objIcon->extra = 'onClick="'.$delValJS.'"';
    $deleteLink->link=$objIcon->show();

    $objIcon= $this->newObject('geticon','htmlelements');
    $editLink=new link($this->uri(array('action'=>'editessay','essayid'=>$row['id'])));
    $objIcon->setIcon('edit');
    $editLink->link=$objIcon->show();

    $detailsLink=new link($this->uri(array('action'=>'essaymembers','essayid'=>$row['id'])));
    $detailsLink->link='Members';

    $previewLink=new link($this->uri(array('action'=>'previewessay','storyid'=>$row['id'])));
    $previewLink->link='Preview';

    $titleLink=new link($this->uri(array('action'=>'viewessayasstudent','essayid'=>$row['id'])));
    $titleLink->link=addslashes($essaydata['title']);

    $membersLink="";
    $deleteTxt="";
    $editTxt = '';
    if($this->objUser->isAdmin()) {
        $membersLink=$detailsLink->show();
        $deleteTxt=$deleteLink->show();
        $editTxt = $editLink->show();
        $titleLink=new link($this->uri(array('action'=>'viewsubmittedessays','essayid'=>$row['id'])));
        $titleLink->link=addslashes($essaydata['title']);

    }
    $data.="[";
    $data.= "'<a href=\"".$codebase."jefla.jnlp\">".$titleLink->show()."',"; //$data.="'<a href=\"".$codebase."jefla.jnlp\">".$row['userid']."</a>',";
    $data.="'".$membersLink."',";
    $data.="'".$previewLink->show()."',";
    $data.="'".$editTxt.$deleteTxt."'";
    $data.="],";

}

/*echo "<pre>"; print_r($dbdata); echo"</pre>";*/

$lastChar = $data[strlen($data)-1];
$len=strlen($data);
if($lastChar == ',') {
    $data=substr($data, 0, (strlen ($data)) - (strlen (strrchr($data,','))));
}
$submitUrl = $this->uri(array('action' => 'saveschedule'));

//$title='Title';
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
                       showEssays(data);
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
$addessay.= "<script type=\"text/javascript\">".$addessayjs."</script>";


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
