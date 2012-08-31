<?php

/**
 * Twitterizer interface class
 *
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: imapi_class_inc.php 11081 2008-10-25 19:39:46Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Twitterizer XML-RPC Class
 *
 * Class to provide Chisimba Twitterizer XML-RPC functionality
 *
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class twitterizerapi extends object
{
    public $isReg;

    /**
     * Standard Chisimba init method
     *
     * @return void
     * @access public
     */
    public function init()
    {
        try {
            $this->objModules = $this->getObject('modules', 'modulecatalogue');
            $this->isReg = $this->objModules->checkIfRegistered('twitterizer');
            if ($this->isReg === TRUE) {
                $this->objDbTwit = $this->getObject('dbtweets', 'twitterizer');
            }
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch (customException $e) {
            customException::cleanUp();
            exit;
        }
    }

    public function addTweet($params)
    {
        $tweet = $params->getParam(0);
        $createdat = $params->getParam(1);
        $tstamp = $params->getParam(2);
        $screen_name = $params->getParam(3);
        $name = $params->getParam(4);
        $image = $params->getParam(5);
        $location = $params->getParam(6);

        $tweet = $tweet->scalarval();
        $createdat = $createdat->scalarval();
        $tstamp = $tstamp->scalarval();
        $screen_name = $screen_name->scalarval();
        $name = $name->scalarval();
        $image = $image->scalarval();
        $location = $location->scalarval();
        
        $insarr = array('tweet' => $tweet, 'createdat' => $createdat, 'tstamp'=> $tstamp, 'screen_name' => $screen_name, 'name' => $name, 'image' => $image, 'location' => $location);

        $res = $this->objDbTwit->addRec($insarr);

        $val = new XML_RPC_Value($res, 'string');
        return new XML_RPC_Response($val);
    }

}
?>
