<?php
    #$objEditForm = $this->getObject('editform', 'computerscience');
    #echo $objEditForm->show();
    //Get the CSS layout to make two column layout
    $cssLayout = $this->newObject('csslayout', 'htmlelements');
    //Add some text to the left column
    //$htmlSetup = '<table cellspacing=0 cellpadding=0><tr><td><b>Computer Science</b></td></tr><tr><td><center><h4>4</h></center></td></tr><tr><td><b><center><h2>Fun</h2></center></b></td><tr></table>';
    //$cssLayout->setLeftColumnContent($htmlSetup);
    //get the editform object and instantiate it
    $objEditForm = $this->getObject('editform', 'computerscience');
    //Add the form to the middle (right in two column layout) area
    $cssLayout->setMiddleColumnContent($objEditForm->show());
    return $cssLayout->show();
?>