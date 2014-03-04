<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_submit_results------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_submit_results
 *
 *  \brief Class that models the interface for the tbl_formbuilder_submit_results database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_submit_results table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_submit_results_handler class.
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
class dbformbuilder_submit_results extends dbTable {

    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_submit_results');
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
     * \return An array with a single value
     */
    function listSingle($id) {
        return $this->getAll("WHERE id='" . $id . "'");
    }

    /*!
     * \brief This member function returns the number of form elements for a
     * particular form with a form and submit numbers as arguments.
     * \param formNumber A string.
     * \param submitNumber An integer.
     * \return An integer with the number of form fields in a form.
     */
    function getNumberofSubmissionElements($formNumber, $submitNumber) {
        $sql = "where formnumber like '" . $formNumber . "' and submitnumber like '" . $submitNumber . "'";
        return $numberofFormSubmissionElements = $this->getRecordCount($sql);
    }

    /*!
     * \brief This member function returns all the submit results for a form
     * with a form number as an argument.
     * \param formNumber A string.
     * \param submitNumber An integer.
     * \return An multi-dimensional array with all the submission results.
     */
    function getAllFormResults($formNumber) {
        return $this->getAll("WHERE formnumber='" . $formNumber . "'");
    }

    /*!
     * \brief This member function returns a submit result for a particular
     * submission.
     * \param submitNumber An integer.
     * \return An multi-dimensional array with all the submission results for that
     * submission.
     */
    function getParticularSubmitResults($submitNumber) {
        return $this->getAll("WHERE submitnumber='" . $submitNumber . "'order by formelementname asc");
    }

    /*!
     * \brief This member function returns only the submit number, name of submitter
     * a the time stamp of submission into an array.
     * \param formNumber A string.
     * \param number_of_entries_per_pagination_request An integer to get only
     * this number of entries.
     * \ param starting_element An integer to start from any point in the
     * database.
     * \return An multi-dimensional array with all the submission result details.
     */
    function getOnlyDistinctFormResults($formNumber, $number_of_entries_per_pagination_request, $starting_element) {
        $sql = "select submitnumber,useridofformsubmitter,max(timeofsubmission) as timeofsubmission
from tbl_formbuilder_submit_results where formnumber like '" . $formNumber . "'
group by submitnumber,useridofformsubmitter
order by submitnumber asc LIMIT $number_of_entries_per_pagination_request OFFSET $starting_element";
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function returns only the submit numbers into an array.
     * \param formNumber A string.
     * \return An multi-dimensional array with all the submission numbers.
     */
    function getOnlyFormSubmitNumbers($formNumber) {
        $sql = "select submitnumber from tbl_formbuilder_submit_results where formnumber like '" . $formNumber . "'
group by submitnumber
order by submitnumber asc";
        return $this->getArray($sql);
    }

    function getOnlyFormElementTypes($formNumber) {
        $sql = "select formelementname,formelementtype
from tbl_formbuilder_submit_results where formnumber like '" . $formNumber . "'
group by formelementname,formelementtype
order by formelementname asc";

        return $this->getArray($sql);
    }

    /*!
     * \brief This member function returns only the submit number, name of submitter
     * a the time stamp of submission into an array. It only puts the
     * lastest entry
     * \param formNumber A string.
     * \param number_of_entries_per_pagination_request An integer to get only
     * this number of entries.
     * \ param starting_element An integer to start from any point in the
     * database.
     * \return An multi-dimensional array with all the submission result details.
     */
    function getLatestSubmitResult($formNumber, $number_of_entries_per_pagination_request, $starting_element) {

        $sql = "select max(submitnumber) as submitnumber,useridofformsubmitter,max(timeofsubmission) as timeofsubmission
from tbl_formbuilder_submit_results where formnumber like '" . $formNumber . "'
group by useridofformsubmitter
order by submitnumber asc LIMIT $number_of_entries_per_pagination_request OFFSET $starting_element";

        return $this->getArray($sql);
    }

    /*!
     * \brief When submitting a form this member function returns the rank of the
     * submissio based on the form number and submit number.
     * \param formNumber A string.
     * \param submitNumber An integer.
     * \return A string telling the user of the submision rank.
     */
    function getCurrentSubmitNumberofSubmitter($formNumber, $submitNumber) {
        $sql = "where formnumber like '" . $formNumber . "' and submitnumber like '" . $submitNumber . "'";
        $numberofFormSubmissionElements = $this->getRecordCount($sql);
        if ($numberofFormSubmissionElements == NULL) {
            return "1<sup>st</sup>";
        }

        $sqlStatement = "select * from tbl_formbuilder_submit_results where formnumber like '" . $formNumber . "'";
        $submitNumbersArray = $this->getArray($sqlStatement);
        $submitNumberofSubmitter = 0;
        foreach ($submitNumbersArray as $thisSubmitNumber) {


            $databaseSubmitNumber = $thisSubmitNumber["submitnumber"];
            $submitNumberofSubmitter++;
            if ($submitNumber == $databaseSubmitNumber) {
                $realSubmitNumber = ceil($submitNumberofSubmitter / $numberofFormSubmissionElements);
                switch ($realSubmitNumber) {
                    case '1':
                        return "1<sup>st</sup>";
                        break;
                    case '2':
                        return "2<sup>nd</sup>";
                        break;
                    case '3':
                        return "3<sup>rd</sup>";
                        break;
                    default:
                        return $realSubmitNumber . "<sup>th</sup>";
                }
            }
        }
        return "[Error. No Submit Number Match Found.]";
    }

    /*!
     * \brief When submitting a form this member function determines the next
     * submit number to the form submission.
     * \param formNumber A string.
     * \return An integer with a new submit number.
     */
    function getNextSubmitNumber($formNumber) {
        $sqlStatement = "SELECT MAX(submitnumber) AS submitnumber FROM tbl_formbuilder_submit_results";
        $submitNumberOrder = $this->getArray($sqlStatement);
        $maxSubmitNumber = $submitNumberOrder[0]["submitnumber"];
        if ($maxSubmitNumber == NULL) {
            return $maxSubmitNumber = 1;
        } else {
            $maxSubmitNumber++;
        }
        return $maxSubmitNumber;
    }

    /*!
     * \brief This member function returns a user's full name if their user id
     * is supplied as an argument.
     * \param userID A string.
     * \return A string with the user's full name.
     */
    function getSubmitUsersFullName($userID) {
        return $this->objUser->fullname($userID);
    }

    /*!
     * \brief This member function returns a users' email address if their user id
     * is supplied as an argument.
     * \param userID A string.
     * \return A string with the user's full name.
     */
    function getSubmitUsersEmail($userID) {
        return $this->objUser->email($userID);
    }

    /*!
     * \brief Insert a new record
     * \param formNumber A string.
     * \param submitNumber An integer.
     * \param formElementType An string defining the type of form field.
     * \param formElementName An string defining the name of form field.
     * \param formElementValue An string defining the what the user filled
     * or selected for this form field.
     * \return A null value.
     */
    function insertSingle($formNumber, $submitNumber, $formElementType, $formElementName, $formElementValue) {

        $userid = $this->objUser->userId();
        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'submitnumber' => $submitNumber,
                    'formelementtype' => $formElementType,
                    'formelementname' => $formElementName,
                    'formelementvalue' => $formElementValue,
                    'useridofformsubmitter' => $userid,
                    'timeofsubmission' => $this->now()
                ));
        return $id;
    }

    /*!
     * \brief This member function deletes all form submissions for a particular
     * form with a form number as an argument.
     * \param formNumber A string.
     * \return A boolean value whether its was successful or not.
     */
    function deleteAllSubmissions($formNumber) {
        $sql = "where formnumber like '" . $formNumber . "'";
        $numberOfSubmissions = $this->getRecordCount($sql);
        if ($numberOfSubmissions > 0) {
//$formElementDBEntry = $this->getAll("where formnumber like '".$formNumber."' and formelementname like '".$formElementName."'");
////$formElementDBEntry = $this->getArray($sql);
//   $formElementDBEntryType = $formElementDBEntry[0]["formelementtpye"];
            $this->delete("formnumber", $formNumber);
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
