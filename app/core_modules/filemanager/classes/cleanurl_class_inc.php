<?php

/**
 * Class to Clean up a URL
 *
 * This class cleans up URLs in the following way:
 * - Backstrokes (\) are converted to forward strokes (/)
 * - Double strokes or more are made into one
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to Clean up a URL
 *
 * This class cleans up URLs in the following way:
 * - Backstrokes (\) are converted to forward strokes (/)
 * - Double strokes or more are made into one
 * 
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
class cleanurl extends object
{
    
    /**
    * Constructor
    */
    public function init()
    { }
    
    /**
    * Method to clean up a url
    * @param string $url Url to clean up
    */
    public function cleanUpUrl($url)
    {
        $url = str_replace ('\\', '/', $url); // Convert backstrokes to forward strokes
        
        //$url = preg_replace('/\/{2,}/', '/', $url); // convert 
        $url = preg_replace('/\\/{2,}/', '/', $url); // Convert multiples stokes into one
        
        return $url;
    }
    
    /**
    * Method for processing a filename for better display and making it XHTML compliant
    * @param string $fileName
    */
    public function processFileName($fileName)
    {
        $fileName = htmlentities($fileName);
        $fileName = str_replace('_', ' ', $fileName);
        
        return $fileName;
    }
    
    /**
    * Method to clean up a filename. It removes all punctuation marks,
    * besides letters, numbers, round brackers, full stops
    *
    * Forward slashes are ignored and is considered part of the directory,
    * so it is possible to provide the path to a file as a parameter
    * 
    * @param  string $filename Filename or Path to File
    * @return string $filename Cleaned up Filename or Path to File
    */
    public function cleanFilename($filename)
    {
        // Clean slashes
        $this->cleanUpUrl($filename);
        
        // Clean up funny characters
        $filename = preg_replace('/[^-\\w\\s.()\/]/', '', $filename);
        $filename = preg_replace('/\\s/', '_', $filename);
        
        // Rename .php files to .phps
        $filename = preg_replace('/(\\.php)\\Z/', '.phps', $filename);
        $filename = preg_replace('/(\\.php3)\\Z/', '.phps', $filename);
        $filename = preg_replace('/(\\.php4)\\Z/', '.phps', $filename);
        $filename = preg_replace('/(\\.php5)\\Z/', '.phps', $filename);
        
        // Various other possible characters that may cause problems
        $filename = str_replace(' ', '_', $filename);
        $filename = str_replace("'", '', $filename);
        $filename = str_replace('"', '', $filename);
        $filename = str_replace('?', '', $filename);
        
        return $filename;
    }
}
?>