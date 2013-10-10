<?php
/**
* Block for the help on the right menu 
*
* @package etd
* @author Megan Watson
* @version 0.1
* @copyright (c) UWC 2006
*/

class block_etdhelp extends object
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
        $this->objLanguage =& $this->getObject('language','language');
        
        //Set the title
        $this->title = $this->objLanguage->languageText('word_help');
    }
    
    /**
    * The display method for the block
    */
    public function show()
	{
	    return '';
    }
}