<?php
//input template
//$cssLayout = &$this->newObject('csslayout', 'htmlelements');
//$objSideBar = $this->newObject('sidebar', 'navigation');
// Set columns to 3
//$cssLayout->setNumColumns(3);
//$leftMenu = NULL;

//$rightSideColumn = NULL;
//$middleColumn = NULL;
//$leftCol = NULL;
$output = NULL;

if($this->objUser->isLoggedIn() == FALSE)
{
	$objLogin = & $this->getObject('logininterface', 'security');
	$objFeatureBox = $this->getObject('featurebox', 'navigation');
	$output .= $objFeatureBox->show($this->objLanguage->languageText("word_login", "system"), $objLogin->renderLoginBox());
}
else {
	$this->objComApi = $this->getObject('commentapi');
	$output = $this->objComApi->commentAddForm('init_56', 'blog', 'tbl_blog_posts', TRUE, TRUE, TRUE);
}

//$middleColumn .= "<h1><em><center>" . $errmsg . "</center></em></h1>";
//$middleColumn .= "<br />";

//$cssLayout->setMiddleColumnContent($middleColumn);
//$cssLayout->setLeftColumnContent($leftCol); //$leftMenu->show());
//$cssLayout->setRightColumnContent($rightSideColumn);
echo $output;
?>