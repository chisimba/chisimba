<?php

/**
 * Tabbed box block class
 *
 * Creates a tabbed box block for use in Chisimba
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
 * @package   blocks
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Tabbed box block class
 *
 * Creates a tabbed box block for use in Chisimba
 *
 * @category  Chisimba
 * @package   blocks
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class block_tabbedbox extends object {

    /**
     * Title
     *
     * @var    string
     * @access public
     */
    public $title;

    /**
     * init function
     *
     * Standard init function for Chisimba modules
     *
     * @return void
     * @access public
     */
    public function init() {
        $this->title = "Type: tabbedbox with a very long string  title";
    }

    /**
     * Show method
     *
     * Standard show method to output to _toString()
     *
     * @return string Return text
     * @access public
     */
    public function show() {
        return "This is an example of a block rendered using type tabbedbox. It places the title in a tab, and the block output in a tabbed box.";
    }
}

?>