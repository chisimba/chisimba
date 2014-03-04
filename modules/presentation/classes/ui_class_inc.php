<?php
/**
 * 
 * UI class for S5 presentation module
 * 
 * UI class for S5 presentation module
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
 * @package   presentation
 * @author    Derek Keats
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: ui_class_inc.php 20461 2011-01-31 17:58:07Z dkeats $
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
* UI class for S5 presentation module
* 
* @author Derek Keats
* @package presentation
*
*/
class ui extends object
{
    /**
    * 
    * var string $resourceBase The URL for the resource directory
    * of this module
    * @access public
    * 
    */
    public $resourceBase;
    
    /**
    * 
    * Intialiser for the presentation  UI class
    * @access public
    * 
    */
    public function init()
    {
        $this->resourceBase = $this->getResourceUri('','presentation');
        // Load scriptaclous since we can no longer guarantee it is there
        $scriptaculous = $this->getObject('scriptaculous', 'prototype');
        $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
    }
    
    public function getCss()
    {
        
        $ret = "<!-- style sheet links -->"
          . "<link rel=\"stylesheet\" href=\"" . $this->resourceBase . "ui/default/slides.css\" type=\"text/css\" media=\"projection\" id=\"slideProj\" />\n"
          . "<link rel=\"stylesheet\" href=\"" . $this->resourceBase . "ui/default/outline.css\" type=\"text/css\" media=\"screen\" id=\"outlineStyle\" />\n"
          . "<link rel=\"stylesheet\" href=\"" . $this->resourceBase . "ui/default/print.css\" type=\"text/css\" media=\"print\" id=\"slidePrint\" />\n"
          . "<link rel=\"stylesheet\" href=\"" . $this->resourceBase . "ui/default/opera.css\" type=\"text/css\" media=\"projection\" id=\"operaFix\" />\n";
        return $ret;
    }
    
    public function getInlineCss()
    {
        $ret = "\n\n<!-- embedded styles -->\n
        <style type=\"text/css\" media=\"all\">\n
        .imgcon {width: 525px; margin: 0 auto; padding: 0; text-align: center;}\n
        #anim {width: 270px; height: 320px; position: relative; margin-top: 0.5em;}\n
        #anim img {position: absolute; top: 42px; left: 24px;}\n
        img#me01 {top: 0; left: 0;}\n
        img#me02 {left: 23px;}\n
        img#me04 {top: 44px;}\n
        img#me05 {top: 43px;left: 36px;}\n
        </style>\n\n";
        return $ret;
    }
    
    public function getScript()
    {
        $ret = "<!-- S5 JS -->\n
          <script src=\"" . $this->resourceBase . "ui/default/slides.js\" type=\"text/javascript\"></script>\n";
        return $ret;
    }
    
    public function getLayout($presHeader=NULL, $presFooter=NULL)
    {
        $ret = "\n\n<div class=\"layout\">\n
          <div id=\"controls\"><!-- DO NOT EDIT --></div>\n
          <div id=\"currentSlide\"><!-- DO NOT EDIT --></div>\n
          <div id=\"header\">$presHeader</div>\n
          <div id=\"footer\">$presFooter</div>\n
          </div>\n\n";
    }
    
}
?>
