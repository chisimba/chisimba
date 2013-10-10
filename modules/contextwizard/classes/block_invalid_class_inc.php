<?php
/**
 *
 * A block for contextwizard.
 *
 * A block to display an error message for an invalid or unauthorised action
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
 * @version    0.001
 * @package    contextwizard
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 * 
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
// end security check

/**
 * 
 * A block for contextwizard.
 *
 * A block to display an error message for an invalid or unauthorised action
 *
 * @category  Chisimba
 * @author    Kevin Cyster kcyster@gmail.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_invalid extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;

    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $titleLabel = $this->objLanguage->languageText('mod_contextwizard_invalidaction', 'contextwizard', 'ERROR: mod_contextwizard_invalidaction');
        
        $this->title = ucfirst(strtolower($titleLabel));
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return '<div class="error">' . $this->objLanguage->languageText('mod_contextwizard_actionerror', 'contextwizard', 'ERROR: mod_contextwizard_actionerror') . '</div>';
    }
}
?>