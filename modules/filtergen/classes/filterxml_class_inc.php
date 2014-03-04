<?php
/**
 *
 * _SHORTDESCRIPTION
 *
 * _LONGDESCRIPTION
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
 * @package   _MODULECODE
 * @author    _AUTHORNAME _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: db_MODULECODE.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
*  
*
* @author _AUTHORNAME
* @package _MODULECODE
*
*/
class filterxml extends object
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
    }
    
    public function getFilters()
    {
        
        $objFilters = $this->getObject('filterinfo', 'filters');
        $parsers = $objFilters->getFilters();
        $arParsers = array();
        foreach ($parsers as $parser) {
            $tmp = str_replace("_class_inc.php", "", $parser);
            $tmp = str_replace("parse4", "", $tmp);
            $arParsers[] = $tmp;
        }
        unset($parsers);
        return $arParsers;
    }
    
    public function getFiltersAsLinks()
    {
        $baseUrl = $this->uri(array('action' => 'showlinked'), 'filtergen');
        $ar = $this->getFilters();
        $ret = "";
        foreach ($ar as $parser) {
            $ret .= '<a href="' 
              . $this->uri(array('action' => 'showlinked', 'filter' => $parser), 'filtergen')
              . '">' . $parser . '</a><br />';
        }
        return $ret;

        
    }
    
    public function getFilterXml($filter, $raw=TRUE)
    {
        $filterXmlPath = $this->getResourcePath('xml', 'filters') . '/' . $filter . '.xml';
        if (file_exists($filterXmlPath)) {
            $xml =  simplexml_load_file($filterXmlPath);
            return $xml;
        } else {
            return FALSE;
        }
    }
}
?>