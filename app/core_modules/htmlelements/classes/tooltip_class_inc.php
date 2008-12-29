<?php
/**
 * Class for tooltips.
 * Note: This tooltip JS lib is compatible with and requires prototype 1.5.0.
 *
 * This class loads the JavaScript for the tooltip JS lib and allows developers to
 * insert their own tooltips into their modules
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
 * @package   htmlelements
 * @author  Jeremy O'Connor
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
* Class for tooltips.
* Note: This tooltip JS lib is compatible with and requires prototype 1.5.0.
*
* This class loads the JavaScript for the tooltip JS lib and allows developers to
* insert their own tooltips into their modules.
*
* Example:
*
* $tooltipHelp =& $this->getObject('tooltip','htmlelements');
* $tooltipHelp->setCaption('Help');
* $tooltipHelp->setText('Some help text...');
* $tooltipHelp->setCursor('help');
* echo $tooltipHelp->show();
*
* @author  Jeremy O'Connor
* @package htmlelements
*/
class tooltip extends object
{
     /**
     * @var $caption string The caption
     */
     private $caption;

     /**
     * @var $text string The text
     */
     private $text;

     /**
     * @var $cursor string The cursor for the caption
     */
     private $cursor = 'default';

    /**
    * The constructor.
    */
    public function init()
    {
        // Include the JS tooltip lib if not already included
        if (!defined('CHISIMBA_TOOLTIP_JS_INCLUDED')) {

    /**
     * Description for define
     */
              define('CHISIMBA_TOOLTIP_JS_INCLUDED', TRUE);
            $this->appendArrayVar('headerParams',$this->getJavascriptFile('tooltip.js','htmlelements'));
            // Incremented each time the show function is called...
            $GLOBALS['CHISIMBA_TOOLTIP_ID'] = 0;
        }
    }

    /**
    * Set the caption.
    * @param string The caption
    * @return null
    */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
    * Set the text.
    * @param string The text
    * @return null
    */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
    * Set the cursor.
    * @param string The cursor type
    * @return null
    */
    public function setCursor($cursor)
    {
        $this->cursor = $cursor;
    }

    /**
    * Method display a tooltip.
    * @return string Tooltip
    */
    public function show()
    {
        $id = $GLOBALS['CHISIMBA_TOOLTIP_ID']++;;
        $_text = nl2br($this->text);
        return <<<EOB
<div id="tooltip_div_{$id}" style="display:none; margin: 5px; background-color: yellow;">{$_text}</div>
<span id="tooltip_span_{$id}" style="cursor: {$this->cursor};">{$this->caption}</span>
<script type="text/javascript" language="JavaScript">
new Tooltip('tooltip_span_{$id}', 'tooltip_div_{$id}');
</script>
EOB;
    }
}

?>
