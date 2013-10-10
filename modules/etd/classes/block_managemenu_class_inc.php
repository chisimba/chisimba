<?php
/**
* Block for the management menu 
*
* @package etd
* @author Megan Watson
* @version 0.1
* @copyright (c) UWC 2006
*/

class block_managemenu extends object
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
        $this->title = $this->objLanguage->languageText('word_management');
        $this->access = $this->getSession('accessLevel', array());
    }
    
    /**
    * The display method for the block
    * 
    * @access public
    * @return string html
    */
    public function show()
	{
        $resources = $this->objLanguage->languageText('word_resources');
        $submissions = $this->objLanguage->languageText('phrase_newsubmissions');
        $repository = $this->objLanguage->languageText('phrase_managerepository');
        $users = $this->objLanguage->languageText('phrase_userpermissions');
        $configure = $this->objLanguage->languageText('phrase_configuresystem');
        
        $objLink = new link($this->uri(array('action'=>'managesubmissions')));
        $objLink->link = $submissions;
        $list = '<p style="padding-top: 5px;">'.$objLink->show().'</p>';

        if(in_array('manager', $this->access)){
            $objLink = new link($this->uri(array('action'=>'managesubmissions', 'mode' => 'resources')));
            $objLink->link = $repository;
            $list .= '<p>'.$objLink->show().'</p>';
                    
            $objLink = new link($this->uri(array('action'=>'showconfig')));
            $objLink->link = $configure;
            $list .= '<p>'.$objLink->show().'</p>';
        }
        
        return $list;
    }
}
?>