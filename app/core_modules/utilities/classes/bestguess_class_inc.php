<?php
/**
 *
 *  Take the best guess of the user or module that we are in
 *
 *  Take the best guess of the user or module that we are in using
 *  first the querystring, then the module, then the default module
 *  It is used to get the user who owns a page when the viewing
 *  user is not logged in or has come via a page that does not
 *  display the userId or userName in the querystring
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
 * @package   _MODULECODE
 * @author    _AUTHORNAME _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: db_MODULECODE.php,v 1.2 2008-01-08 13:07:15 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
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
* Take the best guess of the user or module that we are in
*
* @author Derek Keats
* @package Utilities
*
*/
class bestguess extends object
{

    /**
    *
    * @var string $objUser String object property for holding the user object
    *
    * @access public
    *
    */
    public $objUser;

    /**
    *
    * Constructor for the CLASSNAME object
    *
    * @access public
    *
    */
    public function init()
    {
        // Get an instance of the user object
        $this->objUser = $this->getObject('user', 'security');
    }

    public function guessUserId()
    {
        // Get the userid from the querystring
        $uid = $this->getParam('userid', FALSE);
        if (!$uid) {
            // See if username is in the querystring
            $chk = $this->getParam('userid', FALSE);
            $getUid = $this->objUser->userId($chk);
            if ($getUid) {
                // If we found it return it
                $uid = $getUid;
            // Otherwise keep trying
            } else {
                // Get the module and see if we can deduce userId from that
                $curMod = $this->identifyModule();
                switch ($curMod)
                {
                    case 'blog':
                        //Find out whose blog we are in
                        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                        $blogType= $objSysConfig->getValue('blog_action', 'blog');
                        if ($blogType == "single user") {
                            $uid = $objSysConfig->getValue('blog_singleuserid', 'blog');
                        }
                        break;
                    default:
                        if ($this->objUser->isLoggedIn()) {
                            $uid = $this->objUser->userId();
                        } else {
                            $uid=FALSE;
                        }
                        break;
                }
            }
        }
        return $uid;
    }

    /**
    *
    * Try our best to guess the Username of the person who owns
    * the page that we are on.
    *
    * @return string The username of the page owner
    * @access public
    *
    */
    public function guessUserName()
    {
        // See if username is in the querystring
        $un = $this->getParam('username', FALSE);
        if (!$un) {
            $uid = $this->guessUserId();
            if ($uid) {
                $un = $this->objUser->userName($uid);
            } else {
                $un=FALSE;
            }
        }
        return $un;
    }


    public function __guessUserName()
    {
        // Get the userid from the querystring
        $uid = $this->getParam('userid', FALSE);
        if ($uid) {
            // If we find it just look it up
            $un = $this->objUser->userName($uid);
        // Otherwise try some other steps to get it
        } else {
            // See if username is in the querystring
            $chk = $this->getParam('username', FALSE);
            if ($chk) {
                // If we found it return it
                $un = $chk;
            // Otherwise keep trying
            } else {
                // Get the module and see if we can deduce username from that
                $curMod = $this->identifyModule();
                switch ($curMod)
                {
                    case 'blog':
                        //Find out whose blog we are in
                        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                        $blogType= $objSysConfig->getValue('blog_action', 'blog');
                        if ($blogType == "single user") {
                            $uid = $objSysConfig->getValue('blog_singleuserid', 'blog');
                            $un = $this->objUser->userName($uid);
                        }
                        break;
                    default:
                        if ($this->objUser->isLoggedIn()) {
                            $un = $this->objUser->userName();
                        } else {
                            $un=FALSE;
                        }
                        break;
                }
            }
        }
        return $un;
    }

    /**
    *
    * Try our best to guess the module that we are in
    *
    * @return string The modulecode
    * @access public
    *
    */
    public function identifyModule()
    {
        $objConfig = $this->getObject('altconfig', 'config');
        $curMod = $this->getParam("module", FALSE);
        if ($curMod) {
            if ($curMod !== "_default") {
                return $curMod;
            } else {
                return $objConfig->getdefaultModuleName();
            }
        } else {
            return $objConfig->getdefaultModuleName();
        }
    }

}
?>
