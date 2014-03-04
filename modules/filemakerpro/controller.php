<?php

/**
 * Filemaker Pro controller class
 *
 * Class to control the filemokaer pro module
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
 * @package   filemakerpro
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11212 2008-10-30 12:44:26Z wnitsckie $
 * @link      http://avoir.uwc.ac.za
 * @see       xmpphp
 */

class filemakerpro extends controller {

    public $objUser;
    public $objLanguage;
    public $conn;
    public $objSysConfig;
    public $fm;
    public $scripts;
    public $layouts;
    public $databases;
    public $obbbjFMPro;

    /**
     *
     * Standard constructor method to retrieve the action from the
     * querystring, and instantiate the user and lanaguage objects
     *
     */
    public function init() {
        try {
            // Get the security object
            $this->objUser = $this->getObject ( "user", "security" );
            //Create an instance of the language object
            $this->objLanguage = $this->getObject ( "language", "language" );
            // Get the sysconfig variables for the FMP user to set up the connection.
            $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            // Get the FM abstraction object
            $this->objFMPro = $this->getObject ( 'fmpro' );

            // create an instance and connect to FMP
            $this->objFMPro->connFMP ();

        } catch ( customException $e ) {
            // Bail gracefully
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
            case 'getstudentlist' :
                //$newPerformScript = $this->fm->newPerformScriptCommand ( $this->layouts [3], $this->scripts [3] );
                //$result = $newPerformScript->execute ();


                //foreach ( $result->_impl->_records as $kid ) {
                //    var_dump ( $kid->_impl->_fields );
                //}


                //var_dump($result->_impl->_records[0]->_impl->_fields);


                die ();
                break;

            case 'dologin' :
                // Auth the user against the FMP db. I am doing it here because I don't want to pollute the COR in Chisimba just yet
                //var_dump($this->scripts);
                //var_dump($this->layouts);
                //var_dump($this->layouts);
                //var_dump($this->scripts);


                $layout_name = $this->layouts [68];
                $layout_object = $this->fm->getLayout ( $layout_name );
                // get the fields as an array of objects
                $field_objects = $layout_object->getFields ();

                var_dump ( $field_objects );

                die ();
                $newPerformScript = $this->fm->newPerformScriptCommand ( $this->layouts [0], $this->scripts [1] );
                $result = $newPerformScript->execute ();
                $data = $this->fm->getRecordById ( $this->layouts [0], $result->_impl->_records [0]->_impl->_recordId );

                var_dump ( $data );
                break;

            case NULL :

                break;

            default :
                die ( "unknown action" );
                break;
        }
    }
}
?>