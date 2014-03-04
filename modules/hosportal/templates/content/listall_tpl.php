<?php

//$objElement = $this->newObject('multitabbedbox', 'htmlelements');
////$switchmenu->addHeading("dfdfgdfggfgdfgggdfgdfg");
//  $objElement =new multitabbedbox(100,500);
//            $objElement->addTab(array('name'=>'First','url'=>'http://www.google.com','content' => $form,'default' => true));
//            $objElement->addTab(array('name'=>'Second','url'=>'http://www.google.com','content' => $check.$radio.$calendar));
//            $objElement->addTab(array('name'=>'Third','url'=>'http://localhost','content' => $tab,'height' =>
//         '300','width' => '600'));
//$myTable=$this->loadClass('tigramenu','htmlelements');
//            $myTable=$this->newObject('tigramenu','htmlelements');
//            $myTable->width='60%';
//            $myTable->border='1';
//            $myTable->cellspacing='1';
//            $myTable->cellpadding='10';
//
//            $myTable->startHeaderRow();
//            $myTable->addHeaderCell('header1');
//            $myTable->addHeaderCell('header2');
//            $myTable->endHeaderRow();
//
//            $myTable->startRow();
//            $myTable->addCell('cell1');
//            $myTable->addCell('cell2');
//            $myTable->endRow();

        //    echo $myTable->show();
    /**
    * Method to add a menu item under a heading on the menu.
    * @return
    */
//$switchmenu->addMenuItem("dfdfgdfggfgdfgggdfgdfg",'fdgsdfgfsdgsdgsdgsdfgfgs');

$objListallForm = $this->getObject('view_all_messages', 'hosportal');
$objNavigationLinks = $this->getObject('side_other_links', 'hosportal');
$objListMessageOptions = $this->getObject('set_original_message_options', 'hosportal');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
//Add some text to the left column
$cssLayout->setLeftColumnContent($objNavigationLinks->showBuiltSwitchMenu());
//get the editform object and instantiate it
$objEditForm = $this->getObject('editmessage', 'hosportal');


//Add the form to the middle (right in two column layout) area

$objListallForm->setNoOfDesiredMessagesPerPage($noOfMessages);

//$objListallForm->sortMessages($sortOptions);
$objListallForm->setPageNumber($pageNumber);
$cssLayout->setMiddleColumnContent($objListallForm->show());
//$switchmenu = $this->newObject('switchmenu', 'htmlelements');
//$option1 = "option ot";
//$option2 = "option otsdfsdf";
//  $switchmenu->addBlock('Title 1', $option1.' <br />' .$option2.'Block Text 1 <br /> Block Text 1');
//  $switchmenu->addBlock('Title 2', 'Block Text 2 <br /> Block Text 2 <br /> Block Text 2', 'confirm'); // Adds
 // $cssLayout->setLeftColumnContent("Place holder text");
  $cssLayout->setRightColumnContent($objListMessageOptions->showBuiltSwitchMenu());

echo $cssLayout->show();






?>
