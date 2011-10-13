<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * zipmanager
 *
 * Manages ZIP files.
 *
 * @category  Chisimba
 * @package utilities
 * @author Wesley Nitsckie
 * @copyright 2004, 2011 University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version $Id$
 * @link      http://avoir.uwc.ac.za
 */

class zipmanager extends object {

    /**
     * Constructor
     */

    function init(){
    }

    /**
     * Create a zip archive.
     *
     * @param string $zipFN zip filename
     * @param array $files Files to zip
     * @return void
     */

    public function packFilesZip($zipFN, $files)
    {
        if (!extension_loaded('zip')) {
            throw new customException($this->objLanguage->languageText("mod_utilities_nozipext", "utilities"));
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFN, ZIPARCHIVE::CREATE) !== TRUE) {
            log_debug("Zip pack Error: cannot open [$zipFN]\n");
            throw new customException($this->objLanguage->languageText("mod_utilities_nozipcreate", "utilities"));
        } else {
            foreach ($files as $f) {
                $zip->addFile($f, $f);
            }
            $zip->close();
            return $zipFN;
        }
    }

    /**
     * Unzip files from an archive.
     *
     * @param string $zipFN zip filename
     * @param string $dest Destination path
     * @return bool Status
     */

    public function unPackFilesFromZip($zipFN, $dest)
    {
        if (!extension_loaded('zip')) {
            throw new customException($this->objLanguage->languageText("mod_utilities_nozipext", "utilities"));
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFN) !== TRUE) {
            return FALSE;
            //log_debug("Zip unPack Error: cannot open <$zipFN>\n");
            //throw new customException($this->objLanguage->languageText("mod_utilities_nozipopen", "utilities"));
        } else {
            $zip->extractTo($dest);
            $zip->close();
            return TRUE;
        }
    }
}
?>