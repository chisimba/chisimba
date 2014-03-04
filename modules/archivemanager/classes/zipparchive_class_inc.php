<?php
/**
 *
 * Zipparchive class
 *
 * Extracts zip archives
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



	require_once "archive_class_inc.php";
	
	class zipparchive extends archive{
	
		public $Zip;

		public function init(){
                    parent::init();
		}

		public function extractTo( $path, $entries = NULL, $Zipfilename = NULL ){
                    $this->Zip = zipparchive::open($Zipfilename);
                    $this->Zip->extractTo($path, $entries);
                    
                    $this->Zip->close();
                    return $res;
                }

                private function getActiveFile($filename = NULL){
                    if($filename === NULL){
                        return parent::getfilename();
                    }else return $filename;
                }

                private function open($filename){
                    $filename = zipparchive::getActiveFile($filename);
                    $zip = new ZipArchive;
                    $res = $zip->open($filename);
                    if($res === TRUE){
                        return $zip;
                    }else{
                        exit('Error opening file: '.$filename);
                    }
                }
               
                public function addToArchive($entry, $Zipfilename = NULL){
                    $this->Zip = zipparchive::open($Zipfilename);
                    $res = $this->Zip->addFile($entry, basename($entry));
                    $this->Zip->close();
                    return $res;
                }

                public function listFiles($filename = NULL){
                    $this->Wzip = $this->getObject('wzip', 'utilities');
                    if($filename===NULL){
                        $files = $this->Wzip->listArchiveFiles(parent::getfilename());
                        if($files===FALSE){
                            return "Error reading file: ".parent::getfilename();
                        }
                    }
                    else{
                        $files = $this->Wzip->listArchiveFiles($filename);
                        if($files===FALSE){
                            return "Error reading file: ".$filename;
                        }
                    }
                    return $files;
                }

                public function preview($filename = NULL){

                    if($filename===NULL){
                        $files = zipparchive::listFiles(parent::getfilename());
                        $current_file = parent::getfilename();
                    }else{
                        $files = zipparchive::listFiles($filename);
                        $current_file = $filename;
                    }
                    
                    parent::preview($files,$current_file);
                }
                
	}


?>