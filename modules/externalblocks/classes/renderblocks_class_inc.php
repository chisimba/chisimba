<?php
/**
 *
 * External blocks
 *
 * Render blocks from this site so they may be used by an external site, such
 * as another Chisimba site, or any site capable of sending an Ajax request.
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
 * @package   externalblocks
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbexternalblocks.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* Render blocks
*
* Render external blocks based on supplied criteria
*
* @author Derek Keats
* @package externalblocks
*
*/
class renderblocks extends object
{

    /**
    *
    * Intialiser for the renderblocks class
    * 
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objBlock = $this->getObject ( 'blocks', 'blocks' );
    }


    /**
     *
     * Get a block in response to an external request
     *
     * @return string A rendered block with no surrounding HTML page
     *
     */
    public function getBlock()
    {
        $blockName = $this->getParam('bn', FALSE);
        $owningModule = $this->getParam('om', FALSE);
        $blockType = $this->getParam('bt', NULL);
        $titleLength = $this->getParam('tl', 20);
        $wrapStr = $this->getParam('ws', TRUE);
        $showToggle = $this->getParam('stg', TRUE);
        $hidden = $this->getParam('hd', 'default');
        $showTitle = $this->getParam('st', TRUE);
        $cssClass = $this->getParam('cls', 'featurebox');
        $cssId = $this->getParam('cid', '');
        if ($blockName && $owningModule) {
              $blockContent = $this->objBlock->showBlockExternal(
              $blockName, $owningModule, $blockType,
              $titleLength, $wrapStr, $showToggle,
              $hidden, $showTitle, $cssClass, $cssId
            );
        } else {
            $blockContent = '<div class="featurebox"><div class="error">'
            . $blockName . "||" . $owningModule
            . '</div></div>';
        }
        return $blockContent;
    }

}
?>