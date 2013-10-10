<?php
/**
 * Controller class
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
 * @package   conversions
 * @author    Nonhlanhla Gangeni <2539399@uwc.ac.za>
 * @author    Nazheera Khan <2524939@uwc.ac.za>
 * @author    Faizel Lodewyk <2528194@uwc.ac.za>
 * @author    Hendry Thobela <2649282@uwc.ac.za>
 * @author    Ebrahim Vasta <2623441@uwc.ac.za>
 * @author    Keanon Wagner <2456923@uwc.ac.za>
 * @author    Raymond Williams <2541826@uwc.ac.za>
 * @copyright 2007 UWC
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11927 2008-12-29 21:14:03Z charlvn $
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
 * Main class with method for integrating all files
 * @category  Chisimba
 * @package   conversions
 * @author    Administrative User <admin@localhost.local.za>
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11927 2008-12-29 21:14:03Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * 
 */
class conversions extends controller
{
    /**
     * Constructor method to instantiate objects and get variables
     *
     * @return void
     * @access public
     */
    public function init() 
    {
        try {
            $this->objUser = $this->getObject('user', 'security');
            $this->objDist = $this->getObject('dist');
            $this->objTemp = $this->getObject('temp');
            $this->objVol = $this->getObject('vol');
            $this->objWeight = $this->getObject('weight');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    /**
     * Method to process actions to be taken
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null) 
    {
        switch ($action) {
            default:
            case 'default':
                $goTo = $this->getParam('goTo');
                $this->setVarByRef('goTo', $goTo);
                return 'convertit_tpl.php';
                break;

            case 'dist':
                $value = $this->getParam('value');
                $from = $this->getParam('from');
                $to = $this->getParam('to');
                $this->setVarByRef('action', $action);
                $this->setVarByRef('value', $value);
                $this->setVarByRef('from', $from);
                $this->setVarByRef('to', $to);
                $goTo = 'dist';
                $this->setVarByRef('goTo', $goTo);
                return 'convertit_tpl.php';
                break;

            case 'temp':
                $value = $this->getParam('value');
                $from = $this->getParam('from');
                $to = $this->getParam('to');
                $this->setVarByRef('action', $action);
                $this->setVarByRef('value', $value);
                $this->setVarByRef('from', $from);
                $this->setVarByRef('to', $to);
                $goTo = 'temp';
                $this->setVarByRef('goTo', $goTo);
                return 'convertit_tpl.php';
                break;

            case 'vol':
                $value = $this->getParam('value');
                $from = $this->getParam('from');
                $to = $this->getParam('to');
                $this->setVarByRef('action', $action);
                $this->setVarByRef('value', $value);
                $this->setVarByRef('from', $from);
                $this->setVarByRef('to', $to);
                $goTo = 'vol';
                $this->setVarByRef('goTo', $goTo);
                return 'convertit_tpl.php';
                break;

            case 'weight':
                $value = $this->getParam('value');
                $from = $this->getParam('from');
                $to = $this->getParam('to');
                $this->setVarByRef('action', $action);
                $this->setVarByRef('value', $value);
                $this->setVarByRef('from', $from);
                $this->setVarByRef('to', $to);
                $goTo = 'weight';
                $this->setVarByRef('goTo', $goTo);
                return 'convertit_tpl.php';
                break;
        }
    }
}
?>
