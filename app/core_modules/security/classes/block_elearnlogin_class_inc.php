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
 * 
 * @category  Chisimba
 * @package   files
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id: block_login_class_inc.php 2881 2007-08-14 16:32:04Z jsc $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* 
* A block class to produce a login box in a block
*
* @author Derek Keats
* 
* $Id: block_login_class_inc.php 2881 2007-08-14 16:32:04Z jsc $
*
*/
class block_elearnlogin extends object
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
    
    public $defaultHidden = TRUE;

    /**
    * Standard init function to instantiate language object
    * and create title, etc
    */
    public function init()
    {
        try {
            $this->objLanguage =  $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            if($this->objUser->isLoggedIn())
            {
                $this->blockType="invisible";
            }
            $this->title = $this->objLanguage->languageText("word_login");
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
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        
        $form = new form ('elearnlogin', $this->uri(array('action'=>'login'), 'security'));
        
        $label = new label ($this->objLanguage->languageText('word_username', 'system', 'Username').':', 'username');
        $form->addToForm($label->show());
        $username = new textinput('username');
        $form->addToForm('<br />'.$username->show());
        
        $label = new label ($this->objLanguage->languageText('word_password', 'system', 'Password').':', 'username');
        $form->addToForm('<br />'.$label->show());
        $password = new textinput('password');
        $password->fldType = 'password';
        $form->addToForm('<br />'.$password->show());
        
        $button = new button ('login', $this->objLanguage->languageText('word_login', 'system', 'Login'));
        $button->setToSubmit();
        $form->addToForm('<br />'.$button->show());
        
        $str = $form->show();
        
        $str .= '<hr />';
        
        $header = new htmlheading();
        $header->type = 5;
        $header->str = $this->objLanguage->languageText('mod_security_forgotyourpassword', 'security', 'Forgot your password').'?';
        
        $str .= $header->show();
        
        $link = new link($this->uri(array('action'=>'needpassword')), 'security');
        $link->link = $this->objLanguage->languageText('mod_security_helpmelogin', 'security', 'Yes, help me login');
        
        $str .= '<p>'.$link->show().'</p>';
        
        return $str;
    }
}
?>
