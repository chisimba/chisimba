<?php
/**
 * Simple Twitter API
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account.
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
 * @package   twitter
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: twitterlib_class_inc.php 16028 2009-12-23 02:47:26Z charlvn $
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
 * Class to supply an easy API for use from this module or even other modules.
 * @author Charl van Niekerk
 * @package twitter
 */
class twitterlib extends object
{
    /**
     * @var object $_objSysConfig System configuration
     * @access protected
     */
    protected $_objSysConfig;

    /**
     * @var string $_defaultUid The username of the default account to pull Twitter settings from
     * @access protected
     */
    protected $_defaultUid;

    /**
     * @var string $_uid The username of the account to pull Twitter settings from
     * @access protected
     */
    protected $_uid;

    /**
     * @var object $_objUserParams The user configuration read from the database
     * @access protected
     */
    protected $_objUserParams;

    /**
     * @var object $_objTwitterRemote The Twitter service API interface
     * @access protected
     */
    protected $_objTwitterRemote;

    /**
     * Constructor for the twitterlib class
     * @access public
     * @return VOID
     */
    public function init()
    {
        // Retrieve system configuration
        $this->_objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->_defaultUid = $this->_objSysConfig->getValue('mod_twitter_defaultuid', 'twitter');

        // Set up the user object to pull the Twitter settings from
        $this->_objUserParams = $this->getObject('dbuserparamsadmin', 'userparamsadmin');
        if ($this->_defaultUid) {
            $this->setUid($this->_defaultUid);
        }

        // Initialise the Twitter remote API
        $this->_objTwitterRemote = $this->getObject('twitterremote', 'twitter');
    }

    /**
     * Sets the account whose Twitter settings to use
     * @param string $uid The username of the account
     * @access public
     * @return VOID
     */
    public function setUid($uid) {
        $this->_uid = $uid;
        $this->_objUserParams->setUid($this->_uid);
        $this->_objUserParams->readConfig();
    }

    /**
     * Post a new Twitter microblog post
     * @param string $status The content of the new post
     * @access public
     * @return VOID
     */
    public function updateStatus($status)
    {
        $twittername = $this->_objUserParams->getValue('twittername');
        $twitterpassword = $this->_objUserParams->getValue('twitterpassword');
        $latitude = $this->_objUserParams->getValue('latitude');
        $longitude = $this->_objUserParams->getValue('longitude');
        if ($twittername && $twitterpassword) {
            $this->_objTwitterRemote->initializeConnection($twittername, $twitterpassword);
            $this->_objTwitterRemote->updateStatus($status, $latitude, $longitude);
        }
    }
}
