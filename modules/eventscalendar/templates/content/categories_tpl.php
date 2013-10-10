<?php
$myTable = $this->newObject('htmltable', 'htmlelements');
$objIcon = & $this->newObject('geticon', 'htmlelements');
$link = & $this->newObject('link', 'htmlelements');
$objDelIcon = & $this->newObject('geticon', 'htmlelements');

$myTable->width='60%';
$myTable->border='0';
$myTable->cellspacing='1';
$myTable->cellpadding='10';
$myTable->css_class = "table1";
    
$myTable->startHeaderRow();
$myTable->addHeaderCell('Event Category');
$myTable->addHeaderCell('Colour');
$myTable->addHeaderCell('&nbsp;');
$myTable->endHeaderRow();    


print "<h1>Event Categories </h1>";
print '<span class="icon">'.$objIcon->getAddIcon($this->uri(array('action' => 'addcat'))).'</span>';
if($categories)
{
    foreach ($categories as $category)
    {
        //edit link
        $link->link =$category['title'];
		$link->href = $this->uri(array('action' => 'addcat', 'mode' => 'edit', 'id' => $category['id']));
		
		$rep = array('ITEM', $category['id']);
		$objDelIcon->setIcon("delete", "gif");
        $objDelIcon->alt=$this->objLanguage->code2Txt('mod_contextadmin_deletecontext','contextadmin',array('context'=>'course'));
		$delLink = $this->uri(array(
              'action' => 'delete',
              'confirm' => 'yes',
              'id' => $category['id']));
        $objConfirm = & $this->newObject('confirm','utilities');
        $objConfirm->setConfirm($objDelIcon->show(),$delLink,$this->objLanguage->code2Txt("mod_quotes_confirm", $rep));
        
        
        $myTable->startRow();
        $myTable->addCell($link->show());
        $myTable->addCell($category['colour'] , null , null, null, null, ' style= " background:'.$category['colour'].'"');
        $myTable->addCell($objConfirm->show());
        $myTable->endRow();

        $objConfirm = null;
    }
    
    echo $myTable->show();
     

} else {
    
    $str .= '<div align="center" style="font-size:large;font-weight:bold;color:#CCCCCC;font-family: Helvetica, sans-serif;">No Categories Found</div>';
}

?>