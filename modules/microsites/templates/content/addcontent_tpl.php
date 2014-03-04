<?php
//Add a page template
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('add');

$addLink = new link ($this->uri(array('action'=>'showAddSite')));
$addLink->link = $objIcon->show();

     $objDateTime = $this->getObject('dateandtime', 'utilities');
            $objTrimString = $this->getObject('trimstr', 'strings');
            
            $table = $this->newObject('htmltable', 'htmlelements');
			$table->width= '50%';
            $table->startHeaderRow();            
            $table->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Page Name'));
           // $table->addHeaderCell($this->objLanguage->languageText("mod_microsites_url", "microsites"));          
			$table->addHeaderCell('&nbsp;');
			$table->endHeaderRow();
            
            
            foreach ($pagesArr as $page)
            {
                $link = new link ($this->uri(array('action'=>'editpage','pageid' => $page['id'], 'siteid'=>$this->getParam('siteid'))));
                $link->link = $page['content_title'];
                //Get and set the edit icon
                $objEdIcon = $this->getObject('geticon', 'htmlelements');
	 		    $objEdIcon->setIcon('edit');
                
                //Link to edit using the edit icon
            	$editLink = new link ($this->uri(array('action'=>'editsite', 'id'=>$site['id'])));
                $editLink->link = $objEdIcon->show();
                
                //Get and set the delete icon
                $objIcon = $this->getObject('geticon', 'htmlelements');
	 		    $objIcon->setIcon('delete');
                
                //Link to delete using the delete icon
                $deleteArray = array('action'=>'deletepage', 'id'=>$page['id']);    
                $deleteLink = $objIcon->getDeleteIconWithConfirm($page['id'], $deleteArray, 'microsites');
				
				//add content
				$objIcon->setIcon('addessay');
                $addPageLink = new link ($this->uri(array('action'=>'showAddContent', 'siteid'=>$site['id'])));
                $addPageLink->link = $objIcon->show();
                
                $table->startRow();
               // $table->addCell($page['content_title'], '15%');
                $table->addCell($link->show(),'50%');
              //  $table->addCell($this->objUser->fullName($announcement['createdby']), '20%');
          
                
               // $table->addCell($type, 200);
				
				$table->addCell($deleteLink, '15%');				
                $table->endRow();
                
            }
            
            echo $addLink->show();
            echo $table->show();
            
            //Add content section
            
            $objForm = $this->newObject('form', 'htmlelements');
$objTextBox = $this->newObject('textinput', 'htmlelements');
$objTextBoxUrl = $this->newObject('textinput', 'htmlelements');
$objButton = $this->newObject('button', 'htmlelements');

$objForm->action = $this->uri(array('action' => 'saveaddpage', 'siteid' => $this->getParam("siteid")));
$objForm->displayType = 1;


$htmlArea = $this->newObject('htmlarea', 'htmlelements');
$htmlArea->name = 'content';
$htmlArea->value = "";
$htmlArea->label = $this->objLanguage->languageText('word_content', 'system', 'Page Contents');

$objTextBox->name = 'content_title';
$objTextBox->value = "";
$objTextBox->label = $this->objLanguage->languageText('word_title', 'system', 'Page Title');

$objButton->setToSubmit();
$objButton->value = $this->objLanguage->languageText('word_save', 'system', 'Add Page');

$objForm->addToForm($objTextBox);
$objForm->addToForm($htmlArea);
$objForm->addToForm($objButton);

echo '<br><p>'.$objForm->show().'<p>';
?>


