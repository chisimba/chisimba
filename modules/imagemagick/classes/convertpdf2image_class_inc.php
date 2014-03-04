<?php
/**
 *
 * Image Magick
 *
 * A set of functions to do image conversions with imagemagick
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
 * @package   imagemagick
 * @author    Tohir Solomons _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbimagemagick.php,v 1.2 2008-01-08 13:07:15 dkeats Exp $
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

/**
*
* Database accesss class for Chisimba for the module imagemagick
*
* @author Tohir Solomons
* @package imagemagick
*
*/
class convertpdf2image extends object
{

    /**
    *
    * Intialiser for the imagemagick controller
    * @access public
    *
    */
    public function init()
    { }
    
    /**
     * Method to convert the PDF to an image
     * @param string $source Path to PDF
     * @param string $destination Destination of Image
     * @return boolean Whether image was created or not
     */
    public function convert($source, $destination)
    {
        // Check that Source File exists
        if (!file_exists($source)) {
            return FALSE;
        }
        
        // If destination file exists, return TRUE;
        if (file_exists($destination)) {
            return TRUE;
        }
        
        // Check that directory exists
        $objMkDir = $this->getObject('mkdir', 'files');
        $objMkDir->mkdirs(dirname($destination));
        
        // Command: convert 0071377840.pdf[0] bookpreview.jpg
        // -geometry '200x200'
        
        // Create command and log it
        $command = "convert  -verbose  {$source}[0] $destination ";
        log_debug($command);
        
        // Run command and log results
        $result = shell_exec($command);
        log_debug($result);
        
        // Check whether successful or not
        if (file_exists($destination)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Method to convert the PDF to an image and create a thumbnail
     * @param string $source Path to PDF
     * @param string $destination Destination of Thumbnail
     * @return boolean Whether image was created or not
     */
    public function createThumbnail($source, $destination)
    {
        // Check that Source File exists
        if (!file_exists($source)) {
            return FALSE;
        }
        
        // If destination file exists, return TRUE;
        if (file_exists($destination)) {
            return TRUE;
        }
        
        // Check that directory exists
        $objMkDir = $this->getObject('mkdir', 'files');
        $objMkDir->mkdirs(dirname($destination));
        
        // Command: convert 0071377840.pdf[0] bookpreview.jpg
        // -geometry '200x200'
        
        // Create command and log it
        $command = "convert  -verbose -geometry '100x100' {$source}[0] $destination ";
        log_debug($command);
        
        // Run command and log results
        $result = shell_exec($command);
        log_debug($result);
        
        // Check whether successful or not
        if (file_exists($destination)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
?>
