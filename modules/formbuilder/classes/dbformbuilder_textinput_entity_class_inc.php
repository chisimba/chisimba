<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_textinput_entity------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_textinput_entity
 *
 *  \brief Class that models the interface for the tbl_formbuilder_textinput_entity database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_textinput_entity table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity_textinput and form_entity handler class.
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
class dbformbuilder_textinput_entity extends dbTable {
   
    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_textinput_entity');
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
     * \brief This member function checks whether or not an entry
         * exists with the text input name as an argument.
     * \param formElementName A string for the html name for the text input
     * \return An boolean, true if it exists.
     */
    function checkIfEntryExists($formNumber,$formElementName)
    {
 $sql = "where formnumber like '".$formNumber."' and textinputname like '" . $formElementName . "'";
   $Exists = $this->getRecordCount($sql);
        if ($Exists > 0) {
            return true;
        } else {
            return FALSE;
        }
    }

    /*!
     * \brief This member function returns a single record with a particular
     * text input name
     * \param textInputFormname A string.
     * \return An array with all the text area parameters that have a name
     * supplied by the argument
     */
    function listTextInputParameters($formNumber,$textInputFormname) {
        return $this->getAll("WHERE formnumber='".$formNumber."' AND textinputformname='" . $textInputFormname . "'");
    }

    /*!
     * \brief This member function checks for duplicate text input form elements
     * \param textinputformname A string which defines the form element indentifier.
     * \param textinputname A string which is basically the name of the form element.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateTextInputEntry($formNumber,$textinputformname, $textinputname) {

///Get entries where the text input form name and the text input name are like the search
/// parameters.
        $sql = "where formnumber like '".$formNumber."' and textinputformname like '" . $textinputformname . "' and textinputname like '" . $textinputname . "'";

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
     * \param  textinputformname A string. This is form element identifier.
     * \param textinputname A string.
     * \param textvalue A string that defines the default text that should be
     * displayed inside the text input.
     * \param textype A string to define whether the text input is a text or
     * a password field.
     * \param textsize An integer that defines the width of the text input.
     * \param maskedinputchoice An string. Seven possibilties exist.
     * \param textinputlabel A string that is the label text for the form element.
     * \param labelorientation A string either "top", "bottom","left", "right".
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber,$textinputformname, $textinputname, $textvalue, $texttype, $textsize, $maskedinputchoice, $textinputlabel, $labelorientation) {
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'textinputformname' => $textinputformname,
                    'textinputname' => $textinputname,
                    'textvalue' => $textvalue,
                    'texttype' => $texttype,
                    'textsize' => $textsize,
                    'maskedinputchoice' => $maskedinputchoice,
                    'label' => $textinputlabel,
                    'labelorientation' => $labelorientation
                ));
        return $id;
    }
    
    function updateSingle($formNumber,$textinputformname,$textinputname,$textvalue,$texttype,$textsize,$maskedinputchoice,$formElementLabel,$formElementLabelLayout){
        $UpdateSQL = "UPDATE tbl_formbuilder_textinput_entity
        SET textvalue='".$textvalue."', texttype='".$texttype."', textsize='".$textsize."', maskedinputchoice='".$maskedinputchoice."', label='".$formElementLabel."', labelorientation='".$formElementLabelLayout."' WHERE textinputformname='".$textinputformname."' and textinputname='".$textinputname."' and formnumber='".$formNumber."'";
        $this->_execute($UpdateSQL);
    }

    /*!
     * \brief This member function deletes a text input according to its form element
     * indentifier.
     * \param  formElementName A string. This is form element identifier.
     * \return A boolean value whether its was successful or not.
     */
    function deleteFormElement($formNumber,$formElementName) {
        $sql = "where formnumber like '".$formNumber."' and textinputformname like '" . $formElementName . "'";
        $valueExists = $this->getRecordCount($sql);
        if ($valueExists >= 1) {
            $deleteSQLStatement = "DELETE FROM tbl_formbuilder_textinput_entity WHERE formnumber like '".$formNumber."' AND textinputformname like '" . $formElementName . "'";
            $this->_execute($deleteSQLStatement);
            $this->delete("textinputformname", $formElementName);
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
