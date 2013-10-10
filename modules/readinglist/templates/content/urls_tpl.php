<?php
		//$this->setLayoutTemplate('popuplayout_tpl.php')
		$this->setVar('pageSuppressContainer',TRUE);
		$this->setVar('pageSuppressBanner',TRUE);
		$this->setVar('pageSuppressToolbar',TRUE);
		$this->setVar('suppressFooter',TRUE);
		
		//Load Classes
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('label', 'htmlelements');
		$this->loadClass('link', 'htmlelements');

    // Show the heading
    $objHeading =& $this->getObject('htmlheading','htmlelements');
    $objHeading->type=1;
    $objHeading->str =$objLanguage->languageText("mod_readinglist_link", 'readinglist')." ".$contextTitle;
    echo $objHeading->show();
    $form = new form("urls", 
		$this->uri(array(
	    		'module'=>'readinglist',
	   		'action'=>'urlsConfirm',
			'id'=>$id
	)));
    //echo "<br/>";
    // Create a table object
    $table =& $this->newObject("htmltable","htmlelements");
    $table->border = 0;
    $table->cellspacing='12';
    $table->cellpadding='12';
    $table->width = "100%";
    // Add the table heading.
    $table->startRow();
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_link",'readinglist')."</b>");
  
    
    $table->addHeaderCell("<b>".$objLanguage->languageText("mod_readinglist_action",'readinglist')."</b>");
    $table->endRow();

$objTable =& $this->newObject('htmltable','htmlelements');
        $objTable->width='20';
        $objTable->attributes=" align='left' border='0'";
        $objTable->cellspacing='12';
        $objTable->cellpadding='12';
        
        
        
        $urlFieldset = &$this->newObject('fieldset','htmlelements');
        // Create Header Tag ' Website Links
		$this->urlLinks =& $this->getObject('htmlheading', 'htmlelements');
		$this->urlLinks->type=3;
		$this->urlLinks->str=$this->objLanguage->languageText('mod_readinglist_websitelinks', 'readinglist');
		
		$urlFieldset->addContent($this->urlLinks->show());


		
		$urlNum = 0;

		if (count($urls) == 0) 
		{
			$urlFieldset->addContent ('<ul><li>'.$this->objLanguage->languageText('mod_readinglist_noUrlsFound', 'readinglist').'. </li></ul>');
			
		} else {
			
			$urlFieldset->addContent ('<ul>');	
			
			if(!empty($urls))
			{
		    foreach ($urls as $element) {
		        if(!empty($element['link'])){
				  $urlFieldset->addContent ('<li><p>');
				
				  $itemLink = new link($element['link']);
				  $itemLink->target = '_blank';
				  $itemLink->link =$element['link'];
				
				  $urlFieldset->addContent( $itemLink->show());
				
				  $urlFieldset->addContent( ' - ' );
		
				  // URL Delete Link
				  $confirmDelete = $this->objLanguage->languageText('mod_readinglist_pop_deleteurl','readinglist');
				  $deleteLinkIcon =& $this->newObject('geticon', 'htmlelements');
				  
					$linkArr = array(
								'module'=>'readinglist', 
								'action'=>'deleteurl', 
								'urlId'=>$element['id'] ,
								'id'=>$element['itemid'] ,
							);
				  $delIcon = $deleteLinkIcon->getDeleteIconWithConfirm('', $linkArr, 'readinglist', $confirmDelete);
		
		
				  $urlFieldset->addContent ($delIcon);
				  $urlFieldset->addContent ('</p></li>');
				}
		
			}
			
			$urlFieldset->addContent ('</ul>');
		  }
		  /*else
		  {
             return $urls;
          }*/
		}
        //start of Form
        
        $addUrlForm = new form('addWord', $this->uri(array(
		'module'=>'readinglist', 
		'action'=>'urlsconfirm',
		'id'=>$id
		)));

	  	  
        $hiddenIdInput = new textinput('id');
		$hiddenIdInput->fldType = 'hidden';
		$hiddenIdInput->value = $id;
		$addUrlForm->addToForm($hiddenIdInput->show());
		
     	$urlInput = new textinput('url');
		$urlInput->size = 30;
		$urlInput->extra = ' title ="asfas"';
		$urlInput->value = 'http://';


		$urlLabel = new label($this->objLanguage->languageText('mod_readinglist_addUrl', 'readinglist'), 'input_url');
		$addUrlForm->addToForm($urlLabel->show().':', null);
		$addUrlForm->addToForm(' &nbsp; '.$urlInput->show());
		
		$submitButton = new button('submit', $this->objLanguage->languageText('mod_readinglist_addUrl', 'readinglist'));
		$submitButton->setToSubmit();

		
		$addUrlForm->addToForm(' &nbsp; '.$submitButton->show());
		$addUrlForm->displayType =3;

		$urlFieldset->addContent( $addUrlForm->show());


		echo $urlFieldset->show();
	
	

    	//Close button
	$button = new button("close",
	$objLanguage->languageText("word_close"));    //word_close
	$button->setOnClick("javascript:window.close()");
	$row = array($button->show());
	$objTable->addRow($row, 'even');
	$form->addToForm($objTable->show());
	echo $form->show();
?>