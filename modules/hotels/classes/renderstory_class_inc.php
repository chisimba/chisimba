<?php

class renderstory extends object
{

    public function init()
    {
		$this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
		$this->loadClass('htmlheading', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
		
		
        // Load Menu Tools Class
        $this->objMenuTools = $this->getObject('tools', 'toolbar');
		
		// Permissions Module
        $this->objDT = $this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        $this->objDT->create('news');
        // Collect information from the database.
        $this->objDT->retrieve('news');
		
		$this->objIcon = $this->newObject('geticon', 'htmlelements');
    }
    
    public function render($story, $category, $beforeOtherStuff='')
	{
		$objDateTime = $this->getObject('dateandtime', 'utilities');
        
        $categoryLink = new link ($this->uri(array('action'=>'viewcategory', 'id'=>$category['id'])));
        $categoryLink->link = $category['categoryname'];
        
        $this->objMenuTools->addToBreadCrumbs(array($categoryLink->show(), $story['storytitle']));
        
        //$this->setVar('pageTitle', $story['storytitle']);
        
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $story['storytitle'];
        $this->setVar('pageTitle', $story['storytitle']);
        
        if ($this->objDT->isValid('editstory')) {
            $this->objIcon->setIcon('edit');
            $this->objIcon->alt = $this->objLanguage->languageText('mod_hotels_editstory', 'hotels', 'Edit Hotel');
            $this->objIcon->title = $this->objLanguage->languageText('mod_hotels_editstory', 'hotels', 'Edit Hotel');
            $editLink = new link ($this->uri(array('action'=>'editstory', 'id'=>$story['id'])));
            $editLink->link = $this->objIcon->show();
            
            $header->str .= ' '.$editLink->show();
        }
        
        if ($this->objDT->isValid('deletestory')) {
            $this->objIcon->setIcon('delete');
            $this->objIcon->alt = $this->objLanguage->languageText('mod_hotels_deletestory', 'hotels', 'Delete Hotel');
            $this->objIcon->title = $this->objLanguage->languageText('mod_hotels_deletestory', 'hotels', 'Delete Hotel');
            $editLink = new link ($this->uri(array('action'=>'deletestory', 'id'=>$story['id'])));
            $editLink->link = $this->objIcon->show();
            
            $header->str .= ' '.$editLink->show();
        }
        
        
        
        $str = $header->show();
        
        $str .= '<p>'.$objDateTime->formatDateOnly($story['storydate']).'</p>';
        
        /*
        if ($story['storyimage'] != '') {
            $objThumbnails = $this->getObject('thumbnails', 'filemanager');
            $str .= '<img class="storyimage" src="'.$objThumbnails->getThumbnail($story['storyimage'], $story['filename']).'" alt="'.$story['storytitle'].'" title="'.$story['storytitle'].'" />';
        }
        */

        $objWashOut = $this->getObject('washout', 'utilities');

        $str .= $objWashOut->parseText($story['storytext']);

        if ($story['storysource'] != '') {
            $objUrl = &$this->getObject('url', 'strings'); 
            
            $source = $story['storysource'];
            
            $source = $objUrl->makeClickableLinks(htmlentities($source));
            
            $str .= '<p><strong>'.$this->objLanguage->languageText('word_source', 'word', 'Source').':</strong><br />'.$source.'</p>';
        }
        
        $str .= $beforeOtherStuff;
        
        if ($category['showsocialbookmarking'] == 'Y') {
            $objSocialBookmarking = $this->getObject('socialbookmarking', 'utilities');
            
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->startRow();
            $table->addCell($objSocialBookmarking->diggThis());
            $table->addCell($objSocialBookmarking->show(), NULL, NULL, 'center');
            $table->endRow();
            
            $str .= $table->show();
        }
        
       
        
        $objNewsStories = $this->getObject('dbhotelsstories');
        $objKeywords = $this->getObject('dbhotelskeywords');
        
        $rightContent = '';
        $rightContent .= $objNewsStories->getRelatedStoriesFormatted($story['id'], $story['storydate'], $story['datecreated']);
        $rightContent .= $objKeywords->getStoryKeywordsBlock($story['id']);
        
        $objNewsBlocks = $this->getObject('dbhotelsblocks');
        $objNewsBlocks->getBlocksAndSendToTemplate('story', $story['id']);
        
        // Send to Layout Template
        $this->setVar('rightContent', $rightContent);
        
        return $str;
    }


}
?>