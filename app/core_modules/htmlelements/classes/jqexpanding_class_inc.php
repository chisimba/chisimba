<?php

/**
 * Makes text areas be auto expanding
 *
 * Makes text areas be auto expanding using the jQuery
 * The textarea will shrink/grow just as it does on Facebook.
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
 * @category  Chisimba
 * @package   htmlelements
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: alertbox_class_inc.php 3601 2008-02-22 09:26:43Z tohir $
 * @link      http://avoir.uwc.ac.za
 */

/**
 *
 * Makes text areas be auto expanding
 *
 * Makes text areas be auto expanding using the jQuery
 * The textarea will shrink/grow just as it does on Facebook.
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: alertbox_class_inc.php 3601 2008-02-22 09:26:43Z tohir $
 * @link      http://www.reindel.com/truncate/
 * @link      http://avoir.uwc.ac.za
 */
class jqexpanding extends object
{

    /**
    * Class Constructor
    */
    public function init()
    {
    }

    /**
    * Method to load the truncate jQuery plugin
    *
    * @return string The HTML code of the rendered textinput and colorpicker
    * @access public
    */
    private function loadScript()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery/plugins/textareagrow/jquery.jgrow-0.2.js'));
    }

    public function setSetScript($divId)
    {
        $hScr = '<script type="text/javascript">
// <![CDATA[
jQuery(document).ready(function() {

    jQuery("textarea#' . $divId . '").jGrow({ rows: 25 });

});
// ]]>
</script>';
        $this->appendArrayVar('headerParams', $hScr);
        return TRUE;
    }

    public function show($divId)
    {
        $this->loadScript();
        $this->setSetScript($divId);
        return TRUE;
    }
}
?>