<?
    $adminMessage=$this->getVar('adminMessage');
    if ($adminMessage){
        print "<h2>".$objLanguage->languageText('word_problem').': '.$objLanguage->languageText($adminMessage)."</h2>\n";
    }
    
    $this->loadclass('textinput','htmlelements');
    
    
    $objLanguage=& $this->getObject('language', 'language');
    //$objDropdown=$this->getObject('dropdown','display');
    $objButtons=&$this->getObject('navbuttons','navigation');
             
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='';
    $objTblclass->attributes=" align='center' border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';
	
    $this->loadclass('dropdown','htmlelements');
    $objDrop2= new dropdown('title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    $objDrop2->addOption('',$objLanguage->languageText('option_selectatitle'));
    foreach ($titles as $row)
    {
        $row=$objLanguage->languageText($row);
        $objDrop2->addOption($row,$row);
    }

    $this->loadclass('form','htmlelements');
    $objNewForm= new form('Form1', $this->uri('','useradmin'));
?>
<!--- <form action='index.php?module=useradmin' method=post name='Form1'> -->
<?
 //print textinput('action','hidden','Add User');
   $objNewForm->addToForm($this->textinput('action','hidden','adduser'));
?><?
    $phrase_new_user=$objLanguage->languageText("word_new")." ".$objLanguage->languageText('word_user');
    $objTblclass->startRow();
    $objTblclass->addCell("<h1>".$phrase_new_user."</h1>", "", NULL, 'center', NULL, 'colspan=2');
    $objTblclass->endRow();
    
    $objTblclass->startRow();
    $objTblclass->addCell('&nbsp;', "", NULL, 'center', 'heading', 'colspan=2');
    $objTblclass->endRow();

    // userId
    $row=array($objLanguage->languageText('word_userId','userId'),$this->textinput('userId','text'));
    $objTblclass->addRow($row,'even');

    $idbutton="<a onclick=\"document.Form1.userId.value=Math.round(Math.random()*1000)+'".date('ydi')."';\">\n";
    $idbutton.="<input type='button' class='button' value='".$objLanguage->languageText("hyperlink_generaterandomnumber")."'>\n";
    $idbutton.="</a>\n";
    $objTblclass->startRow();
    $objTblclass->addCell($idbutton, "", NULL, 'center', 'heading', 'colspan=2');
    $objTblclass->endRow();
    
    // username
    $row=array($objLanguage->languageText('word_username'),$this->textinput('username','text'));
    $objTblclass->addRow($row,'even');

    //title
    $row=array($objLanguage->languageText('word_title'),$objDrop2->show());
    $objTblclass->addRow($row,'even');

    //firstname
    $row=array($objLanguage->languageText('phrase_firstname'),$this->textinput('firstname','text'));
    $objTblclass->addRow($row,'even');

    // surname
    $row=array($objLanguage->languageText('word_surname'),$this->textinput('surname','text'));
    $objTblclass->addRow($row,'even');

    // password
    $row=array($objLanguage->languageText('word_password'),$this->textinput('password','password'));
    $objTblclass->addRow($row,'even');
    
    // password again
    $row=array($objLanguage->languageText('word_password'),$this->textinput('passwd','password'));
    $objTblclass->addRow($row,'even');

    // email
    $row=array($objLanguage->languageText('phrase_emailaddress'),$this->textinput('email','text'));
    $objTblclass->addRow($row,'even');

    // sex
    $objDrop3= new dropdown('sex');
    $titles=array("M", "F");
    foreach ($titles as $row)
    {
        $objDrop3->addOption($row,$row);
    }
    $row=array($objLanguage->languageText('word_sex'),$objDrop3->show()); 
    //$objDropdown->makeDropDown(array('M','F'), "sex", "sex", "sex", "1", "44",""));
    $objTblclass->addRow($row,'even');
    
    // country
    $countries=$this->getObject('countries','utilities');
    $objDrop4=new dropdown('country');
    $objDrop4->addFromDB($countries->getAll(), "printable_name", "iso", 'ZA');
    
    $row=array($objLanguage->languageText('word_country'),$objDrop4->show());
   //$objDropdown->makeDropDownFromModel($this->getObject('countries','utilities'), "printable_name", "iso", "country", "1", "144", 'ZA'));
    $objTblclass->addRow($row,'even');

    //access level
    /*
    $objDrop5= new dropdown('accessLevel');
    $levels=$this->getObject('groups','security');
    $objDrop5->addFromDB($levels->getAll(),'groupId','groupId','students');
    $row=array($objLanguage->languageText('access_level'),$objDrop5->show());
    //$objDropdown->makeDropDownFromModel($this->getObject('groups','security'), "groupId", "groupId", "accessLevel", "1", "144", 'students'));
    $objTblclass->addRow($row,'even');
    */

    // save button

    //$objTblclass->startRow();
    //$objTblclass->addCell("<input type=submit class='button' value='".$phrase_new_user."'>", "", NULL, 'center', 'even', 'colspan=2');
    //$objTblclass->endRow();

    $row=array('&nbsp',$objButtons->putSaveButton());
    $objTblclass->addRow($row,'even');
    
    //print $objTblclass->show();
    $objNewForm->addToForm($objTblclass->show());
    $objNewForm->displayType=3;
    print $objNewForm->show();
?>
<!--- </form>-->
