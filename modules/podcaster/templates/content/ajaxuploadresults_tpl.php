<?php
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$buttonNote = $this->objLanguage->languageText('mod_podcaster_clicknexttwolink', 'podcaster', 'Click on the "Next step" link to describe the podcast');

$buttonLabel = $this->objLanguage->languageText('word_next', 'system', 'System')." ".$this->objLanguage->languageText('mod_podcaster_wordstep', 'podcaster', 'Step');

$hasUploaded = $this->objLanguage->languageText('mod_podcaster_hasuploaded', 'podcaster', 'has been uploaded');

$nextStep = $this->objLanguage->languageText('mod_podcaster_nextstep', 'podcaster', 'Next Step');
$nextStepBold = "<b>".$nextStep."</b>";
$descProdLink = new link($this->uri(array(
    'module' => 'podcaster',
    'action' => 'describepodcast',
    'fileid' => $fileid,
    )));
$descProdLink->link = $nextStepBold;
$linkDescribe = $descProdLink->show()." ".$buttonNote;

$this->setVar('pageSuppressXML', TRUE);

$this->appendArrayVar('bodyOnLoad', '

var par = window.parent.document;
window.history.forward(1);

par.forms[\'uploadfile_'.$id.'\'].reset();
par.getElementById(\'form_upload_'.$id.'\').style.display=\'block\';
par.getElementById(\'uploadresults\').style.display=\'block\';
par.getElementById(\'uploadresults\').innerHTML = \'<span class="confirm">'.addslashes(htmlentities($filename)).' '.$hasUploaded.'</span><br /><br /> '.$linkDescribe.' \';
par.getElementById(\'div_upload_'.$id.'\').style.display=\'none\';

window.location = "'.str_replace('&amp;', '&', $this->uri(array('action'=>'tempiframe', 'id'=>$id))).'";
');


?>