<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = new htmlheading();

if ($userid == $objUser->userId())
{
    $heading->str = $this->objLanguage->languageText('mod_webpresent_myslides', 'webpresent', 'My Slides');;
} else {
    $text = $this->objLanguage->languageText('mod_webpresent_personsslides', 'webpresent', '[PERSON]\'s Slides');
    $heading->str = stripslashes(str_replace('[PERSON]', $objUser->fullname($userid), $text));
}

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('rss');

$rssLink = new link ($this->uri(array('action'=>'userrss', 'userid'=>$userid)));
$rssLink->link = $objIcon->show();

$heading->str .= ' '.$rssLink->show();


$heading->type = 1;

echo $heading->show();

if (count($files) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_webpresent_userhasnotuploadedfiles', 'webpresent', 'User has not uploaded any files').'.</div>';
} else {
    $sortOptions = array(
        'dateuploaded_desc' => $this->objLanguage->languageText('phrase_newestfirst', 'webpresent', 'Newest First'),
        'dateuploaded_asc' =>  $this->objLanguage->languageText('phrase_oldestfirst', 'webpresent', 'Oldest First'),
        'title_asc' =>  $this->objLanguage->languageText('word_alphabetical', 'webpresent', 'Alphabetical'),
        //'title_desc' => 'Alphabetical Reversed',
    );

    echo '<p><strong>'.$this->objLanguage->languageText('sort_by', 'forum', 'Sort by').':</strong> ';

    $divider = '';
    foreach ($sortOptions as $sortOption=>$optionText)
    {
        if ($sortOption == $sort)
        {
            echo $divider.$optionText;
        } else {
            $sortLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$userid, 'sort'=>$sortOption)));
            $sortLink->link = $optionText;

            echo $divider.$sortLink->show();
        }

        $divider = ' | ';

    }

    echo '</strong></p>';

    $objViewer = $this->getObject('viewer');
    echo $objViewer->displayAsTable($files);


}

$homeLink = new link ($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText('phrase_backhome', 'system', 'Back to home');

echo '<p>'.$homeLink->show().'</p>';

?>