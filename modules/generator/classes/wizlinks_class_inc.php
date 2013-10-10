<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check


/**
* 
* Class to render the wizard links on a page
* 
* @author Derek Keats
* @category Chisimba
* @package generator
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class wizlinks extends object
{
    /**
    * 
    * @var string object $objLanguage A string to hold the language object
    * 
    */
    public $objLanguage;
    /**
    * 
    * @var string $page The page that we are on in the wizard templates
    * 
    */
    private $page;
    /**
    * @var string $generatorBaseDir The base path to the generators directory 
    * @access Private
    */
    private $generatorBaseDir;
    
    /**
    * 
    * Constructor class to initialize language and load form elements
    * 
    */
    public function init() 
    {
        //Create an instance of the language object
        $this->objLanguage = & $this->getObject('language', 'language');
		//Load the link object
		$this->objLink = $this->getObject('link', 'htmlelements');
		// Add the heading to the content
		$this->objH =& $this->getObject('htmlheading', 'htmlelements');
        //Get the base dir of the generators and set it here
        $this->generatorBaseDir = $this->getResourcePath("generators") ."/";
    }
    
    /**
    *
    * Show method to show the links for the template
    *
    */
    public function show()
    {
        //Variable for the rightside column wizard heading text
		$this->objH->str=$this->objLanguage->languageText("mod_generator_start_rt", "generator");
		$ret = $this->objH->show();
		//Get the array of all generators
        $ar = $this->getListOfGenerators();
        $ret .= "<ul>";
        foreach ($ar as $dr) {
            $ret .= "<li>" . $this->convertToLink($dr) . "</li>";
        }
        $ret .="</ul>";
        return $ret;
    }
    
    /**
    *
    * Method to recurse the list of generators in the file system
    * and build the links to them all
    *
    */
    public function getListOfGenerators()
    {
        $ret=array();
        $directories = scandir($this->generatorBaseDir);
        //set a counter
        $i=0;
        //is_dir($directory . $dir) && 
        foreach ($directories as $dir) {
            if (($dir!=".") && ($dir!="..") && ($dir!="CVS") && ($dir!=".svn")) {
                $ret[$i]=$dir;
                $i++;
            } #if
        } #while
        return $ret;
    } #fn
    
    /**
    *
    * Method to take the directory name and use it to build the 
    * link to the generator.
    *
    */
    public function convertToLink($dir)
    {
        $page = $this->getParam('page', 'buildstart');
        if ($page !=='build' . $dir) {
	        //Set up the form action to generate the controller and register.conf
            $paramArray=array(
              'action'=>'getui',
              'objecttype' => $dir,
              'page'=>'build' . $dir);
            $objLink = $this->getObject('link', 'htmlelements');
            $objLink->href = $this->uri($paramArray);
            $objLink->link = $this->getTitle($dir);
            return $objLink->show() . "<br />";
        } else {
            return $this->getTitle($dir);
        }
    }
    
    /**
    *
    * Method to get the title for the generator from the 
    * OBJECTTYPE_ui_link.xml file
    *
    * @param string $dir The directory to look in (as full filesystem path)
    * @return string The title for the generator
    *
    */
    
    public function getTitle($dir)
    {   
        //Load the XML class template
        $xml = simplexml_load_file($this->generatorBaseDir
          . $dir . "/" . $dir . "_ui_link.xml");
        $title = $xml->title[0];
        return $title;
    }


//------------Code to generate the snippet links for the left column go here
    

    
    /**
    * 
    * Method to return standard text for the left column in the 3 column
    * layout of the wizards.
    * 
    * @return string The standard left column text
    * 
    */
    function putStandardLeftTxt()
    {
        // Add the heading to the content of the left column
		$this->objH->str=$this->objLanguage->languageText("mod_generator_name", "generator");
		$ret = $this->objH->show();
		return $ret . "Need to put the code snippet generator here";
    }

}
?>
