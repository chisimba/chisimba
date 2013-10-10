<?php
/**
* etdtools class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* etdtools class
* @author Megan Watson
* @author Jonathan Abrahams
* @copyright (c) 2004 UWC
* @version 0.1
*/

class etdtools extends object
{
    /**
    * @var string $rightContent String containing the content for the right side menu.
    */
    private $rightContent = '';

    /**
    * @var string $leftContent String containing the content for the left side menu.
    */
    private $leftContent = '';

    /**
    * @var bool $hideMenu Boolean value determining whether to hide the menu block on the right side
    */
    private $hideMenu = FALSE;

    /**
    * @var bool $hideLinks Boolean value determining whether to hide the links block on the right side
    */
    private $hideLinks = FALSE;

    /**
    * @var bool $hideHelp Boolean value determining whether to hide the help block on the right side
    */
    private $hideHelp = FALSE;

    /**
    * @var bool $hideHelp Boolean value determining whether to hide the help block on the right side
    */
    private $access = array('user');

    /**
    * Constructor method
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objCountry = $this->getObject('languagecode', 'language');
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');

        $this->objTable = $this->newObject('htmltable', 'htmlelements');
        $this->objHead = $this->newObject('htmlheading', 'htmlelements');
        $this->objLayer = $this->newObject('layer', 'htmlelements');
        $this->loadClass('link', 'htmlelements');

        $this->objBlocks = $this->newObject('blocks', 'blocks');

        $this->access = $this->getSession('accessLevel', array());
    }

    /**
    * Method to get the contents for the left column
    *
    * @access public
    * @return string html
    */
    public function getLeftSide()
    {
        if(isset($this->leftContent) && !empty($this->leftContent)){
            return $this->leftContent;
        }

        $str = $this->getBrowseMenu();
        $str .= $this->getLogin();
        return $str;
    }

    /**
    * Method to get the contents for the right column
    *
    * @access public
    * @return string html
    */
    public function getRightSide()
    {
        if(isset($this->rightContent) && !empty($this->rightContent)){
            return $this->rightContent;
        }

        return $str;
    }

    /**
    * Method to display the menu for browsing and searching the repository.
    *
    * @access private
    * @return string html
    */
    private function getBrowseMenu()
    {
        $str = '';
        if(!$this->hideMenu){
            $str = $this->objBlocks->showBlock('rightmenu', 'etd', '','','', FALSE);
        }
        if(!$this->hideLinks){
            $str .= $this->objBlocks->showBlock('etdlinks', 'etd', '','','', FALSE);
        }
        if(in_array('manager', $this->access) || in_array('editor', $this->access) || in_array('board', $this->access)){
            $str .= $this->objBlocks->showBlock('managemenu', 'etd', '','','', FALSE);
        }
        if(!$this->hideHelp){
            //$str .= $this->objBlocks->showBlock('etdhelp', 'etd', '','','', FALSE);
        }
        return $str;
    }

    /**
    * Method to get the login block for display on the home page
    *
    * @access private
    * @return string html
    */
    private function getLogin()
    {
        $str = $this->objBlocks->showBlock('login', 'security', '','','', TRUE, 'none');
        return $str;
    }

    /**
    * Method to set the content for the right side menu
    *
    * @access public
    * @param string $str The right side content
    * @param bool $append Flag to determine whether the new side content is appended to the standand content
    * @return
    */
    public function setRightSide($str, $append = FALSE)
    {
        if($append){
            $this->rightContent = $this->getLogin();
        }
        $this->rightContent .= $str;
    }

    /**
    * Method to set the content for the left side menu
    *
    * @access public
    * @param string $str The left side content
    * @param bool $append Flag to determine whether the new side content is appended to the standand content
    * @return
    */
    public function setLeftSide($str, $append = FALSE)
    {
        if($append){
            $this->leftContent = $this->getBrowseMenu();
        }
        $this->leftContent .= $str;
    }

    /**
    * Method to set the blocks on the left menu to hide or display
    *
    * @access public
    * @param bool $menu Determines whether to display the browse menu block
    * @param bool $links Determines whether to display the links block
    * @param bool $help Determines whether to display the help block
    * @return
    */
    public function setLeftBlocks($menu = FALSE, $links = FALSE, $help = FALSE)
    {
        $this->hideMenu = $menu;
        $this->hideSearch = $links;
        $this->hideHelp = $help;
    }

    /**
    * Method to create the dropdown of countries with additional entries for areas covering several countries
    *
    * @access public
    * @param string $selected The current selection
    * @return string html
    */
    public function getCountriesDropdown($selected = 'South Africa')
    {
        return $this->objCountry->countryAlpha();
    }

    /**
    * Method to create a dropdown list of degree levels - masters, phd
    *
    * @access public
    * @return string html
    */
    public function getDegreeLevels($select)
    {
        $lbMasters = $this->objLanguage->languageText('word_masters');
        $lbPhd = $this->objLanguage->languageText('word_phd');

        $objDrop = new dropdown('level');
        $objDrop->addOption($lbMasters, $lbMasters);
        $objDrop->addOption($lbPhd, $lbPhd);
        $objDrop->setSelected($select);

        return $objDrop->show();
    }

    /**
    * Method to create a dropdown list of years using the configurable variable for the start date
    *
    * @access public
    * @param string $name The element name
    * @param string $select The selected year
    * @return string html
    */
    public function getYearSelect($name, $select = '')
    {
        $start = $this->objConfig->getValue('ARCHIVE_START_YEAR', 'etd');

        $objDrop = new dropdown($name);

        for($i=$start; $i <= date('Y'); $i++){
            $objDrop->addOption($i, $i);
        }
        $objDrop->setSelected($select);
        return $objDrop->show();
    }
}
?>