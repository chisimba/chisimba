<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_datepicker_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_datepicker_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_datepicker_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_datepicker_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_datepicker and form_entity handler class.
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
class dbformbuilder_datepicker_entity extends dbTable {
   
    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_datepicker_entity');
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
///Note getAll is a member function within dbtable.
        return $this->getAll("WHERE id='" . $id . "'");
    }

    /*!
     * \brief This member function returns a single record with a particular datepicker name
     * \param dpName A string.
     * \return An array with all the date picker objects that have a name supplied by the argument
     */
    function listDatePickerParameters($formNumber,$dpName) {
///Store a mysql query string.
        $sql = "select * from tbl_formbuilder_datepicker_entity where formnumber like '".  $formNumber  ."' AND datepickername like '" . $dpName . "'";
///Return the array of entries. Note that is function in part of the parent class dbTable.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function checks for duplicate datepicker form elements
     * \param dpName. A string which defines the form element indentifier.
     * \param dpValue A string. This can be thought of as a name.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateDatepickerEntry($formNumber,$dpName, $dpValue) {

///Get entries where the date picker form name and the date picker name are like the search
/// parameters.
        $sql = "where datepickername LIKE '" . $dpName . "' AND formnumber LIKE '".$formNumber."' AND datepickervalue like '" . $dpValue . "'";

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
     * \param  dpName A string. This is form element identifier.
     * \param dpValue A string. The is more sort of like a name. A date picker
     * has a value as a name.
     * \param dpDefaultDate A string in a date format.
     * \param isDateFormat A string.
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber,$dpName, $dpValue, $dpDefaultDate, $dpDateFormat) {
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'datepickername' => $dpName,
                    'datepickervalue' => $dpValue,
                    'defaultdate' => $dpDefaultDate,
                    'dateFormat' => $dpDateFormat
                ));
        return $id;
    }
    
    function updateSingle($formNumber,$formElementName,$datePickerValue,$defaultCustomDate,$dateFormat){
        $UpdateSQL = "UPDATE tbl_formbuilder_datepicker_entity
        SET defaultdate='".$defaultCustomDate."', dateFormat='".$dateFormat."' WHERE datepickername='".$formElementName."' and datepickervalue='".$datePickerValue."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a date picker element according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber,$formElementName) {
///Get all the entries the have a date picker form name according to the
///search value.
        $sql = "where formnumber like '".$formNumber."' and datepickername like '" . $formElementName . "'";
///Get the number of records for the entries. Note the getRecordCount
///is a dbtable member function.
        $valueExists = $this->getRecordCount($sql);
///If there are values that exist then delete the records and return true
///otherwise return false.
        if ($valueExists >= 1) {
              $deleteSQLStatement = "DELETE FROM tbl_formbuilder_datepicker_entity WHERE formnumber like '".$formNumber."' AND datepickername like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            //$this->delete("datepickername", $formElementName);
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
