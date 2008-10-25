<?php

/**
 * alertbox_class_inc.php
 *
 * Facebox
 * This generates Facebook-style lightbox which can display images, divs, or entire remote pages.
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
 * @version   $Id: alertbox_class_inc.php 3601 2008-02-22 09:26:43Z tohir $
 * @link      http://avoir.uwc.ac.za
 */

/**
 * Alertbox
 *
 * This generates truncated text, with more and less links.
 *
 * See: http://famspam.com/facebox
 *
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: alertbox_class_inc.php 3601 2008-02-22 09:26:43Z tohir $
 * @link      http://www.reindel.com/truncate/
 * @link      http://avoir.uwc.ac.za
 */
class jqtruncate extends object
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
    private function loadTruncate()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('truncate/jquery.truncate-2.3-pack.js'));
    }

    public function setTruncation(&$cls, &$chars, &$moreText, &$lessText)
    {
        $hScr = '
<script language="javascript" type="text/javascript">
$(function() {

    $(".' . $cls . '").truncate( ' . $chars . ', {
        chars: /\s/,
        trail: [ " ( <a href=\'#\' class=\'truncate_show\'>' . $moreText . '</a> . . . )", " ( . . . <a href=\'#\' class=\'truncate_hide\'> ' . $lessText . '</a> )" ]
    });

});
</script>';
        $this->appendArrayVar('headerParams', $hScr);
        return TRUE;
    }

    public function show($txt, $cls, $chars, $moreText, $lessText)
    {
        $this->loadTruncate();
        $this->setTruncation($cls, $chars, $moreText, $lessText);
        return "<div class=\"$cls\"$txt</div";
    }
}
?>