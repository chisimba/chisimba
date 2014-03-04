<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//$objListallForm = $this->getObject('view_all_messages', 'hosportal');

$objListMessageOptions = $this->getObject('set_message_options', 'hosportal');
$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
$sortOptions= $this->getParam("sortOptions");
//$this->setVar('sortOptions', $sortOptions);

$objListSortedMessages->sortMessages($sortOptions);
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
//Add some text to the left column
//$cssLayout->setLeftColumnContent("Place holder text");
//get the editform object and instantiate it
//$objEditForm = $this->getObject('editmessage', 'hosportal');


//Add the form to the middle (right in two column layout) area
$cssLayout->setMiddleColumnContent($objListSortedMessages->show());
  $cssLayout->setRightColumnContent($objListMessageOptions->show());

echo $cssLayout->show();





//$objListallForm = $this->getObject('view_all_messages', 'hosportal');
//$objListMessageOptions = $this->getObject('set_message_options', 'hosportal');
//$cssLayout = $this->newObject('csslayout', 'htmlelements');
//$cssLayout->setNumColumns(3);
////Add some text to the left column
//$cssLayout->setLeftColumnContent("Place holder text");
////get the editform object and instantiate it
//$objEditForm = $this->getObject('editmessage', 'hosportal');
//
//
////Add the form to the middle (right in two column layout) area
//$cssLayout->setMiddleColumnContent($objListallForm->show());
//$switchmenu = $this->newObject('switchmenu', 'htmlelements');
//$option1 = "option ot";
//$option2 = "option otsdfsdf";
//  $switchmenu->addBlock('Title 1', $option1.' <br />' .$option2.'Block Text 1 <br /> Block Text 1');
//  $switchmenu->addBlock('Title 2', 'Block Text 2 <br /> Block Text 2 <br /> Block Text 2', 'confirm'); // Adds
// // $cssLayout->setLeftColumnContent("Place holder text");
//  $cssLayout->setRightColumnContent($objListMessageOptions->show());
//
//echo $cssLayout->show();
?>
