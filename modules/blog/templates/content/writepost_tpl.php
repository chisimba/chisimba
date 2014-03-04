<?php

// Display dialogue if necessary.
echo $this->objTermsDialogue->show();

//write post template
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$middleColumn = NULL;
//get the posts editor
$middleColumn = $this->objblogPosts->postEditor($userid, NULL);
//geotag part
//$middleColumn .= $this->objblogExtras->geoTagForm();


// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    $cssLayout->setNumColumns(1);
    $cssLayout->setMiddleColumnContent($middleColumn);
    echo $cssLayout->show();
}
?>