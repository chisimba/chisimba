<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_radio_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_radio_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_radio_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_radio_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_radio and form_entity handler class.
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
class dbformbuilder_radio_entity extends dbTable {

    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_radio_entity');
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
     * \brief This member function returns a single record with a particular
     * radio button name
     * \param radioFormName A string.
     * \return An array with all the radio button parameters that have a name
     * supplied by the argument
     */
    function listRadioParameters($formNumber,$radioFormName) {
        return $this->getAll("WHERE formnumber='".$formNumber."' AND radioname='" . $radioFormName . "'");
    }

    /*!
     * \brief This member function checks for duplicate radio button form elements
     * \param radioName A string which defines the form element indentifier.
     * \param radiooptionvalue A string. This is the value of radio button option.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateRadioEntry($formNumber,$radioName, $radiooptionvalue) {

///Get entries where the radio form name and the radio option value are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and radioname like '" . $radioName . "' and radiooptionvalue like '" . $radiooptionvalue . "'";

///Return the number of entries. Note that is function in part of the parent class dbTable.
        $numberofDuplicates = $this->getRecordCount($sql);
        if ($numberofDuplicates < 1) {
            return true;
        } else {
            return FALSE;
        }
///Check whether there are multiple entries.
    }

    /*!
     * \brief Insert a record
     * \param  radioName A string. This is form element identifier and the radio
     * button name.
     * \param radioLabel A string. This is the label for the radio button option.
     * \param radioValue A string. This is the actual value that gets stored when
     * the user selects this option and submits a form.
     * \param defaultValue A boolean To determine when an option is checked by
     * default.
     * \param breakSpace A string. Either a "tab", "new line" or "normal".
     * \param formElementLabel A string that is the label text for the form element.
     * \param labelLayout A string either "top", "bottom","left", "right".
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber,$radioName, $radioLabel, $radiooptionvalue, $defaultvalue, $breakspace, $formElementLabel, $formElementLabelLayout) {
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'radioname' => $radioName,
                    'radiooptionlabel' => $radioLabel,
                    'radiooptionvalue' => $radiooptionvalue,
                    'defaultvalue' => $defaultvalue,
                    'breakspace' => $breakspace,
                    'label' => $formElementLabel,
                    'labelorientation' => $formElementLabelLayout
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
    
    function updateSingle($optionID,$formNumber,$radioName, $radioLabel, $radiooptionvalue, $defaultvalue, $breakspace, $formElementLabel, $formElementLabelLayout) {
        $UpdateSQL = "UPDATE tbl_formbuilder_radio_entity
        SET radiooptionlabel='".$radioLabel."', radiooptionvalue='".$radiooptionvalue."', defaultvalue='".$defaultvalue."', breakspace='".$breakspace."', label='".$formElementLabel."', labelorientation='".$formElementLabelLayout."' WHERE radioname='".$radioName."' and formnumber='".$formNumber."' and id='".$optionID."'";
        $this->_execute($UpdateSQL);
    }
    
    
    function updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout){
                $UpdateSQL = "UPDATE tbl_formbuilder_radio_entity
        SET label='".$formElementLabel."', labelorientation='".$formElementLabelLayout."' WHERE radioname='".$formElementName."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a radio button according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber,$formElementName) {
        $sql = "where formnumber like '".$formNumber."' and radioname like '" . $formElementName . "'";
        $valueExists = $this->getRecordCount($sql);
        if ($valueExists >= 1) {
            $deleteSQLStatement = "DELETE FROM tbl_formbuilder_radio_entity WHERE formnumber like '".$formNumber."' AND radioname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            //$this->delete("radioname", $formElementName);
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
