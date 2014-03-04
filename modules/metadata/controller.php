<?php
/**
 * Metadata controller class
 *
 * Class to control the metadata module
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
 * @package   metadata
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
 * Metadata controller class
 *
 * Class to control the Events module.
 *
 * @category  Chisimba
 * @package   metadata
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class metadata extends controller
{
    public $objLanguage;
    public $objSysConfig;
    public $objUser;
    public $objConfig;
    public $objExif;
    public $objMarc;
    public $objRdf;

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
            // Get the sysconfig variables for the Jabber user to set up the connection.
            $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->objUser       = $this->getObject('user', 'security');
            $this->objModuleCat  = $this->getObject('modules', 'modulecatalogue');
            $this->objExif       = $this->getObject('exifmeta');
            $this->objIPTC       = $this->getObject('iptcmeta');
            // $this->objMarc       = $this->getObject('marcmeta');
            $this->objRdf        = $this->getObject ('rdf', 'rdfgen');
			
			// Define the paths we will be needing
			define ( "RDFAPI_INCLUDE_DIR", $this->getResourcePath ('api/', 'rdfgen'));
			include (RDFAPI_INCLUDE_DIR . "RdfAPI.php");
			
            if($this->objModuleCat->checkIfRegistered('activitystreamer'))
            {
                $this->objActStream = $this->getObject('activityops','activitystreamer');
                $this->eventDispatcher->addObserver(array($this->objActStream, 'postmade' ));
                $this->eventsEnabled = TRUE;
            } else {
                $this->eventsEnabled = FALSE;
            }
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
                echo "Metadata module. This module has no end user functionality, but will demonstrate some metadata concepts using the actions";
                break;

            case 'exif' :
                foreach(glob('/var/www/example_photos/*.jpg') as $image) {
                    echo $image."<br />";
                    $contents = file_get_contents($image);
                    $hash = sha1($contents);
                    
                    $this->objExif->getImageType($image);
                    var_dump($this->objExif->readHeaders($image, FALSE));
                    //var_dump($this->objExif->readHeadersByKey($image, "IFD0"));
                    echo $this->objExif->getExifThumb($image, 200, 200);
                }
                break;
                
            case 'iptc' :
                foreach(glob('/var/www/example_photos/*.jpg') as $image) {
                    echo $image."<br />";
                    $this->objIPTC->setImage($image);
                    $valid = $this->objIPTC->isValid();
                
                    $tagarr = $this->objIPTC->getAllTags();
                    $copyarr = $tagarr['2#116'];
                    $keywords = $tagarr['2#025'];
                    $data = array_merge($copyarr, $keywords);
                    var_dump($tagarr);
                    var_dump($this->objIPTC->getTag('originating_program'));
                    echo "<br /><br />";
                }
                break;
                
            case 'marc' :
                $ccl_query = "au='scott'";
                $recs = $this->objMarc->doQuery($ccl_query);
                foreach($recs as $rec) {
                    print $rec."<br />";
                }
                break;
                
            case 'dublincore' :
				$params = array ('url' => 'http://www.example.com/somepage.html', 'creator' => "Paul Scott", 'date' => date ( 'r' ), 'contributor' => 'some dude', 
				                 'coverage' => 'testing', 'description' => 'A test document', 'example data' => 'test', 'format' => 'html', 'identifier' => '', 
				                 'language' => 'en', 'publisher' => 'me', 'relation' => '', 'rights' => 'cc-by-sa', 'source' => 'me', 'subject' => 'testing', 
				                 'title' => 'test doc', 'type' => 'dynamic' );
				
				$message = $this->objRdf->generateDC ( $params );
				$this->appendArrayVar ( 'headerParams', "<!--" . $message . "-->" );
				$this->setVarByRef ( 'message', $message );
				var_dump($message);
				break;

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
