<?php
/**
* @package faq
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* The faq block class displays a block with a list of categories.
* @author Megan Watson
*/

class block_faq extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_faq_name');

        $this->objDbFaqCategories =& $this->getObject('dbfaqcategories');
        $this->objDbContext = &$this->getObject('dbcontext', 'context');

        $this->contextCode = $this->objDbContext->getContextCode();
        // If we are not in a context...
//
        if ($this->contextCode == null) {
            $this->contextCode = 'root';
        }

        $this->objIcon =& $this->getObject('geticon', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
    }

    /**
    * Method to show the form for selecting a category
    */
    public function showForm()
    {
        $lbCategory = $this->objLanguage->languageText('mod_faq_selectcategory');
        $lbAllCats = $this->objLanguage->languageText('mod_faq_allcategories');
        $lbGo = $this->objLanguage->languageText('word_go');

        $categories =  $this->objDbFaqCategories->getContextCategories($this->contextCode);

        // Category Form.
        $form = new form('category', $this->uri(''));
        $form->setDisplayType(3);
        $form->method = 'GET';

        $moduleHiddenInput = new hiddeninput('module', 'faq');
        $actionHiddenInput = new hiddeninput('action', 'changeCategory');

        $form->addToForm($moduleHiddenInput->show());
        $form->addToForm($actionHiddenInput->show());

        $label = new label($lbCategory.':&nbsp;&nbsp;', 'input_category');
        $form->addToForm($label->show());

        $dropdown = new dropdown('category');
        $dropdown->addOption('All Categories', $lbAllCats);
        foreach ($categories as $item) {
            $dropdown->addOption($item['id'], $item['categoryname']);
        }
        $form->addToForm($dropdown);

        $form->addToForm('&nbsp;');
        $button = new button('submit', $lbGo);
        $button->setToSubmit();
        $form->addToForm($button);

        return $form->show();
    }

    /**
    * Display link to FAQ
    */
    public function getLink()
    {
        $url = $this->uri('', 'faq');
        $this->objIcon->setModuleIcon('faq');
        $objLink = new link($url);
        $objLink->link = $this->objIcon->show();
        $lnStr = '<p>'.$objLink->show();
        $objLink = new link($url);
        $objLink->link = $this->title;
        $lnStr .= '&nbsp;'.$objLink->show().'</p>';

        return $lnStr;
    }

    /**
    * Display public function
    */
    public function show()
    {
        return $this->showForm().$this->getLink();
    }
}
?>
