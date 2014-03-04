<?php
/**
 *
 * Installer class for the module
 *
 * The installer class for the module creates a
 * group called AboutContact and sets up files
 * etc.
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
 * @package   about
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright Kenga Solutions
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-07-28 09:35:42Z charlvn $
 * @link      http://kengasolutions.com/
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
 * Installer class for the module
 *
 * The installer class for the module creates a
 * group called AboutContact and sets up files
 * etc.
 *
 * @category  Chisimba
 * @package   oeruserdata
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2010 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-12-31 16:12:33Z dkeats $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
 */
class bannerhelper_installscripts extends dbtable
{
    /**
     * Instance of the altconfig class in the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
     * The object property initialiser.
     *
     * @access public
     */
    public function init()
    {
       $this->objAltConfig = $this->getObject('altconfig', 'config');
    }

    /**
     * The actions to perform after installation of the module.
     *
     * @access public
     * @return void
     *
     */
    public function postinstall()
    {
        $this->createAuthorGroup();
        $this->makeDefaultFile();
    }

    /**
     * Create a group for people who can edit the about text
     *
     * @access public
     * @return VOID
     *
     */
    public function createAuthorGroup()
    {
        $objGa = $this->getObject('gamodel','groupadmin');
        $objGa->addGroup("BannerHelper", "Can edit the banner files.");
    }

    /**
     *
     * Copy the default about, contact, disclaimer and copyright
     * text file to the about directory in usrfiles
     *
     * @access public
     * @return VOID
     *
     */
    public function makeDefaultFile()
    {
        $about = $this->objAltConfig->getModulePath() . 'bannerhelper/resources/about.txt';
        $plMenu = $this->objAltConfig->getModulePath() . 'bannerhelper/resources/plmenu.txt';
        $banner0 = $this->objAltConfig->getModulePath() . 'bannerhelper/resources/banner0.txt';
        $banner1 = $this->objAltConfig->getModulePath() . 'bannerhelper/resources/banner1.txt';
        $banner2 = $this->objAltConfig->getModulePath() . 'bannerhelper/resources/banner2.txt';
        $banner3 = $this->objAltConfig->getModulePath() . 'bannerhelper/resources/banner3.txt';

        $targetAbout = $this->objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/about.txt';
        $targetPlMenu = $this->objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/plmenu.txt';
        $targetBanner0 = $this->objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/banner0.txt';
        $targetBanner1 = $this->objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/banner1.txt';
        $targetBanner2 = $this->objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/banner2.txt';
        $targetBanner3 = $this->objAltConfig->getSiteRootPath() . 'usrfiles/bannerhelper/banner3.txt';

        // Copy files but not if they already exist (prevents 
        //   updates from copying files over edited files).
        if (!file_exists($targetAbout)) {
            copy($about, $targetAbout);
        }
        if (!file_exists($targetPlMenu)) {
            copy($plMenu, $targetPlMenu);
        }
        if (!file_exists($targetBanner0)) {
            copy($banner0, $targetBanner0);
        }
        if (!file_exists($targetBanner1)) {
            copy($banner2, $targetBanner1);
        }
        if (!file_exists($targetBanner2)) {
            copy($banner2, $targetBanner2);
        }
        if (!file_exists($targetBanner3)) {
            copy($banner3, $targetBanner3);
        }
    }
}
?>