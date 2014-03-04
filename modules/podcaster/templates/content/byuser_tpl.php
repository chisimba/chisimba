<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = new htmlheading();
//Get uploader name
$uploader = $this->objUser->getUserDetails($userId);
$uploaderOfficialName = $uploader['title'].". ".$uploader['surname'].' - '.$this->objLanguage->languageText("mod_podcaster_uploads", "podcaster", 'Uploads');
$heading->str = $uploaderOfficialName;

$altText = $this->objLanguage->languageText("mod_podcaster_rssown", "podcaster", 'RSS your own podcasts');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('rss');
$objIcon->alt = $altText;

$rssLink = new link ($this->uri(array('action'=>'userrss', 'userid'=>$userId)));
$rssLink->link = $objIcon->show();

$heading->str .= ' '.$rssLink->show();

$heading->type = 1;

echo $heading->show();

if (count($files) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText("mod_podcaster_nouploadsbyu", "podcaster", 'You have not Uploaded any files yet. Click on uploads link to upload a podcast').'</div>';
} else {
    $sortOptions = array(
        'datecreated_desc' => $this->objLanguage->languageText("mod_podcaster_newestfirst", "podcaster", 'Newest First'),
        'datecreated_asc' => $this->objLanguage->languageText("mod_podcaster_oldestfirst", "podcaster", 'Oldest First'),
        'title_asc' => $this->objLanguage->languageText("mod_podcaster_alphabetical", "podcaster", 'Alphabetical'),
        'artist_asc' => $this->objLanguage->languageText("mod_podcaster_author", "podcaster", 'Author')
    );

    echo '<p><strong>'.$this->objLanguage->languageText("mod_podcaster_sortby", "podcaster", 'Sort by').':</strong> ';

    $divider = '';
    foreach ($sortOptions as $sortOption=>$optionText)
    {
        if ($sortOption == $sort)
        {
            echo $divider.$optionText;
        } else {
            $sortLink = new link ($this->uri(array('action'=>'myuploads', 'sort'=>$sortOption)));
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
$homeLink->link = $this->objLanguage->languageText("mod_podcaster_backtohome", "podcaster", 'Back to home');

echo '<p>'.$homeLink->show().'</p>';

?>