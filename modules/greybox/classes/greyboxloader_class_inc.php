<?php
/**
 *
 * A chisimba wrapper for Greybox
 *
 * A chisimba wrapper for a pop-up window that doesn't suck.
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
 * @package   greybox
 * @author    Derek Keats  <derek@dkeats.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbgreybox.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* Database accesss class for Chisimba for the module greybox
*
* @author Wrapper for Greybox
* @package greybox
*
*/
class greyboxloader extends object
{

    /**
    *
    * Intialiser for the greybox database connector
    * @access public
    *
    */
    public function init()
    {
        // Nothing to do in this module
    }

    /**
    *
    * Load the hard coded GB_ROOT_DIR into the page header. This is needed for
    * the Greybox to work.
    *
    * @return boolean TRUE
    *
    */
    public function loadGbRootDir()
    {
        $objConfig=$this->getObject('altconfig','config');
        $greypath = $objConfig->getSiteRoot() . $objConfig->getModuleURI() . "greybox/resources/";
        $script = "<script type=\"text/javascript\">\n    var GB_ROOT_DIR = \"" . $greypath . "\";\n</script>";
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the AJS javascript file
    *
    * @return boolean TRUE
    * @access public
    *
    */
    public function loadAjs()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('AJS.js'));
        return TRUE;
    }

    /**
    *
    * Load the AJS_fx javascript file
    *
    * @return boolean TRUE
    * @access public
    *
    */
    public function loadAjsFx()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('AJS_fx.js'));
        return TRUE;
    }

    /**
    *
    * Load the Greybox Scripts javascript file
    *
    * @return boolean TRUE
    * @access public
    *
    */
    public function loadGbScripts()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('gb_scripts.js'));
        return TRUE;
    }

    /**
    *
    * Load the stylesheet file
    *
    * @return boolean TRUE
    * @access public
    *
    */
    public function loadStyles()
    {
        $objConfig=$this->getObject('altconfig','config');
        $moduleUri = $objConfig->getModuleURI();
        $script = '<link href="' . $moduleUri . '/greybox/resources/gb_styles.css" rel="stylesheet" type="text/css" />';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the all the required and optional javascript files and stylesheet
    *
    * @return boolean TRUE
    * @access public
    *
    */
    public function loadAll()
    {
        $this->loadGbRootDir();
        $this->loadAjs();
        $this->loadAjsFx();
        $this->loadGbScripts();
        $this->loadStyles();
        return TRUE;
    }

}
?>