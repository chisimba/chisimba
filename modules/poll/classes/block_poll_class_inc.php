<?php
/**
* Block for the block_poll
*
* @package poll
* @author Megan Watson
* @version 0.1
* @copyright (c) 2007 University of the Western Cape
*/

class block_poll extends object
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
        $this->pollTools = $this->getObject('polltools', 'poll');
        $this->objLanguage = $this->getObject('language','language');
        
        //Set the title
        $this->title = $this->objLanguage->languageText('word_poll');
    }
    
    /**
    * The display method for the block
    */
    public function show()
	{
	    return $this->pollTools->getPollBlock();
    }
}