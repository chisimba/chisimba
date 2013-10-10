<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class side_menu_handler
 *
 *  \brief This class models all the navigation menus for this module.
 * \brief There are two diferent types of menus. The slide menu has items next to
 * each other while the side menu has items under each other.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
class side_menu_handler extends object {

    /*!
     * \brief Standard constructor that loads classes for other modules.
     * \note the button class are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('button', 'htmlelements');
    }

    /*!
     * \brief This member function constructs a slide menu. This menu contains
     * buttons next to each other.
     * \note This member function only provides the html content. The fancy
     * icons and functionality is done in the jQuery javascript.
     * \return A build slide menu.
     */
    private function buildSlideMenu() {

//Some of this code is none functional. Only the links specified are used.
//------------------------
        $objHomeButton = new button('homeButton');
        $objHomeButton->setValue('Home');
        $objHomeButton->setCSS("homeButton");
        $mngHomelink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'home'
                        )));
        $objHomeButton->setOnClick("parent.location='$mngHomelink'");
        $mngHomeButton = $objHomeButton->showDefault();



        $objListAllFormsButton = new button('listAllFormsButton');
        $objListAllFormsButton->setValue('List All Forms');
        $objListAllFormsButton->setCSS("listAllFormsButton");
        $objListAllFormsLink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'listAllForms',
                        )));
        $objListAllFormsButton->setOnClick("parent.location='$objListAllFormsLink'");
        $mngListAllFormsButton = $objListAllFormsButton->showDefault();

        $objCreateNewFormButton = new button('createNewFormButton');
        $objCreateNewFormButton->setValue('Create New Form');
        $objCreateNewFormButton->setCSS("createNewFormButton");
        $objCreateNewFormLink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'addFormParameters',
                        )));
        $objCreateNewFormButton->setOnClick("parent.location='$objCreateNewFormLink'");
        $mngCreateNewFormButton = $objCreateNewFormButton->showDefault();

        $objHelpButton = new button('helpButton');
        $objHelpButton->setValue('Help');
        $objHelpButton->setCSS("helpButton");
        $objHelpLink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'moduleHelp',
                        )));
        $objHelpButton->setOnClick("parent.location='$objHelpLink'");
        $mngHelpButton = $objHelpButton->showDefault();
//----------------------
//This is the acutal slide menu
        $mngHomeButton = "<button class='homeButton' onclick=parent.location='$mngHomelink'>Home</button>";
        $mngListAllFormsButton = "<button class='listAllFormsButton' onclick=parent.location='$objListAllFormsLink'>List All Designed Forms</button>";
        $mngCreateNewFormButton = "<button class='createNewFormButton' onclick=parent.location='$objCreateNewFormLink'>Create A New Form</button>";
        $mngHelpButton = "<button class='helpButton' onclick=parent.location='$objHelpLink'>Help</button>";
        $slideMenuUnderConstruction = $mngHomeButton
                . $mngListAllFormsButton
                . $mngCreateNewFormButton
                . $mngHelpButton;

        return $slideMenuUnderConstruction;
    }

    /*!
     * \brief This member function constructs a slide menu. This menu contains
     * buttons and links under each other.
     * \note This member function only provides the html content. The fancy
     * icons and functionality is done in the jQuery javascript.
     * \return A build side menu.
     */
    private function buildSideMenu() {
//Some of this code is none functional. Only the links specified are used.
//------------------------

        $objHomeButton = new button('homeButton');
        $objHomeButton->setValue('Home');
        $objHomeButton->setCSS("homeButton");
        $mngHomelink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'home'
                        )));
        $objHomeButton->setOnClick("parent.location='$mngHomelink'");
        $mngHomeButton = $objHomeButton->showDefault();



        $objListAllFormsButton = new button('listAllFormsButton');
        $objListAllFormsButton->setValue('List All Forms');
        $objListAllFormsButton->setCSS("listAllFormsButton");
        $objListAllFormsLink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'listAllForms',
                        )));
        $objListAllFormsButton->setOnClick("parent.location='$objListAllFormsLink'");
        $mngListAllFormsButton = $objListAllFormsButton->showDefault();

        $objCreateNewFormButton = new button('createNewFormButton');
        $objCreateNewFormButton->setValue('Create New Form');
        $objCreateNewFormButton->setCSS("createNewFormButton");
        $objCreateNewFormLink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'addFormParameters',
                        )));
        $objCreateNewFormButton->setOnClick("parent.location='$objCreateNewFormLink'");
        $mngCreateNewFormButton = $objCreateNewFormButton->showDefault();

        $objHelpButton = new button('helpButton');
        $objHelpButton->setValue('Help');
        $objHelpButton->setCSS("helpButton");
        $objHelpLink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'moduleHelp',
                        )));
        $objHelpButton->setOnClick("parent.location='$objHelpLink'");
        $mngHelpButton = $objHelpButton->showDefault();
        
        $objStyleSettingsButton = new button('styleSettingsButton');
        $objStyleSettingsButton->setValue('Style Settings');
        $objStyleSettingsButton->setCSS("styleSettingsButton");
        $mngStyleSettingslink = html_entity_decode($this->uri(array(
                            'module' => 'formbuilder',
                            'action' => 'styleSettings'
                        )));
        $objStyleSettingsButton->setOnClick("parent.location='$mngStyleSettingslink'");
        $mngStyleSettingsButton = $objStyleSettingsButton->showDefault();

//----------------------
//This is the acutal side menu
        $mngHomeButton = "<button class='homeButton' onclick=parent.location='$mngHomelink'>Home</button>";
        $mngListAllFormsButton = "<button class='listAllFormsButton' onclick=parent.location='$objListAllFormsLink'>List All Forms</button>";
        $mngCreateNewFormButton = "<button class='createNewFormButton' onclick=parent.location='$objCreateNewFormLink'>Create A New Form</button>";
        $mngStyleSettinsButton = "<button class='styleSettingsButton' onclick=parent.location='$mngStyleSettingslink'>Style Settings</button>";
        $mngHelpButton = "<button class='helpButton' onclick=parent.location='$objHelpLink'>Help</button>";
        $sideMenuUnderConstruction = $mngHomeButton
                . "<br>" . $mngListAllFormsButton
                . "<br>" . $mngCreateNewFormButton
                . "<br>" . $mngStyleSettinsButton
                . "<br>" . $mngHelpButton;
        return $sideMenuUnderConstruction;
    }

    /*!
     * \brief This member function allows you to get a side menu, when this class
     * is instatiated.
     * \return A build side menu.
     */
    public function showSideMenu() {
        return $this->buildSideMenu();
    }

    /*!
     * \brief This member function allows you to get a slide menu, when this class
     * is instatiated.
     * \return A build slide menu.
     */
    public function showSlideMenu() {
        return "<div id='mainMenu' style='float:left;'>" . $this->buildSlideMenu() . "</div>";
    }

}

?>
