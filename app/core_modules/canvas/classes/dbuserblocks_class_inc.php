<?php
/**
 *
 * Dynamic canvas data access for user-level blocks
 *
 * Database accesss class for Chisimba for accessing dynamic blocks in
 * a dynamic canvas in which the person who owns the blocks is
 * identified and used as the selector. Blocks are retrieved from
 * tbl_MODULE_personalblocks.
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
 * Dynamic canvas data access for user-level blocks
 *
 * Database accesss class for Chisimba for accessing dynamic blocks in
 * a dynamic canvas in which the person who owns the blocks is
 * identified and used as the selector. Blocks are retrieved from
 * tbl_MODULE_personalblocks.
*
* @author Derek Keats
* @package canvas
*
*/
class dbuserblocks extends dbtable
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
    * Intialiser for the userblock model
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
        $blockTable = "tbl_" . $curMod . "_userblocks";
        parent::init($blockTable);
        $this->objUser = $this->getObject('user','security');
    }


    /**
     *
     * Method to get a list of blocks used, and have them
     * rendered one time
     *
     * @param string $userId Userid of the owning user
     * @param string $side Side on which the blocks are on
     * @return array
     * @access public
     * 
     */
    public function getUserBlocks($userId, $side)
    {
        $results = $this->getUserBlocksList($userId, $side);

        if (count($results) == 0) {
           return '';
        } else {

            $str = '';

            $objBlocks = $this->getObject('blocks', 'blocks');
            $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

            foreach ($results as $result)
            {
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
     * Method to get a list of blocks for a particular side they are on
     *
     * @param string $userId The user id of the owning user
     * @param string $side Side on which the blocks are on
     * @return array
     * @access public
     *
     */
    public function getUserBlocksList($userId, $side)
    {
        return $this->getAll(' WHERE side=\''.$side.'\' AND userid=\''.$userId.'\' ORDER BY position');
    }

    /**
     *
     * Method to get a list of blocks used by a user
     *
     * @param string $userId The userid of the owning user
     * @return array List of Blocks
     * @access public
     * 
     */
    public function getUserBlocksArray($userId)
    {
        $results = $this->getAll(' WHERE userid=\''.$userId.'\' ');
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
     * Method to add a block to a user-level block page
     *
     * @param string $block Block Id
     * @param string $side Side Block is On
     * @param string $userId The user id of the owner
     * @param string $module Module Block is from
     * @access public
     * @return boolean The result of an insert
     *
     */
    public function addBlock($block, $side, $userId, $module)
    {
        return $this->insert(array(
                'userid' => $userId,
                'block' => $block,
                'side' => $side,
                'module' => $module,
                'position' => $this->getLastOrder($side, $userId)+1,
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    /**
     * Method to get the last order of a block on a side
     * This is used for ordering purposes
     *
     * @param string $side Side block will be added
     * @param string $userId The userid of the owner
     * @return int The postion of the block on its side
     * @access private
     * 
     */
    private function getLastOrder($side, $userId)
    {
        $results = $this->getAll(' WHERE side=\''.$side.'\' AND userid=\''.$userId.'\' ORDER BY position DESC LIMIT 1');

        if (count($results) == 0) {
            return 0;
        } else {
            return $results[0]['position'];
        }
    }


    /**
     * 
     * Method to remove a block
     *
     * @param string $id Block Id
     * @return string Success/fail
     * @access public
     *
     */
    public function removeBlock($id)
    {
        return $this->delete('id', $id);
    }

    /**
     *
     * Method to remove all blocks by a userid
     * 
     * @param string $userId The user id of the owner
     * @return string Success/fail
     * @access public
     *
     */
    public function removeUserBlocks($userId)
    {
        return $this->delete('userid', $userId);
    }

    /**
     * Method to move a block up
     *
     * @param string $id Block Id
     * @param string $userId The user id of the owner
     * @return boolean TRUE|FALSE
     * @access public
     * 
     */
    public function moveBlockUp($id, $userId)
    {
        $record = $this->getRow('id', $id);

        if ($record == FALSE) {
            return FALSE;
        } else {

            if ($record['userid'] != $userId) {
                return FALSE;
            }

            $prevRecord = $this->getPreviousBlock($record['userid'], $record['side'], $record['position']);

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
     * Method to move a block down
     *
     * @param string $id Block Id
     * @param string $userId The user id of the owner
     * @return boolean TRUE|FALSE
     * @access public
     * 
     */
    public function moveBlockDown($id, $userId)
    {
        $record = $this->getRow('id', $id);

        if ($record == FALSE) {
            return FALSE;
        } else {

            if ($record['userid'] != $userId) {
                return FALSE;
            }

            $nextRecord = $this->getNextBlock($record['userid'], $record['side'], $record['position']);

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
     * Method to get the details of the previous block
     *
     * @param string $userId The userId of the owner
     * @param string $side Side Block is on
     * @param int $position Position of the Block
     * @return array
     * @access private
     * 
     */
    private function getPreviousBlock($userId, $side, $position)
    {
        $results = $this->getAll(' WHERE side=\''.$side.'\' AND userid=\''.$userId.'\' AND position < '.$position.' ORDER BY position DESC LIMIT 1');

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }

    /**
     * Method to get the details of the next block
     *
     * @param string $userId The userId of the owner
     * @param string $side Side Block is on
     * @param int $position Position of the Block
     * @return array
     * @access private
     */
    private function getNextBlock($userId, $side, $position)
    {
        $results = $this->getAll(' WHERE side=\''.$side.'\' AND userid=\''.$userId.'\' AND position > '.$position.' ORDER BY position LIMIT 1');

        if (count($results) == 0) {
            return FALSE;
        } else {
            return $results[0];
        }
    }
    

}
?>