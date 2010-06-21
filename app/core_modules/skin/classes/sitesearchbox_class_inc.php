<?php
/**
 *
 * Site search box
 *
 * Render a site search box that can be positioned using CSS.
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
 * @package   skin
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Site search box
*
* Render a site search box that can be positioned using CSS.
*
* @package   skin
* @author    Derek Keats <derek.keats@wits.ac.za>
*
*/
class sitesearchbox extends object
{
    /**
    *
    * @var string object Hold configuration reading object
    * @access public
    *
    */
    public $objConfig;

    /*
    *
    * @var string object Hold the modules object
    * @access public
    *
    */
    public $objModules;

    /*
    *
    * @var string object Hold language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * Intialiser for the skin chooser
    * @access public
    *
    */
    public function init()
    {
        // Load an instance of the language object for text rendering.
        $this->objLanguage = $this->getObject('language', 'language');
        // Load an instance of the module class for checking if a module is registered.
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
        // Load an instance of the config object.
        $this->objConfig = $this->getObject('altconfig','config');
        // Load the form building classes.
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('form','htmlelements');
        $this->loadClass('button','htmlelements');
    }

    /**
    *
    * Method to generate a form for a site-wide search
    *
    * @param boolean $compact whether or not to use the compact search form for small screens.
    * @return str Search Form
    * @access public
    *
    */
    public function show($compact = FALSE)
    {
        //checking if configuration exist-By Emmanuel Natalis
        if(strtoupper($this->objConfig->getenable_searchBox()) == 'TRUE'
          && $this->objModules->checkIfRegistered('search')) {
            $slabel = new label($this->objLanguage->languageText('phrase_sitesearch', 'search', 'Site Search') .':', 'input_search');
            $sform = new form('query', $this->uri(NULL,'search'));
            //$sform->addRule('searchterm', $this->objLanguage->languageText("mod_blog_phrase_searchtermreq", "blog") , 'required');
            $query = new textinput('search');
            $query->size = 15;
            $objSButton = new button($this->objLanguage->languageText('word_go', 'system'));
            // Add the search icon
            $objSButton->setIconClass("search");
            //$this->objSButton->setValue($this->objLanguage->languageText('mod_skin_find', 'skin'));
            $objSButton->setValue('Find');
            $objSButton->setToSubmit();
            if ($compact) {
                $sform->addToForm($slabel->show().' '.$objSButton->show().'<br /> '.$query->show());
            } else {
                $sform->addToForm($slabel->show().' '.$query->show().' '.$objSButton->show());
            }
            $sform = '<div id="search">'.$sform->show().'</div>';
            return $sform;
        } else {
            return NULL;
        }
    }
}
?>