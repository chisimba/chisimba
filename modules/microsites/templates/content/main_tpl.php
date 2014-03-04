<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('add');

$addLink = new link ($this->uri(array('action'=>'showAddSite')));
$addLink->link = $objIcon->show();

     $objDateTime = $this->getObject('dateandtime', 'utilities');
            $objTrimString = $this->getObject('trimstr', 'strings');
            
            $table = $this->newObject('htmltable', 'htmlelements');
			$table->width= '80%';
            $table->startHeaderRow();            
            $table->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Site Name'));
            $table->addHeaderCell($this->objLanguage->languageText("mod_microsites_url", "microsites"));          
			$table->addHeaderCell('&nbsp;');
			$table->endHeaderRow();
            
            
            foreach ($sitesArr as $site)
            {
                $link = new link ($site['url']);
                $link->link = $site['site_name'];
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
                $deleteArray = array('action'=>'deletesite', 'id'=>$site['id']);    
                $deleteLink = $objIcon->getDeleteIconWithConfirm($site['id'], $deleteArray, 'microsites');
				
				//add content
				$objIcon->setIcon('addessay');
                $addPageLink = new link ($this->uri(array('action'=>'showAddContent', 'siteid'=>$site['id'])));
                $addPageLink->link = $objIcon->show();
                
                $table->startRow();
                $table->addCell($site['site_name'], '15%');
                $table->addCell($link->show(),'50%');
              //  $table->addCell($this->objUser->fullName($announcement['createdby']), '20%');
          
                
               // $table->addCell($type, 200);
				
				$table->addCell($addPageLink->show()."&nbsp;&nbsp;".$editLink->show()."&nbsp;&nbsp;".$deleteLink, '15%');				
                $table->endRow();
                
            }
            
            echo $addLink->show();
            echo $table->show();

?>




