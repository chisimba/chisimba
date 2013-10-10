<?php
$this->setLayoutTemplate('faq2_layout_tpl.php');
// Show the heading.
$objHeading =& $this->getObject('htmlheading','htmlelements');
$objHeading->type=4;
$objHeading->str="&nbsp;".$this->objLanguage->languageText("faq2_managecategories","faq2"); 

//Create link icon and link to view template
$this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$link = new link($this->uri(array(
    'action' => 'showcats'
)));
$objIcon->setIcon('prev');
$objIcon->alt=$objLanguage->languageText("word_back","faq2");
$link->link = $objIcon->show();
$previous = $link->show();
$objHeading->str.="&nbsp;$previous";
//display heading
echo $objHeading->show();
//display page css layout
echo $display;



// Show the add link
$objLink =& $this->getObject('link','htmlelements');
$objLink->link($this->uri(array(
 		'module'=>'faq2',
		'action'=>'addCategory',
)));


// Create table for categories.
$objTable =& $this->newObject('htmltable','htmlelements');
$objTable->width='';
$objTable->cellpadding='5';
$objTable->cellspacing='3';
// Add the table header.
$objTable->startHeaderRow();
$objTable->addHeaderCell($objLanguage->languageText("mod_faq2_category","faq2"), 150);
$objTable->addHeaderCell($objLanguage->languageText("mod_faq2_license","faq2"), 100);
$objTable->addHeaderCell($objLanguage->languageText("mod_faq2_noitems","faq2"), 50);
$objTable->addHeaderCell($objLanguage->languageText("mod_faq2_action","faq2"), 100);
$objTable->endHeaderRow();
$index = 0;
//check if there are categories
$count=count($categories);
if($count==0)
$norecords=$this->objLanguage->languageText("faq2_norecords","faq2");
foreach ($categories as $item) {
    
    // Create the delete link.
	$objConfirm=&$this->newObject('confirm','utilities');
    $iconDelete=$this->getObject('geticon','htmlelements');
    $iconDelete->setIcon('delete');
    $iconDelete->alt=$objLanguage->languageText("word_delete");
    $iconDelete->align=false;
	$objConfirm->setConfirm(
    	$iconDelete->show(),
		$this->uri(array(
	    	'module'=>'faq2',
		  	'action'=>'deletecategoryconfirm',
		  	'id'=>$item["catid"]
		)),
        $objLanguage->languageText('phrase_suredelete')."<br>This category will go, plus all entries on it"
    );
    // Create the edit link.
    $iconEdit=$this->getObject('geticon','htmlelements');
    $iconEdit->setIcon('edit');
    $iconEdit->alt=$objLanguage->languageText("word_edit");
    $iconEdit->align=false;
    $objEditLink =& $this->getObject('link', 'htmlelements');
    $objEditLink->link($this->uri(array('action'=>'editcategory','id'=>$item['catid']), 'faq2'));
    $objEditLink->link =$iconEdit->show();
    
    
	
    
    // Count no items in category
    $this->objFaqEntries =& $this->getObject('dbfaqentries', 'faq2');
    $list=$this->objFaqEntries->getFaqEntriesCount($item['catid']);
    $count=count($list);
    
    //get category license
    //print_r($item);
    $catlicense =  $this->objFaqCategories->getLicenseCode($item['catid']);
    //print_r($catlicense);//create the icons
     $action = $objEditLink->show()."&nbsp;".$objConfirm->show();
    
    // Create link to category
    $categoryLink =& $this->getObject('link', 'htmlelements');
    $categoryLink->link($this->uri(array('action'=>'view','category'=>$item['catid']), 'faq2'));
    $categoryLink->link = $item['name'];
    $categoryLink->title = $this->objLanguage->languageText('mod_faq2_viewcategory','faq2');

   
    
    $objTable->startRow();
    $objTable->addCell($categoryLink->show());
    $objTable->addCell($this->objLicense->show($catlicense[0]['license']));
    $objTable->addCell($count);
    $objTable->addCell($action);
    $objTable->endRow();
    $ncaction = "";
    
}
// Show the table.
echo($objTable->show());
//If no entries then display message.
	if ($count==0) {
		echo "<div align=\"left\"class=\"noRecordsMessages\">" . $norecords . "</div>";
	}

?>
