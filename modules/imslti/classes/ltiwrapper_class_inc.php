<?php
/**
 *
 * IMS LTI Wrapper
 *
 * Builds a wrapper for the IMS LTI content. Currently it uses a simple
 * IFRAME wrapper, as recommended by the specification
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
 * @version   $Id: tweetbox_class_inc.php 8227 2008-03-27 20:05:32Z dkeats $
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
 * IMS LTI Wrapper
 *
 * Builds a wrapper for the IMS LTI content. Currently it uses a simple
 * IFRAME wrapper, as recommended by the specification
*
* @author Derek Keats
* @package imslti
*
*/
class ltiwrapper extends object
{
	
    /**
    * @var string $width The width of the wrapper
    * @access private
    */
    private $width;

    /**
    * @var string $height The height of the wrapper
    * @access private
    */
    private $height;

    /**
     *
    /**
    * @var bool $frameborder The frameborder of the wrapper
    *  must be 0 or 1
    * @access private
    */
    private $frameborder;


    /**
     * Description for public
     * @var    string
     * @access public
     */
    private $marginheight;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    private $marginwidth;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    private $name;


    /**
    *
    * Constructor for the ltiwrapper class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
		$this->height = "1200";
		$this->width = "100%";
		$this->frameborder = 0;
		$this->marginheight = 0;
		$this->marginwidth = 0;
    }
    
    public function set($param, $value)
    {
    	$this->$param = $value;
    }

    /**
    *
    * Method to render the tweetbox
    *
    * @access public
    * @return string The rendered tweetbox
    *
    */
    public function show($uri)
    {
        switch ($uri)
        {
            case "504":
                return "<span class=\"error\">" 
                  . $this->objLanguage->languageText("mod_imslti_error_urlnotfound", "imslti")
                  . "</span>";
                break;
            default:
            	$this->loadClass("iframe", "htmlelements");
            	$iframe = new iframe();
            	$iframe->width=$this->width;
            	$iframe->height=$this->height;
            	$iframe->frameborder = $this->frameborder;
            	$iframe->marginheight = $this->marginheight;
            	$iframe->marginwidth = $this->marginwidth;
            	$iframe->src = $uri;
            	return $iframe->show();
        }
    }


}
?>