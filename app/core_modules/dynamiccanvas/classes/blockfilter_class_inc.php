<?php
/**
 *
 * Dynamic canvas
 *
 * Dynamic canvas obviates the need to render content to a template, instead
 * you render blocks, and the dynamic canvas exposes them to users as blocks.
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
 * @package   dynamiccanvas
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: dbdynamiccanvas.php,v 1.1 2007-11-25 09:13:27 dkeats Exp $
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
* Block filter
*
* The block filter allows for the insertion of blocks in dynamic canvas templates
* using the pattern {block:blockname} or {wideblock:blockname}
*
*
* @author Derek Keats
* @package dynamiccanvas
*
*/
class blockfilter extends object
{
    /**
    *
    * Intialiser for the dynamiccanvas database connector
    * @access public
    *
    */
    public function init()
    {
        // Get the data class for connecting to blocks.
        $this->objDbBlocks = $this->getObject('dbblocksdata', 'blocks');
        // Get the main blocks class.
        $this->objBlock = $this->getObject('blocks', 'blocks');
    }

    /**
    *
    * Method to check if the block is valid or not.
    *
    * @param string $blockName The block name
    * @param string $owningModule The module that owns the block
    * @return boolean TRUE|FALSE
    *
    */
    public function isValidBlock($blockName, $owningModule)
    {
        if ($this->objBlock->blockExists($blockName, $owningModule)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Parse the page content for blocks
    *
    * @param string $pageContent The content of the page from the output buffer
    * @return string The parsed content
    * 
    */
    public function parse($pageContent)
    {
        $pageContent = stripslashes($pageContent);
        preg_match_all('/\\{(.*?)\\}/', $pageContent, $results, PREG_PATTERN_ORDER);
        $counter = 0;

        foreach ($results[0] as $item)
        {
            $extracted = strtolower($results[1][$counter]);
            if (!strpos($extracted, ':')) {
                $blockName = $extracted;
                $owningModule = $this->moduleName;
            } else {
                $arSettings = explode(":",$extracted);
                $owningModule = $arSettings[0];
                $blockName = $arSettings[1];
            }
            
            if ($this->isValidBlock($blockName, $owningModule)) {
                $blockCode = $this->getBlock($blockName, $owningModule);
            } else {
                $blockCode = '<div class="featurebox"><div class="error">' . $item . '</div></div>';
            }
            $replacement = $blockCode;
            $pageContent = str_replace($item, $replacement, $pageContent);
            $counter++;
        }
        return $pageContent;
    }

    /**
    *
    * Get the block and render it.
    *
    * @param string $blockName The block name
    * @param string $owningModule The module that owns the block
    * @return string The parsed content
    *
    */
    public function getBlock($blockName, $owningModule)
    {
        $blockContent = $this->objBlock->showBlock($blockName, $owningModule);
        //, NULL, 20, TRUE, TRUE, 'default', FALSE, 'featurebox', 'canvasblock'
        // showBlock($block, $module, $blockType = NULL, $titleLength = 20, $wrapStr = TRUE, $showToggle = TRUE, $hidden = 'default', $showTitle = TRUE, $cssClass = 'featurebox', $cssId = '')
        return $blockContent;
    }
}
?>