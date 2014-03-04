<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_label_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_label_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_label_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_label_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_label and form_entity handler class.
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
class dbformbuilder_label_entity extends dbTable {

    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_label_entity');
// $this->objUser = &$this->getObject('user', 'security');
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
     * \brief This member function returns a single record with a particular label name
     * \param labelFormName A string.
     * \return An array with all the label parameters that have a name supplied by the argument
     */
    function listLabelParameters($formNumber, $labelFormName) {
        return $this->getAll("WHERE formnumber='".$formNumber."' AND labelname='" . $labelFormName . "'");
    }

    /*!
     * \brief This member function checks for duplicate label form elements
     * \param labelName A string which defines the form element indentifier.
     * \param label A string. This is the actual text of the label.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateLabelEntry($formNumber, $labelName) {

///Get entries where the label form name and the label text are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and labelname like '" . $labelName . "'";

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
     * \param  labelName A string. This is form element identifier.
     * \param label A string. The is actual label text.
     * \param breakspace A string to store what breakspace comes before the
     * label. There are three possibilities, "new line","double space" and "no spaces".
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber, $labelName, $label, $breakSpace) {
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'labelname' => $labelName,
                    'label' => $label,
                    'breakspace' => $breakSpace
                ));
        return $id;
    }
    
    function updateSingle($formNumber,$labelName,$labelText,$breakSpace){
        $UpdateSQL = "UPDATE tbl_formbuilder_label_entity
        SET label='".$labelText."', breakspace='".$breakSpace."' WHERE labelname='".$labelName."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a label element according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber,$formElementName) {
        $sql = "where formnumber like '".$formNumber."' and labelname like '" . $formElementName . "'";
        $valueExists = $this->getRecordCount($sql);
        if ($valueExists >= 1) {
            $deleteSQLStatement = "DELETE FROM tbl_formbuilder_label_entity WHERE formnumber like '".$formNumber."' AND labelname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            //$this->delete("labelname", $formElementName);
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
