<?php

class section_list extends object
{

    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');

        $this->objStories = $this->getObject('dbnewsstories');
        // Load Menu Tools Class
        $this->objMenuTools = $this->getObject('tools', 'toolbar');

        // Permissions Module
        //$this->objDT = $this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        //$this->objDT->create('news');
        // Collect information from the database.
        //$this->objDT->retrieve('news');

        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');

    }


    public function renderSection($category)
    {
        $header = new htmlheading();
        $header->type = 1;
        $header->str = $category['categoryname'];



        $output = $header->show();

        $objTrimString = $this->getObject('trimstr', 'strings');
        $objThumbnails = $this->getObject('thumbnails', 'filemanager');
        $objDateTime = $this->getObject('dateandtime', 'utilities');

        $this->setVar('pageTitle', $category['categoryname']);
        $this->objMenuTools->addToBreadCrumbs(array($category['categoryname']));

        //var_dump ($category);
        if ($category['showintroduction'] == 'Y') {
            $objWashOut = $this->getObject('washout', 'utilities');
            $output .= $objWashOut->parseText($category['introduction']).'<br /><br />';
        }

        $categoryStories = $this->objStories->getCategoryStories($category['id'], str_replace('_', ' ', $category['itemsorder']));

        if (count($categoryStories) == 0) {
            $output .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_news_categorydoesnothavestories', 'news', 'This category does not have any stories yet.').'</div>';
        } else {

            $output .= '<ul>';

            foreach ($categoryStories as $story)
            {
                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                $storyLink->link = $story['storytitle'];

                $storyLink->link = $story['storytitle'];

                $output .= '<li>'.$objDateTime->formatDateOnly($story['storydate']).' - '.$storyLink->show().'</li>';
            }

            $output .= '</ul>';

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