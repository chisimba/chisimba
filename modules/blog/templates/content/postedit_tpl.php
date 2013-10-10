<?php
//post edit template
$cssLayout = $this->newObject('csslayout', 'htmlelements');
$middleColumn = NULL;
//get the posts editor
$middleColumn = $this->objblogPosts->postEditor($userid, $editid);
// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');
if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    // DWK changed editor to single column 2011 04 15
    $cssLayout->setNumColumns(1);
    $cssLayout->setMiddleColumnContent($middleColumn);
    echo $cssLayout->show();
}
?>