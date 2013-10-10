<?php
/**
 *
 * MARC helper class
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
 * @package   metadata
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
 * MARC helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package metadata
 *
 */
class marcmeta extends object {

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
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * @var array $ccl_fields Define the usable fields for our CCL query. Note these should be mapped to SysConfigs
     * @TODO: Map ccl_fields to sysconfig vars
     *
     * @access public
     */
    public $ccl_fields = array(
        "ti" => "1=4",
        "au"  => "1=1003",
        "isbn" => "1=7"
    );
    
    /**
     * @var string $conn String object property for holding the YAZ server connection object
     *
     * @access public
     */
    public $conn;

    /**
     * @var string $ccl_results the array that will hold the parsed results
     *
     * @access public
     */
    public $ccl_results = array();


    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        if(!function_exists("yaz_connect")) {
            die("You need to install the PHP YAZ extension to use these functions");
        }
        // Get the MARC handling code from lib/pear
        $this->getPearResource("File/MARC.php");
        $this->yazConnect();
    }
    
    private function yazConnect() {
        $yazserver = 'innopac.wits.ac.za:210/INNOPAC';
        $this->conn = yaz_connect($yazserver);
        yaz_ccl_conf($this->conn, $this->ccl_fields);
        return;
    }
    
    public function doQuery($ccl_query) {
        // Parse the CCL query into yaz's native format
        $result = yaz_ccl_parse($this->conn, $ccl_query, $this->ccl_results);
        if (!$result) {
            return $this->ccl_results['errorstring'];
            exit();
        }

        // Submit the query
        $rpn = $this->ccl_results['rpn'];
        yaz_search($this->conn, 'rpn', $rpn);
        yaz_wait();

        // Any errors trying to retrieve this record?
        $error = yaz_error($this->conn);
        if ($error) {
            return $error;
            exit();
        }

        // Retrieve the first MARC record as raw MARC
        $rec = yaz_record($this->conn, 1, "raw");
        if (!$rec) {
            // error, no results found
            return FALSE; 
            exit();
        }

        // Parse the retrieved MARC record
        $this->marc_file = new File_MARC($rec, File_MARC::SOURCE_STRING);
        
        while ($marc_record = $this->marc_file->next()) {
            $recs[] = $marc_record; 
            // print $marc_record;
            // print "<br />";
        }
        
        return $recs;
    }
}
?>
