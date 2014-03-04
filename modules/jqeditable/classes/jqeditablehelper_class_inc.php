<?php
/**
 *
 * Interface to the jQuery Jeditable plugin
 *
 * Interface to the jQuery Jeditable plugin for building an editable area
 * or table cell. It allows a user to click and edit the content of different
 * xhtml elements. User clicks text on web page. Block of text becomes a form.
 * User edits contents and presses submit button. New text is sent to webserver
 * and saved. Form becomes normal text again. It is based on Jeditable by
 * Mika Tuupola available at:
 *   http://www.appelsiini.net/projects/jeditable
 *
 * Code above does several things: Elements with class edit or edit_area
 * become editable.
 * 
 * Editing starts with single mouse click. Form input element is text. Width
 * and height of input element matches the original element. If users clicks
 * outside form changes are discarded. Same thing happens if users hits ESC.
 * When user hits ENTER browser submits text to the appropriate PHP file.
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
 * @package   jqeditable
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbjqingrid.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 * 
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
* Interface to the jQuery Jeditable plugin
*
* Interface to the jQuery Jeditable plugin for building an editable area
* or table cell. It allows a user to click and edit the content of different
* xhtml elements. User clicks text on web page. Block of text becomes a form.
* User edits contents and presses submit button. New text is sent to webserver
* and saved. Form becomes normal text again. It is based on Jeditable by
* Mika Tuupola available at:
*   http://www.appelsiini.net/projects/jeditable
*
* @author Derek Keats <derek.keats@wits.ac.za>
* @package jqeditable
*
*/
class jqeditablehelper extends object
{
    /**
    *
    * Intialiser for the jqeditablehelper class
    * @access public
    *
    */
    public function init()
    {
        //
    }
    /**
    *
    * Load the editable jQuery plugin javascript file
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadJs()
    {
        $script = '<script language="javascript" src="'
          . $this->getResourceUri("jquery.jeditable.js", "jqeditable")
          . '" type="text/javascript"></script>';
        $this->appendArrayVar('headerParams', $script);
        return TRUE;
    }

    /**
    *
    * Load the editable jQuery plugin ready function that is the business end
    *
    * @access public
    * @return TRUE
    *
    */
    public function loadReadyFunction()
    {
        $targetUrl = str_replace('&amp;', '&', $targetUrl);
        $this->appendArrayVar('headerParams', $this->readyFunction);
        return TRUE;
    }

    /**
    *
    * Some examples of $arrayParams are
    *         $arrayParams = array('indicator' => 'Saving...',
    *             'tooltip' => 'Click to edit...')
    *
    *         $arrayParams = array(
    *             'type' =>'textarea',
    *             'cssclass' => 'someclass',
    *             'cancel' => 'Cancel',
    *             'submit' => 'OK',
    *             'indicator' => '<img src="img/indicator.gif">',
    *             'tooltip' => 'Click to edit...',
    *             'style' => 'display: inline'
    *           )
    *
    * @param string array $arrayParams An array of param => value pairs
    * @param string $targetUrl The URL that will save the changes
    * @param string $areaClass The CSS class of the editable item
    *
    */
    public function buildReadyFunction($arrayParams, $targetUrl, $areaClass='edit')
    {
        $ret = '<script type="text/javascript">';
        $ret .= "\n     jQuery(document).ready(function() {\n";
        $ret .= "          jQuery('.$areaClass').editable('$targetUrl'";
        $ret .=", {\n";
        $entries = count($arrayParams);
        $counter = 0;
        foreach ($arrayParams as $key=>$value) {
            $counter++;
            $ret .= "               $key : '$value'";
            if ($counter < $entries) {
                $ret .= ",\n";
            } else {
                $ret .= "\n";
            }
        }
        $ret .= "          });\n     });\n</script>";
        $this->readyFunction = $ret;
    }

}
?>