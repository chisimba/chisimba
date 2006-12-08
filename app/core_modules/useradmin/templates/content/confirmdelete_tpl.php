<?php
    $userId=$this->getParam('userId');
    
    $objLanguage=& $this->getObject('language', 'language');

    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='';
    $objTblclass->attributes=" align='center' border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';
    
    $yeslink=$this->uri(array('module'=>'useradmin','action'=>'delete','confirm'=>'yes','userId'=>$userId));
    $nolink=$this->uri(array('module'=>'useradmin','confirm'=>'no','userId'=>$userId));

    $yesbutton=$objLanguage->LanguageText("word_yes");
    $nobutton=$objLanguage->LanguageText("word_no");

    $yesfield="<form name='useryes' action='$yeslink' method='POST'>\n<input type=submit name='userconfirm' class='button' value='$yesbutton'>\n</form>\n";
    $nofield="<form name='userno' action='$nolink' method='POST'>\n<input type=submit name='userconfirm' class='button' value='$nobutton'>\n</form>\n";
    
    $checktext=$objLanguage->languageText('delete_user_confirm','useradmin');
    $checktext=str_replace('{USER}',$this->userdata['firstName']." ".$this->userdata['surname'],$checktext);
    
    $objTblclass->startRow();
    $objTblclass->addCell('<h1>'.$objLanguage->languageText("phrase_confirmdeletion").'</h1>', "", NULL, 'center', NULL, 'colspan=2');
    $objTblclass->endRow();

    $objTblclass->startRow();
    $objTblclass->addCell($checktext, "", NULL, 'center', 'odd', 'colspan=2');
    $objTblclass->endRow();


    $objTblclass->startRow();
    $objTblclass->addCell($yesfield, "", NULL, 'right', 'button', 'colspan=1');
    $objTblclass->addCell($nofield, "", NULL, 'left', 'button', 'colspan=1');
    $objTblclass->endRow();

    print "<div>\n";
    print $objTblclass->show();
    print "</div>\n";
?>

