<?php

class section_previous extends object
{

    public function init()
    {
		$this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
        $this->objStories = $this->getObject('dbhotelsstories');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    

    public function renderSection($category)
    {
        $story = $this->objStories->getFirstStory($category['id'], str_replace('_', ' ',$category['itemsorder']));
        
        return $this->renderPage($story, $category);
        
        
    }
    
    public function renderPage($story, $category)
    {
        if ($story == FALSE) {
            $str = '<h1>'.$category['categoryname'].'</h1>';
            $str .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_hotels_categorydoesnothavestories', 'hotels', 'This category does not have any hotels yet.').'</div>';
        } else {
            
            $str = '';
            $this->setVar('pageId', $story['id']);
            
            $nextItems = $this->objStories->getNextItem($story['id'], $category['id'], $category['pagination']);
            
            if (count($nextItems) > 0 && $nextItems != FALSE) {
                $str .= '<h4>Previous Stories</h4>';
                $str .= '<ul>';
                foreach($nextItems as $item)
                {
                    $link = new link ($this->uri(array('action'=>'viewstory', 'id'=>$item['id'])));
                    $link->link = $item['storytitle'];
                    $str .= '<li>'.$item['storydate'].' - '.$link->show().'</li>';
                }
                $str .= '</ul>';
            }
            
            $objRender = $this->getObject('renderstory');
            $str = $objRender->render($story, $category, $str);
        }
        
        return $str;
    }

}
?>