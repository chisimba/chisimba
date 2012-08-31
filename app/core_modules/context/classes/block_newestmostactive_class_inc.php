<?php

/**
* The most recently active contexts
*
* Shows the most recently active contexts, with the context image and a link
* to choose and enter the context.
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
* @package   context
* @author    Derek Keats <derek@dkeats.com>
* @copyright 2012 Kenga Solutions
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
* @see       core
*/
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
* Description for $GLOBALS
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name   $kewl_entry_point_run
*/
$GLOBALS['kewl_entry_point_run']) {
die("You cannot view this page directly");
}
// end security check


/**
*
* The most recently active contexts
*
* Shows the most recently active contexts, with the context image and a link
* to choose and enter the context.
*
* @category  Chisimba
* @package   context
* @author    Derek Keats <derek@dkeats.com>
* @copyright 2012 Kenga Solutions
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class block_newestmostactive extends object
{
    /**
    * @var object $objLanguage : The Language Object
    */
    public $objLanguage;


    /**
    * Constructor
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = ucwords($this->objLanguage->code2Txt('mod_context_mostactive', 'context', NULL, 'The [-contexts-] with the most activity'));
        $this->wrapStr = FALSE;
    }

    /**
     * Method to show the block
     */
    public function show()
    {
        $objNewest = $this->getObject('newestops', 'context');
        return $objNewest->fetchBlock('MostActive', 6);
    }
}
?>