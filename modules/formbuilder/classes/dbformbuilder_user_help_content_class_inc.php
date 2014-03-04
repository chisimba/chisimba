<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* ----------- data class extends dbTable for tbl_formbuilder_user_help_content------------ */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/*!  \class dbformbuilder_user_help_content
 *
 *  \brief Class that models the interface for the tbl_formbuilder_user_help_content database.
 *  \brief It basically is an interface class between the formbuilder module and
 * the tbl_formbuilder_user_help_content table.
 *  \brief This class is solely used by the help_page_handler class.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 * \note It is important to realize that there are no insert or edit functions
 * inside this class. All the entries have already been inserted into a database
 * from the defaultdata.xml file inside the resources folder of this module.
 * \note This class only provides functionality to get certain help content inside
 * the database.
 * \warning This class's parent is dbTable which is a chisimba core class.
 * \warning If the dbTable class is altered in the future. This class may not work.
 * \warning Apart from normal PHP. This class uses the mysql language to provide
 * the actual functionality. This language is encapsulated with in "double quotes" in a string format.
 */
class dbformbuilder_user_help_content extends dbTable {
    
    /*!
     * \brief Constructor method to define the table
     */
    function init() {
        parent::init('tbl_formbuilder_user_help_content');
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
     * \brief This member function returns a certain help content according to
     * its name identifier.
     * \param name A string.
     * \return An array with relavent help content.
     */
    function listPageContent($name) {
        return $this->getAll("WHERE name='" . $name . "'");
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
