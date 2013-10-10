<?php
/**
 * Blog fetchblock class to fetch site blocks
 *
 * This will fetch blocks and is called by blogui class to insert site blocks
 * into the blog.
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
 * @version    $Id: fetchblock_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @package    blog
 * @subpackage blogui
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2007 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * class to fetch site blocks
 *
 * This class fetch blocks and is called by blogui class to insert site blocks
 * into the blog.
 *
 * @category  Chisimba
 * @package   blog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2006-2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: fetchblock_class_inc.php 11076 2008-10-25 18:13:10Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class fetchblock extends object
{
    /**
    *
    * Standard init function
    *
    * Initialises and constructs the object via the framework
    *
    * @return void
    * @access public
    *
    */
    public function init()
    {

    }

    public function show($block, $module)
    {
        $objBlock = &$this->getObject("blocks", "blocks");
        return $objBlock->showBlock($block, $module);
    }
}
?>