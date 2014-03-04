<?php
/**
* Block for the right menu 
*
* @package etd
* @author Megan Watson
* @version 0.1
* @copyright (c) UWC 2006
*/

class block_rightmenu extends object
{
    /**
    * @var the block title
    */
    public $title;
    
    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language','language');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('button','htmlelements');
        $this->loadClass('form','htmlelements');
        $this->loadClass('textinput','htmlelements');
        
        //Set the title
        $this->title = $this->objLanguage->languageText('word_menu');
    }
    
    /**
    * Method to display the search block with the link to the advanced searc
    
    * @access private
    * @return string html
    */
    private function showSearch()
    {
        $searchLabel = $this->objLanguage->languageText('word_search');
        $advSearchLabel = $this->objLanguage->languageText('phrase_advancedsearch');
        $lbKeywords = $this->objLanguage->languageText('word_keywords');
        $lbAuthors = $this->objLanguage->languageText('mod_etd_authorsurnamename', 'etd');

        // search button and input
        $objInput = new textinput('searchField');
        $objInput->extra = "autocomplete='off'";
        $objInput->size = 10;
        $search = '<p>'.$lbKeywords.':<br />'.$objInput->show().'</p>';

        $objInput = new textinput('searchAuthors');
        $objInput->extra = "autocomplete='off'";
        $objInput->size = 10;
        $search .= '<p>'.$lbAuthors.':<br />'.$objInput->show().'</p>';

        $objButton = new button('search', $searchLabel);
        $objButton->setToSubmit();
        $search .= '<p>'.$objButton->show().'</p>';

        $objForm = new form('search', $this->uri(array('action' => 'advsearch', 'mode'=>'simple')));
        $objForm->addToForm($search);
        $str = $objForm->show();

        $objLink = new link($this->uri(array('action' => 'search')));
        $objLink->link = $advSearchLabel;
        $str .= '<p>'.$objLink->show().'</p>';

        return $str;
    }
    
    /**
    * The display method for the block
    *
    * @access public
    * @return string html
    */
    public function show()
	{
        $home = $this->objLanguage->languageText('word_home');
        $browse = $this->objLanguage->languageText('word_browse','etd');
        $faculties = $this->objLanguage->languageText('word_faculties');
        $departments = $this->objLanguage->languageText('word_departments');
        $degrees = $this->objLanguage->languageText('word_degrees');
        $authors = $this->objLanguage->languageText('word_authors');
        $titles = $this->objLanguage->languageText('word_titles');
        $list = '';
        
        // Home page
        $objLink = new link($this->uri(''));
        $objLink->link = $home;
        $list .= $objLink->show().'<br />';
        
	    // Browse menu items
        $list .= '<b>'.$browse.':</b><br /><ul>';
        
        $objLink = new link($this->uri(array('action'=>'browsefaculty')));
        $objLink->link = $faculties;
        $list .= '<li style="padding-bottom: 5px;">'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(array('action'=>'browsedepartment')));
        $objLink->link = $departments;
        $list .= '<li style="padding-bottom: 5px;">'.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action'=>'browsedegrees')));
        $objLink->link = $degrees;
        $list .= '<li style="padding-bottom: 5px;">'.$objLink->show().'</li>';

        $objLink = new link($this->uri(array('action'=>'browseauthor')));
        $objLink->link = $authors;
        $list .= '<li style="padding-bottom: 5px;">'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(array('action'=>'browsetitle')));
        $objLink->link = $titles;
        $list .= '<li>'.$objLink->show().'</li>';
        
        $list .= '</ul>';
        
        $list .= $this->showSearch();
        
        return $list;
    }
}
