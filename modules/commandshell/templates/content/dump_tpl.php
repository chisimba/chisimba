<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(1);






if (isset($str)) {
   $cssLayout->setMiddleColumnContent($str);
}
if (isset($ar)) {
    //Create an instance of the table object
    $objTable = $this->newObject('htmltable', 'htmlelements');
    //Turn on active rows
    $objTable->active_rows=TRUE;
    //Turn the array into a table
    $objTable->arrayToTable($ar);
    //Show the table
    $cssLayout->setMiddleColumnContent($objTable->show());
}
//Render it out
echo $cssLayout->show();
?>