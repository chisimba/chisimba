<?php
/**
 *
 * Build a canvas from dyanmic blocks
 *
 * This class builds a dynamic canvas, which allows the user interface to
 * be constructed using the 'Turn editing on' approach.
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
 * @package   myprofile
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbmyprofile.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
 * Build a canvas from dyanmic blocks
 *
 * This class builds a dynamic canvas, which allows the user interface to
 * be constructed using the 'Turn editing on' approach.
*
* @author Derek Keats
* @package myprofile
*
*/
class buildcanvas extends object
{

    /**
     *
     * @var string Object Holds the small blocks dropdown so it doesn't have
     * to be generated twice
     * @access private
     * 
     */
    
    private $smallBlocksDropDown=NULL;

    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;

    /**
     *
     * @var string Object $objUser String for the user object
     * @access public
     *
     */
    public $objUser;

    /**
     *
     * @var string $userId The user id of the profile owner
     * @access private
     *
     */
    private $userId;

    /**
     *
     * @var string $upIcon The icon for moving a block up
     * @access private
     *
     */
    private $upIcon;

    /**
     *
     * @var string $downIcon The icon for moving a block down
     * @access private
     *
     */
    private $downIcon;

    /**
     *
     * @var string $deleteIcon The icon for deleting a block
     * @access private
     *
     */
    private $deleteIcon;

    /**
     *
     * @var boolean $isOwner Whether the viewing user is the owner of the profile
     * @access private
     *
     */
    private $isOwner;

    /**
    *
    * Intialiser for the canvas builder
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject('user', 'security');
        // We are going to create a dynamic block based interface.
        $this->objContextBlocks = $this->getObject('dbcontextblocks', 'context');
        $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
        $this->objPersonalSpaceBlocks = $this->getObject('dbdynamiccanvas', 'canvas');
        // Load the livequery that works with blocks.
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('jquery.livequery.js', 'jquery'));
        // Guess the user whose profile we are on.
        $objGuessUser = $this->getObject('bestguess', 'utilities');
        $this->userId = $objGuessUser->guessUserId();
        // Set a property to indicate if we are the profile owner or not
        if ($this->userId !== $this->objUser->userId()) {
            $this->isOwner = FALSE;
        } else {
            $this->isOwner = TRUE;
        }
        // Load the various blocks and get the data we need
        $this->objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        // Get any wideblocks the user has.
        $this->wideDynamicBlocks = $this->objDynamicBlocks->getWideUserBlocks($this->userId);
        // Get any user small blocks that the user has.
        $this->smallDynamicBlocks = $this->objDynamicBlocks->getSmallUserBlocks($this->userId);
        // Get the userblocks
        $this->middleBlocks = $this->objPersonalSpaceBlocks->getUserBlocks($this->userId, 'middle');
        $this->rightBlocks = $this->objPersonalSpaceBlocks->getUserBlocks($this->userId, 'right');
        $this->leftBlocks = $this->objPersonalSpaceBlocks->getUserBlocks($this->userId, 'left');
        $this->smallDynamicBlocks = $this->objDynamicBlocks->getSmallUserBlocks($this->userId);
        // Load other required HTML elements.
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        // Generate Icons used by JavaScript.
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('up');
        $this->upIcon = $objIcon->show();
        $objIcon->setIcon('down');
        $this->downIcon = $objIcon->show();
        $objIcon->setIcon('delete');
        $this->deleteIcon = $objIcon->show();
        unset ($objIcon);
    }

    /**
     *
     * Render the canvas. This is what renders the whole interface
     * for an editable dynamic canvas view.
     *
     * @return string The rendered content
     * @access public
     *
     */
    public function show()
    {
        // Initialise the return string with two blank lines.
        $ret = "\n\n";
        // Add the script values to the return string.
        $ret .= $this->getScriptValues();
        $ret .= "\n\n" . $this->getContextBlocksJs() . "\n\n";
        // Set a 3-column layout.
        $objCssLayout = $this->getObject('csslayout', 'htmlelements');
        $objCssLayout->setNumColumns(3);
        // Get the left and right blocks
        $rightBlocks = $this->getSmallBlocks('right');
        $leftBlocks = $this->getSmallBlocks('left');

        // Make the content of the left column.
        if ($this->objUser->isLoggedIn()) {
            $userMenu  = $this->newObject('usermenu','toolbar');
            $leftContent = $userMenu->show();
        } else {
            $leftContent = "";
        }
        $leftContent .= '<div id="leftblocks">'. $this->leftBlocks . '</div>';
        $leftContent .= '<div id="leftaddblock">' . $this->getHeader() .$leftBlocks;
        $leftContent .= '<div id="lefttpreview"><div id="leftpreviewcontent"></div> '
          .$this->getLeftButton() .' </div></div>';
        $objCssLayout->setLeftColumnContent($leftContent);
        unset ($leftContent);

        // Make the content of the right column.
        $rightContent = '<div id="editmode">' . $this->getEditOnButton() . '</div>';
        $rightContent  .= '<div id="rightblocks">' . $this->rightBlocks .'</div>';
        $rightContent .= '<div id="rightaddblock">' . $this->getHeader() . $rightBlocks;
        $rightContent .= '<div id="rightpreview"><div id="rightpreviewcontent"></div> '
          . $this->getRightButton() . ' </div></div>';
        $objCssLayout->setRightColumnContent($rightContent);
        unset ($rightContent);


        // Make the content of the middle column.
        $middleContent = '<div id="middleblocks">'. $this->middleBlocks .'</div>';
        $middleContent .= '<div id="middleaddblock">' . $this->getHeader() . $this->getWideBlocks();
        $middleContent .= '<div id="middlepreview"><div id="middlepreviewcontent"></div> '. $this->getMiddleButton() .' </div>';
        $middleContent .= '</div>';
        $objCssLayout->setMiddleColumnContent($middleContent);
        return $this->getScriptValues() . $this->getContextBlocksJs() . $objCssLayout->show();
    }

    /**
     *
     * Get the header that appears above each add block dropdown
     * when editing is turned on.
     *
     * @return string The header HTML text
     * @access private
     *
     */
    private function getHeader()
    {
        $header = new htmlheading();
        $header->type = 3;
        $header->str = $this->objLanguage->languageText('mod_context_addablock', 'context', 'Add a Block');
        return $header->show();
    }

    /**
     *
     * Gets the small (narrow) blocks for either the left or the right side. It
     * renders the dropdown for selecting blocks when 'Turn editing on' is
     * enabled.
     *
     * @param string $position Either left or right
     * @return string A dropdown for selecting blocks
     * @access public
     *
     */
    public function getSmallBlocks($position='right')
    {
        // Note that it uses a class property to avoid having to repeat it twice.
        switch ($position) {
            case 'right':
                if ($this->smallBlocksDropDown== NULL) {
                    $this->smallBlocksDropDown = new dropdown ('rightblocks');
                } else {
                    $this->smallBlocksDropDown->name = 'rightblocks';
                }
                $this->smallBlocksDropDown->cssId = 'ddrightblocks';
                break;
            case 'left':
                if ($this->smallBlocksDropDown== NULL) {
                    $this->smallBlocksDropDown = new dropdown ('leftblocks');
                } else {
                    $this->smallBlocksDropDown->name = 'leftblocks';
                }
                $this->smallBlocksDropDown->cssId = 'ddleftblocks';
                break;
        }

        // Get the right or left blocks dropdown
        $this->smallBlocksDropDown->addOption(
          '', $this->objLanguage->languageText(
          'phrase_selectone', 'context', 'Select One')
          .'...');
        // Create array for sorting
        $smallBlockOptions = array();
        // Add Small Dynamic Blocks
        foreach ($this->smallDynamicBlocks as $smallBlock) {
            $smallBlockOptions['dynamicblock|' . $smallBlock['id'] . '|'
              . $smallBlock['module']] = htmlentities($smallBlock['title']);
        }
       
        // Add Small Blocks.
        $objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
        $smallBlocks = $objBlocks->getBlocks('normal', 'site|user');
        foreach ($smallBlocks as $smallBlock) {
            $block = $this->newObject('block_' 
              . $smallBlock['blockname'], $smallBlock['moduleid']);
            $title = $block->title;
            if ($title == '') {
                $title = $smallBlock['blockname'] .'|' . $smallBlock['moduleid'];
            }
            $smallBlockOptions['block|' . $smallBlock['blockname'] . '|'
              . $smallBlock['moduleid']] = htmlentities($title);
            // Sort Alphabetically
            asort($smallBlockOptions);
            // Add Small Blocks
            foreach ($smallBlockOptions as $block=>$title) {
                $this->smallBlocksDropDown->addOption($block, $title);
            }
        }
        return $this->smallBlocksDropDown->show();
    }

    /**
     *
     * Gets the wide blocks for the template middle. It renders the dropdown
     * for selecting wide blocks when 'Turn editing on' is enabled.
     *
     * @return string A dropdown for selecting blocks
     * @access public
     *
     */
    public function getWideBlocks()
    {
        // Create array for sorting
        $wideBlockOptions = array();

        $wideBlocksDropDown = new dropdown ('middleblocks');
        $wideBlocksDropDown->cssId = 'ddmiddleblocks';
        $wideBlocksDropDown->addOption('', $this->objLanguage->languageText('phrase_selectone', 'context', 'Select One').'...');

        foreach ($this->wideDynamicBlocks as $wideBlock) {
            $smallBlockOptions['dynamicblock|'.$wideBlock['id'].'|'.$wideBlock['module']] = htmlentities($wideBlock['title']);
        }
        $wideBlocks = $this->objBlocks->getBlocks('wide', 'site|user');
        foreach ($wideBlocks as $wideBlock) {
            $block = $this->newObject('block_'
              . $wideBlock['blockname'], $wideBlock['moduleid']);
            $title = $block->title;

            if ($title == '') {
                $title = $wideBlock['blockname'].'|'.$wideBlock['moduleid'];
            }

            $wideBlockOptions['block|'.$wideBlock['blockname'].'|'.$wideBlock['moduleid']] = htmlentities($title);
        }
        // Sort Alphabetically
        asort($wideBlockOptions);

        // Add wide Blocks
        foreach ($wideBlockOptions as $block=>$title){
            $wideBlocksDropDown->addOption($block, $title);
        }
        return $wideBlocksDropDown->show();
    }

    /**
     *
     * Render the left 'add block' button
     *
     * @return string The rendered button
     * @access private
     *
     */
    private function getLeftButton()
    {
        $button = new button ('addleftblock', $this->objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
        $button->cssId = 'leftbutton';
        return $button->show();
    }

     /**
     *
     * Render the right 'add block' button
     *
     * @return string The rendered button
     * @access private
     *
     */
    private function getRightButton()
    {
        $button = new button ('addrightblock', $this->objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
        $button->cssId = 'rightbutton';
        return $button->show();
    }

    /**
     *
     * Render the middle 'add block' button
     *
     * @return string The rendered button
     * @access private
     *
     */
    private function getMiddleButton()
    {
        $button = new button ('addmiddleblock', $this->objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'));
        $button->cssId = 'middlebutton';
        return $button->show();
    }

    /**
     *
     * Render the 'Turn editing on...' button
     *
     * @return string The rendered button
     * @access private
     *
     */
    private function getEditOnButton()
    {
        if ($this->isOwner) {
            $editOnButton = new button (
              'editonbutton', $this->objLanguage->languageText(
                'mod_context_turneditingon', 'context', 'Turn Editing On'
               )
            );
            $editOnButton->cssId = 'editmodeswitchbutton';
            $editOnButton->setOnClick("switchEditMode();");
            return '<div id="editmode">'.$editOnButton->show().'</div>';
        } else {
            return NULL;
        }

    }

    /**
     *
     * Render the script values for use by the external ajax script.
     *
     * @return string The rendered script for inclusion in the page.
     * @access private
     * 
     */
    private function getScriptValues()
    {
        // Guess the module we are in
        $objGuess = $this->getObject('bestguess', 'utilities');
        $curMod = $objGuess->identifyModule();
        $ret = '
<script type="text/javascript">
// <![CDATA[
    upIcon = \'' . $this->upIcon . '\';
    downIcon = \'' . $this->downIcon . '\';
    deleteIcon = \'' . $this->deleteIcon . '\';
    deleteConfirm = \'' . $this->objLanguage->languageText('mod_context_confirmremoveblock', 'context', 'Are you sure you want to remove the block') . '\';
    unableMoveBlock = \'' . $this->objLanguage->languageText('mod_context_unablemoveblock', 'context', 'Error - Unable to move block') . '\';
    unableDeleteBlock = \'' . $this->objLanguage->languageText('mod_context_unabledeleteblock', 'context', 'Error - Unable to delete block') . '\';
    unableAddBlock = \'' . $this->objLanguage->languageText('mod_context_unableaddblock', 'context', 'Error - Unable to add block') . '\';
    turnEditingOn = \'' . $this->objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On') . '\';
    turnEditingOff = \'' . $this->objLanguage->languageText('mod_context_turneditingoff', 'context', 'Turn Editing Off') . '\';
    theModule = \'' . $curMod . '\';
// ]]>
</script>
        ';
        return $ret;
    }

    /**
     *
     * Render the javascript file that does the block handling.
     *
     * @return string The rendered javascript file for inclusion.
     * @access private
     *
     */
    private function getContextBlocksJs()
    {
        return $this->getJavaScriptFile('contextblocks.js', 'context');
    }

}
?>