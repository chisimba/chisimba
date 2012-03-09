<?php
/**
 *
 * Serialize data from PHP to Javascript
 *
 * Serialize data from PHP to Javascript by writing vars to the 
 * page header for use in helper javascripts.
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
 * @package   oerfixer
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 * Serialize data from PHP to Javascript
 *
 * Serialize data from PHP to Javascript by writing vars to the 
 * page header for use in helper javascripts.
*
* @package   oerfixer
* @author    Derek Keats derek@dkeats.com
*
*/
class serializevars extends dbtable
{

    /**
    *
    * Intialiser for the serializer class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     *
     * Serialize language elements so they are available to 
     * Javascript. It does this by creating an 
     *  
     * @param string array $arrayVars an array of key value pairs
     * @access private
     * @return TRUE
     * 
     */
    public function languagetojs($arrayVars, $callingModule)
    {
        $ret = "\n\n<script type='text/javascript'>\n";
        foreach ($arrayVars as $key=>$value) {
            $ret .= "    var " . $key . " = '" . $this->objLanguage->languageText($value, $callingModule) . "';\n";
        }
        $ret .= "</script>\n\n";
        $this->appendArrayVar('headerParams', $ret);
        return TRUE;
    }
    
    /**
     *
     * Serialize language elements so they are available to 
     * Javascript. It does this by creating an 
     *  
     * @param string array $arrayVars an array of key value pairs
     * @access private
     * @return TRUE
     * 
     */
    public function varsToJs($arrayVars)
    {
        $ret = "\n\n<script type='text/javascript'>\n";
        foreach ($arrayVars as $key=>$value) {
            $ret .= "var " . $key . " = '" . $value . "';\n";
        }
        $ret .= "</script>\n\n";
        $this->appendArrayVar('headerParams', $ret);
        return TRUE;
    }
    
    /**
     *
     * Method to set up a php array to be available in javascript
     * 
     * @access public
     * @param string $name The javascript variable name
     * @param array $array The php array to sent to njavascript
     * @param boolean $keepKeys TRUE to keep the PHP array keys
     * @return TRUE
     */
    public function arrayFromPhpToJs($name, $array, $keepKeys = FALSE)
    {
        $str = "\n\n<script type='text/javascript'>\n";
        $str .= "var " . $name . " = [ \n";
        if (!$keepKeys)
        {
            foreach($array as $key => $value)
            {
                $str .= "'" . $value . "',\n"; 
            }
            $str .= "];\n";
        }
        else
        {
            foreach($array as $key => $value)
            {
                $str .= "{ label: '" . $value . "', value: '" . $key . "' },\n";
            }
            $str .= "];\n";
        }
        $str .= "</script>\n\n";

        $this->appendArrayVar('headerParams', $str);
        return TRUE;
    }
}
?>