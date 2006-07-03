<?
    // First the language Object
    $this->objLanguage=&$this->getObject('language','language');

    // Now 3 table objects, two will go inside the other
    // The first is the main table
    $objTblClass=&$this->newObject('htmltable','htmlelements');
    $objTblClass->width='';
    $objTblClass->attributes=" align='center' border='0'";
    $objTblClass->cellspacing='10';
    $objTblClass->cellpadding='2';
                                  
    // The second table will have the register.conf main data
    $objTbl2=&$this->newObject('htmltable','htmlelements');
    $objTbl2->width='';
    $objTbl2->attributes=" align='center' border='0'";
    $objTbl2->cellspacing='2';
    $objTbl2->cellpadding='2';

    // The 3rd table has a list of the SQL tables the module added to the database.
    $objTbl3=&$this->newObject('htmltable','htmlelements');
    
    $this->modname = $this->getParam('mod');
    
    // Here we add the title
    $objTblClass->startRow();
    $objTblClass->addCell("<h1>".$this->confirmRegister('mod_moduleadmin_info',$this->modname)."</h1>", "", NULL, 'center',NULL, 'colspan="2"');
    $objTblClass->endRow();

    // Now we get the data for the tables
    $moduleData=$this->objModule->getRow('module_id',$this->modname);
    $authors=$this->registerdata['MODULE_AUTHORS'];
    $releaseDate=$this->registerdata['MODULE_RELEASEDATE'];
    $version=$this->registerdata['MODULE_VERSION'];
    $longName=$this->registerdata['MODULE_NAME'];
    $desc=$this->registerdata['MODULE_DESCRIPTION'];

    // Loading the data into the tables
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_modname','moduleadmin').':</b>',$longName));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_worddesc','moduleadmin').':</b>',$desc));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_authors','moduleadmin').':</b>',$authors));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_rdate','moduleadmin').':</b>',$releaseDate));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_version','moduleadmin').':</b>',$version));
    if (isset($this->registerdata['MENU_CATEGORY'])){
        foreach ($this->registerdata['MENU_CATEGORY'] as $line)
        {
            $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_menucat').':</b>',$line));
        }
    }
    
    // Now the dependencies
    if (isset($this->registerdata['DEPENDS'])){
        $str="<ul>\n";
        foreach ($this->registerdata['DEPENDS'] as $line)
        {
            $str.="<li><a href='".$this->uri(array('action'=>'info','modname'=>$line))."'>$line</a></li>\n";
        }
        $str.="</ul>\n";
        $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_moduleadmin_depend1','moduleadmin').':</b>',$str));
    }


    $str='<b>'.$this->objLanguage->languageText('mod_moduleadmin_tables','moduleadmin').":</b>\n";
    if (isset($this->registerdata['TABLE'])){
        $str.="<ul>\n";
        foreach ($this->registerdata['TABLE'] as $table)
        {
            $str.="<li>$table</li>\n";
        }
        $str.="</ul>\n";
    }
    $objTbl3->addRow(array($str));


    // Finally we put the tables together and print out the result:
    $objTblClass->addRow(array($objTbl2->show(),$objTbl3->show()),'even',"valign='top'");


    // Now in case of errors
    if ($this->objModule->errorText){
        $objTblClass->startRow();
        $objTblClass->addCell('<b>'.$this->objLanguage->languageText('mod_moduleadmin_problem1','moduleadmin','Problems detected').':</b>', "", NULL, NULL, 'odd',"colspan='2'");
        $objTblClass->endRow();
        $objTblClass->startRow();
        $objTblClass->addCell($this->objModule->errorText, "", NULL, NULL, 'odd','colspan="2"');
        $objTblClass->endRow();
    }
 
    // Link back
    $link1="<a href='".$this->uri(array(),'moduleadmin')."'>".$this->objLanguage->languageText('mod_moduleadmin_return','moduleadmin')."</a>";
    $link2='';
    $space='';
    if ($this->objModule->checkIfRegistered($this->modname)){
        if ($this->hasController($this->modname)){
            $link2="<a href='".$this->uri(array(),$this->modname)."'>"
            .$this->objLanguage->languageText('mod_moduleadmin_go','moduleadmin')."&nbsp;<b>"
            .$this->modname."</b></a>";
            $space='&nbsp;<b>/</b>&nbsp;';
        }
        $reglink="<a href='".$this->uri(array('action'=>'register','modname'=>$this->modname))."'>".$objLanguage->languagetext('mod_word_register','moduleadmin')."</a>";
    } else {
        $reglink="<a href='".$this->uri(array('action'=>'deregister','modname'=>$this->modname))."'>".$objLanguage->languagetext('mod_word_deregister','moduleadmin')."</a>";
    }
    
    $objTblClass->startRow();
    $objTblClass->addCell($link2.$space.$link1, "", NULL, 'center',NULL, 'colspan="2"');
    $objTblClass->endRow();

    print $objTblClass->show();
    
?>