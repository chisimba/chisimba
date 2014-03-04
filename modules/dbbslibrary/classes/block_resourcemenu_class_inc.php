<?php
/**
* Block for the resource's menu containing the back, email, print buttons and statistics
*
* @package etd
* @author Megan Watson
* @version 0.1
* @copyright (c) UWC 2006
*/

class block_resourcemenu extends object
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
        $this->dbStats = $this->getObject('dbstatistics','etd');
        $this->etdResources = $this->getObject('etdresource','etd');
        
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        
        //Set the title
        $this->title = '';//$this->objLanguage->languageText('word_menu');
    }
    
    /**
    * The display method for the block
    * 
    * @access public
    * @return string html
    */
    public function show()
	{
	    $resourceId = $this->getSession('resourceId');
	    
	    $lbPrint = $this->objLanguage->languageText('phrase_printfriendly');
        $lbEmail = $this->objLanguage->languageText('phrase_emailresource');
        $lbBack = $this->objLanguage->languageText('word_back');
	    
        // Create back button / link
        $objButton = new button('back', $lbBack);
        $objButton->setToSubmit();
        
        // back link - use the session variable to create the link
        $session = $this->getSession('return');
        $objForm = new form('backform', $this->uri($session));
        
        $objForm->addToForm($objButton->show());
        $str = $objForm->show();
        
        // Print friendly page
        $url = $this->uri(array('action' => 'printresource'));
            
        $onclick = "javascript:window.open('" .$url."', 'resource', 'left=100, top=100, width=500, height=400, scrollbars=1, fullscreen=no, toolbar=yes, menubar=yes, resizable=yes')";
        $objButton = new button('print', $lbPrint);
        $objButton->setOnClick($onclick);
            
        $str .= $objButton->show();
        
        // Email resource
        $url = $this->uri(array('action' => 'emailresource'));
            
        $objButton = new button('email', $lbEmail);
        $objButton->setToSubmit();
            
        $btnEmail = $objButton->show();
            
        $objForm = new form('emailresource', $url);
        $objForm->addToForm($btnEmail);
            
        $str .= $objForm->show();
        
        $str .= $this->dbStats->showResourceStats($resourceId);
        
        //$str .= $this->etdResources->showCitation();
        
	    return $str;
    }
}
?>