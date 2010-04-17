<?php

/**
 * Class to Parse for PROTECTED and and remove the tags
 *
 * This class searches for [PROTECTED][/PROTECTED] and just
 * removes them. This is used to protect code pasted in
 * source view in CKEDITOR from being eaten by the editor.
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
 * @package   filters
 * @author    Derek Keats
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: parse4reference_class_inc.php 11052 2008-10-25 16:04:14Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 *
 * Class to Parse for PROTECTED and and remove the tags
 *
 * This class searches for [PROTECTED][/PROTECTED] and just
 * removes them. This is used to protect code pasted in
 * source view in CKEDITOR from being eaten by the editor.
 *
 * @author Derek Keats
 */
class parse4protected extends object
{
    /**
    * Constructor
    */
    public function init()
    { }

    /**
    * Method to parse text for [REF]s and replace them with footnotes
    * @param  string $string String to be parse
    * @return string
    */
    public function parse($string)
    {
        $string = (str_ireplace('[PROTECTED]', NULL, $string));
        $string = (str_ireplace('[/PROTECTED]', NULL, $string));
        return $string;
    }
}
?>