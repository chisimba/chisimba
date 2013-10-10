<?php

class section_thumbnails extends object
{

    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');

        $this->objStories = $this->getObject('dbnewsstories');
        // Load Menu Tools Class
        $this->objMenuTools =& $this->getObject('tools', 'toolbar');
        $this->loadClass('htmlheading', 'htmlelements');


        // Permissions Module
        //$this->objDT = &$this->getObject( 'decisiontable','decisiontable' );
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
            $output .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_news_categorydoesnothavestories', 'news', 'This category does not have any stories yet.').'</div>';;
        } else {

            $this->objIcon->setIcon('imagepreview');
            $noImage = $this->objIcon->show();

            foreach ($categoryStories as $story)
            {

                $output .= '<div class="newsblock">';

                $storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));

                if ($story['storyimage'] != '') {
                    $storyImage = '<img  src="'.$objThumbnails->getThumbnail($story['storyimage'], $story['filename']).'" alt="'.$story['storytitle'].'" title="'.$story['storytitle'].'" />';
                } else {
                    $storyImage = $noImage;
                }

                $storyLink->link = '<div class="newsblockimage">'.$storyImage.'</div>'.$story['storytitle'];

                $output .= $storyLink->show();

                $output .= '</div>';
            }
        }

        return $output;
    }

    public function renderPage($story, $category)
    {
        $objRender = $this->getObject('renderstory');

        return $objRender->render($story, $category);
    }

}
?>