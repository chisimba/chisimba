<?php
$this->setVar('pageSuppressXML', TRUE);
$objLanguage=$this->getObject("language","language");
$hasBeenUploaded=$objLanguage->languageText("mod_oer_hasbeenuploaded","oer");
$this->appendArrayVar('bodyOnLoad', '

var par = window.parent.document;
window.history.forward(1);

par.forms[\'uploadfile_'.$id.'\'].reset();
par.getElementById(\'form_upload_'.$id.'\').style.display=\'block\';
par.getElementById(\'uploadresults\').style.display=\'block\';
par.getElementById(\'uploadresults\').innerHTML = \'<span class="confirm">'.addslashes(htmlentities($filename)).' '.$hasBeenUploaded.'</span><br />\';
par.getElementById(\'div_upload_'.$id.'\').style.display=\'none\';
window.location = "'.str_replace('&amp;', '&', $this->uri(array('action'=>'tempiframe', 'id'=>$id))).'";
    

');


?>