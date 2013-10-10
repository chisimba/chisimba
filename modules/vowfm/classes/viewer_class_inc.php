<?php
class viewer extends object {
    function init() {
        $this->loadClass("link","htmlelements");

    }

    function createOnAirNowNav() {
        $objCategories=$this->getObject("dbnewscategories","news");
        $news=$this->getObject("dbnewsstories","news");
        $categories=$objCategories->getCategories();
        $currentShow="No show has been set up";
        $link=new link($this->uri(array("action"=>"viewonairshow")));
        foreach ($categories as $cat) {

            if($cat['categoryname'] == 'on air now') {
                $onAirNowId=$cat['id'];
                $onAirNowStories=$news->getCategoryStories($onAirNowId);
                $currentShow=$onAirNowStories[0]['storytext'];
            }
        }
        $link->link=$currentShow;
        return $link->show();
    }
    function createTopNavAdverts() {
        $objCategories=$this->getObject("dbnewscategories","news");
        $news=$this->getObject("dbnewsstories","news");
        $categories=$objCategories->getCategories();
        $topNavAdverts="No adverts have been set up";
        $link=new link($this->uri(array("action"=>"topnavadverts")));
        foreach ($categories as $cat) {

            if($cat['categoryname'] == 'topnavadverts') {
                $topNavAdvertsId=$cat['id'];
                $topNavAdvertsStories=$news->getCategoryStories($topNavAdvertsId);
                $topNavAdverts=$topNavAdvertsStories[0]['storytext'];
            }
        }
        $link->link=$topNavAdverts;
        return $link->show();
    }


    function getNews($category) {
        $objCategories=$this->getObject("dbnewscategories","news");
        $news=$this->getObject("dbnewsstories","news");
        $categories=$objCategories->getCategories();
        foreach ($categories as $cat) {

            if($cat['categoryname'] == $category) {
                $catId=$cat['id'];
                return $newsStories=$news->getCategoryStories($catId);

            }
        }

        return array();
    }

    function getBottomBlocks() {
        $objFeatureBox = $this->newObject ( 'featurebox', 'navigation' );
        $objCategories=$this->getObject("dbnewscategories","news");
        $news=$this->getObject("dbnewsstories","news");
        $categories=$objCategories->getCategories();
        $bottomcontent="<table>";
        $bottomcontent.="<tr>";
        $index=0;
        foreach ($categories as $cat) {

            if($cat['categoryname'] == 'bottomblocks') {
                $catId=$cat['id'];
                $stories=$news->getCategoryStories($catId);
                foreach($stories as $story) {
                    $title=$story['storytitle'];
                    $link=new link($this->uri(array("action"=>"viewstory","storyid"=>$competition['id'])));
                    $link->link=$story['storytext'];
                    $content=$link->show();
                    $block="bottomblocks".$index++;
                    $hidden='default';
                    $showToggle=true;
                    $showTitle=true;
                    $cssClass="featurebox";
                    $storyRendered=$objFeatureBox->show (
                            $title,
                            $content,
                            $block,
                            $hidden,
                            $showToggle,
                            $showTitle,
                            $cssClass,'');
                    $bottomcontent.='<td valign="top">'.$storyRendered.'</td>';
                }

            }
        }
        $bottomcontent.="</tr>";
        $bottomcontent.="</table>";
        return $bottomcontent;
    }
}

?>
