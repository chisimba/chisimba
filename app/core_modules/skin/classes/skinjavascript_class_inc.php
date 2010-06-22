<?php
/**
 *
 * Load the skin Javascript
 *
 * Load the skin Javascript into the page header.
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
 * Load the skin Javascript
 *
 * Load the skin Javascript into the page header.
*
* @package   skin
* @author    Derek Keats <derek.keats@wits.ac.za>
*
*/
class skinjavascript extends object
{
    /**
    *
    * Intialiser for the skin chooser
    * @access public
    *
    */
    public function init()
    {
    }

    /**
    * Method to return the common JavaScript that is used and needs to go into the page templates
    * This loads Prototype and JavaScript into the page templates
    *
    * @param string $mime Mimetype of Page - Either text/html or application/xhtml+xml
    * @param string array $headerParams List of items that needs to go into the header of the page
    * @param string array $bodyOnLoad List of items that needs to go into the bodyOnLoad section of the page
    *
    */
    public function loadAll($mime='text/html', $headerParams=NULL, $bodyOnLoad=NULL)
    {
        if ($mime != 'application/xhtml+xml') {
            $mime = 'text/html';
        }
        $str = '';
        $str .= $this->getScriptaculous($mime);
        $str .= $this->getJQuery();
        $str .= $this->getHeaderParams($headerParams);
        $str .= $this->getBodyParams($bodyOnLoad);
        return $str;
    }

    /**
    *
    * Insert the scriptaculous library into the page head
    *
    * @param string $mime Mimetype of Page
    * @return string The rendered javascript
    * @access public
    *
    */
    public function getScriptaculous($mime)
    {
        $supressPrototype = $this->getVar('SUPPRESS_PROTOTYPE', false);
        if (!$supressPrototype){
            // Add Scriptaculous
            $scriptaculous = $this->getObject('scriptaculous', 'htmlelements');
            return $scriptaculous->show($mime);
        } else {
            return NULL;
        }
    }

    /**
    *
    * Insert the jQuery library into the page head
    *
    * @return string The rendered javascript
    * @access public
    *
    */
    public function getJQuery()
    {
        $supressJQuery = $this->getVar('SUPPRESS_JQUERY', false);
        $jQueryVersion = $this->getVar('JQUERY_VERSION', '1.2.3');
        if (!$supressJQuery){
            $jquery = $this->getObject('jquery', 'jquery');
            $jquery->setVersion($jQueryVersion);
            return $jquery->show();
        } else {
            return NULL;
        }
    }

    /**
    *
    * Insert the headerparams into the page head
    *
    * @var string #headerParams The header parameters
    * @return string The rendered header parameters
    * @access public
    *
    */
    public function getHeaderParams($headerParams)
    {
        if ($headerParams == NULL) {
            $headerParams = $this->getVar('headerParams');
        }

        if (is_array($headerParams)) {
            $headerParams = array_unique($headerParams);
            foreach ($headerParams as $headerParam) {
                $ret .= $headerParam."\n\n";
            }
            return $ret;
        } else {
            return NULL;
        }
    }

    /**
    *
    * Insert the body parameters
    *
    * @return string The rendered body params
    * @access public
    *
    */
    public function getBodyParams()
    {
        if ($bodyOnLoad == NULL) {
            $bodyOnLoad = $this->getVar('bodyOnLoad');
        }

        if (is_array($bodyOnLoad)) {
            $str .= '<script type="text/javascript">';
            $str .= 'window.onload = function() {'."\n";
            foreach ($bodyOnLoad as $bodyParam) {
                $str .= '   '.$bodyParam."\n";
            }
            $str .= '}
</script>'."\n\n";
        }
    }
}
?>