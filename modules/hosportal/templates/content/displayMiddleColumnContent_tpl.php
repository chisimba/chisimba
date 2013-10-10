<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//$objListallForm = $this->getObject('view_all_messages', 'hosportal');
$objNavigationLinks = $this->getObject('side_other_links', 'hosportal');
////$objListMessageOptions = $this->getObject('set_original_message_options', 'hosportal');
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
//Add some text to the left column
$cssLayout->setLeftColumnContent($objNavigationLinks->showBuiltSwitchMenu());
$cssLayout->setMiddleColumnContent($theData);

echo $cssLayout->show();
?>
