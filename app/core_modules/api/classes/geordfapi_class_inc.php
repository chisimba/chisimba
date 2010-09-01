<?php
/**
 * GeoRDF interface class
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
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: skypeapi_class_inc.php 11040 2008-10-24 15:45:36Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * GeoRDF XML-RPC Class
 * 
 * Class to provide Chisimba Skype recording functionality via the XML-RPC interface. 
 * The skype python tools will do most of the ghard work here, this class simply accepts the data once processed, and does somwhat useful stuff with it.
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
class geordfapi extends object
{

    public $dataCapable;
    
    /**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        try {
            // Some config
            $this->objConfig = $this->getObject('altconfig', 'config');
            // multilingualize responses etc
            $this->objLanguage = $this->getObject('language', 'language');
            // make sure the users are who they say they are!
            $this->objUser = $this->getObject('user', 'security');  
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            if($this->objModuleCat->checkIfRegistered('geonames'))
            {
                $this->dataCapable = TRUE;
                $this->objDbGeonames = $this->getObject('dbgeonames', 'geonames');
            }
            else {
                $this->dataCapable = FALSE;
            }
        }
        catch (customException $e)
        {
            // Bail dude, something went pear shaped!
            customException::cleanUp();
            exit;
        }
    }
    
    /**
     * Method to grab geo see:Also RDF data from a user via the python upload of the geonames SWS all-geonames.txt RDF file
     * 
     * @param Parameters coming from XML-RPC transaction object $params
     * @return object of XML-RPC response object.
     */
    public function accept($params)
    {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        // finally grok the actual message out of the xmlrpc message encoding
        $msg = $param->scalarval();
        // messages are base64_encoded
        $msg = base64_decode($msg);
        $domDocument = new DOMDocument;
        $domDocument->loadXML($msg);
        $domXPath = new DOMXPath($domDocument);
        foreach ($domXPath->query('//@rdf:resource') as $keyDOM) {
            $resources[] = $keyDOM->textContent;
        }
        $resources = array_slice($resources, 5, sizeof($resources));
        
        // Now we match the geoname to the record in the db
        $name = $resources[0];
        $name = str_replace('http://www.geonames.org/','', $name);
        $name = explode('/', $name);
        $geonameid = $name[0];
        //log_debug($geonameid);
        foreach ($resources as $resource) {
            $insarr = array('resource' => $resource, 'geonameid' => $geonameid );
            log_debug($insarr); 
            if($this->dataCapable == TRUE) {
                // insert the record to the database
                $this->objDbGeonames->insertResource($insarr);
            }
        }
        
        $ret = "Success";
        $val = new XML_RPC_Value($ret, 'string');
        return new XML_RPC_Response($val);
        // Ooops, couldn't open the file so return an error message.
        return new XML_RPC_Response(0, $XML_RPC_erruser+1, $this->objLanguage->languageText("mod_packages_fileerr", "packages"));
    }
    
    /**
     * Method to grab geo see:Also RDF data from a user via the python upload of the geonames SWS all-geonames.txt RDF file
     * 
     * @param Parameters coming from XML-RPC transaction object $params
     * @return object of XML-RPC response object.
     */
    public function acceptData($params)
    {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        // finally grok the actual message out of the xmlrpc message encoding
        $msg = $param->scalarval();
        
        $user = $params->getParam(1);
        if (!XML_RPC_Value::isValue($user)) {
            log_debug($user);
        }
        // finally grok the actual username out of the xmlrpc message encoding
        $user = $user->scalarval();
        
        $pass = $params->getParam(2);
        if (!XML_RPC_Value::isValue($pass)) {
            log_debug($pass);
        }
        // finally grok the actual username out of the xmlrpc message encoding
        $pass = $pass->scalarval();
        $auth = $this->objUser->authenticateUser($user, $pass);
        if($auth != 1) {
            $ret = "Login failure!";
            $val = new XML_RPC_Value($ret, 'string');
            return new XML_RPC_Response($val);
        }
        // messages are base64_encoded
        $msg = base64_decode($msg);
        // msg is tab separated string of data
        $data = explode("\t", $msg);
        // log_debug($data);
        $userid = $this->objUser->getUserId($user);
        
        // map the data to the insert array
        $insarr = array(
            // 'id' => '',
            'userid' => $userid,
            'geonameid' => $data[0],
            'name' => $data[1],
            'asciiname' => $data[2],
            'alternatenames' => $data[3],
            'latitude' => $data[4],
            'longitude' => $data[5],
            'fclass' => $data[6],
            'fcode' => $data[7],
            'country' => $data[8],
            'cc2' => $data[9],
            'admin1' => $data[10],
            'admin2' => $data[11],
            'admin3' => $data[12],
            'admin4' => $data[13],
            'population' => $data[14],
            'elevation' => $data[15],
            'gtopo30' => $data[16],
            'timezone' => $data[17],
            'moddate' => $data[18],
        );
        // log_debug($insarr);
        // insert the row to the database
        if($this->dataCapable == TRUE) {
            $this->objDbGeonames->insertGeoname($insarr);
            $ret = "Success";
            $val = new XML_RPC_Value($ret, 'string');
            return new XML_RPC_Response($val);
        }
        else {
            $ret = "This server is not yet capable of handling geo data. Please install the geonames module!";
            $val = new XML_RPC_Value($ret, 'string');
            return new XML_RPC_Response($val);
        }
    }
}
?>
