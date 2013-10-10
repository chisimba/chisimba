<?php

 /**
 * This displays a form for a lecturer to create an essay
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
$this->loadClass('form','htmlelements');
$this->loadClass('label','htmlelements');

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');

$data=array();
$title='';
$action=$this->uri(array('action'=>'saveessay','topicid'=>$topicid));
if($mode == 'edit'){
  $data=$this->essays->getessay($essayid);

  $title=$data['title'];
  $action=$this->uri(array('action'=>'updateessay','essayid'=>$essayid,'topicid'=>$topicid));

}

$objForm = new form('essayform',$action);
$titleField=new textinput('titlefield',$title, NULL, 150);
$multipleSubDropDown=new dropdown('multiSubmit');

$multipleSubDropDown->addOption('1','yes');
$multipleSubDropDown->addOption('0','no');

$activeDropDown=new dropdown('active');

$activeDropDown->addOption('1','draft');
$activeDropDown->addOption('0','not draft');

$table=$this->getObject('htmltable','htmlelements');
$table->cellspacing = '20';

$table->startRow();
$table->addCell('Title', 60, NULL, 'left');
$table->addCell($titleField->show(), 600);
$table->endRow();

$table->startRow();
$table->addCell('Allow multiple submissions?', 60, NULL, 'left');
$table->addCell($multipleSubDropDown->show(), 600);
$table->endRow();

$table->startRow();
$table->addCell('Is this still a draft?', 60, NULL, 'left');
$table->addCell($activeDropDown->show(), 600);
$table->endRow();

$htmlarea = $this->newObject('htmlarea', 'htmlelements');
$htmlarea->setName('pagecontent');
$htmlarea->context = TRUE;
if ($mode == 'add') {
    $htmlarea->setContent('');
} else {
    $htmlarea->setContent($data['content']);
}

$label = new label ('Content', 'input_htmlarea');

$table->startRow();
$table->addCell($label->show());
$table->addCell($htmlarea->show());
$table->endRow();

$button = new button('submitform', 'Save');
$button->setToSubmit();

$table->startRow();
$table->addCell('&nbsp;');
$table->addCell($button->show());
$table->endRow();

$objForm->addToForm($table->show());
$objForm->addRule('titlefield', 'Essay title is required', 'required');

// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent($postLoginMenu->show());
//$rightSideColumn='<h1>'.$coursedata['title'].'</h1>';
$rightSideColumn='<div id="gtx"></div><div style="padding:10px;">';

//Add the table to the centered layer
$rightSideColumn .= $objForm->show();//
$rightSideColumn.= '</div>';
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
echo $cssLayout->show();
?>
