<?php

    // Table to display the texts
    $objButtons=&$this->getObject('navbuttons','navigation');
    $objTblclass=$this->newObject('htmltable','htmlelements');
    $objTblclass->width='';
    $objTblclass->attributes=" align='center' border='0'";
    $objTblclass->cellspacing='2';
    $objTblclass->cellpadding='2';

    $count=0;
    $missing=0;

    echo '<h1>'.$objLanguage->languagetext('mod_modulecatalogue_textelementsfor','modulecatalogue').' <em>'.$modname.'</em></h1>';

    // Display the headings
    $objTblclass->startRow();
    $objTblclass->addCell("&nbsp;", "", NULL, NULL, 'heading', NULL);
    $objTblclass->addCell($this->objLanguage->languageText("mod_modulecatalogue_regfile","modulecatalogue"), "", NULL, 'center', 'heading', 'colspan="2"');
    $objTblclass->addCell($this->objLanguage->languageText("mod_modulecatalogue_langtable",'modulecatalogue'), "", NULL, 'center', 'heading', 'colspan="2"');
    $objTblclass->endRow();


    $objTblclass->addRow(array($this->objLanguage->languageText('word_code'),$this->objLanguage->languageText('word_description'),
    	$this->objLanguage->languageText('word_content'),$this->objLanguage->languageText('word_description'),
    	$this->objLanguage->languageText('word_content')),'heading');

    // Now build up the table from the supplied array $moduledata
    foreach ($moduledata as $line)
    {
        $row=array($line['code'],$line['desc'],$line['content'],$line['isreg']['desc'],stripslashes($line['isreg']['content']));
        $objTblclass->addRow($row,'odd');
        $count=$count+1;
        if ($line['isreg']['flag']!=11)
        {
            $missing=$missing+1;
        }
    }


    // Table for the navigation buttons
    $objButtons=&$this->getObject('navbuttons','navigation');
    $objTbl2=$this->newObject('htmltable','htmlelements');
    $objTbl2->attributes=" align='center' border='0'";
    $objTbl2->cellspacing='2';
    $objTbl2->width='30%';

    $objTbl2->startRow();

    // Button to add new texts
    if ($missing>0)
    {
        //$objTbl2->startRow();
        $addphrase=$objLanguage->languageText('mod_modulecatalogue_addtext','modulecatalogue');
        $addlink=$objButtons->pseudoButton($this->uri(array('action'=>'addtext','mod'=>$modname,'cat'=>$activeCat)),$addphrase);
        $objTbl2->addCell($addlink,'',NULL,'center',NULL,NULL);
        //$objTbl2->endRow();
    }

    // Button to replace all Texts
    if ($count>0){
        $rphrase=$objLanguage->languageText('mod_modulecatalogue_replacetext','modulecatalogue');
        $rlink=$objButtons->pseudoButton($this->uri(array('action'=>'replacetext','mod'=>$modname,'cat'=>$activeCat)),$rphrase);
        $objTbl2->addCell($rlink,'',NULL,'center',NULL,NULL);
    }

    // Button to return to main menu
    $objTbl2->addCell($objButtons->pseudoButton($this->uri(array('cat'=>$activeCat)),$objLanguage->languagetext('phrase_goback')),"",NULL,'center',NULL,NULL);

    $objTbl2->endRow();


    if ($count>0)
    {
        print $objTbl2->show();
        print $objTblclass->show();
        print $objTbl2->show();
    }
    else
    {
        print $objLanguage->languageText('mod_modulecatalogue_notext',"modulecatalogue")."<br/>\n";
        print $objTbl2->show();
    }

?>