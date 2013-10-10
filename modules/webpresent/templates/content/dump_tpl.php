
<?php

    // set up html elements
    $this->objLanguage =& $this->getObject('language','language');
    $objHead=$this->newObject('htmlheading','htmlelements');
    $table = $this->newObject('htmltable', 'htmlelements');
    $this->loadclass('link','htmlelements');

    $table->cellpadding = 5;
    $table->cellpadding = 5;

    // Create an instance of the css layout class
    $cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
    $cssLayout->setNumColumns(2);




    $objHead->type=2;
    $objHead->str=$title;

    //Set the content of the left side column
    $leftSideColumn = $desc;
    // Add Left column
    $cssLayout->setLeftColumnContent($leftSideColumn);
    $rightSideColumn = "<div align=\"left\">" . $objHead->show() . "</div>";


        
    $table->startRow();
    $table->addCell($content);
        
    $table->endRow();

    $this->objLink = new link($this->uri(array('action'=>'default')));
    $this->objLink->title='Home';


    $table->startRow();
    $table->addCell($this->objLink->show());
    $table->endRow();

    //Add the table to the centered layer
    $rightSideColumn .= $table->show();

    // Add Left column
    $cssLayout->setLeftColumnContent($leftSideColumn);

    // Add Right Column
    $cssLayout->setMiddleColumnContent($rightSideColumn);

    //Output the content to the page
    echo $cssLayout->show();
?>
