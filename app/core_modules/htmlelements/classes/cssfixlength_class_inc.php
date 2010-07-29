<?php

/**
 * cssfixlength_class_inc.php
 *
 * This file contains the code for fixing the column lengths in the case
 * of two or three column layouts for module templates. It is used exclusively
 * by thge csslayout class.
 *
 * Created by Derek Keats from code originally by Tohir Solomons <tsolomons@uwc.ac.za>
 *
 *
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
*/

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class
require_once("ifhtml_class_inc.php");


/**
 *
 * cssfixlength_class_inc.php
 *
 * This file contains the code for fixing the column lengths in the case
 * of two or three column layouts for module templates. It is used exclusively
 * by the csslayout class.
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Derek Keats from code originally by Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.sitepoint.com/print/exploring-limits-css-layout
 */
class cssfixlength extends object
{

    /**
     *
     * skinEngine seems to be a fake concept introduced by Charl Mert for a
     * specific site.
     *
     * @todo Remove this and deprecate it
     *
     * @var string Object
     *
     */
    public $skinEngine;

    /**
    *
    * Constructor Method for the class
    *
    */
    public function init()
    {
        $this->skinEngine = "";
    }

    /**
    *
    * Method to return the JavaScript that fixes a two column css layout using
    * Javascript for the older skins. Do not use this method with version 2
    * skins.
    *
    * @access public
    * @return string $fixLayoutScript the JavaScript that goes in the header
    */
    public function fixTwoColumnLayoutJavascript()
    {
        $fixLayoutScript ='
        <script type="text/javascript">
        // <![CDATA[
        function adjustLayout()
        {
            //Inhouse browser detection
            var browser=navigator.appName;
            var b_version=navigator.appVersion;
            var version=parseFloat(b_version);

            // Get natural heights
            var cHeight = xHeight("contentcontent");
            var lHeight = xHeight("leftcontent");
            var rHeight = xHeight("rightcontent");

            // Find the maximum height
            var maxHeight = Math.max(cHeight, Math.max(lHeight, rHeight));

            // Assign maximum height to all columns
            if ((browser!="Microsoft Internet Explorer")) {

              xHeight("content", maxHeight);
              xHeight("left", maxHeight);
              xHeight("right", maxHeight);

            }

        }
        // ]]>
        </script>';
        return $fixLayoutScript;
    }

    /**
    *
    * Method to return the JavaScript that fixes a three column css layout using
    * Javascript for the older skins. Do not use this method with version 2
    * skins.
    *
    * @access public
    * @return string  $fixLayoutScript the JavaScript that goes in the header
    *
    */
    public function fixThreeColumnLayoutJavascript()
    {
        $fixLayoutScript = '
        <script type="text/javascript">
        // <![CDATA[
        function adjustLayout()
        {
            //Inhouse browser detection
            var browser=navigator.appName;
            var b_version=navigator.appVersion;
            var version=parseFloat(b_version);

             // Get natural heights
            var cHeight = xHeight("contentcontent");
            var lHeight = xHeight("leftcontent");
            var rHeight = xHeight("rightcontent");

            // Find the maximum height
            var maxHeight = Math.max(cHeight, Math.max(lHeight, rHeight));

            // Assign maximum height to all columns
            if ((browser!="Microsoft Internet Explorer")) {

              xHeight("content", maxHeight);
              xHeight("left", maxHeight);
              xHeight("right", maxHeight);

            }

        }
        // ]]>
        </script>';
        return $fixLayoutScript;
    }










    /**
    * Fix the column lengths for the three column css layout using
    * javascript for version 3.0 skins. Do not use this with older skins.
    *
    * @access public
    * @return boolean  TRUE|FALSE
    */
    public function fixThreeSkinTwo()
    {
        $fixLayoutScript = '
        <script type="text/javascript">
        // <![CDATA[
        function adjustLayout()
        {
            //Inhouse browser detection
            var browser=navigator.appName;
            var b_version=navigator.appVersion;
            var version=parseFloat(b_version);
             // Get natural heights
             // Get natural heights
            var cHeight = xHeight("content");
            var lHeight = xHeight("left");
            var rHeight = xHeight("right");
             // Find the maximum height
            var maxHeight = Math.max(cHeight, Math.max(lHeight, rHeight));
            // Assign maximum height to all columns
            if ((browser!="Microsoft Internet Explorer")) {
              xHeight("content", maxHeight);
              xHeight("left", maxHeight);
              xHeight("right", maxHeight);
            }
        }
        // ]]>
        </script>';
        if ($this->skinEngine == 'default' || $this->skinEngine == '') {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('x_minified.js','htmlelements'));
            $this->appendArrayVar('headerParams', $fixLayoutScript);
            $this->appendArrayVar('bodyOnLoad',$this->bodyOnLoadScript());
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    * Fix the column lengths for the two column css layout using
    * javascript for version 3.0 skins. Do not use this with older skins.
    *
    * @access public
    * @return boolean  TRUE|FALSE
    */
    public function fixTwoSkinTwo()
    {
        $fixLayoutScript = '
        <script type="text/javascript">
        // <![CDATA[
        function adjustLayout()
        {
            //Inhouse browser detection
            var browser=navigator.appName;
            var b_version=navigator.appVersion;
            var version=parseFloat(b_version);
             // Get natural heights
            var cHeight = xHeight("content");
            var lHeight = xHeight("left");
            var rHeight = xHeight("right");
            // Find the maximum height
            var maxHeight = Math.max(cHeight, Math.max(lHeight, rHeight));
            // Assign maximum height to all columns
            if ((browser!="Microsoft Internet Explorer")) {
              xHeight("content", maxHeight);
              xHeight("left", maxHeight);
              xHeight("right", maxHeight);
            }
        }
        // ]]>
        </script>';
        if ($this->skinEngine == 'default' || $this->skinEngine == '') {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('x_minified.js','htmlelements'));
            $this->appendArrayVar('headerParams', $fixLayoutScript);
            if (isset($this->bodyOnLoadScript)) {
                $this->appendArrayVar('bodyOnLoad',$this->bodyOnLoadScript);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }















    /**
    * Fix the column lengths for the three column css layout using
    * javascript for version 3.0 skins. Do not use this with older skins.
    *
    * @access public
    * @return boolean  TRUE|FALSE
    */
    public function fixThree()
    {
        $fixLayoutScript = '
        <script type="text/javascript">
        // <![CDATA[
        function adjustLayout()
        {
            //Inhouse browser detection
            var browser=navigator.appName;
            var b_version=navigator.appVersion;
            var version=parseFloat(b_version);
             // Get natural heights
            var cHeight = xHeight("Canvas_Content_Body_Region2");
            var lHeight = xHeight("Canvas_Content_Body_Region1");
            var rHeight = xHeight("Canvas_Content_Body_Region3");
            // Find the maximum height
            var maxHeight = Math.max(cHeight, Math.max(lHeight, rHeight));
            // Assign maximum height to all columns
            if ((browser!="Microsoft Internet Explorer")) {
              xHeight("Canvas_Content_Body_Region2", maxHeight);
              xHeight("Canvas_Content_Body_Region1", maxHeight);
              xHeight("Canvas_Content_Body_Region3", maxHeight);
              xHeight("Canvas_Content_Body", maxHeight);
            }
        }
        // ]]>
        </script>';
        if ($this->skinEngine == 'default' || $this->skinEngine == '') {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('x_minified.js','htmlelements'));
            $this->appendArrayVar('headerParams', $fixLayoutScript);
            $this->appendArrayVar('bodyOnLoad',$this->bodyOnLoadScript());
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    * Fix the column lengths for the two column css layout using
    * javascript for version 3.0 skins. Do not use this with older skins.
    *
    * @access public
    * @return boolean  TRUE|FALSE
    */
    public function fixTwo()
    {
        $fixLayoutScript = '
        <script type="text/javascript">
        // <![CDATA[
        function adjustLayout()
        {
            //Inhouse browser detection
            var browser=navigator.appName;
            var b_version=navigator.appVersion;
            var version=parseFloat(b_version);
             // Get natural heights
            var cHeight = xHeight("Canvas_Content_Body_Region2");
            var lHeight = xHeight("Canvas_Content_Body_Region1");
            var bHeight = xHeight("Canvas_Content_Body");
            // Find the maximum height
            var maxHeight = Math.max(bHeight, Math.max(lHeight, cHeight));
            // Assign maximum height to all columns
            if ((browser!="Microsoft Internet Explorer")) {
              xHeight("Canvas_Content_Body_Region2", maxHeight);
              xHeight("Canvas_Content_Body_Region1", maxHeight);
              xHeight("Canvas_Content_Body", maxHeight);
            }
        }
        // ]]>
        </script>';
        if ($this->skinEngine == 'default' || $this->skinEngine == '') {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('x_minified.js','htmlelements'));
            $this->appendArrayVar('headerParams', $fixLayoutScript);
            $this->appendArrayVar('bodyOnLoad',$this->bodyOnLoadScript);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    * Fix the column lengths for the two column css layout where there is one
    * wide left column, and the two narrow columns are stacked on the right. It
    * uses javascript and workd for version 3.0 skins. Do not use this with
    * older skins.
    *
    * @access public
    * @return boolean  TRUE|FALSE
    */
    public function fixWideTwoRight()
    {
        die("THIS NOT WORKING YET");
    }

    /**
    * Method to return the JavaScript that should run when the page loads
    *
    * @access public
    * @return string The javascript that must be run
    */
    public function bodyOnLoadScript()
    {	
        return 'xAddEventListener(window, "resize", adjustLayout, false);'."\n"
          .'adjustLayout();'."\n";
    }

}
?>