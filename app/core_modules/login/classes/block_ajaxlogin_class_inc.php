<?php
 /**
 * Ajax login block class for Chisimba
 *
 * A block class to produce a login box in a block
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
 *
 * @category  Chisimba
 * @package   login
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2011, Wits University
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
 *
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Ajax login block class for Chisimba
 *
 * A block class to produce a login box in a block
 *
 * @category  Chisimba
 * @package   login
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2011, Wits University
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
 */
class block_ajaxlogin extends object
{
    /**
    * @var string $title The title of the block
    * @access public
    */
    public $title;

    /**
    *
    * @var string object $objLanguage String to hold the language object
    * @access private
    *
    */

    private $objLanguage;
    /**
     * @var string object $objUser String to hold the language object
     * @access private
     *
    */
    private $objUser;

    /**
     *
     * @var string The type of block
     * @access public
     * 
     */
    public $blockType;

    /**
     * Standard init function to instantiate language object
     * and create title, etc
     *
     * @access public
     * @return void
     * 
    */
    public function init()
    {
        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            if($this->objUser->isLoggedIn()) {
                $this->blockType="invisible";
            } else {
                $this->title = $this->objLanguage->languageText("word_login");
            }
        } catch (customException $e) {
            customException::cleanUp();
        }
    }

    /**
     * Standard block show method. It uses the renderform
     * class to render the login box
     *
     * @access public
     * @return string The rendered block
     *
    */
    public function show()
    {
        try {
            if($this->objUser->isLoggedIn()) {
                return NULL;
            } else {
                // Guess the module we are in
                $objGuess = $this->getObject('bestguess', 'utilities');
                $curMod = $objGuess->identifyModule();
                // Get the login box.
                $objLogin =  $this->getObject('showloginbox', 'login');
                // Show an ajax login box, retaining the current module.
                return $objLogin->show($curMod, TRUE);
            }
        } catch (customException $e) {
            customException::cleanUp();
        }
    }
}
?>