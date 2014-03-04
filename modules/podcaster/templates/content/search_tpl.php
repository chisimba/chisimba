<?php

if (trim($query) == '') {
    echo '<h1>' . $this->objLanguage->languageText("mod_podcaster_search", "podcaster", 'Search') . '</h1>';
    echo '<p>' . $this->objLanguage->languageText("mod_podcaster_searchparams", "podcaster", 'Search podcasts by description, title, artist, file name or tag') . '</p>';
} else {
    echo '<h1>' . $this->objLanguage->languageText("mod_podcaster_searchresultsfor", "podcaster", 'Search results for') . '<em> ' . $query . '</em></h1>';
}

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objDateTime = $this->getObject('dateandtime', 'utilities');

$form = new form('searchform', $this->uri(array('action' => 'search')));
$form->method = 'GET';

$module = new hiddeninput('module', 'podcaster');
$form->addToForm($module->show());

$action = new hiddeninput('action', 'search');
$form->addToForm($action->show());

$textinput = new textinput('q');
$textinput->value = $this->getParam('q');
$textinput->size = 60;
$button = new button('search', 'Search');
$button->setToSubmit();

$form->addToForm($textinput->show() . ' ' . $button->show());

echo $form->show();

if (trim($query) != '') {

    $this->setVar('pageTitle', $this->objConfig->getSiteName() . ' -  ' . $this->objLanguage->languageText("mod_podcaster_searchresultsfor", "podcaster", 'Search results for') . $query);


    if ($query == '*') {
        $query = '';
    }

    $results = $this->objMediaFileData->searchFileInAllFields("all", $query, '1');

    $numHits = count($results);

    $displayResults = $this->objViewer->displayAsTable($results);


    $resultText = ($numHits == 1) ? $this->objLanguage->languageText("mod_podcaster_result", "podcaster", 'Result') : $this->objLanguage->languageText("mod_podcaster_results", "podcaster", 'Results');

    echo $this->objLanguage->languageText("mod_podcaster_found", "podcaster", 'Found') . ' ' . $numHits . ' ' . $resultText . ' ' . $this->objLanguage->languageText("mod_podcaster_for", "podcaster", 'for') . " <strong>$query</strong> <br /><br />";

    echo $displayResults;
}
?>