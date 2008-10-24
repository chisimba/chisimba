<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load a personal data item into a page in place of a pseudotag in the form
 * [PDATA]item[/PDATA]
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
 * 
 * @category  Chisimba
 * @package   filters
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
    // security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Class to parse a string (e.g. page content) that contains a request
 * to load a personal data item into a page in place of a pseudotag in the form
 * [PDATA]item[/PDATA]
 *
 * @author Derek Keats
 *
 */

class parse4pdata extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
    {
		$this->objUser = $this->getObject("user", "security");
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($str)
    {
        $str = stripslashes($str);
        //Get all the tags into an array
        preg_match_all('/\\[PDATA](.*?)\\[\/PDATA]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
        	$dataItem = $results[1][$counter];
        	$replacement = $this->getReplacement($dataItem);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }

        return $str;
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $dataItem Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access private
     */
    private function getReplacement($dataItem)
    {
    	$dataMethod = strtolower($dataItem);
    	if (method_exists($this, $dataMethod)) {
    		return $this->$dataMethod();
    	} else {
    	    return "No corresponding method for: }}}$dataItem{{{";
    	}
    }
    
    /**
    * 
    * Method to correspond to FIRSTNAME
    * 
    */
    private function firstname()
    {
    	$ret = $this->objUser->getFirstName($this->objUser->userId());
    	if ($ret == "") {
    	    $ret = "Guest";
    	}

        return $ret;
    }

    /**
    * 
    * Method to correspond to FULLNAME
    * 
    */
    private function fullname()
    {
    	$ret = $this->objUser->getFirstName($this->objUser->userId()) . " " . $this->objUser->getSurname($this->objUser->userId());
    	if ($ret == " ") {
    	    $ret = "Guest";
    	}

        return $ret;
    }

    /**
    * 
    * Method to correspond to SURNAME
    * 
    */
    private function surname()
    {
    	$ret = $this->objUser->getSurname($this->objUser->userId());
    	if ($ret == "") {
    	    $ret = "Guest";
    	}

        return $ret;
    }
    
    /**
    * 
    * Method to correspond to USERPIC
    * 
    */
    private function userpic()
    {
        return $this->objUser->getUserImage($this->objUser->userId());
    }

    /**
    * 
    * Method to correspond to TITLE
    * 
    */
    private function title()
    {
    	$pkId = $this->objUser->PKId($this->objUser->userId());

        return $this->objUser->getItemFromPkId($pkId, "title");
    }

}
?>