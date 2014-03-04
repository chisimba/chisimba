<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_htmlheading_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_htmlheading_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_htmlheading_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_htmlheading_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_htmlheading and form_entity handler class.
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
class dbformbuilder_htmlheading_entity extends dbTable {

    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_htmlheading_entity');
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
     * \brief This member function returns a single record with a particular htmlheading name
     * \param headingFormName A string.
     * \return An array with all the html headings that have a name supplied by the argument
     */
    function listHTMLHeadingParameters($formNumber,$headingFormName) {
        return $this->getAll("WHERE formnumber='".$formNumber."' AND headingname='" . $headingFormName . "'");
    }

    /*!
     * \brief This member function checks for duplicate html heading form elements
     * \param headingname. A string which defines the form element indentifier.
     * \param heading A string. This is the actual text of the heading.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateHTMLheadingEntry($formNumber, $headingname) {
///Get entries where the html heading form name and the html heading text are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and headingname like '" . $headingname . "'";

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
     * \param  headingname A string. This is form element identifier.
     * \param heading A string. The is actual html heading text.
     * \param fontSize A integer between 1-6 with 1 being the highest and
     * 6 being lowest.
     * \param textAlignment A string to store the alignment. There are three
     * possibilities, "left","right" and "center".
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber,$headingname, $heading, $fontSize, $textAlignment) {

        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'headingname' => $headingname,
                    'heading' => $heading,
                    'size' => $fontSize,
                    'alignment' => $textAlignment
                ));
        return $id;
    }
    
    function updateSingle($formNumber,$headingName, $heading, $fontSize, $textAlignment){
        $UpdateSQL = "UPDATE tbl_formbuilder_htmlheading_entity
        SET heading='".$heading."', size='".$fontSize."', alignment='".$textAlignment."' WHERE headingname='".$headingName."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a html heading element according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber, $formElementName) {
        $sql = "where formnumber like '".$formNumber."' and headingname like '" . $formElementName . "'";
        $valueExists = $this->getRecordCount($sql);
        if ($valueExists >= 1) {
            $deleteSQLStatement = "DELETE FROM tbl_formbuilder_htmlheading_entity WHERE formnumber like '".$formNumber."' AND headingname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            //$this->delete("headingname", $formElementName);
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
