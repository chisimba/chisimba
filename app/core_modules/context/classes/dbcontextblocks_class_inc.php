<?php

/**
 * Context Blocks
 *
 * Class to add, rearrange, move around and remove blocks from a context home page
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
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Context Blocks
 *
 * Class to add, rearrange, move around and remove blocks from a context home page
 *
 * @category  Chisimba
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbcontextblocks extends dbTable {
    /**
     * The user Object
     *
     * @var object $objUser
     */
    public $objUser;

    /**
     * Constructor
     */
    public function init() {
        parent::init ( 'tbl_context_blocks' );
        $this->objUser = $this->getObject ( 'user', 'security' );
    }

    /**
     * Method to get a list of blocks used in a context, and have them rendered one time
     *
     * @param string $contextCode Context Code
     * @param string $side Side on which the blocks are on
     * @param return array
     */
    public function getContextBlocks($contextCode, $side) {
        $results = $this->getContextBlocksList ( $contextCode, $side );
        if (count ( $results ) == 0) {
            return '';
        } else {
            $str = '';
            $objBlocks = $this->getObject ( 'blocks', 'blocks' );
            $objDynamicBlocks = $this->getObject ( 'dynamicblocks', 'blocks' );

            foreach ( $results as $result ) {
                $block = explode ( '|', $result ['block'] );
                $blockId = $side . '___' . str_replace ( '|', '___', $result ['block'] );

                // At the moment, only blocks are catered for, not yet dynamic blocks
                if ($block [0] == 'block') {
                    $blockStr = $objBlocks->showBlock ( $block [1], $block [2], NULL, 20, TRUE, FALSE );
                    $str .= '<div id="' . $result ['id'] . '" class="block">' . $blockStr . '</div>';
                } else if ($block [0] == 'dynamicblock') {
                    $block = explode ( '|', $result ['block'] );
                    $blockStr = $objDynamicBlocks->showBlock ( $block [1] );
                    $str .= '<div id="' . $result ['id'] . '" class="block">' . $blockStr . '</div>';
                }
            }

            return $str;
        }
    }

    /**
     * Method to get a list of blocks used in a context
     *
     * @param string $contextCode Context Code
     * @param string $side Side on which the blocks are on
     * @param return array
     */
    public function getContextBlocksList($contextCode, $side) {
        return $this->getAll ( ' WHERE side=\'' . $side . '\' AND contextcode=\'' . $contextCode . '\' ORDER BY position' );
    }

    /**
     * Method to get a list of blocks used by a context
     *
     * @param string $contextCode
     * @return array List of Blocks
     */
    public function getContextBlocksArray($contextCode) {
        $results = $this->getAll ( ' WHERE contextcode=\'' . $contextCode . '\' ' );
        $array = array ();
        if (count ( $results ) > 0) {
            foreach ( $results as $result ) {
                $array [] = $result ['block'];
            }
        }

        return $array;
    }

    /**
     * Method to add a block to a context
     *
     * @param string $block Block Id
     * @param string $side Side Block is On
     * @param string $contextCode Context Code
     * @param string $module Module Block is from
     *
     */
    public function addBlock($block, $side, $contextCode, $module) {
        return $this->insert ( array ('contextcode' => $contextCode, 'block' => $block, 'side' => $side, 'module' => $module, 'position' => $this->getLastOrder ( $side, $contextCode ) + 1, 'updatedby' => $this->objUser->userId (), 'datelastupdated' => strftime ( '%Y-%m-%d %H:%M:%S', mktime () ) ) );
    }

    /**
     * Method to get the last order of a block on a side
     * This is used for ordering purposes
     *
     * @param string $side Side block will be added
     * @param string $contextCode Context Code
     *
     * @return int
     */
    private function getLastOrder($side, $contextCode) {
        $results = $this->getAll ( ' WHERE side=\'' . $side . '\' AND contextcode=\'' . $contextCode . '\' ORDER BY position DESC LIMIT 1' );
        if (count ( $results ) == 0) {
            return 0;
        } else {
            return $results [0] ['position'];
        }
    }

    /**
     * Method to remove a block
     *
     * @param string $id Block Id
     */
    public function removeBlock($id) {
        return $this->delete ( 'id', $id );
    }

    /**
     * Method to remove a block
     *
     * @param string $id Block Id
     */
    public function removeContextBlocks($contextCode) {
        return $this->delete ( 'contextcode', $contextCode );
    }

    /**
     * Method to move a block up
     *
     * @param string $id Block Id
     * @param string $contextCode Context Code - required to prevent malicious changes
     */
    public function moveBlockUp($id, $contextCode) {
        $record = $this->getRow ( 'id', $id );
        if ($record == FALSE) {
            return FALSE;
        } else {
            if ($record ['contextcode'] != $contextCode) {
                return FALSE;
            }
            $prevRecord = $this->getPreviousBlock ( $record ['contextcode'], $record ['side'], $record ['position'] );
            if ($prevRecord == FALSE) {
                return FALSE;
            } else {
                $this->update ( 'id', $record ['id'], array ('position' => $prevRecord ['position'] ) );
                $this->update ( 'id', $prevRecord ['id'], array ('position' => $record ['position'] ) );

                return TRUE;
            }
        }
    }

    /**
     * Method to move a block down
     *
     * @param string $id Block Id
     * @param string $contextCode Context Code - required to prevent malicious changes
     */
    public function moveBlockDown($id, $contextCode) {
        $record = $this->getRow ( 'id', $id );
        if ($record == FALSE) {
            return FALSE;
        } else {
            if ($record ['contextcode'] != $contextCode) {
                return FALSE;
            }
            $nextRecord = $this->getNextBlock ( $record ['contextcode'], $record ['side'], $record ['position'] );
            if ($nextRecord == FALSE) {
                return FALSE;
            } else {
                $this->update ( 'id', $record ['id'], array ('position' => $nextRecord ['position'] ) );
                $this->update ( 'id', $nextRecord ['id'], array ('position' => $record ['position'] ) );

                return TRUE;
            }
        }
    }

    /**
     * Method to get the details of the previous block
     *
     * @param string $contextCode Context Code - required to prevent malicious changes
     * @param string $side Side Block is on
     * @param int $position Position of the Block
     *
     * @return array
     */
    private function getPreviousBlock($contextCode, $side, $position) {
        $results = $this->getAll ( ' WHERE side=\'' . $side . '\' AND contextcode=\'' . $contextCode . '\' AND position < ' . $position . ' ORDER BY position DESC LIMIT 1' );
        if (count ( $results ) == 0) {
            return FALSE;
        } else {
            return $results [0];
        }
    }

    /**
     * Method to get the details of the next block
     *
     * @param string $contextCode Context Code - required to prevent malicious changes
     * @param string $side Side Block is on
     * @param int $position Position of the Block
     *
     * @return array
     */
    private function getNextBlock($contextCode, $side, $position) {
        $results = $this->getAll ( ' WHERE side=\'' . $side . '\' AND contextcode=\'' . $contextCode . '\' AND position > ' . $position . ' ORDER BY position LIMIT 1' );
        if (count ( $results ) == 0) {
            return FALSE;
        } else {
            return $results [0];
        }
    }

}

?>