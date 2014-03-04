<?php
//Sending display to 1 column layout
ob_start();

echo '<h1>'.$objLanguage->languageText('mod_forum_searchresultsfor', 'forum', 'Search Results for').': <em>'.htmlentities($searchTerm).'</em></h1>';

if (count($errors > 0)) {

    echo '<ul>';
    foreach ($errors as $error)
    {
        echo '<li class="error">'.$error.'</li>';
    }
    echo '</ul>';
}

echo $searchResults;
// echo '</pre>';

echo $searchForm;

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>