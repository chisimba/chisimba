<?php
    $this->loadclass('form','htmlelements');
    $objNewForm= new form('Change', $this->uri(array('action'=>'adminchangepassword'),'useradmin'));
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

    // Breadcrumbs object
    $objTools=&$this->getObject('tools','toolbar');
    $link1="<a href='".$this->uri(array('action'=>'edit','userId'=>$this->info['userId']))."'>".$objLanguage->languageText('user details')."</a>";
    $link2=$objLanguage->languageText('mod_useradmin_changepassword2','Change Password');
    $objTools->addToBreadCrumbs(array($link1,$link2));

    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='';
    $objTblclass->attributes=" align='center' border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';

    $objTblclass->startRow();
    $objTblclass->addCell(str_replace('{USER}',$this->info['username'],$objLanguage->languageText('mod_moduleadmin_changepassword')), "", NULL, 'center', 'heading', 'colspan=3');
    $objTblclass->endRow();

    if (isset($change_error))
    {
        $objTblclass->startRow();
        $objTblclass->addCell($objLanguage->languageText($change_error), "", NULL, 'center', 'heading', 'colspan=3');
        $objTblclass->endRow();
    }

    $new1=$objLanguage->languageText('word_new')." ".$objLanguage->languageText('word_password')."\n";
    $new2=$objLanguage->languageText('word_confirm')." ".$objLanguage->languageText('word_new')." ".$objLanguage->languageText('word_password')."\n";
    $row=array($new1,$new2);
    $objTblclass->addRow($row,'even');
    
    $row=array(textinput('newpassword','password'),textinput('confirmpassword','password'));
    $objTblclass->addRow($row,'even');

    $objNewForm->addtoForm(textinput('userId','hidden',$this->info['userId']));
    $objNewForm->addtoForm($objTblclass->show());
    $objNewForm->addtoForm($objButtons->putSaveButton());
    
    print $objNewForm->show();
?>
