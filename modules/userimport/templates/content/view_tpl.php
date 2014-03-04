<?php
    /**
    * View template file for the userimport module
    * Displays data and links
    */
    
    $objConfirm = $this->newObject('confirm','utilities'); 
    
    print "<div align='center'>";
    
    if (is_array($this->result)){

        $batchId=$this->result['batchCode'];
        $contextId=$this->result['courseCode'];
        
        // Basic table setup
        $objTable=$this->newObject('htmltable','htmlelements');
        $objTable->attributes=" align='center' border='0'";
        $objTable->width='50%';
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        $objTable->alternate_row_colors=TRUE;
        
        // Adding a heading
        $objHeading=$this->newObject('htmlheading','htmlelements');
        $heading=str_replace('[BATCH]',$batchId,$this->objLanguage->languageText("mod_userimport_show",'userimport'));
        $heading=str_replace('[COURSE]',$contextId,$heading);
        $objHeading->str=$heading;
        $objHeading->type=3;
        print $objHeading->show();

        // Table heading
        $headers=array('word_userid','word_username','phrase_firstname','word_surname','word_title','word_sex','phrase_emailaddress');
        $objTable->startHeaderRow();
        foreach ($headers as $element)
        {
            $objTable->addHeaderCell($this->objLanguage->languageText($element));
        }
        $objTable->endHeaderRow();
        
        // Using arrayToTable() to build the table
        $objTable->arrayToTable($this->result['users']);

        print $objTable->show();
    
    
        // Basic table setup
        $objTable=$this->newObject('htmltable','htmlelements');
        $objTable->attributes=" align='center' border='0'";
        $objTable->width='50%';
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        
        // Get the link for deleting a batch
        $deleteURI=$this->uri(array('action'=>'delete','batchCode'=>$batchId));
        // Build the confirmation text
        $confirmText=str_replace('[BATCH]',$batchId,$this->objLanguage->languageText('mod_userimport_confirm','userimport'));
        // Add the javascript to make a confirmation pop-up
        $objConfirm->setConfirm($objLanguage->languagetext('word_delete'),$deleteURI,$confirmText,"class='pseudobutton'");
        $deleteLink=$objConfirm->show();

        $exportCSV="<a href='".$this->uri(array('action'=>'exportcsv','batchCode'=>$batchId))."' class='pseudobutton'>".$this->objLanguage->languageText("mod_userimport_exportcsv",'userimport')."</a>";
        $exportXML="<a href='".$this->uri(array('action'=>'exportxml','batchCode'=>$batchId))."' class='pseudobutton'>".$this->objLanguage->languageText("mod_userimport_exportxml",'userimport')."</a>";
        
        $objTable->addRow(array($deleteLink,$exportCSV,$exportXML));
        
        print $objTable->show();
    }
    print "</div>\n";
?>
