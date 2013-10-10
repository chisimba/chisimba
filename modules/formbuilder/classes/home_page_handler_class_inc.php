<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class home_page_handler
 *
 *  \brief This class models all the home cotnent for this module.
 *  \brief This class get content for a text file inside the resources folder
 * and spits out some content.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
class home_page_handler extends object {

    /*!
     * \brief Standard constructor that loads classes for other modules.
     * \note the htmlheading and link class are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /*!
     * \brief This member function builds the content for the home page.
     * \return Constructed home page content.
     */
    private function buildHomePage() {
///Create a html heading
        $homePageHeading = new htmlheading("Form Builder", 2);
        $homePageUnderConstruction = $homePageHeading->show();

///Open the home page text file
        $myFile = html_entity_decode($this->getResourceUri('textfiles/home.txt', $this->moduleName));
//  $myFile = "packages/".$this->moduleName."/resources/textfiles/home.txt";
///Read all the content from the file
        $fh = fopen($myFile, 'r');

///Insert the content into page content
        $theData = fread($fh, filesize($myFile));

///Close the file
        fclose($fh);
        $homePageUnderConstruction .=$theData;
///Create another html heading
        $QuickStartHeading = new htmlheading("Quick Start Links", 3);
        $homePageUnderConstruction .= $QuickStartHeading->show();

        $createNewFormlink = new link($this->uri(array(
                            'module' => $this->moduleName,
                            'action' => 'addFormParameters'
                        )));
        $createNewFormlink->link = "Create a New Form";

        $homePageUnderConstruction .= $createNewFormlink->show() . "<br>";
///Create all the module links
        $listAllFormslink = new link($this->uri(array(
                            'module' => $this->moduleName,
                            'action' => 'listAllForms'
                        )));
        $listAllFormslink->link = "List All Designed Forms";
        $homePageUnderConstruction .= $listAllFormslink->show() . "<br>";

//$homePageUnderConstruction .= $createNewFormlink->show()."<br>";

        $helpLink = new link($this->uri(array(
                            'module' => $this->moduleName,
                            'action' => 'moduleHelp'
                        )));
        $helpLink->link = "Module Help and Tutorials";
        $homePageUnderConstruction .= $helpLink->show() . "<br>";

///Return all the constructed cotent.
        return $homePageUnderConstruction;
    }

    /*!
     * \brief This member function provides an interface to home page content. Call
     * it to get all the home page content.
     * \return Constructed home page content.
     */
    public function showHomePage() {
        return $this->buildHomePage();
    }

}

?>
