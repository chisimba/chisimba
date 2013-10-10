<?php
/**
 *
 * A middle block for statusbar.
 *
 * A middle block for statusbar. This creates a floating bar to hold various icon links to features that should be available to the user at all times regardless of the page the user is viewing..
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
 * @package    statusbar
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
 * A middle block for statusbar.
 *
 * A middle block for statusbar. This creates a floating bar to hold various icon links to features that should be available to the user at all times regardless of the page the user is viewing..
 *
 * @category  Chisimba
 * @author    Kevin Cyster kcyster@gmail.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_statusbarmain extends object
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
        $this->objOps = $this->getObject('statusbarops', 'statusbar');
        $this->objLanguage = $this->getObject('language', 'language');

        $titleLabel = $this->objLanguage->languageText('mod_statusbar_statusbar', 'statusbar', 'ERROR: mod_statusbar_statusbar');        
        $this->title = ucfirst(strtolower($titleLabel));
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        return $this->objOps->showMain();
    }
}
?>