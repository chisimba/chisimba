<?php

/**
 * Context Image
 *
 * CThis class allows users to retrieve and set an image as their context image
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
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Context Image
 *
 * CThis class allows users to retrieve and set an image as their context image
 *
 * @category  Chisimba
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class contextimage extends object {

    /**
     * Constructor
     */
    public function init() {
        $this->objFiles = $this->getObject ( 'dbfile', 'filemanager' );
        $this->objThumbnails = $this->getObject ( 'thumbnails', 'filemanager' );
        $this->objConfig = $this->getObject ( 'altconfig', 'config' );
        $this->objMkdir = $this->getObject ( 'mkdir', 'files' );
        $this->objCleanUrl = $this->getObject ( 'cleanurl', 'filemanager' );
    }

    /**
     * Method to retrieve a context image
     *
     * @param string $contextCode Context Code
     * @return string Path to Image - Still needs html img tags added
     */
    public function getContextImage($contextCode) {
        $image = $this->objConfig->getcontentPath () . '/contextimage/' . $contextCode . '.jpg';

        if (file_exists ( $image )) {
            return $this->objCleanUrl->cleanUpUrl ( $image );
        } else {
            return FALSE;
        }

    }

    /**
     * Method to set a context image
     *
     * @param string $contextCode Context Code
     * @param string $fileId Record Id of the file from file manager
     */
    public function setContextImage($contextCode, $fileId) {
        $this->checkContextImageFolder ();

        $filename = $this->objFiles->getFileName ( $fileId );

        if ($filename == FALSE) {
            return FALSE;
        } else {
            $image = $this->objThumbnails->getThumbnail ( $fileId, $filename );

            if ($image != FALSE) {
                $destination = $this->objConfig->getcontentPath () . '/contextimage/' . $contextCode . '.jpg';

                if (file_exists ( $destination )) {
                    $canCopy = unlink ( $destination );
                } else {
                    $canCopy = TRUE;
                }

                if ($canCopy) {
                    copy ( $image, $destination );
                }
            }
        }
    }

    /**
     * Method to remove an existing context image
     *
     * @param string $contextCode Context Code
     * @return boolean Whether the image has been successfully removed or not
     */
    public function removeContextImage($contextCode) {
        $destination = $this->objConfig->getcontentPath () . '/contextimage/' . $contextCode . '.jpg';

        if (file_exists ( $destination )) {
            return unlink ( $destination );
        } else {
            return TRUE;
        }
    }

    /**
     * Method to check that the user folder for uploads, and subfolders exist
     *
     * @param  string  $userId UserId of the User
     * @return boolean True if folder exists, else False
     */
    private function checkContextImageFolder() {
        // Set Up Path
        $path = $this->objConfig->getcontentBasePath () . '/contextimage';
        $path = $this->objCleanUrl->cleanUpUrl ( $path );

        // Check if Folder exists, else create it
        $result = $this->objMkdir->mkdirs ( $path );

        return $result;
    }

}

?>