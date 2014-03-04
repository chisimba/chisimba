<?php
/**
 *
 * jQuery oembed interface
 *
 *  A simple jQuery plugin that uses OEmbed API to help displaying embedded
 *  content (such as photos or videos) in your website. It makes use of the
 *  jquery-oembed code developed by Richard Chamorro, and available from:
 *      http://code.google.com/p/jquery-oembed/
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
 * @package   oembed
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: jqoembed_class_inc.php 1 2009-12-23 16:48:15Z dkeats $
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
* jQuery oembed interface
*
*  A simple jQuery plugin that uses OEmbed API to help displaying embedded
*  content (such as photos or videos) in your website. It makes use of the
*  jquery-oembed code developed by Richard Chamorro, and available from:
*      http://code.google.com/p/jquery-oembed/
*
* @author Derek Keats
* @package oembed
*
*/
class jqoembed extends object
{
   
    /**
    *
    * Constructor for the jqoembd class
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
    }


    /**
    *
    * Method to load the oembed jQuery plugin
    * 
    * @access public
    * @return VOID
    *
    */
    public function loadOembedPlugin()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("starfishmod/jquery.oembed.js", "oembed")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        $css = '<link rel="stylesheet" type="text/css" href="' 
          . $this->getResourceUri("starfishmod/jquery.oembed.css", "oembed")
          . '">';
        $this->appendArrayVar('headerParams', $css);
        return TRUE;
    }

    /**
    *
    * Method to load the oembed embed-method-fill script based on the
    * flickr-embedmethod-fill-example.html example.
    *
    * @param string $divClass The name of the class used for the display
    *   in the anchor tag.
    * @access public
    * @return string The embeddable script.
    *
    */
    public function getEmbedFill($divClass="oembed")
    {
        return "<script type=\"text/javascript\">\n"
	  . "jQuery(document).ready(function() {\n"
          . "jQuery(\"." . $divClass . "\")."
          . $divClass . "(null, {embedMethod: \"fill\"});\n"
	  . "});\n"
          . "</script>";
    }

    /**
    *
    * Method to load the oembed embed-method-append script based on the
    * flickr-embedmethod-append-example.html example.
    *
    * @param string $divClass The name of the class used for the display
    *   in the anchor tag.
    * @access public
    * @return string The embeddable script.
    *
    */
    public function getEmbedAppend($divClass="oembed")
    {
        return "<script type=\"text/javascript\">\n"
	  . "jQuery(document).ready(function() {\n"
          . "jQuery(\"." . $divClass . "\")."
          . $divClass . "(null, {embedMethod: \"append\"});\n"
	  . "});\n"
          . "</script>";
    }

    /**
    *
    * Method to load the oembed default embeddable script 
    *
    * @param string $divClass The name of the class used for the display
    *   in the anchor tag.
    * @access public
    * @return string The embeddable script.
    *
    */
    public function getDefaultEmbed($divClass="oembed")
    {
        return "<script type=\"text/javascript\">\n"
          . "jQuery(document).ready(function() {\n"
          . "jQuery(\"a." . $divClass . "\")."
          . $divClass . "();\n"
          . "});"
          . "</script>";
    }
    
    /**
    *
    * Method to load the div for explicit inclusion. This will rarely, if ever,
    * be used except in testing.
    *
    * @access public
    * @return VOID
    *
    */
    public function getExplicitDiv($divClass='oembed')
    {
        return '<div id="' . $divClass . '"></div>';
    }
}
?>