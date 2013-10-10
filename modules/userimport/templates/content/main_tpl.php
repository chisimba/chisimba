<?php
    /**
    * Main template file for the userimport module
    * Displays data and forms
    */
    
    print "<div align='center'>";
    
    // If data has just been added, we make a table to display it.
    if ( ($this->result!='') && isset($this->result['student']) ){
        
        // Basic table setup
        $objTable=$this->newObject('htmltable','htmlelements');
        $objTable->attributes=" align='center' border='0'";
        $objTable->width='50%';
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        $objTable->alternate_row_colors=TRUE;
        
        // Adding a heading
        $objHeading=$this->newObject('htmlheading','htmlelements');
        $objHeading->str=$this->objLanguage->languageText("mod_userimport_message3",'userimport');
        $objHeading->type=3;
        print $objHeading->show();

        // Table heading
        $headers=array('word_userid','word_username','phrase_firstname','word_surname');
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('Id');
        foreach ($headers as $element)
        {
            $objTable->addHeaderCell($this->objLanguage->languageText($element));
        }
        $objTable->endHeaderRow();
        
        // Using arrayToTable() to build the table
        $objTable->arrayToTable($this->result['student']);

        print $objTable->show();
    }


/**
* Upload template for userimport module
* Allows upload of CSV files
* Written by James Scoble using code written by Wesley Nitsckie
*/

    //Creating the form
    $this->loadClass('form', 'htmlelements');
    $form = new form('importusers');
    $form->extra=' enctype="multipart/form-data" ';
    //$form->name='importusers';
    $paramArray = array('action' => 'upload');
    $form->setAction($this->uri($paramArray,'userimport'));

    //the file input
    $this->loadClass('textinput','htmlelements');
    
    $fileInput= new textinput('uploadCSV');
    $fileInput->fldType='file';
    $fileInput->label=$this->objLanguage->languageText("mod_userimport_message2",'userimport');
    $fileInput->name='uploadCSV';
    $fileInput->size=60;

    //the submit button
    $objElement = new button('CSV');	
    $objElement->setToSubmit();	
    $objElement->setValue($this->objLanguage->languageText("word_upload"));

    //the file input
    $fileInput2= new textinput('uploadXML');
    $fileInput2->fldType='file';
    $fileInput2->label=$this->objLanguage->languageText("mod_userimport_message4",'userimport');
    $fileInput2->size=60;

    //the submit button
    $objElement2 = new button('XML');	
    $objElement2->setToSubmit();	
    $objElement2->setValue($this->objLanguage->languageText("word_upload"));

    //add the objects to the form
    $form->setDisplayType(1);
    $form->addToForm($fileInput);
    $form->addToForm($objElement);

    $form->addToForm("<br />\n");
    $form->addToForm($fileInput2);
    $form->addToForm($objElement2);

    //Heading
    $objHeading=$this->newObject('htmlheading','htmlelements');
    $objHeading->str=$this->objLanguage->languageText("mod_userimport_message1",'userimport');
    $objHeading->type=3;
    $strCenter=$objHeading->show();
    $strCenter.=$form->show();

    print $strCenter."\n"; 
    
    print"</div>\n";


    // Now a table will be shown of existing "batches", if there are any this user can work on.

    //Heading
    $objHeading=$this->newObject('htmlheading','htmlelements');
    $contextword=$this->objLanguage->languageText('mod_context_context','userimport');
    $objHeading->str=$this->objLanguage->code2Txt('mod_userimport_showlist','userimport');
    $objHeading->type=3;
    
    // Get the info from the batch classes
    $info=$this->objUserBatch->listBatch($this->contextCode);
    if (count($info)>0){

        print "<div align='center'>\n";
        // print the heading
        print $objHeading->show();
        // Get a link to the "confirm" object
        $objConfirm = $this->newObject('confirm','utilities');
        
        // Basic table setup
        $objTable=$this->newObject('htmltable','htmlelements');
        $objTable->attributes=" align='center' border='0'";
        $objTable->width='50%';
        $objTable->cellspacing='2';
        $objTable->cellpadding='2';
        
        // A loop through the array, with a row for each batch. The uri() method is used to create the links.
        foreach ($info as $line)
        {
            if (isset($line['batchid'])){
                $line['batchId']=$line['batchid'];
            }
            // Get the link for deleting a batch
            $deleteURI=$this->uri(array('action'=>'delete','batchCode'=>$line['batchId']));
            // Build the confirmation text
            $confirmText=str_replace('[BATCH]',$line['batchId'],$this->objLanguage->languageText('mod_userimport_confirm','userimport'));
            // Add the javascript to make a confirmation pop-up
            $objConfirm->setConfirm($objLanguage->languagetext('word_delete'),$deleteURI,$confirmText,"class='pseudobutton'");
            $deleteLink=$objConfirm->show();

            $viewLink="<a href='".$this->uri(array('action'=>'view','batchCode'=>$line['batchId']))."' class='pseudobutton'>".$this->objLanguage->languageText("word_view")."</a>";
            $exportCSV="<a href='".$this->uri(array('action'=>'exportcsv','batchCode'=>$line['batchId']))."' class='pseudobutton'>".$this->objLanguage->languageText("mod_userimport_exportcsv",'userimport')."</a>";
            $exportXML="<a href='".$this->uri(array('action'=>'exportxml','batchCode'=>$line['batchId']))."' class='pseudobutton'>".$this->objLanguage->languageText("mod_userimport_exportxml",'userimport')."</a>";
            $objTable->addRow(array($line['batchId'],$deleteLink,$viewLink,$exportCSV,$exportXML,$line['creationdate'],$line['contextcode']));
        }


        print $objTable->show();
        print "</div>\n";
    }

    $this->objConfig=$this->getObject('dbsysconfig','sysconfig');
    $remoteServer=$this->objConfig->getValue('remotedata','userimport');
    if (($remoteServer!='REMOTE_SERVER') && (strlen($remoteServer)>5)){
        $rstring=$this->objLanguage->languageText('mod_userimport_remotelink','userimport');
        $rlink="<div align='center'><a href='".$this->uri(array('action'=>'remoteimport'))."'>$rstring</a></div>\n";
        print "<br />\n$rlink";
    }
    
?>
