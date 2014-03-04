<?php
/**
 * This template will list all the sections 
 */

//initiate objects
$table = & $this->newObject('htmltable', 'htmlelements');
$h3 = &$this->newObject('htmlheading', 'htmlelements');
$link = & $this->newObject('link', 'htmlelements');
$objIcon = & $this->newObject('geticon', 'htmlelements');


//create a heading
$h3->str = ' Category Manager  '.$objIcon->getAddIcon($this->uri(array('action' => 'addcat')));
//counter for records
$cnt = 1;
//get the pages
$arrCategories = $this->_objCategories->getCategories();
//


//setup the table headings
$table->startHeaderRow();
$table->addHeaderCell('#');
$table->addHeaderCell('Category Name');
$table->addHeaderCell('Published');
//$table->addHeaderCell('Order');
$table->addHeaderCell('Access');
$table->addHeaderCell('Section');
$table->addHeaderCell('Category ID');
$table->addHeaderCell(' ');
//$table->addHeaderCell('#Active');

$table->endHeaderRow();

$rowcount = 0;

//setup the tables rows  and loop though the records
foreach($arrCategories as $arrCategory)
{
    $link->link = $arrCategory['title'];
    $link->href = $this->uri(array('action' => 'addcat', 'mode' => 'edit', 'id' => $arrCategory['id']));

    $oddOrEven = ($rowcount == 0) ? "even" : "odd";

    $tableRow = array();
    $tableRow[]=$cnt++;
    $tableRow[]=$link->show();
    $tableRow[]=$this->_objUtils->getCheckIcon($arrCategory['published'], TRUE);
    // $table->addCell($arrCategory['ordering']);
    $tableRow[]=$this->_objUtils->getAccess($arrCategory['access']);

    $link->link = $this->_objSections->getMenuText($arrCategory['sectionid']);
    $link->href = $this->uri(array('action' => 'addsection', 'mode' => 'edit', 'id' => $arrCategory['sectionid']));

    $tableRow[]=$link->show();
    $tableRow[]=$arrCategory['id'];
    //$table->addCell($this->_objCategories->getCatCount($section['id']));
    //$table->addCell($section['created']);

    //delete link
    $objIcon->setIcon('delete');
    $link->link = $objIcon->show();
    $link->href = $this->uri(array('action' => 'deletecategory', 'id' => $arrCategory['id']));
    //add icon to table
    $tableRow[]=$link->show();

    $table->addRow($tableRow, $oddOrEven);
    $rowcount = ($rowcount == 0) ? 1 : 0;

}


//print out the page
print $h3->show();
print $table->show();


?>
