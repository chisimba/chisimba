<?php
/**
 *
 * Build a canvas from dyanmic blocks for user blocks
 *
 * This class provides the ajax that a dynamic canvas controller needs where
 * the block is indexed to a particular user.  See also moduleblockajax and
 * pageblockajax for module and page blocks.
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
 * This class provides the ajax that a dynamic canvas controller needs. It is
 * provided here so as not to have every module write its own.
*
* @author Derek Keats
* @package canvas
*
*/
class moduleblockajax extends object
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
       $this->objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
       $this->objDbDynamicBlocks =  $this->getObject('dbmodblocks', 'canvas');
       // Guess the user whose profile we are on.
       $objGuessUser = $this->getObject('bestguess', 'utilities');
       $this->userId = $objGuessUser->guessUserId();

    }

    /**
    *
    * Method to render a block for use by ajax
    *
    * @return String The rendered block for use by ajax
    * @access public
    *
    */
    public function renderblock()
    {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');
        $block = explode('|', $blockId);
        $this->setVar('pageSuppressSkin', TRUE);
        $this->setVar('pageSuppressContainer', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
        $this->setVar('suppressFooter', TRUE);
        $blockId = $side.'___'.str_replace('|', '___', $blockId);
        if ($block[0] == 'block') {
            $objBlocks = $this->getObject('blocks', 'blocks');
            $block = '<div id="'.$blockId.'" class="block highlightblock">'.$objBlocks->showBlock($block[1], $block[2], NULL, 20, TRUE, TRUE).'</div>';
            echo $block;
        } if ($block[0] == 'dynamicblock') {
            $block = '<div id="'.$blockId.'" class="block highlightblock">'.$this->objDynamicBlocks->showBlock($block[1]).'</div>';
            echo $block;
        } else {
            echo '';
        }
        die();
    }

    /**
     * Method to add a block
     *
     * @return String The ID of the added block or NULL on failure
     * @access public
     *
     */
    public function addblock()
    {
        $blockId = $this->getParam('blockid');
        $side = $this->getParam('side');
        $block = explode('|', $blockId);
        if ($block[0] == 'block' || $block[0] == 'dynamicblock') {
            // Add Block
            $result = $this->objDbDynamicBlocks->addBlock($blockId, $side, $block[2]);

            if ($result == FALSE) {
                echo '';
            } else {
                echo $result;
            }
        } else {
            echo '';
        }
        die();
    }

    /**
     * Method to remove a context block
     *
     * @return String OK or NOTOK for use by ajax
     * @access public
     *
     */
    public function removeblock()
    {
        $blockId = $this->getParam('blockid');

        $result = $this->objDbDynamicBlocks->removeBlock($blockId);

        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }

    /**
     *
     * Method to move a context block
     *
     * @return String OK or NOTOK for use by ajax
     * @access public
     *
     */
    public function moveblock()
    {
        $blockId = $this->getParam('blockid');
        $direction = $this->getParam('direction');

        if ($direction == 'up') {
            $result = $this->objDbDynamicBlocks->moveBlockUp($blockId);
        } else {
            $result = $this->objDbDynamicBlocks->moveBlockDown($blockId);
        }

        if ($result) {
            echo 'ok';
        } else {
            echo 'notok';
        }
    }

}
?>