<?php
/**
 *
 * Openzoom module operations class
 *
 * The openzoom ops clas provides functionality to build the openzoom image
 * viewer to replace an image. The image needs to have been processed first
 * to convert it to the format used bu openzoom.
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
 * @package   twitter
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: twitterremote_class_inc.php 14230 2009-08-07 12:00:08Z paulscott $
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
 * Openzoom module operations class
 *
 * The openzoom ops clas provides functionality to build the openzoom image
 * viewer to replace an image. The image needs to have been processed first
 * to convert it to the format used bu openzoom.
*
* @author Derek Keats <derek@dkeats.com>
* @package openzoom
*
*/
class openzoomops extends object
{

    /**
    *
    * Constructor for the openzoomops class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Your code goes here.
    }
    
    /**
    * 
    *  Method to get the <IMG> tag with the size and the openzoom source
    *  XML file for the dynamic substitution. Images must include all 
    *  the openzoomified bits.
    *  
    *  @param string $width The width of the default image in in pixels
    *  @param string $height The width of the default image in in pixels
    *  @param string $imagePath The full path to the source image
    *  @param string $xmlFile The full path to the XML file for openzoom
    *  @access public
    *  @return string The formatted image tag
    *  
    *  
    */
    public function getImage($width, $height, $imagePath, $xmlFile) {
    	$img = '<img src="' . $imagePath  . '" width="'
    	  . $width . '" height="' . $height . '" openzoom:source="' 
    	  . $xmlFile . '" openzoom:viewerpath="' . $this->getPlayer()
    	  . '" />';
        return $img;
    }
    
    /**
    * 
    *  Method to get the flash player full web path
    *  @access public
    *  @return string The resource path to the player
    *  
    */
    public function getPlayer() {
    	$player = $this->getResourceUri('flash/');
    	//die($player);
    	return $player;
    }
    
    public function loadJsLib()
    {
        $js = '<script src="' 
          . $this->getResourceUri('js/jquery.openzoom.js') 
    	  . '" type="text/javascript">';
        return $this->appendArrayVar('headerParams', $js);
    }
    
    public function buildJavascript()
    {
        
    }
    
    public function getXmlFileFromUrl($url)
    {
        $base = "../../../../";
        $url=str_replace("http://", "", $url);
        $url=str_replace(".jpg", "", $url);
        $pieces = explode("/", $url);
        $ignore = TRUE;
        $path = "";
        $numOfPieces = count($pieces);
        $counter = 0;
        foreach ($pieces as $piece) {
            $counter++;
            if ($piece == 'usrfiles') {
                $ignore = FALSE;
            }
            if (!$ignore) {
                $path .= $piece . "/";
            }
        }
        //$path = "usrfiles/users/7924090825/openzoom/testing/me/image.xml";
        return $base . $path . "image.xml";
    }

}
?>
