<?php
    $this->loadclass('form','htmlelements');
    $objNewForm= new form('ResetPassword', $this->uri(array('action'=>'resetpassword'),'useradmin'));
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
    $objTblclass->addCell($objLanguage->languageText('mod_useradmin_resetpassword', 'useradmin', 'Enter your username and email address, and a new password will be generated and emailed to you.'), "", NULL, 'center', 'heading', 'colspan=2');
    $objTblclass->endRow();

    if (isset($change_error))
    {
        $objTblclass->startRow();
        $objTblclass->addCell($objLanguage->languageText($change_error), "", NULL, 'center', 'heading', 'colspan=3');
        $objTblclass->endRow();
    }

    $objTblclass->addRow(array($objLanguage->languageText('word_username'),textinput('username','text')));
    $objTblclass->addRow(array($objLanguage->languageText('phrase_emailaddress'),textinput('email','text')));
    

    $objNewForm->addtoForm($objTblclass->show());
    $objNewForm->addtoForm($objButtons->putSaveButton());
    
    print $objNewForm->show();
?>
