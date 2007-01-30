<?php
if (isset($str)) {
   echo $str; 
}
if (isset($ar)) {
    //Create an instance of the table object
    $objTable = $this->newObject('htmltable', 'htmlelements');
    //Turn on active rows
    $objTable->active_rows=TRUE;
    //Turn the array into a table
    $objTable->arrayToTable($ar);
    //Show the table
    echo $objTable->show();
}
?>