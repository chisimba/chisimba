<?
/**
*This is a Language class for kewlNextGen
*@author Prince Mbekwa and Tohir Solomons 
*@copyright (c) 200-2004 University of the Western Cape
*@Version 1
*/

/**
* This class converts retrieves the name of a language by providing the ISO code and also vice versa
*
* The original list of code was taken from a class written by Florian Breit (florian at phpws dot org): 
*  http://www.phpclasses.org/browse/file/8143.html
*/
require_once 'I18Nv2/Country.php';
require_once 'I18Nv2/Negotiator.php';
require_once 'I18Nv2/DecoratedList/HtmlSelect.php';
require_once 'I18Nv2/DecoratedList/HtmlEntities.php';

class languagecode extends object 
{
	/**
     * Config object
     *
     * @var objConfig
     */
    public $objConfig =  null;
    /**
    * @var array $iso_639_2_tags contains an associative array of all the alpha2 languages
    */
    var $iso_639_2_tags = array();
    
    /**
    * Standard constructor method 
    */
    function init()
    { 
    	 $this->objConfig = &$this->getObject('altconfig','config');
    	 $lan = $this->objConfig->getdefaultLanguage();
    	 $neg = &new I18Nv2_Negotiator;
    	 $c = &new I18Nv2_Country("{$lan}", 'iso-8859-1');
		 $e = &new I18Nv2_DecoratedList_HtmlEntities($c);
		 $s = &new I18Nv2_DecoratedList_HtmlSelect($e);
		 $this->iso_639_2_tags = $neg->singleI18NLanguage();
		}
    
    /**
    * Method to get the name of a language by providing the ISO Code
    *
    * This method first lowercases the code (to match the array) and then checks if it exists in the array.
    * If it does, return the language, else NULL
    * @param string $isoKey The two letter ISO code
    * @return string |NULL The Name of the Language
    */
    function getLanguage($isoKey)
    {    	
        if (array_key_exists(strtolower($isoKey), $this->iso_639_2_tags->codes)) {
            return $this->iso_639_2_tags->codes[strtolower($isoKey)];
        } else {
            return NULL;
        }
    }
    
    /**
    * Method to get the name of a ISO Code of a language by providing the ISO Code
    *
    * @param string $language The language to check
    * @return string |NULL The ISO code of the Language
    */
    function getISO($language)
    {
    	
        // Flip Array - makes key the values, and values the key
        //$tempArray = array_flip($this->iso_639_2_tags); 
        $tempArray = $this->iso_639_2_tags->codes;
        // Upper Case the first letter of the Word
        $language = strtolower($language);
        if (array_key_exists($language, $tempArray)) {
        	//$tempArray = array_flip($tempArray);
        	      	
            return $language;
        } else {
        	       	
            return NULL;
        }
    }
    
} // End of Class
?>