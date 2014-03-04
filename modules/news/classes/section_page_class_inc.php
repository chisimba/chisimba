<?php

class section_page extends object
{

    public function init()
    {
		$this->objStories = $this->getObject('dbnewsstories');
        // Load Menu Tools Class
        $this->objMenuTools =& $this->getObject('tools', 'toolbar');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    * Method to render the section view of category
    * @param array $category
    * @return string
    */
    public function renderSection($category)
    {
        $firstStory = $this->objStories->getFirstStory($category['id'], str_replace('_', ' ', $category['itemsorder']));
        
        if ($firstStory == FALSE) {
            $this->setVar('pageTitle', $category['categoryname']);
            $this->objMenuTools->addToBreadCrumbs(array($category['categoryname']));
            return '<h1>'.$category['categoryname'].'</h1>'.
            '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_news_categorydoesnothavestories', 'news', 'This category does not have any stories yet.').'</div>';
        } else {
            return $this->renderPage($firstStory, $category);
        }
    }
    
    public function renderPage($story, $category)
    {
        $this->setVar('pageId', $story['id']);
        $this->setVar('pageType', 'story');
        $this->setVar('pageTypeId', $story['id']);
        
        $objRender = $this->getObject('renderstory');
        
        $str = $objRender->render($story, $category);
        
        $allTitles = $this->objStories->getCategoryTitles($category['id'], str_replace('_', ' ', $category['itemsorder']));
        
        if (count($allTitles) > 1) {
            $str .= '<br /><p style="text-align:center">';
            $divider = '';
            
            foreach($allTitles as $title)
            {
                if ($title['storyid'] == $story['storyid']) {
                    $str .= $divider.$title['storytitle'];
                } else {
                    $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$title['storyid'])));
                    $storyLink->link = $title['storytitle'];
                    $str .= $divider.$storyLink->show();
                }
                
                $divider = ' | ';
            }
            
            $str .= '</p>';
        }
        
        return $str;
    }

}
?>