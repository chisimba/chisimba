<?php

/**
 * IM interface class
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
 * @version   $Id: imapi_class_inc.php 11081 2008-10-25 19:39:46Z charlvn $
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
 * IM XML-RPC Class
 *
 * Class to provide Chisimba IM XML-RPC functionality
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
class imapi extends object
{
    public $isReg;

    /**
     * Standard Chisimba init method
     *
     * @return void
     * @access public
     */
    public function init()
    {
        try {
            $this->objModules = $this->getObject('modules', 'modulecatalogue');
            $this->isReg = $this->objModules->checkIfRegistered('im');
            if ($this->isReg === TRUE) {
                $this->objDbIM = $this->getObject('dbim', 'im');
            }
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
        }
        catch (customException $e) {
            customException::cleanUp();
            exit;
        }
    }

    public function addIM($params)
    {
        $msgfrom = $params->getParam(0);
        $msgbody = $params->getParam(1);

        $msgfrom = $msgfrom->scalarval();
        $msgbody = $msgbody->scalarval();

        $pl['from'] = $msgfrom;
        $pl['body'] = $msgbody;
        $pl['type'] = 'chat';

        $res = $this->objDbIM->addRecord($pl);

        $val = new XML_RPC_Value($res, 'string');
        return new XML_RPC_Response($val);
    }

}
?>