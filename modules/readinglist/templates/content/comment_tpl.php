<?php
	//$this->setLayoutTemplate('popuplayout_tpl.php')
	$this->setVar('pageSuppressContainer',TRUE);
	$this->setVar('pageSuppressBanner',TRUE);
	$this->setVar('pageSuppressToolbar',TRUE);
	$this->setVar('suppressFooter',TRUE);
	
    // Load classes.
	$this->loadClass("form","htmlelements");
	$this->loadClass("textarea","htmlelements");
	$this->loadClass("button","htmlelements");
	$this->loadClass("textinput","htmlelements");
	$this->loadClass("label", "htmlelements");
	//$objLabel =& $this->newObject('label', 'htmlelements');
	
	
	$form = new form("comment", 
		$this->uri(array(
	    	
	   		'action'=>'commentconfirm',
	   		'itemid' => $itemid,
	)));
	
	if(isset($showConfirm) && $showConfirm){
	  echo '<p class="confirm">comment saved</p>';
	  
	}
	
	$objTable =& $this->newObject('htmltable','htmlelements');
        $objTable->width='20';
        $objTable->cellspacing='12';
        $objTable->cellpadding='12';
        
		
					
		
		//... Comment text Area
		if(!empty($id)){
    		$commentData = $this->objDbReadingList_comment->listSingle($id); 
            $comment = $commentData[0]['comment'];	
        }else{
            $comment = '';
        }
		$textarea = new textarea("comment", $comment);
        $textarea->size = 100;
        $textarea->scrollbars = 'yes';
        $row = array("<b>".$label=$objLanguage->languageText("mod_readinglist_comment",'readinglist').":</b>".$textarea->show());
		$objTable->addRow($row, 'even');
		
		
    	//Save and Exit button
		$objButton = new button("submit",$objLanguage->languageText("word_save"));
		$objButton->setToSubmit();
		$button = $objButton->show();
		if(!empty($id)){
            $button = '';
        }
		$button1 = new button("exit",$objLanguage->languageText("word_exit"));  //exit_save
	
        $button1->setOnClick("javascript:opener.location.reload();window.close()");
        
		$row = array($button."&nbsp;".$button1->show());
		
		$objTable->addRow($row, 'even');
		
		$form->addToForm($objTable->show());
			
		echo $form->show();
?>	