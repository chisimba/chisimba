<?php
	
    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("button","htmlelements");
	$this->loadClass("link","htmlelements");
	$this->loadClass("tabbedbox","htmlelements");
	$this->loadClass("htmltable", "htmlelements");
	//$objLabel =& $this->newObject('label', 'htmlelements');
//	$this->loadClass('htmlheading','htmlelements');
	$objHeading1 =& $this->getObject('htmlheading','htmlelements');
	
	//Heading 1
	$heading =$objLanguage->languageText("mod_readinglist_additionals",'readinglist');
	//echo $objHeading->show();
		
        	
	$objTable = new htmltable();
        //$objTable->width='20';
        $objTable->cellspacing='12';
        $objTable->cellpadding='12';
        $row =
 array("<b>".$objLanguage->code2Txt("word_name").":</b>",
        $objUser->fullName());
        $objTable->addRow($row, 'odd');
        $row =
        array("<b>".ucwords($objLanguage->code2Txt("mod_context_context",'context')).":</b>",
        $contextTitle);
        $objTable->addRow($row, 'odd');
        
        
		
		//Heading 2
		
		$heading1 =$objLanguage->languageText("mod_readinglist_additionalcomment",'readinglist');
		
		
		
		
	//... Author text box
        $row = array("<b>".$label=$objLanguage->languageText("mod_readinglist_author",'readinglist').":</b>",$author);
        //$textinput->show());
		$objTable->addRow($row, 'even');

		
    	//Title text box
        $row = array("<b>".$objLanguage->languageText("mod_readinglist_title",'readinglist').":</b>",$title);
        //$textinput->show());
		$objTable->addRow($row, 'even');

		
    	//Publisher text box
        $row = array("<b>".$objLanguage->languageText("mod_readinglist_publisher",'readinglist').":</b>",$publisher);
        //$textinput->show());
		$objTable->addRow($row, 'even');				     

	
    	//Publishing year text box
            $row = array("<b>".$objLanguage->languageText("mod_readinglist_year",'readinglist').":</b>",$publishingYear);
        //$textinput->show());
		$objTable->addRow($row, 'even');
		
          
        //Link text box
        
        $linkList = $this->objDbReadingList_links->getByItem($id);        
        if($linkList){
            foreach($linkList as $key=>$link){
        		$link = "<a href = '".$link['link']."'>".$link['link']."</a>";
                $objTable->startRow();
        		if($key == 0){
                    $objTable->addCell('<b>'.$objLanguage->languageText("mod_readinglist_link",'readinglist').':</b>');
                    $objTable->addCell($link);
                }else{
                    $objTable->addCell('');
                    $objTable->addCell($link);
                }        		
                $objTable->endRow();     
            }
        }else{
            $objTable->startRow();
            $objTable->addCell('<b>'.$objLanguage->languageText("mod_readinglist_link",'readinglist').':</b>');
            $objTable->addCell('');
            $objTable->endRow();     
        }        	
		
		//$row = array('<b>'.$objLanguage->languageText(" ",'readinglist').':</b>');
		//$objTable->addRow($row, 'even');
		
		
	//Publication text box
            $row = array("<b>".$label=$objLanguage->languageText("mod_readinglist_publication",'readinglist').":</b>",$publication);
        //$textinput->show());
		$objTable->addRow($row, 'even');

	
	//... Country text box
	        $row = array("<b>".$label=$objLanguage->languageText("mod_readinglist_country",'readinglist').":</b>",$country);
        //$textinput->show());
		$objTable->addRow($row, 'even');

	
	//... Language text box
        $row = array("<b>".$label=$objLanguage->languageText("mod_readinglist_language",'readinglist').":</b>",$language);
        //$textinput->show());
		$objTable->addRow($row, 'even');
	
	
		//Add reference
        
		$refLink = new link('#');
		$url = $this->uri(array(
                'action'=>'comment',
                'id' => $id
            ));
		$refLink->link = $objLanguage->languageText("phrase_addreference");
		$refLink->extra = "onclick=\"javascript:window.open('{$url}', 'refs', 'width=440, height=200, left=100,top=100,scrollbars = yes');\"";
		$linkWindow = $refLink->show();
	
    	
    	//Back button
		$button = new button("Back",
		$objLanguage->languageText("word_back"));    
		$button->setToSubmit();
		$str = $button->show();
		
		
		// The form
		$form = new form("additionals", 
			$this->uri(array(
		    		'module'=>'readinglist'	
		)));
		$form->addToForm($str);
		
		//creating the tabbed box that contains the additional information table
		$objTab = new tabbedbox();
		$objTab->addTabLabel($heading);
		$objTab->addBoxContent($objTable->show());
		$display = '<p>'.$objTab->show().'</p>';
		
		//creating the tabbed table that will hold the comment to be viewed
		$commentData = $this->objDbReadingList_comment->getByItem($id);
		if(!empty($commentData)){
			$objTab2 = new tabbedbox();
			$objTab2->addTabLabel($heading1);
			$content = '';
			foreach($commentData as $data){
                $content .= '<br />' . $data['comment'];
                if($data['userid'] == $this->userId){
                  
                    $objTable2 =& $this->newObject('htmltable','htmlelements');
                    $objTable2->cellspacing='5';
                    $objTable2->cellpadding='5';
                    $objTable2->startRow();

                    $objTable2->addCell($content."&nbsp;"."&nbsp;"."&nbsp;"."&nbsp;".$data['updated']);
                    
                    $objTable2->endRow();
                    
                }                
            }
            $objTab2->addBoxContent($objTable2->show());
            //$objTab2->addBoxContent($objTable2);
			$display .= '<p>'.$objTab2->show().'</p>';
		}
			
		echo $display;	
		echo '<p>'.$linkWindow.'</p>';
		echo '<p>'.$form->show().'</p>';
?>