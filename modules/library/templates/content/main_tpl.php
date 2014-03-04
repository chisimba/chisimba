<?php

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);

// Add the heading to the content
$objH =& $this->getObject('htmlheading', 'htmlelements');
$objH->type=3; //Heading <h3>
$objH->str=$objLanguage->languageText("mod_library_title",'library');

//Create an instance of the table object
$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->border = "0";
$objTable->cellpadding = "7";
$objTable->cellspacing = "7";

//Loop through and display the records
$rowcount = 0;
if (isset($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";
            $title = stripslashes($line['title']);
            $description = stripslashes($line['description']);
            $objTable->startRow();
            $objTable->addCell("<b>" . $title
              . "</b>", NULL, "top", "left", $oddOrEven);
            $objTable->endRow();


            $objTable->startRow();
            $objTable->addCell($description, NULL, "top", "left", $oddOrEven);
            $objTable->endRow();

            $link = "<a href=\"" . $line['url'] ."\" target=\"_blank\">"
              . $line['url'] . "</a>";
            $objTable->startRow();
            $objTable->addCell($link, NULL, "top", "left", $oddOrEven);
            $objTable->endRow();

           // Set rowcount for bitwise determination of odd or even
           $rowcount = ($rowcount == 0) ? 1 : 0;

        }
    }
}

$this->objUser =  &$this->getObject("user", "security");

if ($this->objUser->isAdmin() || $this->isValid('admin')){

    $options =  "<br /> <a href=\"" .
    $this->uri(array(
        'module'=>'library',
        'action'=>'admin'
        ))
    . "\">" . $objLanguage->languageText("mod_library_admin",'library') . "</a>";
    $options .=  "&nbsp;";

    $objTable->startRow();
    $objTable->addCell($options, "null", "top", "left", "", null);
    $objTable->endRow();

}

//Create the content for the left & right column
$leftSideColumn = "";
$rightSideColumn = "";

//Instantiate the blocks object
$objBlocks = & $this->newObject('blocks', 'blocks');

//Add a block for about library
$leftSideColumn .= $objBlocks->showBlock('about', 'library');
//Add latest search block
$leftSideColumn .= $objBlocks->showBlock('lastsearch', 'websearch');
//ADd plagiarism block
$leftSideColumn .= $objBlocks->showBlock('plagiarism', 'library');

//Add the explanation to the left layer
$cssLayout->setLeftColumnContent($leftSideColumn);

//Add a block for about google
$rightSideColumn .= $objBlocks->showBlock('abgoogle', 'library');
//Add a block for the google api search
$rightSideColumn .= $objBlocks->showBlock('google', 'websearch');
//Put the google scholar google search
$rightSideColumn .= $objBlocks->showBlock('scholarg', 'websearch');
//Put a wikipedia search
$rightSideColumn .= $objBlocks->showBlock('wikipedia', 'websearch');

//Add the explanation to the left layer
$cssLayout->setRightColumnContent($rightSideColumn);

//Add the table to the middle layer
$cssLayout->setMiddleColumnContent($objH->show() . $objTable->show());

//Output the content to the page
echo $cssLayout->show();

?>
