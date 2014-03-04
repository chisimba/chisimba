<?php

/**
 * eduCommons controller class
 * 
 * Class to control the eduCommons module
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
 * @package   educommons
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2008 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 12354 2009-02-09 02:38:53Z charlvn $
 * @link      http://avoir.uwc.ac.za
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
 * eduCommons controller class
 *
 * Class to control the eduCommons module
 *
 * @category  Chisimba
 * @package   educommons
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2008 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */

class educommons extends controller
{
    protected $objContext;
    protected $objFolder;
    protected $objImport;
    protected $objLanguage;
    protected $objUpload;
    protected $objZip;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     *
     * @access public
     */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objFolder = $this->getObject('dbfolder', 'filemanager');
        $this->objImport = $this->getObject('educommonsimport', 'educommons');
        $this->objLanguage = $this->getObject ('language', 'language');
        $this->objUpload = $this->getObject('uploadinput', 'filemanager');
        $this->objZip = $this->getObject('wzip', 'utilities');
    }

    /**
     * Standard dispatch method to handle the various possible actions.
     *
     * @access public
     */
    public function dispatch()
    {
        $action = $this->getParam('action');
        $context = $this->getParam('context');
        $this->setLayoutTemplate('educommons_layout.php');
        switch ($action) {
            case 'rss':
                $uri = $this->getParam('uri');
                $this->objImport->doRssChapters($uri);
                break;
            case 'upload':
                $upload = $this->objUpload->handleUpload();
                $parentFolder = $this->getParam('parentfolder');
                $basePath = $this->objConfig->getcontentBasePath();
                $uploadPath = $this->objFolder->getFolderPath($parentFolder);
                $from = $basePath . $uploadPath . DIRECTORY_SEPARATOR . $upload['name'];
                $to = $basePath . $uploadPath;
                $this->objZip->unZipArchive($from, $to);
                $directory = $to . DIRECTORY_SEPARATOR . substr($upload['name'], 0, strlen($upload['name']) - 4);
                $manifest = $directory . DIRECTORY_SEPARATOR . 'imsmanifest.xml';
                set_time_limit(900);
                $data = $this->objImport->parseIms($manifest);
                $this->objImport->addCourses($data, $context);
                $this->objImport->addPages($data, $directory, $context);
                $this->nextAction(null, null, 'contextcontent');
                break;
            default:
                $contexts = $this->objContext->getListOfContext();
                $this->setVarByRef('contexts', $contexts);
                return 'upload_tpl.php';
        }
    }
}

?>
