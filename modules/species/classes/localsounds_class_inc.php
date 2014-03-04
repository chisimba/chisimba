<?php
/**
 *
 * Access to local sound files
 *
 * Access to local sound files, for example birdsongs
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
 * @package   species
 * @author    Derek Keats derek@localhost.local
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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

/**
*
* Access to local sound files
*
* Access to local sound files, for example birdsongs
*
*
* @package   species
* @author    Derek Keats derek@localhost.local
*
*/
class localsounds extends object
{
    
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     *
     * @var string Object The config object 
     * @access public
     * 
     */
    public $objConfig;

    /**
    *
    * Intialiser for the species operations class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the config object
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }
    
    public function soundsExist($fullName)
    {
        // Convert the name to the folder name.
        $fullName = strtolower($fullName);
        $fullName = str_replace("  ", "_", $fullName);
        $fullName = str_replace(" ", "_", $fullName);
        // Get the type of grouping from the session.
        $type = $this->getSession('speciesgroup', 'birds', 'species');
        $userList = $this->objConfig->getValue('species_userlist', "species");
        if (strstr($userList, ",")) {
            // There are more than one.
            $arUsers =  array($userList);
        } else {
            $arUsers =  explode(",", $userList);
        }
        foreach ($arUsers as $user) {
            $lookDir = 'usrfiles/users/' . $user . '/' . $type . '/' . $fullName;
            if (file_exists($lookDir)) {
                if (file_exists($lookDir . '/sounds.xml')) {
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
    
    /**
     * 
     * Get all the existing local sounds, embeded in a player
     * 
     * @param string $fullName The common fullname of the species
     * @return string One or more embedded players
     * @access public
     * 
     */
    public function getSounds($fullName)
    {
        $fullName = strtolower($fullName);
        $fullName = str_replace("  ", "_", $fullName);
        $fullName = str_replace(" ", "_", $fullName);
        $userList = $this->objConfig->getValue('species_userlist', "species");
        if (strstr($userList, ",")) {
            // There are more than one
            $arUsers =  array($userList);
        } else {
            $arUsers =  explode(",", $userList);
        }
        $ret = NULL;
        // get the type of grouping from the session.
        $type = $this->getSession('speciesgroup', 'birds', 'species');
        $objPlayer = $this->getObject('soundplayer', 'species');
        foreach ($arUsers as $user) {
            $lookDir = 'usrfiles/users/' . $user . '/' . $type . '/' . $fullName;
            if (file_exists($lookDir)) {
                if(is_dir($lookDir)) {
                    if (file_exists($lookDir . '/sounds.xml')) {
                        $sounds = simplexml_load_file($lookDir . '/sounds.xml');
                        foreach ($sounds as $sound) {
                            $soundFile = $lookDir . '/' . $sound->filename;
                            $caption = $sound->caption;
                            $licenseCode = strtolower($sound->licence);
                            $embeded = $objPlayer->embedAudio($soundFile);
                            $doc = new DOMDocument('UTF-8');
                            $div = $doc->createElement('div');
                            $div->setAttribute('class', 'species_sounds');
                            $frag = $doc->createDocumentFragment(); 
                            $frag->appendXML($embeded);
                            $div->appendChild($frag);
                            $br = $doc->createElement('br');
                            $div->appendChild($br);
                            $div->appendChild($doc->createTextNode($caption));
                            $br = $doc->createElement('br');
                            $div->appendChild($br);
                            $webUrl=$sound->recordedby->website;
                            if ($webUrl !== "" && $webUrl !== NULL) {
                                $div->appendChild($doc->createTextNode("Sound by: "));
                                $a = $doc->createElement('a');
                                $a->setAttribute('href', $webUrl);
                                $technician = $sound->recordedby->fullname;
                                $a->appendChild($doc->createTextNode($technician));
                                
                                $div->appendChild($a);
                            } else {
                                $technician = "Sound by: " . $sound->recordedby->fullname;
                                $div->appendChild($doc->createTextNode($technician));
                            }
                            
                            // Add the creative commons license icon
                            if ($licenseCode !== NULL) {
                                $objCc = $this->getObject('displaylicense', 'creativecommons');
                                $lic = $objCc->show($licenseCode);
                                if ($lic !=="") {
                                    $frag = $doc->createDocumentFragment(); 
                                    $frag->appendXML($lic);
                                    $div->appendChild($frag);
                                }
                            }
                            $doc->appendChild($div);
                            $ret .= $doc->saveHTML();
                        }
                    }
                }
            }
        }
        return $ret;
    }
}
?>