<?php
/**
 * Block class for mailmannews
 *
 * Block functions for mailmannrews module
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
 * @version    $Id: block_subscribe_class_inc.php 11960 2008-12-29 21:37:09Z charlvn $
 * @package    mailmannews
 * @subpackage blocks
 * @author     Paul Scott <pscott@uwc.ac.za>
 * @copyright  2006-2008 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://avoir.uwc.ac.za
 * @see        References to other sections (if any)...
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
 * A block to create a form to subscribe to the list
 *
 * @category  Chisimba
 * @author    Paul Scott
 * @version   0.1
 * @copyright 2006-2008 AVOIR
 *
 */
class block_subscribe extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     * last subscribe form box
     *
     * @var    object
     * @access public
     */
    public $display;
    
    /**
     * Language elements
     *
     * @var    object
     * @access public
     */
    public $objLanguage;
    
    /**
     * Mailman signup object
     *
     * @var unknown_type
     */
    public $objMailmanSignup;
    
    /**
     * Standard init function
     *
     * Instantiate language and user objects and create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objMailmanSignup = $this->getObject('mailmansignup');
        $this->display = $this->objMailmanSignup->subsBox(FALSE);
        $this->title = $this->objLanguage->languageText("mod_mailmannews_block_subscribe", "mailmannews");
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return $this->display;
    }
}
?>