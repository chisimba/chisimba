<?php
ini_set('memory_limit', -1);
/**
 *
 * racemap kml helper class
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
 * @package   racemap
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
 * racemap kml helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package racemap
 *
 */
class racemapkml extends object {
    
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
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        $this->objDbRace     = $this->getObject('dbracemap');
    }
    
    /**
     * Method to create a valid KML document from a stored GPS track
     *
     * @param string $id the track ID
     * @access public
     * @return string $metaid the metadata id used
     */
    public function kmlFromTrk($id) {
        $meta = $this->objDbRace->getMetaFromId($id);
        // var_dump($meta); die();
        $name = $meta[0]['name'];
        $description = $meta[0]['description'];
        $metaid = $meta[0]['id'];
        // echo $metaid;
        $kml = NULL;
        $kml .= '<?xml version="1.0" encoding="UTF-8"?>
                 <kml xmlns="http://www.opengis.net/kml/2.2">
                 <Document>
                 <name>'.$name.'</name>
                 <Style id="yellowLineGreenPoly">
                     <LineStyle>
                         <color>7dff0000</color>
                         <width>6</width>
                     </LineStyle>
                     <PolyStyle>
                         <color>7f00ff00</color>
                     </PolyStyle>
                 </Style>';
        $kml .= "<Folder>
                     <name>$name</name>
                     <visibility>0</visibility>
                     <description>$description</description>
                    <Placemark>
                        <name>$name</name>
                        <styleUrl>#yellowLineGreenPoly</styleUrl>
                        <visibility>0</visibility>
                        <description><![CDATA[$description]]></description>
                        <LineString>
                            <tessellate>1</tessellate>
                            <coordinates>";
        // write it down
        $path = $this->objConfig->getSiteRootPath().'/usrfiles/graphs/';
        if(!file_exists($path)) {
            mkdir($path, 0777);
        }
        $fp1 = fopen($path.$metaid.'.kml', 'w');
        fwrite($fp1, $kml);
        fclose($fp1);      
        $kml = NULL;
                      
        $stop = $this->objDbRace->countPoints($metaid);
        $x = 0;
        $kpts = NULL;
        $ptsarr = $this->objDbRace->getPoints($metaid, $x, $stop);
        foreach($ptsarr as $pts) {
            $lat = $pts['lat'];
            $lon = $pts['lon'];
            $kml .= $lon.",".$lat.",0"." \r\n";
            $fp2 = fopen($path.$metaid.'.kml', 'a');
            fwrite($fp2, $kml);
            $kml = NULL; 
        }          
        fclose($fp2);              
        $ptsarr = NULL;
        
        //$kml .= $kpts;
        $kml .=           "</coordinates>
                        </LineString>
                    </Placemark>
                </Folder>
              </Document>
        </kml>";
        $fp3 = fopen($path.$metaid.'.kml', 'a');
        fwrite($fp3, $kml);
        fclose($fp3);
        
        return $metaid;     
        
    }
    
}
?>
