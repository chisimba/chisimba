<?php
/* ----------- data class extends dbTable for tbl_sports------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
/*
* @Author : Nsabagwa Mary
* An interface for adding player data
*/

$content = "";
$playerid = $this->getParam('playerid',NULL);
$infoid = $this->getParam('infoid',NULL);
$this->heading = & $this->getObject('htmlheading','htmlelements');
$this->objDbplayer = & $this->getObject('dbplayer');
$this->loadClass('textarea','htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');
$iconEdit = $this->getObject('geticon','htmlelements');
$this->objDbplayerdata =& $this->getObject('dbplayerdata');
$this->loadClass('hiddeninput','htmlelements');
$this->loadClass('textinput','htmlelements');

//check whether we are editing or adding
	$useEdit=0;
	$useEdit=$this->getParam('useEdit', NULL);
	if($useEdit) {
		$id=0;
		$id=$this->getParam('infoid', NULL);
		
			}

 
//$formaction = $this->uri(array('action'=>'saveplayerdata','sportid'=>$sportid,'playerid'=>$playerid));
$form = new form('playerdata');
$table = new htmltable();
$table->width = "50%";

$playername =  $this->objDbplayer->getPlayerNameById($playerid);

$this->heading->str = $this->objLanguage->languageText('mod_sportsadmin_datafor','sportsadmin')."&nbsp;".$playername;
$content .= $this->heading->show();

$eventdata = new textarea('event');
$eventdata->value =($useEdit?$this->objDbplayerdata->getplayerinfo($infoid):'');
$eventdata->rows = 4;
$eventdata->cols = 30;

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_sportsadmin_enterplayerevent','sportsadmin'));
$table->addCell($eventdata->show());
$table->endRow();


//hidden field to carry the playerid and sportid to the next page
$player_id = new textinput('playerid',$playerid,'hidden');
$sport_id = new textinput('sportid',$sportid,'hidden');

//button
$submitbutton = new button('submit',$this->objLanguage->languageText('word_submit','system'));
$submitbutton->setToSubmit();

$objButton = $this->newObject('button', 'htmlelements');
$objHidden = new hiddeninput('action',($useEdit?'modifyplayerinfo':'saveplayerdata'));

if($useEdit) {
	$objHiddenId = new hiddeninput('id',$infoid);
	}

$backlink =& $this->getObject('link','htmlelements');
$backlink->href = "javascript:history.back()";
$backlink->link = $this->objLanguage->languageText('word_back','system');

$table->startRow();
$table->addCell("");
$table->addCell($submitbutton->show().''.$objHidden->show().''.($useEdit?$objHiddenId->show():''));
$table->endRow();

$table->startRow();
$table->addCell($sport_id->show());
$table->addCell($player_id->show());
$table->endRow();

$form->addToForm($table->show());

$content .= $form->show();

$content .= $backlink->show();
echo $content;
 
?>