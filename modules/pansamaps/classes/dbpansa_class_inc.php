<?php
/**
 *
 * PANSA database class
 *
 * PHP version 5.1.0+
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
 * @package   pansamaps
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * PANSA database class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package pansamaps
 *
 */
class dbpansa extends dbtable {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objWashout String object property for holding the washout object
     *
     * @access public
     */
    public $objWashout;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;

    /**
     * @var string $objCurl String object property for holding the curl object
     *
     * @access public
     */
    public $objCurl;

    public $objLangCode;
    public $objUtils;
    public $objTwitOps;

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        parent::init('tbl_pansa_venues');
        $this->objLanguage  = $this->getObject('language', 'language');
        $this->objConfig    = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objWashout   = $this->getObject('washout', 'utilities');
        $this->objUser      = $this->getObject('user', 'security');
        $this->objCurl      = $this->getObject('curlwrapper', 'utilities');
        $this->objLangCode  = $this->getObject('languagecode', 'language');
        $this->objTags      = $this->getObject('dbtags', 'tagging');
    }
    
    public function getData() {
        $dataArray = $this->getAll();
        return $this->makeMapMarkers($dataArray);
    }
    
    public function makeMapMarkers($dataArray) {
        // build up a set of markers for a google map
        $head = "<markers>";
        $body = NULL;
        foreach($dataArray as $data) {
            if($data['geolat'] == "" || $data['geolon'] == "") {
                continue;
            }
            else {
                $body .= '<marker lat="'.$data['geolat'].'" lng="'.$data['geolon'].'" info="'.htmlentities($data['venuename']."<br />".$data['venuedescription']).'" />';
            }
        }
        $tail = "</markers>";
        $data = $head.$body.$tail;
        $path = $this->objConfig->getModulePath()."pansamaps/markers.xml";
        if(!file_exists($path)) {
            touch($path);
            chmod($path, 0777);
        }
        else {
            unlink($path);
            touch($path);
            chmod($path, 0777);
        }
        file_put_contents($path, $data);
        
        return $data;
    }
    
    public function addRecord($data) {
        $this->insert($data, 'tbl_pansa_venues');
    }
    
    public function updateRecord($id, $data) {
        return $this->update('id', $id, $data, 'tbl_pansa_venues');
    }
    
    public function deleteRecord($recid) {
        return $this->delete('id', $recid, 'tbl_pansa_venues');
    }
    
    public function searchRecords($keyword) {
        if($keyword == '*') {
            return $this->getAll();
        }
        else {
            return $this->getAll("WHERE venuename LIKE '%%$keyword%%'");
        }
    }
    
    public function getSingle($id) {
        return $this->getAll("WHERE id = '$id'");
    }
    
    public function exportData() {
        $data = $this->getAll();
        $counter = 0;
        $csv = NULL;
        foreach($data as $rec) {
            //var_dump($rec);
            if($counter == 0) {
                $keys = array_keys($rec);
                $keys = $this->array_trim($keys, 0);
                $keys = $this->array_trim($keys, 0);
                array_pop($keys);
                array_pop($keys);
                $csv[] = $keys;
            }
            $csv[] = array($rec['venuename'], $rec['venueaddress1'], $rec['venueaddress2'], $rec['city'], $rec['zip'], $rec['phonecode'], $rec['phone'], $rec['faxcode'], $rec['fax'], $rec['email'], $rec['url'], $rec['contactperson'], $rec['otherinfo'], $rec['venuedescription'], $rec['geolat'], $rec['geolon']);
            $counter++;   
        }
        $fp = fopen('file.csv', 'w');
        foreach($csv as $item) {
            fputcsv($fp, $item);
        }
        fclose($fp);
        $this->downloadCSV('file.csv');
        die();
        //unlink('file.csv');
    }
    
    private function array_trim ( $array, $index ) {
        if ( is_array ( $array ) ) {
            unset ( $array[$index] );
            array_unshift ( $array, array_shift ( $array ) );
            return $array;
        }
        else {
            return false;
        }
   }
   
   private function downloadCSV($yourfile) {
       $fp = @fopen($yourfile, 'rb');
       if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
       {
           header('Content-Type: "application/octet-stream"');
           header('Content-Disposition: attachment; filename="pansadata.csv"');
           header('Expires: 0');
           header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
           header("Content-Transfer-Encoding: binary");
           header('Pragma: public');
           header("Content-Length: ".filesize($yourfile));
       }
       else
       {
           header('Content-Type: "application/octet-stream"');
           header('Content-Disposition: attachment; filename="pansadata.csv"');
           header("Content-Transfer-Encoding: binary");
           header('Expires: 0');
           header('Pragma: no-cache');
           header("Content-Length: ".filesize($yourfile));
        }
        fpassthru($fp);
        fclose($fp);
    }
}
?>
