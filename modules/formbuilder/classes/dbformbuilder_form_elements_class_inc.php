<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_form_elements------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_form_elements
 *
 *  \brief Class that models the interface for the tbl_formbuilder_form_elements database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_form_elements table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity handler class.
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
class dbformbuilder_form_elements extends dbTable {
    
    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_form_elements');
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
     * \brief This member function returns all entries that have a specific form
     * element type and belong to one form with a form number.
     * \param formNumber An integer.
     * \param formElementType A string.
     * \return An array with a all the values of a single form element type.
     */
    function listFormElementsTypeForForm($formNumber,$formElementType)
    {
             $sql = "select * from tbl_formbuilder_form_elements where formnumber like '" . $formNumber . "'
                 and formelementtpye like '" . $formElementType . "' order by formelementorder asc";

///Return the array of entries. Note that is function in part of the parent class dbTable.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function returns multiple records with a particular
     * form number.
     * \param formNumber A string that contains a number.
     * \return An array with all the form elements belonging to the same form
     * number.
     */
    function listFormElementsForForm($formNumber) {
///Store a mysql query string into a temporary variable
///Select all entries that belong to the form number suppled by the
///argument.
        $sql = "select * from tbl_formbuilder_form_elements where formnumber like '" . $formNumber . "'  order by formelementorder asc";

///Return the array of entries. Note that is function in part of the parent class dbTable.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function returns the form number to a form element name
     * the form belongs to
     * \param formElementName A string with the form element identifier.
     * \return An integer with the form number.
     */
    function getFormNumber($formElementName)
    {
              $sql = "select * from tbl_formbuilder_form_elements where formelementname like '" . $formElementName . "'";

///Return the array of entries. Note that is function in part of the parent class dbTable.
       $selectedFormElementArray= $this->getArray($sql);
               return $formNumber = $selectedFormElementArray[0]["formnumber"];
    }

    /*!
     * \brief Insert a record
     * \param  formNumber A string that contains a number. This form number is important
     * as all of the form elements belonging to a certain form number are inside
     * that form.
     * \param formName A string. This is form metadata.
     * \param formElementType A string. This can any one of the form element types
     * provided by the form builder.
     * \param formElementName A string that contains the form element identifier.
     * \return A string with the form name supplied.
     */
    function insertSingle($formNumber, $formName, $formElementType, $formElementName) {
///Check the last or maximum form number
        $sql = " SELECT MAX(formelementorder) AS formelementorder FROM tbl_formbuilder_form_elements";
        $maxFormOrder = $this->getArray($sql);
        $formElementOrder = $maxFormOrder[0]["formelementorder"];
        if ($formElementOrder == NULL) {
            $formElementOrder = 1;
        } else {
            $formElementOrder++;
        }
///Set a new form number by incrementing the last or maximum form number.
///Insert the new entry with this new form number.
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'formname' => $formName,
                    'formelementtpye' => $formElementType,
                    'formelementname' => $formElementName,
                    'formelementorder' => $formElementOrder,
                ));
///Return the supplied form number.
        return $formName;
    }

    /*!
     * \brief This member function checks for duplicate form names or numbers
     * \param formElementName. A string.
     * \param formName A string. Form metadata.
     * \return An boolean value depending on the success or failiure.
     */
    function checkDuplicateFormElementName($formElementName, $formName) {

///Get entries where the form element name and the form name name are the search
/// parameters.
        $sql = "where formname like '" . $formName . "' and formelementname like '" . $formElementName . "'";

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
     * \brief This member function update the order of the form element with in
     * a form
     * \param formNumber. A string containing a number.
     * \param formElementName A string. Form metadata.
     * \param formOrder. The form element order the needs to be updated.
     * \return An boolean value depending on the success or failiure.
     */
    function updatFormElementOrder($formNumber, $formElementName, $formOrder) {
///Check for entries in the database that have a form number and
/// form element name according to search parameters.
        $sql = "where formnumber like '" . $formNumber . "' and formelementname like '" . $formElementName . "'";

///Get the number of entries and check if there is only one.
        $valueExists = $this->getRecordCount($sql);
        if ($valueExists == 1) {
///Update that entry's form order
            $this->update("formelementname", $formElementName, array(
                'formelementorder' => $formOrder
            ));
///return true
            return true;
        } else {
///if there is no entry or multple entries then return an error.
            return "Error. Form Element Name is not Found with this form";
        }
    }

    /*!
     * \brief This member function deletes a form element according to its form element
     * indentifier and form number.
     * \param  formElementName A string. This is form element identifier.
     * \param formNumber A string which contains a number.
     * \return An string with the form element type.
     */
    function deleteFormElement($formElementName, $formNumber) {
///Get all the entries the have a form element name and form number according to the
///search values.
        $sql = "where formnumber like '" . $formNumber . "' and formelementname like '" . $formElementName . "'";
        $valueExists = $this->getRecordCount($sql);
///Get the number of records for the entries. Note the getRecordCount
///is a dbtable member function.
        if ($valueExists > 0) {
///If there are values that exist then delete the records and return true
///otherwise return false.
            $formElementDBEntry = $this->getAll("where formnumber like '" . $formNumber . "' and formelementname like '" . $formElementName . "'");
            $formElementDBEntryType = $formElementDBEntry[0]["formelementtpye"];

            $this->delete("formelementname", $formElementName);

            return $formElementDBEntryType;
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
