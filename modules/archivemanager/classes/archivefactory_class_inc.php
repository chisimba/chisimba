<?php
    /**
 *
 * Archive Object Factory
 *
 * Manages the creation of archive objects
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
 * @package   archivemanager
 * @author    Mofolo Mamabolo <mofolom@gmail.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.5
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

	
	class archivefactory extends object{

                private $objArchive;

		public function init() {
                    $this->objMime = $this->getObject('mimetypes','files');
                }

                public function getSupportedTypes(){
                    $results = array("application/zip","application/x-rar-compressed");
                    return $results;
                }
		public function open( $filename = NULL, $passwd = NULL ){

                        $mimetype = $this->objMime->getMimeType($filename);
                        
			switch( $mimetype ){
				case "application/zip":{
					$this->objArchive = $this->getObject('zipparchive','archivemanager');
					break;
				}
					
				case "application/x-rar-compressed":{
					$this->objArchive = $this->getObject('rararchive','archivemanager');
					break;
				}
					
				default:{ 
					$this->objArchive = $this->getObject('unknownarchive','archivemanager');
					break;
				}
			}

                        if($this->objArchive!==NULL){
                            $this->objArchive->setfilename($filename);
                            $this->objArchive->setpassword($passwd);
                        }
                        
			return $this->objArchive;
		}
	}

?>
