<?php

/**
 *
 * Sasicontext
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
 * @package   sasicontext
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbsasicontext_class_inc.php 19012 2010-09-15 10:18:54Z qfenama $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Database accesss class for Chisimba for the module sasicontext
 *
 * @author Qhamani Fenama <qfenama@gmail.com>
 * @package sasicontext
 *
 */
class dbsasicontext extends dbtable {

    /**
     *
     * Intialiser for the dbsasicontext class
     * @access public
     *
     */
    public function init() {
        //Set the parent table here
        parent::init('tbl_sasicontext');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Method to get an sasi-context from the database.
     * @param string $field
     * @param string $value
     * @return array $data List of sasi - context
     */
    public function getSasicontextByField($field, $value) {

        return $this->getRow($field, $value);
    }

    /**
     * Method to get an sasi-context from the database.
     * @param string $id
     * @return array $data List of sasi - context
     */
    public function getSasicontext($id) {

        return $this->getRow('id', $id);
    }

    /**
     * Method to get an sasi-context from the database.
     * @param string $id
     * @return array $data List of sasi - context
     */
    public function getAllSasicontext() {

        return $this->getAll();
    }

    /**
     * Add new sasi - context record
     * @param <type> $contextcode
     * @param <type> $faculty
     * @param <type> $facultytitle
     * @param <type> $department
     * @param <type> $departmenttitle
     * @param <type> $subject
     * @param <type> $subjecttitle
     * @return <type> $id
     */
    public function addSasicontext($contextcode, $faculty, $facultytitle, $department, $departmenttitle, $subject, $subjecttitle) {

        $id = $this->insert(array(
                'contextcode' => $contextcode,
                'faculty' => $faculty,
                'facultytitle' => $facultytitle,
                'department' => $department,
                'departmenttitle' => $departmenttitle,
                'subject' => $subject,
                'subjecttitle' => $subjecttitle,
                'creatorid' => $this->objUser->userId(),
                'last_modified' => date('Y-m-d H:i:s', time())
        ));
        return $id;
    }

    /**
     * Update a sasi - context record
     * @param <type> $id
     * @param <type> $contextcode
     * @param <type> $faculty
     * @param <type> $facultytitle
     * @param <type> $department
     * @param <type> $departmenttitle
     * @param <type> $subject
     * @param <type> $subjecttitle
     * @return <type> $id
     */
    public function updateSasicontext($id, $contextcode = FALSE, $faculty = FALSE, $facultytitle = FALSE, $department = FALSE, $departmenttitle = FALSE, $subject = FALSE, $subjecttitle = FALSE) {

        $fields = array();

        if ($contextcode !== FALSE) {
            $fields['contextcode'] = $contextcode;
        }

        if ($faculty !== FALSE) {
            $fields['faculty'] = $faculty;
        }

        if ($facultytitle !== FALSE) {
            $fields['facultytitle'] = $facultytitle;
        }

        if ($department !== FALSE) {
            $fields['department'] = $department;
        }

        if ($departmenttitle !== FALSE) {
            $fields['departmenttitle'] = $departmenttitle;
        }

        if ($subject !== FALSE) {
            $fields['subject'] = $subject;
        }

        if ($subjecttitle !== FALSE) {
            $fields['subjecttitle'] = $subjecttitle;
        }

        $fields['creatorid'] = $this->objUser->userId();
        $fields['last_modified'] = date('Y-m-d H:i:s', time());
        $id = $this->update('id', $id, $fields);
        return $id;
    }


    /**
     * delete a sasi - context record
     * @param <type> $id
     * @return <type>
     */
    public function deleteSasicontext($id) {

        $result = $this->delete('id', $id);
        return $result;
    }
}
?>
