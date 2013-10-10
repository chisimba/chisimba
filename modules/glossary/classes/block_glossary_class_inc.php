<?php
/**
* @package glossary
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* The glossary block class displays a block for searching glossary.
* @author Megan Watson
*/

class block_glossary extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language','language');
        $this->title = $this->objLanguage->languageText('mod_glossary_searchglossary', 'glossary');

        $this->objIcon =& $this->newObject('geticon', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /**
    * Method to display the search box.
    */
    public function getSearch()
    {
        $hdSearch = $this->objLanguage->languageText('mod_glossary_searchForWord', 'glossary');
        $lbSearch = $this->objLanguage->languageText('word_search');
        $lnGlossary = $this->objLanguage->languageText('mod_glossary_name', 'glossary');

        //Search Form
        $searchForm = new form('search');
        $searchForm->method = 'GET';

        $searchLabel = new label($hdSearch, 'input_term');
        $searchForm->addToForm($searchLabel->show().': ', null);

        $term = new textinput('term', stripslashes($this->getParam('term')));
        $term->size = 40;
        $searchForm->addToForm($term->show());

        $searchForm->addToForm(' '); // Spacer

        $submitButton = new button('submit', $lbSearch);
        $submitButton->setToSubmit();

        $searchForm->addToForm($submitButton);

        $module = new textinput('module');
        $module->fldType = 'hidden';
        $module->value = 'glossary';

        $action = new textinput('action');
        $action->fldType = 'hidden';
        $action->value = 'search';

        $searchForm->addToForm($module->show().$action->show());

        $str = $searchForm->show();

        // link to glossary
        $url = $this->uri('', 'glossary');
        $this->objIcon->setModuleIcon('glossary');
        $objLink = new link($url);
        $objLink->link = $this->objIcon->show();
        $str .= '<p>'.$objLink->show();
        $objLink = new link($url);
        $objLink->link = $lnGlossary;
        $str .= '&nbsp;'.$objLink->show().'</p>';

        return $str;
    }

    /**
    * Method to display the block
    */
    public function show()
    {
        return $this->getSearch();
    }
}
?>