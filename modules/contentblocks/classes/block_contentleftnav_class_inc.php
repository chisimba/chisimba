<?php
/**
 *
 * Block providing a leftnav for contentblocks module
 *
 * Block  providing a leftnav for contentblocks module, which can be to navigate
 * between wide and narrow blocks
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
 * @version    0.001
 * @package    contentblocks
 * @author     Paul Mungai paulwando@gmail.com
 * @copyright  2012 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
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
// end security check

/**
 * 
 * Block providing a leftnav for contentblocks module
 *
 * Block  providing a leftnav for contentblocks module, which can be to navigate
 * between wide and narrow blocks
 *
 * @category  Chisimba
 * @author    Paul Mungai paulwando@gmail.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_contentleftnav extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    
    /**
    * 
    * @var string $objLanguage String object property for holding the 
    * language object
    * @access public
    * 
    */
    public $objLanguage;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     * @access public
     * 
     */
    public function init() 
    {
        $this->objUi = $this->getObject("contentblockui", "contentblocks");
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText(
          "mod_contentblocks_block_leftnav_title","contentblocks");
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     * @access public
     * 
     */
    public function show() 
    {
        return $this->objUi->showLeftNav();
    }
}
?>