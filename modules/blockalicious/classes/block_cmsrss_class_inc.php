<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The class provides an rss block 
* 
*
* @author Prince Mbekwa
* @copyright GNU/GPL, AVOIR
* @package   blockalicius
* @access    public
*/

class block_cmsrss extends object {
	
    public $title;
    public $objLanguage;
    public $messageForBlock;
    
    /**
    * Constructor for the class
    */
    function init()
    {
    	$this->objLanguage = $this->getObject('language', 'language');
    	$this->loadClass('href', 'htmlelements');

        $this->objIcon = $this->newObject('geticon', 'htmlelements');
          //Set the title - 
        $this->title=$this->objLanguage->languageText("mod_blockalicious_cmsrss_title", "blockalicious");

        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
    	$pageid = $this->getParam('id');
    	$this->messageForBlock = "";
     	$leftCol = NULL;
             
        
        //RSS2.0
        $rss2 = $this->getObject('geticon', 'htmlelements');
        $rss2->setIcon('rss', 'gif', 'icons/filetypes');
		$rss2->align = "top";
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'rss2', 'pageid' => $pageid),'cms'), $this->objLanguage->languageText("mod_cms_word_rss2", "cms"));
        $leftCol .= $rss2->show() . "&nbsp;" . $link->show() . "<br />";

        //RSS0.91
        $rss091 = $this->getObject('geticon', 'htmlelements');
        $rss091->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'rss091', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_rss091", "cms"));
        $leftCol .= $rss091->show() . "&nbsp;" .$link->show() . "<br />";

        //RSS1.0
        $rss1 = $this->getObject('geticon', 'htmlelements');
        $rss1->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'rss1', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_rss1", "cms"));
        $leftCol .= $rss1->show() . "&nbsp;" . $link->show() . "<br />";

        //PIE
        $pie = $this->getObject('geticon', 'htmlelements');
        $pie->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'pie', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_pie", "cms"));
        $leftCol .= $pie->show() . "&nbsp;" . $link->show() . "<br />";

        //MBOX
        $mbox = $this->getObject('geticon', 'htmlelements');
        $mbox->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'mbox', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_mbox", "cms"));
        $leftCol .= $mbox->show() . "&nbsp;" . $link->show() . "<br />";

        //OPML
        $opml = $this->getObject('geticon', 'htmlelements');
        $opml->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'opml', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_opml", "cms"));
        $leftCol .= $opml->show() . "&nbsp;" . $link->show() . "<br />";

        //ATOM
        $atom = $this->getObject('geticon', 'htmlelements');
        $atom->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'atom', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_atom", "cms"));
        $leftCol .= $atom->show() . "&nbsp;" . $link->show() . "<br />";

        //Plain HTML
        $html = $this->getObject('geticon', 'htmlelements');
        $html->setIcon('rss', 'gif', 'icons/filetypes');
        $link = new href($this->uri(array('action' => 'feed', 'format' => 'html', 'pageid' => $pageid),'cms'),$this->objLanguage->languageText("mod_cms_word_html", "cms"));
        $leftCol .= $html->show() . "&nbsp;" . $link->show() . "<br />";
               
                
        $this->messageForBlock = $leftCol;
        $this->expose=TRUE;
   	
    }
    /** Method to output a block with all the type of rss feeds
    */
    function show(){
    	 return $this->messageForBlock;
    }
}

?>