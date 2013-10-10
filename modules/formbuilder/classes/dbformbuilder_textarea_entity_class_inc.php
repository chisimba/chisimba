<?php
//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_textarea_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_textarea_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_textarea_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_textarea_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_textarea and form_entity handler class.
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
class dbformbuilder_textarea_entity extends dbTable {
    
    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_textarea_entity');
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
     * text area name
     * \param textAreaFormname A string.
     * \return An array with all the text area parameters that have a name
     * supplied by the argument
     */
    function listTextAreaParameters($formNumber,$textAreaFormname) {
        return $this->getAll("WHERE formnumber='".$formNumber."' AND textareaformname='" . $textAreaFormname . "'");
    }

    /*!
     * \brief This member function checks for duplicate text area form elements
     * \param textareaformname A string which defines the form element indentifier.
     * \param textareaname A string which is basically the name of the form element.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateTextAreaEntry($formNumber,$textareaformname, $textareaname) {

///Get entries where the text area form name and the text area name are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and textareaformname like '" . $textareaformname . "' and textareaname like '" . $textareaname . "'";

///Return the number of entries. Note that is function in part of the parent class dbTable.
        $numberofDuplicates = $this->getRecordCount($sql);
        if ($numberofDuplicates < 1) {
            return true;
        } else {
            return FALSE;
        }
    }
    


    /*!
     * \brief Insert a new record
     * \param  textareaformname A string. This is form element identifier.
     * \param textareaname A string.
     * \param textareavalue A string that defines the default text that should be
     * displayed inside the text area.
     * \param columnsize An integer that defines the width of the text area.
     * \param rowsize An integer that deinfes the height of the text area.
     * \param simpleoradvancedchoice A string to determine whether a toolbar
     * will be added or not.
     * \param toolbarchoice A string to put the tool bar type.
     * \param textarealabel A string that is the label text for the form element.
     * \param labelorientation A string either "top", "bottom","left", "right".
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formnumber,$textareaformname, $textareaname, $textareavalue, $columnsize, $rowsize, $simpleoradvancedchoice, $toolbarchoice, $textarealabel, $labelorientation) {
        $id = $this->insert(array(
                    'formnumber' => $formnumber,
                    'textareaformname' => $textareaformname,
                    'textareaname' => $textareaname,
                    'textareavalue' => $textareavalue,
                    'columnsize' => $columnsize,
                    'rowsize' => $rowsize,
                    'simpleoradvancedchoice' => $simpleoradvancedchoice,
                    'toolbarchoice' => $toolbarchoice,
                    'label' => $textarealabel,
                    'labelorientation' => $labelorientation
                ));
        return $id;
    }
    
    function updateSingle($formNumber,$textareaformname,$textareaname,$textAreaValue,$ColumnSize,$RowSize,$simpleOrAdvancedHAChoice,$toolbarChoice,$formElementLabel,$labelLayout){
        $UpdateSQL = "UPDATE tbl_formbuilder_textarea_entity
        SET textareavalue='".$textAreaValue."', columnsize='".$ColumnSize."', rowsize='".$RowSize."', simpleoradvancedchoice='".$simpleOrAdvancedHAChoice."', toolbarchoice='".$toolbarChoice."', label='".$formElementLabel."', labelorientation='".$labelLayout."' WHERE textareaformname='".$textareaformname."' and textareaname='".$textareaname."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a text area according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber,$formElementName) {
        $sql = "where formnumber like '".$formNumber."' and textareaformname like '" . $formElementName . "'";
        $valueExists = $this->getRecordCount($sql);
        if ($valueExists >= 1) {
            $deleteSQLStatement = "DELETE FROM tbl_formbuilder_textarea_entity WHERE formnumber like '".$formNumber."' AND textareaformname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            //$this->delete("textareaformname", $formElementName);
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
