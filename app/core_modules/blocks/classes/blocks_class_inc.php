<?php

/**
 * Blocks class
 *
 * Class to handle block generation for Chisimba
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   blocks
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @author    Derek Keats <derek@dkeats.com> Refactored for working with external blocks
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* ----------- data class extends dbTable for tbl_blog------------ */
// security check - must be included in all scripts
if (!/**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS ['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Blocks class
 *
 * Class to handle block generation in Chisimba
 *
 * @category  Chisimba
 * @package   blocks
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class blocks extends object {

    /**
     * Propoerty to hold the objUser object
     *
     * @var object $objUser
     */
    public $objUser;
    /**
     * Property to hold the language object
     *
     * @var object $objLanguage
     */
    public $objLanguage;
    /**
     * Property to hold the module object
     *
     * @var object $objModule
     */
    public $objModule;
    /**
     * Property to hold the config object
     *
     * @var object $objConfig
     */
    public $objConfig;

    /**
     * Constructor method
     */
    public function init() {
        // Create an instance of the modulesadmin class for checking
        // if a module is registered
        try {
            $this->objModule = $this->getObject('modules', 'modulecatalogue');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            //Check if contentblocks is installed
            $this->cbExists = $this->objModule->checkIfRegistered("contentblocks");
            if ($this->cbExists) {
                $this->objTxtBlockBase = $this->getObject("contentblockbase", "contentblocks");
            }
        } catch (customException $e) {
            echo customException::cleanUp($e);
            die ();
        }
    }

    /**
     *
     * This method returns a block formatted for display.
     *
     * Blocks must reside in the classes folder of the module indicated by $module.
     * Blocks must start with the name block_, and an instance where a
     * file name may contain an additional underscore. For example, a
     * Hello block in the helloworld module should be called
     *     block_hello_class_inc.php
     * Block classes must contain a title property and a show
     * method. Block classes may contain other methods or properties
     * as needed in order to create the title and show methods, but
     * generally should rather use methods of other classes to achieve
     * their results.
     *
     * @param string $block     The name of the block after the block_ and
     *                          before the _class in the filename. The class and name of the block
     *                          must be the same.
     *
     * @param string $module    The module to look in for the block
     *
     * @param string $blockType The type of block (e.g. tabbed box)
     */
    public function showBlock($block, $module, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '', $configData=NULL) {
        if ($this->loadBlock($block, $module)) {
            if ($this->checkLoginRequirement()) {
                if ($this->checkAdminRequirement()) {
                    if ($this->checkGroupRequirement()) {
                        $blockArr = array();
                        if (isset($block)) {
                            //split to check if text or wideblock
                            //$blockArr = split("[0-9]", $block);
                            if ($this->cbExists && $module == "contentblocks") {
                                if (is_array($this->block)) {
                                    return $this->fetchTextBlock($block, $module, $this->block, $blockType,
                                            $titleLength, $wrapStr, $showToggle, $hidden,
                                            $showTitle, $cssClass, $cssId, $configData);
                                } else if ($this->block == "class") {
                                    return $this->fetchBlock($block, $module, $blockType,
                                            $titleLength, $wrapStr, $showToggle, $hidden,
                                            $showTitle, $cssClass, $cssId, $configData);
                                } else {
                                    return NULL;
                                }
                            } else {
                                return $this->fetchBlock($block, $module, $blockType,
                                        $titleLength, $wrapStr, $showToggle, $hidden,
                                        $showTitle, $cssClass, $cssId, $configData);
                            }
                        }
                    } else {
                        return '<div class="featurebox"><div class="warning">'
                        . $this->objLanguage->languageText('mod_blocks_requiregroup',
                                'blocks', 'This block requires the user to be a member of a particular group for it to display.')
                        . '</div></div>';
                    }
                } else {
                    return '<div class="featurebox"><div class="warning">'
                    . $this->objLanguage->languageText('mod_blocks_requiresadmin',
                            'blocks', 'This block requires the user to have admin rights for it to display.')
                    . '</div></div>';
                }
            } else {
                return '<div class="featurebox"><div class="warning">'
                . $this->objLanguage->languageText('mod_blocks_loginrequired',
                        'blocks', 'This block requires the user to be logged in for it to display.')
                . '</div></div>';
            }
        } else {
            return NULL;
        }
    }

    /**
     *
     * Check if the block requires login. If the requiresLogin property
     * of the block is set, it will require login, but also it caters
     * for when requiresAdmin and requiresGroup are set but requiresLogin
     * is not set. In the latter cases, login would still be required. This
     * enables the first test to fail without needing to call the others.
     *
     * @return boolean TRUE|FALSE
     * @access private
     *
     */
    private function checkLoginRequirement() {
        if (isset($this->objBlock->requiresLogin) ||
                isset($this->objBlock->requiresAdmin) ||
                isset($this->objBlock->requiresGroup)) {
            if ($this->objBlock->requiresLogin == TRUE ||
                    $this->objBlock->requiresAdmin == TRUE ||
                    $this->objBlock->requiresGroup != FALSE) {
                if ($this->objUser->isLoggedin()) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    /**
     *
     * Check if the block requires the user to be an administrator
     *
     * @return boolean TRUE|FALSE
     * @access private
     *
     */
    private function checkAdminRequirement() {
        if (isset($this->objBlock->requiresAdmin)) {
            if ($this->objBlock->requiresAdmin == TRUE) {
                if ($this->objUser->isAdmin()) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    /**
     * Check if the block is only available to a certain group or groups of
     * users.
     *
     * @return boolean TRUE|FALSE
     * @access private
     * 
     */
    private function checkGroupRequirement() {
        if (isset($this->objBlock->requiresGroup)) {
            $objGroup = $this->getObject('groupadminmodel', 'groupadmin');
            if (is_array($this->objBlock->requiresGroup)) {
                // @todo write this code
                die("ARRAY OF GROUPS NOT READY");
            } else {
                // Check if the user is a member of the group
                if ($this->objUser->isLoggedin()) {
                    $userId = $this->objUser->userId();
                    $groupName = $this->objBlock->requiresGroup;
                    $groupId = $objGroup->getId($groupName);
                    if ($objGroup->isGroupMember($userId, $groupId)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            }
        } else {
            return TRUE;
        }
    }

    /**
     *
     *  Same as the showBlock method, but with extra security for showing external
     *  blocks
     *
     * @param string $block The block to render
     * @param string $module The module from which to retrieve the block
     * @param string $blockType The blocktype
     * @param string $titleLength The length to wrap the title
     * @param boolean TRUE|FALSE $wrapStr Whether or not to wrap the title
     * @param boolean TRUE|FALSE $showToggle Show the toggle button
     * @param string $hidden Whether or not the block is hidden first
     * @param boolean TRUE|FALSE  $showTitle Whether or not to show the title
     * @param string $cssClass The CSS class to wrap the block into
     * @param string $cssId The CSS ID for the block, if any
     * @return string The rendered block
     *
     */
    public function showBlockExternal($block, $module, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '', $configData=NULL) {

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $isAllowed = $objSysConfig->getValue('ALLOW_EXTERNAL_BLOCKS', 'blocks');
        if ($isAllowed == 1) {
            if ($this->loadBlock($block, $module)) {
                if (isset($this->objBlock->expose)) {
                    return $this->fetchBlock($block, $module, $blockType, $titleLength,
                            $wrapStr, $showToggle, $hidden, $showTitle, $cssClass, $cssId,
                            $configData);
                } else {
                    return '<div class="featurebox"><div class="error">'
                    . $this->objLanguage->languageText('mod_blocks_notexposed',
                            'blocks', 'The requested block is not exposed for external
                            display on remote systems.')
                    . '</div></div>';
                }
            } else {
                return '<div class="featurebox"><div class="error">'
                . $this->objLanguage->languageText('mod_blocks_notfoundremote',
                        'blocks', 'The requested block was not found in the
                        module from which it was requested on the remote site.')
                . '</div></div>';
            }
        } else {
            return '<div class="featurebox"><div class="error">'
            . $this->objLanguage->languageText('mod_blocks_externaldisabled',
                    'blocks', 'Provision of external blocks is disabled on
                 the server from which you are requesting the block.')
            . '</div></div>';
        }
    }

    /**
     *
     *  Load the requested block class & instantiate as $this->objBlock
     *
     * @param string $block The block to render
     * @param string $module The module from which to retrieve the block
     * @return string The rendered block
     *
     */
    private function loadBlock($block, $module) {
        if ($this->objModule->checkIfRegistered($module, $module)) {
            if (isset($block)) {
                //split to check if text or wideblock
                //$blockArr = split("[0-9]", $block);
                if ($module == "contentblocks" && $this->cbExists) {
                    // fetch contentblock data                    
                    $dataArr = $this->objTxtBlockBase->setDataArr($block);
                    if ($dataArr["title"] != $block) {
                        $this->block = $dataArr;
                        return TRUE;
                    } else {
                        $this->block = "class";
                        $blockfile = $this->objConfig->getModulePath() . $module . '/classes/block_' . $block . '_class_inc.php';
                        if ($this->blockExists($block, $module)) {
                            // Create an instance of the module's particular block
                            $this->objBlock = $this->getObject('block_' . $block, $module);
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    }
                } else {
                    $blockfile = $this->objConfig->getModulePath() . $module . '/classes/block_' . $block . '_class_inc.php';
                    if ($this->blockExists($block, $module)) {
                        // Create an instance of the module's particular block
                        $this->objBlock = $this->getObject('block_' . $block, $module);
                        return TRUE;
                    }
                }
            } else {
                return FALSE;
            }
        }
        return FALSE;
    }

    /**
     *
     *  Fetch the rendered block
     *
     * @param string $block The block to render
     * @param string $module The module from which to retrieve the block
     * @param string $blockType The blocktype
     * @param string $titleLength The length to wrap the title
     * @param boolean TRUE|FALSE $wrapStr Whether or not to wrap the title
     * @param boolean TRUE|FALSE $showToggle Show the toggle button
     * @param string $hidden Whether or not the block is hidden first
     * @param boolean TRUE|FALSE  $showTitle Whether or not to show the title
     * @param string $cssClass The CSS class to wrap the block into
     * @param string $cssId The CSS ID for the block, if any
     * @return string The rendered block
     */
    private function fetchBlock($block, $module, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '', $configData=NULL) {
        $this->objBlock->configData = $configData;

        if ($block == "content_text" || $block == "content_widetext") {
            return NULL;
        } else {
            // Get the title and wrap it
            $title = $this->objBlock->title;
            // You can override the parameters by setting object properties
            if (isset($this->objBlock->showTitle)) {
                $showTitle = $this->objBlock->showTitle;
            }
            if (isset($this->objBlock->titleLength)) {
                $titleLength = $this->objBlock->titleLength;
            }
            if (isset($this->objBlock->wrapStr)) {
                $wrapStr = $this->objBlock->wrapStr;
            }
            if (isset($this->objBlock->showToggle)) {
                $showToggle = $this->objBlock->showToggle;
            }
            if (isset($this->objBlock->hidden)) {
                $hidden = $this->objBlock->hidden;
            }
            if (isset($this->objBlock->cssClass)) {
                $cssClass = $this->objBlock->cssClass;
            }
            if (isset($this->objBlock->cssId)) {
                $cssId = $this->objBlock->cssId;
            }
            if (isset($this->objBlock->configData)) {
                $configData = $this->objBlock->configData;
            }
            if (isset($this->objBlock->blockType)) {
                $blockType = $this->objBlock->blockType;
            }
            if ($wrapStr) {
                $objWrap = $this->getObject('trimstr', 'strings');
                if (!$title == FALSE) {
                    $title = $objWrap->wrapString($title, $titleLength);
                }
            }
            switch ($blockType) {
                case NULL :
                    $objFeatureBox = $this->newObject('featurebox', 'navigation');
                    if (isset($this->objBlock->defaultHidden)) {
                        if ($this->objBlock->defaultHidden) {
                            $hidden = 'none';
                        }
                    }
                    if (!$showToggle && $hidden != 'default') {
                        $showToggle = TRUE;
                    }
                    if ($title == FALSE) {
                        $showTitle = FALSE;
                    }
                    return $objFeatureBox->show($title, $this->objBlock->show(),
                            $block, $hidden, $showToggle, $showTitle, $cssClass, $cssId);
                case "tabbedbox" :
                    // Put it all inside a tabbed box
                    $objTab = $this->newObject('tabbedbox', 'htmlelements');
                    $objTab->addTabLabel($title);
                    $objTab->addBoxContent($this->objBlock->show());
                    return "<br />" . $objTab->show();
                    break;
                case "table" :
                    // Put it all inside a table
                    $myTable = $this->newObject('htmltable', 'htmlelements');
                    $myTable->border = '1';
                    $myTable->cellspacing = '0';
                    $myTable->cellpadding = '5';
                    $myTable->startHeaderRow();
                    $myTable->addHeaderCell($title);
                    $myTable->endHeaderRow();
                    $myTable->startRow();
                    $myTable->addCell($this->objBlock->show());
                    $myTable->endRow();
                    return $myTable->show();
                case "wrapper" :
                    // Put it all inside wrappers
                    $this->Layer1 = $this->newObject('layer', 'htmlelements');
                    $this->Layer1->cssClass = "wrapperDarkBkg";
                    $this->Layer2 = $this->newObject('layer', 'htmlelements');
                    $this->Layer2->cssClass = "wrapperLightBkg";
                    $this->Layer1->addToStr($title);
                    $this->Layer2->addToStr($this->objBlock->show());
                    $this->Layer1->addToStr($this->Layer2->show());
                    return $this->Layer1->show();
                case "none" :
                    // Just display it - for wide blocks
                    return $this->objBlock->show();
                case "invisible" :
                    // Render boxes like login invisible when logged in
                    return NULL;
            }
        }
    }

    /**
     *
     *  Fetch the rendered text block
     *
     * @param string $block The block to render
     * @param string $module The module from which to retrieve the block
     * @param string $blockDataArr Contains block data
     * @param string $blockType The blocktype
     * @param string $titleLength The length to wrap the title
     * @param boolean TRUE|FALSE $wrapStr Whether or not to wrap the title
     * @param boolean TRUE|FALSE $showToggle Show the toggle button
     * @param string $hidden Whether or not the block is hidden first
     * @param boolean TRUE|FALSE  $showTitle Whether or not to show the title
     * @param string $cssClass The CSS class to wrap the block into
     * @param string $cssId The CSS ID for the block, if any
     * @return string The rendered block
     */
    public function fetchTextBlock($block, $module, $blockDataArr, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '', $configData=NULL) {
        $this->objBlock->configData = $configData;
        //echo "<br />btype: ".$blockType." module: ".$module;
        $blockArr = array();
        //split to check if text or wideblock
        if (isset($block)) {
            if ($module == "contentblocks") {
                // Get the title and wrap it
                $title = $blockDataArr["title"];
                // You can override the parameters by setting object properties
                if (isset($blockDataArr["title"])) {
                    $showTitle = $blockDataArr["title"];
                }
                if (isset($blockDataArr["blockContents"])) {
                    $blockContents = $blockDataArr["blockContents"];
                    $blockContent = $blockContents;
                } else {
                    $blockContents = "";
                }

                if (isset($blockDataArr["cssClass"])) {
                    $cssClass = $blockDataArr["cssClass"];
                }
                if (isset($blockDataArr["cssId"])) {
                    $cssId = $blockDataArr["cssId"];
                }
                if (isset($this->objBlock->configData)) {
                    $configData = $this->objBlock->configData;
                }
                if (isset($blockDataArr["blockType"])) {
                    //$blockType = $block["blockType"];
                }
                if ($wrapStr) {
                    $objWrap = $this->getObject('trimstr', 'strings');
                    if (!$title == FALSE) {
                        $title = $objWrap->wrapString($title, $titleLength);
                    }
                }

                switch ($blockType) {
                    case NULL :
                        $objFeatureBox = $this->newObject('featurebox', 'navigation');
                        if (isset($this->objBlock->defaultHidden)) {
                            if ($this->objBlock->defaultHidden) {
                                $hidden = 'none';
                            }
                        }
                        if (!$showToggle && $hidden != 'default') {
                            $showToggle = TRUE;
                        }
                        if ($title == FALSE) {
                            $showTitle = FALSE;
                        }

                        return $objFeatureBox->show($title, $blockContents,
                                $block, $hidden, $showToggle, $showTitle, $cssClass, $cssId);
                    case "tabbedbox" :
                        // Put it all inside a tabbed box
                        $objTab = $this->newObject('tabbedbox', 'htmlelements');
                        $objTab->addTabLabel($title);
                        $objTab->addBoxContent($this->objBlock->show());
                        return "<br />" . $objTab->show();
                        break;
                    case "table" :
                        // Put it all inside a table
                        $myTable = $this->newObject('htmltable', 'htmlelements');
                        $myTable->border = '1';
                        $myTable->cellspacing = '0';
                        $myTable->cellpadding = '5';
                        $myTable->startHeaderRow();
                        $myTable->addHeaderCell($title);
                        $myTable->endHeaderRow();
                        $myTable->startRow();
                        $myTable->addCell($blockContents);
                        $myTable->endRow();
                        return $myTable->show();
                    case "wrapper" :
                        // Put it all inside wrappers
                        $this->Layer1 = $this->newObject('layer', 'htmlelements');
                        $this->Layer1->cssClass = "wrapperDarkBkg";
                        $this->Layer2 = $this->newObject('layer', 'htmlelements');
                        $this->Layer2->cssClass = "wrapperLightBkg";
                        $this->Layer1->addToStr($title);
                        $this->Layer2->addToStr($blockContents);
                        $this->Layer1->addToStr($this->Layer2->show());
                        return $this->Layer1->show();
                    case "none" :
                        // Just display it - for wide blocks
                        return $blockContents;
                    case "invisible" :
                        // Render boxes like login invisible when logged in
                        return NULL;
                }
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

    /**
     * Method to check that a block exists
     *
     * @param string $block The block to render
     * @param string $module The module from which to retrieve the block
     * @access public
     * @return boolean TRUE|FALSE
     */
    public function blockExists(&$block, &$module) {
        if ($this->isCoreBlock($block, $module) || $this->isModuleBlock($block, $module)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to check if a block is a core block
     *
     * @access public
     * @return boolean TRUE|FALSE
     *
     */
    public function isCoreBlock(&$block, &$module) {
        $blockfile = $this->objConfig->getsiteRootPath() . "core_modules/" . $module . '/classes/block_' . $block . '_class_inc.php';
        if (file_exists($blockfile)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to check that a particular block is a module block
     *
     * @access public
     * @return boolean TRUE|FALSE
     */
    public function isModuleBlock(&$block, &$module) {
        $blockfile = $this->objConfig->getModulePath() . $module . '/classes/block_' . $block . '_class_inc.php';
        if (file_exists($blockfile)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>