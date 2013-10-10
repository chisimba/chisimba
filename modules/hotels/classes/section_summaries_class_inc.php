<?php

class section_summaries extends object
{

    public function init()
    {
		$this->objStories = $this->getObject('dbhotelsstories');
        // Load Menu Tools Class
        $this->objMenuTools =& $this->getObject('tools', 'toolbar');
        $this->loadClass('htmlheading', 'htmlelements');
        
        
        // Permissions Module
        $this->objDT = &$this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        $this->objDT->create('news');
        // Collect information from the database.
        $this->objDT->retrieve('news');
        
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    

    public function renderSection($category)
    {
        
        
        
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $category['categoryname'];
        
        
        
        $output = $header->show();
        
        if ($category['showintroduction'] == 'Y') {
            $objWashOut = $this->getObject('washout', 'utilities');
            $output .= $objWashOut->parseText($category['introduction']).'<br /><br />';
        }

        $objTrimString = $this->getObject('trimstr', 'strings');
        $objThumbnails = $this->getObject('thumbnails', 'filemanager');
        $objDateTime = $this->getObject('dateandtime', 'utilities');
        
        $this->setVar('pageTitle', $category['categoryname']);
        $this->objMenuTools->addToBreadCrumbs(array($category['categoryname']));
        
        $categoryStories = $this->objStories->getCategoryStories($category['id'], str_replace('_', ' ', $category['itemsorder']));
        
        if (count($categoryStories) == 0) {
            $output .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_hotels_categorydoesnothavestories', 'hotels', 'This category does not have any stories yet.').'</div>';;
        } else {
            foreach ($categoryStories as $story)
            {
                
                $output .= '<div class="newsstory">';
                
                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                $storyLink->link = $story['storytitle'];
                
                if ($story['storyimage'] != '') {
                    $storyLink->link = '<div class="storyimagewrapper"><img class="storyimage" src="'.$objThumbnails->getThumbnail($story['storyimage'], $story['filename']).'" alt="'.$story['storytitle'].'" title="'.$story['storytitle'].'" /></div>';
                    
                    $output .= $storyLink->show();
                }
                
                $storyLink->link = $story['storytitle'];
                
                $output .= '<h3>'.$objDateTime->formatDateOnly($story['storydate']).' - '.$storyLink->show().'</h3>';
                
                if ($story['location'] != '') {
                    $locationLink = new link ($this->uri(array('action'=>'viewbylocation', 'id'=>$story['storylocation'])));
                    $locationLink->link = $story['location'];
                    //$output .= '[ '.$locationLink->show().'] ';
                    $output .= '[ '.$story['location'].' ] ';
                }
                
                $output .= $objTrimString->strTrim(strip_tags($story['storytext']), 150, TRUE);
                
                $storyLink->link = 'Read More...';
                $output .= ' ('.$storyLink->show().')';
                
                $output .= '</div><br clear="both" />';
            }
        
        }
        
        return $output;
    }
    
    /**
    *
    *
    *
    */
    public function renderPage($story, $category)
    {
        $objRender = $this->getObject('renderstory');
        return $objRender->render($story, $category);
    }

}
?>