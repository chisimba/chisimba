<?php
//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');


// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

//Set the content of the left side column
$leftSideColumn = $this->objLanguage->languagetext("mod_maillist_intro");

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h3>
$this->objH->str=$objLanguage->languageText("mod_maillist_title");
$rightSideColumn = $this->objH->show();

$rightSideColumn .= $objLanguage->languageText("mod_maillist_baduser");
$rightSideColumn .= "<br><BR>";
$this->loadClass('link', 'htmlelements');
$link = new link ($this->URI(array('action'=>''),'_default'));            
$link->link=$objLanguage->languageText("word_login");
$rightSideColumn .= $link->show();
// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
?>