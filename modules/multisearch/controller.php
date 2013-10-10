<?php
/**
 * Multisearch controller class
 *
 * Class to control the Multisearch module
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
 * @package   Multisearch
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
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
 * Multisearch controller class
 *
 * Class to control the Multisearch module.
 *
 * @category  Chisimba
 * @package   Multisearch
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class multisearch extends controller
{
    public $objLanguage;
    public $objOps;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        try {
            $this->requiresLogin();
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            $this->objOps = $this->getObject('multisearchops');
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Standard dispatch method
     *
     * @access public
     * @param void
     * @return void
     */
    public function dispatch() {
        $action = $this->getParam ( 'action' );
        switch ($action) {
            
            case NULL:
            case 'showform':
                $form = $this->objOps->queryForm();
                $this->setVarByRef('form', $form);
                return 'view_tpl.php';
                break;
             
            case 'lookup' :
                $form = $this->objOps->queryForm();
                $query = $this->getParam('query', NULL);
                $builtQuery = $this->objOps->buildQuery($query);
                $data = $this->objOps->doQuery($builtQuery);
                $output = $this->objOps->formatQuery($data);
                
                $this->setVarByRef('output', $output);
                $this->setVarByRef('form', $form);
                return 'view_tpl.php';
                break;
            
            default:
                $this->objLanguage->languageText("mod_multisearch_somethingwrong", "multisearch");
                break;
        }
    }

    public function requiresLogin() {
        return FALSE;
    }
}
?>