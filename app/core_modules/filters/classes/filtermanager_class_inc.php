<?php
/**
 *
 * Methods for enabling and disabling filters
 *
 * This class gets lists of filters and provides the key features
 * necessary for enabling / disabling filters by moving to the disabled
 * directory.
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
 * @package   filters
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: filterinfo_class_inc.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* Methods for enabling and disabling filters
*
* This class gets lists of filters and provides the key features
* necessary for enabling / disabling filters by moving to the disabled
* directory.
*
* @author Derek Keats
* @package filters
*
*/
class filtermanager extends object
{

    /**
    *
    * Intialiser for the _MODULECODE controller
    * @access public
    *
    */
    public function init()
    {
        //Set the parent table here
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Set the filter base dir
        $this->filterDir = $this->objConfig->getsiteRootPath() . "core_modules/filters/classes/";
        //Set the disabled location for filter classes
        $this->disabledDir = $this->filterDir . "disabled/";
        //Get the language object
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    * 
    * Disable all filters
    * 
    * @return boolean TRUE
    * @access public
    *  
    */
    public function disableAll()
    {
        // save current working directory (cwd)
        $savedDir = getcwd();
        //load up all of the parsers from filters
        chdir($this->filterDir);
        $parsers = glob("parse4*_class_inc.php");
        foreach ($parsers as $parser) {
        	$parserFilePath = $this->filterDir . $parser;
        	if (file_exists($parserFilePath)) {
        		rename($parserFilePath, $this->disabledDir . $parser) 
        		   or die("mod_filters_errcantmove");
        	}
        }
        // restore path
        chdir($savedDir);
        return TRUE;
    }
    
    /**
    * 
    * Enable all filters
    * 
    * @return TRUE
    * @access public
    *  
    */
    public function enableAll()
    {
        // save current working directory (cwd)
        $savedDir = getcwd();
        //load up all of the parsers from filters
        chdir($this->disabledDir);
        $parsers = glob("parse4*_class_inc.php");
        foreach ($parsers as $parser) {
            $parserFilePath = $this->disabledDir . $parser;
            if (file_exists($parserFilePath)) {
                rename($parserFilePath, $this->filterDir . $parser) 
                   or die("Could not move filter. Check permissions.");
            }
        }
        // restore path
        chdir($savedDir);
        return TRUE;
    }
}
?>