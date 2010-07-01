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
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
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
     * Constructor method
     */
    public function init() {
        // Create an instance of the modulesadmin class for checking
        // if a module is registered
        try {
            $this->objModule = $this->getObject ( 'modules', 'modulecatalogue' );
            $this->objConfig = $this->getObject ( 'altconfig', 'config' );
            $this->objLanguage = $this->getObject('language', 'language');
        } catch ( customException $e ) {
            echo customException::cleanUp ( $e );
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
    public function showBlock($block, $module, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '')
    {
        if ($this->loadBlock($block, $module)) {
            return $this->fetchBlock($block, $module, $blockType, $titleLength,
              $wrapStr, $showToggle, $hidden, $showTitle, $cssClass, $cssId);
        } else {
            return NULL;
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
    */
    public function showBlockExternal($block, $module, $blockType = NULL,
      $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE,
      $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox',
      $cssId = '')
    {
        
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $isAllowed = $objSysConfig->getValue('ALLOW_EXTERNAL_BLOCKS', 'blocks');
        if ($isAllowed == 1) {
            if ($this->loadBlock($block, $module)) {
                if (isset($this->objBlock->expose)) {
                    return $this->fetchBlock($block, $module, $blockType, $titleLength,
                      $wrapStr, $showToggle, $hidden, $showTitle, $cssClass, $cssId);
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
    private function loadBlock($block, $module)
    {
        if ($this->objModule->checkIfRegistered ( $module, $module )) {
            $blockfile = $this->objConfig->getModulePath () . $module . '/classes/block_' . $block . '_class_inc.php';
            if ($this->blockExists ( $block, $module )) {
                // Create an instance of the module's particular block
                $this->objBlock = $this->getObject ( 'block_' . $block, $module );
                return TRUE;
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
    private function fetchBlock($block, $module, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '')
    {
            // Get the title and wrap it
            $title = $this->objBlock->title;
            if ($wrapStr) {
                $objWrap = $this->getObject ( 'trimstr', 'strings' );
                if (!$title == FALSE) {
                    $title = $objWrap->wrapString ( $title, $titleLength );
                }
            }

            if (isset ( $this->objBlock->blockType )) {
                $blockType = $this->objBlock->blockType;
            }
            switch ($blockType) {
                case NULL :
                    $objFeatureBox = $this->newObject ( 'featurebox', 'navigation' );
                    if (isset ( $this->objBlock->defaultHidden ) && $objBlock->defaultHidden) {
                        $hidden = 'none';
                    }
                    if (! $showToggle && $hidden != 'default') {
                        $showToggle = TRUE;
                    }
                    if ($title == FALSE) {
                        $showTitle = FALSE;
                    }
                    return $objFeatureBox->show ($title, $this->objBlock->show (), $block, $hidden, $showToggle, $showTitle, $cssClass, $cssId);
                case "tabbedbox" :
                    // Put it all inside a tabbed box
                    // $this->loadClass('tabbedbox', 'htmlelements');
                    $objTab = $this->newObject ( 'tabbedbox', 'htmlelements' );
                    $objTab->addTabLabel ( $title );
                    $objTab->addBoxContent ( $this->objBlock->show () );
                    return "<br />" . $objTab->show ();
                    break;
                case "table" :
                    // Put it all inside a table
                    $myTable = $this->newObject ( 'htmltable', 'htmlelements' );
                    $myTable->border = '1';
                    $myTable->cellspacing = '0';
                    $myTable->cellpadding = '5';
                    $myTable->startHeaderRow ();
                    $myTable->addHeaderCell ( $title );
                    $myTable->endHeaderRow ();
                    $myTable->startRow ();
                    $myTable->addCell ( $this->objBlock->show () );
                    $myTable->endRow ();
                    return $myTable->show ();
                case "wrapper" :
                    // Put it all inside wrappers
                    $this->Layer1 = $this->newObject ( 'layer', 'htmlelements' );
                    $this->Layer1->cssClass = "wrapperDarkBkg";
                    $this->Layer2 = $this->newObject ( 'layer', 'htmlelements' );
                    $this->Layer2->cssClass = "wrapperLightBkg";
                    $this->Layer1->addToStr ( $title );
                    $this->Layer2->addToStr ( $this->objBlock->show () );
                    $this->Layer1->addToStr ( $this->Layer2->show () );
                    return $this->Layer1->show ();
                case "none" :
                    // Just display it - for wide blocks
                    return $this->objBlock->show ();
                case "invisible" :
                    // Render boxes like login invisible when logged in
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
        if ($this->isCoreBlock ( $block, $module ) || $this->isModuleBlock ( $block, $module )) {
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
        $blockfile = $this->objConfig->getsiteRootPath () . "core_modules/" . $module . '/classes/block_' . $block . '_class_inc.php';
        if (file_exists ( $blockfile )) {
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
        $blockfile = $this->objConfig->getModulePath () . $module . '/classes/block_' . $block . '_class_inc.php';
        if (file_exists ( $blockfile )) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>