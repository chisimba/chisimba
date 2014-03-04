<?php

/**
 * Nodechat controller class.
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
 * @category  chisimba
 * @package   nodechat
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2011 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: nodechatlib_class_inc.php 21563 2011-05-19 21:23:40Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
 * Nodechat controller class.
 *
 * @category  chisimba
 * @package   nodechat
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2011 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: nodechatlib_class_inc.php 21563 2011-05-19 21:23:40Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class nodechat extends controller
{
    /**
     * Instance of the nodechatlib class.
     *
     * @author private
     * @var    object
     */
    private $objNodeChat;

    /**
     * Initialises object properties.
     *
     * @access public
     */
    public function init()
    {
        $this->objNodeChat = $this->getObject('nodechatlib', 'nodechat');
    }

    /**
     * Handles an incoming request and returns a result.
     *
     * @access public
     */
    public function dispatch()
    {
        $iframe = $this->objNodeChat->iframe('100%', '600px');
        $this->setVar('nodechatiframe', $iframe);

        return 'main_tpl.php';
    }

    /**
     * This module does not require a login.
     *
     * @access public
     * @return boolean Will always be false.
     */
    public function requiresLogin()
    {
        return FALSE;
    }
}

?>
