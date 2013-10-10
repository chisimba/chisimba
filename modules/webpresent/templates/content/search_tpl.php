<?php

if (trim($query) == '')
{
    echo '<h1>Search</h1>';
} else {
    echo '<h1>Search Results for <em>'.$query.'</em></h1>';
}


$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objDateTime = $this->getObject('dateandtime', 'utilities');

$form = new form ('searchform', $this->uri(array('action'=>'search')));
$form->method = 'GET';

$module = new hiddeninput('module', 'webpresent');
$form->addToForm($module->show());

$action = new hiddeninput('action', 'search');
$form->addToForm($action->show());

$textinput = new textinput ('q');
$textinput->value = $this->getParam('q');
$textinput->size = 60;
$button = new button ('search', 'Search');
$button->setToSubmit();

$form->addToForm($textinput->show().' '.$button->show());

echo $form->show();

if (trim($query) != '')
{

    $this->setVar('pageTitle', $this->objConfig->getSiteName().' - Search Results for '.$query);

    $objSearchResults = $this->getObject('searchresults', 'search');

    if($query == '*'){
        $query='';
    }
    $results = $objSearchResults->getSearchResults($query, 'webpresent');
    
    $numHits = count($results);

    $resultText = ($numHits == 1) ? 'Result' : 'Results';


    echo "Found $numHits $resultText for <strong>$query</strong> <br /><br />";

    if ($numHits > 0)
    {

        //echo '<pre>';
        $objViewer = $this->getObject('viewer');
        $objTrim = $this->getObject('trimstr', 'strings');

        $counter = 0;

        $table = $this->newObject('htmltable', 'htmlelements');

        foreach($results as $hit)
        {


            $counter++;

            if ($counter > 1)
            {
                $table->startRow();
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();
            }


            $id = str_replace('webpresent_', '', $hit->docid);

            //echo " " . $hit->title . "<br /> at URL " . "<a href=\"{$hit->url}\">$hit->url</a> " . "with relevance score of " . $hit->score . "<br /><br />".$hit->docid.' - '.$hit->teaser.'<hr />';

            $table->startRow();
            $table->addCell($counter.'.', 20, 'top', 'center');

            $imageLink = new link ($hit->url);
            $imageLink->link = $objViewer->getPresentationThumbnail($id, $hit->title);

            $table->addCell($imageLink->show(), 120);
            $table->addCell('&nbsp;', 20, 'top', 'center');

            $textLink = new link ($hit->url);
            $textLink->link = htmlentities($hit->title);

            $rightContent = '<p><strong>'.$textLink->show().'</strong><br />';

            if (trim($hit->teaser) == '') {
                $description = '<em>File has no description</em>';
            } else {
                $description = nl2br(htmlentities($objTrim->strTrim($hit->teaser, 200)));
            }

            $userLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$hit->userId)));
            $userLink->link = $objUser->fullName($hit->userId);

            $rightContent .= $description.'<br /><strong>Score:</strong> '.$hit->score.'<br /><strong>Uploaded By:</strong> '.$userLink->show().'<br /><strong>Date Uploaded:</strong> '.$objDateTime->formatDate($hit->date);

            $table->addCell($rightContent);

            $table->endRow();


        }

        echo $table->show();
    }

}
?>