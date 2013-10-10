<?php
/**
 *
 * A left block for Submit a conference talk.
 *
 * A left block for Submit a conference talk. A simplified version of an interface to submit a conference talk and have it accepted or rejected for a conference..
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
 * @package    simpletalk
 * @author     Derek Keats derek@dkeats.com
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
 * A left block for Submit a conference talk.
 *
 * A left block for Submit a conference talk. A simplified version of an interface to submit a conference talk and have it accepted or rejected for a conference..
 *
 * @category  Chisimba
 * @author    Derek Keats derek@dkeats.com
 * @version   0.001
 * @copyright 2011 AVOIR
 *
 */
class block_simpletalkleft extends object
{
    /**
     * The title of the block
     *
     * @var    object
     * @access public
     */
    public $title;
    /**
     *
     * @var string Object $objLanguage String for the language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     * Standard init function
     *
     * Create title
     *
     * @return NULL
     */
    public function init() 
    {
        // Get an instance of the languate object
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = "Instructions";
    }
    /**
     * Standard block show method.
     *
     * @return string $this->display block rendered
     */
    public function show() 
    {
        
        return $this->objLanguage->languageText("mod_simpletalk_instructions",
            "simpletalk", "mod_simpletalk_instructions is missing");
    }
}
?>
