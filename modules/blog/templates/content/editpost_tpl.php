<?php

// Display dialogue if necessary.
echo $this->objTermsDialogue->show();

//write post template
$objUi = $this->getObject('blogui');
// left hand blocks
$leftCol = $objUi->leftBlocks($userid);
// right side blocks
$rightSideColumn = $objUi->rightBlocks($userid, NULL);
$cssLayout = $this->newObject('csslayout', 'htmlelements');
if ($leftCol == NULL || $rightSideColumn == NULL) {
    $cssLayout->setNumColumns(2);
} else {
    $cssLayout->setNumColumns(3);
}
$objSideBar = $this->newObject('sidebar', 'navigation');
//get the posts manager
$middleColumn = $this->objblogPosts->managePosts($userid);
//echo $poststoed;
$objPagination = $this->getObject('pagination', 'navigation');
/*$objPagination->id = 'blogposts';
		$objPagination->module = 'blog';
		$objPagination->action = 'adminpg';
		$objPagination->numPageLinks = (2);
		$objPagination->extra = array('userid'=> $this->objUser->userId());
		$middleColumn = $objPagination->show();
*/

// Added by Tohir - Standard layout for elearn
$layoutToUse = $this->objSysConfig->getValue('blog_layout', 'blog');

if ($layoutToUse == 'elearn') {
    $this->setLayoutTemplate('blogelearn_layout_tpl.php');
    echo $middleColumn;
} else {
    if ($leftCol == NULL) {
        $leftCol = $rightSideColumn;
        $cssLayout->setMiddleColumnContent($middleColumn);
        $cssLayout->setLeftColumnContent($leftCol);
        //$cssLayout->setRightColumnContent($rightSideColumn);
        echo $cssLayout->show();
    } elseif ($rightSideColumn == NULL) {
        $cssLayout->setMiddleColumnContent($middleColumn);
        $cssLayout->setLeftColumnContent($leftCol);
        echo $cssLayout->show();
    } else {
        $cssLayout->setMiddleColumnContent($middleColumn);
        $cssLayout->setLeftColumnContent($leftCol);
        $cssLayout->setRightColumnContent($rightSideColumn);
        echo $cssLayout->show();
    }
}
?>
