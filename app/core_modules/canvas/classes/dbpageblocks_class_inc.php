<?php
/**
 *
 * Dynamic canvas data access for modules with module-level blocks
 *
 * Database accesss class for Chisimba for accessing dynamic blocks in
 * a dynamic canvas where the module has the same blocks across all pages,
 * unless some actions are specified to use JSON or old fashioned
 * Chisimba templates.
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
* Dynamic canvas data access for modules with module-level blocks
*
* Database accesss class for Chisimba for accessing dynamic blocks in
* a dynamic canvas where the module has the same blocks across all pages,
* unless some actions are specified to use JSON or old fashioned
* Chisimba templates.
*
* @author Derek Keats
* @package canvas
*
*/
class dbpageblocks extends dbtable
{
   /**
    *
    * @var string object $objUser A string to hold the user Object
    * @access public
    *
   */
   public $objUser;

    /**
    *
    * Intialiser for the module blocks model
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        // Guess the module we are in so we can load its data connection
        $objGuess = $this->getObject('bestguess', 'utilities');
        $curMod = $objGuess->identifyModule();
        $blockTable = "tbl_" . $curMod . "_pageblocks";
        parent::init($blockTable);
        $this->objUser = $this->getObject('user','security');
    }


    /**
     * Method to get a list of blocks used in a module that uses module-level
     * block, and have them rendered one time to the interface.
     *
     * @param string $side Side on which the blocks are on
     * @return array
     * @access public
     * 
     */
    public function getPageBlocks($pageId, $side)
    {
        $results = $this->getPageBlocksList($pageId, $side);
        if (count($results) == 0) {
           return '';
        } else {
            $str = '';
            $objBlocks = $this->getObject('blocks', 'blocks');
            $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
            foreach ($results as $result) {
                $block = explode('|', $result['block']);
                $blockId = $side.'___'.str_replace('|', '___', $result['block']);
                // At the moment, only blocks are catered for, not yet dynamic blocks
                if ($block[0] == 'block') {
                    $blockStr = $objBlocks->showBlock($block[1], $block[2], NULL, 20, TRUE, TRUE);
                    $str .= '<div id="'.$result['id'].'" class="block">'.$blockStr.'</div>';
                } else if ($block[0] == 'dynamicblock') {
                    $block = explode('|', $result['block']);
                    $blockStr = $objDynamicBlocks->showBlock($block[1]);
                    $str .= '<div id="'.$result['id'].'" class="block">'.$blockStr.'</div>';
                }
           }
           return $str;
        }
    }

    /**
     *
     * Method to get a list of blocks used on a particular side
     *
     * @param string $contextCode Context Code
     * @param string $side Side on which the blocks are on
     * @return array
     * @access public
     *
     */
    public function getPageBlocksList($pageId, $side)
    {
        return $this->getAll(' WHERE side=\'' 
          . $side . '\' AND pageid=\''
          . $pageId . '\' ORDER BY position');
    }


    /**
     *
     * Method to get a list of all blocks used
     *
     * @param string $contextCode
     * @return array List of Blocks
     * @access public
     * 
     */
    public function getModuleBlocksArray()
    {
        $results = $this->getAll();

        $array = array();

        if (count($results) > 0) {
            foreach ($results as $result)
            {
                $array[] = $result['block'];
            }
        }

        return $array;
    }


    /**
     * Method to add a block to a module
     *
     * @param string $block Block Id
     * @param string $side Side Block is On
     * @param string $module Module Block is from
     * @return boolean Result of insert
     * @access public
     *
     */
    public function addBlock($block, $side, $module)
    {
        $pageId = $this->getParam('pageId', NULL);
        $userId = $this->objUser->userId();
        return $this->insert(array(
                'userid' => $userId,
                'block' => $block,
                'side' => $side,
                'module' => $module,
                'pageid' => $pageId,
                'position' => $this->getLastOrder($pageId, $side)+1,
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    /**
     * Method to get the last order of a block on a side
     * This is used for ordering purposes
     *
     * @param string $side Side block will be added
     * @return int The last order position of the block
     * @access private
     * 
     */
    private function getLastOrder($pageId, $side)
    {
        $results = $this->getAll(' WHERE side=\''.$side 
          . '\' AND pageid=\'' .$pageId
          . '\' ORDER BY position DESC LIMIT 1');
        if (count($results) == 0) {
            return 0;
        } else {
            return $results[0]['position'];
        }
    }


    /**
     * Method to remove a block
     * 
     * @param string $id Block Id
     * @return boolean TRUE|FALSE
     * @access public
     *
     */
    public function removeBlock($id)
    {
        return $this->delete('id', $id);
    }

    /**
     * 
     * Method to move a block up
     *
     * @param string $id Block Id
     * @access public
     * @return boolean TRUE|FALSE
     * 
     */
    public function moveBlockUp($id)
    {
        $record = $this->getRow('id', $id);
        if ($record == FALSE) {
            return FALSE;
        } else {
            $prevRecord = $this->getPreviousBlock($record['side'], $record['position']);
            if ($prevRecord == FALSE) {
                return FALSE;
            } else {
                $this->update('id', $record['id'], array('position'=>$prevRecord['position']));
                $this->update('id', $prevRecord['id'], array('position'=>$record['position']));
                return TRUE;
            }
        }
    }

    /**
     *
     * Method to move a block down
     *
     * @param string $id Block Id
     * @access public
     * @return boolean TRUE|FALSE
     *
     */
    public function moveBlockDown($id)
    {
        $record = $this->getRow('id', $id);
        if ($record == FALSE) {
            return FALSE;
        } else {
            $nextRecord = $this->getNextBlock($record['side'], $record['position']);
            if ($nextRecord == FALSE) {
                return FALSE;
            } else {
                $this->update('id', $record['id'], array('position'=>$nextRecord['position']));
                $this->update('id', $nextRecord['id'], array('position'=>$record['position']));
                return TRUE;
            }
        }
    }

    /**
     *
     * Method to get the details of the previous block
     *
     * @param string $side Side Block is on
     * @param int $position Position of the Block
     * @return boolean or int array An array with the position
     * @access private
     * 
     */
    private function getPreviousBlock($side, $position)
    {
        $pageId = $this->getParam('pageId', NULL);
        $results = $this->getAll(' WHERE side=\'' . $side . '\' '
                . 'AND pageid=\'' . $pageId . '\' '
                . 'AND position < ' . $position
                .' ORDER BY position DESC LIMIT 1');
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }

    /**
     *
     * Method to get the details of the next block
     *
     * @param string $side Side Block is on
     * @param int $position Position of the Block
     * @return boolean or int array An array with the position
     * @access private
     * 
     */
    private function getNextBlock($side, $position)
    {
        $pageId = $this->getParam('pageId', NULL);
        //$results = $this->getAll(' WHERE side=\''.$side.'\' AND position > '.$position.' ORDER BY position LIMIT 1');
        $sql = ' WHERE side=\'' . $side . '\' '
        . 'AND pageid=\'' . $pageId . '\' '
        . 'AND position > ' . $position
        .' ORDER BY position DESC LIMIT 1';
        $results = $this->getAll($sql);
        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
}
?>