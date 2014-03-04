<?php
/**
 * This file wraps the IDS resource.
 *
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
 * @package   phpids
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       modules
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

/**
 * ids Class
 *
 * Class to wrap the ids library.
 *
 * @category  Chisimba
 * @package   phpids
 * @author    Jeremy O'Connor <joconnor@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       modules
 */

/*
    Example usage:

    $objIds = $this->newObject('ids', 'phpids');
    $isOK = $objIds->scan($result);

*/

class ids extends object
{
    /**
    * @var string The version of the phpids library.
    * @access private
    */
    private $phpids_rel = 'phpids-0.6.3.1';
    /**
    * The initialization method.
    * @return void
    * @access public
    */
    public function init()
    {
        // set the include path properly for PHPIDS
        set_include_path(
            get_include_path()
            . PATH_SEPARATOR
            . $this->getResourcePath("{$this->phpids_rel}",'phpids').'/lib/'
        );
    }
    /**
    * The scan method.
    * @param string Returns a textual representation of the results, if the scan fails.
    * @return bool True if scan succeeds, false otherwise.
    * @access public
    */
    public function scan(&$result_)
    {
        require_once 'IDS/Init.php';
        try {

            //throw new Exception('!');
            /*
            * It's pretty easy to get the PHPIDS running
            * 1. Define what to scan
            *
            * Please keep in mind what array_merge does and how this might interfer
            * with your variables_order settings
            */
            $request = array(
                'REQUEST' => $_REQUEST,
                'GET' => $_GET,
                'POST' => $_POST,
                'COOKIE' => $_COOKIE
            );

            $init = IDS_Init::init(dirname(__FILE__) . "/../resources/{$this->phpids_rel}/lib/IDS/Config/Config.ini.php");

            /**
             * You can also reset the whole configuration
             * array or merge in own data
             *
             * This usage doesn't overwrite already existing values
             * $config->setConfig(array('General' => array('filter_type' => 'xml')));
             *
             * This does (see 2nd parameter)
             * $config->setConfig(array('General' => array('filter_type' => 'xml')), true);
             *
             * or you can access the config directly like here:
             */

            $init->config['General']['base_path'] = dirname(__FILE__) . "/../resources/{$this->phpids_rel}/lib/IDS/";
            $init->config['General']['use_base_path'] = true;
            $init->config['Caching']['caching'] = 'none';

            // 2. Initiate the PHPIDS and fetch the results
            $ids = new IDS_Monitor($request, $init);
            $result = $ids->run();


            /*
            * That's it - now you can analyze the results:
            *
            * In the result object you will find any suspicious
            * fields of the passed array enriched with additional info
            *
            * Note: it is moreover possible to dump this information by
            * simply echoing the result object, since IDS_Report implemented
            * a __toString method.
            */
            if (!$result->isEmpty()) {
                $result_ = 'An error occured in the input:<hr />'.$result;
                return FALSE;
            }
            else {
                $result_ = '';
                return TRUE;
            }
        } catch (Exception $e) {
            /*
            * sth went terribly wrong - maybe the
            * filter rules weren't found?
            */
            echo 'An internal error occured: '.$e->getMessage();
            exit;
        }
    }
}
?>