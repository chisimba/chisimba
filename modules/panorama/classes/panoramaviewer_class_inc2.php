<?php
/**
 * 
 * This module provides a panorama view from a stiched liner panoramic image. 
 * 
 * Provides a panoramic viewr. You can shoot panoramas by taking a series of overlapping images and stitch them together with the Gimp. This module is necessary for the panorama filter ([PANORAMA: url=imagepath]
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
 * @package   helloforms
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: panoramaviewer_class_inc2.php 11928 2008-12-29 21:15:02Z charlvn $
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
* Controller class for Chisimba for the module panorama
*
* @author Derek Keats
* @package panorama
*
*/
class panoramaviewer extends object
{
    
    /**
    * 
    * Intialiser for the panorama controller
    * @access public
    * 
    */
    public function init()
    {
        //Set the parent table here
    }
    
    /**
    * 
    * Method to return the panorama applet
    * 
    * @param String $image The path to the panorama image
    * @return String The formatted applet
    * @access Public
    * 
    */
    public function show($image)
    {
    	
        $ret = "<applet code=\"ptviewer.class\" archive=\"ptviewer.jar\" width=600 height=450>"
		  . "<param name=file value=\"images/chaberton.jpg\">"
		  . "<param name=cursor value=\"MOVE\">"
		  . "<param name=pan value=-105>"
		  . "<param name=showToolbar value=\"true\">"
		  . "<param name=imgLoadFeedback value=\"false\">"
          . "<param name=hotspot0 value=\"X21.3 Y47.7 u'Sample27L2.htm' n'Hotspot description'\">"
          . "</applet>";
    
    }
    
}
?>
