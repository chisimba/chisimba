<div style="width: 59%; float: left;">
<?php	
$this->loadClass('link', 'htmlelements');
	
    echo "<h1>" . $objLanguage->languageText('mod_homepage_heading', 'homepage') /* . " " . $this->objUser->fullName()*/ . "</h1>";
	//echo "[".($exists?'true':'false')."]";

	echo  '<p align="center"><img src="modules/homepage/resources/homepages.gif"></img></p>';

	//ADDING functionality for alphabetic search
	$objAlphabet=& $this->getObject('alphabet','navigation');
	$linkarray=array('action'=>'ListUsers','how'=>'surname','searchField'=>'LETTER');
	$url=$this->uri($linkarray,'homepage');        
	echo 
		'<p>'
		.$this->objLanguage->languageText('mod_homepage_browsebysurname','homepage')
		.'<br /> '
		.$objAlphabet->putAlpha($url,TRUE,$this->objLanguage->languageText('mod_homepage_listallusers','homepage')).'</p>';	



   echo "<ul>";
	// Edit homepage.
	echo "<li><a href=\"" . 
		$this->uri(array(
			'module'=>'homepage',
			'action'=>'edithomepage',
			'userId'=>$objUser->userId()
		))	
	. "\">".$objLanguage->languageText("mod_homepage_edit", 'homepage')."</a></li>";	
	// View homepage.
	if ($exists) {
		echo "<li><a href=\"" . 
			$this->uri(array(
				'module'=>'homepage',
				'action'=>'viewhomepage',
				'userId'=>$objUser->userId()
			))	
		. "\">".$objLanguage->languageText("mod_homepage_view", 'homepage')."</a></li>";
		// Delete the homepage
        $objConfirm = $this->newObject('confirm','utilities');
        $objConfirm->setConfirm(
			$objLanguage->languageText("mod_homepage_delete", 'homepage'), 
			$this->uri(array(
				'action'=>'deletehomepage'
			)),
			$objLanguage->languageText("mod_homepage_confirmdelete", 'homepage')
		);
		echo "<li>".$objConfirm->show()."</li>";
	}
	//echo "<br/>";
    echo "</ul>";

    if ($exists) {
        echo $this->objdBHomePages->show(NULL, FALSE);
    }
    
echo '</div>';
echo '<div style=" width: 40%; float: left;">';


	//Show countries visitors came from
	$this->objH =& $this->getObject('htmlheading', 'htmlelements');
	$this->objH->type=3; //Heading <h3>
	//$this->objH->str=($objLanguage->code2Txt("mod_homepage_linkstohomepages"));
    $this->objH->str=$objLanguage->languageText('mod_homepage_tenmostviewedhomepages', 'homepage');
	$content = $this->objH->show();
	echo $content;
	
	//Showing the links to homepages of users that have homepages
    $homePageListTable = $this->getObject('htmltable', 'htmlelements');
    
    $homePageListTable->startHeaderRow();
    $homePageListTable->cellpadding = '5';
    $homePageListTable->cellspacing = '1';
    $homePageListTable->addHeaderCell($objLanguage->languageText('mod_homepage_wordhomepage', 'homepage'));
    $homePageListTable->addHeaderCell($objLanguage->languageText('word_hits'));
    
    $homePageListTable->endHeaderRow();
    
    
    foreach ($listHomePages as $homePage)
    {
        $homePageListTable->startRow();
        $homepageLink = new link($this->uri(array('action'=>'viewhomepage', 'userId'=>$homePage['userid'])));
        $homepageLink->link = $homePage['firstname'].' '.$homePage['surname'];
        
        $homePageListTable->addCell($homepageLink->show());
        $homePageListTable->addCell($homePage['visitors']);
        
        $homePageListTable->endRow();
    }
    
    
    
    echo $homePageListTable->show();
    
    if (count($listHomePages) > 10) {
        $viewAllLink = new link ($this->uri(array('action'=>'viewlist')));
        $viewAllLink->link = $objLanguage->languageText('mod_homepage_viewlistofallhomepages', 'homepage');
        echo '<p align="center">'.$viewAllLink->show().'</p>';
    }
echo '</div>';

?>
