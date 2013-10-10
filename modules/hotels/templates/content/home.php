<?php



$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');



$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_hotels_latestnews', 'hotels', 'Latest Hotels');

$middle = $this->objNewsMenu->toolbar('home');

$middle .= $header->show();

$middle .= $topStories;

if (count($categories) > 0) {
    
    $table = $this->newObject('htmltable', 'htmlelements');
    //print_r($topStoriesId);
    $counter = 0;
    foreach ($categories as $category)
    {
        if ($category['blockonfrontpage'] == 'Y') {
            $nonTopStories = $this->objNewsStories->getNonTopStoriesFormatted($category['id'], $topStoriesId);
            if ($nonTopStories != '') {
                
                if ($counter%2 == 0) {
                    $middle .= '<br clear="all" />';
                }
                $middle .= '<div style="width:50%; float:left; "><h3>'.$category['categoryname'].'</h3>';
                $middle .= $nonTopStories.'</div>';
                
                $counter++;
            }
        }
        
    }
}

$middle .= '<br clear="both" />';

echo $middle;

?>