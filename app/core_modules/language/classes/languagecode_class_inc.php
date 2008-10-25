<?php

/**
 * This class converts retrieves the name of a language by providing the ISO code and also vice versa
 *
 * The original list of code was taken from a class written by Florian Breit (florian at phpws dot org):
 *  http://www.phpclasses.org/browse/file/8143.html
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   language
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007 Prince Mbekwa
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       http://www.phpclasses.org/browse/file/8143.html
 */
/**
*This is a Languagecode class
* @author    Prince Mbekwa
* @copyright (c) 200-2004 University of the Western Cape
* @Version   1
*/

/**
 *Description of the class
* This class converts retrieves the name of a language by providing the ISO code and also vice versa
*
* The original list of code was taken from a class written by Florian Breit (florian at phpws dot org):
*  http://www.phpclasses.org/browse/file/8143.html
*/
require_once 'I18Nv2/Country.php';

/**
 * Description for require_once
 */
require_once 'I18Nv2/Negotiator.php';

/**
 * Description for require_once
 */
require_once 'I18Nv2/DecoratedList/HtmlSelect.php';

/**
 * Description for require_once
 */
require_once 'I18Nv2/DecoratedList/HtmlEntities.php';

/**
 * Short description for class
 *
 * Long description (if any) ...
 *
 * @category  Chisimba
 * @package   language
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007 Prince Mbekwa
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
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
    public $iso_639_2_tags = array();
    /**
     * country object
     *
     * @var objcountry
     */
    public $objcountry;
    /**
     * Pear decorator object
     *
     * @var objentity
     */
    public $objentity;
    /**
     * Pear dropdown select
     *
     * @var objselect
     */
    public $objselect;
    /**
     * Default language locale
     *
     * @var unknown_type
     */
    public $lan;
    /**
     * The global error callback for altconfig errors
     *
     * @access public
     * @var    string
    */
    private $_errorCallback;

    /**
    * Standard constructor method
    */
    function init()
    {
    	try {
    	 	$this->objConfig = $this->getObject('altconfig','config');
    	 	$this->lan = $this->objConfig->getdefaultLanguage();
    	 	$neg = &new I18Nv2_Negotiator;
    	 	$this->objcountry = &new I18Nv2_Country("{$this->lan}", 'iso-8859-1');
		 	$this->objentity = &new I18Nv2_DecoratedList_HtmlEntities($this->objcountry);
		 	$this->objselect = &new I18Nv2_DecoratedList_HtmlSelect($this->objentity);
		 	$this->iso_639_2_tags = $neg->singleI18NLanguage();
    	}
    	catch(customException $e)
    	{
    		customException::cleanUp();
    		die();
    	}
	}

    /**
    * Method to get the name of a language by providing the ISO Code
    *
    * This method first lowercases the code (to match the array) and then checks if it exists in the array.
    * If it does, return the language, else NULL
    * @param  string $isoKey The two letter ISO code
    * @return string |NULL The Name of the Language
    */
    public function getLanguage($isoKey)
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
    * @param  string $language The language to check
    * @return string |NULL The ISO code of the Language
    */
    public function getISO($language)
    {

        // Flip Array - makes key the values, and values the key
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
    /**
     *  This method utilizes ob_iconv_handler(), so you should call it at the beginning of your script (prior to any output).
     * Automatically transform output between character sets
     *
     * @param string $ocs desired output character set
     * @param string $ics current intput character set
	 * @return Returns TRUE on success, PEAR_Error on failure.
     */
    public function autoConv($ocs,$ics)
    {
    	try{
    		I18Nv2::autoConv($ocs, $ics);
    	}catch (Exception $e){
    		$this->errorCallback ('Caught exception: '.$e->getMessage());
    		 exit();
    	}

    }//end function
    /**
     *  Function provides country and language lists.
     *
     * @return dropdown of countrues
     */
    public function country($country=NULL)
    {
		$this->objselect->attributes['select']['name'] = 'country';
		// set a selected entry
		if ($country) {
			$language = $country;
		}else{
            $language = strtoupper($this->objConfig->getCountry());
		}
		$this->objselect->selected["{$language}"] = true;
		// print a HTML safe select box
		return  $this->objselect->getAllCodes();
    }

    /**
    *
    * Method to return an alphabetical select box
    * of countries
    *
    * @param string $tongue the two letter code for the language to be
    *    selected in the select box
    * @return string The select box for countries
    * @access Public
    */
    public function countryAlpha($tongue=NULL)
    {
        $ar = $this->countryListArr();
        asort($ar);
        $this->loadClass('dropdown','htmlelements');
        $objSelect = new dropdown('country');
        // set a selected entry
        if ($tongue) {
            $language = $tongue;
        }else{
            $language = strtoupper($this->objConfig->getCountry());
        }
        foreach ($ar as $code=>$country) {
            $objSelect->addOption($code, $country);
        }
        $objSelect->setSelected($language);
        return $objSelect->show();
    }

     /**
     *  Function provides country and language lists.
     *
     * @return array of countrues
     */
    public function countryListArr($country=NULL)
    {
		$this->objselect->attributes['select']['name'] = 'country';
		// set a selected entry
		if ($country) {
			$language = $country;
		}else{
		  $language = strtoupper($this->objConfig->getCountry());
		}
		$this->objselect->selected["{$language}"] = true;

		// print a HTML safe select box
		return  $this->objentity->getAllCodes();
    }

    /**
     *  Function provides decorated classes for country and language lists.
     *
     * @return dropdown of countrues
     */
    public function dec_country()
    {
    	// set some attributes
		$this->objselect->attributes['select']['name'] = 'country';
		$this->objselect->attributes['select']['onchange'] = 'this.form.submit()';

		// set a selected entry
		$language = strtoupper($this->objConfig->getCountry());
		$this->objselect->selected["{$language}"] = true;

		// print a HTML safe select box
		return  $this->objselect->getAllCodes();
    }

    /**
     * Get the corresponding country name of the supplied two letter country code.
     *
     * @param  string      $code
     * @return countryname
     */
    public function getName ($code){

    	return $this->objcountry->getName($code);
    }
    /**
    * The error callback function, defers to configured error handler
    *
    * @param  string $error
    * @return void
    * @access public
    */
    public function errorCallback($exception)
    {
    	echo customException::cleanUp($exception);
    }
} // End of Class
?>