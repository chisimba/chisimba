<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_dropdown_entity--------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_dropdown_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_dropdown_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_dropdown_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_dropdown and form_entity handler class.
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
class dbformbuilder_dropdown_entity extends dbTable {

    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_dropdown_entity');
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
     * \brief This member function returns a single record with a particular dropdown name
     * \param ddName A string.
     * \return An array with all the drop down lists that have a name supplied by the argument
     */
    function listDropdownParameters($formNumber,$ddName) {
///Store a mysql query string into a temporary variable
        $sql = "select * from tbl_formbuilder_dropdown_entity where formnumber like '".$formNumber."' and dropdownname like '" . $ddName . "'";
///Return the array of entries. Note that is function in part of the parent class dbTable.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function checks for duplicate drop down form elements
     * \param ddName. A string which defines the form element indentifier.
     * \param ddValue A string. This can be thought of as a name.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateDropdownEntry($formNumber,$ddName, $ddValue) {

///Get entries where the drop down form name and the drop down name are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and dropdownname like '" . $ddName . "' and ddoptionvalue like '" . $ddValue . "'";

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
     * \param  ddName A string. This is form element identifier and the drop down
     * name.
     * \param ddLabel A string. This is the label for the drop down option
     * \param ddValue A string. This is the actual value that gets stored when
     * the user selects this option and submits a form.
     * \param defaultValue A boolean To determine when an option is selected by
     * default.
     * \param label A string that is the label text for the form element.
     * \param labelOrientation A string either "top", "bottom","left", "right".
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber,$ddName, $ddLabel, $ddValue, $defaultValue, $label, $labelOrientation) {
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'dropdownname' => $ddName,
                    'ddoptionlabel' => $ddLabel,
                    'ddoptionvalue' => $ddValue,
                    'defaultvalue' => $defaultValue,
                    'label' => $label,
                    'labelorientation' => $labelOrientation
                ));
        return $id;
    }
    
    function checkIfOptionExists($id){
           $sql = "where id like '".$id."'";

///Return the number of entries. Note that is function in part of the parent class dbTable.
        $numberofDuplicates = $this->getRecordCount($sql);
        if ($numberofDuplicates < 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    function updateSingle($optionID,$formNumber,$ddName, $ddLabel, $ddValue, $defaultValue, $label, $labelOrientation) {
        $UpdateSQL = "UPDATE tbl_formbuilder_dropdown_entity
        SET ddoptionlabel='".$ddLabel."', ddoptionvalue='".$ddValue."', defaultvalue='".$defaultValue."', label='".$label."', labelorientation='".$labelOrientation."' WHERE dropdownname='".$ddName."' and formnumber='".$formNumber."' and id='".$optionID."'";
        $this->_execute($UpdateSQL);
    }

    function updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout){
        $UpdateSQL = "UPDATE tbl_formbuilder_dropdown_entity
        SET label='".$formElementLabel."', labelorientation='".$formElementLabelLayout."' WHERE dropdownname='".$formElementName."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }
    
    /*!
     * \brief This member function deletes a drop down element according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber,$formElementName) {
///Get all the entries the have a drop down form name according to the
///search value.
        $sql = "where formnumber like '".$formNumber."' and dropdownname like '" . $formElementName . "'";
///Get the number of records for the entries. Note the getRecordCount
///is a dbtable member function.
        $valueExists = $this->getRecordCount($sql);
///If there are values that exist then delete the records and return true
///otherwise return false.
        if ($valueExists >= 1) {
            $deleteSQLStatement = "DELETE FROM tbl_formbuilder_dropdown_entity WHERE formnumber like '".$formNumber."' AND dropdownname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            //$this->delete("dropdownname", $formElementName);
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
