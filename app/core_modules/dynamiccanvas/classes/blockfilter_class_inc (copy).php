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
* using the pattern 
* {
*    "display" : "block",
*    "module" : "modulename",
*    "block" : "blockname",
*    "blocktype" : "blocktype",
*    "titleLength" : "titlelength",
*    "wrapStr" : 0|1,
*    "showToggle" : 0|1,
*    "hidden" : "value,
*    "showTitle" : 0|1,
*    "cssClass" : "cssClass",
*    "cssId " : "cssId"
* }
*
* @author Derek Keats
* @package dynamiccanvas
*
*/
class blockfilter extends object
{
    /**
    *
    * @var string $objJson Object the decoded json object
    *
    */
    private $objJson;
    /**
    *
    * Intialiser for the dynamiccanvas database connector
    * @access public
    *
    */
    public function init()
    {
        // Get the data class for connecting to blocks.
        //$this->objDbBlocks = $this->getObject('dbblocksdata', 'blocks');
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
        preg_match_all('/\\{(.*?)\\}/ism', $pageContent, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        // Loop over all the JSON blocks.
        foreach ($results[0] as $item)
        {
            // To avoid parsing chikis, look for : in the item.
            if (strpos($item, ":")) {
                $jsonTxt=str_replace('&nbsp;', '', $item);
                $jsonTxt=str_replace('<br />', "\n", $jsonTxt);
                $jsonTxt=str_replace('&quot;', '"', $jsonTxt);
                $jsonTxt = trim($jsonTxt);
                $this->objJson = json_decode($jsonTxt);
                // Verify that we must display as a block
                if (isset($this->objJson->display)) {
                    if ($this->objJson->display == 'block') {
                        // Verify that there is a block and a module.
                        if (isset($this->objJson->block) && isset($this->objJson->module)) {
                            if ($this->isValidBlock($this->objJson->block, $this->objJson->module)) {
                                // Parse the block
                                $blockCode = $this->getBlock();
                            } else {
                                // It is not a valid block so wrapt it in an error
                                $blockCode = '<div class="featurebox"><div class="error">mod_dynamiccanvas_invalidblock<br />'
                                  . $item . '</div></div>';
                            }
                        } else {
                            // The JSON is not valid so wrap it in an error
                            $blockCode = '<div class="featurebox"><div class="error">mod_dynamiccanvas_invalidjson<br />' . $item . '</div></div>';
                        }
                    } elseif ($this->objJson->display == 'externalblock') {
                        // Use counter to uniquely identify the div.
                        $blockCode = $this->getExternalBlock($counter);
                    } else {
                        $blockCode = nl2br($item);
                    }
                } else {
                    $blockCode = '<div class="featurebox"><div class="error">mod_dynamiccanvas_invaliddisplaynotset<br />' . $item . '</div></div>';
                }
                $replacement = $blockCode;
                //$tmp = "<h1>" . $counter . $this->objJson->block . "||" . $this->objJson->module . "</h1>";
                $pageContent = str_replace($item, $replacement, $pageContent);
                $counter++;
            }
        }
        return $pageContent;
    }

    /**
    *
    * Get the block and render it. Blocktypes can be NULL, tabbedbox, table,
    * wrapper, none (for wideblocks), or invisible (for turning off blocks
    * programmatically)
    *
    * @param string $blockName The block name
    * @param string $owningModule The module that owns the block
    * @return string The parsed content
    *
    */
    public function getBlock()
    {
        // We don't need to check block and module, already done elsewhere.
        $blockName = $this->objJson->block;
        $owningModule = $this->objJson->module;
        // Set the block type (see method comment or blocks_class in blocks module).
        if (isset($this->objJson->blockType)) {
            $blockType = $this->objJson->blockType;
        } else {
            $blockType = NULL;
        }
        // Set the title length, defaulting to 20. Only works if wrapStr=TRUE
        if (isset($this->objJson->titleLength)) {
            $titleLength = $this->objJson->titleLength;
        } else {
            $titleLength = 20;
        }
        // Set whether or not we wrap the title to title length.
        if (isset($this->objJson->wrapStr)) {
            $wrapStr = $this->objJson->wrapStr;
        } else {
            $wrapStr = TRUE;
        }
        // Set whether wes should display the toggle up/down, defaulting to TRUE.
        if (isset($this->objJson->showToggle)) {
            $showToggle = $this->objJson->showToggle;
        } else {
            $showToggle = TRUE;
        }
        // Set whether the contents are hidden (toggled up) by default.
        if (isset($this->objJson->hidden)) {
            $hidden = $this->objJson->hidden;
        } else {
            $hidden = 'default';
        }
        // Set whether the title is visible.
        if (isset($this->objJson->showTitle)) {
            $showTitle = $this->objJson->showTitle;
        } else {
            $showTitle = TRUE;
        }
        // Set the CSS class to use (normally featurebox).
        if (isset($this->objJson->cssClass)) {
            $cssClass = $this->objJson->cssClass;
        } else {
            $cssClass = 'featurebox';
        }
        // Set a custom cssId if required. Cannot think of a usecase!
        if (isset($this->objJson->cssId)) {
            $cssId = $this->objJson->cssId;
        } else {
            $cssId = '';
        }
        $blockContent = $this->objBlock->showBlock(
          $blockName, $owningModule, $blockType,
          $titleLength, $wrapStr, $showToggle,
          $hidden, $showTitle, $cssClass, $cssId);
        return $blockContent;
    }

    /**
    *
    * Get an external block and render it with Ajax code. Blocktypes can be
    * NULL, tabbedbox, table, wrapper, none (for wideblocks), or invisible
    * (for turning off blocks programmatically)
    * bn = blockname
    * om = owning module
    * bt = blocktype
    * tl = title length
    * ws = wrap string
    * stg = show toggle
    * hd = hidden
    * st = show title
    * cls = CSS class
    * cid = CSS id
    *
    * @param string $blockName The block name
    * @param string $owningModule The module that owns the block
    * @return string The parsed content
    *
    */
    public function getExternalBlock($counter)
    {
        // The layer to render the content into. Use counter to make it unique.
        $layer = "<div id='externalblock_$counter'>Loading...</div>";
        // The basic URL, with the parameter names shortened.
        $url = $this->objJson->server
          . "index.php?module=externalblocks&bn="
          . $this->objJson->block
          . "&om=" .$this->objJson->module;
        // Set the block type (see method comment or blocks_class in blocks module).
        if (isset($this->objJson->blockType)) {
            $url .= "&bt=" . $this->objJson->blockType;
        }
        // Set the title length, defaulting to 20. Only works if wrapStr=TRUE
        if (isset($this->objJson->titleLength)) {
            $url .= "&tl=" . $this->objJson->titleLength;
        }
        // Set whether or not we wrap the title to title length.
        if (isset($this->objJson->wrapStr)) {
             $url .= "&ws=" . $this->objJson->wrapStr;
        }
        // Set whether wes should display the toggle up/down, defaulting to TRUE.
        if (isset($this->objJson->showToggle)) {
             $url .= "&stg=" . $this->objJson->showToggle;
        }
        // Set whether the contents are hidden (toggled up) by default.
        if (isset($this->objJson->hidden)) {
             $url .= "&hd=" . $this->objJson->hidden;
        }
        // Set whether the title is visible.
        if (isset($this->objJson->showTitle)) {
             $url .= "&st=" . $this->objJson->showTitle;
        }
        // Set the CSS class to use (normally featurebox).
        if (isset($this->objJson->cssClass)) {
             $url .= "&cls=" . $this->objJson->cssClass;
        }
        // Set a custom cssId if required. Cannot think of a usecase!
        if (isset($this->objJson->cssId)) {
             $url .= "&cid=" . $this->objJson->cssId;
        }
        // Render the ajax using jQuery.load
        $script = "\n\n<script type=\"text/javascript\">\n"
          . "// <![CDATA[\n"
          . "jQuery('#externalblock_$counter').load('$url')\n"
          . "// ]]>\n</script>\n\n";
        return $layer . $script;
    }

    /**
     *
     * Render the jQuery Embed script to embed the content via
     * Ajax.
     *
     * @param string $cssClass The CSS class which holds the content
     * @param strings $url The URL to render the block
     * @return string The rendered script
     */
    public function renderEmbed($cssClass, $url)
    {
        // Render the ajax using jQuery.load
        $script = "\n\n<script type=\"text/javascript\">\n"
          . "// <![CDATA[\n"
          . "jQuery('$cssClass').load('$url')\n"
          . "// ]]>\n</script>\n\n";
        return $script;
    }
}
?>