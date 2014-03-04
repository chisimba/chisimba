<?php

/**
 * Skype Status
 * 
 * Skype user presence status helper
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
 * @package   skype
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: skypestatus_class_inc.php 16049 2009-12-26 01:32:47Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Skype Status
 * 
 * Skype user presence status helper 
 * 
 * @category  Chisimba
 * @package   skype
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za/
 */

class skypestatus extends object
{
    /**
     * Instance of the curlwrapper class of the utilities module.
     *
     * @access protected
     * @var    object
     */
    protected $curl;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     *
     * @access public
     */
    public function init()
    {
        $this->curl = $this->getObject('curlwrapper', 'utilities');
    }

    /**
     * Retrieve the current presence status of a user.
     *
     * @access public
     * @param  string $username The username of the user to retrieve the status of.
     * @return array  Associative array with the language codes as keys. False on failure.
     */
    public function getStatus($username)
    {
        // Initialise the array to be returned by this method.
        $status = array();

        // Compile the URI to retrieve.
        $uri = sprintf('http://mystatus.skype.com/%s.xml', rawurlencode($username));

        // Retrieve the XML document from the URI.
        $xml = $this->curl->process($uri);

        // Ensure the HTTP fetch was successful.
        if (is_object($xml)) {
            // Create the DOM document and populate it using the XML.
            $document = dom_import_simplexml($xml);

            // Retrieve the presence elements.
            $elements = $document->getElementsByTagName('presence');

            // Initialise the array to be returned by this method.
            $status = array();

            // Loop through each presence element and populate the $status array accordingly.
            foreach ($elements as $element) {
                $lang = $element->getAttribute('xml:lang');
                $status[$lang] = $element->textContent;
            }
        } else {
            // Change the status array to a FALSE in case of a failure.
            $status = FALSE;
        }

        // Return the status array.
        return $status;
    }
}
