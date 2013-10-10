<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_publish_options------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_publish_options
 *
 *  \brief Class that models the interface for the tbl_formbuilder_publish_options database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_publish_options table.
 *  \brief This class provides functions to insert, sort, search, edit and delete entries in the database.
 *  \brief This class is solely used by the form_entity handler and view_form_list class.
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
class dbformbuilder_publish_options extends dbTable {
    
    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_publish_options');
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
     * \brief This member function returns a single record with a form number
     * \param formNumber A string.
     * \return An array with a single value
     */
    function listSingle($formNumber) {
        return $this->getAll("WHERE formnumber='" . $formNumber . "'");
    }

    /*!
     * \brief This member function returns the number of records in the database
     * \return An integer
     */
    function getNumberOfForms() {
        return $this->getRecordCount();
    }

    /*!
     * \brief This member function returns the publishing data for a certain form
     * with a form number as an argument.
     * \param formNumber An integer.
     * \return An array with all the publishing data for that form.
     */
    function getFormPublishingData($formNumber) {
///Select all for the publishing data where the form number is like the search
///parameter and store the result in a temporary variable sql.
        $sql = "select * from tbl_formbuilder_publish_options where formnumber like '" . $formNumber . "'";
///Return the array of entries. Note that is function in part of the parent class dbTable.
        return $this->getArray($sql);
    }

    /*!
     * \brief This member function return the real time stamp.
     * \note now() is a member function in core class dbTable
     * \return An string with a time stamp.
     */
    function getSubmitTime() {
        return $this->now();
    }

    /*!
     * \brief This member function checks if publishing data exists for a certain
     * form with a form number supplied as an argument.
     * \param formNumber An integer.
     * \return An boolean. "true" if the publishing data exists and false
     * if the publishing data does not exist.
     */
    function checkIfPushlishingDataExists($formNumber) {
        if ($this->getRecordCount("WHERE formnumber='" . $formNumber . "'") > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*!
     * \brief Insert a record
     * \param  formNumber An integer.
     * \param formName A string. Form Metadata.
     * \param publishOption A string. This option  has two possibilites either
     * "advanced" or "simple".
     * \param siteURL A string. If the simple publishing option is chosen then
     * this URL will be used for diverting.
     * \param chisimbaModule If the advanced publishing option is chosen then this
     * will be the module the form builder diverts to.
     * \param chisimbaAction If the advanced publishing option is chosen then this
     * will be the action within the specified module the form builder hands over
     * control to.
     * \param chisimbaParameters A string. Two possibilites exist. If this is selected
     * as yes then the form element submission data will get passed to the specified
     * action.
     * \divertDelay A string. Three possibilities exist. "5", "10" as in seconds or null
     * to divert immediately.
     * \return A newly creating random id that gets saved with the new entry.
     */
    function insertSingle($formNumber, $formName, $publishOption, $siteURL, $chisimbaModule, $chisimbaAction, $chisimbaParameters, $divertDelay) {

        $id = $this->insert(array(
                    'formnumber' => $formNumber,
                    'formname' => $formName,
                    'publishoption' => $publishOption,
                    'siteurl' => $siteURL,
                    'chisimbamodule' => $chisimbaModule,
                    'chisimbaaction' => $chisimbaAction,
                    'chisimbaparameters' => $chisimbaParameters,
                    'chisimbadiverterdelay' => $divertDelay
                ));
        return $id;
    }

    /*!
     * \brief Update existing form publishing parameters.
     * \param  formNumber An integer.
     * \param publishOption A string. This option  has two possibilites either
     * "advanced" or "simple".
     * \param siteURL A string. If the simple publishing option is chosen then
     * this URL will be used for diverting.
     * \param chisimbaModule If the advanced publishing option is chosen then this
     * will be the module the form builder diverts to.
     * \param chisimbaAction If the advanced publishing option is chosen then this
     * will be the action within the specified module the form builder hands over
     * control to.
     * \param chisimbaParameters A string. Two possibilites exist. If this is selected
     * as yes then the form element submission data will get passed to the specified
     * action.
     * \divertDelay A string. Three possibilities exist. "5", "10" as in seconds or null
     * to divert immediately.
     * \return The form number initially inserted as an argument.
     */
    function updateSingle($formNumber, $publishOption, $siteURL, $chisimbaModule, $chisimbaAction, $chisimbaParameters, $divertDelay) {

        $this->update("formnumber", $formNumber, array(
            'publishoption' => $publishOption,
            'siteurl' => $siteURL,
            'chisimbamodule' => $chisimbaModule,
            'chisimbaaction' => $chisimbaAction,
            'chisimbaparameters' => $chisimbaParameters,
            'chisimbadiverterdelay' => $divertDelay
        ));
        return $formNumber;
    }

    /*!
     * \brief Update existing form simple publishing parameters.
     * \param  formNumber An integer.
     * \param publishOption A string. This option  has two possibilites either
     * "advanced" or "simple".
     * \param siteURL A string. If the simple publishing option is chosen then
     * this URL will be used for diverting.
     * \divertDelay A string. Three possibilities exist. "5", "10" as in seconds or null
     * to divert immediately.
     * \return The form number initially inserted as an argument.
     */
    function updateSimplePublishingParameters($formNumber, $publishOption, $siteURL, $divertDelay) {
        $this->update("formnumber", $formNumber, array(
            'publishoption' => $publishOption,
            'siteurl' => $siteURL,
            'chisimbadiverterdelay' => $divertDelay
        ));
        return $formNumber;
    }

    /*!
     * \brief Update existing form advanced publishing parameters.
     * \param  formNumber An integer.
     * \param publishOption A string. This option  has two possibilites either
     * "advanced" or "simple".
     * \param chisimbaModule If the advanced publishing option is chosen then this
     * will be the module the form builder diverts to.
     * \param chisimbaAction If the advanced publishing option is chosen then this
     * will be the action within the specified module the form builder hands over
     * control to.
     * \param chisimbaParameters A string. Two possibilites exist. If this is selected
     * as yes then the form element submission data will get passed to the specified
     * action.
     * \divertDelay A string. Three possibilities exist. "5", "10" as in seconds or null
     * to divert immediately.
     * \return The form number initially inserted as an argument.
     */
    function updateAdvancedPublishingParameters($formNumber, $publishOption, $chisimbaModule, $chisimbaAction, $chisimbaParameters, $divertDelay) {

        $this->update("formnumber", $formNumber, array(
            'publishoption' => $publishOption,
            'chisimbamodule' => $chisimbaModule,
            'chisimbaaction' => $chisimbaAction,
            'chisimbaparameters' => $chisimbaParameters,
            'chisimbadiverterdelay' => $divertDelay
        ));
        return $formNumber;
    }

    /*!
     * \brief This member function will update an existing form publishing data
     * entry to be unpublished.
     * \param  formNumber An integer.
     * \param publishOption A string. This option will most probably be set as
     * "unpublished" as an argument.
     * \return The form number initially inserted as an argument.
     */
    function unpublishForm($formNumber, $publishOption) {
        $this->update("formnumber", $formNumber, array(
            'publishoption' => $publishOption,
            'formnumber' => $formNumber
        ));
        return $formNumber;
    }

    /*!
     * \brief Delete a record according to its id
     * \param id A string.
     * \return Nothing.
     */
    function deleteSingle($formNumber) {
        $this->delete("formnumber", $formNumber);
    }

}

?>
