<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_button_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/*!  \class dbformbuilder_button_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_button_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_button_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_button and the form_entity_handler class.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 * \warning This class's parent is dbTable which is a chisimba core class.
 * \warning If the dbTable class is altered in the future. This class may not work.
 * \warning Apart from normal PHP. This class uses the mysql language to provide
 * the actual functionality. This language is encapsulated with in "double quotes" in a string format.
 */

class dbformbuilder_button_entity extends dbTable {

    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_button_entity');
    }

    /*!
     * \brief Return all records in this table
     * \return A mult-dimensional array with all the entries
     */
    function listAll() {
        return $this->getAll();
    }

    /*!
     * \brief This member function returns a single record with a certain id
     * \param id A string.
     * \return An array with a single value
     */
    function listSingle($id) {
        return $this->getAll("WHERE id='" . $id . "'");
    }

    /*!
     * \brief This member function returns a single record with a particular button name
     * \param buttonFormName A string.
     * \return An array with all the buttons that have a name supplied by the argument
     */
    function listButtonParameters($formNumber,$buttonFormName) {
        return $this->getAll("WHERE formnumber='".$formNumber."' AND buttonformname='" . $buttonFormName . "'");
    }

    /*!
     * \brief This member function checks for duplicate button form elements
     * \param buttonFormName A string which defines the form element indentifier.
     * \param buttonName A string.
     * \return An boolean value.
     */
    function checkDuplicateButtonEntry($formNumber,$buttonFormName, $buttonName) {

///Get entries where the button form name and the button name are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and buttonformname like '" . $buttonFormName . "' and buttonname like '" . $buttonName . "'";
///Return the number of entries. Note that is function in part of the parent class dbTable.
        $numberofDuplicates = $this->getRecordCount($sql);
///Check whether there are multiple entries.
        if ($numberofDuplicates < 1) {
            return true;
        } else {
            return FALSE;
        }
    }

    /*!
     * \brief Insert a record
     * \param  buttonFormName A string. This is form element identifier.
     * \param buttonName A string.
     * \param buttonLabel A string.
     * \param isSetToResetOrSubmitstring A string either "submit" or "reset".
     * \return A random generated id for this new entry.
     */
    function insertSingle($formnumber,$buttonFormName, $buttonName, $buttonLabel, $isSetToResetOrSubmit) {
        $id = $this->insert(array(
                    'formnumber' => $formnumber,
                    'buttonformname' => $buttonFormName,
                    'buttonname' => $buttonName,
                    'buttonlabel' => $buttonLabel,
                    'issettoresetorsubmit' => $isSetToResetOrSubmit
                ));
        return $id;
    }
    
    function checkIfButtonExists($id){
           $sql = "where id like '".$id."'";

///Return the number of entries. Note that is function in part of the parent class dbTable.
        $numberofDuplicates = $this->getRecordCount($sql);
        if ($numberofDuplicates < 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    function updateSingle($optionID,$formNumber,$buttonFormName, $buttonName, $buttonLabel, $isSetToResetOrSubmit) {
        $UpdateSQL = "UPDATE tbl_formbuilder_button_entity
        SET buttonname='".$buttonName."', buttonlabel='".$buttonLabel."', issettoresetorsubmit='".$isSetToResetOrSubmit."' WHERE buttonformname='".$buttonFormName."' and formnumber='".$formNumber."' and id='".$optionID."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a button according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value.
     */
    function deleteFormElement($formNumber,$formElementName) {
///Get all the entries the have a button form name according to the
///search value.
        $sql = "where formnumber like '".$formNumber."' AND buttonformname like '" . $formElementName . "'";
///Get the number of records for the entries. Note the getRecordCount
///is a dbtable member function.
        $valueExists = $this->getRecordCount($sql);
///If there are values that exist then delete the records and return true
///otherwise return false.
        

        if ($valueExists >= 1) {
                   $deleteSQLStatement = "DELETE FROM tbl_formbuilder_button_entity WHERE formnumber like '".$formNumber."' AND buttonformname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
//            $this->delete("buttonformname", $formElementName);
            return true;
        } else {
            return false;
        }
    }

    /*!
     * \brief Delete a record according to its id
     * \param id A string.
     * \return Nothing.
     */
    function deleteSingle($id) {
        $this->delete("id", $id);
    }

}

?>
