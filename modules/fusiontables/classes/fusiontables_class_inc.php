<?php

/**
 * Facade to the Google Fusion Tables API.
 * 
 * Library methods for interacting with the Google Fusion Tables API.
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
 * @package   fusiontables
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: fusiontables_class_inc.php 19113 2010-10-05 09:47:13Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
 * Facade to the Google Fusion Tables API.
 * 
 * Library methods for interacting with the Google Fusion Tables API.
 * 
 * @category  Chisimba
 * @package   fusiontables
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: fusiontables_class_inc.php 19113 2010-10-05 09:47:13Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class fusiontables extends object
{
    /**
     * Instance of the Zend_Gdata class.
     *
     * @access private
     * @var    object
     */
    private $gdata;

    /**
     * Initialises the object.
     *
     * @access public
     */
    public function init()
    {
        $this->getObject('zend', 'zend');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $email = $this->objSysConfig->getValue('email', 'fusiontables');
        $password = $this->objSysConfig->getValue('password', 'fusiontables');
        $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password, 'fusiontables');
        $this->gdata = new Zend_Gdata($client);
    }

    /**
     * Executes a query on the API returns the results.
     *
     * @access public
     * @param  string $sql The query to execute.
     * @return array  The results.
     */
    public function query($sql)
    {
        $uri = 'http://tables.googlelabs.com/api/query?sql='.urlencode($sql);
        $csv = $this->gdata->performHttpRequest('GET', $uri)->getBody();

        $file = tmpfile();
        fwrite($file, $csv);
        fseek($file, 0);

        $headers = fgetcsv($file);
        $data = array();

        while (($row = fgetcsv($file)) !== FALSE) {
            $data[] = array_combine($headers, $row);
        }

        fclose($file);

        return $data;
    }
}
