<?php
 /**
 * block_login class for Chisimba
 *
 * A block class to produce a login box in a block.
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
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

class block_spkea4freelogin extends object
{
    /**
    * @var string $title The title of the block
    */
    public $title;

    /**
    * @var string object $objLanguage String to hold the language object
    */
    private $objLanguage;
    /**
    * @var string object $objUser String to hold the language object
    */
    private $objUser;
    public $blockType;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
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
    */
    public function show()
    {
        try {
            if($this->objUser->isLoggedIn()) {
                return NULL;
            } else {
                $objLogin =  $this->getObject('witsstudentloginInterface', 'witsstudentregistration');
                return $objLogin->renderLoginBox();
            }
        } catch (customException $e) {
            customException::cleanUp();
        }
     
    }
}
?>
