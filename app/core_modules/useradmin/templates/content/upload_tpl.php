<?php
    $objImage=$this->getObject('imageupload');
    
    $upload="<form name='fileupload' enctype='multipart/form-data' method='POST' action='".$this->uri(array('action'=>'imageupload'))."'>\n";
    $upload.="<input type='hidden' name='upload' value='1'>\n";
    $upload.="<input type='hidden' name='module' value='useradmin'>\n";
    $upload.="<input type='hidden' name='action' value='imageupload'>\n";
    $upload.="<input type='hidden' name='time' value='".time()."'>\n";
    
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='';
    $objTblclass->attributes=" align='center' border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';

    $input1="<input type='file' name='userFile'>\n";
    $input2="<input type='submit' class='button' value='".$this->objLanguage->languageText('word_submit')."'>\n";

    $img="<img src='".$objImage->userpicture($this->objUser->userId())."'>";

    $objTblclass->addrow(array("<h1>".$objLanguage->languageText("phrase_upload_image")."</h1>"));
    $objTblclass->addrow(array($img));
    $objTblclass->addRow(array($input1));
    $objTblclass->addRow(array($input2));
    
    $upload.=$objTblclass->show();

    $upload.="</form>\n";

    print $upload;

    $link=$this->uri(array('action'=>'mydetails','userId'=>$this->objUser->userId()));
    print "<a href='$link'  class='pseudobutton'>".$objLanguage->languageText("word_back")."</a>\n";
?>
