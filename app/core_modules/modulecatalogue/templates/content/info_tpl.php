<?php
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

    $infoHead = '<h3>'.stripslashes(str_replace('MODULE',ucwords($this->modname),$this->objLanguage->languageText('mod_modulecatalogue_info','modulecatalogue'))).'</h3>';
    // Here we add the title
    $objTblClass->startRow();
    $objTblClass->addCell($infoHead, "", NULL, 'center',NULL, 'colspan="2"');
    $objTblClass->endRow();

    // Now we get the data for the tables
    $moduleData=$this->objModule->getRow('module_id',$this->modname);
    $authors=$this->registerdata['MODULE_AUTHORS'];
    $releaseDate=$this->registerdata['MODULE_RELEASEDATE'];
    $version=$this->registerdata['MODULE_VERSION'];
    $longName=$this->registerdata['MODULE_NAME'];
    $desc=$this->registerdata['MODULE_DESCRIPTION'];

    // Loading the data into the tables
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_modname','modulecatalogue').':</b>',$longName));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_worddesc','modulecatalogue').':</b>',$desc));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_authors','modulecatalogue').':</b>',$authors));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_rdate','modulecatalogue').':</b>',$releaseDate));
    $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_version','modulecatalogue').':</b>',$version));
    if (isset($this->registerdata['MENU_CATEGORY'])){
        foreach ($this->registerdata['MENU_CATEGORY'] as $line)
        {
            $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_menucat','modulecatalogue').':</b>',$line));
        }
    }

    // Now the dependencies
    if (isset($this->registerdata['DEPENDS'])){
        $str="<ul>\n";
        foreach ($this->registerdata['DEPENDS'] as $line)
        {
            $str.="<li><a href='".$this->uri(array('action'=>'info','mod'=>$line))."'>$line</a></li>\n";
        }
        $str.="</ul>\n";
        $objTbl2->addRow(array('<b>'.$this->objLanguage->languageText('mod_modulecatalogue_depend1','modulecatalogue').':</b>',$str));
    }


    $str='<b>'.$this->objLanguage->languageText('mod_modulecatalogue_tables','modulecatalogue').":</b>\n";
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

    // Link back
    $link1="<a href='".$this->uri(array('cat'=>$this->getParm('cat')),'modulecatalogue')."'>".$this->objLanguage->languageText('mod_modulecatalogue_return','modulecatalogue')."</a>";
    $link2='';
    $space='';
    if ($this->objModFile->findController($this->modname)){
    	$link2="<a href='".$this->uri(array(),$this->modname)."'>"
    	.$this->objLanguage->languageText('mod_modulecatalogue_go','modulecatalogue')."&nbsp;<b>"
    	.ucwords($this->modname)."</b></a>";
    	$space='&nbsp;<b>/</b>&nbsp;';
    }
    $link3 ="&nbsp;<b>/</b>&nbsp;<a href='".$this->uri(array('action'=>'reloaddefaultdata','moduleid'=>$this->registerdata['MODULE_ID']))."'>".$this->objLanguage->languageText('mod_modulecatalogue_reloaddefault','modulecatalogue')."</a>";
    $objTblClass->startRow();
    $objTblClass->addCell($link2.$space.$link1.$link3, "", NULL, 'center',NULL, 'colspan="2"');
    $objTblClass->endRow();

    echo $objTblClass->show();

?>