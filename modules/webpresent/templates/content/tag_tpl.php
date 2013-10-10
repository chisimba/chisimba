<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = new htmlheading();
$heading->str = 'Tag - '.$tag;

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('rss');

$rssLink = new link ($this->uri(array('action'=>'tagrss', 'tag'=>$tag)));
$rssLink->link = $objIcon->show();

$heading->str .= ' '.$rssLink->show();

$heading->type = 1;

echo $heading->show();

if (count($files) == 0) {
    echo '<div class="noRecordsMessage">No files matches this tag</div>';
} else {
    $sortOptions = array(
        'dateuploaded_desc' => 'Newest First',
        'dateuploaded_asc' => 'Oldest First',
        'title_asc' => 'Alphabetical',
        //'title_desc' => 'Alphabetical Reversed',
        'creatorname_asc' => 'User',
        //'creatorname_desc' => 'User Reversed',
    );

    echo '<p><strong>Sort By:</strong> ';

    $divider = '';
    foreach ($sortOptions as $sortOption=>$optionText)
    {
        if ($sortOption == $sort)
        {
            echo $divider.$optionText;
        } else {
            $sortLink = new link ($this->uri(array('action'=>'tag', 'tag'=>$tag, 'sort'=>$sortOption)));
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
$homeLink->link = 'Back to Home';

echo '<p>'.$homeLink->show().'</p>';

?>