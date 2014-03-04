<?php



// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);
//Add the table to the centered layer
$rightSideColumn =  $this->utils->downloadfile();

$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent( $rightSideColumn);

//Output the content to the page
echo $cssLayout->show();

?>
