<?php
/**
 * Yahoo API controller class
 *
 * Controller class for the Yahoo API controller module
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
 * @package   yapi
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Yahoo API controller class
 *
 * Yahoo API controller class
 *
 * @category  Chisimba
 * @package   yapi
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class yapi extends controller {
    public $objOps;

    public function init() {
        $this->objOps = $this->getObject('yapiops');
    }

    public function dispatch($action = NULL) {
        switch ($action) {
            default :
                $query = $this->getParam('yquery', "select * from social.profile where guid=me");
                $res = $this->objOps->executeYQL($query);
                echo $this->objOps->debugOutput($res);
                break;
        }
    }

    /**
     * A simple method that implements print_r/var_dump in a HTML friendly way.
     */
    public function print_html($object) {
        return str_replace(array(" ", "\n"), array("&nbsp;", "<br>"), htmlentities(print_r($object, true), ENT_COMPAT, "UTF-8"));
    }
}
?>