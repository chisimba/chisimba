<?php

echo '<pre>';
print_r($_GET);


$this->appendArrayVar('bodyOnLoad', '

var par = window.parent.document;

par.forms[\'uploadfile_'.$id.'\'].reset();
par.getElementById(\'form_upload_'.$id.'\').style.display=\'block\';
par.getElementById(\'uploadresults\').style.display=\'block\';
par.getElementById(\'uploadresults\').innerHTML = \'<span class="error">Error: '.addslashes(htmlentities($message)).'</span><br />\';
par.getElementById(\'div_upload_'.$id.'\').style.display=\'none\';



');

?>