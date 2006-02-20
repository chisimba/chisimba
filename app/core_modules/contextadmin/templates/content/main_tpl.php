<?php

//View template for table: tbl_quotes
//Note that you will probably need to edit this to make it actually work


//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

// Create an instance of the css layout class
$cssLayout2 = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout2->setNumColumns(2);

//Set the content of the left side column
$leftSideColumn2 = $this->objLanguage->code2Txt('mod_contextadmin_help',array('context'=>'course'));



// Add Left column
//$cssLayout->setLeftColumnContent($leftSideColumn);// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=3; //Heading <h3>
$this->objH->str=ucwords($this->objLanguage->code2Txt('mod_contextadmin_name',array('context'=>'course')));
$rightSideColumn2 = "<div align=\"center\">" 
  . $this->objH->show()  . "</div>";

//Create a table
$table = $this->newObject('htmltable', 'htmlelements');
$table->cellspacing="2";
$table->width="80%";
$table->attributes="align=\"center\"";
//Create the array for the table header
$tableRow=array();

$tableHd[]="Code";
$tableHd[]=$this->objLanguage->languageText("word_title");
$tableHd[]="dateCreated";
$tableHd[]=$this->objLanguage->languageText("mod_contextadmin_isActive");
$tableHd[]=$this->objLanguage->languageText("mod_contextadmin_isclosed");
//$tableHd[]="modifierId";
$allowAdmin = True; //You need to write your security here
if ($allowAdmin) {
    $paramArray = array('action' => 'add');
    $tableHd[] = $objButtons->linkedButton("add",
    $this->uri($paramArray));
    $tableHd[]="&nbsp;";
    $tableHd[]="&nbsp;";
} else {
    $tableHd[]="&nbsp;";
    $tableHd[]="&nbsp;";
}

//Get the icon class and create an add, edit and delete instance
$objAddIcon = & $this->newObject('geticon', 'htmlelements');
$objEditIcon = & $this->newObject('geticon', 'htmlelements');
$objDelIcon = & $this->newObject('geticon', 'htmlelements');
$objRightIcon = & $this->newObject('geticon', 'htmlelements');
$objWrongIcon = & $this->newObject('geticon', 'htmlelements');
$objConfIcon = $this->newObject('geticon', 'htmlelements');
$objLink= &$this->newObject('link','htmlelements');

$objRightIcon->setIcon('greentick');
$objRightIcon->alt = '';
$objWrongIcon->setIcon('redcross');
$objWrongIcon->alt ='';

//Create the table header for display
$table->addHeader($tableHd, "heading");

//Loop through and display the records
$rowcount = 0;
if (isset($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";         
            $tableRow[]=strtoupper( $line['contextCode'] );
            $tableRow[]=strtoupper( $line['title'] );
            $tableRow[]=$line['dateCreated'];
            $tableRow[]= ($line['isActive'] == 1) ? $objRightIcon->show(): $objWrongIcon->show();
            $tableRow[]= ($line['isClosed'] == 1) ? $objRightIcon->show(): $objWrongIcon->show();
            
            //The context configuration link
            $confLink = $this->uri(array('action' => 'courseadmin'));
       
               $paramArray = array('action' => 'joincontext','contextCode' => $line['id']);
                $objConfIcon->setModuleIcon('contextadmin');
                $objConfIcon->alt=ucwords($this->objLanguage->code2Txt('mod_contextadmin_confcourse',array('context'=>'course')));
                $objLink->href=$this->uri($paramArray, "contextadmin");                
                $objLink->link=$objConfIcon->show();
                $config = $objLink->show().'&nbsp;';
          
            //The URL for the edit link
            $editLink=$this->uri(array('action' => 'edit',
             'contextCode' => $line['contextCode'],
             'id' =>$line['id']));
            $objEditIcon->alt=$this->objLanguage->languageText("mod_quotes_editalt");
            $ed = $objEditIcon->getEditIcon($editLink);

            // The delete icon with link uses confirm delete utility
            $objDelIcon->setIcon("delete", "gif");
            $objDelIcon->alt=$this->objLanguage->code2Txt('mod_contextadmin_deletecontext',array('context'=>'course'));
            $delLink = $this->uri(array(
              'action' => 'delete',
              'confirm' => 'yes',
              'contextCode' => $line['contextCode'],
              'id' => $line['id']));
            $objConfirm = & $this->newObject('confirm','utilities');
            
            
            $rep = array('ITEM', $line['id']);
            $objConfirm->setConfirm($objDelIcon->show(),
            $delLink,$this->objLanguage->code2Txt("mod_quotes_confirm", $rep));
            $conf = $objConfirm->show();
            $tableRow[]=$config;
            $tableRow[]=$ed;
            $tableRow[]=$conf;            //Add the row to the table for output
           $table->addRow($tableRow, $oddOrEven);
           $tableRow=array(); // clear it out
           // Set rowcount for bitwise determination of odd or even
           $rowcount = ($rowcount == 0) ? 1 : 0;

        }
    }
}

//Add the table to the centered layer
$rightSideColumn2 .= $table->show();

// Add Left column
$cssLayout2->setLeftColumnContent($leftSideColumn2);

// Add Right Column
$cssLayout2->setMiddleColumnContent($rightSideColumn2);

//Output the content to the page
echo $cssLayout2->show();
?>
