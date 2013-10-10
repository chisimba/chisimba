<?php
/* ----------- wikiTextParser class extends object ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class wikiTextParser to abstract the Text_Wiki PEAR package so that it can be used in Chisimba
* 
* @author Kevin Cyster 
* @package wiki
* @subpackage Text_Wiki PEAR package author Paul M. Jones
*/
class wikiTextParser extends object 
{ 
    /**
    * @var object $objLanguage: The language class in the language module
    * @access public
    */
    public $objLangauge;
    
    /**
    * @var object $objConfig: The altconfig class in the config module
    * @access public
    */
    public $objConfig;
    
    /**
    * Method to check if Text_Wiki PEAR package is installed and define the class
    * 
    * @access public
    * @return 
    */
    public function init()
    {
    	$this->objLanguage = $this->getObject('language', 'language');
    	$this->objConfig = $this->getObject('altconfig', 'config');
    	$this->objDbwiki = $this->getObject('dbwiki', 'wiki');
        $errorLabel = $this->objLanguage->languageText('mod_wiki_missingpear', 'wiki');
        
        if (!@include_once('Text/Wiki.php')) {
    		throw new customException($errorLabel);
    		return FALSE;
    	}
        $this->objWiki = new Text_Wiki();
        $this->configure();
    }

    

    /**
    * Method to transform structured wiki text into xhtml
    * 
    * @access public
    * @param string $text The wiki text to transform
    * @return string $xhtml The transformed text (XHTML)
    */

    public function transform($text, $format = 'Xhtml', $encodeEntities = true)
    {
        // Transform the wiki text into XHTML
        $xhtml = $this->objWiki->transform($text, $format, $encodeEntities);
        return $xhtml;
    }

    /**
    * Method to configure Text_Wiki parser
    * 
    * @access public
    * @param array $pages: The array of pages in this wiki
    * @param array $links: The array of interwiki links
    * @return void
    */
     
     public function configure()
     {
        // Disabling HTML tags	 
    	$this->objWiki->disableRule('Html');
	 	
        //Set the params for the table rule
        $this->objWiki->setRenderConf('xhtml', 'Table', 'css_table', 'wiki_table');
        $this->objWiki->setRenderConf('xhtml', 'Table', 'css_th', 'wiki_th');
        $this->objWiki->setRenderConf('xhtml', 'Table', 'css_td', 'wiki_td');
        
        //Set the sites array for the interwiki rule
        $pages = $this->objDbwiki->getAllPages();
        $wikiPages = array();
        if(!empty($pages)){
            foreach($pages as $page){
                $wikiPages[] = $page['page_name'];
            }
        }
        $links = $this->objDbwiki->getLinks();
        $wikiLinks = array();
        if(!empty($links)){
            foreach($links as $link){
                $wikiLinks[$link['wiki_name']] = $link['wiki_link'];
            }
        }
        $this->objWiki->setRenderConf("xhtml", "Wikilink", "pages", $wikiPages);
        $this->objWiki->setRenderConf("xhtml", "Interwiki", "sites", "$wikiLinks");

        //Set the add page url param for the wikilink rule
        $addUrl = $this->objConfig->getsiteRoot().'index.php?module=wiki&action=add_page&name=';
        $this->objWiki->setRenderConf("xhtml", "Wikilink", "new_url", $addUrl);

         //Set the view page url param for the wikilink rule
         $viewUrl = $this->objConfig->getsiteRoot().'index.php?module=wiki&action=view_page&name=';
         $this->objWiki->setRenderConf("xhtml", "Wikilink", "view_url", $viewUrl);
    }

    /**
    * Method to render a title from SmashWords to Smash Words
    * i.e. add spaces between capital letters
    * 
    * @access public
    * @param string $title Wiki page title
    * @return string The rendered page title
    */

    function renderTitle($title)
    {
    	$letters = array();
    	$replacement = array();
    	for ($i=65; $i<=90; $i++) { // Loop from A to Z
    		$letters[] = chr($i);
    		$replacement[] = ' '.chr($i);
    	}
    	$str = ltrim(str_replace($letters, $replacement, $title));
    	return $str;
    }
} 
?>
