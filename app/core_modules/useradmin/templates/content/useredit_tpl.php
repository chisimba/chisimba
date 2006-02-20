<?
    // Get the User Details
    $line=$this->getvar('userdata');
    
    echo '<h1>User Details for: '.$line['firstName'].' '.$line['surname'].'</h1>';
    
    // Put User Admin menus if user is admin
    if ($admin_user) {
        echo $alphaBrowseList;
    }

    
    $this->loadclass('textinput','htmlelements');
    $this->loadclass('dropdown','htmlelements');
    $this->loadclass('button','htmlelements');

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
    $link1=$objLanguage->languageText('menu_userdetails');
    $objTools->addToBreadCrumbs(array($link1));

    $objButtons=&$this->getObject('navbuttons','navigation');

    

    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='99%';
    $objTblclass->attributes=" align='center' border=0";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';

    $this->loadclass('form','htmlelements');
    $objNewForm= new form('Form1', $this->uri('','useradmin'));

    // Add hidden fields
    $objNewForm->addToForm(textinput('userId','hidden',$line['userId']));
    $objNewForm->addToForm(textinput('admin_user','hidden',$admin_user));
    $objNewForm->addToForm(textinput('old_username','hidden',$line['username']));
    $objNewForm->addToForm(textinput('old_accesslevel','hidden',$line['accesslevel']));


    // username
    // LDAP users may not change their username
    if ($line['howCreated']=='LDAP'){
        $row=array($objLanguage->languageText('word_username'),$line['username'].textinput('username','hidden',$line['username']));
    } else {
        $row=array($objLanguage->languageText('word_username'),textinput('username','text',$line['username']));
    }
    $objTblclass->addRow($row);
    
    // title
    $objDrop2= new dropdown('title');
    $titles=array("title_mr", "title_miss", "title_mrs", "title_ms", "title_dr", "title_prof", "title_rev", "title_assocprof");
    
    foreach ($titles as $row)
    {
        $row=$objLanguage->languageText($row);
        $objDrop2->addOption($row,$row);
    }
    $objDrop2->setSelected($line['title']);                              
    $row=array($objLanguage->languageText('word_title'),$objDrop2->show());
    $objTblclass->addRow($row);

    // firstname
    $row=array($objLanguage->languageText('phrase_firstname'),textinput('firstname','text',$line['firstName']));
    $objTblclass->addRow($row);

    // surname
    $row=array($objLanguage->languageText('word_surname'),textinput('surname','text',$line['surname']));
    $objTblclass->addRow($row);

    // email
    $row=array($objLanguage->languageText('phrase_emailaddress'),textinput('email','text',$line['emailAddress']));
    $objTblclass->addRow($row);

    // sex
    $objDrop3= new radio('sex');
    
    $objDrop3->addOption('M', $objLanguage->languageText('word_male'));
    $objDrop3->addOption('F', $objLanguage->languageText('word_female'));
    
    $objDrop3->setSelected($line['sex']);
    $row=array($objLanguage->languageText('word_sex'),$objDrop3->show());
    $objTblclass->addRow($row);

    // country
    $objCountries=&$this->getObject('countries','utilities');
    $objDrop4=new dropdown('country');
    $objDrop4->addFromDB($objCountries->getAll(' order by name'), "printable_name", "iso", $line['country']); 
    $row=array($objLanguage->languageText('word_country'),$objDrop4->show());
    $objTblclass->addRow($row);

    // Save Button
    
    $submitButton = new button('updatedetails');
    $submitButton->setValue($objLanguage->languageText('mod_useradmin_updatedetails'));
    $submitButton->setToSubmit();
    
    $row=array('&nbsp',$submitButton->show());
    $objTblclass->addRow($row);
    
    //print $objTblclass->show();
    
    $actionvalue='applyselfedit';

    $objNewForm->addToForm($objTblclass->show());
    $objNewForm->addToForm(textinput('userAdminAction','hidden',$actionvalue));
    $objNewForm->addToForm(textinput('action','hidden',$actionvalue));
    $objNewForm->displayType=3;
    //print $objNewForm->show();
    
    
    /***
    *
    *  SECOND COLUMN - USER IMAGE
    *
    */
    $startForm="<form name='fileupload' enctype='multipart/form-data' method='POST' action='".$this->uri(array('action'=>'imageupload'))."'>\n";
    $startForm.="<input type='hidden' name='upload' value='1'>\n";
    $startForm.="<input type='hidden' name='module' value='useradmin'>\n";
    $startForm.="<input type='hidden' name='action' value='imageupload'>\n";
    $startForm.="<input type='hidden' name='time' value='".time()."'>\n";
    
    $link2 = NULL;
    
    if (($this->isAdmin)||($line['userId']==$this->objUser->userId())){
        $link=$this->uri(array('action'=>'imagereset','userId'=>$line['userId'], 'admin_user' => $admin_user));
        
        // Check if Image is Default Image - Else show link to reset image
        if (substr_count($this->imagelink, 'default.jpg') < 1) {
            $link2.='<p align="center"><a href="'.$link.'" class="pseudobutton">'.$objLanguage->languageText("phrase_reset_image").'</a></p>';
        }
    }
             
    if ($line['userId']==$this->objUser->userId()){
        $link2 .="<input type='file' name='userFile'><br><br>";
        $link2 .="<input type='submit' class='button' value='".$this->objLanguage->languageText('mod_useradmin_changepicture')."'>\n"; 
    }
    $endForm = textinput('admin_user','hidden',$admin_user).'</form>';

    $col2 = $startForm.'<p align="center"><img src="'.$this->imagelink.'"></p><p align="center">'.$link2.'</p>'.$endForm;
    

    // Here we build up a table to display all the output

    $objTable2=$this->newObject('htmltable','htmlelements');
    $objTable2->width='99%';
    $objTable2->attributes=" border=0";
    $objTable2->cellspacing='5';
    $objTable2->cellpadding='5';
    $objTable2->startRow();
    $objTable2->addCell($objNewForm->show());
    $objTable2->addCell($col2, NULL, 'center', 'center');
    $objTable2->endRow();

    print $objTable2->show();
    print "<br />\n";
    if (($line['userId']==$this->objUser->userId())&& ($ldapflag==FALSE)){ 
        $this->loadclass('href','htmlelements');
        $objHref=new href($this->uri(array('module'=>'useradmin','action'=>'changepassword')),
        $objLanguage->languageText("word_change")." ".$objLanguage->languageText("word_password"),"class='pseudobutton'");
        print $objHref->show();
        
        if (!$this->isAdmin){
            $objHref=new href($this->uri(array('module'=>'useradmin','action'=>'selfdelete')),
            $objLanguage->languageText('mod_useradmin_selfdelete0'),"class='pseudobutton'");
            print "<br />".$objHref->show();
        }
            
     } else if (($this->isAdmin)&& ($ldapflag==FALSE)){ 
        $this->loadclass('href','htmlelements');
        $objHref=new href($this->uri(array('module'=>'useradmin','action'=>'adminchangepassword','userId'=>$line['userId'],'username'=>$line['username'])),
        $objLanguage->languageText("mod_useradmin_changepassword2","Change Password"),"class='pseudobutton'");
        print $objHref->show();
     }

    if ($admin_user) {
        echo $menu;
    }
?>
