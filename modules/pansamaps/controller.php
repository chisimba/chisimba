<?php
/**
 * PANSA controller class
 *
 * Class to control the PANSA Maps module
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
 * @category  chisimba
 * @package   pansamaps
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
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
 * PANSA controller class
 *
 * Class to control the PANSA maps module.
 *
 * @category  Chisimba
 * @package   pansamaps
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class pansamaps extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    public $objOps;
    public $objCurl;
    public $objDbTags;
    public $objUtils;
    public $ip2Country;
    public $objWashout;
    public $objTwtOps;
    public $objTeeny;
    public $objSocial;
    public $dbFoaf;
    public $objModuleCat;
    public $objActStream;
    public $eventsEnabled;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->requiresLogin();
            $this->objLanguage   = $this->getObject ( 'language', 'language' );
            $this->objConfig     = $this->getObject('altconfig', 'config');
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objCurl       = $this->getObject('curlwrapper', 'utilities');
            $this->objDbPansa    = $this->getObject('dbpansa');
            $this->objOps        = $this->getObject('pansaops');
            $this->ip2Country    = $this->getObject('iptocountry', 'utilities');
            $this->objWashout    = $this->getObject('washout', 'utilities');
        }
        catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            case NULL:

            case 'main' :
                $this->setVar('pageSuppressSkin', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->objDbPansa->getData();
                return 'mapview_tpl.php';
                break;
                
            case 'input':
                $this->setVar('pageSuppressSkin', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                return 'input_tpl.php';
                break;
                
            case 'editrecords':
                $this->setVar('pageSuppressSkin', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $recid = $this->getParam('recid');
                $editparams = $this->objDbPansa->getSingle($recid);
                $this->setVarByRef('editparams', $editparams[0]);
                return 'input_tpl.php';
                break;
                
            case 'getmapdata':
                header('Content-type: text/xml');
                echo $this->objDbPansa->getData();
                break;
                
            case 'searchvenues':
                $this->setVar('pageSuppressSkin', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $term = $this->getParam('keyword');
                $res = $this->objDbPansa->searchRecords($term);
                // var_dump($res); die();
                $this->setVarByRef('results',$res);
                return 'results_tpl.php';
                break;
                
            case 'updatedata':
                $id = $this->getParam('id');
                $geotag = $this->getParam('geotag');
                $geo = explode(",  ", $geotag);
                $geolat = $geo[0];
                $geolon = $geo[1];
                $venuename = $this->getParam('venuename');
                $venueaddress1 = $this->getParam('venueaddress1');
                $venueaddress2 = $this->getParam('venueaddress2');
                $city = $this->getParam('city');
                $zip = $this->getParam('zip');
                $phonecode = $this->getParam('phonecode');
                $phone = $this->getParam('phone');
                $faxcode = $this->getParam('faxcode');
                $fax = $this->getParam('fax');
                $email = $this->getParam('email');
                $url = $this->getParam('url');
                $contactperson = $this->getParam('contactperson');
                $otherinfo = $this->getParam('otherinfo');
                $venuedescription = $this->getParam('venuedescription');
                $venuelocation = $this->getParam('venuelocation');
                
                $dataArray = array('venuename' => $venuename, 'venueaddress1' => $venueaddress1, 'venueaddress2' => $venueaddress2, 'city' => $city,
                                   'zip' => $zip, 'phonecode' => $phonecode, 'phone' => $phone, 'faxcode' => $faxcode, 'fax' => $fax,'email' => $email,
                                   'url' => $url, 'contactperson' => $contactperson, 'otherinfo' => $otherinfo, 'venuedescription' => $venuedescription,
                                   'geolat' => $geolat, 'geolon' => $geolon, 'venuelocation' => $venuelocation, );
                $this->objDbPansa->updateRecord($id, $dataArray);
                $this->nextAction('');
                break;
                
                
            case 'adddata':
                $geotag = $this->getParam('geotag');
                $geo = explode(",  ", $geotag);
                $geolat = $geo[0];
                $geolon = $geo[1];
                $venuename = $this->getParam('venuename');
                $venueaddress1 = $this->getParam('venueaddress1');
                $venueaddress2 = $this->getParam('venueaddress2');
                $city = $this->getParam('city');
                $zip = $this->getParam('zip');
                $phonecode = $this->getParam('phonecode');
                $phone = $this->getParam('phone');
                $faxcode = $this->getParam('faxcode');
                $fax = $this->getParam('fax');
                $email = $this->getParam('email');
                $url = $this->getParam('url');
                $contactperson = $this->getParam('contactperson');
                $otherinfo = $this->getParam('otherinfo');
                $venuedescription = $this->getParam('venuedescription');
                $venuelocation = $this->getParam('venuelocation');
                
                $dataArray = array('venuename' => $venuename, 'venueaddress1' => $venueaddress1, 'venueaddress2' => $venueaddress2, 'city' => $city,
                                   'zip' => $zip, 'phonecode' => $phonecode, 'phone' => $phone, 'faxcode' => $faxcode, 'fax' => $fax,'email' => $email,
                                   'url' => $url, 'contactperson' => $contactperson, 'otherinfo' => $otherinfo, 'venuedescription' => $venuedescription,
                                   'geolat' => $geolat, 'geolon' => $geolon, 'venuelocation' => $venuelocation, );
                $this->objDbPansa->addRecord($dataArray);
                $this->objOps->emailNotify($dataArray);
                $this->nextAction('');
                break;
                
            case 'deleterecord':
                $recid = $this->getParam('recid');
                $this->objDbPansa->deleteRecord($recid);
                $this->nextAction('');
                
            case 'exportdata':
                $this->objDbPansa->exportData();
                          
            default:
                $this->nextAction('');
                break;
        }
    }

    public function requiresLogin() {
        return FALSE;
    }
}
?>
