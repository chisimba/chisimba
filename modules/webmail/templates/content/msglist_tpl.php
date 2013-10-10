<?php
$this->href = $this->getObject('href', 'htmlelements');
$table = $this->newObject('htmltable', 'htmlelements');
$table->border = 1;

$table->attributes="align=\"center\"";
//Create the array for the table header
$tableRow=array();
$tableHd[]="From";
$tableHd[]="Subject";
$tableHd[]="Date";

$table->addHeader($tableHd, "heading");
//Loop through and display the records
$rowcount = 0;
if (isset($data))
{
    if (count($data) > 0)
    {
        foreach ($data as $line)
        {
        	$oddOrEven = ($rowcount == 0) ? "odd" : "even";
            $tableRow[]=htmlentities($line['address']);
			$tableRow[]=htmlentities($line['subject']);
			$tableRow[]=htmlentities($line['date']);
			$table->addRow($tableRow, $oddOrEven);
            $tableRow=array();
            $rowcount = ($rowcount == 0) ? 1 : 0;
        }
    }
}

echo $table->show();