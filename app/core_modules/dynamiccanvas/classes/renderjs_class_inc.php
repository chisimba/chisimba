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
 * Render javascript
 *
 * A simple class for rendering the
*
* @author Derek Keats
* @package dynamiccanvas
*
*/
class renderjs extends object
{

    /**
    *
    * Intialiser for the dynamiccanvas renderjs class
    * @access public
    *
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
    }

    public function renderAdminScripts($moduleThis)
    {
        $script=NULL;
        // Add JavaScript if User can update blocks
        if ($this->objUser->isAdmin()) {

            $objIcon = $this->newObject('geticon', 'htmlelements');
            $objIcon->setIcon('up');
            $upIcon = $objIcon->show();


            $objIcon->setIcon('down');
            $downIcon = $objIcon->show();

            $objIcon->setIcon('delete');
            $deleteIcon = $objIcon->show();
            $script = '<script type="text/javascript">
            // <![CDATA[
                upIcon = \''. $upIcon . '\'
                downIcon = \'' .  $downIcon . '\'
                deleteIcon = \'' . $deleteIcon . '\'
                deleteConfirm = \'' . $objLanguage->languageText('mod_context_confirmremoveblock', 'context', 'Are you sure you want to remove the block') .'\
                unableMoveBlock = \'' . $objLanguage->languageText('mod_context_unablemoveblock', 'context', 'Error - Unable to move block') . '\'
                unableDeleteBlock = \'' . $objLanguage->languageText('mod_context_unabledeleteblock', 'context', 'Error - Unable to delete block') . '\'
                unableAddBlock = \'' . $objLanguage->languageText('mod_context_unableaddblock', 'context', 'Error - Unable to add block') . '\'
                turnEditingOn = \'' . $objLanguage->languageText('mod_context_turneditingon', 'context', 'Turn Editing On') .'\'
                turnEditingOff = \'' . $objLanguage->languageText('mod_context_turneditingoff', 'context', 'Turn Editing Off') . '\'
                theModule = \'' . $moduleThis . '\';

            // ]]>'
            . "</script>\n\n";

            $script .= $this->getJavaScriptFile('contextblocks.js', 'context');
        }
        return $script;
    }
}
?>