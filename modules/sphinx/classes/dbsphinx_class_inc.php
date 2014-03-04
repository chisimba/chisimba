<?php
/**
 * Sphinx records dbtable derived class
 *
 * Class to interact with the database for the sphinx search module
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
 * @package   sphinx
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbsphinx extends dbTable {
    /**
     * Constructor
     *
     */
    public function init() {
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
    }
    
    /**
     * Method to dynamically set the table name to the recordset table
     * 
     * @param string $table
     * @access public
     */
    public function setTable($table) {
        parent::init($table);
    }
    
    /**
     * Method to retrieve a full row resultset from a puid as set by sphinx
     *
     * @param integer $puid
     * @access public
     */
    public function getResultRow($puid) {
        // only will ever be one single result
        return $this->getAll("WHERE puid = '$puid'");
    }
    
    /**
     * Method to convert a resultset to a JSON object
     *
     * @param array $resultset
     */
    public function jsonify($resultset) {
        return json_encode($resultset);
    }
}
?>
