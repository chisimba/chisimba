<?php

/**
 * XML-RPC interface class
 * 
 * XML-RPC (Remote Procedure call) class
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
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: cmsrpcapi_class_inc.php 23864 2012-03-25 17:13:08Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
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
 * XML-RPC Class
 * 
 * Class to provide XML-RPC functionality to Chisimba
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class cmsrpcapi extends object {

    public $objCmsDb;

    public function init() {
        try {
            require_once($this->getPearResource('XML/RPC/Server.php'));
            require_once($this->getPearResource('XML/RPC/Dump.php'));
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objCmsDb = $this->getObject('rpcdbcmsadmin', 'cmsadmin');
        } catch (customException $e) {
            // garbage collection
            customException::cleanUp();
            // die, as we are screwed anyway
            exit;
        }
    }

    /**
     * server method
     * 
     * Create and deploy the XML-RPC server for use on an URL
     * 
     * @return object server object
     * @access public
     */
    public function serve() {
        // map web services to methods
        $server = new XML_RPC_Server(
            array(
                'cms.getSectionList' => array('function' => array($this->objCmsDb, 'getSections'),
                    'signature' => array(
                        array('array', 'string', 'string'),
                    ),
                    'docstring' => 'Grabs a list of current sections returns an array'),
                'cms.getFilteredSections' => array('function' => array($this->objCmsDb, 'getFilteredSecs'),
                    'signature' => array(
                        array('array', 'string', 'string'),
                    ),
                    'docstring' => 'Grabs a list of current sections (filtered) returns an array'),
                'cms.getArchivedSections' => array('function' => array($this->objCmsDb, 'getArcSections'),
                    'signature' => array(
                        array('array', 'string'),
                    ),
                    'docstring' => 'Grabs a list of archived sections returns an array'),
                'cms.getRootNodeSections' => array('function' => array($this->objCmsDb, 'getSectionRootNodes'),
                    'signature' => array(
                        array('array', 'string', 'string'),
                    ),
                    'docstring' => 'Grabs a list of root nodes returns an array'),
                'cms.getSectionById' => array('function' => array($this->objCmsDb, 'getSectionId'),
                    'signature' => array(
                        array('array', 'string'),
                    ),
                    'docstring' => 'returns an array of the section details'),
                'cms.getFirstSecId' => array('function' => array($this->objCmsDb, 'getFirstSectionId'),
                    'signature' => array(
                        array('string', 'string'),
                    ),
                    'docstring' => 'returns an array of the section details'),
                'cms.addSection' => array('function' => array($this->objCmsDb, 'addSec'),
                    'signature' => array(
                        array('string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string'),
                    ),
                    'docstring' => 'add a section to the cms'),
                'cms.addPage' => array('function' => array($this->objCmsDb, 'addPg'),
                    'signature' => array(
                        array('string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string'),
                    ),
                    'docstring' => 'add a page to a section'),
            ), 1, 0);


        return $server;
    }

}

?>