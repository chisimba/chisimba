<?
    $this->loadclass('form','htmlelements');
    $objNewForm= new form('Change', $this->uri(array('action'=>'changepassword'),'useradmin'));
    $objButtons=&$this->getObject('navbuttons','navigation');
    $this->loadclass('textinput','htmlelements');
    /* method to act as a 'wrapper' for textelement class
    * @author James Scoble
    * @param $name string
    * @param $type string
    * @param $value  string
    * @returns string
    */
    function textinput($name,$type,$value=NULL)
    {
        $field=new textinput($name,$value);
        $field->fldType=$type;
        return $field->show();
    }

    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='';
    $objTblclass->attributes=" align='center' border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';

    $objTblclass->startRow();
    $objTblclass->addCell($objLanguage->languageText('word_change')." ".$objLanguage->languageText('word_password'), "", NULL, 'center', 'heading', 'colspan=3');
    $objTblclass->endRow();

    if (isset($change_error))
    {
        $objTblclass->startRow();
        $objTblclass->addCell($objLanguage->languageText($change_error), "", NULL, 'center', 'heading', 'colspan=3');
        $objTblclass->endRow();
    }

    $current=$objLanguage->languageText('word_current')." ".$objLanguage->languageText('word_password')."\n";
    $new1=$objLanguage->languageText('word_new')." ".$objLanguage->languageText('word_password')."\n";
    $new2=$objLanguage->languageText('word_confirm')." ".$objLanguage->languageText('word_new')." ".$objLanguage->languageText('word_password')."\n";
    $row=array($current,$new1,$new2);
    $objTblclass->addRow($row,'even');
    
    $row=array(textinput('oldpassword','password'),textinput('newpassword','password'),textinput('confirmpassword','password'));
    $objTblclass->addRow($row,'even');

    $objNewForm->addtoForm($objTblclass->show());
    $objNewForm->addtoForm($objButtons->putSaveButton());
    
    print $objNewForm->show();
?>
