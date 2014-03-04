<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_helloforms_comments------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_form_list
 *
 *  \brief Class that models the interface for the tbl_formbuilder_form_elements database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_form_list table.
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
class dbformbuilder_form_list extends dbTable {

    /*!
     * Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_form_list');
        $this->objUser = &$this->getObject('user', 'security');
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
     * \return An array with a single entry
     */
    function listSingle($formNumber) {
        return $this->getAll("WHERE formnumber='" . $formNumber . "'");
    }

    /*!
     * \brief This member function returns the number of forms
     * \return An integer. The number of entries in this table.
     */
    function getNumberOfForms() {
        return $this->getRecordCount();
    }

    /*!
     * \brief This member function returns the user's full name as according
     * if a user id is supplied.
     * \param userID An integer.
     * \return A string. The user full name.
     */
    function getFormAuthorsFullName($userID) {
        return $this->objUser->fullname($userID);
    }

    /*!
     * \brief This member function returns the form title if the form number is
     * supplied as an argument.
     * \param formNumber An integer.
     * \return A string. The form title.
     */
    function getFormName($formNumber) {
        $sql = "select * from tbl_formbuilder_form_list where formnumber='" . $formNumber . "'";
        $formParameters = $this->getArray($sql);
        return $formParameters['0']['name'];
    }

    /*!
     * \brief This member function returns the number of searched forms that
     * have contain any metadata that closely resembles the search parameter.
     * \param searchValue Any string.
     * \return An integer with the number of searched entries.
     */
    function getNumberofSearchedEntries($searchValue) {
        $sql = "where name like '%" . $searchValue . "%' || label like '%$searchValue%' || details like '%$searchValue%' || author like '%$searchValue%'
|| submissionemailaddress like '%$searchValue%' || created like '%$searchValue%'";
        return $this->getRecordCount($sql);
    }

    /*!
     * \brief This member function returns the entries of the searched forms that
     * have contain any metadata that closely resembles the search parameter.
     * \param searchValue Any string.
     * \return A multi-dimesional array with entries.
     */
    function searchFormList($searchValue, $number_of_entries_per_page = 5, $starting_element = 0) {

        $sql = "select * from tbl_formbuilder_form_list where name like '%" . $searchValue . "%' || label like '%$searchValue%' || details like '%$searchValue%' || author like '%$searchValue%'
|| submissionemailaddress like '%$searchValue%' || searchclobmetadata like '%$searchValue%' || created like '%$searchValue%' LIMIT $number_of_entries_per_page OFFSET $starting_element
";
//  $sql = "select tbl_formbuilder_form_list.formnumber from tbl_formbuilder_form_list, tbl_formbuilder_form_elements where tbl_formbuilder_form_elements.formelementname like '%$searchValue%'";

        return $this->getArray($sql);
    }

    /*!
     * \brief This member function returns a selected few entries for pagination
     * \param number_of_entries_per_page An integer.
     * \param starting_element An integer.
     * \return A multi-dimesional array with entries.
     */
    function getPaginatedEntries($number_of_entries_per_page = 5, $starting_element = 0) {
///Select all or * entries from this table and limit and spit out entries from a starting
///index to a number of entries specified
///and store the result in a temporary variable. The language MYSQL is used to do this. It is important to
///note that no changes are being made in the database.
        $sql = "select*from tbl_formbuilder_form_list LIMIT $number_of_entries_per_page OFFSET $starting_element";
///Convert the temporary variable into an array and return it.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function gets the metadata for a particular form.
     * \param formNumber An integer.
     * \return A multi-dimesional array of entries for one form.
     */
    function getFormMetaData($formNumber) {
///Select all stuff from this table for a form number
// and store the result in a temporary variable sql.
        $sql = "select * from tbl_formbuilder_form_list where formnumber like '" . $formNumber . "'";

///Return the array of entries. Note that is function in part of the parent class dbTable.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function returns a real time stamp.
     * \return A string with a time stamp.
     */
    function getSubmitTime() {
        return $this->now();
    }

    /*!
     * \brief This member function checks whether another form name exists
     * to the one in the argument list.
     * \param  formNumber An integer.
     * \param formName An string
     * \return A boolean value
     */
    function checkDuplicateFormEntry($formNumber, $formName) {

        $sqlStatementForFormName = "where name like '" . $formName . "'";
        $numberofDuplicatesFormName = $this->getRecordCount($sqlStatementForFormName);
        if ($numberofDuplicatesFormName < 1) {
            return true;
        } else {
            return FALSE;
        }
    }

    /*!
     * \brief This member function returns one increment of the highest form number
     * found in the database.
     * \return An integer for the current form number.
     */
    function getCurrentFormNumber() {
        $sqlStatement = "SELECT MAX(formnumber) AS formnumber FROM tbl_formbuilder_form_list";
        $formNumberOrder = $this->getArray($sqlStatement);
        $maxFormNumber = $formNumberOrder[0]["formnumber"];
        if ($maxFormNumber == NULL) {
            return $maxFormNumber = 1;
        } else {
            return $maxFormNumber+=1;
        }
    }

        /*!
     * \brief This member function gets all the details of the designer of the
     * form and concantenates them into a string.
     * \note This will help in searching for forms when designers put
     * in their details to find their forms.
     * \return An string with all desinger's user details.
     */
    function getSearchClobMetadata() {
       $userid = $this->objUser->userId();
       $searchClobMetaData = "";
        $searchClobMetaData .=  "  " .$userid;
        $userName = $this->objUser->userName();
        $searchClobMetaData .= "  " .$userName;
        $userTitle = $this->objUser->getTitle();
        $searchClobMetaData .= "  " . $userTitle;
        $fullName = $this->objUser->fullname();
        $searchClobMetaData .= "  " .$fullName;
        $staffNumber = $this->objUser->getStaffNumber();
        $searchClobMetaData .= "  " . $staffNumber;
        $email = $this->objUser->email();
        $searchClobMetaData .= "  " .$email;
        return $searchClobMetaData;
    }

    /*!
     * \brief Insert a record
     * \param formName A string. This form name is important
     * as all of the form elements belonging to a certain form name are inside
     * that form.
     * \param formLabel A string. This is form title or metadata.
     * \param formDetails A string. Thhis the form's description and is metadata.
     * \param submissionEmailAddress A string.
     * \param submissionOption A string to determine whether to submit
     * results in a database or email them or both.

     * \return An integer for a newly form number for this form.
     */
    function insertSingle($formName, $formLabel, $formDetails, $submissionEmailAddress, $submissionOption) {
        $sqlStatement = "SELECT MAX(formnumber) AS formnumber FROM tbl_formbuilder_form_list";
        $formNumberOrder = $this->getArray($sqlStatement);
        $maxFormNumber = $formNumberOrder[0]["formnumber"];
        if ($maxFormNumber == NULL) {
            $maxFormNumber = 1;
        } else {
            $maxFormNumber++;
        }


       $userid = $this->objUser->userId();
 
        $id = $this->insert(array(
                    'formnumber' => $maxFormNumber,
                    'name' => $formName,
                    'label' => $formLabel,
                    'details' => $formDetails,
                    'author' => $userid,
                    'submissionemailaddress' => $submissionEmailAddress,
                    'submissionoption' => $submissionOption,
                    'searchclobmetadata' => $this->getSearchClobMetadata(),
                    'created' => $this->now()
                ));
        return $maxFormNumber;
    }

    /*!
     * \brief Update a record
     * \param formNumber This integer will be used to update the entry.
     * \param formName A string. This form name is important
     * as all of the form elements belonging to a certain form name are inside
     * that form.
     * \param formLabel A string. This is form title or metadata.
     * \param formDetails A string. Thhis the form's description and is metadata.
     * \param submissionEmailAddress A string.
     * \param submissionOption A string to determine whether to submit
     * results in a database or email them or both.

     * \return An integer for the form number for this form.
     */
    function updateSingle($formNumber, $formLabel, $formDetails, $submissionEmailAddress, $submissionOption) {
        $userid = $this->objUser->userId();
        $this->update("formnumber", $formNumber, array(
            'label' => $formLabel,
            'details' => $formDetails,
            'author' => $userid,
            'submissionemailaddress' => $submissionEmailAddress,
            'submissionoption' => $submissionOption
        ));
        return $formNumber;
    }

    /*!
     * \brief This member function deletes a form metadata record.
     * \param formNumber A string which contains a number.
     * \return Nothing.
     */
    function deleteSingle($formNumber) {
        $this->delete("formnumber", $formNumber);
    }

}

?>
